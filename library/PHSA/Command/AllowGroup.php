<?php
namespace PHSA\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Command used to list the ACLs stored in the database
 */
class AllowGroup extends BaseCommand {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('allow-group');
        $this->setDescription('Allow a group');
        $this->setHelp('Allow a group access to one or more repositories');

        $this->addArgument('group', InputArgument::REQUIRED, 'Name of group to allow');
        $this->addArgument('path', InputArgument::IS_ARRAY, 'Optional paths to allow the group access to in the specified repositories');

        $this->addOption('repos', null, InputOption::VALUE_OPTIONAL, 'Comma separated list of repositories to allow the group access to');
        $this->addOption('all', null, InputOption::VALUE_NONE, 'Use this option to allow access to all currently available repositories');
    }

    /**
     * Execute the command
     *
     * @see \Symfony\Components\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $dbDriver = $this->configuration['database']['driver'];

        $group = $input->getArgument('group');
        $paths = $input->getArgument('path');

        $repositories = array();

        if ($input->getOption('all')) {
            // Fetch list of repositories from the subversion driver
            $svnDriver = $this->configuration['subversion']['driver'];
            $repositories = $svnDriver->listRepositories($this->configuration['svnParentDir']);
        } else if ($input->getOption('repos')) {
            $tmp = $input->getOption('repos');
            $repositories = array_map('trim', explode(',', $tmp));
        } else {
            throw new \InvalidArgumentException('Specify either --all or --repos');
        }

        $rules = 0;

        foreach ($repositories as $repos) {
            if (empty($paths)) {
                $rules++;
                $dbDriver->allowGroup($group, $repos);
            } else {
                // Paths have been specified. Add all paths to all repositories
                foreach ($paths as $path) {
                    $rules++;
                    $dbDriver->allowGroup($group, $repos, $path);
                }
            }
        }

        $output->writeln($rules . ' rule' . ($rules !== 1 ? 's' : '') . ' have been added to the database');
    }
}
