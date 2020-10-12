const routes = require("./routes.json");

module.exports = function(name, params) {
    if (routes[name] === undefined) {
        console.error("Unknown route ", name);
    } else {
        return (
            document.querySelector("base").getAttribute("href") +
            "/" +
            routes[name]
                .split("/")
                .map(s => {
                    if (s[0] == "{") {
                        var paramName = s.substring(1, s.length - 1);

                        if(params[paramName]) {
                            return params[paramName];
                        }

                        if (params.id) {
                            return params.id;
                        }

                        return paramName;
                    }

                    return s;
                })
                .join("/")
        );
    }
};
