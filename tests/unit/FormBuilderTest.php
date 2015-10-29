<?php namespace Solinor\PaymentHighway\Tests\Unit;

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
}