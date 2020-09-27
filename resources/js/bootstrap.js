/**
 * Dependencies
 */

window._ = require('lodash');
window.Vue = require('vue');
window.collect = require('collect.js');
window.Pusher = require('pusher-js');

import Echo from "laravel-echo"

/**
 * Preparing fetch
 */

var defaultFetchHeaders = {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').getAttribute('content')
};

/**
 * Sent a GET request
 * @param {*} url
 */
window.apiGet = async (url) => {
    const response = await fetch(url, {
        headers: defaultFetchHeaders
    });

    const json = await response.json();

    return json;
};

/**
 * Sent a generic request
 * @param {*} url
 */
window.apiSend = async(url, params, method = 'POST') => {
    let options = {
        method: method,
        headers: defaultFetchHeaders
    };

    if(params) {
        options['body'] = JSON.stringify(params);
    }

    const response = await fetch(url, options);

    try {
        const json = await response.json();
        return json;
    } catch {
        return response;
    }
}

/**
 * Sent a POST request
 * @param {*} url
 */
window.apiPost = async (url, params) => {
    return await apiSend(url, params, 'POST');
};

/**
 * Sent a PUT request
 * @param {*} url
 */
window.apiPut = async (url, params) => {
    return await apiSend(url, params, 'PUT');
};

/**
 * Sent a DELETE request
 * @param {*} url
 */
window.apiDelete = async (url, params) => {
    return await apiSend(url, params, 'DELETE');
};

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
