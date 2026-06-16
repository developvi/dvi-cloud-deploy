<?php

namespace DVICloudDeploy\App\FaqHelp\Support;

use DVICloudDeploy\App\FaqHelp\Admin\FaqHelpPage;

/**
 * Legacy WPCD FAQ/Help aliases.
 * Prefer DVICD_HIDE_HELP_TAB, dvicd_faq_and_help, and dvicd_settings_help_tab_text.
 */
class Deprecated
{
    private const VERSION = '6.3.0';

    public const LEGACY_PAGE_SLUG = 'wpcd_faq_and_help';

    public static function register(): void
    {
        self::bridgeHideHelpTabConstant();

        add_action('admin_menu', [self::class, 'registerLegacyPageSlug'], 21);
        add_action('admin_init', [self::class, 'redirectLegacyPageSlug']);
    }

    /**
     * Map WPCD_HIDE_HELP_TAB → DVICD_HIDE_HELP_TAB and emit a deprecation notice.
     */
    private static function bridgeHideHelpTabConstant(): void
    {
        if (defined('DVICD_HIDE_HELP_TAB')) {
            return;
        }

        if (!defined('WPCD_HIDE_HELP_TAB')) {
            return;
        }

        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
        trigger_error(
            sprintf(
                'Constant WPCD_HIDE_HELP_TAB is <strong>deprecated</strong> since version %1$s! Use DVICD_HIDE_HELP_TAB instead.',
                self::VERSION
            ),
            E_USER_DEPRECATED
        );

        define('DVICD_HIDE_HELP_TAB', WPCD_HIDE_HELP_TAB);
    }

    /**
     * Keep the old submenu slug registered (then hide it) so bookmarks still resolve.
     */
    public static function registerLegacyPageSlug(): void
    {
        if (self::isHelpTabHidden()) {
            return;
        }

        $parent = 'edit.php?post_type=wpcd_app_server';

        add_submenu_page(
            $parent,
            __('FAQ & Help', 'wpcd'),
            __('FAQ & Help', 'wpcd'),
            'manage_options',
            self::LEGACY_PAGE_SLUG,
            [self::class, 'renderLegacyPage']
        );

        // Hide from the menu UI while keeping the page registered for redirects.
        global $submenu;
        if (!empty($submenu[$parent]) && is_array($submenu[$parent])) {
            foreach ($submenu[$parent] as $index => $item) {
                if (isset($item[2]) && self::LEGACY_PAGE_SLUG === $item[2]) {
                    unset($submenu[$parent][$index]);
                }
            }
        }
    }

    /**
     * Redirect old ?page=wpcd_faq_and_help to ?page=dvicd_faq_and_help.
     */
    public static function redirectLegacyPageSlug(): void
    {
        if (!is_admin() || empty($_GET['page']) || self::LEGACY_PAGE_SLUG !== $_GET['page']) {
            return;
        }

        _deprecated_argument(
            'page=' . self::LEGACY_PAGE_SLUG,
            self::VERSION,
            'page=' . FaqHelpPage::PAGE_SLUG
        );

        wp_safe_redirect(
            add_query_arg(
                [
                    'post_type' => 'wpcd_app_server',
                    'page'      => FaqHelpPage::PAGE_SLUG,
                ],
                admin_url('edit.php')
            )
        );
        exit;
    }

    /**
     * Fallback render if redirect does not run.
     *
     * @deprecated 6.3.0
     */
    public static function renderLegacyPage(): void
    {
        _deprecated_function(__METHOD__, self::VERSION, FaqHelpPage::class . '::render');
        FaqHelpPage::render();
    }

    public static function isHelpTabHidden(): bool
    {
        return defined('DVICD_HIDE_HELP_TAB') && DVICD_HIDE_HELP_TAB;
    }
}
