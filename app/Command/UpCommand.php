<?php

namespace Mantainance\Command;

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
            ->setHelp('Help here');

        return;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('It Worked!'); 

        return;
    }
}