<?php namespace Solinor\PaymentHighway\Tests\Unit;

use Solinor\PaymentHighway\Model\Card;
use Solinor\PaymentHighway\Model\Request\Transaction;
use Solinor\PaymentHighway\PaymentApi;
use Solinor\PaymentHighway\Tests\TestBase;

class PaymentApiTest extends TestBase
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

    protected static $orderId;

    /**
     * setup uniqid for each test!
     */
    public static function setupBeforeClass(){
        self::$orderId = uniqid();
    }

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
        $this->assertEquals('100', $jsonresponse->result->code);
        $this->assertEquals('OK', $jsonresponse->result->message);

        return $jsonresponse->id;
    }

    /**
     * @depends      paymentApiExists
     * @depends      initHandlerSuccessfully
     * @test
     * @param PaymentApi $api
     * @param string $transactionId
     * @return string transactionId
     */
    public function debitTransactionSuccess(PaymentApi $api, $transactionId )
    {

        $card = $this->getValidCard();

        $response = $api->debitTransaction( $transactionId, $card)->body;

        $this->assertEquals('100', $response->result->code);
        $this->assertEquals('OK', $response->result->message);

        return $transactionId;
    }

    /**
     * @test
     * @depends     paymentApiExists
     * @depends     debitTransactionSuccess
     *
     * @param PaymentApi $api
     */
    public function searchByOrderIdSuccess(PaymentApi $api){

        $response = $api->searchByOrderId(self::$orderId)->body;

        $this->assertCount(1, $response->transactions);
        $this->assertEquals('100', $response->result->code);
        $this->assertEquals('OK', $response->result->message);

    }

    /**
     * @depends      paymentApiExists
     * @depends      debitTransactionSuccess
     * @test
     * @param PaymentApi $api
     * @param string $transactionId
     */
    public function transactionStatusAfterDebit(PaymentApi $api, $transactionId)
    {
        $response = $api->statusTransaction($transactionId)->body;

        $this->assertEquals('4000', $response->transaction->status->code);
        $this->assertEquals('100', $response->result->code);
        $this->assertEquals('OK', $response->result->message);
    }

    /**
     * @depends paymentApiExists
     * @depends debitTransactionSuccess
     * @depends transactionStatusAfterDebit
     * @test
     *
     * @param PaymentApi $api
     * @param $transactionId
     */
    public function revertTransactionSuccess(PaymentApi $api, $transactionId)
    {
        $response = $api->revertTransaction($transactionId, 99)->body;

        $this->assertEquals('100', $response->result->code);
        $this->assertEquals('OK', $response->result->message);

        return $transactionId;
    }

    /**
     * @depends      paymentApiExists
     * @depends      revertTransactionSuccess
     * @test
     * @param PaymentApi $api
     * @param string $transactionId
     */
    public function transactionStatusAfterRevert(PaymentApi $api, $transactionId)
    {
        $response = $api->statusTransaction($transactionId)->body;

        $this->assertEquals('5700', $response->transaction->status->code);
        $this->assertEquals('100', $response->result->code);
        $this->assertEquals('OK', $response->result->message);
    }

    /**
     * @test
     * @depends paymentApiExists
     */
    public function getReportSuccess( PaymentApi $api )
    {
        date_default_timezone_set('UTC');
        $date = date('Ymd');

        $response = $api->getReport($date)->body;

        $this->assertEquals('100', $response->result->code);
        $this->assertEquals('OK', $response->result->message);

    }

    /**
     * @return Card
     */
    private function getValidCard()
    {
        return new Transaction(
            new Card(
                self::ValidPan,
                self::ValidExpiryYear,
                self::ValidExpiryMonth,
                self::ValidCvc
            ),
            99,
            'EUR',
            true,
            self::$orderId
        );
    }
}