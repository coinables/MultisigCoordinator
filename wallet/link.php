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

$m = intval($_POST["m"]);
$n = intval($_POST["n"]);
$uid = intval($_POST["uid"]);
$p1 = intval($_POST["p1"]);
$p2 = intval($_POST["p2"]);
$p3 = intval($_POST["p3"]);
$p4 = intval($_POST["p4"]);
$p5 = intval($_POST["p5"]);
$p6 = intval($_POST["p6"]);
$p7 = intval($_POST["p7"]);
$p8 = intval($_POST["p8"]);
$p9 = intval($_POST["p9"]);
$p10 = intval($_POST["p10"]);
$a1 = $_POST["a1"];
$a2 = $_POST["a2"];
$a3 = $_POST["a3"];
$a4 = $_POST["a4"];
$a5 = $_POST["a5"];
$a6 = $_POST["a6"];
$a7 = $_POST["a7"];
$a8 = $_POST["a8"];
$a9 = $_POST["a9"];
$a10 = $_POST["a10"];

$usraliases = [];

if($p1===$uid){
	array_push($usraliases, $a1);
}
if($p2===$uid){
	array_push($usraliases, $a2);
}
if($p3===$uid){
	array_push($usraliases, $a3);
}
if($p4===$uid){
	array_push($usraliases, $a4);
}
if($p5===$uid){
	array_push($usraliases, $a5);
}
if($p6===$uid){
	array_push($usraliases, $a6);
}
if($p7===$uid){
	array_push($usraliases, $a7);
}
if($p8===$uid){
	array_push($usraliases, $a8);
}
if($p9===$uid){
	array_push($usraliases, $a9);
}
if($p10===$uid){
	array_push($usraliases, $a10);
}




function getRandomBytes($nbBytes = 32)
{
    $bytes = openssl_random_pseudo_bytes($nbBytes, $strong);
    if (false !== $bytes && true === $strong) {
        return $bytes;
    }
    else {
        throw new \Exception("Unable to generate secure token from OpenSSL.");
    }
}

function generatePassword($length){
    return substr(preg_replace("/[^a-zA-Z0-9]/", "", base64_encode(getRandomBytes($length+1))),0,$length);
}

$wid = generatePassword(16);

$tryinsert = "INSERT INTO `wallet` (`mofn`,`nsigners`,`wlink`,`p1`,`p2`,`p3`,`p4`,`p5`,`p6`,`p7`,`p8`,`p9`,`p10`,`a1`,`a2`,`a3`,`a4`,`a5`,`a6`,`a7`,`a8`,`a9`,`a10`) VALUES('$m','$n','$wid','$p1','$p2','$p3','$p4','$p5','$p6','$p7','$p8','$p9','$p10','$a1','$a2','$a3','$a4','$a5','$a6','$a7','$a8','$a9','$a10')";
$querytry = mysqli_query($conn, $tryinsert) or die("oops error contact admin");

//find length of aliases
$aliaslength = count($usraliases);
$aliasarr = [$a1,$a2,$a3,$a4,$a5,$a6,$a7,$a8,$a9,$a10];
for($i=0;$i<$aliaslength;$i++){
	//find deriv acct
	$findwallets = "SELECT * FROM `walletmembers` WHERE `cuser`='$uid'";
	$querywallets = mysqli_query($conn, $findwallets);
	$derivacct = mysqli_num_rows($querywallets);
	$derivacct = $derivacct ? $derivacct : 0;
	//find wallet position
	
	$thisalias = $usraliases[$i];
	
	$thissigningposition = array_search($thisalias, $aliasarr);
	
	$trymembers = "INSERT INTO `walletmembers` (`walletid`,`cuser`,`ualias`,`derivacct`,`signingpos`) VALUES('$wid','$uid','$thisalias','$derivacct','$thissigningposition')";
	$querymembers = mysqli_query($conn, $trymembers);
	
	//grab 10 pubkeys from pool
	$thissigningpositionaddone = $thissigningposition +1;
	$pubkeystring = $_POST["pk".$thissigningpositionaddone];
	$explodekeys = explode(",",$pubkeystring);
	for($i=0;$i<10;$i++){
		//add to keypool table
		$keytoadd = $explodekeys[$i];
		$trypubkeyadd = "INSERT INTO `pubkeypool` (`pubkey`,`derivacct`,`derivindex`,`walletid`,`walletindex`,`pubkeyposition`,`cuser`) VALUES('$keytoadd','$derivacct','$i','$wid','$i','$thissigningposition','$uid')";
		mysqli_query($conn, $trypubkeyadd) or die("error adding public keys");
	}
	
	//check to see if this is final join needed to activate
	//check to see if this is final join needed to activate
	$countmembersjoined = "SELECT * FROM `wallet` WHERE `wlink`='$wid'";
	$querymembersjoined = mysqli_query($conn,$countmembersjoined);
	$fetchmembersjoined = mysqli_fetch_assoc($querymembersjoined);
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
	
	if($nummembersjoined==$nsigners){
		//all users joined activate wallet
		$tryactivate = "UPDATE `wallet` SET `aactive`='yes' WHERE `wlink`='$wid'";
		mysqli_query($conn, $tryactivate);
		//wallet active send to view
		header("Location: view/?w=".$wid);
		exit();
	}
	
}

echo $wid;

?>