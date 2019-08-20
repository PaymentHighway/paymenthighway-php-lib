<?php namespace Solinor\PaymentHighway\Model\Sca;

/**
 * Class ReturnUrls
 *
 * @package Solinor\PaymentHighway\Model\Sca
 */

class ReturnUrls extends \Solinor\PaymentHighway\Model\JsonSerializable
{
    public $success_url = null;
    public $cancel_url = null;
    public $failure_url = null;
    public $webhook_success_url = null;
    public $webhook_cancel_url = null;
    public $webhook_failure_url = null;
    public $webhook_delay = null;

    /**
     * @param string $success_url Success URL the user is redirected after 3DS if SCA is required
     * @param string $cancel_url Cancel URL the user is redirected after 3DS if SCA is required
     * @param string $failure_url Failure URL the user is redirected after 3DS if SCA is required
     * @param string $webhook_success_url Webhook URL that is called (server-to-server) after successful 3DS if SCA is required
     * @param string $webhook_cancel_url Webhook URL that is called (server-to-server) after cancelled 3DS if SCA is required
     * @param string $webhook_failure_url Webhook URL that is called (server-to-server) after failed 3DS if SCA is required
     * @param int $webhook_delay Value 0-900 seconds, the delay between the event and calling of the Webhook
     */
    public function __construct(
        $success_url,
        $cancel_url,
        $failure_url,
        $webhook_success_url = null,
        $webhook_cancel_url = null,
        $webhook_failure_url = null,
        $webhook_delay = null
    ){
        $this->success_url = $success_url;
        $this->cancel_url = $cancel_url;
        $this->failure_url = $failure_url;
        $this->webhook_success_url = $webhook_success_url;
        $this->webhook_cancel_url = $webhook_cancel_url;
        $this->webhook_failure_url = $webhook_failure_url;
        $this->webhook_delay = $webhook_delay;
    }
}