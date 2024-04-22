<?php
$sql = "SELECT
    month_at,
    responsible_ou AS ou,
    part, vendor, product, version,
    sum(add_cnt) AS add_cnt,
    sum(fixed_cnt) AS fixed_cnt,
    (sum(add_cnt)+sum(fixed_cnt)) AS total
    FROM (
        SELECT
            left(rapix_cpe_client_map.created_at,7) as month_at,
            rapix_ou.responsible_ou,
            rapix_cpes.part, rapix_cpes.vendor, rapix_cpes.product, rapix_cpes.version,
            count(1) as add_cnt,
            0 as fixed_cnt
        FROM rapix_cpe_client_map
        INNER JOIN rapix_cpes ON rapix_cpes.id = rapix_cpe_client_map.rapix_cpe_id AND(rapix_cpes.cvss_v3_score > 7 OR rapix_cpes.cvss_v2_score > 7)
        INNER JOIN rapix_clients ON rapix_cpe_client_map.rapix_client_id = rapix_clients.id
        INNER JOIN rapix_ou ON rapix_clients.org_id = rapix_ou.id AND rapix_ou.responsible_ou IS NOT NULL
        WHERE rapix_cpe_client_map.created_at  > LAST_DAY(date_sub(curdate(),interval 4 month))
        GROUP BY month_at,rapix_ou.responsible_ou,rapix_cpes.part,rapix_cpes.vendor,rapix_cpes.product,rapix_cpes.version
        union all
        SELECT
            left(rapix_cpe_client_map.deleted_at,7) as month_at,
            rapix_ou.responsible_ou,
            rapix_cpes.part, rapix_cpes.vendor, rapix_cpes.product, rapix_cpes.version,
            0 as add_cnt,
            count(1) as fixed_cnt
        FROM rapix_cpe_client_map
        INNER JOIN rapix_cpes ON rapix_cpes.id = rapix_cpe_client_map.rapix_cpe_id AND(rapix_cpes.cvss_v3_score > 7 OR rapix_cpes.cvss_v2_score > 7)
        INNER JOIN rapix_clients ON rapix_cpe_client_map.rapix_client_id = rapix_clients.id
        INNER JOIN rapix_ou ON rapix_clients.org_id = rapix_ou.id AND rapix_ou.responsible_ou IS NOT NULL
        WHERE rapix_cpe_client_map.deleted_at  > LAST_DAY(date_sub(curdate(),interval 4 month))
        GROUP BY month_at,rapix_ou.responsible_ou,rapix_cpes.part,rapix_cpes.vendor,rapix_cpes.product,rapix_cpes.version
    ) t1
    GROUP BY month_at,responsible_ou,t1.part,t1.vendor,t1.product,t1.version";
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



// 修補率
foreach ($month_gradually as &$item) {
    $item['fixed_rate'] = floor(100 * $item['fixed_cnt'] / ($item['add_cnt'] + $item['fixed_cnt']));
}
unset($item);
foreach ($ou_gradually as &$item) {
    $item['fixed_rate'] = floor(100 * $item['fixed_cnt'] / ($item['add_cnt'] + $item['fixed_cnt']));
}
unset($item);
usort($ou_gradually, fn ($a, $b) => $a["fixed_rate"] < $b["fixed_rate"]);

require 'view/header/default.php';
require 'view/body/vans/vans_cpe_fixed_summary.php';
require 'view/footer/default.php';
