<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 06.03.15
 * Time: 19:34
 */
class model extends base
{
    public $table;
    protected $pdo;
    protected $id_array;

    function __construct($table, $db = null, $user = null, $password = null)
    {
        $this->pdo = db_connect_singleton::getInstance($db ? $db : MDB_NAME)->pdo;
        $this->table = $table;
        $this->init();
    }

    public function id_array($field = 'id')
    {
        $this->id_array = $field;
        return $this;
    }

    /**
     * @param PDOStatement $stm
     * @param array $data
     * @param bool $assoc
     * @return array
     */

    protected function get_all(PDOStatement $stm, array $data = array(), $assoc = true)
    {
        ($data ? $stm->execute($data) : $stm->execute());
        if($assoc) {
            $stm->setFetchMode(PDO::FETCH_ASSOC);
        } else {
            $stm->setFetchMode(PDO::FETCH_NUM);
        }
        $res = array();
        if(!$this->id_array) {
            while($row = $stm->fetch()) {
                $res[] = $row;
            }

        } else {
            while($row = $stm->fetch())
                $res[$row[$this->id_array]] = $row;
            $this->id_array = false;
        }
        return $res;
    }


    /**
     * @param PDOStatement $stm
     * @param array $data
     * @return array
     */

    protected function get_row(PDOStatement $stm, array  $data = array())
    {
        $data ? $stm->execute($data) : $stm->execute();
        $stm->setFetchMode(PDO::FETCH_ASSOC);
        return $stm->fetch();
    }

    /**
     * @param string $order
     * @param string $limit
     * @param bool $show
     * @return array
     */

    public function getAll($order = "", $limit = "", $show = false)
    {
        $stm = $this->pdo->prepare('SELECT * FROM ' . $this->table .
            ( $order ? ' ORDER BY ' . $order : '' ) .
            ( $limit ? ' LIMIT ' . $limit : '' )
        );
        if($show)echo $stm->queryString;
        return $this->get_all($stm);
    }

    /**
     * @param string $order
     * @return array
     */

    public function getRow($order = null)
    {
        $stm = $this->pdo->prepare('SELECT * FROM ' . $this->table . ( $order ? ' ORDER BY ' . $order : '' ));
        return $this->get_row($stm);
    }

    /**
     * @param array $row
     * @param bool $show
     * @return int
     */

    public function insert(array $row, $show = false)
    {
        if(!empty($row['id']))
        {
            $id = $row['id'];
        }
        unset($row['id']);
        $rows = array();
        $names = array();
        $data = array();
        foreach($row as $k=>$v)
        {
            $rows[] = '`' . $k . '`';
            $names[] = ':' . $k;
            $data[] = $k . " = :" . $k;

        }
        if(isset($id))
        {
            $stm = $this->pdo->prepare(
                'UPDATE ' . $this->table . ' SET ' . implode(', ', $data) . ' WHERE id = :id'
            );
            $row['id'] = $id;
        }
        else $stm = $this->pdo->prepare(
            'INSERT INTO ' . $this->table . ' (' . implode(', ', $rows) . ') VALUES ( ' . implode(', ', $names) . ')'
        );
        if($show == 1) {
            echo $stm->getQuery($row);
        } elseif($show == 2) {
            $this->writeLog('MYSQL', $stm->getQuery($row));
        }
        $stm->execute($row);

        if(!empty($id))return $id;
        return $this->pdo->lastInsertId();
    }

    /**
     * @param array $rows
     * @param int $show
     * @return bool
     */

    public function insertRows(array $rows, $show = 0)
    {
        if(!$rows) {
            return false;
        }
        $fields = [];
        $values = [];
        $vals = [];
        foreach($rows as $k => $row) {
            foreach($row as $key => $val) {
                $fields[$key] = $key;
                $values[$k][$key] = $key.$k;
                $vals[$key.$k] = $val;
            }
        }
        $value = [];
        foreach($values as $v) {
            $value[] = '(:' . implode(',:', $v) . ')';
        }
        $stm = $this->pdo->prepare('
            INSERT INTO ' . $this->table . ' (' . implode(',', $fields) . ') VALUES ' . implode(',', $value) . '
        ');
        if($show == 1) {
            echo $stm->getQuery($vals);
        } elseif($show == 2) {
            $this->writeLog('MYSQL', $stm->getQuery($vals));
        }
        return $stm->execute($vals);
    }

    public function updateRows($rows, $show = 0)
    {
        if(!$rows) {
            return false;
        }
        $fields = [];
        $values = [];
        $vals = [];

        $query = 'UPDATE ' . $this->table . ' SET CASE id ';
        foreach ($rows as $k => $row) {
            foreach($row as $key => $val) {
                if($k == 'id') {
                    continue;
                }
                $fields[$key] = $key;
                $values[$k][$key] = $key.$k;
                $vals[$key.$k] = $val;
            }
        }
        print_r($fields);
//exit;
        $value = [];
        foreach($values as $v) {
            $value[] = '(:' . implode(',:', $v) . ')';
        }
        $case_parts = [];
        foreach ($rows as $k => $row) {
            print_r($row);
            $case_parts[] =  '(' . implode(', ', $values[$k]) . ') WHEN ' . $row['id'] . ' THEN VALUES (' . implode(', ', $values[$k]) ;
        }
        print_r($case_parts);
        $query .= '(' . implode(',', $fields) . ') VALUES ' . implode(',', $value);


    }

    /**
     * @param int $id
     * @param bool $show
     * @return array
     */

    public function getById($id, $show = false)
    {
        $stm = $this->pdo->prepare('SELECT * FROM ' . $this->table . ' WHERE id = :id');
        $vals = ['id' => $id];
        if($show == 1) {
            echo $stm->getQuery($vals);
        } elseif($show == 2) {
            $this->writeLog('MYSQL', $stm->getQuery($vals));
        }
        return $this->get_row($stm, $vals);
    }

    /**
     * @param string $field
     * @param string $value
     * @param bool $show_all
     * @param string $order
     * @param string $limit
     * @param bool $show
     * @return array
     */

    public function getByField($field, $value, $show_all = false, $order = "", $limit = '', $show = false)
    {
        $stm = $this->pdo->prepare(
            'SELECT * FROM
        ' . $this->table . ' WHERE ' . $field . ' = ?'
            . ( $order ? ' ORDER BY ' . $order : '')
            . ( $limit ? ' LIMIT ' . $limit : '')
        );
        if($show_all)
            $result = $this->get_all($stm, array($value));
        else
            $result = $this->get_row($stm, array($value));
        if($show)echo $stm->queryString;
        return $result;
    }

    public function getByFieldIn($field, array $values, $show_all = false, $order = "", $limit = '', $show = false)
    {
        $vals = [];
        foreach ($values as $value) {
            $vals[] = '"' . $value . '"';
        }
        if(!$vals) {
            return false;
        }
        $in = implode(', ', $vals);
        $stm = $this->pdo->prepare(
            'SELECT * FROM
        ' . $this->table . ' WHERE ' . $field . ' IN (' . $in . ')'
            . ( $order ? ' ORDER BY ' . $order : '')
            . ( $limit ? ' LIMIT ' . $limit : '')
        );
        if($show_all)
            $result = $this->get_all($stm, array($value));
        else
            $result = $this->get_row($stm, array($value));
        if($show)echo $stm->queryString;
        return $result;
    }

    /**
     * @param array $fields
     * @param bool $show_all
     * @param string $order
     * @param string $limit
     * @param bool $show
     * @return array
     */

    public function getByFields(array $fields, $show_all = false, $order = "", $limit = '', $show = false)
    {
        $where = array();
        foreach($fields as $k => $v) {
            $where[] = $k . ' = :' . $k;
        }
        $stm = $this->pdo->prepare(
            'SELECT * FROM
        ' . $this->table . ' WHERE ' . implode(' AND ', $where)
            . ( $order ? ' ORDER BY ' . $order : '')
            . ( $limit ? ' LIMIT ' . $limit : '')
        );
        if($show) {
            $query = $stm->getQuery($fields);
            echo $query;
            $this->writeLog('MYSQL', $query);
        }
        if($show_all)
            $result = $this->get_all($stm, $fields);
        else
            $result = $this->get_row($stm, $fields);
        $query = $stm->getQuery($fields);

        return $result;
    }

    /**
     * @param int $id
     * @param bool $show
     * @return bool
     */

    public function deleteById($id, $show = false)
    {
        if($id == '')return;
        $stm = $this->pdo->prepare('
        DELETE FROM ' . $this->table . ' WHERE id = :id
        ');
        if($show)echo $stm->queryString;
        if($stm->execute(array('id'=>$id)))
            return true;
        else
            return false;
    }

    /**
     * @param string $field
     * @param string $value
     * @param bool $show
     * @return bool
     */

    public function delete($field, $value, $show = false)
    {
        $stm = $this->pdo->prepare('
        DELETE FROM ' . $this->table . ' WHERE ' . $field . ' = :' . $field . '
        ');
        if($show)echo $stm->queryString;
        if($stm->execute(array($field => $value)))
            return true;
        else
            return false;
    }

    /**
     * @param bool $show
     * @return bool
     */

    public function deleteAll($show = false)
    {
        $stm = $this->pdo->prepare('
        DELETE FROM ' . $this->table . '
        ');
        if($show)echo $stm->queryString;
        if($stm->execute())
            return true;
        else
            return false;
    }

    /**
     * @param array $fields
     * @param integer $show
     * @return mixed
     */

    public function deleteByFields(array $fields, $show = 0)
    {
        $where = array();
        foreach($fields as $k => $v) {
            $where[] = $k . ' = :' . $k;
        }
        $stm = $this->pdo->prepare(
            'DELETE FROM
        ' . $this->table . ' WHERE ' . implode(' AND ', $where)
        );
        if($show == 1) {
            echo $stm->getQuery($fields);
        }
        if($show == 2) {
            $this->writeLog('MYSQL', $stm->getQuery($fields));
        }
        return $stm->execute($fields);
    }

    /**
     * @param null $field
     * @param null $value
     * @return mixed
     */

    public function countByField($field = null, $value = null)
    {
        $stm = $this->pdo->prepare('
        SELECT COUNT(id) count FROM ' . $this->table . ( $field ? ' WHERE ' . $field . ' = :' .$field : '' ) . '
        ');
        return $this->get_row($stm, array($field => $value))['count'];
    }

    public function countByFields(array $fields)
    {
        $where = array();
        foreach($fields as $k => $v) {
            $where[] = $k . ' = :' . $k;
        }
        $stm = $this->pdo->prepare('
        SELECT COUNT(id) count FROM ' . $this->table . ' WHERE ' . implode(' AND ', $where));
        return $this->get_row($stm, $fields)['count'];
    }

    public function memcached($lifetime, $method)
    {
        $arg_list = func_get_args();
        unset($arg_list[0]);
        unset($arg_list[1]);
        if(MEMCACHED)
        {
            $str = get_class($this) . $method . ( $arg_list ? implode('', $arg_list) : '' );
            $key = md5($str);

            $memcache_obj = new Memcache;
            $memcache_obj->connect('127.0.0.1', 11211) or die('Could not connect');
            $var_key = @$memcache_obj->get($key);
            if(!empty($var_key))
            {
                $result =  $var_key;
            }
            else
            {
                $result = call_user_func_array(array($this, $method),$arg_list);
                $memcache_obj->set($key, $tmp, false, $lifetime);
            }
            $memcache_obj->close();
        }
        else
            $result = call_user_func_array(array($this, $method),$arg_list);


        return $result;
    }

    public function setOption($key, $value)
    {
        if($this->getOption($key) !== null) {

            $stm = $this->pdo->prepare('UPDATE options SET option_value = :option_value WHERE option_key = :option_key');
        } else {
            $stm = $this->pdo->prepare('INSERT INTO reports_options SET option_key = :option_key, option_value = :option_value');
        }
        $row = array('option_key' => $key, 'option_value' => $value);
        $stm->execute($row);
    }

    public function getOption($key)
    {
        $stm = $this->pdo->prepare('SELECT * FROM options WHERE `option_key` = :option_key');
        $res = $this->get_row($stm, array('option_key' => $key));
        return $res['option_value'];
    }

    /**
     * @param array $row
     * @param string $field
     * @param bool $show
     * @return int
     */

    public function updateByField(array $row, $field = '', $show = false)
    {
        if(!$field) $field = $this->table . '_id';
        if(isset($row[$field]))
        {
            $id = $row[$field];
            unset($row[$field]);
        }
        $rows = array();
        $names = array();
        $data = array();
        foreach($row as $k=>$v)
        {
            $rows[] = '`' . $k . '`';
            $names[] = ':' . $k;
            $data[] = '`' .$k . '` = :' . $k;

        }
        if(isset($id))
        {
            $stm = $this->pdo->prepare(
                'UPDATE ' . $this->table . ' SET ' . implode(', ', $data) . ' WHERE ' . $field . ' = :' . $field
            );
            $row[$field] = $id;
        }
        else $stm = $this->pdo->prepare(
            'INSERT INTO ' . $this->table . ' (' . implode(', ', $rows) . ') VALUES ( ' . implode(', ', $names) . ')'
        );
        $stm->execute($row);
        if($show)echo $stm->getQuery($row);
        if(!empty($id))return $id;
        return $this->pdo->lastInsertId();
    }

    protected function init()
    {

    }

    /**
     * @param string $field
     * @param string $str
     * @param array $select
     * @param bool $offset
     * @return array
     */

    public function findByField($field, $str, array $select = array('*'), $offset = true)
    {
        $select = implode(', ', $select);
        $stm = $this->pdo->prepare('
        SELECT ' . $select . ' FROM ' . $this->table . ' WHERE ' . $field . ' LIKE :str
        ');
        return $this->get_all($stm, array('str' => ( $offset ? '%' : '' ) . $str . '%'));
    }

    /**
     * @param mixed $value
     */

    protected function log($value)
    {
        $log = registry::get('log');
        registry::remove('log');
        $log[] = print_r($value,1);
        registry::set('log', $log);
    }

    /**
     *
     */

    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    /**
     *
     */

    public function commitTransaction()
    {
        $this->pdo->commit();
    }

    /**
     *
     */

    public function rollbackTransaction()
    {
        $this->pdo->rollBack();
    }
}
