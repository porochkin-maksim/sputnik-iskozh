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
    const images = document.querySelectorAll('.lightbox img');

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

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
