<?php

namespace DVICloudDeploy\Core\AdminPanel\Contract;

interface TabGroupRepositoryInterface
{
    /**
     * @param string $context site|server
     * @return array{groups: array, tabs: array}
     */
    public function get(string $context): array;
}
