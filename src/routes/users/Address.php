<?php

namespace reashetyr\NameCheap\routes\users;

use reashetyr\NameCheap\ApiError;
use reashetyr\NameCheap\NameCheap;

class Address
{
    private $namecheap;

    public function __construct(NameCheap $namecheap)
    {
        $this->namecheap = $namecheap;
    }

    public function create(string $addressName, string $emailAddress, string $firstName, string $lastName, string $address1, string $city, string $stateProvince, string $stateProvinceChoice, string $zip, string $country, string $phone, int $defaultYN = null, string $jobTitle = null, string $organization = null, string $address2 = null, string $phoneExt = null, string $fax = null): \reashetyr\NameCheap\ApiResult
    {
        $extra_payload = [
            'AddressName' => $addressName, 'DefaultYN' => $defaultYN, 'EmailAddress' => $emailAddress,
            'FirstName' => $firstName, 'LastName' => $lastName, 'JobTitle' => $jobTitle,
            'Organization' => $organization, 'Address1' => $address1, 'Address2' => $address2,
            'City' => $city, 'StateProvince' => $stateProvince, 'StateProvinceChoice' => $stateProvinceChoice,
            'Zip' => $zip, 'Country' => $country, 'Phone' => $phone, 'PhoneExt' => $phoneExt, 'Fax' => $fax
        ];
        return $this->namecheap->_call('namecheap.users.address.create', $extra_payload);
    }

    public function delete(int $addressId): \reashetyr\NameCheap\ApiResult
    {
        $extra_payload = ['AddressId' => $addressId];
        return $this->namecheap->_call('namecheap.users.address.delete', $extra_payload);
    }

    public function getInfo(int $addressId): array
    {
        $extra_payload = ['AddressId' => $addressId];
        $results = $this->namecheap->_call('namecheap.users.address.getinfo', $extra_payload);
        return $results->getJson()['CommandResponse']['GetAddressInfoResult'];
    }

    public function getList(): array
    {
        $results = $this->namecheap->_call('namecheap.users.address.getList');
        return $results->getJson()['CommandResponse']['AddressGetListResult'];
    }

    public function setDefault(int $addressId): \reashetyr\NameCheap\ApiResult
    {
        $extra_payload = ['AddressId' => $addressId];
        return $this->namecheap->_call('namecheap.users.address.setDefault', $extra_payload);
    }

    public function update(int $addressId, string $addressName, string $emailAddress, string $firstName, string $lastName, string $address1, string $city, string $stateProvince, string $stateProvinceChoice, string $zip, string $country, string $phone, int $defaultYN = null, string $jobTitle = null, string $organization = null, string $address2 = null, string $phoneExt = null, string $fax = null): \reashetyr\NameCheap\ApiResult
    {
        $extra_payload = [
            'AddressId'=> $addressId, 'AddressName' => $addressName, 'DefaultYN' => $defaultYN, 'EmailAddress' => $emailAddress,
            'FirstName' => $firstName, 'LastName' => $lastName, 'JobTitle' => $jobTitle,
            'Organization' => $organization, 'Address1' => $address1, 'Address2' => $address2,
            'City' => $city, 'StateProvince' => $stateProvince, 'StateProvinceChoice' => $stateProvinceChoice,
            'Zip' => $zip, 'Country' => $country, 'Phone' => $phone, 'PhoneExt' => $phoneExt, 'Fax' => $fax
        ];
        return $this->namecheap->_call('namecheap.users.address.update', $extra_payload);
    }
}