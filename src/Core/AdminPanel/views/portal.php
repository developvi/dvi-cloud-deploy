<?php
/**
 * Admin Panel portal shell.
 *
 * @var string $brand
 * @var string $user_name
 * @var string $logout_url
 * @var array  $nav
 * @var string $content
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="dvicd-ap-portal-shell">
	<header class="dvicd-ap-portal-header">
		<a class="dvicd-ap-portal-brand" href="<?php echo esc_url(home_url('/')); ?>">
			<?php echo esc_html($brand); ?>
		</a>
		<div class="dvicd-ap-portal-user">
			<span><?php echo esc_html($user_name); ?></span>
			<a href="<?php echo esc_url($logout_url); ?>"><?php esc_html_e('Log Out', 'wpcd'); ?></a>
		</div>
	</header>
	<div class="dvicd-ap-portal-body">
		<aside class="dvicd-ap-portal-sidebar">
			<ul class="dvicd-ap-portal-nav">
				<?php foreach ($nav as $item) : ?>
					<li>
						<a
							href="<?php echo esc_url($item['url']); ?>"
							class="<?php echo !empty($item['active']) ? 'is-active' : ''; ?>"
						>
							<?php if (!empty($item['icon'])) : ?>
								<i class="<?php echo esc_attr($item['icon']); ?>"></i>
							<?php endif; ?>
							<span><?php echo esc_html($item['label']); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</aside>
		<main class="dvicd-ap-portal-content">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</main>
	</div>
</div>
