<?php
// Sanitizes data and converts strings to UTF-8 (if available), according to the provided field whitelist
$whitelist = array("type", "server_id", "jsonConditions");
$_GET = $gump->sanitize($_GET, $whitelist);
$data_array = [];

// 安全性群組
$securityGroupsHtml = '';
$query = "SELECT
        twcc_security_group.security_group_name,
        twcc_security_group.direction,
        IFNULL(twcc_security_group.port_range_max, 'any') AS port_range_max,
        IFNULL(twcc_security_group.port_range_min, 'any') AS port_range_min,
        twcc_security_group.protocol,
        twcc_security_group.remote_ip_prefix AS CIDR,
        twcc_security_group.security_group_desc
    FROM twcc_security_group
    INNER JOIN twcc_site_security_group_map ON twcc_site_security_group_map.security_group_id = twcc_security_group.security_group_id
    INNER JOIN twcc_server ON twcc_server.site_id = twcc_site_security_group_map.site_id
    WHERE twcc_site_security_group_map.site_id AND twcc_server.server_id = '{$_GET['server_id']}'";

$rules = $db->execute($query, $data_array);

$securityGroupsHtml = buildTableHtml($rules, [
    'security_group_name' => '名稱',
    'direction' => '方向',
    'port_range_max' => 'Port(最小)',
    'port_range_min' => 'Port(最大)',
    'protocol' => '協定',
    'CIDR' => 'CIDR',
    'security_group_desc' => '描述',
]);


// 防火牆
$fwInfoHtml = '';
/*
        twcc_network.network_name,
        twcc_network.`status`,
        twcc_firewall.firewall_status,
*/
$query = "SELECT
        twcc_firewall.firewall_name,
        twcc_firewall.firewall_desc,
        twcc_firewall.firewall_rule_name,
        twcc_firewall.protocol,
        IFNULL(twcc_firewall.source_ip_address, 'any') AS source,
        IFNULL(twcc_firewall.source_port, 'any') AS source_port,
        IFNULL(twcc_firewall.destination_ip_address, 'any') AS destination_ip,
        IFNULL(twcc_firewall.destination_port, 'any') AS destination_port,
        twcc_firewall.action
    FROM twcc_network
    INNER JOIN twcc_firewall ON twcc_network.firewall_id = twcc_firewall.firewall_id
    INNER JOIN twcc_server_network_map ON twcc_server_network_map.network_id = twcc_network.network_id
    WHERE twcc_server_network_map.server_id = '{$_GET['server_id']}'
    ORDER BY twcc_network.firewall_id, firewall_rule_order";
$fwRules = $db->execute($query, $data_array);

$fwInfoHtml = buildTableHtml($fwRules, [
    'firewall_name' => 'FW名稱',
    'firewall_desc' => 'FW描述',
    'firewall_rule_name' => '規則名稱',
    'protocol' => '協定',
    'source' => '來源',
    'source_port' => '來源Port',
    'destination_ip' => '目的',
    'destination_port' => '目的Port',
    'action' => '動作',
]);



// 防火牆規則




function buildTableHtml($rows, $headMap = [])
{
    if (empty($rows)) return '';
    
    // 標題
    $table = '<table class="datatable display compact stripe hover" style="width:100%"><thead><tr><td></td>';
    $heads = array_keys(current($rows));
    foreach ($heads as $head) {
        $headName = $head;
        if (array_key_exists($head, $headMap))
            $headName = $headMap[$head];
        $table .= "<td>{$headName}</td>";
    }
    $table .= '</tr></thead><tbody>';

    // 內文
    foreach ($rows as $key => $colums) {
        $table .= '<tr><td>' . ($key + 1) . '</td>';
        foreach ($colums as $col) {
            $table .= '<td>' . $col . '</td>';
        }
        $table .= '</tr>';
    }
    $table .= '</tbody></table>';

    return $table;
}



?>

<div class="ui styled fluid accordion">
    <div class="title">
        <i class="dropdown icon"></i>
        安全性群組規則
    </div>
    <div class="content">
        <?= $securityGroupsHtml ?>
    </div>
</div>
<div class="ui styled fluid accordion">
    <div class="title">
        <i class="dropdown icon"></i>
        防火牆規則
    </div>
    <div class="content">
        <?= $fwInfoHtml ?>
    </div>
</div>