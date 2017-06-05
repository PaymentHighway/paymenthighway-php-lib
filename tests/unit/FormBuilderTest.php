<?php namespace Solinor\PaymentHighway\Tests\Unit;

use Ramsey\Uuid\Uuid;
use Solinor\PaymentHighway\Tests\TestBase;
use Solinor\PaymentHighway;

class FormBuilderTest extends TestBase
{

    /**
     * @dataProvider addPaymentCardParameters
     * @test
     */
    public function addPaymentCard($method, $signatureKeyId, $signatureSecret, $account,
                                   $merchant, $baseUrl, $successUrl, $failureUrl,
                                   $cancelUrl, $language
    )
    {
        $formbuilder = new \Solinor\PaymentHighway\FormBuilder(
            $method, $signatureKeyId, $signatureSecret, $account,
            $merchant, $baseUrl, $successUrl, $failureUrl,
            $cancelUrl, $language
        );

        $form = $formbuilder->generateAddCardParameters();

        $this->assertInstanceOf('\Solinor\PaymentHighway\Model\Form', $form);
        $this->assertEquals($baseUrl . '/form/view/add_card', $form->getAction());
        $this->assertEquals($method, $form->getMethod());
        $this->assertCount(9, $form->getParameters());

    }

    /**
     * @dataProvider payWithCardParameters
     * @test
     */
    public function PaymentParameters($method, $signatureKeyId, $signatureSecret, $account,
                                      $merchant, $baseUrl, $successUrl, $failureUrl,
                                      $cancelUrl, $language, $amount, $currency, $orderId, $description,
                                      $showPaymentSelector
    )
    {
        $formbuilder = new \Solinor\PaymentHighway\FormBuilder(
            $method, $signatureKeyId, $signatureSecret, $account,
            $merchant, $baseUrl, $successUrl, $failureUrl,
            $cancelUrl, $language
        );

        $form = $formbuilder->generatePaymentParameters($amount, $currency, $orderId, $description, null,
            null, null, null, $showPaymentSelector);

        $this->assertInstanceOf('\Solinor\PaymentHighway\Model\Form', $form);
        $this->assertEquals($baseUrl . '/form/view/pay_with_card', $form->getAction());
        $this->assertEquals($method, $form->getMethod());
        $this->assertCount(14, $form->getParameters());
    }

    /**
     * @dataProvider payWithCardParameters
     * @test
     */
    public function addCardAndPayParameters($method, $signatureKeyId, $signatureSecret, $account,
                                            $merchant, $baseUrl, $successUrl, $failureUrl,
                                            $cancelUrl, $language, $amount, $currency, $orderId, $description
    )
    {
        $formbuilder = new \Solinor\PaymentHighway\FormBuilder(
            $method, $signatureKeyId, $signatureSecret, $account,
            $merchant, $baseUrl, $successUrl, $failureUrl,
            $cancelUrl, $language
        );

        $form = $formbuilder->generateAddCardAndPaymentParameters($amount, $currency, $orderId, $description);

        $this->assertInstanceOf('\Solinor\PaymentHighway\Model\Form', $form);
        $this->assertEquals($baseUrl . '/form/view/add_and_pay_with_card', $form->getAction());
        $this->assertEquals($method, $form->getMethod());
        $this->assertCount(13, $form->getParameters());
    }

    /**
     * @dataProvider payWithCvcAndTokenParameters
     * @test
     */
    public function payWithCvcAndToken($method, $signatureKeyId, $signatureSecret,
                                       $account, $merchant, $baseUrl,
                                       $successUrl, $failureUrl, $cancelUrl,
                                       $language, $tokenId, $amount,
                                       $currency, $orderId, $description
    )
    {
        $formbuilder = new \Solinor\PaymentHighway\FormBuilder(
            $method, $signatureKeyId, $signatureSecret, $account,
            $merchant, $baseUrl, $successUrl, $failureUrl,
            $cancelUrl, $language
        );

        $form = $formbuilder->generatePayWithTokenAndCvcParameters($tokenId, $amount, $currency, $orderId, $description);

        $this->assertInstanceOf('\Solinor\PaymentHighway\Model\Form', $form);
        $this->assertEquals($baseUrl . '/form/view/pay_with_token_and_cvc', $form->getAction());
        $this->assertEquals($method, $form->getMethod());
        $this->assertCount(13, $form->getParameters());
        $this->assertArrayHasKey('sph-token', $form->getParameters());
    }

    /**
     * @dataProvider payWithMobilePayParameters
     * @test
     */
    public function payWithMobilePay($method, $signatureKeyId, $signatureSecret,
                                     $account, $merchant, $baseUrl,
                                     $successUrl, $failureUrl, $cancelUrl,
                                     $language, $amount,
                                     $currency, $orderId, $description
    )
    {
        $formbuilder = new \Solinor\PaymentHighway\FormBuilder(
            $method, $signatureKeyId, $signatureSecret, $account,
            $merchant, $baseUrl, $successUrl, $failureUrl,
            $cancelUrl, $language
        );

        $form = $formbuilder->generatePayWithMobilePayParameters($amount, $currency, $orderId, $description);

        $this->assertInstanceOf('\Solinor\PaymentHighway\Model\Form', $form);
        $this->assertEquals($baseUrl . '/form/view/mobilepay', $form->getAction());
        $this->assertEquals($method, $form->getMethod());
        $this->assertCount(12, $form->getParameters());
    }

    /**
     * @dataProvider payWithMobilePayWithOptionalParametersParameters
     * @test
     */
    public function payWithMobilePayWithOptionalParameters($method, $signatureKeyId, $signatureSecret,
                                                           $account, $merchant, $baseUrl, $successUrl, $failureUrl, $cancelUrl,
                                                           $language, $amount, $currency, $orderId, $description, $shopLogoUrl,
                                                           $phoneNumber, $shopName, $subMerchantId, $subMerchantName
    )
    {
        $formbuilder = new \Solinor\PaymentHighway\FormBuilder(
            $method, $signatureKeyId, $signatureSecret, $account,
            $merchant, $baseUrl, $successUrl, $failureUrl,
            $cancelUrl, $language
        );

        $form = $formbuilder->generatePayWithMobilePayParameters($amount, $currency, $orderId, $description, null,
            $shopLogoUrl, $phoneNumber, $shopName, $subMerchantId, $subMerchantName);

        $this->assertInstanceOf('\Solinor\PaymentHighway\Model\Form', $form);
        $this->assertEquals($baseUrl . '/form/view/mobilepay', $form->getAction());
        $this->assertEquals($method, $form->getMethod());
        $this->assertCount(17, $form->getParameters());
    }

    /**
     * @dataProvider payWithMasterpassParameters
     * @test
     */
    public function paymentWithMasterpass($method, $signatureKeyId, $signatureSecret, $account,
                                          $merchant, $baseUrl, $successUrl, $failureUrl,
                                          $cancelUrl, $language, $amount, $currency, $orderId, $description
    )
    {
        $formbuilder = new \Solinor\PaymentHighway\FormBuilder(
            $method, $signatureKeyId, $signatureSecret, $account,
            $merchant, $baseUrl, $successUrl, $failureUrl,
            $cancelUrl, $language
        );

        $form = $formbuilder->generateMasterpassParameters($amount, $currency, $orderId, $description);

        $this->assertInstanceOf('\Solinor\PaymentHighway\Model\Form', $form);
        $this->assertEquals($baseUrl . '/form/view/masterpass', $form->getAction());
        $this->assertEquals($method, $form->getMethod());
        $this->assertCount(13, $form->getParameters());
    }

    /**
     * @dataProvider addPaymentCardWebhookParameters
     * @test
     */
    public function addPaymentCardWithWebhook($method, $signatureKeyId, $signatureSecret, $account,
                                              $merchant, $baseUrl, $successUrl, $failureUrl,
                                              $cancelUrl, $language, $webhookSuccessUrl, $webhookFailureUrl, $webhookCancelUrl,
                                              $webhookDelay
    )
    {
        $formbuilder = new \Solinor\PaymentHighway\FormBuilder(
            $method, $signatureKeyId, $signatureSecret, $account,
            $merchant, $baseUrl, $successUrl, $failureUrl,
            $cancelUrl, $language
        );

        $form = $formbuilder->generateAddCardParameters(null, null, null, null, null, $webhookSuccessUrl, $webhookFailureUrl,
            $webhookCancelUrl, $webhookDelay);
        $this->validateWebhookParameters($form->getParameters());
    }

    /**
     * @dataProvider payWithCardWebhookParameters
     * @test
     */
    public function paymentParametersWithWebhook($method, $signatureKeyId, $signatureSecret, $account,
                                                 $merchant, $baseUrl, $successUrl, $failureUrl,
                                                 $cancelUrl, $language, $amount, $currency, $orderId, $description,
                                                 $skipPaymentSelector,$webhookSuccessUrl, $webhookFailureUrl,
                                                 $webhookCancelUrl, $webhookDelay
    )
    {
        $formbuilder = new \Solinor\PaymentHighway\FormBuilder(
            $method, $signatureKeyId, $signatureSecret, $account,
            $merchant, $baseUrl, $successUrl, $failureUrl,
            $cancelUrl, $language
        );

        $form = $formbuilder->generatePaymentParameters($amount, $currency, $orderId, $description, null, null, null, null,
            $webhookSuccessUrl, $webhookFailureUrl, $webhookCancelUrl, $webhookDelay);

        $this->validateWebhookParameters($form->getParameters());
    }

    /**
     * @dataProvider payWithCardWebhookParameters
     * @test
     */
    public function addCardAndPayWithWebhookParameters($method, $signatureKeyId, $signatureSecret, $account,
                                                       $merchant, $baseUrl, $successUrl, $failureUrl,
                                                       $cancelUrl, $language, $amount, $currency, $orderId, $description,
                                                       $skipPaymentSelector,$webhookSuccessUrl, $webhookFailureUrl,
                                                       $webhookCancelUrl, $webhookDelay
    )
    {
        $formbuilder = new \Solinor\PaymentHighway\FormBuilder(
            $method, $signatureKeyId, $signatureSecret, $account,
            $merchant, $baseUrl, $successUrl, $failureUrl,
            $cancelUrl, $language
        );

        $form = $formbuilder->generateAddCardAndPaymentParameters($amount, $currency, $orderId, $description, null, null, null, null,
            $webhookSuccessUrl, $webhookFailureUrl, $webhookCancelUrl, $webhookDelay);

        $this->validateWebhookParameters($form->getParameters());
    }

    /**
     * @dataProvider payWithCvcAndTokenWebhookParameters
     * @test
     */
    public function payWithCvcAndTokenWithWebhookParameters($method, $signatureKeyId, $signatureSecret,
                                                            $account, $merchant, $baseUrl,
                                                            $successUrl, $failureUrl, $cancelUrl,
                                                            $language, $tokenId, $amount,
                                                            $currency, $orderId, $description,
                                                            $webhookSuccessUrl, $webhookFailureUrl, $webhookCancelUrl, $webhookDelay
    )
    {
        $formbuilder = new \Solinor\PaymentHighway\FormBuilder(
            $method, $signatureKeyId, $signatureSecret, $account,
            $merchant, $baseUrl, $successUrl, $failureUrl,
            $cancelUrl, $language
        );

        $form = $formbuilder->generatePayWithTokenAndCvcParameters($tokenId, $amount, $currency, $orderId, $description,
            null, null, null, null, $webhookSuccessUrl, $webhookFailureUrl, $webhookCancelUrl, $webhookDelay);

        $this->validateWebhookParameters($form->getParameters());
    }

    /**
     * @dataProvider payWithMobilePayWebhookParameters
     * @test
     */
    public function payWithMobilePayWithWebhook($method, $signatureKeyId, $signatureSecret,
                                                $account, $merchant, $baseUrl,
                                                $successUrl, $failureUrl, $cancelUrl,
                                                $language, $amount,
                                                $currency, $orderId, $description,
                                                $webhookSuccessUrl, $webhookFailureUrl, $webhookCancelUrl, $webhookDelay
    )
    {
        $formbuilder = new \Solinor\PaymentHighway\FormBuilder(
            $method, $signatureKeyId, $signatureSecret, $account,
            $merchant, $baseUrl, $successUrl, $failureUrl,
            $cancelUrl, $language
        );

        $form = $formbuilder->generatePayWithMobilePayParameters($amount, $currency, $orderId, $description, null, null,
            null, null, null, null, $webhookSuccessUrl, $webhookFailureUrl, $webhookCancelUrl, $webhookDelay);

        $this->validateWebhookParameters($form->getParameters());
    }

    /**
     * @dataProvider payWithMasterpassWebhookParameters
     * @test
     */
    public function PaymentWithMasterpassWithWebhook($method, $signatureKeyId, $signatureSecret, $account,
                                                     $merchant, $baseUrl, $successUrl, $failureUrl,
                                                     $cancelUrl, $language, $amount, $currency, $orderId, $description,
                                                     $skipPaymentSelector,$webhookSuccessUrl, $webhookFailureUrl,
                                                     $webhookCancelUrl, $webhookDelay
    )
    {
        $formbuilder = new \Solinor\PaymentHighway\FormBuilder(
            $method, $signatureKeyId, $signatureSecret, $account,
            $merchant, $baseUrl, $successUrl, $failureUrl,
            $cancelUrl, $language
        );

        $form = $formbuilder->generateMasterpassParameters($amount, $currency, $orderId, $description, null, null, null, null,
            $webhookSuccessUrl, $webhookFailureUrl, $webhookCancelUrl, $webhookDelay);

        $this->validateWebhookParameters($form->getParameters());
    }

    /**
     * @param array $parameters
     */
    private function validateWebhookParameters(array $parameters)
    {
        $webhookParameters = $this->getWebhookParametersArray();
        $this->assertEquals($webhookParameters[0], $parameters[PaymentHighway\FormBuilder::$SPH_WEBHOOK_SUCCESS_URL]);
        $this->assertEquals($webhookParameters[1], $parameters[PaymentHighway\FormBuilder::$SPH_WEBHOOK_FAILURE_URL]);
        $this->assertEquals($webhookParameters[2], $parameters[PaymentHighway\FormBuilder::$SPH_WEBHOOK_CANCEL_URL]);
        $this->assertEquals($webhookParameters[3], $parameters[PaymentHighway\FormBuilder::$SPH_WEBHOOK_DELAY]);
    }


    /**
     * @return array
     */
    public function addPaymentCardParameters()
    {
        return array(
            array(
                'POST',
                'testKey',
                'testSecret',
                'test',
                'test_merchantId',
                'https://v1-hub-staging.sph-test-solinor.com',
                'https://example.com/success',
                'https://example.com/failure',
                'https://example.com/cancel',
                'FI'
            )
        );
    }

    /**
     * @return array
     */
    public function payWithCardParameters()
    {
        return array(
            array(
                'POST',
                'testKey',
                'testSecret',
                'test',
                'test_merchantId',
                'https://v1-hub-staging.sph-test-solinor.com',
                'https://example.com/success',
                'https://example.com/failure',
                'https://example.com/cancel',
                'FI',
                '100',
                'EUR',
                '123',
                'testitilaus',
                false
            )
        );
    }

    /**
     * @return array
     */
    public function payWithCvcAndTokenParameters()
    {
        return array(
            array(
                'POST',
                'testKey',
                'testSecret',
                'test',
                'test_merchantId',
                'https://v1-hub-staging.sph-test-solinor.com',
                'https://example.com/success',
                'https://example.com/failure',
                'https://example.com/cancel',
                'FI',
                Uuid::uuid4()->toString(),
                '100',
                'EUR',
                '123',
                'testitilaus'
            )
        );
    }

    /**
     * @return array
     */
    public function payWithMobilePayParameters()
    {
        return array(
            array(
                'POST',
                'testKey',
                'testSecret',
                'test',
                'test_merchantId',
                'https://v1-hub-staging.sph-test-solinor.com',
                'https://example.com/success',
                'https://example.com/failure',
                'https://example.com/cancel',
                'FI',
                '100',
                'EUR',
                '123',
                'testitilaus'
            )
        );
    }

    /**
     * @return array
     */
    public function payWithMobilePayWithOptionalParametersParameters()
    {
        return array(
            array(
                'POST',
                'testKey',
                'testSecret',
                'test',
                'test_merchantId',
                'https://v1-hub-staging.sph-test-solinor.com',
                'https://example.com/success',
                'https://example.com/failure',
                'https://example.com/cancel',
                'FI',
                '100',
                'EUR',
                '123',
                'testitilaus',
                'https://foo.bar/biz.png',
                '+3581234567',
                'Jaakon solki',
                'subMerchantId',
                'subMerchantName'
            )
        );
    }

    /**
     * @return array
     */
    public function payWithMasterpassParameters()
    {
        return $this->payWithCardParameters();
    }

    /**
     * @return array
     */
    public function addPaymentCardWebhookParameters()
    {
        $paymentCardParameters = $this->addPaymentCardParameters();
        return array(
            array_merge(
                $paymentCardParameters[0],
                $this->getWebhookParametersArray()
            )
        );
    }

    /**
     * @return array
     */
    public function payWithCardWebhookParameters()
    {
        $paymentCardParameters = $this->payWithCardParameters();
        return array(
            array_merge(
                $paymentCardParameters[0],
                $this->getWebhookParametersArray()
            )
        );
    }

    /**
     * @return array
     */
    public function payWithCvcAndTokenWebhookParameters()
    {
        $paymentCardParameters = $this->payWithCvcAndTokenParameters();
        return array(
            array_merge(
                $paymentCardParameters[0],
                $this->getWebhookParametersArray()
            )
        );
    }

    /**
     * @return array
     */
    public function payWithMobilePayWebhookParameters()
    {
        $paymentCardParameters = $this->payWithMobilePayParameters();
        return array(
            array_merge(
                $paymentCardParameters[0],
                $this->getWebhookParametersArray()
            )
        );
    }

    /**
     * @return array
     */
    public function payWithMasterpassWebhookParameters()
    {
        $paymentCardParameters = $this->payWithMasterpassParameters();
        return array(
            array_merge(
                $paymentCardParameters[0],
                $this->getWebhookParametersArray()
            )
        );
    }

    private function getWebhookParametersArray()
    {
        return array(
            'http://example.com/?q=success',
            'http://example.com/?q=failure',
            'http://example.com/?q=cancel',
            0
        );
    }
}
