import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['firstField', 'form'];
    static values = { currentStep: Number };

    connect() {
        if (this.hasFirstFieldTarget && this.currentStepValue > 1) {
            queueMicrotask(() => this.firstFieldTarget.focus({ preventScroll: false }));
        }

        if (this.hasFormTarget) {
            this._onSubmit = this._onSubmit.bind(this);
            this.formTarget.addEventListener('submit', this._onSubmit);
        }
    }

    disconnect() {
        if (this.hasFormTarget) {
            this.formTarget.removeEventListener('submit', this._onSubmit);
        }
    }

    _onSubmit(event) {
        const clicked = event.submitter;
        if (!clicked) return;

        // Defer the visual lock so the browser has already serialized the clicked
        // button's name/value in the form payload. Disabling synchronously here
        // would strip the button from the request and break FormFlow navigation.
        setTimeout(() => {
            clicked.classList.add('opacity-70', 'cursor-wait', 'pointer-events-none');
            if (clicked.tagName === 'BUTTON' || clicked.tagName === 'INPUT') {
                clicked.dataset.originalLabel = clicked.value || clicked.textContent;
                if (clicked.tagName === 'INPUT') {
                    clicked.value = 'Envoi en cours…';
                } else {
                    clicked.textContent = 'Envoi en cours…';
                }
            }
        }, 0);
    }
}
