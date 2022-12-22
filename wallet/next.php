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

$uid = intval($_POST["uid"]);

//find deriv acct
$findwallets = "SELECT * FROM `walletmembers` WHERE `cuser`='$uid'";
$querywallets = mysqli_query($conn, $findwallets);
$derivacct = mysqli_num_rows($querywallets);
$derivacct = $derivacct ? $derivacct : 0;

echo $derivacct;

?>