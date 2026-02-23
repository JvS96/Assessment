<?php

namespace Controllers;

use Core\Session;
use Core\Csrf;
use Core\Request;
use Core\Response;
use Services\GitHubService;

class AuthController
{
    public function login()
    {
        Session::start();

        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;

        $params = http_build_query([
            'client_id' => \env('GITHUB_CLIENT_ID'),
            'redirect_uri' => \env('GITHUB_REDIRECT_URI'),
            'scope' => 'repo',
            'state' => $state
        ]);

        header("Location: https://github.com/login/oauth/authorize?$params");
    }

    public function callback()
    {
        Session::start();

        if (!isset($_GET['state']) || $_GET['state'] !== $_SESSION['oauth_state']) {
            die("Invalid OAuth state.");
        }

        $code = $_GET['code'] ?? null;

        if (!$code) {
            die("Authorization code missing.");
        }

        $response = file_get_contents('https://github.com/login/oauth/access_token', false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
                'content' => json_encode([
                    'client_id' => \env('GITHUB_CLIENT_ID'),
                    'client_secret' => \env('GITHUB_CLIENT_SECRET'),
                    'code' => $code
                ])
            ]
        ]));

        $data = json_decode($response, true);

        if (!isset($data['access_token'])) {
            die("Failed to retrieve access token.");
        }

        Session::regenerate();
        Session::set('access_token', $data['access_token']);

        header("Location: /issue.php");
    }
}