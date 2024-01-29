<div id="page" class="container">
    <div id="content">
        <div class="sub-content show">
            <!-- <div class="post scanResult"> -->
            <div class="post twcc_server">
                <?= $route->createBreadcrumbs(' > '); ?>
                <h2 class="ui dividing header">國網主機網路規則</h2>
                <div class="post_cell">
                    <form class="ui form" action="">
                        <div class="query_content"></div>
                        <div class="fields">
                            <div class="field">
                                <label>欄位</label>
                                <select name="keyword" id="keyword" class="ui fluid dropdown" required>
                                    <option value="twcc_server.hostname">主機名稱</option>
                                    <!-- <option value="server_status">主機狀態</option> -->
                                    <option value="site_name">站台名稱</option>
                                    <option value="site_desc">站台描述</option>
                                    <option value="site_status">站台狀態</option>
                                    <option value="private_ip">私有ip</option>
                                    <option value="public_ip">對外ip</option>
                                    <option value="public_ip_name">對外ip名稱</option>
                                    <option value="network_name">內部網段名稱</option>
                                    <option value="os_type">os類型</option>
                                    <option value="os_version">os版本</option>
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


<script>
    function twcc_fw(server_id){
        console.log('server_id:' + server_id);
        
		$.ajax({
			 url: '/ajax/twcc_fw_query/?server_id=' + server_id,
			 cache: false,
			 dataType:'html',
			 type:'GET',
			//  data: input,
		})
        .done(function(data) {
            
            let fwInfo = $('#fwInfo'+server_id);
            fwInfo.html(data);
            
            $('#fwInfo' + server_id + ' .ui.accordion').accordion('refresh');
            new DataTable('#fwInfo' + server_id + ' .datatable');

            // $(selector + '.record_content').html(data);
        })
        .fail(function(jqXHR) {
            ajax_check_user_logged_out(jqXHR);
        });


    }
</script>