<?php
namespace Salavati\WaMessenger;

use Salavati\WaMessengerException\WaMessengerException;

class WaMessenger extends WaMessengerModel {
    use WaMessengerCurl;
    public $timeout = 30;

    /**
     * @throws WaMessengerException
     */
    public function sendMessage($text, $fileUrl = null) {
        if (empty($this->receivers)) throw new WaMessengerException('شماره دریافت‌کنندگان خالی است.');
        if (empty($text) && empty($fileUrl)) throw new WaMessengerException('متن پیام و نشانی فایل خالی هستند.');
        $dataToSend = [
            'apikey' => $this->apiKey,
            'text' => $text,
            'phonenumber' => $this->receivers,
        ];
        if (!empty($fileUrl)) $dataToSend['url'] = $fileUrl;
        return $this->sendCurlRequest("https://api.wamessenger.ir/sendMessage/{$this->apiKey}", $dataToSend);
    }

    /**
     * @throws WaMessengerException
     */
    public function sendWebhook($webhook) {
        if (!filter_var($webhook, FILTER_VALIDATE_URL)) throw new WaMessengerException('URL is not valid');
        $this->webhook = $webhook;
        $url = "https://api.wamessenger.ir/webhook/set/{$this->apiKey}?url={$this->webhook}";
        $response = $this->sendCurlRequest($url, [], 'GET');
        $result = json_decode($response);
        $success = $result && strtolower($result->set) == 'true';
        if (!$success) throw new WaMessengerException(isset($result->message) ? $result->message : 'Unknown Error.');
        return $result;
    }

    /**
     * @throws WaMessengerException
     */
    public function receiveWebhookPending() {
        $url = "https://api.wamessenger.ir/webhook/pending/{$this->apiKey}";
        $response = $this->sendCurlRequest($url, [], 'GET');
        $result = json_decode($response);
        if (!$result) throw new WaMessengerException('Unknown Error.');
        return (int) $result->pending;
    }

    /**
     * @throws WaMessengerException
     */
    public function sendSeen() {
        $url = "https://api.wamessenger.ir/sendSeen/{$this->apiKey}?seen=on";
        $response = $this->sendCurlRequest($url, [], 'GET');
        $result = json_decode($response);
        $success = $result && strtolower($result->set) == 'true';
        if (!$success) throw new WaMessengerException(isset($result->message) ? $result->message : 'Unknown Error.');
    }

    /**
     * @throws WaMessengerException
     */
    public function enableReceiveMessage($enable = true) {
        $onOrOff = $enable ? 'on' : 'off';
        $url = "https://api.wamessenger.ir/receive/{$this->apiKey}?receive={$onOrOff}";
        $response = $this->sendCurlRequest($url, [], 'GET');
        $result = json_decode($response);
        $success = $result && strtolower($result->set) == 'true';
        if (!$success) throw new WaMessengerException(isset($result->message) ? $result->message : 'Unknown Error.');
    }

}