<?php

/**
 * Class Unit_FormBuilderTest
 */

class Unit_FormBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     * @test
     */
    public function testAddPaymentCardSuccess($method, $signatureKeyId, $signatureSecret, $account,
                                              $merchant, $baseUrl, $successUrl, $failureUrl,
                                              $cancelUrl, $language
    )
    {
        $formb = new \Solinor\PaymentHighway\FormBuilder(
            $method, $signatureKeyId, $signatureSecret, $account,
            $merchant, $baseUrl, $successUrl, $failureUrl,
            $cancelUrl, $language
        );

        $form = $formb->generateAddCardParameters();



    }


    public function provider()
    {
        return array(
            array(
                'POST',
                'testKey',
                'testSecret',
                'test',
                'test_merchantId',
                'https://v1-hub-staging.sph-test-solinor.com/',
                'https://example.com/success',
                'https://example.com/failure',
                'https://example.com/cancel',
                'FI'
            )
        );
    }
}