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
	echo "Vous pouvez créer la database et les tables <a href='config/setup.php'>ici</a>";
}
if (isset($_SESSION['id']) && is_int($_SESSION['id']))
{
	
	$_SESSION['ERROR'] = "Vous êtes déjà connectés";
}
else
{
	if (isset($_POST['login']) && ctype_alnum($_POST['login']) && strlen($_POST['login'] <= 24))
	{
		if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && strlen($_POST['email'] < 255))
		{
			if (isset($_POST['password']) && ctype_alnum($_POST['password']) && strlen($_POST['password'] <= 18))
			{
				if (isset($_POST['password_conf']) && ctype_alnum($_POST['password_conf']) && $_POST['password'] == $_POST['password_conf'])
				{
					$req = $pdo->prepare('SELECT id,login FROM account WHERE login = :login');
					$req->execute(array(
					'login' => $_POST['login']
					));
					$row = $req->fetch();
					if(!$row){
						$password = hash("whirlpool",$_POST['password']);
						$key = hash("whirlpool",time());
						$req = $pdo->prepare('INSERT INTO account(login, email, password, keyregister) VALUES(:login, :email, :password, :keyregister)');
						$req->execute(array(
						'login' => $_POST['login'],
						'email' => $_POST['email'],
						'password' => $password,
						'keyregister' => $key
						));
						$to      = $_POST['email'];
					    $subject = 'Confirmer votre mail !';
					    $message = 'Cliquez sur ce lien pour confirmer votre adresse mail : <a href="'.$SITE_ADDR.'validate.php?key='.$key.'&mail='.$to.'">ici</a>';
					    $headers = 'From: baalexan@student.42.fr' . "\r\n" .
					    'Content-type: text/html; charset=UTF-8' . "\r\n" .
					    'Reply-To: baalexan@student.42.fr' . "\r\n" .
					    'X-Mailer: PHP/' . phpversion();
					    mail($to, $subject, $message, $headers);
						$_SESSION['SUCCES'] = 'Inscrit!';
						header("Location: index.php");
					}
					else
					{
						echo 'Login déjà utilisé';
					}
				}
				else{
					echo 'Mot de passe non identique';
				}
			}
			else{
				echo 'Votre mot de passe doit seulement comporter des caractères alphanumérique [a-Z][0-9] et doit avoir une longueur maximum de 18 caractères.';
			}
		}
		else{
			echo 'Votre adresse email est incorrecte ou/et doit avoir une longueur maximum de 254 caractères.';
		}
	}
	else{
		echo 'Votre login doit seulement comporter des caractères alphanumérique [a-Z][0-9] et doit avoir une longueur maximum de 24 caractères.';
	}
}
echo '<a href="index.php"> retourner à l\'index</a>';
?>