(function (root) {
    'use strict';
    var sizes = {lower: 26, upper: 26, digit: 10, symbol: 32};
    function combinations(length, available, required) {
        if (!Number.isInteger(length) || length < 1 || length > 128) { return 0n; }
        var alphabet = 0, groups = [];
        Object.keys(sizes).forEach(function (key) {
            if (available[key]) { alphabet += sizes[key]; }
            if (required[key]) { groups.push(available[key] ? sizes[key] : -1); }
        });
        if (!alphabet || groups.indexOf(-1) !== -1) { return 0n; }
        var count = 0n;
        for (var mask = 0; mask < (1 << groups.length); mask++) {
            var removed = 0, bits = 0;
            for (var i = 0; i < groups.length; i++) { if (mask & (1 << i)) { removed += groups[i]; bits++; } }
            var term = BigInt(alphabet - removed) ** BigInt(length);
            count += bits % 2 ? -term : term;
        }
        return count;
    }
    function validTiming(rate, rules) {
        if (!Number.isFinite(rate) || rate <= 0 || !rules.length || rules.length > 8) { return false; }
        for (var k = 0; k < rules.length; k++) {
            var r = rules[k];
            if (![r.attempts,r.block_minutes,r.window_minutes,r.escalation_minutes].every(function (n) { return Number.isInteger(n) && n > 0; }) || r.attempts > 100 || r.block_minutes > 10080 || r.window_minutes > 1440 || r.escalation_minutes > 10080 || (k && r.block_minutes < rules[k-1].block_minutes)) { return false; }
            if ((r.attempts - 1) / rate >= r.window_minutes * 60 || (k && r.attempts / rate > rules[k - 1].escalation_minutes * 60)) { return false; }
        }
        return true;
    }
    function onlineSeconds(guesses, rate, rules) {
        if (!Number.isFinite(guesses) || guesses < 1 || !validTiming(rate,rules)) { return null; }
        var seconds = 0, remaining = guesses;
        for (var stage = 0; stage < rules.length - 1; stage++) {
            var rule = rules[stage];
            if (remaining <= rule.attempts) { return seconds + remaining / rate; }
            seconds += rule.attempts / rate + rule.block_minutes * 60; remaining -= rule.attempts;
        }
        var last = rules[rules.length - 1];
        return seconds + remaining / rate + Math.max(0, Math.floor((remaining - 1) / last.attempts)) * last.block_minutes * 60;
    }
    function averageOnlineSeconds(count, rate, rules) {
        if (typeof count !== 'bigint' || count < 1n || !validTiming(rate,rules)) { return null; }
        function ratio(numerator, denominator) { return Number(numerator / denominator) + Number(numerator % denominator) / Number(denominator); }
        var total = (Number(count) + 1) / 2 / rate, used = 0n;
        for (var stage = 0; stage < rules.length - 1; stage++) {
            used += BigInt(rules[stage].attempts);
            if (used >= count) { return total; }
            total += ratio(count - used,count) * rules[stage].block_minutes * 60;
        }
        var last = rules[rules.length-1], remaining = count-used, attempts = BigInt(last.attempts);
        var blocks = (remaining-1n)/attempts;
        var weighted = blocks*remaining-attempts*blocks*(blocks+1n)/2n;
        return total + ratio(weighted,count)*last.block_minutes*60;
    }
    var api = {combinations: combinations, onlineSeconds: onlineSeconds, averageOnlineSeconds: averageOnlineSeconds};
    if (typeof module !== 'undefined' && module.exports) { module.exports = api; }
    if (typeof document === 'undefined') { return; }
    root.GuardianPasswordModel = api;
    document.querySelectorAll('[data-auth-safety]').forEach(function (panel) {
        var rows = panel.querySelector('[data-rate-rules]'), template = rows.firstElementChild.cloneNode(true);
        var add = panel.querySelector('[data-add-rule]'), form = panel.querySelector('[data-security-form]');
        function renumber() {
            var all = rows.querySelectorAll('[data-rate-rule]');
            all.forEach(function (row, index) {
                row.querySelector('[data-rule-number]').textContent = String(index + 1);
                row.querySelectorAll('[data-rule-field]').forEach(function (input) { input.name = 'login_rules[' + index + '][' + input.dataset.ruleField + ']'; });
                row.querySelector('[data-remove-rule]').disabled = all.length === 1;
            });
            add.disabled = all.length >= 8;
        }
        function addRow(values) {
            var row = template.cloneNode(true);
            row.querySelectorAll('[data-rule-field]').forEach(function (input) { input.value = String(values[input.dataset.ruleField]); });
            rows.appendChild(row); renumber();
        }
        function changed() { form.dispatchEvent(new Event('change', {bubbles: true})); }
        add.addEventListener('click', function () {
            if (rows.children.length >= 8) { return; }
            var previous = rows.lastElementChild.querySelector('[data-rule-field="block_minutes"]').value;
            addRow({attempts: 5, block_minutes: Math.min(10080, Math.max(10, Number(previous) * 2)), window_minutes: 30, escalation_minutes: 30}); changed();
        });
        rows.addEventListener('click', function (event) {
            var button = event.target.closest('[data-remove-rule]');
            if (button && rows.children.length > 1) { button.closest('[data-rate-rule]').remove(); renumber(); changed(); }
        });
        panel.querySelectorAll('[data-rate-preset]').forEach(function (button) {
            button.addEventListener('click', function () {
                rows.replaceChildren();
                var preset = button.dataset.ratePreset === 'progressive' ? [{attempts: 5, block_minutes: 10, window_minutes: 30, escalation_minutes: 30}, {attempts: 5, block_minutes: 120, window_minutes: 30, escalation_minutes: 30}] : [{attempts: 10, block_minutes: 10, window_minutes: 30, escalation_minutes: 30}];
                preset.forEach(addRow); changed();
            });
        });
        renumber();
        var lab = panel.querySelector('[data-password-lab]'), output = lab.querySelector('[data-lab-results]'), labels = JSON.parse(output.dataset.labMessages);
        var pinned = null, timer;
        function value(selector) { return Number(lab.querySelector(selector).value); }
        function format(n) { return new Intl.NumberFormat(document.documentElement.lang, {maximumSignificantDigits: 4, notation: 'standard', useGrouping: true}).format(n); }
        function duration(seconds) { return seconds >= 31557600 ? format(seconds / 31557600) + ' ' + labels.years : format(seconds) + ' ' + labels.seconds; }
        function snapshot() {
            var available = {}, required = {};
            lab.querySelectorAll('[data-lab-alphabet]').forEach(function (x) { available[x.dataset.labAlphabet] = x.checked; });
            lab.querySelectorAll('[data-lab-required]').forEach(function (x) { required[x.dataset.labRequired] = x.checked; });
            var length = value('[data-lab-length]'), count = combinations(length, available, required);
            var online = value('[data-lab-online]'), offline = value('[data-lab-offline]'), cost = value('[data-lab-cost]');
            if (!count || ![online,offline,cost].every(Number.isFinite) || online < 0.001 || online > 1e9 || offline < 0.001 || offline > 1e12 || cost < 0 || cost > 1e6) { return null; }
            var rules = [];
            rows.querySelectorAll('[data-rate-rule]').forEach(function (row) {
                var r = {}; row.querySelectorAll('[data-rule-field]').forEach(function (input) { r[input.dataset.ruleField] = Number(input.value); }); rules.push(r);
            });
            var n = Number(count), average = (n + 1) / 2;
            return {combinations: format(n), bits: format(Math.log2(n)), online_plain: duration(average / online), online_locked: averageOnlineSeconds(count, online, rules), offline_time: duration(average / offline), offline_cost: format(average / offline / 3600 * cost) + ' €'};
        }
        function render() {
            var current = snapshot(); output.replaceChildren();
            if (!current) { output.textContent = labels.invalid_simulation; return; }
            var table = document.createElement('table'), head = document.createElement('tr');
            ['', labels.current_scenario].concat(pinned ? [labels.scenario] : []).forEach(function (label) { var th = document.createElement('th'); th.textContent = label; th.scope = 'col'; head.appendChild(th); });
            table.appendChild(head);
            ['combinations','bits','online_plain','online_locked','offline_time','offline_cost'].forEach(function (key) {
                var tr = document.createElement('tr'), th = document.createElement('th'); th.scope = 'row'; th.textContent = labels[key]; tr.appendChild(th);
                [current].concat(pinned ? [pinned] : []).forEach(function (scenario) { var td = document.createElement('td'); td.textContent = key === 'online_locked' ? (scenario[key] === null ? labels.window_assumption : duration(scenario[key])) : scenario[key]; tr.appendChild(td); }); table.appendChild(tr);
            });
            output.appendChild(table);
        }
        panel.addEventListener('input', function () { clearTimeout(timer); timer = setTimeout(render, 120); });
        panel.addEventListener('change', render);
        lab.querySelector('[data-lab-pin]').addEventListener('click', function () { pinned = snapshot(); render(); });
        lab.querySelector('[data-lab-policy]').addEventListener('click', function () {
            lab.querySelector('[data-lab-length]').value = form.querySelector('[name="password_policy[min_length]"]').value;
            lab.querySelectorAll('[data-lab-required]').forEach(function (x) { x.checked = form.querySelector('[name="password_policy[require_' + x.dataset.labRequired + ']"]').checked; }); render();
        });
        render();
    });
}(typeof globalThis !== 'undefined' ? globalThis : this));
