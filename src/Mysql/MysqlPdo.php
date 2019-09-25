<?php

namespace Dang\Mysql;

class MysqlPdo
{
    protected $_db;
    protected $_stmt;
    protected $_param;

    function __construct($dbname, $host, $port, $user, $passwd)
    {
        $dsn = "mysql:dbname=".$dbname.";host=".$host.";port=".$port;

        try {
            $db = new \Dang\Mysql\SafePdo($dsn, $user, $passwd, array(
                \PDO::ATTR_TIMEOUT => 1,
                \PDO::ATTR_PERSISTENT => true,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
            ));
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Mysql pdo connection failed: ' . $e->getMessage();
        }

        $this->_db = $db;
        $this->_stmt = new \PDOStatement();
    }

    function prepareSql($sql)
	{
        $this->_stmt = $this->_db->prepare($sql);
        return $this;
	}

    function execute()
    {
        $result = $this->_stmt->execute($this->_param);
        return $result;
    }

    function closeCursor()
    {
        $this->_stmt->closeCursor();
        return $this;
    }

    function prepareInsert($table, $data, $action = "INSERT")
	{
		reset($data);
        $space = $query_1 = $query_2 = '';
        foreach($data as $key=>$val)
        {
            $query_1 .= $space.$key;
            $query_2 .= $space."'".$val."'";
            $space=', ';
        }
        $query = $action.' INTO `' . $table . '` ('.$query_1.') VALUES ('.$query_2.')';

        $this->prepareSql($query);
        return $this;
    }

    function prepareUpdate($table, $data, $where = '')
	{
        $query = 'UPDATE `' . $table . '` SET ';
        $space='';
        foreach($data as $key=>$val)
        {
            if($val instanceof \Dang\Mysql\Expression){
                $query .= $space."`".$key. "` = ". $val->__toString();
            }else{
                $query .= $space.$key . "= '" . $val. "'";
            }
            $space=', ';
        }
        if($where){
            $query .=' WHERE ' . $where.'';
        }

        $this->prepareSql($query);
        return $this;
    }

    function doUpdate()
    {
        $result = $this->execute();
        $this->closeCursor();
        return $result;
    }

    function doInsert()
    {
        return $this->doUpdate();
    }

	function getOne()
	{
        $this->execute();
        $result = $this->_stmt->fetchColumn();
		return $result;
	}

	function getRow()
	{
        $this->_stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $this->execute();
		$result = $this->_stmt->fetch();
		return $result;
	}

    function getRowList()
	{
        $this->_stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $this->execute();
        $result = $this->_stmt->fetchAll();
		return $result;
	}

    function getLastInsertId()
    {
        $result = $this->_db->lastInsertId();
        return $result;
    }

    function closeDb()
    {
        $this->_db = null;
    }

    function bindParam($param)
    {
        $this->_param = $param;
        return $this;
    }
}
