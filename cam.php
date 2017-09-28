<?php
if (session_id() == "") {
    session_start();
}
include('config/config.php');
try {
    $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
    echo "Vous pouvez créer la database et les tables <a href='config/setup.php'>ici</a>";
}
?>
    <!DOCTYPE HTML>
    <HTML>
    <HEAD>
        <TITLE>Page Title</TITLE>
        <LINK rel="stylesheet" type="text/css" href="style.css">
    </HEAD>`
    <BODY>
    <?php include("menu.php");
    if (isset($_SESSION['ERROR'])) {
        echo htmlspecialchars($_SESSION['ERROR']);
        unset($_SESSION['ERROR']);
    }
    if (isset($_SESSION['SUCCES'])) {
        echo htmlspecialchars($_SESSION['SUCCES']);
        unset($_SESSION['SUCCES']);
    }
    if (!isset($_SESSION['id'])){
        echo "Connecte toi avant de continuer :(";
    }
    else
    {
    ?>
    <div class="cam-contener-flex">
        <div class="cam-contener-item" style="max-width: 640px;">
            <span id="nocam"></span>
            <div id="webcam">
                <img id="filtresurvideo" src="image/0.png">
                <video id="video" width="640" height="480" autoplay></video>
                <canvas id="canvas" width="640" height="480" src="" style="display: none;"></canvas>
                <br>
                <div class="two-button">
                    <button class="button" onclick="showUploadimage()">Upload Picture</button>
                    <button class="button" id="snap" disabled>Take Picture</button>
                </div>
            </div>
            <div id="uploadimage" style="display:none;">
                <form class="margin-bottom" action="#" method="post" enctype="multipart/form-data">
                    <input class="button" type="file" name="fileToUpload" id="fileToUpload">
                    <input class="button" type="submit" name="uploadimage" value="Upload Image"><br>
                    <div> Format autorisé : jpeg, jpg, gif, png, format conseillé : png, taille conseillé : 640*480
                    </div>
                    <br>
                </form>
                <img id="filtresurimg" src="image/0.png">
                <img id="image" width="640" height="480" src="<?php echo $_SESSION['img']; ?>">
                <br>
                <div class="two-button margin-bottom">
                    <button class="button" id="showwebcam" onclick="showWebcam()">Use Webcam</button>
                    <button class="button" id="Montage" style="display:none;"
                            onclick="senddata('<?php echo $_SESSION['img']; ?>','1')" disabled>
                        Take Picture
                    </button>
                </div>
            </div>
            <div class="filtre">
                <span id="filtre" value="0" style="display:none;"></span>
                <img src="image/1.png" width="100" onclick="changefilter(1)"><br/>
                <img src="image/2.png" width="100" onclick="changefilter(2)"><br/>
                <img src="image/3.png" width="100" onclick="changefilter(3)">
            </div>
        </div>
        <div class="cam-contener-item" id="cam-cadrephoto">
            <h1> Vos photos </h1>
            <hr>
            <?php
            $req = $pdo->prepare('SELECT data FROM image WHERE id_login = :idlogin');
            $req->execute(array(
                    'idlogin' => $_SESSION['id'])
            );
            $row = $req->fetchAll();
            if ($row) {
                foreach ($row as $array) {
                    ?>
                    <img id="image" width="128" height="96" src="<?php echo $array['data']; ?>">
                    <?php
                }
            } else {
                echo "Vous n'avez pas encore de photo :(";
            }
            ?>
        </div>
    </div>
    <?php
    if (isset($_POST['uploadimage'])) // Upload image
    {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }
        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            $data = file_get_contents($_FILES["fileToUpload"]["tmp_name"]);
            if ($imageFileType == "jpg")
                $base64 = 'data:image/jpg;base64,' . base64_encode($data);
            if ($imageFileType == "png")
                $base64 = 'data:image/png;base64,' . base64_encode($data);
            if ($imageFileType == "jpeg")
                $base64 = 'data:image/jpeg;base64,' . base64_encode($data);
            if ($imageFileType == "gif")
                $base64 = 'data:image/gif;base64,' . base64_encode($data);
            $_SESSION['img'] = $base64;
            ?>
            <script>
							var base64 = '<?PHP echo $base64;?>';
							document.getElementById("image").src = base64;
            </script>
            <?php

        }
    }
    if (isset($_POST['data']) && isset($_POST['filtre']) && isset($_POST['type']) && $_POST['data'] != "" && $_POST['filtre'] != "" && $_POST['type'] == "0") {
        header("Content-type: image/png");
        $replace = str_replace(" ", "+", $_POST['data']);
        $source = imagecreatefrompng($_POST['filtre']);
        $destination = imagecreatefrompng($replace);
        $largeur_source = imagesx($source);
        $hauteur_source = imagesy($source);
        $largeur_destination = imagesx($destination);
        $hauteur_destination = imagesy($destination);
        imagealphablending($source, 1);
        imagesavealpha($source, 1);
        imagecopy($destination, $source, 0, 0, 0, 0, $largeur_source, $hauteur_source);
        $path = "tmp/" . $_SESSION['id'] . ".png";
        imagepng($destination, $path, 9);
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/png;base64,' . base64_encode($data);
        unlink($path);
        $req = $pdo->prepare('INSERT INTO image (data, title ,id_login) VALUES(:data, :title, :id_login)');
        $req->execute(array(
            'data' => $base64,
            'title' => "test",
            'id_login' => $_SESSION['id']
        ));
    } else if (isset($_POST['data']) && isset($_POST['filtre']) && isset($_POST['type']) && $_POST['data'] != "" && $_POST['filtre'] != "" && $_POST['type'] == "1") {
        header("Content-type: image/png");
        $path = "tmp/" . $_SESSION['id'] . ".png";
        $replace = str_replace(" ", "+", $_POST['data']);
        preg_match('/(data:image\/([a-zA-Z]+);)/', $replace, $out);
        $_SESSION['error']['dest'] = $out;
        $source = imagecreatefrompng($_POST['filtre']);
        if (isset($out['2']) && $out['2'] == "png")
            $destination = imagecreatefrompng($replace);
        elseif (isset($out['2']) && $out['2'] == "jpg")
            $destination = imagecreatefromjpeg($replace);
        elseif (isset($out['2']) && $out['2'] == "jpeg")
            $destination = imagecreatefromjpeg($replace);
        elseif (isset($out['2']) && $out['2'] == "gif")
            $destination = imagecreatefromgif($replace);
        if (imagesx($destination) != 640 || imagesy($destination) != 480 || $out['2'] != "png") {
            $resize = imagecreatefrompng('image/fondneuf.png');
            imagecopyresampled($resize, $destination, 0, 0, 0, 0, 640, 480, imagesx($destination), imagesy($destination));
            imagepng($resize, $path, 9);
            $destination = imagecreatefrompng($path);
        }
        $largeur_source = imagesx($source);
        $hauteur_source = imagesy($source);
        $largeur_destination = imagesx($destination);
        $hauteur_destination = imagesy($destination);
        imagealphablending($source, 1);
        imagesavealpha($source, 1);
        imagecopyresampled($destination, $source, 0, 0, 0, 0, 640, 480, $largeur_source, $hauteur_source);
        imagepng($destination, $path, 9);
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/png;base64,' . base64_encode($data);
        unlink($path);
        $req = $pdo->prepare('INSERT INTO image (data, title ,id_login) VALUES(:data, :title, :id_login)');
        $req->execute(array(
            'data' => $base64,
            'title' => "test",
            'id_login' => $_SESSION['id']
        ));
    }
    ?>
    <FOOTER>
    </FOOTER>
    </BODY>
    </HTML>
    <SCRIPT>
			window.addEventListener("DOMContentLoaded", function () {
				var canvas = document.getElementById('canvas');
				var context = canvas.getContext('2d');
				var video = document.getElementById('video');
				var mediaConfig = { video: true };
				var errBack = function (e) {
					document.getElementById("nocam").innerHTML = "<i>Vous semblez ne pas avoir de webcam, ou celle-ci n'est pas reconnue, nous avons donc desactivé l'option webcam. </i>";
					showUploadimage();
					blockButton();
				};
				if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
					navigator.mediaDevices.getUserMedia(mediaConfig).then(function (stream) {
						video.src = window.URL.createObjectURL(stream);
						video.play();
					}, errBack);
				} else if (navigator.getUserMedia) { // Standard
					navigator.getUserMedia(mediaConfig, function (stream) {
						video.src = stream;
						video.play();
					}, errBack);
				} else if (navigator.webkitGetUserMedia) { // WebKit-prefixed
					navigator.webkitGetUserMedia(mediaConfig, function (stream) {
						video.src = window.URL.createObjectURL(stream);
						video.play();
					}, errBack);
				} else if (navigator.mozGetUserMedia) { // Mozilla-prefixed
					navigator.mozGetUserMedia(mediaConfig, function (stream) {
						video.src = window.URL.createObjectURL(stream);
						video.play();
					}, errBack);
				}

				// Trigger photo take
				document.getElementById('snap').addEventListener('click', function () {
					context.drawImage(video, 0, 0, 640, 480);
					canvas.getContext('2d').drawImage(video, 0, 0, 640, 480);
					canvas = document.getElementById("canvas");
					var data = canvas.toDataURL();
					canvas.setAttribute('src', data);
					senddata(data, '0');

				});
			}, false);

			function senddata(data, type) {
				xhttp = new XMLHttpRequest();
				xhttp.open("POST", "cam.php", true);
				if (type == "0")
					filtre = document.getElementById("filtresurvideo").src;
				else
					filtre = document.getElementById("filtresurimg").src;
				if (filtre != "http://localhost/camagru/image/0.png" && filtre != "http://localhost:8080/camagru/image/0.png")
				{
					xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					var datasend = "data=" + data + "&filtre=" + filtre + "&type=" + type;
					xhttp.send(datasend);
					window.location.reload();
				}
				else {
					alert("Vous devez selectionner un filtre !");
				}
			}

			function showimg(dataimg) {
				canvas = document.getElementById("canvas");
				var data = canvas.toDataURL();
				canvas.setAttribute('src', data);
			}

			function changefilter(data) {
				document.getElementById("filtresurvideo").src = "image/" + data + ".png";
				document.getElementById("filtresurimg").src = "image/" + data + ".png";
				document.getElementById("snap").disabled = false;
				document.getElementById("Montage").disabled = false;
			}

			function showUploadimage() {
				var img = document.getElementById('uploadimage');
				img.style.display = "";
				var webcam = document.getElementById('webcam');
				webcam.style.display = "none";
				var montage = document.getElementById('Montage');
				montage.style.display = "";
				var snap = document.getElementById('snap');
				snap.style.display = "none";
			}

			function showWebcam() {
				var img = document.getElementById('uploadimage');
				img.style.display = "none";
				var webcam = document.getElementById('webcam');
				webcam.style.display = "";
				var montage = document.getElementById('Montage');
				montage.style.display = "none";
				var snap = document.getElementById('snap');
				snap.style.display = "";
			}

			function blockButton() {
				var buttonchangewebcam = document.getElementById('showwebcam');
				buttonchangewebcam.style.display = "none";
			}
    </SCRIPT>
    <?php
    if (isset($_SESSION['img'])) {
        echo '<script>showUploadimage();</script>';
    }
}
?>