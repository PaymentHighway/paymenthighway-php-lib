<?php

/**
 * Class PaymentApiTest
 */

use Solinor\PaymentHighway\PaymentApi;

class PaymentApiTest extends PHPUnit_Framework_TestCase
{

    const ValidExpiryMonth = '11';
    const ValidExpiryYear = '2017';
    const ValidPan = '4153013999700024';
    const ValidCvc = '024';
    const ValidType = 'Visa';

    const InvalidExpiryMonth = '10';
    const InvalidExpiryYear = '2014';
    const InvalidPan = '415301399900024';
    const InvalidCvc = '022';

    /**
     * @test
     * @return PaymentApi
     */
    public function paymentApiExists()
    {
        $api = new PaymentApi('https://v1-hub-staging.sph-test-solinor.com/',  'testKey',  'testSecret',  'test',  'test_merchantId');

        $this->assertInstanceOf('Solinor\PaymentHighway\PaymentApi',$api);

        return $api;
    }

    /**
     *
     * @depends paymentApiExists
     * @test
     *
     * @param PaymentApi $api
     * @return string
     */
    public function initHandlerSuccessfully( PaymentApi $api )
    {

        $jsonresponse = $api->initTransaction()->body;

        $this->assertRegExp('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $jsonresponse->id);
        $this->assertEquals(100, $jsonresponse->result->code);
        $this->assertEquals('OK', $jsonresponse->result->message);

        return $jsonresponse->id;
    }

    /**
     * @depends      paymentApiExists
     * @depends      initHandlerSuccessfully
     * @test
     * @param PaymentApi $api
     * @param string $transactionId
     */
    public function debitTransactionSuccess(PaymentApi $api, $transactionId )
    {
        $response = $api->debitTransaction( $transactionId, $this->getValidCard(), 99, 'EUR')->body;

        $this->assertEquals("100", $response->result->code);
        $this->assertEquals("OK", $response->result->message);
    }

    /**
     * @return \Solinor\PaymentHighway\Model\Request\Card
     */
    private function getValidCard()
    {
        return new \Solinor\PaymentHighway\Model\Request\Card(
            self::ValidPan,
            self::ValidExpiryYear,
            self::ValidExpiryMonth,
            self::ValidCvc
        );
    }
}