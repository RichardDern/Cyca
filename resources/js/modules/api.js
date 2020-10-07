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
    async send(url, params, method = "POST", upload = false) {
        let headers = defaultHeaders;

        if(upload) {
            headers['Content-Type'] = 'multipart/form-data';
        }

        let options = {
            method: method,
            headers: headers
        };

        if (params) {
            if(upload) {
                options.body = params;
            } else {
                options.body = JSON.stringify(params);
            }
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
    async post(url, params, upload = false) {
        return this.send(url, params, "POST", upload);
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
