<?php

namespace Mantainance\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument as InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as OutputInterface;

class ListCommand extends Command {

    protected function configure()
    {
        $this->setName('list')
            ->setDescription('List of names')
            ->setHelp('Help here');
            
        return;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {    
        $home = getenv('HOME');    
        $dir = $home."/maintenance/";
        $list_name=[]; 
        if($files = array_diff(scandir($dir), array('..', '.'))){
            foreach ($files as $file) {
                if(is_dir($dir.$file))
                    array_push($list_name, $file);
            }
            if($list_name){
                $output->writeln('<info> List name:</info>');
                foreach ($list_name as $key => $name) {
                    $key=$key+1;
                    $output->writeln("<info> ".$key.". ".$name."</info>");
                }
            }
            else
                $output->writeln("<infoYou yet don't create any name</info>");            
        }
        else
            $output->writeln("<infoYou yet don't create any name</info>");
        
            
        
        return;
    }
}