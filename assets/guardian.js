/*
 * ΩGuardian · Interactions d'administration (sans réseau externe).
 * Les champs désactivés restent conservés dans le stockage serveur.
 * Toute autorisation de profiler est calculée par la politique PHP.
 */
(function () {
    'use strict';
    var fields = document.querySelectorAll('fieldset[data-enabled-by]');
    function updateDependencies() {
        Array.prototype.forEach.call(fields, function (group) {
            var source = document.querySelector('input[name="' + group.getAttribute('data-enabled-by') + '"]');
            if (!source) { return; }
            var disabled = !source.checked;
            group.disabled = disabled;
            group.classList.toggle('is-disabled', disabled);
            group.setAttribute('aria-disabled', disabled ? 'true' : 'false');
        });
    }
    document.addEventListener('change', updateDependencies);
    updateDependencies();

    var errors = document.getElementById('guardian-error-pages');
    if (errors) {
        function updateErrorFields() {
            [['message', 'error_mode', 'error_custom_text'], ['reference', 'error_reference', 'error_reference_text']].forEach(function (item) {
                var label = errors.querySelector('[data-error-field="' + item[0] + '"]');
                var select = errors.querySelector('[name="' + item[1] + '"]');
                var input = errors.querySelector('[name="' + item[2] + '"]');
                if (!label || !select || !input) { return; }
                input.disabled = select.value !== 'custom';
                label.classList.toggle('is-disabled', input.disabled);
            });
        }
        errors.addEventListener('change', updateErrorFields);
        updateErrorFields();
    }

    // Progressive enhancement only: these controls do not persist settings.
    var workspace = document.querySelector ? (document.querySelector('.workspace') || document.body) : null;
    var french = document.documentElement && document.documentElement.lang.indexOf('fr') === 0;
    var dirtyLabel = workspace ? workspace.getAttribute('data-unsaved-label') : '';
    Array.prototype.forEach.call(document.querySelectorAll('form.config-form, form.quick-preset'), function (form) {
        var badge = document.createElement('span');
        badge.className = 'unsaved-badge';
        badge.setAttribute('role', 'status');
        badge.hidden = true;
        form.appendChild(badge);
        function changed(event) {
            if (!event.target || event.target.type === 'password' || event.target.type === 'hidden' || event.target.closest('.password-wrap')) { return; }
            badge.hidden = false;
            badge.textContent = dirtyLabel || (french ? 'Modifications non enregistrées' : 'Unsaved changes');
            form.classList.add('form-dirty');
        }
        form.addEventListener('input', changed);
        form.addEventListener('change', changed);
    });
    Array.prototype.forEach.call(document.querySelectorAll('input[type="password"]'), function (input) {
        var label = input.closest('label');
        if (label && !input.getAttribute('aria-label')) { input.setAttribute('aria-label', Array.prototype.filter.call(label.childNodes, function (node) { return node.nodeType === 3; }).map(function (node) { return node.textContent; }).join(' ').trim()); }
        var wrapper = document.createElement('div');
        wrapper.className = 'password-wrap';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'password-toggle';
        button.textContent = '◉';
        var show = workspace ? workspace.getAttribute('data-show-password') : (french ? 'Afficher le mot de passe' : 'Show password');
        var hide = workspace ? workspace.getAttribute('data-hide-password') : (french ? 'Masquer le mot de passe' : 'Hide password');
        button.setAttribute('aria-label', show);
        button.setAttribute('title', show);
        button.setAttribute('aria-pressed', 'false');
        button.addEventListener('click', function () {
            var visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            button.setAttribute('aria-pressed', visible ? 'true' : 'false');
            button.setAttribute('aria-label', visible ? hide : show);
            button.setAttribute('title', visible ? hide : show);
        });
        wrapper.appendChild(button);
    });
    if (errors) {
        function paintErrorPreview() {
            var preview = errors.querySelector('.error-preview');
            if (!preview) { return; }
            var nativeMode = errors.querySelector('[name="error_mode"]').value === 'native';
            var custom = errors.querySelector('[name="error_mode"]').value === 'custom';
            var ref = errors.querySelector('[name="error_reference"]').value;
            var note = preview.querySelector('[data-preview-native]');
            var title = preview.querySelector('[data-preview-title]');
            var message = preview.querySelector('[data-preview-message]');
            var reference = preview.querySelector('[data-preview-reference]');
            note.textContent = nativeMode ? preview.getAttribute('data-native-help') : '';
            title.hidden = message.hidden = reference.hidden = nativeMode;
            // Mirror the renderer's plain-text fallback, never use innerHTML.
            var text = errors.querySelector('[name="error_custom_text"]').value;
            message.textContent = custom && text.trim() ? text : 'The requested resource is unavailable.';
            reference.textContent = ref === 'custom' ? errors.querySelector('[name="error_reference_text"]').value : (ref === 'random' ? 'A1B2C3D4E5F' : '');
        }
        errors.addEventListener('input', paintErrorPreview);
        errors.addEventListener('change', paintErrorPreview);
        paintErrorPreview();
    }

    // No auto-submit or focus handler on the language disclosure.
    document.querySelectorAll('[data-language-picker]').forEach(function (picker) {
        var search = picker.querySelector('[data-language-search]');
        search.addEventListener('input', function () {
            var needle = search.value.trim().toLocaleLowerCase(), visible = 0;
            picker.querySelectorAll('[data-language-option]').forEach(function (label) {
                label.hidden = label.textContent.toLocaleLowerCase().indexOf(needle) === -1;
                if (!label.hidden) { visible++; }
            });
            picker.querySelector('[data-language-empty]').hidden = visible > 0;
        });
    });
    var accounts = document.getElementById('guardian-accounts');
    if (accounts) {
        var accountSearch = accounts.querySelector('[data-account-search]');
        var accountRole = accounts.querySelector('[data-account-role]');
        function filterAccounts() {
            var needle = accountSearch.value.trim().toLocaleLowerCase(), count = 0;
            var entries = accounts.querySelectorAll('[data-account-name]');
            entries.forEach(function (entry) {
                entry.hidden = entry.dataset.accountName.toLocaleLowerCase().indexOf(needle) === -1 || (accountRole.value !== '' && entry.dataset.accountKind !== accountRole.value);
                if (!entry.hidden) { count++; }
            });
            accounts.querySelector('[data-account-count]').textContent = count + ' / ' + entries.length;
        }
        accountSearch.addEventListener('input', filterAccounts);
        accountRole.addEventListener('change', filterAccounts);
        filterAccounts();
    }

    var panel = document.getElementById('guardian-access-schedule');
    if (!panel) { return; }
    var summary = document.getElementById('guardian-schedule-summary');
    var detail = document.getElementById('guardian-schedule-detail');
    var warning = document.getElementById('guardian-schedule-warning');
    var messages = {};
    try { messages = JSON.parse(panel.getAttribute('data-messages') || '{}'); } catch (ignore) { messages = {}; }
    function message(key) { return messages[key] || key; }
    var observer = panel.querySelector('.timezone-observer');
    var confirmed = document.getElementById('guardian-timezone-confirm');
    var originalZone = observer ? observer.getAttribute('data-original-timezone') : '';
    var serverZone = observer ? observer.getAttribute('data-server-timezone') : '';
    var labels = Array.prototype.map.call(panel.querySelectorAll('.day-face>span:first-child'), function (node) { return node.textContent; });
    function field(name) { return panel.querySelector('[name="' + name + '"]'); }
    function paint() {
        var enabled = !!field('schedule_enabled').checked;
        var portal = !!field('portal_closed_outside_hours').checked && enabled;
        var selected = Array.prototype.slice.call(panel.querySelectorAll('input[name="weekdays[]"]:checked'));
        var days = selected.map(function (el) { return Number(el.value); });
        var from = field('start').value;
        var until = field('end').value;
        var tz = field('timezone').value.trim();
        var requiresAck = enabled && tz !== originalZone && tz !== serverZone;
        if (confirmed) {
            confirmed.hidden = !requiresAck;
            var ack = confirmed.querySelector('input[name="timezone_ack"]');
            ack.required = requiresAck;
            if (!requiresAck) { ack.checked = false; }
        }
        var valid = !!from && !!until && from < until && days.length > 0;
        warning.hidden = !enabled || valid;
        if (!enabled) {
            summary.textContent = message('schedule.js.disabled');
            detail.textContent = message('schedule.js.retained');
            return;
        }
        summary.textContent = (days.length ? days.map(function (day) { return labels[day - 1]; }).join(', ') : message('schedule.js.no_days'))
            + ' · ' + (from||'--:--') + '–' + (until||'--:--') + ' · ' + (tz || message('schedule.js.zone_missing'));
        detail.textContent = message('schedule.js.allowed');
        if (portal) { detail.textContent += message('schedule.js.portal_closed'); }
    }
    panel.addEventListener('change', paint);
    panel.addEventListener('input', paint);
    paint();
}());
