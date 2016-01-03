<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// ------------------------------------------------------------------------
/**
 * FDCore Studio
 *
 * Полный функционал от студии FDCore Studio
 *
 * @package         CodeIgniter
 * @author          NetSoul - Head Develper FDCore Studio
 * @copyright		Copyright (c) 2009, FDCore
 * @link			http://fdcore.ru
 * @since			Version 1.0
*/
// ------------------------------------------------------------------------

/*
|--------------------------------------------------------------------------
| Cоединяется с базой MS SQL
|--------------------------------------------------------------------------
|
|@принимает: имя базы для соединения
|@выводит:	ничего
|
*/
if ( ! function_exists('connectdb'))
{
    function connectdb($db='rf_user')
    {
		license_file();
        $CI =& get_instance();
        include ( APPPATH . "config/rf_config.php" );
        $connect=explode('|',$server[0]);
        $config['hostname'] = $connect[0];
        $config['username'] = $connect[1];
        $config['password'] = $connect[2];
        $config['database'] = $connect[3];
        $config['dbdriver'] = "mssql";
        $config['pconnect'] = FALSE;
        $config['db_debug'] = TRUE;
        $GLOBALS['SELF_DB']=(bool)$connect[5];
        $CI->MSSQL=$CI->load->database($config,true);
        //echo 'соединение с mssql<hr>';
    }
}
/*
|--------------------------------------------------------------------------
| Соединяется с базой MY SQL
|--------------------------------------------------------------------------
|
|@принимает: ничего
|@выводит:	ничего
|
*/
if ( ! function_exists('connectmydb'))
{
    function connectmydb()
    {
        $CI =& get_instance();
        include ( APPPATH . "config/rf_config.php" );
        $connect=explode('|',$server[1]);
        $config['hostname'] = $connect[0];
        $config['username'] = $connect[1];
        $config['password'] = $connect[2];
        $config['database'] = $connect[3];
        $config['dbdriver'] = "mysql";
        $config['char_set'] = "cp1251";
        $config['dbcollat'] = "cp1251_general_ci";
        $config['pconnect'] = FALSE;
        $config['db_debug'] = TRUE;
        $CI->MYSQL=$CI->load->database($config,true);
      //  echo 'соединение с mysql<hr>';
    }
}
/*
|--------------------------------------------------------------------------
| Получение имени базы мира игры
|--------------------------------------------------------------------------
|
|@принимает: ничего
|@выводит:	имя мира
|
*/
if ( ! function_exists('get_world'))
{
	function get_world()
	{
	    $CI =& get_instance();
	    include ( APPPATH . "config/rf_config.php" );
	    $connect=explode('|',$server[0]);
	    return $connect[3];
	}
}	
/*
|--------------------------------------------------------------------------
| Получение имени базы аккаунтов игры
|--------------------------------------------------------------------------
|
|@принимает: ничего
|@выводит:	имя базы аккаунтов
|
*/
if ( ! function_exists('get_user'))
{
	function get_user()
	{
	    $CI =& get_instance();
	    include ( APPPATH . "config/rf_config.php" );
	    $connect=explode('|',$server[0]);
	    return $connect[4];
	}
}
/*
|--------------------------------------------------------------------------
| Поиск пустого слота
|--------------------------------------------------------------------------
|
|@принимает: AccountSerial
|@выводит:	номер слота
|
*/
if ( ! function_exists('find_empty_slot'))
{
	function find_empty_slot($AccountSerial)
	{
	    $CI =& get_instance();
	    if($AccountSerial=='' || $AccountSerial==0) return 0;
	    $world = get_world();$dd='';
	    $AccountSerial=xss_clean($AccountSerial);
	    $query = $CI->MSSQL->query("SELECT * FROM {$world}.dbo.tbl_AccountTrunk WHERE AccountSerial = '$AccountSerial'");
	    if ($query->num_rows() > 0)
	    {
	     foreach ($query->result() as $row)
	        {
	           for ($i=0; $i<100; $i++)
	           {
	               eval('if ($row->K'.$i.'=="-1" & $dd=="") { $dd = "'.$i.'"; }');
	           }
	        }
	        if($dd=='') {$dd=100;}
	        return $dd;
	    } else return 100;
	}
}
/*
|--------------------------------------------------------------------------
| Вывод таблицы по типу
|--------------------------------------------------------------------------
|
|@принимает: тип кода
|@выводит:	имя таблицы
|
*/
if ( ! function_exists('GetTableName'))
{
	function GetTableName($type)
	{
		if($type=='bx')	{return 'tbl_code_box';}
		if($type=='iw')	{return 'tbl_code_weapon';}
		if($type=='tr')	{return 'tbl_code_trap';}
		if($type=='in')	{return 'tbl_code_unitkey';}
		if($type=='un')	{return 'tbl_code_unmannedminer';}
		if($type=='iu')	{return 'tbl_code_upper';}
		if($type=='sk')	{return 'tbl_code_siegekit';}
		if($type=='ti')	{return 'tbl_code_ticket';}
		if($type=='iq')	{return 'tbl_code_town';}
		if($type=='id')	{return 'tbl_code_shield';}
		if($type=='rd')	{return 'tbl_code_radar';}
		if($type=='re')	{return 'tbl_code_recovery';}
		if($type=='ir')	{return 'tbl_code_resource';}
		if($type=='ii')	{return 'tbl_code_ring';}
		if($type=='is')	{return 'tbl_code_shoe';}
		if($type=='im')	{return 'tbl_code_maketool';}
		if($type=='iz')	{return 'tbl_code_map';}
		if($type=='lk')	{return 'tbl_code_npclink';}
		if($type=='io')	{return 'tbl_code_ore';}
		if($type=='ip')	{return 'tbl_code_potion';}
		if($type=='gt')	{return 'tbl_code_guardtower';}
		if($type=='il')	{return 'tbl_code_lower';}
		if($type=='ih')	{return 'tbl_code_helmet';}
		if($type=='ev')	{return 'tbl_code_event';}
		if($type=='if')	{return 'tbl_code_face';}
		if($type=='fi')	{return 'tbl_code_firecracker';}
		if($type=='ic')	{return 'tbl_code_force';}
		if($type=='ix')	{return 'tbl_code_battledungeon';}
		if($type=='ib')	{return 'tbl_code_bullet';}
		if($type=='ik')	{return 'tbl_code_cloak';}
		if($type=='ig')	{return 'tbl_code_gauntlet';}
		if($type=='ia')	{return 'tbl_code_amulet';}
		if($type=='ij')	{return 'tbl_code_animus';}
		if($type=='ie')	{return 'tbl_code_bag';}
		if($type=='it')	{return 'tbl_code_battery';}
		if($type=='iy')	{return 'tbl_code_booty';}
	}
}
/*
|--------------------------------------------------------------------------
| Проверка на бан
|--------------------------------------------------------------------------
|
|@принимает: AccountSerial
|@выводит:	статус с подробностями
|
*/
if ( ! function_exists('CheckBan'))
{
	function CheckBan($SA)
	{
	    $CI =& get_instance();
	    $user= get_user();
	    $query_chk = $CI->MSSQL->query("SELECT dtStartDate, nPeriod, nKind, szReason FROM {$user}.dbo.tbl_UserBan WHERE nAccountSerial = $SA");
	    $query_chk2 = $CI->MSSQL->query("SELECT COUNT(*) as count FROM {$user}.dbo.tbl_UserBan_Log WHERE nAccountSerial = '$SA'");
	    if ($query_chk->num_rows() > 0)
		{
	     foreach ($query_chk->result() as $row)
	        {
	            if($row->nKind==0) $text=icon('32x32/delete_user.png').lang('off_ban_acc').$row->nPeriod.lang('off_day');
	            if($row->nKind==1) $text=icon('32x32/user_comment.png').lang('off_ban_chat').$row->nPeriod.lang('off_day');
	            $text.=br(1).check_refban($SA);
	        }
	    } else $text = icon('32x32/user_accept.png').lang('off_ban_work');
	    if ($query_chk2->num_rows() > 0)
		{
	         foreach ($query_chk2->result() as $row)
	        {
	           if($row->count>=1) $text.=nbs(2).lang('off_ban_last').$row->count.lang('off_ban_last2');
	        }
	    }
	return $text;
	}
}
if ( ! function_exists('FastCheckBan'))
{
	function FastCheckBan($SA)
	{
	    $CI =& get_instance();
	    $user= get_user();
	    $query_chk = $CI->MSSQL->query("SELECT dtStartDate, nPeriod, nKind, szReason FROM {$user}.dbo.tbl_UserBan WHERE nAccountSerial = $SA AND nKind = 0");
	    if ($query_chk->num_rows() > 0)	 return TRUE; else return FALSE;
   }
}
/*
|--------------------------------------------------------------------------
| Бан аккаунта
|--------------------------------------------------------------------------
|
|@принимает: AccountSerial, Период, Тип бана, причина
|@выводит:	bool
|
*/
if ( ! function_exists('ban'))
{
function ban($nAccountSerial,$nPeriod,$nKind,$szReason)
  {
		  $CI =& get_instance();
		  $dtStartDate = date("Y-m-d G:i").":00"; 
		  $CI->MSSQL->query("INSERT INTO rf_user.dbo.tbl_UserBan 
		  (nAccountSerial, dtStartDate, nPeriod, nKind, szReason) 
		  VALUES ($nAccountSerial, '$dtStartDate', $nPeriod, $nKind, '$szReason')");
		  return true;
  } 
}
/*
|--------------------------------------------------------------------------
| Получение AccountSerial по нику
|--------------------------------------------------------------------------
|
|@принимает: Ник
|@выводит:	AccountSerial
|
*/  
if ( ! function_exists('GetASerialFromName'))
{
	function GetASerialFromName($name)
	{
	    $CI =& get_instance();
	    $world = get_world();
	    $query = $CI->MSSQL->query("SELECT AccountSerial FROM {$world}.dbo.tbl_base WHERE name='{$name}'");
	    if ($query->num_rows() > 0)
	    {
	        foreach ($query->result() as $row)
	        {
	            return $row->AccountSerial ;
	        }
	    } else return 0;
	}
}

/*
|--------------------------------------------------------------------------
| Поик пустого слота в банке игрока
|--------------------------------------------------------------------------
|
|@принимает: AccountSerial
|@выводит:	номер ячейки при ошибке выводит значение 100
|в базе ячейки от 0 до 99
|
*/ 
if ( ! function_exists('find_empty_slot'))
{
	function find_empty_slot($AccountSerial)
	{
	    $CI =& get_instance();
	    if($AccountSerial=='' || $AccountSerial==0) return 100;
	    $world = get_world();$dd='';
	    $AccountSerial=xss_clean($AccountSerial);
	    $query = $CI->MSSQL->query("SELECT * FROM {$world}.dbo.tbl_AccountTrunk WHERE AccountSerial = '$AccountSerial'");
	    if ($query->num_rows() > 0)
	    {
	     foreach ($query->result() as $row)
	        {
	           for ($i=0; $i<100; $i++)
	           {
	               eval('if ($row->K'.$i.'=="-1" & $dd=="") { $dd = "'.$i.'"; }');
	           }
	        }
	        if($dd=='') {$dd=100;}
	        return $dd;
	    } else return 100;
	}
}

/*
|--------------------------------------------------------------------------
| Получение id предмета по гм коду
|--------------------------------------------------------------------------
|
|@принимает: таблица, код
|@выводит:	id предмета
|в базе ячейки от 0 до 99
|
*/
if ( ! function_exists('GetItemCode'))
{
	function GetItemCode($table='',$code='')
	{
	    $CI =& get_instance();
	    if($table!=='')
	    {
	        $query = $CI->MSSQL->query("SELECT * FROM RF_ItemsDB.dbo.".$table." WHERE item_code='".$code."'");
	        foreach ($query->result() as $row)
	        {
	            return $row->item_id;
	        }
	    } else return 0;
	}
}

function compile_rfcode ($id,$detype,$slot)
{
    $numdec=bindechex($id, 3);
    $full=$numdec.$detype.$slot;
    $fulldec=bindechex($full, 5);
    return $fulldec;
}
function GetItemType($type)
{
	$resuli='';
		if($type=='ff')	{ $resuli='';}
	elseif($type=='00')	{ $resuli='iu';}
	elseif($type=='01') { $resuli='il';}
	elseif($type=='02') { $resuli='ig';}
	elseif($type=='03') { $resuli='is';}
	elseif($type=='04') { $resuli='ih';}
	elseif($type=='05') { $resuli='id';}
	elseif($type=='06') { $resuli='iw';}
	elseif($type=='07') { $resuli='ik';}
	elseif($type=='08') { $resuli='ii';}
	elseif($type=='09') { $resuli='ia';}
	elseif($type=='10') { $resuli='it';}	
	elseif($type=='12') { $resuli='ir';}
	elseif($type=='14') { $resuli='iy';}
	elseif($type=='15') { $resuli='iz';}
	elseif($type=='16') { $resuli='iq';}
	elseif($type=='1f') { $resuli='bx';}
	elseif($type=='0f') { $resuli='ic';}
	elseif($type=='0d') { $resuli='ip';}
	elseif($type=='0e') { $resuli='ie';}
	elseif($type=='0b') { $resuli='im';}
	elseif($type=='1e') { $resuli='re';}
	elseif($type=='0a') { $resuli='ib';}
	elseif($type=='21') { $resuli='un';}	
	elseif($type=='22') { $resuli='rd';}	
	elseif($type=='35') { $resuli='lk';}	
	return $resuli;
}
#########################################################################################
### получение типов
function getdetype($type)
{
	if($type=="lk") {return "35";}
	if($type=="rd") {return "22";}
	if($type=="un") {return "21";}
	if($type=="fi") {return "20";}
	if($type=="bx") {return "1f";}
	if($type=="re") {return "1e";}
	if($type=="ev") {return "1d";}
	if($type=="ti") {return "1c";}
	if($type=="sk") {return "1b";}
	if($type=="tr") {return "1a";}
	if($type=="gt") {return "19";}
	if($type=="ij") {return "18";}
	if($type=="ix") {return "17";}
	if($type=="iq") {return "16";}
	if($type=="iz") {return "15";}
	if($type=="iy") {return "14";}
	if($type=="in") {return "13";}
	if($type=="ic") {return "0f";}
	if($type=="ir") {return "12";}
	if($type=="io") {return "11";}
	if($type=="it") {return "10";}
	if($type=="ie") {return "0e";}
	if($type=="ip") {return "0d";}
	if($type=="im") {return "0b";}
	if($type=="ib") {return "0a";}
	if($type=="it") {return "10";}	
	if($type=="ia") {return "09";}
	if($type=="ii") {return "08";}
	if($type=="ik") {return "07";}
	if($type=="iw") {return "06";}
	if($type=="id") {return "05";}
	if($type=="ih") {return "04";}
	if($type=="is") {return "03";}
	if($type=="ig") {return "02";}
	if($type=="il") {return "01";}
	if($type=="iu") {return "00";}
}
#########################################################################################
### конвентатор


function show_talic( $code )
{
	$code=trim($code);
    $code	=	str_replace('0x', '', $code);
    if ($code=='fffffff') return '<a title="Upgrade: '.$code.'">empty</a>';
    $count	=	substr($code,0,1);
    $num	=	substr($code,1,$count);
    //if(strlen($num)<>7) return '<a title="Upgrade: '.$code.'">'.icon('24x24/block.png').'</a>';
    $num=str_replace ('0', 'TAL0', $num);
    $num=str_replace ('1', 'TAL1', $num);
    $num=str_replace ('2', 'TAL2', $num);
    $num=str_replace ('3', 'TAL3', $num);
    $num=str_replace ('4', 'TAL4', $num);
    $num=str_replace ('5', 'TAL5', $num);
    $num=str_replace ('6', 'TAL6', $num);
    $num=str_replace ('7', 'TAL7', $num);
    $num=str_replace ('8', 'TAL8', $num);
    $num=str_replace ('9', 'TAL9', $num);
    $num=str_replace ('a', 'TALA', $num);
    $num=str_replace ('b', 'TALB', $num);
    $num=str_replace ('c', 'TALC', $num);
    $num=str_replace ('d', 'TALD', $num);
    $num=str_replace ('f', 'TALF', $num);

    $num=str_replace ('TAL0', icon('talic/t-01.png','Ignorant Talic','width="13" height="19"'),		$num);
    $num=str_replace ('TAL1', icon('talic/t-02.png','Destruction Talic','width="13" height="19"'), 	$num);
    $num=str_replace ('TAL2', icon('talic/t-03.png','Darkness Talic','width="13" height="19"'), 	$num);
    $num=str_replace ('TAL3', icon('talic/t-04.png','Chaos Talic','width="13" height="19"'), 		$num);
    $num=str_replace ('TAL4', icon('talic/t-05.png','Hatred Talic','width="13" height="19"'), 		$num);
    $num=str_replace ('TAL5', icon('talic/t-06.png','Favor Talic','width="13" height="19"'), 		$num);
    $num=str_replace ('TAL6', icon('talic/t-07.png','Wisdom Talic','width="13" height="19"'), 		$num);
    $num=str_replace ('TAL7', icon('talic/t-08.png','SacredFire Talic','width="13" height="19"'), 	$num);
    $num=str_replace ('TAL8', icon('talic/t-09.png','Belief Talic','width="13" height="19"'), 		$num);
    $num=str_replace ('TAL9', icon('talic/t-10.png','Guard Talic','width="13" height="19"'), 		$num);
    $num=str_replace ('TALA', icon('talic/t-11.png','Glory Talic','width="13" height="19"'), 		$num);
    $num=str_replace ('TALB', icon('talic/t-12.png','Grace Talic','width="13" height="19"'), 		$num);
    $num=str_replace ('TALC', icon('talic/t-13.png','Mercy Talic','width="13" height="19"'), 		$num);
    $num=str_replace ('TALD', icon('talic/t-14.png','Restoration Talic','width="13" height="19"'), 	$num);
    $num=str_replace ('TALF', icon('talic/t-00.png','No upgrade','width="13" height="19"'), 		$num);
    return $num;
}

// ------------------------------------------------------------------------

/**
 * Получение AccountSerial
 *
 * @param	none or login
 * @return	string
 */
function Get_AccountSerial($login='')
{
 	$CI=	& get_instance();
 	$user	= get_user();
 	if($CI->AccountSerial<>0) return $CI->AccountSerial;
 	$CI->MSSQL->cache_on();
 	$my=false;
	if($login=='') {$login = get_login();$my=true;}
	
    if($login{0}=='!')
    {
        $query_auth = $CI->MSSQL->query("SELECT Serial FROM {$user}.dbo.tbl_StaffAccount WHERE ID = CONVERT(binary,'".xss_clean($login)."')");
        if ($query_auth->num_rows() > 0)
        {
            foreach ($query_auth->result() as $row)
            {
            	if($my==true) $CI->AccountSerial=$row->Serial;
                return $row->Serial;
            }
        } else return 0;
    }
	if($login)
	{
        $query_auth = $CI->MSSQL->query("SELECT * FROM {$user}.dbo.tbl_UserAccount WHERE id = CONVERT(binary,'".xss_clean($login)."')");
        if ($query_auth->num_rows() > 0)
        {
            foreach ($query_auth->result() as $row)
            {
            	if($my==true) $CI->AccountSerial=$row->serial;
                return $row->serial;
            }
        } else return 0;
	} else return 0;
}
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

/**
 * Получение AccountSerial
 *
 * @param	 login
 * @return	int
 */
function Get_AS($login='')
{
 	$CI=	& get_instance();
 	$user	= get_user();
    $query_auth = $CI->MSSQL->query("SELECT * FROM {$user}.dbo.tbl_UserAccount WHERE id = CONVERT(binary,'".xss_clean($login)."')");
        if ($query_auth->num_rows() > 0)
        {
            foreach ($query_auth->result() as $row)
            {
                return $row->serial;
            }
        } else return 0;
}
/*
|--------------------------------------------------------------------------
| Определение логина по номеру аккаунта
|--------------------------------------------------------------------------
|
|@принимает: Номер аккаунта
|@выводит:	логин
|
*/
if ( ! function_exists('GetLoginOnAS'))
{
function GetLoginOnAS($ASerial)
{
    $CI =& get_instance();
    $query = $CI->MSSQL->query("SELECT Account FROM ".get_world().".dbo.tbl_base WHERE AccountSerial=$ASerial");
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
            return$row->Account;
        }
    }
}
}
/**
 * Получение причины
 *
 * @param string $SA 
 * @return empty
 * @author NetSoul
 */
function get_reason($SA)
{
    $CI =& get_instance();
    $user= get_user();
    $query_chk = $CI->MSSQL->query("SELECT dtStartDate, nPeriod, nKind, szReason FROM {$user}.dbo.tbl_UserBan WHERE nAccountSerial = $SA");
     if ($query_chk->num_rows() > 0)
	{
     foreach ($query_chk->result() as $row)
        {
            return $row->szReason;
        }
    } else return '';
}
function BanKind($type)
{
	if($type==0) return lang('off_ban_acc2');
	if($type==1) return lang('off_ban_chat');
}
// если есть то true если нет то false
function player_exits($name)
{
    $CI =& get_instance();
    $world = get_world();
    $query = $CI->MSSQL->query("SELECT Name FROM {$world}.dbo.tbl_base WHERE Name = '$name'");
    if ($query->num_rows() > 0){ return true;} else return false;
}
//получение имени гильдии
function get_guild($gserial)
{
    $CI =& get_instance();
    $world = get_world();
    if($gserial!=='-1')
    {
        $query = $CI->MSSQL->query("SELECT Id FROM {$world}.dbo.tbl_Guild WHERE Serial=".$gserial);
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                return $row->Id;
            }
        } else return 'None';
    } else return 'Без гильдии';
}
function get_total_min($min)
{
    return timespan(time()-($min*60),time());
}
function get_race($r)
{
    if($r==0) return icon('icon_bcc.gif').icon('male.gif');
    if($r==1) return icon('icon_bcc.gif').icon('female.gif');
    if($r==2) return icon('icon_ccc.gif').icon('male.gif');
    if($r==3) return icon('icon_ccc.gif').icon('female.gif');
    if($r==4) return icon('icon_acc.gif');
    if($r>4) return 'O_o what?';
}
function get_rfclass($class)
{
    $CI =& get_instance();
    $query = $CI->MSSQL->query("SELECT class_name FROM RF_ItemsDB.dbo.tbl_Classes WHERE class_code='".$class."'");
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
            return $row->class_name;
        }
    } else return 'o_O';
}
function is_online($serial)
{
   $CI =& get_instance();
   $world = get_world();
   $query = $CI->MSSQL->query("SELECT * FROM {$world}.dbo.tbl_general WHERE OnlineStatus>=dateadd(minute,-5,getdate()) AND serial=".$serial);
   if ($query->num_rows() > 0) return true; else return false;
}
function secure_serial_check($serial)
{
   $CI =& get_instance();$world = get_world();
   if(!is_numeric($serial)) return false;
   $query = $CI->MSSQL->query("SELECT Name FROM {$world}.dbo.tbl_base WHERE Serial={$serial} AND Account='".get_login()."'");
   if ($query->num_rows() > 0) return true; else return false;
}


function show_lider_rank($num)
{
    if($num==0) return lang('off_lider_rank0');
    if($num==1) return lang('off_lider_rank1');
    if($num==2) return lang('off_lider_rank2');
    if($num==3) return lang('off_lider_rank3');
    if($num==4) return lang('off_lider_rank4');
    if($num==5) return lang('off_lider_rank5');
    if($num==6) return lang('off_lider_rank6');
    if($num==7) return lang('off_lider_rank7');
    if($num==8) return lang('off_lider_rank8');
    return lang('off_nonlider');
}
function show_rank_state($num)
{
    if($num==1) return lang('off_rank_state0');
    if($num==2) return lang('off_rank_state1');
    if($num==3) return lang('off_rank_state2');
    if($num==4) return lang('off_rank_state3');
    return lang('off_nonlider');
}
function get_race2($r)
{
    if($r==0) return icon('icon_bcc.gif');
    if($r==1) return icon('icon_ccc.gif');
    if($r==2) return icon('icon_acc.gif');
    if($r>4) return 'O_o what?';
}
function GetID($var,$adm=true)
{
	$de=bindechex($var, 3);
	$num=strlen($de);$num2=$num-2;
	$num3=$num2-2;
	$type=substr($de,$num2-2,$num2);
	$type=GetItemType(substr($type,0,2));
	$item=substr($de,0,$num2-2);
	$code = bindechex($item, 5);
	if ($type=='') return lang('off_emptyslot'); else return GetItemID(GetTableName($type),$code,$adm);
}
#########################################################################################
### получение кода предмета
function GetItemID($table,$code,$adm=true)
{
    $CI =& get_instance();
	if($table<>'')
	{
	    $query = $CI->MSSQL->query("SELECT * FROM RF_ItemsDB.dbo.".$table." WHERE item_id='".$code."'");
	    foreach ($query->result() as $row)
	    {
	        if($adm==false) return trim_block($row->item_name); else return $row->item_code;
	    }
	}
	return icon('16x16/block.png',lang('off_emptyslot'));
}
function permaban($account)
{
	$CI =& get_instance();
	$CI->MSSQL->query("UPDATE rf_user.dbo.".config('table_account','query')." SET Password = (CONVERT(binary, '')) WHERE Id = CONVERT(binary,'".$account."')");
}
function active_record_serial_data($serial)
{
	$CI =& get_instance();
	$world = get_world();
	$query=$CI->MSSQL->query("SELECT * FROM {$world}.dbo.tbl_base WHERE Serial='$serial'");
	return $row = $query->row_array();
}
function maps($text)
{
	if($text==0) {return 'Базовая локация Беллато';}
	elseif($text==1) {return 'Базовая локация Кор';}
	elseif($text==2) {return 'Краговы Шахты';}
	elseif($text==3) {return 'Базовая локация Акретия';}
	elseif($text==4) {return 'Укрепление Солус';}
	elseif($text==5) {return 'Укрепление Анакаде';}
	elseif($text==6) {return 'Колония Харам';}
	elseif($text==7) {return 'Колония Нумерус';}
	elseif($text==8) {return 'Застава 213';}
	elseif($text==9) {return 'Застава 117';}
	elseif($text==10) {return 'Этер';}
	elseif($text==11) {return 'Сеттова пустыня';}
	elseif($text==12) {return 'Вулкан';}
	elseif($text==13) {return 'Элан';}
	elseif($text==24) {return 'Земли изгнанников';}
	elseif($text==25) {return 'Горы чудовищ';}
	elseif($text==26) {return 'Биолаборатория Картелы';}
	else {return $text;}
}
if ( ! function_exists('GetLoginOnSerial'))
{
	function GetLoginOnSerial($ASerial)
	{
	    $CI =& get_instance();
	    $query = $CI->MSSQL->query("SELECT Account FROM ".get_world().".dbo.tbl_base WHERE Serial=$ASerial");
	    if ($query->num_rows() > 0)
	    {
	        foreach ($query->result() as $row)
	        {
	            return $row->Account;
	        }//for
	    }//if
	}//fun
}//ifex
function chararray($login='')
{
	if($login=='') $login=get_login();
	$CI =& get_instance();
	$char=array();
	$query =$CI->MSSQL->query("SELECT Serial,Name FROM ".get_world().".dbo.tbl_base WHERE Account='$login' AND DCK=0");
    if ($query->num_rows() > 0)
    {
        foreach ($query->result() as $row)
        {
            $char[$row->Serial]=$row->Name;
        }//for
    } else return false;//if
    return $char;
}
function get_new_sex($serial)
{
	    $CI =& get_instance();
	    $query = $CI->MSSQL->query("SELECT Race FROM ".get_world().".dbo.tbl_base WHERE Serial=$serial");
	    if ($query->num_rows() > 0)
	    {
	        foreach ($query->result() as $row)
	        {
	            $race=$row->Race;
	            if($race==0) return '1';
	            if($race==1) return '0';
	            if($race==2) return '3';
	            if($race==3) return '2';
	            return '4';
	        }//for
	    }//if	
}
function get_name($serial)
{
	    $CI =& get_instance();
	    $query = $CI->MSSQL->query("SELECT Name FROM ".get_world().".dbo.tbl_base WHERE Serial=$serial");
	    if ($query->num_rows() > 0)
	    {
	        foreach ($query->result() as $row)
	        {
	            return $row->Name;
	        }//for
	    }//if	
}
function get_serial($name)
{
	    $CI =& get_instance();
	    $query = $CI->MSSQL->query("SELECT Serial FROM ".get_world().".dbo.tbl_base WHERE Name='$name'");
	    if ($query->num_rows() > 0)
	    {
	        foreach ($query->result() as $row)
	        {
	            return $row->Serial;
	        }//for
	    }//if	
}
function init_race($r)
{
    if($r==0) return 'Мужской';
    if($r==1) return 'Женский';
    if($r==2) return 'Мужской';
    if($r==3) return 'Женский';
    if($r==4) return 'Средний';
    if($r>4) return 'O_o what?';
}
function change_race($serial,$sex)
{
	    $CI =& get_instance();
	    $CI->MSSQL->query("UPDATE ".get_world().".dbo.tbl_base SET Race ='$sex' WHERE Serial  = '".$serial."'");
}

if ( ! function_exists('return_db'))
{
    function return_db($db='rf_user')
    {
		license_file();
        $CI =& get_instance();
        include ( APPPATH . "config/rf_config.php" );
        $connect=explode('|',$server[0]);
        $config['hostname'] = $connect[0];
        $config['username'] = $connect[1];
        $config['password'] = $connect[2];
        $config['database'] = $connect[3];
        $config['dbdriver'] = "mssql";
        $config['pconnect'] = FALSE;
        $config['db_debug'] = TRUE;
        $GLOBALS['SELF_DB']=(bool)$connect[5];
        return $config;
    }
}

if ( ! function_exists('return_mydb'))
{
    function return_mydb()
    {
        $CI =& get_instance();
        include ( APPPATH . "config/rf_config.php" );
        $connect=explode('|',$server[1]);
        $config['hostname'] = $connect[0];
        $config['username'] = $connect[1];
        $config['password'] = $connect[2];
        $config['database'] = $connect[3];
        $config['dbdriver'] = "mysql";
        $config['char_set'] = "cp1251";
        $config['dbcollat'] = "cp1251_general_ci";
        $config['pconnect'] = FALSE;
        $config['db_debug'] = TRUE;
        return $config;
    }
}
?>