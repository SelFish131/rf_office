<h2>��� Fireguard ����</h2>
<?
$CI	=&get_instance();
$CI->MSSQL->query("DELETE FROM rf_user.dbo.tbl_UserBan WHERE nPeriod = 24");
?>