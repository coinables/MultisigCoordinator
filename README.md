# Multisig Coordination Server 

A self-hosted local home server that acts as your personal Multisig Coordinator allowing any browser enabled device in your home to potentially be a multisig signing device. 

Multisig Coordination Platform explained video: https://youtu.be/3Z3AGUyebRw       
Video demonstrating the multisig coordiantion server running on transactus.org: https://youtu.be/U8XjWMSR5NM?t=130


This is free and unencumbered software released into the public domain.

Anyone is free to copy, modify, publish, use, compile, sell, or distribute this software, either in source code form or as a compiled binary, for any purpose, commercial or non-commercial, and by any means.

In jurisdictions that recognize copyright laws, the author or authors of this software dedicate any and all copyright interest in the software to the public domain. We make this dedication for the benefit of the public at large and to the detriment of our heirs and successors. We intend this dedication to be an overt act of relinquishment in perpetuity of all present and future rights to this software under copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

For more information, please refer to http://unlicense.org/

# Requirements    

### 1. Apache Web Server

### 2. MySQL Database

### 3. PHP
 
NOTE: If you're on Windows tools like XAMPP and WAMP will install the above three requirements for you and you can skep to the SETUP.

=================================================       
###INSTALL ON LINUX

#### Installing Apache and Updating the Firewall
```
sudo apt update
sudo apt install apache2
```
#### Adjust the Firewall to Allow Web Traffic
```
sudo ufw app list
```       
```Output
Available applications:
  Apache
  Apache Full
  Apache Secure
  OpenSSH
```
If you look at the ```Apache Full``` profile, it should show that it enables traffic to ports 80 and 443:
```
sudo ufw app info "Apache Full"
```
```
Output
Profile: Apache Full
Title: Web Server (HTTP,HTTPS)
Description: Apache v2 is the next generation of the omnipresent Apache web
server.

Ports:
  80,443/tcp
```
Allow incoming HTTP and HTTPS traffic for this profile:
```
sudo ufw allow in "Apache Full"
```
Visit your IP address in any browser

http://your_server_ip

#### Install MySQL
    Update the system packages to the latest versions:

	> `sudo apt update && sudo apt upgrade`

	#####Installing MySql

	Run the following command to install MySql;

	> `sudo apt install mysql-server`

	<!-- ##### Now confirm the installation -->

	Now confirm the installation and check MySQL version typing following command:

	> `mysql -V`

	After completing installation MySQL will start automatically. Check MySQL version by typing:

	> `sudo systemctl status mysql`

	The output something like below:

	```
	mysql.service - MySQL Community Server
	   Loaded: loaded (/lib/systemd/system/mysql.service; enabled; vendor preset: enabled)
	   Active: active (running) since ...
	```

	##### Start MySql Console by typing

	> `mysql -u root -p`

	#####Setting Up MySQL

	> `sudo mysql_secure_installation`  

	#####Import the sql file
	
	> `sudo mysql -u username -p database_name < db.sql 
	
#### Install PHP

```
sudo apt install php libapache2-mod-php php-mysql
```

Restart Apache Server
```
sudo systemctl restart apache2
```

=================================================

### SETUP

1. Import SQL file named db.sql in this repository 

2. Copy the wallet directory from this repository to your root web directory (ie xampp/htdocs/

3. Run Apache and MySQL

4. Test by opening a browser and go to localhost/wallet

5. To access with other devices in your home visit the PC running the server's local IP address (ie 128.0.10.12/wallet)

=================================================

If you find this useful please consider buying me a pint: 3LnFQjs5HracJn5cakAmGQ8N1PZey65kyJ 
