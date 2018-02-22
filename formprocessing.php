<?php
session_start();
$uploadImg = $_FILES['uploadImg']['name'];


$uploaddir = 'uploads/img/'.$_SESSION['id'].'/';

if(!is_dir($uploaddir)){ // si le repertoire $uploaddir n'existe pas alors
	mkdir($uploaddir, 0777); // créer un repertoire
}

$serveruploaddir = 'http://trsrv.ddns.net/newsletter/';
$uploadfile = $uploaddir . basename($uploadImg);

//echo '<pre>';
move_uploaded_file($_FILES['uploadImg']['tmp_name'], $uploadfile);
// 	echo "Le fichier ".$uploadImg." est valide, et a été téléchargé
//            avec succés. Voici plus d'informations :\n";
// } else {
// 	echo "Attaque potentielle par téléchargement de fichiers.
//           Voici plus d'informations :\n";
// }

// echo 'Voici quelques informations de débogage :';
// print_r($_FILES);

// echo '</pre>';

// echo "<br/>Chemin complet = ".$uploadfile;

//version prod server
echo $serveruploaddir . $uploadfile;

//version dev local
//echo $uploadfile;

?>