<?php

namespace Controllers;

use Core\Session;
use Core\Response;
use Services\GitHubService;

class AuthController
{
    public function login()
    {
        Session::start();

        $state = bin2hex(random_bytes(16));
        Session::set('oauth_state', $state);

        $params = http_build_query([
            'client_id'     => env('GITHUB_CLIENT_ID'),
            'redirect_uri'  => env('GITHUB_REDIRECT_URI'),
            'scope'         => 'repo',
            'state'         => $state
        ]);

        header("Location: https://github.com/login/oauth/authorize?$params");
        exit;
    }

    public function callback()
    {
        Session::start();

        $state = $_GET['state'] ?? null;
        $storedState = Session::get('oauth_state');

        if (!$state || $state !== $storedState) {
            return Response::json(['error' => 'Invalid OAuth state'], 400);
        }

        $code = $_GET['code'] ?? null;

        if (!$code) {
            return Response::json(['error' => 'Authorization code missing'], 400);
        }

        try {
            $gitHub = new GitHubService('');
            $accessToken = $gitHub->exchangeCodeForToken($code);

            Session::regenerate();
            Session::set('access_token', $accessToken);

            header('Location: /issue.php');
            exit;

        } catch (\Throwable $e) {
            return Response::json([
                'error' => 'OAuth failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}