<?php
/**
 * Created by PhpStorm.
 * User: quentin
 * Date: 29/04/19
 * Time: 14:57
 */

class Mysql
{

    const DEFAULT_USER = "Quentin";
    const DEFAULT_HOST = "localhost";
    const DEFAULT_PASS = "toto";
    const DEFAULT_DBNAME = "ORM";

    private $PDOInstance = null;

    private static $MysqlInstance = null;

    /**
     * Mysql constructor.
     */
    public function __construct()
    {
        //$this->PDOInstance = new PDO('mysql:host='. self::DEFAULT_HOST .';dbname='. self::DEFAULT_DBNAME.','.self::DEFAULT_USER.','.self::DEFAULT_PASS);
        $this->PDOInstance  = new PDO('mysql:host=127.0.0.1;port=3306;dbname=ORM', 'Quentin', 'toto');
    }

    public static function getInstance(){

        if(is_null(self::$MysqlInstance)){
            self::$MysqlInstance = new Mysql();
        }
        return self::$MysqlInstance;
    }

    public function getConnection(){
        return $this->PDOInstance;
    }

}