<?php
namespace PHSA\Command;

use PHSA\Acl\Rule;
use PHSA\Database\Query;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base command for other PHSA commands
 */
class BaseCommand extends Command {
    /**
     * Application configuration
     */
    protected $configuration;

    /**
     * Set the configuration
     *
     * @param array $configuration
     */
    protected function setConfiguration(array $configuration) {
        $this->configuration = $configuration;
    }

    /**
     * Get the configuration
     *
     * @return array
     */
    protected function getConfiguration() {
        return $this->configuration;
    }

    /**
     * Initialization method
     *
     * This method will be triggered before any commands will be executed. It will load the
     * configuration based on the --config-file option given to the application and store it in
     * this class.
     *
     * @see \Symfony\Components\Console\Command\Command::initialize()
     */
    protected function initialize(InputInterface $input, OutputInterface $output) {
        if ($this->configuration !== null) {
            return;
        }

        $configFile = $input->getOption('config-file');

        if (empty($configFile) || !is_file($configFile)) {
            throw new \InvalidArgumentException('Missing or invalid path to configuration file');
        }

        // Fetch configuration array
        $config = require $configFile;

        $this->setConfiguration($config);
    }

    /**
     * Create a PHSA\Database\Query instance based on input options
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return PHSA\Database\Query
     */
    protected function createQueryFromInputOptions(InputInterface $input) {
        $repositories = $input->getoption('repos');
        $users        = $input->getoption('user');
        $groups       = $input->getoption('group');
        $role         = $input->getOption('role');
        $rule         = $input->getOption('rule');

        $repositories = empty($repositories) ? array() : array_map('trim', explode(',', $repositories));
        $users        = empty($users) ? array() : array_map('trim', explode(',', $users));
        $groups       = empty($groups) ? array() : array_map('trim', explode(',', $groups));

        if ($role !== Query::USER && $role !== Query::GROUP) {
            $role = null;
        }

        if ($rule !== Rule::ALLOW && $rule !== Rule::DENY) {
            $rule = null;
        }

        $query = new Query();

        return $query->setRepositories($repositories)
                     ->setUsers($users)
                     ->setGroups($groups)
                     ->setRole($role)
                     ->setRule($rule);
    }
}
