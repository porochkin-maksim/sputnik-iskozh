import axios from 'axios';

import jQuery from 'jquery';

window.$ = jQuery;

import bootstrap from 'bootstrap/dist/js/bootstrap.bundle';

window.bootstrap = bootstrap;

import lightbox from 'lightbox2';

lightbox.option({
    resizeDuration             : 500,
    alwaysShowNavOnTouchDevices: true,
    disableScrolling           : true,
    wrapAround                 : true,
});

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials                    = true;

import { installCsrfRefreshInterceptor } from './utils/csrf-refresh.js';

installCsrfRefreshInterceptor();

document.addEventListener('DOMContentLoaded', () => {
    // Инициализация тултипов
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Инициализация поповеров
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

    // Инициализация дропдаунов
    // Для всех существующих и будущих дропдаунов
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-bs-toggle="dropdown"]');
        if (btn) {
            e.preventDefault();
            const dropdown = bootstrap.Dropdown.getInstance(btn);
            if (dropdown) {
                dropdown.toggle();
            }
            else {
                // На случай, если экземпляр ещё не создан
                new bootstrap.Dropdown(btn).toggle();
            }
        }
    }, false);

    document.addEventListener('click', (event) => {
        const toggler = event.target.closest('[data-navbar-toggle="collapse"]');
        if ( ! toggler) {
            return;
        }

        const targetSelector = toggler.getAttribute('data-navbar-target');
        const target = targetSelector ? document.querySelector(targetSelector) : null;
        if ( ! target) {
            return;
        }

        event.preventDefault();

        const collapse = bootstrap.Collapse.getOrCreateInstance(target, { toggle: false });
        collapse.toggle();

        const isShown = target.classList.contains('show');
        toggler.setAttribute('aria-expanded', isShown ? 'true' : 'false');
    }, false);

    document.addEventListener('click', async (event) => {
        const button = event.target.closest('#cookie-agreement-btn');
        if (!button) {
            return;
        }

        event.preventDefault();

        const banner = document.getElementById('cookie-banner');
        if (!banner) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            const response = await fetch(button.dataset.endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (response.ok) {
                banner.remove();
            }
        }
        catch (error) {
            console.error('Cookie agreement request failed', error);
        }
    }, false);

    // // Инициализация модальных окон
    // const modalTriggerList = document.querySelectorAll('[data-bs-toggle="modal"]');
    // [...modalTriggerList].map(modalTriggerEl => new bootstrap.Modal(modalTriggerEl));
    //
    // // Инициализация коллапсов
    // const collapseTriggerList = document.querySelectorAll('[data-bs-toggle="collapse"]');
    // [...collapseTriggerList].map(collapseTriggerEl => new bootstrap.Collapse(collapseTriggerEl, { toggle: false }));
});
