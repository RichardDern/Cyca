require("./modules/bootstrap");
const components = require("./modules/components")("highlights");

const app = new Vue({
    components: { components },
    el: "#app"
});
