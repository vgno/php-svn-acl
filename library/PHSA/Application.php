<?php
namespace PHSA;

use PHSA\Command;
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
        $this->add(new Command\ListReposes());
        $this->add(new Command\ListAcls());
        $this->add(new Command\AllowUser());
        $this->add(new Command\AllowGroup());
        $this->add(new Command\DenyUser());
        $this->add(new Command\DenyGroup());
        $this->add(new Command\Dump());
        $this->add(new Command\Import());

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
