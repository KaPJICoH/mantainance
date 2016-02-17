<?php
namespace Mantainance\Driver;

class Check {    
	public static function check_is_file($path, $log){
		if(file_exists($path))
        {            
            if(!is_file($path)){                
                file_put_contents($log, date('Y-m-d  G:i:s')." This path '".$path."' not leading to the file\n", FILE_APPEND);
                return false;                
            }
            return true;                                  
        }
        else {
            file_put_contents($log, date('Y-m-d  G:i:s')." File '".$path."' not exists\n", FILE_APPEND);
            //$output->writeln("<error>File not exists</error>"); use log
            return false;       
        }
	}

	public static function check_rewrite($log){
		if(strripos(shell_exec('apache2ctl -M'), 'rewrite_module'))
			return true;
		//return in log
        file_put_contents($log, date('Y-m-d  G:i:s')." You haven't 'mod_rewrite'. Without this module maintenance does not work. Please install this module before start install. Use command 'a2enmod rewrite' for install module then restart server\n", FILE_APPEND);
		//$output->writeln("<error> You haven't 'mod_rewrite'. Without this module maintenance does not work. Please install this module before start install. Use command 'a2enmod rewrite' for install module then restart server </error>");
		return false;
	}

    public static function check_name_dir($path, $name){
        $files = scandir($path);         
        foreach ($files as $file) {
            if(is_dir($path.$file) && $file==$name)
                return false;
        }
        return true;
    }
    public static function check_permision(){

    }
}