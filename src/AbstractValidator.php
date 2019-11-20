<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 19.04.2019
 * Time: 16:13
 */

namespace SimpleLibs\Uri;

/**
 * Class AbstractValidator
 * @see https://www.w3.org/Addressing/
 * @see https://tools.ietf.org/html/rfc3986
 *
 * @package App\Helpers
 * @version 1.0.0rc1
 */
abstract class AbstractValidator
{
    /**
     * Character classes defined in RFC-3986
     */
    const CHAR_RESERVED     = ':\/\?#\[\]@!\$&\'\(\)\*\+,;=';
    const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~';
    const CHAR_GEN_DELIMS   = ':\/\?#\[\]@';
    const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';
    /**
     * Not in the spec - those characters have special meaning in urlencoded query parameters
     */
    const CHAR_QUERY_DELIMS = '!\$\'\(\)\*\,';

    protected static $defaultPorts = [
        'http'  => 80,
        'https' => 443,
        'ftp' => 21,
        'gopher' => 70,
        'nntp' => 119,
        'news' => 119,
        'telnet' => 23,
        'tn3270' => 23,
        'imap' => 143,
        'pop' => 110,
        'ldap' => 389,
    ];

    protected $allowedSchemes = [
    ];

    /** @var UriParserInterface */
    protected $parser = null;

    public $defaultParserClass = UriParser::class;

    private $lastParts = [];

    public function __construct(UriParserInterface $parser = null)
    {
        if (! $parser) {
            $this->parser = $this->createParser();
        } else {
            $this->parser = $parser;
        }
    }

    protected function createParser()
    {
        if (class_exists($this->defaultParserClass) &&
            in_array(UriParserInterface::class, class_implements($this->defaultParserClass))) {
            /** @var UriParserInterface $parser */
            $parser = new $this->defaultParserClass;
            $parser
                ->allowIpv4()
                ->allowIpv6()
                ->allowLocalIp()
                ->allowLocalDomain()
                ->allowWithoutScheme()
                ->allowWithoutAuthority()
                ->allowPunycode(false);
            return $parser;
        } else {
            throw new \RuntimeException('Can not create default parser ' . $this->defaultParserClass);
        }
    }

    public function getLastParts()
    {
        return $this->lastParts;
    }

    public function isValid(array $parts)
    {
        // To implement
        return true;
    }

    /**
     * Set the allowed schemes.
     *
     * @param array|string $allowedSchemes
     *   The schemes to allow.
     * @return $this
     * @throws \InvalidArgumentException
     *   If a scheme is empty or contains illegal characters.
     */
    public function allowSchemes($allowedSchemes)
    {
        if (empty($allowedSchemes)) {
            throw new \InvalidArgumentException("Allowed schemes cannot be empty.");
        }
        $allowedSchemes = (array) $allowedSchemes;
        $c = count($allowedSchemes);
        for ($i = 0; $i < $c; ++$i) {
            if (empty($allowedSchemes[$i])) {
                throw new \InvalidArgumentException("An allowed scheme cannot be empty.");
            } elseif (! preg_match(UriParser::SCHEME_PATTERN, $allowedSchemes[$i])) {
                throw new \InvalidArgumentException(
                    "Allowed scheme [{$allowedSchemes[$i]}] contains illegal characters (see RFC3986).");
            }
        }
        $this->allowedSchemes = $allowedSchemes;
        return $this;
    }

    public function validate($url)
    {
        $this->lastParts = $this->parser->parse($url);
        if (is_array($this->lastParts) && ! empty($this->lastParts) && $this->isValid($this->lastParts)) {
            return true;
        }
        return false;
    }
}
