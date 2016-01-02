<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\CodingStandard\Runner;

use Symfony\Component\Process\Process;
use Symplify\CodingStandard\Contract\Runner\RunnerInterface;

final class SymfonyRunner implements RunnerInterface
{
    /**
     * @var string
     */
    const CONTRIB_FIXERS = 'short_array_syntax,newline_after_open_tag,ordered_use,php_unit_construct,phpdoc_order';

    /**
     * @var string
     */
    private $output;

    /**
     * {@inheritdoc}
     */
    public function runForDirectory($directory)
    {
        $process = new Process(
            sprintf(
                'php vendor/bin/php-cs-fixer fix %s --dry-run --diff -v --level=symfony --fixers=%s',
                $directory,
                self::CONTRIB_FIXERS
            )
        );
        $process->run();

        $this->output = $process->getOutput();

        return $this->output;
    }

    /**
     * {@inheritdoc}
     */
    public function hasErrors()
    {
        if (strpos($this->output, 'end diff') !== false) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function fixDirectory($directory)
    {
        $process = new Process(
            sprintf(
                'php vendor/bin/php-cs-fixer fix %s --diff -v --level=symfony --fixers=%s',
                $directory,
                self::CONTRIB_FIXERS
            )
        );
        $process->run();
    }
}
