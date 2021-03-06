# Omnipay: Yandex.Money

**Библиотека для приема платежей с помощью пластиковых карт на сайте.**

[![Build Status](https://travis-ci.org/lazychaser/omnipay-ym.png?branch=master)](https://travis-ci.org/lazychaser/omnipay-ym)
[![Latest Stable Version](https://poser.pugx.org/omnipay/ym/version.png)](https://packagist.org/packages/omnipay/ym)
[![Total Downloads](https://poser.pugx.org/omnipay/ym/d/total.png)](https://packagist.org/packages/omnipay/ym)

## Как это работает?

Для того, чтобы совершать запросы к API, необходимо [зарегистрировать][register] приложение. Средства будут зачисляться
прямиком на указанный кошелек.

* Пользователь нажимает кнопку "оплатить", с помощью API формируется запрос на оплату;
* Пользователь перенаправляется на сайт Яндекс.Деньги для ввода данных карты;
* Если необходимо, данные дополнительно проверяются банком;
* Если платеж прошел успешно, пользователь возвращается на `returnUrl`, где проверяется статус платежа;
* Можно сохранить токен карты для платежей без ввода данных;
* Если платеж не прошел, пользователь перенаправляется на `cancelUrl`, где также необходимо проверять статус платежа.

Больше информации можно найти на [официальном сайте][api] Яндекс.Деньги.

[api]: https://tech.yandex.ru/money/apps/ "Описание API на сайте Яндекс.Деньги"
[register]: https://sp-money.yandex.ru/myservices/new.xml "Регистрация приложения"

## Установка

```
composer require omnipay/ym:~1.0
```

## Использование

Для начала необходимо создать гейт, через который уже будут выполняться запросы API:

```php
$gateway = Omnipay::create('YM_External');

$gateway->setInstanceId($instanceId);
```

Для любого запроса необходим параметр `instanceId`. Данный параметр генерируется для каждого приложения отдельно
на основе `client_id`, который был получен при регистрации приложения. Чтобы сгенерировать новый `instanceId`:

```php
$instanceId = $gateway->obtainInstanceId($clientId);

// сохранить где-нибудь для последующего использования
```

### Формирование счета

```php
$response = $gateway->requestPayment(array(
    'walletId' => '12345678910', // ID кошелька, на который поступят средства
    'amount' => 256.0, // Сумма в рублях
    'description' => 'Оплата услуг',
    
))->send();

if ( ! $response->isSuccessful())
{
    // Произошла ошибка
}

// Получаем идентификатор транзакции, его нужно где-нибудь сохранить, т.к. он нам понадобится
$transaction = $response->getTransactionReference();
```

Теперь необходимо перенаправить пользователя на страницу ввода данных, для этого нужно запросить статус платежа:

```php
$response = $gateway->processPayment(array(
    'transactionReference' => $transaction,
    'returnUrl' => 'http://example.com/success',
    'cancelUrl' => 'http://example.com/fail',
    
)->send();

if ($response->isRedirect()) $response->redirect();

// Произошла ошибка
```

### Проверка статуса платежа

После того, как пользователь успешно совершил платеж, либо отменил операцию (банк отклонил платеж), он будет 
перенаправлен либо на `returnUrl` при успешности, либо на `cancelUrl` при ошибке. Если нет какой-то особой 
дополнительной логики, то можно просто выводить сообщение об успешности/ошибочности платежа. Иначе необходимо 
обязательно проверять статус платежа.

```php
$response = $gateway->processPayment(array(
    'transactionReference' => $transaction,
    'returnUrl' => '...',
    'cancelUrl' => '...',
    
)->send();

if ($response->isSuccessful())
{
    // Платеж совершен!
}
elseif ($response->isPaymentRefused())
{
    // Платеж отклонен!
}
else
{
    // Произошла ошибка при запросе
}
```

Обратите внимание, что для получение статуса платежа, необходим номер транзакции, который был получен при 
формировании платежа. Он передается при перенаправлении пользователя под именем `cps_context_id`. Но лучше его сохранять
на сервере и восстанавливать когда пользователь возвращается на сайт.

### Сохранение карты

Если пользователь успешно совершил платеж, то есть возможность сохранить введенные им данные и использовать уже их 
при других платежах.

```php
$response = $gateway->createCard(array(
    'transactionReference' => $transaction,
    'returnUrl' => '...',
    'cancelUrl' => '...',
    
))->send();

if ($cardRef = $response->getCardReference())
{
    // Токен карты успешно получен, нужно его сохранить куда-нибудь
    
    // Другие данные:
    $response->getCardType(); // VISA, MasterCard
    $response->getCardNumber(); // Маскированный номер карты
}
```

### Проведение платежа с помощью сохраненной карты

Сам платеж проходит по той же схеме, что и раньше, то есть сначала формируется платеж, но при первом запросе статуса
передается токен карты, а также защитный код `cvv` с обратной стороны карты (который должен вводить пользователь):

```php
$response = $gateway->processPayment(array(
    'transactionReference' => $transaction,
    'returnUrl' => '...',
    'cancelUrl' => '...',
    'cardReference' => $cardRef,
    'cvv' => $cvv,
    
))->send();

if ($response->isSuccessful())
{
    // Платеж прошел!
}
elseif ($response->isRedirect())
{
    // Дополнительная авторизация
    $response->redirect();
}
else
{
    // Произошла ошибка
}
```
