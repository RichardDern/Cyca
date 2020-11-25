require("./modules/bootstrap");
const components = require("./modules/components")("import");

const app = new Vue({
    components: { components },
    el: "#app"
});
