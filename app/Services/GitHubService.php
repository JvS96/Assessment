<?php
namespace Services;

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
                'method' => $method,
                'header' => implode("\r\n", $headers),
            ]
        ];

        if ($data !== null) {
            $options['http']['header'] .= "\r\nContent-Type: application/json";
            $options['http']['content'] = json_encode($data);
        }

        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            throw new Exception("GitHub API request failed.");
        }

        $decoded = json_decode($response, true);

        if (isset($decoded['message'])) {
            // GitHub error message
            throw new Exception("GitHub Error: " . $decoded['message']);
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
}