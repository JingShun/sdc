<!--index.php-->
<?php session_start(); 
	require_once("login/function.php");
	verifyBySession("account");
?>

<?php
	if(isset($_GET['mainpage']) AND !empty($_GET['mainpage'])){
		$page = $_GET['mainpage'];
	}else{
		$page = "info";
	}

	include('header.php'); // 載入共用的頁首
	switch($page){  // 依照 GET 參數載入共用的內容
		case "dashboard":
		  include('dashboard.php');
		break;
		case "info":
		  include('info.php');
		break;
		case "query":
		  include('query.php');
		break;
		case "vulnerability":
		  include('vulnerability.php');
		break;
		case "nmap":
		  include('nmap.php');
		break;

	}
	include('sidebar.php'); // 載入共用的側欄
	include('footer.php'); // 載入共用的頁尾
?>
