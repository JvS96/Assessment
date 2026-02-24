<?php
namespace Services;

use Exception;

class GitHubService
{
    private string $baseUrl = 'https://api.github.com';
    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getAuthenticatedUser(): array
    {
        return $this->request('GET', '/user');
    }

    private function request(string $method, string $endpoint, array $data = null): array
    {
        $url = $this->baseUrl . $endpoint;

        $headers = [
            "Authorization: Bearer {$this->token}",
            "User-Agent: GitIntegrationApp",
            "Accept: application/vnd.github.v3+json"
        ];

        $options = [
            'http' => [
                'method'  => $method,
                'header'  => implode("\r\n", $headers),
                'ignore_errors' => true
            ]
        ];

        if ($data !== null) {
            $options['http']['header'] .= "\r\nContent-Type: application/json";
            $options['http']['content'] = json_encode($data);
        }

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            throw new \Exception("GitHub API request failed: Unable to connect.");
        }

        $statusLine = $http_response_header[0] ?? '';
        preg_match('{HTTP/\S*\s(\d{3})}', $statusLine, $match);
        $statusCode = $match[1] ?? 0;

        $decoded = json_decode($response, true);

        if ($statusCode >= 400) {
            $message = $decoded['message'] ?? 'Unknown error';

            if ($statusCode == 401) {
                throw new \Exception("Unauthorized: Access token invalid or expired.");
            }

            if ($statusCode == 403) {
                // Possible rate limit
                throw new \Exception("Forbidden: Possibly rate limited or insufficient permissions.");
            }

            if ($statusCode == 404) {
                throw new \Exception("Repository not found.");
            }

            if ($statusCode >= 500) {
                throw new \Exception("GitHub server error ({$statusCode}).");
            }

            throw new \Exception("GitHub error ({$statusCode}): {$message}");
        }

        return $decoded;
    }

    public function getIssues(): array
    {
        $owner = env('GITHUB_REPO_OWNER');
        $repo = env('GITHUB_REPO_NAME');

        return $this->request(
            'GET',
            "/repos/{$owner}/{$repo}/issues?state=all"
        );
    }

    public function createIssue(
        string $title,
        string $body,
        array $labels,
        array $assignees = []
    ): array {
        $owner = env('GITHUB_REPO_OWNER');
        $repo = env('GITHUB_REPO_NAME');

        return $this->request(
            'POST',
            "/repos/{$owner}/{$repo}/issues",
            [
                'title'     => $title,
                'body'      => $body,
                'labels'    => $labels,
                'assignees' => $assignees
            ]
        );
    }

    public function exchangeCodeForToken(string $code): string
    {
        $response = file_get_contents(
            'https://github.com/login/oauth/access_token',
            false,
            stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
                    'content' => json_encode([
                        'client_id' => env('GITHUB_CLIENT_ID'),
                        'client_secret' => env('GITHUB_CLIENT_SECRET'),
                        'code' => $code
                    ])
                ]
            ])
        );

        if ($response === false) {
            throw new \Exception('Failed to contact GitHub.');
        }

        $data = json_decode($response, true);

        if (!isset($data['access_token'])) {
            throw new \Exception('Access token not returned.');
        }

        return $data['access_token'];
    }
}