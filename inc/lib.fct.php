<?php
/**
 * Retourne les données en paramètre après traitement (trim, stripslashes et htmlspecialchars).
 * 
 * @param unknown $data
 * @return string
 */
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}



/**
 * Ajoute le libellé d'une info au tableau des infos
 * @param string $msg : le libellé de l'info
 */
function ajouterInfo($msg) {
	if (!isset($_REQUEST['info'])) {
		$_REQUEST['info'] = array();
	}
	$_REQUEST['info'][] = $msg;
}

/**
 * Ajoute le libellé d'une erreur au tableau des erreurs
 * @param string $msg : le libellé de l'erreur
 */
function ajouterErreur($msg) {
	if (!isset($_REQUEST['erreur'])) {
		$_REQUEST['erreur'] = array();
	}
	$_REQUEST['erreur'][] = $msg;
}


/**
 * Retourne le nom du navigateur utilisé par le client
 * @param variableGlobale $user_agent $_SERVER['HTTP_USER_AGENT']
 * @return string nom du navigateur utilisé
 */
function get_browser_name($user_agent)
{
	if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
	elseif (strpos($user_agent, 'Edge')) return 'Edge';
	elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
	elseif (strpos($user_agent, 'Safari')) return 'Safari';
	elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
	elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';

	return 'Other';
}


function controlVersionBrowser() {
	$info_browser = get_browser(null, true);
	$getBrowser = "";

	$navigateur = new Browser();

	foreach ($navigateur->get_browser() as $leNavigateur){

		if ($info_browser['browser'] == "IEMOBILE" && floatval($info_browser['version']) < 11.0) {
			$getBrowser = "<ul>
						<li>Vous utilisez le navigateur : <b style=\"color:green;\">".$info_browser['browser']."</b></li>
						<li>Version : <b style=\"color:red;\">".$info_browser['version']."</b></li>
						<li>Votre système d'exploitation : <b style=\"color:green;\">".$info_browser['platform']."</b></li>
  					</ul>
				La version de votre navigateur est trop ancienne, vous risquez d'avoir des erreurs d'affichage.<br/>
				Pour résoudre ce problème, veuillez télécharger la dernière version de votre navigateur ou au moins la version 11.0<br/>";
			ajouterErreur($getBrowser);
			break;
		} elseif ($info_browser['browser'] == "Safari") {
			$getBrowser = "<ul>
						<li>Vous utilisez le navigateur : <b style=\"color:red;\">".$info_browser['browser']."</b></li>
						<li>Version : <b style=\"color:red;\">".$info_browser['version']."</b></li>
						<li>Votre système d'exploitation : <b style=\"color:green;\">".$info_browser['platform']."</b></li>
  					</ul>
				<b style=\"color:red;\">La Navigateur Safari ne vous permet pas un confort de navigation optimal pour ce site.<br/>
				Veuillez utiliser la dernière version de l'un des navigateurs suivants :</b></br>
				";
			ajouterErreur($getBrowser."<b>Liste des navigateurs compatibles :</b><br/>".$navigateur->affiche());
			break;

		} elseif ($info_browser['browser'] == $leNavigateur['name'] && floatval($info_browser['version']) < $leNavigateur['version']) {
			$getBrowser = "<ul>
						<li>Vous utilisez le navigateur : <b style=\"color:green;\">".$info_browser['browser']."</b></li>
						<li>Version : <b style=\"color:red;\">".$info_browser['version']."</b></li>
						<li>Votre système d'exploitation : <b style=\"color:green;\">".$info_browser['platform']."</b></li>
  					</ul>
				<b style=\"color:red;\">La version de votre navigateur est trop ancienne, vous risquez d'avoir des erreurs d'affichage.<br/>
				Pour résoudre ce problème, veuillez télécharger la dernière version de votre navigateur ou au moins la version ".$leNavigateur['version']."</b><br/>";
			ajouterErreur($getBrowser."<b>Liste des navigateurs compatibles :</b><br/>".$navigateur->affiche());
			break;
		}
	}

	//	elseif ($info_browser['browser'] == $navigateur->get_browser()["IE"]["name"] && floatval($info_browser['version']) < $navigateur->get_browser()["IE"]["version"])

	if ($getBrowser != "") {
		return $getBrowser;
	} else {
		$getBrowser = "<ul>
						<li>Vous utilisez le navigateur : <b style=\"color:green;\">".$info_browser['browser']."</b></li>
						<li>Version : <b style=\"color:green;\">".$info_browser['version']."</b></li>
						<li>Votre système d'exploitation : <b style=\"color:green;\">".$info_browser['platform']."</b></li>
  					</ul>";
		return $getBrowser;
	}

}

?>