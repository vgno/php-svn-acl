<?php
namespace PHSA\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Command used to list the ACLs stored in the database
 */
class ListAclsCommand extends Command {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('rules');
        $this->setDescription('List stored rules');
        $this->setHelp('Display all ACLs stored in the database');

        $this->addOption('repos', null, InputOption::VALUE_OPTIONAL,
                         'Comma separated list of repositories to show rules from');
        $this->addOption('user', null, InputOption::VALUE_OPTIONAL,
                         'Comma-separated list of usernames to show rules from');
        $this->addOption('group', null, InputOption::VALUE_OPTIONAL,
                         'Comma-separated list of groups to show rules from');
        $this->addOption('role', null, InputOption::VALUE_OPTIONAL,
                         'Show only rules with this rols. Values can be "user" or "group"');
        $this->addOption('rule', null, InputOption::VALUE_OPTIONAL,
                         'Show only rules with this type. Values can be "allow" or "deny"');
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
        $driver = $config['database']['driver'];

        $repositories = $input->getoption('repos');
        $users        = $input->getoption('user');
        $groups       = $input->getoption('group');
        $role         = $input->getOption('role');
        $rule         = $input->getOption('rule');

        $repositories = empty($repos) ? array() : array_map('trim', explode(',', $repositories));
        $users        = empty($users) ? array() : array_map('trim', explode(',', $users));
        $groups       = empty($groups) ? array() : array_map('trim', explode(',', $groups));

        if ($role !== 'user' && $role !== 'group') {
            $role = null;
        }

        if ($rule !== 'allow' && $rule !== 'deny') {
            $rule = null;
        }

        $acls = $driver->getAcls($repositories, $users, $groups, $role, $rule);

        if (count($acls) === 0) {
            $output->writeln('No rules matches your query. Broaden your search.');
        } else {
            $output->writeln(
                str_pad('Repos', 9) . '|' .
                str_pad('User', 18, ' ', STR_PAD_BOTH) . '|' .
                str_pad('Group', 18, ' ', STR_PAD_BOTH) . '|' .
                str_pad('Path', 18, ' ', STR_PAD_BOTH) . '|' .
                str_pad('Rule', 9, ' ', STR_PAD_LEFT)
            );
            $output->writeln(str_repeat('-', 80));

            foreach ($acls as $rule) {
                $output->writeln(
                    str_pad($rule->repos, 9) . '|' .
                    str_pad($rule->user, 18, ' ', STR_PAD_BOTH) . '|' .
                    str_pad($rule->group, 18, ' ', STR_PAD_BOTH) . '|' .
                    str_pad($rule->path, 18, ' ', STR_PAD_BOTH) . '|' .
                    str_pad($rule->type, 9, ' ', STR_PAD_LEFT)
                );
            }
        }
    }
}
