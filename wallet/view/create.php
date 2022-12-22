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
//table txs
//walletid, txlink, usrcreated, lasthash, nextsigned {default no}, mofn, finished {default no}, usrsigned

//post data: wlink: walletid, txlink: subhash, uid: myuid, lasthash: txhex, sigsrequired: mn, outaddr: oad, outamt: oa, chgaddr: cho, chgamt: ca

$outaddr = strip_tags($_POST["outaddr"]);
$sanitizedoutaddr = mysqli_real_escape_string($conn, $outaddr);
$outamt = intval($_POST["outamt"]);
$chgaddr = strip_tags($_POST["chgaddr"]);
$sanitizedchgaddr = mysqli_real_escape_string($conn, $chgaddr);
$chgamt = intval($_POST["chgamt"]);
$fromaddr = strip_tags($_POST["fromaddress"]);
$sanitaizedfrom = mysqli_real_escape_string($conn, $fromaddr);

$walletlink = strip_tags($_POST["wlink"]);
$sanitizedlink = mysqli_real_escape_string($conn, $walletlink);

//look up wallet link and find walletid
$findwallet = "SELECT * FROM `wallet` WHERE `wlink`='$sanitizedlink'";
$querywallet = mysqli_query($conn, $findwallet);
$fetchwallet = mysqli_fetch_assoc($querywallet);
$thiswalletid = $fetchwallet["wid"];

$thisuser = intval($_POST["uid"]);
$txlink = strip_tags($_POST["txlink"]);
$sanitizedtxlink = mysqli_real_escape_string($conn, $txlink);
$lasthash = strip_tags($_POST["lasthash"]);
if(ctype_xdigit($lasthash)){
	$sanitizedhash = $lasthash;
} else {
	die("invalid input");
}
$sigsrequired = intval($_POST["sigsrequired"]);

$maketx = "INSERT INTO `txs` (`walletid`,`txlink`,`usrcreated`,`mofn`,`lasthash`,`fromaddr`) VALUES('$thiswalletid','$sanitizedtxlink','$thisuser','$sigsrequired','$sanitizedhash','$sanitaizedfrom')";
$querytx = mysqli_query($conn, $maketx) or die("could not add tx");
//add to outputs table
//check if change
$lengthchngaddr = strlen($sanitizedchgaddr);
if($lengthchngaddr>20){
	//change address exists
	$makeouts = "INSERT INTO `outputs` (`txlink`,`oaddr`,`oamt`,`caddr`,`camt`) VALUES('$sanitizedtxlink','$sanitizedoutaddr','$outamt','$sanitizedchgaddr','$chgamt')";
} else {
	//no change
	$makeouts = "INSERT INTO `outputs` (`txlink`,`oaddr`,`oamt`) VALUES('$sanitizedtxlink','$sanitizedoutaddr','$outamt')";
}
$querymakeouts = mysqli_query($conn, $makeouts) or die("could not add outputs");

echo $querymakeouts ? "success" : "failed";
	
	
?>