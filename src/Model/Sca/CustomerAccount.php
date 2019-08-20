<?php namespace Solinor\PaymentHighway\Model\Sca;

/**
 * Class CustomerAccount
 *
 * @package Solinor\PaymentHighway\Model\Sca
 */

class CustomerAccount extends \Solinor\PaymentHighway\Model\JsonSerializable
{
    public $account_age_indicator = null;
    public $account_date = null;
    public $change_indicator = null;
    public $change_date = null;
    public $password_change_indicator = null;
    public $password_change_date = null;
    public $number_of_recent_purchases = null;
    public $number_of_add_card_attempts_day = null;
    public $number_of_transaction_activity_day = null;
    public $number_of_transaction_activity_year = null;
    public $shipping_address_indicator = null;
    public $shipping_address_usage_date = null;
    public $suspicious_activity = null;

    /**
     * @param string $account_age_indicator Length of time that the cardholder has had the account.
     * @param string $account_date Date, when the cardholder opened the account at merchant (eg. "2019-07-05")
     * @param string $change_indicator Length of time since the cardholder’s account information was last changed.
     * @param string $change_date Date, when the cardholder’s account was last changed. Including Billing or Shipping address (eg. "2019-07-05")
     * @param string $password_change_indicator Length of time since the cardholder’s account had a password change or account reset.
     * @param string $password_change_date Date, when cardholder’s account with the 3DS Requestor had a password change or account reset. (eg. "2019-07-05")
     * @param int $number_of_recent_purchases Max value: 9999, Number of purchases with this cardholder account during the previous six months.
     * @param int $number_of_add_card_attempts_day Max value: 999, Number of Add Card attempts in the last 24 hours.
     * @param int $number_of_transaction_activity_day Max value: 999, Number of transactions (successful and abandoned) for this cardholder account across all payment accounts in the previous 24 hours.
     * @param int $number_of_transaction_activity_year Max value: 999, Number of transactions (successful and abandoned) for this cardholder account with the 3DS Requestor across all payment accounts in the previous year.
     * @param string $shipping_address_indicator Indicates when the shipping address used for this transaction was first used.
     * @param string $shipping_address_usage_date Date, when the shipping address used for this transaction was first used. (eg. "2019-07-05")
     * @param string $suspicious_activity Indicates whether suspicious activity has been experienced (including previous fraud) on the cardholder account.
     */
    public function __construct(
        $account_age_indicator = null,
        $account_date = null,
        $change_indicator = null,
        $change_date = null,
        $password_change_indicator = null,
        $password_change_date = null,
        $number_of_recent_purchases = null,
        $number_of_add_card_attempts_day = null,
        $number_of_transaction_activity_day = null,
        $number_of_transaction_activity_year = null,
        $shipping_address_indicator = null,
        $shipping_address_usage_date = null,
        $suspicious_activity = null
    ){
        $this->account_age_indicator = $account_age_indicator;
        $this->account_date = $account_date;
        $this->change_indicator = $change_indicator;
        $this->change_date = $change_date;
        $this->password_change_indicator = $password_change_indicator;
        $this->password_change_date = $password_change_date;
        $this->number_of_recent_purchases = $number_of_recent_purchases;
        $this->number_of_add_card_attempts_day = $number_of_add_card_attempts_day;
        $this->number_of_transaction_activity_day = $number_of_transaction_activity_day;
        $this->number_of_transaction_activity_year = $number_of_transaction_activity_year;
        $this->shipping_address_indicator = $shipping_address_indicator;
        $this->shipping_address_usage_date = $shipping_address_usage_date;
        $this->suspicious_activity = $suspicious_activity;
    }
}

/**
 * Length of time that the cardholder has had the account.
 * 01 = No account (guest check-out)
 * 02 = Created during this transaction
 * 03 = Less than 30 days
 * 04 = 30−60 days
 * 05 = More than 60 days
 */
abstract class AccountAgeIndicator {
    const NoAccount = "01";
    const CreatedDuringTransaction = "02";
    const LessThan30Days = "03";
    const Between30And60Days = "04";
    const MoreThan60Days = "05";
}

/**
 * Length of time since the cardholder’s account information was last changed. Including Billing or Shipping address, new payment account, or new user(s) added.
 * 01 = Changed during this transaction
 * 02 = Less than 30 days
 * 03 = 30−60 days
 * 04 = More than 60 days
 */
abstract class AccountInformationChangeIndicator {
    const ChangedDuringTransaction = "01";
    const LessThan30Days = "02";
    const Between30And60Days = "03";
    const MoreThan60Days = "04";
}

/**
 * Length of time since the cardholder’s account had a password change or account reset.
 * 01 = No change
 * 02 = Changed during this transaction
 * 03 = Less than 30 days
 * 04 = 30−60 days
 * 05 = More than 60 days
 */
abstract class AccountPasswordChangeIndicator {
    const NoChange = "01";
    const ChangedDuringTransaction = "02";
    const LessThan30Days = "03";
    const Between30And60Days = "04";
    const MoreThan60Days = "05";
}

/**
 * Indicates when the shipping address used for this transaction was first used.
 * 01 = This transaction
 * 02 = Less than 30 days
 * 03 = 30−60 days
 * 04 = More than 60 days
 */
abstract class ShippingAddressFirstUsedIndicator {
    const ThisTransaction = "01";
    const LessThan30Days = "02";
    const Between30And60Days = "03";
    const MoreThan60Days = "04";
}

/**
 * Indicates whether suspicious activity has been experienced (including previous fraud) on the cardholder account.
 * 01 = No suspicious activity has been observed
 * 02 = Suspicious activity has been observed
 */
abstract class SuspiciousActivityIndicator {
    const NoSuspiciousActivity = "01";
    const SuspiciousActivityObserved = "02";
}