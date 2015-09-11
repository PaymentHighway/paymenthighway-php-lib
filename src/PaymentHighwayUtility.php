<?php namespace Solinor\PaymentHighway;

use Rhumsaa\Uuid\Uuid;


class PaymentHighwayUtility {

    /**
     * @return bool|string
     */
    public static function getDate()
    {
        return date('Y-m-d\TH:i:s\Z');
    }

    /**
     * Generate a pseudo random v4 UUID
     * @return string|UUID
     */
    public static function createRequestId()
    {
        return Uuid::uuid4()->toString();
    }

    /**
     * @param array $nameValuePairs
     * @return mixed
     */
    public static function parseSphParameters(array $nameValuePairs)
    {

        foreach($nameValuePairs as $key => $value)
        {
            strpos($key, 'sph') === 0 ? $filtered[$key] = $value : null;
        }

        return $filtered;
    }
}