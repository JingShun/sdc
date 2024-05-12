<?php
$sql = "SELECT
        nics_vans_asset.unit_name,
        nics_vans_asset.product_type,
        nics_vans_asset.asset_vendor,
        nics_vans_asset.asset_name,
        Sum(nics_vans_asset.asset_number) AS asset_number,
        Max(nics_vans_asset.cvss) AS cvss,
        Sum(nics_vans_asset.cve_number) AS cve_number,
        Sum(nics_vans_asset.cve_number - nics_vans_asset.progress_number) AS no_progress_number
    FROM `nics_vans_asset`
    WHERE cve_number <> progress_number
    GROUP BY unit_name, product_type, asset_name, asset_vendor
    ORDER BY unit_name, product_type, asset_vendor";
$queryNotProgressAsset = $db->execute($sql);

$nicsOuUpdatedAt = 0;
$ou_list = array();
$nicsOuRows = array();
foreach ($queryNotProgressAsset as $asset) {
    if (!isset($nicsOuRows[$asset['unit_name'] . $asset['product_type']])) {
        $nicsOuRows[$asset['unit_name'] . $asset['product_type']] = array(
            'unit_name' => $asset['unit_name'],
            'product_type' => $asset['product_type'],
            'asset_number' => 0,
            'cvss' => $asset['cvss'],
            'cve_number' => 0,
            'no_progress_number' => 0,
        );
    }

    $nicsOuRows[$asset['unit_name'] . $asset['product_type']]['asset_number'] += $asset['asset_number'];
    $nicsOuRows[$asset['unit_name'] . $asset['product_type']]['cve_number'] += $asset['cve_number'];
    $nicsOuRows[$asset['unit_name'] . $asset['product_type']]['no_progress_number'] += $asset['no_progress_number'];
    $nicsOuRows[$asset['unit_name'] . $asset['product_type']]['cvss'] = max($nicsOuRows[$asset['unit_name'] . $asset['product_type']]['cvss'], $asset['cvss']);

    $ou_list[$asset['unit_name']] = 1;
}



require 'view/header/default.php';
require 'view/body/vans/vans_nics_cpe_progress.php';
require 'view/footer/default.php';
