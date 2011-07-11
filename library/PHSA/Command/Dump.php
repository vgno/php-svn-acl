<?php
namespace PHSA\Command;

use PHSA\Version;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command used to dump the ruleset as XML
 */
class Dump extends BaseCommand {
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('dump');
        $this->setDescription('Dump the ruleset');
        $this->setHelp('Dump the complete ruleset as XML. This dump can be imported back into PHSA using the import command');
    }

    /**
     * Execute the commend
     *
     * @see \Symfony\Components\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $driver = $this->configuration['database']['driver'];
        $rules = $driver->getAcls();

        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;

        $ruleset = $document->createElement('ruleset');

        foreach ($rules as $r) {
            $rule = $document->createElement('rule');

            $rule->setAttribute('id',    $r->id);
            $rule->setAttribute('repos', $r->repos);
            $rule->setAttribute('user',  $r->user);
            $rule->setAttribute('group', $r->group);
            $rule->setAttribute('user',  $r->user);
            $rule->setAttribute('rule',  $r->rule);

            if (!empty($r->path)) {
                $path = $document->createTextNode($r->path);
                $rule->appendChild($path);
            }

            $ruleset->appendChild($rule);
        }

        $phsa = $document->createElement('phsa');
        $document->appendChild($phsa);
        $phsa->appendChild($ruleset);

        $output->write($document->saveXml(), false, OutputInterface::OUTPUT_RAW);
    }
}

