<?php
error_reporting(E_ALL & ~E_NOTICE);
$conn = mysqli_connect("localhost", "root", "", "transactus");
	if (mysqli_connect_errno()){
	echo "Connection to DB failed" . mysqli_connect_error();
	}
session_start();
$_SESSION["antibot"] = "9H4zWNUx9yK4mEYt";	

$userip = $_SERVER['REMOTE_ADDR'];

$walletlinkexists = $_GET["w"];

if(strlen($walletlinkexists)===16){
	$striplink = strip_tags($walletlinkexists);
	$sanitizedlink = mysqli_real_escape_string($conn, $striplink);
	//check if matching walletid exists
	$searchlink = "SELECT * FROM `wallet` WHERE `wlink`='$sanitizedlink'";
	$querylink = mysqli_query($conn, $searchlink);
	$numlink = mysqli_num_rows($querylink);
	if($numlink>0){
		//walletid found! Fetch details (m of n, aliases etc)
		$searchlink2 = "SELECT * FROM `wallet` WHERE `wlink`='$sanitizedlink'";
		$querylink2 = mysqli_query($conn, $searchlink2);
		$fetchlink = mysqli_fetch_assoc($querylink2);
		$mofn = $fetchlink["mofn"];
		$numsigners = $fetchlink["nsigners"];
		$isactive = $fetchlink["aactive"];
		//check if wallet is already activated
		if($isactive=="no"){
			//not active send back to join
			header("Location: ../join/?w=".$sanitizedlink);
			exit();
		}
		
		//fetch signing positions
		//fetch public keys
		$masterpubkeyarray = [];
		$numsinplusone = $numsigners+1;
		for($i=1;$i<$numsinplusone;$i++){
			$thisuserid = $fetchlink["p".$i];
			//find this user id and walletlink in pubkeypool
			for($loopi=0;$loopi<10;$loopi++){
				$findpubkeys = "SELECT * FROM `pubkeypool` WHERE `walletid`='$sanitizedlink' AND `cuser`='$thisuserid' AND `walletindex`='$loopi'";
				$querypubkeys = mysqli_query($conn, $findpubkeys);
				$fetchpubkeys = mysqli_fetch_assoc($querypubkeys);
				
				array_push($masterpubkeyarray, $fetchpubkeys["pubkey"]);
			}
			
			
			
		}
		
		$pubkeyjson = json_encode($masterpubkeyarray);
		//if 3 signers first addr will be array position 0,10 & 20
		//             second addr will be array position 1,11 & 21, etc
		
		
		
	} else {
		//could not find matching wallet id
		die("invalid wallet link");
	}
	
} else {
	//wallet link is  invalid
	//could not find matching wallet id
		die("invalid wallet link");
}


$randominput = mt_rand(10000,99999);
$randominput2 = uniqid();
$mixedinput = $randominput.$randominput2;
$hashit = hash("sha256",$mixedinput);
$subhash = substr($hashit,0,32);



?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
<link rel="stylesheet" href="bootstrap.min.css">
<script src="buidl.js?tx=1"></script>
<script type="text/javascript" src="bitcoin.js"></script>
<script type="text/javascript" src="buffer.js"></script>
<script src="jquery-3.2.1.min.js"></script>
<script src="qrcode.js"></script>
<style>
body, html {
	padding-left: 12px;
	font-family: "Verdana", "sans-serif";
}
#recQR{
	width: 350px;
	margin: auto;
	left: 40px;
	position: relative;
}
#backupblock{
	display: none;
}
#sendblock{
	display: none;
}
.subnum{
	font-size: 2rem;
	color: #aaa;
}
.eachword{
	display: inline-block;
	margin: 10px;
	padding: 4px;
	border: 1px solid #e1e1e1;
}
.input {color:#3cf281;border-color:#3cf281;background-color: transparent; border-radius: 100%;}
.input:hover{color:#fff;background-color:#3cf281;border-color:#3cf281}
.green { background-color: #00cc00; }
#amtsats{
	background-color: #ccc;
}
.wideinput{
	width: 500px;
}
.addrEach{
	display: inline-block;
	position: relative;
	margin: 2px;
	border: 1px solid #ffc107;
	border-radius: 5px;
	padding: 7px;
	cursor: pointer;
}
.addrEach:hover{
	border: 1px solid #ffea07;
	cursor: pointer;
}
#tx{
    display: none;
}
.mb-3{
	position: relative;
	margin: auto;
}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary" style="max-width: 98%;">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"><b>Transact</b>us</a>
    

    <div class="navbar w-100 order-1 order-md-0" id="navbarColor01">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <a class="nav-link active" href="#" id="receivelink" onclick="return showReceive();">Receive</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" id="sendlink" onclick="return showSend();">Send</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" id="backuplink" onclick="return showBackUp();">Back-Up</a>
        </li>
        
      </ul>
      <form class="d-flex">
        <input class="form-control me-sm-2" type="text" value="0.00000000 BTC" id="walletbal" readonly>
        <button class="btn btn-secondary my-2 my-sm-0" id="fiatout">$0.00</button>
      </form>
    </div>
  </div>
</nav>
<br>
<center><h2 class="card-title" style="color: #fff;">Transactus Makes <span id="ms">Multisig</span> with Anyone Easy!</h2></center>
<!-- Receive block visible by default -->
<div class="card border-primary mb-3" style="max-width: 100rem;" id="receiveblock" style="text-align: center;">
 
  
  <div class="card-body" style="text-align: center;">
   <div style="width: 100%;">
	<div id="qrcode" style="width: 500px; position: relative; margin: auto; left: 140px;"></div>
   </div>
	<br>
	<h4 id="addrout"></h4>
	<label style="font-size: 18px;">Signatures Required to Spend: </label><input type="number" class="maininput" value="<?php echo $mofn;?>" style="width: 48px; font-size: 20px;" readonly> 
	
	<input type="hidden" id="sigpos1" value=""><input type="hidden" id="sigpos2" value=""><input type="hidden" id="sigpos3" value=""><input type="hidden" id="sigpos4" value=""><input type="hidden" id="sigpos5" value=""><input type="hidden" id="sigpos6" value=""><input type="hidden" id="sigpos7" value=""><input type="hidden" id="sigpos8" value=""><input type="hidden" id="sigpos9" value=""><input type="hidden" id="sigpos10" value=""><input type="hidden" id="pubk1" value=""><input type="hidden" id="pubk2" value=""><input type="hidden" id="pubk3" value=""><input type="hidden" id="pubk4" value=""><input type="hidden" id="pubk5" value=""><input type="hidden" id="pubk6" value=""><input type="hidden" id="pubk7" value=""><input type="hidden" id="pubk8" value=""><input type="hidden" id="pubk9" value=""><input type="hidden" id="pubk10" value="">
	<div class="input_fields_wrap">
<?php
	
	for($i=0;$i<$numsigners;$i++){
		$plusone = $i+1;
		$thistaken = $fetchlink["p".$plusone]; //0 means space available
		$thistaken = intval($thistaken);
		
		$disableswitch = $thistaken !== 0 ? 'readonly' : '';
		$buttonswitch = $thistaken !== 0 ? 'Position Already Full' : 'Make Me Signer #'.$plusone;
		$scriptswitch = $thistaken !== 0 ? 'null' : 'this';
		
		$thisaliasout = $fetchlink["a".$plusone];
		
		echo '<div><label>Signer #'.$plusone.': </label><input type="text" class="maininput" id="sigalias'.$i.'" placeholder="Name/Alias (optional)" value="'.$thisaliasout.'" '.$disableswitch.'></div>';
		 
	}
		
?>
    </div>
	<br><div id="joindiv"><button type="button" class="btn btn-outline-primary" onclick="return newAddr();">Get Next Address</button>
		  <br>Address <span id="countOut">0</span>/9 </div>
	
	
	<br>
	<div id="statusout"></div>
  </div>
  <div style="width: 60%; margin: auto; font-size: 18px; text-align: left;">
	<p>Each wallet contains 10 addresses.</p>
	<p>Use the "Get Next Address" button to cycle through your 10 wallet addresses.</p>
	<p>If you need more than 10 addresses, initiate a new wallet.</p>
	<p>If you want to change signers or signing requirements, initiate a new wallet.</p>
	</div>
</div>
<!-- Back Up block invisible by default -->
<div class="card border-primary mb-3" style="max-width: 100rem;" id="backupblock">
  
  <div class="card-body" style="text-align: center;">
    <h4 class="card-title">Mnemonic Back Up</h4>
    <p class="card-text">Write down these words to back up your wallet. We use the BIP49 derivation path.</p>
	<button type="button" class="btn btn-outline-primary" onclick="return showWords();">Reveal Words</button>
	<button type="button" class="btn btn-outline-danger" onclick='return confirm("Are you sure this will erase your existing wallet. BE SURE TO SAVE YOUR MNEMONIC WORDS BEFORE ATTEMPTING TO IMPORT/RECOVER!")?importWallet():null;'>Import/Recover</button>
	<div id="mnemonicOut" class="form-group"></div>
	Below is how your mnemonic is used to generate a public key included in a multisig. <br> Using a standard BIP49 derivation path m/49/0/0/0, the path typically used for change instead will be used <br>as an account and will increment up by one for every new wallet where you are a participant. <br> Each wallet currently allows 12 addresses max, the index paths used are in ascending order 0-11.
	<br><br><pre style="position: relative; width:700px; margin: auto; text-align: left;">
	let seedhex = buidl.mnemonic2SeedHex(fw);
	let ac = process.env.NEXT_USER_ACCT;
	let keypair = buidl.fromHDSeed(seedhex.seedHex,49,0,ac,0); //1st keypair in chain
	let pubkey = buidl.getDetails(keypair.pk).publicKey; //1st pubkey in chain
	
	//second pubkey in chain
	let keypair = buidl.fromHDSeed(seedhex.seedHex,49,0,ac,1); //2nd keypair in chain
	//and so on for all 12 keys
	</pre>
	<br><br>
	12 key pairs are collected from each wallet participant and their public keys are input into a multisig for each address in respective order (ie all users will be using 2 for the index derivtion path on the 3rd keypair, and 3 for the 4th key pair, etc). The change path (Accounts) are derived in ascending order starting from 0 based on the individual, the account may be the same or different as other users in the same wallet.
  </div>
</div>
<!-- Send block invisible by default -->
<div class="card border-primary mb-3" style="max-width: 100rem;" id="sendblock">
  
  <div class="card-body" style="text-align: center;">
    <h4 class="card-title">Send</h4>
    <p class="card-text">Choose which coins to spend.</p>
	<div id="addr_container"></div>
	<br>
	<div id="utxobuttons"></div>
	<div id="tx">
	<div class="form-group" style="width: 100%;">
	<table align="center"><tr><td width="75%"><input type="text" class="form-control" id="outputaddr" placeholder="SEND TO ADDRESS" required></td><td width="25%"><input type="text" id="outamtinput" class="form-control" onchange="return minerCalc();" placeholder="AMOUNT"></td></tr></table></div>
	<div class="form-group" style="width: 100%;">
	<table align="center"><tr><td width="75%"><input type="text" class="form-control" id="changeout" placeholder="CHANGE ADDRESS"></td><td width="25%"><input type="text" class="form-control" id="changeamt" onchange="return minerCalc();"  placeholder="AMOUNT"></td></tr></table></div>
	<table align="center"><tr><td width="75%">SATS PER BYTE</td><td width="25%"><input type="text" class="form-control" id="spb" value="6" readonly></td></tr><tr><td width="75%">TOTAL MINERS FEE</td><td width="25%"><input type="text" class="form-control" id="minerfee" value="1000" readonly></td></tr><tr><td></td><td><button type="button" class="btn btn-outline-secondary" onclick="return sendtx();">Sign Transaction</button></td></tr></table><br>
	
	<div id="linkdiv" style="display: none;"><br>
		<div id="statusoutsign"></div>
	    <h4>Transaction Link: </h4>
		<br><div style="width: 500px; margin: auto;"><input style="position: relative; float: right;" type="text" class="form-control" style="font-size: 28px;" id="txname" value="localhost/transactus/tx/?t=<?php echo $subhash; ?>" readonly> <button class="btn btn-outline-success" onclick='return null'>Copy Link</button></div><br>
		
		<br><b>Share this transaction link with other members of your wallet to obtain the necessary number of signatures.<br>After all necessary signatures have been obtained the transaction will be automatically broadcast.</b>
		</div><br><br>
		
	
	<input type="hidden" id="utxocount">
	
	</div>
	<input type="hidden" id="addrbal"><input type="hidden" id="addrsel">
	
	<textarea id="inputdata" cols="100" rows="1" style="opacity: 0;"></textarea>
	<textarea id="utxosout" cols="100" rows="10" style="opacity: 0;"></textarea>
	<textarea id="hexout" style="opacity: 0;"></textarea>
	</div>
<br>
  </div>
<!--
<h2>MultiSig Models to Ponder:</h2>


<h3>Single Leader Model</h3> <p>(One higher-level signer only needs one other person to spend, but the other signers can't spend without the higher level signing)<br><br>

CEO (holds 3 keys)<br>
Associate (holds 1 key)<br>
Associate (holds 1 key)<br>
Associate (holds 1 key)<br>
<em>(4 required to spend means the associates cannot spend without the CEO signing and the CEO can spend with just one other associate)</em></p>

<h3>Two Leader Model</h3> <p>(Two Higher Level Signers only need one other person to send, the two lower level signers require two other people. This model prevents an Associate sending without a higher level approving.)<br><br>
CEO (holds 2 keys)<br>
CFO (holds 2 keys)<br>
Associate (holds 1 key)<br>
Associate (holds 1 key)<br>
<em>(3 required to spend)</em></p>

<h3>Three Tiered Leader Model</h3> <p>(Three tiered, highest level only needs one other person to spend, mid-high level can spend with two lower level signers or another mid-high level signer.<br><br>
CEO (holds 3 keys)<br>
CFO (holds 2 keys)<br>
CTO (holds 2 keys)<br>
Associate (holds 1 key)<br>
Associate (holds 1 key)<br>
Associate (holds 1 key)<br>
<em>(4 required to spend allows CEO to only need one other person higher or lower level while all others require at least a higher level and two associates or two higher levels on all spends)</em></p><br>
-->
<script>

function loadWallet(){
	console.log("Welcome back loading wallet...");
	return localStorage.getItem("fastWallet");
	
}


//retreive localstorage item labeled fastWallet
var fw = localStorage.getItem("fastWallet");

//check if retreival is null, if it is run createNewWallet function
fw === null ? createNewWallet() : loadWallet(); 

//get JSON of all pubkeys from each participant
let inputPubKeyArray = <?php echo $pubkeyjson; ?>;
console.log(inputPubKeyArray);

//you can reverse calc for number of signers by dividing by 10, as each participant provides 10 pubkeys for each wallet
let numsigners = inputPubKeyArray.length/10;
numsigners = parseFloat(numsigners);
let neededToSpend = <?php echo $mofn; ?>;

//generate all 10 multisig addresses using the public keys

let addressesPool = []; //this array will hold the resulting addresses
let redeemScriptPool = [];

// loop through 10 iterations to generate 10 multisigs
for(i=0;i<10;i++){
	//get 0,10,20 for the first address if 3 total signers, or 1,11,21,31,41 if 2nd addr with 5 signers.
	let slicedPubKeys = []; //this array will be used as the input for each multisig address
	var imod = i;
	for(ii=0;ii<numsigners;ii++){
		slicedPubKeys.push(inputPubKeyArray[imod]);
		imod = parseFloat(imod) + 10;
	}
	
	var newpair = buidl.multisigRedeem(slicedPubKeys,neededToSpend);
	addressesPool.push(newpair["addr"]);
	redeemScriptPool.push(newpair["redeemScript"]);
}


var addrCounter = 0;
//console.dir(newpair);
//blank out any existing qr code
document.getElementById("qrcode").innerHTML = "";
//create new qrcode with segwit address
new QRCode(document.getElementById("qrcode"), addressesPool[addrCounter]);
document.getElementById("addrout").innerHTML = addressesPool[addrCounter];

function newAddr(){
	if(addrCounter >= 9){
		addrCounter = 0;
	} else {
	    addrCounter++;
	}
	$("#qrcode").fadeOut(200);
	$("#addrout").fadeOut(200,function(){
		var newaddress = addressesPool[addrCounter];
		$("#qrcode").html("");
		new QRCode(document.getElementById("qrcode"), newaddress);
		$("#addrout").html(newaddress);
		$("#addrout").fadeIn(100);
		$("#qrcode").fadeIn(100);
		$("#countOut").html(addrCounter);
		
		//console.log(addrCounter);
	});
}


function showReceive(){
	$("#backupblock").fadeOut(200, function(){
		$("#sendblock").fadeOut(1);
		$("#receiveblock").fadeIn(100);
		$("#receivelink").addClass("active");
		$("#backuplink").removeClass("active");
		$("#sendlink").removeClass("active");
	});
}

function showSend(){
	$("#receiveblock").fadeOut(200, function(){
		$("#backupblock").fadeOut(1);
		$("#sendblock").fadeIn(100);
		$("#sendlink").addClass("active");
		$("#receivelink").removeClass("active");
		$("#backuplink").removeClass("active");
	});
}

function showBackUp(){
	$("#receiveblock").fadeOut(200, function(){
	    $("#sendblock").fadeOut(1);
		$("#backupblock").fadeIn(100);
		$("#backuplink").addClass("active");
		$("#receivelink").removeClass("active");
		$("#sendlink").removeClass("active");
	});
}

function showWords(){
    $("#mnemonicOut").html("");
	let splitwords = fw.split(" ");
	let wordcounter = 1;
	for(var i = 0; i<splitwords.length; i++){
		$("#mnemonicOut").append('<span class="eachword"><span class="subnum">'+wordcounter+'</span> '+splitwords[i]+'</span>');
		wordcounter++;
	}
	
}

function importWallet(){
	console.log("import wallet");
	$("#mnemonicOut").html('<br><input type="text" class="form-control" id="mnemonicInput" placeholder="ENTER YOUR 12 WORD MNEMONIC BACK UP SEPARATED BY A SPACE"><button class="btn btn-outline-secondary" onclick="return doImport();">Import</button><br><br>Alternatively, you can erase your current wallet and create a new one.<br><button class="btn btn-outline-danger" onclick="return doDelete();">Erase Wallet</button>');
}

function doImport(){
    var recoveryIn = $("#mnemonicInput").val();
	
	window.localStorage.setItem("fastWallet", recoveryIn); 
    fw = recoveryIn;
	//calc pub key to assign existing user
	let importedhex = buidl.mnemonic2SeedHex(fw);
	let importpair = buidl.fromHDSeed(importedhex.seedHex,49,0,0,0);
	let importedpubkey = buidl.getDetails(importpair.pk).publicKey;
	
	$.ajax({
			 type: "POST",
			 url: '../restore.php',
			 data: {firstpub: importedpubkey},
			 success: function(pubcallback){
				
				if(pubcallback=="none"){
					$("#mnemonicOut").html("Mnemonic phrase imported. Refreshing...");
					$("#mnemonicOut").fadeOut(2000,function(){
						location.reload();
					});
				} else {
					//set txuid
					window.localStorage.setItem("txuid", parseFloat(pubcallback));
					$("#mnemonicOut").html("Mnemonic phrase imported. Refreshing...");
					$("#mnemonicOut").fadeOut(2000,function(){
						location.reload();
					});
				}
				
				 
			 },
			 error: function(err){
				console.error(err);
			 }
	});
	
	
	
}

function doDelete(){
	window.localStorage.removeItem("fastWallet");
	$("#mnemonicOut").html("Wallet deleted. Refreshing...");
	$("#mnemonicOut").fadeOut(2000,function(){
		location.reload();
	});
}

function pubkeypoolcreate(phrase, acct){
	var getuid = localStorage.getItem("txuid");
	
	let seedhex = buidl.mnemonic2SeedHex(phrase);
	let pubkeyarr = [];
	
		for(i=0;i<10;i++){
			let keypair = buidl.fromHDSeed(seedhex.seedHex,49,0,acct,i);
			let pubkey = buidl.getDetails(keypair.pk).publicKey;
			pubkeyarr.push(pubkey);
		}
	
	var pubkeyload = pubkeyarr.join();
	return pubkeyload;
}


$(document).ready(function(){
		
	addrstring = "";
		
	addrstring = addressesPool.join("|");

	recdarr = [];
			
	$.ajax({
		async: true,
		type: "GET",
		url: "https://blockchain.info/multiaddr?active="+addrstring,
		success: function(result) {
			
		 console.log(result);
		 console.log(addrstring);
		 dataout = result.wallet.final_balance;
		 var fullbtcs = dataout/100000000;
		 fullbtcs.toFixed(8);
		 
		 $("#walletbal").val(fullbtcs+" BTC");	 
		 
			for(var i=0;i<10;i++){
				var addrbalance = result.addresses[i].final_balance;
				var convaddrbalance = addrbalance/100000000;
				convaddrbalance.toFixed(8);
				var addrballessfee = addrbalance - 1000;
				
				if(addrbalance > 0){
					recdarr.push(result.addresses[i].address);
					 $("#addr_container").append('<div class="addrEach" id="'+result.addresses[i].address+'"><span class="addr">'+result.addresses[i].address+'</span><br>'+addrbalance+' SATS</div>');
			    }
		    }
			
			var addrObjs = document.getElementsByClassName("addrEach"); 

			for(i=0;i<addrObjs.length;i++){
				var addrBlock = addrObjs[i];
				addrBlock.onclick = function(){
					
					this.style.backgroundColor = "#ffc107";
					var loopaddress = this.id;
					$("#fromaddress").val(loopaddress);
					var walletIndexPosition = addressesPool.indexOf(this.id);
										
					  var data
					  $.ajax({
						async: true,
						type: "GET",
						url: "https://api.blockchair.com/bitcoin/dashboards/address/"+loopaddress,
						success: function(res) {
						 //console.log(res.data);
						 console.log(res.data[loopaddress].utxo.length + " utxos found");
						 var addrbal = res.data[loopaddress].address.balance;
						 $("#utxocount").val(res.data[loopaddress].utxo.length);
						 //default fee 6 sats per byte
						 var spb = $("#spb").val();
						 var basebytes = 124;
						 var inputbytes = res.data[loopaddress].utxo.length * 68;
						 var calctotalbytes = (inputbytes + basebytes);
						 var calctotalfee = calctotalbytes * spb;
						 $("#minerfee").val(calctotalfee);
						 $("#addrbal").val(addrbal);
						 $("#addrsel").val(loopaddress);
						 $("#outamtinput").val(addrbal-calctotalfee);
						 data = res.data[loopaddress].utxo.map(function(item){
							return {
							  "txid": item.transaction_hash,
							  "vout": item.index,
							  "satoshis": item.value
							}
						  });
						  console.dir(data);
						  //dump utxos to hidden textbox
						  $("#utxosout").val(JSON.stringify(data));
						  $("#tx").fadeIn(100);
						  
						}
					});
				}
			}
		}

	});
});

function minerCalc(){
	const wb = $("#addrbal").val();
	const oa = $("#outamtinput").val();
	const ca = $("#changeamt").val();
	let mf = wb - oa - ca;
	$("#minerfee").val(mf);
	
	
	 var basebytes = 124;
	 var inputbytes = $("#utxocount").val() * 68;
	 var calctotalbytes = (inputbytes + basebytes);
	 var solveforrate = mf / calctotalbytes;
	 var roundsolvefor = solveforrate.toFixed(0);
	 $("#spb").val(roundsolvefor);
	
	if(mf > 9999){
		alert("Your miner fee is very high. Be sure to use a change address if you aren't spending the full amount.");
	}
}

function sendtx(){
	
	let myuid = localStorage.getItem("txuid");
	//fw -> get pk for m/49/0/acct/walletpos
	let walletid = "<?php echo $sanitizedlink; ?>";
	var thisAcct
	
		$.ajax({
			 type: "POST",
			 url: 'acct.php',
			 data: {wid: walletid, uid: myuid},
			 success: function(data){
				document.getElementById("linkdiv").style.display = "block";
				 				 
				thisAcct = JSON.parse(data);
				console.log(thisAcct);
				 
				const derivacctlength = thisAcct.length
				//console.log("deriv length "+derivacctlength)
				const fromaddr = $("#addrsel").val(); 				
				const wb = $("#addrbal").val();
				const oa = $("#outamtinput").val();
				const ca = $("#changeamt").val();
				const oad = $("#outputaddr").val();
				if(oad.length < 20){
					alert("You must input a bitcoin address");
				} else {
				
					const cho = $("#changeout").val();
					//oad, oa, cho, ca
					var mf = wb - oa - ca;
					var savedUTXOS = $("#utxosout").val();
					var inputjson = JSON.parse(savedUTXOS);
					var inputlen = inputjson.length;
					var totalSatoshis = 0;
					
					//loop through all keys if user has more than one key in the wallet
					//for(var mi=0;mi<derivacctlength;mi++){
						
						
						var walletIndexPosition = addressesPool.indexOf(fromaddr);
						let seedhex = buidl.mnemonic2SeedHex(fw);
						let keypair = buidl.fromHDSeed(seedhex.seedHex,49,0,thisAcct[0],walletIndexPosition);
						
						var NETWORK = b.bitcoin.networks.bitcoin;
						let signingkey = b.bitcoin.ECPair.fromWIF(keypair.pk, NETWORK);
						
						var txb = new b.bitcoin.TransactionBuilder(NETWORK)
						
						//need redeemscript for adding input
						var thisRedeemScript = redeemScriptPool[walletIndexPosition];
						var thisRedeemScriptBuffer = buffer.bufferFrom(thisRedeemScript);
						var scriptPubkey = b.bitcoin.script.scriptHash.output.encode(b.bitcoin.crypto.hash160(buffer.bufferFrom(thisRedeemScript)));
																	
						for(var ii=0;ii<inputlen;ii++){
							txb.addInput(inputjson[ii].txid,
										inputjson[ii].vout,
										null,
										scriptPubkey)
							totalSatoshis = parseFloat(totalSatoshis) + parseFloat(inputjson[ii].satoshis);	
						}
						
						//add output
						var toaddress = oad;
						var toaddressamt = oa;
						toaddressamt = parseFloat(toaddressamt);
						var changeaddress = cho;
						var changeaddressamt = ca;
						changeaddressamt = parseFloat(changeaddressamt);
						txb.addOutput(toaddress,toaddressamt);
						if(changeaddressamt.length<1){
						} else {
							if(changeaddress.length<1){
							} else {
								txb.addOutput(changeaddress,changeaddressamt);
							}	
						}
						//sign each input
						
						//access localstorage -> deriv path {acct} -> wallet position
						
						
						for(var iii=0;iii<inputlen;iii++){
							var inputvalue = inputjson[iii].satoshis;
							txb.sign(iii, signingkey, thisRedeemScriptBuffer);
						}
						
						
					//} //end multiple deriv acct loop
							
										
					var tx = txb.buildIncomplete();
					var txhex = tx.toHex();
					let mn = <?php echo $mofn; ?>;
					
					let remainsig = parseFloat(mn) - 1;
					let subhash = "<?php echo $subhash; ?>";
					//create.php
					//walletid, txlink, usrcreated, lasthash, nextsigned {default no}, mofn, finished {default no}, usrsigned
					//outputs table
					//oad, oa, cho, ca
					$.ajax({
						 type: "POST",
						 url: 'create.php',
						 data: {wlink: walletid, fromaddress: fromaddr, txlink: subhash, uid: myuid, lasthash: txhex, sigsrequired: mn, outaddr: oad, outamt: oa, chgaddr: cho, chgamt: ca},
						 success: function(resp){
							 console.log(resp);
						 }
					});									
					
					$("#hexout").val(txhex);
					$("#statusoutsign").html("<h3>Transaction signed. "+remainsig+" Signatures remaining in order to spend.</h3>");
					console.log(txhex);
				}// end if else output length check
				 
			}
		}); 
	
}

</script>
<script>
var ws = new WebSocket("wss://api-pub.bitfinex.com/ws/2");
ws.onopen = function(){
  ws.send(JSON.stringify({"event":"subscribe", "channel":"ticker", "pair":"BTCUSD"}))
};
ws.onmessage = function(msg){
	
  var response = JSON.parse(msg.data);
  var hb = response[1];
  
  if(hb !== "hb"){
	var pricenow = response[1][0];
	var balnow = document.getElementById("walletbal").value;
	balnow = balnow.slice(0,-4);
	var usdbal = balnow * pricenow;
	usdbal = usdbal.toFixed(2);
	$("#fiatout").html("$" + usdbal);
  }
};
</script>
</body>
</html>