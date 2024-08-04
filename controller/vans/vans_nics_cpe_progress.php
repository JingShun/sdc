<?php
$sql = "SELECT
        nics_vans_asset.unit_name,
        security_rank.rank,
        nics_vans_asset.product_type,
        nics_vans_asset.asset_vendor,
        nics_vans_asset.asset_name,
        Sum(nics_vans_asset.asset_number) AS asset_number,
        Max(nics_vans_asset.cvss) AS cvss,
        Sum(nics_vans_asset.cve_number) AS cve_number,
        Sum(nics_vans_asset.cve_number - nics_vans_asset.progress_number) AS no_progress_number
    FROM `nics_vans_asset`
    LEFT JOIN security_rank ON nics_vans_asset.unit_name = security_rank.`name`
    WHERE cve_number > 0
    GROUP BY unit_name, product_type, asset_name, asset_vendor
    ORDER BY unit_name, product_type, asset_vendor";
$queryNotProgressAsset = $db->execute($sql);

$nicsOuUpdatedAt = 0;
$ou_list = array();
$nicsOuRows = array();
foreach ($queryNotProgressAsset as $asset) {
    if (!isset($nicsOuRows[$asset['unit_name'] . 'AnyType'])) {
        $nicsOuRows[$asset['unit_name']  . 'AnyType'] = array(
            'unit_name' => $asset['unit_name'],
            'rank' => $asset['rank'],
            'product_type' => 'AnyType',
            'asset_number' => 0,
            'cvss' => $asset['cvss'],
            'cve_number' => 0,
            'no_progress_number' => 0,
        );
    }

    $nicsOuRows[$asset['unit_name'] . 'AnyType']['asset_number'] += $asset['asset_number'];
    $nicsOuRows[$asset['unit_name'] . 'AnyType']['cve_number'] += $asset['cve_number'];
    $nicsOuRows[$asset['unit_name'] . 'AnyType']['no_progress_number'] += $asset['no_progress_number'];
    $nicsOuRows[$asset['unit_name'] . 'AnyType']['cvss'] = max($nicsOuRows[$asset['unit_name'] . 'AnyType']['cvss'], $asset['cvss']);

    $totalCVE = $nicsOuRows[$asset['unit_name'] . 'AnyType']['cve_number'];

    $nicsOuRows[$asset['unit_name'] . 'AnyType']['rate'] = round(100 * ($totalCVE - $nicsOuRows[$asset['unit_name'] . 'AnyType']['no_progress_number']) / $totalCVE, 2);


    $ou_list[$asset['unit_name']] = 1;
}

// 依照修補率高到低排序
usort($nicsOuRows, fn ($a, $b) => $a['rate'] < $b['rate'] ? 1 : -1);

// 尚未填寫完畢機關數
$notFinishOuCount = count(array_filter($nicsOuRows, fn ($ou) => $ou['no_progress_number']));

require 'view/header/default.php';
require 'view/body/vans/vans_nics_cpe_progress.php';
require 'view/footer/default.php';
