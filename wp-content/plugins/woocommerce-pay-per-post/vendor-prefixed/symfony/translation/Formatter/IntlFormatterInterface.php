<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by __root__ on 08-August-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Pramadillo\PayForPost\Symfony\Component\Translation\Formatter;

/**
 * Formats ICU message patterns.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
interface IntlFormatterInterface
{
    /**
     * Formats a localized message using rules defined by ICU MessageFormat.
     *
     * @see http://icu-project.org/apiref/icu4c/classMessageFormat.html#details
     */
    public function formatIntl(string $message, string $locale, array $parameters = []): string;
}
