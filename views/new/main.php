<html>
<head>
<meta content="en-us" http-equiv="Content-Language">
<meta content="text/html; charset=windows-1251" http-equiv="Content-Type" />
<title>{title} - RF Office CI 0.9</title>
<style type="text/css">
body,html{
	margin: 0; 
	background-image: url('{tpl}/toplight.gif');
	background-repeat:repeat-x;
	height:100%;
}
html {background: #ffffff; font-family: Arial, Helvetica, sans-serif;}
.style1 {
	text-align: right;
}
#topl{
	background-image:url('{tpl}/windows_topl.png');
	background-repeat:no-repeat;
	background-position:left;
	height:92px;
	width:61px;
}
#topc{
	background-image:url('{tpl}/windows_topc.png');
	height:92px;
}
#topr{
	background-image:url('{tpl}/windows_topr.png');
	background-repeat:no-repeat;
	background-position:left;
	height:92px;
	width:58px;
}
#footerl{
	background-image:url('{tpl}/footerl.png');
	background-repeat:no-repeat;
	height:76px;
	width:36px;
}
#footerc{
	background-image:url('{tpl}/footerc.png');
	background-repeat:repeat-x;
	height:76px;
	width:100%;
}
#footerr{
	background-image:url('{tpl}/footerr.png');
	background-repeat:no-repeat;
	height:76px;
	width:41px;
}
.style2 {
	text-align: center;
}
#light{
background-image:url('{tpl}/light.png');
background-repeat:repeat-x;
background-position:top;	
padding: 2em 2em 4em 2em;
font-size:12px;
}
a {color: #636363;text-decoration:none;}
a:hover {
	color: #808080;
	text-decoration:none;
	text-shadow: 1px 1px 3px #fff;
	border-bottom-color: gray;
	border-bottom-style: solid;
	border-bottom-width: 3px;
}
table {border-collapse: collapse; border: none;}
table td {padding: 0;}
img {border: none;}
#menu {
	text-align:center;
	text-shadow: 1px 1px 3px #fff;
	vertical-align:middle;
}
#menu a{
	display: block;
	text-decoration: none;
	color: gray;
	height: 92px;
	width: 20%;
	vertical-align: text-top;
	font-size: 20px;
	float:left;
	border-bottom-width: 0px;
}
#menu a:hover{
	background-image:url('{tpl}/menuactive.png');
}
#topmenu ul li{
	float: left;
	list-style: none;
	padding-left:5px;
}
.office_row_start{background-color:#DBDBDB}
.office_row_alt_start{background-color:#A7A7A7}
</style>
<script src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load("prototype", "1.6.0.3");
google.load("scriptaculous", "1.8.2");

function bildup()
{
  Effect.BlindDown('content',{duration:1.0});
}
</script>
</head>

<body onload="bildup()">

<table style="width: 100%; height:100%">
	<tr>
		<td class="style1" valign="top" height="118" >
		<table style="width: 100%">
			<tr>
				<td width="118" height="118"><a href="{site}">
				<img alt="FDCore Studio" height="118" src="{tpl}/logo.png" width="118"></a></td>
				<td style="width: 520px">&nbsp;</td>
				<td valign="top" id="light" class="style1"><div id="topmenu"><ul>{menu}</ul></div></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td align="center">
		<table cellpadding="0" cellspacing="0" style="width: 70%">
			<tr>
				<td>
				<table style="width: 100%;height:92px;">
					<tr>
						<td>
						<img alt="" height="92" src="{tpl}/windows_topl.png" width="61"></td>
						<td style="background-image:url('{tpl}/windows_topc.png'); width:100%">
						<div id="menu">
						<a href="{site}">�������</a>
						<a href="{site}index.php/stat">��� PvP</a>
						<a href="{site}index.php/stat/liders">������ ���</a>
						<a href="{site}index.php/stat/hours">������ �� �����</a>
						<a href="{site}/stat/banlist">��� ����</a>
						</div></td>
						<td>
						<img alt="" height="92" src="{tpl}/windows_topr.png" width="58"></td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td style="background-color:#e9e9e9; padding:25px;">
				<div id="content" style="display:none;"><div>{content}</div></div></td>
			</tr>
			<tr>
				<td>
				<table style="width: 100%">
					<tr>
						<td>
						<img alt="" height="76" src="{tpl}/footerl.png" width="36"></td>
						<td id="footerc" class="style2">{copyright} {lang}</td>
						<td>
						<img alt="" height="76" src="{tpl}/footerr.png" width="41"></td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="background-image:url('{tpl}/footerlight.gif'); height:243px;" class="style2"><img src="http://78.24.221.238/FUI.png"></td>
	</tr>
</table>
</body>
</html>
