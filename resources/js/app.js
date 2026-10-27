import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * Behaviour shared by every password field in the auth flow.
 *
 * Caps Lock detection is feedback, not decoration: a student typing a
 * password they cannot see needs to know why it keeps failing. The strength
 * score mirrors the server rule (8+ characters, letters and numbers) so the
 * meter never promises something the backend rejects.
 */
Alpine.data('passwordField', ({ strength = false, matchField = null } = {}) => ({
    visible: false,
    caps: false,
    value: '',
    matched: false,
    other: null,
    onOtherInput: null,

    get checks() {
        return {
            length: this.value.length >= 8,
            letter: /\p{L}/u.test(this.value),
            number: /\d/.test(this.value),
            long: this.value.length >= 12,
        };
    },

    get score() {
        const { length, letter, number, long } = this.checks;

        return [length, letter, number, long].filter(Boolean).length;
    },

    get scoreLabel() {
        return ['Muy corta', 'Débil', 'Aceptable', 'Buena', 'Fuerte'][this.score] ?? '';
    },

    get scoreTone() {
        return ['bg-slate-300', 'bg-amber-500', 'bg-blue-500', 'bg-blue-600', 'bg-emerald-600'][this.score];
    },

    get scoreText() {
        return this.score <= 1 ? 'text-amber-700' : this.score === 2 ? 'text-blue-700' : 'text-slate-500';
    },

    init() {
        if (!this.matchField) {
            return;
        }

        this.other = document.getElementById(this.matchField);

        if (!this.other) {
            return;
        }

        this.onOtherInput = () => this.compare();
        this.other.addEventListener('input', this.onOtherInput);
        this.$watch('value', () => this.compare());
    },

    compare() {
        this.matched = this.value !== '' && this.other?.value === this.value;
    },

    destroy() {
        if (this.other && this.onOtherInput) {
            this.other.removeEventListener('input', this.onOtherInput);
        }
    },
}));

/**
 * Numeric-only phone field.
 *
 * Students type the 8-digit national number; letters, spaces and symbols are
 * dropped on input and on paste instead of being rejected after a round trip.
 * `touched` keeps the amber warning from flashing while the first digits are
 * still being typed, and the counter in the label row makes the limit visible
 * before the field is full.
 */
Alpine.data('phoneField', ({ max = 8, initial = '' } = {}) => ({
    value: String(initial ?? '').replace(/\D+/g, '').slice(0, max),
    touched: false,
    max,

    get incomplete() {
        return this.touched && this.value.length > 0 && this.value.length < this.max;
    },

    onInput(event) {
        const clean = String(event.target.value ?? '').replace(/\D+/g, '').slice(0, this.max);

        if (event.target.value !== clean) {
            const atEnd = event.target.selectionStart === event.target.value.length;

            event.target.value = clean;

            if (atEnd) {
                event.target.setSelectionRange(clean.length, clean.length);
            }
        }

        this.value = clean;
        this.touched = false;
    },

    onBlur() {
        this.touched = true;
    },
}));

/**
 * Header navigation on small screens.
 *
 * The panel is absolutely positioned under the sticky header, so the page
 * underneath must not scroll: without the lock, a thumb swipe moves the
 * content instead of the menu and the panel loses its anchor.
 */
Alpine.data('mobileNav', () => ({
    open: false,

    init() {
        this.onResize = () => {
            if (window.matchMedia('(min-width: 1024px)').matches) {
                this.close();
            }
        };

        this.onKey = (event) => {
            if (event.key === 'Escape') {
                this.close();
            }
        };

        window.addEventListener('resize', this.onResize);
        window.addEventListener('keydown', this.onKey);
    },

    toggle() {
        this.open ? this.close() : this.show();
    },

    show() {
        this.open = true;
        document.documentElement.style.overflow = 'hidden';
    },

    close() {
        this.open = false;
        document.documentElement.style.overflow = '';
    },

    destroy() {
        window.removeEventListener('resize', this.onResize);
        window.removeEventListener('keydown', this.onKey);
        this.close();
    },
}));

/**
 * Account menu. Click-outside and Escape both close it, and the trigger keeps
 * a stable size because the avatar is fixed.
 */
Alpine.data('userMenu', () => ({
    open: false,

    toggle() {
        this.open = !this.open;
    },

    close() {
        this.open = false;
    },
}));

/**
 * Copy the current project URL to the clipboard.
 *
 * `navigator.clipboard` is unavailable on insecure origins, which is the norm
 * when a centre demos the portal over a local IP. The textarea fallback keeps
 * the action working there instead of failing silently.
 */
Alpine.data('shareLink', ({ url = window.location.href } = {}) => ({
    url,
    copied: false,
    failed: false,
    timer: null,

    async copy() {
        let copied = false;

        if (navigator.clipboard && window.isSecureContext) {
            try {
                await navigator.clipboard.writeText(this.url);
                copied = true;
            } catch {
                copied = false;
            }
        }

        if (!copied) {
            const field = document.createElement('textarea');
            field.value = this.url;
            field.setAttribute('readonly', '');
            field.style.position = 'fixed';
            field.style.opacity = '0';
            document.body.appendChild(field);
            field.select();

            try {
                copied = document.execCommand('copy');
            } catch {
                copied = false;
            } finally {
                field.remove();
            }
        }

        this.copied = copied;
        this.failed = !copied;

        window.clearTimeout(this.timer);
        this.timer = window.setTimeout(() => {
            this.copied = false;
            this.failed = false;
        }, 2400);
    },

    destroy() {
        window.clearTimeout(this.timer);
    },
}));

/**
 * Cover picker with an instant local preview.
 *
 * Server validation has the final word; these checks exist so a 3 MB photo is
 * rejected before the student waits through an upload. Object URLs are revoked
 * on every replacement and on destroy to avoid leaking the file in memory.
 */
Alpine.data('coverField', ({ current = null } = {}) => ({
    preview: current,
    filename: null,
    filesize: null,
    error: null,
    objectUrl: null,

    choose(event) {
        const file = event.target.files?.[0];

        this.error = null;

        if (!file) {
            return;
        }

        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            this.error = 'Selecciona una imagen JPG, PNG o WebP.';
            event.target.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            this.error = 'La imagen supera los 2 MB.';
            event.target.value = '';
            return;
        }

        this.release();
        this.objectUrl = URL.createObjectURL(file);
        this.preview = this.objectUrl;
        this.filename = file.name;
        this.filesize = this.format(file.size);
    },

    clear() {
        this.release();
        this.preview = current;
        this.filename = null;
        this.filesize = null;
        this.error = null;

        if (this.$refs.cover) {
            this.$refs.cover.value = '';
        }
    },

    format(bytes) {
        return bytes >= 1024 * 1024
            ? `${(bytes / 1024 / 1024).toFixed(1)} MB`
            : `${Math.max(1, Math.round(bytes / 1024))} KB`;
    },

    release() {
        if (this.objectUrl) {
            URL.revokeObjectURL(this.objectUrl);
            this.objectUrl = null;
        }
    },

    destroy() {
        this.release();
    },
}));

/**
 * Two-step delete without a modal.
 *
 * A modal for a single destructive row action is heavier than the decision
 * deserves. The confirmation appears in place, focuses itself, and expires so
 * a forgotten click cannot delete something minutes later.
 */
Alpine.data('deleteConfirm', () => ({
    confirming: false,
    timer: null,

    ask() {
        this.confirming = true;
        window.clearTimeout(this.timer);
        this.timer = window.setTimeout(() => this.confirming = false, 5000);
        this.$nextTick(() => this.$refs.confirm?.focus());
    },

    cancel() {
        this.confirming = false;
        window.clearTimeout(this.timer);
    },

    destroy() {
        window.clearTimeout(this.timer);
    },
}));

Alpine.start();
