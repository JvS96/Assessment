<?php
namespace Requests;

use Core\Request;
use Core\Validator;

class StoreIssueRequest
{
    private Request $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    public function validate(): array
    {
        $data = [
            'title'    => $this->request->input('title'),
            'body'     => $this->request->input('body'),
            'client'   => $this->request->input('client'),
            'priority' => $this->request->input('priority'),
            'type'     => $this->request->input('type'),
        ];

        $errors = Validator::validateIssue($data);

        if (!empty($errors)) {
            throw new \Exception(json_encode($errors), 422);
        }

        return $data;
    }
}