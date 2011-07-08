<?php
namespace PHSA;

use PHSA\Command\ListReposesCommand;
use PHSA\Command\ListAclsCommand;
use Symfony\Component\Console;

/**
 * Main application
 */
class Application extends Console\Application {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('PHSA', Version::getVersionNumber());

        // Register commands
        $this->add(new ListReposesCommand());
        $this->add(new ListAclsCommand());

        // Add global options
        $this->getDefinition()->addOption(
            new Console\Input\InputOption(
                'config-file',
                null,
                Console\Input\InputOption::VALUE_OPTIONAL,
                'Path to configuration file',
                PHSA_CONFIG_FILE
            )
        );
    }
}
