<?php
namespace PHSA\Command;

use PHSA\Database\DriverInterface as Database;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Command used to dump the ruleset as XML
 */
class Import extends BaseCommand {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('import');
        $this->setDescription('Import fules from a dump file');
        $this->setHelp('Import rules from a previously dumped ruleset. Use the --replace option to replace all current rules with the one from the dump file');
        $this->addArgument('dumpfile', InputArgument::REQUIRED, 'Path to the dump file to import');
        $this->addOption('replace', null, InputOption::VALUE_NONE, 'Replace all curret rules with rules specified in the dump file');
    }

    /**
     * Execute the command
     *
     * @see \Symfony\Components\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $database = $this->configuration['database']['driver'];

        $path = $input->getArgument('dumpfile');

        if (!is_file($path)) {
            throw new \InvalidArgumentException('Specified dumpfile could not be opened: ' . $path);
        }

        $document = new \DOMDocument();
        $result = @$document->load($path); // Y U NO SHUT UP?!

        if (!$result) {
            throw new \InvalidArgumentException('Invalid XML file: ' . $path);
        }

        $xpath = new \DOMXPath($document);
        $elements = $xpath->query('/phsa/ruleset/rule');

        if ($input->getOption('replace')) {
            $dialog = $this->getHelper('dialog');
            $result = $dialog->askConfirmation($output, 'Are you sure you want to replace all existing rules? ', false);

            if ($result && $database->removeRules()) {
                $output->writeln('Existing rules has been removed');
            } else {
                $output->writeln('Command aborted');
                return;
            }
        }

        // Variables used in the output
        $added = 0;
        $skipped = 0;

        foreach ($elements as $element) {
            $id    = $element->getAttribute('id');
            $repos = $element->getAttribute('repos');
            $user  = $element->getAttribute('user') ?: null;
            $group = $element->getAttribute('group') ?: null;
            $rule  = $element->getAttribute('rule');
            $path  = $element->nodeValue ?: null;

            if (empty($repos)) {
                $output->writeln('Skipping rule with id ' . $id);
                $skipped++;
                continue;
            }

            if (empty($user) && !empty($group)) {
                if ($rule === Database::RULE_ALLOW) {
                    $database->allowGroup($group, $repos, $path);
                    $added++;
                } else if ($rule === Database::RULE_DENY) {
                    $database->denyGroup($group, $repos, $path);
                    $added++;
                }
            } else if (empty($group) && !empty($user)) {
                if ($rule === Database::RULE_ALLOW) {
                    $database->allowUser($user, $repos, $path);
                    $added++;
                } else if ($rule === Database::RULE_DENY) {
                    $database->denyUser($user, $repos, $path);
                    $added++;
                }
            }
        }

        $output->writeln(sprintf('%d rule(s) was added (%s skipped) from the dump file', $added, $skipped));
    }
}
