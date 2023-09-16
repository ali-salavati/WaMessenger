<?php

use Salavati\WaMessenger\WaMessenger;
use Salavati\WaMessengerException\WaMessengerException;

$waMessenger = new WaMessenger();
try {
    $phoneNumber = '989123456789'; // Or ['989123456788', '989123456789', ...]
    $id = $waMessenger
        ->setApiKey(env('WA_MESSENGER_API_KEY'))
        ->setReceivers($phoneNumber)
        ->sendMessage('پیام تستی', 'https://wamessenger.net/wp-content/uploads/2022/08/logo-wamessenger-v4.png'); // ارسال با مدیا
        // ->sendMessage('پیام تستی'); // ارسال بدون مدیا
    echo "با موفقیت ارسال شد. شناسه پیام: " . $id;
} catch (WaMessengerException $e) {
    echo "خطا: " . $e->getMessage();
}
