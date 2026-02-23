<?php

namespace Controllers;

use Core\Session;
use Core\Csrf;
use Core\Request;
use Core\Response;
use Core\Validator;
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

        $data = [
            'title'    => $request->input('title'),
            'body'     => $request->input('body'),
            'client'   => $request->input('client'),
            'priority' => $request->input('priority'),
            'type'     => $request->input('type'),
        ];

        $errors = Validator::validateIssue($data);

        if (!empty($errors)) {
            return Response::json([
                'error' => 'Validation failed',
                'fields' => $errors
            ], 422);
        }

        $service = new IssueService(
            new GitHubService(Session::get('access_token'))
        );

        $issue = $service->create(
            $data['title'],
            $data['body'],
            $data['client'],
            $data['priority'],
            $data['type']
        );

        return Response::json($issue->toArray(), 200);
    }
}