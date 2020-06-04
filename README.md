# PaymentHighway PHP SDK
[![][Build Status img]][Build Status]

This library is being **Deprecated**, and won't get new features.

## Requirements

* PHP 5.6.+
* Composer

## Installation

add following to composer

```json
    "require" : 
    {
        "solinor/paymenthighwayio" : "2.0.0"
    }
```

## Structure 

* `\Solinor\PaymentHighway`

Contains API methods. Use these to create Payment Highway API requests.

* `\Solinor\PaymentHihgway\Model`

Contains Models used to inject paymentApi requests.

* `\Solinor\PaymentHihgway\Security`

Contains classes used for hash calculations

# Overview

Start with building the HTTP form parameters by using the FormParameterBuilder. Create an instance of the builder, then use the generate methods to receive a list of parameters for each API call.

### Initializing the builder

```php

use \Solinor\PaymentHighway\FormBuilder;

$method = "POST";
$signatureKeyId = "testKey";
$signatureSecret = "testSecret";
$account = "test";
$merchant = "test_merchantId";
$baseUrl = "https://v1-hub-staging.sph-test-solinor.com";
$successUrl = "https://example.com/success";
$failureUrl = "https://example.com/failure";
$cancelUrl = "https://example.com/cancel";
$language = "EN";

$formBuilder = new FormBuilder($method, $signatureKeyId, $signatureSecret, $account,
                              $merchant, $baseUrl, $successUrl, $failureUrl,
                              $cancelUrl, $language);
```

### Example generateAddCardParameters

```php
$form = $formBuilder->generateAddCardParameters($accept_cvc_required = false);
```

For all Form objects returned by FormBuilder methods
```php
// read form parameters
$httpMethod = $form->getMethod();
$actionUrl = $form->getAction();
$parameters = $form->getParameters(); 

// Header parameters as key => value array
foreach ($parameters as $key => $value) {
	echo $key .":". $value;
}
```

### Example generatePaymentParameters 

```php
$amount = "1990";
$currency = "EUR";
$orderId = "1000123A";
$description = "A Box of Dreams. 19,90€";

$form = $formBuilder->generatePaymentParameters($amount, $currency, $orderId, $description);

```
        	
### Example generateGetAddCardAndPaymentParameters
```php
$amount = "1990";
$currency = "EUR";
$orderId = "1000123A";
$description = "A Box of Dreams. 19,90€";

$form = $formBuilder->generateAddCardAndPaymentParameters($amount, $currency, $orderId, $description);
```


### Example generatePayWithMobilePayParameters with optional parameters
```php
$amount = "1990";
$currency = "EUR";
$orderId = "1000123A";
$description = "A Box of Dreams. 19,90€";
$exitIframeOnResult = null;
$shopLogoUrl = "https://foo.bar/biz.png";
$phoneNumber = "+3581234567"; 
$shopName = "Jaskan solki";
		
$form = $formBuilder->generatePayWithMobilePayParameters(
		$amount, 
		$currency, 
		$orderId, 
		$description, 
		$exitIframeOnResult, 
		$shopLogoUrl, 
		$phoneNumber, 
		$shopName
	);
```

##### About shop logo in MobilePay
* The logo must be 250x250 pixel in .png format. 
* MPO will show a default logo in the app if this is empty or the image location doesn’t exist. 
* Once a ShopLogoURL has been sent to MPOnline the .png-file on that URL must never be changed. If the shop wants a new (or more than one) logo, a new ShopLogoURL must be used. 
* The logo must be hosted on a HTTPS (secure) server.

### Example validateFormRedirect

```php

use Solinor\PaymentHighway\Security\SecureSigner;

$secureSigner = new SecureSigner(signatureKeyId, signatureSecret);

try{
    $secureSigner->validateFormRedirect($params) // redirected get params as [ key => value] array
}
catch(Exception $e) {
    // Validation failed, handle here
}
```

## PaymentApi

In order to do safe transactions, an execution model is used where the first call to /transaction acquires a financial transaction handle, later referred as “ID”, which ensures the transaction is executed exactly once. Afterwards it is possible to execute a debit transaction by using the received id handle. If the execution fails, the command can be repeated in order to confirm the transaction with the particular id has been processed. After executing the command, the status of the transaction can be checked by executing the `PaymentAPI->statusTransaction( $transactionId )` request. 

In order to be sure that a tokenized card is valid and is able to process payment transactions the corresponding tokenization id must be used to get the actual card token. 

### Remember to check the result code:
```php
$response->body->result->code
```
Code 100 means "Request successful". Other response codes can be found: [https://paymenthighway.fi/dev/?php#rcode-result-codes](https://paymenthighway.fi/dev/?php#rcode-result-codes)

### Initializing the Payment API

```php
use Solinor\PaymentHighway\PaymentApi;

$serviceUrl = "https://v1-hub-staging.sph-test-solinor.com";
$signatureKeyId = "testKey";
$signatureSecret = "testSecret";
$account = "test";
$merchant = "test_merchantId";

$paymentApi = new PaymentApi($serviceUrl, $signatureKeyId, $signatureSecret, $account, $merchant);
```
        
### Example Commit Form Transaction
```php
$transactionId = "f23a9be0-15fe-43df-98ac-92f6a5731c3b"; // get sph-transaction-id as a GET parameter
$amount = 1999;
$currency = "EUR";

$response = $paymentApi->commitFormTransaction($transactionId, $amount, $currency ); //response is pure json run through json_decode();
```

### Example Tokenize (get the actual card token by using token id)
```php
$response = $paymentApi->tokenize( $tokenizationId );
```

### Example Init transaction
```php
$response = $paymentApi->initTransaction();
```

### Example Debit with Token
NOTE: The `debitTransaction` method will be deprecated starting from Sep 14th 2019 in favor of the new `chargeCustomerInitiatedTransaction` and `chargeMerchantInitiatedTransaction` in order to comply with the EU's PSD2 directive.

```php
$token = new \Solinor\PaymentHighway\Model\Token( $tokenId );

$transaction = new \Solinor\PaymentHighway\Model\Request\Transaction( $token, $amount, $currency );

$response = $paymentApi->debitTransaction( $transactionId, $transaction);
```

### Charging a card

After the introduction of the European PSD2 directive the electronic payment transactions are categorised in so called customer initiated transactions (CIT) and merchant initiated transactions (MIT). 

Customer initiated transactions are scenarios where the customer takes actively part in the payment process either by providing their card information or selecting a previously stored payment method. Also so-called "one-click" purchases where the transaction uses a previously saved default payment method are CITs.

Merchant initated transactions are transactions which are initated by the merchant without customer's participation. Merchant initated transactions require a prior agreement between the customer and merchant also called the "mandate". Merchant initiated transactions can be used for example in scenarios where the final price is not known at the time of the purchase or the customer is not present when the charge is made.

#### Charging a customer initiated transaction (CIT)

When charging a customer initiated transaction there is always a possibility that the card issuer requires strong customer authentication. In case the issuer requests SCA then the response will contain "soft decline" code 400 and an URL where the customer needs to be redirected to perform authentication. The URLs where the customer will be redirected after completing authentication need to be defined in the [`ReturnUrls`](/src/Model/Sca/ReturnUrls.php) object.

In addition to the return urls the [`StrongCustomerAuthentication`](/src/Model/Sca/StrongCustomerAuthentication.php) object has many optional fields for information about the customer and the transaction. This information is used in transaction risk analysis (TRA) and can increase the likelihood that the transaction is considered low risk so that strong customer authentication is not needed.

```php
$token = new \Solinor\PaymentHighway\Model\Token( $tokenId );

$strongCustomerAuthentication = new \Solinor\PaymentHighway\Model\Sca\StrongCustomerAuthentication(
	new \Solinor\PaymentHighway\Model\Sca\ReturnUrls(
		"https://example.com/success", // URL the user is redirected after succesful 3D-Secure authentication if strong customer authentication is required
		"https://example.com/cancel", // URL the user is redirected after cancelled 3D-Secure authentication if strong customer authentication is required
		"https://example.com/failure" // URL the user is redirected after failed 3D-Secure authentication if strong customer authentication is required
	)
	// Optinally other information about the customer and purchase to help in transaction risk analysis (TRA)
);

$transaction = new \Solinor\PaymentHighway\Model\Request\CustomerInitiatedTransaction( $token, $amount, $currency, $strongCustomerAuthentication );

$response = $paymentApi->chargeCustomerInitiatedTransaction( $transactionId, $transaction);
```

#### Charging a merchant initiated transaction (MIT)

When charging the customer's card in context where the customer is not actively participating in the transaction you should use the `chargeMerchantInitiatedTransaction` method. The MIT transactions are exempt from the strong customer authentication requirements of PSD2 so the request cannot be answered with "soft-decline" response (code 400) unlike customer initated transactions.

```php
$token = new \Solinor\PaymentHighway\Model\Token( $tokenId );

$transaction = new \Solinor\PaymentHighway\Model\Request\Transaction( $token, $amount, $currency );

$response = $paymentApi->chargeMerchantInitiatedTransaction( $transactionId, $transaction);
```

### Example Revert
```php
$response = $paymentApi->revertTransaction("transactionId", "amount");
```

### Example Transaction Status
```php
$status = $paymentApi->statusTransaction( $transactionId );
```

### Example Daily Batch Report
```php
$response = $paymentApi->getReport( $date ); //in "date('Y-M-D')" format
```

### Example Order Status
```php
$response = $paymentApi->searchByOrderId( $orderId );
```	

# Errors
Payment Highway API can raises exceptions and you should handle them in graceful manner.
```php
try {
	// Use Payment Highway's bindings...
} 
catch (Exception $e) {
  	// Something else happened
}
```

# Help us make it better
Please tell us how we can make the API better. If you have a specific feature request or if you found a bug, please use GitHub issues. Fork these docs and send a pull request with improvements.

[Build Status]:https://travis-ci.org/PaymentHighway/paymenthighway-php-lib
[Build Status img]:https://travis-ci.org/PaymentHighway/paymenthighway-php-lib.svg?branch=master
