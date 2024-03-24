<?php
$sql = "SELECT
    nics_vans_unit_summary.unit_name,
    nics_vans_unit_summary.unit_level,
    if(actionDescr IS NOT NULL, if(substring(actionDescr,3,4)='KBID','KBID','CPE'), null) AS type,
    SUBSTRING_INDEX(SUBSTRING_INDEX(actionDescr, ' [',1),') ',-1) AS asset_type,
    SUBSTRING_INDEX(SUBSTRING_INDEX(actionDescr, ')',1),'(',-1) AS upload_type,
    GROUP_CONCAT(DISTINCT SUBSTRING_INDEX(SUBSTRING_INDEX(actionDescr, ']',1),'[',-1)) AS target_unit,
    Max(nics_vans_log.created_at) AS updated_at
    FROM
    nics_vans_unit_summary
    LEFT JOIN nics_vans_log ON nics_vans_unit_summary.unit_name = nics_vans_log.unit_name
        AND nics_vans_log.`actionType` = '上傳'
        AND nics_vans_log.created_at > DATE_SUB(NOW(),INTERVAL 62 day)
    WHERE (nics_vans_unit_summary.`unit_level` IN ('B', 'C') or (unit_level = 'D' and nics_vans_log.created_at IS NOT NULL))
    GROUP BY unit_name,unit_level,type,asset_type,upload_type
    ORDER BY unit_level,unit_name,asset_type,updated_at DESC,upload_type
    ";
$nicsUnitLog = $db->execute($sql);

$nicsOuRows = array();
foreach ($nicsUnitLog as $log) {
    if (!isset($nicsOuRows[$log['unit_name']])) {
        $nicsOuRows[$log['unit_name']] = array(
            'ou' => $log['unit_name'],
            'level' => $log['unit_level'],
            'userCPE' => '',
            'userCPE_warning' => true,
            'userKBID' => '',
            'userKBID_warning' => true,
            'systemCPE' => '',
            'systemCPE_warning' => true,
            'systemKBID' => '',
            'systemKBID_warning' => true,
        );
    }

    // 沒資料就換下一個
    if (empty($log['updated_at'])) {
        continue;
    }

    $field = $log['asset_type'] . $log['type'];
    $value = implode(
        ', ',
        array_filter(array_merge(
            [$nicsOuRows[$log['unit_name']][$field]],
            ['[' . $log['upload_type'] . '] ' . date('m/d', strtotime($log['updated_at']))]
        ))
    );
    $dateDifference = abs(time() - strtotime($log['updated_at'])) / (60 * 60 * 24);
    $warning = $dateDifference >= 30;


    $nicsOuRows[$log['unit_name']][$field] = $value;

    // 因應可能上傳多次，只要有一次在時間內即可
    if (!$warning && $nicsOuRows[$log['unit_name']][$field . '_warning'])
        $nicsOuRows[$log['unit_name']][$field . '_warning'] = $warning;
}


$rapixOULog = array();
// $sql = "SELECT
//         vans_unit_name,
//         'cpe' as type,
//         SUBSTRING_INDEX(SUBSTRING_INDEX(vans_url,'Insert',-1), 'Unit', 1) AS asset_type,
//         vans_return_code AS return_code,
//         max(vans_return_at) AS return_at,
//         CONCAT(DATE_FORMAT(max(vans_return_at),'%m/%d'), ' ' ,vans_return_code, ' (', count(1) ,') ') AS msg
//     FROM `rapix_ou`
//     WHERE  vans_unit_name IS NOT NULL AND vans_unit_name <> '' AND vans_return_code <> '' AND vans_return_at > DATE_SUB(NOW(),INTERVAL 31 day)
//     GROUP BY vans_unit_name, vans_url, vans_return_code
//     UNION
//     SELECT
//         vans_unit_name,
//         'kb' as type,
//         SUBSTRING_INDEX(SUBSTRING_INDEX(vans_kburl,'Insert',-1), 'Unit', 1) AS asset_type,
//         CONCAT(vans_kb_return_code,' (', count(1), ')') AS kb_return_code,
//         max(vans_kb_return_at)AS kb_return_at,
//         CONCAT(DATE_FORMAT(max(vans_kb_return_at), '%m/%d'), ' ' , vans_kb_return_code, ' (' ,count(1) ,') ') AS kb_msg
//     FROM `rapix_ou`
//     WHERE  vans_unit_name IS NOT NULL AND vans_unit_name <> '' AND vans_kb_return_code <> ''
//     AND vans_kb_return_at > DATE_SUB(NOW(),INTERVAL 31 day)
//     GROUP BY vans_unit_name, vans_kburl, vans_kb_return_code";


// $rapixOULog = $db->execute($sql);

$rapixOuRows = array();
foreach ($rapixOULog as $log) {
    if (!isset($rapixOuRows[$log['vans_unit_name']])) {
        $rapixOuRows[$log['vans_unit_name']] = array(
            'ou' => $log['vans_unit_name'],
            'userCPE' => [],
            'userCPE_warning' => false,
            'userKBID' => [],
            'userKBID_warning' => false,
            'systemCPE' => [],
            'systemCPE_warning' => false,
            'systemKBID' => [],
            'systemKBID_warning' => false,
        );

        $dateDifference = abs(time() - strtotime($log['return_at'])) / (60 * 60 * 24);
        $warning = $dateDifference >= 30;

        if ($log['type'] == 'kb' && $log['asset_type'] == 'Computer') {
            $rapixOuRows[$log['vans_unit_name']]['userKBID'][] = $log['msg'];
            $rapixOuRows[$log['vans_unit_name']]['userKBID_warning'] |= $warning;
        }
        if ($log['type'] == 'cpe' && $log['asset_type'] == 'Computer') {
            $rapixOuRows[$log['vans_unit_name']]['userCPE'][] = $log['msg'];
            $rapixOuRows[$log['vans_unit_name']]['userCPE_warning'] |= $warning;
        }
        if ($log['type'] == 'kb' && $log['asset_type'] == 'System') {
            $rapixOuRows[$log['vans_unit_name']]['systemKBID'][] = $log['msg'];
            $rapixOuRows[$log['vans_unit_name']]['systemKBID_warning'] |= $warning;
        }
        if ($log['type'] == 'cpe' && $log['asset_type'] == 'System') {
            $rapixOuRows[$log['vans_unit_name']]['systemCPE'][] = $log['msg'];
            $rapixOuRows[$log['vans_unit_name']]['systemCPE_warning'] |= $warning;
        }
    }
}
$rapixOuRows_num = $db->getLastNumRows();



require 'view/header/default.php';
require 'view/body/vans/vans_return.php';
require 'view/footer/default.php';
