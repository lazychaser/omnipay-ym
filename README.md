# Omnipay: Yandex.Money

**Yandex.Money driver for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.org/lazychaser/omnipay-ym.png?branch=master)](https://travis-ci.org/lazychaser/omnipay-ym)
[![Latest Stable Version](https://poser.pugx.org/omnipay/ym/version.png)](https://packagist.org/packages/omnipay/ym)
[![Total Downloads](https://poser.pugx.org/omnipay/ym/d/total.png)](https://packagist.org/packages/omnipay/ym)

[Русская версия](/README-RU.md)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements PayPal support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "omnipay/ym": "~1.0"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* YM_External

Since Yandex.Money API handles payments a little bit differently, there are now standard methods like `purchase` for 
this gateway. You can find usage samples below.

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository. You can also visit [Yandex.Money API page](https://tech.yandex.ru/money/).

## Examples

### Setting up a gateway

```php
$gateway = Omnipay::create('YM_External');

$gateway->setWalledId('my wallet id'); // The id of the wallet that will receive payments
$gateway->setInstanceId('my instance id'); // A unique id, see below
```

You can get your instance id from your client id:

```php
$instanceId = $gateway->obtainInstanceId($clientId);

//store the instance id somewhere for further payments
```

__IMPORTANT!__ You do not need to obtain instance id for each payment! It is retrieved only once per app.

### Creating a purchase

```php
$response = $gateway->requestPayment(array( 'amount' => 10.0 ))->send();

if ( ! $response->isSuccessful())
{
   // display error
}

$transactionReference = $response->getTransactionReference();

// Save the transaction reference somewhere because we'll need one later to complete the purchase

$response = $gateway->processPayment(array(
    'transactionReference' => $transactionReference,
    'returnUrl' => '...',
    'cancelUrl' => '...',
))->send();

if ($response->isRedirect()) $response->redirect();

// couldn't process the payment, show error
```

### Completing the purchase

After customer fills required data, he'll be redirected to `returnUrl` where you need to complete the purchase:

```php
// Retrieve previously stored transaction reference
$transactionReference = ...;

// Same options here
$response = $gateway->processPayment(...);

if ($response->isSuccessful())
{
    // All done!
    // We can get internal invoice id of the payment
    $invoiceId = $response->getTransactionReference();
}
else
{
    // Payment failed
}
```

### Saving customer's card

After successful payment it is possible to save the card for further payments. You just need transaction reference which
you've got when created payment.

```php
// Same options as for processPayment method
$response = $gateway->createCard(...);

if ($response->isSuccessful() and ($cardReference = $response->getCardReference()))
{
    // Store card reference somewhere
    
    // You can also get extra info:
    $cardNumber = $response->getCardNumber();
    $cardType = $response->getCardType();
}
```

Note that response may be successful but have no info about card.

To make a purchase using a card, you need a card reference and `cvv` code:

```php
$response = $gateway->requestPayment(array('amount' => 10.0))->send();

// Check status

$response = $gateway->processPayment(array(
    'transactionReference' => $response->getTransactionReference(),
    
    ..., // same options
    
    'cardReference' => $cardReference,
    'cvv' => $cvv, // a user should enter this value
));

if ($response->isSuccessful())
{
    // Payment done!
}
elseif ($response->isRedirect())
{
    // Confirmation required!
    $response->redirect();
}

// Payment failed!
```

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/lazychaser/omnipay-ym/issues),
or better yet, fork the library and submit a pull request.