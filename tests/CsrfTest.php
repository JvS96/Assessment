<?php

namespace tests;

use Core\Csrf;
use Core\Session;
use PHPUnit\Framework\TestCase;

class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        Session::start();
        $_SESSION = []; // reset session before each test
    }

    public function testGenerateTokenStoresInSession()
    {
        $token = Csrf::generateToken();

        $this->assertNotEmpty($token);
        $this->assertEquals($_SESSION['_csrf'], $token);
    }

    public function testValidateTokenReturnsTrueForValidToken()
    {
        $token = Csrf::generateToken();

        $this->assertTrue(Csrf::validate($token));
    }

    public function testValidateTokenFailsForInvalidToken()
    {
        Csrf::generateToken();

        $this->assertFalse(Csrf::validate('invalid'));
    }
}