<?php

namespace DVICloudDeploy\Core\Version;

/** Defaults derived from the PHP version label. */
final class PhpLifecycle
{
    public static function isLegacyInstall(string $label): bool
    {
        return version_compare($label, '7.4', '<');
    }

    /** @return list<string> */
    public static function supportedWebservers(string $label): array
    {
        return self::isLegacyInstall($label) ? ['nginx'] : ['nginx', 'ols'];
    }

    public static function usesManualInstallMeta(string $label): bool
    {
        return $label !== '8.0';
    }
}
