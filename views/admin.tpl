<html><head>
<title>{title}</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" >
<meta http-equiv="Content-Language" content="ru">
<script src="<?=base_url()?>javascript/prototype.js" type="text/javascript"></script>
<script src="<?=base_url()?>javascript/effects.js" type="text/javascript"></script>
<script src="<?=base_url()?>javascript/dragdrop.js" type="text/javascript"></script>
<script src="<?=base_url()?>javascript/controls.js" type="text/javascript"></script>
<style type="text/css">
<!--
BODY {scrollbar-face-color: #9A9A9A;scrollbar-shadow-color: #CDCDCD;scrollbar-highlight-color: #CDCDCD;scrollbar-3dlight-color: #CDCDCD;scrollbar-darkshadow-color: #CDCDCD;scrollbar-track-color: #CDCDCD;scrollbar-arrow-color: #CDCDCD;color:#333333;}
a {margin:0px;padding:0px;color:#333333;text-decoration:none;}
a:hover {color:#999999;border-bottom:1px dashed;}
li {background-color:#DDD;display:block;text-decoration:none;border: 1px solid #9E9E9E;color: #666666;font-family: Verdana, Tahoma, helvetica, sans-serif;text-align:center;vertical-align:baseline;font-size: 15px;height: 30px;width:200px;}
li:hover {background-color:#FFFFFF;display:block;text-decoration:none;border: 1px solid #9E9E9E;color: #666666;font-family: Verdana, Tahoma, helvetica, sans-serif;text-align:center;vertical-align:middle;font-size: 15px;	height: 30px;width:200px;}
H1 {font-size: 20px;color: #999999;font-weight: bold;}
H2 {font-size: 17px}
H3 {color: #666666;font-weight: bold;}
code {border: 1px dashed; border-color:gray; display:block; background-color:#F1F1F1;}
code:hover {border: 1px solid; border-color:gray; display:block; background-color:#B1B1B1;}
abbr {border: 1px dashed; border-color:gray; display:block; background-color:#FBFBFB;}
abbr:hover {border: 1px solid; border-color:#BBBBBB; font-weight: bold; display:block; background-color:#F1F1F1;}
hr {border: 1px dashed; border-color:#F1F1F1;}
hr:hover {border: 1px solid; border-color:#B1B1B1;}
.tables TD { FONT-SIZE: 8pt;  FONT-FAMILY: verdana;}
.tables TR {BACKGROUND: #F8F8F8; COLOR: #676767; FONT-FAMILY: verdana;}
.tables TD.header { FONT-WEIGHT: normal; FONT-SIZE: 10pt; BACKGROUND: #EEEEEE; COLOR: white; FONT-FAMILY: verdana;}
.tables TD:hover {BACKGROUND: #DDDDDD; COLOR: white; FONT-FAMILY: verdana; font-weight: bold}
.tables TR:hover {BACKGROUND: #EEEEEE; COLOR: black; FONT-FAMILY: verdana;}
-->
</style></head><body><div align=center>
<table style="width: 100%; height:100%">
	<tr>
		<td colspan="2">
		<table style="width: 100%; height:100%">
			<tr>
				<td align="left">{left_toggle}</td>
				<td align="center"><div id="info">Information panel</div></td>
				<td align="right">Выход</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td valign="top">
		<table style="height:100%">
			<tr>
				<td valign="top" align="center">{left_menu}</td>
			</tr>
			<tr>
				<td valign="bottom">{bottom_toggle}</td>
			</tr>
		</table>
		</td>
		<td valign="top" align="center">{content}</td>
	</tr>
	<tr>
		<td colspan="2">
		<div id="bottom_toggle" style="display:none;">
		<table style="width: 100%;height:100%">
			<tr>
				<td valign="top" align="center">{bottom_link1}</td>
				<td valign="top" align="center">{bottom_link2}</td>
				<td valign="top" align="center">{bottom_link3}</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<hr>
{copyright}
</div>
</body></html>
