<?php


namespace reashetyr\NameCheap;
use reashetyr\NameCheap\routes;

class NameCheap extends ApiBase
{
    public const DEFAULT_ATTEMPTS_COUNT = 1, DEFAULT_ATTEMPTS_DELAY = 0.01;
    public $domains, $ssl, $users, $whoisguard;

    public function __construct($ApiUser, $ApiKey, $UserName, $ClientIP, $sandbox = false, $attempts_count = self::DEFAULT_ATTEMPTS_COUNT, $attempts_delay = self::DEFAULT_ATTEMPTS_DELAY) {
        parent::__construct($ApiUser, $ApiKey, $UserName, $ClientIP, $sandbox, $attempts_count, $attempts_delay);

        $this->setupBase();
    }

    private function setupBase(): void
    {
        $this->domains = new routes\Domains($this);
//        $this->ssl = new routes\Ssl($this);
//        $this->users = new routes\Users($this);
//        $this->whoisguard = new routes\Whoisguard($this);
    }

    public function sandboxMode($sandbox = true): void
    {
        $this->endpoint = parent::ENDPOINTS[$sandbox ? 'sandbox' : 'production'];

        // Reinitialize the base routes/objects
        $this->setupBase();
    }
}