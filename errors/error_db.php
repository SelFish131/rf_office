<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta content="text/html; charset=UTF-8" http-equiv=Content-Type>
<style type="text/css" media="screen">
body{
	font-family: Verdana, Tahoma, Arial, Trebuchet MS, Sans-Serif, Georgia, Courier, Times New Roman, Serif;font-size: 11px;
	margin: 0;
	padding: 0; 
}
.errorwrap {
	background: #F2DDDD;
	border: 1px solid #992A2A;
	border-top: 0;
	margin: 5px;
	padding: 0;
}
.errorwrap h4 {
	background: #E3C0C0;
	border: 1px solid #992A2A;
	border-left: 0;
	border-right: 0;
	color: #992A2A;
	font-size: 12px;
	font-weight: bold;
	margin: 0;
	padding: 5px;
}
.errorwrap p {
	background: transparent;
	border: 0;
	color: #992A2A;
	margin: 0;
	padding: 8px;
	font-size: 11px;
}
.space{
	padding: 5%;
}
</style>
	<title>Database Error</title>
	</head>
<body>
<div class="space"></div>
<table border="0" width="600" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td width="100%">
			<div class="errorwrap">
				<h4><?php echo $heading; ?></h4>
				<p><? echo $message; ?></p>
				<p align='right'>RF Office - FDCore Studio</p>
			</div>
		</td>
	</tr>
</table>
</body >
</html >