<?php /** @noinspection PhpUnused */

namespace Salavati\WaMessenger;

class WaMessengerModel {
    protected $apiKey = null;
    protected $receivers = [];
    protected $webhook = '';

    public function __construct($apiKey = null) {
        $this->apiKey = $apiKey;
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

    public function getApiKey() {
        return $this->apiKey;
    }

    public function getReceivers() {
        return $this->receivers;
    }
}