<?php
namespace Salavati\WaMessenger;

use Salavati\WaMessengerException\WaMessengerException;

class WaMessenger {
    public $timeout = 30;
    private $apiKey = null;
    private $receivers = [];

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
        $response = $this->sendCurlRequest("https://api.wamessenger.ir/sendMessage/{$this->apiKey}", $dataToSend);
        return count($this->receivers) == 1 ? [['phonenumber' => $this->receivers[0], 'tracking_code' => $response]] : json_decode($response, true);
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
    private function sendCurlRequest($address, $data) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $address,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => [
                "cache-control: no-cache",
                "Content-Type: application/x-www-form-urlencoded"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        if ($err) throw new WaMessengerException("cURL Error #:" . $err);
        curl_close($curl);
        if ($response === 'Bad Request') throw new WaMessengerException('Bad Request');
        return $response;
    }

}