<?php
namespace Mantainance\Driver\Nginx;

use Mantainance\Driver\DriverInterface;

class Driver implements DriverInterface {
	public function applySettings($pathToConfig, $pathToMantainancePage, $name){
		$file = file($pathToConfig);
        $dir = $home."/maintenance/";
        $new_file=[]; 
        $home = getenv('HOME');
        $filename=basename($pathToMantainancePage);
        $dirname =dirname($pathToMantainancePage);
        //add include
        if(!preg_grep('/maintenance.enable/', $file)){ 

            $key_location = array_keys(preg_grep( "/location(\s+)\/(\s+)/i", $file))[0];
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
            
            $config_name=basename("pathToConfig");
            file_put_contents($config_name, $new_file);
            shell_exec("sudo mv ".$config_name." ".$pathToConfig." -f");      
        }        
        
        //add config file 
        $conf=[	
        	"    error_page 503 @maintenance;\n",
            "    location @maintenance {\n",
            "        root ".$dirname.";\n",
            "        rewrite ^(.*)$ /".$filename." break;\n", 
            "    }\n"];
        shell_exec("sudo mkdir ".$dir.$name);
        file_put_contents("maintenance.conf" , $conf);
        shell_exec("sudo mv maintenance.conf ".$home."/maintenance/".$name."/maintenance.nginx.conf -f");
        shell_exec("sudo service nginx restart");
    }
}