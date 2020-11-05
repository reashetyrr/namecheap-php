<?php

namespace reashetyr\NameCheap;


class LazyGetListIterator implements \Iterator
{
    private NameCheap $api;
    private $payload, $results, $i;

    public function __constructor(NameCheap $api, $payload) {
        $this->api = $api;
        $this->payload = $payload;
        $this->results = [];
        $this->i = -1;
    }

    public function _get_more_results() {
        $json_result = $this->api->_fetch_json($this->payload);
        $namespace = NameCheap::NAMESPACE;
        $domains = $json_result["${namespace}CommandResponse"]["${namespace}DomainGetListResult"]["${namespace}Domain"];
        foreach($domains as $domain) {
            $this->results [] = $domain;
        }
        $this->payload['Page']++;
    }

    public function current()
    {
        return $this->results[$this->i];
    }

    public function next()
    {
        $this->i++;
        if ($this->i >= count($this->results))
            $this->_get_more_results();
    }

    public function key()
    {
        return $this->i;
    }

    public function valid()
    {
        return isset($this->results[$this->i]);
    }

    public function rewind()
    {
        $this->i = 0;
    }
}