<?php

namespace reashetyr\NameCheap\routes;

use reashetyr\NameCheap\ApiResult;
use reashetyr\NameCheap\NameCheap;
use reashetyr\NameCheap\routes\domains\Dns;
use reashetyr\NameCheap\routes\domains\Ns;
use reashetyr\NameCheap\routes\domains\Transfer;

class Domains
{
    private $namecheap;
    public $dns, $ns, $transfer;

    public function __construct(NameCheap $namecheap)
    {
        $this->namecheap = $namecheap;
        $this->dns = new Dns($namecheap);
        $this->ns = new Ns($namecheap);
        $this->transfer = new Transfer($namecheap);
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

    public function getContacts(string $domainname): array
    {
        $extra_params = [
            'DomainName' => $domainname
        ];

        $results = $this->namecheap->_call('namecheap.domains.getContacts', $extra_params);
        $foundResults = $results->getJson()['CommandResponse']['DomainContactsResult'];
        $domainContacts = [];
        foreach($foundResults as $contactType => $contactInfo) {
            if ($contactType === '@attributes' || $contactType === 'CurrentAttributes') continue;

            $domainContacts[$contactType] = $contactInfo;
        }
        return $domainContacts;
    }

    public function create($domain_name, $first_name, $last_name, $address1, $city, $state_province, $postal_code, $country, $phone, $email_address, $address2 = null, $years=1, $whois_guard = false): ApiResult
    {
        $contact_types = ['Registrant', 'Tech', 'Admin', 'AuxBilling'];
        $extra_payload = [
            'DomainName' => $domain_name,
            'years' => $years
        ];

        if ($whois_guard) {
            $extra_payload = array_push($extra_payload, [
                'AddFreeWhoisguard' => 'yes',
                'WGEnabled' => 'yes'
            ]);
        }

        foreach ($contact_types as $contact_type) {
            $extra_payload = array_push($extra_payload, [
                "${contact_type}FirstName" => $first_name,
                "${contact_type}LastName" => $last_name,
                "${contact_type}Address1" => $address1,
                "${contact_type}City" => $city,
                "${contact_type}StateProvince" => $state_province,
                "${contact_type}PostalCode" => $postal_code,
                "${contact_type}Country" => $country,
                "${contact_type}Phone" => $phone,
                "${contact_type}EmailAddress" => $email_address
            ]);
            if ($address2) {
                $extra_payload["${contact_type}Address2"] = $address2;
            }
        }

        return $this->namecheap->_call('namecheap.domains.create', $extra_payload);
    }

    public function getTldList(): \reashetyr\NameCheap\ApiResult
    {
        return $this->namecheap->_call('namecheap.domains.gettldlist');
    }

    public function setContacts($first_name,$last_name,$address1,$city,$state_province,$postal_code,$country,$phone,$email_address,$address2=null):  \reashetyr\NameCheap\ApiResult
    {
        $contact_types = ['Registrant', 'Tech', 'Admin', 'AuxBilling'];
        $extra_payload = [];

        foreach ($contact_types as $contact_type) {
            $extra_payload = array_push($extra_payload, [
                "${contact_type}FirstName" => $first_name,
                "${contact_type}LastName" => $last_name,
                "${contact_type}Address1" => $address1,
                "${contact_type}City" => $city,
                "${contact_type}StateProvince" => $state_province,
                "${contact_type}PostalCode" => $postal_code,
                "${contact_type}Country" => $country,
                "${contact_type}Phone" => $phone,
                "${contact_type}EmailAddress" => $email_address
            ]);
            if ($address2) {
                $extra_payload["${contact_type}Address2"] = $address2;
            }
        }

        return $this->namecheap->_call('namecheap.domains.setContacts', $extra_payload);
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

    public function reactivate(string $domainName, string $promotionCode=null, int $yearsToAdd = 2, bool $isPremiumDomain = false, int $premiumPrice=null):  \reashetyr\NameCheap\ApiResult
    {
        $extra_payload = [
            'DomainName' => $domainName,
            'PromotionCode' => $promotionCode,
            'YearsToAdd' => $yearsToAdd,
            'IsPremiumDomain' => $isPremiumDomain,
            'PremiumPrice' => $premiumPrice
        ];
        return $this->namecheap->_call('namecheap.domains.reactivate', $extra_payload);
    }

    public function renew(string $domainName, int $years, string $promotionCode = null, bool $isPremiumDomain = false, int $premiumPrice = null):  \reashetyr\NameCheap\ApiResult
    {
        $extra_payload = [
            'DomainName' => $domainName,
            'PromotionCode' => $promotionCode,
            'Years' => $years,
            'IsPremiumDomain' => $isPremiumDomain,
            'PremiumPrice' => $premiumPrice
        ];
        return $this->namecheap->_call('namecheap.domains.renew', $extra_payload);
    }

    public function getRegistrarLock(string $domain): array
    {
        $extra_payload = ['DomainName' => $domain];
        $result = $this->namecheap->_call('namecheap.domains.getRegistrarLock', $extra_payload);
        return $result->getJson()['CommandResponse']['DomainGetRegistrarLockResult'];
    }

    public function setRegistrarLock(string $domainName, string $lockAction = 'LOCK'): \reashetyr\NameCheap\ApiResult
    {
        if (!!in_array($lockAction, ['LOCK', 'UNLOCK']))
            $lockAction = 'LOCK';

        $extra_payload = [
            'DomainName' => $domainName,
            'LockAction' => $lockAction
        ];
        return $this->namecheap->_call('namecheap.domains.setRegistrarLock', $extra_payload);
    }

    public function getInfo(string $domainName, string $hostName = null): array
    {
        $extra_payload = [
            'DomainName' => $domainName,
            'HostName' => $hostName
        ];
        $results = $this->namecheap->_call('namecheap.domains.getinfo', $extra_payload);
        return $results->getJson()['CommandResponse']['DomainGetInfoResult'];
    }
}