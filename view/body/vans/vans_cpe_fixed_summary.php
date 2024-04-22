<!-- 透視表CDN PivotTable.js (2024.04.22 當前最新版2.23.0) -->
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pivottable@2.23.0/dist/pivot.min.css">
<script src="https://cdn.jsdelivr.net/npm/pivottable@2.23.0/dist/pivot.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pivottable@2.23.0/dist/pivot.zh.js"></script>

<div id="page" class="container">
    <div id="content">
        <div class="sub-content show">
            <div class="post">
                <?= $route->createBreadcrumbs(' > '); ?>
                <h2 class="ui dividing header">VANS修補情形</h2>
                <div class="post_cell">
                    <div class="ui secondary pointing menu">
                        <a class="active item">每月累計修補情形</a>
                        <a class="item">各局處累計修補情形</a>
                        <a class="item">透視圖</a>
                    </div>
                    <div class="ui noborder bottom attached segment">
                        <div class="tab-content yonghua show">
                            <?php if (count($month_gradually) == 0) : ?>
                                <p>查無此筆紀錄</p>
                            <?php else : ?>
                                <table class="ui celled table">
                                    <thead>
                                        <tr>
                                            <th>月份</th>
                                            <th>新增數</th>
                                            <th>修補數</th>
                                            <th>修補率</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($month_gradually as $row) : ?>
                                            <tr>
                                                <td><?= $row['month'] ?></td>
                                                <td><?= $row['add_cnt'] ?></td>
                                                <td><?= $row['fixed_cnt'] ?></td>
                                                <td><?= $row['fixed_rate'] ?></td>
                                                <td>
                                                    <div class='ui teal progress yckuo' data-sort-value='<?= round($row['fixed_rate'], 0) ?>' data-percent='<?= round($row['fixed_rate'], 0) ?>' data-total='100'>
                                                        <div class='bar'>
                                                            <div class='progress'></div>
                                                        </div>
                                                        <div class='label'><?= $row['fixed_cnt'] ?>/<?= $row['fixed_cnt'] + $row['add_cnt'] ?></div>
                                                    </div>
                                                </td>
                                                <!-- <td class="<?= '' ? 'warning' : '' ?>"><?= '' ?></td> -->
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            <?php endif    ?>
                        </div> <!-- end of .tabular-->
                        <div class="tab-content minjhih">
                            <?php if (count($ou_gradually) == 0) : ?>
                                <p>查無此筆紀錄</p>
                            <?php else : ?>

                                <table class="ui celled table">
                                    <thead>
                                        <tr>
                                            <th>機關</th>
                                            <th>新增數</th>
                                            <th>修補數</th>
                                            <th>修補率</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($ou_gradually as $row) : ?>
                                            <tr>
                                                <td><?= $row['ou'] ?></td>
                                                <td><?= $row['add_cnt'] ?></td>
                                                <td><?= $row['fixed_cnt'] ?></td>
                                                <td><?= $row['fixed_rate'] ?></td>
                                                <td>
                                                    <div class='ui teal progress yckuo' data-sort-value='<?= round($row['fixed_rate'], 0) ?>' data-percent='<?= round($row['fixed_rate'], 0) ?>' data-total='100'>
                                                        <div class='bar'>
                                                            <div class='progress'></div>
                                                        </div>
                                                        <div class='label'><?= $row['fixed_cnt'] ?>/<?= $row['fixed_cnt'] + $row['add_cnt'] ?></div>
                                                    </div>
                                                </td>
                                                <!-- <td class="<?= '' ? 'warning' : '' ?>"><?= '' ?></td> -->
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            <?php endif    ?>
                        </div> <!-- end of .tabular-->
                        <div class="tab-content">
                            <div id="output" class="record_content"></div> <!-- end of #record_content-->
                        </div> <!-- end of .tabular-->
                    </div> <!-- end of .attached.segment-->
                </div><!--end of .post_cell-->
            </div><!--end of .post-->
        </div><!--end of .sub-content-->
        <div style="clear: both;">&nbsp;</div>
    </div><!-- end #content -->
</div> <!--end #page-->

<script>
    var myData = <?= !empty($cpeDetailList) ? json_encode($cpeDetailList, JSON_UNESCAPED_UNICODE) : '[]' ?>;
    $("#output").pivotUI(
        myData, {
            rows: ["product"],
            cols: ["month_at"],
            vals: ["fixed_cnt"],
            rowOrder: "value_z_to_a",
        },
        false,
        "zh"
    );
</script>