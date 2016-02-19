<?php

namespace Mantainance\Driver\Apache;

use Mantainance\Driver\Shell as Shell;
use Mantainance\Driver\DriverInterface;

class Driver implements DriverInterface {
    public function applySettings($pathToConfig, $pathToMantainancePage, $name){
        $home = getenv('HOME');
        $dir = $home."/maintenance/";
        $config_dir=$dir.$name;

        $file = file($pathToConfig);               
        $filename=basename($pathToMantainancePage);
        $dirname =dirname($pathToMantainancePage);
        $new_file=[];  

        //add config file
        if(!preg_grep('/\/maintenance\.apache\.conf/', $file)){        
            if(Shell::command("sudo touch", $dir."maintenance") && Shell::command("sudo chmod -R 777 * ".$dir)){            
                $conf=[ "Alias /errors ".$dirname."\n",
                		"<If \"-f '".$config_dir."/maintenance.enable'\">\n",
                		"	<IfModule mod_rewrite.c>\n",
                        "   	RewriteEngine On\n",                  		
                        "   	RewriteCond %{SCRIPT_FILENAME} !".$filename."\n",
                        "   	RewriteRule ^.*$ /".$filename." [R=503,L]\n",
                        "   	ErrorDocument 503 /errors/".$filename."\n",
                        "	</IfModule>\n",
                        "</If>\n"];

                //add include
                if(file_put_contents($dir."maintenance" , $conf) && Shell::command("sudo mkdir", $config_dir) && Shell::command("sudo mv ".$dir."maintenance ".$config_dir."/maintenance.apache.conf -f") && Shell::command("sudo chmod -R 777 * ".$dir)){
                    $config_name=basename("pathToConfig");

                    if(@$virtual = preg_grep( '/<virtualhost/i', $file)[0] && preg_match('/virtualhost/i', $virtual)>=0){         
                        $key_virtual = array_keys(preg_grep( '/<virtualhost/i', $file))[0];
                        foreach ($file as $key => $line) {      
                            array_push( $new_file, $line);
                            if ($key == $key_virtual) {
                                echo $key_virtual;
                                array_push($new_file,   "   Include ".$config_dir."/maintenance.apache.conf\n" );
                            }
                        }
                    }
                    else
                    {
                        file_put_contents($dir."maintenance.log", date('Y-m-d  G:i:s')." Your config dont have virtualhost \n", FILE_APPEND);
                        return false;
                    }
                        

                    if( Shell::command("sudo touch", $dir.$config_name) && Shell::command("sudo chmod -R 777 * ".$dir) && file_put_contents($dir.$config_name, $new_file) && Shell::command("sudo mv ".$dir.$config_name." ".$pathToConfig." -f"))                   
                        return Shell::command("sudo service apache2 restart");                                             
                }
                return false; 
            }           
        }else {
            file_put_contents($dir."maintenance.log", date('Y-m-d  G:i:s')." This file alredy have include maintenance\n", FILE_APPEND);
            return false; 
        }       
    }
}

        
          
           

