<div id="page" class="container">
    <div id="content">
        <div class="sub-content show">
            <!-- <div class="post scanResult"> -->
            <div class="post twcc_fw">
                <?= $route->createBreadcrumbs(' > '); ?>
                <h2 class="ui dividing header">國網基礎防火牆規則</h2>
                <div class="post_cell">
                    <form class="ui form" action="">
                        <div class="query_content"></div>
                        <div class="fields">
                            <div class="field">
                                <label>欄位</label>
                                <select name="keyword" id="keyword" class="ui fluid dropdown" required>
                                    <option value="twcc_firewall.firewall_desc">FW描述</option>
                                    <option value="twcc_firewall.firewall_name">FW名稱</option>
                                    <option value="network_name">網段名稱</option>
                                    <option value="twcc_firewall.firewall_rule_name">規則名稱</option>
                                    <option value="twcc_firewall.firewall_rule_order">規則順序</option>
                                    <option value="twcc_firewall.protocol">協定</option>
                                    <option value="twcc_firewall.source_ip_address">來源IP</option>
                                    <option value="twcc_firewall.source_port">來源Port</option>
                                    <option value="twcc_firewall.destination_ip_address">內部IP</option>
                                    <option value="twcc_firewall.destination_port">內部Port</option>
                                    <option value="twcc_firewall.action">動作</option>
                                    <option value="cidr">CIDR</option>
                                    <!-- <option value="all">全部</option> -->
                                </select>
                            </div>
                            <div class="field">
                                <label>關鍵字</label>
                                <div class="ui input">
                                    <input type='text' name='key' id='key' placeholder="請輸入關鍵字">
                                </div>
                            </div>
                            <div class="field">
                                <label>新增條件</label>
                                <i class="large square icon plus"></i>
                            </div>
                            <div class="field">
                                <button type="submit" id="search_btn" name="search_btn" class="ui button">搜尋</button>
                            </div>
                            <div class="field">
                                <button type="button" id="show_all_btn" class="ui button" onclick="location.href='/twcc/server/'">顯示全部</button>
                            </div>
                            <div class="field">
                                <button type="button" id="export2csv_btn" class="ui button">匯出</button>
                            </div>
                        </div>
                    </form>
                    <div class="record_content"></div> <!-- end of #record_content-->
                </div><!--end of .post_cell-->
            </div><!--end of .post-->
        </div><!--end of .sub-content-->
        <div style="clear: both;">&nbsp;</div>
    </div><!-- end #content -->
</div> <!--end #page-->
