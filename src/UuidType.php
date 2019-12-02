<?php declare (strict_types=1);

namespace Dreamsbond\Uuid\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @package App
 */
class UuidType extends Type
{
    /**
     * @var string
     */
    const NAME = 'limoncelloUuid';

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array                                     $fieldDeclaration The field declaration.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform         The currently used database platform.
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getGuidTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * @inheritDoc
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }
        if (
            $value instanceof UuidInterface
            || (
                (is_string($value) || method_exists($value, '__toString'))
                && Uuid::isValid((string)$value)
            )
        ) {
            return (string)$value;
        }
        throw ConversionException::conversionFailed($value, static::NAME);
    }

    /**
     * @inheritDoc
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }
        if ($value instanceof UuidInterface) {
            return $value;
        }
        try {
            $uuid = Uuid::fromString($value);
        } catch (InvalidArgumentException $e) {
            throw ConversionException::conversionFailed($value, static::NAME);
        }
        return $uuid;
    }

    /**
     * @inheritDoc
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
