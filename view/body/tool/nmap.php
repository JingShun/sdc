<div id="page" class="container">
	<div id="content">
		<div class="sub-content show">
			<div class="post nmap">
                <?=$route->createBreadcrumbs(' > ');?>
                <h2 class="ui dividing header">Nmap</h2>
				<div class="post_cell">
					<div class="ui secondary pointing menu">
						<a class="active item">Nmap</a>
						<a class="item">Portscan Result</a>
					</div>
					<div class="ui noborder bottom attached segment">
						<div class="tab-content nmap show">
							<form class="ui form" action="">
								<div class="field">
									<label>Host( IP or Domain name )</label>
									<div class="ui input">
										<input type="text" name="target" value="<?php echo $target;?>" placeholder="<?php echo $target;?>">
									</div>
								</div>
								<div class="field">
									<label>Nmap</label>
									<input type="submit" class="ui button" value="Scan">
									<div class="ui centered inline loader"></div>
								</div>
								<div class="ui message">
									<div class="header"><a href='https://nmap.org/nsedoc/' target='_blank'>Nmap Scripts</a></div>
									<ul class="list">
									  <li>nmap -sV --script ssl-cert,ssl-enum-ciphers  -p 443,465,993,995 &lthost&gt</li>
									  <li>nmap -sV --script ssl-enum-ciphers,ssl-cert -p 443 &lthost&gt</li>
									  <li>nmap --script=vuln &lthost&gt</li>
									  <li>nmap --script=default &lthost&gt</li>
									  <li>nmap -T4 -Pn &lthost&gt</li>
									</ul>
							  	</div>
							</form>
							<div class="record_content"></div>
						</div> <!-- end of .tabular-->
						<div class="tab-content portscan">
							<table class='ui celled table'>
							<thead>	
							<tr>
								<th>系統名稱</th>
								<th>IP</th>
								<th>協定</th>
								<th>Port</th>
								<th>服務</th>
								<th>狀態</th>
								<th>Nmap結果</th>
							</tr>
							</thead>
							<tbody>	
							<?php foreach($systems as $system): ?>
							    <?php if($system['size'] == 0 ): ?>
									<tr>
										<td><a href='<?=$system['url']?>' target='_blank'><?=$system['name']?></a></td>
										<td><?=$system['ip']?></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td><?=$system['scan_result']?></td>
									</tr>
								<?php else: ?>
								    <?php foreach ($system['ports'] as $index => $port): ?>
										<?php if($index == 0): ?>
											<tr>
												<td rowspan=<?=$system['size']?>><a href='<?=$system['url']?>' target='_blank'><?=$system['name']?></a></td>
												<td rowspan=<?=$system['size']?>><?=$system['ip']?></td>
												<td rowspan=<?=$system['size']?>>tcp</td>
												<td><?=$port['port_number']?></td>
												<td><?=$port['service']?></td>
												<td><?=$port['status']?></td>
												<td rowspan=<?=$system['size']?>><?=$system['scan_result']?></td>
											</tr>
										<?php else: ?>
											<tr>
                                                <td><?=$port['port_number']?></td>
                                                <td><?=$port['service']?></td>
                                                <td><?=$port['status']?></td>
											</tr>
										<?php endif ?>	
									<?php endforeach ?>
								<?php endif ?>
							<?php endforeach ?>
							</tbody>
							</table>
						</div> <!-- end .tabular-->
					</div> <!-- end .attached.segment-->
				</div> <!-- end .post_cell-->
			</div> <!-- end .post-->
		</div> <!-- end .sub-content-->
		<div style="clear: both;">&nbsp;</div>
	</div><!-- end #content -->
</div> <!--end #page-->
