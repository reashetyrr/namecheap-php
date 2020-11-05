<?php

namespace reashetyr\NameCheap\routes;

use reashetyr\NameCheap\ApiError;
use reashetyr\NameCheap\ApiResult;
use reashetyr\NameCheap\NameCheap;

class Domains
{
    private $namecheap;

    public function __construct(NameCheap $namecheap)
    {
        $this->namecheap = $namecheap;
    }

    public function getList(string $listType = 'ALL', string $searchTerm = null, int $page = 1, int $pageSize = 20, string $sortBy = null): array
    {
        if (!in_array($listType, ['ALL', 'EXPIRING', 'EXPIRED']))
            $listType = 'ALL';

        if (!in_array($sortBy, ['NAME', 'NAME_DESC', 'EXPIREDATE', 'EXPIREDATE_DESC', 'CREATEDATE', 'CREATEDATE_DESC']))
            $sortBy = 'CREATEDATE';

        $extra_params = [
            'ListType' => $listType,
            'SearchTerm' => $searchTerm,
            'Page' => $page,
            'PageSize' => $pageSize,
            'SortBy' => $sortBy
        ];

        $results = $this->namecheap->_call('namecheap.domains.getList', $extra_params);
        return $results->getJson()['CommandResponse']['DomainGetListResult'];
    }

    public function getContacts() {
        throw new ApiError('MAKE THIS', 500);
    }

    public function create() {
        throw new ApiError('MAKE THIS', 500);
    }

    public function getTldList() {
        throw new ApiError('MAKE THIS', 500);
    }

    public function setContacts() {
        throw new ApiError('MAKE THIS', 500);
    }

    public function check($domains): array
    {
        if (!is_array($domains))
            $domains = [$domains];

        $extra_payload = ['DomainList' => join(',', $domains)];
        $api_results = $this->namecheap->_call('namecheap.domains.check', $extra_payload);
        $results = [];
        $found_results = $api_results->getJson()["CommandResponse"]["DomainCheckResult"];
        foreach ($found_results as $domain) {
            $results[$domain["Domain"]] = $domain["Available"] === 'true';
        }
        return $results;
    }

    public function reactivate() {
        throw new ApiError('MAKE THIS', 500);
    }

    public function renew() {
        throw new ApiError('MAKE THIS', 500);
    }

    public function getRegustrarLock() {
        throw new ApiError('MAKE THIS', 500);
    }

    public function setRegistrarLock() {
        throw new ApiError('MAKE THIS', 500);
    }

    public function getInfo() {
        throw new ApiError('MAKE THIS', 500);
    }
}