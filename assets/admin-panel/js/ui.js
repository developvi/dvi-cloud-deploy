(function ($) {
	'use strict';

	var cfg = window.DvicdAdminPanel || {};
	var hashPrefix = cfg.hashPrefix || 'ap';

	function getMap() {
		return cfg.tabGroups || { groups: {}, tabs: {} };
	}

	function findTabsRoot($scope) {
		return $scope.find('.rwmb-tabs').first();
	}

	function buildToolbar($tabs) {
		var $existing = $tabs.children('.dvicd-ap-toolbar');
		if ($existing.length) {
			return $existing;
		}

		var $toolbar = $('<div class="dvicd-ap-toolbar" />');
		var $back = $('<button type="button" class="dvicd-ap-back" />')
			.text(cfg.backLabel || 'Back to Home');

		// Isolated form + honeypot so browsers do not treat the filter as a login field.
		var $form = $('<form class="dvicd-ap-search-form" autocomplete="off" novalidate="novalidate" />')
			.on('submit', function (e) {
				e.preventDefault();
			});
		var $honeypot = $('<input type="text" class="dvicd-ap-search-honeypot" tabindex="-1" aria-hidden="true" />')
			.attr({
				autocomplete: 'username',
				name: 'username_' + Date.now(),
				value: '',
			});
		var $search = $('<input type="text" class="dvicd-ap-search" />')
			.attr({
				placeholder: cfg.searchLabel || 'Search features…',
				autocomplete: 'new-password',
				autocorrect: 'off',
				autocapitalize: 'none',
				spellcheck: 'false',
				inputmode: 'search',
				role: 'searchbox',
				readonly: 'readonly',
				'data-lpignore': 'true',
				'data-1p-ignore': 'true',
				'data-bwignore': 'true',
				'data-form-type': 'other',
				'aria-autocomplete': 'none',
			})
			.on('focus', function () {
				var el = this;
				el.removeAttribute('readonly');
				// Re-focus after Chrome drops readonly so the caret appears.
				window.setTimeout(function () {
					el.focus();
				}, 0);
			})
			.on('blur', function () {
				if (!($(this).val()).trim()) {
					$(this).attr('readonly', 'readonly');
				}
			});

		$form.append($honeypot, $search);
		$toolbar.append($back, $form);
		$tabs.prepend($toolbar);

		// Strip any delayed browser autofill.
		window.setTimeout(function () {
			if (!$search.data('dvicdTyped') && $search.val()) {
				$search.val('').trigger('input');
			}
			$honeypot.val('');
		}, 250);
		window.setTimeout(function () {
			if (!$search.data('dvicdTyped') && $search.val()) {
				$search.val('').trigger('input');
			}
			$honeypot.val('');
		}, 1000);

		return $toolbar;
	}

	function resolveGroup(slug, map) {
		if (map.tabs && map.tabs[slug] && map.tabs[slug].group) {
			return map.tabs[slug].group;
		}
		return '';
	}

	function slugifyKey(str) {
		return String(str || '')
			.toLowerCase()
			.replace(/[^a-z0-9]+/g, '-')
			.replace(/^-+|-+$/g, '') || 'item';
	}

	function metaboxTitle($tabs) {
		var $box = $tabs.closest('.postbox');
		if (!$box.length) {
			return '';
		}
		var raw = $box.children('.postbox-header').find('.hndle').first().text()
			|| $box.children('h2.hndle, .hndle').first().text()
			|| '';
		return (String(raw).replace(/\s+/g, ' ')).trim();
	}

	function ensureGroup(groups, $home, key, meta) {
		if (groups[key]) {
			return groups[key];
		}
		groups[key] = createGroup(key, meta || {});
		$home.append(groups[key].$el);
		return groups[key];
	}

	function normalizeIcon(cls) {
		return String(cls || 'fas fa-cube')
			.replace(/\bfad\b/g, 'fas')
			.replace(/\bfa-duotone\b/g, '')
			.replace(/\bfa-info-circle\b/g, 'fa-circle-info')
			.replace(/\bfa-cog\b/g, 'fa-gear')
			.replace(/\bfa-cogs\b/g, 'fa-gears')
			.replace(/\bfa-home\b/g, 'fa-house')
			.replace(/\bfa-sync-alt\b/g, 'fa-rotate')
			.replace(/\bfa-sync\b/g, 'fa-rotate')
			.replace(/\bfa-shield-alt\b/g, 'fa-shield-halved')
			.replace(/\bfa-tachometer-alt\b/g, 'fa-gauge-high')
			.replace(/\bfa-ellipsis-h\b/g, 'fa-ellipsis')
			.replace(/\bfa-cloud-upload-alt\b/g, 'fa-cloud-arrow-up')
			.replace(/\bfa-exchange-alt\b/g, 'fa-right-left')
			.replace(/\bfa-file-alt\b/g, 'fa-file-lines')
			.replace(/\bfa-chart-bar\b/g, 'fa-chart-column')
			.replace(/\bfa-sliders-h\b/g, 'fa-sliders')
			.replace(/\s+/g, ' ')
			.trim();
	}

	function resolveIcon(slug, $navItem, map) {
		if (map.tabs && map.tabs[slug] && map.tabs[slug].icon) {
			return normalizeIcon(map.tabs[slug].icon);
		}
		var $icon = $navItem.find('i').first();
		if ($icon.length) {
			return normalizeIcon($icon.attr('class'));
		}
		return 'fas fa-cube';
	}

	function refreshIcons(root) {
		var node = root && root.jquery ? root.get(0) : root;
		if (!node) {
			return;
		}
		if (window.FontAwesome && FontAwesome.dom && typeof FontAwesome.dom.i2svg === 'function') {
			FontAwesome.dom.i2svg({ node: node });
		}
	}

	function sortedGroupKeys(map) {
		var groups = map.groups || {};
		return Object.keys(groups).sort(function (a, b) {
			return (groups[a].order || 100) - (groups[b].order || 100);
		});
	}

	function panelSlug($el) {
		return String($el.attr('data-panel') || $el.data('panel') || '');
	}

	function findPanelBySlug($tabs, slug) {
		var $panels = $tabs.children('.rwmb-tab-panels').children('.rwmb-tab-panel');
		if (!$panels.length) {
			$panels = $tabs.find('> .rwmb-tab-panels > .rwmb-tab-panel');
		}
		if (!$panels.length) {
			$panels = $tabs.find('.rwmb-tab-panels').first().children('.rwmb-tab-panel');
		}

		var $match = $panels.filter(function () {
			return panelSlug($(this)) === slug;
		});
		if ($match.length) {
			return $match.first();
		}

		return $panels.filter(function () {
			var cls = ' ' + (this.className || '') + ' ';
			return cls.indexOf(' rwmb-tab-panel-' + slug + ' ') !== -1;
		}).first();
	}

	function buildGrid($tabs) {
		var map = getMap();
		var $nav = $tabs.children('.rwmb-tab-nav');
		var $toolbar = buildToolbar($tabs);
		var $home = $('<div class="dvicd-ap-home" />');
		var $empty = $('<div class="dvicd-ap-empty" />').text(cfg.searchEmpty || 'No matching features');
		var groups = {};
		var boxTitle = metaboxTitle($tabs);
		var $postbox = $tabs.closest('.postbox');
		var isPrimary = $postbox.hasClass('wpcd-wpapp-actions')
			|| $tabs.closest('.wpcd-wpapp-actions').length > 0;

		$home.append($empty);

		sortedGroupKeys(map).forEach(function (key) {
			// Skip generic Other — unnamed tabs get real metabox/tab titles instead.
			if (key === 'other') {
				return;
			}
			ensureGroup(groups, $home, key, map.groups[key]);
		});

		$nav.children('li').each(function () {
			var $li = $(this);
			var slug = panelSlug($li);
			if (!slug) {
				return;
			}

			var label = ($li.find('a').clone().children().remove().end().text()).trim() || slug;
			var icon = resolveIcon(slug, $li, map);
			var groupKey = resolveGroup(slug, map);

			if (!groupKey || !groups[groupKey]) {
				if (!isPrimary && boxTitle) {
					groupKey = 'metabox-' + slugifyKey(boxTitle);
					ensureGroup(groups, $home, groupKey, {
						label: boxTitle,
						icon: 'fas fa-layer-group',
					});
				} else {
					groupKey = 'tab-' + slugifyKey(slug);
					ensureGroup(groups, $home, groupKey, {
						label: label,
						icon: icon,
					});
				}
			}

			var openLabel = (cfg.openLabel || 'Open');
			var $card = $('<div class="dvicd-ap-card" role="button" tabindex="0" />')
				.attr('data-panel', slug)
				.attr('data-label', label.toLowerCase())
				.append($('<span class="dvicd-ap-card__icon" aria-hidden="true" />').append($('<i />').addClass(icon)))
				.append($('<span class="dvicd-ap-card__label" />').text(label))
				.append($('<span class="dvicd-ap-card__hint" />').text(openLabel));

			groups[groupKey].$grid.append($card);
			groups[groupKey].count += 1;
		});

		Object.keys(groups).forEach(function (key) {
			var group = groups[key];
			if (!group.count) {
				group.$el.addClass('is-hidden');
				return;
			}
			group.$count.text(String(group.count));
		});

		$toolbar.after($home);
		$tabs.addClass('dvicd-ap-ready');
		refreshIcons($tabs.get(0));
		return { $home: $home, $toolbar: $toolbar };
	}

	function createGroup(key, meta) {
		meta = meta || {};
		var $el = $('<section class="dvicd-ap-group" />').attr('data-group', key);
		var $head = $('<div class="dvicd-ap-group__head" />');
		var $title = $('<h3 class="dvicd-ap-group__title" />');
		if (meta.icon) {
			$title.append(
				$('<span class="dvicd-ap-group__icon" aria-hidden="true" />')
					.append($('<i />').addClass(normalizeIcon(meta.icon)))
			);
		}
		$title.append($('<span class="dvicd-ap-group__name" />').text(meta.label || key));
		var $count = $('<span class="dvicd-ap-group__count" />').text('0');
		$head.append($title, $count);
		var $grid = $('<div class="dvicd-ap-grid" />');
		$el.append($head, $grid);
		return { $el: $el, $grid: $grid, $count: $count, count: 0 };
	}

	function showHome($tabs) {
		$tabs.children('.dvicd-ap-home').removeClass('is-hidden');
		$tabs.children('.dvicd-ap-toolbar').removeClass('is-detail');
		$tabs.find('.dvicd-ap-back').removeClass('is-visible');
		$tabs.children('.rwmb-tab-panels').addClass('is-hidden');
		$tabs.find('.rwmb-tab-panel').removeClass('dvicd-ap-panel-active');
	}

	function showPanel($tabs, slug) {
		slug = String(slug || '').split('~~')[0];
		if (!slug) {
			showHome($tabs);
			return;
		}

		var $navItem = $tabs.find('.rwmb-tab-nav > li').filter(function () {
			return panelSlug($(this)) === slug;
		});
		var $panel = findPanelBySlug($tabs, slug);

		if (!$panel.length && $navItem.length) {
			// Last resort: read panel id from nav item.
			$panel = findPanelBySlug($tabs, panelSlug($navItem));
		}

		if (!$panel.length) {
			showHome($tabs);
			return;
		}

		$tabs.children('.dvicd-ap-home').addClass('is-hidden');
		$tabs.children('.dvicd-ap-toolbar').addClass('is-detail');
		$tabs.find('.dvicd-ap-back').addClass('is-visible');
		$tabs.children('.rwmb-tab-panels').removeClass('is-hidden');

		// Switch panels directly — do NOT trigger nav <a> click.
		// wpcd-mbio-tabs-fix.js rewrites hash to "#x~~panel" on nav clicks,
		// which breaks Admin Panel hash routing and snaps back to home.
		$tabs.find('.rwmb-tab-nav > li').removeClass('rwmb-tab-active');
		if ($navItem.length) {
			$navItem.addClass('rwmb-tab-active');
		}
		$tabs.find('.rwmb-tab-panel').removeClass('dvicd-ap-panel-active');
		$panel.addClass('dvicd-ap-panel-active');

		setTimeout(function () {
			if (window.rwmb && rwmb.$document) {
				rwmb.$document.trigger('mb_init_editors');
			}
			$(window).trigger('rwmb_map_refresh');
		}, 200);
	}

	function setHash(slug) {
		var next = slug ? '#' + hashPrefix + '/' + slug : '#' + hashPrefix;
		if (window.location.hash === next) {
			return;
		}
		// replaceState avoids hashchange races with wpcd-mbio-tabs-fix.js
		if (window.history && window.history.replaceState) {
			window.history.replaceState(null, '', window.location.pathname + window.location.search + next);
		} else {
			window.location.hash = next;
		}
	}

	function readHash() {
		var hash = window.location.hash.replace(/^#/, '');
		if (!hash) {
			return '';
		}
		// Ignore Meta Box / WPCD "main~~sub" hashes that are not ours.
		if (hash.indexOf('~~') !== -1 && hash.indexOf(hashPrefix + '/') !== 0) {
			return '';
		}
		var parts = hash.split('/');
		if (parts[0] !== hashPrefix) {
			return '';
		}
		return (parts[1] || '').split('~~')[0];
	}

	function bindSearch($home, $toolbar) {
		$toolbar.on('keydown compositionstart paste', '.dvicd-ap-search', function () {
			$(this).data('dvicdTyped', 1);
		});

		$toolbar.on('input', '.dvicd-ap-search', function () {
			var $input = $(this);
			if ($input.val()) {
				$input.data('dvicdTyped', 1);
			}
			var q = ($input.val()).trim().toLowerCase();
			var visible = 0;

			$home.find('.dvicd-ap-card').each(function () {
				var $card = $(this);
				var label = String($card.attr('data-label') || '');
				var match = !q || label.indexOf(q) !== -1;
				$card.toggleClass('is-hidden', !match);
				if (match) {
					visible += 1;
				}
			});

			$home.find('.dvicd-ap-group').each(function () {
				var $group = $(this);
				var has = $group.find('.dvicd-ap-card:not(.is-hidden)').length > 0;
				$group.toggleClass('is-hidden', !has);
			});

			$home.find('.dvicd-ap-empty').toggleClass('is-visible', visible === 0);
		});
	}

	function initScope($scope) {
		var $metabox = $scope.find('.dvicd-ap-metabox');
		if (!$metabox.length && $scope.hasClass('dvicd-ap-metabox')) {
			$metabox = $scope;
		}
		if (!$metabox.length) {
			$metabox = $scope.find('.rwmb-tabs').closest('.postbox, .rwmb-meta-box');
		}

		$metabox.each(function () {
			var $box = $(this);
			if ($box.data('dvicdApInit')) {
				return;
			}

			var $tabs = findTabsRoot($box);
			if (!$tabs.length) {
				return;
			}

			$box.data('dvicdApInit', true);
			$box.addClass('dvicd-ap-metabox');

			var built = buildGrid($tabs);
			var $home = built.$home;
			var $toolbar = built.$toolbar;
			bindSearch($home, $toolbar);

			$home.on('click', '.dvicd-ap-card', function (e) {
				e.preventDefault();
				var slug = panelSlug($(this));
				if (!slug) {
					return;
				}
				showPanel($tabs, slug);
				setHash(slug);
			});

			$home.on('keydown', '.dvicd-ap-card', function (e) {
				if (e.key !== 'Enter' && e.key !== ' ') {
					return;
				}
				e.preventDefault();
				$(this).trigger('click');
			});

			$toolbar.on('click', '.dvicd-ap-back', function (e) {
				e.preventDefault();
				showHome($tabs);
				setHash('');
			});

			var initial = readHash();
			if (initial) {
				showPanel($tabs, initial);
			} else {
				showHome($tabs);
			}

			$(window).on('hashchange.dvicdAp', function () {
				var slug = readHash();
				if (slug) {
					showPanel($tabs, slug);
				} else if (String(window.location.hash || '').indexOf('#' + hashPrefix) === 0) {
					showHome($tabs);
				}
			});
		});
	}

	function moveOverviewToSidebar() {
		if (!$('body').hasClass('dvicd-ap-active')) {
			return;
		}

		var $side = $('#postbox-container-1 .meta-box-sortables').first();
		if (!$side.length) {
			return;
		}

		var $overview = $(
			'#wpcd_wordpress-app_tab_top_of_site_details, #wpcd_server_wordpress-app_tab_top_of_server_details'
		).first();
		if (!$overview.length) {
			return;
		}

		// Ensure overview is the first control card in the right column.
		if (!$overview.parent().is($side) || !$overview.is($side.children().first())) {
			$side.prepend($overview);
		}

		// Legacy fallback: move any leftover footer mount into Overview.
		mergeSummaryIntoOverview($overview);
		normalizeOverviewActions($overview);
	}

	function normalizeOverviewActions($overview) {
		if (!$overview || !$overview.length || $overview.data('dvicdApActionsReady')) {
			return;
		}
		$overview.data('dvicdApActionsReady', 1);

		// Passwordless chicklet → clear one-click login label.
		$overview
			.find('.wpcd_site_details_top_row_element_passwordless_login a.wpcd_action_passwordless_login')
			.each(function () {
				var $link = $(this);
				if ($link.data('dvicdApLabeled')) {
					return;
				}
				$link
					.data('dvicdApLabeled', 1)
					.text(cfg.oneClickLoginLabel || 'One-click Login')
					.attr('title', cfg.oneClickLoginLabel || 'One-click Login');
			});

		// Avoid two "Login" CTAs: Admin Login → WP Admin.
		$overview.find('.wpcd_site_details_top_row_admin_login .button').each(function () {
			var $btn = $(this);
			var $inner = $btn.children('a').first();
			var $target = $inner.length ? $inner : $btn;
			var text = String($target.text() || '').replace(/\s+/g, ' ').trim();
			if (/admin\s*login/i.test(text) || text === 'Login') {
				$target.text(cfg.wpAdminLabel || 'WP Admin');
			}
		});
	}

	function mergeSummaryIntoOverview($overview) {
		var $mount = $('#dvicd-ap-stats-mount');
		if (!$mount.length) {
			return;
		}

		var $stats = $mount.children('.dvicd-ap-stats').first();
		if (!$stats.length) {
			$mount.remove();
			return;
		}

		var $inside = $overview.children('.inside').first();
		if (!$inside.length) {
			$mount.remove();
			return;
		}

		if (!$inside.find('.dvicd-ap-stats--embedded').length) {
			$stats.addClass('dvicd-ap-stats--embedded');
			$inside.append($stats);
		}

		$mount.remove();
	}

	function boot() {
		// Settings page uses dvicd-admin-settings.js — do not mount site/server toolbar/search.
		if ($('body').hasClass('dvicd-ap-settings-page')) {
			return;
		}
		if (!$('body').hasClass('dvicd-ap-active') && !$('.dvicd-ap-metabox').length) {
			return;
		}
		moveOverviewToSidebar();
		initScope($(document));
		// Stats mount is printed in admin_footer; retry once after paint.
		setTimeout(moveOverviewToSidebar, 50);
	}

	$(boot);
	$(document).on('mb_ready', boot);
})(jQuery);
