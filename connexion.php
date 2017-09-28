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
	if (isset($_POST['login']) && ctype_alnum($_POST['login']))
	{
			if (isset($_POST['password']) && ctype_alnum($_POST['password']))
			{
				$password = hash("whirlpool",$_POST['password']);
				$req = $pdo->prepare('SELECT * FROM account WHERE login = :login AND password = :password');
				$req->execute(array(
				'login' => $_POST['login'],
				'password' => $password
				));
				$row = $req->fetch();
				if(!$row){
					$_SESSION['ERROR'] = 'Mauvais login ou mot de passe';
				}
				else{
					$req = $pdo->prepare("SELECT * FROM account WHERE login = :login AND validate = '1'");
					$req->execute(array(
					'login' => $_POST['login']
					));
					$row = $req->fetch();
					if(!$row){
						$_SESSION['ERROR'] = 'Vous devez valider votre compte pour vous connecter.';
					}
					else{
						$_SESSION['id'] = intval($row['id']);
						$_SESSION['login'] = $row['login'];
					}

				}
			}
			else{
				$_SESSION['ERROR'] = 'Votre mot de passe doit seulement comporter des caractères alphanumérique [a-Z][0-9] et doit avoir une longueur maximum de 18 caractères.';
			}
	}
	else{
		$_SESSION['ERROR'] = 'Votre login doit seulement comporter des caractères alphanumérique [a-Z][0-9] et doit avoir une longueur maximum de 24 caractères.';
	}
}
header("Location: cam.php");