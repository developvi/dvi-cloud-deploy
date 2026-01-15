<?php

namespace DVICloudDeploy\Core\AdminPanel\Contract;

interface StatsProviderInterface
{
    /**
     * @return array<int, array{label: string, value: string, status?: string}>
     */
    public function collect(int $postId, string $context): array;
}
