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

$walletid = strip_tags($_POST["wid"]);
$sanitizedlink = mysqli_real_escape_string($conn, $walletid);

//check to see if this is final join needed to activate
	$countmembersjoined = "SELECT * FROM `wallet` WHERE `wlink`='$sanitizedlink'";
	$querymembersjoined = mysqli_query($conn,$countmembersjoined);
	$fetchmembersjoined = mysqli_fetch_assoc($querymembersjoined);
	$nsigners = $fetchmembersjoined["nsigners"];
	$existmembersarray = [];
	$fp1 = $fetchmembersjoined["p1"];
	if($fp1>0){
		array_push($existmembersarray,$fp1);
	} 
	$fp2 = $fetchmembersjoined["p2"];
	if($fp2>0){
		array_push($existmembersarray,$fp2);
	} 
	$fp3 = $fetchmembersjoined["p3"];
	if($fp3>0){
		array_push($existmembersarray,$fp3);
	} 
	$fp4 = $fetchmembersjoined["p4"];
	if($fp4>0){
		array_push($existmembersarray,$fp4);
	} 
	$fp5 = $fetchmembersjoined["p5"];
	if($fp5>0){
		array_push($existmembersarray,$fp5);
	} 
	$fp6 = $fetchmembersjoined["p6"];
	if($fp6>0){
		array_push($existmembersarray,$fp6);
	} 
	$fp7 = $fetchmembersjoined["p7"];
	if($fp7>0){
		array_push($existmembersarray,$fp7);
	} 
	$fp8 = $fetchmembersjoined["p8"];
	if($fp8>0){
		array_push($existmembersarray,$fp8);
	} 
	$fp9 = $fetchmembersjoined["p9"];
	if($fp9>0){
		array_push($existmembersarray,$fp9);
	} 
	$fp10 = $fetchmembersjoined["p10"];
	if($fp10>0){
		array_push($existmembersarray,$fp10);
	} 
	
	$nummembersjoined = count($existmembersarray);
	echo $nummembersjoined;
	
	if($nummembersjoined==$nsigners){
		//all users joined activate wallet
		$tryactivate = "UPDATE `wallet` SET `aactive`='yes' WHERE `wlink`='$sanitizedlink'";
		mysqli_query($conn, $tryactivate);
		
	}


?>