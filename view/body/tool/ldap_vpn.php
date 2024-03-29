<div id="page" class="container">
	<div id="content">
		<div class="sub-content show">
			<div class="post ldap">
                <?=$route->createBreadcrumbs(' > ');?>
                <h2 class="ui dividing header">LDAP</h2>
				<div class="post_title">Operation</div>
				<div class="post_cell ldap">
					<form class="ui form" action="">
						<div class="fields">
                            <div class="field">
                                <label>欄位</label>
                                <select name="objectCategory" class="ui fluid dropdown" required>
                                    <option value="vpn" selected>VPN</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>通用名稱</label>
                                <div class="ui input">
                                    <input type="text" name="target" value="<?=$target?>" placeholder="<?=$target?>">
                                </div>
                            </div>
                            <div class="field">
                                <button type="submit" id="ldap_search_btn" class="ui button">
                                    <i class="search icon"></i>Search
                                </button>
                            </div>
                            <div class="field">
                                <button type="button" id="ldap_newvpn_btn" class="ui button disabled">
                                    <i class="user icon"></i>New VPN
                                </button>
                            </div>
                            <div class="field">
                                <button type="button" id="ldap_bind_btn" class="ui grey button">
                                    <i class="ui edit icon"></i>Edit
                                </button>
                            </div>
                            <div class="ui centered inline loader"></div>
                        </div>
					</form>

				<div class="record_content"></div>
                </div> <!-- end of .post_cell-->
                <div class="post_title">List</div>
                <div class="ui secondary pointing menu">
                    <a class="active item">VPN</a>
                    <!-- <a class="item">TainanComputer</a> -->
                    <!-- <a class="item">Unassigned Computer</a> -->
                </div>
                <div class="ui noborder bottom attached segment">
                    <div class="tab-content ldap_vpns show">
                        <div class="post_cell ldap_vpns">
                            <div class='ui centered grid'>
                                <div class='sixteen wide column' style='max-height: 50vh; overflow-x: hidden; overflow-y: auto;'>
                                <!--<div class='sixteen wide column'>-->
                                    <div class="ldap_tree_content ldap_vpns">
                                        <div class="ui list">
                                            <div class="item hide">
                                                <i class="plus square outline icon" base="<?=$user_base?>" ou="<?=$user_ou?>" description="<?=$user_description?>"></i>
                                                <i class="folder icon"></i>
                                                <div class="content">
                                                    <div class="header"><?=$user_ou?>(<?=$user_description?>)</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!--end of .post_cell-->
                    </div> <!-- end of .tabular-->
                </div> <!-- end of .attached.segment-->
			</div><!--end of .post-->
		</div><!--end of .sub-content-->
		<div style="clear: both;">&nbsp;</div>
	</div><!-- end #content -->
</div> <!--end #page-->
