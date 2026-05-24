<?php
/**
 * Stats sidebar items.
 *
 * @var array  $items
 * @var string $title
 * @var bool   $embedded When true, render inside Site/Server Overview (no separate title).
 */

if (!defined('ABSPATH')) {
    exit;
}

$embedded = !empty($embedded);
$aside_class = 'dvicd-ap-stats' . ($embedded ? ' dvicd-ap-stats--embedded' : '');
?>
<aside class="<?php echo esc_attr($aside_class); ?>">
	<?php if (!$embedded) : ?>
		<h3 class="dvicd-ap-stats__title"><?php echo esc_html($title); ?></h3>
	<?php endif; ?>
	<?php foreach ($items as $item) : ?>
		<div class="dvicd-ap-stats__row">
			<span class="dvicd-ap-stats__label"><?php echo esc_html($item['label']); ?></span>
			<span class="dvicd-ap-stats__value">
				<?php if (!empty($item['status'])) : ?>
					<span class="dvicd-ap-badge dvicd-ap-badge--<?php echo esc_attr($item['status']); ?>">
						<?php echo esc_html($item['value']); ?>
					</span>
				<?php else : ?>
					<?php echo esc_html($item['value']); ?>
				<?php endif; ?>
			</span>
		</div>
	<?php endforeach; ?>
</aside>
