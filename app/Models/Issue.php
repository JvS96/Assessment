<?php
namespace Models;
class Issue
{
    public function __construct(
        public int $number,
        public string $title,
        public ?string $body,
        public ?string $client,
        public ?string $priority,
        public ?string $type,
        public ?string $assignedTo,
        public string $status
    ) {}

    public function toArray(): array
    {
        return [
            'number' => $this->number,
            'title' => $this->title,
            'body' => $this->body,
            'client' => $this->client,
            'priority' => $this->priority,
            'type' => $this->type,
            'assigned_to' => $this->assignedTo,
            'status' => $this->status,
        ];
    }
}