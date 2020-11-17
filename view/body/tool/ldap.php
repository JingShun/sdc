<!--tool_ldap-->
<div id="page" class="container">
	<div id="content">
		<div class="sub-content show">
			<div class="post ldap">
				<div class="post_title">LDAP Search</div>
				<div class="post_cell">
					<form class="ui form" action="javascript:void(0)">
					<!--<div class="fields">-->
						<div class="field">
							<label>CN(=User account or PC name)</label>
							<div class="ui input">
								<?php $target = "yckuo";?>
								<input type="text" class="target" value="<?php echo $target;?>" placeholder="<?php echo $target;?>">
							</div>
						</div>
						<div class="field">
							<label>LDAP</label>
							<div class="two fields">
								<div class="field">
									<button type="submit" id="ldap_search_btn" class="ui button">Search</button>
								</div>
								<div class="field">
									<button id="ldap_newuser_btn" class="ui button">New User</button>
								</div>
							</div>
							<div class="ui centered inline loader"></div>
						</div>
					<!--</div>-->	
					</form>

				<div class="record_content"></div>
			</div> <!-- end of .post_cell-->
			</div>
			<div class="post ldap_computer_tree">
				<div class="post_title">AD Computer Tree</div>
				<div class="post_cell">
					<div class="ui centered inline loader"></div>
					<div class="ldap_tree_content"></div>
				</div>
			</div>
		</div>
		<div style="clear: both;">&nbsp;</div>
	</div><!-- end #content -->
</div> <!--end #page-->
