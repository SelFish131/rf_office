<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>{title} - RF Office CI 1.0</title>

<!-- CSS -->
<link href="{tpl}/style/css/transdmin.css" rel="stylesheet" type="text/css" media="screen" />
<!--[if IE 6]><link rel="stylesheet" type="text/css" media="screen" href="{tpl}/style/css/ie6.css" /><![endif]-->
<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="{tpl}/style/css/ie7.css" /><![endif]-->
<script src="{site}javascript/prototype.js" type="text/javascript"></script>
<script src="{site}javascript/effects.js" type="text/javascript"></script>
<script src="{site}javascript/dragdrop.js" type="text/javascript"></script>
<script src="{site}javascript/controls.js" type="text/javascript"></script>
</head>

<body>
	<div id="wrapper">
    	<h1><a href="#"><span>RF Office CI 1.0</span></a></h1>
        <ul id="mainNav">
        	<li><a href="{site}" class="active">Главная</a></li> <!-- Use the "active" class for the active menu item  -->
        	<li><a href="{site}index.php/stat">TOP PVP</a></li>
        	<li><a href="{site}index.php/stat/liders">Лидеры рас</a></li>
        	<li class="logout">{username}{lang}</li>
        </ul>
        <!-- // #end mainNav -->
        
        <div id="containerHolder">
			<div id="container">
        		<div id="sidebar">
                	<ul class="sideNav">
						{menu}                 		       		
                    </ul>
                    <!-- // .sideNav -->
                </div>    
                <!-- // #sidebar -->
                
                <!-- h2 stays for breadcrumbs -->
                <div id="main">
                	{content}
                </div>
                <!-- // #main -->
                
                <div class="clear"></div>
            </div>
            <!-- // #container -->
        </div>	
        <!-- // #containerHolder -->
        
        <p id="footer">Затрачено времени {elapsed_time} секунд, использовано памяти {memory_usage}<br>
        <div align=center>{copyright}</div></p>
    </div>
    <!-- // #wrapper -->
</body>
</html>
