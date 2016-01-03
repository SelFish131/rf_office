<?php
// ------------------------------------------------------------------------
/**
 * FDCore Studio
 *
 *
 * @package		CodeIgniter
 * @author		NetSoul
 * @copyright	Copyright (c) 2010, FDCore
 * @link		http://fdcore.ru
 * @since		Version 1.1
*/
// ------------------------------------------------------------------------
class topsms extends Controller {

var $data 		=	array ();
var $is_logged	=	false;
var $AccountSerial=0;
var $master_answer=	false;

	function topsms()
	{
		parent::Controller();
        $this->load->helper(array(
        	'fdcore_framework',
        	'office_framework',
        	'rf_framework_helper',
        	));
        $this->load->helper(array('language','url','security','form','html','pay','date'));
        $this->load->library(array('session','parser','table','ajax'));
        $this->lang->load('office', get_lang());
        $this->login=xss_clean($this->session->userdata('login'));
        RunFunc('connectdb|connectmydb|check_offline|check_sql_inject');
        $this->output->enable_profiler(config('profiler','core'));
        if(!is_logged()) redirect( base_url() );
	}
	/**
	 * показ топа по пвп очкам
	 *
	 * @return void
	 * @author NetSoul
	 **/
	function index()
	{
        $world = get_world();
        
        $this->data['title']='Топ голосов за смс';
        
		include( APPPATH . "config/mmotop.php" );

		$all_data=file_get_contents($config['logfile']);
		
		$rows       =   explode("\n",$all_data);
		
		$chars=array();
		
		foreach($rows as $row){
		    
		    $cols=explode("\t",$row);
		
		    if(isset($cols[3]) && $cols[3]!==''){
		    
		       if($cols[4]==2) $chars[]=iconv('UTF-8','CP1251',$cols[3]);
		       
		    }
		    
		    
		}
		
		$top=array_count_values($chars);
		arsort($top);
		
		$i=1;
		
		$this->table->set_heading('#','Имя персонажа');
		
		foreach($top as $key=>$value){
		
		 	$this->table->add_row($i,$key);
		    $i++;
		    if($i > 100) break;
		    
		}		
		
        $this->data['content']=$this->table->generate();
    	compile('',false);
	}


}