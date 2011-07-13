<?php
namespace PHSA\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Command used to remove rules from the database
 */
class Remove extends BaseCommand {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('remove');
        $this->setDescription('Remove rules');
        $this->setHelp('Remove rules from the database based on one or more options. Several options can be combined to remove more rules');

        $this->addOption('all', null, InputOption::VALUE_NONE, 'Remove all rules');

        $this->addOption('repos', null, InputOption::VALUE_OPTIONAL, 'Comma separated list of repositories to remove rules from');
        $this->addOption('user', null, InputOption::VALUE_OPTIONAL, 'Comma separated list of users to remove rules from');
        $this->addOption('group', null, InputOption::VALUE_OPTIONAL, 'Comma separated list of groups to remove rules from');
        $this->addOption('rule', null, InputOption::VALUE_OPTIONAL, 'Remove rules of this type. "allow" or "deny"');
        $this->addOption('role', null, InputOption::VALUE_OPTIONAL, 'Remove rules with this role. "user" or "group"');
    }

    /**
     * Execute the command
     *
     * @see \Symfony\Components\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $dialog = $this->getHelper('dialog');
        $result = $dialog->askConfirmation($output, 'Are you sure you want to remove rules? ', false);

        if (!$result) {
            $output->writeln('Command aborted');
            return;
        }

        $databaseDriver = $this->configuration['database']['driver'];

        if ($input->getOption('all')) {
            $result = $dialog->askConfirmation($output, 'Are you REALLY sure you want to remove ALL rules? ', false);

            if ($result) {
                $num = $databaseDriver->removeAllRules();

                if ($num === false) {
                    $output->writeln('An error occured. No rules have been removed');
                } else {
                    $output->writeln($num . ' rule' . ($num === 1 ? '' : 's') . ' removed');
                }

                return;
            }

            $output->writeln('Command aborted');
            return;
        }

        // Remove rules based on query
        $query = $this->createQueryFromInputOptions($input);
        $num = $query->removeRules($databaseDriver);

        if ($num === false) {
            $output->writeln('An error occured. No rules have been removed');
        } else {
            $output->writeln($num . ' rule' . ($num === 1 ? '' : 's') . ' removed');
        }
    }
}
