<?php namespace Solinor\PaymentHighway\Tests\Unit\Model;

use Solinor\PaymentHighway\Model\Request\Token;
use Solinor\PaymentHighway\Tests\TestBase;

/**
 * Class TokenTest
 */

class TokenTest extends TestBase
{
    /**
     * @test
     */
    public function tokenReturnsExpectedJson()
    {
        $expectedJson = '{"token":{"id":"sometestid"},"amount":99,"currency":"EUR"}';

        $token = new Token(99,'EUR','sometestid');

        $this->assertEquals($expectedJson, $token->toJson());

    }
}