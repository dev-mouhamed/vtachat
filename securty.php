<?php 

    // Données entrantes
    function echapper($string)
    {
        // On regarde si le type de string est un nombre entier (int)
        if(ctype_digit($string))
        {
            $string = htmlspecialchars($string ?? '');
        }
        // Pour tous les autres types
        else
        {
           // $string = mysql_real_escape_string($string);
            // $string = addcslashes($string, '%_');
            $string = htmlspecialchars($string ?? '');
        }
            
        return $string;
    }//fin fonction echapper
     
   //pour les données provenant de la base de données on applique: htmlentities et stripcslashes