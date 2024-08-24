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

namespace Pramadillo\PayForPost\Symfony\Component\Translation\Dumper;

use Pramadillo\PayForPost\Symfony\Component\Translation\MessageCatalogue;

/**
 * DumperInterface is the interface implemented by all translation dumpers.
 * There is no common option.
 *
 * @author Michel Salib <michelsalib@hotmail.com>
 */
interface DumperInterface
{
    /**
     * Dumps the message catalogue.
     *
     * @param array $options Options that are used by the dumper
     */
    public function dump(MessageCatalogue $messages, $options = []);
}
