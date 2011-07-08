<?php
namespace PHSA\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Command used to list the repositories in the svnParentDir directory specified in the
 * configuration
 */
class ListReposesCommand extends Command {
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
        $configFile = $input->getOption('config-file');

        if (empty($configFile) || !is_file($configFile)) {
            throw new \InvalidArgumentException('Missing or invalid path to configuration file');
        }

        // Fetch configuration array
        $config = require $configFile;
        $repositories = array();

        // Fetch driver
        $driver = $config['subversion']['driver'];
        $svnParentDir = $config['svnParentDir'];

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
