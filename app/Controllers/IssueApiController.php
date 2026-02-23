<?php

namespace Controllers;

use Core\Session;
use Core\Csrf;
use Core\Request;
use Core\Response;
use Services\GitHubService;
use Services\IssueService;
use Throwable;
class IssueApiController
{
    public function index()
    {
        Session::start();

        if (Session::get('access_token') === null) {
            return Response::json(['error' => 'Unauthenticated'], 401);
        }

        try {
            $service = new IssueService(
                new GitHubService(Session::get('access_token'))
            );

            $issues = $service->getAll();

            return Response::json(
                array_map(fn($issue) => $issue->toArray(), $issues)
            );
        } catch (Throwable $e) {
            return Response::json([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store()
    {
        Session::start();

        $request = new Request();

        if (!Csrf::validate($request->input('_csrf'))) {
            return Response::json(['error' => 'Invalid CSRF'], 403);
        }

        $service = new IssueService(
            new GitHubService(Session::get('access_token'))
        );

        $issue = $service->create(
            $request->input('title'),
            $request->input('body'),
            $request->input('client'),
            $request->input('priority'),
            $request->input('type')
        );

        return Response::json($issue->toArray(), 200);
    }
}