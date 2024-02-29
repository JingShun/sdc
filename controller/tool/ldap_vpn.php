<?php 
$ld = new MyLDAP();
$target = "ivend";

$data_array = array();
$data_array['base'] =  "ou=YongHua,ou=TainanComputer,dc=tainan,dc=gov,dc=tw";
$data_array['filter'] = "(objectClass=organizationalUnit)";
$data_array['attributes'] = array("name", "description");
$OUs = $ld->getData($data_array);

$default_computer_base = "cn=Computers, dc=tainan, dc=gov, dc=tw";
$default_computer_ou = "Computers";
$default_computer_description = "網域未分配電腦的容器";

$computer_base = "ou=TainanComputer, dc=tainan, dc=gov, dc=tw";
$computer_ou = "TainanComputer";
$computer_description = "臺南市政府公務個人電腦";

$user_base = "ou=VPN, dc=tainan, dc=gov, dc=tw";
$user_ou = "VPN";
$user_description = "臺南市政府使用者VPN帳號";

require 'view/header/default.php'; 
require 'view/body/tool/ldap_vpn.php';
require 'view/footer/default.php'; 
