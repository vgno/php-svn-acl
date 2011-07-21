<?php
namespace PHSA\Command;

use PHSA\Acl\Rule;
use PHSA\Database\Query;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command used to test rules
 */
class Test extends BaseCommand {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('test');
        $this->setDescription('Test the rules');
        $this->setHelp('Test the defined rules by simulating a commit by a user to some file in a repository');

        $this->addArgument('repos', InputArgument::REQUIRED, 'Repository to commit to');
        $this->addArgument('user', InputArgument::REQUIRED, 'User to commit as');
        $this->addArgument('groups', InputArgument::IS_ARRAY|InputArgument::REQUIRED, 'Groups the user belongs to');

        $this->addOption('path', null, InputOption::VALUE_OPTIONAL, 'Optional path to commit to. If omitted, repository root will be assumed.');
        $this->addOption('default', null, InputOption::VALUE_OPTIONAL, sprintf('Default behaviour ("%s" or "%s")', Rule::ALLOW, Rule::DENY), Rule::ALLOW);
    }

    /**
     * Execute the command
     *
     * @see \Symfony\Components\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $repos = $input->getArgument('repos');
        $user = $input->getArgument('user');
        $groups = $input->getArgument('groups');

        $path = $input->getOption('path');
        $allow = ($input->getOption('default') == Rule::ALLOW);
        $topLevels = array();

        if ($path !== null) {
            $path = trim($path, '/');

            if (!empty($path)) {
                $parts = explode('/', $path);
                $topLevels[] = $parts[0];
            }
        }

        // Prepend a slash
        $path = '/' . $path;

        $dbDriver = $this->configuration['database']['driver'];

        $rules = $dbDriver->getEffectiveRules($repos, $user, $groups, $topLevels);

        $userRules = array();
        $groupRules = array();

        foreach ($rules as $rule) {
            $result = ($rule->rule === Rule::ALLOW);

            if ($rule->isUserRule()) {
                $userRules[$rule->path ?: '/'] = $result;
            } else {
                $groupRules[$rule->path ?: '/'] = $result;
            }
        }

        // Generate the union of the rules, and make the user rules override the group rules
        $ruleset = $userRules + $groupRules;

        foreach ($ruleset as $rulePath => $flag) {
            if (strpos($path, $rulePath) === 0) {
                $allow = $flag;
            }
        }

        if ($allow) {
            $output->writeln(sprintf('<info>%s is allowed to write to "%s" in the %s repository</info>', $user, $path, $repos));
        } else {
            $output->write(array(
                sprintf('<error>%s is not allowed to write to "%s" in the %s repository</error>', $user, $path, $repos),
                'The following rules have been applied:',
                'Default: ' . ($allow ? '<info>allow</info>' : '<error>deny</error>'),
            ), true);

            foreach ($rules as $rule) {
                $tag = ($rule->rule === Rule::ALLOW) ? 'info' : 'error';

                if ($rule->isUserRule()) {
                    $output->writeln('<' . $tag . '>User: ' . $rule->user . ', path: ' . ($rule->path ?: '/') . '</' . $tag . '>');
                } else {
                    $output->writeln('<' . $tag . '>Group: ' . $rule->group . ', path: ' . ($rule->path ?: '/') . '</' . $tag . '>');
                }
            }
        }
    }
}
