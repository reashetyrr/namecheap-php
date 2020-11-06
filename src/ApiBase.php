<?php


namespace reashetyr\NameCheap;


class ApiBase
{
    protected $ApiUser, $ApiKey, $UserName, $ClientIP, $endpoint, $payload_limit, $attempts_count, $attempts_delay;
    protected const ENDPOINTS = [
        'sandbox' => 'https://api.sandbox.namecheap.com/xml.response',
        'production' => 'https://api.namecheap.com/xml.response'
    ];

    public function __construct($ApiUser, $ApiKey, $UserName, $ClientIP, $sandbox, $attempts_count, $attempts_delay) {
        $this->ApiUser = $ApiUser;
        $this->ApiKey = $ApiKey;
        $this->UserName = $UserName;
        $this->ClientIP = $ClientIP;
        $this->endpoint = self::ENDPOINTS[$sandbox ? 'sandbox' : 'production'];
        $this->payload_limit = 10;
        $this->attempts_count = $attempts_count;
        $this->attempts_delay = $attempts_delay;
    }

    public function _call($command, $extra_payload = []): \reashetyr\NameCheap\ApiResult
    {
        $res = $this->_payload($command, $extra_payload);
        $payload = $res[0];$extra_payload = $res[1];
        return $this->_fetch($payload, $extra_payload);
    }

    public function _payload($command, $extra_payload = []): array
    {
        $payload_data = [
            'ApiUser' => $this->ApiUser,
            'ApiKey' => $this->ApiKey,
            'UserName' => $this->UserName,
            'ClientIP' => $this->ClientIP,
            'Command' => $command
        ];

        $payload = \reashetyr\NameCheap\Payload::from_array($payload_data);

        if (count($extra_payload) < $this->payload_limit) {
            $payload->merge_extra($extra_payload);
            $extra_payload = [];
        }
        return [$payload, $extra_payload];
    }

    public function _fetch(\reashetyr\NameCheap\Payload $payload, array $extra_payload = []): \reashetyr\NameCheap\ApiResult
    {
        $attempts_left = $this->attempts_count;
        $response_content = '';
        while ($attempts_left > 0) {
            $parameters = $payload->to_query_string();
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
                throw new \reashetyr\NameCheap\ApiError('An error occured', $response_code);

            curl_close($r);
            if (200 <= $response_code && $response_code <= 299) {
                break;
            }

            if ($attempts_left <= 1)
                throw new \reashetyr\NameCheap\ApiError('Did not receive 200 (Ok) response');


            sleep($this->attempts_delay);
            $attempts_left--;
        }

        $apiResponse = new \reashetyr\NameCheap\ApiResult();
        $response = \reashetyr\NameCheap\ApiResult::parseResult($response_content);
        $xml = $response['xml'];

        $apiResponse->setJson($response['json']);
        $apiResponse->setXml($xml);

        if ((string) $xml['Status'] === 'ERROR') {
            $error = (string) $xml->Errors->{'Error'};
            throw new \reashetyr\NameCheap\ApiError($error, 400);
        }
        return $apiResponse;
    }

    public function _list_of_objects_to_numbered_payload($objects) {
        $definite_payload = [];
        for ($i = 0; $i < count($objects); $i++) {
            $key_num = $i + 1;
            foreach ($objects[$i] as $object_key => $object) {
                $definite_payload["${object_key}${key_num}"] = $object;
            }
        }
        return $definite_payload;
    }
}