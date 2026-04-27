import axios from 'axios';

window.axios = axios;

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

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

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

    // // Инициализация модальных окон
    // const modalTriggerList = document.querySelectorAll('[data-bs-toggle="modal"]');
    // [...modalTriggerList].map(modalTriggerEl => new bootstrap.Modal(modalTriggerEl));
    //
    // // Инициализация коллапсов
    // const collapseTriggerList = document.querySelectorAll('[data-bs-toggle="collapse"]');
    // [...collapseTriggerList].map(collapseTriggerEl => new bootstrap.Collapse(collapseTriggerEl, { toggle: false }));
});