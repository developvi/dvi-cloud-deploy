<?php

namespace DVICloudDeploy\Core\Version;

use DateTimeImmutable;
use DVICloudDeploy\Core\Version\Enum\OsFamily;
use DVICloudDeploy\Core\Version\Enum\OsStatus;
use DVICloudDeploy\Core\Version\Enum\PhpRepo;
use DVICloudDeploy\Core\Version\Enum\PhpSet;
use DVICloudDeploy\Core\Version\Enum\WebRepo;

final class OsVersion
{
    /**
     * @param list<string> $extraPackages
     * @param array<string, scalar|null> $quirks
     * @param PhpSet|list<string> $supportedPhp
     * @param list<string> $supportedWebservers
     * @param array<string, string> $providerImages
     */
    public function __construct(
        private readonly string $slug,
        private readonly OsFamily $family,
        private readonly string $release,
        private readonly string $codename,
        private readonly string $label,
        private readonly string $supportEndsAt,
        private readonly OsStatus $status,
        private readonly PhpRepo $phpRepository,
        private readonly WebRepo $webserverRepository,
        private readonly array $extraPackages,
        private readonly array $quirks,
        private readonly PhpSet|array $supportedPhp,
        private readonly array $supportedWebservers,
        private readonly array $providerImages = [],
        private readonly bool $deployable = true,
        private readonly int $sortOrder = 100,
        private readonly ?string $disableOptionKey = null,
        private readonly ?string $enableOptionKey = null,
        private readonly ?string $selectLabelOverride = null,
    ) {
    }

    /** @param array<string, scalar|null>|null $quirks */
    public static function ubuntu(
        string $release,
        string $codename,
        string $supportEndsAt,
        int $sortOrder,
        PhpSet $supportedPhp,
        ?string $disableOptionKey = null,
        ?string $enableOptionKey = null,
        ?array $quirks = null,
        ?string $selectLabelOverride = null,
        bool $deployable = true,
        ?PhpRepo $phpRepository = null,
        ?OsStatus $status = null,
    ): self {
        [$major, $minor] = explode('.', $release);

        return new self(
            slug: 'ubuntu' . str_replace('.', '', $release) . 'lts',
            family: OsFamily::Ubuntu,
            release: $release,
            codename: $codename,
            label: 'Ubuntu ' . $release . ' LTS',
            supportEndsAt: $supportEndsAt,
            status: $status ?? OsStatus::Supported,
            phpRepository: $phpRepository ?? PhpRepo::OndrejPpa,
            // nginx now comes from the distribution; the ondrej nginx PPAs were removed.
            webserverRepository: WebRepo::None,
            // software-properties-common is only needed to add a PPA.
            extraPackages: ($phpRepository ?? PhpRepo::OndrejPpa) === PhpRepo::OndrejPpa ? ['software-properties-common'] : [],
            quirks: $quirks ?? ['ssh_service' => 'ssh', 'cgroup' => 'v2'],
            supportedPhp: $supportedPhp,
            supportedWebservers: ['nginx', 'ols'],
            providerImages: ['digital-ocean' => sprintf('ubuntu-%s-%s-x64', $major, $minor)],
            sortOrder: $sortOrder,
            disableOptionKey: $disableOptionKey,
            enableOptionKey: $enableOptionKey,
            selectLabelOverride: $selectLabelOverride,
            deployable: $deployable,
        );
    }

    public static function debian(string $release, string $codename, string $supportEndsAt, PhpSet $supportedPhp, bool $deployable = false): self
    {
        return new self(
            slug: 'debian' . $release,
            family: OsFamily::Debian,
            release: $release,
            codename: $codename,
            label: 'Debian ' . $release,
            supportEndsAt: $supportEndsAt,
            status: OsStatus::Experimental,
            phpRepository: PhpRepo::Sury,
            webserverRepository: WebRepo::Sury,
            extraPackages: ['lsb-release', 'ca-certificates', 'curl', 'apt-transport-https', 'gnupg'],
            quirks: ['ssh_service' => 'ssh', 'cgroup' => 'v2'],
            supportedPhp: $supportedPhp,
            supportedWebservers: ['nginx'],
            deployable: $deployable,
        );
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getFamily(): OsFamily
    {
        return $this->family;
    }

    public function getRelease(): string
    {
        return $this->release;
    }

    public function getCodename(): string
    {
        return $this->codename;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getSelectLabel(): string
    {
        return $this->selectLabelOverride ?? $this->label;
    }

    /**
     * The catalog's hand-written dropdown label, if it set one.
     */
    public function getSelectLabelOverride(): ?string
    {
        return $this->selectLabelOverride;
    }

    public function getSupportEndsAt(): string
    {
        return $this->supportEndsAt;
    }

    /**
     * How long before support ends do we start steering people away from an OS.
     */
    public const APPROACHING_EOL_MONTHS = 12;

    /**
     * The effective status, taking today's date into account.
     *
     * An OS whose support window has already closed reports end_of_life, and one
     * that is inside APPROACHING_EOL_MONTHS of that date reports deprecated -
     * it still works, but new servers should not be built on it.  Statuses set
     * explicitly in the catalog (end_of_life, experimental) are left alone.
     */
    public function getStatus(?DateTimeImmutable $today = null): OsStatus
    {
        if ($this->status !== OsStatus::Supported) {
            return $this->status;
        }

        $today ??= new DateTimeImmutable('today');
        $end = new DateTimeImmutable($this->supportEndsAt);

        if ($today > $end) {
            return OsStatus::EndOfLife;
        }

        if ($today >= $end->modify('-' . self::APPROACHING_EOL_MONTHS . ' months')) {
            return OsStatus::Deprecated;
        }

        return OsStatus::Supported;
    }

    /**
     * Should this OS be avoided for new servers?
     */
    public function isRecommended(?DateTimeImmutable $today = null): bool
    {
        return $this->getStatus($today) === OsStatus::Supported;
    }

    /**
     * Whole months left in the support window; negative once it has passed.
     */
    public function monthsOfSupportLeft(?DateTimeImmutable $today = null): int
    {
        $today ??= new DateTimeImmutable('today');
        $diff = $today->diff(new DateTimeImmutable($this->supportEndsAt));
        $months = ($diff->y * 12) + $diff->m;

        return $diff->invert === 1 ? -$months : $months;
    }

    public function getPhpRepository(): PhpRepo
    {
        return $this->phpRepository;
    }

    public function getWebserverRepository(): WebRepo
    {
        return $this->webserverRepository;
    }

    /** @return list<string> */
    public function getExtraPackages(): array
    {
        return $this->extraPackages;
    }

    /** @return array<string, scalar|null> */
    public function getQuirks(): array
    {
        return $this->quirks;
    }

    /** @return list<string> */
    public function getSupportedPhp(): array
    {
        if ($this->supportedPhp instanceof PhpSet) {
            return $this->supportedPhp->slugs(VersionRegistry::allPhp());
        }

        return $this->supportedPhp;
    }

    /** @return list<string> */
    public function getSupportedWebservers(): array
    {
        return $this->supportedWebservers;
    }

    /** @return array<string, string> */
    public function getProviderImages(): array
    {
        return $this->providerImages;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function getProviderImage(string $provider): ?string
    {
        return $this->providerImages[$provider] ?? null;
    }

    /**
     * Can a server be provisioned on this OS at all?
     *
     * False means the OS is kept in the catalog for reference only - typically
     * because no repository still serves the packages it would need.
     */
    public function isDeployable(): bool
    {
        return $this->deployable;
    }

    public function isVisibleInDeployList(): bool
    {
        if (!$this->deployable) {
            return false;
        }

        if ($this->disableOptionKey !== null && function_exists('wpcd_get_option') && (bool) wpcd_get_option($this->disableOptionKey)) {
            return false;
        }

        if ($this->enableOptionKey !== null) {
            return function_exists('wpcd_get_option') && (bool) wpcd_get_option($this->enableOptionKey);
        }

        return true;
    }
}
