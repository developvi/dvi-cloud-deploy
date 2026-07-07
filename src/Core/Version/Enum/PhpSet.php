<?php

namespace DVICloudDeploy\Core\Version\Enum;

use DVICloudDeploy\Core\Version\PhpLifecycle;
use DVICloudDeploy\Core\Version\PhpVersion;

enum PhpSet
{
    /** Every PHP version registered in the catalog. */
    case All;

    /** PHP >= 7.4 (non-legacy install path). */
    case Modern;

    /**
     * @param array<string, PhpVersion> $registered
     *
     * @return list<string>
     */
    public function slugs(array $registered): array
    {
        $slugs = [];

        foreach ($registered as $php) {
            if ($this->includes($php)) {
                $slugs[] = $php->getSlug();
            }
        }

        return $slugs;
    }

    private function includes(PhpVersion $php): bool
    {
        return match ($this) {
            self::All => true,
            self::Modern => !PhpLifecycle::isLegacyInstall($php->getLabel()),
        };
    }
}
