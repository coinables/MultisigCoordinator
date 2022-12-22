<?php
error_reporting(E_ALL & ~E_NOTICE);
$conn = mysqli_connect("localhost", "root", "", "transactus");
	if (mysqli_connect_errno()){
	echo "Connection to DB failed" . mysqli_connect_error();
	}
	
session_start();
if($_SESSION["antibot"] !== "9H4zWNUx9yK4mEYt"){
	die("unauthorized request");
}	

$wid = $_POST["wid"];
$wid = strip_tags($wid);
$wid = mysqli_real_escape_string($conn, $wid);

$findwallet = "SELECT * FROM `wallet` WHERE `wlink`='$wid'";
$querywallet = mysqli_query($conn, $findwallet);
$fetchwallet = mysqli_fetch_assoc($querywallet);
$walletstatus = $fetchwallet["aactive"];

echo $walletstatus;


?>