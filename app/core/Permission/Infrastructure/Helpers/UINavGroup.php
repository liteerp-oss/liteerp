<?php

namespace Core\Permission\Infrastructure\Helpers;

final class UINavGroup
{
    public const DASHBOARD     = 'Dashboard';
    public const SALES         = 'Sales';
    public const PURCHASING    = 'Purchasing';
    public const INVENTORY     = 'Inventory';
    public const ORGANIZATION  = 'Organization';
    public const SYSTEM        = 'System';

    public static function all(): array
    {
        return [
            self::DASHBOARD,
            self::SALES,
            self::PURCHASING,
            self::INVENTORY,
            self::ORGANIZATION,
            self::SYSTEM,
        ];
    }
    public static function exists(string $group): bool
    {
        return in_array($group, self::all(), true);
    }
}
