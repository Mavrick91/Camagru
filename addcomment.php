<?php
if (session_id() == "")
{
    session_start();
}
include('config/config.php');
try
{
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
}
catch (Exception $e)
{
	die('Erreur : ' . $e->getMessage());
	$_SESSION['ERROR'] = "Vous pouvez créer la database et les tables <a href='config/setup.php'>ici</a>";
}
if (isset($_POST['comment']))
{
	if (isset($_POST['photoid']) && is_numeric($_POST['photoid']))
	{
		if (isset($_POST['loginid']) && is_numeric($_POST['loginid']) )
		{
			$req = $pdo->prepare('INSERT INTO comment(comment, id_photo, id_login) VALUES(:comment, :id_photo, :id_login)');
			$req->execute(array(
			'comment' => $_POST['comment'],
			'id_photo' => $_POST['photoid'],
			'id_login' => $_POST['loginid']
			));
			$req2 = $pdo->prepare('SELECT * FROM account, image WHERE account.id = image.id_login AND image.id = :id');
			$req2->execute(array(
				'id' => $_POST['photoid']
			));
			$row2 = $req2->fetch();
			if ($row2['id_login'] != $_SESSION['id']){
				$to      = $row2['email'];
				$subject = 'Un nouveau commentaire vous attends !';
				$message = 'Vous avez reçu un nouveau commentaire sur votre photo ! Connectez vous vite ! <br> <a href="'.$SITE_ADDR.'galerie.php?imageid='.$_POST['photoid'].'">Camagru</a>';
				$headers = 'From: baalexan@student.42.fr' . "\r\n" .
				'Content-type: text/html; charset=UTF-8' . "\r\n" .
				'Reply-To: baalexan@student.42.fr' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
				mail($to, $subject, $message, $headers);
			}
			$_SESSION['SUCCES'] = 'commentaire ajouté!';
			header("Location: galerie.php");
		}
	}
	else{
		$_SESSION['ERROR'] = 'L\'id de la photo est incorrect.';
	}
}
else{
	$_SESSION['ERROR'] = 'Vous devez entrer un commentaire.';
}
?>