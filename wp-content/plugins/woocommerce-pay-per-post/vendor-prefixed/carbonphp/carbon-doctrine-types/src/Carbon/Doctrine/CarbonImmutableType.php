<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 08-August-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Pramadillo\PayForPost\Carbon\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class CarbonImmutableType extends DateTimeImmutableType implements CarbonDoctrineType
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'carbon_immutable';
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
