<?php
// ------------------------------------------------------------------------
/**
 * FDCore Studio
 *
 * Главная страница кабинета, онаже авторизация
 *
 * @package         CodeIgniter
 * @author          NetSoul
 * @copyright		Copyright (c) 2009 - 2011, FDCore
 * @link			http://fdcore.ru
 * @since			Version 1.0
*/
// ------------------------------------------------------------------------
class shop extends Controller {

var $data = array ();
var $is_logged=false;
var $AccountSerial=0;
var $master_answer=false;

// ------------------------------------------------------------------------
# шапка от версии 0.6
    function shop()
    {
		parent::Controller();
        $this->load->helper(array(
        	'fdcore_framework',
        	'office_framework',
        	'rf_framework_helper',
        	));
        $this->load->helper(array('language','url','file','security','date','form','html','pay'));
        $this->load->library(array('session','table','parser','cache','ajax'));
        $this->lang->load('office', get_lang());
        $this->data['title']=lang('off_shop_title');
        RunFunc('connectdb|connectmydb|check_offline|check_sql_inject');
        $this->output->enable_profiler(config('profiler','core'));
	}
// ------------------------------------------------------------------------
# 0.7
function index()
{
    if(!is_logged()) redirect( base_url() );
    $this->lang->load('service', get_lang());
    $TotalDonateResult 	= $this->MYSQL->query("SELECT COUNT(*) as count FROM items WHERE type = 0");
    $TotalDonate 		= $TotalDonateResult->row_array();
    $TotalBonusResult 	= $this->MYSQL->query("SELECT COUNT(*) as count FROM items WHERE type = 1");
    $TotalBonus 		= $TotalBonusResult->row_array();
    $text=heading(lang('off_shop_section'), 2);
    $text.=heading(icon('32x32/info.png').lang('off_username_you_have').donate_show(Get_AccountSerial()).lang('off_money_donate').', '.
    bonus_show(Get_AccountSerial()).lang('off_money_bonus'), 4);
    $this->table->set_template(tpl_table());
    # покупка доната
if(config('module_donate','core')==true){
    $this->table->add_row(icon('32x32/insert_to_shopping_cart.png'),anchor('shop/donate',lang('off_buydonate')),'['.$TotalDonate['count'].']');
}
	# покупка бонусов
if(config('module_bonus','core')==true){
    $this->table->add_row(icon('32x32/package_add.png'),anchor('shop/bonus',lang('off_buybonus')),'['.$TotalBonus['count'].']');
}
    # информация о пополнени счёта
if(config('module_buyinfo','core')==true){
	$this->table->add_row(icon('32x32/dollar_currency_sign.png'),anchor('shop/buyinfo',lang('off_buyinfo')),'');
}
# дополнительные сервисы
if(config('module_service','core')==true){
    $this->table->add_row(icon('32x32/note_accept.png'),anchor('service',lang('off_addserv')),'');
}
    #покупка премиумов
if(config('module_getpremium','core')==true){
	$this->table->add_row(icon('32x32/package_add.png'),anchor('service/getpremium',lang('service_buy_btn')),'');
}
# конвертер
if(config('module_convert','core')==true){
	$this->table->add_row(icon('32x32/chart.png'),anchor('service/convert',lang('off_convertbp_title')),lang('off_kurs').' : '.config('convert_1gp','core').' BP');
}
	# пополнениче счёта через wmz
if(config('module_wmzform','core')==true){
	$this->table->add_row(icon('32x32/dollar_currency_sign.png'),anchor('shop/wmzform',lang('off_addwmz')),'');
}
	 //$this->table->add_row(icon('32x32/report.png'),anchor('service/convertcash',lang('off_exchange')),lang('off_kurs').' : '.config('convert_1gpcash','core').' Cash Shop');
	//$this->table->add_row(icon('32x32/euro_currency_sign.png'),anchor('shop/yandex','Пополнить счёт через Yandex Money'),'');
	//$this->table->add_row(icon('32x32/credit_cart.png'),anchor('shop/paypal','Пополнить счёт через PayPal'),'');
	//$this->table->add_row(icon('32x32/credit_cart.png'),anchor('flypay','Пополнить счёт через FlyPay'),'');
	//$this->table->add_row(icon('32x32/mobile_phone.png'),anchor('flypay/sms','Пополнить счёт через FlyPay - SMS'),'');	
	
    $text.='<div align=center>'.$this->table->generate().'</div>';
    $this->data['content']=$text;
    compile();
}
// ------------------------------------------------------------------------
# 0.7
function buyinfo()
{
	if(config('module_buyinfo','core')==FALSE){	redirect( 'main' );	}
    $this->data['title'].=' - '.lang('off_shop_titleinfo');
    $this->data['content']=config('buyinfo','rf_settings');
    compile();
}
// ------------------------------------------------------------------------
# 0.6
function donate()
{
    if(!is_logged()) redirect( base_url() );
    if(config('module_donate','core')==FALSE){	redirect( 'main' );	}
    $i=0;
    $this->output->cache(30);
    //        стиль таблицы
    $this->table->set_template(tpl_table());
    //        заголовки
    $text=heading(icon('64x64/folder_full.png').lang('off_cat'), 2);
//        список категорий
    $query = $this->MYSQL->query("SELECT id, title, cid FROM cat ORDER BY id");
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
            $TotalResult = $this->MYSQL->query("SELECT COUNT(*) as count FROM items WHERE type=0 and catalog = ".$row->cid);
            $Total = $TotalResult->row_array();
            if($Total['count']>0)
            {
                $i++;
                $this->table->add_row($i,icon('32x32/folder.png').anchor('/shop/donate_cat/'.$row->cid,urldecode($row->title).' ('.$Total['count'].')'));
            }
        }
        $text.=$this->table->generate();
    } else $text.=icon('64x64/block.png').lang('off_admin_empty');
   $this->data['content']=$text;
    compile('',false);
}
// ------------------------------------------------------------------------
# 0.6
function donate_cat($id=0)
{
    if(!is_logged())     redirect( base_url() );
    if(config('module_donate','core')==FALSE){	redirect( 'main' );	}
    if(!is_numeric($id)  || $id <= 0) redirect( 'shop/donate' );
    $i=1;
	$text='';
    $id=intval($id);
    $this->table->set_template(tpl_table());
    $query = $this->MYSQL->query("SELECT title FROM cat WHERE cid=".intval($id));
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
            $text.=heading(urldecode($row->title), 2);
            $this->table->add_row(b('№'),b(lang('off_items_name')),b(lang('off_items_price')),b(lang('off_shop_buycount')),b(lang('off_do')));
            $query = $this->MYSQL->query("SELECT * FROM items WHERE type=0 AND catalog=".intval($id)." ORDER BY count DESC");
                if ($query->num_rows() > 0)
                {
                    foreach ($query->result() as $row)
                    {
                        $this->table->add_row($i,'<a title="'.urldecode($row->description).'">'.urldecode($row->title).'</a>',
                            urldecode($row->price),
                            $row->count,
                            $this->_action_donateinfo($row->id).$this->_action_donatebuy($row->id,$row->price));
                        $i++;
                    }
                    $text.=$this->table->generate();
                }
        }
    } else $text.=icon('dialog-error.png').lang('off_notfound').go_back('shop');
   $this->data['content']=$text;
    compile();
}
// ------------------------------------------------------------------------
# 0.6
function _action_donateinfo($id=0)
{
	$id=office_secure($id);
    return anchor('/shop/donate_info/'.$id,icon('32x32/help.png',lang('off_inform')));
}
// ------------------------------------------------------------------------
# 0.6
function donate_info($id=0)
{
    if(!is_logged())     redirect( base_url() );
    if(!is_numeric($id) || $id < 0) redirect( 'shop/donate' );
    if(config('module_donate','core')==FALSE){	redirect( 'main' );	}
    $text='';
    $id=intval($id);
    $this->table->set_template(tpl_table());
     $query = $this->MYSQL->query("SELECT * FROM items WHERE type=0 AND id=".intval($id));
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
                        $this->table->add_row(lang('off_items_name'),urldecode($row->title));
                        $this->table->add_row(lang('off_items_desc'),urldecode($row->description));
if($row->picture<>'')   $this->table->add_row(lang('off_items_pic'),'<img src='.$row->picture.'>');
                        $this->table->add_row(lang('off_items_u'),show_talic($row->U));
                        $this->table->add_row(lang('off_items_d'),b($row->D));
if($row->T>0)           $this->table->add_row(lang('off_expi'),b($row->T/60/60/24).lang('off_dayshop'));             
                        $this->table->add_row(lang('off_items_price'),b($row->price.lang('off_money_donate')).' '.lang('off_username_you_have').donate_show(Get_AccountSerial()).lang('off_money_donate'));
                        $this->table->add_row(go_back('',true),$this->_action_donatebuy($id,$row->price));
        }
        $text.=$this->table->generate();
    } else $text.=icon('dialog-error.png').lang('off_notfound').go_back('index.php/shop/donate');

   $this->data['content']=$text;
   compile();
}
// ------------------------------------------------------------------------
# 0.7
function _action_donatebuy($id,$price)
{
	$id=office_secure($id);
    if(donate_show(Get_AccountSerial())>=$price) return anchor('/shop/donate_buy/'.$id.'/'.md5($id.$price),icon('32x32/insert_to_shopping_cart.png',lang('off_shop_btn')));
    else return icon('32x32/remove_from_shopping_cart.png',lang('off_username_you_have').donate_show(Get_AccountSerial()).lang('off_money_donate'));
}

function donate_buy($id = 0,$checksumm)
{
    if(!is_logged()  || $id < 0) redirect( base_url() );
    $text='';
    $id=office_secure(intval($id));
    $options = array('0'=>lang('off_bel1'),'1'=>lang('off_cor1'),'2'=>lang('off_Acc1'));
    
    $query = $this->MYSQL->query("SELECT * FROM items WHERE type=0 AND id=".xss_clean($id));
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
            if(md5($id.$row->price)==$checksumm)
            {
                 if(donate_show(Get_AccountSerial())>=$row->price)
                 {
                    $this->table->set_template(tpl_table());
                    $this->table->add_row(lang('off_items_name'),urldecode($row->title));
                    $this->table->add_row(lang('off_items_price'),b($row->price.lang('off_money_donate')).', '.lang('off_username_you_have').donate_show(Get_AccountSerial()).lang('off_money_donate'));
                    $this->table->add_row(lang('off_select_race'),form_dropdown('race', $options, '0'));
                    $this->table->add_row('',form_submit('mysubmit', lang('off_shop_btn'),'onclick="return confirm(\'Перед покупкой вы должны иметь пустую банковскую ячейку и обязаны полностью выйти из игры. Нажмите ОК для подтверждения того, что вы не в игре и у вас есть свободная ячейка в банке.\')"'));
                    $text.=
                    form_open('shop/donate_buy_get/'.$id).
                        form_hidden('checksumm', $checksumm).b(lang('off_shop_needoffline')).br(2).
                        $this->table->generate().
                    form_close();
                 }
            } else $text.=icon('dialog-error.png').'Check Summ Error!'.go_back('shop/donate');
        }
    }  else $text.=icon('dialog-error.png').lang('off_notfound').go_back('shop/donate');
    $this->data['content']=$text;
    compile();
}
function donate_buy_get($id = 0)
{
    if(!is_logged() || $id < 0) redirect( base_url() );
    $text='';
    if(!is_numeric($id)) redirect( 'shop' );
    $race=office_secure(intval($this->input->post('race')));
    $checksumm=office_secure(xss_clean($this->input->post('checksumm')));
    $id=office_secure(intval($id));

    $world = get_world();
    $query = $this->MYSQL->query("SELECT * FROM items WHERE type=0 AND id=".intval($id));
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
            if(md5($id.$row->price)==$checksumm)
            {
                 if(donate_show(Get_AccountSerial())>=$row->price)
                 {
                    $dd=find_empty_slot(Get_AccountSerial());
                  	if($row->T==0) $timebuy=0; else {
                  		if($row->unix=='y') 
                  				$timebuy=time()+$row->T;
                  			else 
                  				$timebuy=$row->T;
                  	}
                        $this->MSSQL->query(sprintf("INSERT INTO {$world}.dbo.tbl_AccountTrunkCharge (AccountSerial,DCK,TID,K,D,U,R,T) VALUES(%d,0,0,%d,%d,%s,%d, %d)", Get_AccountSerial(), $row->K, $row->D, $row->U, $race, $timebuy));
                        $text=icon('64x64/shopping_cart_accept.png').lang('off_shop_success');
                        $this->MYSQL->query("UPDATE items SET count=count+1 WHERE id = ".intval($id));
//                        логирование
						write_in_buylog(lang('off_items_name').': '.urldecode($row->title),1);
                        donate_edit(Get_AccountSerial(),donate_show(Get_AccountSerial())-$row->price);
                        $this->cache->remove('total_money', get_login());
                } 
            } else $text.=icon('64x64/security.png').'Check Summ Error!'.go_back('shop/donate');
        }
    }  else $text.=icon('64x64/red_button.png').lang('off_notfound').go_back('shop/donate');
    $this->data['content']=$text;
    get_license();
    compile();
}
// ------------------------------------------------------------------------
# 0.6
// bonus system -------------------------------------------------------------------------
function bonus()
{
    if(!is_logged()) redirect( base_url() );
    $i=0;
    //        стиль таблицы
    $this->table->set_template(tpl_table());
    //        заголовки
    $text=heading(icon('64x64/folder_full.png').lang('off_cat'), 2);
//        список категорий
    $query = $this->MYSQL->query("SELECT id, title, cid FROM cat ORDER BY id");
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
            $TotalResult = $this->MYSQL->query("SELECT COUNT(*) as count FROM items WHERE type=1 and catalog=".$row->cid);
            $Total = $TotalResult->row_array();
            if($Total['count']>0) {
                $i++;
                $this->table->add_row($i,icon('32x32/folder.png').anchor('/shop/bonus_cat/'.$row->cid,urldecode($row->title).' ('.$Total['count'].')'));
            }
        }
        $text.=$this->table->generate();
    } else $text.=icon('32x32/block.png').lang('off_admin_empty').go_back('',true);
   $this->data['content']=$text;
    compile();
}
// ------------------------------------------------------------------------
# 0.6
function bonus_cat($id=0)
{
    if(!is_logged())     redirect( base_url() );
    if(!is_numeric($id)) redirect( 'shop/bonus' );
	$i=1;
	$id=office_secure(intval($id));
    $this->table->set_template(tpl_table());
    $query = $this->MYSQL->query("SELECT title FROM cat WHERE cid=".xss_clean($id));
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
            $text=heading(urldecode($row->title), 2);
            $this->table->add_row(b('№'),b(lang('off_items_name')),b(lang('off_items_price')),b(lang('off_menu_buycount')),b(lang('off_do')));
            $query = $this->MYSQL->query("SELECT * FROM items WHERE type=1 AND catalog=".intval($id)." ORDER BY count DESC");
                if ($query->num_rows() > 0)
                {
                    foreach ($query->result() as $row)
                    {
                        $this->table->add_row($i,'<a title="'.urldecode($row->description).'">'.urldecode($row->title).'</a>',
                            urldecode($row->price),$row->count,
                            $this->_action_bonusinfo($row->id).$this->_action_bonusbuy($row->id,$row->price));
                        $i++;
                    }
                    $text.=$this->table->generate();
                }
        }
    } else $text.=icon('32x32/block.png').lang('off_notfound').go_back('shop');
   $this->data['content']=$text;
    compile();
}
// ------------------------------------------------------------------------
# 0.7
function _action_bonusinfo($id=0)
{
	$id=office_secure($id);
    return anchor('/shop/bonus_info/'.$id.'/',icon('32x32/help.png',lang('off_inform')));
}
// ------------------------------------------------------------------------
# 0.6
function bonus_info($id=0)
{
    if(!is_logged())     redirect( base_url() );
    if(!is_numeric($id) || $id < 0) redirect( 'shop/bonus' );
    $text='';
    $id=office_secure(intval($id));
    $this->table->set_template(tpl_table());
     $query = $this->MYSQL->query("SELECT * FROM items WHERE type=1 AND id=".xss_clean($id));
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
                        $this->table->add_row(lang('off_items_name'),urldecode($row->title));
                        $this->table->add_row(lang('off_items_desc'),urldecode($row->description));
if($row->picture<>'')   $this->table->add_row(lang('off_items_pic'),'<img src='.$row->picture.'>');
                        $this->table->add_row(lang('off_items_u'),show_talic($row->U));
                        $this->table->add_row(lang('off_items_d'),b($row->D));
if($row->T>0)          $this->table->add_row(lang('off_expi'),b($row->T/60/60/24).lang('off_dayshop'));    
                        $this->table->add_row(lang('off_items_price'),b($row->price.lang('off_money_bonus')).', '.lang('off_username_you_have').bonus_show(Get_AccountSerial()).lang('off_money_bonus'));
                        $this->table->add_row(go_back('',true),$this->_action_bonusbuy($id,$row->price));
        }
        $text.=$this->table->generate();
    } else $text.=icon('32x32/block.png').lang('off_notfound').go_back('shop/bonus');
   $this->data['content']=$text;
   compile();
}
// ------------------------------------------------------------------------
# 0.7
function _action_bonusbuy($id,$price)
{
	$id=office_secure($id);
    if(bonus_show(Get_AccountSerial())>=$price) return anchor('/shop/bonus_buy/'.$id.'/'.md5($id.$price),icon('32x32/insert_to_shopping_cart.png',lang('off_shop_btn')));
    else return icon('32x32/remove_from_shopping_cart.png',lang('off_username_you_have').bonus_show(Get_AccountSerial()).lang('off_money_bonus'));
}
// ------------------------------------------------------------------------
# 0.6
function bonus_buy($id = 0,$checksumm)
{
    if(!is_logged() || $id < 0) redirect( base_url() );
    $text='';
    $id=office_secure($id);
    $options = array('0'=>lang('off_bel1'),'1'=>lang('off_cor1'),'2'=>lang('off_Acc1'));
    
    $query = $this->MYSQL->query("SELECT * FROM items WHERE type=1 AND id=".intval($id));
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
            if(md5($id.$row->price)==$checksumm)
            {
                 if(bonus_show(Get_AccountSerial())>=$row->price)
                 {
                    $this->table->set_template(tpl_table());
                    $this->table->add_row(lang('off_items_name'),urldecode($row->title));
                    $this->table->add_row(lang('off_items_price'),b($row->price.lang('off_money_bonus')).', '.
                        lang('off_username_you_have').bonus_show(Get_AccountSerial()).lang('off_money_bonus'));
                    $this->table->add_row(lang('off_select_race'),form_dropdown('race', $options, '0'));
                    $this->table->add_row('',form_submit('mysubmit', lang('off_shop_btn'),'onclick="return confirm(\'Перед покупкой вы должны иметь пустую банковскую ячейку и обязаны полностью выйти из игры. Нажмите ОК для подтверждения того, что вы не в игре и у вас есть свободная ячейка в банке.\')"'));
                    $text.=
                    form_open('shop/bonus_buy_get/'.$id).
                        form_hidden('checksumm', $checksumm).lang('off_shop_needoffline').
                        $this->table->generate().
                    '</form>';
                 }
            } else $text.=icon('32x32/block.png').'Check Summ Error!'.go_back('shop/bonus');
        }
    }  else $text.=icon('32x32/block.png').lang('off_notfound').go_back('shop/bonus');
    $this->data['content']=$text;
    compile();
}
// ------------------------------------------------------------------------
# 0.8
function bonus_buy_get($id = 0)
{
    if(!is_logged()) redirect( base_url() );
    $text='';
    if(!is_numeric($id) || $id < 0) redirect( 'shop' );
    $race=office_secure(intval($this->input->post('race')));
    $checksumm=office_secure(xss_clean($this->input->post('checksumm')));
    $id=office_secure(intval($id));
    $world = get_world();
    $query = $this->MYSQL->query("SELECT * FROM items WHERE type=1 AND id=".$id);
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
            if(md5($id.$row->price)==$checksumm)
            {
                 if(bonus_show(Get_AccountSerial())>=$row->price)
                 {
                  	if($row->T==0) $timebuy=0; else {
                  		if($row->unix=='y') 
                  				$timebuy=time()+$row->T;
                  			else 
                  				$timebuy=$row->T;
                  	}

						$this->MSSQL->query(sprintf("INSERT INTO {$world}.dbo.tbl_AccountTrunkCharge (AccountSerial,DCK,TID,K,D,U,R,T) VALUES(%d,0,0,%d,%d,%s,%d, %d)", Get_AccountSerial(), $row->K, $row->D, $row->U, $race, $timebuy));
						
                        $text=icon('32x32/insert_to_shopping_cart.png').lang('off_shop_success');
                        $this->MYSQL->query("UPDATE items SET count=count+1 WHERE id=".intval($id));
                        write_in_buylog(lang('off_items_name').': '.urldecode($row->title),2);
                        bonus_edit(Get_AccountSerial(),bonus_show(Get_AccountSerial())-$row->price);
                        $this->cache->remove('total_money', get_login());
                 }
            } else $text.=icon('dialog-error.png').'Check Summ Error!'.go_back('shop/bonus');
        }
    }  else $text.=icon('32x32/block.png').lang('off_notfound').go_back('shop/bonus');
    $this->data['content']=$text;
    compile();
}
// ------------------------------------------------------------------------
# 0.6
function wmzform()
{
    if(!is_logged()) redirect( base_url() );
    $this->table->set_template(tpl_table());
	$this->load->view('wmzform');
    compile('',false);
}
// ------------------------------------------------------------------------
# 0.6
function wmzpay()
{
    if(!is_logged()) redirect( base_url() );
    $paysumm=office_secure(trim($this->input->post('pay')));
    $kurs=(int)$paysumm*$this->config->item('KURS');
    
    $this->data['title']='Пополнение счёта через WebMoney';
    $this->data['content']='<h2>WebMoney WMZ<br><br>Пополнение счета на '.$kurs.nbs(1).lang('off_money_donate').'</h2><br>
<form action="https://merchant.webmoney.ru/lmi/payment.asp" method="post" name="jump">
<input type="hidden" name="LMI_PAYEE_PURSE" value="'.$this->config->item('LMI_PAYEE_PURSE').'">
<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="'.$paysumm.'">
<input type="hidden" name="LMI_PAYMENT_NO" value="1">
<input type="hidden" name="LMI_PAYMENT_DESC" value="RF Office WM Pay ['.get_login().'] '.$paysumm.' on '.$kurs.nbs(1).lang('off_money_donate').'">
<input type="hidden" name="LMI_SIM_MODE" value="0">
<input type="hidden" name="LOGIN" value="'.get_login().'">
</form></div>
<script language="Javascript">document.forms["jump"].submit();</script>';
    compile();
}
// ------------------------------------------------------------------------
# 0.6
function wmupay()
{
    if(!is_logged()) redirect( base_url() );
    $paysumm=office_secure(trim($this->input->post('pay')));
    $kurs=(int)($paysumm*$this->config->item('KURS'))/$this->config->item('DU');
    $this->data['title']='Пополнение счёта через WebMoney';
    $this->data['content']='<h2>WebMoney WMU<br><br>Пополнение счета на '.$kurs.nbs(1).lang('off_money_donate').'</h2><br>
<form action="https://merchant.webmoney.ru/lmi/payment.asp" method="post" name="jump">
<input type="hidden" name="LMI_PAYEE_PURSE" value="'.$this->config->item('LMI_PAYEE_PURSE_U').'">
<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="'.$paysumm.'">
<input type="hidden" name="LMI_PAYMENT_NO" value="1">
<input type="hidden" name="LMI_PAYMENT_DESC" value="RF Office WM Pay ['.get_login().'] '.$paysumm.' on '.$kurs.nbs(1).lang('off_money_donate').'">
<input type="hidden" name="LMI_SIM_MODE" value="0">
<input type="hidden" name="LOGIN" value="'.get_login().'">
</form></div><script language="Javascript">document.forms["jump"].submit();</script>';
    compile();
}
// ------------------------------------------------------------------------
# 0.6
function wmrpay()
{
    if(!is_logged()) redirect( base_url() );
    $paysumm=office_secure(trim($this->input->post('pay')));
   
    $kurs=(int)($paysumm*$this->config->item('KURS'))/$this->config->item('DR');
    
    $this->data['title']='Пополнение счёта через WebMoney';
    $this->data['content']='<h2>WebMoney WMR<br><br>Пополнение счета на '.$kurs.nbs(1).lang('off_money_donate').'</h2><br>
<form action="https://merchant.webmoney.ru/lmi/payment.asp" method="post" name="jump">
<input type="hidden" name="LMI_PAYEE_PURSE" value="'.$this->config->item('LMI_PAYEE_PURSE_R').'">
<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="'.$paysumm.'">
<input type="hidden" name="LMI_PAYMENT_NO" value="'.time().'">
<input type="hidden" name="LMI_PAYMENT_DESC" value="RF Office WM Pay  ['.get_login().'] '.$paysumm.' on '.$kurs.nbs(1).lang('off_money_donate').'">
<input type="hidden" name="LMI_SIM_MODE" value="0">
<input type="hidden" name="LOGIN" value="'.get_login().'">
</form></div><script language="Javascript">document.forms["jump"].submit();</script>';
    compile();
}
// ------------------------------------------------------------------------
# 0.6
function wmzsuccess()
{
    $login=office_secure(trim($this->input->post('LOGIN')));
    $LMI_PAYEE_PURSE=$this->input->post('LMI_PAYEE_PURSE');
    $kurs=0;
	 if($LMI_PAYEE_PURSE[0]=='Z') $kurs=$this->input->post('LMI_PAYMENT_AMOUNT')*$this->config->item('KURS');
	 if($LMI_PAYEE_PURSE[0]=='R') $kurs=$this->input->post('LMI_PAYMENT_AMOUNT')*$this->config->item('KURS')/$this->config->item('DR');
	 if($LMI_PAYEE_PURSE[0]=='U') $kurs=$this->input->post('LMI_PAYMENT_AMOUNT')*$this->config->item('KURS')/$this->config->item('DU');
    $this->data['title']='Пополнение счёта через WebMoney';
    $this->cache->remove('total_money', get_login());
    if($kurs==0)$this->data['content']='<h2>'.icon('64x64/accept.png').'Пополнение счёта успешно завершено!</h2>';
    else $this->data['content']='<h2>'.icon('64x64/accept.png').'Пополнение счёта успешно завершено! Вы получили '.$kurs.' GP</h2>';
    compile();
}
// ------------------------------------------------------------------------
# 0.6
function wmzfail()
{
    $this->data['title']='Пополнение счёта через WebMoney';
    $this->data['content']='<h2>'.icon('64x64/delete.png').'Пополнение счёта неудачно, или вы отказались от оплаты.</h2>';
    compile();
}
// ------------------------------------------------------------------------
# 0.6 fixed in 0.7 and 8
function wmzresult()
{
	    $login=office_secure(trim($this->input->post('LOGIN')));
	    $LMI_PAYEE_PURSE=$this->input->post('LMI_PAYEE_PURSE');
	    if(strlen($login)>1)
		{
			 if($LMI_PAYEE_PURSE[0]=='Z')$kurs=(int)$this->input->post('LMI_PAYMENT_AMOUNT')*$this->config->item('KURS');
			 if($LMI_PAYEE_PURSE[0]=='R')$kurs=(int)$this->input->post('LMI_PAYMENT_AMOUNT')*$this->config->item('KURS')/$this->config->item('DR');
			 if($LMI_PAYEE_PURSE[0]=='U')$kurs=(int)$this->input->post('LMI_PAYMENT_AMOUNT')*$this->config->item('KURS')/$this->config->item('DU');
			 
		    log_in_history('Пополнение счёта успешно. '.$kurs.' GP',0,$login);
		    $kurs=round($kurs);
		    donate_add(Get_AccountSerial($login),intval($kurs));
		    echo iconv('windows-1251', 'iso-8859-1',"YES");
		    $line=implode('|',$_POST)."\r\n";
		    write_file(APPPATH . 'pay.log', $line,"a+");
	    } else echo 'error';	
}


// bonus system -------------------------------------------------------------------------
function event()
{
    if(!is_logged()) redirect( base_url() );

    $i=0;
    //        стиль таблицы
    $this->table->set_template(tpl_table());
    //        заголовки
    $text=heading(icon('64x64/folder_full.png').lang('off_cat'), 2);
//        список категорий
    $query = $this->MYSQL->query("SELECT id, title, cid FROM cat ORDER BY id");
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
            $TotalResult = $this->MYSQL->query("SELECT COUNT(*) as count FROM items WHERE type=2 and catalog=".$row->cid);
            $Total = $TotalResult->row_array();
            if($Total['count']>0) {
                $i++;
                $this->table->add_row($i,icon('32x32/folder.png').anchor('shop/event_cat/'.$row->cid,urldecode($row->title).' ('.$Total['count'].')'));
            }
        }
        $text.=$this->table->generate();
    } else $text.=icon('32x32/block.png').lang('off_admin_empty').go_back('',true);
   $this->data['content']=$text;
    compile();
}
// ------------------------------------------------------------------------
# 0.6
function event_cat($id=0)
{
    if(!is_logged())     redirect( base_url() );
    if(!is_numeric($id) || $id < 0) redirect( 'shop/event' );
	$i=1;
	$id=intval($id);
    $this->table->set_template(tpl_table());
    $query = $this->MYSQL->query("SELECT title FROM cat WHERE cid=".xss_clean($id));
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
            $text=heading(urldecode($row->title), 2);
            $this->table->add_row(b('№'),b(lang('off_items_name')),b(lang('off_items_price')),b(lang('off_menu_buycount')),b(lang('off_do')));
            $query = $this->MYSQL->query("SELECT * FROM items WHERE type=2 AND catalog=".xss_clean($id)." ORDER BY count DESC");
                if ($query->num_rows() > 0)
                {
                    foreach ($query->result() as $row)
                    {
                        $this->table->add_row($i,'<a title="'.urldecode($row->description).'">'.urldecode($row->title).'</a>',
                            urldecode($row->price),$row->count,
                            $this->_action_eventinfo($row->id).$this->_action_eventbuy($row->id,$row->price));
                        $i++;
                    }
                    $text.=$this->table->generate();
                }
        }
    } else $text.=icon('32x32/block.png').lang('off_notfound').go_back('shop');
   $this->data['content']=$text;
    compile();
}
// ------------------------------------------------------------------------
# 0.7
function _action_eventinfo($id=0)
{
	$id=intval($id);
    return anchor('/shop/event_info/'.$id.'/',icon('32x32/help.png',lang('off_inform')));
}
// ------------------------------------------------------------------------
# 0.6
function event_info($id=0)
{
    if(!is_logged())     redirect( base_url() );
    if(!is_numeric($id) || $id < 0) redirect( 'shop/bonus' );
    $text='';
    $id=intval($id);
    $this->table->set_template(tpl_table());
     $query = $this->MYSQL->query("SELECT * FROM items WHERE type=2 AND id=".intval($id));
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
                        $this->table->add_row(lang('off_items_name'),urldecode($row->title));
                        $this->table->add_row(lang('off_items_desc'),urldecode($row->description));
if($row->picture<>'')   $this->table->add_row(lang('off_items_pic'),'<img src='.$row->picture.'>');
                        $this->table->add_row(lang('off_items_u'),show_talic($row->U));
                        $this->table->add_row(lang('off_items_d'),b($row->D));
if($row->T>0)          $this->table->add_row(lang('off_expi'),b($row->T/60/60/24).lang('off_dayshop'));    
                        $this->table->add_row(lang('off_items_price'),b($row->price.'EP').', '.lang('off_username_you_have').eventp_show(Get_AccountSerial()).'EP');
                        $this->table->add_row(go_back('',true),$this->_action_eventbuy($id,$row->price));
        }
        $text.=$this->table->generate();
    } else $text.=icon('32x32/block.png').lang('off_notfound').go_back('shop/bonus');
   $this->data['content']=$text;
   compile();
}
// ------------------------------------------------------------------------
# 0.11
function _action_eventbuy($id = 0,$price)
{
	$id=office_secure($id);
    if(eventp_show(Get_AccountSerial())>=$price) return anchor('/shop/event_buy/'.$id.'/'.md5($id.$price),icon('32x32/insert_to_shopping_cart.png',lang('off_shop_btn')));
    else return icon('32x32/remove_from_shopping_cart.png',lang('off_username_you_have').eventp_show(Get_AccountSerial()).lang('off_money_bonus'));
}
// ------------------------------------------------------------------------
# 0.1
function event_buy($id = 0,$checksumm)
{
    if(!is_logged()) redirect( base_url() );
    $text='';
    $id=intval($id);
    $options = array('0'=>lang('off_bel1'),'1'=>lang('off_cor1'),'2'=>lang('off_Acc1'));
    
    $query = $this->MYSQL->query("SELECT * FROM items WHERE type=2 AND id=".xss_clean($id));
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
            if(md5($id.$row->price)==$checksumm)
            {
                 if(eventp_show(Get_AccountSerial())>=$row->price)
                 {
                    $this->table->set_template(tpl_table());
                    $this->table->add_row(lang('off_items_name'),urldecode($row->title));
                    $this->table->add_row(lang('off_items_price'),b($row->price.'EP').', '.
                        lang('off_username_you_have').eventp_show(Get_AccountSerial()).'EP');
                    $this->table->add_row(lang('off_select_race'),form_dropdown('race', $options, '0'));
                    $this->table->add_row('',form_submit('mysubmit', lang('off_shop_btn')));
                    $text.=
                    form_open('shop/event_buy_get/'.$id).
                        form_hidden('checksumm', $checksumm).lang('off_shop_needoffline').
                        $this->table->generate().
                    '</form>';
                 }
            } else $text.=icon('32x32/block.png').'Check Summ Error!'.go_back('shop/event');
        }
    }  else $text.=icon('32x32/block.png').lang('off_notfound').go_back('shop/event');
    $this->data['content']=$text;
    compile();
}
// ------------------------------------------------------------------------
# 0.11
function event_buy_get($id = 0)
{
    if(!is_logged()) redirect( base_url() );
    $text='';
    if(!is_numeric($id) || $id < 0) redirect( 'event' );
    $race=office_secure(intval($this->input->post('race')));
    $checksumm=office_secure(xss_clean($this->input->post('checksumm')));
    $id=office_secure(intval($id));
    $world = get_world();
    $query = $this->MYSQL->query("SELECT * FROM items WHERE type=2 AND id=".$id);
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
            if(md5($id.$row->price)==$checksumm)
            {
                 if(eventp_show(Get_AccountSerial())>=$row->price)
                 {
						if($row->T==0) $timebuy=0; else $timebuy=time()+$row->T;
						$this->MSSQL->query(sprintf("INSERT INTO {$world}.dbo.tbl_AccountTrunkCharge (AccountSerial,DCK,TID,K,D,U,R,T) VALUES(%d,0,0,%d,%d,%s,%d, %d)", Get_AccountSerial(), $row->K, $row->D, $row->U, $race, $timebuy));
                        $text=icon('32x32/insert_to_shopping_cart.png').lang('off_shop_success');
                        $this->MYSQL->query("UPDATE items SET count=count+1 WHERE id=".intval($id));
                        write_in_buylog(lang('off_items_name').': '.urldecode($row->title),2);
                        eventp_edit(Get_AccountSerial(),eventp_show(Get_AccountSerial())-$row->price);
                        $this->cache->remove('total_money', get_login());
                 }
            } else $text.=icon('dialog-error.png').'Check Summ Error!'.go_back('shop/event');
        }
    }  else $text.=icon('32x32/block.png').lang('off_notfound').go_back('shop/event');
    $this->data['content']=$text;
    compile();
}


}
?>