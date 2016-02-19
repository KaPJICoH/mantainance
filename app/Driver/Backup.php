<?php
namespace Mantainance\Driver;

use Mantainance\Driver\Shell as Shell;

class Backup {    
	public static function return_config($file, $path){
        $home = getenv('HOME');
        $dir = $home."/maintenance/";
        if( Shell::command("sudo touch", $dir."backup") && Shell::command("sudo chmod -R 777 * ".$dir) &&
            file_put_contents($dir."backup", $file) && Shell::command("sudo mv ".$dir."backup ".$path." -f") ){
            return true;
        }
        return false;                
    }
}