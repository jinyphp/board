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

/**
 * 계시판 CRUD 작업을 위한 데이터베이스 brige 입니다.
 */
class CRUD
{
    private $_db;
    public function __construct()
    {
        $dbinfo = \jiny\dbinfo();
        $this->db = \jiny\mysql($dbinfo);
    }

    /**
     * 
     */
}