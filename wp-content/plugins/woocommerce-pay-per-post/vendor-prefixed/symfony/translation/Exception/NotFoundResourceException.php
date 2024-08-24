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

namespace Pramadillo\PayForPost\Symfony\Component\Translation\Exception;

/**
 * Thrown when a resource does not exist.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NotFoundResourceException extends \InvalidArgumentException implements ExceptionInterface
{
}
