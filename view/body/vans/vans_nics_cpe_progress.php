<div id="page" class="container">
    <div id="content">
        <div class="sub-content show">
            <div class="post">
                <?= $route->createBreadcrumbs(' > '); ?>
                <h2 class="ui dividing header">VANS改善措施填寫情形</h2>
                <div class="post_cell">
                    <div class="ui secondary pointing menu">
                        <a class="active item">各局處填寫情形</a>
                        <a class="item">各資產填寫情形</a>
                        <a class="item"></a>
                    </div>
                    <div class="ui noborder bottom attached segment">
                        <div class="tab-content yonghua show">
                            <?php if (count($nicsOuRows) == 0) : ?>
                                <p>查無此筆紀錄</p>
                            <?php else : ?>
                                <p>共有 <?= $notFinishOuCount ?> 個機關的改善措施尚未填寫完畢！</p>
                                <table class="ui celled table">
                                    <thead>
                                        <tr>
                                            <th>ou</th>
                                            <th>level</th>
                                            <!-- <th>類別</th> -->
                                            <th>資產數量</th>
                                            <!-- <th>CVSS</th> -->
                                            <th>CVE數量</th>
                                            <th>未填改善措施數量</th>
                                            <th>填寫率(%)</th>
                                            <th width="40%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($nicsOuRows as $row) : ?>
                                            <tr>
                                                <td><?= $row['unit_name'] ?></td>
                                                <td><?= $row['rank'] ?></td>
                                                <!-- <td><?= $row['product_type'] == 'system' ? '資通系統' : ($row['product_type'] == 'user' ? 'PC' : $row['product_type']) ?></td> -->
                                                <td><?= $row['asset_number'] ?></td>
                                                <!-- <td><?= $row['cvss'] ?></td> -->
                                                <td><?= $row['cve_number'] ?></td>
                                                <td><?= $row['no_progress_number'] ?></td>
                                                <td><?= $row['rate'] ?></td>
                                                <td>
                                                    <div class='ui teal progress yckuo' data-sort-value='<?= $row['rate'] ?>' data-percent='<?= $row['rate'] ?>' data-total='100'>
                                                        <div class='bar'>
                                                            <div class='progress'></div>
                                                        </div>
                                                        <div class='label'><?= $row['cve_number'] - $row['no_progress_number'] ?>/<?= $row['cve_number'] ?></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            <?php endif    ?>
                        </div> <!-- end of .tabular-->
                        <div class="tab-content minjhih">
                            <?php if (count($queryNotProgressAsset) == 0) : ?>
                                <p>查無此筆紀錄</p>
                            <?php else : ?>

                                <table class="ui celled table">
                                    <thead>
                                        <tr>
                                            <th>ou</th>
                                            <th>類型</th>
                                            <th>供應商</th>
                                            <th>產品</th>
                                            <th>資產數量</th>
                                            <th>CVSS</th>
                                            <th>CVE數量</th>
                                            <th>未填改善措施數量</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($queryNotProgressAsset as $row) : ?>
                                            <tr>
                                                <td><?= $row['unit_name'] ?></td>
                                                <td><?= $row['product_type'] == 'system' ? '資通系統' : 'PC' ?></td>
                                                <td><?= $row['asset_vendor'] ?></td>
                                                <td><?= $row['asset_name'] ?></td>
                                                <td><?= $row['asset_number'] ?></td>
                                                <td><?= $row['cvss'] ?></td>
                                                <td><?= $row['cve_number'] ?></td>
                                                <td class="warning"><?= $row['no_progress_number'] ?></td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            <?php endif ?>
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