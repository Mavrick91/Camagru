<?php
if (session_id() == "")
{
    session_start();
}
if(isset($_SESSION['id']) || !is_numeric($_SESSION['id']))
{
	foreach ($_SESSION as $key => $value) {
		unset($_SESSION[$key]);
	}
}
?>