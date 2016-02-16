<?php

namespace Mantainance\Command;

use Mantainance\Driver\Check as Check;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument as InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as OutputInterface;

class UpCommand extends Command {

    protected function configure()
    {
        $this->setName('up')
            ->setDescription('Moves your app in to the mantainance mode')
            ->setHelp('Help here')
            ->addArgument(
                'config-name', 
                InputArgument::REQUIRED, 
                'Name your config'
            );

        return;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $home = getenv('HOME');
        $dir = $home."/maintenance/"; 
        $name = $input->getArgument('config-name');
        
        if(!Check::check_name_dir($dir, $name)){
            if(!file_exists($dir.$name."/maintenance.enable")){
                shell_exec("sudo touch ".$dir.$name."/maintenance.enable");
                $output->writeln('<info>This site working in maintenance</info>');
            }                
            else
                $output->writeln('<comment>This site already working in maintenance!</comment>');
        }   
        else{
            $output->writeln('<error>This name does not exists.</error>');
        } 

        return;
    }
}