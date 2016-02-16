<?php
namespace Mantainance\Driver;

class Check {
	public static function check_is_file($path){
		if(file_exists($path))
        {            
            if(!is_file($path)){                
                //$output->writeln("<error>Conf text</error>");use log
                return false;                
            }
            return true;                                  
        }
        else {
            //$output->writeln("<error>File not exists</error>"); use log
            return false;       
        }
	}
	public static function check_rewrite(){
		if(strripos(shell_exec('apache2ctl -M'), 'rewrite_module'))
			return true;
		//return in log
		//$output->writeln("<error> You haven't 'mod_rewrite'. Without this module maintenance does not work. Please install this module before start install. Use command 'a2enmod rewrite' for install module then restart server </error>");
		return false;
	}
}