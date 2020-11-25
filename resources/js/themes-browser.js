require("./modules/bootstrap");
const components = require("./modules/components")("themesBrowser");

const app = new Vue({
    components: { components },
    el: "#app"
});
