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

// 자바스크립트
trait Javascript
{
    // protected $scriptfile = "../resource/board/board.js";
    protected $scriptfile = "../vendor/jiny/board/src/board.js";

    /**
     * 계시판 
     * 자바스크립트 코드 삽입.
     */
    protected function javascript()
    {
        // 로직에서 생성된값 적용
        $vars['csrf'] = \jiny\board\csrf()->get();

        // 스크립트 파일 읽기
        $javascript = \jiny\html_get_contents($this->scriptfile, $vars);
        return  \jiny\javascript($javascript);
    }

}