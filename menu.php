<?php
if (session_id() == "") {
    session_start();
}
?>
<div class="header-contener-flex">
    <div class="header-item-flex" id="title">
        <h1>Camagru</h1>
    </div>
    <div class="header-item-flex">
        <div id="menu_horizontal">
            <a href="index.php">Accueil</a>
            <a href="cam.php">Prendre une photo</a>
            <a href="galerie.php">Galerie</a>
            <?php
            if (isset($_SESSION['id']) && is_int($_SESSION['id']) == TRUE) {
                echo '<a href="#" onclick="deconnexion()">Deconnexion</a>';
            } else {
                ?>
                <a href="#" onclick="showConnexion()">Connexion</a>
                <a href="#" onclick="showInscription()">Inscription</a>
                <?php
            } ?>
        </div>
    </div>
</div>
<div id="menu" id="display:none;">
    <!--/*    CONNECTION    */-->
    <div class="wrapper-form">
        <div id="connexion" style="display:none;">
            <form class="form" method="post" action="connexion.php" enctype="multipart/form-data">
                <input type="text" name="login" placeholder="login"/>
                <input type="password" name="password" placeholder="password"/>
                <input class="submit" type="submit" value="Submit">
            </form>
            <button class="submit" onclick="showReset()">reset password</button>
        </div>
    </div>
    <!--/*    RESET PASSWORD    */-->
    <div class="wrapper-form">
        <div id="reset" style="display:none;">
            <form class="form"  method="post" action="resetpassword.php" enctype="multipart/form-data" style="display: flex;flex-direction: column">
                <input type="text" name="resetpassword" placeholder="login"/>
                <input class="submit" type="submit" value="Submit">
            </form>
            <button class="submit" onclick="showConnexion()">login</button>
        </div>
    </div>
    <!--/*    INSCRIPTION    */-->
    <div class="wrapper-form">
        <form method="post" action="inscription.php" enctype="multipart/form-data" style="display:none;"
              id="inscription">
            <div class="form">
            <input type="text" name="login" placeholder="login"/>
            <input type="email" name="email" placeholder="email"/>
            <input type="password" name="password" placeholder="password"/>
            <input type="password" name="password_conf" placeholder="password"/>
            <input class="submit" type="submit" value="Submit">
            </div>
        </form>
    </div>
</div>
<script>
	function deconnexion() {
		xhttp = new XMLHttpRequest();
		xhttp.open("POST", "deconnexion.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send();
		location.reload();
	}

	function showConnexion() {
		if (document.getElementById("connexion").style.display == "none") {
			document.getElementById("connexion").style.display = "inline";
			document.getElementById("inscription").style.display = "none";
			document.getElementById("reset").style.display = "none";
		}
		else {
			document.getElementById("connexion").style.display = "none";
			document.getElementById("inscription").style.display = "none";
			document.getElementById("reset").style.display = "none";
		}
	}

	function showReset() {
		if (document.getElementById("connexion").style.display == "inline") {
			document.getElementById("connexion").style.display = "none";
			document.getElementById("inscription").style.display = "none";
			document.getElementById("reset").style.display = "inline";
		}
	}

	function showInscription() {
		if (document.getElementById("inscription").style.display == "none") {
			document.getElementById("connexion").style.display = "none";
			document.getElementById("inscription").style.display = "inline";
			document.getElementById("reset").style.display = "none";
		}
		else {
			document.getElementById("connexion").style.display = "none";
			document.getElementById("inscription").style.display = "none";
			document.getElementById("reset").style.display = "none";
		}
	}
</script>