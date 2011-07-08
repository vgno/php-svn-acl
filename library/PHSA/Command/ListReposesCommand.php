<?php
namespace PHSA\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command used to list the repositories in the svnParentDir directory specified in the
 * configuration
 */
class ListReposesCommand extends BaseCommand {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('reposes');
        $this->setDescription('List available repositories');
        $this->setHelp('Display all available Subversion repositories');
    }

    /**
     * Execute the commend
     *
     * @see \Symfony\Components\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $repositories = array();

        // Fetch driver
        $driver = $this->configuration['subversion']['driver'];
        $svnParentDir = $this->configuration['svnParentDir'];

        $iterator = new \DirectoryIterator($svnParentDir);

        foreach ($iterator as $entry) {
            if ($entry->isDot()) {
                continue;
            }

            if ($entry->isDir() && $driver->validRepository($entry->getPathname())) {
                $repositories[] = (string) $entry;
            }
        }

        sort($repositories);

        if (!count($repositories)) {
            $output->writeln('No available repositories at the specified path');
        } else {
            $output->writeln('Available repositories:');
            $output->writeln(str_repeat('-', 80));
            $output->write($repositories, true);
        }
    }
}
