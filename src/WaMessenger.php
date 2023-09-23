<?php /** @noinspection PhpUnused */

namespace Salavati\WaMessenger;

use Salavati\WaMessengerException\WaMessengerException;

class WaMessenger extends WaMessengerModel {
    use WaMessengerCurl;
    private $domain = 'https://api.wamessenger.ir';

    /**
     * @throws WaMessengerException
     */
    public function sendMessage($text, $fileUrl = null) {
        if (empty($this->receivers)) throw new WaMessengerException('At least one receiver phone number is required.');
        if (empty($text) && empty($fileUrl)) throw new WaMessengerException('Message text and file URL are empty. At least one of them is required.');
        $dataToSend = [
            'apikey' => $this->apiKey,
            'text' => $text,
            'phonenumber' => $this->receivers,
        ];
        if (!empty($fileUrl)) $dataToSend['url'] = $fileUrl;
        $response = $this->sendCurlRequest("{$this->domain}/sendMessage/{$this->apiKey}", $dataToSend, 'POST');
        $result = json_decode($response);
        if (!$result) throw new WaMessengerException('Is not JSON: ' . $response);
        return $result;
    }

    /**
     * @throws WaMessengerException
     */
    public function receiveMessageStatus($messageId) {
        if (empty($messageId)) throw new WaMessengerException('Message ID is required.');
        $url = "{$this->domain}/getStatus/{$this->apiKey}?id={$messageId}";
        $response = $this->sendCurlRequest($url, []);
        $result = json_decode($response);
        $success = $result && !empty($result->pageCount);
        if (!$success) throw new WaMessengerException(isset($result->message) ? $result->message : 'Unknown Error.');
        return $result;
    }

    /**
     * @throws WaMessengerException
     */
    public function receiveAllMessagesStatus($phoneNumber, $page = 1) {
        if (empty($phoneNumber)) throw new WaMessengerException('Phone number is required.');
        $url = "{$this->domain}/showAllMessages/{$this->apiKey}?phonenumber={$phoneNumber}&page={$page}";
        $response = $this->sendCurlRequest($url, []);
        $result = json_decode($response);
        $success = $result && strtolower($result->set) == 'true';
        if (!$success) throw new WaMessengerException(isset($result->message) ? $result->message : 'Unknown Error.');
        return $result;
    }

    /**
     * @throws WaMessengerException
     */
    public function receivePendingMessages() {
        $url = "{$this->domain}/pending/{$this->apiKey}";
        $response = $this->sendCurlRequest($url, []);
        if ($response == 'No Pending Message') return [];
        $result = json_decode($response);
        if (!$result) throw new WaMessengerException('Unknown Error.');
        return $result;
    }

    /**
     * @throws WaMessengerException
     */
    public function receiveAllMessages($phoneNumber, $page = 1) {
        if (empty($messageId)) throw new WaMessengerException('Message ID is required.');
        $url = "{$this->domain}/showAllGetMessages/{$this->apiKey}?phonenumber={$phoneNumber}&page={$page}";
        $response = $this->sendCurlRequest($url, []);
        $result = json_decode($response);
        $success = $result && !empty($result->pageCount);
        if (!$success) throw new WaMessengerException(isset($result->message) ? $result->message : 'Unknown Error.');
        return $result;
    }

    /**
     * @throws WaMessengerException
     */
    public function sendWebhook($webhook) {
        if (!filter_var($webhook, FILTER_VALIDATE_URL)) throw new WaMessengerException('URL is not valid');
        $this->webhook = $webhook;
        $url = "{$this->domain}/webhook/set/{$this->apiKey}?url={$this->webhook}";
        $response = $this->sendCurlRequest($url, []);
        $result = json_decode($response);
        $success = $result && strtolower($result->set) == 'true';
        if (!$success) throw new WaMessengerException(isset($result->message) ? $result->message : 'Unknown Error.');
        return $result;
    }

    /**
     * @throws WaMessengerException
     */
    public function removeWebhook() {
        $url = "{$this->domain}/webhook/remove/{$this->apiKey}";
        $response = $this->sendCurlRequest($url, []);
        $result = json_decode($response);
        $success = $result && strtolower($result->set) == 'true';
        if (!$success) throw new WaMessengerException(isset($result->message) ? $result->message : 'Unknown Error.');
        return $result;
    }

    /**
     * @throws WaMessengerException
     */
    public function receiveWebhookPending() {
        $url = "{$this->domain}/webhook/pending/{$this->apiKey}";
        $response = $this->sendCurlRequest($url, []);
        $result = json_decode($response);
        if (!$result) throw new WaMessengerException('Unknown Error.');
        return (int) $result->pending;
    }

    /**
     * @throws WaMessengerException
     */
    public function sendSeen() {
        $url = "{$this->domain}/sendSeen/{$this->apiKey}?seen=on";
        $response = $this->sendCurlRequest($url, []);
        $result = json_decode($response);
        $success = $result && strtolower($result->set) == 'true';
        if (!$success) throw new WaMessengerException(isset($result->message) ? $result->message : 'Unknown Error.');
    }

    /**
     * @throws WaMessengerException
     */
    public function enableReceiveMessage($enable = true) {
        $onOrOff = $enable ? 'on' : 'off';
        $url = "{$this->domain}/receive/{$this->apiKey}?receive={$onOrOff}";
        $response = $this->sendCurlRequest($url, []);
        $result = json_decode($response);
        $success = $result && strtolower($result->set) == 'true';
        if (!$success) throw new WaMessengerException(isset($result->message) ? $result->message : 'Unknown Error.');
    }

}