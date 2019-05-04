<?php
/**
 * Created by PhpStorm.
 * User: quentin
 * Date: 30/04/19
 * Time: 11:18
 */



class SchemaBdd
{

    /**
     * Récupérer tous les fichiers JSON du dossier config
     * Créer les tables de chaque fichiers
     * Enregistrer dans une table la version ainsi que le nom de la table correspondant
     *
     */
    public function createTables(){

        //TODO Récupérer tous les fichiers JSON du dossier config
        //TODO Créer les tables de chaque fichiers
        //TODO Enregistrer dans une table la version ainsi que le nom de la table correspondant
        //TODO Gérer les execptions par exemple pas de type dans le fichie json pour les champs ou field sans "s", etc...

        $directory = "./config";
        $listeEntite = array();

        //Récupération des différents fichiers dans le dossier config
        if($dossier = opendir($directory))
        {
            while(($fichier = readdir($dossier)))
            {
                if($fichier != "." && $fichier != "..") {
                    //var_dump($fichier);
                    array_push($listeEntite, $fichier);
                }
            }
        }

        //Création de la table qui va contenir le nom des entités ainsi que leur version
        $queryDelete = "DROP TABLE IF EXISTS entites";
        Mysql::getInstance()->getConnection()->exec($queryDelete);

        $queryEntityVersion = "CREATE TABLE entites (id int AUTO_INCREMENT NOT NULL PRIMARY KEY, libelle varchar (255), version varchar (10))";
        Mysql::getInstance()->getConnection()->exec($queryEntityVersion);


        //Boucle de création des différentes tables ainsi que de leurs champs
        foreach ($listeEntite as $entite){
            $jsonFile = file_get_contents($directory . "/" . $entite);
            $json = json_decode($jsonFile, true);

            $version = $json['version'];
            $tableName = $json['tableName'];

            if (!$version || $version == ""){
                throw new Exception("Aucune version renseignée");
            }

            if (!$tableName || $tableName == ""){
                throw new Exception("Aucun nom de table renseigné");
            }

            $fields = array();
            foreach ($json['fields'] as $k => $v){

                if (!$v['type'] || $v['type'] == ""){
                    throw new Exception("Aucun type renseigné pour le champ " . $k);
                }

                $this->checkTypes($v['type'], $k);

                $fields[] = $k . ' ' . $v['type'] . ' ' . $v['property'];
            }

            $query = "CREATE TABLE " . $tableName . " ( " . implode(' , ', $fields) . ")";
            Mysql::getInstance()->getConnection()->exec($query);

            $queryAddEntite = "INSERT INTO entites (libelle, version) VALUES ('" . $tableName . "', '" . $version . "')";
            Mysql::getInstance()->getConnection()->exec($queryAddEntite);

        }

    }

    public function checkTypes($typeValue, $champ){
        $types = array('INTEGER', 'INT', 'SMALLINT', 'TINYINT', 'MEDIUMINT', 'BIGINT', 'DECIMAL', 'NUMERIC', 'FLOAT', 'DOUBLE', 'DATE', 'DATETIME', 'TIMESTAMP', 'TEXT');

        if ((strpos(strtoupper($typeValue),"VARCHAR")) || (strpos(strtoupper($typeValue),"CHAR"))){
            $valid = true;
        }elseif(!in_array(strtoupper($typeValue), $types)){
            $valid = false;
        }

        if ($valid == false){
            throw new Exception("Le type " . $typeValue . " pour le champ " . $champ. " n' est pas reconnu");
        }

    }

    //TODO dans un premier temps drop la table et recréer peut être une solution
    //TODO ou voir pour juste reset le type et les propirétés de chaque champ de l'netite dont la version a changé
    //TODO créer une entite "entites" avec les différents champs(à revoir)
    public function editTables(){
        $directory = "./config";
        $listeEntite = array();

        //récupération des différentes entites et de leur version en base

        $queryEntitiesVersion = "SELECT * FROM entites";
        $entitesVersion = Mysql::getInstance()->getConnection()->query($queryEntitiesVersion)->fetch(PDO::FETCH_ASSOC);


        //Récupération des différents fichiers dans le dossier config
        if($dossier = opendir($directory))
        {
            while(($fichier = readdir($dossier)))
            {
                if($fichier != "." && $fichier != "..") {
                    //var_dump($fichier);
                    array_push($listeEntite, $fichier);
                }
            }
        }


        foreach ($listeEntite as $entite) {

            $jsonFile = file_get_contents($directory . "/" . $entite);
            $json = json_decode($jsonFile, true);

            $listeQuery = array();

            $version = $json['version'];
            $tableName = $json['tableName'];

            if (!$version || $version == ""){
                throw new Exception("Aucune version renseignée");
            }

            if (!$tableName || $tableName == ""){
                throw new Exception("Aucun nom de table renseigné");
            }

            // Si on trouve le nom de la table dans la liste des versions => on regarde si il y a des modifications à faire sur cette table
            // Sinon cela signifie qu'il faut créer la table en question
            if(in_array($tableName, $entitesVersion)){

                foreach ($json['fields'] as $k => $v){

                    $modification = $v['modification'];

                    if($modification == 1){

                        if (!$v['type'] || $v['type'] == ""){
                            throw new Exception("Aucun type renseigné pour le champ " . $k);
                        }

                        $modifs  = $v['listeModifications'];

                            if ($modifs['type'] == "add"){

                                $query = "ALTER TABLE " . $tableName . " ADD " . $k . $v['type'] . " " . $v['property'];
                                //print_r($query);
                                Mysql::getInstance()->getConnection()->exec($query);

                                array_push($listeQuery, $query);

                            }elseif ($modifs['type'] == "delete"){

                                $query = "ALTER TABLE " . $tableName . " DROP " . $k;
                                //print_r($query);
                                Mysql::getInstance()->getConnection()->exec($query);
                                array_push($listeQuery, $query);

                            }elseif ($modifs['type'] == "modify"){

                                //Tester si il s'agit du nom de la colonne, du type ou des propriétés.
                                if($modifs['name'] || $modifs['name'] != ""){
                                    $query = "ALTER TABLE " . $tableName . " CHANGE " . $k . " " . $modifs['name'] . " " . $v['type'];
                                    //print_r($query);
                                    Mysql::getInstance()->getConnection()->exec($query);
                                    $nameTemp = $modifs['name'];
                                    array_push($listeQuery, $query);
                                }

                                if($modifs['typeChamp'] || $modifs['typeChamp'] != ""){
                                    $query = "ALTER TABLE " . $tableName . " MODIFY " . $nameTemp . " " . $modifs['typeChamp'];
                                    //print_r($query);
                                    Mysql::getInstance()->getConnection()->exec($query);
                                    array_push($listeQuery, $query);
                                }


                            }else{
                                throw new Exception("Modification inconnue pour le champ " . $k);
                            }
                    }

                }

            }else{

                $fields = array();
                foreach ($json['fields'] as $k => $v){

                    if (!$v['type'] || $v['type'] == ""){
                        throw new Exception("Aucun type renseigné pour le champ " . $k);
                    }

                    $this->checkTypes($v['type'], $k);

                    $fields[] = $k . ' ' . $v['type'] . ' ' . $v['property'];
                }

                $query = "CREATE TABLE " . $tableName . " ( " . implode(' , ', $fields) . ")";
                Mysql::getInstance()->getConnection()->exec($query);
                array_push($listeQuery, $query);

            }

        }

        //Voir pour demander si la personne veux effectuer les MAJs
        print "Voici la liste des requêtes effectuées : ";
        echo "\n";
        foreach ($listeQuery as $q){
            print $q;
            echo "\n";
        }
        print "Vous pouvez maintenant mettre à jour chacun des fichiers de configurations des entités .";
        echo "\n";
    }

}
