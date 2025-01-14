<?php
// src/Doctrine/Type/UserRoleType.php

namespace App\Doctrine\Type;

use App\Enum\UserRole;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class UserRoleType extends Type
{
    const NAME = 'user_role';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "ENUM('ROLE_ADMIN', 'ROLE_USER')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return UserRole::from($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof UserRole) {
            return $value->value;
        }

        throw new \InvalidArgumentException('Invalid role');
    }

    public function getName()
    {
        return self::NAME;
    }
}