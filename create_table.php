<?php
require("mysql_connect.inc.php");

$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
 }

$conn->query('NAMES UTF8');
$sql = "CREATE TABLE drip_client_list
		(
		DetectorName		varchar(30),	
		DetectorIP			varchar(30),		
		DetectorGroup		varchar(30),
        IP					varchar(30),		
        MAC					varchar(30),		
        GroupName			varchar(30),
        ClientName			varchar(30),
        SwitchIP			varchar(30),
        SwitchName			varchar(30),
        PortName			varchar(30),
        NICProductor		varchar(30),
        LastOnlineTime		varchar(30),
        LastOfflineTime		varchar(30),
		PRIMARY KEY (IP,MAC)
		)";
if($result = mysqli_query($conn,$sql)){
}else{
	echo("Error description: " . mysqli_error($conn));
}

$conn->close();	

?>
