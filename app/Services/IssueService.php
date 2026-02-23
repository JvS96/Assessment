<?php
namespace Services;

use Models\Issue;
use Utils\LabelParser;
use Services\GitHubService;
class IssueService
{
    private GitHubService $gitHub;

    public function __construct(GitHubService $gitHub)
    {
        $this->gitHub = $gitHub;
    }

    public function getAll(): array
    {
        $issues = $this->gitHub->getIssues();
        $result = [];

        foreach ($issues as $issue) {

            $parsed = LabelParser::parse($issue['labels']);

            $result[] = new Issue(
                $issue['number'],
                $issue['title'],
                $issue['body'] ?? null,
                $parsed['client'],
                $parsed['priority'],
                $parsed['type'],
                $issue['assignee']['login'] ?? null,
                $issue['state']
            );
        }

        return $result;
    }

    public function create(string $title, string $body, string $client, string $priority, string $type): Issue
    {
        $labels = [
            "C: {$client}",
            "P: {$priority}",
            "T: {$type}"
        ];

        $issue = $this->gitHub->createIssue($title, $body, $labels);

        $parsed = LabelParser::parse($issue['labels']);

        return new Issue(
            $issue['number'],
            $issue['title'],
            $issue['body'] ?? null,
            $parsed['client'],
            $parsed['priority'],
            $parsed['type'],
            $issue['assignee']['login'] ?? null,
            $issue['state']
        );
    }
}