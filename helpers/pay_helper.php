<?php
// ------------------------------------------------------------------------
/**
 * FDCore Studio
 *
 * Хелпер системы оплат
 *
 * @package		CodeIgniter
 * @author		NetSoul
 * @copyright	Copyright (c) 2009, FDCore
 * @link		http://fdcore.ru
 * @since		Version 1.0 [10.05.09]
*/
// ------------------------------------------------------------------------

/*
|--------------------------------------------------------------------------
| Показ количества донат денег
|--------------------------------------------------------------------------
|
|@принимает: Номер аккаунта
|@выводит:	количество денег
|
*/
if ( ! function_exists('donate_show'))
{
    function donate_show($AccountSerial)
    {
        $CI =& get_instance();
        if($AccountSerial=='' || $AccountSerial==0) return 0;
        $user = get_user();
        $AccountSerial=xss_clean($AccountSerial);
        $query = $CI->MSSQL->query("SELECT JoinCode FROM {$user}.dbo.tbl_UserAccount WHERE serial={$AccountSerial}");
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                return $row->JoinCode;
            }
        } else return 0;
    }
}
/*
|--------------------------------------------------------------------------
| Изменяет значение донат денег
|--------------------------------------------------------------------------
|
|@принимает: Номер аккаунта, количество денег
|@выводит:	ничего
|
*/
if ( ! function_exists('donate_edit'))
{
    function donate_edit($AccountSerial,$donate)
    {
        $CI =& get_instance();
        if($AccountSerial=='' || $AccountSerial==0) return 0;
        $user = get_user();
        $CI->MSSQL->query("UPDATE {$user}.dbo.tbl_UserAccount SET JoinCode='{$donate}' WHERE serial={$AccountSerial}");
        return 1;
    }
}
/*
|--------------------------------------------------------------------------
| Добавляет донат денег
|--------------------------------------------------------------------------
|
|@принимает: Номер аккаунта, количество добавляемых денег
|@выводит:	ничего
|
*/
if ( ! function_exists('donate_add'))
{
    function donate_add($AccountSerial,$donate)
    {
        $CI =& get_instance();
        if($AccountSerial=='' || $AccountSerial==0) return 0;
        $user = get_user();
        $CI->MSSQL->query("UPDATE {$user}.dbo.tbl_UserAccount SET JoinCode=JoinCode+'{$donate}' WHERE serial={$AccountSerial}");
        return 1;
    }
}
/*
|--------------------------------------------------------------------------
| Показ количества бонус денег
|--------------------------------------------------------------------------
|
|@принимает: Номер аккаунта
|@выводит:	кол. бонус денег
|
*/
if ( ! function_exists('bonus_show'))
{
    function bonus_show($AccountSerial)
    {
        $CI =& get_instance();
        if($AccountSerial=='' || $AccountSerial==0) return 0;
        $user = get_user();
        $AccountSerial=xss_clean($AccountSerial);
        $query = $CI->MSSQL->query("SELECT gpoint FROM {$user}.dbo.tbl_UserAccount WHERE serial={$AccountSerial}");
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                return $row->gpoint;
            }
        } else return 0;
    }
}
/*
|--------------------------------------------------------------------------
| Изменить количество бонусов
|--------------------------------------------------------------------------
|
|@принимает: Номер аккаунта, количество бонусов
|@выводит:	ничего
|
*/
if ( ! function_exists('bonus_edit'))
{
    function bonus_edit($AccountSerial,$donate)
    {
        $CI =& get_instance();
        if($AccountSerial=='' || $AccountSerial==0) return 0;
        $user = get_user();
        $CI->MSSQL->query("UPDATE {$user}.dbo.tbl_UserAccount SET gpoint='{$donate}' WHERE serial={$AccountSerial}");
        return 1;
    }
}
/*
|--------------------------------------------------------------------------
| Добавить бонусов на аккаунт
|--------------------------------------------------------------------------
|
|@принимает: Номер аккаунта, количество бонусов для добавления
|@выводит:	ничего
|
*/
if ( ! function_exists('bonus_add'))
{
    function bonus_add($AccountSerial,$donate)
    {
        $CI =& get_instance();
        $donate=(int)$donate;
        if($AccountSerial=='' || $AccountSerial==0) return 0;
        $user = get_user();
        $CI->MSSQL->query("UPDATE {$user}.dbo.tbl_UserAccount SET gpoint=gpoint+'{$donate}' WHERE serial={$AccountSerial}");
        return 1;
    }
}
/*
|--------------------------------------------------------------------------
| Добавить кеша на аккаунт
|--------------------------------------------------------------------------
|
|@принимает: Номер аккаунта, количество кеша для добавления
|@выводит:	ничего
|
*/
if ( ! function_exists('cash_add'))
{
    function cash_add($AccountSerial,$donate)
    {
        $CI =& get_instance();
        if($AccountSerial=='' || $AccountSerial==0) return 0;
        $user = get_user();
        $query = $CI->MSSQL->query("SELECT Cash FROM BILLING.dbo.tbl_user WHERE UserID='".GetLoginOnAS($AccountSerial)."'");
        if ($query->num_rows() == 0) $CI->MSSQL->query("INSERT INTO BILLING.dbo.tbl_user (ID,UserID,Cash) VALUES ($AccountSerial,'".GetLoginOnAS($AccountSerial)."', 0)"); 
        $CI->MSSQL->query("UPDATE BILLING.dbo.tbl_user SET Cash=Cash+'{$donate}' WHERE UserID='".GetLoginOnAS($AccountSerial)."'");
        return 1;
    }
}
?>