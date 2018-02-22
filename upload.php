<?php

require_once('inc/lib.fct.php');

// $nom = $_POST['nom'];                    //On r�cup�re le pseudo et on le stocke dans une variable
// $message = $_POST['message'];            //On fait de même avec le message
// $ligne = $nom.' > '.$message.'<br>';     //Le message est créé 
// $leFichier = file('3.3-ac.htm');         //On lit le fichier ac.htm et on stocke la r�ponse dans une variable (de type tableau)
// array_unshift($leFichier, $ligne);       //On ajoute le texte calcul� dans la ligne pr�c�dente au d�but du tableau
// file_put_contents('3.3-ac.htm', $leFichier); //On �crit le contenu du tableau $leFichier dans le fichier ac.htm


$target_dir = "uploads/img/"; // specifies the directory where the file is going to be placed
$target_file = $target_dir . basename($_FILES["file"]["name"]); // specifies the path of the file to be uploaded
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION); // holds the file extension of the file

// Check if image file is a actual image or fake image
if(isset($_POST["file"])) {
	$check = getimagesize($_FILES["file"]["tmp_name"]);
	if($check !== false) {
		ajouterInfo("File is an image - " . $check["mime"] . ".");
		$uploadOk = 1;
	} else {
		ajouterErreur("File is not an image.");
		$uploadOk = 0;
	}
}
// Check if file already exists
if (file_exists($target_file)) {
	ajouterErreur("Sorry, file already exists.");
	$uploadOk = 0;
}
// Check file size
if ($_FILES["file"]["size"] > 500000) {
	ajouterErreur("Sorry, your file is too large.");
	$uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
			ajouterErreur("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
			$uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
	ajouterErreur("Sorry, your file was not uploaded.");
	// if everything is ok, try to upload file
} else {
	if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
		ajouterInfo("The file ". basename( $_FILES["file"]["name"]). " has been uploaded.");
	} else {
		ajouterErreur("Sorry, there was an error uploading your file.");
	}
}

// affichage des infos et des erreurs dans une fen�tre d'alerte
if(isset($_REQUEST['info']) || isset($_REQUEST['erreur']) ) {
	//echo "<div id=\"alertMain\">";
	 
	if (isset($_REQUEST['info'])) {
		echo  "
	<div id=\"alertInfo\" class =\"alert alert-info\">
		<div class=\"controls\">
		";
		foreach ($_REQUEST['info'] as $info){
			echo $info."</br>";
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
			echo $erreur."</br>";
		}
		echo "
	  	</div>
	</div>
  		";

	}

	//echo "</div>";
}

?>