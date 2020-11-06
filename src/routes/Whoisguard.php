<?php

namespace reashetyr\NameCheap\routes;

use reashetyr\NameCheap\ApiError;
use reashetyr\NameCheap\NameCheap;

class Whoisguard
{
    private $namecheap;

    public function __construct(NameCheap $namecheap)
    {
        $this->namecheap = $namecheap;
    }

    public function changeEmailAddress(): array
    {
        throw new ApiError('COMING SOON', 500);
    }

    public function enable() {
        throw new ApiError('COMING SOON', 500);
    }

    public function disable() {
        throw new ApiError('COMING SOON', 500);
    }

    public function getList() {
        throw new ApiError('COMING SOON', 500);
    }

    public function renew() {
        throw new ApiError('COMING SOON', 500);
    }
}