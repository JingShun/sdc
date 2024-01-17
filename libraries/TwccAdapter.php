<?php

/** 
 * 參考: https://docs.twcc.ai/docs/api/CCS
 */

class TWCCAdapter
{

    private $HOST_URL = 'https://apigateway.twcc.ai/api/v3';

    public function listFirewall()
    {
        $url = $this->HOST_URL . "/openstack-taichung-default-2/firewalls/?" . http_build_query([
            'project' => TWCC::TWCC_PROJECT,
            'all_users' => 1
        ]);

        $query = $this->SendHttpRequest($url);
        $json = json_decode($query, true);

        return $json;

        /* return array
            id : 3094
            name : "fw07rdec"
            platform : "openstack-taichung-default-2"
            status : "ACTIVE"
            project : 47404
            rules : array(9)
                0 : 15025
                2 : 20848
                3 : 20851
                4 : 20854
                5 : 20857
                6 : 20860
                7 : 20863
                8 : 20866
            associate_networks : array(1)
                0 : 303535
            user : array(4)
                id : "1ce20380-1591-4413-aaab-f591961063b3"
                username : "twsqzbr999"
                email : "sphinxw@mail.tainan.gov.tw"
                display_name : "王士坤"
            desc : "消防局"
        */
    }

    public function getFirewallDetail($firewall_id)
    {
        $url = $this->HOST_URL . "/openstack-taichung-default-2/firewalls/$firewall_id/";

        $query = $this->SendHttpRequest($url);
        $json = json_decode($query, true);

        return $json;

        /* return array
            associate_networks: [{id: 331306, name: "f5bigipsrv"}] // 關聯網路
                0: {id: 331306, name: "f5bigipsrv"}
            create_time: "2023-03-10T08:39:22Z" // 建立時間
            desc: "f5 管理" // 描述
            id: 3727
            name: "fw212f5" // 名稱
            platform: "openstack-taichung-default-2"
            project: 47404
            rules: [{id: 19942, name: "fwr1678437341811"}, {id: 19945, name: "fwr1678437431278"},…] // 防火牆規則
                0: {id: 19942, name: "fwr1678437341811"}
                1: {id: 19945, name: "fwr1678437431278"}
                2: {id: 20734, name: "f52edrhttps1"}
                3: {id: 20737, name: "f52edrhttps2"}
                4: {id: 20740, name: "f52edrhttps3"}
                5: {id: 20743, name: "f52edrhttps4"}
                6: {id: 20746, name: "f52edrhttps5"}
                7: {id: 20749, name: "f52edrhttps6"}
                8: {id: 20752, name: "f52edrhttps7"}
                9: {id: 20755, name: "f52edrhttps8"}
                10: {id: 20758, name: "f52edrhttps9"}
            status: "ACTIVE" // 狀態
            status_reason: ""
            user: {id: "fa213238-994f-4068-898a-853fc9b57c41", username: "twsfbzh931",…} // 建立者
                display_name: "溫仁祥"
                email: "eddie.wen@twister5.com.tw"
                id: "fa213238-994f-4068-898a-853fc9b57c41"
                username: "twsfbzh931"
        */
    }


    public function listFirewallRules()
    {
        $url = $this->HOST_URL . "/openstack-taichung-default-2/firewall_rules/?" . http_build_query([
            'project' => TWCC::TWCC_PROJECT
        ]);

        $query = $this->SendHttpRequest($url);
        $json = json_decode($query, true);

        return $json;

        /* return array
         [   {
                "id": 13111,
                "name": "bigdataweb2out1",
                "platform": "openstack-taichung-default-2",
                "project": 47404,
                "protocol": "tcp",
                "action": "allow",
                "destination_ip_address": null,
                "destination_port": null,
                "ip_version": 4,
                "source_ip_address": "192.168.211.11/32",
                "source_port": null,
                "user": {
                    "id": "81b48256-52a8-4272-b1f5-3d4c33a6edef",
                    "username": "twswqdp722",
                    "email": "chhung@mail.tainan.gov.tw",
                    "display_name": "洪將涵"
                }
            },...
            ]
        */
    }


    /**
     * @param int $firewall_rule_id id
     */
    public function getFirewallRuleDetail($firewall_rule_id)
    {
        $url = $this->HOST_URL . "/openstack-taichung-default-2/firewall_rules/$firewall_rule_id/";

        $query = $this->SendHttpRequest($url);
        $json = json_decode($query, true);

        return $json;

        /* return {
            "id": 13111,
            "name": "bigdataweb2out1",
            "platform": "openstack-taichung-default-2",
            "project": 47404,
            "protocol": "tcp",
            "action": "allow",
            "destination_ip_address": null,
            "destination_port": null,
            "ip_version": 4,
            "source_ip_address": "192.168.211.11/32",
            "source_port": null,
            "create_time": "2022-08-04T09:25:04Z",
            "user": {
                "id": "81b48256-52a8-4272-b1f5-3d4c33a6edef",
                "username": "twswqdp722",
                "email": "chhung@mail.tainan.gov.tw",
                "display_name": "洪將涵"
            }
        }
        */
    }

    public function listNetworks()
    {
        $url = $this->HOST_URL . "/openstack-taichung-default-2/networks/?" . http_build_query([
            'project' => TWCC::TWCC_PROJECT,
            'all_users' => 1
        ]);

        $query = $this->SendHttpRequest($url);
        $json = json_decode($query, true);

        return $json;
    }


    public function getNetworkDetail($netID)
    {
        $url = $this->HOST_URL . "/openstack-taichung-default-2/networks/$netID/";

        $query = $this->SendHttpRequest($url);
        $json = json_decode($query, true);

        return $json;
    }

    /** 安全性群組
     * @param int $serverID
     * @param int $loadbalancerID
     * @return array
     * @see https://docs.twcc.ai/docs/api/VCS/list-security-groups
     */
    public function listSecurityGroups($serverID = 0, $loadbalancerID = 0)
    {
        $httpQuery = [];
        $httpQuery['project'] = TWCC::TWCC_PROJECT;
        $httpQuery['tpye'] = (empty($serverID) && empty($loadbalancerID)) ? 'VM' : 'LB';
        if ($httpQuery['tpye'] == 'LB') {
            $httpQuery['server'] = $serverID;
            $httpQuery['loadbalancer'] = $loadbalancerID;
        }

        $url = $this->HOST_URL . "/openstack-taichung-default-2/security-groups/?" . http_build_query($httpQuery);

        $query = $this->SendHttpRequest($url);
        $json = json_decode($query, true);

        return $json;
    }

    public function listSites()
    {
        $url = $this->HOST_URL . "/openstack-taichung-default-2/sites/?" . http_build_query([
            'project' => TWCC::TWCC_PROJECT,
            'sol_categ ' => 'os',
            'all_users' => 1,
        ]);

        $query = $this->SendHttpRequest($url);
        $json = json_decode($query, true);

        return $json;

        /* return array[]
            callback : ""
            create_time : "2022-07-29T05:56:08Z"
            id : 2883469
            is_preemptive : false
            name : "tnwebap01"
            platform : "openstack-taichung-default-2"
            progress : 100
            project : 47404
            public_ip : "211.73.81.129"
            servers : [{status: "ACTIVE", hostname: "tnwebap01-2883469-iaas", id: 3293548, flavor_id: 663}]
                0 : {status: "ACTIVE", hostname: "tnwebap01-2883469-iaas", id: 3293548, flavor_id: 663}
            solution : 4485
            status : "Ready"
            termination_protection : false
            user :
                display_name : "TWS TSE環境帳號"
                email : "tse@twsc.io"
                id : "7ec47e13-063b-4e15-a393-30521a79adf7"
                username : "twsecyt387"
        */
    }
    public function getSiteDetail($siteId)
    {
        $url = $this->HOST_URL . "/openstack-taichung-default-2/sites/$siteId/";

        $query = $this->SendHttpRequest($url);
        $json = json_decode($query, true);

        return $json;

        /* return array[]
            callback : ""
            create_time : "2022-07-29T05:56:08Z"
            id : 2883469
            is_preemptive : false
            name : "tnwebap01"
            platform : "openstack-taichung-default-2"
            progress : 100
            project : 47404
            public_ip : "211.73.81.129"
            servers : [{status: "ACTIVE", hostname: "tnwebap01-2883469-iaas", id: 3293548, flavor_id: 663}]
                0 : {status: "ACTIVE", hostname: "tnwebap01-2883469-iaas", id: 3293548, flavor_id: 663}
            solution : 4485
            status : "Ready"
            termination_protection : false
            user :
                display_name : "TWS TSE環境帳號"
                email : "tse@twsc.io"
                id : "7ec47e13-063b-4e15-a393-30521a79adf7"
                username : "twsecyt387"
        */
    }

    public function listServers()
    {
        $url = $this->HOST_URL . "/openstack-taichung-default-2/servers/?" . http_build_query([
            'project' => TWCC::TWCC_PROJECT,
            'sol_categ ' => 'os',
            'all_users' => 1,
        ]);

        $query = $this->SendHttpRequest($url);
        $json = json_decode($query, true);

        return $json;

        /* return array[]
           [73]=>array(15) {
                ["id"]=>
                int(4055311)
                ["hostname"]=>
                string(22) "nreportvm-3908869-iaas"
                ["flavor"]=>
                string(8) "v.xsuper"
                ["image"]=>
                string(16) "img1693894525510"
                ["platform"]=>
                string(28) "openstack-taichung-default-2"
                ["keypair"]=>
                string(13) "culturetainan"
                ["private_nets"]=>
                array(1) {
                    [0]=>
                        array(3) {
                            ["ip"]=>
                            string(13) "192.168.211.1"
                            ["name"]=>
                            string(15) "default_network"
                            ["id"]=>
                            int(301156)
                        }
                }
                ["public_nets"]=>
                array(1) {
                    [0]=>
                        array(2) {
                            ["ip"]=>
                            string(14) "203.145.222.35"
                            ["name"]=>
                            string(15) "default_network"
                        }
                }
                ["site"]=>
                int(3908869)
                ["status"]=>
                string(17) "SHELVED_OFFLOADED"
                ["os"]=>
                string(5) "Linux"
                ["os_version"]=>
                string(1) "2"
                ["auto_scaling_policy"]=>
                NULL
                ["availability_zone"]=>
                string(0) ""
                ["fqdn"]=>
                string(0) ""
            }
        */
    }

    public function getServerDetail($id)
    {
        $url = $this->HOST_URL . "/openstack-taichung-default-2/servers/$id/";

        $query = $this->SendHttpRequest($url);
        $json = json_decode($query, true);

        return $json;

        /* return array
            {
                "id": 3908869,
                "name": "nreportvm",
                "solution": 3736,
                "desc": "",
                "project": 47404,
                "public_ip": "203.145.222.35",
                "status": "NotReady",
                "status_reason": "",
                "platform": "openstack-taichung-default-2",
                "create_time": "2023-09-05T06:22:04Z",
                "user": {
                    "id": "7ec47e13-063b-4e15-a393-30521a79adf7",
                    "username": "twsecyt387",
                    "email": "tse@twsc.io",
                    "display_name": "TWS TSE環境帳號"
                },
                "servers": [
                    {
                        "status": "SHELVED_OFFLOADED",
                        "hostname": "nreportvm-3908869-iaas",
                        "flavor_id": 561,
                        "id": 4055311
                    }
                ],
                "progress": 100,
                "is_preemptive": false,
                "callback": "",
                "termination_protection": false
            }
        */
    }

    public function customRequest($queryUrl)
    {
        $url = $this->HOST_URL . '/openstack-taichung-default-2' . $queryUrl;

        $query = $this->SendHttpRequest($url);
        $json = json_decode($query, true);

        return $json;
    }

    private function SendHttpRequest($url, $postField = array(), $httpHeader = array())
    {

        $header = [];
        $httpHeader = $httpHeader + [
            "Accept" => 'application/json',
            "X-Api-Host" => 'openstack-taichung-default-2',
            "x-api-key" => TWCC::TWCC_API_KEY,
        ];
        foreach ($httpHeader as $key => $value) {
            if (is_int($key))
                $header[] = $value;
            else
                $header[] = "$key: $value";
        }

        $curl_opt_array = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => $header,
        );

        if (empty($postField)) {
            $curl_opt_array[CURLOPT_CUSTOMREQUEST] = "GET";
        } else {
            $curl_opt_array[CURLOPT_CUSTOMREQUEST] = "POST";
            $curl_opt_array[CURLOPT_POSTFIELDS] = $postField;
        }

        $curl = curl_init();
        curl_setopt_array($curl, $curl_opt_array);
        $response = curl_exec($curl);

        if (strpos($response, 'API rate limit exceeded') !== false) {
            // echo 'wait API rate limit exceeded' . PHP_EOL;
            sleep(60);
            $response = curl_exec($curl);
        }

        // Check if any error occurred
        if (curl_errno($curl)) {
            echo 'Curl error: ' . curl_error($curl);
        }

        curl_close($curl);

        return $response;
    }
}
