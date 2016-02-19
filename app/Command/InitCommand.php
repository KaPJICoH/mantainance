<?php

namespace Mantainance\Command;

use Mantainance\Driver\Apache as Apache;
use Mantainance\Driver\Nginx as Nginx;
use Mantainance\Driver\Check as Check;
use Mantainance\Driver\Shell as Shell;
use Mantainance\Driver\Backup as Backup;
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
        $log= $dir."maintenance.log";

        $output->writeln("<info> Create directory '".$dir."' and file '".$log."'</info>");
        if(Shell::command("sudo mkdir", $home."/maintenance") && Shell::command("sudo touch", $home ."/maintenance/maintenance.log") && Shell::command("sudo chmod -R 777 * ".$dir) ){
        
            $name = $input->getArgument('config-name');
            $server=$input->getArgument('server-type');
            $path_config=$input->getArgument('config-path');
            $path_page=$input->getArgument('mantainance-page-path');

            $backup_config =file($path_config);     
            
            if(Check::check_name_dir($dir, $name)){            
                $output->writeln("<info> Your name for config: '$name'</info>");
                $server = strtolower($server);            
                switch ($server) {

                    //nginx
                    case 'nginx':
                        $output->writeln( '<info> You choose nginx </info>');
                        $nginx =new Nginx\Driver();
                        $output->writeln('<info> Validation your path</info>');
                        if(Check::check_is_file($path_config, $log) && Check::check_is_file($path_page, $log)){
                           $output->writeln('<info> Creating config file and connection him in your config file</info>');                           
                            if ($nginx->applySettings($path_config, $path_page, $name))
                            {
                                $output->writeln('<info> Finish install now you can use maintenance for this site</info>');
                            }
                            else {
                                Shell::command("sudo rm -rf ".$dir.$name);
                                Backup::return_config($backup_config, $path_config);                               
                                $output->writeln("<error>Sorry but something wrong in your config for more datail look in logs: ".$log."</error>");
                            }   
                        }
                        else
                            $output->writeln("<error> You did not passed validation. Please check the entered data. Details can see in the logs: ".$log."</error>");
                        break;

                    //apache    
                    case 'apache':
                        $output->writeln( "<info> You choose apache </info>");
                        $apache =new Apache\Driver();
                        $output->writeln('<info> Validation your path and check your mod_rewrite </info>');
                        if(Check::check_rewrite($log) && Check::check_is_file($path_config, $log) && Check::check_is_file($path_page, $log)){                            
                            $output->writeln('<info> Creating config file and connection him in your config file</info>');
                            if ($apache->applySettings($path_config, $path_page, $name))
                            {
                                $output->writeln('<info> Finish install now you can use maintenance for this site</info>');
                            }
                            else {
                                Shell::command("sudo rm -rf ".$dir.$name);
                                Backup::return_config($backup_config, $path_config);
                                $output->writeln("<error>Sorry but something wrong in your config for more datail look in logs: ".$log."</error>");
                            }
                                   
                        }
                        else
                            $output->writeln("<error> You did not passed validation. Please check the entered data and check whether you have rewrite module. Details can see in the logs: ".$log."</error>");
                                
                        break;


                    default:
                        $output->writeln("<error> You choose '$server' we dont work with it. Please chose Apache or Nginx</error>");               
                        break;
                } 
            }
            else{
                $output->writeln("<error> You name: '$name' already exists</error>"); 
            }
        }
        else
            $output->writeln("<error> Something wrong. For this command need sudo permision please check if you have</error>");   
        return;
    }   
}