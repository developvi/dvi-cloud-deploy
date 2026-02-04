<?php

namespace DVICloudDeploy\Core\AdminPanel\Admin;

use DVICloudDeploy\Core\AdminPanel\Support\StyleDetector;

class BodyClass
{
    public static function register()
    {
        add_filter('admin_body_class', [self::class, 'admin']);
        add_filter('body_class', [self::class, 'front']);
    }

    public static function admin(string $classes): string
    {
        if (StyleDetector::shouldLoadForScreen()) {
            $classes .= ' dvicd-ap-active';
        }

        return $classes;
    }

    public static function front(array $classes): array
    {
        if (StyleDetector::shouldLoadPublic()) {
            $classes[] = 'dvicd-ap-active';
            $classes[] = 'dvicd-ap-portal';
        }

        return $classes;
    }
}
