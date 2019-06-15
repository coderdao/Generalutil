<?php
/**
 * Function:
 * Description:
 * Abo 2019/6/14 15:40
 * Email: abo2013@foxmail.com
 */

namespace Abo\Generalutil\V1\Utils;


class PregUtil
{
    public function numeric( string $paramStr ):array
    {
        preg_match_all( '/[0-9]*/', $paramStr, $matches, PREG_PATTERN_ORDER );
    }
}