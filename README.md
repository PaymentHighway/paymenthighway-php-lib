# PaymentHighway PHP SDK

## Requirements

* PHP 5.4.+
* Composer

## Installation

add following to composer

```json
    "require" : 
    {
        "solinor/paymenthighwayio" : "1.0.0-RC1"
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

Initializing the builder

```php

use \Solinor\PaymentHighway\FormBuilder

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

Example generateAddCardParameters

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

Example generatePaymentParameters 

```php
$amount = "1990";
$currency = "EUR";
$orderId = "1000123A";
$description = "A Box of Dreams. 19,90€";

$form = $formBuilder->generatePaymentParameters($amount, $currency, $orderId, $description);

```
        	
Example generateGetAddCardAndPaymentParameters
```php
$amount = "1990";
$currency = "EUR";
$orderId = "1000123A";
$description = "A Box of Dreams. 19,90€";

$form = formBuilder->generateAddCardAndPaymentParameters($amount, $currency, $orderId, $description);
```

Each method returns a Form object which provides required hidden fields for the HTML form to make a successful transaction to Form API. The builder will generate a request id, timestamp, and secure signature for the transactions, which are included in the Form fields.

In order to charge a card given in the Form API, the corresponding transaction id must be committed by using Payment API.

In addition, after the user is redirected to one of your provided success, failure or cancel URLs, you should validate the request parameters and the signature.

Example validateFormRedirect (NOT IMPLEMENTED YET!)

```php

use Solinor\PaymentHighway\Model\Security\SecureSigner

$secureSigner = new SecureSigner(signatureKeyId, signatureSecret);

if ( ! secureSigner->validateFormRedirect(requestParams)) {
throw new Exception("Invalid signature!");
}
```

## PaymentApi

In order to do safe transactions, an execution model is used where the first call to /transaction acquires a financial transaction handle, later referred as “ID”, which ensures the transaction is executed exactly once. Afterwards it is possible to execute a debit transaction by using the received id handle. If the execution fails, the command can be repeated in order to confirm the transaction with the particular id has been processed. After executing the command, the status of the transaction can be checked by executing the `PaymentAPI->statusTransaction( $transactionId )` request. 

In order to be sure that a tokenized card is valid and is able to process payment transactions the corresponding tokenization id must be used to get the actual card token. 

Initializing the Payment API
```php
use Solinor\PaymentHighway\PaymentApi

$serviceUrl = "https://v1-hub-staging.sph-test-solinor.com";
$signatureKeyId = "testKey";
$signatureSecret = "testSecret";
$account = "test";
$merchant = "test_merchantId";

$paymentApi = new PaymentApi($serviceUrl, $signatureKeyId, $signatureSecret, $account, $merchant)
```
        
Example Commit Form Transaction
```php
$transactionId = ""; // get sph-transaction-id as a GET parameter
$amount = "1999";
$currency = "EUR";

$response = $paymentApi->commitTransaction($transactionId, $amount, $currency); //response is pure json run through json_decode();
```

Example Init transaction
```php
$response = $paymentApi->initTransaction();
```

Example Tokenize (get the actual card token by using token id)
```php
$response = $paymentApi->tokenize( $tokenizationId );
```

Example Debit with Token
```php
$token = new \Solinor\PaymentHighway\Model\Token( $tokenId );

$transaction = new \Solinor\PaymentHighway\Model\Request\Transaction( $token, $amount, $currency);

$response = $paymentApi->debitTransaction( $transactionId, $transaction);
```

Example Revert
```php
$response = $paymentApi->revertTransaction("transactionId", "amount");
```

Example Transaction Status
```php
$status = paymentApi->statusTransaction( $transactionId );
```

Example Daily Batch Report
```php
$response = paymentApi->getReport( $date ); //in "date('Y-M-D')" format
```

Example Order Status
```php
$response = paymentApi->searchByOrderId( $orderId );
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
