<?php

namespace DVICloudDeploy\App\FaqHelp\Admin;

use DVICloudDeploy\App\FaqHelp\Support\Deprecated;

/**
 * FAQ & Help admin page (docs.dvicd.com links).
 */
class FaqHelpPage
{
    public const PAGE_SLUG = 'dvicd_faq_and_help';

    public static function register(): void
    {
        add_action('admin_menu', [self::class, 'addMenuPage'], 20);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueAssets']);
    }

    public static function addMenuPage(): void
    {
        if (Deprecated::isHelpTabHidden()) {
            return;
        }

        add_submenu_page(
            'edit.php?post_type=wpcd_app_server',
            __('FAQ & Help', 'wpcd'),
            __('FAQ & Help', 'wpcd'),
            'manage_options',
            self::PAGE_SLUG,
            [self::class, 'render'],
            20
        );
    }

    public static function enqueueAssets(string $hook): void
    {
        if ('wpcd_app_server_page_' . self::PAGE_SLUG !== $hook) {
            return;
        }

        wp_enqueue_style(
            'dvicd-admin-settings',
            DVICD_URL . 'assets/css/dvicd-admin-settings.css',
            [],
            defined('WPCD_SCRIPTS_VERSION') ? WPCD_SCRIPTS_VERSION : DVICD_VERSION
        );
    }

    public static function render(): void
    {
        $docs = 'https://docs.dvicd.com';

        $help  = '<div class="wrap dvicd-faq-help">';
        $help .= '<header class="dvicd-faq-help__header">';
        $help .= '<h1 class="dvicd-faq-help__title">' . esc_html__('FAQ & Help', 'wpcd') . '</h1>';
        $help .= '<p class="dvicd-faq-help__intro">' . esc_html__('Links to commonly requested documentation on the DVICloudDeploy docs site.', 'wpcd') . '</p>';
        $help .= '<div class="dvicd-faq-help__actions">';
        $help .= '<a class="dvicd-faq-help__btn dvicd-faq-help__btn--primary" href="' . esc_url($docs . '/01-cloud-deploy-core/introduction-installation-and-quick-start-guide/') . '" target="_blank" rel="noopener noreferrer">' . esc_html__('View All Documentation', 'wpcd') . '</a>';
        $help .= '<a class="dvicd-faq-help__btn" href="' . esc_url($docs . '/faq/') . '" target="_blank" rel="noopener noreferrer">' . esc_html__('FAQ', 'wpcd') . '</a>';
        $help .= '<a class="dvicd-faq-help__btn" href="' . esc_url($docs . '/changelog/') . '" target="_blank" rel="noopener noreferrer">' . esc_html__('Changelog', 'wpcd') . '</a>';
        $help .= '<a class="dvicd-faq-help__btn" href="https://discord.com/invite/bpBWNrxr" target="_blank" rel="noopener noreferrer">' . esc_html__('Discord Community', 'wpcd') . '</a>';
        $help .= '</div></header>';

        $help .= '<div class="dvicd-faq-help__grid">';

        $help .= self::card(
            __('Getting Started', 'wpcd'),
            [
                $docs . '/' => __('Documentation home', 'wpcd'),
                $docs . '/01-cloud-deploy-core/quick-start/' => __('Quick start', 'wpcd'),
                $docs . '/01-cloud-deploy-core/introduction-installation-and-quick-start-guide/' => __('Installation & quick start guide', 'wpcd'),
                $docs . '/01-cloud-deploy-core/requirements/' => __('Requirements', 'wpcd'),
                $docs . '/01-cloud-deploy-core/release-notes/' => __('Release notes', 'wpcd'),
            ]
        );

        $help .= self::card(
            __('Tasks', 'wpcd'),
            [
                $docs . '/02-User-guide/deploy-a-server/' => __('Deploy a new server', 'wpcd'),
                $docs . '/02-User-guide/deploy-a-new-wordpress-site/' => __('Deploy a new WordPress site', 'wpcd'),
                $docs . '/02-User-guide/managing-ssl-certificates/' => __('SSL certificates', 'wpcd'),
                $docs . '/03-Adminstrator-guide/backups-with-aws-s3/' => __('Backups with AWS S3', 'wpcd'),
                $docs . '/02-User-guide/cloning-(copying)-sites/' => __('Cloning sites', 'wpcd'),
                $docs . '/02-User-guide/managing-sftp-users/' => __('sFTP users', 'wpcd'),
                $docs . '/02-User-guide/page-cache/' => __('Page cache', 'wpcd'),
            ]
        );

        $help .= self::card(
            __('Troubleshooting', 'wpcd'),
            [
                $docs . '/06-Troubleshooting-and-FAQs/reasons-servers-fail-to-deploy/' => __('Common server deployment issues', 'wpcd'),
                $docs . '/06-Troubleshooting-and-FAQs/reasons-sites-fail-to-deploy/' => __('Reasons sites fail to deploy', 'wpcd'),
                $docs . '/06-Troubleshooting-and-FAQs/common-server-deployment-issues-and-error-messages/' => __('Deployment error messages', 'wpcd'),
                $docs . '/06-Troubleshooting-and-FAQs/server-faqs/' => __('Server FAQs', 'wpcd'),
                $docs . '/faq/' => __('FAQ', 'wpcd'),
            ]
        );

        $help .= self::card(
            __('Reading', 'wpcd'),
            [
                $docs . '/18-Wordpress-Saas/wordpress-saas-introduction/' => __('WordPress SaaS introduction', 'wpcd'),
                $docs . '/13-Articales/sizing-your-wordpress-servers/' => __('WordPress server sizing guide', 'wpcd'),
                $docs . '/13-Articales/deployment-options-for-dvi/' => __('Deployment options', 'wpcd'),
                $docs . '/13-Articales/all-about-wp-crons/' => __('All about WP crons', 'wpcd'),
            ]
        );

        $help .= self::card(
            __('Teams', 'wpcd'),
            [
                $docs . '/04-Teams/introduction-to-teams/' => __('Introduction', 'wpcd'),
                $docs . '/04-Teams/preparing-users-for-a-team/' => __('Preparing users for a team', 'wpcd'),
                $docs . '/04-Teams/creating-teams/' => __('Creating teams', 'wpcd'),
                $docs . '/04-Teams/assigning-teams/' => __('Assigning teams', 'wpcd'),
                $docs . '/04-Teams/roles-and-capabilities/' => __('Roles and capabilities', 'wpcd'),
            ]
        );

        $help .= self::card(
            __('Components', 'wpcd'),
            [
                $docs . '/23-Multisite/multisite-introduction/' => __('Multisite', 'wpcd'),
                $docs . '/25-Server%20Sync/server-sync-introduction/' => __('Server Sync', 'wpcd'),
                $docs . '/03-Adminstrator-guide/custom-servers-(bring-your-own-server)/' => __('Custom servers (BYOS)', 'wpcd'),
                $docs . '/03-Adminstrator-guide/virtual-cloud-providers/' => __('Virtual providers', 'wpcd'),
            ]
        );

        $help .= self::card(
            __('Developers', 'wpcd'),
            [
                $docs . '/05-Developers-Notes/custom-post-types-used-by-dvi/' => __('Custom post types', 'wpcd'),
                $docs . '/05-Developers-Notes/ssh-execution-models/' => __('SSH execution models', 'wpcd'),
                $docs . '/12-Developer-Tips/using-visual-studio-code-with-developvideploy/' => __('Developer tips', 'wpcd'),
                $docs . '/12-Developer-Tips/customizing-front-end-styles/' => __('Customizing front-end styles', 'wpcd'),
                $docs . '/05-Developers-Notes/filter-hook-wpcd_settings_help_tab_text/' => __('Help tab filter hook', 'wpcd'),
            ]
        );

        $help .= '</div></div>';

        $help = apply_filters('dvicd_settings_help_tab_text', $help);
        echo apply_filters_deprecated('wpcd_settings_help_tab_text', [$help], '6.3.0', 'dvicd_settings_help_tab_text');
    }

    /**
     * @param array<string, string> $links
     */
    private static function card(string $title, array $links): string
    {
        $card  = '<section class="dvicd-faq-help__card">';
        $card .= '<h2 class="dvicd-faq-help__card-title">' . esc_html($title) . '</h2>';
        $card .= '<ul class="dvicd-faq-help__list">';
        foreach ($links as $url => $label) {
            $card .= '<li><a class="dvicd-faq-help__link" href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer">' . esc_html($label) . '</a></li>';
        }
        $card .= '</ul></section>';

        return $card;
    }
}
