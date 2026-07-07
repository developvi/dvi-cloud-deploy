<?php

namespace DVICloudDeploy\Core\Version;

use DVICloudDeploy\Core\Version\Enum\OsStatus;

final class VersionRegistry
{
    /** @var array<string, PhpVersion> */
    private static array $php = [];

    /** @var array<string, OsVersion> */
    private static array $os = [];

    private static bool $booted = false;

    public static function boot(): void
    {
        if (self::$booted) {
            return;
        }

        self::loadPhp(Catalog::php());
        self::loadOs(Catalog::os());

        if (function_exists('do_action')) {
            do_action('dvicd_register_php_versions');
            do_action('dvicd_register_os_versions');
        }

        self::$booted = true;
    }

    public static function registerPhp(PhpVersion $version): void
    {
        self::$php[$version->getSlug()] = $version;
    }

    public static function registerOs(OsVersion $version): void
    {
        self::$os[$version->getSlug()] = $version;
    }

    /** @param list<PhpVersion> $versions */
    private static function loadPhp(array $versions): void
    {
        if (function_exists('apply_filters')) {
            $versions = apply_filters('dvicd_php_versions', $versions);
        }

        foreach ($versions as $version) {
            self::registerPhp($version);
        }
    }

    /** @param list<OsVersion> $versions */
    private static function loadOs(array $versions): void
    {
        if (function_exists('apply_filters')) {
            $versions = apply_filters('dvicd_os_versions', $versions);
        }

        foreach ($versions as $version) {
            self::registerOs($version);
        }
    }

    public static function hasPhp(string $slug): bool
    {
        return isset(self::$php[$slug]);
    }

    public static function php(string $slug): ?PhpVersion
    {
        return self::$php[$slug] ?? null;
    }

    /** @return array<string, PhpVersion> */
    public static function allPhp(): array
    {
        return self::$php;
    }

    public static function hasOs(string $slug): bool
    {
        return isset(self::$os[$slug]);
    }

    public static function os(string $slug): ?OsVersion
    {
        return self::$os[$slug] ?? null;
    }

    /** @return array<string, OsVersion> */
    public static function allOs(): array
    {
        return self::$os;
    }

    /** @return list<OsVersion> */
    public static function sortedOs(): array
    {
        $items = array_values(self::$os);
        usort($items, static fn (OsVersion $a, OsVersion $b): int => $a->getSortOrder() <=> $b->getSortOrder());

        return $items;
    }

    /** @return array<string, string> */
    public static function osSelectList(): array
    {
        $list = [];

        foreach (self::sortedOs() as $os) {
            if (!$os->isVisibleInDeployList()) {
                continue;
            }

            $label = $os->getSelectLabel();

            // Say so in the dropdown when an OS is close to the end of its
            // support window, so nobody builds a new server on it by accident.
            if ($os->getStatus() === OsStatus::Deprecated && $os->getSelectLabelOverride() === null) {
                $months = $os->monthsOfSupportLeft();
                $label .= $months > 0
                    /* translators: %d is the number of months of support remaining. */
                    ? ' ' . sprintf(_n('(not recommended - support ends in %d month)', '(not recommended - support ends in %d months)', $months, 'wpcd'), $months)
                    : ' ' . __('(not recommended - support ending)', 'wpcd');
            }

            $list[$os->getSlug()] = $label;
        }

        return $list;
    }

    /**
     * Operating systems that should be avoided for new servers but are still
     * selectable - inside their last year of support.
     *
     * @return list<string>
     */
    public static function deprecatedOsSlugs(): array
    {
        $slugs = [];

        foreach (self::sortedOs() as $os) {
            if ($os->isVisibleInDeployList() && $os->getStatus() === OsStatus::Deprecated) {
                $slugs[] = $os->getSlug();
            }
        }

        return $slugs;
    }

    public static function osDescription(string $slug): string
    {
        return self::os($slug)?->getLabel() ?? $slug;
    }

    public static function osProviderImage(string $slug, string $provider): ?string
    {
        return self::os($slug)?->getProviderImage($provider);
    }

    /** @return list<PhpVersion> */
    public static function sortedPhp(): array
    {
        $items = array_values(self::$php);
        usort($items, static fn (PhpVersion $a, PhpVersion $b): int => version_compare($a->getLabel(), $b->getLabel()));

        return $items;
    }

    /** @return array<string, string> */
    public static function phpSelectList(): array
    {
        $list = [];

        foreach (self::sortedPhp() as $php) {
            $label = $php->getLabel();
            $list[$label] = $label;
        }

        return $list;
    }

    /** @return array<string, string> */
    public static function phpSelectListModern(): array
    {
        $list = [];

        foreach (self::sortedPhp() as $php) {
            if ($php->isLegacyInstall()) {
                continue;
            }

            $label = $php->getLabel();
            $list[$label] = $label;
        }

        return $list;
    }

    /** @return array<string, string> */
    public static function phpSlugLabelMap(): array
    {
        $map = [];

        foreach (self::sortedPhp() as $php) {
            $map[$php->getSlug()] = $php->getLabel();
        }

        return $map;
    }

    /** @return list<string> */
    public static function phpLabels(): array
    {
        return array_map(static fn (PhpVersion $php): string => $php->getLabel(), self::sortedPhp());
    }

    public static function hasPhpLabel(string $label): bool
    {
        foreach (self::$php as $php) {
            if ($php->getLabel() === $label) {
                return true;
            }
        }

        return false;
    }

    public static function phpForLabel(string $label): ?PhpVersion
    {
        foreach (self::$php as $php) {
            if ($php->getLabel() === $label) {
                return $php;
            }
        }

        return null;
    }

    /**
     * @param callable(string): string $labelFormatter
     *
     * @return array<string, string>
     */
    public static function legacyInstallSelectList(callable $labelFormatter): array
    {
        $list = [];

        foreach (self::sortedPhp() as $php) {
            if (!$php->isLegacyInstall()) {
                continue;
            }

            $label = $php->getLabel();
            $list[$label] = $labelFormatter($label);
        }

        return $list;
    }

    /** @return list<string> */
    public static function legacyInstallLabels(): array
    {
        $labels = [];

        foreach (self::sortedPhp() as $php) {
            if ($php->isLegacyInstall()) {
                $labels[] = $php->getLabel();
            }
        }

        return $labels;
    }

    /** @return list<string> */
    public static function defaultPhpWarnVersions(): array
    {
        $labels = [];

        foreach (self::sortedPhp() as $php) {
            if ($php->isLegacyInstall() || $php->getLabel() === '8.0') {
                $labels[] = $php->getLabel();
            }
        }

        return $labels;
    }

    /**
     * PHP versions installed on every server during provisioning.
     *
     * @return list<string>
     */
    public static function baselinePhpLabels(): array
    {
        return Catalog::baselinePhpLabels();
    }

    /**
     * Oldest PHP version guaranteed to exist on a freshly provisioned server.
     */
    public static function minimumPhpLabel(): string
    {
        return Catalog::minimumPhpLabel();
    }

    /**
     * PHP versions installed on request and left disabled until a site uses one.
     *
     * @return list<string>
     */
    public static function legacyInstallPhpLabels(): array
    {
        return Catalog::legacyInstallPhpLabels();
    }

    /**
     * PHP versions available on request but newer than the baseline.
     *
     * @return list<string>
     */
    public static function optionalPhpLabels(): array
    {
        return Catalog::optionalPhpLabels();
    }

    /**
     * Is this PHP version present on a server as soon as it finishes provisioning?
     */
    public static function isBaselinePhpLabel(string $label): bool
    {
        return in_array($label, Catalog::baselinePhpLabels(), true);
    }

    public static function fpmStatusCommand(string $slug): string
    {
        $php = self::php($slug);

        return $php !== null ? 'sudo service ' . $php->getFpmService() . ' status' : '';
    }

    public static function fpmRestartCommand(string $slug): string
    {
        $php = self::php($slug);

        return $php !== null ? 'sudo service ' . $php->getFpmService() . ' restart' : '';
    }

    /** @return array<string, string> */
    public static function phpFpmLogSelectList(): array
    {
        $list = [];

        foreach (self::sortedPhp() as $php) {
            $list[$php->getFpmLogPath()] = sprintf(
                /* translators: %s: PHP version label such as 8.4 */
                __('PHP %s FPM Log', 'wpcd'),
                $php->getLabel()
            );
        }

        return $list;
    }

    public static function phpForInstallScript(string $script): ?PhpVersion
    {
        if ($script === 'run_upgrade_install_old_php_version.txt') {
            return null;
        }

        foreach (self::allPhp() as $php) {
            if ($php->getInstallScript() === $script) {
                return $php;
            }
        }

        return null;
    }

    public static function installScriptRawBasenameForLogicalScript(string $logicalScript): ?string
    {
        if ($logicalScript === 'run_upgrade_install_php.txt') {
            return '1110-upgrade_install_php';
        }

        return self::phpForInstallScript($logicalScript)?->getInstallScriptRawBasename();
    }

    public static function installSuccessPhraseForScript(string $script): ?string
    {
        if ($script === 'run_upgrade_install_php.txt') {
            return null;
        }

        return self::phpForInstallScript($script)?->getInstallSuccessPhrase();
    }

    public static function phpRestartActionSlug(string $slug): string
    {
        return 'php-server-restart-' . $slug;
    }

    public static function phpActivateActionSlug(string $slug): string
    {
        return 'php-server-activate-' . $slug;
    }

    public static function phpDeactivateActionSlug(string $slug): string
    {
        return 'php-server-deactivate-' . $slug;
    }

    public static function phpUpgradeActionSlug(string $slug): string
    {
        return 'server-upgrade-' . $slug;
    }

    public static function phpSlugFromServiceAction(string $action): ?string
    {
        foreach (array('php-server-restart-', 'php-server-activate-', 'php-server-deactivate-') as $prefix) {
            if (str_starts_with($action, $prefix)) {
                $slug = substr($action, strlen($prefix));

                return self::hasPhp($slug) ? $slug : null;
            }
        }

        return null;
    }

    public static function phpSlugFromUpgradeAction(string $action): ?string
    {
        if (!str_starts_with($action, 'server-upgrade-php')) {
            return null;
        }

        $slug = substr($action, strlen('server-upgrade-'));

        return self::hasPhp($slug) ? $slug : null;
    }

    public static function olsServiceName(string $label): string
    {
        return 'lsphp' . str_replace('.', '', $label);
    }

    public static function labelFromOlsService(string $service): ?string
    {
        if (!str_starts_with($service, 'lsphp')) {
            return null;
        }

        $slug = 'php' . substr($service, 5);

        return self::php($slug)?->getLabel();
    }

    public static function defaultAppPhpLabel(): string
    {
        return Catalog::defaultAppPhpLabel();
    }

    /**
     * Which PHP version a site built by the given plugin version defaulted to.
     *
     * Use this - not defaultAppPhpLabel() - when working out what an existing
     * server or site is already running.
     */
    public static function defaultAppPhpLabelForPluginVersion(?string $pluginVersion): string
    {
        return Catalog::defaultAppPhpLabelForPluginVersion($pluginVersion);
    }

    /** @return array<string, string> */
    public static function defaultAppPhpHistory(): array
    {
        return Catalog::defaultAppPhpHistory();
    }

    public static function legacyDefaultAppPhpLabel(): string
    {
        return Catalog::legacyDefaultAppPhpLabel();
    }

    /** @return list<string> */
    public static function acceptableServerCliDefaultPhpLabels(): array
    {
        return Catalog::acceptableServerCliDefaultPhpLabels();
    }

    public static function phpSlugForLabel(string $label): string
    {
        return 'php' . str_replace('.', '', $label);
    }

    /**
     * @param callable(int, string, bool): mixed $metaReader
     */
    public static function isPhpInstalledOnServer(int $serverId, string $slug, callable $metaReader): bool
    {
        $version = self::php($slug);

        return $version !== null && $version->isInstalledOnServer($serverId, $metaReader);
    }

    public static function supportsPhpOnOs(string $phpSlug, string $osSlug): bool
    {
        $os = self::os($osSlug);

        return $os !== null && in_array($phpSlug, $os->getSupportedPhp(), true);
    }

    /** @return list<string> */
    public static function unsupportedOsForPhp(string $phpSlug): array
    {
        $excluded = [];

        foreach (self::$os as $os) {
            if (!in_array($phpSlug, $os->getSupportedPhp(), true)) {
                $excluded[] = $os->getSlug();
            }
        }

        return $excluded;
    }
}
