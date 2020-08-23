<?php
/*
 * This file is part of the jinyPHP package.
 *
 * (c) hojinlee <infohojin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jiny\Board;

trait Setting
{
    private function setting()
    {
        //$body = json_encode($this->conf, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        //$body = nl2br($body);
        $body = "";
        foreach($this->conf as $key => $value) {
            $body .= $key."<br>";
        }
        return $body;
    }
}