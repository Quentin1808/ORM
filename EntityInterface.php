<?php
/**
 * Created by PhpStorm.
 * User: quentin
 * Date: 29/04/19
 * Time: 13:31
 */

interface EntityInterface{

    public function save();
    public function load($id);
    //public static function find($clauseWhere):array ;

}