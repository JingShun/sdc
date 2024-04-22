<?php
$ld = new MyLDAP();
$timestamp_attribute_array = array("pwdlastset", "lastlogon", "badpasswordtime", "lastlogontimestamp", "lockouttime");

$search_ou = "ou=VPN, dc=tainan, dc=gov, dc=tw";
$move_ou = "ou=VPN, dc=tainan, dc=gov, dc=tw";
$filter = "(cn=" . $target . ")";			

$data_array = array();
$data_array['base'] = $search_ou;
$data_array['filter'] = $filter;
$data = $ld->getData($data_array);

$data_array = array();
$data_array['base'] = $move_ou;
$data_array['filter'] = "(objectClass=organizationalUnit)";
$data_array['attributes'] = array("name", "description");
$data_ou = $ld->getData($data_array);

$input_array = array("displayname", "title", "mail", "telephonenumber", "physicaldeliveryofficename");
?>

<p class="header"><?=count($data)?> entries returned from <?=$search_ou?></p>

<?php foreach($data as $entry): ?>
    <?php 
        $value_array = array();
        foreach($input_array as $input):
            $value_array[$input] = empty($entry[$input]) ? "" : $entry[$input];
        endforeach; 

        $displayname = empty($entry['displayname']) ? "" : "(" . $entry['displayname'] . ")";
        if (isDisabled($entry['useraccountcontrol'])) {
            $uac_text = "__已停用";
            $uac_checkbox = "<input type='checkbox' name='isActive' value='true'>";
            $user_icon = "<i class='user icon'></i>";
        } else {
            $uac_text = "";
            $uac_checkbox = "<input type='checkbox' name='isActive' value='true' checked>";
            $user_icon = "<i class='blue user icon'></i>";
        }
    ?>
    <form id='form-ldap' class='ui form' action='javascript:void(0)'>
        <h4 class='ui dividing header'>Entry Information</h4>
        <div class='fields'>
            <div class='six wide field'>
                <?=$user_icon?>
                <?=$entry['cn'] . $displayname . $uac_text?>
                <input type='hidden' name='type' value='editvpn' >
                <input type='hidden' name='cn' value='<?=$entry['cn']?>' >
                <?php if(!empty($entry['lockouttime'])): ?>
                    &nbsp;<i class='red lock icon'></i>鎖定
                <?php endif ?>
            </div>

            <div class='six wide field'>
                <div class='ui toggle checkbox'>
                    <?=$uac_checkbox?>
                    <label>是否啟用</label>
                </div>
            </div>
            <div class='two wide field'>
                <button type='button' id='ldap_clear_btn' class='ui button'>Cancel</button>
            </div>
            <div class='two wide field'>
                <button type='submit' id='ldap_save_btn' class='ui button'>Save</button>
            </div>
        </div>
        <div class='field'>
            <label>移動單位</label>
            <input list='brow' name='moveOU' placeholder='請選擇ou'>
            <datalist id='brow' name='moveOU'>
                <?php foreach($data_ou as $ou): ?> 
                    <?php if(isset($ou['description'])): ?>
                        <option value='<?=$ou['name']?>(<?=$ou['description']?>)'>
                    <?php else: ?>
                        <option value='<?=$ou['name']?>'>
                    <?php endif ?>
                <?php endforeach ?>
            </datalist>
        </div>
        <div class='field'>
            <label>新密碼</label>
            <input type='password' name='new_password' placeholder='新密碼'>
        </div>
        <div class='field'>
            <label>確認密碼</label>
            <input type='password' name='confirm_password' placeholder='確認密碼'>
        </div>
        <div class='field'>
            <label>姓名</label>
            <input type='text' name='displayname' value='<?=$value_array['displayname']?>' placeholder='姓名' required>
        </div>
        <div class='field'>
            <label>職稱</label>
            <input type='text' name='title' value='<?=$value_array['title']?>' placeholder='職稱' required>
        </div>
        <div class='field'>
            <label>mail</label>
            <input type='text' name='mail' value='<?=$value_array['mail']?>' placeholder='mail' required>
        </div>
        <div class='field'>
            <label>電話</label>
            <input type='text' name='telephonenumber' value='<?=$value_array['telephonenumber']?>' placeholder='電話'>
        </div>
        <div class='field'>
            <label>分機</label>
            <input type='text' name='physicaldeliveryofficename' value='<?=$value_array['physicaldeliveryofficename']?>' placeholder='分機'>
        </div>
        <div class='field'>
            <label>描述</label>
            <input type='text' name='description' placeholder='描述'>
        </div>
        <div class='ui ordered list'>
            <?php foreach($entry as $key => $val): ?>
                <?php if ($key == "distinguishedname") : ?>
                    <?php $ou_description = $ld->getAllOUDescription($search_ou, $val); ?>
                    <div class='item'><?=$key?>: <?=$val?> <br> <?=$ou_description?></div> 
                <?php elseif ($key == "dnshostname") : ?>
                    <?php $output = shell_exec("/usr/bin/dig +short ".$val) ?>
                    <div class='item'><?=$key?>: <?=$val?> | IP: <?=$output?></div>
                <?php elseif (in_array($key, $timestamp_attribute_array)) : ?>
                    <div class='item'><?=$key?>: <?=WindowsTime2DateTime($val)?></div>
                <?php elseif ($key == "useraccountcontrol") : ?>
                    <div class='item'><?=$key?>: <?=$val ." = (" . getUACDescription($val) . ")"?></div>
                <?php else : ?>
                    <div class='item'><?=$key?>: <?=$val?></div>
                <?php endif ?>
            <?php endforeach ?>
        </div>
        <p></p>
    </div>
</form>

<?php endforeach ?>
