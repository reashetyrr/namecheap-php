<?php


namespace reashetyr\NameCheap;
use reashetyr\NameCheap\routes;

class NameCheap extends ApiBase
{
    public const DEFAULT_ATTEMPTS_COUNT = 1, DEFAULT_ATTEMPTS_DELAY = 0.01;
    public $ssl;

    public function __construct($ApiUser, $ApiKey, $UserName, $ClientIP, $sandbox = false, $attempts_count = self::DEFAULT_ATTEMPTS_COUNT, $attempts_delay = self::DEFAULT_ATTEMPTS_DELAY) {
        parent::__construct($ApiUser, $ApiKey, $UserName, $ClientIP, $sandbox, $attempts_count, $attempts_delay);

        $this->setupBase();
    }

    private function setupBase(): void
    {
        $this->ssl = new routes\Ssl($this);
    }

    public function sandboxMode($sandbox = true): void
    {
        $this->endpoint = parent::ENDPOINTS[$sandbox ? 'sandbox' : 'production'];

        // Reinitialize the base routes/objects
        $this->setupBase();
    }
}