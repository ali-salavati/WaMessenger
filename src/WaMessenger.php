<?php
/** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection, PhpUnused */

namespace Salavati\WaMessenger;

class WaMessenger extends WaMessengerModel {
    use WaMessengerCurl;
    private $domain = 'https://api.360messenger.com';

    /**
     * @throws WaMessengerException
     */
    public function sendMessage($text, $fileUrl = null, $log = null) {
        if (empty($this->receivers)) throw new WaMessengerException('At least one receiver phone number is required.');
        if (empty($text) && empty($fileUrl)) throw new WaMessengerException('Message text and file URL are empty. At least one of them is required.');
        $dataToSend = [
            'apikey' => $this->apiKey,
            'text' => $text,
            'phonenumber' => $this->receivers,
        ];
        if (!empty($fileUrl)) $dataToSend['url'] = $fileUrl;
        $address = "{$this->domain}/sendMessage/{$this->apiKey}";
        if ($log) {
            $log($address, __FILE__, __LINE__);
            $log($dataToSend, __FILE__, __LINE__);
        }
        $response = $this->sendCurlRequest($address, $dataToSend, 'POST', $log);
        if ($log) $log($response, __FILE__, __LINE__);
        return $response;
    }

    /**
     * @throws WaMessengerException
     */
    private function sendRequest($url, $isSuccess, $log = null) {
        $response = $this->sendCurlRequest($url, [], 'GET', $log);
        if ($log) $log($response, __FILE__, __LINE__);
        if ($response == 'No Pending Message') return [];
        $result = json_decode($response);
        if ($log) $log($result, __FILE__, __LINE__);
        $success = $result && $isSuccess($result);
        if ($log) $log($success, __FILE__, __LINE__);
        if (!$success) throw new WaMessengerException(isset($result->message) ? $result->message : "Unknown Error. Server response: {$this->response}");
        return $result;
    }

    /**
     * @throws WaMessengerException
     */
    public function receiveMessageStatus($messageId, $log = null) {
        if (empty($messageId)) throw new WaMessengerException('Message ID is required.');
        $address = "{$this->domain}/getStatus/{$this->apiKey}?id={$messageId}";
        if ($log) $log($address, __FILE__, __LINE__);
        $response = $this->sendRequest($address, function ($result) { return !empty($result->pageCount); });
        if ($log) $log($response, __FILE__, __LINE__);
    }

    /**
     * @throws WaMessengerException
     */
    public function receiveAllMessagesStatus($phoneNumber, $page = 1, $log = null) {
        if (empty($phoneNumber)) throw new WaMessengerException('Phone number is required.');
        $url = "{$this->domain}/showAllMessages/{$this->apiKey}?phonenumber={$phoneNumber}&page={$page}";
        return $this->sendRequest($url, function ($result) { return strtolower($result->set) == 'true'; });
    }

    /**
     * @throws WaMessengerException
     */
    public function receivePendingMessages($log = null) {
        $url = "{$this->domain}/pending/{$this->apiKey}";
        return $this->sendRequest($url, function () { return true; });
    }

    /**
     * @throws WaMessengerException
     */
    public function receiveAllMessages($phoneNumber, $page = 1, $log = null) {
        if (empty($phoneNumber)) throw new WaMessengerException('Phone number is required.');
        $url = "{$this->domain}/showAllGetMessages/{$this->apiKey}?phonenumber={$phoneNumber}&page={$page}";
        return $this->sendRequest($url, function ($result) { return  !empty($result->pageCount); });
    }

    /**
     * @throws WaMessengerException
     */
    public function setWebhook($webhook, $log = null) {
        if (!filter_var($webhook, FILTER_VALIDATE_URL)) throw new WaMessengerException('URL is not valid');
        $this->webhook = $webhook;
        $url = "{$this->domain}/webhook/set/{$this->apiKey}?url={$this->webhook}";
        return $this->sendRequest($url, function ($result) { return strtolower($result->set) == 'true'; });
    }

    /**
     * @throws WaMessengerException
     */
    public function removeWebhook($log = null) {
        $url = "{$this->domain}/webhook/remove/{$this->apiKey}";
        return $this->sendRequest($url, function ($result) { return strtolower($result->set) == 'true'; });
    }

    /**
     * @throws WaMessengerException
     */
    public function receiveWebhookPending($log = null) {
        $url = "{$this->domain}/webhook/pending/{$this->apiKey}";
        return $this->sendRequest($url, function () { return true; });
    }

    /**
     * @throws WaMessengerException
     */
    public function resendWebhook($log = null) {
        $url = "{$this->domain}/webhook/resend/{$this->apiKey}";
        return $this->sendRequest($url, function () { return true; });
    }

    /**
     * @throws WaMessengerException
     */
    public function sendSeen($log = null) {
        $url = "{$this->domain}/sendSeen/{$this->apiKey}?seen=on";
        return $this->sendRequest($url, function ($result) { return strtolower($result->set) == 'true'; });
    }

    /**
     * @throws WaMessengerException
     */
    public function enableReceiveMessage($enable = true, $log = null) {
        $onOrOff = $enable ? 'on' : 'off';
        $url = "{$this->domain}/receive/{$this->apiKey}?receive={$onOrOff}";
        return $this->sendRequest($url, function ($result) { return strtolower($result->set) == 'true'; });
    }

}