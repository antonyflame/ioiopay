<?php namespace Antonyflame\Ioiopay;

use Exception;

class IoiopayException extends Exception {
    protected $additionalData;
    public function __construct(string $message, array $data = []) {
        $this->additionalData = $data;
        parent::__construct($message);
    }

    public function getAdditionalData(): array {
        return $this->additionalData;
    }
}

