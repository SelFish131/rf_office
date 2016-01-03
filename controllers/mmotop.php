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
 * @since		Version 1.1
 *
*/

// ------------------------------------------------------------------------
class mmotop extends Controller {


var $data = array ();
var $login="";
var $MSSQL;
var $MYSQL;
var $is_logged=false;
var $AccountSerial=0;
var $master_answer=false;

	function mmotop()
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
        include( APPPATH . "config/mmotop.php" );
        $this->data['title']=lang('off_vote');
        $text=icon('64x64/chart_up.png').'<p>ќбработка голосов длитс€ от 30 минут до 2 часов, только по прошествию этого времени вы можете получить свой бонус.</p>';
        $text.='<a href="'.$config['votelink'].'" target="_blank">√олосовать</a>'.icon('32x32/comment.png').br(1);
        $text.=anchor('mmotop/getbonus/','получить бонус').icon('32x32/note_accept.png');
        $this->data['content']=$text;//.br(1).icon('32x32/sms.png').anchor('vote/sms',lang('off_vote_sms'));
        compile();
	}
	function getbonus()
	{
	     // проверка авторизации
        if(!is_logged()) redirect( base_url() );
        include( APPPATH . "config/mmotop.php" );
        $this->data['title']=lang('off_vote');
        $text=icon('64x64/chart_up.png').'<p>ќбработка голосов длитс€ от 30 минут до 2 часов, только по прошествию этого времени вы можете получить свой бонус.</p>';
        $text.='<a href="'.$config['votelink'].'" target="_blank">√олосовать</a>'.icon('32x32/comment.png').br(1);
        $text.=anchor('mmotop/getbonus/','получить бонус').icon('32x32/note_accept.png');
      //  скачивание лога
      	
      	if(!$all_data = $this->cache->load('mmotop', 'file', 60*30)){
      		
      		$all_data=@file_get_contents($config['logfile']);
      		
      		$this->cache->save($all_data, 'mmotop', 'file');
      		
      	}
		
		if(date('Ymd') == date('Ym').'01'){
			$this->data['content'] = '—егодн€ нельз€ получить бонус, идЄт обработка логов. ѕопробуйте завтра.';
       		compile();
		}
		//fix
		$aChars = $this->_chararray(get_login());
		$rows=explode("\n",$all_data);
		$find=0;
		// обработка строк
		for ($i=0; $i < count($rows); $i++) {
			$rows[$i] = @iconv('UTF-8','CP1251',$rows[$i]);
			$row=explode("\t",$rows[$i]);
			if(count($row)<>5) continue;
			if($row[3]=='') continue; // нет ника
			//check time			
			$unix_time=strtotime($row[1]);
			$time_left=time()-$unix_time;
			$days=round($time_left/60/60/24);
			if($days > 20) continue;
			//check time			
			if($row[3]==preg_name(get_login())){ // найден логин
			if(!$this->_id_exits($row[0],0)){ // просмотр записи в базе
				$find++;
				$text.=br(1).icon('32x32/accept.png').'бонус выдан';
				if($row[4]==1) $summ=$config['normal']; else $summ=$config['sms'];
				$bonus=$summ*(premium_bonus()/100);
				$bold=bonus_show(Get_AccountSerial());
				bonus_add(Get_AccountSerial(),$summ+$bonus);
				$bnow=bonus_show(Get_AccountSerial());
				file_put_contents(APPPATH.'mmotop.log',"add bonus for $row[3], have $bold now have $bnow\r\n",FILE_APPEND);
				$this->_accepted($row,0);
			} else $text.=br(1).icon('32x32/block.png').'бонус был выдан раньше '.$row[1];
	
		} elseif (is_array($aChars) && @in_array($row[3],$aChars)) {
			if(!$this->_id_exits($row[0],0) && $this->_checkname(office_secure($row[3]))){
				$find++;
				$text.=br(1).icon('32x32/accept.png').'бонус выдан';
				if($row[4]==1) $summ=$config['normal']; else $summ=$config['sms'];
				$bonus=$summ*(premium_bonus()/100);
				$bold=bonus_show(Get_AccountSerial());
				bonus_add(Get_AccountSerial(),$summ+$bonus);
				$bnow=bonus_show(Get_AccountSerial());
				file_put_contents(APPPATH.'mmotop.log',"add bonus for $row[3], have $bold now have $bnow\r\n",FILE_APPEND);
				$this->_accepted($row,0);
			}
			}
	}//for
		if($find==0) $text.=br(1).icon('32x32/delete.png').'√олос не найден';	
        $this->data['content']=$text;//.br(2).icon('32x32/sms.png').anchor('vote/sms',lang('off_vote_sms'));;
        compile();			
	}
	// метод проверки в таблице
	function _id_exits($id,$server)
	{
		$query = $this->MYSQL->get_where('tbl_mmotop', array('number' => intval($id),'server'=>intval($server),'month' => date("m"))); 
		if ($query->num_rows() > 0) return TRUE; else return FALSE;
	}
	
	// метод добавлениИ в таблицу
	function _accepted($array,$server)
	{
		$id=$array[0];
		$time=strtotime($array[1]);
		$data = array(
			'number' =>	intval($id),
			'date' 	=>	$time,
			'addr' 	=> office_secure($array[2]),
			'char' 	=>	urlencode($array[3]),
			'sms' => intval($array[4]),									
			'server' => intval($server),
			'login' => get_login(),
			'voted_time'=>time(),
			'month' => date("m")
		);
		$this->MYSQL->insert('tbl_mmotop', $data); 
	}
	
	function _checkname($name)
	{
		$name = preg_name(office_secure($name));
		if (player_exits($name)) {
	    $world = get_world();
	    $query = $this->MSSQL->query("SELECT Account FROM {$world}.dbo.tbl_base WHERE name='$name'");
	    if ($query->num_rows() > 0)
	    {
	        foreach ($query->result() as $row)
	        {
	            if (preg_name($row->Account)==get_login()) {
	             	
	             	return TRUE;
	             
	             } else return FALSE;	
	        }
	    } 	else return FALSE;	
		}
	}
	
	function _chararray($login='')
	{
		$char=array();
		$login = preg_name(office_secure($login));
		$query =$this->MSSQL->query("SELECT Serial,Name FROM ".get_world().".dbo.tbl_base WHERE Account='$login' AND DCK=0");
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$char[$row->Serial]=$row->Name;
			}//for
		} else return false;//if
		return $char;
	}		
	function install()
	{
	   $this->MYSQL->query("CREATE TABLE IF NOT EXISTS `tbl_mmotop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `addr` varchar(15) NOT NULL,
  `login` varchar(30) NULL,
  `char` varchar(50) NOT NULL,
  `sms` int(1) NOT NULL DEFAULT '0',
  `server` int(255) NOT NULL DEFAULT '0',
  `month` int(11) NOT NULL,
  `voted_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1;");
	   echo 'ok';
	}
}