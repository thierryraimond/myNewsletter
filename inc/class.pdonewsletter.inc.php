<?php
/**
 * Classe d'acc�s aux donn�es.
 *
 * Utilise les services de la classe PDO
 * pour l'application newsletter
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoNewsletter qui contiendra l'unique instance de la classe
 * @package default
 * @author Thierry
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */
class PdoNewsletter {
	private static $serveur='mysql:host=trsrv.no-ip.org'; // version dev = trsrv.ddns.net // version prod = trsrv.no-ip.org
	private static $bdd='dbname=newsletter';
	private static $user='root' ; // version dev = thierry // version prod = root
	private static $mdp='london06' ;
	private static $monPdo;
	private static $monPdoNewsletter=null;
	/**
	 * Constructeur priv�, cr�e l'instance de PDO qui sera sollicit�e
	 * pour toutes les m�thodes de la classe
	 */
	private function __construct(){
		PdoNewsletter::$monPdo = new PDO(PdoNewsletter::$serveur.';'.PdoNewsletter::$bdd, PdoNewsletter::$user, PdoNewsletter::$mdp);
		PdoNewsletter::$monPdo->query("SET CHARACTER SET utf8");
	}
	public function _destruct(){
		PdoNewsletter::$monPdo = null;
	}
	/**
	 * Fonction statique qui cr�e l'unique instance de la classe
	 *
	 * Appel : $instancePdoNewsletter = PdoNewsletter::getPdoNewsletter();
	 * @return objet l'unique objet de la classe PdoNewsletter
	 */
	public static function getPdoNewsletter(){
		if(PdoNewsletter::$monPdoNewsletter==null){
			PdoNewsletter::$monPdoNewsletter= new PdoNewsletter();
		}
		return PdoNewsletter::$monPdoNewsletter;
	}
	
	public function getLesLibellesSmtp($utilisateurId) {
		$req = "SELECT smtp.id as id, smtp.libelle as libelle 
				FROM smtp
				WHERE smtp.utilisateur_id = '$utilisateurId'";
		$rs = PdoNewsletter::$monPdo->query($req);
		$lesLignes = $rs->fetchAll();
		$rs->closeCursor();
		return $lesLignes;
	}
	
	/**
	 * Crée un nouveau serveur de messagerie sortant (SMTP)
	 * à partir des informations fournies en paramètre
	 *
	 * @param string $libelle
	 * @param string $host
	 * @param integer $port
	 * @param string $smtpsecure
	 * @param string $username
	 * @param string $password
	 * @param string $utilisateurId
	 * @param string $libelle_societe
	 * @param string $adresse_societe
	 * @param string $url_ou_email_societe
	 */
	public function creeNouveauSmtp($libelle,$host,$port,$smtpsecure,$username,$password,$utilisateurId,$libelle_societe,$adresse_societe,$url_ou_email_societe){
		$req = "INSERT INTO smtp
				values('','$libelle','$host','$port','$smtpsecure','$username','$password','$utilisateurId','$libelle_societe','$adresse_societe','$url_ou_email_societe')";
		PdoNewsletter::$monPdo->exec($req);
	}
	
	/**
	 * Retourne les informations d'un serveur SMTP à partir de son id
	 *
	 * @param string $id
	 * @return array l'id, le libelle, l'host, le port, le type de chiffrement, le user,
	 * le password, le libelle de la société, son adresse et son url ou email sous la forme d'un tableau associatif
	 */
	public function getInfosSmtp($id) {
		$req = "SELECT smtp.id as id, smtp.libelle as libelle, smtp.host as host, smtp.port as port,
				smtp.smtpsecure as smtpsecure, smtp.username as username, smtp.password as password,
				smtp.libelle_societe as libelle_societe, smtp.adresse_societe as adresse_societe,
				smtp.url_ou_email_societe as url_ou_email_societe
				FROM smtp
				WHERE smtp.id = '$id'";
		$rs = PdoNewsletter::$monPdo->query($req);
		$ligne = $rs->fetch();
		$rs->closeCursor();
		return $ligne;
	}
	
	/**
	 * Modifie les informations d'un serveur SMTP
	 *
	 * @param integer $id
	 * @param string $libelle
	 * @param string $host
	 * @param integer $port
	 * @param string $smtpsecure
	 * @param string $username
	 * @param string $password
	 * @param string $libelle_societe
	 * @param string $adresse_societe
	 * @param string $url_ou_email_societe
	 */
	public function majSmtp($id, $libelle,$host,$port,$smtpsecure,$username,$password,$libelle_societe,$adresse_societe,$url_ou_email_societe){
// 		$req = "UPDATE smtp
// 				SET libelle = '$libelle', host = '$host', port = $port,
// 				smtpsecure = '$smtpsecure', username = '$username', password = '$password'
// 				WHERE smtp.id = $id";
		$req = "UPDATE smtp SET smtp.libelle = '$libelle', host = '$host', port = $port,
 				smtpsecure = '$smtpsecure', username = '$username', password = '$password', 
 				libelle_societe = '$libelle_societe', adresse_societe = '$adresse_societe', 
 				url_ou_email_societe = '$url_ou_email_societe'
				WHERE smtp.id = $id";
		PdoNewsletter::$monPdo->exec($req);
	}
	
	/**
	 * Supprime les informations d'un serveur SMTP
	 *
	 * @param string $id
	 */
	public function delSmtp($id){
		$req = "DELETE FROM smtp WHERE smtp.id = '$id'";
		PdoNewsletter::$monPdo->exec($req);
	}
	
	/**
	 * Retourne la liste des listes de diffusion par rapport à l'id utilisateur en paramètre
	 * 
	 * @return un tableau associatif comprenant les id et libellés de toutes les listes de diffusion
	 */
	public function getLesListediffusion($utilisateurId) {
		$req = "SELECT listediffusion.id as id, listediffusion.libelle as libelle 
				FROM listediffusion
				WHERE listediffusion.utilisateur_id = '$utilisateurId'";
		$rs = PdoNewsletter::$monPdo->query($req);
		$lesLignes = $rs->fetchAll();
		$rs->closeCursor();
		return $lesLignes;
	}
	
	/**
	 * Crée une nouvelle liste de diffusion
	 * à partir des informations fournies en paramètre
	 *
	 * @param string $libelle
	 * @param string $utilisateurId
	 */
	public function creeNouvelleListediffusion($libelle, $utilisateurId){
		$req = "INSERT INTO listediffusion
		values('','$libelle','$utilisateurId')";
		PdoNewsletter::$monPdo->exec($req);
	}
	
	/**
	 * Retourne sous forme d'un tableau associatif toutes les lignes de destinataire
	 * concernées par l'id de la liste de diffusion en paramètre
	 * @param integer $idListediffusion
	 * @return array[] l'id destinataire, l'email, le nom et l'id de la liste de diffusion sous la forme d'un tableau associatif
	 */
	public function getLesDestinataires($idListediffusion){
		$req = "SELECT destinataire.id as id, destinataire.email as email,
				destinataire.nom as nom, destinataire.listediffusion_id as listediffusion_id,
				destinataire.resiliation as resiliation
				FROM destinataire INNER JOIN listediffusion
				ON destinataire.listediffusion_id = listediffusion.id
				WHERE listediffusion.id ='$idListediffusion'
				ORDER BY destinataire.id";
		$res = PdoNewsletter::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		$res->closeCursor();
		return $lesLignes;
	}
	
	/**
	 * Créer un nouveau destinataire associé à une liste de diffusion
	 * à partir des informations fournies en paramètre
	 *
	 * @param integer $idListediffusion
	 * @param string $nom
	 * @param string $email
	 */
	public function creeNouveauDestinataireListediffusion($idListediffusion, $nom, $email){
		$req = "INSERT INTO destinataire
		values('','$email', '$nom', '$idListediffusion', NULL)";
		PdoNewsletter::$monPdo->exec($req);
	}
	
	/**
	 * Retourne le libellé d'une liste de diffusion à partir de son id
	 *
	 * @param string $id
	 * @return string le libelle de la la liste de diffusion
	 */
	public function getLibelleListediffusion($id) {
		$req = "SELECT listediffusion.libelle as libelle
				FROM listediffusion
				WHERE listediffusion.id = '$id'";
		$rs = PdoNewsletter::$monPdo->query($req);
		$ligne = $rs->fetch();
		$rs->closeCursor();
		return $ligne['libelle'];
	}
	
	/**
	 * Supprime une liste de diffusion grâce à l'id cité en paramètre
	 *
	 * @param integer $id
	 */
	public function delListediffusion($id){
		$req = "DELETE FROM listediffusion WHERE listediffusion.id = '$id'";
		PdoNewsletter::$monPdo->exec($req);
	}
	
	/**
	 * Supprime tous les destinataires d'une liste de diffusion grâce à l'id de la liste cité en paramétre
	 *
	 * @param integer $id
	 */
	public function delDestinataireListediffusion($id){
		$req = "DELETE FROM destinataire WHERE destinataire.listediffusion_id = '$id'";
		PdoNewsletter::$monPdo->exec($req);
	}
	
	/**
	 * Supprime le destinataire comportant l'id en paramètre
	 *
	 * @param integer $id
	 */
	public function delDestinataire($id){
		$req = "DELETE FROM destinataire WHERE destinataire.id = '$id'";
		PdoNewsletter::$monPdo->exec($req);
	}
	
	/**
	 * Modifie le nom d'un destinataire
	 *
	 * @param string $id
	 * @param string $nom
	 */
	public function majNomDestinataire($id, $nom){
		$req = "UPDATE destinataire SET nom = '$nom' WHERE id = $id";
		PdoNewsletter::$monPdo->exec($req);
	}
	
	/**
	 * Modifie l'email d'un destinataire
	 *
	 * @param string $id
	 * @param string $email
	 */
	public function majEmailDestinataire($id, $email){
		$req = "UPDATE destinataire SET email = '$email' WHERE id = $id";
		PdoNewsletter::$monPdo->exec($req);
	}
	
	/**
	 * Modifie la date de résiliation
	 *
	 * @param integer $id
	 */
	public function majResiliationlDestinataire($id){
		$req = "UPDATE destinataire SET resiliation = now() WHERE id = $id";
		PdoNewsletter::$monPdo->exec($req);
	}
	
	/**
	 * Crée un nouveau utilisateur
	 * à partir des informations fournies en paramètre
	 *
	 * @param string $login
	 * @param string $password
	 */
	public function creerNouveauUtilisateur($login,$password){
		$req = "INSERT INTO utilisateur
		values('','$login','$password','')";
		PdoNewsletter::$monPdo->exec($req);
	}
	
	/**
	 * Active l'utilisateur en paramètre
	 *
	 * @param string $email
	 */
	public function majActifUtilisateur($email){
		$req = "UPDATE utilisateur SET actif = 1 WHERE login = '$email'";
		PdoNewsletter::$monPdo->exec($req);
	}
	
	/**
	 * Retourne les informations d'un utilisateur à partir de son login (email) et mot de passe
	 *
	 * @param string $login
	 * @param string $password
	 * @return array l'id, le login, le password et l'activité 
	 * sous la forme d'un tableau associatif
	 */
	public function getInfosUtilisateur($login, $password) {
		$req = "SELECT utilisateur.id as id, utilisateur.login as login,
				utilisateur.password as password, utilisateur.actif as actif
				FROM utilisateur
				WHERE utilisateur.login = '$login'
				AND utilisateur.password = '$password'";
		$rs = PdoNewsletter::$monPdo->query($req);
		$ligne = $rs->fetch();
		$rs->closeCursor();
		return $ligne;
	}
	
	/**
	 * Retourne le mot de passe d'un utilisateur à partir de son login
	 *
	 * @param string $login
	 * @return le mot de passe de l'utilisateur cité en paramètre
	 */
	public function getMdpUtilisateur($login) {
		$req = "SELECT utilisateur.password as password
				FROM utilisateur
				WHERE utilisateur.login = '$login'";
		$rs = PdoNewsletter::$monPdo->query($req);
		$ligne = $rs->fetch();
		$rs->closeCursor();
		return $ligne['password'];
	}
	
	/**
	 * Retourne le nombre d'utilisateur qui porte le même email cité en paramètre
	 * 
	 * @param string $login
	 * @return integer le nombre d'utilisateur
	 */
	public function getNbUtilisateurMemeEmail($login) {
		$req = "SELECT COUNT(login) as nbUtilisateurLogin
				FROM utilisateur
				WHERE login = '$login'";
		$rs = PdoNewsletter::$monPdo->query($req);
		$ligne = $rs->fetch();
		$rs->closeCursor();
		return $ligne['nbUtilisateurLogin'];
	}
	
	
	/**
	 * Crée une nouvelle ligne d'historique de transmission
	 * à partir des informations fournies en paramètre
	 *
	 * @param string $resultat
	 * @param string $destinataire_id
	 * @param string $listediffusion_id
	 */
	public function creerNouvelleLigneHistoriqueTransmission($resultat,$destinataire_id,$listediffusion_id){
		$req = "INSERT INTO historique_transmission
		values('', now(),'$resultat','$destinataire_id','$listediffusion_id')";
		PdoNewsletter::$monPdo->exec($req);
	}
	
	/**
	 * Retourne sous forme d'un tableau associatif toutes les lignes de l'historique de transmission
	 * concernées par l'id du destinataire et l'id de la liste de diffusion
	 * 
	 * @param integer $destinataire_id
	 * @param integer $listediffusion_id
	 * @return array[] l'id destinataire, l'email, le nom et l'id de la liste de diffusion sous la forme d'un tableau associatif
	 */
	public function getLesHistoriquesTransmissionByDestinataireDiffusion($destinataire_id,$listediffusion_id){
		$req = "SELECT historique_transmission.id as id, historique_transmission.transmission_date as transmission_date,
		historique_transmission.resultat as resultat, historique_transmission.destinataire_id as destinataire_id,
		historique_transmission.destinataire_listediffusion_id as destinataire_listediffusion_id
		FROM historique_transmission
		WHERE historique_transmission.destinataire_id ='$destinataire_id'
		AND historique_transmission.destinataire_listediffusion_id = '$listediffusion_id'
		ORDER BY historique_transmission.transmission_date DESC";
		$res = PdoNewsletter::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		$res->closeCursor();
		return $lesLignes;
	}
	
	/**
	 * Supprime l'historique du destinataire faisant partie d'une liste de diffusion en paramètre
	 *
	 * @param integer $destinataire_id
	 * @param integer $listediffusion_id
	 */
	public function delHistoriqueDestinataire($destinataire_id,$listediffusion_id){
		$req = "DELETE FROM historique_transmission 
		WHERE historique_transmission.destinataire_id ='$destinataire_id'
		AND historique_transmission.destinataire_listediffusion_id = '$listediffusion_id'";
		PdoNewsletter::$monPdo->exec($req);
	}
	
	/**
	 * Retourne le nombre le nombre de destinataire de la liste de diffusion cité en paramètre
	 *
	 * @param string $listediffusion_id
	 * @return integer le nombre de destinataire
	 */
	public function getNbDestinataireListediffusion($listediffusion_id) {
		$req = "SELECT COUNT(listediffusion_id) as nb_destinataire_listediffusion
		FROM destinataire
		WHERE listediffusion_id = '$listediffusion_id'";
		$rs = PdoNewsletter::$monPdo->query($req);
		$ligne = $rs->fetch();
		$rs->closeCursor();
		return $ligne['nb_destinataire_listediffusion'];
	}
}
?>