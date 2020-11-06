<?php

namespace reashetyr\NameCheap\routes;

use reashetyr\NameCheap\ApiError;
use reashetyr\NameCheap\NameCheap;

class Ssl
{
    private $namecheap;

    public function __construct(NameCheap $namecheap)
    {
        $this->namecheap = $namecheap;
    }

    public function create(): array
    {
        throw new ApiError('COMING SOON', 500);
    }

    public function getList() {
        throw new ApiError('COMING SOON', 500);
    }

    public function parseCSR() {
        throw new ApiError('COMING SOON', 500);
    }

    public function getApproverEmailList() {
        throw new ApiError('COMING SOON', 500);
    }

    public function activate() {
        throw new ApiError('COMING SOON', 500);
    }

    public function resendApproverEmail($domains): array
    {
        throw new ApiError('COMING SOON', 500);
    }

    public function getInfo() {
        throw new ApiError('COMING SOON', 500);
    }

    public function renew() {
        throw new ApiError('COMING SOON', 500);
    }

    public function reissue() {
        throw new ApiError('COMING SOON', 500);
    }

    public function resendFulfillmentEmail() {
        throw new ApiError('COMING SOON', 500);
    }

    public function purchaseMoreSans() {
        throw new ApiError('COMING SOON', 500);
    }

    public function revokeCertificate() {
        throw new ApiError('COMING SOON', 500);
    }

    public function editDCVMethod() {
        throw new ApiError('COMING SOON', 500);
    }
}