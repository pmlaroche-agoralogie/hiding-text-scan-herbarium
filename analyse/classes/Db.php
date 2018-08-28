<?php


/**
 * Class Db
 */
class Db
{
    /** @var string Server (eg. localhost) */
    protected $server;

    /**  @var string Database user (eg. root) */
    protected $user;

    /** @var string Database password (eg. can be empty !) */
    protected $password;

    /** @var string Database name */
    protected $database;

    /** @var DB  */
    public static $instance ;

    /** @var PDO */
    protected $link;

    /**
     * Instantiates a database connection
     *
     * @param string $server Server address
     * @param string $user User login
     * @param string $password User password
     * @param string $database Database name
     * @param bool $connect
     */
    public function __construct($server, $user, $password, $database, $connect = true)
    {
        $this->server = $server;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;

        if ($connect) {
            $this->connect();
        }
    }

    /**
     * Returns database object instance.
     *
     * @return Db Singleton instance of Db object
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Db(
                DB_HOST,
                DB_USER,
                DB_PASSWD,
                DB_NAME
            );
        }

        return self::$instance;
    }

    /**
     * Tries to connect to the database
     *
     * @return PDO
     */
    public function connect()
    {
        try {
            $this->link = $this->_getPDO($this->server, $this->user, $this->password, $this->database, 5);
        } catch (PDOException $e) {
            echo 'Connexion échouée : ' . $e->getMessage();
        }

        $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // UTF-8 support
        if ($this->link->exec('SET NAMES \'utf8\'') === false) {
            echo ('Fatal error: no utf-8 support. Please check your server configuration.');
        }

        $this->link->exec('SET SESSION sql_mode = \'\'');

        return $this->link;
    }

    /**
     * Destroys the database connection link
     */
    public function disconnect()
    {
        unset($this->link);
    }


    /**
     * Returns a new PDO object (database link)
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $dbname
     * @param int $timeout
     * @return PDO
     */
    protected static function _getPDO($host, $user, $password, $dbname, $timeout = 5)
    {
        $dsn = 'mysql:';
        if ($dbname) {
            $dsn .= 'dbname='.$dbname.';';
        }
        if (preg_match('/^(.*):([0-9]+)$/', $host, $matches)) {
            $dsn .= 'host='.$matches[1].';port='.$matches[2];
        } elseif (preg_match('#^.*:(/.*)$#', $host, $matches)) {
            $dsn .= 'unix_socket='.$matches[1];
        } else {
            $dsn .= 'host='.$host;
        }

        return new PDO($dsn, $user, $password, array(PDO::ATTR_TIMEOUT => $timeout, PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));

    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object or true/false.
     *
     * @param string $sql
     */
    public function query($sql)
    {
        if ($this->link->inTransaction())
            $this->result = $this->link->query($sql);
        else
        {
            try {
                $this->result = $this->link->query($sql);
            } catch (PDOException $e) {
                Tools::stopError('404','Not found','PDO Query:'.$e->getMessage()."\t".$sql);
            }
        }
    }

    /**
     * Escape a strinf for query
     *
     * @param string $sql
     * @return string
     */
    public function quote($string)
    {
        if ($this->link->inTransaction())
            return $this->link->quote($string);
        else
        {
            try {
                return $this->link->quote($string);
            } catch (PDOException $e) {
                Tools::stopError('404','Not found','PDO Quote:'.$e->getMessage()."\t".$sql);
            }
        }
    }

    /**
     * Returns all rows from the result set.
     *
     * @param PDOStatement $result
     * @return array|false|null
     */
    public function getAll($result = false)
    {
        if (!$result) {
            $result = $this->result;
        }

        if (!is_object($result)) {
            return false;
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns row count from the result set.
     *
     * @param PDOStatement $result
     * @return int
     */
    public function numRows()
    {
        if (!$result) {
            $result = $this->result;
        }
        if (!is_object($result)) {
            return false;
        }
        return $result->rowCount();
    }

    public function beginTransaction()
    {
        $this->link->beginTransaction();
    }

    public function commitTransaction()
    {
        $this->link->commit();
    }

    public function rollbackTransaction()
    {
        $this->link->rollBack();
    }

    public function lastInsertId()
    {
        return $this->link->lastInsertId();
    }


}