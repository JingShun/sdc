<!--info.php-->
<?php 
	if(isset($_GET['subpage'])) $subpage = $_GET['subpage'];
	else						$subpage = 'enews';

	switch($subpage){
		case 'enews': load_info_enews(); break;
		case 'comparison': load_info_comparison(); break;
		case 'ranking': load_info_ranking(); break;
		case 'vul': load_info_vul(); break;
		case 'client': load_info_client(); break;
	}
?>
<?php
function load_info_enews(){
?>
<div id="page" class="container">
	<div id="content">
		<div class="sub-content show">
			<div class="post">
				<div class="post_title">Enews</div>
					<div class="post_cell">
					<div class="post_table">
               		 <?php //select data form database
                    	require("mysql_connect.inc.php");
                   		 //select row_number,and other field value
                    	$sql = "SELECT OccurrenceTime FROM security_event";
                    	$result = mysqli_query($conn,$sql);
                    	$num_total_entry = mysqli_num_rows($result);
                    	$sql = "SELECT * FROM security_event WHERE Status LIKE '已結案'";
                    	$result = mysqli_query($conn,$sql);
                    	$num_done_entry = mysqli_num_rows($result);
                    	$sql = "SELECT * FROM security_event WHERE Status LIKE '未完成'";
                    	$result = mysqli_query($conn,$sql);
                    	$num_undone_entry = mysqli_num_rows($result);
						$sql = "SELECT * FROM security_event WHERE Status LIKE '未完成' AND NOT(UnprocessedReason LIKE '' )";
                    	$result = mysqli_query($conn,$sql);
                    	$num_exception_entry = mysqli_num_rows($result);
						
						$date_from_week = date('Y-m-d',strtotime('monday this week'));  
						$date_to_week = date('Y-m-d',strtotime('sunday this week'));
						$date_from_month = date('Y-m-d',strtotime('first day of this month'));
						$date_to_month = date('Y-m-d',strtotime('last day of this month'));  

                    	$sql = "SELECT * FROM security_event WHERE OccurrenceTime BETWEEN '".$date_from_month."' AND '".$date_to_month."'";
                    	$result = mysqli_query($conn,$sql);
                    	$num_thisMonth_entry = mysqli_num_rows($result);
                    	$sql = "SELECT * FROM security_event WHERE OccurrenceTime BETWEEN '".$date_from_week."' AND '".$date_to_week."'";
                    	$result = mysqli_query($conn,$sql);
                    	$num_thisWeek_entry = mysqli_num_rows($result);
						$completion_rate = round($num_done_entry/$num_total_entry*100,2)."%"; 
					?>

					<table>
					<tr>
						<th>項目</th>
						<th>數值</th>
					</tr>
					<tr>
						<td>列管數量</td>
						<td><?php echo $num_total_entry ?></td>
					</tr>
					<tr>
						<td>已完成</td>
						<td><?php echo $num_done_entry ?></td>
					</tr>
					<tr>
						<td>未完成</td>
						<td><?php echo $num_undone_entry ?></td>
					</tr>
					<tr>
						<td>無法完成</td>
						<td><?php echo $num_exception_entry ?></td>
					</tr>
						<td>本月已發生</td>
						<td><?php echo $num_thisMonth_entry ?></td>
					</tr>
					<tr>
						<td>本周已發生</td>
						<td><?php echo $num_thisWeek_entry ?></td>
					</tr>
					<tr>
						<td>完成率</td>
						<td><?php echo $completion_rate ?></td>
					</tr>
					</table>
					<?php
                    $conn->close();

					?>
					</div>
					</div>
			</div>
			<div class="post">
				<div class="post_title">資安事件趨勢圖(最近3個月)</div>
				<div class="post_cell">
					<div id="chartA" class="chart"></div>	
				</div>
			</div>
			<div class="post">
				<div class="post_title">資安事件清單(最近10筆)</div>
				<div class="post_cell">
					<table class="ui very basic single line table">
					 <?php //select data form database
						require("mysql_connect.inc.php");
						 //select row_number,and other field value
						$sql = "SELECT * FROM security_event ORDER BY EventID desc LIMIT 10";
						$result = mysqli_query($conn,$sql);
						$num_total_entry = mysqli_num_rows($result);
						echo "<thead>";	
						echo "<tr>";
							echo "<th>發現日期</th>";
							echo "<th>結案狀態</th>";
							echo "<th>資安事件類型</th>";
							echo "<th>位置</th>";
							echo "<th>設備IP</th>";
							echo "<th>所有人機關</th>";
							echo "<th>所有人姓名</th>";
						echo "</tr>";
						echo "</thead>";	
						echo "<tbody>";	
						 while($row = mysqli_fetch_assoc($result)) {
							echo "<tr>";
								echo "<td>".date_format(new DateTime($row['OccurrenceTime']),'Y-m-d')."</td>";
								echo "<td>".$row['Status']."</td>";
                        		echo "<td>".$row['EventTypeName']."</td>";
								echo "<td>".$row['Location']."</td>";
								echo "<td>".$row['IP']."</td>";
								echo "<td>".$row['AgencyName']."</td>";
								echo "<td>".$row['DeviceOwnerName']."</td>";
							echo "</tr>";
						 }
						echo "</tbody>";
						echo "</table>";
						$conn->close();
					?>
					<div class="see_more" style="text-align:right">
						<a href="/query/event/">See More...</a>
					</div>
				</div>
			</div>
			<div class="post">
				<div class="post_title">資安事件SOP</div>
				<div class="post_cell">
					<img class="image" src="images/sop.png">
				</div>
			</div>
		</div>
		<div style="clear: both;">&nbsp;</div>
	</div><!-- end #content -->
</div> <!--end #page-->
<?php } 
function load_info_comparison(){
?>	
<div id="page" class="container">
	<div id="content">
		<div class="sub-content show">
			<div class="post">
				<div class="post_title">Compared with last year</div>
				<div class="post_cell">
				繪製長條圖時，長條柱或柱組中線須對齊項目刻度。相較之下，折線圖則是將數據代表之點對齊項目刻度。在數字大且接近時，兩者皆可使用波浪形省略符號，以擴大表現數據間的差距，增強理解和清晰度。
				</div>
				<div id="chartB" class="chart"></div>	
				<button id="show_chart_btn" class="ui button">Plot</button>
			</div>
		</div>
		<div style="clear: both;">&nbsp;</div>
	</div><!-- end #content -->
</div> <!--end #page-->
<?php } 
function load_info_ranking(){
?>	
<div id="page" class="container">
	<div id="content">
		<div class="sub-content show">
			<div class="post">
				<div class="post_title">資安類型統計圖</div>
				<div class="post_cell">
					<div id="chartC" class="chart"></div>	
			    </div>		
			</div>
			<div class="post">
				<div class="post_title">Top 10 機關資安事件排序</div>
				<div class="post_cell">
					<div id="chartC-2" class="chart"></div>	
			    </div>		
			</div>
			<div class="post">
				<div class="post_title">Top 10 攻擊目標IP</div>
				<div class="post_cell">
					<div id="chartC-3" class="chart"></div>	
			    </div>		
			</div>
			<!--<div class="post">
				<div class="post_title">Top 10 攻擊來源IP</div>
				<div class="post_cell">
					<div id="chartC-4" class="chart"></div>	
			    </div>		
			</div>-->
		</div>
		<div style="clear: both;">&nbsp;</div>
	</div><!-- end #content -->
<?php } 
function load_info_vul(){
?>	
<div id="page" class="container">
	<div id="content">
		<div class="sub-content show">
			<div class="post">
				<div class="post_title">VUL Bar</div>
					<div class="post_cell">
						臺南市政府弱掃平台各單位弱點數量，高風險應於<font color="red">1</font>個月內修補完成，中風險應於<font color="red">2</font>個月內修補完成。<br>
						<div class="post_table">
						<?php //select row_number,and other field value
                    	require("mysql_connect.inc.php");
						$sql = "SELECT ou,sum(total_VUL) as total_VUL,sum(fixed_VUL) as fixed_VUL,sum(fixed_VUL)*100.0 / NULLIF(SUM(total_VUL), 0) as total_completion,sum(total_high_VUL) as total_high_VUL, sum(fixed_high_VUL) as fixed_high_VUL,sum(fixed_high_VUL)*100.0 / NULLIF(SUM(total_high_VUL), 0) as high_completion,sum(total_VUL - overdue_high_VUL - overdue_medium_VUL) as non_overdue_VUL, sum(total_VUL - overdue_high_VUL - overdue_medium_VUL)*100.0 / NULLIF(SUM(total_VUL), 0) as non_overdue_completion	FROM V_VUL_tableau GROUP BY ou ORDER BY total_completion desc";
						$result 	= mysqli_query($conn,$sql);
						$rowcount	= mysqli_num_rows($result);
						?>
						<table>
							<thead>
								<tr>
									<th>OU</th>
									<th>修補率 | 已修補數 | 總數</th>
									<th>未逾期率 | 未逾期未修補+已修補數 | 總數</th>
									<th>高風險(修補率 | 已修補數 | 總數)</th>
								</tr>
							</thead>
							<tbody>
							<?php
							while($row = mysqli_fetch_assoc($result)) {
							    //hide the 區公所 ou
								if(strchr($row['ou'],"區公所") == "區公所") echo "<tr style='opacity:0.5'>";
								else echo "<tr>";
									echo "<td data-label='OU'>".$row['ou']."</td>";
									echo "<td data-label='Total-Risks'>";
									echo "<div class='ui teal progress yckuo' data-percent='".round($row['total_completion'],0)."' data-total='100' id='example1'>";
						 			echo "<div class='bar'><div class='progress'></div></div>";
						  			echo "<div class='label'>".$row['fixed_VUL']."/".$row['total_VUL']."</div>";
									echo "</div>";
									echo "</td>";
									echo "<td data-label='Non-Overdue-Risks'>";
									echo "<div class='ui teal progress yckuo' data-percent='".round($row['non_overdue_completion'],0)."' data-total='100' id='example1'>";
						 			echo "<div class='bar'><div class='progress'></div></div>";
						  			echo "<div class='label'>".$row['non_overdue_VUL']."/".$row['total_VUL']."</div>";
									echo "</div>";
									echo "</td>";
									echo "<td data-label='High-Risks'>";
									echo "<div class='ui teal progress yckuo' data-percent='".round($row['high_completion'],0)."' data-total='100' id='example1'>";
						 			echo "<div class='bar'><div class='progress'></div></div>";
						  			echo "<div class='label'>".$row['fixed_high_VUL']."/".$row['total_high_VUL']."</div>";
									echo "</div>";
									echo "</td>";
								echo "</tr>";
							}
							$sql = "SELECT sum(total_VUL) as total_VUL,sum(fixed_VUL) as fixed_VUL,sum(fixed_VUL)*100.0 / NULLIF(SUM(total_VUL), 0) as total_completion,sum(total_high_VUL) as total_high_VUL, sum(fixed_high_VUL) as fixed_high_VUL, sum(fixed_high_VUL)*100.0 / NULLIF(SUM(total_high_VUL), 0) as high_completion ,sum(total_VUL - overdue_high_VUL - overdue_medium_VUL) as non_overdue_VUL, sum(total_VUL - overdue_high_VUL - overdue_medium_VUL)*100.0 / NULLIF(SUM(total_VUL), 0) as non_overdue_completion	FROM V_VUL_tableau";
							$result 	= mysqli_query($conn,$sql);
							$rowcount	= mysqli_num_rows($result);
							while($row = mysqli_fetch_assoc($result)) {
								echo "<tr style='color:#FF0000'>";
									echo "<td data-label='OU'>Total</td>";

									echo "<td data-label='Total-Risks'>";
									echo "<div class='ui teal progress yckuo' data-percent='".round($row['total_completion'],0)."' data-total='100' id='example1'>";
						 			echo "<div class='bar'><div class='progress'></div></div>";
						  			echo "<div class='label'>".$row['fixed_VUL']."/".$row['total_VUL']."</div>";
									echo "</div>";
									echo "</td>";
									echo "<td data-label='Non-Overdue-Risks'>";
									echo "<div class='ui teal progress yckuo' data-percent='".round($row['non_overdue_completion'],0)."' data-total='100' id='example1'>";
						 			echo "<div class='bar'><div class='progress'></div></div>";
						  			echo "<div class='label'>".$row['non_overdue_VUL']."/".$row['total_VUL']."</div>";
									echo "</div>";
									echo "</td>";
									echo "<td data-label='High-Risks'>";
									echo "<div class='ui teal progress yckuo' data-percent='".round($row['high_completion'],0)."' data-total='100' id='example1'>";
						 			echo "<div class='bar'><div class='progress'></div></div>";
						  			echo "<div class='label'>".$row['fixed_high_VUL']."/".$row['total_high_VUL']."</div>";
									echo "</div>";
									echo "</td>";
								echo "</tr>";
								$fixed_high_VUL	= $row['fixed_high_VUL'];
								$total_high_VUL = $row['total_high_VUL'];
								$high_completion = $row['high_completion'];
							}
							$conn->close();
							?>
							</tbody>
						</table>	
						</div>
					</div>
			</div>
			<div class="post">
				<div class="post_title">High Severity Info</div>
				<div class="post_cell">
					<center>
					  <div class="ui statistic">
						  <div class="value"><?php echo round($high_completion,0) ?> % </div>
						  <div class="label">修補率</div>
					  </div>
					  <br>
					  <div class="ui tiny statistic">
						  <div class="value"><?php echo $fixed_high_VUL ?> / <?php echo $total_high_VUL ?></div>
						  <div class="label">已修補數 / 高風險總數</div>
					  </div>
					  <br>
					  <div class="ui tiny statistic">
						  <div class="value"><?php echo "220" ?> / <?php echo "295" ?></div>
						  <div class="label">總掃描主機 / 網站數</div>
					  </div>
					</center>
				</div>
			</div>
		</div>
		<div style="clear: both;">&nbsp;</div>
	</div><!-- end #content -->
</div> <!--end #page-->
<?php } 
function load_info_client(){
?>	
<div id="page" class="container">
	<div id="content">
		<div class="sub-content show">
			<div class="post">
				<div class="post_title">用戶端資安總表</div>
					<div class="post_cell">
					<div class="post_table">
					<?php //select data form database
                    	require("mysql_connect.inc.php");
                   		 //select row_number,and other field value
						$sql = "SELECT COUNT(*) AS total_num,SUM(ad) AS ad_num,SUM(gcb) AS gcb_num,SUM(wsus) AS wsus_num,SUM(antivirus) AS antivirus_num FROM drip_client_list";
						$result = mysqli_query($conn,$sql);
						$row = @mysqli_fetch_assoc($result);
						$total_num = $row['total_num'];
						$ad_num = $row['ad_num'];
						$gcb_num = $row['gcb_num'];
						$wsus_num = $row['wsus_num'];
						$antivirus_num = $row['antivirus_num'];
						$total_rate = round($total_num/$total_num*100,2)."%"; 
						$ad_rate = round($ad_num/$total_num*100,2)."%"; 
						$gcb_rate = round($gcb_num/$total_num*100,2)."%"; 
						$wsus_rate = round($wsus_num/$total_num*100,2)."%"; 
						$antivirus_rate = round($antivirus_num/$total_num*100,2)."%"; 
					?>

					<table>
					<tr>
						<th>項目</th>
						<th>佈署率 | 數量</th>
					</tr>
					<tr>
						<td>用戶端總數</td>
						<td>
						<div class="ui teal progress yckuo" data-percent="<?php echo $total_rate?>" data-total="100" id="example1">
						  <div class="bar"><div class="progress"></div></div>
						  <div class="label"><?php echo $total_num ?></div>
						</div>
						</td>
					</tr>
					<tr>
						<td>ad安裝數</td>
						<td>
						<div class="ui teal progress yckuo" data-percent="<?php echo $ad_rate?>" data-total="100" id="example1">
						  <div class="bar"><div class="progress"></div></div>
						  <div class="label"><?php echo $ad_num ?></div>
						</div>
						</td>
					</tr>
					<tr>
						<td>gcb安裝數</td>
						<td>
						<div class="ui teal progress yckuo" data-percent="<?php echo $gcb_rate?>" data-total="100" id="example1">
						  <div class="bar"><div class="progress"></div></div>
						  <div class="label"><?php echo $gcb_num ?></div>
						</div>
						</td>
					</tr>
					<tr>
						<td>wsus安裝數</td>
						<td>
						<div class="ui teal progress yckuo" data-percent="<?php echo $wsus_rate?>" data-total="100" id="example1">
						  <div class="bar"><div class="progress"></div></div>
						  <div class="label"><?php echo $wsus_num ?></div>
						</div>
						</td>
					</tr>
					<tr>
						<td>antivirus安裝數</td>
						<td>
						<div class="ui teal progress yckuo" data-percent="<?php echo $antivirus_rate?>" data-total="100" id="example1">
						  <div class="bar"><div class="progress"></div></div>
						  <div class="label"><?php echo $antivirus_num ?></div>
						</div>
						</td>
					</tr>
					</table>
					<?php
                    $conn->close();

					?>
					</div>
				</div>
			</div> <!--end #post-->
			<div class="post">
				<div class="post_title">網段使用IP統計圖</div>
				<div class="post_cell">
					<div id="chartG" class="chart"></div>	
				</div>
			</div> <!--end #post-->
			<div class="post">
				<div class="post_title">GCB總通過率</div>
					<div class="post_cell">
					<div class="post_table">
               		 <?php //select data form database
                    	require("mysql_connect.inc.php");
                   		 //select row_number,and other field value
                    	$sql = "SELECT COUNT(ID) as total_count,SUM(CASE WHEN GsAll_2 = GsAll_1 THEN 1 ELSE 0 END) as pass_count FROM gcb_client_list";
                    	$result = mysqli_query($conn,$sql);
						$row = @mysqli_fetch_assoc($result);
                    	$total_count = $row['total_count'];
                    	$pass_count = $row['pass_count'];
						$total_rate = ($total_count != 0) ? round($total_count/$total_count*100,2)."%" : 0; 
						$pass_rate = ($total_count != 0) ? round($pass_count/$total_count*100,2)."%" : 0; 
						$conn->close();
					?>
					</div>
					<center>
						<div class="ui statistic">
						  <div class="value"><?php echo $pass_count." / ".$total_count ?>  </div>
						  <div class="label">通過數 / 總安裝數</div>
						</div>
					</center>
					<div id="chartF" class="chart"></div>	
				</div>
			</div>
			<div class="post">
				<div class="post_title">GCB作業系統統計圖</div>
				<div class="post_cell">
					<div id="chartE" class="chart"></div>	
			    </div>		
			</div>
			<div class="post">
				<div class="post_title">WSUS總通過率</div>
					<div class="post_cell">
					<div class="post_table">
               		 <?php //select data form database
                    	require("mysql_connect.inc.php");
                   		 //select row_number,and other field value
                    	$sql = "SELECT COUNT(TargetID) as total_count,SUM(CASE WHEN Failed LIKE '0' THEN 1 ELSE 0 END) as pass_count FROM wsus_computer_status";
                    	$result = mysqli_query($conn,$sql);
						$row = @mysqli_fetch_assoc($result);
                    	$total_num = $row['total_count'];
                    	$pass_num = $row['pass_count'];
						$sql = "SELECT * FROM wsus_computer_status WHERE LastSyncTime > ADDDATE(NOW(), INTERVAL -1 WEEK)";
                    	$result = mysqli_query($conn,$sql);
						//用戶端1周內同步成功數	
						$sync_num = mysqli_num_rows($result);
						$total_rate = round($total_num/$total_num*100,2)."%"; 
						$pass_rate = round($pass_num/$total_num*100,2)."%"; 
						$sync_rate = round($sync_num/$total_num*100,2)."%"; 
					?>

					<table>
					<tr>
						<th>項目</th>
						<th>數值</th>
						<th>百分比</th>
					</tr>
					<tr>
						<td>用戶端總數</td>
						<td><?php echo $total_num ?></td>
						<td><?php echo $total_rate ?></td>
					</tr>
					<tr>
						<td>安裝成功數</td>
						<td><?php echo $pass_num ?></td>
						<td><?php echo $pass_rate ?></td>
					</tr>
					<tr>
						<td>1周內同步成功數</td>
						<td><?php echo $sync_num ?></td>
						<td><?php echo $sync_rate ?></td>
					</tr>
					</table>
					<?php
                    $conn->close();

					?>
					</div>
				</div>
			</div>
			<div class="post">
				<div class="post_title">AntiVirus總通過率</div>
					<div class="post_cell">
					<div class="post_table">
               		 <?php //select data form database
                    	require("mysql_connect.inc.php");
                   		 //select row_number,and other field value
                    	$sql = "SELECT COUNT(GUID) AS total_count FROM antivirus_client_list";
                    	$result = mysqli_query($conn,$sql);
						$row = @mysqli_fetch_assoc($result);
                    	$total_num = $row['total_count'];
						$sql = "SELECT COUNT(*) AS dlp_count FROM antivirus_client_list WHERE DLPState IN ('已停止','需要重新啟動','執行') ";
                    	$result = mysqli_query($conn,$sql);
						$row = @mysqli_fetch_assoc($result);
						//用戶端DLP安裝成功數	
                    	$dlp_num = $row['dlp_count'];
						$total_rate = round($total_num/$total_num*100,2)."%"; 
						$dlp_rate = round($dlp_num/$total_num*100,2)."%"; 
						$client_av_num = $antivirus_num;
						$server_av_num = $total_num - $client_av_num;
						$client_av_rate = round($client_av_num/$total_num*100,2)."%"; 
						$server_av_rate = round($server_av_num/$total_num*100,2)."%"; 
					?>
					<table>
					<tr>
						<th>項目</th>
						<th>數值</th>
						<th>百分比</th>
					</tr>
					<tr>
						<td>用戶端總數</td>
						<td><?php echo $total_num ?></td>
						<td><?php echo $total_rate ?></td>
					</tr>
					<tr>
						<td>DLP安裝成功數</td>
						<td><?php echo $dlp_num ?></td>
						<td><?php echo $dlp_rate ?></td>
					</tr>
					<tr>
						<td>Client安裝數</td>
						<td><?php echo $client_av_num ?></td>
						<td><?php echo $client_av_rate ?></td>
					</tr>
					<tr>
						<td>Server安裝數</td>
						<td><?php echo $server_av_num ?></td>
						<td><?php echo $server_av_rate ?></td>
					</tr>
					</table>
					<?php
                    $conn->close();

					?>
					</div>
				</div>
			</div>
			<div class="post">
				<div class="post_title">AD統計</div>
				<div class="post_cell">
               		 <?php //select data form database
                    	require("mysql_connect.inc.php");
                   		 //select row_number,and other field value
                    	$sql = "SELECT COUNT(*) AS ad_num FROM ad_comupter_list";
                    	$result = mysqli_query($conn,$sql);
						$row = @mysqli_fetch_assoc($result);
                    	$ad_num = $row['ad_num'];
					?>
					<center>
						<div class="ui statistic">
						  <div class="value"><?php echo $ad_num ?>  </div>
						  <div class="label">電腦導入數</div>
						</div>
						<!--<div class="ui statistic">
						  <div class="value"><?php echo $ad_num ?>  </div>
						  <div class="label">使用者數</div>
						</div>-->
					</center>
			    </div>		
			</div>
		</div>
		<div style="clear: both;">&nbsp;</div>
	</div><!-- end #content -->
</div> <!--end #page-->
 
<?php } 
?>	
		
		
