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

class View
{
    private $_controller;
    private $_action;
    private $_file=null;

    public function __construct($controller)
    {
        $this->_controller = $controller;
        $this->action = "/".\jiny\httpEndpoint()->first()."/";
    }

    public function setFile($file)
    {
        $this->_file= $file;
        return $this;
    }

    public function new($vars=[], $file=null)
    {
        if (!$file) $file = $this->_file;
        $vars['csrf'] = \jiny\board\csrf($this->_controller->salt());
        $vars['action'] = $this->action."new";
        return \jiny\view\template($file, $vars);
    }

    public function edit($vars=[], $file=null)
    {
        if (!$file) $file = $this->_file;
        $vars['csrf'] = \jiny\board\csrf($this->_controller->salt());
        $vars['action'] = $this->action."edit";
        return \jiny\view\template($file, $vars);
    }

    public function list($vars=[], $file=null)
    {
        if (!$file) $file = $this->_file;
        $vars['csrf'] = \jiny\board\csrf($this->_controller->salt());
        $vars['btnNew'] = $this->action."new";
        $vars['action'] = $this->action;
        return \jiny\view\template($file, $vars);
    }

    public function table($rows)
    {
        $table = "<table>";
        if ($rows) {
            
            foreach($rows as $row) {
                $table .= "<tr>";
                foreach($row as $key => $value) {
                    $table .= "<td><a href='".$row->id."'>". $value. "</a></td>";
                }
                $table .= "</tr>";
            }
            
        } else {
            $table .= "<tr><td>데이터목록이 없습니다.</td></tr>";
        }
        $table .= "</table>";
        return $table;
    }

    /**
     * 
     */
}