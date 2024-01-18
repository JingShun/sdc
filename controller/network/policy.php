<?php 
// above PAN-OS 9.0
$pa_hosts = ['yonghua', 'minjhih']; // , 'idc'
$policy_configs = array(
    array('key' => 'ExpiredSecurityRules', 'label' => 'ExpiredSecurityRules(含1個月內到期)', 'type' => 'SecurityRules'),
    array('key' => 'SecurityRules', 'label' => 'SecurityRules', 'type' => 'SecurityRules'),
    array('key' => 'NatRules', 'label' => 'NatRules', 'type' => 'NatRules'),
);

$host_policy_results = array();

foreach($pa_hosts as $host) {
    $pa = new PaloAltoAPI($host);
    $policy_results = array();

    foreach($policy_configs as $policy_config) {
    //foreach($policy_labels as $index => $policy_label) {
        //$response = $pa->getPoliciesList($policy_types[$index], $name = "");
        $response = $pa->getPoliciesList($policy_config['type'], $name = "");

        if (empty($response['result']['@total-count'])) {
            $data_array = array();
            $data_array['status'] = "error";
            $data_array['total_count'] = 0;
            $data_array['apps'] = array();
        } else {
            $data_array = array();
            $data_array['status'] = $response['@status'];
            $data_array['total_count'] = $response['result']['@total-count'];
            $data_array['apps'] = $response['result']['entry'];
        }

        if ($policy_config['key'] == 'ExpiredSecurityRules') {
            $data_array['apps'] = array_filter($data_array['apps'], function($val) {
                if (!isset($val['schedule'])) {
                    return false;
                }
                
                if (isset($val['disabled']) && $val['disabled'] == 'yes') {
                    return false;
                }

                $target_days = 30;
                $keywords = preg_split("/-|_/", $val['schedule']);
                $end_date = trim(end($keywords));
                $end_date = substr($end_date, 0, 8);

                if(strlen($end_date) == 4)
                    $end_date = date('Y') . $end_date;

                $days = getDaysAfterToday($end_date, $format='Ymd');

                if ($days > $target_days) {
                    return false;
                }

                return true;
            });
            $data_array['total_count'] = count($data_array['apps']);
        }

        $policy_results[$policy_config['key']] = array(
            'label' => $policy_config['label'],
            'data' => $data_array,
        );
    }
    $host_policy_results[$host] = $policy_results;
}


// PAN-OS 8.0
$pa_hosts = ['intrayonghua'];

foreach($pa_hosts as $host) {
    $pa = new PaloAltoAPI($host);
    $policy_results = array();

    foreach($policy_configs as $policy_config) {

        $data_array = array();
        $data_array['status'] = "error";
        $data_array['total_count'] = 0;
        $data_array['apps'] = array();

        if($policy_config['key'] == 'NatRules') {
            $policy_results[$policy_config['key']] = array(
                'label' => $policy_config['label'],
                'data' => $data_array,
            );
            continue;
        }

        $response = $pa->getXmlSecurityRules();

        if (empty($response['@attributes']['status'])) {
            $data_array = array();
            $data_array['status'] = "error";
            $data_array['total_count'] = 0;
            $data_array['apps'] = array();
        } else {
            $data_array = array();
            $data_array['status'] = $response['@attributes']['status'];
            $data_array['total_count'] = count($response['result']['security']['rules']['entry']);
            $data_array['apps'] = $response['result']['security']['rules']['entry'];
        }

        if ($policy_config['key'] == 'ExpiredSecurityRules') {
            $data_array['apps'] = array_filter($data_array['apps'], function($val) {
                if (!isset($val['schedule'])) {
                    return false;
                }
                
                if (isset($val['disabled']) && $val['disabled'] == 'yes') {
                    return false;
                }

                $target_days = 30;
                $keywords = preg_split("/-|_/", $val['schedule']);
                $end_date = trim(end($keywords));

                echo $end_date ."|". PHP_EOL;
                
                $days = getDaysAfterToday($end_date, $format='Ymd');

                if ($days > $target_days) {
                    return false;
                }

                return true;
            });
            $data_array['total_count'] = count($data_array['apps']);
        }

        $policy_results[$policy_config['key']] = array(
            'label' => $policy_config['label'],
            'data' => $data_array,
        );
    }
    $host_policy_results[$host] = $policy_results;
}

$host_policy_results['idc'] = [];

require 'view/header/default.php'; 
require 'view/body/network/policy.php';
require 'view/footer/default.php'; 
