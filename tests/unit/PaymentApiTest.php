<?php namespace Solinor\PaymentHighway\Tests\Unit;

use Solinor\PaymentHighway\Model\Card;
use Solinor\PaymentHighway\Model\Request\Transaction;
use Solinor\PaymentHighway\Model\Request\CustomerInitiatedTransaction;
use Solinor\PaymentHighway\Model\Sca\StrongCustomerAuthentication;
use Solinor\PaymentHighway\Model\Sca\ReturnUrls;
use Solinor\PaymentHighway\Model\Sca\CustomerAccount;
use Solinor\PaymentHighway\Model\Sca\AccountAgeIndicator;
use Solinor\PaymentHighway\Model\Sca\AccountInformationChangeIndicator;
use Solinor\PaymentHighway\Model\Sca\AccountPasswordChangeIndicator;
use Solinor\PaymentHighway\Model\Sca\ShippingAddressFirstUsedIndicator;
use Solinor\PaymentHighway\Model\Sca\SuspiciousActivityIndicator;
use Solinor\PaymentHighway\Model\Sca\CustomerDetails;
use Solinor\PaymentHighway\Model\Sca\PhoneNumber;
use Solinor\PaymentHighway\Model\Sca\Purchase;
use Solinor\PaymentHighway\Model\Sca\ShippingIndicator;
use Solinor\PaymentHighway\Model\Sca\DeliveryTimeFrame;
use Solinor\PaymentHighway\Model\Sca\ReorderItemsIndicator;
use Solinor\PaymentHighway\Model\Sca\PreOrderPurchaseIndicator;
use Solinor\PaymentHighway\Model\Sca\ShippingNameIndicator;
use Solinor\PaymentHighway\Model\Sca\Address;
use Solinor\PaymentHighway\Model\Sca\ChallengeWindowSize;
use Solinor\PaymentHighway\Model\Splitting;
use Solinor\PaymentHighway\PaymentApi;
use Solinor\PaymentHighway\Tests\TestBase;

class PaymentApiTest extends TestBase
{

    const ValidExpiryMonth = '11';
    const ValidExpiryYear = '2023';
    const ValidPan = '4153013999700024';
    const ValidCvc = '024';
    const ValidType = 'Visa';

    const SoftDeclineExpiryMonth = '11';
    const SoftDeclineExpiryYear = '2023';
    const SoftDeclinePan = '4153013999701162';
    const SoftDeclineCvc = '162';
    const SoftDeclineType = 'Visa';

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
        $api = new PaymentApi('https://v1-hub-psd2.sph-test-solinor.com/',  'testKey',  'testSecret',  'test',  'test_merchantId');

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

        $card = $this->getValidCardTransactionRequest();

        $response = $api->debitTransaction( $transactionId, $card)->body;

        $this->assertEquals('100', $response->result->code);
        $this->assertEquals('OK', $response->result->message);

        return $transactionId;
    }

    /**
     * @depends      paymentApiExists
     * @depends      debitTransactionSuccess
     * @test
     * @param PaymentApi $api
     * @param string $transactionId
     * @return string transactionId
     */
    public function transactionStatus(PaymentApi $api, $transactionId )
    {

        $card = $this->getValidCardTransactionRequest();

        $api->debitTransaction( $transactionId, $card)->body;

        $response = $api->transactionResult( $transactionId)->body;

        $this->assertEquals('100', $response->result->code);
        $this->assertEquals('OK', $response->result->message);
        $this->assertEquals(true, $response->committed);
        $this->assertEquals(99, $response->committed_amount);

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
     * @depends      paymentApiExists
     * @test
     * @param PaymentApi $api
     * @param string $transactionId
     * @return string transactionId
     */
    public function chargeMerchantInitiatedTransactionSuccess(PaymentApi $api)
    {
        $transactionId = $api->initTransaction()->body->id;

        $card = $this->getValidCardTransactionRequest();

        $response = $api->chargeMerchantInitiatedTransaction( $transactionId, $card)->body;

        $this->assertEquals('100', $response->result->code);
        $this->assertEquals('OK', $response->result->message);

        return $transactionId;
    }

    /**
     * @depends      paymentApiExists
     * @test
     * @param PaymentApi $api
     * @param string $transactionId
     * @return string transactionId
     */
    public function chargeCustomerInitiatedTransactionSuccess(PaymentApi $api)
    {
        $transactionId = $api->initTransaction()->body->id;

        $card = $this->getValidCustomerInitiatedTransactionRequest();

        $response = $api->chargeCustomerInitiatedTransaction( $transactionId, $card)->body;

        $this->assertEquals('100', $response->result->code);
        $this->assertEquals('OK', $response->result->message);

        return $transactionId;
    }

    /**
     * @depends      paymentApiExists
     * @test
     * @param PaymentApi $api
     * @param string $transactionId
     * @return string transactionId
     */
    public function chargeCustomerInitiatedTransactionWithFullScaSuccess(PaymentApi $api)
    {
        $transactionId = $api->initTransaction()->body->id;

        $strongCustomerAuthentication = $this->getFullStrongCustomerAuthentication();

        $card = $this->getValidCustomerInitiatedTransactionRequest($strongCustomerAuthentication);

        $response = $api->chargeCustomerInitiatedTransaction( $transactionId, $card)->body;

        $this->assertEquals('100', $response->result->code);
        $this->assertEquals('OK', $response->result->message);

        return $transactionId;
    }

    /**
     * @depends      paymentApiExists
     * @test
     * @param PaymentApi $api
     * @param string $transactionId
     * @return string transactionId
     */
    public function chargeCustomerInitiatedTransactionSoftDecline(PaymentApi $api)
    {
        $transactionId = $api->initTransaction()->body->id;

        $card = $this->getSoftDeclineCustomerInitiatedTransactionRequest();

        $response = $api->chargeCustomerInitiatedTransaction( $transactionId, $card)->body;

        $this->assertEquals('400', $response->result->code);
        $this->assertNotNull($response->three_d_secure_url);

        return $transactionId;
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
     * @depends      paymentApiExists
     * @test
     * @param PaymentApi $api
     * @return string transactionId
     */
    public function splittingDetailsAreReturnedInTransactionStatus(PaymentApi $api)
    {
        $transactionId = $api->initTransaction()->body->id;

        $subMerchantId = "12345";
        $amountToSubMerchant = 90;

        $splitting = new Splitting($subMerchantId, $amountToSubMerchant);

        $transactionRequest = $this->getValidCardTransactionRequest($splitting);

        $debitResponse = $api->debitTransaction($transactionId, $transactionRequest)->body;

        $this->assertEquals('100', $debitResponse->result->code);

        $statusResponse = $api->statusTransaction($transactionId)->body;

        $this->assertEquals($subMerchantId, $statusResponse->transaction->splitting->merchant_id);
        $this->assertEquals($amountToSubMerchant, $statusResponse->transaction->splitting->amount);

        return $transactionId;
    }

    /**
     * @param Splitting $splitting
     * @return Transaction
     */
    private function getValidCardTransactionRequest($splitting = null)
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
            self::$orderId,
            $splitting
        );
    }

    /**
     * @param Splitting $splitting
     * @return CustomerInitiatedTransaction
     */
    private function getValidCustomerInitiatedTransactionRequest($strongCustomerAuthentication = null)
    {
        if(is_null($strongCustomerAuthentication)) {
            $strongCustomerAuthentication = new StrongCustomerAuthentication(
                new ReturnUrls(
                    "https://example.com/success",
                    "https://example.com/cancel",
                    "https://example.com/failure"
                )
            );
        }

        return new CustomerInitiatedTransaction(
            new Card(
                self::ValidPan,
                self::ValidExpiryYear,
                self::ValidExpiryMonth,
                self::ValidCvc
            ),
            99,
            'EUR',
            $strongCustomerAuthentication,
            true,
            self::$orderId
        );
    }

    /**
     * @param Splitting $splitting
     * @return CustomerInitiatedTransaction
     */
    private function getSoftDeclineCustomerInitiatedTransactionRequest($strongCustomerAuthentication = null)
    {
        if(is_null($strongCustomerAuthentication)) {
            $strongCustomerAuthentication = new StrongCustomerAuthentication(
                new ReturnUrls(
                    "https://example.com/success",
                    "https://example.com/cancel",
                    "https://example.com/failure"
                )
            );
        }

        return new CustomerInitiatedTransaction(
            new Card(
                self::SoftDeclinePan,
                self::SoftDeclineExpiryYear,
                self::SoftDeclineExpiryMonth,
                self::SoftDeclineCvc
            ),
            99,
            'EUR',
            $strongCustomerAuthentication,
            true,
            self::$orderId
        );
    }

    private function getFullStrongCustomerAuthentication() {
        return new StrongCustomerAuthentication(
            new ReturnUrls(
                "https://example.com/success",
                "https://example.com/cancel",
                "https://example.com/failure",
                "https://example.com/webhook/success",
                "https://example.com/webhook/cancel",
                "https://example.com/webhook/failure",
                0
            ),
            new CustomerDetails(
                true,
                "Eric Example",
                "eric.example@example.com",
                new PhoneNumber("358", "123456789"),
                new PhoneNumber("358", "441234566"),
                new PhoneNumber("358", "441234566")
            ),
            new CustomerAccount(
                AccountAgeIndicator::MoreThan60Days,
                "2018-07-05",
                AccountInformationChangeIndicator::MoreThan60Days,
                "2018-09-11",
                AccountPasswordChangeIndicator::NoChange,
                "2018-07-05",
                7,
                1,
                3,
                8,
                ShippingAddressFirstUsedIndicator::Between30And60Days,
                "2019-07-01",
                SuspiciousActivityIndicator::NoSuspiciousActivity
            ),
            new Purchase(
                ShippingIndicator::ShipToCardholdersAddress,
                DeliveryTimeFrame::SameDayShipping,
                "eric.example@example.com",
                ReorderItemsIndicator::FirstTimeOrdered,
                PreOrderPurchaseIndicator::MerchandiseAvailable,
                "2019-08-20",
                ShippingNameIndicator::AccountNameMatchesShippingName
            ),
            new Address(
                "Helsinki",
                "246",
                "Arkadiankatu 1",
                "",
                "",
                "00101",
                "18"
            ),
            new Address(
                "Helsinki",
                "246",
                "Arkadiankatu 1",
                "",
                "",
                "00101",
                "18"
            ),
            ChallengeWindowSize::Window600x400,
            false,
            false
        );
    }
}
