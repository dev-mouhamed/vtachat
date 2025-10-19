<?php 

    require_once('./connexion/connexion.php');

	// Données entrantes
    function echapper($string)
    {
        // On regarde si le type de string est un nombre entier (int)
        if(ctype_digit($string))
        {
            $string = htmlspecialchars($string);
        }
        // Pour tous les autres types
        else
        {
           // $string = mysql_real_escape_string($string);
            // $string = addcslashes($string, '%_');
            $string = htmlspecialchars($string);
        }
            
        return $string;
    }//fin fonction echapper
     

    // fonction permettant d'effectuer une insertion dans une table
    function save($table, array $parameters)
    {
        global $pdo;

        // preparation des colonnes a inserer
        $colums_parameters = implode(', ', array_keys($parameters));

        // recuperations des valeurs a inserer
        $values = array_values($parameters);

        // insertion des valeurs sous forme de parametre
        $bind_values = str_repeat('?, ', count(array_keys($parameters)));

        // supprimer le dernier ', ' a la fin
        $bind_values = rtrim($bind_values, ', ');


        $requete = "INSERT INTO {$table}($colums_parameters) VALUES({$bind_values})";


        $insert = $pdo->prepare($requete);

        return $insert->execute($values);
    }

    // fonction permettant de mettre a jour les informations d'un table
    function update($table, array $parameters, $champs_id, $value_id)
    {
        global $pdo;

        $columsParameters = implode(' = ?, ', array_keys($parameters)). ' = ?';

        $values = array_values($parameters);

        $values [] = $value_id;


        $update = $pdo->prepare("UPDATE {$table} SET {$columsParameters}
                                    WHERE {$champs_id} = ?");

        $status_update = $update->execute($values);

        return $status_update;
    }

    // fonction pour afficher les informations d'une seule ligne
    function getOne($statement)
    {
        global $pdo;

        try
        {
            return $pdo->query($statement)->fetch();
        }
        catch(Exception $e)
        {
            trigger_error($e->getMessage(), E_USER_ERROR);
            return null;
        }
    }

    // fonction pour afficher toutes les informations d'une requete
    function getAll($statement)
    {
        global $pdo;

        try
        {
            return $pdo->query($statement);
        }
        catch(Exception $e)
        {
            trigger_error($e->getMessage(), E_USER_ERROR);
            return null;
        }
    }

    // fonction pour supprimer un enregistrement dans une table
    function delete($table, $champs_id, $value_id)
    {
        global $pdo;

        try
        {
            $pdo->beginTransaction();

                $requete = "DELETE FROM {$table} WHERE {$champs_id} = ?";

                $delete = $pdo->prepare($requete);
                $delete->execute([$value_id]);

            $pdo->commit();
            
            return true;
        }
        catch(Exception $e)
        {
            //on annule la transation
            $pdo->rollback();
            return false;
        }

    }

    // fonction pour supprimer un enregistrement dans une table
    function change_statut($table, $champs, $etat, $champs_id, $value_id)
    {
        global $pdo;

        try
        {
            $pdo->beginTransaction();

                $requete = "UPDATE {$table} SET {$champs}={$etat} WHERE {$champs_id} = ?";

                $delete = $pdo->prepare($requete);
                $delete->execute([$value_id]);

            $pdo->commit();
            
            return true;
        }
        catch(Exception $e)
        {
            //on annule la transation
            $pdo->rollback();
            return false;
        }

    }
