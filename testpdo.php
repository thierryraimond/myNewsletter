<?php

require_once('inc/PHPMailer-master/class.phpmailer.php');
require_once('inc/PHPMailer-master/class.smtp.php');
require_once('inc/class.browser.php');
require_once('inc/lib.fct.php');
require_once('inc/class.pdonewsletter.inc.php');

//$nom = $_POST['inputDestinataireNom'];
//$id = $_POST['inputDestinataireId'];

// $id = $_POST['smtpId'];
// $libelle = $_POST['smtpLibelle'];
// $host = $_POST['smtpHost'];
// $port = $_POST['smtpPort'];
// $secure = $_POST['smtpSecure'];
// $user = $_POST['smtpUser'];
// $password = $_POST['smtpPassword'];

$id = $_GET['id'];
// $email = $_GET['email'];
// $from = $_GET['from'];
$diffusionId = $_GET['diffusionId'];

// exemple : http://www.site.com/page.php?param1=15&param2=rouge

$pdo = PdoNewsletter::getPdoNewsletter();

// $pdo->majResiliationlDestinataire($id);

// echo "l'adresse email '".$email."' est désabonnée de la newsletter en provenance de '".$from."'";

// $pdo->majSmtp($id,$libelle,$host,$port,$secure,$user,$password);

//$pdo->majNomDestinataire($id, $nom);

$lesHistoriques = $pdo->getLesHistoriquesTransmissionByDestinataireDiffusion($id,$diffusionId);
$nb = 0;
echo "<table class=\"table table-hover\">";
echo "<thead>";
echo "<tr>";
echo "<td class=\"col-lg-1\">Id</td>";
echo "<td class=\"col-lg-4\">Date</td>";
echo "<td class=\"col-lg-7\">R�sultat</td>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
foreach ($lesHistoriques as $leHistorique) {
	echo "<tr>";
	echo "<td class=\"col-lg-1\">";
	echo "<span class=\"form-control\">";
	echo $nb++;
	echo "</span>";
	echo "</td>";
	echo "<td class=\"col-lg-4\">";
	echo "<span class=\"form-control\">";
	echo date("d/m/Y H:i:s", strtotime($leHistorique['transmission_date']));
	echo "</span>";
	echo "</td>";
	echo "<td class=\"col-lg-7\">";
	echo "<span class=\"form-control\">";
	echo $leHistorique['resultat'];
	echo "</span>";
	echo "</td>";
	echo "</tr>";
}
echo "</tbody></table>";

?>