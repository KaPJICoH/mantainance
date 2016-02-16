<?php

namespace Mantainance\Command;

use Mantainance\Driver\Apache as Apache;
use Mantainance\Driver\Nginx as Nginx;
use Mantainance\Driver\Check as Check; 
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument as InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as OutputInterface;



class InitCommand extends Command {

    protected function configure()
    {
        $this->setName('init')
            ->setDescription('Init command initialize settings for mantainance for your app')
            ->addArgument(
                'config-name', 
                InputArgument::REQUIRED, 
                'Name for you config'
            )
            ->addArgument(
                'server-type', 
                InputArgument::REQUIRED, 
                'apache or nginx'
            )
            ->addArgument(
                'config-path',                 
                InputArgument::REQUIRED, 
                'Define path of server configuration file for your app'
            )
            ->addArgument(
                'mantainance-page-path', 
                InputArgument::REQUIRED, 
                'Path to the directory with mantainance page'
            )
            ->setHelp('Help here');

        return;
    }   
    protected function execute(InputInterface $input, OutputInterface $output)
    {   
        $home = getenv('HOME');
        $dir = $home."/maintenance/"; 
        if(!file_exists($home.'/maintenance'))
            shell_exec("sudo mkdir ~/maintenance");
        
        $name = $input->getArgument('config-name');
        $server=$input->getArgument('server-type');
        $path_config=$input->getArgument('config-path');
        $path_page=$input->getArgument('mantainance-page-path');     
        
        if(Check::check_name_dir($dir, $name)){
            shell_exec("sudo mkdir ".$dir.$name);
            $output->writeln("You name: '$name'");
            die();
            $server = strtolower($server);            
            switch ($server) {

                //nginx
                case 'nginx':
                    $output->writeln( 'You choose nginx');
                    $nginx =new Nginx\Driver();

                    if(Check::check_is_file($path_config) && Check::check_is_file($path_page)){
                        $output->writeln('You work with nginx');            
                        $nginx->applySettings($path_config, $path_page, $name);   
                    }  
                    break;

                //apache    
                case 'apache':
                    $output->writeln( "You choose apache");
                    $apache =new Apache\Driver();
                    if(Check::check_rewrite() && Check::check_is_file($path_config) && Check::check_is_file($path_page)){
                        $output->writeln('You work with apache');
                        $apache->applySettings($path_config, $path_page, $name);   
                    }
                    break;


                default:
                    $output->writeln("<error>You choose '$server' we dont work with it. Please chose Apache or Nginx</error>");               
                    break;
            } 
        }
        else{
            $output->writeln("<error>You name: '$name' already exists</error>"); 
        }     

        return;
    }   
}