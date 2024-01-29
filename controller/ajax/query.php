<?php
// input validation
$v1 = 0; $v2 = 0;

// Sanitizes data and converts strings to UTF-8 (if available), according to the provided field whitelist
$whitelist = array("key", "keyword", "page", "jsonConditions", "jsonSorts",  "type", "ap");
$_GET = $gump->sanitize($_GET, $whitelist); 

foreach ($_GET as $getKey => $val) {
	$$getKey = $val;
	if ($getKey == "jsonConditions" && $val == "[]") {
		$v1 = 1;	
	} elseif($getKey != "jsonConditions" && $val == "") {
		$v2 = 1;	
	}
}

if ($v1 && $v2) {
	echo "沒有輸入";
	return;
}

$jsonSortsMap = array('ascending' => 'ASC', 'descending' => 'DESC');

$page = isset($page) ? $page : 1;
$ap = isset($ap) ? $ap : 'html';

$jsonConditions = $gump->sanitize(json_decode(html_entity_decode($jsonConditions), true));
if (!empty($jsonSorts)) {
    $jsonSorts = $gump->sanitize(json_decode(html_entity_decode($jsonSorts), true));
}

$select = '*';
switch($type){
	case 'event':
		$condition_table = "security_event";
		$table = "security_event";
		$order_by = "EventID DESC, OccurrenceTime DESC";	
		break;
	case 'ncert':
		$condition_table = "security_ncert";
		$table = "security_ncert";
		$order_by = "id DESC, DiscoveryTime DESC";	
		break;
	case 'contact':
		$condition_table = "security_contact";
		//$table = "(SELECT * FROM security_contact UNION SELECT * FROM security_contact_custom)a";
		$table = "(SELECT a.*, b.rank FROM( SELECT * FROM security_contact UNION SELECT * FROM security_contact_custom ORDER by oid asc,person_type asc )a LEFT JOIN security_rank AS b ON a.oid = b.oid)v";
		$order_by = "oid, person_type";	
		break;
	case 'drip':
		$condition_table = "drip_client_list";
		$table = "drip_client_list";
		$order_by = "DetectorName, IP";	
		break;
	case 'gcb':
		$condition_table = "gcb_client_list";
		//$table = "(SELECT a.*,b.name as os_name,c.name as ie_name,ROUND(a.GsAll_1/a.GsAll_2*100,1) as GsPass FROM gcb_client_list as a LEFT JOIN gcb_os as b ON a.OSEnvID = b.id LEFT JOIN gcb_ie as c ON a.IEEnvID = c.id)A";
        $table = "(
		SELECT 
			a.*, b.name AS os_name, c.name AS ie_name, ROUND(a.GsAll_1/a.GsAll_2*100,1) AS GsPass, GROUP_CONCAT(e.name) AS cpe_names, GROUP_CONCAT(e.number_of_cves ORDER BY e.number_of_cves DESC) AS number_of_cves
		FROM 
			gcb_client_list AS a 
		LEFT JOIN 
			gcb_os AS b 
		ON 
			a.OSEnvID = b.id 
		LEFT JOIN 
			gcb_ie AS c 
		ON 
			a.IEEnvID = c.id
		LEFT JOIN
			rapix_cpe_client_map AS d
		ON 
			a.ID = d.rapix_client_id
		LEFT JOIN
			rapix_cpes AS e
		ON 
			e.id = d.rapix_cpe_id
		Group BY a.ID
		)A";


		if($keyword == 'ExternalIP' or $keyword == 'InternalIP') $key = ip2long($key);
		$order_by = "ID";	
		break;
	case 'wsus':
		$condition_table = "wsus_computer_status";
		$table = "wsus_computer_status";
		$order_by = "TargetID";	
		break;
	case 'antivirus':
		$condition_table = "antivirus_client_list";
		$table = "antivirus_client_list";
		$order_by = "GUID";	
		break;
	case 'edr':
		$condition_table = "edr_coreclouds";
		$table = "edr_coreclouds";
		$order_by = "id";	
		break;
	case 'twcc_server':
		$condition_table = "twcc_server";
		$table = "twcc_server INNER JOIN twcc_site ON twcc_server.site_id = twcc_site.site_id";
		$order_by = "twcc_server.public_ip DESC, twcc_server.site_id DESC, server_id DESC";	
        $select = "twcc_server.site_id, twcc_site.site_name, twcc_site.site_desc,
            twcc_site.`status` AS site_status,
            twcc_server.server_id, twcc_server.hostname,
            twcc_server.os, twcc_server.os_version,
            twcc_server.`status` AS server_status,
            twcc_server.image, twcc_server.flavor,
            twcc_server.private_ip, twcc_server.network_id, twcc_server.network_name,
            twcc_server.public_ip, twcc_server.public_ip_name,
            twcc_server.waf_id, twcc_server.waf_hostname";

        // 緩解因public_ip、private_ip欄位同時出現在不同資料表的錯誤
        if (empty($jsonConditions) && $key != 'any' && $keyword != 'all') {
            $jsonConditions[] = [
                'key' => $key,
                'keyword' => $keyword,
            ];
        }
        if (!empty($jsonConditions)) {
            // reset field name
            foreach ($jsonConditions as &$value) {
                switch ($value['keyword']) {
                    case 'public_ip':
                        $value['keyword'] = 'twcc_server.public_ip';
                        break;
                    case 'private_ip':
                        $value['keyword'] = 'twcc_server.private_ip';
                        break;
                    default:
                        break;
                }
            }
        }

		break;
    case 'twcc_fw':
        $condition_table = "twcc_firewall";
        $table = "twcc_firewall LEFT JOIN ( SELECT firewall_id,network_id,network_name,cidr,status AS network_status FROM twcc_network ) t1 ON twcc_firewall.firewall_id = t1.firewall_id AND t1.firewall_id!=0";
        $order_by = "twcc_firewall.firewall_name DESC, twcc_firewall.firewall_rule_order";
        $select = "twcc_firewall.firewall_id,
            twcc_firewall.firewall_name,
            twcc_firewall.firewall_status,
            twcc_firewall.firewall_desc,
            twcc_firewall.firewall_rule_id,
            twcc_firewall.firewall_rule_order,
            twcc_firewall.firewall_rule_name,
            twcc_firewall.protocol,
            twcc_firewall.source_ip_address,
            twcc_firewall.source_port,
            twcc_firewall.destination_ip_address,
            twcc_firewall.destination_port,
            twcc_firewall.action,
            t1.network_id,
            t1.network_name,
            t1.cidr,
            t1.network_status";
        break;
	default:
        echo "no target type";
        return;
		break;
}

// get conditions
$data_array = [];
if (!empty($jsonConditions)) {
	$condition = "";
	foreach ($jsonConditions as $val) {
		if ($val['keyword'] == "all") {
			$res = $db->getFullTextSearchCondition($condition_table, $val['key']);
			$one_condition = "(".$res['condition'].") "; 
			$data_array = array_merge($data_array, $res['data']);
		} else {
			$one_condition = $val['keyword']." LIKE ?";
			$data_array[] = "%".$val['key']."%"; 
		}
		$condition = $condition." AND ".$one_condition;
	}
	$condition = substr($condition,4);
} else {
	if ($keyword == 'all' && $key == 'any') {
        $condition = "1 = ?";
        $data_array[] = 1;
    } elseif ($keyword == "all" && $key != 'any') {
		$res = $db->getFullTextSearchCondition($condition_table, $key);
		$condition = "(".$res['condition'].") "; 
		$data_array = $res['data'];
    } else {
		$condition = $keyword." LIKE ?";
		$data_array[] = "%".$key."%"; 
	}
}

// get order_by
if (!empty($jsonSorts)) {
    if (array_key_exists($jsonSorts['sort'], $jsonSortsMap)) {
        $order_by = $jsonSorts['label'] . " " . $jsonSortsMap[$jsonSorts['sort']];
    }
}

$limit = 10;
$links = 4;
$query = "SELECT {$select} FROM {$table} WHERE {$condition} ORDER BY {$order_by}";

$Paginator = new Paginator($query, $data_array);
$entries = $Paginator->getData($limit, $page, $data_array);
$last_num_rows = $Paginator->getTotal();

$pageAttr = array();	

if ($ap=='csv') {
    $total_entries = $db->execute($query, $data_array);
	$arrs = array();
    switch($type){
		case 'event': 
			break;
		case 'ncert': 
			break;
		case 'contact': 
			break;
		case 'drip': 
			foreach($total_entries as $entry) {
                $arr = array();
				foreach($entry as $key => $val){
					$arr[$key] = $val;
				}
				array_push($arrs,$arr);	
			}
			array_to_csv_download($arrs, "export.csv", ";"); 	
			break;
		case 'gcb': 
			break;
		case 'wsus': 
			break;
		case 'antivirus': 
			break;
        case 'twcc_server': 
        case 'twcc_fw': 
			foreach($total_entries as $entry) {
                $arr = array();
				foreach($entry as $key => $val){
					$arr[$key] = $val;
				}
				array_push($arrs,$arr);	
			}
			array_to_csv_download($arrs, "export.csv", ";"); 	
            break;
	}
} elseif($ap=='html') { 
?>
	<?php if ($last_num_rows == 0): ?>
		很抱歉，該分類目前沒有資料！
	<?php else: ?>
		該分類共搜尋到 <?=$last_num_rows?> 筆資料！
		<?php switch ($type):
                case "event": ?> 
                <?php
                $icon_map = array(
                    'Status' => array('已結案' => 'green check circle icon', '未完成' => 'exclamation circle icon')
                );
                ?>
                <div class='ui relaxed divided list'>
                <?php foreach($entries->data as $event): ?>
                    <div class='item'>
                        <div class='content'>
                            <a>
                            <i class="<?=$icon_map['Status'][$event['Status']]?>"></i>
                            <?=date_format(new DateTime($event['OccurrenceTime']),'Y-m-d')?>&nbsp;&nbsp;
                            <?=$event['Status']?>&nbsp;&nbsp;
                            <span style='background:#fde087'><?=$event['EventTypeName']?></span>&nbsp;&nbsp;
                            <?=$event['Location']?>&nbsp;&nbsp;
                            <span style='background:#DDDDDD'><?=$event['IP']?></span>&nbsp;&nbsp;
                            <?=$event['DeviceOwnerName']?>&nbsp;&nbsp;
                            <?=$event['DeviceOwnerPhone']?>&nbsp;&nbsp;
                            <i class='angle down icon'></i>
                            </a>
                            <div class='description'>
                                <ol>
                                    <li>序號: <?=$event['EventID']?></li>
                                    <li>結案狀態: <?=$event['Status']?></li>
                                    <li>發現日期: <?=date_format(new DateTime($event['OccurrenceTime']),'Y-m-d')?></li>
                                    <li>資安事件類型: <?=$event['EventTypeName']?></li>
                                    <li>位置: <?=$event['Location']?></li>
                                    <li>電腦IP: <?=$event['IP']?></li>
                                    <li>封鎖原因: <?=$event['BlockReason']?></li>
                                    <li>設備類型: <?=$event['DeviceTypeName']?></li>
                                    <li>電腦所有人姓名: <?=$event['DeviceOwnerName']?></li>
                                    <li>電腦所有人分機: <?=$event['DeviceOwnerPhone']?></li>
                                    <li>機關: <?=$event['AgencyName']?></li>
                                    <li>單位: <?=$event['UnitName']?></li>
                                    <li>處理日期(國眾): <?=$event['NetworkProcessContent']?></li>
                                    <li>處理日期(三佑科技): <?=$event['MaintainProcessContent']?></li>
                                    <li>處理日期(京稘或中華SOC): <?=$event['AntivirusProcessContent']?></li>
                                    <li>未能處理之原因及因應方式: <?=$event['UnprocessedReason']?></li>
                                    <li>備註: <?=$event['Remarks']?></li>
                                </ol>
                                <button type="button" class="ui button edit" key="<?=$event['EventID']?>">Edit</button>
                                <button type="button" class="ui button delete" key="<?=$event['EventID']?>">Delete</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
                </div>
		        <?php break; ?>
			<?php case "ncert": ?> 
                <?php
                $icon_map = array(
                    'Status' => array('已結案' => 'green check circle icon', '未完成' => 'exclamation circle icon')
                );
                $label_map = array( 
                    'NccstPT' => array('是' => '<div class="ui label">攻防</div>', '否' => '', '' => '')
                );
                ?>
                <div class='ui relaxed divided list'>
                <?php foreach($entries->data as $incident): ?>
                    <div class='item'>
                    <div class='content'>
                        <a>
                        <i class="<?=$icon_map['Status'][$incident['Status']]?>"></i>
                        <?=date_format(new DateTime($incident['DiscoveryTime']),'Y-m-d') ?>&nbsp;&nbsp;
                        <?=$incident['Status'] ?>&nbsp;&nbsp;
                        <span style='background:#DDDDDD'><?=$incident['ImpactLevel'] ?></span>&nbsp;&nbsp;
                        <?=$label_map['NccstPT'][$incident['NccstPT']]?>
                        <?=$incident['Classification'] ?>&nbsp;&nbsp;
                        <span style='background:#fde087'><?=$incident['PublicIP'] ?> </span>&nbsp;&nbsp;
                        <?=$incident['OrganizationName'] ?>
                        <i class='angle down icon'></i>
                        </a>
                        <div class='description'>
                            <ol>
                            <li>編號:  <?=$incident['id'] ?></li>
                            <li>結案狀態: <?=$incident['Status'] ?></li>
                            <li>事件編號: <?=$incident['NccstID'] ?></li>
                            <li>行政院攻防演練: <?=$incident['NccstPT'] ?></li>
                            <li>攻防演練衝擊性: <?=$incident['NccstPTImpact'] ?></li>
                            <li>機關名稱: <?=$incident['OrganizationName'] ?></li>
                            <li>聯絡人: <?=$incident['ContactPerson'] ?></li>
                            <li>電話: <?=$incident['Tel'] ?></li>
                            <li>電子郵件: <?=$incident['Email'] ?></li>
                            <li>資安維護廠商: <?=$incident['SponsorName'] ?></li>
                            <li>對外IP或網址: <?=$incident['PublicIP'] ?></li>
                            <li>使用用途: <?=$incident['DeviceUsage'] ?></li>
                            <li>作業系統: <?=$incident['OperatingSystem'] ?></li>
                            <li>入侵網址: <?=$incident['IntrusionURL'] ?></li>
                            <li>影響等級: <?=$incident['ImpactLevel'] ?></li>
                            <li>事故分類: <?=$incident['Classification'] ?></li>
                            <li>事故說明: <?=$incident['Explaination'] ?></li>
                            <li>影響評估: <?=$incident['Evaluation'] ?></li>
                            <li>應變措施: <?=$incident['Response'] ?></li>
                            <li>解決辦法/結報內容: <?=$incident['Solution'] ?></li>
                            <li>發現時間: <?=$incident['DiscoveryTime'] ?></li>
                            <li>通報時間: <?=$incident['InformTime'] ?></li>
                            <li>修復時間: <?=$incident['RepairTime'] ?></li>
                            <li>審核機關審核時間: <?=$incident['TainanGovVerificationTime'] ?></li>
                            <li>技服中心審核時間: <?=$incident['NccstVerificationTime'] ?></li>
                            <li>通報結報時間: <?=$incident['FinishTime'] ?></li>
                            <li>通報執行時間(時: 分): <?=$incident['InformExecutionTime'] ?></li>
                            <li>結案執行時間(時: 分): <?=$incident['FinishExecutionTime'] ?></li>
                            <li>中華SOC複測結果: <?=$incident['SOCConfirmation'] ?></li>
                            <li>改善計畫提報日期: <?=$incident['ImprovementPlanTime'] ?></li>
                            <li>通報單ID: <?=$incident['Incident_id'] ?></li>
                            </ol>
                        </div>
                        </div>
                    </div>
                <?php endforeach ?>
                </div>
				<?php break; ?>
			<?php case "contact": ?>
                <?php 
                $condition = $condition." GROUP BY oid";
                $fields = "oid";
                $db->query($table, $condition, $order_by = "1", $fields, $limit = "", $data_array);
                $oid_num = $db->getLastNumRows();
		        ?>
                該分類共搜尋到 <?=$oid_num?> 個機關！
                <div class='ui relaxed divided list'>
                <?php foreach($entries->data as $contact): ?>
                    <?php $text_ext = empty($contact['ext']) ? "" : "#" . $contact['ext']; ?>
                    <div class='item'>
                    <div class='content'>
                        <a>
                        <?=$contact['organization']?>&nbsp;&nbsp;
                        <?php if( !empty($contact['rank'] )) ?><span style='color:#f80000'><?=$contact['rank']?></span>&nbsp;&nbsp;
                        <?=$contact['person_name']?>&nbsp;&nbsp;
                        <span style='background:#fde087'><?=$contact['person_type']?></span>&nbsp;&nbsp;
                        <?=$contact['email']?>&nbsp;&nbsp;
                        <span style='background:#DDDDDD'><?=$contact['tel'] . $text_ext?></span>&nbsp;&nbsp;
                        <i class='angle down icon'></i>
                        </a>
                        <div class='description'>
                            <ol>
                            <li>序號: <?=$contact['id']?></li>
                            <li>OID: <?=$contact['oid']?></li>
                            <li>資安責任等級: <?=$contact['rank']?></li>
                            <li>機關名稱: <?=$contact['organization']?></li>
                            <li>單位名稱: <?=$contact['unit']?></li>
                            <li>姓名: <?=$contact['person_name']?></li>
                            <li>職稱: <?=$contact['position']?></li>
                            <li>資安聯絡人類型: <?=$contact['person_type']?></li>
                            <li>地址: <?=$contact['address']?></li>
                            <li>電話: <?=$contact['tel']?></li>
                            <li>分機: <?=$contact['ext']?></li>
                            <li>傳真: <?=$contact['fax']?></li>
                            <li>email: <?=$contact['email']?></li>
                            </ol>
                        </div>
                        </div>
                    </div>
                <?php endforeach ?>
                </div>
                <?php break; ?>
			<?php case "drip": ?>
                <?php
                $state_icon_map = array( 
                    'type' => array('computer' => 'desktop icon', 'device' => 'hdd icon'),
                    'ad' => array('outline circle icon', 'yellow circle icon'),
                    'gcb' => array('outline circle icon', 'green circle icon'),
                    'wsus' => array('outline circle icon', 'red circle icon'),
                    'antivirus' => array('outline circle icon', 'blue circle icon'),
                    'edr' => array('outline circle icon', 'brown circle icon')
                );
                $condition = $condition." AND type LIKE 'computer' ";
                $db->query($table, $condition, $order_by = "1", $fields = "*", $limit = "", $data_array);
                $pc_num = $db->getLastNumRows();
                $device_num = $last_num_rows - $pc_num;
		        ?>
                (含 <?=$pc_num?> 個公務電腦, <?=$device_num?> 個設備)
                <div class='ui relaxed divided list'>
                <?php foreach($entries->data as $client): ?>
                    <div class='item'>
                        <div class='content'>
                            <a>
                                <i class="<?=$state_icon_map['ad'][$client['ad']]?>"></i>
                                <i class="<?=$state_icon_map['gcb'][$client['gcb']]?>"></i>
                                <i class="<?=$state_icon_map['wsus'][$client['wsus']]?>"></i>
                                <i class="<?=$state_icon_map['antivirus'][$client['antivirus']]?>"></i>
                                <i class="<?=$state_icon_map['edr'][$client['edr']]?>"></i>
                                <?=$client['DetectorName']?>&nbsp;&nbsp;
                                <span style='background:#fde087'><?=$client['IP']?></span>&nbsp;&nbsp;
                                <div class="ui label">
                                    <i class="<?=$state_icon_map['type'][$client['type']]?>"></i>
                                    <?=$client['ClientName']?>
                                    <?php if(!empty($client['OrgName'])): ?>
                                        <div class="detail">
                                            <i class="sitemap icon"></i>
                                            <?=$client['OrgName']?>
                                        </div>
                                    <?php endif ?>
                                </div>
                                <?=$client['Owner']?>&nbsp;&nbsp;
                                <?php if(!empty($client['UserName'])): ?>
                                    <div class="ui grey label">
                                        <i class="user icon"></i>
                                        <?=$client['UserName']?>
                                        <?php if(!empty($client['UserOrgName'])): ?>
                                            <div class="detail">
                                                <i class="sitemap icon"></i>
                                                <?=$client['UserOrgName']?>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                <?php endif ?> 
                                <i class='angle down icon'></i>
                            </a>
                            <div class='description'>
                                <ol>
                                <li>設備類型: <?=$client['type']?></li>
                                <li>內網IP: <?=$client['IP']?></li>
                                <li>MAC位址: <?=$client['MAC']?></li>
                                <li>設備名稱: <?=$client['ClientName']?></li>
                                <li>群組名稱: <?=$client['GroupName']?></li>
                                <li>網卡製造商: <?=$client['NICProductor']?></li>
                                <li>偵測器名稱: <?=$client['DetectorName']?></li>
                                <li>偵測器IP: <?=$client['DetectorIP']?></li>
                                <li>偵測器群組: <?=$client['DetectorGroup']?></li>
                                <li>交換器名稱: <?=$client['SwitchName']?></li>
                                <li>連接埠名稱: <?=$client['PortName']?></li>
                                <li>最後上線時間: <?=$client['LastOnlineTime']?></li>
                                <li>最後下線時間: <?=$client['LastOfflineTime']?></li>
                                <li>IP封鎖原因: <?=$client['IP_BlockReason']?></li>
                                <li>MAC封鎖原因: <?=$client['MAC_BlockReason']?></li>
                                <li>備註ByIP: <?=$client['MemoByIP']?></li>
                                <li>備註ByMac: <?=$client['MemoByMAC']?></li>
                                <li>ad安裝: <?=$client['ad']?></li>
                                <li>gcb安裝: <?=$client['gcb']?></li>
                                <li>wsus安裝: <?=$client['wsus']?></li>
                                <li>antivirus安裝: <?=$client['antivirus']?></li>
                                <li>edr安裝: <?=$client['edr']?></li>
                                <li>OrgName: <?=$client['OrgName']?></li>
                                <li>Owner: <?=$client['Owner']?></li>
                                <li>UserName: <?=$client['UserName']?></li>
                                <li>UserOrgName: <?=$client['UserOrgName']?></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
                </div>
                <?php break; ?>
			<?php case "gcb": ?>
                <?php 
                $GsStatMap = array(
                    '0' => '未套用',
                    '1' => '已套用',
                    '-1' => '套用失敗',
                    '2' => '還原成功',
                    '-2' => '還原失敗'
                );                
                $icon_map = array(
                    'IsOnline' =>  array('1' => 'green circle icon', '' => 'circle outline icon')
                );
                ?>
                <div class='ui relaxed divided list'>
                <?php foreach($entries->data as $client): ?>
                    <div class='item'>
                    <div class='content'>
                        <a>
                        <i class="<?=$icon_map['IsOnline'][$client['IsOnline']]?>"></i>
                        <?=$client['Name']?>&nbsp;&nbsp;
                        <span style='background:#fde087'><?=$client['OrgName']?></span>&nbsp;&nbsp;
                        <?=$client['UserName']?>&nbsp;&nbsp;
                        <?=$client['Owner']?>&nbsp;&nbsp;
                        <span style='background:#DDDDDD'><?=long2ip($client['InternalIP'])?></span>&nbsp;&nbsp;
                        <?=$client['os_name']?>&nbsp;&nbsp;
                        <span style='background:#fbc5c5'><?=$client['GsPass']?>%</span>&nbsp;&nbsp;
                        <i class='angle down icon'></i>
                        </a>
                        <div class='description'>
                            <ol>
								<li><a href='/ajax/gcb_detail/?action=detail&id=<?=$client['ID']?>' target='_blank'>序號: <?=$client['ID']?>(用戶端資訊)&nbsp;<i class='external alternate icon'></i></a></li>
								<li>外部IP: <?=long2ip($client['ExternalIP'])?></li>
								<li>內部IP: <?=long2ip($client['InternalIP'])?></li>
								<li>電腦名稱: <?=$client['Name']?></li>
								<li>單位名稱: <?=$client['OrgName']?></li>
								<li>使用者帳號: <?=$client['UserName']?></li>
								<li>使用者名稱: <?=$client['Owner']?></li>
								<li>OS: <?=$client['os_name']?></li>
								<li>OS位元: <?=$client['OSArch']?></li>
								<li>IE: <?=$client['ie_name']?></li>
								<li>是否上線: <?=$client['IsOnline']?></li>
								<li>gcb總掃描數: <?=$client['GsAll_2']?></li>
								<li>gcb總通過數[包含例外]: <?=$client['GsAll_1']?></li>
								<li>gcb總通過數[未包含例外]: <?=$client['GsAll_0']?></li>
								<li>gcb例外數量: <?=$client['GsExcTot']?></li>
								<li>gcb通過率: <?=$client['GsPass']?>%</li>
								<li><a href='/ajax/gcb_detail/?action=gscan&id=<?=$client['GsID']?>' target='_blank'>gcb掃描編號: <?=$client['GsID']?>(掃描結果資訊)&nbsp;<i class='external alternate icon'></i></a></li>
								<li>gcb派送編號: <?=$client['GsSetDeployID']?></li>
								<li>gcb狀態: <?=$GsStatMap[$client['GsStat']]?></li>
								<li>gcb回報時間: <?=$client['GsUpdatedAt']?></li>
								<li>資訊資產: 
									<?php $cpe_array = explode(",", $client['cpe_names']); ?>
									<?php $number_of_cves_array = explode(",", $client['number_of_cves']); ?>
									<?php foreach($cpe_array as $index => $cpe): ?>
                                        <?php if($number_of_cves_array[$index] == 0): ?>
                                            <div class='ui label'>
                                        <?php else: ?>
                                            <div class='ui brown label'>
                                        <?php endif ?>
                                            <?=$cpe?>
                                            <div class="detail">
                                                <i class="bug icon"></i>
                                                <?=$number_of_cves_array[$index]?>
                                            </div>
                                        </div>
									<?php endforeach ?>
								</li>
                            </ol>
                        </div>
                    </div>
                    </div>
                <?php endforeach ?>
                </div>
                <?php break; ?>
			<?php case "wsus": ?>
                <div class='ui relaxed divided list'>
                    <?php foreach($entries->data as $index => $client): ?>
                        <?php
                        $table = "wsus_computer_updatestatus_kbid";
                        $order_by = "ID";
                        $condition = "TargetID = :TargetID AND UpdateState = :UpdateState";
                        $data_array = [':TargetID'=>$client['TargetID'], ':UpdateState'=>'NotInstalled'];
                        $notinstalled_kb = $db->query($table, $condition, $order_by, $fields = "*", $kb_limit = "",$data_array);
                        $data_array = [':TargetID'=>$client['TargetID'],':UpdateState'=>'Failed'];
                        $failed_kb = $db->query($table, $condition, $order_by, $fields = "*", $kb_limit = "", $data_array);
                        ?>
                        <div class='item'>
                            <div class='content'>
                                <a>
                                <?=strtoupper(str_replace(".tainan.gov.tw","",$client['FullDomainName']))?>&nbsp;&nbsp;
                                <span style='background:#fde087'><?=$client['IPAddress']?></span>&nbsp;&nbsp;
                                <span style='background:#DDDDDD'><?=$client['NotInstalled']?></span>&nbsp;&nbsp;
                                <span style='background:#fbc5c5'><?=$client['Failed']?></span>&nbsp;&nbsp;
                                <?=$client['OSDescription']?>
                                <i class='angle down icon'></i>
                                </a>
                                <div class='description'>
                                    <ol>
                                    <li>序號: <?=$client['TargetID']?></li>
                                    <li>電腦名稱: <?=strtoupper(str_replace(".tainan.gov.tw","",$client['FullDomainName']))?></li>
                                    <li>內網IP: <?=$client['IPAddress']?></li>
                                    <li>未知更新數量: <?=$client['Unknown']?></li>
                                    <li>未安裝更新數量: <?=$client['NotInstalled']?></li>
                                    <?php foreach($notinstalled_kb as $kb): ?>
                                        <strong>KB<?=$kb['KBArticleID']?></strong>
                                    <?php endforeach ?>
                                    <li>已下載更新數量: <?=$client['Downloaded']?></li>
                                    <li>已安裝更新數量: <?=$client['Installed']?></li>
                                    <li>安裝失敗更新數量: <?=$client['Failed']?></li>
                                    <?php foreach($failed_kb as $kb): ?>
                                        <strong>KB<?=$kb['KBArticleID']?></strong>
                                    <?php endforeach ?>	
                                    <li>已安裝待重開機更新數量: <?=$client['InstalledPendingReboot']?></li>
                                    <li>上次狀態回報日期: <?=dateConvert($client['LastReportedStatusTime'])?></li>
                                    <li>上次更新重開機日期: <?=dateConvert($client['LastReportedRebootTime'])?></li>
                                    <li>上次可用更新日期: <?=dateConvert($client['EffectiveLastDetectionTime'])?></li>
                                    <li>上次同步日期: <?=dateConvert($client['LastSyncTime'])?></li>
                                    <li>上次修改日期: <?=dateConvert($client['LastChangeTime'])?></li>
                                    <li>上次同步結果: <?=$client['LastSyncResult']?></li>
                                    <li>製造商: <?=$client['ComputerMake']?></li>
                                    <li>型號: <?=$client['ComputerModel']?></li>
                                    <li>作業系統: <?=$client['OSDescription']?></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
                <?php break; ?>
			<?php case "antivirus": ?>
                <?php
                $icon_map = array(
                    'ConnectionState' =>  array('線上' => 'green circle icon', '離線' => 'circle outline icon')
                );
                ?>
                
                <div class='ui relaxed divided list'>
                <?php foreach($entries->data as $client): ?>
                    <div class='item'>
                        <div class='content'>
                            <a>
                            <i class="<?=$icon_map['ConnectionState'][$client['ConnectionState']]?>"></i>   
                            <?=$client['ClientName']?>&nbsp;&nbsp;
                            <span style='background:#fde087'><?=$client['IP']?></span>&nbsp;&nbsp;
                            <span style='background:#fbc5c5'><?=$client['OS']?></span>&nbsp;&nbsp;
                            <?=$client['VirusNum']?>&nbsp;&nbsp;
                            <?=$client['SpywareNum']?>&nbsp;&nbsp;
                            <span style='background:#DDDDDD'><?=$client['VirusPatternVersion']?></span>&nbsp;&nbsp;
                            <?=$client['LogonUser']?>&nbsp;&nbsp;
                            <i class='angle down icon'></i>
                            </a>
                            <div class='description'>
                                <ol>
                                <li>設備名稱: <?=$client['ClientName']?></li>
                                <li>內網IP: <?=$client['IP']?></li>
                                <li>網域階層: <?=$client['DomainLevel']?></li>
                                <li>連線狀態: <?=$client['ConnectionState']?></li>
                                <li>GUID: <?=$client['GUID']?></li>
                                <li>掃描方式: <?=$client['ScanMethod']?></li>
                                <li>DLP狀態: <?=$client['DLPState']?></li>
                                <li>病毒數量: <?=$client['VirusNum']?></li>
                                <li>間諜程式數量: <?=$client['SpywareNum']?></li>
                                <li>作業系統: <?=$client['OS']?></li>
                                <li>位元版本: <?=$client['BitVersion']?></li>
                                <li>MAC位址: <?=$client['MAC']?></li>
                                <li>設備版本: <?=$client['ClientVersion']?></li>
                                <li>病毒碼版本: <?=$client['VirusPatternVersion']?></li>
                                <li>登入使用者: <?=$client['LogonUser']?></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
                </div>
                <?php break; ?>
			<?php case "edr": ?>
               <?php
                $state_icon_map = array(
                    '已移除' => 'close icon',
                    '連線中' => 'green circle icon',
                    '離線中' => 'circle outline icon',
                    '暫停監控' => 'pause icon',
                    'INIT' => 'dot circle outline icon'
                );
                $edrs = array();
                foreach($entries->data as $entry){
                    $table = "edr_corecloud_ips";
                    $order_by = "";
                    $condition = "edr_corecloud_id = :edr_corecloud_id";
                    $data_array = [':edr_corecloud_id' => $entry['id'] ];
                    $ip_array = $db->query($table, $condition, $order_by, $fields = "*", $limit = "", $data_array);
                    $edr = array();
                    $edr['icon'] = array_key_exists($entry['state'], $state_icon_map) ? $state_icon_map[$entry['state']]: ""; 
                    $edr['id'] = $entry['id']; 
                    $edr['ip_array'] = $ip_array; 
                    $edr['host_name'] = $entry['host_name']; 
                    $edr['state'] = $entry['state']; 
                    $edr['last_online_at'] = $entry['last_online_at']; 
                    $edr['os'] = $entry['os']; 
                    $edr['finished_number'] = $entry['finished_number']; 
                    $edr['unfinished_number'] = $entry['unfinished_number']; 
                    $edr['total_number'] = $entry['total_number']; 
                    $edr['hidden_state'] = $entry['hidden_state']; 
                    array_push($edrs, $edr);
                }
                ?> 
                <div class='ui relaxed divided list'>
                    <?php foreach($edrs as $entry): ?>
                        <div class='item'>
                            <div class='content'>
                                <a>
                                    <i class='<?=$entry['icon']?>'></i>&nbsp;&nbsp;
                                    <?=$entry['host_name']?>&nbsp;&nbsp;
                                    <span style='background:#fde087'><?=$entry['state']?></span>&nbsp;&nbsp;
                                    <?=$entry['os']?>&nbsp;&nbsp;
                                    <?php foreach($entry['ip_array'] as $ip_entry): ?>
                                        <span style='background:#DDDDDD'><?=$ip_entry['ip']?></span>&nbsp;&nbsp;
                                    <?php endforeach ?>
                                    <?=$entry['total_number']?>&nbsp;&nbsp;
                                    <i class='angle down icon'></i>
                                </a>
                                <div class='description'>
                                    <ol>
                                        <li>序號: <?=$entry['id']?></li>
                                        <li>IP: 
                                            <?php foreach($entry['ip_array'] as $ip_entry): ?>
                                                <?=$ip_entry['ip']?>&nbsp;
                                            <?php endforeach ?>
                                         </li>  
                                        <li>主機名稱: <?=$entry['host_name']?></li>
                                        <li>監控狀態: <?=$entry['state']?></li>
                                        <li>最後上線時間: <?=$entry['last_online_at']?></li>
                                        <li>OS: <?=$entry['os']?></li>
                                        <li>已關單數: <?=$entry['finished_number']?></li>
                                        <li>處理中單數: <?=$entry['unfinished_number']?></li>
                                        <li>總單數: <?=$entry['total_number']?></li>
                                        <li>隱藏狀態: <?=$entry['hidden_state']?></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
                <?php break; ?>
			<?php case "twcc_server": ?>
                <?php
                ?>
                
                <div class='ui relaxed divided list'>
                <?php foreach($entries->data as $server): ?>
                    <div class='item'>
                        <div class='content'>
                            <a>
                            <i class="<?=$server['site_status'] == 'Ready' ? 'green circle icon' : 'circle outline icon' ?>"></i>   
                            <?=$server['site_name']?>&nbsp;&nbsp;
                            <?=$server['os']?>&nbsp;&nbsp;
                            <span style='background:#fbc5c5'><?=$server['private_ip']?></span>&nbsp;&nbsp;
                            <span style='background:#fde087'><?=$server['public_ip'] ?: '-' ?></span>&nbsp;&nbsp;
                            <?=$server['network_name']?>&nbsp;&nbsp;
                            <span style='background:#DDDDDD'><?=$server['flavor']?></span>&nbsp;&nbsp;
                            <?=$server['site_desc']?>&nbsp;&nbsp;
                            <i class='angle down icon'></i>
                            </a>
                            <div class='description'>
                                <ol>
                                    <li>站台名稱: <?=$server['site_name']?></li>
                                    <li>站台描述: <?=$server['site_desc']?></li>
                                    <li>主機名稱: <?=$server['hostname']?></li>
                                    <li>os: <?=$server['os']?></li>
                                    <li>站台狀態: <?=$server['site_status']?></li>
                                    <li>主機狀態: <?=$server['server_status']?></li>
                                    <li>對外ip: <?=$server['public_ip'] ?: '-' ?></li>
                                    <li>內部IP: <?=$server['private_ip']?></li>
                                    <li>network_name: <?=$server['network_name']?></li>
                                    <li>public_ip_name: <?=$server['public_ip_name']?></li>
                                    <li>waf_hostname: <?=$server['waf_hostname']?></li>
                                    <li>VM型號: <?=$server['flavor']?></li>
                                    <li>映像檔: <?=$server['image']?></li>
                                    <li>os版本: <?=$server['os_version']?></li>
                                    <li>server_id: <?=$server['server_id']?></li>
                                    <li>site_id: <?=$server['site_id']?></li>
                                    <li>network_id: <?=$server['network_id']?></li>
                                    <li>waf_id: <?=$server['waf_id']?></li>
                                
                                    <li><button type="submit" class="ui button tiny purple" onclick="twcc_fw(<?=$server['server_id']?>);$(this).addClass('disabled');">網路規則</button></li>
                                </ol>

                                <div id="fwInfo<?=$server['server_id']?>">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
                <script>$('.ui.accordion').accordion('refresh');</script>
                </div>
                <?php break; ?>
            <?php case "twcc_fw": ?>
                <?php
                ?>
                
                <div class='ui relaxed divided list'>
                FW狀態 / 網段狀態 / FW名稱 / 描述 / 順序 / 協定 / <span style='background:#fbc5c5'>來源</span> / 來源Port / <span style='background:#DDDDDD'>目的</span> / 目的Port / <span style='background:#fde087'>動作</span> / 網段名稱
                <?php foreach($entries->data as $fw): ?>
                    <div class='item'>
                        <div class='content'>
                        <a>
                            <i class="<?= $fw['firewall_status'] == 'ACTIVE' ? 'green circle icon' : 'circle outline icon' ?>"></i>
                            <i class="<?= $fw['network_status'] == 'ACTIVE' ? 'green circle icon' : 'circle outline icon' ?>"></i>
                            <?= $fw['firewall_name'] ?: 'n/a' ?>&nbsp;&nbsp;
                            <?= $fw['firewall_desc'] ?>&nbsp;&nbsp;
                            <?= $fw['firewall_name'] ? $fw['firewall_rule_order'] + 1 : 0 ?>&nbsp;&nbsp;
                            <?= $fw['protocol'] ?>&nbsp;&nbsp;
                            <span style='background:#fbc5c5'><?= $fw['source_ip_address'] ?: 'any' ?></span>&nbsp;&nbsp;
                            <?= $fw['source_port'] ?: 'any' ?>&nbsp;&nbsp;
                            <span style='background:#DDDDDD'><?= $fw['destination_ip_address'] ?: 'any' ?></span>&nbsp;&nbsp;
                            <?= $fw['destination_port'] ?: 'any' ?>&nbsp;&nbsp;
                            <span style='background:#fde087'><?= $fw['action']  ?></span>&nbsp;&nbsp;
                            <?= $fw['network_name'] ?>&nbsp;&nbsp;
                            <i class='angle down icon'></i>
                        </a>
                        <div class='description'>
                            <ol>
                                <li>FW名稱: <?= $fw['firewall_name'] ?: 'n/a' ?></li>
                                <li>FW狀態: <?= $fw['firewall_status'] ?></li>
                                <li>FW描述: <?= $fw['firewall_desc'] ?></li>
                                <li>規則順序: <?= $fw['firewall_rule_order'] ?></li>
                                <li>規則名稱: <?= $fw['firewall_rule_name'] ?></li>
                                <li>protocol: <?= $fw['protocol'] ?></li>
                                <li>來源IP: <?= $fw['source_ip_address'] ?: 'any' ?></li>
                                <li>來源Port: <?= $fw['source_port'] ?: 'any' ?></li>
                                <li>目的IP: <?= $fw['destination_ip_address'] ?: 'any' ?></li>
                                <li>目的Port: <?= $fw['destination_port'] ?: 'any' ?></li>
                                <li>動作: <?= $fw['action'] ?></li>
                                <li>網段名稱: <?= $fw['network_name'] ?></li>
                                <li>網段狀態: <?= $fw['network_status'] ?></li>
                                <li>cidr: <?= $fw['cidr'] ?></li>

                            </ol>
                        </div>
                        </div>
                    </div>
                <?php endforeach ?>
                <script>$('.ui.accordion').accordion('refresh');</script>
                </div>
                <?php break; ?>
		<?php endswitch ?>
		<?=$Paginator->createLinks($links, 'ui pagination menu', $pageAttr, $method='ajax')?>
    <?php endif ?>
<?php
}
