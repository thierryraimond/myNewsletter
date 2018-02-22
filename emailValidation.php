<?php

require_once('inc/PHPMailer-master/class.phpmailer.php');
require_once('inc/PHPMailer-master/class.smtp.php');
require_once('inc/class.browser.php');
require_once('inc/lib.fct.php');
require_once('inc/class.pdonewsletter.inc.php');

$email = $_GET['login'];

// exemple : http://www.site.com/page.php?param1=15&param2=rouge

$pdo = PdoNewsletter::getPdoNewsletter();

$pdo->majActifUtilisateur($email);

echo "Votre inscription pour l'adresse ".$email." est validée.";


?>