<?php
namespace AppBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Created by PhpStorm.
 * User: porad
 * Date: 15.12.2016
 * Time: 13:06
 */

class SendMessageCommand extends ContainerAwareCommand
{
    protected function configure()
    {
       $this->setName("app:send-message");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get("doctrine.orm.default_entity_manager");

    }
}