<?php

namespace Jiny\Board;

class Admin
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
        
        $this->table = "board_info";
        $this->board = new \Jiny\Board\Data($this->db);
        $this->board->setBoard($this->table)->setField(['id','board','total']);     
        $this->action = new \Jiny\Board\Action($this->board);
   
    }

    private $table;
    private $board;
    private $action;

    public function list()
    {
        return $this->action->links('board', "/board/admin/")->list();
    }

    public function edit($id)
    {
        return $this->action->edit($id, "board_admin_edit");
    }

    public function __invoke()
    {
        
    }



}