<?php
namespace Mantainance\Driver\Nginx;

use Mantainance\Driver\DriverInterface;
use Mantainance\Driver\Shell as Shell;

class Driver implements DriverInterface {
	public function applySettings($pathToConfig, $pathToMantainancePage, $name){
		$home = getenv('HOME');
        $dir = $home."/maintenance/";
        $config_dir=$dir.$name;

        $file = file($pathToConfig);               
        $filename=basename($pathToMantainancePage);
        $dirname =dirname($pathToMantainancePage);
        $new_file=[];

        //add include
        if( preg_grep('/server( +)|{/', $file) && !preg_grep('/\/maintenance\.nginx\.conf/', $file)){        
            if(Shell::command("sudo touch", $dir."maintenance") && Shell::command("sudo chmod -R 777 * ".$dir)){            
                 $conf=[     "    error_page 503 @maintenance;\n",
                            "    location @maintenance {\n",
                            "        root ".$dirname.";\n",
                            "        rewrite ^(.*)$ /".$filename." break;\n", 
                            "    }\n"];    

                //add include
                if(file_put_contents($dir."maintenance" , $conf) && Shell::command("sudo mkdir", $config_dir) && Shell::command("sudo mv ".$dir."maintenance ".$config_dir."/maintenance.nginx.conf -f") && Shell::command("sudo chmod -R 777 * ".$dir)){
                    $config_name=basename("pathToConfig");
                    
                    if(@$key_location = array_keys(preg_grep( "/location(\s+)\/(\s+)/i", $file))[0]){                        
                        foreach ($file as $key => $line) {
                            if ($key == $key_location) {
                                array_push($new_file,   "    include ".$home."/maintenance/".$name."/maintenance.nginx.conf;\n" );
                            }
                            array_push( $new_file, $line);
                            if ($key == $key_location) {
                                array_push($new_file,   "        if (-f ".$home."/maintenance/".$name."/maintenance.enable) {\n",
                                                        "           return 503;\n",
                                                        "        }\n" );
                            }
                        }                        
                    }else{
                        file_put_contents($dir."maintenance.log", date('Y-m-d  G:i:s')." Your config dont have location \ {} \n", FILE_APPEND);
                        return false;
                    }
                    if( Shell::command("sudo touch", $dir.$config_name) && Shell::command("sudo chmod -R 777 * ".$dir) && file_put_contents($dir.$config_name, $new_file) && Shell::command("sudo mv ".$dir.$config_name." ".$pathToConfig." -f"))                   
                        return true; //Shell::command("sudo service nginx restart");                                             
                }
                return false; 
            }           
        }else {
            file_put_contents($dir."maintenance.log", date('Y-m-d  G:i:s')." Look in your config file and check your server blog or look mayby your alredy have maintenance\n", FILE_APPEND);
            return false; 
        } 
    }
}