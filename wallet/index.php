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
		//walletid found!
		$searchlink2 = "SELECT * FROM `wallet` WHERE `wlink`='$sanitizedlink'";
		$querylink2 = mysqli_query($conn, $searchlink2);
		$fetchlink = mysqli_fetch_assoc($querylink2);
		$mofn = $fetchlink["mofn"];
		$numsigners = $fetchlink["nsigners"];
		$isactive = $fetchlink["aactive"];
		//check if wallet is already activated
		$walletactive = false;
		
		
		
		if($isactive==="yes"){
			$walletactive = true;
		}
		if($walletactive){
			//wallet active send to view
			header("Location: view/?w=".$walletlinkexists);
			exit();
		} else {
			//members still joining send to join
			header("Location: join/?w=".$walletlinkexists);
		}
	} else {
		//could not find matching wallet id
		die("invalid wallet link");
	}
	
	
	//redirect to VIEW
	//redirect to JOIN
} else{
	//wallet link is  invalid
	//could not find matching wallet id
		//die("invalid wallet link");
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
<link rel="stylesheet" href="bootstrap.min.css">
<script src="buidl.js"></script>
<script type="text/javascript" src="bitcoin.js"></script>
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
   
	<br>
	<h3>MultiSig in no time!</h3> <h4>Create the general wallet schema below, grab a spot as a signer and generate a link to let your friends join!</h4>
	<br>
	<div class="alert alert-dismissible alert-danger" id="backupwarn" style="display: none;">
	  
	  <h4>BackUp Notice:</h4><br> <span style="font-size:18px;">Your personal mnemonic back-up phrase has been instantly created and is accessible from the Back-Up tab. Although you are not required to back-up to use the wallet; <b>it is recommended that you write down your mnemonic at your earliest convenience.</b><br><br>There is no registration, or password. Your mnemonic is your account and site identity. You can restore your account from any device using the import feature in the back-up tab.</span><br><br>
	  <b><a href="#" onclick="document.getElementById('backupwarn').style.display='none';return false;" class="close" data-dismiss="alert">DISMISS THIS MESSAGE</a></b> 
	</div>
		<br>
	<br><br>
	
	
	<label style="font-size: 18px;">Signatures Required to Spend: </label><input type="number" id="nreq" name="nreq" class="maininput" value="" style="width: 48px; font-size: 20px;" required>
	<br><br>
	<input type="hidden" id="sigpos1" value=""><input type="hidden" id="sigpos2" value=""><input type="hidden" id="sigpos3" value=""><input type="hidden" id="sigpos4" value=""><input type="hidden" id="sigpos5" value=""><input type="hidden" id="sigpos6" value=""><input type="hidden" id="sigpos7" value=""><input type="hidden" id="sigpos8" value=""><input type="hidden" id="sigpos9" value=""><input type="hidden" id="sigpos10" value=""><input type="hidden" id="pubk1" value=""><input type="hidden" id="pubk2" value=""><input type="hidden" id="pubk3" value=""><input type="hidden" id="pubk4" value=""><input type="hidden" id="pubk5" value=""><input type="hidden" id="pubk6" value=""><input type="hidden" id="pubk7" value=""><input type="hidden" id="pubk8" value=""><input type="hidden" id="pubk9" value=""><input type="hidden" id="pubk10" value="">
	<div class="input_fields_wrap">
	<div><label>Signer #1: </label><input type="text" class="maininput mi2" placeholder="Name/Alias (optional)" id="sigalias" name="sig"> <button class="maininput mi2 btn btn-outline-warning" onclick="makeSigner(this, 1);">Make Me Signer #1</button> &nbsp; <a href="#wrapcont" class="maininput mi2 remove_field">Remove</a></div>	
		
    </div>
	<br><button type="button" class="maininput mi2 add_field_button btn btn-outline-primary">Add A Participant/Signer</button> <button class="maininput btn btn-outline-success" onclick='return genlink();'>Generate Join Link</button>
	
	<br><br>
	
	<div id="linkdiv" style="display: none;"><br>
	  <h3>Wallet Link: </h3>
<br><div style="width: 500px; margin: auto;"><input style="position: relative; float: right;" type="text" class="form-control" style="font-size: 28px;" id="walletname" value="" required> <button class="btn btn-outline-success" onclick='return null'>Copy Link</button></div>
<br><br><b>Share this wallet link with friends to let them join.<br>After all spaces have been filled this link can also be used to view and access the wallet. <Br> Anyone with the link can join(assuming spaces are still available).</b>
	</div><br><br>
	<div id="statusout"></div>
  </div>
  <div style="width: 60%; margin: auto; font-size: 18px; text-align: left;">
	<p>For every signer you want in your group wallet create a space for them as a participant by clicking "Add A Participant/Signer".</p>
	<p>If you want to be one of the participants click on one(or more) of the "Make Me Signer#_" buttons.</p>
	<p>Aliases are random, can be changed by yourself or anyone you invite(unless someone else already took the space) or omitted entirely.</p>
	<p>Set the number of required signers to spend.</p>
	<p>Once satisifed with the basic wallet structure click "Generate Join Link". Share this link with who you would like to join (anyone with the link can join).</p>
	<p>Once all users join the wallet will become active and users can start generating addresses and spend.</p>
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
	let seedhex = buidl.mnemonic2SeedHex(MNEMONIC_PHRASE);
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
	<table align="center"><tr><td width="75%">SATS PER BYTE</td><td width="25%"><input type="text" class="form-control" id="spb" value="6" readonly></td></tr><tr><td width="75%">TOTAL MINERS FEE</td><td width="25%"><input type="text" class="form-control" id="minerfee" value="1000" readonly></td></tr><tr><td></td><td><button type="button" class="btn btn-outline-secondary" onclick="return sendtx();">Send Transaction</button></td></tr></table><br>
	<input type="hidden" id="utxocount">
	<div id="hexout"></div>
	</div>
	<input type="hidden" id="addrbal"><input type="hidden" id="addrsel">
	
	<textarea id="inputdata" cols="100" rows="1" style="opacity: 0;"></textarea>
	
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
function createNewWallet(){ 
 let newWords = buidl.newMnemonic();
 let justWords = newWords.words;
 let userIP = "<?php echo $userip; ?>";
 
 window.localStorage.setItem("fastWallet", justWords); 
	 $.ajax({
	 type: "POST",
	 url: 'start.php',
	 data: {ip: userIP},
	 success: function(data){
		if(data==isNaN){
			console.log("error");
		} else {
			console.log(data);
			window.localStorage.setItem("txuid", data);
			document.getElementById('backupwarn').style.display='block';
		}
	 },
	 error: function(xhr, status, error){
		console.error(xhr);
	 }
	});
 fw = justWords;
 console.log("No Wallet Found, New Wallet Created");
 
}

function loadWallet(){
	console.log("Welcome back loading wallet...");
	return localStorage.getItem("fastWallet");
	
}

function genlink(){
		
	var inputm = document.getElementById("nreq").value;
	var inputn = document.getElementsByClassName("btn-outline-warning").length;
	
	pps = [];
	aas = [];
	pki = [];
	aas[0] = document.getElementById("sigalias").value;
	
	for(ii=1;ii<inputn;ii++){
		aas[ii] = document.getElementById("sigalias"+ii).value;
	}
	for(iii=1;iii<(inputn+1);iii++){
		pps[iii] = document.getElementById("sigpos"+iii).value;
		pki[iii] = document.getElementById("pubk"+iii).value;
	}
	
	
	if(inputm<1){
		alert("You must enter number of signers required");
		document.getElementById("nreq").focus();
	} else {
		var nummaininputs = document.getElementsByClassName("maininput").length;
		var getuid = localStorage.getItem("txuid");
		for(i=0;i<nummaininputs;i++){
			document.getElementsByClassName("maininput")[i].disabled=true;
		}
		document.getElementById("linkdiv").style.display = "block";
		
		 $.ajax({
			 type: "POST",
			 url: 'link.php',
			 data: {m: inputm, n: inputn, uid: getuid, p1: pps[1], p2: pps[2], p3: pps[3], p4: pps[4], p5: pps[5], p6: pps[6], p7: pps[7], p8: pps[8], p9: pps[9], p10: pps[10], a1: aas[0], a2: aas[1], a3: aas[2], a4: aas[3], a5: aas[4], a6: aas[5], a7: aas[6], a8: aas[7], a9: aas[8], a10: aas[9], pk1: pki[1], pk2: pki[2], pk3: pki[3], pk4: pki[4], pk5: pki[5], pk6: pki[6], pk7: pki[7], pk8: pki[8], pk9: pki[9], pk10: pki[10]},
			 success: function(data){
				
					console.log(data);
					document.getElementById("walletname").value = "localhost/transactus/?w="+data;
					document.getElementById("walletname").focus();
					runchecker(data);
			 },
			 error: function(xhr, status, error){
				console.error(xhr);
			 }
			});
	}
	
	
}

function runchecker(walletid){
	$.ajax({
			 type: "POST",
			 url: 'check.php',
			 data: {wid: walletid},
			 success: function(data){
				
										
			 },
			 error: function(xhr, status, error){
				console.error(xhr);
			 }
			});
}

//retreive localstorage item labeled fastWallet
var fw = localStorage.getItem("fastWallet");

//check if retreival is null, if it is run createNewWallet function
fw === null ? createNewWallet() : loadWallet(); 



$(document).ready(function() {
	document.getElementById("sigalias").value = pickone(colors)+pickone(animals)+Math.floor(Math.random() * 999);
	
    var wrapper         = $(".input_fields_wrap"); 
    var add_button      = $(".add_field_button"); 
    
    var x = 0; //initlal text box count
    $(add_button).on("click",function(e){ //on add input button click
        e.preventDefault();
        if(x < 9){ //max input box allowed
            x++; //text box increment
            console.log(x);
			let genUsrName = pickone(colors)+pickone(animals)+Math.floor(Math.random() * 999);
            $(wrapper).append('<div><label>Signer #'+(x+1)+': </label><input type="text" class="maininput mi2" placeholder="Name/Alias (optional)" value="'+genUsrName+'" id="sigalias'+x+'">  <button  class="maininput mi2 btn btn-outline-warning" onclick="makeSigner(this, '+(x+1)+');">Make Me Signer #'+(x+1)+'</button> &nbsp; <a href="#wrapcont" class="maininput mi2 remove_field">Remove</a></div>'); //add input box
        }
    });
    
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
		if(confirm("Are you sure you want to remove?")){
			e.preventDefault(); $(this).parent('div').remove(); x--;
			
		} else {
			//do nothing
		}
    })
	
	
});

globalcounter = 0;
function makeSigner(elem, nn){
	let myuid = localStorage.getItem("txuid");
	var nummi2 = document.getElementsByClassName("mi2").length;
		
		for(i=0;i<nummi2;i++){
			document.getElementsByClassName("mi2")[i].disabled=true;
		}
		
		$.ajax({
			 type: "POST",
			 url: 'next.php',
			 data: {uid: myuid},
			 success: function(data){
				
					console.log(data);
					let fetcheddata = data;
					let nextacct = parseFloat(fetcheddata) + parseFloat(globalcounter);
		
					let seedhex = buidl.mnemonic2SeedHex(fw);
					let pubkeystring = pubkeypoolcreate(fw,nextacct);
					
					elem.disabled = true;
					document.getElementById("statusout").innerHTML += '<span style="font-size: 18px; color: '+colorCodes[nn-1]+'">You have added your Public Keys (m/49&#39;/0&#39;/0&#39;/'+nextacct+'/0) as Signer #'+nn+'.</br><input type="text" value="'+pubkeystring+'" class="pubkeyin" id="pubkeystring'+nn+'" readonly></br>';
					document.getElementById("pubk"+nn).value = pubkeystring;
					document.getElementById("sigpos"+nn).value = myuid;
					globalcounter++;
			 },
			 error: function(xhr, status, error){
				console.error(xhr);
			 }
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
			 url: 'restore.php',
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

var city_names = ["Aberdeen", "Abilene", "Akron", "Albany", "Albuquerque", "Alexandria", "Allentown", "Amarillo", "Anaheim", "Anchorage", "Ann Arbor", "Antioch", "Apple Valley", "Appleton", "Arlington", "Arvada", "Asheville", "Athens", "Atlanta", "Atlantic City", "Augusta", "Aurora", "Austin", "Bakersfield", "Baltimore", "Barnstable", "Baton Rouge", "Beaumont", "Bel Air", "Bellevue", "Berkeley", "Bethlehem", "Billings", "Birmingham", "Bloomington", "Boise", "Boise City", "Bonita Springs", "Boston", "Boulder", "Bradenton", "Bremerton", "Bridgeport", "Brighton", "Brownsville", "Bryan", "Buffalo", "Burbank", "Burlington", "Cambridge", "Canton", "Cape Coral", "Carrollton", "Cary", "Cathedral City", "Cedar Rapids", "Champaign", "Chandler", "Charleston", "Charlotte", "Chattanooga", "Chesapeake", "Chicago", "Chula Vista", "Cincinnati", "Clarke County", "Clarksville", "Clearwater", "Cleveland", "College Station", "Colorado Springs", "Columbia", "Columbus", "Concord", "Coral Springs", "Corona", "Corpus Christi", "Costa Mesa", "Dallas", "Daly City", "Danbury", "Davenport", "Davidson County", "Dayton", "Daytona Beach", "Deltona", "Denton", "Denver", "Des Moines", "Detroit", "Downey", "Duluth", "Durham", "El Monte", "El Paso", "Elizabeth", "Elk Grove", "Elkhart", "Erie", "Escondido", "Eugene", "Evansville", "Fairfield", "Fargo", "Fayetteville", "Fitchburg", "Flint", "Fontana", "Fort Collins", "Fort Lauderdale", "Fort Smith", "Fort Walton Beach", "Fort Wayne", "Fort Worth", "Frederick", "Fremont", "Fresno", "Fullerton", "Gainesville", "Garden Grove", "Garland", "Gastonia", "Gilbert", "Glendale", "Grand Prairie", "Grand Rapids", "Grayslake", "Green Bay", "GreenBay", "Greensboro", "Greenville", "Gulfport-Biloxi", "Hagerstown", "Hampton", "Harlingen", "Harrisburg", "Hartford", "Havre de Grace", "Hayward", "Hemet", "Henderson", "Hesperia", "Hialeah", "Hickory", "High Point", "Hollywood", "Honolulu", "Houma", "Houston", "Howell", "Huntington", "Huntington Beach", "Huntsville", "Independence", "Indianapolis", "Inglewood", "Irvine", "Irving", "Jackson", "Jacksonville", "Jefferson", "Jersey City", "Johnson City", "Joliet", "Kailua", "Kalamazoo", "Kaneohe", "Kansas City", "Kennewick", "Kenosha", "Killeen", "Kissimmee", "Knoxville", "Lacey", "Lafayette", "Lake Charles", "Lakeland", "Lakewood", "Lancaster", "Lansing", "Laredo", "Las Cruces", "Las Vegas", "Layton", "Leominster", "Lewisville", "Lexington", "Lincoln", "Little Rock", "Long Beach", "Lorain", "Los Angeles", "Louisville", "Lowell", "Lubbock", "Macon", "Madison", "Manchester", "Marina", "Marysville", "McAllen", "McHenry", "Medford", "Melbourne", "Memphis", "Merced", "Mesa", "Mesquite", "Miami", "Milwaukee", "Minneapolis", "Miramar", "Mission Viejo", "Mobile", "Modesto", "Monroe", "Monterey", "Montgomery", "Moreno Valley", "Murfreesboro", "Murrieta", "Muskegon", "Myrtle Beach", "Naperville", "Naples", "Nashua", "Nashville", "New Bedford", "New Haven", "New London", "New Orleans", "New York", "New York City", "Newark", "Newburgh", "Newport News", "Norfolk", "Normal", "Norman", "North Charleston", "North Las Vegas", "North Port", "Norwalk", "Norwich", "Oakland", "Ocala", "Oceanside", "Odessa", "Ogden", "Oklahoma City", "Olathe", "Olympia", "Omaha", "Ontario", "Orange", "Orem", "Orlando", "Overland Park", "Oxnard", "Palm Bay", "Palm Springs", "Palmdale", "Panama City", "Pasadena", "Paterson", "Pembroke Pines", "Pensacola", "Peoria", "Philadelphia", "Phoenix", "Pittsburgh", "Plano", "Pomona", "Pompano Beach", "Port Arthur", "Port Orange", "Port Saint Lucie", "Port St. Lucie", "Portland", "Portsmouth", "Poughkeepsie", "Providence", "Provo", "Pueblo", "Punta Gorda", "Racine", "Raleigh", "Rancho Cucamonga", "Reading", "Redding", "Reno", "Richland", "Richmond", "Richmond County", "Riverside", "Roanoke", "Rochester", "Rockford", "Roseville", "Round Lake Beach", "Sacramento", "Saginaw", "Saint Louis", "Saint Paul", "Saint Petersburg", "Salem", "Salinas", "Salt Lake City", "San Antonio", "San Bernardino", "San Buenaventura", "San Diego", "San Francisco", "San Jose", "Santa Ana", "Santa Barbara", "Santa Clara", "Santa Clarita", "Santa Cruz", "Santa Maria", "Santa Rosa", "Sarasota", "Savannah", "Scottsdale", "Scranton", "Seaside", "Seattle", "Sebastian", "Shreveport", "Simi Valley", "Sioux City", "Sioux Falls", "South Bend", "South Lyon", "Spartanburg", "Spokane", "Springdale", "Springfield", "St. Louis", "St. Paul", "St. Petersburg", "Stamford", "Sterling Heights", "Stockton", "Sunnyvale", "Syracuse", "Tacoma", "Tallahassee", "Tampa", "Temecula", "Tempe", "Thornton", "Thousand Oaks", "Toledo", "Topeka", "Torrance", "Trenton", "Tucson", "Tulsa", "Tuscaloosa", "Tyler", "Utica", "Vallejo", "Vancouver", "Vero Beach", "Victorville", "Virginia Beach", "Visalia", "Waco", "Warren", "Washington", "Waterbury", "Waterloo", "West Covina", "West Valley City", "Westminster", "Wichita", "Wilmington", "Winston", "Winter Haven", "Worcester", "Yakima", "Yonkers", "York", "Youngstown"];

var animals = ["Aardvark",
    "Albatross",
    "Alligator",
    "Alpaca",
    "Ant",
    "Anteater",
    "Antelope",
    "Ape",
    "Armadillo",
    "Donkey",
    "Baboon",
    "Badger",
    "Barracuda",
    "Bat",
    "Bear",
    "Beaver",
    "Bee",
    "Bison",
    "Boar",
    "Buffalo",
    "Butterfly",
    "Camel",
    "Capybara",
    "Caribou",
    "Cassowary",
    "Cat",
    "Caterpillar",
    "Cattle",
    "Chamois",
    "Cheetah",
    "Chicken",
    "Chimpanzee",
    "Chinchilla",
    "Chough",
    "Clam",
    "Cobra",
    "Cockroach",
    "Cod",
    "Cormorant",
    "Coyote",
    "Crab",
    "Crane",
    "Crocodile",
    "Crow",
    "Curlew",
    "Deer",
    "Dinosaur",
    "Dog",
    "Dogfish",
    "Dolphin",
    "Dotterel",
    "Dove",
    "Dragonfly",
    "Duck",
    "Dugong",
    "Dunlin",
    "Eagle",
    "Echidna",
    "Eel",
    "Eland",
    "Elephant",
    "Elk",
    "Emu",
    "Falcon",
    "Ferret",
    "Finch",
    "Fish",
    "Flamingo",
    "Fly",
    "Fox",
    "Frog",
    "Gaur",
    "Gazelle",
    "Gerbil",
    "Giraffe",
    "Gnat",
    "Gnu",
    "Goat",
    "Goldfinch",
    "Goldfish",
    "Goose",
    "Gorilla",
    "Goshawk",
    "Grasshopper",
    "Grouse",
    "Guanaco",
    "Gull",
    "Hamster",
    "Hare",
	"HatHacker",
    "Hawk",
    "Hedgehog",
    "Heron",
    "Herring",
    "Hippopotamus",
    "Hornet",
    "Horse",
    "Human",
    "Hummingbird",
    "Hyena",
    "Ibex",
    "Ibis",
    "Jackal",
    "Jaguar",
    "Jay",
    "Jellyfish",
    "Kangaroo",
    "Kingfisher",
    "Koala",
    "Kookabura",
    "Kouprey",
    "Kudu",
    "Lapwing",
    "Lark",
    "Lemur",
    "Leopard",
    "Lion",
    "Llama",
    "Lobster",
    "Locust",
    "Loris",
    "Louse",
    "Lyrebird",
    "Magpie",
    "Mallard",
    "Manatee",
    "Mandrill",
    "Mantis",
    "Marten",
    "Meerkat",
    "Mink",
    "Mole",
    "Mongoose",
    "Monkey",
    "Moose",
    "Mosquito",
    "Mouse",
    "Mule",
    "Narwhal",
    "Newt",
    "Nightingale",
    "Octopus",
    "Okapi",
    "Opossum",
    "Oryx",
    "Ostrich",
    "Otter",
    "Owl",
    "Oyster",
    "Panther",
    "Parrot",
    "Partridge",
    "Peafowl",
    "Pelican",
    "Penguin",
    "Pheasant",
    "Pig",
    "Pigeon",
    "Pony",
    "Porcupine",
    "Porpoise",
    "Quail",
    "Quelea",
    "Quetzal",
    "Rabbit",
    "Raccoon",
    "Rail",
    "Ram",
    "Rat",
    "Raven",
    "Red deer",
    "Red panda",
    "Reindeer",
    "Rhinoceros",
    "Rook",
    "Salamander",
    "Salmon",
    "Sand Dollar",
    "Sandpiper",
    "Sardine",
    "Scorpion",
    "Seahorse",
    "Seal",
    "Shark",
    "Sheep",
    "Shrew",
    "Skunk",
    "Snail",
    "Snake",
    "Sparrow",
    "Spider",
    "Spoonbill",
    "Squid",
    "Squirrel",
    "Starling",
    "Stingray",
    "Stinkbug",
    "Stork",
    "Swallow",
    "Swan",
    "Tapir",
    "Tarsier",
    "Termite",
    "Tiger",
    "Toad",
    "Trout",
    "Turkey",
    "Turtle",
    "Viper",
    "Vulture",
    "Wallaby",
    "Walrus",
    "Wasp",
    "Weasel",
    "Whale",
    "Wildcat",
    "Wolf",
    "Wolverine",
    "Wombat",
    "Woodcock",
    "Woodpecker",
    "Worm",
    "Wren",
    "Yak",
    "Zebra"];

var colors = ["AliceBlue",
  "AntiqueWhite",
  "Aqua",
  "Aquamarine",
  "Azure",
  "Beige",
  "Bisque",
  "Bitcoin",
  "Black",
  "BlanchedAlmond",
  "Blue",
  "BlueViolet",
  "Brown",
  "BurlyWood",
  "CadetBlue",
  "Chartreuse",
  "Chocolate",
  "Coral",
  "CornflowerBlue",
  "Cornsilk",
  "Crimson",
  "Crypto",
  "Cyan",
  "DeepPink",
  "DeepSkyBlue",
  "DimGray",
  "DimGrey",
  "DodgerBlue",
  "FireBrick",
  "FloralWhite",
  "ForestGreen",
  "Fuchsia",
  "Gainsboro",
  "GhostWhite",
  "Gold",
  "GoldenRod",
  "Gray",
  "Grey",
  "Green",
  "GreenYellow",
  "HoneyDew",
  "HotPink",
  "IndianRed",
  "Indigo",
  "Ivory",
  "Khaki",
  "Lavender",
  "LavenderBlush",
  "LawnGreen",
  "LemonChiffon",
  "Lime",
  "LimeGreen",
  "Linen",
  "Magenta",
  "Maroon",
  "MidnightBlue",
  "MintCream",
  "MistyRose",
  "Navy",
  "OldLace",
  "Olive",
  "OliveDrab",
  "Orange",
  "OrangeRed",
  "Orchid",
  "PaleGoldenRod",
  "PaleGreen",
  "PaleTurquoise",
  "PaleVioletRed",
  "PapayaWhip",
  "PeachPuff",
  "Peru",
  "Pink",
  "Plum",
  "PowderBlue",
  "Purple",
  "RebeccaPurple",
  "Red",
  "RosyBrown",
  "RoyalBlue",
  "SaddleBrown",
  "Salmon",
  "SandyBrown",
  "SeaGreen",
  "SeaShell",
  "Sienna",
  "Silver",
  "SkyBlue",
  "SlateBlue",
  "SlateGray",
  "SlateGrey",
  "Snow",
  "SpringGreen",
  "SteelBlue",
  "Tan",
  "Teal",
  "Thistle",
  "Tomato",
  "Turquoise",
  "Violet",
  "Wheat",
  "White",
  "WhiteSmoke",
  "Yellow",
  "YellowGreen"];
  
  colorCodes = ["#CF24B1","#4D4DFF","#E0E722","#FFAD00","#D22730","#DB3EB1","#44D62C","#00FFFF","#FF00FF","#9D00FF"];
  
  function pickone(listarr){
  		var getLen = listarr.length;
        var pickRan = Math.floor(Math.random() * getLen);
        return listarr[pickRan];
  }
  
  //document.write(pickone(colors)+pickone(animals)+Math.floor(Math.random() * 999))

</script>
</body>
</html>