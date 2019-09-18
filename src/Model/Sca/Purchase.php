<?php namespace Solinor\PaymentHighway\Model\Sca;

/**
 * Class Purchase
 *
 * @package Solinor\PaymentHighway\Model\Sca
 */

class Purchase extends \Solinor\PaymentHighway\Model\JsonSerializable
{
    public $shipping_indicator = null;
    public $delivery_time_frame = null;
    public $delivery_email = null;
    public $reorder_items_indicator = null;
    public $pre_order_purchase_indicator = null;
    public $pre_order_date = null;
    public $shipping_name_indicator = null;

    /**
     * @param string $shipping_indicator
     * @param string $delivery_time_frame
     * @param string $delivery_email max length 254
     * @param string $reorder_items_indicator
     * @param string $pre_order_purchase_indicator
     * @param string $pre_order_date For a pre-ordered purchase, the expected date that the merchandise will be available
     * @param string $shipping_name_indicator
     */
    public function __construct(
        $shipping_indicator = null,
        $delivery_time_frame = null,
        $delivery_email = null,
        $reorder_items_indicator = null,
        $pre_order_purchase_indicator = null,
        $pre_order_date = null,
        $shipping_name_indicator = null
    ){
        $this->shipping_indicator = $shipping_indicator;
        $this->delivery_time_frame = $delivery_time_frame;
        $this->delivery_email = $delivery_email;
        $this->reorder_items_indicator = $reorder_items_indicator;
        $this->pre_order_purchase_indicator = $pre_order_purchase_indicator;
        $this->pre_order_date = $pre_order_date;
        $this->shipping_name_indicator = $shipping_name_indicator;
    }
}

/**
 * 01 = Ship to cardholder’s billing address,
 * 02 = Ship to another verified address on file with merchant
 * 03 = Ship to address that is different than the cardholder’s billing address
 * 04 = “Ship to Store” / Pick-up at local store (Store address shall be populated in shipping address fields)
 * 05 = Digital goods (includes online services, electronic gift cards and redemption codes)
 * 06 = Travel and Event tickets, not shipped
 * 07 = Other (for example, Gaming, digital services not shipped, emedia subscriptions, etc.)
 */
abstract class ShippingIndicator {
    const ShipToCardholdersAddress = "01";
    const ShipToVerifiedAddress = "02";
    const ShipToDifferentAddress = "03";
    const ShipToStore = "04";
    const DigitalGoods = "05";
    const TravelAndEventTickets = "06";
    const Other = "07";
}

/**
 * Indicates the merchandise delivery timeframe.
 * 01 = Electronic Delivery
 * 02 = Same day shipping
 * 03 = Overnight shipping
 * 04 = Two-day or more shipping
 */
abstract class DeliveryTimeFrame {
    const ElectronicDelivery = "01";
    const SameDayShipping = "02";
    const OvernightShipping = "03";
    const TwoDarOrMoreShipping= "04";
}

abstract class ReorderItemsIndicator {
    const FirstTimeOrdered = "01";
    const Reorder = "02";
}

abstract class PreOrderPurchaseIndicator {
    const MerchandiseAvailable = "01";
    const FutureAvailability = "02";
}

/**
 * 01 = Account Name identical to shipping Name
 * 02 = Account Name different than shipping Name
 */
abstract class ShippingNameIndicator {
    const AccountNameMatchesShippingName = "01";
    const AccountNameDifferentThanShippingName= "02";
}