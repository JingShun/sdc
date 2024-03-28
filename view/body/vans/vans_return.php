<div id="page" class="container">
    <div id="content">
        <div class="sub-content show">
            <div class="post">
                <?= $route->createBreadcrumbs(' > '); ?>
                <h2 class="ui dividing header">VANS上傳情形</h2>
                <div class="post_cell">
                    <div class="ui secondary pointing menu">
                        <a class="active item">資安院VANS</a>
                        <a class="item">瑞思VANS上傳</a>
                        <a class="item"></a>
                    </div>
                    <div class="ui noborder bottom attached segment">
                        <div class="tab-content yonghua show">
                            <?php if (count($nicsOuRows) == 0) : ?>
                                <p>查無此筆紀錄</p>
                            <?php else : ?>
                                <p>共有 <?= count($nicsOuRows) ?> 個機關，最後上傳於 <?= $nicsOuUpdatedAt ?> ！</p>
                                <table class="ui celled table">
                                    <thead>
                                        <tr>
                                            <th>ou</th>
                                            <th>level</th>
                                            <th>PC CPE</th>
                                            <th>PC KBID</th>
                                            <th>System CPE</th>
                                            <th>System KBID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($nicsOuRows as $row) : ?>
                                            <tr>
                                                <td><?= $row['ou'] ?></td>
                                                <td><?= $row['level'] ?></td>
                                                <td class="<?= $row['userCPE_warning'] ? 'warning' : '' ?>"><?= $row['userCPE'] ?></td>
                                                <td class="<?= $row['userKBID_warning'] ? 'warning' : '' ?>"><?= $row['userKBID'] ?></td>
                                                <td class="<?= $row['systemCPE_warning'] ? 'warning' : '' ?>"><?= $row['systemCPE'] ?></td>
                                                <td class="<?= $row['systemKBID_warning'] ? 'warning' : '' ?>"><?= $row['systemKBID'] ?></td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            <?php endif    ?>
                        </div> <!-- end of .tabular-->
                        <div class="tab-content minjhih">
                            <?php if (count($nicsOuRows) == 0) : ?>
                                <p>查無此筆紀錄</p>
                            <?php else : ?>
                                <p>共有 <?= count($rapixOuRows) ?> 個機關，最後上傳於 <?= $rapixOuUpdatedAt ?></p>
                                
                                <table class="ui celled table">
                                    <thead>
                                        <tr>
                                            <th>ou</th>
                                            <th>PC CPE</th>
                                            <th>PC KBID</th>
                                            <th>System CPE</th>
                                            <th>System KBID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rapixOuRows as $row) : ?>
                                            <tr>
                                                <td><?= $row['ou'] ?></td>
                                                <td class="<?= $row['userCPE_warning'] ? 'warning' : '' ?>"><?= implode('<br>', $row['userCPE']) ?></td>
                                                <td class="<?= $row['userKBID_warning'] ? 'warning' : '' ?>"><?= implode('<br>', $row['userKBID']) ?></td>
                                                <td class="<?= $row['systemCPE_warning'] ? 'warning' : '' ?>"><?= implode('<br>', $row['systemCPE']) ?></td>
                                                <td class="<?= $row['systemKBID_warning'] ? 'warning' : '' ?>"><?= implode('<br>', $row['systemKBID']) ?></td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            <?php endif    ?>
                            <br>
                            <div>
                                <h2>回傳代號與訊息描述說明</h2>
                                <table class="ui celled table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>資訊資產回傳代號</th>
                                                <th>已安裝KBID回傳代號</th>
                                                <th>代號說明</th>
                                                <th>訊息描述說明</th>
                                                <th>資訊資產訊息描述範例</th>
                                                <th>已安裝KBID訊息描述範例</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>A-S/PC-0101</td>
                                                <td>KB-S/PC-0101</td>
                                                <td>上傳成功</td>
                                                <td>N/A</td>
                                                <td colspan="2" style="text-align:center;">上傳成功</td>
                                            </tr>
                                            <tr>
                                                <td>A-S/PC-0301</td>
                                                <td>KB-S/PC-0301</td>
                                                <td>API KEY錯誤</td>
                                                <td>N/A</td>
                                                <td colspan="2" style="text-align:center;">API KEY錯誤</td>
                                            </tr>
                                            <tr>
                                                <td>A-S/PC-0303</td>
                                                <td>KB-S/PC-0303</td>
                                                <td>機關OID錯誤或上傳使用之IP未通過審核</td>
                                                <td>N/A</td>
                                                <td colspan="2" style="text-align:center;">機關OID錯誤或上傳使用之IP尚未核可</td>
                                            </tr>
                                            <tr>
                                                <td>A-S/PC-0401</td>
                                                <td>KB-S/PC-0401</td>
                                                <td>缺少必要參數</td>
                                                <td>第n筆資料缺少必要參數</td>
                                                <td colspan="2" style="text-align:center;">1, 3, 7</td>
                                            </tr>
                                            <tr>
                                                <td>A-S/PC-0402</td>
                                                <td>KB-S/PC-0402</td>
                                                <td>機關名稱欄位錯誤</td>
                                                <td>第n筆資料機關名稱欄位錯誤</td>
                                                <td colspan="2" style="text-align:center;">1, 3, 7</td>
                                            </tr>
                                            <tr>
                                                <td>A-S/PC-0403</td>
                                                <td>KB-S/PC-0403</td>
                                                <td>資產/KBID數量欄位錯誤</td>
                                                <td>第n筆資料資產/KBID數量欄位錯誤</td>
                                                <td colspan="2" style="text-align:center;">1, 3, 7</td>
                                            </tr>
                                            <tr>
                                                <td>A-S/PC-0404</td>
                                                <td>KB-S/PC-0404</td>
                                                <td>CPE/KBID格式錯誤</td>
                                                <td>第n筆資料CPE/KBID格式錯誤</td>
                                                <td colspan="2" style="text-align:center;">1, 3, 7</td>
                                            </tr>
                                            <tr>
                                                <td>A-S/PC-0405</td>
                                                <td>KB-S/PC-0405</td>
                                                <td>重複新增相同資產/KBID</td>
                                                <td>單次上傳資料包含重複之資產/KBID</td>
                                                <td style="text-align:center;">WinRAR 5.50, RARLAB, 5.50, cpe:2.3:a:rarlab:winrar:5.50:*:*:*:*:*:*:*</td>
                                                <td style="text-align:center;">KB4565483</td>
                                            </tr>
                                            <tr>
                                                <td>A-S/PC-0406</td>
                                                <td>KB-S/PC-0406</td>
                                                <td>發生非預期錯誤</td>
                                                <td>N/A</td>
                                                <td colspan="2" style="text-align:center;">發生非預期錯誤</td>
                                            </tr>
                                            <tr>
                                                <td>A-S/PC-0407</td>
                                                <td>KB-S/PC-0407</td>
                                                <td>上傳數量超過限制</td>
                                                <td>N/A</td>
                                                <td colspan="2" style="text-align:center;">上傳數量超過限制</td>
                                            </tr>
                                            <tr>
                                                <td rowspan="2">A-S/PC-0408</td>
                                                <td rowspan="2">KB-S/PC-0408</td>
                                                <td rowspan="2">此機關尚有資料解析中</td>
                                                <td rowspan="2">N/A</td>
                                                <td style="text-align:center;">[機關OID] 系統解析資產清單中，完成後將會寄發郵件通知</td>
                                                <td style="text-align:center;">[機關OID]系統解析已安裝KBID清單中，完成後將會寄發郵件通知</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:center;">已完成資產清單解析，進行弱點比對中，完成後將會寄發郵件通知</td>
                                                <td style="text-align:center;">已完成已安裝KBID清單解析，進行KBID串聯中，完成後將會寄發郵件通知</td>
                                            </tr>
                                        </tbody>
                                </table>
                            </div>
                        </div> <!-- end of .tabular-->
                        <div class="tab-content">

                        </div> <!-- end of .tabular-->
                    </div> <!-- end of .attached.segment-->
                </div><!--end of .post_cell-->
            </div><!--end of .post-->
        </div><!--end of .sub-content-->
        <div style="clear: both;">&nbsp;</div>
    </div><!-- end #content -->
</div> <!--end #page-->