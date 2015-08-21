<?php namespace Solinor\PaymentHighway;

use Rhumsaa\Uuid\Uuid;


class PaymentHighwayUtility {

    /**
     * @return bool|string
     */
    public static function getDate()
    {
        return date('Y-m-d\TH:m:s\Z');
    }

    /**
     * Generate a pseudo random v4 UUID
     * @return string|UUID
     */
    public static function createRequestId()
    {
        return Uuid::uuid4()->toString();
    }

    public static function parseSphParameters($nameValuePairs)
    {

        foreach($nameValuePairs as $key => $value)
        {
            strpos($key, 'sph') === 0 ? $filtered[$key] = $value : null;
        }

        return $filtered;
    }
}