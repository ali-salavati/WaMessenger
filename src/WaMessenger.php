<?php
namespace Salavati\WaMessenger;

use Salavati\WaMessengerException\WaMessengerException;

class WaMessenger {
    public $timeout = 30;
    private $apiKey = null;
    private $receivers = [];
    private $webhook = '';
    public $response = null;

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
    public function setWebhook($webhook) {
        if (!filter_var($webhook, FILTER_VALIDATE_URL)) throw new WaMessengerException('URL is not valid');
        $this->webhook = $webhook;
        $url = "https://api.wamessenger.ir/webhook/set/{$this->apiKey}?url={$this->webhook}";
        $response = $this->sendCurlRequest($url, [], 'GET');
        $response = json_decode($response);
        if (!$response || $response->set != 'true') throw new WaMessengerException(empty($response->message) ? 'URL was not set correctly.' : $response->message);
        return $this;
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

    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function setReceivers($receivers) {
        if (is_array($receivers)) $this->receivers = $receivers;
        if (is_string($receivers)) $this->receivers = [$receivers];
        return $this;
    }

    /**
     * @throws WaMessengerException
     */
    private function sendCurlRequest($address, $data = [], $method = 'POST') {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $address,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => [
                "cache-control: no-cache",
                "Content-Type: application/x-www-form-urlencoded"
            ],
        ]);

        $this->response = curl_exec($curl);
        $err = curl_error($curl);
        if ($err) throw new WaMessengerException("cURL Error #:" . $err);
        curl_close($curl);
        if ($this->response === 'Bad Request') throw new WaMessengerException('Bad Request');
        return $this->response;
    }

}