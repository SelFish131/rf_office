<?php
// ------------------------------------------------------------------------
/**
 * FDCore Studio
 *
 * Главное меню системы
 *
 * @package         CodeIgniter
 * @author          NetSoul
 * @copyright		Copyright (c) 2009, FDCore
 * @link			http://fdcore.ru
 * @since			Version 1.0
*/
// ------------------------------------------------------------------------
class main extends Controller {

var $data = array ();
var $login="";
var $MSSQL;
var $MYSQL;
var $is_logged=false;
var $AccountSerial=0;
var $master_answer=false;

	function main()
	{
		parent::Controller();
       $this->load->helper(array(
       	'fdcore_framework',
       	'office_framework',
       	'rf_framework',
       	'email',
       	'prototype',
       	));
       	$this->load->helper(array('language','url','file','security','date','form','html','pay'));
        $this->load->library(array('session','parser','table','ajax'));
        $this->lang->load('office', get_lang());
        RunFunc('connectdb|connectmydb|allow_ip|check_offline|check_sql_inject');
        
        
        $this->login=get_login();
        $this->output->enable_profiler(config('profiler','core'));
        
        if(!is_logged()) redirect( base_url(). '#auth error');
        
 	}

	function index()
	{
	 	   
       $this->data['title']=lang('off_acc').gen_name_profile();
       $this->data['content']='<p>'.lang('off_welcome').gen_name_profile().lang('off_welcome2').'</p>';
       if(check_master_answer()==false)
       {
           $this->data['content'].=icon('32x32/security.png').anchor('main/profile',b(lang('off_attepprofile')));
       }
        $this->data['content'].=br(2).'<p>'.effect_toggle(b(lang('off_main_lastbuy')),'log','slide').'</p>
        <hr>'.hiddendiv("log",GetLogBuy()).$this-> _reflink();
        //$this->data['content'].=anchor('rdonate',icon('24x24/euro_currency_sign.png').lang('off_return_donate')).br(1);
        
       	if (is_gm()) {
       		$this->data['content'].=lang('off_gm_panel').anchor('manager',lang('off_gm_btn2'));
       	}
       	if(prenium_active()==true){
	       		$this->data['content'].=icon('32x32/package_download.png',lang('off_premhave')).lang('off_premium_expire').timespan(time(),premium_expire());
	       	}
       compile();
    }
    function _reflink()
    {
        if(config('REF_PAY','rf_settings')==true){
            return  icon('32x32/add_to_favorites.png').
            lang('off_bonus_info').
            heading(anchor('/registration/'.Get_AccountSerial(),
       		base_url().index_page().'/registration/'.Get_AccountSerial()),4);
        } else return "";
    }
    
    function account()
    {
        $this->data['title']=lang('off_acc_inf').gen_name_profile();
        $user = get_user();$world = get_world();
        if( is_gm() ) {
        	$query = $this->MSSQL->query(query_config('query_account_q2'),get_login());
        } else {
        	$query = $this->MSSQL->query(query_config('query_account_q1'),get_login());
        }
$this->table->set_template(tpl_table());
if ($query->num_rows() > 0)
{
    foreach ($query->result() as $row)
    {
     if(!is_gm())
        {
                                   // $this->table->add_row(lang('off_acc_serial'), $row->serial);
                                    $this->table->add_row(lang('off_acc_id'), xss_clean($row->id));
                                    $this->table->add_row(lang('off_acc_pass'), $this->_constr_change ('main/password','password',$row->password));
if(isset($row->Email))$this->table->add_row(lang('off_acc_email'),$this->_constr_change ('main/email','email',$row->Email));
//if(isset($row->Email))$this->table->add_row(lang('off_acc_email'),$row->Email);
                                    $this->table->add_row(lang('off_acc_ct'), $row->createtime);
                                    $this->table->add_row(lang('off_acc_ci'), $row->createip);
                                    $this->table->add_row(lang('off_acc_lci'), $row->lastconnectip);
                                    $this->table->add_row(lang('off_acc_llt'), $row->lastlogintime);
                                    $this->table->add_row(lang('off_acc_llot'), $row->lastlogofftime);
if(isset($row->uilock_pw))          $this->table->add_row(lang('off_acc_up'),$this->_constr_change ('main/fgpass','fgpass',$row->uilock_pw));
//if(isset($row->uilock_hintanswer))  $this->table->add_row(lang('off_acc_uh'), $row->uilock_hintanswer);
                                    $this->table->add_row(lang('off_acc_tp'), $row->TrunkPass);
                                    $this->table->add_row(lang('off_acc_ha'), $row->HintAnswer);
                                    $this->table->add_row(lang('off_acc_stat'), CheckBan($row->serial));
                                    if(config('prembtn','core')==true)$this->table->add_row(lang('off_premium'), $this->_is_premium($row->id,$row->serial));
        } else {
                                   // $this->table->add_row(lang('off_acc_serial'), $row->Serial);
                                    $this->table->add_row(lang('off_acc_id'), xss_clean($row->ID));
                                    $this->table->add_row(lang('off_acc_pass'), $this->_constr_change ('main/gmpassword','password',$row->PW));
                                    $this->table->add_row(lang('off_acc_ct'), $row->CreateDT);
                                    $this->table->add_row(lang('off_acc_lci'), $row->LastConnIP);
                                    $this->table->add_row(lang('off_acc_llt'), $row->LastLoginDT);
                                    $this->table->add_row(lang('off_acc_llot'), $row->LastLogoffDT);
                                    $this->table->add_row('Grade',$row->Grade);
                                    $this->table->add_row('SubGrade',$row->SubGrade);
                                    $this->table->add_row(lang('off_activeto'),$row->ExpireDT);
                                    $this->table->add_row(lang('off_acc_tp'), $row->TrunkPass);
                                    $this->table->add_row(lang('off_acc_ha'), $row->HintAnswer);
                                    $this->table->add_row(lang('off_acc_stat'), CheckBan($row->Serial));
                                    
        }

        $this->data['content']=$this->table->generate();
    }
} else $this->data['content']=lang('off_error_1');
        compile();
    }
    
    function _is_premium($id,$serial)
    {
    	$id=preg_name($id);
    	 $query = $this->MSSQL->query("SELECT BillingType FROM BILLING.dbo.tbl_personal_billing WHERE ID='$id' ");
	    if ($query->num_rows() > 0)
	    {
	        foreach ($query->result() as $row)
	        {
	            if($row->BillingType==1) 
	            return icon("32x32/delete.png").
	            lang('off_poff').', <a href="'.site_url().'/main/premon/'.$serial.'" '.lang('off_ptitleoff');
	             else return icon("32x32/accept.png").lang('off_pon').', <a href="'.site_url().'/main/premoff/'.$serial.'" '.lang('off_ptitleton');
	        }
	    } else return icon("32x32/delete.png").lang('off_poff').', <a href="'.site_url().'/main/premon/'.$serial.'" '.lang('off_ptitleoff');    	 
    	
    }
    
    function premon($value='')
    {
    	$this->data['title']='';
    	if (is_numeric($value)) {
    		$value=GetLoginOnAS($value);
    		$query = $this->MSSQL->query("SELECT BillingType FROM BILLING.dbo.tbl_personal_billing WHERE ID='$value' ");
	    if ($query->num_rows() == 0)
	    {
	        $this->MSSQL->query("INSERT INTO BILLING.dbo.tbl_personal_billing (ID,BillingType,EndDate,RemainTime) 
		VALUES ((CONVERT (binary,'".get_login()."')),1,DATEADD(year, 1, GETDATE()),0)");
	    }
	    
    		$this->MSSQL->query("UPDATE BILLING.dbo.tbl_personal_billing SET BillingType=2 WHERE ID='$value' ");
    	}
    	 $this->data['content']=lang('off_premactive_on');
    	 compile();
    }
    
    function premoff($value='')
    {
    	$this->data['title']='';
    	if (is_numeric($value)) {
    		$value=GetLoginOnAS($value);
    		$query = $this->MSSQL->query("UPDATE BILLING.dbo.tbl_personal_billing SET BillingType=1 WHERE ID='$value' ");
    	}
    	$this->data['content']=lang('off_premactive_off');
    	compile();
    }    
    function _constr_change($action,$name,$val)
    {
        if(check_master_answer()==false) return form_open($action).form_input($name,xss_clean($val)).form_submit('submit',lang('off_btn_change')).'</form>';
        if(check_master_answer()==true) return b(preg_name($val)).ajaxtoggle(icon('16x16/edit.png'),'che_'.$name).'<div id="che_'.$name.'" style="display:none;">'.form_open($action).form_input($name,xss_clean($val)).br(1).lang('off_profile_master').br(1).form_password('master').form_submit('submit',lang('off_btn_change')).'</form></div>';
    }
    function logout()
    {
        $this->session->sess_destroy();
        @session_destroy();
        redirect( base_url() . '#logout' );
    }
    function password()
    {
        $this->data['title']=lang('off_title_chpass').gen_name_profile();
        $password=office_secure($this->input->post('password'));
        $stop="";
        if(check_master_answer()==true)
         {
            $master=office_secure($this->input->post('master'));
            if(md5($master)<>show_master_answer()) $stop=lang('off_master_error');
         }
        if(strlen($password)>16 || strlen($password)<2) $stop=lang('off_login_passwsh');
        if($stop=="")
        {
            $user = get_user();
            $this->MSSQL->query("UPDATE {$user}.dbo.".query_config('table_account')." SET Password = (CONVERT(binary, '$password')) WHERE Id = '".get_login()."'");
            $this->data['content']='<p>'.icon('32x32/accept.png').lang('off_ch_pass')."</p>";
            $this->session->set_userdata('passw', xss_clean($password));
             $_SESSION['passw']	=	xss_clean($password);
        } else $this->data['content']='<p>'.icon('32x32/delete.png').lang('off_error').": ".$stop.go_back()."</p>";
        compile();
    }
//07_________________________________
    function gmpassword()
    {
        $this->data['title']=lang('off_title_chpass').gen_name_profile();
        $password=office_secure($this->input->post('password'));
        $stop="";
        if(strlen($password)>16 || strlen($password)<2) $stop=lang('off_login_passwsh');
        if($stop=="")
        {
            $user = get_user();
            $this->MSSQL->query("UPDATE {$user}.dbo.tbl_StaffAccount SET PW = (CONVERT(binary, '$password')) WHERE ID = '".get_login()."'");
            $this->data['content']='<p>'.icon('32x32/accept.png').lang('off_ch_pass')."</p>";
            $this->session->set_userdata('passw', xss_clean($password));
             $_SESSION['passw']	=	xss_clean($password);
        } else $this->data['content']='<p>'.icon('32x32/delete.png').lang('off_error').": ".$stop.go_back()."</p>";
        compile();
    }    
    function email()
    {
        $this->data['title']=lang('off_chmail').gen_name_profile();
        $email=office_secure($this->input->post('email'));
        $stop="";
         if(check_master_answer()==true)
         {
            $master=office_secure($this->input->post('master'));
            if(md5($master)<>show_master_answer()) $stop=lang('off_master_error');
         }
        if (valid_email($email) and trim($stop)=='')
        {
            $user = get_user();
            $this->MSSQL->query("UPDATE {$user}.dbo.".query_config('table_account')." SET email = '$email' WHERE Id = '".$this->login."'");
            $this->data['content']='<p>'.icon('32x32/accept.png').lang('off_ch_email')."</p>";
        }
        else
        {
            $this->data['content']='<p>'.icon('32x32/delete.png').lang('off_reg_email').$stop.go_back()."</p>";
        }
        compile();
    }
    function fgpass()
    {
        $this->data['title']=lang('off_title_fg').gen_name_profile();
        $FireGuard=analyze_name(office_secure($this->input->post('fgpass')));
        $stop="";
        if(check_master_answer()==true)
         {
            $master=office_secure($this->input->post('master'));
            if(md5($master)<>show_master_answer()) $stop=lang('off_master_error');
         }
        if(strlen($FireGuard)>20 || strlen($FireGuard)<6 || strpos('\'',$FireGuard)) $stop=lang('off_login_passwsh');
        if($stop=="")
        {
            $user = get_user();
            $this->MSSQL->query("UPDATE {$user}.dbo.tbl_UserAccount SET uilock_pw = (CONVERT(binary, '$FireGuard')) WHERE id = '".get_login()."'");
            $this->data['content']='<p>'.icon('32x32/accept.png').lang('off_title_chfg')."</p>";
        } else $this->data['content']='<p>'.icon('32x32/delete.png').lang('off_error').$stop.go_back()."</p>";
        compile();
    }

    function log($date=0)
    {
    $this->load->library('calendar');
    $this->data['title']=lang('off_title_log');
    $text	='';
    $days	=array();
    $Y		=date('Y');
    $m		=date('m');
    if($date==0)
    {
        $query = $this->MYSQL->query("SELECT * FROM buy_log WHERE login='".get_login()."' AND LEFT(date,".strlen(date('Y-m')).")='".date('Y-m')."' ORDER BY id DESC LIMIT 5");
    }   else
    {
       $date=office_secure($date);
       $query = $this->MYSQL->query("SELECT * FROM buy_log WHERE login='".get_login()."' AND LEFT(date,".strlen($date).")='$date' ORDER BY id DESC");
    }
    $this->table->set_template(tpl_table());
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
            $this->table->add_row('#'.$row->id,$row->date,base64_decode($row->text));
            $delimer=explode('-',$row->date);
            $days[(int)substr($delimer[2],0,2)]=base_url().index_page().'/main/log/'.$delimer[0].'-'.$delimer[1].'-'.substr($delimer[2],0,2);
        }
        $text=$this->table->generate();
    }
        if($date==0) $text2=$this->calendar->generate($Y,$m,$days); else $text2=go_back('',true).heading($date,2);
        $this->data['content']=
	        $text2.
	        br(2).
	        '<div id="full_log">'.$text.'</div>'.
		    ajax_load('main/show_log',icon('24x24/shopping_cart.png').lang('off_showallbuy'),'full_log');
        compile();
    }
    function show_log()
    {
    	$result	=GetLogBuy(true);
    	echo win2utf($result);
    }
/*
 * построение списка персонажей
 * => /main/characters/
 */
    function characters()
    {
//        заголовок страницы
        $this->data['title']=lang('off_title_list').gen_name_profile();
//        узнаём имена баз
        $user = get_user();
        $world = get_world();
//        узнаём список персонажей аккаунта
        $query = $this->MSSQL->query("SELECT * FROM {$world}.dbo.tbl_base WHERE Account = '".get_login()."'");
        $this->data['content']='';
//        если персонажи есть то составляем список
        $this->table->set_template(tpl_table());
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
                {
                    if($row->DCK==0)$this->table->add_row(b(preg_name($row->Name)),$this->_char_menu($row->Serial,false));
                    if($row->DCK==1)$this->table->add_row(b(icon('32x32/trash_can.png',lang('off_cheinfo_del')).$row->DeleteName),$this->_char_menu($row->Serial,true));
                }
//        генерируем табличку
          $this->data['content'].=$this->table->generate();
//        а если персов нет?
        } else $this->data['content']=icon('64x64/delete_image.png').lang('off_notfoundpl');
        $this->data['content'].='<hr>'.icon('32x32/info.png').effect_toggle(lang('off_shop_titleinfo'),'info','slide').
        hiddendiv('info','<div>'.
         icon('32x32/trash_can.png').lang('off_cheinfo_del').br(1)
        .icon('32x32/add_user.png').lang('off_cheinfo_ressurect').br(1)
        .icon('32x32/delete_user.png').lang('off_cheinfo_delete').br(1)
        .icon('32x32/support.png').lang('off_cheinfo_safe').br(1)
        .icon('32x32/search_database.png').lang('off_inven_info').br(1)
        .icon('32x32/tools.png').lang('off_char_cleardoctip').br(1)
        .icon('32x32/id_card.png').lang('off_cheinfo_info').'</div>');
//        выводим ;)
         compile();
    }

/*
 * построение меню персонажа
 * принимает скрийник перса и статус, удалён или нет
 */
function _char_menu($serial,$del=false)
{
	$text='';
	
	//    информация о персонаже
	if(config('char_info','core')){
	    $text= anchor('main/char/'.$serial,icon('32x32/id_card.png',lang('off_cheinfo_info')));
    }
	//    если не удалён то кнопка удаления
	if(config('del_user','core')){
		if($del==false) $text.=anchor('main/delete_char/'.$serial,icon('32x32/delete_user.png',lang('off_cheinfo_delete')));
	}
//    если удалён то кнопка оживления
	if(config('res_user','core')){
		if($del==true) $text.=anchor('main/ressurect_char/'.$serial,icon('32x32/add_user.png',lang('off_cheinfo_ressurect')));
	}
	//    телепортация на элан
	if(config('teleport','core')){
	    $text.=anchor('main/teleport/'.$serial,icon('32x32/support.png',lang('off_cheinfo_safe')));
	}
	//	инвентарь
	if(config('inven','core')){
	    $text.=anchor('main/inven/'.$serial,icon('32x32/search_database.png',lang('off_inven_info')));
	} 
	if(config('dataclear','core')){
	 if(is_online($serial)==FALSE && $del==FALSE)   
	$text.=anchor('main/dataclear/'.$serial,icon('32x32/tools.png',lang('off_charclear')));
	}
	    return $text;
}

/*
 * удаление персонажей
 */
function delete_char($serial)
{
        if(!secure_serial_check($serial)  || !config('del_user','core') ) redirect( base_url() . '#warn');        
        if(!is_numeric($serial)) redirect( 'main' );
//      заголовок страницы
        $this->data['title']=lang('off_title_delete');
//      узнаём имена баз
        $world = get_world();
//        удвление
        $query = $this->MSSQL->query("SELECT Name FROM {$world}.dbo.tbl_base WHERE Account = '".get_login()."' AND Serial=$serial");
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
                {
                    $this->MSSQL->query("UPDATE {$world}.dbo.tbl_base SET Name = (CONVERT(binary, '*".$serial."')),DeleteName='".preg_name($row->Name)."' , DCK=1 WHERE Serial = '".$serial."' AND Account = '".get_login()."'");
                    $this->MSSQL->query("UPDATE {$world}.dbo.tbl_general SET DCK=1 WHERE Serial = '".$serial."'");
                }
                $this->data['content']=icon('64x64/delete_user.png').lang('off_title_del1');
        } else  $this->data['content']=icon('64x64/block.png').lang('off_title_del2');
         compile();
}

/*
 * восстановление персонажей
 * => /main/ressurect_char/
 */
function ressurect_char($serial)
{
        if(!secure_serial_check($serial) || !config('res_user','core')) redirect( base_url() . '#warn');
         
        if(!is_numeric($serial)) redirect( 'main' );
        $serial=office_secure($serial);
//      заголовок страницы
        $this->data['title']=lang('off_ressurect_char_title');
//      узнаём имена баз
        $world = get_world();
        $query = $this->MSSQL->query("SELECT Serial, DeleteName FROM {$world}.dbo.tbl_base WHERE Account = '".get_login()."' AND Serial=$serial");
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
                {
                    if(player_exits(preg_name($row->DeleteName))==true) $new_name=time(); else $new_name=$row->DeleteName;
                    $this->MSSQL->query("UPDATE {$world}.dbo.tbl_base SET Name = '".preg_name($new_name)."', DeleteName='*', DCK=0 WHERE Serial = $serial");
                    $this->MSSQL->query("UPDATE {$world}.dbo.tbl_general SET DCK=0 WHERE Serial = ".$serial);
                }
               	$this->data['content']=icon('64x64/add_user.png').lang('off_ressurect_char_true');
        } else 	$this->data['content']=icon('64x64/block.png').lang('off_ressurect_char_false');
        compile();
}

function char($serial)
{
        if(!is_numeric($serial)) redirect( 'main' );
        if(!secure_serial_check($serial)) redirect( '#warn');        
//      заголовок страницы
        $this->data['title']=lang('off_title_info');
//      узнаём имена баз
        $world = get_world();
        $query = $this->MSSQL->query("
        SELECT *
        FROM {$world}.dbo.tbl_base, {$world}.dbo.tbl_general
        WHERE {$world}.dbo.tbl_base.Account = '".get_login()."'
        AND {$world}.dbo.tbl_base.Serial={$serial}
        AND {$world}.dbo.tbl_base.Serial={$world}.dbo.tbl_general.Serial");
        $this->table->set_template(tpl_table());
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
                {
                    if($row->DCK==0)$this->table->add_row(b(lang('off_char_name')),preg_name($row->Name));
                    $name=preg_name($row->Name);
                    if($row->DCK==1)$this->table->add_row(b(lang('off_char_name')),icon('24x24/delete_user.png').preg_name($row->DeleteName));
                    $this->table->add_row(b(lang('off_char_race')),get_race($row->Race));
                    $this->table->add_row(b(lang('off_char_totalp')),get_total_min($row->TotalPlayMin));
                    $this->table->add_row(b(lang('off_char_guild')),get_guild($row->GuildSerial));
                    $this->table->add_row(b(lang('off_char_pvp')),round($row->PvpPoint));
                    $this->table->add_row(b(lang('off_char_class')),get_rfclass($row->Class));
                    $this->table->add_row(b(lang('off_char_level')),$row->Lv);
                    $this->table->add_row(b(lang('off_char_money')),$row->Dalant);
                    $this->table->add_row(b(lang('off_char_gold')),$row->Gold);
                    $this->table->add_row(b(lang('off_char_create')),$row->CreateTime);
                    if(is_online($row->Serial)==true)$stat=icon('32x32/globe.png').b(lang('off_char_online')); else $stat=icon('32x32/globe_warning.png').b(lang('off_char_offline'));
                    $this->table->add_row(b(lang('off_acc_stat')),$stat);
                }
        $this->data['content']=icon('64x64/user.png').$this->table->generate().ItemsInChar($name);
        } else $this->data['content']=icon('32x32/red_button.png').lang('off_error');
        compile();
}
function teleport($serial)
{
    if(!secure_serial_check($serial)) redirect( 'main' );
    $world = get_world();
    //      заголовок страницы
    $this->data['title']=lang('off_title_tp');
    if(is_online($serial)==false)
    {
        $this->MSSQL->query("UPDATE {$world}.dbo.tbl_general SET Map='13', X='-5242', Y='429', Z='-2949' WHERE Serial = '$serial'");
        $this->data['content']=lang('off_tp_safe_ok').icon('elan_tp.jpg');
    } else $this->data['content']=icon('64x64/warning.png').lang('off_online_chk');
    compile();
}

function news()
{
	   redirect( 'main' );
       $this->data['title']=lang('off_acc').get_login();
       $this->data['content']=$this->_rss_get();
       compile();
}

function _rss_get($count=0)
{
       $this->config->load('rf_settings',FALSE, TRUE);

       $text='';
       if($count==0) $count=$this->config->item('RSS_NUM');
       if($this->config->item('RSS')==true)
       {
            $this->load->library('RSSParser', array('url' => $this->config->item('RSS_LINK'), 'life' => 2));
            $data = $this->rssparser->getFeed($count);
        foreach ($data as $item):
            $this->table->clear();
            $this->table->set_template(tpl_table());
            $this->table->add_row(b(Utf8Win($item['title'])));
            $this->table->add_row(Utf8Win($item['description']));
            $this->table->add_row('<div align="right">'.anchor($item['link'],lang('off_rss_more')).'</div>');
            $text.=br(3).$this->table->generate();
        endforeach;
       }
        return $text;
}
function profile()
{
       $this->data['title']=lang('off_acc').get_login();
       $this->table->set_template(tpl_table());
       $query = $this->MYSQL->query("SELECT * FROM profile WHERE AccountSerial=".Get_AccountSerial());
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
           $this->table->add_row(lang('off_profile_name1'),		form_input('username',urldecode($row->username)),'');
           $this->table->add_row(lang('off_profile_icq'),		form_input('icq',$row->icq),'');
           $this->table->add_row(lang('off_profile_skype'),		form_input('skype',$row->skype),'');
		if(config('module_allowip','core')==true){
           $this->table->add_row(lang('off_profile_allowip'),	form_input(
           	array('name'=> 'allow_ip','value'=> $row->allow_ip,'onclick'=>"Element.show('allip')")),ajaxtoggle(icon('post_exclamation.png'),'allip').
            hiddendiv("allip",lang('off_allowip')).b(lang('off_youip').$this->session->userdata('ip_address')));
        }
           $this->table->add_row('',form_submit('mysubmit',lang('off_btn_change')),'');
        }
    } else {
      		$this->table->add_row(lang('off_profile_name1'),	form_input('username'),'');
      		$this->table->add_row(lang('off_profile_icq'),		form_input('icq'),'');
      		$this->table->add_row(lang('off_profile_skype'),	form_input('skype'),'');
		if(config('module_allowip','core')==true){      		
      		$this->table->add_row(lang('off_profile_allowip'),	form_input(
      			array('name'=> 'allow_ip','onclick'=>"Element.show('allip')")),ajaxtoggle(icon('post_exclamation.png'),'allip').
        		hiddendiv(
        			"allip",lang('off_allowip')).b(lang('off_youip').$this->session->userdata('ip_address')
        			)
        		);
    		}
       		$this->table->add_row(lang('off_profile_master').icon('24x24/warning.png'), form_input('master_answer'),lang('off_profile_masternotice'));
       		$this->table->add_row(lang('off_profile_mac'), 		form_input('mac_adress'),lang('off_profile_macnotice'));
       		$this->table->add_row(lang('off_profile_btnnotice1'),form_submit('mysubmit',lang('off_profile_btnfirst')));
    }
       $this->data['content']=heading(icon('security_red.png').lang('off_profile_notice2').icon('security_blue.png'),2).
       form_open('main/uprofile').$this->table->generate().form_close();
       compile();
}

function uprofile()
{
    $this->data['title']=lang('off_acc').get_login();
    $username	=	urlencode(office_secure($this->input->post('username')));
    $icq		=(int)trim(office_secure($this->input->post('icq')),"-");
    $skype		=	urlencode(office_secure($this->input->post('skype')));
    $master_answer=	office_secure($this->input->post('master_answer'));
    $allow_ip	=	office_secure($this->input->post('allow_ip'));
    $mac_adress=	urlencode(office_secure($this->input->post('mac_adress')));
    if($master_answer<>$this->input->post('master_answer')) redirect( base_url() . 'profile/#error');
    $block=TRUE;
    if(strlen($this->session->userdata('ip_address'))>2)
	{
	    if(substr($this->session->userdata('ip_address'),0,strlen($allow_ip))<>$allow_ip)
		    {
		    	$this->data['content']=icon('32x32/edit_profile.png').lang('off_profile_check_ip');	
		    	compile();	
		    	$block=FALSE;
		    } 
    }
    if(!check_master_answer() && $master_answer && $block<>FALSE)
    {
        $this->MYSQL->query("
        INSERT INTO profile SET username='{$username}',
            icq='{$icq}',
            skype='{$skype}',
            master_answer='".md5($master_answer)."',
            mac_adress='{$mac_adress}',
            allow_ip='{$allow_ip}',
            AccountSerial=".Get_AccountSerial());
        $this->data['content']=icon('32x32/edit_profile.png').lang('off_profile_save_ok');
    } else 	{
        if(check_master_answer()==FALSE && $block<>FALSE) redirect( 'main/profile' );
        $this->MYSQL->query("
        UPDATE profile
        SET username='{$username}',
            icq='{$icq}',
            skype='{$skype}',
            allow_ip='{$allow_ip}'
            WHERE AccountSerial=".Get_AccountSerial());
        $this->data['content']=icon('32x32/edit_profile.png').lang('off_profile_save_up');
        	}
       compile();
}
function inven($serial)
{
        if(!is_numeric($serial)) redirect( 'main' );
        if(!secure_serial_check($serial)) redirect( '#warn'); 
        $this->table->set_template(tpl_table());
        $world = get_world();
    	$query = $this->MSSQL->query("SELECT * FROM {$world}.dbo.tbl_inven WHERE Serial = '$serial'");
        if ($query->num_rows() > 0)
        {
            $row = $query->row_array(); 
            $this->table->set_heading('№', lang('off_inven_items'),lang('off_inven_count'), lang('off_inven_update'),lang('off_inven_do'));
            for ($i=0; $i < 100; $i++) { 
            	$this->table->add_row($i,GetID($row['K'.$i],false),$row['D'.$i],show_talic(bindechex($row['U'.$i],3)),anchor("main/del_inven/$serial/$i",icon('16x16/delete.png',lang('off_inven_del'))));
            }
        } 
        $this->data['title']=lang('off_inven_title');
        $this->data['content']=$this->table->generate();
       compile(); 
}
function del_inven($serial,$num)
{
	if($num>100) redirect( base_url() );
	$numeric=(int)office_secure($num);
    if(!is_numeric($serial)) redirect( 'main' );
    if(!secure_serial_check($serial)) redirect( '#warn'); 	
    $this->table->set_template(tpl_table());
    $world = get_world();
	$query = $this->MSSQL->query("SELECT K{$num},D{$num},U{$num} FROM {$world}.dbo.tbl_inven WHERE Serial = '$serial'");
    if ($query->num_rows() > 0)
    {
        $row = $query->row_array(); 
        $this->table->add_row(lang('off_inven_items'),GetID($row['K'.$num],false));
        $this->table->add_row(lang('off_inven_count'),$row['D'.$num]);
        $this->table->add_row(lang('off_inven_update'),show_talic(bindechex($row['U'.$num],3)));
        $this->table->add_row(
        	form_open('main/acceptdelitem').
        	form_hidden('num', $num).
        	form_hidden('serial', $serial).
        	form_submit('accept', lang('off_accept_del')).
        	form_close(),go_back('',true));
    }
    	$this->data['title']=lang('off_acceptdelitemtitle');
    	$this->data['content']=$this->table->generate();
       	compile(); 
}
function acceptdelitem()
{
	$num	=	office_secure($this->input->post('num'));
	$serial	=	office_secure($this->input->post('serial'));
	$this->data['title']=lang('off_acceptdelitemtitle');
	$world = get_world();
	if($num>100) redirect( base_url() );
	if(!is_numeric($serial)) redirect( 'main' );
	if(!secure_serial_check($serial)) redirect( '#warn'); 
	$this->MSSQL->query("UPDATE {$world}.dbo.tbl_inven SET K{$num}='-1', D{$num}='0',U{$num}=0xfffffff WHERE Serial='$serial'");
	$this->data['content']=icon('32x32/trash_can.png').lang('off_acceptdelitem_suss').go_back('main/inven/'.$serial	);
	compile(); 
}

function dataclear($serial)
{
	if(!is_numeric($serial)) redirect( base_url() );
	if(config('dataclear','core')==FALSE) redirect( base_url() );
	if(is_online($serial)==FALSE) redirect( base_url() );
	 
	if(!secure_serial_check($serial)) redirect( '#warn'); 
	$world = get_world();
    if(is_online($serial)==FALSE)
    {
	    $query = $this->MSSQL->query("SELECT Lv FROM {$world}.dbo.tbl_base WHERE Serial = '$serial'");
	    if ($query->num_rows() > 0)
	    {
	    	$row = $query->row_array(); 
	    	$now=$row['Lv'];
	    	$max=50;
	    	if($now<50) $max=50;
	    	elseif($now==50) $max=50;
	    	elseif($now<=55 && $now>=51) $max=55;
	    	elseif($now>=56) $max=65;
	    	$this->MSSQL->query("UPDATE {$world}.dbo.tbl_base SET Lv ='".$row['Lv']."' WHERE Serial = '$serial'");
	    	$this->MSSQL->query("UPDATE {$world}.dbo.tbl_general SET MaxLevel='$max' WHERE Serial = '$serial'");
	        //$this->MSSQL->query("DELETE {$world}.dbo.tbl_NpcData WHERE Serial = '$serial'");	    	
		}
        $this->data['content']=icon('32x32/edit_profile.png').lang('off_clearsucc');
    } else $this->data['content']=icon('64x64/warning.png').lang('off_online_chk');
	compile(); 	
}
function page($page='')
{
		$page=office_secure($page);
		if(!file_exists(APPPATH.'views/static/'.$page.'.php')){
			$page='index';
		}
		$this->data['title']='Static Pages';
	    $this->data['content']=$this->parser->parse('static/'.$page,array('title'=>'Static'),true);
		compile(); 
}
}
?>