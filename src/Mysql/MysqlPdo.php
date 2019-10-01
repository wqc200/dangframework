<?php

namespace Dang\Mysql;

class MysqlPdo
{
    protected $_db;
    protected $_stmt;
    protected $_param;

    function __construct($dbname, $host, $port, $user, $passwd, $persistent = true)
    {
        $dsn = "mysql:dbname=" . $dbname . ";host=" . $host . ";port=" . $port;

        try {
            $db = new \Dang\Mysql\SafePdo($dsn, $user, $passwd, array(
                \PDO::ATTR_TIMEOUT => 1,
                \PDO::ATTR_PERSISTENT => $persistent,
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
        $param = array();
        $field = array();
        $value = array();
        foreach ($data as $key => $val) {
            $field[] = "`" . $key . "`";
            if ($val instanceof \Dang\Mysql\Expression) {
                $value[] = $val->__toString();
            } else {
                $value[] = "?";
                $param[] = $val;
            }
        }
        if (count($field) < 1) {
            return false;
        }
        $sql = $action . ' INTO `' . $table . '` (' . join(", ", $field) . ') VALUES (' . join(", ", $value) . ')';
        $this->prepareSql($sql)->bindParam($param)->execute();
        return true;
    }

    function prepareUpdate($table, $data, $condition)
    {
        $param = array();

        $sql = 'UPDATE `' . $table . '` SET ';
        $field = array();
        foreach ($data as $key => $val) {
            if ($val instanceof \Dang\Mysql\Expression) {
                $field[] = "`" . $key . "` = " . $val->__toString();
            } else {
                $field[] = "`" . $key . "` = ?";
                $param[] = $val;
            }
        }
        if (count($field) < 1) {
            return false;
        }
        $sql .= join(", ", $field);

        reset($condition);
        $where = array();
        foreach ($condition as $key => $val) {
            $where[] = "`" . $key . "` = ?";
            $param[] = $val;
        }
        if ($where) {
            $sql .= ' WHERE ' . join("AND", $where) . '';
        }

        $this->prepareSql($sql)->bindParam($param)->execute();
        return true;
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
