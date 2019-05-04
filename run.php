<?php
/**
 * Created by PhpStorm.
 * User: quentin
 * Date: 29/04/19
 * Time: 13:36
 */

require_once "Post.php";
require_once "SchemaBdd.php";

/*$post = new Post();
$post->setContent("Test");

$post->load(1);
$post->setContent("SAve");
$post->save();

$post::find("content LIKE '%es%'");
*/
$bdd = new SchemaBdd();
//$bdd->createTables();
$bdd->editTables();