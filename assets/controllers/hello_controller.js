import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["menu", "openButton", "closeButton"]

    connect() {
        console.log('Stimulus controller connecté');
    }

    toggleMenu() {
        this.menuTarget.classList.toggle("hidden");
    }

    closeMenu() {
        this.menuTarget.classList.add("hidden");
    }
}