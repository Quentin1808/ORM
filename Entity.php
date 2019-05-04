<?php
/**
 * Created by PhpStorm.
 * User: quentin
 * Date: 29/04/19
 * Time: 13:15
 */

require_once "EntityInterface.php";
require_once "Mysql.php";

abstract class Entity implements EntityInterface
{

    protected static $tableName = NULL;
    private $reflection;

    public function save(){

        $rc = new \ReflectionClass($this);
        $properties = $rc->getProperties(ReflectionProperty::IS_PUBLIC);

        $listProperties = array();

        foreach ($properties as $property){
            if($property->getName() != "id") {
                $listProperties[] = '`' . $property->getName() . '` = "' . $property->getValue($this) .'"' ;
            }
        }

        if($this->id){
            $sqlQuery = "UPDATE " . static::getTableName() . " SET " . implode(' , ', $listProperties) . " WHERE id = " . $this->id;
        }else{

            $sqlQuery = "INSERT INTO " . static::getTableName() . " SET " . implode(' , ', $listProperties);

        }

        Mysql::getInstance()->getConnection()->exec($sqlQuery);

    }

    public static  function getTableName(){
        $reflection = new ReflectionClass(get_called_class());
        $class = get_called_class();
        return NULL !== $class::$tableName ? $class::$tableName : strtolower($reflection->getName());
    }

    public function load($id)
    {
        $rc = new \ReflectionClass($this);
        $properties = $rc->getProperties(ReflectionProperty::IS_PUBLIC);

        $query = "SELECT * FROM ".static::getTableName()." WHERE id = ".$id;

        $result = Mysql::getInstance()->getConnection()->query($query)->fetch(PDO::FETCH_ASSOC);

        foreach ($properties as $property){
            $propertyName = $property->getName();
            $property->setValue($this,$result[$propertyName]);
        }

    }

    public static function find($clauseWhere)
    {
        $clauseWhere = (isset($clauseWhere)) ? $clauseWhere : '1';

        $query = "SELECT * FROM ". static::getTableName() . " WHERE " . $clauseWhere;

        return $result = Mysql::getInstance()->getConnection()->query($query)->fetchAll(PDO::FETCH_ASSOC);

    }

}