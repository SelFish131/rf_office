<?php
if ( ! function_exists('directory_map'))
{
	function directory_map($source_dir, $directory_depth = 0, $hidden = FALSE)
	{
		if ($fp = @opendir($source_dir))
		{
			$filedata	= array();
			$new_depth	= $directory_depth - 1;
			$source_dir	= rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;		
						
			while (FALSE !== ($file = readdir($fp)))
			{
				// Remove '.', '..', and hidden files [optional]
				if ( ! trim($file, '.') OR ($hidden == FALSE && $file[0] == '.'))
				{
					continue;
				}
				if ($file == '.DS_Store') continue;
								
				if (($directory_depth < 1 OR $new_depth > 0) && @is_dir($source_dir.$file))
				{
					if(strstr($source_dir.$file,'.svn')) continue;
					$filedata[$file] = directory_map($source_dir.$file.DIRECTORY_SEPARATOR, $new_depth, $hidden);
				}
				else
				{
					$filedata[] = $file;
				}
			}
			
			closedir($fp);
			return $filedata;
		}

		return FALSE;
	}
}

define('EXT', '.php');


$config_folder_path='rf_office/config/';
$logs_folder_path='system/logs/';

		$RAPORT=array();

        ob_start();
        
        phpinfo();
        
        $buffer = ob_get_contents();
        
        ob_end_clean();
        
        $RAPORT['phpinfo']=$buffer;      
        $RAPORT['platform_tree']=directory_map('rf_office');
        $RAPORT['modules_tree']=directory_map('system');
        $config=array();
        $logs=array();
        
        
        $cfg_file=directory_map($config_folder_path);
        
        foreach ($cfg_file as $file){
        	if(file_exists($config_folder_path.$file) && strstr($file,EXT) && $file!=="database".EXT){
        		$config[$file]=file_get_contents($config_folder_path.$file);
        	}
        }
        
        $RAPORT['config_params']=$config;
        
        $log_file=directory_map($logs_folder_path);
        
        foreach ($log_file as $file){
        	if(file_exists($logs_folder_path.$file) && strstr($file,EXT)){
        		$logs[$file]=file_get_contents($logs_folder_path.$file);
        	}
        }
        
        $RAPORT['logs_params']=$logs;
        
               
        $_S_RAPORT=base64_encode(serialize($RAPORT));
        
        $arr = str_split($_S_RAPORT, 100);
        
        file_put_contents('report.s',implode("\r\n",$arr));
        
        echo "File report.s is created.";