<?php


namespace reashetyr\NameCheap;


class Payload
{
    public $ApiUser, $ApiKey, $UserName, $ClientIP, $Command, $extra = [];

    public static function from_array(array $source): self
    {
        $c = new self();

        foreach ($source as $key => $value) {
            if (property_exists(Payload::class, $key)) {
                $c->{$key} = $value;
            } else {
                $c->extra[$key] = $value;
            }
        }
        return $c;
    }

    public function merge_extra(array $extra_payload): void
    {
        foreach ($extra_payload as $key => $value) {
            $this->extra[$key] = $value;
        }
    }

    public function to_array(): array
    {
        $t = [
            'ApiUser' => $this->ApiUser,
            'ApiKey' => $this->ApiKey,
            'UserName' => $this->UserName,
            'ClientIP' => $this->ClientIP,
            'Command' => $this->Command
        ];

        foreach($this->extra as $key => $value) {
            $t[$key] = $value;
        }

        return $t;
    }

    public function to_query_string(array $value = null): string
    {
        $qs = '';
        $loopOver = get_object_vars($this);
        if ($value)
            $loopOver = $value;
        foreach ($loopOver as $key => $value) {
            if (is_array($value)) {
                $qs .= $this->to_query_string($value);
                continue;
            }
            $qs .= "${key}=${value}&";
        }

        $qs = rtrim($qs, '&');

        return $qs;
    }
}