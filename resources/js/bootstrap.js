/**
 * Dependencies
 */

window._ = require('lodash');
window.axios = require('axios');
window.Vue = require('vue');
window.collect = require('collect.js');
window.moment = require('moment');
window.io = require('socket.io-client');

import Echo from "laravel-echo"

moment.locale(document.querySelector('html').getAttribute('lang'));

/**
 * Preparing axios
 */

window.axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').getAttribute('content')
};

/**
 * Laravel Echo
 */

window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: window.location.hostname
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
