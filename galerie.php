<?php
if (session_id() == "")
{
    session_start();
}
include('config/config.php');
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
echo '<div id="spacer"></div>';
try
{
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
}
catch (Exception $e)
{
	die('Erreur : ' . $e->getMessage());
}
if(isset($_POST['delete']) && is_numeric($_POST['delete'])){
	$req = $pdo->prepare('DELETE FROM `image` WHERE id = :id');
		$req->execute(array(
			'id' => $_POST['delete']
		));
}
if(isset($_POST['liked']) && isset($_POST['idphoto']) && isset($_SESSION['id']))
{
	if($_POST['liked'] == "0")
	{
		$req = $pdo->prepare('INSERT INTO liked(id_login, id_photo) VALUES(:id_login, :id_photo)');
		$req->execute(array(
			'id_login' => $_SESSION['id'],
			'id_photo' => $_POST['idphoto']
		));
	}
	else
	{
		$req = $pdo->prepare('DELETE FROM `liked` WHERE id = :id_like');
		$req->execute(array(
			'id_like' => $_POST['idlike']
		));
	}
}
if(isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0)
	$id = $_GET['id'];
else
	$id = "10";
if (isset($_GET['imageid']) && is_numeric($_GET['imageid']) && $_GET['imageid'] > 0){
	$req = $pdo->prepare('SELECT * FROM image WHERE `id` = :id');
	$req->execute(array(
		'id' => $_GET['imageid']  
	));
}
else{
	$req = $pdo->prepare('SELECT * FROM image ORDER BY id DESC LIMIT 0,'.$id);
	$req->execute();
}
while ($row = $req->fetch())
{
	$reqlogin = $pdo->prepare('SELECT * FROM account WHERE id = :idlogin');
	$reqlogin->execute(array(
		'idlogin' => $row['id_login'])
	);
	$rowlogin = $reqlogin->fetch();
	echo '<div id="allphoto">';
	echo '<div id="login">';
	if (isset($_SESSION['id']) && $row['id_login'] == $_SESSION['id']){
	?>
		<img src="image/delete.png" id="deleteimg" width="15" onclick="<?php echo 'delimg('.$row['id'].')'; ?>">
	<?php 
	}
	echo $rowlogin['login'];
	echo "<BR />";
	echo '</div>';
	echo '<div id="photo">';
	?>
	<img src="<?php echo $row['data']; ?>" class="image" width="640" height="480">
	<?php
	echo '</div>';
	
	$req2 = $pdo->prepare('SELECT * FROM comment WHERE id_photo = :id_photo');
	$req2->execute(array(
		'id_photo' => $row['id'])
	);
	echo '<div id="commentblock">';
	while ($rowcomment = $req2->fetch())
	{
		$reqlogincomment = $pdo->prepare('SELECT * FROM account WHERE id = :idlogin');
		$reqlogincomment->execute(array(
		'idlogin' => $rowcomment['id_login'])
		);
		$rowlogincomment = $reqlogincomment->fetch();
		echo '<div id="comment">';
		echo $rowlogincomment['login']." : ";
		echo $rowcomment['comment'];
		echo "<BR />";
		echo "</div>";
	}
	echo "<hr />";
	echo "<div style='padding-top:10px;'></div>";
	$reqlike = $pdo->prepare('SELECT COUNT(id) FROM liked WHERE id_photo = :id_photo');
	$reqlike->execute(array(
			'id_photo' => $row['id']
	));
	$numberlike = $reqlike->fetchColumn();
	echo $numberlike." j'aime";
	if (isset($_SESSION['id'])){
		$reqlike = $pdo->prepare('SELECT * FROM liked WHERE id_photo = :id_photo AND id_login = :id_login');
		$reqlike->execute(array(
			'id_photo' => $row['id'],
			'id_login' => $_SESSION['id'])
		);
		$rowlike = $reqlike->fetch();
		if ($rowlike){
			$liked = "image/like.png";
			$like = 1;
			$idlike = $rowlike['id'];
		}
		else{
			$liked = "image/notlike.png";
			$like = 0;
			$idlike = 0;
		}?>
		<form method="post" action="addcomment.php" enctype="multipart/form-data" id="addcomment">
			<IMG SRC="<?php echo $liked; ?>" WIDTH="16px" HEIGHT="16px" onclick="<?php echo 'like('.$like.','.$row['id'].','.$idlike.')'; ?>">
		    <input type="text" name="comment" placeholder="Entrez votre commentaire ...">
		    <input type="hidden" name="photoid" value="<?php echo $row['id']; ?>">
		    <input type="hidden" name="loginid" value="<?php echo $_SESSION['id']; ?>">
		    <input type="submit" value="Submit">
    	</form>
    	<?php
	}
	else{
		$liked = "image/notlike.png";
		$like = 0;
		$idlike = 0;
		?>
		<form method="post" action="#" enctype="multipart/form-data" id="addcomment">
			<IMG SRC="<?php echo $liked; ?>" WIDTH="16px" HEIGHT="16px">
		    <input type="text" name="comment" placeholder="Connectez vous pour commenter...">
		    <input type="submit" value="Submit">
    	</form>
    	<?php
	}
	?>

	<?php
	echo "</div>";
	echo "</div>";
	echo '<div id="spacer"></div>';
}
$req = $pdo->prepare('SELECT COUNT(id) FROM image');
$req->execute();
$max = $req->fetchColumn();
echo "<div style='width:100%;'><div style='display: table;margin: 0 auto;'><button onclick='more(".$id.",".$max.")'>Plus d'image</button></div></div>";
?>
<FOOTER></FOOTER>
</BODY>
</HTML>
<SCRIPT>
function like(liked, idphoto, idlike){
	xhttp = new XMLHttpRequest();
    xhttp.open("POST", "galerie.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    var datasend = "liked="+liked+"&idphoto="+idphoto+"&idlike="+idlike;
    xhttp.send(datasend);
    location.reload();
}

function delimg(id){
	xhttp = new XMLHttpRequest();
    xhttp.open("POST", "galerie.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    var datasend = "delete="+id;
    xhttp.send(datasend);
    location.reload();
}
function more(nbr, maximg){
	if (nbr < maximg)
		nbr = nbr + 10;
	nbr.toString();
	document.location.href = "galerie.php?id="+nbr;
}

</SCRIPT>
<?php
?>
