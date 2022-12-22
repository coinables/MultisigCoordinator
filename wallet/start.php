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

$ip = $_SERVER['REMOTE_ADDR'];

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

$pw = generatePassword(16);


$start = "INSERT INTO `cusers` (`ppw`,`ipp`) VALUES ('$pw','$ip')";
$dostart = mysqli_query($conn, $start) or die("error");


if($dostart){
	$finduser = "SELECT * FROM `cusers` WHERE `ppw`='$pw'";
	$dofinduser = mysqli_query($conn, $finduser);
	$fetchuser = mysqli_fetch_assoc($dofinduser);
	$thisuserid = $fetchuser["idd"];
	echo $thisuserid;
} else {
	echo "error";
}

?>