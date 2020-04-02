<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 19.04.2019
 * Time: 16:13
 */

namespace SimpleLibs\Uri;

/**
 * Class HttpValidator
 * @package App\Helpers
 * @version 1.0.0rc1
 */
class HttpValidator extends AbstractValidator
{
    protected $allowedSchemes = [
        'http',
        'https'
    ];

    protected $allowRelative = false;

    public function __construct(UriParserInterface $parser = null, $allowRelative = false)
    {
        $this->allowRelative = $allowRelative;
        parent::__construct($parser);
    }

    protected function createParser()
    {
        return (new UriParser())
            ->allowLocalIp()
            ->allowLocalDomain()
            ->allowWithoutScheme()
            ->allowWithoutAuthority($this->allowRelative)
            ->allowPunycode(true);
    }

    public function isValid(array $parts)
    {
        if (! is_array($parts)) {
            throw new \InvalidArgumentException('Invalid parameter. Only array of parts is allowed.');
        }
        $authority = UriHelper::getAuthority($parts);
        if (empty($authority)) {
            if (
                empty($parts['scheme']) && ! empty($parts['path']) &&
                0 !== strpos($parts['path'], '/')
            ) {
                return false;
            }
        }
        if (! empty($parts['scheme'])) {
            if (! in_array($parts['scheme'], $this->allowedSchemes)) {
                return false;
            }
        }
        if (! empty($parts['host'])) {
            if (ctype_digit($parts['host'])) {
                return false;   // deny all digits host
            }
            // if not ipv4/ipv6/regname - allow only latin
            if (! preg_match('~[.:]~u', $parts['host']) && preg_match('~[^a-z0-9-_]~iu', $parts['host'])) {
                return false;
            }
        } else {
            if (! empty($parts['port'])) {
                return false;
            }
        }
        if (! empty($parts['port'])) {
            if (0 > $parts['port'] || 0xffff < $parts['port']) {
                return false;
            }
        }
        return true;
    }

    public function isSecure($url)
    {
        // consider extend
        foreach (['<script', '<iframe', 'data:image/', '<img ', ' href='] as $notSecure) {
            if (false !== mb_stripos($url, $notSecure)) {
                return false;
            }
        }
        //  7.2.  Malicious Construction
        // "well-known port" range (0 - 1023) is not checks
        return true;
    }

    // aka normalize
    public function suggestFix(array $parts)
    {
        // TODO when absolute ^//
        if (! is_array($parts)) {
            throw new \InvalidArgumentException('Invalid parameter. Only array of parts is allowed.');
        }
        if (isset($parts['host'])) {
            $parts['host'] = preg_replace('~\s+~', '', $parts['host']);
            $parts['host'] = strtolower(ltrim($parts['host'], '.'));
            if (ctype_digit($parts['host'])) {
                if (PHP_VERSION_ID >= 70100) {
                    $tryConvert = long2ip(intval($parts['host']));
                } else {
                    $tryConvert = long2ip($parts['host']);
                }
                if (is_string($tryConvert)) {
                    $parts['host'] = $tryConvert;
                }
            } else {
                if (extension_loaded('intl')) {
                    $tryNormalizeHost = \Normalizer::normalize($parts['host'], \Normalizer::FORM_KC);
                    // TODO test with UTF-8, why can not works
                    if (! intl_get_error_code() && ! empty($tryNormalizeHost)) {
                        $parts['host'] = $tryNormalizeHost;
                    }
                }
            }
        }
        $user = isset($parts['user']) ? $parts['user'] : null;
        $pass = isset($parts['pass']) ? $parts['pass'] : null;
        $userInfo =  UriHelper::formatUserInfo($user, $pass);
        $userInfoArr = explode(':', $userInfo, 2);
        if (is_array($userInfoArr) && count($userInfoArr) === 2) {
            list($parts['user'], $parts['pass']) = $userInfoArr;
        } elseif ($userInfo) {
            $parts['user'] = $userInfo;
        }
        if (isset($parts['port'])) {
            if (isset($parts['scheme'])
                && isset(static::$defaultPorts[$parts['scheme']])
                && ($parts['port'] == static::$defaultPorts[$parts['scheme']])
            ) {
                unset($parts['port']); // 6.2.3.  Scheme-Based Normalization
            }
        }
        // todo add / when empty path? 6.2.3 section
        if (isset($parts['path'])) {
            $parts['path'] = UriHelper::removePathDotSegments($parts['path']);
            $parts['path'] = UriHelper::decodeUrlEncodedChars($parts['path'], '/[' . self::CHAR_UNRESERVED . ':@&=\+\$,\/;%]/');
            $parts['path'] = UriHelper::formatPath($parts['path']);
        }
        if (isset($parts['query'])) {
            $parts['query'] = UriHelper::decodeUrlEncodedChars($parts['query'], '/[' . self::CHAR_UNRESERVED . self::CHAR_QUERY_DELIMS . ':@\/\?]/');
            $parts['query'] = UriHelper::formatQueryAndFragment($parts['query']);
        }
        if (isset($parts['fragment'])) {
            $parts['fragment'] = UriHelper::decodeUrlEncodedChars($parts['fragment'], '/[' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/\?]/');
            $parts['fragment'] = UriHelper::formatQueryAndFragment($parts['fragment']);
        }
        return \http_build_url($parts);
    }


    public function isValidHost($host)
    {
        // TODO
    }


}
