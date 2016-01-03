<?
if(is_logged())
{
    $login='<strong class="greeting">{username}</strong> {lang}';
} else {
$login='<strong class="greeting">Привет</strong>&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="#" onclick="Effect.toggle(\'loginFormMini\', \'appear\'); return false;">Вход</a>&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="{site}'.index_page().'/register/license">Регистрация</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			{lang}
			<div id="loginFormMini" style="display:none;">
				<form method="post" action="{site}'.index_page().'/login/">
					<div style="margin-top:-4px;"><a href="#" onclick="Effect.toggle(\'loginFormMini\', \'appear\'); return false;">X</a></div>
					<table cellpadding="0" cellspacing="6" border="0" align="center">
						<tr>
							<td align="right">ID</td>
							<td><input type="text" name="login" class="input" id="username" size="15" value="" /></td>
						</tr>
						<tr>
							<td align="right">PW</td>
							<td><input type="password" name="password" class="input" size="15" value="" /></td>
						</tr>
                        <tr>
							<td align="right">FGPW</td>
							<td><input type="password" name="fgpwd" class="input" size="15" value="" /></td>
						</tr>
						<tr>
							<td colspan="2" align="right"><input type="submit" class="input" size="15" value="login" /></td>
						</tr>
					</table>
				</form>
			</div>';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>{title} - RF Office CI 0.11</title>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" >
	<meta http-equiv="Content-Language" content="ru">
	<link rel="shortcut icon" href="{tpl}/img/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="{tpl}/css/style.css" type="text/css" media="screen, projection" charset="utf-8" />
	<script src="{site}javascript/prototype.js" type="text/javascript"></script>
    <script src="{site}javascript/effects.js" type="text/javascript"></script>
    <script src="{site}javascript/dragdrop.js" type="text/javascript"></script>
    <script src="{site}javascript/controls.js" type="text/javascript"></script>
    <script src="{site}project/rf_office/views/tiny_mce/tiny_mce.js" type="text/javascript">
<script type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "simple"
});
</script>
<style>
.office_row_start{background-color:#FFFFFF}
.office_row_alt_start{background-color:#ECF3F7}
.office_table_open{clear:both;}
.office_table_open TD:hover {BACKGROUND: #DDDDDD; COLOR: black;}
.office_table_open TR:hover {BACKGROUND: #EEEEEE; COLOR: black;font-weight: bold}

input{width:150px; height:20px;}
hr {border-color:#D9E6EE; height:1px;}
</style>
</head>
<body>

	<div id="masthead"><a href="http://fdcore.ru/"><img src="{tpl}/img/fdcore.gif" title="Created by FDCore studio" width="293" height="23" alt="FDCore" /></a></div>
	
	<div id="header">
		<div id="login">
            <?=$login?>
		</div>
		<h1><a href="{site}" title="Privae office"><img src="{tpl}/img/logo.gif"  width="241" height="88" alt="Privae office" /></a></h1>
	</div>
	
	<div id="topNav">
		<ul>
			<li class="current"><a href="{site}">Home</a></li>
			<li ><a href="#">Top 50</a>
			<ul>
				<li><a href="{site}index.php/stat">top pvp</a></li>
				<li><a href="{site}index.php/stat/liders">Лидеры рас</a></li>
			</ul>
			</li>
			<li ><a href="#">Статистика</a>
			<ul>
				<li><a href="{site}index.php/stat/hours">Онлайн по часам</a></li>
				<li><a href="{site}index.php/stat/online">Текущий онлайн</a></li>
			</ul>
			</li>			
			<li><a href="{site}index.php/main_index/page/fgunban">Снять бан FG</a></li>
			<li><a href="{site}index.php/stat/banlist">бан лист</a></li>
		</ul>
		<div class="clear"></div>
	</div>
	
	<div id="wrapper">
		<div id="shell">
			<div id="gooey">
				<div id="content">
					<ul id="subNav">
						{menu}
					</ul>
						
					<div id="contentData">
						{content}
					</div>
					
					<div class="clear"></div>	
				</div>
			</div>
		</div>
	</div>
	
	<div id="copyright">
    <div class="line">Затрачено времени {elapsed_time} секунд, использовано памяти {memory_usage}</div>
		<div class="line">{copyright}</div>
	</div>
</body>
</html>