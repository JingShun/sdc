<!--vul_target-->
<div id="page" class="container">
	<div id="content">
		<div class="sub-content show">
			<div class="post">
                <h2 class="ui dividing header">掃描資產</h2>
				<div class="post_cell">
				<?php 
				if($last_num_rows==0){
					echo "<p>查無此筆紀錄</p>";
				}else{
					echo "<p>共有".$last_num_rows."筆資產";
					echo "(含".$host_num."個掃描主機,".$url_num."筆掃描網站)！</p>";
				?>
				<table class="ui celled table">
				  <thead>
					<th>ou</th>
					<th>ip</th>
					<th>hostname</th>
					<th>system_name</th>
					<th>domain</th>
					<th>manager</th>
					<th>email</th>
				  </tr></thead>
				  <tbody>
				<?php
				foreach($scanTarget as $Target){
					$system_names = explode(";",$Target['system_name']);
					$domains = explode(";",$Target['domain']);
					$size = count($domains);
					for($i=0;$i<$size;$i++){
						if($i==0){
                ?>
							<tr>
							<td rowspan=<?=$size?>><?=str_replace('/臺南市政府/','',$Target['ou'])?></td>
							<td rowspan=<?=$size?>><?=$Target['ip']?></td>
							<td rowspan=<?=$size?>><?=$Target['hostname']?></td>
							<td><?=$system_names[$i]?></td>
							<td><a href='<?=$domains[$i]?>' target='_blank'><?=$domains[$i]?></a></td>
							<td rowspan=<?=$size?>><?=$Target['manager']?></td>
							<td rowspan=<?=$size?>><?=$Target['email']?></td>
							</tr>
			    <?php	}else{   ?>
							<tr>
							<td><?=$system_names[$i]?></td>
							<td><a href='<?=$domains[$i]?>' target='_blank'><?=$domains[$i]?></a></td>
							</tr>
				<?php	}
					}
                    }
                }		
				?>
					  </tbody>
					</table>
				</div><!--end of .post_cell-->
			</div><!--end of .post-->
		</div><!--end of .sub-content-->
		<div style="clear: both;">&nbsp;</div>
	</div><!-- end #content -->
</div> <!--end #page-->