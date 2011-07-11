<?php
namespace PHSA\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command used to list the repositories in the svnParentDir directory specified in the
 * configuration
 */
class ListReposes extends BaseCommand {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('reposes');
        $this->setDescription('List available repositories');
        $this->setHelp('Display all available Subversion repositories');
    }

    /**
     * Execute the command
     *
     * @see \Symfony\Components\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $repositories = $this->configuration['subversion']['driver']->listRepositories($this->configuration['svnParentDir']);

        if (!count($repositories)) {
            $output->writeln('No available repositories at the specified path');
        } else {
            $output->writeln('Available repositories:');
            $output->writeln(str_repeat('-', 80));
            $output->write($repositories, true);
        }
    }
}
