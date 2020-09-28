/**
 * Dependencies
 */

window._ = require('lodash');
window.Vue = require('vue');
window.collect = require('collect.js');
window.Pusher = require('pusher-js');

import Echo from "laravel-echo"
import route from 'ziggy';
import { Ziggy } from './routes';
import api from './api';

window.route = route;
window.Ziggy = Ziggy;
window.api = api;

/**
 * Laravel Echo
 */

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'cyca',
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
});

/**
 * Preparing Vue
 */

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.mixin({
    methods: {
        /**
         * Return full URL for specified route
         */
        route: (name, params, absolute) => route(name, params, absolute, Ziggy),
        /**
         * Translate specified string
         * @param {*} langString
         */
        __: function(langString) {
            const translation = lang[langString];

            if(translation) {
                return translation;
            }

            return langString;
        }
    }
})
