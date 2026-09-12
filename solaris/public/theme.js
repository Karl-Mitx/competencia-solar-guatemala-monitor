/* Runs before styles to avoid flashing the opposite theme on navigation. */
(() => {
    const key = 'solaris-theme';
    const valid = value => value === 'dark' || value === 'light';
    const read = () => {
        try {
            const stored = localStorage.getItem(key);
            if (valid(stored)) return stored;
        } catch { /* Storage may be disabled by the browser. */ }
        try {
            const stored = document.cookie.split(';').map(value => value.trim()).find(value => value.startsWith(key + '='))?.slice(key.length + 1);
            if (valid(stored)) return stored;
        } catch { /* Cookies may also be unavailable. */ }
        return typeof matchMedia === 'function' && matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    };
    const apply = theme => {
        document.documentElement.dataset.theme = theme;
        document.documentElement.style.colorScheme = theme;
    };
    window.solarisTheme = {
        set(theme) {
            if (!valid(theme)) return;
            apply(theme);
            try { localStorage.setItem(key, theme); } catch { /* Cookie fallback below. */ }
            try { document.cookie = `${key}=${theme}; Path=/; Max-Age=31536000; SameSite=Lax${location.protocol === 'https:' ? '; Secure' : ''}`; } catch { /* Keep the current-page preference even without persistence. */ }
        },
    };
    apply(read());
})();
