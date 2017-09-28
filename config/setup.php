<?php
include("config.php");
?>
<?php
try
{
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
}
catch (Exception $e)
{
	$pdo_create_db = 0;
	echo "Connexion échoué, création de la base de données<BR />";
	$pdo = new PDO($DB_DEBUG, $DB_USER, $DB_PASSWORD);
	$requete = "CREATE DATABASE IF NOT EXISTS `".$DB_NAME."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
	$pdo->prepare($requete)->execute();
}
if (!file_exists("../tmp")){
	mkdir("../tmp", 0700);
}
if (isset($pdo_create_db))
{
	try
	{
		$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
		echo "Nouvelle tentative de connexion à la base de données réussi. <BR />";
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
}
if (isset($_POST['password']) && $_POST['password'] == "create")
{
	$pdo->exec("DROP TABLE `account`");
	$pdo->exec("DROP TABLE `image`");
	$pdo->exec("DROP TABLE `comment`");
	$pdo->exec("DROP TABLE `like`");
	echo "Les tables ont été supprimé <BR />";
	$req = $pdo->exec("CREATE TABLE IF NOT EXISTS `account` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `login` VARCHAR(24) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `validate` int(1) NOT NULL DEFAULT '0',
    `keyregister` VARCHAR(128) NOT NULL,
    `password` VARCHAR(129) NOT NULL)"
	);
	$req = $pdo->exec("CREATE TABLE IF NOT EXISTS `image` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `data` 	LONGBLOB NOT NULL,
    `title` VARCHAR(64) NOT NULL,
    `id_login` INT NOT NULL)"
	);
	$req = $pdo->exec("CREATE TABLE IF NOT EXISTS `comment` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `comment` text NOT NULL,
    `id_photo` INT NOT NULL,
    `id_login` INT NOT NULL)"
	);
	$req = $pdo->exec("CREATE TABLE IF NOT EXISTS `liked` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `id_photo` INT NOT NULL,
    `id_login` INT NOT NULL)"
	);
	echo "Création des tables de la base de données.<BR />";
}
if (isset($_POST['password']) && $_POST['password'] == "reset")
{
	$pdo->exec("DROP TABLE `account`");
	$pdo->exec("DROP TABLE `image`");
	$pdo->exec("DROP TABLE `comment`");
	$pdo->exec("DROP TABLE `liked`");
	echo "Les tables ont été supprimé <BR />";
}
?>
<script> 
function deleteTable(){
	xhttp = new XMLHttpRequest();
	xhttp.open("POST", "setup.php", true);
  	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  	xhttp.send("password=reset");
}
function createTable(){
	xhttp = new XMLHttpRequest();
	xhttp.open("POST", "setup.php", true);
  	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  	xhttp.send("password=create");
}
</script>
<button onclick="deleteTable()"> Delete table </button>
<button onclick="createTable()"> Recréer/créer les tables</button>
<BR />
Aller à l'index du site : <a href="../index.php">index.php</a>
<?php
?>