<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\POP3;
use PHPMailer\PHPMailer\SMTP;
//Load composer's autoloader
$local_path = "/var/www/html/utility/PHPMailer-master/";
require_once $local_path.'vendor/autoload.php';
require_once("function.php");
require_once("../mysql_connect.inc.php");
$account  	  = $_POST['account'];
$password 	  = $_POST['password'];
$verification = $_POST['verification'];
 //特殊字元跳脫(NUL (ASCII 0), \n, \r, \, ', ", and Control-Z)
$account		= mysqli_real_escape_string($conn,$account);
$password	  	= mysqli_real_escape_string($conn,$password);
$verification 	= mysqli_real_escape_string($conn,$verification);
//搜尋資料庫資料
$sql = "SELECT * FROM users where SSOID = '$account'";
$result = mysqli_query($conn,$sql);
$row = @mysqli_fetch_assoc($result);

switch($verification){
	case "ad":
		if(checkAccountByLDAP($account, $password) && $row['SSOID'] == $account){
			session_start();
			$_SESSION['account']	= $account;
			$_SESSION['UserName']   = $row['UserName'];
			//echo $row['Level']; 
			if($row['Level'] == 2){
				$_SESSION['Level'] = $row['Level'];
			}
			header("refresh:0;url=../"); 
		}else{
			session_start();
			$_SESSION["error"] = "invalid account or password";
			header("refresh:0;url=login.php"); 
		}
		break;
	case "mail":
		$pop = POP3::popBeforeSmtp('pop3.tainan.gov.tw', 110, 30, $account,$password, 1);
		if($pop && $row['SSOID'] == $account){
			session_start();
			$_SESSION['account']	= $account;
			$_SESSION['UserName']   = $row['UserName'];
			//echo $row['Level']; 
			if($row['Level'] == 2){
				$_SESSION['Level'] = $row['Level'];
			}
			header("refresh:0;url=../"); 
		}else{
			session_start();
			$_SESSION["error"] = "invalid account or password";
			header("refresh:0;url=login.php"); 
		}
		break;
}

?>
