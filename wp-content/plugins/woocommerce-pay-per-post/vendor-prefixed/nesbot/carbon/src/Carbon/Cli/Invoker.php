<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by __root__ on 08-August-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Pramadillo\PayForPost\Carbon\Cli;

class Invoker
{
    public const CLI_CLASS_NAME = 'Pramadillo\PayForPost\Carbon\\Cli';

    protected function runWithCli(string $className, array $parameters): bool
    {
        $cli = new $className();

        return $cli(...$parameters);
    }

    public function __invoke(...$parameters): bool
    {
        if (class_exists(self::CLI_CLASS_NAME)) {
            return $this->runWithCli(self::CLI_CLASS_NAME, $parameters);
        }

        $function = (($parameters[1] ?? '') === 'install' ? ($parameters[2] ?? null) : null) ?: 'shell_exec';
        $function('composer require carbon-cli/carbon-cli --no-interaction');

        echo 'Installation succeeded.';

        return true;
    }
}
