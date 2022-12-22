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

$walletlink = strip_tags($_POST["wid"]);
$sanitizedlink = mysqli_real_escape_string($conn, $walletlink);
$thisuser = intval($_POST["uid"]);


//find account deriv path used for this user
	$countmembersjoined = "SELECT * FROM `pubkeypool` WHERE `walletid`='$sanitizedlink' AND `cuser`='$thisuser' GROUP BY `derivacct`";
	$querymembersjoined = mysqli_query($conn,$countmembersjoined);
	$derivacctarray = [];
	while($fetchmembersjoined = mysqli_fetch_assoc($querymembersjoined)){
		$thisderiv = $fetchmembersjoined["derivacct"];
		array_push($derivacctarray, $thisderiv);
	}
	
	
	echo json_encode($derivacctarray);
	
	
?>