<?php
/*
 * This file is part of the jinyPHP package.
 *
 * (c) hojinlee <infohojin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jiny\Board\State;

abstract class Table
{
    protected function error($msg)
    {
        $error = new \Jiny\App\Error($msg);
        return $error->main();
    }

}