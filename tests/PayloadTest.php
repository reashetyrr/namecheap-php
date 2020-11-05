<?php

namespace reashetyr\NameCheap\Tests;

require_once '../src/Payload.php';

use reashetyr\NameCheap\Payload;
use PHPUnit\Framework\TestCase;

class PayloadTest extends TestCase
{

    public function testFrom_array()
    {
        $payload_data = [
            'ApiUser' => 'test',
            'ApiKey' => 'testKey',
            'UserName' => 'test123',
            'ClientIP' => '1.2.3.4',
            'Command' => 'super.awesome.test'
        ];

        $payload = \reashetyr\NameCheap\Payload::from_array($payload_data);
        $this->assertTrue($payload instanceof Payload, 'Payload::from_array did not return an instance of Payload');
        $this->assertTrue($payload->ApiUser == 'test', 'Payload::from_array did not set the ApiUser parameter correctly');
        $this->assertTrue($payload->ApiKey == 'testKey', 'Payload::from_array did not set the ApiKey parameter correctly');
        $this->assertTrue($payload->UserName == 'test123', 'Payload::from_array did not set the UserName parameter correctly');
        $this->assertTrue($payload->ClientIP == '1.2.3.4', 'Payload::from_array did not set the ClientIP parameter correctly');
        $this->assertTrue($payload->Command == 'super.awesome.test', 'Payload::from_array did not set the Command parameter correctly');
    }

    public function testTo_query_string()
    {
        $payload = new \reashetyr\NameCheap\Payload();
        $payload->ApiKey = 'testKey'; $payload->ClientIP = '1.2.3.4'; $payload->UserName = 'test';
        $payload->ApiUser = 'test123'; $payload->Command = 'super.awesome.test'; $payload->extra = ['Domain' => 'example.com', 'FQN' => 'example.com', 'tld' => 'com'];

        $queryString = $payload->to_query_string();
        $this->assertTrue($payload instanceof Payload, 'Payload() did not return an instance of Payload');
        $this->assertStringContainsString('ApiKey=testKey', $queryString, 'Payload->to_query_string did not include the ApiKey');
        $this->assertStringContainsString('ClientIP=1.2.3.4', $queryString, 'Payload->to_query_string did not include the ClientIP');
        $this->assertStringContainsString('UserName=test', $queryString, 'Payload->to_query_string did not include the UserName');
        $this->assertStringContainsString('ApiUser=test123', $queryString, 'Payload->to_query_string did not include the ApiUser');
        $this->assertStringContainsString('Command=super.awesome.test', $queryString, 'Payload->to_query_string did not include the Command');
        $this->assertStringContainsString('Domain=example.com', $queryString, 'Payload->to_query_string did not include the extra payload information properly');
        $this->assertStringContainsString('FQN=example.com', $queryString, 'Payload->to_query_string did not include the extra payload information properly');
        $this->assertStringContainsString('tld=com', $queryString, 'Payload->to_query_string did not include the extra payload information properly');
    }

    public function testTo_array()
    {
        $payload_data = [
            'ApiUser' => 'test',
            'ApiKey' => 'testKey',
            'UserName' => 'test123',
            'ClientIP' => '1.2.3.4',
            'Command' => 'super.awesome.test'
        ];

        $payload = \reashetyr\NameCheap\Payload::from_array($payload_data);
        $payload_to_array = $payload->to_array();
        $this->assertTrue($payload instanceof Payload, 'Payload::from_array did not return an instance of Payload');
        $this->assertSame($payload_data, $payload_to_array, 'Payload->to_array did not produce the same array as used to initialize');
    }

    public function testMerge_extra()
    {
        $payload_data = [
            'ApiUser' => 'test',
            'ApiKey' => 'testKey',
            'UserName' => 'test123',
            'ClientIP' => '1.2.3.4',
            'Command' => 'super.awesome.test'
        ];

        $extra_payload = [
            'DomainName' => 'example.com',
            'years' => 10
        ];

        $payload = \reashetyr\NameCheap\Payload::from_array($payload_data);
        $this->assertTrue($payload instanceof Payload, 'Payload::from_array did not return an instance of Payload');
        $payload->merge_extra($extra_payload);
        $this->assertSame($payload->extra, $extra_payload, 'Payload->merge_extra changed the extra values');
    }
}
