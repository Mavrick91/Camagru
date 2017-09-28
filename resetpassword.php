<?php 
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
if (isset($_POST['resetpassword'])){
	if (isset($_POST['resetpassword'])){

		$req = $pdo->prepare('SELECT email FROM account WHERE login = :login');
		$req->execute(array(
		'login' => $_POST['resetpassword']
		));
		$row = $req->fetch();
		if ($row){
			$key = hash("whirlpool",time());
			$req = $pdo->prepare('UPDATE account SET `key` = :key WHERE `email` = :email');
			$req->execute(array(
			'email' => $row['email'],
			'key' => $key
			));
			$to      = $row['email'];
			$subject = 'Confirmer votre mail !';
			$message = 'Cliquez sur ce lien pour reset votre mot de passe : <a href="'.$SITE_ADDR.'resetpassword.php?key='.$key.'&mail='.$to.'">ici</a>';
			$headers = 'From: baalexan@student.42.fr' . "\r\n" .
			'Content-type: text/html; charset=UTF-8' . "\r\n" .
			'Reply-To: baalexan@student.42.fr' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
			mail($to, $subject, $message, $headers);
			header("Location: index.php");
		}
		else
		{
			$_SESSION['error']['reset'] = "login incorrecte";
			header("Location: index.php");
		}
	}
	else{
		$_SESSION['error']['reset'] = "login incorrecte";
		header("Location: index.php");
	}
}
else if (isset($_GET['mail']) && isset($_GET['key'])){
	?>
	<form method="post" action="resetpassword.php" enctype="multipart/form-data" id="connexion">
        <input type="hidden" name="email" value="<?php echo $_GET['mail'];?>"/>
        <input type="hidden" name="key" value="<?php echo $_GET['key'];?>"/>
        <input type="password" name="password" placeholder="password"/>
        <input type="submit" value="Submit">
   	</form>
	<?php
}
else if (isset($_POST['email']) && isset($_POST['key']) && isset($_POST['password'])){
	$password = hash("whirlpool",$_POST['password']);
	$req = $pdo->prepare('SELECT email FROM account WHERE email = :email');
	$req->execute(array(
	'email' => $_POST['email']
	));
	$row = $req->fetch();
	if ($row){
		$req = $pdo->prepare('UPDATE account SET `password` = :password WHERE `email` = :email AND `key` = :key');
		$req->execute(array(
		'email' => $_POST['email'],
		'key' => $_POST['key'],
		'password' => $password
		));
		header("Location: index.php");
	}
	else{
		$_SESSION['error']['reset'] = "email incorrecte";
		header("Location: index.php");
	}
}
else{
	$_SESSION['error']['reset'] = "ERROR";
	header("Location: index.php");
}
?>