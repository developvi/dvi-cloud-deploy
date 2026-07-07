<?php

namespace DVICloudDeploy\Core\Version;

use DVICloudDeploy\Core\Version\Enum\OsStatus;
use DVICloudDeploy\Core\Version\Enum\PhpRepo;
use DVICloudDeploy\Core\Version\Enum\PhpSet;

/** Default PHP and OS versions shipped with the plugin. */
final class Catalog
{
    /** @return list<PhpVersion> */
    public static function php(): array
    {
        return [
            PhpVersion::define('5.6', '5.3.9', securitySupportEndsAt: '2018-12-31', activeSupportEndsAt: '2017-01-19'),
            PhpVersion::define('7.1', '5.3.9', securitySupportEndsAt: '2019-12-01', activeSupportEndsAt: '2018-12-01'),
            PhpVersion::define('7.2', '5.3.9', securitySupportEndsAt: '2020-11-30', activeSupportEndsAt: '2019-11-30'),
            PhpVersion::define('7.3', '5.3.9', securitySupportEndsAt: '2021-12-06', activeSupportEndsAt: '2020-12-06'),
            PhpVersion::define('7.4', '4.0.0', securitySupportEndsAt: '2022-11-28', activeSupportEndsAt: '2021-11-28'),
            PhpVersion::define('8.0', '4.2.4', securitySupportEndsAt: '2023-11-26', activeSupportEndsAt: '2022-11-26'),
            PhpVersion::define('8.1', '4.12.1', securitySupportEndsAt: '2025-12-31', activeSupportEndsAt: '2023-11-25'),
            PhpVersion::define('8.2', '5.2.6', securitySupportEndsAt: '2026-12-31', activeSupportEndsAt: '2024-12-31'),
            PhpVersion::define('8.3', '5.7.0', securitySupportEndsAt: '2027-12-31', activeSupportEndsAt: '2025-12-31'),
            PhpVersion::define('8.4', '5.9.1', securitySupportEndsAt: '2028-12-31', activeSupportEndsAt: '2026-12-31'),
            PhpVersion::define('8.5', '7.0.0', securitySupportEndsAt: '2029-12-31', activeSupportEndsAt: '2027-12-31'),
        ];
    }

    /**
     * Every deployable entry below was verified end to end against its own
     * repository: all of PhpSet::All (5.6 through 8.5) installs and its FPM
     * service starts.  PhpSet::Modern is no longer used for any OS - the older
     * versions turned out to be available everywhere the modern ones are.
     *
     * @return list<OsVersion>
     */
    public static function os(): array
    {
        return [
            // ppa:ondrej/php has never published for resolute; sury does.
            OsVersion::ubuntu('26.04', 'resolute', '2031-05-31', 10, PhpSet::All, disableOptionKey: 'wordpress_app_disable_ubuntu_lts_2604', phpRepository: PhpRepo::Sury),
            OsVersion::ubuntu('24.04', 'noble', '2029-05-31', 20, PhpSet::All, disableOptionKey: 'wordpress_app_disable_ubuntu_lts_2404'),
            OsVersion::ubuntu('22.04', 'jammy', '2027-05-31', 30, PhpSet::All, disableOptionKey: 'wordpress_app_disable_ubuntu_lts_2204'),
            // No usable PHP source: ppa:ondrej/php serves an empty index for focal,
            // and sury has no focal suite.  sury's bullseye packages do install the
            // PHP core on focal (same glibc), but php*-gd and php*-zip pull in
            // Debian base libraries (libavif9, libjpeg62-turbo, libzip4) that Ubuntu
            // does not have - and WordPress needs both extensions.
            OsVersion::ubuntu('20.04', 'focal', '2025-05-31', 40, PhpSet::All, disableOptionKey: 'wordpress_app_disable_ubuntu_lts_2004', deployable: false, status: OsStatus::EndOfLife),
            // No usable PHP source at all: the ondrej index for bionic is empty, sury
            // has no bionic or buster suite, and sury's bullseye packages need
            // glibc >= 2.30 while 18.04 ships 2.27 - the binaries cannot run here.
            OsVersion::ubuntu('18.04', 'bionic', '2023-05-31', 50, PhpSet::All, enableOptionKey: 'wordpress_app_enable_ubuntu_lts_1804', quirks: ['ssh_service' => 'sshd', 'cgroup' => 'v1'], selectLabelOverride: 'Ubuntu 18.04 LTS (Deprecated, EOL May 2023)', deployable: false, status: OsStatus::EndOfLife),

            OsVersion::debian('13', 'trixie', '2030-06-30', PhpSet::All, deployable: true),
            OsVersion::debian('12', 'bookworm', '2028-06-30', PhpSet::All, deployable: true),
            // bullseye is past end of standard support; 01-prepare_server points its
            // apt sources at snapshot.debian.org so packages still resolve.
            OsVersion::debian('11', 'bullseye', '2026-08-31', PhpSet::All, deployable: true),
        ];
    }

    /**
     * The PHP versions every new server gets during provisioning.
     *
     * This is the "minimum" install: 01-prepare_server.txt installs exactly
     * these on each server, with all extensions, and they are enabled and
     * running when provisioning finishes.
     *
     * Only versions that still receive upstream support belong here - 7.4, 8.0
     * and 8.1 are end of life and 8.2 leaves security support on 2026-12-31.
     * All of them remain installable on request; see optionalPhpLabels().
     *
     * Keep this in sync with the phpversion=() arrays in
     * scripts/v1/raw/01-prepare_server.txt; bin/build-compat-matrix checks it.
     *
     * @return list<string>
     */
    public static function baselinePhpLabels(): array
    {
        return ['8.3', '8.4'];
    }

    /**
     * The oldest PHP version a freshly provisioned server has available.
     */
    public static function minimumPhpLabel(): string
    {
        return self::baselinePhpLabels()[0];
    }

    /**
     * PHP versions that are installed on request rather than at provisioning
     * time, and that are left stopped and disabled until a site asks for one.
     *
     * Keep this in sync with the phpversion=() array near the end of
     * scripts/v1/raw/01-prepare_server.txt.
     *
     * @return list<string>
     */
    public static function legacyInstallPhpLabels(): array
    {
        return ['5.6', '7.1', '7.2', '7.3'];
    }

    /**
     * PHP versions available on request but not installed during provisioning.
     *
     * Everything that is neither in the baseline nor in the legacy set: the
     * end-of-life versions that servers no longer ship with, plus anything
     * newer than the baseline.
     *
     * @return list<string>
     */
    public static function optionalPhpLabels(): array
    {
        $baseline = self::baselinePhpLabels();
        $legacy = self::legacyInstallPhpLabels();
        $optional = [];

        foreach (self::php() as $php) {
            $label = $php->getLabel();

            if (!in_array($label, $baseline, true) && !in_array($label, $legacy, true)) {
                $optional[] = $label;
            }
        }

        return $optional;
    }

    /**
     * The PHP version new sites are created on, and the server CLI default.
     */
    public static function defaultAppPhpLabel(): string
    {
        return '8.3';
    }

    public static function legacyDefaultAppPhpLabel(): string
    {
        return '7.4';
    }

    /**
     * The plugin version at which each default took effect.
     *
     * Existing sites often have no wpapp_php_version stamped on them, so the
     * plugin infers their PHP version from the version of this plugin that
     * built them.  Changing defaultAppPhpLabel() alone would re-label those
     * older sites, so record when each default started instead.
     *
     * Ordered newest first.
     *
     * @return array<string, string> plugin version => PHP label
     */
    public static function defaultAppPhpHistory(): array
    {
        return [
            '7.0.0' => '8.3',
            '4.30.0' => '8.1',
        ];
    }

    /**
     * Which PHP version a site built by the given plugin version defaulted to.
     */
    public static function defaultAppPhpLabelForPluginVersion(?string $pluginVersion): string
    {
        if ($pluginVersion === null || $pluginVersion === '') {
            return self::legacyDefaultAppPhpLabel();
        }

        foreach (self::defaultAppPhpHistory() as $since => $label) {
            if (version_compare($pluginVersion, $since, '>=')) {
                return $label;
            }
        }

        return self::legacyDefaultAppPhpLabel();
    }

    /**
     * Server CLI defaults we accept without warning - every PHP version that
     * has ever been the default.
     *
     * @return list<string>
     */
    public static function acceptableServerCliDefaultPhpLabels(): array
    {
        $labels = [self::legacyDefaultAppPhpLabel(), '8.0'];

        foreach (self::defaultAppPhpHistory() as $label) {
            $labels[] = $label;
        }

        $labels = array_values(array_unique($labels));
        usort($labels, static fn (string $a, string $b): int => version_compare($a, $b));

        return $labels;
    }
}