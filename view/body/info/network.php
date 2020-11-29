<div id="page" class="container">
	<div id="content">
		<div class="sub-content show">
			<div class="post">
				<div class="post_title">Top 對外應用程式</div>
				<div class="post_cell">
					<div id="treemap_chart_div" class="chart"></div>
			    </div>		
			</div>
			<div class="post">
				<div class="post_title">Top 10 攻擊方式</div>
				<div class="post_cell">
					<div id="chartF-2" class="chart"></div>	
			    </div>		
			</div>
			<div class="post">
				<div class="post_title">Top 10 被阻擋應用程式</div>
				<div class="post_cell">
					<div id="chartF-3" class="chart"></div>	
			    </div>		
			</div>
			<div class="post">
				<div class="post_title">威脅日誌(最近10筆)</div>
				<div class="post_cell">
                    <?php if(!isset($data)) {
                        echo "很抱歉，該分類目前沒有資料！";
                    } else{ ?>
					<table class="ui very basic table">
					<thead>	
					<tr>
						<th>接收時間</th>
						<th>名稱</th>
						<th>類型</th>
						<th>來源IP</th>
						<th>目的IP</th>
						<th>目的port</th>
						<th>應用程式</th>
					</tr>
					</thead>	
					<tbody>	
					<?php foreach($data['logs'] as $log){ ?>
						<tr>
							<td><?php echo $log->receive_time ?></td>
							<td><?php echo $log->threatid ?></td>
							<td><?php echo $log->subtype ?></td>
							<td><?php echo $log->src ?></td>
							<td><?php echo $log->dst ?></td>
							<td><?php echo $log->dport ?></td>
							<td><?php echo $log->app ?></td>
						</tr>
					<?php } ?>
					</tbody>
					</table>
				    <?php } ?>
                    <div class="see_more" style="text-align:right">
						<a href="/network/search/">See More...</a>
					</div>
				</div>
			</div>
			<div class="post">
				<div class="post_title">Top 10 目的地國家</div>
				<div class="post_cell">
					<table class="ui very basic single line table">
					<tr>
						<th>目的地國家</th>
						<th>位元組</th>
						<th>同時連線數</th>
					</tr>
					<?php foreach($entries as $entry){
                        $bytes_ratio = round($entry['bytes'] / $max_bytes, 2)*100 ;
                        $sessions_ratio = round($entry['sessions'] / $max_sessions, 2)*100;
					?>
						<tr>
							<td><?=$entry['dstloc']?></td>
							<td>
								<div style='width:<?=$bytes_ratio?>%; background:#78838c'>&nbsp</div>
                                <?=formatBytes($entry['bytes'])?>
							</td>
							<td>
								<div style='width:<?=$sessions_ratio?>%; background:#78838c'>&nbsp</div>
                                <?=formatNumbers($entry['sessions'])?>
							</td>
						</tr>
					<?php } ?>
					</table>
			    </div>		
			</div>
			<div class="post">
				<div class="post_title">市府網段區隔</div>
				<div class="post_cell">
					<img class="image" src="/images/network.png">
				</div>
			</div>
			<div class="post">
				<div class="post_title">ISAC平台更新防火牆範圍</div>
				<div class="post_cell">
					<object type="application/pdf" data="/upload/info/ISACFirewall.pdf" width="100%" height="700"></object>
				</div><!--end of .post_cell-->
			</div><!--end of .post-->
		</div><!--end of .sub-content-->
		<div style="clear: both;">&nbsp;</div>
	</div><!-- end #content -->
</div> <!--end #page-->
