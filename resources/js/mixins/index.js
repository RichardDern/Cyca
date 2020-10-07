import route from 'ziggy';
import { Ziggy } from '../modules/routes';

window.route = route;
window.Ziggy = Ziggy;

Vue.mixin({
    methods: {
        /**
         * Return full URL for specified route
         */
        route: (name, params, absolute) => route(name, params, absolute, Ziggy),

        /**
         * Return url to an icon
         */
        icon: (name) => {
            const iconsFileUrl = document
            .querySelector('meta[name="icons-file-url"]')
            .getAttribute("content")

            const url = iconsFileUrl + '#' + name;

            return url;
        },

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
});
