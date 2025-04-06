/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

import jQuery from 'jquery';
window.$ = jQuery;

import bootstrap from 'bootstrap/dist/js/bootstrap';
window.bootstrap = bootstrap;

import lightbox from 'lightbox2';
lightbox.option({
    'resizeDuration': 500,
    'alwaysShowNavOnTouchDevices': true,
    'disableScrolling': true,
    'wrapAround': true,
})
import Hammer from 'hammerjs';

document.addEventListener("DOMContentLoaded", function () {
    const images = document.querySelectorAll('.lb-image img');

    images.forEach(function (img) {
        const mc = new Hammer(img);

        mc.get('pinch').set({ enable: true });
        mc.on('pinchin', onPinchIn);
        mc.on('pinchout', onPinchOut);
    });

    function onPinchIn(ev) {
        ev.target.style.transform = 'scale(' + (ev.scale * 0.8) + ')';
    }

    function onPinchOut(ev) {
        ev.target.style.transform = 'scale(' + (ev.scale * 1.2) + ')';
    }
});

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Инициализация всех компонентов Bootstrap
document.addEventListener('DOMContentLoaded', () => {
    // Инициализация тултипов
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Инициализация поповеров
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

    // Инициализация дропдаунов
    const dropdownTriggerList = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    [...dropdownTriggerList].map(dropdownTriggerEl => new bootstrap.Dropdown(dropdownTriggerEl));

    // Инициализация модальных окон
    const modalTriggerList = document.querySelectorAll('[data-bs-toggle="modal"]');
    [...modalTriggerList].map(modalTriggerEl => new bootstrap.Modal(modalTriggerEl));

    // Инициализация коллапсов
    const collapseTriggerList = document.querySelectorAll('[data-bs-toggle="collapse"]');
    [...collapseTriggerList].map(collapseTriggerEl => new bootstrap.Collapse(collapseTriggerEl, { toggle: false }));
});
