<?php
/**
 * Created by PhpStorm.
 * User: quentin
 * Date: 29/04/19
 * Time: 13:15
 */

require_once "Entity.php";

class Post extends Entity
{

    public $id;

    public $content;
    private $notme;
    protected static $tableName = "posts";

    /**
     * @return string
     */
    public static function getTableName()
    {
        return self::$tableName;
    }

    /**
     * @param string $tableName
     */
    public static function setTableName($tableName)
    {
        self::$tableName = $tableName;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getNotme()
    {
        return $this->notme;
    }

    /**
     * @param mixed $notme
     */
    public function setNotme($notme)
    {
        $this->notme = $notme;
    }

}