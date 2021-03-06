<?php

if ( ! defined('BASE_PATH')) exit('Acesso negado!');

/**
 * Drive de funcionamento para MySql.
 *
 * @packageMojo*PHP
 * @author Eliel de Paula <elieldepaula@gmail.com>
 * @since 26/12/2012
 */
class mysql {

    //internal vars
    protected $dbid;
    protected $type;
    protected $fields = "*";
    protected $table;
    protected $query;
    protected $where;
    protected $order;
    protected $limit;
    protected $values;
    protected $dbvar = array();
    protected $expvar = array();
    //external vars
    public $error;
    public $result;
    public $rows = array();
    public $numrows;
    public $sql;
    public $insertid;
    public $status;
    public $cntr = 0;

    function __construct($config = array()) {
        $conf = $config[ENVIROMENT];
        $this->connect($conf['user'], $conf['password'], $conf['host'], $conf['database']);
    }
    
    public static function &getInstance($config = array()) {
        
        static $instance = array();
        
        if(!isset($instance[0]) || !$instance[0]):
            
            $instance[0] = new mysql($config);
        
        endif;
        
        return $instance[0];
        
    }
    
    function connect($username, $password, $host, $db) {

        $connect = mysql_connect($host, $username, $password) or die("db fail");
        $db = mysql_select_db($db, $connect);
    }

    function fields($x) {
        $this->set($x);
        $this->fields = "";
        foreach ($this->dbvar as $v) {
            $this->fields.=$seperate . $v;
            $seperate = ",";
        }
    }

    function table($x) {
        $this->table = $x;
    }

    function values($x) {
        if ($this->values == '')
            $this->values = $x;
        else
            $this->values.="," . $x;
    }

    function where($x) {
        if ($this->where == '')
            $this->where = $x;
        else
            $this->where.=" AND " . $x;
    }

    function order($x) {
        $this->order = $x;
    }

    function limit($x) {
        $this->limit = $x;
    }

    function set($x) {
        $tmp = explode(",", $x);
        foreach ($tmp as $t) {
            $parts = explode("=", $t);
            if ($parts[1] == '')
                $parts[1] = $parts[0];
            array_push($this->dbvar, $parts[0]);
            array_push($this->expvar, $parts[1]);
        }
    }

    function get($x) {
        foreach ($x as $field => $value) {

            if (in_array($field, $this->dbvar)) {
                $key = array_keys($this->dbvar, $field);
                $field = $this->expvar[$key[0]];
            }

            $GLOBALS[$field] = $value;
            $this->$field = $value;
        }
        $this->cntr++;
    }

    function status() {
        return $this->status;
    }

    function debug($x) {
        if ($x == 0)
            echo "<div>" . $this->query . "</div>";
        else
            mail("elieldepaula@gmail.com", "Mojo*PHP - SQL Debug", $this->query, "From: debug@elieldepaula.com.br");
    }
    
    function select() {

        $this->query = "SELECT " . $this->fields . " FROM " . $this->table;
        if ($this->where)
            $this->query.=" WHERE " . $this->where;
        if ($this->order)
            $this->query.=" ORDER BY " . $this->order;
        if ($this->limit)
            $this->query.=" LIMIT " . $this->limit;

        $this->rs = mysql_query($this->query);
        $this->numrows = mysql_num_rows($this->rs);


        $id = 0;
        while ($row = mysql_fetch_array($this->rs)) {
            $this->rows[$id] = $row;
            $id++;
        }

        if ($this->numrows == 1) {
            $this->get($this->rows[0]);
            $this->cntr = 0;
        }
    }

    function insert() {

        $this->query = "INSERT INTO " . $this->table . " VALUES (" . $this->values . ")";

        $this->rs = mysql_query($this->query);

        $this->insertid = mysql_insert_id();

        if ($this->rs)
            $this->status = TRUE; else
            $this->status = FALSE;
    }

    function update() {

        $this->query = "UPDATE " . $this->table . " SET " . $this->values . " WHERE " . $this->where;

        $this->rs = mysql_query($this->query);

        if ($this->rs)
            $this->status = TRUE; else
            $this->status = FALSE;
    }

    function delete() {

        $this->query = "DELETE FROM " . $this->table . " where " . $this->where;

        $this->rs = mysql_query($this->query);

        if ($this->rs)
            $this->status = TRUE; else
            $this->status = FALSE;
    }

    function query($x) {

        $this->query = $x;
    }

    function string() {

        $this->rs = mysql_query($this->query);
        $this->numrows = mysql_num_rows($this->rs);


        $id = 0;
        while ($row = mysql_fetch_array($this->rs)) {
            $this->rows[$id] = $row;
            $id++;
        }

        if ($this->numrows == 1) {
            $this->get($this->rows[0]);
            $this->cntr = 0;
        }
    }

}