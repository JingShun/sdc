<?php 
date_default_timezone_set('Asia/Taipei');

$db = Database::get();

$route = new Router(Request::uri()); //搭配 .htaccess 排除資料夾名稱後解析 URL

$template = Tamtamchik\SimpleFlash\TemplateFactory::create(Tamtamchik\SimpleFlash\Templates::SEMANTIC);  // get template from factory, e.g. template for Foundation

$flash = new Tamtamchik\SimpleFlash\Flash($template);  // passing to constructor

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);    //Loads environment variables 
$dotenv->load();
