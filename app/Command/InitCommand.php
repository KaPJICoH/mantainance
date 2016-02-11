<?php

namespace Mantainance\Command;

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
        $output->writeln('It Worked!'); 

        return;
    }
}