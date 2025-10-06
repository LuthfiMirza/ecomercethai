export default function megaMenu(initial = {}) {
    const categories = Array.isArray(initial.categories) ? initial.categories : [];

    return {
        open: false,
        categories,
        activeId: categories[0]?.id ?? null,
        panelStyle: '',
        hovering: false,
        _closeTimeout: null,
        get activeCategory() {
            if (!Array.isArray(this.categories) || this.categories.length === 0) {
                return null;
            }

            return this.categories.find(cat => cat.id === this.activeId) || this.categories[0];
        },
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => this.positionPanel());
            }
        },
        openMenu() {
            this.open = true;
            this.$nextTick(() => this.positionPanel());
        },
        close() {
            if (this._closeTimeout) {
                clearTimeout(this._closeTimeout);
                this._closeTimeout = null;
            }
            this.open = false;
            this.hovering = false;
        },
        setActive(id) {
            this.activeId = id;
        },
        setHover(state) {
            this.hovering = state;

            if (state) {
                if (this._closeTimeout) {
                    clearTimeout(this._closeTimeout);
                    this._closeTimeout = null;
                }
                this.openMenu();
                return;
            }

            if (this._closeTimeout) {
                clearTimeout(this._closeTimeout);
            }

            this._closeTimeout = setTimeout(() => {
                if (!this.hovering) {
                    this.close();
                }
            }, 120);
        },
        positionPanel() {
            const trigger = this.$refs?.trigger;
            if (!trigger) {
                return;
            }

            const rect = trigger.getBoundingClientRect();
            const top = rect.bottom + 12; // 12px gap below trigger

            this.panelStyle = `top:${top}px;`;
        },
        init() {
            this.positionPanel();

            const reposition = () => {
                if (this.open) {
                    this.positionPanel();
                }
            };

            window.addEventListener('resize', reposition);
            window.addEventListener('scroll', reposition, { passive: true });
        },
    };
}
