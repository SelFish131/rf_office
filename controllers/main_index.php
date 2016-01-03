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
class main_index extends Controller {

var $data = array ();
var $login="";
var $MSSQL;
var $MYSQL;
var $is_logged=false;
var $AccountSerial=0;
var $master_answer=false;

	function main_index()
	{
		parent::Controller();
        $this->load->helper(array(
        	'fdcore_framework',
        	'office_framework',
        	'rf_framework_helper',
        	
        	));
        $this->load->helper(array('language','url','file','security','date','form','html','pay'));
        $this->load->library(array('session','table','parser','ajax','email'));
        if (!file_exists(APPPATH.'.installed')) redirect( 'install' , 'refresh' );
        $this->data['menu']='';
        $this->lang->load('office', get_lang());
        $this->data['title']=lang('off_auth');
        RunFunc('connectdb|connectmydb|check_sql_inject');
        $this->output->enable_profiler(config('profiler','core'));
	}

//    @главная авторазация
function index()
{
    $this->_authorize();
    $this->table->set_template(tpl_table());
    $this->table->add_row(lang('off_main_login'), form_input('login'));
    $this->table->add_row(lang('off_main_passw'), form_password('password'));
    if($this->config->item('FG_PASSWD'))$this->table->add_row('FireGuard', form_password('fgpwd'));
    $this->table->add_row(form_submit('mysubmit', lang('off_btn_1')),lang('off_main_reglost').' | '.lang('off_lostpass'));
    $text='
    <div align="center">'.
        heading(icon('64x64/id_card.png').
        lang('off_main_msg1'),2).
        form_open('login/').
        $this->table->generate().
    '</form></div>';
    $this->data['content']=$text;
    compile();
}

// если авторизован то перекинуть )
function _authorize()
{
    if(is_logged()) redirect( 'main' , 'refresh' );
    check_offline();
}

function launcher()
{

    $text=$this->ajax->link_to_remote("Новости", array('url'=> base_url().index_page().'/main_index/launcher_news','update' => 'content')).' | ';
    $text.=$this->ajax->link_to_remote("Регистрация", array('url'=> base_url().index_page().'/main_index/launcher_reg','update' => 'content')).' | ';
    $text.=$this->ajax->link_to_remote("Статистика", array('url'=> base_url().index_page().'/main_index/launcher_news','update' => 'content')).' | ';
    $text.=br(1).'<div id="content"></div>';
    $content=array
    (
        "site"=>base_url(),
        "text"=>$text,
    );
     $this ->parser->parse('launch',$content);
}

function launcher_reg()
{
    $this->config->load('rf_settings', FALSE, TRUE);
    ### Построение вывода
    $text=lang('off_reg_msgreg').form_open('main_index/check');
    $text.=b(lang('off_main_login')).br(1).form_input('login').br(1).lang('off_reg_charuse').br(1);
    $text.=b(lang('off_main_passw')).br(1).form_password('password').br(1).lang('off_reg_charuse').br(1);
    $text.=b(lang('off_acc_email')).br(1).form_input('email').br(1).lang('off_reg_emailsample').br(1);
    $text.=b(lang('off_reg_secure')).form_input('secure').br(1).'<img src="'.base_url().index_page().'/antibot"><br>';
    $text.=$this->ajax->submit_to_remote("submit",lang('off_reg_btn'), array('url'=> base_url().'register/check','update' => 'content'));
    echo $text.'</form>';
}
function offline()
{
    $this->data['content']=$this->load->view('offline','',true);
    compile();
}
function lang($lang)
{
    if($lang=='russian')   $this->session->set_userdata('lang','russian');
    if($lang=='english')   $this->session->set_userdata('lang','english');
    if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']<>'') redirect( $_SERVER['HTTP_REFERER'] , 'refresh' ); else  redirect( base_url() , 'refresh' );
}
function lost()
{
    $this->table->set_template(tpl_table());
    $this->table->add_row(lang('off_main_login'), form_input('login'));
    $this->table->add_row(lang('off_profile_master'), form_input('master_answer'));
    $this->table->add_row(form_submit('mysubmit', lang('off_btn_1')),'');
    $text='
    <div align="center">'.
        heading(icon('64x64/lock.png').
        lang('off_profile_lost'),3).
        form_open('main_index/lostpassword').
        $this->table->generate().
    '</form></div>'.anchor("main_index/lost2",lang('off_lostmaster'));
    $this->data['content']=$text;
    compile();
}
function lost2()
{
		$this->load->library('validation');$this->load->library('email');
		$rules['login']	= "required|min_length[3]|max_length[16]";
		$rules['email']	 = "required|valid_email";
		$this->validation->set_rules($rules);
		$fields['login'] = lang('off_main_login');
		$fields['email'] = lang('off_acc_email');
		$this->validation->set_fields($fields);
	 
		if ($this->validation->run() == FALSE)
		{
	    $this->table->set_template(tpl_table());
	    $this->table->add_row(lang('off_main_login'), form_input('login'));
	    $this->table->add_row(lang('off_acc_email'), form_input('email'));
	    $this->table->add_row(form_submit('mysubmit', lang('off_lostbtn')),'');
	    $text='
	    <div align="center">'.$this->validation->error_string.
	        heading(icon('64x64/lock.png').lang('off_lostmastertitle'),3).
	        form_open('main_index/lost2').
	        $this->table->generate().
	    '</form></div>'.anchor("main_index/lost",lang('off_lostemail'));
	    $this->data['content']=$text;
    } else
		{
			$login=office_secure(trim($this->input->post('login',true)));
			$email=office_secure(trim($this->input->post('email',true)));
			if($this->login_is_exit($login,$email)){
				$this->data['content']=lang('off_emailsend');
				include APPPATH.'config/mail.php';
				$this->email->initialize($config);
				$PWD=$this->login_is_exit($login,$email);
				$FGPWD=$this->get_FGPWD($login);
				$this->email->from('noreply@fdcore.ru', 'Lost Password');
				$this->email->to($email); 
				$name=gen_name_profile(Get_AccountSerial($login));
				$this->email->subject('Repair Password');
				$this->email->message(sprintf(lang('off_emailsubject'),$name,$login,$PWD,$FGPWD,site_url(),config('office_name','rf_settings')));	
				$this->email->send();				
			}else {
					$this->data['content']=lang('off_dataerror');	
			}
		}
    compile();
}

function login_is_exit($login='',$email='')
{
	$login=office_secure($login);
	$email=office_secure($email);
	$user= get_user();
	$query = $this->MSSQL->query("SELECT * 
	FROM 
		{$user}.dbo.".config('table_account','query')."
	WHERE 
		id = '$login' AND Email='$email'");
    if ($query->num_rows() > 0)
	{
	    	foreach ($query->result() as $row)
			{
				return $row->password;
			}
    } else return FALSE;
    
}
function get_FGPWD($login)
{
	$user= get_user();
	$login=office_secure($login);
	$query = $this->MSSQL->query("SELECT uilock_pw 
	FROM 
		{$user}.dbo.tbl_UserAccount
	WHERE 
		id = '$login'");	
   if ($query->num_rows() > 0)
	{
	    	foreach ($query->result() as $row)
			{
				return $row->uilock_pw;
			}
    } else return FALSE;		
}
function notallow()
{
	$this->session->sess_destroy();
    @session_destroy();
    $this->data['content']=icon('32x32/lock.png').lang('off_not_allow_ip');
    compile('',false);
}
function lostpassword()
{
    $stop='';$user= get_user();$text='';
    $login=office_secure(trim($this->input->post('login',true)));
    $master_answer=md5(office_secure(trim($this->input->post('master_answer',true))));
    if($login=='' || $this->input->post('master_answer')=='') redirect( 'main_index/lost' , 'refresh' );
    if(FastCheckBan(Get_AS(office_secure($login)))) {
    	$stop="You account is banned!";
 	}
    $query_auth = $this->MSSQL->query("SELECT * FROM {$user}.dbo.tbl_UserAccount WHERE id = '".xss_clean($login)."'");
    if ($query_auth->num_rows() > 0)
    {
        foreach ($query_auth->result() as $row)
        {
            $seriala=$row->serial;
       }
        if(check_master_answer($seriala))
        {
            $master_answer_query=show_master_answer($seriala);
        } else $stop=lang('off_lost_error');
    } else $stop=lang('off_lost_error');
    if($stop=='')
    {
    $query = $this->MSSQL->query("SELECT id,Password FROM {$user}.dbo.".config('table_account','query')." WHERE id = '".$login."'");
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
            $id_query=$row->id;
            $Password_query=$row->Password;
        }
    } else $stop=lang('off_lost_error');
    }
        if($stop=='')
        {
            if($master_answer_query==$master_answer)
            {
            	if(preg_name($Password_query)=='') exit('Permabanned!');
                $this->session->set_userdata('login', xss_clean($login));
                $this->session->set_userdata('passw', xss_clean($Password_query));
                $this->session->set_userdata('lost_p',xss_clean($master_answer));
                redirect( 'main' , 'refresh' );
            } else {}
        } else $text= $stop;
    $this->data['content']=icon('64x64/delete_user.png').br(2).go_back('',true).$text;
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


/* End of file main_index.php */
/* Location: ./system/application/controllers/main_index.php 
 */
?>