<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 19.04.2019
 * Time: 16:13
 */

namespace SimpleLibs\Uri;

/**
 * Class CustomValidator
 * @package App\Helpers
 * @version 1.0.0rc1
 */
class CustomUriValidator extends HttpValidator
{
    protected $allowedSchemes = [
        'http',
        'https',
        'ftp'
    ];
}
