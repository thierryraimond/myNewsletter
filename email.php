<?php
	require_once('inc/PHPMailer-master/class.phpmailer.php');
	require_once('inc/PHPMailer-master/class.smtp.php');
	require_once('inc/class.browser.php');
	require_once('inc/lib.fct.php');
	require_once('inc/class.pdonewsletter.inc.php');
	
// 	$nom = $_POST['nom'];                    //On r�cup�re le pseudo et on le stocke dans une variable
// 	$message = $_POST['message'];            //On fait de m�me avec le message
// 	$ligne = $nom.' > '.$message.'<br>';     //Le message est cr��
// 	$leFichier = file('3.3-ac.htm');         //On lit le fichier ac.htm et on stocke la r�ponse dans une variable (de type tableau)
// 	array_unshift($leFichier, $ligne);       //On ajoute le texte calcul� dans la ligne pr�c�dente au d�but du tableau
// 	file_put_contents('3.3-ac.htm', $leFichier); //On �crit le contenu du tableau $leFichier dans le fichier ac.htm
	
	$contenu = $_POST['contenu'];
	
	file_put_contents('test.html', $contenu); //On écrit le contenu dans le fichier 'test.html'
	
	//echo $contenu;
	
	$subject = $_POST['subject'];
	$idSmtp = $_POST['idSmtp'];
	$idListediffusion = $_POST['idListediffusion'];
	
	$pdo = PdoNewsletter::getPdoNewsletter();
	
	$smtp = $pdo->getInfosSmtp($idSmtp);
	$lesDestinataires = $pdo->getLesDestinataires($idListediffusion);
	

	
	// Utilisation de la classe PHPMailer
	$mail = new PHPMailer();
	$mail->CharSet = 'UTF-8';
	$mail->setLanguage('fr');
	$mail->isSMTP();
	$mail->SMTPAuth = true;
	$mail->isHTML(true);                                  // Set email format to HTML
	//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
	
	//Paramétre pour le serveur SMTP
	$mail->Host = $smtp['host']; //$mail->Host = "mail.actes-et-contacts.com";
	$mail->Port = $smtp['port']; //$mail->Port = 587;
	$mail->SMTPSecure = $smtp['smtpsecure']; //$mail->SMTPSecure = 'tls';
	// or more succintly : $mail->Host = 'tls://mail.actes-et-contact.com';
	
	$mail->SMTPAutoTLS = false; //empéche le contréle de certificat invalide
	// Autorise les connexions non sécurisées via SMTPOptions
	$mail->SMTPOptions = array(
			'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
			)
	);
	
	$mail->Username = $smtp['username']; //$mail->Username = "contact@actes-et-contacts.com";
	$mail->Password = $smtp['password']; //$mail->Password = "angelique2912";
	
	$mail->From = $smtp['username']; //$mail->From = "thierry@actes-et-contacts.com";
	$mail->FromName = $smtp['libelle']; //$mail->FromName = "noreply";
	
	$mail->Subject = $subject;
	
	set_time_limit(600); //to prevent the script from dying (default:120)
	
	
	//$mail->addAddress("thierry@actes-et-contacts.com", "Thierry actes-et-contacts.com"); 
	//$mail->addAddress("angelique@actes-et-contacts.com", "Angelique actes-et-contacts.com");
	
	foreach ($lesDestinataires as $leDestinatire) {
	
	// si le destinataire est actif (abonné)
	if($leDestinatire['resiliation'] == ''){
		
		
		$messageBody = "
<!DOCTYPE html>
<html>
<head>
<meta charset=\"UTF-8\">
<title>".$subject."</title>
</head>
<body>
	<div align=center>
				<br/>
		".$contenu."
	</div>
		<br/>
		<br/>
		<br/>
	<div align=center>
		<hr>
		<p style='font-size:7.5pt;font-family:\"Helvetica\",\"sans-serif\"; mso-fareast-font-family:\"Times New Roman\";color:black'>
			Ceci est un message marketing émis par ".$smtp['libelle_societe'].",
 			".$smtp['adresse_societe'].".<br/>
			Cliquez
			<a href=\"http://trsrv.ddns.net/newsletter/resiliation.php?id=".$leDestinatire['id']."&email=".$leDestinatire['email']."&from=".$smtp['username']."\">ici</a>
			pour vous désabonner.<br/>
 			Conformément à l'article 34 de la loi \"Informatique et Libertés\", toute personne dispose d'un droit d'accès, 
			de modification, de réctification et de suppression des données conservées pour un durée d'un an par ".$smtp['libelle_societe'].". 
			Pour l'exercer, vous pouvez vous adresser à l'administrateur du site ".$smtp['url_ou_email_societe']."			
		</p>
	</div>
</body>
</html>
";
//	exemple:	http://www.site.com/page.php?param1=15&param2=rouge
		
		$mail->msgHTML($messageBody);
		$ligne = '';
		$resultat = '';
		
		if($mail->addAddress($leDestinatire['email'], $leDestinatire['nom'])){		
			//$mail->addBCC($leDestinatire['email']);
			// envoi du mail par PHPMailer
			if ($mail->send()) {
				ajouterInfo(date('d/m/Y H:i:s')." Votre email a été envoyé avec succés à l'adresse email : ".$leDestinatire['email']."<br/>");
				$resultat = "email envoyé avec succés";
				$ligne = date('d/m/Y H:i:s')." Votre email a été envoyé avec succés à l'adresse email : ".$leDestinatire['email']."<br/>";
				$mail->clearAllRecipients(); //supprime tous les destinataires
			} else {
				ajouterErreur(date('d/m/Y H:i:s')." Echec de l'envoi de votre email : ".$mail->ErrorInfo."<br/>");
				$resultat = $mail->ErrorInfo;
				$ligne = date('d/m/Y H:i:s')." Echec de l'envoi de votre email : ".$mail->ErrorInfo."<br/>";
				$mail->clearAllRecipients(); //supprime tous les destinataires
			}
		}else {
			ajouterErreur(date('d/m/Y H:i:s')." Adresse email déjà utilisée ou invalide : ".$leDestinatire['email']."<br/>");
			$resultat = "email déjà utilisée ou invalide";
			$ligne = date('d/m/Y H:i:s')." Adresse email déjà utilisée ou invalide : ".$leDestinatire['email']."<br/>";
		}
		
		//alimentation du fichier email_transmission.htm
		$leFichier = file('email_transmission.htm');         //On lit le fichier email_transmission.htm et on stocke la réponse dans une variable (de type tableau)
		array_unshift($leFichier, $ligne);       //On ajoute le texte calculé dans la ligne précédente au début du tableau
		file_put_contents('email_transmission.htm', $leFichier); //On écrit le contenu du tableau $leFichier dans le fichier email_transmission.htm
		//insertion historique en bdd
		$pdo->creerNouvelleLigneHistoriqueTransmission($resultat,$leDestinatire['id'],$leDestinatire['listediffusion_id']);
		
	}
	}
	$mail->smtpClose();
	
	//$mail->addCC('cc@example.com'); //copie conforme
	//$mail->addBCC('bcc@example.com'); //copie cachée
	
	
	// envoi du mail par PHPMailer
// 	if ($mail->send()) {
// 		ajouterInfo("Votre email a été envoyé avec succés !");
// 		$mail->smtpClose();
// 	} else {
// 		ajouterInfo("Echec de l'envoi de votre email : ".$mail->ErrorInfo);
// 		$mail->smtpClose();
// 	}
	
	// affiche les événements
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
	
	
?>