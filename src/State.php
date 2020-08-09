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
 * 테이블 동작 상태도
 */
class State
{
    /**
     * 목록을 출력합니다.
     */
    private function factory($name)
    {
        $name = "\Jiny\Board\\State\\".$name;
        return new $name ($this->conf);
    }

    public function list()
    {

    }

    protected function stateLIST($limit=0)
    {
        return $this->factory("TableList")->main($limit);
    }

    public function view()
    {

    }

    protected function stateView($id)
    {
        return $this->factory("TableView")->main($id);
    }

    public function edit()
    {

    }

    protected function stateEDIT($id=null)
    {
        if(!$id) $id = intval($_POST['id']);
        if ($this->request_method == "GET") {
            return $this->factory("TableEdit")->GET($id);
        } else if ($this->request_method == "POST") {
            return $this->factory("TableEdit")->main($id);
        }        
    }

    public function editup()
    {

    }

    protected function stateEDITUP()
    {
        return $this->factory("TableUpdate")->main();
    }

    public function new()
    {

    }
    public function stateNEW()
    {
        return $this->factory("TableNew")->main();
    }

    public function newup()
    {

    }

    protected function stateNEWUP()
    {
        return $this->factory("TableInsert")->main();        
    }

    public function delete()
    {

    }

    protected function stateDELETE()
    {
        return $this->factory("TableDelete")->main(); 
    }

}