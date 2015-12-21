# PaymentHighway PHP SDK

## Requirements

* PHP 5.4.+
* Composer

## Installation

add following to composer

```
    "require" : 
    {
        "solinor/paymenthighwayio" : "dev-develop@dev"
    }
```

## Structure 

* `\Solinor\PaymentHighway`

Contains API methods. Use these to create Payment Highway API requests.

* `\Solinor\PaymentHihgway\Model`

Contains Models used to inject paymentApi requests.

* \Solinor\PaymentHihgway\Security`

Contains classes used for hash calculations

# Overview

Start with building the HTTP form parameters by using the FormParameterBuilder. 

- `\Solinor\PaymentHighway\FormBuilder`

Create an instance of the builder, then use the generate methods to receive a list of parameters for each API call.

Initializing the builder

```php
$method = "POST";
$signatureKeyId = "testKey";
$signatureSecret = "testSecret";
$account = "test";
$merchant = "test_merchantId";
$serviceUrl = "https://v1-hub-staging.sph-test-solinor.com";

formBuilder = new FormBuilder($method, $signatureKeyId, $signatureSecret, $account,
                              $merchant, $baseUrl, $successUrl, $failureUrl,
                              $cancelUrl, $language);
```

Example common parameters for the following form generation functions
 
    successUrl = "https://example.com/success";
    failureUrl = "https://example.com/failure";
    cancelUrl = "https://example.com/cancel";
    language = "EN";

Example generateAddCardParameters

	form = formBuilder->generateAddCardParameters(successUrl, failureUrl, cancelUrl, language);

    // read form parameters
    httpMethod = form->getMethod();
    actionUrl = form->getAction();
    fields = form->getFields();
    
    
    System.out.println("Initialized form with request-id: " + formContainer.getRequestId());

    for (NameValuePair field : fields) {
        field.getName();
        field.getValue();
    }

Example generatePaymentParameters 

	String amount = "1990";
    String currency = "EUR";
    String orderId = "1000123A";
    String description = "A Box of Dreams. 19,90€";

    FormContainer formContainer = formBuilder.generatePaymentParameters(successUrl, failureUrl, cancelUrl, language,
        amount, currency, orderId, description);

    // read form parameters
    String httpMethod = formContainer.getMethod();
    String actionUrl = formContainer.getAction();
    List<NameValuePair> fields = formContainer.getFields();
    
    System.out.println("Initialized form with request-id: " + formContainer.getRequestId());

    for (NameValuePair field : fields) {
        field.getName();
        field.getValue();
    }
        	
Example generateGetAddCardAndPaymentParameters

	String amount = "1990";
    String currency = "EUR";
    String orderId = "1000123A";
    String description = "A Box of Dreams. 19,90€";

    FormContainer formContainer = formBuilder.generateAddCardAndPaymentParameters(successUrl, failureUrl, cancelUrl, 
        language, amount, currency, orderId, description);

    // read form parameters
    String httpMethod = formContainer.getMethod();
    String actionUrl = formContainer.getAction();
    List<NameValuePair> fields = formContainer.getFields();
    
    System.out.println("Initialized form with request-id: " + formContainer.getRequestId());

    for (NameValuePair field : fields) {
        field.getName();
        field.getValue();
    }

Each method returns a FormContainer object which provides required hidden fields for the HTML form to make a successful transaction to Form API. The builder will generate a request id, timestamp, and secure signature for the transactions, which are included in the FormContainer fields.

In order to charge a card given in the Form API, the corresponding transaction id must be committed by using Payment API.

In addition, after the user is redirected to one of your provided success, failure or cancel URLs, you should validate the request parameters and the signature.

Example validateFormRedirect

    SecureSigner secureSigner = new SecureSigner(signatureKeyId, signatureSecret);

    if ( ! secureSigner.validateFormRedirect(requestParams)) {
      throw new Exception("Invalid signature!");
    }


- `PaymentApi`

In order to do safe transactions, an execution model is used where the first call to /transaction acquires a financial transaction handle, later referred as “ID”, which ensures the transaction is executed exactly once. Afterwards it is possible to execute a debit transaction by using the received id handle. If the execution fails, the command can be repeated in order to confirm the transaction with the particular id has been processed. After executing the command, the status of the transaction can be checked by executing the PaymentAPI.transactionStatus("id") request. 

In order to be sure that a tokenized card is valid and is able to process payment transactions the corresponding tokenization id must be used to get the actual card token. 

Initializing the Payment API

	String serviceUrl = "https://v1-hub-staging.sph-test-solinor.com";
    String signatureKeyId = "testKey";
    String signatureSecret = "testSecret";
    String account = "test";
    String merchant = "test_merchantId";

    try (PaymentAPI paymentAPI = new PaymentAPI(serviceUrl, signatureKeyId, signatureSecret, account, merchant)) {
        // Payment API usage
    }
        
Example Commit Form Transaction

	String transactionId = ""; // get sph-transaction-id as a GET parameter
    String amount = "1999";
    String currency = "EUR";
    CommitTransactionResponse response = paymentAPI.commitTransaction(transactionId, amount, currency);

Example Init transaction

	InitTransactionResponse initResponse = paymentAPI.initTransaction();
	
Example Tokenize (get the actual card token by using token id)

	TokenizationResponse tokenResponse = paymentAPI.tokenize("tokenizationId");
			
Example Debit with Token

	Token token = new Token("id");
	TransactionRequest transaction = new TransactionRequest(token, "amount", "currency");
	TransactionResponse response = paymentAPI.debitTransaction("transactionId", transaction);
		
Example Revert

	TransactionResponse response = paymentAPI.revertTransaction("transactionId", "amount");

Example Transaction Status

	TransactionStatusResponse status = paymentAPI.transactionStatus("transactionId");
	
Example Daily Batch Report

	ReportResponse report = paymentAPI.fetchDailyReport("yyyyMMdd");
	
Example Order Status

    OrderSearchResponse orderSearchResponse = paymentAPI.searchOrders("order");
	

# Errors

Payment Highway API can raise exceptions for several reasons. Payment Highway authenticates each request and if there is invalid parameters or a signature mismatch, a HttpResponseException is raised.

The Payment Highway Java client also authenticates response messages, and in case of signature mismatch an AuthenticationException will be raised.

	try {
 		// Use Payment Highway's bindings...
	} catch (AuthenticationException e) {
 		// signals a failure to authenticate Payment Highway response
	} catch (HttpResponseException e) {
  		// Signals a non 2xx HTTP response.
  		// Invalid parameters were supplied to Payment Highway's API
	} catch (IOException e) {
  		// Signals that an I/O exception of some sort has occurred
	} catch (Exception e) {
  		// Something else happened
	}

It is recommended to gracefully handle exceptions from the API.

# Help us make it better

Please tell us how we can make the API better. If you have a specific feature request or if you found a bug, please use GitHub issues. Fork these docs and send a pull request with improvements.
