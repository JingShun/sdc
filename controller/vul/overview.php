<?php
$local_gov_vuls = array(
    array(
        'id' => 'tainan',
        'displayname' => '臺南市',
        'oid' => '2.16.886.101.90028.20002',
        'ou_vuls' => array(),
        'ou_number' => 0,
    ),
    // array(
    //     'id' => 'chiayi',
    //     'displayname' => '嘉義市',
    //     'oid' => '2.16.886.101.90014.20002',
    //     'ou_vuls' => array(),
    //     'ou_number' => 0,
    // ),
    // array(
    //     'id' => 'cyhg',
    //     'displayname' => '嘉義縣',
    //     'oid' => '2.16.886.101.90013.20002',
    //     'ou_vuls' => array(),
    //     'ou_number' => 0,
    // ),
    // array(
    //     'id' => 'yunlin', 
    //     'displayname' => '雲林縣',
    //     'oid' => '2.16.886.101.90012.20002',
    //     'ou_vuls' => array(),
    //     'ou_number' => 0,
    // ),
);

foreach ($local_gov_vuls as $index => $local_gov_vul) {
    $sql = 
    "SELECT 
        oid,
        ou, 
        SUM(number) AS number,
        SUM(fixed_number) AS fixed_number
    FROM 
        scan_stats
    WHERE 
        oid LIKE '" . $local_gov_vul['oid'] . "%'
    GROUP BY 
        oid, ou
    ORDER BY 
        oid";
    $local_gov_vuls[$index]['ou_vuls'] = $db->execute($sql,[]);
    $local_gov_vuls[$index]['ou_number'] = $db->getLastNumRows();
}

$sql_details = 
"SELECT 
    system_name,
    system_living,
    SUM(number) AS number,
    SUM(fixed_number) AS fixed_number
FROM 
    scan_stats
WHERE 
    oid LIKE :oid
GROUP BY
    system_name, system_living
ORDER BY 
    system_living DESC, system_name ASC";

$sql_targets = 
"SELECT 
    * 
FROM 
    scan_targets 
WHERE 
    oid LIKE :oid";

require 'view/header/default.php'; 
require 'view/body/vul/overview.php';
require 'view/footer/default.php'; 
