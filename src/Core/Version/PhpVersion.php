<?php

namespace DVICloudDeploy\Core\Version;

use DVICloudDeploy\Core\Version\Enum\PhpStatus;
use DateTimeImmutable;

final class PhpVersion
{
    /**
     * @param list<string> $supportedWebservers
     */
    public function __construct(
        private readonly string $label,
        private readonly string $minPluginVersion,
        private readonly string $securitySupportEndsAt,
        private readonly ?string $activeSupportEndsAt = null,
        private readonly bool $legacyInstall = false,
        private readonly bool $usesManualInstallMeta = true,
        private readonly array $supportedWebservers = ['nginx', 'ols'],
    ) {
    }

    public static function define(
        string $label,
        string $minPluginVersion,
        string $securitySupportEndsAt,
        ?string $activeSupportEndsAt = null,
        ?bool $legacyInstall = null,
        ?bool $usesManualInstallMeta = null,
        ?array $supportedWebservers = null,
    ): self {
        $legacyInstall ??= PhpLifecycle::isLegacyInstall($label);

        return new self(
            label: $label,
            minPluginVersion: $minPluginVersion,
            securitySupportEndsAt: $securitySupportEndsAt,
            activeSupportEndsAt: $activeSupportEndsAt,
            legacyInstall: $legacyInstall,
            usesManualInstallMeta: $usesManualInstallMeta ?? PhpLifecycle::usesManualInstallMeta($label),
            supportedWebservers: $supportedWebservers ?? PhpLifecycle::supportedWebservers($label),
        );
    }

    public static function resolveStatus(
        string $securitySupportEndsAt,
        ?string $activeSupportEndsAt = null,
        ?DateTimeImmutable $today = null,
    ): PhpStatus {
        $today ??= new DateTimeImmutable('today');
        $securityEnd = new DateTimeImmutable($securitySupportEndsAt);
        $activeEnd = $activeSupportEndsAt !== null
            ? new DateTimeImmutable($activeSupportEndsAt)
            : $securityEnd;

        if ($today > $securityEnd) {
            return PhpStatus::EndOfLife;
        }

        if ($today > $activeEnd) {
            return PhpStatus::SecurityOnly;
        }

        return PhpStatus::Supported;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getSlug(): string
    {
        return 'php' . str_replace('.', '', $this->label);
    }

    public function getFpmService(): string
    {
        return 'php' . $this->label . '-fpm';
    }

    public function getFpmLogPath(): string
    {
        return '/var/log/' . $this->getFpmService() . '.log';
    }

    public function getMetaKey(): string
    {
        return 'wpcd_server_' . $this->getSlug() . '_installed';
    }

    public function getMinPluginVersion(): string
    {
        return $this->minPluginVersion;
    }

    public function getStatus(): PhpStatus
    {
        return self::resolveStatus($this->securitySupportEndsAt, $this->activeSupportEndsAt);
    }

    /**
     * How long before security support ends we start warning about a version.
     */
    public const APPROACHING_EOL_MONTHS = 6;

    /**
     * Is this version inside its last few months of security support?
     *
     * Already end-of-life versions return false - they are past warning.
     */
    public function isApproachingEndOfLife(?DateTimeImmutable $today = null): bool
    {
        $today ??= new DateTimeImmutable('today');
        $end = new DateTimeImmutable($this->securitySupportEndsAt);

        if ($today > $end) {
            return false;
        }

        return $today >= $end->modify('-' . self::APPROACHING_EOL_MONTHS . ' months');
    }

    /**
     * Whole months of security support left; negative once it has passed.
     */
    public function monthsOfSupportLeft(?DateTimeImmutable $today = null): int
    {
        $today ??= new DateTimeImmutable('today');
        $diff = $today->diff(new DateTimeImmutable($this->securitySupportEndsAt));
        $months = ($diff->y * 12) + $diff->m;

        return $diff->invert === 1 ? -$months : $months;
    }

    /**
     * Should a new site be put on this version?
     */
    public function isRecommended(?DateTimeImmutable $today = null): bool
    {
        return $this->getStatus() === PhpStatus::Supported;
    }

    public function getActiveSupportEndsAt(): ?string
    {
        return $this->activeSupportEndsAt;
    }

    public function getSecuritySupportEndsAt(): string
    {
        return $this->securitySupportEndsAt;
    }

    /** @deprecated Use getSecuritySupportEndsAt() */
    public function getSupportEndsAt(): string
    {
        return $this->securitySupportEndsAt;
    }

    public function isLegacyInstall(): bool
    {
        return $this->legacyInstall;
    }

    /** @deprecated Use isLegacyInstall() */
    public function isLegacy(): bool
    {
        return $this->isLegacyInstall();
    }

    public function usesManualInstallMeta(): bool
    {
        return $this->usesManualInstallMeta;
    }

    /** @return list<string> */
    public function getSupportedWebservers(): array
    {
        return $this->supportedWebservers;
    }

    public function getInstallScript(): string
    {
        if ($this->isLegacyInstall() || version_compare($this->label, '8.1', '<')) {
            return 'run_upgrade_install_old_php_version.txt';
        }

        return 'run_upgrade_install_php.txt';
    }

    public function getInstallSuccessPhrase(): string
    {
        return 'PHP ' . $this->label . ' has been installed';
    }

    /** Raw bash script basename without extension, e.g. 1110-upgrade_install_php */
    public function getInstallScriptRawBasename(): ?string
    {
        if ($this->isLegacyInstall() || version_compare($this->label, '8.1', '<')) {
            return null;
        }

        return '1110-upgrade_install_php';
    }

    /**
     * @param callable(int, string, bool): mixed $metaReader
     */
    public function isInstalledOnServer(int $serverId, callable $metaReader): bool
    {
        $initial = (string) $metaReader($serverId, 'wpcd_server_plugin_initial_version', true);
        if ($initial !== '' && version_compare($initial, $this->minPluginVersion, '>=')) {
            return true;
        }

        if (!$this->usesManualInstallMeta) {
            return false;
        }

        return (bool) $metaReader($serverId, $this->getMetaKey(), true);
    }
}
