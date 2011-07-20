<?php
namespace PHSA\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command used to test rules
 */
class Test extends BaseCommand {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('test');
        $this->setDescription('Test the rules');
        $this->setHelp('Test the defined rules by simulating a commit by a user to some file in a repository');

        $this->addArgument('user', InputArgument::REQUIRED, 'User who performs the commit');
        $this->addArgument('repos', InputArgument::REQUIRED, 'Repository to commit to');
        $this->addArgument('path', InputArgument::OPTIONAL, 'Optional path to commit to. If ommited the command assumes a commit to the root level of the repository');
    }

    /**
     * Execute the command
     *
     * @see \Symfony\Components\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

    }
}
