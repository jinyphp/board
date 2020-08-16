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

// 데이터객체
class Config
{
    private $conf;
    public function __construct($conf)
    {
        //echo __CLASS__;
        $this->conf = $conf;
    }

    public function listFields()
    {
        // return $this->conf['list']['fields'];
    }


    /**
     * 
     */
}