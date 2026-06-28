/*
 * This JS file is loaded for the WPCD settings screen.
 */

(function($) {

    var interval;
    var settingsShell = null;

    function settingsCfg() {
        return window.dvicdSettingsCards || {};
    }

    function normalizeIcon(cls) {
        return String(cls || 'fas fa-cube')
            .replace(/\bfad\b/g, 'fas')
            .replace(/\bfa-duotone\b/g, '')
            .replace(/\bfa-cog\b/g, 'fa-gear')
            .replace(/\bfa-cogs\b/g, 'fa-gears')
            .replace(/\bfa-home\b/g, 'fa-house')
            .replace(/\bfa-sync-alt\b/g, 'fa-rotate')
            .replace(/\bfa-shield-alt\b/g, 'fa-shield-halved')
            .replace(/\bfa-ellipsis-h\b/g, 'fa-ellipsis')
            .replace(/\bfa-cloud-upload-alt\b/g, 'fa-cloud-arrow-up')
            .replace(/\bfa-exchange-alt\b/g, 'fa-right-left')
            .replace(/\bfa-file-alt\b/g, 'fa-file-lines')
            .replace(/\bfa-chart-bar\b/g, 'fa-chart-column')
            .replace(/\bfa-sliders-h\b/g, 'fa-sliders')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function dashToFa(icon) {
        var map = {
            'dashicons-text': 'fas fa-align-left',
            'dashicons-text-page': 'fas fa-file-lines',
            'dashicons-align-full-width': 'fas fa-server',
            'dashicons-admin-multisite': 'fas fa-globe',
            'dashicons-images-alt2': 'fas fa-cloud-arrow-up',
            'dashicons-editor-unlink': 'fas fa-link',
            'dashicons-admin-plugins': 'fas fa-puzzle-piece',
            'dashicons-cloud': 'fas fa-cloud',
            'dashicons-bell': 'fas fa-bell',
            'dashicons-email': 'fas fa-envelope',
            'dashicons-email-alt2': 'fas fa-at',
            'dashicons-welcome-write-blog': 'fas fa-plug',
            'dashicons-color-picker': 'fas fa-palette',
            'dashicons-editor-kitchensink': 'fas fa-table-columns',
            'dashicons-rest-api': 'fas fa-code',
            'dashicons-randomize': 'fas fa-shuffle',
            'dashicons-shortcode': 'fas fa-terminal',
            'dashicons-editor-code': 'fas fa-code',
            'dashicons-analytics': 'fas fa-chart-line',
            'dashicons-format-quote': 'fas fa-tags',
            'dashicons-admin-links': 'fas fa-link',
            'dashicons-shield': 'fas fa-shield-halved',
            'dashicons-lock': 'fas fa-lock',
            'dashicons-admin-users': 'fas fa-users',
            'dashicons-admin-tools': 'fas fa-wrench',
            'dashicons-admin-generic': 'fas fa-gear',
            'dashicons-admin-settings': 'fas fa-sliders',
            'dashicons-database': 'fas fa-database',
            'dashicons-backup': 'fas fa-hard-drive',
            'dashicons-share': 'fas fa-share-nodes',
            'dashicons-update': 'fas fa-rotate',
            'dashicons-hammer': 'fas fa-hammer',
            'dashicons-performance': 'fas fa-gauge-high'
        };
        var raw = String(icon || '');
        if (raw.indexOf('fa-') !== -1) {
            return normalizeIcon(raw);
        }
        for (var key in map) {
            if (raw.indexOf(key) !== -1) {
                return map[key];
            }
        }
        return '';
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

    function panelSlug($el) {
        return String($el.attr('data-panel') || $el.data('panel') || '');
    }

    function boxTitle($box) {
        var raw = $box.children('.postbox-header').find('.hndle').first().text()
            || $box.children('h2.hndle, .hndle').first().text()
            || $box.attr('id')
            || '';
        return (String(raw).replace(/\s+/g, ' ')).trim();
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

    function buildBackButton(cfg) {
        return $('<button type="button" class="dvicd-ap-settings-back" />')
            .text(cfg.backLabel || 'Back to Home')
            .hide();
    }

    function setHash(slug) {
        var cfg = settingsCfg();
        var prefix = cfg.hashPrefix || 'ap';
        // Home = no hash (avoid "#ap~~panel" collisions from legacy tab scripts).
        var next = slug ? '#' + prefix + '/' + slug : '';
        var current = window.location.pathname + window.location.search + (window.location.hash || '');
        var target = window.location.pathname + window.location.search + next;
        if (current === target) {
            return;
        }
        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, '', target);
        } else if (next) {
            window.location.hash = next;
        } else if (window.location.hash) {
            window.location.hash = '';
        }
    }

    function readHash() {
        var cfg = settingsCfg();
        var prefix = cfg.hashPrefix || 'ap';
        var hash = window.location.hash.replace(/^#/, '');
        if (!hash) {
            return '';
        }
        // Ignore legacy Meta Box "main~~sub" hashes.
        if (hash.indexOf('~~') !== -1 && hash.indexOf(prefix + '/') !== 0) {
            return '';
        }
        var parts = hash.split('/');
        if (parts[0] !== prefix) {
            return '';
        }
        return String(parts[1] || '').split('~~')[0];
    }

    function findPanel($box, slug) {
        var $panels = $box.find('.rwmb-tab-panels').first().children('.rwmb-tab-panel');
        var $match = $panels.filter(function() {
            return panelSlug($(this)) === slug;
        });
        if ($match.length) {
            return $match.first();
        }
        return $panels.filter(function() {
            var cls = ' ' + (this.className || '') + ' ';
            return cls.indexOf(' rwmb-tab-panel-' + slug + ' ') !== -1;
        }).first();
    }

    function clearPanelState($box) {
        $box.find('.rwmb-tab-nav > li').removeClass('rwmb-tab-active');
        $box.find('.rwmb-tab-panel').each(function() {
            var $panel = $(this);
            $panel.removeClass('dvicd-ap-panel-active rwmb-tab-active');
            this.style.removeProperty('display');
            $panel.hide();
        });
    }

    function initSettingsSelect2($scope) {
        if (!$.fn.select2) {
            return;
        }
        var $root = ($scope && $scope.length) ? $scope : $('.wrap.wpcd-settings-single-page');
        if (!$root.length) {
            return;
        }

        $root.find('select.rwmb-select, select.rwmb-select_advanced, .rwmb-input > select').each(function() {
            var $el = $(this);
            if (!$el.length || $el.data('dvicdSelect2')) {
                return;
            }
            // Already enhanced by Meta Box select_advanced.
            if ($el.hasClass('select2-hidden-accessible') || $el.data('select2')) {
                $el.data('dvicdSelect2', 1);
                return;
            }

            var opts = {
                width: '100%',
                dropdownParent: $(document.body),
                minimumResultsForSearch: 6,
                allowClear: !$el.prop('multiple') && $el.find('option[value=""]').length > 0
            };

            try {
                $el.select2(opts);
                $el.data('dvicdSelect2', 1);
            } catch (err) {
                // Ignore select2 init errors on odd fields.
            }
        });
    }

    function showHome(shell) {
        if (!shell) {
            return;
        }
        shell.mode = 'home';
        shell.activeSlug = '';
        shell.$home.removeClass('is-hidden').show();
        if (shell.$back) {
            shell.$back.hide();
        }
        shell.$boxes.removeClass('dvicd-ap-settings-box-active');
        shell.$boxes.each(function() {
            clearPanelState($(this));
            $(this).hide();
        });
        shell.$wrap.removeClass('is-detail').addClass('is-home');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function showDetail(shell, slug) {
        if (!shell) {
            return;
        }
        var item = shell.items[slug];
        if (!item) {
            showHome(shell);
            return;
        }

        shell.mode = 'detail';
        shell.activeSlug = slug;

        // Hide home cards hard (class + inline) so they cannot flash back.
        shell.$home.addClass('is-hidden').hide();
        if (shell.$back) {
            shell.$back.show();
        }
        shell.$wrap.removeClass('is-home').addClass('is-detail');

        shell.$boxes.removeClass('dvicd-ap-settings-box-active');
        shell.$boxes.each(function() {
            var $box = $(this);
            clearPanelState($box);
            if (!$box.is(item.$box)) {
                $box.hide();
            }
        });

        item.$box.addClass('dvicd-ap-settings-box-active dvicd-ap-metabox').show();
        item.$box.find('.handle-actions, .handlediv, .postbox-header .handle-order-higher, .postbox-header .handle-order-lower').hide();

        var $selectScope = item.$box;
        if (item.panel) {
            var $navItem = item.$box.find('.rwmb-tab-nav > li').filter(function() {
                return panelSlug($(this)) === item.panel;
            });
            var $panel = findPanel(item.$box, item.panel);
            if ($navItem.length) {
                $navItem.addClass('rwmb-tab-active');
            }
            if ($panel.length) {
                $panel.addClass('dvicd-ap-panel-active rwmb-tab-active');
                $panel.get(0).style.setProperty('display', 'block', 'important');
                $selectScope = $panel;
            }
            setTimeout(function() {
                // Re-assert detail after Meta Box tab scripts finish.
                if (shell.mode !== 'detail' || shell.activeSlug !== slug) {
                    return;
                }
                shell.$home.addClass('is-hidden').hide();
                shell.$wrap.removeClass('is-home').addClass('is-detail');
                item.$box.addClass('dvicd-ap-settings-box-active').show();
                if ($panel.length) {
                    clearPanelState(item.$box);
                    if ($navItem.length) {
                        $navItem.addClass('rwmb-tab-active');
                    }
                    $panel.addClass('dvicd-ap-panel-active rwmb-tab-active');
                    $panel.get(0).style.setProperty('display', 'block', 'important');
                }
                initSettingsSelect2($selectScope);
                if (window.rwmb && rwmb.$document) {
                    rwmb.$document.trigger('mb_init_editors');
                }
                $(window).trigger('rwmb_map_refresh');
            }, 250);
        } else {
            initSettingsSelect2($selectScope);
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function initSettingsAdminPanel() {
        var $wrap = $('.wrap.wpcd-settings-single-page');
        if (!$wrap.length || $wrap.data('dvicdSettingsAp')) {
            return;
        }

        var $boxes = $wrap.find('#poststuff .postbox');
        if (!$boxes.length) {
            return;
        }

        $wrap.data('dvicdSettingsAp', 1);
        $wrap.addClass('dvicd-ap-settings is-home');
        $('body').addClass('dvicd-ap-active dvicd-ap-settings-page');

        var cfg = settingsCfg();
        var icons = cfg.icons || {};
        var defaultIcon = cfg.defaultIcon || 'fas fa-gear';
        var openLabel = cfg.openLabel || 'Open';
        // Mount shell OUTSIDE <form.rwmb-settings-form> so search is not autofilled as login.
        var $mount = $wrap.find('.rwmb-settings-wrap').first();
        if (!$mount.length) {
            $mount = $wrap.find('.rwmb-settings-form-wrap').parent();
        }
        if (!$mount.length) {
            $mount = $wrap;
        }

        var $back = buildBackButton(cfg);
        var $home = $('<div class="dvicd-ap-home" />');
        var $shell = $('<div class="dvicd-ap-settings-shell" />').append($back, $home);

        var groups = {};
        var items = {};

        function ensureGroup(key, meta) {
            if (groups[key]) {
                return groups[key];
            }
            groups[key] = createGroup(key, meta);
            $home.append(groups[key].$el);
            return groups[key];
        }

        function addCard(opts) {
            var slug = opts.slug;
            if (!slug || items[slug]) {
                return;
            }
            items[slug] = {
                $box: opts.$box,
                panel: opts.panel || '',
                label: opts.label || slug,
                icon: opts.icon || defaultIcon
            };
            var group = ensureGroup(opts.groupKey, {
                label: opts.groupLabel,
                icon: opts.groupIcon
            });
            var $card = $('<div class="dvicd-ap-card" role="button" tabindex="0" />')
                .attr('data-panel', slug)
                .attr('data-label', String(opts.label || '').toLowerCase())
                .append($('<span class="dvicd-ap-card__icon" aria-hidden="true" />').append($('<i />').addClass(opts.icon)))
                .append($('<span class="dvicd-ap-card__label" />').text(opts.label))
                .append($('<span class="dvicd-ap-card__hint" />').text(openLabel));
            group.$grid.append($card);
            group.count += 1;
        }

        $boxes.each(function() {
            var $box = $(this);
            var id = String($box.attr('id') || '');
            var title = boxTitle($box) || id;
            var boxIcon = normalizeIcon(icons[id] || defaultIcon);
            var $nav = $box.find('.rwmb-tabs > .rwmb-tab-nav, .rwmb-tabs .rwmb-tab-nav').first();
            var $lis = $nav.children('li');

            $box.addClass('dvicd-ap-metabox');

            if ($lis.length) {
                $lis.each(function() {
                    var $li = $(this);
                    var slug = panelSlug($li);
                    if (!slug) {
                        var cls = String($li.attr('class') || '');
                        var m = cls.match(/rwmb-tab-([^\s]+)/);
                        if (m && m[1] && m[1] !== 'active') {
                            slug = m[1];
                        }
                    }
                    if (!slug) {
                        return;
                    }
                    var label = ($li.find('a').clone().children().remove().end().text()).trim() || slug;
                    var iconCls = $li.find('i').first().attr('class') || '';
                    var icon = dashToFa(iconCls) || boxIcon;
                    addCard({
                        slug: slug,
                        label: label,
                        icon: icon,
                        $box: $box,
                        panel: slug,
                        groupKey: id || title,
                        groupLabel: title,
                        groupIcon: boxIcon
                    });
                });
            } else {
                addCard({
                    slug: id,
                    label: title,
                    icon: boxIcon,
                    $box: $box,
                    panel: '',
                    groupKey: 'general-settings',
                    groupLabel: 'General',
                    groupIcon: 'fas fa-sliders'
                });
            }
        });

        Object.keys(groups).forEach(function(key) {
            var group = groups[key];
            if (!group.count) {
                group.$el.addClass('is-hidden');
                return;
            }
            group.$count.text(String(group.count));
        });

        var $formWrap = $mount.children('.rwmb-settings-form-wrap').first();
        if ($formWrap.length) {
            $formWrap.before($shell);
        } else {
            $mount.prepend($shell);
        }

        var shell = {
            $wrap: $wrap,
            $shell: $shell,
            $back: $back,
            $home: $home,
            $boxes: $boxes,
            items: items,
            mode: 'home',
            activeSlug: ''
        };
        settingsShell = shell;

        // Block legacy tab-hash rewrites inside settings Admin Panel.
        $wrap.find('.rwmb-tab-nav').on('click.dvicdSettingsAp', 'a', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
        });

        $home.on('click', '.dvicd-ap-card', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var slug = panelSlug($(this));
            if (!slug || !items[slug]) {
                return;
            }
            showDetail(shell, slug);
            setHash(slug);
        });
        $home.on('keydown', '.dvicd-ap-card', function(e) {
            if (e.key !== 'Enter' && e.key !== ' ') {
                return;
            }
            e.preventDefault();
            $(this).trigger('click');
        });
        $back.on('click', function(e) {
            e.preventDefault();
            showHome(shell);
            setHash('');
        });

        $(window).on('hashchange.dvicdSettingsAp', function() {
            var raw = String(window.location.hash || '');
            var slug = readHash();

            // Corrupted legacy hash while in detail — restore current route.
            if (raw.indexOf('~~') !== -1) {
                if (shell.mode === 'detail' && shell.activeSlug) {
                    setHash(shell.activeSlug);
                    return;
                }
                if (slug && items[slug]) {
                    showDetail(shell, slug);
                    setHash(slug);
                    return;
                }
                showHome(shell);
                setHash('');
                return;
            }

            if (slug && items[slug]) {
                if (shell.mode !== 'detail' || shell.activeSlug !== slug) {
                    showDetail(shell, slug);
                }
                return;
            }

            if (shell.mode !== 'home') {
                showHome(shell);
            }
        });

        var initial = readHash();
        if (initial && items[initial]) {
            showDetail(shell, initial);
        } else {
            showHome(shell);
            setHash('');
        }

        refreshIcons($wrap.get(0));
    }

    $(document).ready(function() {
        init();
        initSettingsAdminPanel();
        initSettingsSelect2();
        setTimeout(initSettingsAdminPanel, 0);
        setTimeout(initSettingsAdminPanel, 150);
        setTimeout(initSettingsSelect2, 200);
    });

    // for toggle password text
    function initPasswordToggle() {
        // add the password toggle icon to text fields and text areas that have a class of wpcd_settings_pass_toggle
        $('.wpcd_settings_pass_toggle input').after($('<span class="wpcd_settings_pass_toggle_icon dashicons dashicons-visibility wpcd-not-showing"></span>'));
        $('.wpcd_settings_pass_toggle textarea').after($('<span class="wpcd_settings_pass_toggle_icon dashicons dashicons-visibility wpcd-not-showing"></span>'));

        // hide the passwords by default
        $('.wpcd_settings_pass_toggle_icon').parent().find('input[type="text"]').attr('type', 'password');
        $('.wpcd_settings_pass_toggle_icon').parent().find('textarea').css('color', '#F5F5F5').css('text-decoration', 'line-through underline overline'); // text areas cannot be password fields so make the text difficult to see.
        $('.wpcd_settings_pass_toggle_icon').removeClass('dashicons-hidden').addClass('dashicons-visibility').addClass('wpcd-not-showing');

        // toggle the showing of plain text password.
        $('.wpcd_settings_pass_toggle_icon').on('click', function(e) {
            e.preventDefault();
            if ($(this).hasClass('wpcd-not-showing')) {
                // show data in text and text area fields
                $(this).parent().find('input[type="password"]').attr('type', 'text');
                $(this).removeClass('dashicons-visibility').addClass('dashicons-hidden').removeClass('wpcd-not-showing');

                $(this).parent().find('textarea').css('color', 'inherit').css('text-decoration', 'none');
            } else {
                // hide the data in text and text area fields
                $(this).parent().find('input[type="text"]').attr('type', 'password');
                $(this).removeClass('dashicons-hidden').addClass('dashicons-visibility').addClass('wpcd-not-showing');

                $(this).parent().find('textarea').css('color', '#F5F5F5').css('text-decoration', 'line-through underline overline'); // text areas cannot be password fields so make the text difficult to see.
            }
        });
    }
    
    // To create public pages.
    function initCreatePublicPages() {
        $('body').on('click', '#wordpress_public_create_pages_button', function(e) {
            e.preventDefault();

            var action = $(this).data('action');
            var nonce = $(this).data('nonce');

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: action,
                    nonce: nonce
                },
                success: function(data) {
                    alert(data.data.msg);
                    location.reload();
                }
            });

        });
    }

    // for cleaning up apps - triggered from the SETTINGS->TOOLS->CLEAN UP APPS button.
    function initCleanUpApps() {
        $('body').on('click', '#wpcd-cleanup-apps', function(e) {
            e.preventDefault();

            var action = $(this).data('action');
            var nonce = $(this).data('nonce');

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: action,
                    nonce: nonce
                },
                success: function(data) {
                    alert(data.data.msg);
                    location.reload();
                }
            });

        });
    }

    // for cleaning up servers - triggered from the SETTINGS->TOOLS->CLEAN UP SERVERS button
    function initCleanUpServers() {
        $('body').on('click', '#wpcd-cleanup-servers', function(e) {
            e.preventDefault();

            var action = $(this).data('action');
            var nonce = $(this).data('nonce');

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: action,
                    nonce: nonce
                },
                success: function(data) {
                    alert(data.data.msg);
                    location.reload();
                }
            });

        });
    }

    // for clearing provider cache
    function initClearProviderCache() {
        $('body').on('click', '.wpcd-provider-clear-cache', function(e) {
            e.preventDefault();

            var action = $(this).data('action');
            var nonce = $(this).data('nonce');
            var provider = $(this).data('provider');

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: action,
                    nonce: nonce,
                    provider: provider
                },
                success: function(data) {
                    alert(data.data.msg);
                    location.reload();
                }
            });

        });
    }
	
    // for automatically creating ssh keys.
    function initAutoCreateSSHKeys() {
        $('body').on('click', '.wpcd-provider-auto-create-ssh-key', function(e) {
            e.preventDefault();

            var action = $(this).data('action');
            var nonce = $(this).data('nonce');
            var provider = $(this).data('provider');

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: action,
                    nonce: nonce,
                    provider: provider
                },
                success: function(data) {
                    alert(data.data.msg);
                    location.reload();
                }
            });

        });
    }
	
    // For testing connections to a provider.
    function initTestProviderConnection() {
        $('body').on('click', '.wpcd-provider-test-provider-connection', function(e) {
            e.preventDefault();

            var action = $(this).data('action');
            var nonce = $(this).data('nonce');
            var provider = $(this).data('provider');

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: action,
                    nonce: nonce,
                    provider: provider
                },
                success: function(data) {
                    alert(data.data.msg);
                    location.reload();
                }
            });

        });
    }		

    // Checking for WPCD updates - triggered from the SETTINGS->LICENSE AND UPDATES->CHECK FOR UPDATES button
    // Validate licenses - triggered from the SETTINGS->LICENSE AND UPDATES->VALIDATE LICENSES button
    function initCheckUpdatesValidateLicenses() {
        $('body').on('click', '#wpcd-check-for-updates, #wpcd-validate-licenses', function(e) {
            e.preventDefault();

            var current_btn = $(this);
            var action = $(this).data('action');
            var nonce = $(this).data('nonce');
            var loading_msg = $(this).data('loading_msg');

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: action,
                    nonce: nonce
                },
                beforeSend: function() {
                    current_btn.prop('disabled', true);
                    $("<div class='wpcd_btn_loading_msg'>" + loading_msg + "</p>").insertAfter(current_btn);
                },
                success: function(data) {
                    location.reload();
                }
            });

        });
    }

    // Reset defaults brand colors.
    function initResetDefaultsBrandColors() {
        $('body').on('click', '#wordpress_app_reset_brand_colors', function(e) {
            e.preventDefault();

            var current_btn = $(this);
            var action = $(this).data('action');
            var nonce = $(this).data('nonce');
            var loading_msg = $(this).data('loading_msg');
            var confirm_msg = $(this).data('confirm');

            if (confirm(confirm_msg)) {
                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: action,
                        nonce: nonce,
                    },
                    beforeSend: function() {
                        current_btn.prop('disabled', true);
                        $("<div class='wpcd_btn_loading_msg'>" + loading_msg + "</p>").insertAfter(current_btn);
                    },
                    success: function(data) {
                        alert(data.data.msg);
                        location.reload();
                    }
                });
            }
        });
    }

    function init() {
        initCreatePublicPages();
        initPasswordToggle();
        initCleanUpApps();
        initCleanUpServers();
        initClearProviderCache();
		initAutoCreateSSHKeys();
		initTestProviderConnection();
        initCheckUpdatesValidateLicenses();
        initResetDefaultsBrandColors();
    }

})(jQuery);