<?php namespace Solinor\PaymentHighway\Tests\Unit\Model;

use Solinor\PaymentHighway\Model\Token;
use Solinor\PaymentHighway\Model\Request\Transaction;
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
        $expectedJson = '{"amount":99,"currency":"EUR","blocking":true,"token":{"id":"sometestid"}}';

        $token = new Token('sometestid');

        $request = new Transaction($token, 99 , 'EUR');

        $this->assertEquals($expectedJson, json_encode($request));

    }
}