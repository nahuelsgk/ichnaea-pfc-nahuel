<?php

namespace Ichnaea\Amqp\Mime;

/**
 * This class represents a part in MIME/Multipart data
 *
 * The part can contain a list of headers, each one with a name and a value
 * and a body.
 *
 * The part can be converted from and to a string. It automatically
 * converts from and to base64.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 * @see http://www.w3.org/Protocols/rfc1341/7_2_Multipart.html
 */
class MimePart
{
    /**
     * The string that separates the name and the value of a header
     *
     * @var string
     */
    const HeaderSeparator = ":";

    /**
     * The end of line character for separating lines. Since the standard
     * is the windows end of line, also in unix software, we use that
     *
     * @var string
     */
    const EndOfLine = "\r\n";

    /**
     * The array of headers indexed by the name
     *
     * @var array
     */
    private $headers = array();

    /**
     * The part body
     *
     * @var string
     */
    private $body;

    /**
     * Constructor
     */
    public function __construct($body="")
    {
        $this->body = $body;
    }

    /**
     * Returns all the headers indexed by name
     *
     * @return the header array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Returns the value for a header by name, null otherwise
     *
     * @param  string $name the name of the header
     * @return string the header value
     */
    public function getHeader($name)
    {
        if (array_key_exists($name, $this->headers)) {
            return $this->headers[$name];
        }
    }

    /**
     * Sets a header value
     *
     * @param string $name the name of the header
     * @param string $name the value of the header
     *
     */
    public function setHeader($name, $value)
    {
        $name = str_replace("\n", " ", $name);
        $value = str_replace("\n", " ", $value);
        $this->headers[trim($name)] = trim($value);
    }

    /**
     * Sets the entire header array
     *
     * @param array $headers the header array
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array();
        foreach ($headers as $name=>$value) {
            $this->setHeader($name, $value);
        }
    }

    /**
     * Sets the part body. If the base64 header is present
     * the body will be encoded base64 before setting it
     *
     * @param string $body the body
     */
    public function setBody($body)
    {
        if ($this->isBase64()) {
            $body = base64_encode($body);
        }
        $this->body = $body;
    }

    /**
     * Gets the part body. If the base64 header is present
     * the body will be decoded base64 before returning
     *
     * @return string the body
     */
    public function getBody()
    {
        $body = $this->body;
        if ($this->isBase64()) {
            $body = base64_decode($body);
        }

        return $body;
    }

    /**
     * Checks if the base64 header is present
     *
     * @return bool true if the base64 header is present
     */
    public function isBase64()
    {
        $encoding = $this->getHeader("Content-Transfer-Encoding");

        return strpos(strtolower($encoding), "base64") !== false;
    }

    /**
     * Converts the object to a valid MIME part
     *
     * @return string the MIME part data
     */
    public function __toString()
    {
        $str = "";
        foreach ($this->headers as $k=>$v) {
            $str .= $k.self::HeaderSeparator." ".$v.self::EndOfLine;
        }
        $str .= self::EndOfLine.$this->body;

        return $str;
    }

    /**
     * Loads the MIME part data into the object
     *
     * @param string $data the MIME part data
     */
    public static function fromString($data)
    {
        $part = new MimePart();
        $lines = explode(self::EndOfLine, $data);
        $header = true;
        $body = "";
        foreach ($lines as $k=>$line) {
            if ($header) {
                $line = trim($line);
                if ($line) {
                    $line = explode(self::HeaderSeparator, $line);
                    $part->setHeader(trim($line[0]), trim($line[1]));
                } else {
                    $header = false;
                }
            } else {
                $body .= $line;
                if ($k<count($lines)-1) {
                    $body .= self::EndOfLine;
                }
            }
        }
        $part->setBody($body);

        return $part;
    }
}
