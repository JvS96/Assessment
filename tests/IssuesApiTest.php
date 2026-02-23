<?php

namespace tests;

use Core\Session;
use PHPUnit\Framework\TestCase;

class IssuesApiTest extends TestCase
{
    protected function setUp(): void
    {
        Session::start();
        $_SESSION = [];
    }

    public function testIssuesApiBlocksUnauthenticatedUser()
    {
        unset($_SESSION['access_token']);

        ob_start();
        require __DIR__ . '/../public/api/issues.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('Unauthenticated', $output);
    }

    public function testIssuesApiReturnsJsonWhenAuthenticated()
    {
        $_SESSION['access_token'] = 'fake';

        ob_start();
        require __DIR__ . '/../public/api/issues.php';
        $output = ob_get_clean();

        $this->assertJson($output);
    }
}