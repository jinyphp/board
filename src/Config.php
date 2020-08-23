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

// 설정관련 기능들
trait Config
{
    protected $conf;
    
    /**
     * 설정관련 기능
     */

    // 요청한 경로를 json으로 변경합니다.
    private function confPath($path)
    {
        return str_replace(".php", ".json", $path);
    }

    private function confLoad($path)
    {
        $path = $this->confPath($path);
        if (\file_exists($path)) {
            $body = \file_get_contents($path);
            $this->conf = \json_decode($body, true);
        } else {
            echo $path."파일이 존재하지 않습니다.";
            exit;
        }
        
        return $this;
    }

    public function getConf($key=null)
    {
        if($key) return $this->conf[$key];
        return $this->conf;
    }

    /**
     * 상태처리 설정파일
     */
    public function setConf($conf)
    {
        $this->conf = $conf;
    }

    public function setEnv($path)
    {
        $this->conf =  \json_decode(\file_get_contents($path), true);
    }


}