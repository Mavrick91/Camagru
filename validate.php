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
if (isset($_SESSION['id']) && is_int($_SESSION['id']))
{
	$_SESSION['ERROR'] = "tu es déjà connecté";
}
else
{
	if (isset($_GET['key']) && ctype_alnum($_GET['key']) && isset($_GET['mail']) && filter_var($_GET['mail'], FILTER_VALIDATE_EMAIL))
	{
				$req = $pdo->prepare("SELECT * FROM account WHERE email = :email AND keyregister = :key AND validate = :validate");
				$req->execute(array(
				'email' => $_GET['mail'],
				'key' => $_GET['key'],
				'validate' => '0',
				));
				$row = $req->fetch();
				if(!$row){
					$_SESSION['ERROR'] = 'La clef ou le compte est incorrecte ou votre compte est déjà validé.';
				}
				else{
					$req = $pdo->prepare("UPDATE `account` SET validate = :validate WHERE email = :email");
					$req->execute(array(
					'validate' => "1",
					'email' => $_GET['mail']
					));
					$_SESSION['SUCCES'] = "Votre compte a été validé.";
				}
	}
	else{
		$_SESSION['ERROR'] = 'Une erreur est intervenue.';
	}
}
header("Location: index.php");
?>