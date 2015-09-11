<?php

/**
 * Class PaymentApiTest
 */

use Solinor\PaymentHighway\PaymentApi;

class PaymentApiTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        // set timezone to UTC-0
        date_default_timezone_set('UTC');
    }

    /**
     * @dataProvider provider
     * @test
     */
    public function initHandlerSuccessfully($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant)
    {
        $api = new PaymentApi($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant);

        $jsonresponse = $api->initTransaction()->body;

        $this->assertRegExp('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $jsonresponse->id);
        $this->assertEquals(100, $jsonresponse->result->code);
        $this->assertEquals('OK', $jsonresponse->result->message);
    }


    /**
     * testdata provider for paymentapi tests
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array(
                'https://v1-hub-staging.sph-test-solinor.com/',
                'testKey',
                'testSecret',
                'test',
                'test_merchantId'
            )
        );
    }
}