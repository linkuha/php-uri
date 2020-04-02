<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 16.08.2019
 */

namespace SimpleLibs\Uri;

use Laminas\Uri\Http;

class LaminasCustomUri extends Http
{
    /**
     * @see Uri::$validSchemes
     */
    protected static $validSchemes = [
        'http',
        'https',
        'ftp'
    ];

    /**
     * @see Uri::$defaultPorts
     */
    protected static $defaultPorts = [
        'http'  => 80,
        'https' => 443,
        'ftp' => 21
    ];

    public function setPort($port)
    {
        if (($port !== null) && (! self::validatePort($port))) {
            throw new \Laminas\Uri\Exception\InvalidUriPartException(sprintf(
                'Port "%s" is not valid or is not accepted by %s',
                $port,
                get_class($this)
            ), \Laminas\Uri\Exception\InvalidUriPartException::INVALID_PORT);
        }
        $this->port = $port;
        return $this;
    }

    protected static function isValidDnsHostname($host)
    {
        $validator = new \Laminas\Validator\Hostname([
            'allow' =>
                \Laminas\Validator\Hostname::ALLOW_DNS |
                \Laminas\Validator\Hostname::ALLOW_LOCAL |
                \Laminas\Validator\Hostname::ALLOW_IP
        ]);

        return $validator->isValid($host);
    }
}
