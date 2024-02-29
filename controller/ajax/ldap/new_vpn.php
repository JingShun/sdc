<?php
$ld = new MyLDAP();

$data_array = array();
$data_array['base'] = "ou=VPN,dc=tainan,dc=gov,dc=tw"; 
$data_array['filter'] = "(objectClass=organizationalUnit)";
$data_array['attributes'] = array("name", "description");
$data_ou = $ld->getData($data_array);
?>

<form id='form-ldap' class='ui form' action='javascript:void(0)'>
<h4 class='ui dividing header'>New User Information</h4>
<div class='inline fields'>
    <div class='twelve wide field'>
    Setting
    <input type='hidden' name='type' value='newuser' >
    </div>
    <div class='two wide field'>
        <button id='ldap_clear_btn' class='ui button'>Cancel</button>
    </div>
    <div class='two wide field'>
        <button id='ldap_save_btn' class='ui button'>Save</button>
    </div>
</div>
<div class='field'>
    <label>單位<font color='red'>*</font></label>
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
    <label>
        帳號<font color='red'>*</font>
    </label>
    <input type='text' name='cn' placeholder='帳號' required>
</div>
<div class='field'>
    <label>
        新密碼<font color='red'>*</font>
    </label>
    <input type='password' name='new_password' placeholder='新密碼'>
</div>
<div class='field'>
    <label>
        確認密碼<font color='red'>*</font>
    </label>
    <input type='password' name='confirm_password' placeholder='確認密碼'>
</div>
<div class='field'>
    <label>
       姓名<font color='red'>*</font>
    </label>
    <input type='text' name='displayname' placeholder='姓名' required>
</div>
<div class='field'>
    <label>
       職稱<font color='red'>*</font>
    </label>
    <input type='text' name='title' placeholder='職稱' required>
</div>
<div class='field'>
    <label>
       mail<font color='red'>*</font>
    </label>
    <input type='text' name='mail' placeholder='mail' required>
</div>
<div class='field'>
    <label>電話</label>
    <input type='text' name='telephonenumber' placeholder='電話'>
</div>
<div class='field'>
    <label>分機</label>
    <input type='text' name='physicaldeliveryofficename' placeholder='分機'>
</div>
</form>
