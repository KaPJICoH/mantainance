<?php
namespace Mantainance\Driver;

class Shell {    
	public static function command($cmd, $path=null){
        if(!$path){
            system($cmd , $retval);
            return $retval? false : true;
        }
        if(!file_exists($path)){
            system($cmd." ".$path , $retval);            
            return $retval? false : true;
        }
        return true;
    }
}