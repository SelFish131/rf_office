<?php
// ------------------------------------------------------------------------
/**
 * FDCore Studio
 *
 *
 * @package		CodeIgniter
 * @author		NetSoul
 * @copyright	Copyright (c) 2009, FDCore
 * @link		http://fdcore.ru
 * @since		Version 1.0

CREATE TABLE IF NOT EXISTS `tbl_emudevtop` (
  `id` varchar(32) CHARACTER SET cp1251 NOT NULL,
  `date` int(11) NOT NULL,
  `addr` varchar(15) CHARACTER SET cp1251 NOT NULL,
  `char` varchar(50) CHARACTER SET cp1251 NOT NULL,
  `type` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251
*/
// ------------------------------------------------------------------------
class evote extends Controller {


var $data = array ();
var $login="";
var $MSSQL;
var $MYSQL;
var $is_logged=false;
var $AccountSerial=0;
var $master_answer=false;

	function evote()
	{
		parent::Controller();
        $this->load->helper(array(
        	'fdcore_framework',
        	'office_framework',
        	'rf_framework_helper',
        	));
        $this->load->helper(array('language','url','file','security','date','form','html','pay'));
        $this->load->library(array('session','table','parser','ajax','cache'));
        $this->lang->load('office', get_lang());
        $this->login=xss_clean($this->session->userdata('login'));
        RunFunc('connectdb|connectmydb|allow_ip|check_offline|check_sql_inject');
        $this->limit=config('vote_limit','core');
        $this->output->enable_profiler(config('profiler','core'));
	}

	function index()
	{
        if(!is_logged()) redirect( base_url() );
        include( APPPATH . "config/evote.php" );
        $this->data['title']=lang('off_vote');
        
        $text=icon('64x64/chart_up.png').'<h2>Голосование на TOP EMUDEV</h2><p>После голосования нажмите получить бонус.</p><p>При простом голосовании вы получите <b>'.$config['normal'].'</b> BP, 
        При смс голосе вы получите <b>'.$config['sms'].'</b> BP.</p>';
        $smslink=str_replace('server','sms',$config['votelink']);
        
        $text.=icon('32x32/comment.png').'<a href="'.$config['votelink'].'" target="_blank">Голосовать</a>'.br(1);
        $text.=icon('32x32/sms.png').'<a href="'.$smslink.'" target="_blank">Голосовать через SMS</a>'.br(1);
        $this->load->model('Account');
			$this->Account->login=get_login();
			$array=$this->Account->char_array();
			$form=form_dropdown('char', $array);  
      
        $text.=form_open('evote/getbonus').$form.form_submit('mysubmit','Получить бонус').icon('32x32/note_accept.png').form_close();
        $this->data['content']=$text;
        compile();
	}
	function getbonus()
	{
        if(!is_logged()) redirect( base_url() );
        include( APPPATH . "config/evote.php" );
        $this->data['title']=lang('off_vote');
      $text=icon('64x64/chart_up.png').'<h2>Голосование на TOP EMUDEV</h2><p>После голосования нажмите получить бонус.</p>';  
      $char	=(int)office_secure($this->input->post('char')); 
        if ($char <= 0)   redirect( 'evote' );
		$query =$this->MSSQL->query("SELECT Name FROM ".get_world().".dbo.tbl_base WHERE Serial='$char'");
	    if ($query->num_rows() > 0)
	    {
	        foreach ($query->result() as $row)
	        {
	            $char=preg_name($row->Name);
	        }//for
	    } else redirect( 'evote' );
                 
		# Генерация полной ссылки текущего года/месяца
		$FullLink	=	$config['logfile'] . md5(date("Ym")) . '.txt';
		# Получение данных статистики
		$data			=	file_get_contents($FullLink);
		# Разбитие данных в строки массива
		$rows			=	explode("\r\n",$data);		
		$find=0;
		for ($i=0; $i < count($rows)-1; $i++) { 
			$row=explode("\t",$rows[$i]);
			if($row[3]=='') continue;
			if($row[3]==$char){
			if(!$this->_id_exits($row[0])){
				$find++;
				$text.=br(1).icon('32x32/accept.png').'Бонус выдан';
				if($row[4]==0) $summ=$config['normal']; else $summ=$config['sms'];
				bonus_add(Get_AccountSerial(),$summ);
				$this->_accepted($row);
			}
		}
	}//for
		if($find==0) $text.=br(1).icon('32x32/delete.png').'Голос не найден';	
        $this->data['content']=$text;
        compile();			
	}
	
	function _id_exits($id)
	{
		$query = $this->MYSQL->get_where('tbl_emudevtop', array('id' => $id)); 
		if ($query->num_rows() > 0) return TRUE; else return FALSE;
	}
	
	function _accepted($array)
	{
		$id=$array[0];

		$data = array(
			'id' 		=>	$id,
			'date' 	=>	time(),
			'addr' 	=> $array[2],
			'char' 	=>	urlencode($array[3]),
			'type' => $array[4]								
		);
		$this->MYSQL->insert('tbl_emudevtop', $data); 
	}	
	
	function install()
	{
		$this->MYSQL->query("
		CREATE TABLE IF NOT EXISTS `tbl_emudevtop` (
		  `id` varchar(32) CHARACTER SET cp1251 NOT NULL,
		  `date` int(11) NOT NULL,
		  `addr` varchar(15) CHARACTER SET cp1251 NOT NULL,
		  `char` varchar(50) CHARACTER SET cp1251 NOT NULL,
		  `type` int(1) NOT NULL DEFAULT '0',
		 	PRIMARY KEY (`id`)
			)ENGINE=MyISAM DEFAULT CHARSET=cp1251");
        $this->data['content']='модуль установлен';
        compile();	
	}
	
}