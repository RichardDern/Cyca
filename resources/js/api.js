/**
 * Default headers to apply to every query
 */
const defaultHeaders = {
    "Content-Type": "application/json",
    "X-Requested-With": "XMLHttpRequest",
    "X-CSRF-TOKEN": document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content")
};

export default {
    /**
     * Send a request and return resulting JSON
     */
    async send(url, params, method = "POST") {
        let options = {
            method: method,
            headers: defaultHeaders
        };

        if (params) {
            options["body"] = JSON.stringify(params);
        }

        const response = await fetch(url, options);

        try {
            const json = await response.json();
            return json;
        } catch {
            return response;
        }
    },

    /**
     * Send a GET request and return resulting JSON
     */
    async get(url) {
        return this.send(url, null, "GET");
    },

    /**
     * Send a POST request and return resulting JSON
     */
    async post(url, params) {
        return this.send(url, params, "POST");
    },

    /**
     * Send a PUT request and return resulting JSON
     */
    async put(url, params) {
        return this.send(url, params, "PUT");
    },

    /**
     * Send a DELETE request and return resulting JSON
     */
    async delete(url, params) {
        return this.send(url, params, "DELETE");
    }
};
