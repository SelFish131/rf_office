<?php
// ------------------------------------------------------------------------
/**
 * FDCore Studio
 *
 * Главная страница кабинета, онаже авторизация
 *
 * @package         CodeIgniter
 * @author          NetSoul
 * @copyright		Copyright (c) 2009, FDCore
 * @link			http://fdcore.ru
 * @since			Version 1.0
*/
// ------------------------------------------------------------------------
class register extends Controller {

var $data = array ();
var $MSSQL;
var $MYSQL;
var $is_logged=false;
var $AccountSerial=0;
var $master_answer=false;

	function register()
	{
		parent::Controller();
       $this->load->helper(array(
       	'fdcore_framework',
       	'office_framework',
       	'rf_framework_helper',
       	'email',
       	));
       	$this->load->helper(array('language','url','file','security','date','form','html','pay','prototype'));
        $this->load->library(array('session','table','parser','ajax'));
        $this->data['menu']='';
        $this->lang->load('office', get_lang());
        $this->data['title']=lang('off_reg_title');
      	$this->output->enable_profiler(config('profiler','core'));
        RunFunc('connectdb|connectmydb|check_offline|check_sql_inject');
	}
function index()
{
    $this->config->load('rf_settings', FALSE, TRUE);
    ### Построение вывода
    $this->table->set_template(tpl_table());
    $this->table->add_row(lang('off_main_login'),   form_input('login'),lang('off_reg_charuse'));
    $this->table->add_row(lang('off_main_passw'),   form_password('password'),lang('off_reg_charuse'));
    $this->table->add_row(lang('off_acc_email'),    form_input('email'),lang('off_reg_emailsample'));
    if($this->config->item('REF_PAY')==true) $this->table->add_row(lang('off_reg_friend'),   form_input('part'),lang('off_reg_friendsi'));
    $this->table->add_row(lang('off_reg_secure'),   form_input('secure'),'<div id="captcha"><img src="'.base_url().index_page().'/antibot"></div>');
    
    $this->table->add_row(form_submit('submit',lang('off_reg_btn')),'',ajax_load('register/capt', lang('off_chptchau'), 'captcha'));
    
    $this->data['content']=heading(icon('48x48/add_image.png').lang('off_reg_msgreg'),4).form_open('register/check').$this->table->generate().'</form>';
    compile();
}
function license()
{
	$this->data['content']=$this->load->view('rules','',true);
	$this->data['content'].=br(2).heading(anchor('register',lang('off_agree')).nbs(5).anchor('http://www.rfonline.ru',lang('off_nagree')),3);
    compile();	
}
function capt()
{
	echo '<img src="'.base_url().index_page().'/antibot/rnd/'.time().'">';
}
function referer($id='')
{
    ### Построение вывода
    $this->config->load('rf_settings', FALSE, TRUE);
    if($this->config->item('REF_PAY')==false) redirect( 'register' , 'refresh' );
    $this->table->set_template(tpl_table());
    $this->table->add_row(lang('off_main_login'),   form_input('login'),lang('off_reg_charuse'));
    $this->table->add_row(lang('off_main_passw'),   form_password('password'),lang('off_reg_charuse'));
    $this->table->add_row(lang('off_acc_email'),    form_input('email'),lang('off_reg_emailsample'));
    $this->table->add_row(lang('off_reg_secure'),    form_input('secure'),'<img src="'.base_url().index_page().'/antibot">');
    $this->table->add_row(form_submit('submit',lang('off_reg_btn')),'');
    $this->data['content']=heading(icon('48x48/add_image.png').
        lang('off_reg_msgreg'),4).form_open('register/check').form_hidden('refid',xss_clean($id)).$this->table->generate().'</form>';
    compile();
}
function check()
{

$this->load->library('validation');
//      получем данные
$login		=	office_secure(xss_clean($this->input->post('login')));

$id			=	office_secure(xss_clean($this->input->post('id')));
$password	=	office_secure(xss_clean($this->input->post('password')));
$email		=	office_secure(xss_clean($this->input->post('email')));
$part		=	office_secure(xss_clean($this->input->post('part')));
$secure		=	office_secure(xss_clean($this->input->post('secure')));
$stop		=	array();
//      начинаем проверки

if(!$this->validation->required($login)) $stop[]=lang('off_reg_login_check');
if(!$this->validation->required($password)) $stop[]=lang('off_reg_pass_chk');
if($this->validation->alpha($password)) $stop[]=lang('off_reg_pass_range');
if(!$this->validation->valid_email($email)) $stop[]=lang('off_reg_email');
$sec_code_session=$this->session->userdata('sec_code_session');

//fix at 03.10.2011
if(preg_match("/[^0-9a-zA-Z_-]/", $login)) $stop[]=lang('off_useletters');
if(preg_match("/[^0-9a-zA-Z_-]/", $password)) $stop[]=lang('off_useletters');

if($sec_code_session){
	if($sec_code_session<>md5($secure)) $stop[]=lang('off_reg_captha');
} else if($_SESSION['sec_code_session']<>md5($secure)) $stop[]=lang('off_reg_captha');

if(!$this->validation->valid_ip($this->session->userdata('ip_address'))) $stop[]=lang('off_reg_chkip');
if($login===$password) $stop[]=lang('off_reg_chk_logpass');
   
if(count($stop)==0)
{
    $user = get_user();
//    проверка на логин
    $query = $this->MSSQL->query("SELECT * FROM {$user}.dbo.".query_config('table_account')." WHERE id = '{$login}'");
    if ($query->num_rows() > 0) $stop[]=lang('off_reg_loginuse');
//    проверка на мыло
    $query = $this->MSSQL->query("SELECT * FROM {$user}.dbo.".query_config('table_account')." WHERE Email = '{$email}'");
    if ($query->num_rows() > 0) $stop[]=lang('off_reg_emailuse');
//    проверка на пароль
    $query = $this->MSSQL->query("SELECT * FROM {$user}.dbo.".query_config('table_account')." WHERE password = '{$password}'");
    if ($query->num_rows() > 0) $stop[]=lang('off_reg_passuse');
//    завершение реги
    if(count($stop)==0)
    {
//        регистрация 
		$this->config->load('core', FALSE, TRUE);

		$register			=	query_config('query_register');
		$this->MSSQL->query($register,array($login,$password,$email));
        $result = $this->MSSQL->query("SELECT SCOPE_IDENTITY() AS [SCOPE_IDENTITY]");
        var_dump($result);
        exit();

	    $this->config->load('rf_settings', FALSE, TRUE);
         if($id<>'') bonus_add($id,$this->config->item('REF_BONUS'));
         elseif ($part<>'') bonus_add(GetASerialFromName($part),$this->config->item('REF_BONUS'));
         
        $this->data['content']=icon('48x48/image_accept.png').lang('off_reg_comp').go_back();
//    не прошли проверки
    } else $this->data['content']=heading(icon('48x48/delete_image.png').lang('off_reg_error'),4).ul($stop).go_back('',true);
    
} else $this->data['content']=heading(icon('48x48/delete_image.png').lang('off_reg_error'),4).ul($stop).go_back('',true);
compile();
}

function _get_count()
{
	$query = $this->MSSQL->query("SELECT COUNT(*) as cnt FROM rf_user.dbo.".query_config('table_account'));
	foreach ($query->result() as $row)
	{
	   return $row->cnt;
	} 
}
}