<!--query.php-->
<div id="page" class="container">
<div id="content">
		<div class="sub-content show">
			<div class="post security_event">
				<div class="post_title">府內資安事件查詢</div>
				<form class="ui form" action="javascript:void(0)">

 				<div class="fields">
			    	<div class="field">
					    <label>種類</label>
						<select name="keyword_type" id="keyword_type" class="ui fluid dropdown" required>
                   		 <!--<option value="" class="keyword_paper active" selected>關鍵字種類</option>-->
						<option value="IP" class="keyword_paper active" selected>設備IP</option>
						<option value="Status" class="keyword_paper active">結案狀態</option>
						<option value="EventTypeName" class="keyword_paper active">資安類型</option>
						<option value="DeviceTypeName" class="keyword_paper active">設備類型</option>
						<option value="DeviceOwnerName" class="keyword_paper active">所有人姓名</option>
						<option value="OccurrenceTime" class="keyword_paper active">發現日期</option>
						<option value="all" class="keyword_paper active">全部</option>
						</select>
					</div>
				 	<div class="field">
					    <label>關鍵字</label>
						<div class="ui input">
							<input type='text' name='key' id='key' placeholder="請輸入關鍵字">
						</div>
					</div>
					<div class="field">
						<button id="search_btn" name="search_btn" class="ui button">搜尋</button>
					</div>
					 <div class="field">
						<button id="show_all_btn" class="ui button" onclick="window.location.href='index.php?mainpage=query&subpage=1'">顯示全部</button>
					</div>
				</div>
				</form>
				<div class="record_content">
                <?php //select data form database
                    require("mysql_connect.inc.php");
                    //------------pagination----------//
					/*$table = "security_event";
					$condition = "1";
					$order = "ORDER by EventID desc,OccurrenceTime desc"; 
				
					$loop_header =  "<div class='ui relaxed divided list'>";
					$loop_header = $loop_header."<div class='item'>";
					$loop_header = $loop_header."<div class='content'>";
					$loop_header = $loop_header."<a class='header'>";
				   	$loop_header = $loop_header."發現日期&nbsp&nbsp";
					$loop_header = $loop_header."結案狀態&nbsp&nbsp";
					$loop_header = $loop_header."資安事件類型&nbsp&nbsp";
                   	$loop_header = $loop_header."位置&nbsp&nbsp";
					$loop_header = $loop_header."設備IP&nbsp&nbsp";
					$loop_header = $loop_header."姓名&nbsp&nbsp";
					$loop_header = $loop_header."分機&nbsp&nbsp";
					$loop_header = $loop_header."</a>";
					$loop_header = $loop_header."</div>";
					$loop_header = $loop_header."</div>";

					include("pagination.php");
					 */

					$pages=" ";
                    if (!isset($_GET['page'])){ 
                        $pages = 1; 
                    }else{
                        $pages = $_GET['page']; 
                    }
                    
                    //select row_number,and other field value
                    $sql = "SELECT * FROM security_event ORDER by EventID desc,OccurrenceTime desc";
                    $result = mysqli_query($conn,$sql);
                    $rowcount = mysqli_num_rows($result);

					//record number on each page			
					$per = 10; 	
					// maximum pages on pagination	
                    $max_pages = 10;

					list($sql_subpage,$prev_page,$next_page,$lower_bound,$upper_bound,$Totalpages) = getPaginationSQL($sql,$per,$max_pages,$rowcount,$pages);

					$result = mysqli_query($conn,$sql_subpage);
                                        
                    if($rowcount==0){
                        echo "查無此筆紀錄";
                    }else{
                        echo "共有".$rowcount."筆資料！";


						echo "<div class='ui relaxed divided list'>";
							echo "<div class='item'>";
								echo "<div class='content'>";
									echo "<a class='header'>";
									//echo "序號&nbsp";
                        			echo "發現日期&nbsp&nbsp";
                        			echo "結案狀態&nbsp&nbsp";
                        			echo "資安事件類型&nbsp&nbsp";
                       				echo "位置&nbsp&nbsp";
									echo "設備IP&nbsp&nbsp";
									echo "姓名&nbsp&nbsp";
									echo "分機&nbsp&nbsp";
									echo "</a>";
								echo "</div>";
							echo "</div>";

                    while($row = mysqli_fetch_assoc($result)) {
						echo "<div class='item'>";
						echo "<div class='content'>";
							echo "<a>";
							//echo $row['EventID']."&nbsp&nbsp"";
							if($row['Status']=="已結案")echo "<i class='check circle icon' style='color:green'></i>";
							else echo "<i class='exclamation circle icon'></i>";
                        	echo date_format(new DateTime($row['OccurrenceTime']),'Y-m-d')."&nbsp&nbsp";
                        	echo $row['Status']."&nbsp&nbsp";
                        	echo "<span style='background:#fde087'>".$row['EventTypeName']."</span>&nbsp&nbsp";
                        	echo $row['Location']."&nbsp&nbsp";
							echo "<span style='background:#DDDDDD'>".$row['IP']."</span>&nbsp&nbsp";
							echo $row['DeviceOwnerName']."&nbsp&nbsp";
							echo $row['DeviceOwnerPhone']."&nbsp&nbsp";
							echo "<i class='angle double down icon'></i>";
							echo "</a>";
							echo "<div class='description'>";
								echo "<ol>";
								echo "<li>序號:".$row['EventID']."</li>";
								echo "<li>結案狀態:".$row['Status']."</li>";
								echo "<li>發現日期:".date_format(new DateTime($row['OccurrenceTime']),'Y-m-d')."</li>";
								echo "<li>資安事件類型:".$row['EventTypeName']."</li>";
								echo "<li>位置:".$row['Location']."</li>";
								echo "<li>電腦IP:".$row['IP']."</li>";
								echo "<li>封鎖原因:".$row['BlockReason']."</li>";
								echo "<li>設備類型:".$row['DeviceTypeName']."</li>";
								echo "<li>電腦所有人姓名:".$row['DeviceOwnerName']."</li>";
								echo "<li>電腦所有人分機:".$row['DeviceOwnerPhone']."</li>";
								echo "<li>機關:".$row['AgencyName']."</li>";
								echo "<li>單位:".$row['UnitName']."</li>";
								echo "<li>處理日期(國眾):".$row['NetworkProcessContent']."</li>";
								echo "<li>處理日期(三佑科技):".$row['MaintainProcessContent']."</li>";
								echo "<li>處理日期(京稘或中華SOC):".$row['AntivirusProcessContent']."</li>";
								echo "<li>未能處理之原因及因應方式:".$row['UnprocessedReason']."</li>";
								echo "<li>備註:".$row['Remarks']."</li>";
								echo "</ol>";
							echo "</div>";
							echo "</div>";
						echo "</div>";
                    }
					
					echo "</div>";
                                            
                    //The href-link of bottom pages
                    echo "<div class='ui pagination menu'>";	
                    echo "<a class='item test' href='?mainpage=query&page=1'>首頁</a>";
                    echo "<a class='item test' href='?mainpage=query&page=".$prev_page."'> ← </a>";
                    for ($j = $lower_bound; $j <= $upper_bound ;$j++){
                        if($j == $pages){
                            echo"<a class='active item bold' href='?mainpage=query&page=".$j."'>".$j."</a>";
                        }else{
                            echo"<a class='item test' href='?mainpage=query&page=".$j."'>".$j."</a>";
                        }
                    }
                    echo"<a class='item test' href='?mainpage=query&page=".$next_page."'> → </a>";		
                    //last page
                    echo"<a class='item test' href='?mainpage=query&page=".$Totalpages."'>末頁</a>";
                    echo "</div>";
				   
                    //The mobile href-link of bottom pages
                    echo "<div class='ui pagination menu mobile'>";	
                    echo "<a class='item test' href='?mainpage=query&page=".$prev_page."'> ← </a>";
                    echo"<a class='active item bold' href='?mainpage=query&page=".$pages."'>(".$pages."/".$Totalpages.")</a>";
                    echo"<a class='item test' href='?mainpage=query&page=".$next_page."'> → </a>";		
                    echo "</div>";
				
				
					}

					
                    $conn->close();
                        
                ?>
				</div>

						
			</div>
		</div>
		<div class="sub-content">
			<div class="post tainangov_security_Incident">
				<div class="post_title">NCERT資安通報查詢</div>
				<div class="post_cell">
					<form class="ui form" action="javascript:void(0)">

					<div class="fields">
						<div class="field">
							<label>種類</label>
							<select name="keyword_type" id="keyword_type" class="ui fluid dropdown" required>
							<option value="PublicIP" class="keyword_paper active" selected>對外IP(網址)</option>
							<option value="Status" class="keyword_paper active">結案狀態</option>
							<option value="Classification" class="keyword_paper active">事故類型</option>
							<option value="OrganizationName" class="keyword_paper active">機關名稱</option>
							<option value="NccstPT" class="keyword_paper active">攻防演練(是/否)</option>
							<option value="OccurrenceTime" class="keyword_paper active">發現日期</option>
							<option value="all" class="keyword_paper active">全部</option>
							</select>
						</div>
						<div class="field">
							<label>關鍵字</label>
							<div class="ui input">
								<input type='text' name='key' id='key' placeholder="請輸入關鍵字">
							</div>
						</div>
						<div class="field">
							<button id="search_btn" name="search_btn" class="ui button">搜尋</button>
						</div>
						 <div class="field">
							<button id="show_all_btn" class="ui button" onclick="window.location.href='index.php?mainpage=query&subpage=2'">顯示全部</button>
						</div>
					</div>
					</form>
					<div class="record_content">
					<?php //select data form database
						require("mysql_connect.inc.php");
						//------------pagination----------//
						$pages=" ";
						if (!isset($_GET['page'])){ 
							$pages = 1; 
						}else{
							$pages = $_GET['page']; 
						}
						
						//select row_number,and other field value
						//$sql = "SELECT * FROM security_contact ORDER by OID asc,person_type asc";
						// select security_contact from NCERT and Internal_Primary Unit from self-creation
						$sql = "SELECT * FROM tainangov_security_Incident ORDER by IncidentID desc";
							
						$result = mysqli_query($conn,$sql);
						$rowcount = mysqli_num_rows($result);
									
						$per = 10; 		
						$max_pages = 10;
						$Totalpages = ceil($rowcount / $per); 
						$lower_bound = ($pages <= $max_pages) ? 1 : $pages - $max_pages + 1;
						$upper_bound = ($pages <= $max_pages) ? min($max_pages,$Totalpages) : $pages;					
						$start = ($pages -1)*$per; //計算資料庫取資料範圍的開始值。
						if($pages == 1)					$offset = ($rowcount < $per) ? $rowcount : $per;
						elseif($pages == $Totalpages)	$offset = $rowcount - $start;
						else							$offset = $per;
									
						$prev_page = ($pages > 1) ? $pages -1 : 1;
						$next_page = ($pages < $Totalpages) ? $pages +1 : $Totalpages;	
						$sql_subpage = $sql." limit ".$start.",".$offset;
									
						$result = mysqli_query($conn,$sql_subpage);
											
						if($rowcount==0){
							echo "查無此筆紀錄";
						}else{
							echo "共有".$rowcount."筆資料！";


							echo "<div class='ui relaxed divided list'>";
								echo "<div class='item'>";
									echo "<div class='content'>";
										echo "<a class='header'>";
											echo "發現日期&nbsp&nbsp";
											echo "結案狀態&nbsp&nbsp";
											echo "影響等級";
											echo "資安事件類型&nbsp&nbsp";
											echo "對外IP(URL)&nbsp&nbsp";
											echo "機關&nbsp&nbsp";
										echo "</a>";
									echo "</div>";
								echo "</div>";

						while($row = mysqli_fetch_assoc($result)) {
							echo "<div class='item'>";
							echo "<div class='content'>";
								echo "<a>";
                        		echo date_format(new DateTime($row['OccurrenceTime']),'Y-m-d')."&nbsp&nbsp";
								echo $row['Status']."&nbsp&nbsp";
								echo "<span style='background:#DDDDDD'>".$row['ImpactLevel']."</span>&nbsp&nbsp";
								echo $row['Classification']."&nbsp&nbsp";
								echo "<span style='background:#fde087'>".$row['PublicIP']."</span>&nbsp&nbsp";
								echo $row['OrganizationName'];
								echo "<i class='angle double down icon'></i>";
								echo "</a>";
								
								echo "<div class='description'>";
									echo "<ol>";
									echo "<li>編號:".$row['IncidentID']."</li>";
									echo "<li>結案狀態:".$row['Status']."</li>";
									echo "<li>事件編號:".$row['NccstID']."</li>";	
									echo "<li>行政院攻防演練:".$row['NccstPT']."</li>";
									echo "<li>攻防演練衝擊性:".$row['NccstPTImpact']."</li>";
									echo "<li>機關名稱:".$row['OrganizationName']."</li>";
									echo "<li>聯絡人:".$row['ContactPerson']."</li>";
									echo "<li>電話:".$row['Tel']."</li>";
									echo "<li>電子郵件:".$row['Email']."</li>";
									echo "<li>資安維護廠商:".$row['SponsorName']."</li>";
									echo "<li>對外IP或網址:".$row['PublicIP']."</li>";
									echo "<li>使用用途:".$row['DeviceUsage']."</li>";
									echo "<li>作業系統:".$row['OperatingSystem']."</li>";
									echo "<li>入侵網址:".$row['IntrusionURL']."</li>";
									echo "<li>影響等級:".$row['ImpactLevel']."</li>";
									echo "<li>事故分類:".$row['Classification']."</li>";
									echo "<li>事故說明:".$row['Explaination']."</li>";
									echo "<li>影響評估:".$row['Evaluation']."</li>";
									echo "<li>應變措施:".$row['Response']."</li>";
									echo "<li>解決辦法/結報內容:".$row['Solution']."</li>";
									echo "<li>發生時間:".$row['OccurrenceTime']."</li>";
									echo "<li>通報時間:".$row['InformTime']."</li>";	
									echo "<li>修復時間:".$row['RepairTime']."</li>";
									echo "<li>審核機關審核時間:".$row['TainanGovVerificationTime']."</li>";
									echo "<li>技服中心審核時間:".$row['NccstVerificationTime']."</li>";
									echo "<li>通報結報時間:".$row['FinishTime']."</li>";	
									echo "<li>通報執行時間(時:分):".$row['InformExecutionTime']."</li>";
									echo "<li>結案執行時間(時:分):".$row['FinishExecutionTime']."</li>";
									echo "<li>中華SOC複測結果:".$row['SOCConfirmation']."</li>";
									echo "<li>改善計畫提報日期:".$row['ImprovementPlanTime']."</li>";	
									echo "</ol>";
								echo "</div>";
								echo "</div>";
							echo "</div>";
						}
						
						echo "</div>";
												
						//The href-link of bottom pages
						echo "<div class='ui pagination menu'>";	
						echo "<a class='item test' href='?mainpage=query&subpage=2&page=1'>首頁</a>";
						echo "<a class='item test' href='?mainpage=query&subpage=2&page=".$prev_page."'> ← </a>";
						for ($j = $lower_bound; $j <= $upper_bound ;$j++){
							if($j == $pages){
								echo"<a class='active item bold' href='?mainpage=query&subpage=2&page=".$j."'>".$j."</a>";
							}else{
								echo"<a class='item test' href='?mainpage=query&subpage=2&page=".$j."'>".$j."</a>";
							}
						}
						echo"<a class='item test' href='?mainpage=query&subpage=2&page=".$next_page."'> → </a>";		
						//last page
						echo"<a class='item test' href='?mainpage=query&subpage=2&page=".$Totalpages."'>末頁</a>";
						echo "</div>";
					   
						//The mobile href-link of bottom pages
						echo "<div class='ui pagination menu mobile'>";	
						echo "<a class='item test' href='?mainpage=query&subpage=2&page=".$prev_page."'> ← </a>";
						echo"<a class='active item bold' href='?mainpage=query&subpage=2&page=".$pages."'>(".$pages."/".$Totalpages.")</a>";
						echo"<a class='item test' href='?mainpage=query&subpage=2&page=".$next_page."'> → </a>";		
						echo "</div>";
						}
						$conn->close();
					?>
					</div> <!--End of record_content-->	
				</div><!--End of post_cell-->
			</div><!--End of post-->
		</div><!--End of sub-content-->
		<div class="sub-content">
			<div class="post security_contact">
				<div class="post_title">NCERT資安聯絡人</div>
				<div class="post_cell">
					<form class="ui form" action="javascript:void(0)">

					<div class="fields">
						<div class="field">
							<label>種類</label>
							<select name="keyword_type" id="keyword_type" class="ui fluid dropdown" required>
							 <!--<option value="" class="keyword_paper active" selected>關鍵字種類</option>-->
							<option value="organization" class="keyword_paper active" selected>機關名稱</option>
							<option value="OID" class="keyword_paper active">機關OID</option>
							<option value="person_name" class="keyword_paper active">聯絡人姓名</option>
							<option value="person_type" class="keyword_paper active">聯絡人類別</option>
							<option value="all" class="keyword_paper active">全部</option>
							</select>
						</div>
						<div class="field">
							<label>關鍵字</label>
							<div class="ui input">
								<input type='text' name='key' id='key' placeholder="請輸入關鍵字">
							</div>
						</div>
						<div class="field">
							<button id="search_btn" name="search_btn" class="ui button">搜尋</button>
						</div>
						 <div class="field">
							<button id="show_all_btn" class="ui button" onclick="window.location.href='index.php?mainpage=query&subpage=3'">顯示全部</button>
						</div>
					</div>
					</form>
					<div class="record_content">
					<?php //select data form database
						require("mysql_connect.inc.php");
						//------------pagination----------//
						$pages=" ";
						if (!isset($_GET['page'])){ 
							$pages = 1; 
						}else{
							$pages = $_GET['page']; 
						}
						
						//select row_number,and other field value
						//$sql = "SELECT * FROM security_contact ORDER by OID asc,person_type asc";
						// select security_contact from NCERT and Internal_Primary Unit from self-creation
						$sql = "SELECT * FROM security_contact UNION SELECT * FROM security_contact_extra ORDER by OID asc,person_type asc";
							
						$result = mysqli_query($conn,$sql);
						$rowcount = mysqli_num_rows($result);
									
						$per = 10; 		
						$max_pages = 10;
						$Totalpages = ceil($rowcount / $per); 
						$lower_bound = ($pages <= $max_pages) ? 1 : $pages - $max_pages + 1;
						$upper_bound = ($pages <= $max_pages) ? min($max_pages,$Totalpages) : $pages;					
						$start = ($pages -1)*$per; //計算資料庫取資料範圍的開始值。
						if($pages == 1)					$offset = ($rowcount < $per) ? $rowcount : $per;
						elseif($pages == $Totalpages)	$offset = $rowcount - $start;
						else							$offset = $per;
									
						$prev_page = ($pages > 1) ? $pages -1 : 1;
						$next_page = ($pages < $Totalpages) ? $pages +1 : $Totalpages;	
						$sql_subpage = $sql." limit ".$start.",".$offset;
									
						$result = mysqli_query($conn,$sql_subpage);
											
						if($rowcount==0){
							echo "查無此筆紀錄";
						}else{
							echo "共有".$rowcount."筆資料！";


							echo "<div class='ui relaxed divided list'>";
								echo "<div class='item'>";
									echo "<div class='content'>";
										echo "<a class='header'>";
										//echo "序號&nbsp";
										echo "機關名稱&nbsp&nbsp";
										echo "姓名&nbsp&nbsp";
										echo "聯絡人類別&nbsp&nbsp";
										echo "信箱&nbsp&nbsp";
										echo "電話&nbsp&nbsp";
										echo "<a>";
									echo "</div>";
								echo "</div>";

						while($row = mysqli_fetch_assoc($result)) {
							echo "<div class='item'>";
							echo "<div class='content'>";
								echo "<a>";
								echo $row['organization']."&nbsp&nbsp";
								echo $row['person_name']."&nbsp&nbsp";
								echo "<span style='background:#fde087'>".$row['person_type']."</span>&nbsp&nbsp";
								echo $row['email']."&nbsp&nbsp";
								echo "<span style='background:#DDDDDD'>".$row['tel']."#".$row['ext']."</span>&nbsp&nbsp";
								echo "<i class='angle double down icon'></i>";
								echo "</a>";
								echo "<div class='description'>";
									echo "<ol>";
									echo "<li>序號:".$row['CID']."</li>";
									echo "<li>OID:".$row['OID']."</li>";
									echo "<li>機關名稱:".$row['organization']."</li>";
									echo "<li>單位名稱:".$row['unit']."</li>";
									echo "<li>姓名:".$row['person_name']."</li>";
									echo "<li>職稱:".$row['position']."</li>";
									echo "<li>資安聯絡人類型:".$row['person_type']."</li>";
									echo "<li>地址:".$row['address']."</li>";
									echo "<li>電話:".$row['tel']."</li>";
									echo "<li>分機:".$row['ext']."</li>";
									echo "<li>傳真:".$row['fax']."</li>";
									echo "<li>email:".$row['email']."</li>";
									echo "</ol>";
								echo "</div>";
								echo "</div>";
							echo "</div>";
						}
						
						echo "</div>";
												
						//The href-link of bottom pages
						echo "<div class='ui pagination menu'>";	
						echo "<a class='item test' href='?mainpage=query&subpage=3&page=1'>首頁</a>";
						echo "<a class='item test' href='?mainpage=query&subpage=3&page=".$prev_page."'> ← </a>";
						for ($j = $lower_bound; $j <= $upper_bound ;$j++){
							if($j == $pages){
								echo"<a class='active item bold' href='?mainpage=query&subpage=3&page=".$j."'>".$j."</a>";
							}else{
								echo"<a class='item test' href='?mainpage=query&subpage=3&page=".$j."'>".$j."</a>";
							}
						}
						echo"<a class='item test' href='?mainpage=query&subpage=3&page=".$next_page."'> → </a>";		
						//last page
						echo"<a class='item test' href='?mainpage=query&subpage=3&page=".$Totalpages."'>末頁</a>";
						echo "</div>";
					   
						//The mobile href-link of bottom pages
						echo "<div class='ui pagination menu mobile'>";	
						echo "<a class='item test' href='?mainpage=query&subpage=3&page=".$prev_page."'> ← </a>";
						echo"<a class='active item bold' href='?mainpage=query&subpage=3&page=".$pages."'>(".$pages."/".$Totalpages.")</a>";
						echo"<a class='item test' href='?mainpage=query&subpage=3&page=".$next_page."'> → </a>";		
						echo "</div>";
						}
						$conn->close();
					?>
					</div> <!--End of record_content-->	
				</div><!--End of post_cell-->
			</div><!--End of post-->
		</div><!--End of sub-content-->
		<div class="sub-content">
			<div class="post">
				<div class="post_title">Retrieve from Google Sheets</div>
				<div class="post_cell">
					<button id="gs_event_btn" class="ui button self-btn">Retrieve Event GS</button>
					<button id="gs_ncert_incident_btn" class="ui button self-btn">Retrieve Ncert-Incident_GS</button>
					<div class="retrieve_info"></div>
				</div>
			</div>
			<?php 
				// admin is given permission to edit this block	
				if(issetBySession("Level")){
					//echo $_SESSION['Level'];
			?>
			<div class="post">
				<div class="post_title">Retrieve from Ncert</div>
				<div class="post_cell">
					1.從Ncert下載資安人員列表，另存成csv檔且修改編碼為UTF-8。
					<br>2.上傳此csv檔可更新「資安聯絡人」資料，並顯示已更新數量。
					<p><p>
					<form id="upload_Form" action="ajax/upload_contact.php" method="post">
					<div class="ui action input">
						<input type="text" placeholder="File" readonly>
						<input type="file" name="fileToUpload" id="fileToUpload" style="display: none;">
						<div class="ui icon button"><i class="attach icon"></i></div>
					</div>
					<p><input type="submit" value="Submit" class="ui button" name="submit" style="margin-top:1em"/></p>
					</form>
					<div class="retrieve_ncert"></div>
				</div>
			</div>
			<?php } ?>
		</div>
		<div style="clear: both;">&nbsp;</div>
	</div>
	
		<!-- end #content -->


