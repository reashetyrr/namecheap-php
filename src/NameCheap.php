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

    private function setupBase() {
        $this->domains = new routes\Domains($this);
    }

    public function sandboxMode($sandbox = true) {
        $this->endpoint = parent::ENDPOINTS[$sandbox ? 'sandbox' : 'production'];

        // Reinitialize the base routes/objects
        $this->setupBase();
    }

    public function domains_create ($domain_name, $first_name, $last_name, $address1, $city, $state_province, $postal_code, $country, $phone, $email_address, $address2 = null, $years=1, $whois_guard = false) {
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

        return $this->_call('namecheap.domains.create', $extra_payload);
    }

    public function domains_getContacts($domainName) {
        $extra_payload = ['DomainName' => $domainName];
        $json = $this->_call('namecheap.domains.getContacts', $extra_payload);
        $results = [];
        $found_results = $json['CommandResponse']['DomainContactsResult'];
        foreach ($found_results as $contact_type => $contact_info) {
            if ($contact_type === '@attributes' || $contact_type === 'CurrentAttributes') continue;
            $results[$contact_type] = $contact_info;
        }
        return $results;
    }

    public function domains_dns_setHosts($domain, $host_records) {
        $extra_payload = NameCheap::_list_of_objects_to_numbered_payload($host_records);
        $split_domain = explode('.', $domain);
        $sld = $split_domain[0]; $tld = $split_domain[1];
        $extra_payload = array_merge($extra_payload, ['SLD' => $sld, 'TLD' => $tld]);
        $this->_call('namecheap.domains.dns.setHosts', $extra_payload);
    }
}