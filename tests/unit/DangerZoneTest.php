<?php

/**
 * Class DangerZoneTest
 */

use Solinor\PaymentHighway\Dangerzone;

class DangerZoneTest extends PHPUnit_Framework_TestCase
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
    public function initTokenizationFailsIfPciDssDisabled($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant)
    {
        $this->setExpectedException('Exception');

        $api = new DangerZone($serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant);

        $jsonresponse = $api->tokenInit()->body;
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
        $this->setExpectedException('Exception');

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
        $receivedId = $api->tokenize($api->tokenInit(), '', '','', '', '');

        $this->assertRegExp('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $receivedId);
    }

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