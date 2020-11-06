<?php

namespace reashetyr\NameCheap\routes;

use reashetyr\NameCheap\ApiError;
use reashetyr\NameCheap\NameCheap;
use reashetyr\NameCheap\routes\users\Address;

class Users
{
    private $namecheap;
    public $address;

    public function __construct(NameCheap $namecheap)
    {
        $this->namecheap = $namecheap;
        $this->address = new Address($namecheap);
    }

    public function getPricing(): array
    {
        throw new ApiError('COMING SOON', 500);
    }

    public function getBalances() {
        throw new ApiError('COMING SOON', 500);
    }

    public function changePassword() {
        throw new ApiError('COMING SOON', 500);
    }

    public function update() {
        throw new ApiError('COMING SOON', 500);
    }

    public function createAddFundsRequest() {
        throw new ApiError('COMING SOON', 500);
    }

    public function getAddFundsStatus(): array
    {
        throw new ApiError('COMING SOON', 500);
    }

    public function create() {
        throw new ApiError('COMING SOON', 500);
    }

    public function login() {
        throw new ApiError('COMING SOON', 500);
    }

    public function resetPassword() {
        throw new ApiError('COMING SOON', 500);
    }
}