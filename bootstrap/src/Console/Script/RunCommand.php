<?php

namespace Bootstrap\Console\Script;

use Bootstrap\Console\SlayerCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RunCommand extends SlayerCommand
{
    protected $name = 'run';

    protected $description = 'Automated scripts to be run.';

    public function slash()
    {
        $script = $this->input->getArgument('script');

        $lists = config()->script->toArray();

        if ( isset($lists[$script]) == false ) {
            $this->error("\nWe can't find `" . $script . '` in the lists of script.' . "\n");

            return;
        }

        $lines = $lists[$script];

        $combined = null;

        foreach ( $lines as $line ) {
            $combined .= "echo \"\e[32m{$line}\e[37m\";" .  $line . ";\n";
        }

        $output = shell_exec($combined . " 2>&1");
        if ($output) {
            $this->comment($output);
        }
    }

    protected function arguments()
    {
        return [
            ['script', InputArgument::REQUIRED, 'Automated script to be use'],
        ];
    }
}