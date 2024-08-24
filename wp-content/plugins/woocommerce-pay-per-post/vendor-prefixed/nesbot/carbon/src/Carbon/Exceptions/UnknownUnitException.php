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

namespace Pramadillo\PayForPost\Carbon\Exceptions;

use Throwable;

class UnknownUnitException extends UnitException
{
    /**
     * The unit.
     *
     * @var string
     */
    protected $unit;

    /**
     * Constructor.
     *
     * @param string         $unit
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($unit, $code = 0, Throwable $previous = null)
    {
        $this->unit = $unit;

        parent::__construct("Unknown unit '$unit'.", $code, $previous);
    }

    /**
     * Get the unit.
     *
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }
}
