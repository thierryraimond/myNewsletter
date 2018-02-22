<?php

require_once('inc/PHPMailer-master/class.phpmailer.php');
require_once('inc/PHPMailer-master/class.smtp.php');
require_once('inc/class.browser.php');
require_once('inc/lib.fct.php');
require_once('inc/class.pdonewsletter.inc.php');


// try {
// 	$pdo = PdoNewsletter::getPdoNewsletter();
// 	echo "connexion à la bdd réussie";
// } catch (PDOException $e) {
// 	echo "Erreur !: " . $e->getMessage() . "<br/>";
// 	die();
// }

// $nom = $_POST['nom'];                    //On récupère le pseudo et on le stocke dans une variable
// $message = $_POST['message'];            //On fait de même avec le message
// $ligne = $nom.' > '.$message.'<br>';     //Le message est créé
// $leFichier = file('3.3-ac.htm');         //On lit le fichier ac.htm et on stocke la réponse dans une variable (de type tableau)
// array_unshift($leFichier, $ligne);       //On ajoute le texte calculé dans la ligne précédente au début du tableau
// file_put_contents('3.3-ac.htm', $leFichier); //On écrit le contenu du tableau $leFichier dans le fichier ac.htm


$pdo = PdoNewsletter::getPdoNewsletter();

$controller = $_POST['controller'];

if($controller == 'init_smtp'){
	$utilisateurId = $_POST['utilisateurId'];
	lesLibellesSmtp($pdo, $utilisateurId);
}
if($controller == 'init_listediffusion'){
	$utilisateurId = $_POST['utilisateurId'];
	lesLibellesListediffusion($pdo, $utilisateurId);
}

// $libelle =  $smtp['libelle'];

if($controller == "ajouter_smtp"){
	$libelle = $_POST['smtpLibelle'];
	$host = $_POST['smtpHost'];
	$port = $_POST['smtpPort'];
	$secure = $_POST['smtpSecure'];
	$user = $_POST['smtpUser'];
	$password = $_POST['smtpPassword'];
	$utilisateurId = $_POST['utilisateurId'];
	$libelle_societe = $_POST['libelle_societe'];
	$adresse_societe = $_POST['adresse_societe'];
	$url_ou_email_societe = $_POST['url_ou_email_societe'];
	
	$pdo->creeNouveauSmtp($libelle,$host,$port,$secure,$user,$password,$utilisateurId,$libelle_societe,$adresse_societe,$url_ou_email_societe);
	lesLibellesSmtp($pdo,$utilisateurId);
}

if($controller == "modifier_smtp"){
	$id = $_POST['smtpId'];
	$libelle = $_POST['smtpLibelle'];
	$host = $_POST['smtpHost'];
	$port = $_POST['smtpPort'];
	$secure = $_POST['smtpSecure'];
	$user = $_POST['smtpUser'];
	$password = $_POST['smtpPassword'];
	$utilisateurId = $_POST['utilisateurId'];
	$libelle_societe = $_POST['libelle_societe'];
	$adresse_societe = $_POST['adresse_societe'];
	$url_ou_email_societe = $_POST['url_ou_email_societe'];

	$pdo->majSmtp($id,$libelle,$host,$port,$secure,$user,$password,$libelle_societe,$adresse_societe,$url_ou_email_societe);
	lesLibellesSmtp($pdo,$utilisateurId);
}

if($controller == "infos_smtp"){
	$id = $_POST['id'];
	$smtp = $pdo->getInfosSmtp($id);
	//retourne le résultat en json format
	echo json_encode(array(
			"id" => $id,
			"libelle" => $smtp['libelle'], 
			"host" => $smtp['host'],
			"port" => $smtp['port'],
			"smtpsecure" => $smtp['smtpsecure'],
			"username" => $smtp['username'],
			"password" => $smtp['password'],
			"libelle_societe" => $smtp['libelle_societe'],
			"adresse_societe" => $smtp['adresse_societe'],
			"url_ou_email_societe" => $smtp['url_ou_email_societe']
	));
}

if($controller == "supprimer_smtp"){
	$id = $_POST['id'];
	$utilisateurId = $_POST['utilisateurId'];
	$pdo->delSmtp($id);
	lesLibellesSmtp($pdo,$utilisateurId);
}


if($controller == "ajouter_listediffusion"){
	$libelle = $_POST['listediffusionLibelle'];
	$utilisateurId = $_POST['utilisateurId'];

	$pdo->creeNouvelleListediffusion($libelle, $utilisateurId);
	lesLibellesListediffusion($pdo, $utilisateurId);
}

if($controller == "modifier_listediffusion"){
	$idListediffusion = $_POST['idListediffusion'];
	lesDestinatairesListediffusion($pdo, $idListediffusion);
}

if($controller == "supprimer_listediffusion"){
	$id = $_POST['id'];
	$utilisateurId = $_POST['utilisateurId'];
	$pdo->delDestinataireListediffusion($id); // on supprime d'abord les destinataires associés à la liste
	$pdo->delListediffusion($id); // ce qui nous permet de supprimer ensuite la liste de diffusion
	lesLibellesListediffusion($pdo, $utilisateurId);
}

if($controller == "ajouter_destinataire"){
	$idListediffusion = $_POST['idListediffusion'];
	$nom = $_POST['nom'];
	$email = $_POST['email'];
	
	$pdo->creeNouveauDestinataireListediffusion($idListediffusion, $nom, $email);
	lesDestinatairesListediffusion($pdo, $idListediffusion);
}

if($controller == "supprimer_destinataire"){
	$id = $_POST['id'];
	$idListediffusion = $_POST['idListediffusion'];
	$pdo->delHistoriqueDestinataire($id,$idListediffusion);
	$pdo->delDestinataire($id); 
	lesDestinatairesListediffusion($pdo, $idListediffusion);
}

if($controller == "modifier_nom_destinataire"){
	$id = $_POST['id'];
	$nom = $_POST['nom'];
	$idListediffusion = $_POST['idListediffusion'];
	$pdo->majNomDestinataire($id, $nom);
	lesDestinatairesListediffusion($pdo, $idListediffusion);
}
if($controller == "modifier_email_destinataire"){
	$id = $_POST['id'];
	$email = $_POST['email'];
	$idListediffusion = $_POST['idListediffusion'];
	$pdo->majEmailDestinataire($id, $email);
	lesDestinatairesListediffusion($pdo, $idListediffusion);
}

if($controller == "afficher_historique_transmission_destinataire"){
	$id = $_POST['id'];
	$idListediffusion = $_POST['idListediffusion'];
	afficherHistoriqueTransmissionDestinataire($pdo,$id,$idListediffusion);
}


/**
 * Retourne sur la page Html la liste des libellés des serveurs SMTP
 */
function lesLibellesSmtp($pdo, $utilisateurId){
	$smtp = $pdo->getLesLibellesSmtp($utilisateurId);
	foreach ($smtp as $libelleSmtp) {
		echo "<div class=\"col-lg-12\">";
		echo "<div class=\"row\">";
		echo "<div class=\"col-lg-8\"><a class=\"libelleSmtp\" id=\"".$libelleSmtp['id']."\" href=\"#\">".$libelleSmtp['libelle']."</a></div>";
		echo "<div class=\"col-lg-4\"><a class=\"btn btn-xs btn-default edit\" id=\"".$libelleSmtp['id']."\"><span class=\"glyphicon glyphicon-edit\" style=\"color:green;\"></span></a>";
		echo "<a class=\"btn btn-xs btn-default remove\" id=\"".$libelleSmtp['id']."\"><span class=\"glyphicon glyphicon-remove\" style=\"color:red;\"></span></a></div>";
		echo "</div></div>";
	}
	echo "<div class=\"col-lg-12\"><hr><a class=\"libelleSmtp\" href=\"#\">Paramétrer un nouveau serveur SMTP</a></div>";
}

/**
 * Retourne sur la page Html la liste des libellés des listes de diffusion
 */
function lesLibellesListediffusion($pdo, $utilisateurId){
	$lesListes = $pdo->getLesListediffusion($utilisateurId);
	echo "<div class=\"row\">";
	echo "<div class=\"col-lg-2 form-group\">";
	echo "<button class=\"btn btn-primary add\" id=\"btnAddListediffusion\" title=\"Ajouter une nouvelle liste de diffusion\" type=\"button\"><span class=\"glyphicon glyphicon-plus\"></span></button>";
	echo "</div>";
	echo "<div class=\"col-lg-10 form-group\">";
	echo "<form class=\"form-horizontal\" method=\"post\" action=\"pdo.php\" id=\"addListediffusion\">";
	echo "<div class=\"input-group\" id=\"input-group-add\" style=\"display:none;\">";
	echo "<input class=\"form-control\" id=\"listediffusionAdd\" name=\"listediffusionAdd\" placeholder=\"Saisissez un libellé\" type=\"text\" value=\"\" required>";
	echo "<span class=\"input-group-btn\">";
	echo "<button type=\"button\" class=\"btn btn-success add\" id=\"btnAddListediffusionValid\" type=\"submit\"><span class=\"glyphicon glyphicon-ok\"></span></button>";
	echo "</span></div></form></div></div>";
	echo "<div class=\"col-lg-12\" id=\"tableListediffusion\">";
	echo "<table class=\"table table-hover\">";
	echo "<tbody>";
	foreach ($lesListes as $laListe) {
		echo "<tr>";
		echo "<td class=\"col-lg-10\">";
		echo "<a class=\"libelleListediffusion\" id=\"".$laListe['id']."\" href=\"#\">".$laListe['libelle']."&nbsp&nbsp&nbsp<span class=\"badge\" style=\"background-color:black; color:white;\">".$pdo->getNbDestinataireListediffusion($laListe['id'])."</span></a></td>";
		echo "<td class=\"col-lg-1\"><a class=\"btn btn-xs btn-default edit\" id=\"".$laListe['id']."\"><span class=\"glyphicon glyphicon-edit\" style=\"color:green;\"></span></a></td>";
		echo "<td class=\"col-lg-1\"><a class=\"btn btn-xs btn-default remove\" id=\"".$laListe['id']."\"><span class=\"glyphicon glyphicon-remove\" style=\"color:red;\"></span></a></td>";
		echo "</tr>";
	}
	echo "</tbody></table></div>";
}

/**
 * Retourne sur la page Html la liste de tous les destinataires d'une liste de diffusion
 * 
 * @param $pdo L'instance de la classe PDO
 */
function lesDestinatairesListediffusion($pdo, $idListediffusion){
	$libelleListediffusion = $pdo->getLibelleListediffusion($idListediffusion);
	$lesDestinataires = $pdo->getLesDestinataires($idListediffusion);
	echo "<div class=\"panel-heading\" id=\"panelHeadingDestinataire\">";
		echo "<button type=\"button\" class=\"close\" aria-label=\"Close\"><span aria-hidden=\"true\" style=\"color:black;\">&times;</span></button>";
		echo "<h3 class=\"panel-title\" id=\"panelTitleDestinataireAjouter\">Les destinataires associés à la liste de diffusion \"".$libelleListediffusion."\"</h3>";
	echo "</div>";
	echo "<div class=\"panel-body\">";
		echo "<div class=\"col-lg-12\" id=\"tableDestinataire\">";
		
			echo "<div class=\"row\">";
				echo "<div class=\"col-lg-2 form-group\">";
					echo "<button class=\"btn btn-primary add\" id=\"btnAddDestinataireListediffusion\" title=\"Ajouter un nouveau destinataire à cette liste de diffusion\" type=\"button\"><span class=\"glyphicon glyphicon-plus\"></span></button>";
				echo "</div>";
				echo "<div class=\"col-lg-10 form-group\" style=\"display:none;\" id=\"divAddDestinataireForm\">";
					echo "<form class=\"form-horizontal\" method=\"post\" action=\"pdo.php\" id=\"addDestinataireListediffusion\">";
						echo "<div class=\"col-lg-5 form-group\">";
							echo "<input class=\"form-control\" id=\"destinataireNameAdd\" name=\"destinataireNameAdd\" placeholder=\"Saisissez un nom\" type=\"text\" required >";
						echo "</div>";
						echo "<div class=\"col-lg-5 form-group\">";
							echo "<input class=\"form-control\" id=\"destinataireEmailAdd\" name=\"destinataireEmailAdd\" placeholder=\"Saisissez un email\" type=\"email\" required >";
						echo "</div>";
						echo "<div class=\"col-lg-2 form-group\" id=\"divA\">";
							echo "<button type=\"button\" class=\"btn btn-success add\" id=\"btnAddDestinataireListediffusionValid\" type=\"submit\" ><span class=\"glyphicon glyphicon-ok\"></span></button>";
							echo "<a href=\"#\" id=\"".$idListediffusion."\" style=\"display:none;\"></a>";
						echo "</div>";
					echo "</form>";
				echo "</div>";
			echo "</div>";
	
	
			echo "<table class=\"table table-hover\">";
				echo "<thead>";
					echo "<tr>";
						echo "<td class=\"col-lg-4\">Nom</td>";
						echo "<td class=\"col-lg-4\">Email</td>";
						echo "<td class=\"col-lg-2\" style=\"text-align:center;\">Résiliation</td>";
					echo "</tr>";
				echo "</thead>";
				echo "<tbody>";
				foreach ($lesDestinataires as $leDestinatire) {
					echo "<tr id=\"destinataire".$leDestinatire['id']."\">";
						echo "<td class=\"col-lg-4\">";
							echo "<form class=\"form-horizontal destinataireNom\" action=\"testpdo.php\" method=\"post\" id=\"destinataireNom".$leDestinatire['id']."\">";
								echo "<div class=\"input-group\">";
									echo "<input class=\"form-control destinataireNom\" id=\"".$leDestinatire['id']."\" name=\"inputDestinataireNom\" placeholder=\"Saisissez un nom\" type=\"text\" value=\"".$leDestinatire['nom']."\" required>";
									echo "<input style=\"display:none;\" class=\"form-control destinataireId\" id=\"".$leDestinatire['id']."\" name=\"inputDestinataireId\" type=\"number\" value=\"".$leDestinatire['id']."\" required>";
									echo "<span class=\"input-group-btn\">";
										echo "<button class=\"btn btn-success\" id=\"btnValidDestinataireNom".$leDestinatire['id']."\" type=\"submit\"><span class=\"glyphicon glyphicon-ok\"></span></button>";
									echo "</span>";
								echo "</div>";
							echo "</form>";
						echo "</td>";
						echo "<td class=\"col-lg-4\">";
							echo "<form class=\"form-horizontal destinataireEmail\" action=\"pdo.php\" method=\"post\" id=\"destinataireEmail".$leDestinatire['id']."\">";
								echo "<div class=\"input-group\">";
									echo "<input class=\"form-control destinataireEmail\" id=\"".$leDestinatire['id']."\" name=\"inputDestinataireEmail\" placeholder=\"Saisissez un email\" type=\"email\" value=\"".$leDestinatire['email']."\" required>";
									echo "<input style=\"display:none;\" class=\"form-control destinataireId\" id=\"".$leDestinatire['id']."\" name=\"inputDestinataireId\" type=\"number\" value=\"".$leDestinatire['id']."\" required>";
									echo "<span class=\"input-group-btn\">";
										echo "<button class=\"btn btn-success\" id=\"btnValidDestinataireEmail".$leDestinatire['id']."\" type=\"submit\"><span class=\"glyphicon glyphicon-ok\"></span></button>";
									echo "</span>";
								echo "</div>";
							echo "</form>";
						echo "</td>";
						echo "<td class=\"col-lg-2\" style=\"text-align:center;\">";
						if($leDestinatire['resiliation'] != ''){
							echo "<span class=\"form-control\" style=\"color:#D9534F;\">";
								echo date("d-m-Y", strtotime($leDestinatire['resiliation']));
							echo "</span>";
						} else {
							echo "<span class=\"form-control\" style=\"color:#5CB85C;\">";
								echo "Actif";
							echo "</span>";
						}
						echo "</td>";
						echo "<td class=\"col-lg-2\" style=\"text-align:center;\">";
							echo "<button title=\"Historique des transmissions\" class=\"btn btn-warning historique_transmission\" id=\"".$leDestinatire['id']."\" type=\"button\">H <span class=\"glyphicon glyphicon-menu-down\" aria-hidden=\"true\"></span></button>";
							echo "<button class=\"btn btn-danger remove\" id=\"".$leDestinatire['id']."\" title=\"Supprime le destinataire de la liste '".$libelleListediffusion."'\" type=\"button\"><span class=\"glyphicon glyphicon-remove\"></span></button>";
						echo "</td>";
				//		echo "<div class=\"toggleHistorique\" id=\"toggleHistorique".$leDestinatire['id']."\" style=\"display:none; padding: 10px;\"></div>";
					echo "</tr>";
					echo "<tr class=\"col-lg-12 toggleHistorique\" id=\"toggleHistorique".$leDestinatire['id']."\" style=\"display:none; padding: 10px;height:200px; overflow-x:hidden; overflow-y:scroll;\"></tr>";
				}
				echo "</tbody>";
			echo "</table>";
		echo "</div>";

	echo "</div>";
}

function afficherHistoriqueTransmissionDestinataire($pdo,$id,$idListediffusion){
	$nb = 1;
	$lesHistoriques = $pdo->getLesHistoriquesTransmissionByDestinataireDiffusion($id,$idListediffusion);
	
	echo "<td>";
	echo "<table class=\"table table-striped\">";
	echo "<thead>";
	echo "<tr>";
	echo "<td class=\"col-lg-1\">Id</td>";
	echo "<td class=\"col-lg-4\">Date</td>";
	echo "<td class=\"col-lg-7\">Résultat</td>";
	echo "</tr>";
	echo "</thead>";
	echo "<tbody>";
	foreach ($lesHistoriques as $leHistorique) {
		echo "<tr>";
		echo "<td class=\"col-lg-1\">";
		echo $nb++;
		echo "</td>";
		echo "<td class=\"col-lg-4\">";
		echo date("d/m/Y H:i:s", strtotime($leHistorique['transmission_date']));
		echo "</td>";
		echo "<td class=\"col-lg-7\">";
		echo $leHistorique['resultat'];
		echo "</td>";
		echo "</tr>";
	}
	echo "</tbody></table>";
	echo "</td>";
}

?>