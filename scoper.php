<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$nowDateTime = new DateTime('now');
$timestamp = $nowDateTime->format('Ym');

// @see https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md
use Nette\Utils\Strings;

// see https://github.com/humbug/php-scoper
return [
    'prefix' => 'CodingStandard' . $timestamp,
    'expose-classes' => [
        // part of public interface of configs.php
        'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator',
    ],
    'expose-constants' => ['#^SYMFONY\_[\p{L}_]+$#'],
    'exclude-namespaces' => ['#^Symplify\\\\CodingStandard#', '#^Symfony\\\\Polyfill#'],
    'exclude-files' => [
        // do not prefix "trigger_deprecation" from symfony - https://github.com/symfony/symfony/commit/0032b2a2893d3be592d4312b7b098fb9d71aca03
        // these paths are relative to this file location, so it should be in the root directory
        'vendor/symfony/deprecation-contracts/function.php',
        'stubs/PHPUnit/PHPUnit_Framework_TestCase.php',
    ],
    'patchers' => [
        // scope symfony configs
        function (string $filePath, string $prefix, string $content): string {
            if (! Strings::match($filePath, '#(packages|config|services)\.php$#')) {
                return $content;
            }

            // fix symfony config load scoping, except CodingStandard
            $content = Strings::replace(
                $content,
                '#load\(\'Symplify\\\\\\\\(?<package_name>[A-Za-z]+)#',
                function (array $match) use ($prefix) {
                    if (in_array($match['package_name'], ['CodingStandard'], true)) {
                        // skip
                        return $match[0];
                    }

                    return 'load(\'' . $prefix . '\Symplify\\' . $match['package_name'];
                }
            );

            return $content;
        },

        // unprefix test case class names
        function (string $filePath, string $prefix, string $content): string {
            if (! str_ends_with($filePath, 'packages/Testing/UnitTestFilter.php')) {
                return $content;
            }

            $content = Strings::replace(
                $content,
                '#' . $prefix . '\\\\PHPUnit\\\\Framework\\\\TestCase#',
                'PHPUnit\Framework\TestCase'
            );

            return Strings::replace(
                $content,
                '#' . $prefix . '\\\\PHPUnit_Framework_TestCase#',
                'PHPUnit_Framework_TestCase'
            );
        },

        // unprefix kernerl test case class names
        function (string $filePath, string $prefix, string $content): string {
            if (! str_ends_with($filePath, 'packages/Testing/UnitTestFilter.php')) {
                return $content;
            }

            $content = Strings::replace(
                $content,
                '#' . $prefix . '\\\\Symfony\\\\Bundle\\\\FrameworkBundle\\\\Test\\\\KernelTestCase#',
                'Symfony\Bundle\FrameworkBundle\Test\KernelTestCase'
            );

            return Strings::replace(
                $content,
                '#' . $prefix . '\\\\Symfony\\\\Component\\\\Form\\\\Test\\\\TypeTestCase',
                'Symfony\Component\Form\Test\TypeTestCase'
            );
        },

        // unprefix string class names to ignore, to keep original class names
        function (string $filePath, string $prefix, string $content): string {
            if (! str_ends_with($filePath, 'packages/ActiveClass/Filtering/PossiblyUnusedClassesFilter.php')) {
                return $content;
            }

            return Strings::replace($content, '#DEFAULT_TYPES_TO_SKIP = (?<content>.*?)\;#ms', function (array $match) use (
                $prefix
            ) {
                // remove prefix from there
                return 'DEFAULT_TYPES_TO_SKIP = ' .
                    Strings::replace($match['content'], '#' . $prefix . '\\\\#', '') . ';';
            });
        },
    ],
];
