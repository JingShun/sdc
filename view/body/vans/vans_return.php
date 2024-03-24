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
                                <p>共有 <?= count($nicsOuRows) ?> 個機關！</p>
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
                                <p>共有 <?= count($nicsOuRows) ?> 個機關！</p>
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