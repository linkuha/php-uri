<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 16.08.2019
 * Time: 17:50
 */

namespace SimpleLibs\Uri;

class ZendCustomUri extends \Zend\Uri\Http
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
            throw new \Zend\Uri\Exception\InvalidUriPartException(sprintf(
                'Port "%s" is not valid or is not accepted by %s',
                $port,
                get_class($this)
            ), \Zend\Uri\Exception\InvalidUriPartException::INVALID_PORT);
        }
        $this->port = $port;
        return $this;
    }

    protected static function isValidDnsHostname($host)
    {
        $validator = new \Zend\Validator\Hostname([
            'allow' =>
                \Zend\Validator\Hostname::ALLOW_DNS |
                \Zend\Validator\Hostname::ALLOW_LOCAL |
                \Zend\Validator\Hostname::ALLOW_IP
        ]);

        return $validator->isValid($host);
    }
}
