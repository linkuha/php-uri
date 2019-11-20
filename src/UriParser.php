<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 07.09.2019
 * Time: 12:48
 */

namespace SimpleLibs\Uri;

/**
 * Class UriParser
 * @see https://www.w3.org/Addressing/
 * @see https://tools.ietf.org/html/rfc3986
 *
 * @package App\Helpers\Uri
 */
class UriParser implements UriParserInterface
{
    const SCHEME_PATTERN = '[a-z][a-z0-9+\.-]*';

    private $allowPunycode = true;

    private $allowIpv4 = true;

    private $allowIpv6 = true;

    private $allowLocalIp = true;

    private $allowLocalDomain = false;

    private $allowWithoutScheme = false;

    private $allowWithoutAuthority = false;

    public function allowPunycode($allowedFlag = true)
    {
        if (! is_bool($allowedFlag)) {
            throw new \InvalidArgumentException(self::EXC_BOOLEAN_PARAM);
        }
        $this->allowPunycode = $allowedFlag;
        return $this;
    }

    public function allowIpv4($allowedFlag = true)
    {
        if (! is_bool($allowedFlag)) {
            throw new \InvalidArgumentException(self::EXC_BOOLEAN_PARAM);
        }
        $this->allowIpv4 = $allowedFlag;
        return $this;
    }

    public function allowIpv6($allowedFlag = true)
    {
        if (! is_bool($allowedFlag)) {
            throw new \InvalidArgumentException(self::EXC_BOOLEAN_PARAM);
        }
        $this->allowIpv6 = $allowedFlag;
        return $this;
    }

    public function allowLocalIp($allowedFlag = true)
    {
        if (! is_bool($allowedFlag)) {
            throw new \InvalidArgumentException(self::EXC_BOOLEAN_PARAM);
        }
        $this->allowLocalIp = $allowedFlag;
        return $this;
    }

    public function allowLocalDomain($allowedFlag = true)
    {
        if (! is_bool($allowedFlag)) {
            throw new \InvalidArgumentException(self::EXC_BOOLEAN_PARAM);
        }
        $this->allowLocalDomain = $allowedFlag;
        return $this;
    }

    public function allowWithoutScheme($allowedFlag = true)
    {
        if (! is_bool($allowedFlag)) {
            throw new \InvalidArgumentException(self::EXC_BOOLEAN_PARAM);
        }
        $this->allowWithoutScheme = $allowedFlag;
        return $this;
    }

    public function allowWithoutAuthority($allowedFlag = true)
    {
        if (! is_bool($allowedFlag)) {
            throw new \InvalidArgumentException(self::EXC_BOOLEAN_PARAM);
        }
        $this->allowWithoutAuthority = $allowedFlag;
        return $this;
    }

    public static function regexpIpv4($allowLocalIp = true)
    {
        return ($allowLocalIp === false ? (
                // exclusion private & local networks
                '(?!10(?:\.\d{1,3}){3})' .
                '(?!127(?:\.\d{1,3}){3})' .
                '(?!169\.254(?:\.\d{1,3}){2})' .
                '(?!192\.168(?:\.\d{1,3}){2})' .
                '(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})') : "") .
            // IP address dotted notation octets
            // * excludes loopback network 0.0.0.0
            // * excludes reserved space >= 224.0.0.0, not handled by hardware as host
            // ** Class D, 224-239 https://en.wikipedia.org/wiki/Multicast_address
            // ** Class E, 240-255 was experimental
            // * excludes network & broacast addresses (first & last IP address of each class)
            '(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])' .
            '(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}' .
            '(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))';
    }

    // IPv6 addresses:
    //  zero compressed IPv6 addresses (section 2.2 of rfc5952)
    //  link-local IPv6 addresses with zone index (section 11 of rfc4007)
    //  IPv4-Embedded IPv6 Address (section 2 of rfc6052)
    //  IPv4-mapped IPv6 addresses (section 2.1 of rfc2765)
    //  IPv4-translated addresses (section 2.1 of rfc2765)

    // IPv6 RegEx - http://stackoverflow.com/a/17871737/273668
    // IP-literal (v6 or later) must be within square brackets
    public static function regexpIpv6()
    {
        return '\[(?:' .
            '(?:[0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|' .         // 1:2:3:4:5:6:7:8
            '(?:[0-9a-fA-F]{1,4}:){1,7}:|' .                        // 1::                              1:2:3:4:5:6:7::
            '(?:[0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|' .        // 1::8             1:2:3:4:5:6::8  1:2:3:4:5:6::8
            '(?:[0-9a-fA-F]{1,4}:){1,5}(?::[0-9a-fA-F]{1,4}){1,2}|' . // 1::7:8           1:2:3:4:5::7:8  1:2:3:4:5::8
            '(?:[0-9a-fA-F]{1,4}:){1,4}(?::[0-9a-fA-F]{1,4}){1,3}|' . // 1::6:7:8         1:2:3:4::6:7:8  1:2:3:4::8
            '(?:[0-9a-fA-F]{1,4}:){1,3}(?::[0-9a-fA-F]{1,4}){1,4}|' . // 1::5:6:7:8       1:2:3::5:6:7:8  1:2:3::8
            '(?:[0-9a-fA-F]{1,4}:){1,2}(?::[0-9a-fA-F]{1,4}){1,5}|' . // 1::4:5:6:7:8     1:2::4:5:6:7:8  1:2::8
            '[0-9a-fA-F]{1,4}:(?:(?::[0-9a-fA-F]{1,4}){1,6})|' .      // 1::3:4:5:6:7:8   1::3:4:5:6:7:8  1::8
            ':(?:(?::[0-9a-fA-F]{1,4}){1,7}|:)|' .                    // ::2:3:4:5:6:7:8  ::2:3:4:5:6:7:8 ::8       ::
            'fe80:(?::[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|' .      // fe80::7:8%eth0   fe80::7:8%1     (link-local IPv6 addresses with zone index)
            '::(?:ffff(?::0{1,4}){0,1}:){0,1}' .
            '(?:(?:25[0-5]|(?:2[0-4]|1{0,1}[0-9]){0,1}[0-9]).){3,3}' .
            '(?:25[0-5]|(?:2[0-4]|1{0,1}[0-9]){0,1}[0-9])|' .         // ::255.255.255.255   ::ffff:255.255.255.255  ::ffff:0:255.255.255.255 (IPv4-mapped IPv6 addresses and IPv4-translated addresses)
            '(?:[0-9a-fA-F]{1,4}:){1,4}:' .
            '(?:(?:25[0-5]|(?:2[0-4]|1{0,1}[0-9]){0,1}[0-9]).){3,3}' .
            '(?:25[0-5]|(?:2[0-4]|1{0,1}[0-9]){0,1}[0-9])' .          // 2001:db8:3:4::192.0.2.33  64:ff9b::192.0.2.33 (IPv4-Embedded IPv6 Address)
            ')\]';
    }

    /**
     * Parse url for next actions
     * UTF-8 supported
     *
     * @param string $uri
     * @return false|array If the url is empty or incorrect type - false, otherwise - Url parts array.
     */
    public function parse($uri)
    {
        if (! is_string($uri)) {
            return false;
        }

        if (extension_loaded('intl')) {
            // NFC form is a requirement for a valid URL.
            if (! \Normalizer::isNormalized($uri, \Normalizer::NFKC)) {
//            throw new \InvalidArgumentException("URL must be in Unicode normalization form NFKC.");
                return false;
            }
        }

        if ('' === ($uri = trim($uri))) {
            return false;
        }

//        $allowedSchemes = join('|', $this->allowedSchemes);

        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            throw new \ErrorException($errstr, $errno, $errno, $errfile, $errline);
        });

        // NOTE! i wanted optimize this pattern to avoid maximal pattern length limit error
        // but throughout DEFINE in pattern regexp does not correctly works
        // todo check again?

        $patternFull = '~^' .			// begin string
            '(?:(?P<scheme>' . self::SCHEME_PATTERN . '):)' .      // scheme [1]
            ( $this->allowWithoutScheme ? '?' : '' ) .
            '(?://(?:(?P<userinfo>[^\s#?/]+(?::[^\s#?/]*)?)@)?' .    // (auth login:(password)) [2] // not RFC compliant, for workable usages
            '(?:' .
            ( $this->allowPunycode === false ?
                '(?P<host>' . ($this->allowLocalDomain ? '(?:[a-z\x{00a1}-\x{fffff}0-9-_]{1,63}\.?)|' : '') . // host [3]
                '(?:(?:[a-z\x{00a1}-\x{fffff}0-9-_]{1,63})' .
                '(?:\.[a-z\x{00a1}-\x{fffff}0-9-_]{1,63})*' .
                '(?:\.[a-z\x{00a1}-\x{fffff}]{1,63}\.?)' .		// TLD [4], in future may be need to add numbers support at TLD
                '))'
                :
                // IDN support
                '(?P<host>' . ($this->allowLocalDomain ?
                    '(?:[a-z\x{00a1}-\x{fffff}0-9-_]{1,63}\.?)|' : '') . // host [3] (local - not UTF-8)
                '(?:(?:xn--[a-z0-9\-]{1,59}|(?:[a-z\x{00a1}-\x{fffff}0-9-_]{1,63}))' .
                '(?:\.(?:xn--[a-z0-9\-]{1,59}|[a-z\x{00a1}-\x{fffff}0-9-_]{1,63}))*' .
                '(?:\.(?:xn--[a-z0-9\-]{1,59}|(?:[a-z\x{00a1}-\x{fffff}]{1,63}))\.?)' .        // TLD [4]
                '))'
            ) .
            ( $this->allowIpv4 === false ? '' : '|(?P<ip4>' . self::regexpIpv4($this->allowLocalIp) . ')' ) .
            ( $this->allowIpv6 === false ? '' : '|(?P<ip6>' . self::regexpIpv6() . ')' ) .
            ')' .
            '(?::(?P<port>\d{2,5})?)?)' .      // (port) [5]
            ( $this->allowWithoutAuthority ? '?' : '') .
            '(?:(?P<segments>[/#?]{1}.*))?' .   // (path, fragment, query) [6]
            // path require but may be empty
            '$~iuS';                         // end string.
//        file_put_contents('full.txt', $patternFull);

        $validRes = preg_match($patternFull, $uri, $matches);
        restore_error_handler();
        if (1 !== $validRes) {
            if ($this->allowWithoutScheme && $this->allowWithoutAuthority) {
                if (1 !== preg_match('~^(?:(?P<scheme>' . self::SCHEME_PATTERN . '):)(?:(?P<segments>.*))?~iuS', $uri, $matches)) {
                    $matches['segments'] = $uri;
                }
            } else {
                return false;
            }
        }

        $uriParts = [];
        if (! empty($matches['scheme'])) { $uriParts['scheme'] = $matches['scheme']; }

        if (! empty($matches['host'])) {
            $uriParts['host'] = $matches['host'];
            foreach (explode('.', $uriParts['host']) as $domainPart) {
                if (
                    1 === preg_match('~^[^a-z\x{00a1}-\x{fffff}0-9]~iu', $domainPart) ||
                    1 === preg_match('~.*[^a-z\x{00a1}-\x{fffff}0-9]$~iu', $domainPart)
                ) {
                    // deny begin and end on symbols '-' or '_'
                    return false;
                }
            }
        } elseif (! empty($matches['ip4'])) {
            $uriParts['host'] = $matches['ip4'];
        } elseif (! empty($matches['ip6'])) {
            $uriParts['host'] = $matches['ip6'];   // filter_var with FILTER_FLAG_IPV6 is not fully correct
        }

        if (! empty($matches['port'])) {
            $uriParts['port'] = intval($matches['port']);
        }

        if (! empty($matches['userinfo'])) {
            $userInfo = explode(':', $matches['userinfo'], 2);
            if (empty($userInfo)) {
                $uriParts['user'] = $matches['userinfo'];
            } else {
                if (isset($userInfo[0])) { $uriParts['user'] = $userInfo[0]; }
                if (isset($userInfo[1])) { $uriParts['pass'] = $userInfo[1]; }
            }
        }
        $tail = isset($matches['segments']) ? $matches['segments'] : "";
        if ('' !== $tail) {
            $forFragment = explode('#', $tail, 2);
//            var_dump($forFragment);
            if (! empty($forFragment)) {
                if (isset($forFragment[0])) { $tail = $forFragment[0]; }
                if (isset($forFragment[1])) { $uriParts['fragment'] = $forFragment[1]; }
            }
            $forQuery = explode('?', $tail, 2);
//            var_dump($forQuery);
            if (! empty($forQuery)) {
                if (isset($forQuery[0]) && ! empty($forQuery[0])) { $uriParts['path'] = $forQuery[0]; }
                if (isset($forQuery[1])) { $uriParts['query'] = $forQuery[1]; }
            }
        }
//        var_dump($uriParts);
        return $uriParts;
    }
}
