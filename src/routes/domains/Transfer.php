<?php

namespace reashetyr\NameCheap\routes\domains;

use reashetyr\NameCheap\ApiError;
use reashetyr\NameCheap\NameCheap;

class Transfer
{
    private $namecheap;

    public function __construct(NameCheap $namecheap)
    {
        $this->namecheap = $namecheap;
    }

    public function create(string $domainName, int $years, string $eppCode, string $promotionCode = null, string $addFreeWhoisguard = 'Yes', string $wgEnable = 'No'): \reashetyr\NameCheap\ApiResult
    {
        if (!in_array($eppCode, [".biz",".ca",".cc",".co",".com",".com.pe",".in",".info",".me",".mobi",".net, net.pe",".org",".org.pe",".pe",".tv",".us"])) throw new \reashetyr\NameCheap\ApiError('Invalid EPPCode passed', 400);
        $extra_payload = [
            'DomainName' => $domainName,
            'Years' => $years,
            'EPPCode' => $eppCode,
            'PromotionCode' => $promotionCode,
            'AddFreeWhoisguard' => $addFreeWhoisguard,
            'WGenable' => $wgEnable
        ];
        return $this->namecheap->_call('namecheap.domains.transfer.create', $extra_payload);
    }

    public function getStatus(int $transferId): array
    {
        $extra_payload = [
            'TransferID' => $transferId
        ];

        $results = $this->namecheap->_call('namecheap.domains.transfer.getStatus', $extra_payload);
        return $results->getJson()['CommandResponse']['DomainTransferGetStatusResult'];
    }

    public function updateStatus(string $transferId, string $resubmit = 'true'): \reashetyr\NameCheap\ApiResult
    {
        $extra_payload = [
             'TransferID' => $transferId,
            'Resubmit' => $resubmit
        ];
        return $this->namecheap->_call('namecheap.domains.transfer.updateStatus', $extra_payload);
    }

    public function getList(string $listType = 'ALL', string $searchTerm = null, int $page = 1, int $pageSize = 10, string $sortBy = 'DOMAINNAME'): array
    {
        if (!in_array($listType, ['ALL', 'INPROGRESS', 'CANCELLED', 'COMPLETED']))
            $listType = 'ALL';
        if (!in_array($sortBy, ["DOMAINNAME", "DOMAINNAME_DESC", "TRANSFERDATE", "TRANSFERDATE_DESC", "STATUSDATE", "STATUSDATE_DESC"]))
            $sortBy = 'DOMAINNAME';

        $extra_payload = [
            'ListType' => $listType,
            'SearchTerm' => $searchTerm,
            'Page' => $page,
            'PageSize' => $pageSize,
            'SortBy' => $sortBy
        ];
        $results = $this->namecheap->_call('namecheap.domains.Transfer.getlist', $extra_payload);

        return $results->getJson()['CommandResponse']['TransferGetListResult'];
    }
}