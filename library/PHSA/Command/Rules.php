<?php
namespace PHSA\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command used to list or delete rules
 */
class Rules extends BaseCommand {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('rules');
        $this->setDescription('List or delete stored rules');
        $this->setHelp('Display or delete rules stored in the database');

        $this->addOption('repos', null, InputOption::VALUE_OPTIONAL, 'Comma separated list of repositories to show rules from');
        $this->addOption('user', null, InputOption::VALUE_OPTIONAL, 'Comma-separated list of usernames to show rules from');
        $this->addOption('group', null, InputOption::VALUE_OPTIONAL, 'Comma-separated list of groups to show rules from');
        $this->addOption('role', null, InputOption::VALUE_OPTIONAL, 'Show only rules with this role. Values can be "user" or "group"');
        $this->addOption('rule', null, InputOption::VALUE_OPTIONAL, 'Show only rules of this type. Values can be "allow" or "deny"');
        $this->addOption('delete', null, InputOption::VALUE_NONE, 'Set this option to delete the rules matching your query');
    }

    /**
     * Execute the command
     *
     * @see \Symfony\Components\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $driver = $this->configuration['database']['driver'];

        $query = $this->createQueryFromInputOptions($input);
        $ruleset = $query->getRules($driver);

        if (count($ruleset) === 0) {
            $output->writeln('No rules matches your query');
        } else {
            if ($input->getOption('delete')) {
                $num = $driver->removeRules($query);

                if ($num === false) {
                    $output->writeln('An error occured. No rules have been deleted');
                } else {
                    $output->writeln($num . ' rule(s) deleted');
                }
            } else {
                $output->writeln(
                    str_pad('Repos', 15, ' ', STR_PAD_BOTH) . '|' .
                    str_pad('User', 15, ' ', STR_PAD_BOTH) . '|' .
                    str_pad('Group', 15, ' ', STR_PAD_BOTH) . '|' .
                    str_pad('Path', 15, ' ', STR_PAD_BOTH) . '|' .
                    str_pad('Rule', 15, ' ', STR_PAD_BOTH)
                );
                $output->writeln(str_repeat('-', 80));

                foreach ($ruleset as $rule) {
                    $output->writeln(
                        str_pad($rule->getRepos(), 15, ' ', STR_PAD_BOTH) . '|' .
                        str_pad($rule->getUser() ?: ' - ', 15, ' ', STR_PAD_BOTH) . '|' .
                        str_pad($rule->getGroup() ?: ' - ', 15, ' ', STR_PAD_BOTH) . '|' .
                        str_pad($rule->getPath(), 15, ' ', STR_PAD_BOTH) . '|' .
                        str_pad($rule->getRule(), 15, ' ', STR_PAD_BOTH)
                    );
                }
            }
        }
    }
}
