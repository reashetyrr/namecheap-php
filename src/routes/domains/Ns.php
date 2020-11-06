<?php

namespace reashetyr\NameCheap\routes\domains;

use reashetyr\NameCheap\NameCheap;

class Ns
{
    private $namecheap;

    public function __construct(NameCheap $namecheap)
    {
        $this->namecheap = $namecheap;
    }

    public function create(string $sld, string $tld, string $nameserver, string $ip): \reashetyr\NameCheap\ApiResult
    {
        $extra_payload = [
            'SLD' => $sld,
            'TLD' => $tld,
            'Nameserver' => $nameserver,
            'IP' => $ip
        ];
        return $this->namecheap->_call('namecheap.domains.ns.create', $extra_payload);
    }

    public function delete(string $sld, string $tld, string $nameserver): \reashetyr\NameCheap\ApiResult
    {
        $extra_payload = [
            'SLD' => $sld,
            'TLD' => $tld,
            'Nameserver' => $nameserver
        ];
        return $this->namecheap->_call('namecheap.domains.ns.delete', $extra_payload);
    }

    public function getInfo(string $sld, string $tld, string $nameserver): array
    {
        $extra_payload = [
            'SLD' => $sld,
            'TLD' => $tld,
            'Nameserver' => $nameserver
        ];
        $results = $this->namecheap->_call('namecheap.domains.ns.getInfo', $extra_payload);
        return $results->getJson()['CommandRepsponse']['DomainNSInfoResult'];
    }

    public function update(string $sld, string $tld, string $nameserver, string $oldIp, string $ip): \reashetyr\NameCheap\ApiResult
    {
        $extra_payload = [
            'SLD' => $sld,
            'TLD' => $tld,
            'Nameserver' => $nameserver,
            'OldIP' => $oldIp,
            'IP' => $ip
        ];
        return $this->namecheap->_call('namecheap.domains.ns.update', $extra_payload);
    }
}