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
?>
<!DOCTYPE HTML>
<HTML>
<HEAD>
    <TITLE>Page Title</TITLE>
    <LINK rel="stylesheet" type="text/css" href="style.css">
</HEAD>
<BODY>
    <?php include("menu.php");
	if (isset($_SESSION['ERROR']))
	{
		echo htmlspecialchars($_SESSION['ERROR']);
		unset($_SESSION['ERROR']);
	}
	if (isset($_SESSION['SUCCES']))
	{
		echo htmlspecialchars($_SESSION['SUCCES']);
		unset($_SESSION['SUCCES']);
	}
?>
<H1>Les 10 dernières images upload sur notre site !</H1>
<div class="index-flex"> 
	<?php 
	$req = $pdo->prepare('SELECT * FROM image ORDER BY id DESC LIMIT 0,10');
	$req->execute();
	while ($row = $req->fetch())
	{
		echo '<div class="index-case-item">';
		echo '<img src='.$row['data'].' style="width:240px;">';
		echo '</div>';
	}
	 ?>
</div>
<script type="text/javascript">

</script>
<FOOTER></FOOTER>
</BODY>
</HTML>