<?php namespace Solinor\PaymentHighway\Tests\Unit;

use \Solinor\PaymentHighway\PaymentHighwayUtility;
use Solinor\PaymentHighway\Tests\TestBase;

class PaymentHighWayUtilityTest extends TestBase {

    /**
     * @test
     */
    public function dateIsFormattedCorrectly()
    {
        $date = PaymentHighwayUtility::getDate();

        $this->assertRegExp('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $date);
    }

    /**
     *
     * @test
     */
    public function UUIDIsCorrectV4Format()
    {
        $uuid = PaymentHighwayUtility::createRequestId();

        $this->assertRegExp('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $uuid);
    }

    /**
     * @dataProvider provider
     * @test
     * @param array $values
     */
    public function arrayFilteringIsWorkingCorrectly($values, $expectedRows)
    {
        $filtered = PaymentHighwayUtility::parseSphParameters($values);

        $this->assertCount($expectedRows, $filtered);
    }

    /**
     * @return array
     */
    public function provider()
    {
        return array(
            array(
                array(
                    'sph-testi1' => 'arvo1',
                    'sph-testi2' => 'arvo2',
                    'sph-testi3' => 'arvo3',
                    'sph-testi4' => 'arvo4',
                    'testi5' => 'arvo5',
                ),
                4, // expected count of sph rows
            ),
            array(
                array(
                    'sph-testi1' => 'arvo1',
                    'sph-testi2' => 'arvo2',
                    'sph-testi3' => 'arvo3',
                    'testi5' => 'arvo5',
                ),
                3, // expected count of sph rows
            ),
        );
    }

}