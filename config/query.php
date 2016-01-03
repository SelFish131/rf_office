<?
$config['table_account']='tbl_rfaccount';

# login and fireguard check
$config['query_login_withfg'] = 'SELECT {user}.dbo.tbl_rfaccount.id,{user}.dbo.tbl_rfaccount.Password, 
{user}.dbo.tbl_UserAccount.uilock_pw
FROM {user}.dbo.tbl_rfaccount,{user}.dbo.tbl_UserAccount
WHERE {user}.dbo.tbl_rfaccount.id =?
AND {user}.dbo.tbl_rfaccount.Password=?
AND {user}.dbo.tbl_UserAccount.uilock_pw=?
AND {user}.dbo.tbl_UserAccount.id={user}.dbo.tbl_rfaccount.id';

# login check
$config['query_login']='SELECT {user}.dbo.tbl_rfaccount.id,{user}.dbo.tbl_rfaccount.Password
FROM {user}.dbo.tbl_rfaccount,{user}.dbo.tbl_UserAccount
WHERE {user}.dbo.tbl_rfaccount.id = ?
AND {user}.dbo.tbl_rfaccount.Password=?
AND {user}.dbo.tbl_UserAccount.id={user}.dbo.tbl_rfaccount.id';

# game master auth
$config['query_gmlogin']='SELECT ID, PW FROM {user}.dbo.tbl_StaffAccount WHERE ID=? AND PW=?';

# register
$config['query_register']='INSERT INTO {user}.dbo.tbl_rfaccount (id,password,accounttype,birthdate,Email)
VALUES ((CONVERT (binary,?)),(CONVERT (binary,?)),0,0,?)';

$config['query_billregister1']='INSERT INTO BILLING.dbo.tbl_User (UserID,Cash) VALUES (?,0)';
$config['query_billregister2']='INSERT INTO BILLING.dbo.tbl_personal_billing (ID,BillingType,EndDate,RemainTime) 
VALUES ((CONVERT (binary,?)),0,GETDATE(),0)';

# is_logged
$config['query_is_logged']="SELECT CONVERT(varchar, Id) as Id, CONVERT(varchar, Password) as Password FROM {user}.dbo.tbl_rfaccount WHERE Id = CONVERT(binary,?) AND Password = CONVERT(binary,?)";

# account info
$config['query_account_q1']='SELECT 
CONVERT(varchar, {user}.dbo.tbl_UserAccount.id) as id,
CONVERT(varchar, {user}.dbo.tbl_rfaccount.password) as password,
{user}.dbo.tbl_rfaccount.Email,
createtime,
createip,
lastconnectip,
lastlogintime,
lastlogofftime,
serial,
CONVERT(varchar, {user}.dbo.tbl_UserAccount.uilock_pw) as uilock_pw,
CONVERT(varchar, {world}.dbo.tbl_AccountTrunk.TrunkPass) as TrunkPass,
CONVERT(varchar, {world}.dbo.tbl_AccountTrunk.HintAnswer) as HintAnswer

FROM {user}.dbo.tbl_UserAccount,{user}.dbo.tbl_rfaccount,{world}.dbo.tbl_AccountTrunk
WHERE {user}.dbo.tbl_UserAccount.id=CONVERT(binary,?)
AND {user}.dbo.tbl_UserAccount.id={user}.dbo.tbl_rfaccount.id
AND {world}.dbo.tbl_AccountTrunk.AccountSerial={user}.dbo.tbl_UserAccount.serial';

# account info for GM
$config['query_account_q2']='SELECT *
FROM {user}.dbo.tbl_StaffAccount,{world}.dbo.tbl_AccountTrunk
WHERE {user}.dbo.tbl_StaffAccount.ID=?
AND {world}.dbo.tbl_AccountTrunk.AccountSerial={user}.dbo.tbl_StaffAccount.Serial';

$config['query_game_premium']="UPDATE BILLING.dbo.tbl_personal_billing SET BillingType=2, EndDate=DATEADD(month, ?, GETDATE()) WHERE ID='?'";