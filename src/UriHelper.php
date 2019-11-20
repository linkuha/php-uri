<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 19.04.2019
 * Time: 16:13
 */

namespace SimpleLibs\Uri;

/**
 * Class UriHelper
 * @package App\Helpers
 * @version 1.0.0rc1
 */
final class UriHelper
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

    const SCHEME_PATTERN = '[a-z][a-z0-9+\.-]*';

    /*
     * Removes the query string and the anchor from the given uri.
     */
    public static function cleanup($uri)
    {
        return self::cleanupQuery(self::cleanupAnchor($uri));
    }

    /*
     * Remove the query string from the uri.
     */
    public static function cleanupQuery($uri)
    {
        if (false !== $pos = strpos($uri, '?')) {
            return substr($uri, 0, $pos);
        }
        return $uri;
    }

    /*
     * Remove the anchor from the uri.
     */
    public static function cleanupAnchor($uri)
    {
        if (false !== $pos = strpos($uri, '#')) {
            return substr($uri, 0, $pos);
        }
        return $uri;
    }

    public static function formatPath($path)
    {
        $pattern = '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/}{]++|%(?![A-Fa-f0-9]{2}))/';
        return preg_replace_callback($pattern, [self::class, 'urlEncodeMatchZero'], $path);
    }

    public static function formatQueryAndFragment($component)
    {
        if (null === $component || '' === $component) {
            return $component;
        }

        $pattern = '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/';
        return preg_replace_callback($pattern, [self::class, 'urlEncodeMatchZero'], $component);
    }

    public static function formatUserInfo($user = null, $password = null)
    {
        if (null === $user) {
            return $user;
        }

        $userPattern = '/(?:[^%' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . ']++|%(?![A-Fa-f0-9]{2}))/';
        $user = preg_replace_callback($userPattern, [self::class, 'urlEncodeMatchZero'], $user);
        if (null === $password) {
            return $user;
        }

        $passwordPattern = '/(?:[^%:' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . ']++|%(?![A-Fa-f0-9]{2}))/';
        return $user.':'.preg_replace_callback($passwordPattern, [self::class, 'urlEncodeMatchZero'], $password);
    }

    private static function urlEncodeMatchZero(array $match)
    {
        return rawurlencode($match[0]);
    }

    /**
     * Removes dot segments from a path and returns the new path.
     *
     * @param string $path
     *
     * @return string
     * @link http://tools.ietf.org/html/rfc3986#section-5.2.4
     */
    public static function removePathDotSegments($path)
    {
        if ($path === '' || $path === '/') {
            return $path;
        }

        $results = [];
        $segments = explode('/', $path);
        foreach ($segments as $segment) {
            if ($segment === '..') {
                array_pop($results);
            } elseif ($segment !== '.') {
                $results[] = $segment;
            }
        }

        $newPath = implode('/', $results);

        if ($path[0] === '/' && (! isset($newPath[0]) || $newPath[0] !== '/')) {
            // Re-add the leading slash if necessary for cases like "/.."
            $newPath = '/' . $newPath;
        } elseif ($newPath !== '' && ($segment === '.' || $segment === '..')) {
            // Add the trailing slash if necessary
            // If newPath is not empty, then $segment must be set and is the last segment from the foreach
            $newPath .= '/';
        }

        return $newPath;
    }

    // apply for path or query
    public static function capitalizePercentEncoding($part)
    {
        $regex = '/(?:%[A-Fa-f0-9]{2})++/';

        $callback = function (array $match) {
            return strtoupper($match[0]);
        };

        return preg_replace_callback($regex, $callback, $part);
    }

    /**
     * Decode all percent encoded characters which are allowed to be represented literally
     *
     * Will not decode any characters which are not listed in the 'allowed' list
     *
     * @param string $input
     * @param string $allowedRegexp Pattern of allowed characters
     * @return mixed
     */
    public static function decodeUrlEncodedChars($input, $allowedRegexp = '')
    {
        $decodeCb = function ($match) use ($allowedRegexp) {
            $char = rawurldecode($match[0]);
            if (preg_match($allowedRegexp, $char)) {
                return $char;
            }
            return strtoupper($match[0]);
        };

        return preg_replace_callback('/%[A-Fa-f0-9]{2}/', $decodeCb, $input);
    }

    /**
     * Encodes a Uniform Resource Identifier (URI) by replacing non-alphanumeric
     * characters with a percent (%) sign followed by two hex digits, excepting
     * characters in the URI reserved character set.
     *
     * Assumes that the URI is a complete URI, so does not encode reserved
     * characters that have special meaning in the URI.
     *
     * Simulates the encodeURI function available in JavaScript
     * https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/encodeURI
     *
     * Source: http://stackoverflow.com/q/4929584/264628
     *
     * @param string $uri The URI to encode
     * @return string The original URL with special characters encoded
     */
    public static function encodeURI($uri)
    {
        $unescaped = array(
            '%2D'=>'-','%5F'=>'_','%2E'=>'.','%21'=>'!', '%7E'=>'~',
            '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')'
        );
        $reserved = array(
            '%3B'=>';','%2C'=>',','%2F'=>'/','%3F'=>'?','%3A'=>':',
            '%40'=>'@','%26'=>'&','%3D'=>'=','%2B'=>'+','%24'=>'$',
//            '%5B'=>'[', '%5D'=>']'
        );
        $score = array(
            '%23'=>'#'
        );
        // encode all and then replace specials
        return strtr(rawurlencode(rawurldecode($uri)), array_merge($reserved, $unescaped, $score));
    }

    /**
     * Decodes a punycode encoded string to it's original utf8 string
     * Returns false in case of a decoding failure.
     *
     * @param  string $encoded Punycode encoded string to decode (part after 'xn--')
     * @return string|false
     */
    public static function decodePunycodePart($encoded)
    {
        if (! preg_match('/^[a-z0-9-]+$/i', $encoded)) {
            // no punycode encoded string
            return false;
        }

        $decoded = [];
        $separator = strrpos($encoded, '-');
        if ($separator > 0) {
            for ($x = 0; $x < $separator; ++$x) {
                // prepare decoding matrix
                $decoded[] = ord($encoded[$x]);
            }
        }

        $lengthd = count($decoded);
        $lengthe = strlen($encoded);

        // decoding
        $init  = true;
        $base  = 72;
        $index = 0;
        $char  = 0x80;

        for ($indexe = ($separator) ? ($separator + 1) : 0; $indexe < $lengthe; ++$lengthd) {
            for ($oldIndex = $index, $pos = 1, $key = 36; 1; $key += 36) {
                $hex   = ord($encoded[$indexe++]);
                $digit = ($hex - 48 < 10) ? $hex - 22
                    : (($hex - 65 < 26) ? $hex - 65
                        : (($hex - 97 < 26) ? $hex - 97
                            : 36));

                $index += $digit * $pos;
                $tag    = ($key <= $base) ? 1 : (($key >= $base + 26) ? 26 : ($key - $base));
                if ($digit < $tag) {
                    break;
                }

                $pos = (int) ($pos * (36 - $tag));
            }

            $delta   = intval($init ? (($index - $oldIndex) / 700) : (($index - $oldIndex) / 2));
            $delta  += intval($delta / ($lengthd + 1));
            for ($key = 0; $delta > 910 / 2; $key += 36) {
                $delta = intval($delta / 35);
            }

            $base   = intval($key + 36 * $delta / ($delta + 38));
            $init   = false;
            $char  += (int) ($index / ($lengthd + 1));
            $index %= ($lengthd + 1);
            if ($lengthd > 0) {
                for ($i = $lengthd; $i > $index; $i--) {
                    $decoded[$i] = $decoded[($i - 1)];
                }
            }

            $decoded[$index++] = $char;
        }

        // convert decoded ucs4 to utf8 string
        foreach ($decoded as $key => $value) {
            if ($value < 128) {
                $decoded[$key] = chr($value);
            } elseif ($value < (1 << 11)) {
                $decoded[$key]  = chr(192 + ($value >> 6));
                $decoded[$key] .= chr(128 + ($value & 63));
            } elseif ($value < (1 << 16)) {
                $decoded[$key]  = chr(224 + ($value >> 12));
                $decoded[$key] .= chr(128 + (($value >> 6) & 63));
                $decoded[$key] .= chr(128 + ($value & 63));
            } elseif ($value < (1 << 21)) {
                $decoded[$key]  = chr(240 + ($value >> 18));
                $decoded[$key] .= chr(128 + (($value >> 12) & 63));
                $decoded[$key] .= chr(128 + (($value >> 6) & 63));
                $decoded[$key] .= chr(128 + ($value & 63));
            } else {
                return false;
            }
        }

        return implode($decoded);
    }

    // BETA functions below

    // TODO: need tests and path reference compliant
    /**
     * @param array $parts
     * @param string $return Return url type: 'full'|'root'|'base'
     * @return string
     */
    public static function build(array $parts, $return = 'full')
    {
        if (! is_array($parts)) {
            throw new \InvalidArgumentException('Parts of url must be array.');
        }
        $uri = '';
        if (isset($parts['scheme'])) {
            $uri .= "$parts[scheme]:";
        }
        $authority = self::getAuthority($parts);
        if (! empty($authority)) {
            $uri .= "//$authority";
        }
        if ($return = 'root') {
            return "$uri/";
        }
        if (isset($parts['path'])) {
            $uri .= "/$parts[path]";
        }
        if ($return = 'base') {
            return $uri;
        }
        if (isset($parts['query'])) {
            $uri .= "?$parts[query]";
        }
        if (isset($parts['fragment'])) {
            $uri .= "#$parts[fragment]";
        }
        return $uri;
    }

    public static function getAuthority(array $parts)
    {
        $authority = '';
        $userInfo = '';
        if (isset($parts['user'])) {
            $userInfo .= $parts['user'];
            if (isset($parts['pass'])) {
                $userInfo .= ":$parts[pass]";
            }
        }
        if (! empty($userInfo)) {
            $authority .= "$userInfo@";
        }
        if (isset($parts['host'])) {
            $authority .= $parts['host'];
        }
        if (isset($parts['port'])) {
            $authority .= ":$parts[port]";
        }
        return $authority;
    }

    public static function getRootUrl($url)
    {
        return self::build(parse_url($url), 'root');
    }

    public static function getBaseUrl($url)
    {
        return self::build(parse_url($url), 'base');
    }

    public static function isAbsoluteUrl($uri)
    {
        if (preg_match('~^' . self::SCHEME_PATTERN . '://~Di', $uri)) {
            return true;
        }
        return false;
    }

    /*
     * Absolute with relative scheme (network path reference)
     */
    public static function isAbsoluteUrlRS($uri)
    {
        if (substr($uri, 0, 2) === '//') {
            return true;
        }
        return false;
    }

    /**
     * Removes the URL suffix from path info.
     * @param string $pathInfo path info part in the URL
     * @param string $urlSuffix the URL suffix to be removed
     * @return string path info with URL suffix removed.
     */
    public static function removeUrlSuffix($pathInfo, $urlSuffix)
    {
        if ($urlSuffix !== '' && substr($pathInfo, -strlen($urlSuffix)) === $urlSuffix) {
            return substr($pathInfo, 0, -strlen($urlSuffix));
        } else {
            return $pathInfo;
        }
    }

    public static function decodePunycodeDomain($domain)
    {
        if (extension_loaded('intl')) {
            return idn_to_utf8($domain);
        }
        return $domain;
    }

    public static function encodePunycodeDomain($host)
    {
        if (extension_loaded('intl') && defined('INTL_IDNA_VARIANT_UTS46')) {
            $formattedHost = idn_to_ascii($host, 0, INTL_IDNA_VARIANT_UTS46, $arr);
            if (0 !== $arr['errors']) {
                // TODO
                $error = self::getIdnaErrorMessage($arr['errors']);
            }
            return $formattedHost;
        }
        return $host;
    }

    /**
     * Retrieves and format IDNA conversion error message.
     *
     * @see http://icu-project.org/apiref/icu4j/com/ibm/icu/text/IDNA.Error.html
     */
    private static function getIdnaErrorMessage($errorByte)
    {
        /**
         * IDNA errors.
         */
        $idnErrors = [
            IDNA_ERROR_EMPTY_LABEL => 'a non-final domain name label (or the whole domain name) is empty',
            IDNA_ERROR_LABEL_TOO_LONG => 'a domain name label is longer than 63 bytes',
            IDNA_ERROR_DOMAIN_NAME_TOO_LONG => 'a domain name is longer than 255 bytes in its storage form',
            IDNA_ERROR_LEADING_HYPHEN => 'a label starts with a hyphen-minus ("-")',
            IDNA_ERROR_TRAILING_HYPHEN => 'a label ends with a hyphen-minus ("-")',
            IDNA_ERROR_HYPHEN_3_4 => 'a label contains hyphen-minus ("-") in the third and fourth positions',
            IDNA_ERROR_LEADING_COMBINING_MARK => 'a label starts with a combining mark',
            IDNA_ERROR_DISALLOWED => 'a label or domain name contains disallowed characters',
            IDNA_ERROR_PUNYCODE => 'a label starts with "xn--" but does not contain valid Punycode',
            IDNA_ERROR_LABEL_HAS_DOT => 'a label contains a dot=full stop',
            IDNA_ERROR_INVALID_ACE_LABEL => 'An ACE label does not contain a valid label string',
            IDNA_ERROR_BIDI => 'a label does not meet the IDNA BiDi requirements (for right-to-left characters)',
            IDNA_ERROR_CONTEXTJ => 'a label does not meet the IDNA CONTEXTJ requirements',
        ];

        $res = [];
        foreach ($idnErrors as $error => $reason) {
            if ($errorByte & $error) {
                $res[] = $reason;
            }
        }

        return empty($res) ? 'Unknown IDNA conversion error.' : implode(', ', $res).'.';
    }
}
