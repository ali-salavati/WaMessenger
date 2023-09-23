<?php

namespace Salavati\WaMessenger;

use Salavati\WaMessengerException\WaMessengerException;

trait WaMessengerCurl {
    public $timeout = 30;
    public $response = null;

    /**
     * @throws WaMessengerException
     */
    private function sendCurlRequest($address, $data = [], $method = 'GET') {
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
