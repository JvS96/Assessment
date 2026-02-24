<?php

namespace Controllers;

use Requests\StoreIssueRequest;
use Core\Session;
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

        if (Session::get('access_token') === null) {
            return Response::json(['error' => 'Unauthenticated'], 401);
        }

        try {
            $request = new StoreIssueRequest();
            $data = $request->validate();

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

            return Response::json($issue->toArray(), 201);

        } catch (\Throwable $e) {
            return Response::json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
}