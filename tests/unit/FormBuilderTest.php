<?php namespace Solinor\PaymentHighway\Tests\Unit;

use Rhumsaa\Uuid\Uuid;
use Solinor\PaymentHighway\Tests\TestBase;

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
                                      $cancelUrl, $language, $amount, $currency, $orderId, $description
    )
    {
        $formbuilder = new \Solinor\PaymentHighway\FormBuilder(
            $method, $signatureKeyId, $signatureSecret, $account,
            $merchant, $baseUrl, $successUrl, $failureUrl,
            $cancelUrl, $language
        );

        $form = $formbuilder->generatePaymentParameters($amount, $currency, $orderId, $description);

        $this->assertInstanceOf('\Solinor\PaymentHighway\Model\Form', $form);
        $this->assertEquals($baseUrl . '/form/view/pay_with_card', $form->getAction());
        $this->assertEquals($method, $form->getMethod());
        $this->assertCount(13, $form->getParameters());
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
        $this->assertCount(16, $form->getParameters());
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
                'testitilaus'
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
}
