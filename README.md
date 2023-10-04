<!--suppress HtmlDeprecatedAttribute -->
<div align='center'>
<img src=https://wamessenger.net/wp-content/uploads/2022/08/logo-wamessenger-v4.png alt="logo" width=195 height=50 />
<p>Whatsapp API using <a href="https://wamessenger.net" target="_blank">wamessenger.net</a></p>
<h4>
    <span> · </span>
    <a href="https://github.com/ali-salavati/WaMessenger/blob/master/README.md"> Documentation </a>
    <span> · </span>
    <a href="https://github.com/ali-salavati/WaMessenger/issues"> Report Bug </a>
    <span> · </span>
    <a href="https://github.com/ali-salavati/WaMessenger/issues"> Request Feature </a>
</h4>
</div>

### :notebook_with_decorative_cover: Table of Contents
- [About the Project](#star2-about-the-project)
- [Install](#install)
- [Usage](#usage)
- [License](#warning-license)


## :star2: About the Project
<b>WaMessenger</b> is a web service for sending and receiving messages, webhooks, and other reports from WhatsApp.

## Install
```bash
composer require salavati/wa-messenger
```

## Usage
Go to https://wamessenger.net and register.

Create a new service.
Then, receive the API key by clicking on "Connect to WhatsApp" and scanning the barcode with WhatsApp.

Insert this API key in your code or database. For example, if you are using the Laravel framework, you can write in the .env file:
```env
WA_MESSENGER_API_KEY="Your api key"
```
Then, create an object of the WaMessenger class:
```php
use Salavati\WaMessenger\WaMessenger;

$waMessenger = new WaMessenger(env('WA_MESSENGER_API_KEY'));
// Or
$waMessenger = new WaMessenger();
$waMessenger->setApiKey(env('WA_MESSENGER_API_KEY'));
```
Then set receivers:
```php
$waMessenger->setReceivers('989121234567'); // For single receiver
// Or
$phoneNumber = ['989121234567', '989121234568', ...]; // For multiple receivers.
$waMessenger->setReceivers($phoneNumber);
```
Then send message
```php
// For text message
$result = $waMessenger->sendMessage($text)

// For file message
$result = $waMessenger->sendMessage($caption, $fileUrl)
```
Response is something like this:
```json
[
  {
    "phonenumber": "989121234567",
    "tracking_Code": 80461451
  },
  {
    "phonenumber": "989121234568",
    "tracking_Code": 80461452
  }
]
```

## :warning: License
* <a href="https://choosealicense.com/licenses/mit/" target="_blank">MIT</a>
