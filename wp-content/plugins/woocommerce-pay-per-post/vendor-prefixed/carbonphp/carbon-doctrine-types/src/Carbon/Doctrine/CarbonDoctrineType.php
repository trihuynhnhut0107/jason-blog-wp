<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 08-August-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Pramadillo\PayForPost\Carbon\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;

interface CarbonDoctrineType
{
    public const MAXIMUM_PRECISION = 10;

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform);

    public function convertToPHPValue($value, AbstractPlatform $platform);

    public function convertToDatabaseValue($value, AbstractPlatform $platform);
}
