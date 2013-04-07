<?php

namespace Ichnaea\Amqp\Mime;

class MimePart
{
    const HeaderSeparator = ":";
    const EndOfLine = "\r\n";
    private $headers = array();
    private $body;

    public function __construct()
    {
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeader($name)
    {
        if (array_key_exists($name, $this->headers)) {
            return $this->headers[$name];
        }
    }

    public function setHeader($name, $value)
    {
        $this->headers[trim($name)] = trim($value);
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function setBody($body)
    {
        if ($this->isBase64()) {
            $body = base64_encode($body);
        }
        $this->body = $body;
    }

    public function getBody()
    {
        $body = $this->body;
        if ($this->isBase64()) {
            $body = base64_decode($body);
        }

        return $body;
    }

    public function isBase64()
    {
        $encoding = $this->getHeader("Content-Transfer-Encoding");

        return strpos(strtolower($encoding), "base64") !== false;
    }

    public function __toString()
    {
        $str = "";
        foreach ($this->headers as $k=>$v) {
            $str .= $k.self::HeaderSeparator.$v.self::EndOfLine;
        }
        $str .= self::EndOfLine.$this->body;

        return $str;
    }

    public static function fromString($data)
    {
        $part = new MimePart();
        $lines = explode(self::EndOfLine, $data);
        $header = true;
        $body = "";
        foreach ($lines as $line) {
            if ($header) {
                $line = trim($line);
                if ($line) {
                    $line = explode(self::HeaderSeparator, $line);
                    $part->setHeader($line[0], $line[1]);
                } else {
                    $header = false;
                }
            } else {
                $body .= $line.self::EndOfLine;
            }
        }
        $part->setBody($body);

        return $part;
    }
}
