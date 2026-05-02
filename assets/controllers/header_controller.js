import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['panel', 'drawer', 'backdrop', 'toggle', 'closeButton'];

    connect() {
        this.boundEscape = this.onEscape.bind(this);
    }

    disconnect() {
        document.removeEventListener('keydown', this.boundEscape);
        this.unlockBody();
    }

    open() {
        // 1. Render the panel (display: block) so transitions can happen.
        this.panelTarget.classList.remove('hidden');

        // 2. Force a reflow so the browser registers the initial off-screen state.
        // eslint-disable-next-line no-unused-expressions
        this.drawerTarget.offsetHeight;

        // 3. Next frame, swap the classes that drive the transition.
        requestAnimationFrame(() => {
            this.drawerTarget.classList.remove('translate-x-full');
            this.drawerTarget.classList.add('translate-x-0');
            this.backdropTarget.classList.remove('opacity-0');
            this.backdropTarget.classList.add('opacity-100');
        });

        this.toggleTarget.setAttribute('aria-expanded', 'true');
        this.lockBody();
        document.addEventListener('keydown', this.boundEscape);

        // Defer focus move until the drawer is actually visible.
        setTimeout(() => this.closeButtonTarget?.focus(), 50);
    }

    close() {
        // Animate out
        this.drawerTarget.classList.add('translate-x-full');
        this.drawerTarget.classList.remove('translate-x-0');
        this.backdropTarget.classList.add('opacity-0');
        this.backdropTarget.classList.remove('opacity-100');

        this.toggleTarget.setAttribute('aria-expanded', 'false');
        document.removeEventListener('keydown', this.boundEscape);

        // After the transition, hide the wrapper completely.
        setTimeout(() => {
            this.panelTarget.classList.add('hidden');
            this.unlockBody();
            this.toggleTarget.focus();
        }, 300);
    }

    onEscape(event) {
        if (event.key === 'Escape') {
            event.preventDefault();
            this.close();
        }
    }

    lockBody() {
        document.body.style.overflow = 'hidden';
    }

    unlockBody() {
        document.body.style.overflow = '';
    }
}
