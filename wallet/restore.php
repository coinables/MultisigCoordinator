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

$firstpub = strip_tags($_POST["firstpub"]);
$sanitizedpub = mysqli_real_escape_string($conn, $firstpub);

//find deriv acct
$findwallets = "SELECT * FROM `pubkeypool` WHERE `pubkey`='$sanitizedpub'";
$querywallets = mysqli_query($conn, $findwallets);
$fetchkeys = mysqli_num_rows($querywallets);
if($fetchkeys<1){
	die("none");
} else {
	$findwallets2 = "SELECT * FROM `pubkeypool` WHERE `pubkey`='$sanitizedpub'";
	$querywallets2 = mysqli_query($conn, $findwallets2);
	$fetchkeys2 = mysqli_fetch_assoc($querywallets2);
	$fetcheduid = $fetchkeys2["cuser"];
	echo $fetcheduid;
}


?>