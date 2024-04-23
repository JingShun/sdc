<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$db = Database::get();
$loadData = load();


$table = "drip_ip_mac_used_list";
$key_column = "1";
$id = "1";
$db->delete($table, $key_column, $id);

$count = 0;
foreach ($loadData['rows'] as $data) {
    if (!empty($data[5]) && $data[5] != 'IP') {

        //var_dump($data);
        //return;

        $status['isOnline'] = trim($data[0]);
        $status['DetectorName'] = trim($data[2]);
        $status['DetectorIP'] = trim($data[32]);
        $status['DetectorGroup'] = trim($data[2]);
        $status['IP'] = trim($data[5]);
        $status['MAC'] = trim($data[6]);
        $status['IP_Block'] = trim($data[7]);
        $status['MAC_Block'] = trim($data[8]);
        $status['GroupName'] = trim($data[9]);
        $status['ClientName'] = trim($data[10]);
        $status['SwitchIP'] = trim($data[15]);
        $status['SwitchName'] = trim($data[16]);
        $status['PortName'] = trim($data[17]);
        $status['NICProductor'] = trim($data[13]);
        $status['LastOnlineTime'] = trim($data[26]);
        $status['LastOfflineTime'] = trim($data[27]);
        $status['IPMAC_Bind'] = trim($data[28]);
        $status['IP_Grant'] = trim($data[34]);
        $status['MAC_Grant'] = trim($data[35]);
        $status['IP_BlockReason'] = trim($data[44]);
        $status['MAC_BlockReason'] = trim($data[45]);
        $status['MemoByMAC'] = trim($data[48]);
        $status['MemoByIP'] = trim($data[65]);

        echo $status['SwitchName'] . '|' . $status['IP'] . PHP_EOL;

        $db->insert($table, $status);
        $count = $count + 1;
    }
}

$nowTime = date("Y-m-d H:i:s", $loadData['fileTime']);
echo "The " . $count . " records have been inserted or updated into the drip_ip_mac_used_list on " . $nowTime . "\n\r<br>";
$status = 200;

$error = $db->getErrorMessageArray();
if (!empty($error)) {
    return;
}

$table = "apis"; // 設定你想查詢資料的資料表
$condition = "class LIKE :class and name LIKE :name";
$apis = $db->query($table, $condition, $order_by = "1", $fields = "*", $limit = "", [':class' => 'ip管理', ':name' => '使用ip清單']);
$table = "api_status"; // 設定你想新增資料的資料表
$data_array = array();
$data_array['api_id'] = $apis[0]['id'];
$data_array['url'] = "";
$data_array['status'] = $status;
$data_array['data_number'] = $count;
$data_array['updated_at'] = $nowTime;
$db->insert($table, $data_array);



function load()
{
    $result = [
        'fileTime' => 0,
        'rows' => [],
    ];

    $inputFileNameList = array(
        __DIR__ . '/../../upload/drip/DrIP_IP_MAC_USED_IP_List_1.xls',
        __DIR__ . '/../../upload/drip/DrIP_IP_MAC_USED_IP_List_2.xls',
    );

    foreach ($inputFileNameList as $inputFileName) {
        // $inputFileName =  __DIR__ . '/../../upload/drip/DrIP_IP_MAC_USED_IP_List.xls';
        
        $result['fileTime'] = max($result['fileTime'], filemtime($inputFileName));


        /** Load $inputFileName to a Spreadsheet Object  **/
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);

        if (empty($spreadsheet)) {
            echo "The inputFile : " . $inputFileName . " can't been loaded \n\r<br>";
            exit;
        }


        $worksheet = $spreadsheet->getActiveSheet();

        foreach ($worksheet->getRowIterator() as $index => $row) {
            if ($index == 1) {
                continue;
            }
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            $result['rows'][] = $cells;
        }
    }


    return $result;
}
