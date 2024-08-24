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

namespace Pramadillo\PayForPost\Carbon\Traits;

use Pramadillo\PayForPost\Carbon\Exceptions\InvalidCastException;
use DateTimeInterface;

/**
 * Trait Cast.
 *
 * Utils to cast into an other class.
 */
trait Cast
{
    /**
     * Cast the current instance into the given class.
     *
     * @param string $className The $className::instance() method will be called to cast the current object.
     *
     * @return DateTimeInterface
     */
    public function cast(string $className)
    {
        if (!method_exists($className, 'instance')) {
            if (is_a($className, DateTimeInterface::class, true)) {
                return new $className($this->rawFormat('Y-m-d H:i:s.u'), $this->getTimezone());
            }

            throw new InvalidCastException("$className has not the instance() method needed to cast the date.");
        }

        return $className::instance($this);
    }
}
