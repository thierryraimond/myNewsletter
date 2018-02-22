<?php

require_once('inc/PHPMailer-master/class.phpmailer.php');
require_once('inc/PHPMailer-master/class.smtp.php');
require_once('inc/class.browser.php');
require_once('inc/lib.fct.php');
require_once('inc/class.pdonewsletter.inc.php');


$pdo = PdoNewsletter::getPdoNewsletter();
$controller = $_POST['controller'];

if($controller == "inscription"){
	$login = $_POST['login'];
	$password = $_POST['password'];
	$nbUtilisateurEmail = $pdo->getNbUtilisateurMemeEmail($login);
	if ($nbUtilisateurEmail > 0){
		ajouterErreur("l'adresse email ".$login." existe déjà.");
	} else {
		$pdo->creerNouveauUtilisateur($login,$password);
		emailInscription($login, $password);
	}
}

if($controller == "impossible"){
	$login = $_POST['login'];
	$nbUtilisateurEmail = $pdo->getNbUtilisateurMemeEmail($login);
	if ($nbUtilisateurEmail == 0){
		ajouterErreur("l'adresse email ".$login." n'existe pas. Veuillez-vous inscrire.");
	} else {
		$password = $pdo->getMdpUtilisateur($login);
		emailInscription($login, $password);
	}
}

if($controller == "authentification"){
	$login = $_POST['login'];
	$password = $_POST['password'];
	$message = '';
	$utilisateur = $pdo->getInfosUtilisateur($login, $password);
	if(!is_array($utilisateur)){
		$message = "<div class =\"alert alert-danger\">Adresse email ou mot de passe incorrect.</div>";
		echo json_encode(array(
			"message" => $message
		));
	} else {
		if($utilisateur['actif'] == 0){
			$message = "<div class =\"alert alert-danger\">Votre compte n'est pas actif. <br/>";
			$message .= "Veuillez l'activer en cliquant sur le lien 'Impossible de se connecter ?' ci-dessous.</div>";
			echo json_encode(array(
					"message" => $message
			));
		} else {
			/*session is started if you don't write this line can't use $_Session  global variable*/
			session_start();
			$_SESSION['login'] = $utilisateur['login'];
			$_SESSION['id'] = $utilisateur['id'];
			echo json_encode(array(
					"message" => $message,
					"login" => $utilisateur['login'],
					"id" => $utilisateur['id']
			));
		}
		//retourne le résultat en json format
// 		echo json_encode(array(
// 				"id" => $id,
// 				"libelle" => $smtp['libelle'],
// 				"host" => $smtp['host'],
// 				"port" => $smtp['port'],
// 				"smtpsecure" => $smtp['smtpsecure'],
// 				"username" => $smtp['username'],
// 				"password" => $smtp['password']
// 		));
	}	
}

if($controller == "deconnexion"){
	// remove all session variables
	session_unset();
	
	// destroy the session
	session_destroy();
}

if(isset($_REQUEST['info']) || isset($_REQUEST['erreur']) ) {
	echo "<div id=\"alertMain\">";

	if (isset($_REQUEST['info'])) {
		echo  "
		<div id=\"alertInfo\" class =\"alert alert-info\">
			<div class=\"controls\">
			";
		foreach ($_REQUEST['info'] as $info){
			echo $info;
		}
		echo "
		  	</div>
		</div>
	  		";

	}

	if (isset($_REQUEST['erreur'])) {
		echo  "
		<div id=\"alertErreur\" class =\"alert alert-danger\">
			<div class=\"controls\">
			";
		foreach ($_REQUEST['erreur'] as $erreur){
			echo $erreur;
		}
		echo "
		  	</div>
		</div>
	  		";

	}

	echo "</div>";
}


function emailInscription($email, $password){
	
	$messageBody = "
<html>
<head>
<title>Validation inscription 'ma newsletter'</title>
</head>
<body>
<p>			
Madame, Monsieur,<br/>
<br/>
Suite à votre inscription effectuée sur notre site, nous vous communiquons vos identifiants pour vous connecter sur votre espace client 'ma newsletter'.<br/>
<br/>
---<br/>
Identifiant : ".$email."<br/>
Mot de passe : ".$password."<br/>
----<br/>			
</p>	
<p>Cliquez
	<a href=\"http://trsrv.ddns.net/newsletter/emailValidation.php?login=".$email."\">ici</a>
	pour valider votre inscription.
</p>

</body>
</html>
";


	// Utilisation de la classe PHPMailer
	$mail = new PHPMailer();
	$mail->CharSet = 'UTF-8';
	$mail->setLanguage('fr');
	$mail->isSMTP();
	$mail->SMTPAuth = true;

	//Param�tre pour le serveur SMTP
	$mail->Host = "mail.envicomacte.fr";
	$mail->Port = 587;
	$mail->SMTPSecure = 'tls';
	// or more succintly : $mail->Host = 'tls://mail.actes-et-contact.com';

	$mail->SMTPAutoTLS = false; //emp�che le contr�le de certificat invalide
	// Autorise les connexions non s�curis�es via SMTPOptions
	$mail->SMTPOptions = array(
			'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
			)
	);

	$mail->Username = "contact@envicomacte.fr";
	$mail->Password = "angelique2912";

	$mail->From = "contact@envicomacte.fr";
	$mail->FromName = "contact";
	$mail->Subject = "Validation inscription 'ma newsletter'";
	$mail->msgHTML($messageBody);
	$mail->addAddress($email);

	// envoi du mail par PHPMailer
	if ($mail->send()) {
		ajouterInfo("une demande de confirmation a bien été envoyée à l'adresse ".$email." !<br/>");
		ajouterInfo("Veuillez consulter votre messagerie et confirmer votre inscription.");
		$mail->smtpClose();
	} else {
		ajouterInfo("Echec de l'envoi de votre email de confrimation : ".$mail->ErrorInfo);
		$mail->smtpClose();
	}
	
}



?>