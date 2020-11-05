<?php


namespace reashetyr\NameCheap;


class ApiResult
{
    private $xml, $json;

    /**
     * @param string $result
     */
    public static function parseResult(string $result): array
    {
        $result = str_replace(array("\n", "\r", "\t"), '', $result);
        $result = trim(str_replace('"', "'", $result));

        $xml = simplexml_load_string($result);
        $json = json_decode(json_encode($xml), true);

        return ['xml' => $xml, 'json' => $json];
    }

    /**
     * @param mixed $json
     */
    public function setJson($json): void
    {
        $this->json = $json;
    }

    /**
     * @param \SimpleXMLElement $xml
     */
    public function setXml(\SimpleXMLElement $xml): void
    {
        $this->xml = $xml;
    }

    /**
     * @return mixed
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getXml(): \SimpleXMLElement
    {
        return $this->xml;
    }
}