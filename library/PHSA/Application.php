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
        $this->addCommands(array(
            new Command\ListReposes(),
            new Command\ListAcls(),
            new Command\AllowUser(),
            new Command\AllowGroup(),
            new Command\DenyUser(),
            new Command\DenyGroup(),
            new Command\Dump(),
            new Command\Import(),
            new Command\Remove(),
        ));

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
