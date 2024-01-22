<?php
use gcb\api\RapixWebAPIAdapter;
require_once __DIR__ .'/../../vendor/autoload.php';

$db = Database::get();
$rapix = new RapixWebAPIAdapter();

// 同步CPE
$cpes = $rapix->fetchCPEAssets();
$key_array = array(
    array('input' => 'ID', 'output' => 'id', 'default_value' => 0),
    array('input' => 'Name', 'output' => 'name', 'default_value' => NULL),
    array('input' => 'Title', 'output' => 'title', 'default_value' => NULL),
    array('input' => 'UpdatedAt', 'output' => 'updated_at', 'default_value' => 0),
    array('input' => 'Part', 'output' => 'part', 'default_value' => NULL),
    array('input' => 'Vendor', 'output' => 'vendor', 'default_value' => NULL),
    array('input' => 'Product', 'output' => 'product', 'default_value' => NULL),
    array('input' => 'Version', 'output' => 'version', 'default_value' => NULL),
    array('input' => 'NumberOfCVEs', 'output' => 'number_of_cves', 'default_value' => 0),
    array('input' => 'CVSS_V3_Severity', 'output' => 'cvss_v3_severity', 'default_value' => NULL),
    array('input' => 'CVSS_V3_Score', 'output' => 'cvss_v3_score', 'default_value' => 0),
    array('input' => 'CVSS_V2_Severity', 'output' => 'cvss_v2_severity', 'default_value' => NULL),
    array('input' => 'CVSS_V2_Score', 'output' => 'cvss_v2_score', 'default_value' => 0),
);

$cpe_count = 0;
if (empty($cpes)) {
    echo "No target-data" . PHP_EOL;
    $cpe_nowTime = date("Y-m-d H:i:s");
    $cpe_status = 400;
} else {
    $table = "rapix_cpes";
    $key_column = "1";
    $id = "1";
    $db->delete($table, $key_column, $id);

    foreach ($cpes as $cpe) {
        $entry = array();

        foreach ($key_array as $key) {
            $entry[$key['output']] = empty($cpe[$key['input']]) ? $key['default_value'] : $cpe[$key['input']];
        }

        $db->insert($table, $entry);
        $cpe_count = $cpe_count + 1;
    }

    $cpe_nowTime = date("Y-m-d H:i:s");
    echo "The " . $cpe_count . " records have been inserted or updated into the rapix_cpes on " . $cpe_nowTime . PHP_EOL;
    $cpe_status = 200;
}

// 同步主機
$clients = $rapix->fetchClientList();
$key_array = array(
    array('input' => 'ID', 'output' => 'id', 'default_value' => 0),
    array('input' => 'ExternalIP', 'output' => 'external_ip', 'default_value' => 0),
    array('input' => 'IEEnvID', 'output' => 'ie_env_id', 'default_value' => 0),
    array('input' => 'InternalIP', 'output' => 'internal_ip', 'default_value' => 0),
    array('input' => 'IsOnline', 'output' => 'is_online', 'default_value' => 0),
    array('input' => 'LastActiveAt', 'output' => 'last_active_at', 'default_value' => NULL),
    array('input' => 'Name', 'output' => 'name', 'default_value' => NULL),
    array('input' => 'OSArch', 'output' => 'os_arch', 'default_value' => 0),
    array('input' => 'OSEnvID', 'output' => 'os_env_id', 'default_value' => 0),
    array('input' => 'OrgID', 'output' => 'org_id', 'default_value' => 0),
    array('input' => 'OrgName', 'output' => 'org_name', 'default_value' => NULL),
    array('input' => 'OwnerAssoc', 'output' => 'owner_assoc', 'default_value' => NULL),
    array('input' => 'UpdatedAt', 'output' => 'updated_at', 'default_value' => NULL),
    array('input' => 'UserName', 'output' => 'user_name', 'default_value' => NULL),
);

$count = 0;
if (empty($clients)) {
    echo "No target-data" . PHP_EOL;
    $nowTime = date("Y-m-d H:i:s");
    $status = 400;
} else {
    $table = "rapix_clients";
    $key_column = "1";
    $id = "1";
    $db->delete($table, $key_column, $id);

    foreach ($clients as $client) {
        $entry = array();

        foreach ($key_array as $key) {
            $entry[$key['output']] = empty($client[$key['input']]) ? $key['default_value'] : $client[$key['input']];
        }

        $db->insert($table, $entry);
        $count = $count + 1;
    }

    $nowTime = date("Y-m-d H:i:s");
    echo "The " . $count . " records have been inserted or updated into the rapix_clients on " . $nowTime . PHP_EOL;
    $status = 200;
}

// 同步CPE與主機對應
$maps = $rapix->fetchCPEIDAndClientIDMap();
$key_array = array(
    array('input' => 'cpe_id', 'output' => 'CPEID', 'default_value' => 0),
    array('input' => 'client_id', 'output' => 'ClientID', 'default_value' => 0),
);

$count = 0;
if (empty($maps)) {
    echo "No target-data" . PHP_EOL;
    $nowTime = date("Y-m-d H:i:s");
    $status = 400;
} else {
    $table = "rapix_cpe_client_map";
    $key_column = "1";
    $id = "1";
    $db->delete($table, $key_column, $id);

    foreach ($maps as $index => $map) {
        $entry = array();

        $entry['rapix_cpe_id'] = $map['CPEID'];
        foreach ($map['ClientID'] as $client_id) {
            $entry['rapix_client_id'] = $client_id;
            $entry['id'] = $count + 1;
            $db->insert($table, $entry);
            $count = $count + 1;
        }
    }

    $nowTime = date("Y-m-d H:i:s");
    echo "The " . $count . " records have been inserted or updated into the rapix_cpe_client_map on " . $nowTime . PHP_EOL;
    $status = 200;
}

// 同步單位
$count = 0;
$dataList = $rapix->fetchOU();
$key_aray = array(
    array('input' => 'id', 'output' => 'id', 'default_value' => 0),
    array('input' => 'parent_id', 'output' => 'parent_id', 'default_value' => 0),
    array('input' => 'oid', 'output' => 'oid', 'default_value' => ''),
    array('input' => 'dn', 'output' => 'dn', 'default_value' => ''),
    array('input' => 'title', 'output' => 'title', 'default_value' => ''),
    array('input' => 'responsible_ou', 'output' => 'responsible_ou', 'default_value' => ''),
    array('input' => 'ldap_ou', 'output' => 'ldap_ou', 'default_value' => ''),
    array('input' => 'depth', 'output' => 'depth', 'default_value' => ''),
    array('input' => 'count', 'output' => 'count', 'default_value' => 0),
    array('input' => 'tree_left', 'output' => 'tree_left', 'default_value' => 0),
    array('input' => 'tree_right', 'output' => 'tree_right', 'default_value' => 0),
    array('input' => 'vans_unit_name', 'output' => 'vans_unit_name', 'default_value' => ''),
    array('input' => 'vans_config_updated_at', 'output' => 'vans_config_updated_at', 'default_value' => NULL),
    array('input' => 'vans_return_code', 'output' => 'vans_return_code', 'default_value' => ''),
    array('input' => 'vans_return_at', 'output' => 'vans_return_at', 'default_value' => NULL),
    array('input' => 'vans_kb_return_code', 'output' => 'vans_kb_return_code', 'default_value' => ''),
    array('input' => 'vans_kb_return_at', 'output' => 'vans_kb_return_at', 'default_value' => NULL),
);
if (!empty($dataList)) {

    // 欄位整理
    foreach ($dataList as &$ou) {

        // created_at
        if (!empty($ou['created_at'])) {
            $ou['created_at'] = date('Y-m-d h:i:s', strtotime($ou['created_at']));
        }
        // updated_at
        if (!empty($ou['updated_at'])) {
            $ou['updated_at'] = date('Y-m-d h:i:s', strtotime($ou['updated_at']));
        }

        // OU
        if (empty($ou['dn']) || strpos($ou['dn'], 'tainan.gov.tw') !== false)
            continue;

        $ouParts = preg_replace('/\(.*?\)/si', '', $ou['dn']); // 去掉責任等級括號
        $ouParts = explode('/', $ouParts);

        if (in_array($ouParts[0], ['一級機關', '臺南市政府', '區公所']) && count($ouParts) >= 2) {
            $ou['responsible_ou'] = $ouParts[1];
        }

        if ($ouParts[0] == '智發中心')
            $ou['responsible_ou'] = '智發中心';
    }

    // 更新VANS各組織回報結果
    $orgStatus = $rapix->fetchVansOrgTreeStatus();
    if (isset($orgStatus['status']) && $orgStatus['status'] == 'success') {
        foreach ($orgStatus['data'] as $key => $org) {
            updateVansOrgReturnStatus($org, $vansReturnStatus);
        }
    }
    foreach ($vansReturnStatus as $item) {
        $arrIndex = array_key_first(array_filter($dataList, fn ($entry) => $entry['id'] = $item['id']));
        if (!empty($arrIndex)) {
            $dataList[$arrIndex] = $item + $dataList[$arrIndex];
        }
    }


    // remove all data
    $table = "rapix_ou";
    $key_column = "1";
    $id = "1";
    $db->delete($table, $key_column, $id);

    // 不知為啥會有一筆重複的ID
    $repeat = [];
    $repeatID = [];
    foreach ($dataList as $idx => $ou) {
        $repeat[$ou['id']] = isset($repeat[$ou['id']]) ? $repeat[$ou['id']] + 1 : 1;
        if ($repeat[$ou['id']] > 1)
            $repeatID[] = $idx;
    }
    $repeat = array_filter($repeat, fn ($n) => $n > 1);

    // insert data
    foreach ($dataList as $idx => $ou) {
        // 排除重複
        if (in_array($idx, $repeatID))
            continue;

        $db->insert($table, $ou);
        $count = $count + 1;
    }


    $nowTime = date("Y-m-d H:i:s");
    echo "The " . $count . " records have been inserted or updated into the rapix_ou on " . $nowTime . PHP_EOL;
    $status = 200;
} else {
    echo "No target-data" . PHP_EOL;
    $nowTime = date("Y-m-d H:i:s");
    $status = 400;
}

/** 更新VANS各組織回報結果 */
function updateVansOrgReturnStatus(array $org, &$vansReturnStatus = [])
{
    // sub org
    if (isset($org['Children'])) {
        foreach ($org['Children'] as $subOrg) {
            updateVansOrgReturnStatus($subOrg, $vansReturnStatus);
        }
    }

    // 沒有VANS相關屬性就跳過
    if (!isset($org['Attributes']['VANSConfig'])) return;
    // VANS相關屬性是空值的也跳過
    if (empty($org['Attributes']['VANSConfig']['APIKey'])) return;

    // 更新
    $vansReturnStatus[] =  [
        'id' => $org['ID'],
        'vans_unit_name' => isset($org['Attributes']['VANSConfig']['UnitName']) ? $org['Attributes']['VANSConfig']['UnitName'] : '',
        'vans_config_updated_at' =>  isset($org['Attributes']['VANSConfig']['UpdatedAt']) ? $org['Attributes']['VANSConfig']['UpdatedAt'] : null,
        'vans_return_code' => isset($org['Attributes']['VANSConfig']['ReturnCode']) ? $org['Attributes']['VANSConfig']['ReturnCode'] : '',
        'vans_return_at' => isset($org['Attributes']['VANSConfig']['ReportedAt']) ? $org['Attributes']['VANSConfig']['ReportedAt'] : null,
        'vans_kb_return_code' => isset($org['Attributes']['VANSConfig']['KBReturnCode']) ? $org['Attributes']['VANSConfig']['KBReturnCode'] : '',
        'vans_kb_return_at' =>  isset($org['Attributes']['VANSConfig']['KBReportedAt']) ? $org['Attributes']['VANSConfig']['KBReportedAt'] : null,
    ];
}



$error = $db->getErrorMessageArray();
if (!empty($error)) {
    return;
}

$table = "apis"; // 設定你想查詢資料的資料表
$condition = "class LIKE :class and name LIKE :name";
$apis = $db->query($table, $condition, $order_by = "1", $fields = "*", $limit = "", [':class' => 'vans', ':name' => '資訊資產']);
$table = "api_status"; // 設定你想新增資料的資料表
$data_array = array();
$data_array['api_id'] = $apis[0]['id'];
$data_array['url'] = "";
$data_array['status'] = $cpe_status;
$data_array['data_number'] = $cpe_count;
$data_array['updated_at'] = $cpe_nowTime;
$db->insert($table, $data_array);
