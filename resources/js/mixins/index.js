import router from "../modules/router";

window.route = router;

Vue.mixin({
    methods: {
        route: (name, params) => route(name, params),
        /**
         * Return url to an icon
         */
        icon: name => {
            const iconsFileUrl = document
                .querySelector('meta[name="icons-file-url"]')
                .getAttribute("content");

            const url = iconsFileUrl + "#" + name;

            return url;
        },

        /**
         * Translate specified string
         * @param {*} langString
         */
        __: function(langString) {
            const translation = lang[langString];

            if (translation) {
                return translation;
            }

            return langString;
        }
    }
});
