# ORM
## auteur : Quentin PRUVOST
## MASTER I2L

# /!\ A LIRE /!\ : 
Pour le dossier de config : 
* Il doit ce situé dans le même répertoire de les autres fichiers
* il ne doit comporter que les fichiers des entités au format JSON

Pour les fichiers JSON :
* Ils doivent respecter la syntaxe suivante :  
>{  
> >     "fields": {  
> >       "id" : {  
> >         "type" : "int",  
> >         "property" : "AUTO_INCREMENT NOT NULL PRIMARY KEY",  
> >         "modification" : 0  
> >     },  
> >       "content" : {  
> >         "type" : "varchar(255)",  
> >         "property" : "",  
> >         "modification" : 0,  //Mettre 1 si il y a des modifications sur le champ
> >           "listeModifications" : {  
> >             "type" : "",  //add, delete, modifiy
> >             "name" : "",  //Nouveau nom du champ
> >             "typeChamp" : "",  //Nouveau type du champ
> >             "property" : ""  //Propriété du champ
> >           }  
> >       }  
> >     },  
> >     "version" : "0.1",  
> >     "tableName" : "posts"  
>   }

__Il est bien évident que cette de méthode de mise à jour des entités (voir SchameBdd.php => editTables) n'est pas la meilleure.__  
__En effet après chaque mise à jour en base, il faut mettre à jour, à la main, les fichiers JSON ainsi que les fichiers des entités et réécrire les champs ainsi que les méthodes.__