<?php


namespace reashetyr\NameCheap;

class NameCheap
{
    public const ENDPOINTS = [
        'sandbox' => 'https://api.sandbox.namecheap.com/xml.response',
        'production' => 'https://api.namecheap.com/xml.response'
    ], NAMESPACE = 'http://api.namecheap.com/xml.response',
    DEFAULT_ATTEMPTS_COUNT = 1, DEFAULT_ATTEMPTS_DELAY = 0.01;

    private $ApiUser, $ApiKey, $UserName, $ClientIP, $endpoint, $payload_limit, $attempts_count, $attempts_delay;

    public function __construct($ApiUser, $ApiKey, $UserName, $ClientIP, $sandbox = true, $attempts_count = self::DEFAULT_ATTEMPTS_COUNT, $attempts_delay = self::DEFAULT_ATTEMPTS_DELAY) {
        $this->ApiUser = $ApiUser;
        $this->ApiKey = $ApiKey;
        $this->UserName = $UserName;
        $this->ClientIP = $ClientIP;
        $this->endpoint = self::ENDPOINTS[$sandbox ? 'sandbox' : 'production'];
        $this->payload_limit = 10;
        $this->attempts_count = $attempts_count;
        $this->attempts_delay = $attempts_delay;
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

    public function _call($command, $extra_payload = []) {
        $res = $this->_payload($command, $extra_payload);
        $payload = $res[0];$extra_payload = $res[1];
        return $this->_fetch_json($payload, $extra_payload);
    }

    public function _payload($command, $extra_payload = []) {
        $payload = [
            'ApiUser' => $this->ApiUser,
            'ApiKey' => $this->ApiKey,
            'UserName' => $this->UserName,
            'ClientIP' => $this->ClientIP,
            'Command' => $command
        ];
        if (count($extra_payload) < $this->payload_limit) {
            $payload = array_merge($payload, $extra_payload);
            $extra_payload = [];
        }
        return [$payload, $extra_payload];
    }

    public function _fetch_json($payload, $extra_payload = []) {
        $attempts_left = $this->attempts_count;
        $response_content = '';
        while ($attempts_left > 0) {
            $parameters = http_build_query($payload);
            $endpoint = $this->endpoint;

            $defaults = array(
                CURLOPT_URL => "${endpoint}?${parameters}",
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true
            );
            $r = curl_init();
            curl_setopt_array($r, $defaults);

            if ($extra_payload) {
                curl_setopt($r, CURLOPT_POSTFIELDS, $extra_payload);
            }
            $response_content = curl_exec($r);

            $response_code = curl_getinfo($r, CURLINFO_HTTP_CODE);
            if(curl_error($r))
                throw new \App\Helpers\ApiError('An error occured', $response_code);

            curl_close($r);
            if (200 <= $response_code && $response_code <= 299) {
                break;
            }

            if ($attempts_left <= 1)
                throw new \App\Helpers\ApiError('Did not receive 200 (Ok) response');


            sleep($this->attempts_delay);
            $attempts_left--;
        }

        $response_content = str_replace(array("\n", "\r", "\t"), '', $response_content);
        $reponse_content = trim(str_replace('"', "'", $response_content));
        $xml = simplexml_load_string($reponse_content);
        $result = json_decode(json_encode($xml), true);

        if ($result['@attributes']['Status'] === 'Error') {
            $error = $result["Errors"]["Error"];
            throw new ApiError($error['Number'], $error["text"]);
        }
        return $result;
    }

    public static function _list_of_objects_to_numbered_payload($objects) {
        $definite_payload = [];
        for ($i = 0; $i < count($objects); $i++) {
            $key_num = $i + 1;
            foreach ($objects[$i] as $object_key => $object) {
                $definite_payload["${object_key}${key_num}"] = $object;
            }
        }
        return $definite_payload;
    }

    public function domains_check($domains) {
        if (!is_array($domains))
            $domains = [$domains];

        $extra_payload = ['DomainList' => join(',', $domains)];
        $json = $this->_call('namecheap.domains.check', $extra_payload);
        $results = [];
        $found_results = $json["CommandResponse"]["DomainCheckResult"];
        foreach ($found_results as $domain) {
            $results[$domain["Domain"]] = $domain["Available"] === 'true';
        }
        return $results;
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