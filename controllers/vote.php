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
*/
// ------------------------------------------------------------------------
class vote extends Controller {


var $data = array ();
var $login="";
var $MSSQL;
var $MYSQL;
var $BPAY=0;
var $limit=null;
var $is_logged=false;
var $AccountSerial=0;
var $master_answer=false;

	function vote()
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
        $this->data['title']=lang('off_vote');
        $text=icon('64x64/chart_up.png').'<p>';
  if (config('alt_vote','rf_settings')) {
   	$text.='<br><h2>'.anchor('evote','Emudev Top').'</h2>';
   	$text.='<h2>'.anchor('mmotop','MMOTOP').'</h2>';
   } else {
   
        if($this->_get_last_vote()<>0) 
        {
            $text.=lang('off_youvote').timespan($this->_get_last_vote(true),time()).','.br(1);
            $text.='<p>'.icon('32x32/clock.png').lang('off_vote_wait').timespan(time()+$this->_get_last_vote(),time()+$this->limit).'</p>';
        }elseif($this->_last_ip()<>0) 
	        {
	        	$text.=icon("32x32/globe_warning.png").lang('off_ip_vote');
	        	$text.='<p>'.icon('32x32/clock.png').lang('off_vote_wait').timespan(time()+$this->_last_ip(),time()+$this->limit).'</p>';
	        }
	        else
        {
            $text.=lang('off_vote_title').'</p>'.$this->_show_vote_link().
            anchor('vote/getbonus/'.gen_secure(),lang('off_vote_get')).icon('32x32/note_accept.png');
            // .gen_secure()
        }
	}
        $this->data['content']=$text.br(2).icon('32x32/sms.png').anchor('vote/sms',lang('off_vote_sms'));
        compile();
	}

    function votelocation($num)
    {
        if(!is_logged()) redirect( base_url() );
        if(!is_numeric($num) && $num<1) redirect( 'vote' );
        $num=office_secure(xss_clean($num));
        $voted = $this->session->userdata('vote'.$num);
        if($voted=='true') die();
        $this->session->set_userdata('vote'.$num, 'true');
        include ( APPPATH . "config/vote.php" );
        if(isset($vote[$num])) redirect($vote[$num]); else redirect( 'vote' );
    }
    
    function _show_vote_link()
    {
        if(!is_logged()) redirect( base_url() );
        include ( APPPATH . "config/vote.php" );
        $this->table->set_template(tpl_table());
        $x=0;
        for($i=0;$i<=count($vote)-1;$i++)
        {
            if(!$this->session->userdata('vote'.$i)) 
            {
                $this->table->add_row(b('<a href="'.base_url().index_page().'/vote/votelocation/'.$i.'" target="_blank">'.$vote[$i]).icon('32x32/cloud_comment.png').'</a>');
                $x++;
            }
            
        }
        if($x<>0) return $this->table->generate();
    }

    function getbonus($hash=0)
    {
        if(!is_logged()) redirect( base_url() );
        if($this->_get_last_vote()<>0) redirect( 'vote' );
        if($this->_last_ip()<>0) redirect( 'vote' );
        if(gen_secure()<>$hash) redirect( 'vote' );
        include ( APPPATH . "config/vote.php" );
        $voted = $this->session->userdata('voted');
        if($voted=='true') redirect( 'vote' );
        $this->data['title']=lang('off_vote');
        $this->table->set_template(tpl_table());
        $votecount=0;$text='';
        $text.=icon('64x64/chart.png').'<p>'.lang('off_vote_title').'</p>';
        for($i=0;$i<=count($vote)-1;$i++)
        {
            if($this->session->userdata('vote'.$i))
            {
                $this->table->add_row('<i>'.$vote[$i].'</i>'.icon('32x32/accept.png'));
                $votecount++;
            }   else
            {
                $this->table->add_row(b($vote[$i]).icon('32x32/delete.png'));
            }
        }

        $text.=$this->table->generate();
        if(count($vote)==$votecount) 
        {
           
            if($this->_paybonus(count($vote)));
            $this->session->set_userdata('vote', time());
            $text.=icon('32x32/add_comment.png').lang('off_vote_ok');
        } else {
            $text.=b(icon('32x32/warning.png').anchor('vote',lang('off_vote_error')));
        }
        $this->data['content']=$text;
        compile();
    }

    function _get_last_vote($show=false)
    {
        $query = $this->MYSQL->query("SELECT * FROM vote_logip WHERE accname='".xss_clean(get_login()."'"));
            if ($query->num_rows() > 0)
            {
                foreach ($query->result() as $row)
                {
                    $last=time()-$row->realtime;
                    if($last>=$this->limit) return 0; else {
                        if($show) return $row->realtime; else return $last;
                        }
                }
            } else {
                $this->MYSQL->query("INSERT INTO vote_logip (accname,ip,realtime)
                    VALUES('".xss_clean(get_login())."','".$this->session->userdata('ip_address')."',0)");
            return 0;
            }
    }
/**
 * выдача бонусов
 *
 * @param string $num 
 * @return bool
 * @author NetSoul
 */
    function _paybonus($num)
    {
        $this->config->load('rf_settings',FALSE, TRUE);
        $this->session->set_userdata('voted', 'true');
        if($this->_get_last_vote()==0)
        {
            $this->MYSQL->query("UPDATE vote_logip
            SET ip='".$this->session->userdata('ip_address')."', realtime=".time()." WHERE accname='".get_login()."'");
            $bonus=config('VOTE_BONUS')*(premium_bonus()/100);
            bonus_add(Get_AccountSerial(),config('VOTE_BONUS')+$bonus);
            $this->cache->remove('total_money', get_login());
            $this->BPAY=(int)config('VOTE_BONUS')+$bonus;
            return true;
        } else return false;
    }
    
    function sms()
    {
        if(!is_logged()) redirect( base_url() );
        $this->config->load('rf_sms',FALSE, TRUE);
        $this->data['title']=lang('off_votesms');
        $data['link']=$this->config->item('link');
        $data['pay']=$this->config->item('pay');
        $this->table->add_row(
        	icon('32x32/send_sms.png'),
        	form_input(
        		's_pair',lang('off_code')),form_submit('mysubmit',lang('off_get').lang('off_money_bonus')
        		)
        	);
        $data['form']=form_open('vote/smskey').
                      form_hidden('vote_server', $this->config->item('vote_server')).
                      $this->table->generate().
                      '</form>';
        $this->data['content']=$this->load->view('vote', $data, true);
        compile();
    }
    
    
    function smskey()
    {
        if(!is_logged()) redirect( base_url() );
        $this->config->load('rf_sms',FALSE, TRUE);
        $this->data['title']=lang('off_votesms');
        $s_pair=		office_secure(trim($this->input->post('s_pair')));
        #http://rf.mmotop.ru/sms/2650/?s_pair=dds34fr&votes_charname=&votes_server=167
        
        $link=config('link').'?s_pair='.$s_pair.'&votes_charname=&votes_server='.config('vote_server');
        
        $html_page = file_get_contents($link);
        
        $html_page = iconv('UTF-8', 'CP1251', $html_page);
        
        $pos=			strpos($html_page,'Ваш голос учтен');
        if($pos==TRUE)
        {
            bonus_add(Get_AccountSerial(),config('pay'));
            $this->data['content']=icon('32x32/accept.png').lang('off_votesms_ok');
            log_in_history('Получены бонусы за SMS голосование');
        } else {
            log_in_history('Неудачная попытка получения бонуса за SMS');
            $this->data['content']=icon('32x32/delete.png').lang('off_votesms_err').go_back();
        }
        compile();
    }
    
    function _last_ip()
    {
    	$new_vote=$this->limit;
        $query = $this->MYSQL->query("
        SELECT realtime 
        FROM 
        	vote_logip 
        WHERE 
        	accname<>'".get_login()."'
        AND
        	ip='".$this->session->userdata('ip_address')."'	
        AND
        	".time()."-realtime<$new_vote
        ");
        	
            if ($query->num_rows() > 0)
            {
            	foreach ($query->result() as $row)
                {
                    $last=time()-$row->realtime;
                    if($last>=$this->limit) return 0; else {
                       return $last;
                        }
            	}
            } else return 0;   	
    }
}
?>