<?php
/**
 * Class DangerZoneTest
 */

use Solinor\PaymentHighway\Dangerzone;
use Solinor\PaymentHighway\Exception\PciDssDisabledException;
use Solinor\PaymentHighway\Exception\ServerCouldNotInitializeTokenException;
use Solinor\PaymentHighway\Exception\ServerCouldNotReturnTokenException;
use Solinor\PaymentHighway\Exception\ServerCouldNotTokenizeCardDataException;
use Solinor\PaymentHighway\Model\Token;

class DangerZoneTest extends PHPUnit_Framework_TestCase
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

    public function setUp()
    {
        // set timezone to UTC-0
        date_default_timezone_set('UTC');
    }

    /**
     * @dataProvider provider
     * @test
     */
    public function initTokenizationFailsIfPciDssDisabled($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant)
    {
        $this->setExpectedException('Solinor\PaymentHighway\Exception\PciDssDisabledException');

        $api = new DangerZone($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant);

        $api->tokenInit()->body;
    }

    /**
     * @dataProvider provider
     * @test
     */
    public function initTokenizationSuccessfully($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant)
    {
        $api = new DangerZone($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant);
        $api->enablePciDss();
        $receivedId = $api->tokenInit();

        $this->assertRegExp('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $receivedId);
    }

    /**
     * @dataProvider invalidProvider
     * @test
     */
    public function initTokenizationFail($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant)
    {
        $this->setExpectedException('Solinor\PaymentHighway\Exception\ServerCouldNotInitializeTokenException');

        $api = new DangerZone($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant);
        $api->enablePciDss();
        $receivedId = $api->tokenInit();
    }

    /**
     * @dataProvider provider
     * @test
     */
    public function tokenizationSuccessfully($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant)
    {
        $api = new DangerZone($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant);
        $api->enablePciDss();
        $response = $api->tokenize(
            $api->tokenInit(),
            $this::ValidExpiryMonth,
            $this::ValidExpiryYear,
            $this::ValidCvc,
            $this::ValidPan
        );

        $this->assertTrue($response);
    }

    /**
     * @dataProvider provider
     * @test
     */
    public function tokenizationFail($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant)
    {
        $api = new DangerZone($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant);
        $api->enablePciDss();

        $this->setExpectedException('Solinor\PaymentHighway\Exception\ServerCouldNotTokenizeCardDataException');

        $response = $api->tokenize(
            $api->tokenInit(),
            $this::InvalidExpiryMonth,
            $this::InvalidExpiryYear,
            $this::InvalidCvc,
            $this::InvalidPan
        );
    }

    /**
     * @dataProvider provider
     * @test
     */
    public function getTokenSuccessfully($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant)
    {
        $api = new DangerZone($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant);
        $api->enablePciDss();

        $tokenizationId = $api->tokenInit();

        $api->tokenize(
            $tokenizationId,
            $this::ValidExpiryMonth,
            $this::ValidExpiryYear,
            $this::ValidCvc,
            $this::ValidPan
        );

        $response = $api->getToken($tokenizationId);

        $this->assertInstanceOf('Solinor\PaymentHighway\Model\Token', $response);
        $this->assertNotEmpty($response->cardToken);
        $this->assertEquals($this::ValidType, $response->type);
        $this->assertEquals(substr($this::ValidPan, -4), $response->partialPan);
        $this->assertEquals($this::ValidExpiryYear, $response->expireYear);
        $this->assertEquals($this::ValidExpiryMonth, $response->expireMonth);
    }

    /**
     *
     * This test is not done since I am not sure if we can simulate that tokenisation returns ok, but getToken fails..
     *
     * public function getTokenFail($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant)
     * {
     *   $this->setExpectedException('Solinor\PaymentHighway\Exception\ServerCouldNotReturnTokenException');
     *
     *   $api = new DangerZone($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant);
     *   $api->enablePciDss();
     *   $response = $api->tokenize(...);
     *   );
     * }
     */

    /**
     * testdata provider for dangerozone tests
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

    /**
     * invalid testdata provider for dangerzone tests
     *
     * @return array
     */
    public function invalidProvider()
    {
        return array(
            array(
                'https://v1-hub-staging.sph-test-solinor.com/',
                'testKeyInvalid',
                'testSecret',
                'testInvalid',
                'test_merchantId'
            )
        );
    }
}