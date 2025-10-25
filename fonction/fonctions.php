<?php
	// fonctions pour renvoyer les informations en cas d'erreur
	function set_value($input_name='', $value=null){
		if(isset($_POST[$input_name])){
			echo htmlspecialchars($_POST[$input_name]);
		}
		else if(isset($value)){
			echo htmlspecialchars($value);
		}
	}

	// fonction permettant de selectionner un element option dans un select
	function select_option($name_select, $option_value, $value_simple=null){
		if(!empty($_POST[$name_select])){
			if($_POST[$name_select] == $option_value){
				echo 'selected';
			}
		}
		else if(isset($value_simple)){
			if($value_simple == $option_value){
				echo 'selected';
			}
		}
	}

	// fonction permettant de selectionner un element checkbox
	function set_checked($name_check, $statut_check=null){
		if(isset($_POST[$name_check])){
			if(!empty($_POST[$name_check])){
				echo "checked";
			}
		}
		else if(!empty($statut_check)){
			echo "checked";
		}
	}

	// fonction permettant de selectionner un element radio
	function set_radio($name_check, $statut_check = null, $value_check = null){
		if(isset($_POST[$name_check])){
			if(!empty($_POST[$name_check]) && ($_POST[$name_check] == $value_check)){
				echo "checked";
			}
		}
		else if(isset($statut_check) && ($statut_check == $value_check)){
			echo "checked";
		}
	}

	// fonction permettant de selectionner un element checkbox tableau
	function set_checked_tableau($name_check, $value, $tab =[]){
		if(!empty($_POST[$name_check])){
			// si la valeur du check est dans le tableau post
			if(in_array($value, $_POST[$name_check])){
				echo "checked";
			}

		}
		else if(in_array($value, $tab)){
			echo "checked";
		}
	}

	// fonction pour activer le tab dans la page traitement
	function active_tag($name_tag = null)
	{
		if($name_tag==null && count($_GET)==1)
		{
			echo 'active';
		}
		else if(!empty($_GET[$name_tag]) && $_GET[$name_tag] == 'active-tag')
		{
			echo "active";
		}
	}

	// fonction pour activer le tab dans la page traitement
	function active_tag_prix($name_tag = 'produit')
	{
		if($name_tag == 'produit' && count($_GET) == 0)
		{
			echo 'active';
		}
		else if(!empty($_GET[$name_tag]) && $_GET[$name_tag] == 'active-tag')
		{
			echo "active";
		}
	}

	// fonction pour activer le tab dans la page traitement
	function active_tag_mouvement($name_tag = 'caisse')
	{
		if($name_tag == 'caisse' && count($_GET) == 0)
		{
			echo 'active';
		}
		else if(!empty($_GET[$name_tag]) && $_GET[$name_tag] == 'active-tag')
		{
			echo "active";
		}
	}

	// fonction pour activer le tab dans la page traitement
	function active_tag_stock($name_tag = 'station')
	{
		if($name_tag == 'station' && count($_GET) == 0)
		{
			echo 'active';
		}
		else if(!empty($_GET[$name_tag]) && $_GET[$name_tag] == 'active-tag')
		{
			echo "active";
		}
	}

	// fonction pour ecrire format DD-MM-YYYY H:I:S
	function D_M_Y_format($format){

		$tab_date = explode(' ', $format);
		$date_y_m_d = D_M_Y($tab_date[0]);

		$date = $date_y_m_d.' '.$tab_date[1];
		return $date;
	}

	// fonction pour ecrire format DD-MM-YYYY H:I:S
	function D_M_Y_format0($format){

		$tab_date = explode(' ', $format);
		$date_y_m_d = D_M_Y($tab_date[0]);

		// $date = $date_y_m_d.' '.$tab_date[1];
		return $date_y_m_d;
	}

	// fonction pour ecrire la date YYYY-MM-DD en format DD-MM-YYYY
	function D_M_Y($date){
		if(count(explode('-', $date)) == 3){
			list($annee, $mois, $jour)	=	explode('-', $date);
			if(checkdate($mois, $jour, $annee)){
				return htmlspecialchars($jour.'-'.$mois.'-'.$annee);
			}
			else{
				return $date;
			}
		}
		else{
			return $date;
		}
	}

	// fonction de convertion d'une date sous la forme Y_M_D
	function Y_M_D($date)
	{
		return empty($date) ? null : htmlspecialchars(date("d-m-Y", strtotime($date)));
	}

	// fonction de convertion d'une date sous la forme Y_M_D
	function A_M_J($date)
	{
		return empty($date) ? null : htmlspecialchars(date("Y-m-d", strtotime($date)));
	}


	// fonction de convertion d'une date sous la forme Y_M_D
	function H_M_S($date)
	{
		return empty($date) ? null : htmlspecialchars(date("H:i:s", strtotime($date)));
	}

	// fonction de convertion d'une date sous la forme Y_M_D
	function H_M($date)
	{
		return empty($date) ? null : htmlspecialchars(date("H:i", strtotime($date)));
	}


	// fonction qui va retourner les mois de l'annee
	function donner_mois_annee(){
		$mois 	=	[
						'1' 	=> 'Janvier',
						'2' 	=> 'Février',
						'3' 	=> 'Mars',
						'4' 	=> 'Avril',
						'5' 	=> 'Mai',
						'6' 	=> 'Juin',
						'7' 	=> 'Juillet',
						'8' 	=> 'Août',
						'9' 	=> 'Septembre',
						'10' 	=> 'Octobre',
						'11' 	=> 'Novembre',
						'12' 	=> 'Décembre',
		];

		return $mois;
	}


	// fonction pour verifier plusieurs empty en meme temps
	function not_empty_group($file=[]){
		if(!empty($file)){
			foreach($file as $input){
				if(empty($input) || empty($_POST[$input])){
					return false;
				}
			}
		}
		else{
			return false;
		}
		return true;
	}

	// fonction pour verifier plusieurs empty en meme temps
	function not_isset_group($file=[]){
		if(isset($file)){
			foreach($file as $input){
				if(!isset($input) || !isset($_POST[$input])){
					return false;
				}
			}
		}
		else{
			return false;
		}
		return true;
	}

	// fonction pour verifier si la date saisi est correct
	function verifier_date($date){
		$partie_date =	explode('-', $date);
		if(count($partie_date)	== 3){
			list($jour, $mois, $annee)	=	explode('-', $date);
			if(is_numeric($jour) && is_numeric($mois) && is_numeric($annee)){
				if(!empty($jour) && !empty($mois) && !empty($annee)){
					if(checkdate($mois, $jour, $annee)){
						return true;
					}
					else{
						return false;
					}
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}

	// fonction de comparaison entre deux date
	function comparer_date($date1, $date2){
		if(verifier_date($date1) && verifier_date($date2)){
			if(strtotime($date1) - strtotime($date2) <=0){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}

	// fonction qui va nous permettre de mettre notre message dans les sessions pour ensuites les affiches apres;
	function alert_message($type_alert, $contenu_alert){
		if(!empty($type_alert) && !empty($contenu_alert)){

			$_SESSION['alert']['type']		=	$type_alert;
			$_SESSION['alert']['message']	=	$contenu_alert;
		}
	}
	
	// fonction qui va nous permettre de mettre notre message dans les sessions pour ensuites les affiches apres;
	function toast_message($type_alert, $contenu_alert){
		if(!empty($type_alert) && !empty($contenu_alert)){

			$_SESSION['notification']['type']		=	$type_alert;
			$_SESSION['notification']['message']	=	$contenu_alert;
		}
	}

	// fonction qui va afficher un nombre en bloc de 3 chiffre
	function bloc_de_3_chiffre($nombre){

		return htmlspecialchars(number_format ( $nombre , $decimals = 0 , $dec_point = '.' , $thousands_sep = ' ' ));
	}

	// fonction qui va afficher un nombre en bloc de 3 chiffre
	function b_c_3($nombre, $decimals = 0, $dec_point = '.'){

		return htmlspecialchars(number_format ( $nombre , $decimals, $dec_point , $thousands_sep = ' ' ));
	}
	
	// fonction qui va permettre de savoir si une personne a droit a une action
	function verifier_droit_action($url_action){

		// CONTROLE D'ACCES AUX action
		// tableau contenant les pages autorisees
		$action_autorisees 	=	array();
		// recuperations des differentes action cote configuration pour les mettre dans le tableau action_autorisees
		foreach($_SESSION['bloc_config'] as $config){
			$action_autorisees[]	=	$config['url_action'];
		}

		// recuperations des differentes action cote simple pour les mettre dans le tableau action_autorisees
		foreach($_SESSION['bloc_simple'] as $simple){
			$action_autorisees[]	=	$simple['url_action'];
		}
		// verification de l'existance du droit dans la liste des action autorisees
		if(in_array($url_action, $action_autorisees)){
			return true;
		}
		else{
			return false;
		}

	}


	// fonction qui va permettre de verifier si la personne a droit a la page
	function verifier_droit_page(){

		// CONTROLE D'ACCES AUX action
		// tableau contenant les pages autorisees
		$page_autorisees 	=	array();
		// recuperations des differentes action cote configuration pour les mettre dans le tableau action_autorisees
		foreach($_SESSION['bloc_config'] as $config){
			$page_autorisees[]	=	$config['url_action'];
		}

		// recuperations des differentes action cote simple pour les mettre dans le tableau action_autorisees
		foreach($_SESSION['bloc_simple'] as $simple){
			$page_autorisees[]	=	$simple['url_action'];
		}

		// recuperations des differentes action cote simple pour les mettre dans le tableau action_autorisees
		foreach($_SESSION['bloc_deroulant'] as $simple){
			$page_autorisees[]	=	$simple['url_action'];
		}

		$url_courante = $_SERVER['PHP_SELF'];
		$url_courante_segmente = explode('/', $url_courante);
		$page_courante = array_pop($url_courante_segmente);

		// verification si la page figure dans la liste des pages autorisees
		if(in_array($page_courante, $page_autorisees)){
			return true;
		}
		else{
			session_destroy();
			// demarrage de la session
			session_start();
			alert_message('danger','Vous n\'avez pas accés à cette page.');
			header("location:../index.php");
			die();
		}
	}

	// fonction qui va renvoyer le nombre de jour entre deux dates
	function date_diff_jour($date1, $date2){

		// conversion des dates en secondes
		$date1 = strtotime($date1);
		$date2 = strtotime($date2);

		// un jour en seconde
		$jour_en_seconde = 86400;

		return ($date1 -$date2)/ $jour_en_seconde;
	}


	// fonction d'envoi de mail
	function sendmail($destinataire, $objet, $message){
		$header = "From:'2I gestion des ressources humaines'\r\n";
		$header .= 'Content-Type: text/html; charset="utf-8"';

		mail($destinataire, $objet, $message, $header);
	}


	// fonction pour charger les fichier
	function upload_file($file_name, $path, $default_name)
	{
		// si le champ document a ete renseigne
        if(!empty($_FILES[$file_name]['name']))
        {
            $document  = $_FILES[$file_name]['name'];
            $document_tmp  = $_FILES[$file_name]['tmp_name'];
            $document_extension  = explode('/', $_FILES[$file_name]['type'])[1];
            $extension_autorisee =  array('pdf', 'docx', 'png', 'jpg', 'jpeg');

            // est ce que l'extension a ete respectee
            if(in_array($document_extension, $extension_autorisee)){
                // on essaie de charger le document
                if(move_uploaded_file($document_tmp, $path.$document)){
                    $document  = $path.$document;
                }
                else{
                    // echec de chargement du document
                    $document = $default_name;
                }
            }
            else{
                // l'extension n'a pas ete respectee
                $document = $default_name;
            }
        }
        else{
            // si le document n'a pas ete renseignee
            $document = $default_name;
        }

        return $document;
	}

	// fonction pour charger les fichier
	function upload_file_tab($file_name, $position, $path, $default_name)
	{
		// si le champ document a ete renseigne
        if(!empty($_FILES[$file_name]['name'][$position]))
        {
            $document  = $_FILES[$file_name]['name'][$position];
            $document_tmp  = $_FILES[$file_name]['tmp_name'][$position];
            $file_type = $_FILES[$file_name]['type'][$position];
            $document_extension  = explode('/', $file_type)[1];
            $extension_autorisee =  array('pdf', 'docx', 'png', 'jpg', 'jpeg');

            // est ce que l'extension a ete respectee
            if(in_array($document_extension, $extension_autorisee)){
                // on essaie de charger le document
                if(move_uploaded_file($document_tmp, $path.$document)){
                    $document  = $path.$document;
                }
                else{
                    // echec de chargement du document
                    $document = $default_name;
                }
            }
            else{
                // l'extension n'a pas ete respectee
                $document = $default_name;
            }
        }
        else{
            // si le document n'a pas ete renseignee
            $document = $default_name;
        }

        return $document;
	}

	function rmAllDir($strDirectory)
	{
		try
		{
		    $handle = opendir($strDirectory);
		    while(false !== ($entry = readdir($handle)))
		    {
		        if($entry != '.' && $entry != '..')
		        {
		            if(is_dir($strDirectory.'/'.$entry))
		            {
		                rmAllDir($strDirectory.'/'.$entry);
		            }
		            elseif(is_file($strDirectory.'/'.$entry))
		            {
		                unlink($strDirectory.'/'.$entry);
		            }
		        }
		    }
		    rmdir($strDirectory.'/'.$entry);
		    closedir($handle);
		}
		catch(Exception $e)
		{
			
		}
	}

	// fonction qui permet de reconstruire un montant parser
	function dd(...$args){
	    echo '<pre>'; // pour lisibilité dans le navigateur
	    foreach ($args as $arg) {
	        var_dump($arg);
	    }
	    echo '</pre>';
	    die();
	}

	function montant_parse(string $valeur): string {
	    // Enlève espaces normaux, espaces insécables et autres caractères invisibles
	    return str_replace([' ', ' ', "\t", "\n", "\r"], '', $valeur);
	}

	function statutBadge($id_statut) {
	    switch ($id_statut) {
	        case 1:
	            return '<span class="badge bg-success">Payé</span>';
	        case 2:
	            return '<span class="badge bg-warning text-dark">Partiel</span>';
	        case 3:
	            return '<span class="badge bg-danger">Crédit</span>';
	        default:
	            return '<span class="badge bg-secondary">Inconnu</span>';
	    }
	}

	function statutBadgePaiement($id_statut) {
	    switch ($id_statut) {
	        case true:
	            return '<span class="badge bg-success">Validé</span>';
	        case false:
	            return '<span class="badge bg-danger text-dark">Pas Validé</span>';
	        default:
	            return '<span class="badge bg-secondary">Inconnu</span>';
	    }
	}

?>