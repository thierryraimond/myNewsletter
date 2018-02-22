<?php

require_once('inc/PHPMailer-master/class.phpmailer.php');
require_once('inc/PHPMailer-master/class.smtp.php');
require_once('inc/class.browser.php');
require_once('inc/lib.fct.php');
require_once('inc/class.pdonewsletter.inc.php');

$id = $_GET['id'];
$email = $_GET['email'];
$from = $_GET['from'];

// exemple : http://www.site.com/page.php?param1=15&param2=rouge

$pdo = PdoNewsletter::getPdoNewsletter();

$pdo->majResiliationlDestinataire($id);

echo "l'adresse email '".$email."' est désabonnée de la newsletter en provenance de '".$from."'";


?>