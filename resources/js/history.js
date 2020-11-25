require("./modules/bootstrap");
const components = require("./modules/components")("historyBrowser");

const app = new Vue({
    components: { components },
    el: "#app"
});
