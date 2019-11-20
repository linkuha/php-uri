<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 07.09.2019
 * Time: 12:48
 */

namespace SimpleLibs\Uri;

interface UriParserInterface
{
    const EXC_BOOLEAN_PARAM = 'The allowed flag parameter must be boolean.';

    /**
     * @param bool $allowedFlag
     * @return static
     */
    public function allowPunycode($allowedFlag = true);

    /**
     * @param bool $allowedFlag
     * @return static
     */
    public function allowIpv4($allowedFlag = true);

    /**
     * @param bool $allowedFlag
     * @return static
     */
    public function allowIpv6($allowedFlag = true);

    /**
     * @param bool $allowedFlag
     * @return static
     */
    public function allowLocalIp($allowedFlag = true);

    /**
     * @param bool $allowedFlag
     * @return static
     */
    public function allowLocalDomain($allowedFlag = true);

    /**
     * @param bool $allowedFlag
     * @return static
     */
    public function allowWithoutScheme($allowedFlag = true);

    /**
     * @param bool $allowedFlag
     * @return static
     */
    public function allowWithoutAuthority($allowedFlag = true);

    /**
     * Parse uri for next actions
     *
     * @param string $uri
     * @return false|array If the url is empty or incorrect type - false, otherwise - Url parts array.
     */
    public function parse($uri);
}
