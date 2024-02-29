<?php 


class MySqlConnection
{
    /**
     * PDO connection 
     *
     * @var \PDO
     */
    private static $connection;

    public function __construct()
    {
        if (! $this->isConnected()) {
            $this->connect();
        }
    }

    
    /**
     * determining and ensuring it's a single connection
     *
     * @return bool
     */
    private function isConnected()
    {
        return static::$connection instanceof PDO;
    }
    
    /**
     * connect to the database server
     *
     * @return void
     */
    private function connect()
    {
        $connectionData = require_once 'c:/xampp/htdocs/Contacts/config/Database.php';
        extract($connectionData);

        try {
            static::$connection = new PDO('mysql:host=' . $server . ';dbname=' . $dbname, $dbuser, $dbpass);
            
            static::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

            static::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    
    /**
     * get database Connection instance
     *
     * @return \PDO
     */
    public function getConnection()
    {
        return static::$connection;
    }

}