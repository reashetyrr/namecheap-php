<?php

namespace reashetyr\NameCheap\routes\domains;

use reashetyr\NameCheap\ApiError;
use reashetyr\NameCheap\ApiResult;
use reashetyr\NameCheap\NameCheap;

class Dns
{
    private $namecheap;

    public function __construct(NameCheap $namecheap)
    {
        $this->namecheap = $namecheap;
    }

    public function setDefault(string $sld, string $tld): \reashetyr\NameCheap\ApiResult
    {
        $extra_payload = [
            'SLD' => $sld,
            'TLD' => $tld
        ];
        return $this->namecheap->_call('namecheap.domains.dns.setDefault', $extra_payload);
    }

    public function setCustom(string $sld, string $tld, string $nameservers): \reashetyr\NameCheap\ApiResult
    {
        if (is_array($nameservers))
            $nameservers = join(',', $nameservers);

        $extra_payload = [
            'SLD' => $sld,
            'TLD' => $tld,
            'Nameservers' => $nameservers
        ];
        return $this->namecheap->_call('namecheap.domains.dns.setCustom', $extra_payload);
    }

    public function getList(string $sld, string $tld): array
    {
        $extra_payload = ['SLD'=>$sld, 'TLD'=>$tld];
        $results = $this->namecheap->_call('namecheap.domains.dns.getList', $extra_payload);
        return $results->getJson()['CommandResponse']['DomainDNSGetListResult'];
    }

    public function getHosts(string $sld, string $tld): array
    {
        $extra_payload = ['SLD'=>$sld, 'TLD'=>$tld];
        $results = $this->namecheap->_call('namecheap.domains.dns.getHosts', $extra_payload);
        return $results->getJson()['CommandResponse']['DomainDNSGetHostsResult'];
    }

    public function getEmailForwarding(string $domainName): array
    {
        $extra_payload = ['DomainName'=>$domainName];
        $results = $this->namecheap->_call('namecheap.domains.dns.getEmailForwarding', $extra_payload);
        return $results->getJson()['CommandResponse']['DomainDNSGetEmailForwardingResult'];
    }

    public function setEmailForwarding(string $domainName, array $mailboxes, array $forwards): \reashetyr\NameCheap\ApiResult
    {
        $extra_payload = [
            'DomainName' => $domainName
        ];

        $t = $this->namecheap->_list_of_objects_to_numbered_payload($mailboxes);
        $extra_payload = array_merge($extra_payload, $t);
        $t = $this->namecheap->_list_of_objects_to_numbered_payload($forwards);
        $extra_payload = array_merge($extra_payload, $t);
        return $this->namecheap->_call('namecheap.domains.dns.setEmailForwarding', $extra_payload);
    }

    public function setHosts($domain, $host_records): \reashetyr\NameCheap\ApiResult
    {
        $split_domain = explode('.', $domain);
        $sld = $split_domain[0]; $tld = $split_domain[1];

        $extra_payload = ['SLD' => $sld, 'TLD' => $tld];
        $t = $this->namecheap->_list_of_objects_to_numbered_payload($host_records);
        $extra_payload = array_merge($extra_payload, $t);
        return $this->namecheap->_call('namecheap.domains.dns.setHosts', $extra_payload);
    }

}