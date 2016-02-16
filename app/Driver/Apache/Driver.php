<?php

namespace Mantainance\Driver\Apache;

use Mantainance\Driver\DriverInterface;

class Driver implements DriverInterface {
    public function applySettings($pathToConfig, $pathToMantainancePage, $name){
        $file = file($pathToConfig);
        $dir = $home."/maintenance/";         
        $new_file=[];        
        $filename=basename($pathToMantainancePage);
        $dirname =dirname($pathToMantainancePage);

        //add config file        
        $home = getenv('HOME'); 
        $conf=[ "Alias /errors ".$dirname."\n",
        		"<If \"-f '".$home."/maintenance/".$name."/maintenance.enable'\">\n",
        		"	<IfModule mod_rewrite.c>\n",
                "   	RewriteEngine On\n",                  		
                "   	RewriteCond %{SCRIPT_FILENAME} !".$filename."\n",
                "   	RewriteRule ^.*$ /".$filename." [R=503,L]\n",
                "   	ErrorDocument 503 /errors/".$filename."\n",
                "	</IfModule>\n",
                "</If>\n"];
        shell_exec("sudo mkdir ".$dir.$name);
        file_put_contents("maintenance.conf" , $conf);
        shell_exec("sudo mv maintenance.conf ".$home."/maintenance/".$name."/maintenance.apache.conf -f");
        
        //add include
        if(!preg_grep('/maintenance\/maintenance\.apache\.conf/', $file)){        	
        	$key_virtual = array_keys(preg_grep( '/<virtualhost/i', $file))[0];
        	foreach ($file as $key => $line) {   	
        		array_push( $new_file, $line);
        		if ($key == $key_virtual) {
        			array_push($new_file, 	"	Include ".$home."/maintenance/".$name."/maintenance.apache.conf\n" );
        		}
        	}
        	$config_name=basename("pathToConfig");
        	file_put_contents($config_name, $new_file);
        	shell_exec("sudo mv ".$config_name." ".$pathToConfig." -f");       	
        }
	
        
        
        shell_exec("sudo service apache2 restart");
    }
}

        
          
           

