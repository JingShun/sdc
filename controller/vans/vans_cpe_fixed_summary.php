<?php

if (!isset($_GET['cvssge'])) {
    $cvssGE = 7;
} else {
    $cvssGE = filter_var($_GET['cvssge'], FILTER_VALIDATE_INT);
    if ($cvssGE === false) {
        echo ("參數錯誤");
        return;
    }
}

$type = 1;
$sqlWhere = '';
if (isset($_GET['type'])) {
    $type = filter_var($_GET['type'], FILTER_VALIDATE_INT);
}

switch ($type) {
    case 1: // 忽略 微軟、要授權的軟體
        // 微軟
        $sqlWhere .= "AND rapix_cpes.title NOT LIKE '%Microsoft%'";
        // 眾多相依
        $sqlWhere .= "AND rapix_cpes.title NOT LIKE '%JRE%'";
        $sqlWhere .= "AND rapix_cpes.title NOT LIKE '%Java%'";
        $sqlWhere .= "AND rapix_cpes.title NOT LIKE '%Python%'";
        $sqlWhere .= "AND rapix_cpes.title NOT LIKE '%MariaDB%'";
        $sqlWhere .= "AND rapix_cpes.title NOT LIKE '%MySQL%'";
        // 要授權
        $sqlWhere .= "AND rapix_cpes.title NOT LIKE '%Acrobat%'";
        $sqlWhere .= "AND rapix_cpes.title NOT LIKE '%Photoshop%'";
        $sqlWhere .= "AND rapix_cpes.title NOT LIKE '%Illustrator%'";
        $sqlWhere .= "AND rapix_cpes.title NOT LIKE '%Autodesk%'";
        // $sqlWhere .= "AND rapix_cpes.title NOT LIKE '%WinRAR%'"; // 永久試用
        break;
    case 2: // 忽略 OS、Office
        $sqlWhere .= "AND rapix_cpes.part <> 'o'";
        $sqlWhere .= "AND rapix_cpes.title NOT LIKE 'Microsoft Office%'";
        break;
    case 0:
    default:
        break;
}

// 條件串聯處理
$sqlWhere = trim($sqlWhere);
if (!empty($sqlWhere) && strtoupper(substr($sqlWhere, 0, 3)) != 'AND') {
    $sqlWhere = ' AND (' . $sqlWhere . ')';
}
$sql = "SELECT
    month_at,
    responsible_ou AS ou,
    part, vendor, product,
    sum(add_cnt) AS add_cnt,
    sum(fixed_cnt) AS fixed_cnt,
    (sum(add_cnt)) AS total
    FROM (
        SELECT
            left(rapix_cpe_client_map.created_at,7) as month_at,
            rapix_ou.responsible_ou,
            rapix_cpes.part, rapix_cpes.vendor, rapix_cpes.product,
            count(1) as add_cnt,
            0 as fixed_cnt
        FROM rapix_cpe_client_map
        INNER JOIN rapix_cpes ON rapix_cpes.id = rapix_cpe_client_map.rapix_cpe_id AND(rapix_cpes.cvss_v3_score > $cvssGE OR rapix_cpes.cvss_v2_score > $cvssGE)
        INNER JOIN rapix_clients ON rapix_cpe_client_map.rapix_client_id = rapix_clients.id
        INNER JOIN rapix_ou ON rapix_clients.org_id = rapix_ou.id AND rapix_ou.responsible_ou IS NOT NULL AND rapix_ou.dn NOT LIKE '區公所%'
        WHERE YEAR(rapix_cpe_client_map.created_at) = YEAR(curdate()) $sqlWhere
        GROUP BY month_at,rapix_ou.responsible_ou,rapix_cpes.part,rapix_cpes.vendor,rapix_cpes.product
        union all
        SELECT
            left(rapix_cpe_client_map.deleted_at,7) as month_at,
            rapix_ou.responsible_ou,
            rapix_cpes.part, rapix_cpes.vendor, rapix_cpes.product,
            0 as add_cnt,
            count(1) as fixed_cnt
        FROM rapix_cpe_client_map
        INNER JOIN rapix_cpes ON rapix_cpes.id = rapix_cpe_client_map.rapix_cpe_id AND(rapix_cpes.cvss_v3_score > $cvssGE OR rapix_cpes.cvss_v2_score > $cvssGE)
        INNER JOIN rapix_clients ON rapix_cpe_client_map.rapix_client_id = rapix_clients.id
        INNER JOIN rapix_ou ON rapix_clients.org_id = rapix_ou.id AND rapix_ou.responsible_ou IS NOT NULL AND rapix_ou.dn NOT LIKE '區公所%'
        WHERE YEAR(rapix_cpe_client_map.created_at) = YEAR(curdate()) AND YEAR(rapix_cpe_client_map.deleted_at) = YEAR(curdate()) $sqlWhere
        GROUP BY month_at,rapix_ou.responsible_ou,rapix_cpes.part,rapix_cpes.vendor,rapix_cpes.product
    ) t1
    GROUP BY month_at,responsible_ou,t1.part,t1.vendor,t1.product
    ";

$cpeDetailList = $db->execute($sql);

$month_gradually = []; // 想知道每月整體累計修補狀況
$ou_gradually = []; // 想知道各局處當前累計修補狀況

// 統計
foreach ($cpeDetailList as $cpe) {
    // 初始化
    if (!array_key_exists($cpe['month_at'], $month_gradually))
        $month_gradually[$cpe['month_at']] = [
            'month' => $cpe['month_at'],
            'add_cnt' => 0,
            'fixed_cnt' => 0,
            'fixed_rate' => 0,
        ];

    if (!array_key_exists($cpe['ou'], $ou_gradually))
        $ou_gradually[$cpe['ou']] = [
            'ou' => $cpe['ou'],
            'add_cnt' => 0,
            'fixed_cnt' => 0,
            'fixed_rate' => 0,
        ];

    $month_gradually[$cpe['month_at']]['add_cnt'] += $cpe['add_cnt'];
    $month_gradually[$cpe['month_at']]['fixed_cnt'] += $cpe['fixed_cnt'];
    $ou_gradually[$cpe['ou']]['add_cnt'] += $cpe['add_cnt'];
    $ou_gradually[$cpe['ou']]['fixed_cnt'] += $cpe['fixed_cnt'];
}

// 每月累計
ksort($month_gradually);
$month_gradually = array_values($month_gradually);
$add_list = array_column($month_gradually, 'add_cnt');
$fixed_list = array_column($month_gradually, 'fixed_cnt');
foreach ($month_gradually as $idx => $item0) {
    $month_gradually[$idx]['add_cnt'] = array_sum(array_slice($add_list, 0, $idx + 1));
    $month_gradually[$idx]['fixed_cnt'] = array_sum(array_slice($fixed_list, 0, $idx + 1));
}

$sql = "SELECT rapix_ou.responsible_ou AS ou, count(1) AS total, SUM(CASE WHEN rapix_cpe_client_map.deleted_at IS NOT NULL THEN 1 ELSE 0 END) AS fixed_cnt
    FROM rapix_cpe_client_map
    INNER JOIN rapix_cpes ON rapix_cpes.id = rapix_cpe_client_map.rapix_cpe_id AND(rapix_cpes.cvss_v3_score > $cvssGE OR rapix_cpes.cvss_v2_score > $cvssGE)
    INNER JOIN rapix_clients ON rapix_cpe_client_map.rapix_client_id = rapix_clients.id
    INNER JOIN rapix_ou ON rapix_clients.org_id = rapix_ou.id AND rapix_ou.responsible_ou IS NOT NULL AND rapix_ou.dn NOT LIKE '區公所%'
    WHERE YEAR(rapix_cpe_client_map.created_at) < YEAR(curdate()) $sqlWhere
    GROUP BY rapix_ou.responsible_ou";
$ouCPE_lastYear = $db->execute($sql);


// 修補率
$history_total = 0;
$history_fixed = 0;

foreach ($ou_gradually as $ou => &$item) {
    $history_ou_total = current(array_filter($ouCPE_lastYear, fn ($e) => $e['ou'] == $ou))['total'];
    $history_ou_fixed = current(array_filter($ouCPE_lastYear, fn ($e) => $e['ou'] == $ou))['fixed_cnt'];
    $history_total += $history_ou_total;
    $history_fixed += $history_ou_fixed;

    $item['last_cnt'] = $history_ou_total;
    $item['total'] = $item['add_cnt'] + $history_ou_total;
    $item['fixed_cnt'] = $item['fixed_cnt'] + $history_ou_fixed;
    $item['fixed_rate'] = floor(100 * $item['fixed_cnt'] / $item['total']);
}
unset($item);

// xdebug_break();
foreach ($month_gradually as &$item) {

    $item['fixed_rate'] = floor(100 * ($item['fixed_cnt'] + $history_fixed) / ($item['add_cnt'] + $history_total));
    $item['history_total'] = $item['add_cnt'] + $history_total;
    $item['history_fixed'] = $item['fixed_cnt'] + $history_fixed;
}
unset($item);
usort($ou_gradually, fn ($a, $b) => $a["fixed_rate"] < $b["fixed_rate"]);

require 'view/header/default.php';
require 'view/body/vans/vans_cpe_fixed_summary.php';
require 'view/footer/default.php';
