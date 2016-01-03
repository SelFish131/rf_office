<?
$config['table_account']='tbl_LUAccount';

# login and fireguard check
$config['query_login_withfg'] = 'SELECT {user}.dbo.tbl_LUAccount.id,{user}.dbo.tbl_LUAccount.Password, 
{user}.dbo.tbl_UserAccount.uilock_pw
FROM {user}.dbo.tbl_LUAccount,{user}.dbo.tbl_UserAccount
WHERE {user}.dbo.tbl_LUAccount.id =?
AND {user}.dbo.tbl_LUAccount.Password=?
AND {user}.dbo.tbl_UserAccount.uilock_pw=?
AND {user}.dbo.tbl_UserAccount.id={user}.dbo.tbl_LUAccount.id';

# login check
$config['query_login']='SELECT CONVERT(varchar,{user}.dbo.tbl_LUAccount.id) as id ,CONVERT(varchar,{user}.dbo.tbl_LUAccount.Password) as Password
FROM {user}.dbo.tbl_LUAccount
WHERE {user}.dbo.tbl_LUAccount.id=CONVERT(binary,?)
AND {user}.dbo.tbl_LUAccount.Password=CONVERT(binary,?)
';

# game master auth
$config['query_gmlogin']='SELECT ID, PW FROM {user}.dbo.tbl_StaffAccount WHERE ID=? AND PW=?';

# register
/*
$config['query_register']='INSERT INTO {user}.dbo.tbl_rfaccount (id,password,accounttype,birthdate,Email)
VALUES ((CONVERT (binary,?)),(CONVERT (binary,?)),0,0,?)';
*/	
$config['query_register']='INSERT INTO {user}.dbo.tbl_LUAccount (id,password,BCodeTU,Email)
VALUES ((CONVERT (binary,?)),(CONVERT (binary,?)),1,?)';

$config['query_billregister1']='INSERT INTO BILLING.dbo.tbl_User (ID,UserID,Cash) VALUES (?,?,0)';
$config['query_billregister2']='INSERT INTO BILLING.dbo.tbl_personal_billing (ID,BillingType,EndDate,RemainTime) 
VALUES ((CONVERT (binary,?)),1,GETDATE(),null)';
# is_logged
$config['query_is_logged']="SELECT Id, Password FROM {user}.dbo.tbl_LUAccount WHERE Id = ? AND Password=?";

# account info
$config['query_account_q1']='SELECT *
FROM {user}.dbo.tbl_UserAccount,{user}.dbo.tbl_LUAccount,{world}.dbo.tbl_AccountTrunk
WHERE {user}.dbo.tbl_UserAccount.id=?
AND {user}.dbo.tbl_UserAccount.id={user}.dbo.tbl_LUAccount.id
AND {world}.dbo.tbl_AccountTrunk.AccountSerial={user}.dbo.tbl_UserAccount.serial';

# account info for GM
$config['query_account_q2']='SELECT *
FROM {user}.dbo.tbl_StaffAccount,{world}.dbo.tbl_AccountTrunk
WHERE {user}.dbo.tbl_StaffAccount.ID=?
AND {world}.dbo.tbl_AccountTrunk.AccountSerial={user}.dbo.tbl_StaffAccount.Serial';

$config['query_game_premium']="UPDATE BILLING.dbo.tbl_personal_billing SET BillingType=2, EndDate=DATEADD(month, ?, GETDATE()) WHERE ID='?'";