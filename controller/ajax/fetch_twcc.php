<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../libraries/TwccAdapter.php';
require_once __DIR__ . '/../../config/twcc.php';

$db = Database::get();
$twcc = new TWCCAdapter();
$recordCount = 0;


// xdebug_break();

$twcc = new TWCCAdapter();

// 更新安全性群組
echo PHP_EOL . '更新安全性群組' . PHP_EOL;
updateSecurityGroups($twcc, 1);

// 更新虛擬網路
echo PHP_EOL . '更新虛擬網路' . PHP_EOL;
updateNetwork($twcc, 1);

// 更新防火牆規則
echo PHP_EOL . '更新防火牆規則' . PHP_EOL;
updateFilewall($twcc, 1);

// 更新Site
echo PHP_EOL . '更新Site' . PHP_EOL;
updateSite($twcc, 1);

// 更新Server
echo PHP_EOL . '更新Server' . PHP_EOL;
updateServer($twcc, 1);




/** 更新安全性群組
 * @param TWCCAdapter $twcc 
 * @return void 
 * @throws PDOException 
 */
function updateSecurityGroups(TWCCAdapter $twcc, $cliMsg = false)
{

    global $db, $recordCount;

    if ($cliMsg) echo '取得安全性群組...';

    $securityGroups = $twcc->listSecurityGroups();

    if ($cliMsg) echo count($securityGroups) . '組' . PHP_EOL;

    $recordCount += count($securityGroups);

    foreach ($securityGroups as $key => $security) {

        // 安全性群組重設
        // if ($cliMsg) echo "[$key]{$security['name']} / {$security['desc']} / " . count($security['security_group_rules']) . '條 ...';
        $db->delete('twcc_security_group', 'security_group_id', $security['id']);
        $item = [
            'security_group_id' => $security['id'],
            'security_group_name' => $security['name'],
            'security_group_desc' => $security['desc'],
            'associated_server_count' => $security['associated_server_count'],
        ];
        if (count($security['security_group_rules'])) {
            foreach ($security['security_group_rules'] as $rule) {
                $item['security_group_rule_id'] = $rule['id'];
                $item['direction'] = $rule['direction'];
                $item['ethertype'] = $rule['ethertype'];
                $item['port_range_max'] = $rule['port_range_max'];
                $item['port_range_min'] =  $rule['port_range_min'];
                $item['protocol'] = $rule['protocol'];
                $item['remote_ip_prefix'] = $rule['remote_ip_prefix'];

                $db->insert('twcc_security_group', $item);
            }
        } else {
            $db->insert('twcc_security_group', $item);
        }
        // if ($cliMsg) echo 'ok' . PHP_EOL;

        // map對應重設
        // if ($cliMsg) echo '　map: ' . count($security['associated_sites']) . ' 台 ...';
        $db->delete('twcc_site_security_group_map', 'security_group_id', $security['id']);
        foreach ($security['associated_sites'] as $key => $siteID) {
            $item = [
                'site_id' => $siteID,
                'security_group_id' => $security['id'],
            ];
            $db->insert('twcc_site_security_group_map', $item);
        }
        // if ($cliMsg) echo 'ok' . PHP_EOL;
    }
    return true;
}

/** 更新虛擬網路
 * @param mixed $twcc 
 * @param bool $cliMsg 
 * @return bool 
 */
function updateNetwork($twcc, $cliMsg = false)
{
    global $db, $recordCount;
    $netList = $twcc->listNetworks();

    if (empty($netList)) return false;

    if ($cliMsg)
        echo '發現虛擬網路共 ' . count($netList)  . ' 個，開始取得詳細資訊...';

    $recordCount += count($netList);

    foreach ($netList as $key => &$net) {

        $detail = $twcc->getNetworkDetail($net['id']);

        $net = [
            'network_id' => $net['id'],
            'network_name' => $net['name'],
            'cidr' => $net['cidr'],
            // 'with_router' => $net['with_router'],
            'gateway' => $net['gateway'],
            'status' => $net['status'],
        ];

        if (!empty($detail['firewall'])) {
            $net['firewall_id'] = $detail['firewall']['id'];
            $net['firewall_name'] = $detail['firewall']['name'];
        }
    }
    unset($net);

    if ($cliMsg) {
        echo 'ok' . PHP_EOL;
        echo '更新DB...';
    }
    $db->execute('truncate table twcc_network');
    foreach ($netList as $key => $net) {
        $db->insert('twcc_network', $net);
    }

    if ($cliMsg)
        echo 'ok' . PHP_EOL;
    return true;
}

/** 更新防火牆規則
 * @param TWCCAdapter $twcc 
 * @return bool 
 */
function updateFilewall(TWCCAdapter $twcc, $cliMsg = false)
{
    global $db, $recordCount;
    if ($cliMsg)
        echo '取得防火牆與其規則...';
    $fwList = $twcc->listFirewall();
    $rules = $twcc->listFirewallRules();

    if (empty($fwList) || empty($fwList)) {
        if ($cliMsg)
            echo ' 失敗' . PHP_EOL;
        return false;
    }

    $recordCount += count($fwList) + count($rules);

    if ($cliMsg) {
        echo '防火牆 ' . count($fwList) . ' 個, 規則 ' . count($rules) . ' 個' . PHP_EOL;
        echo '整理規則對應...';
    }


    $firewalls = [];

    foreach ($fwList as $key => $fw) {

        // $fw['rules'] 這個防火牆規則順序是隨機的，需要取得詳細資料的防火牆規則順序才是對的
        echo "　[{$fw['id']}]{$fw['name']}...";

        $detail = $twcc->getFirewallDetail($fw['id']);

        foreach ($detail['rules'] as $ruleIndex => $rule) {
            $firewalls[$rule['id']] = [
                'firewall_id' => $fw['id'],
                'firewall_name' => $fw['name'],
                'firewall_status' => $fw['status'],
                'firewall_desc' => $fw['desc'],
                // 'firewall_status_reason' => $detail['status_reason'], // 狀態原因
                // 'firewall_networks' => implode(',', $detail['associate_networks']), // 關聯的內部網段
                // 'firewall_create_time' => date('Y-m-d H:i:s', strtotime($detail['create_time'])), // FW建立時間

                'firewall_rule_id' => $rule['id'],
                'firewall_rule_order' => $ruleIndex,
            ];
        }
        echo "ok" . PHP_EOL;
    }

    foreach ($rules as $rule) {

        if (empty($firewalls[$rule['id']])) $firewalls[$rule['id']] = [];

        $firewalls[$rule['id']] = $firewalls[$rule['id']] + [
            'firewall_id' => 0,
            'firewall_name' => null,
            'firewall_status' => null,
            'firewall_desc' => null,
            'firewall_rule_id' => $rule['id'],
            'firewall_rule_name' => $rule['name'],
            'protocol' => $rule['protocol'],
            'ip_version' => $rule['ip_version'],
            'source_ip_address' => $rule['source_ip_address'],
            'source_port' => $rule['source_port'],
            'destination_ip_address' => $rule['destination_ip_address'],
            'destination_port' => $rule['destination_port'],
            'action' => $rule['action'],
        ];
    }


    if ($cliMsg) {
        echo 'ok' . PHP_EOL;
        echo '更新DB...';
    }
    $db->execute('truncate table twcc_firewall');
    foreach ($firewalls as $key => $firewall) {
        $db->insert('twcc_firewall', $firewall);
    }

    if ($cliMsg)
        echo 'ok' . PHP_EOL;
    return true;
}

function updateSite($twcc, $cliMsg = false)
{
    global $db, $recordCount;

    $sites = $twcc->listSites();
    $recordCount += count($sites);
    if ($cliMsg) echo '發現站台共 ' . count($sites)  . ' 個' . PHP_EOL;

    if ($cliMsg) echo '取得詳細資料...';

    foreach ($sites as $key => &$_site) {
        $detail = $twcc->getSiteDetail($_site['id']);
        $_site = [
            "site_id" => $_site['id'],
            "site_name" => $_site['name'],
            "callback" => $_site['callback'],
            "create_time" => date('Y-m-d H:i:s', strtotime($_site['create_time'])),
            "progress" => $_site['progress'],
            "public_ip" => $_site['public_ip'],
            "solution_id" => $_site['solution'],
            "status" => $_site['status'],
            "is_preemptive" => $_site['is_preemptive'] ?: 0,
            "termination_protection" => $_site['termination_protection'] ?: 0,
        ];

        if (!empty($detail)) {
            $_site["site_desc"] = $detail['desc'];
            $_site["status_reason"] = $detail['status_reason'];
        }
    }
    unset($_site);

    if ($cliMsg) echo 'ok' . PHP_EOL . '更新DB...';

    $db->execute('truncate table twcc_site');
    foreach ($sites as $key => $site) {
        $db->insert('twcc_site', $site);
    }

    if ($cliMsg) echo 'ok' . PHP_EOL;
}

function updateServer(TWCCAdapter $twcc, $cliMsg = false)
{
    global $db, $recordCount;
    $servers = $twcc->listServers();
    if ($cliMsg) echo '發現Server共 ' . count($servers)  . ' 個' . PHP_EOL;
    $recordCount += count($servers);

    if ($cliMsg) echo '欄位整理...';
    foreach ($servers as $key => &$server) {

        $data = [
            "server_id" => $server['id'],
            "availability_zone" => $server['availability_zone'],
            "flavor" => $server['flavor'],
            "hostname" => $server['hostname'],
            "image" => $server['image'],
            "os" => $server['os'],
            "os_version" => $server['os_version'],
            "status" => $server['status'],
            "fqdn" => $server['fqdn'],
            "site_id" => $server['site'],

            // network
            "private_ip" => '',
            "network_id" => null,
            "network_name" => '',

            // static ip
            "public_ip" => '',
            "public_ip_id" => null,
            "public_ip_name" => '',
            // "public_ip_type" => '', // detail

            "waf_id" => 0,
            "waf_hostname" => '',
        ];

        // 內部網路
        if (!empty($server['private_nets'])) {
            $data['private_ip'] = [];
            $data['network_id'] = [];
            $data['network_name'] = [];
            foreach ($server['private_nets'] as $key => $value) {
                $data['private_ip'][] = $value['ip'];
                $data['network_id'][] = $value['id'];
                $data['network_name'][] = $value['name'];
            }
            $data['private_ip'] = implode(',', array_filter($data['private_ip']));
            $data['network_id'] = implode(',', array_filter($data['network_id']));
            $data['network_name'] = implode(',', array_filter($data['network_name']));
        }

        // 外部網路
        if (!empty($server['public_nets'])) {
            $data['public_ip'] = [];
            $data['public_ip_name'] = [];
            // $data['public_ip_id'] = []; // detail才有
            // $data['public_ip_type'] = []; // detail才有
            foreach ($server['public_nets'] as $key => $value) {
                $data['public_ip'][] = $value['ip'];
                $data['public_ip_name'][] = $value['name'];
                // $data['public_ip_id'][] = $value['id'];
                // $data['public_ip_type'][] = $value['type'];
            }
            $data['public_ip'] = implode(',', array_filter($data['public_ip']));
            $data['public_ip_name'] = implode(',', array_filter($data['public_ip_name']));
            // $data['public_ip_id'] = implode(',', $data['public_ip_id']);
            // $data['public_ip_type'] = implode(',', $data['public_ip_type']);
        }


        // $detail = $twcc->getServerDetail($server['id']);
        // if (!empty($detail)) {
        //     if (!empty($detail['waf'])) {
        //         $data["waf_id"] = $detail['waf']['id'];
        //         $data["waf_hostname"] = $detail['waf']['hostname'];
        //     }
        // }

        $server = $data;
    }
    unset($server);

    if ($cliMsg) echo 'ok' . PHP_EOL;
    if ($cliMsg) echo '更新DB...';

    $db->execute('truncate table twcc_server');
    foreach ($servers as $key => $server) {
        $db->insert('twcc_server', $server);
    }

    if ($cliMsg) echo 'ok' . PHP_EOL;
}


// =============================

$nowTime = date("Y-m-d H:i:s");
$status = 200;
echo "The " . $recordCount . " records have been inserted or updated into the twcc on " . $nowTime . PHP_EOL;

$error = $db->getErrorMessageArray();
if (!empty($error)) {
    return;
}

$table = "apis"; // 設定你想查詢資料的資料表
$condition = "class LIKE :class and name LIKE :name";
$apis = $db->query($table, $condition, $order_by = "1", $fields = "*", $limit = "", [':class' => '國網', ':name' => '國網相關資訊']);
$table = "api_status"; // 設定你想新增資料的資料表
$data_array = array();
$data_array['api_id'] = $apis[0]['id'];
$data_array['url'] = '';
$data_array['status'] = $status;
$data_array['data_number'] = $recordCount;
$data_array['updated_at'] = $nowTime;
$db->insert($table, $data_array);
