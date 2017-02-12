import Superagent from 'superagent';

export default class ConnectToServer  {
    constructor() {
        this.base = '';

        if (process.env.NODE_ENV !== "production") {
            this.debug = true;
        } else {
            this.debug = true;
        }
    }

    get(url) {
        if (this.debug) {
            url += '?XDEBUG_SESSION_START=phpstorm';
        }

        this.call = Superagent.get(this.base + url);

        return this;
    }

    post(url) {
        if (this.debug) {
            url += '?XDEBUG_SESSION_START=phpstorm';
        }

        this.call = Superagent.post(this.base + url);

        return this;
    }

    auth(token) {
        this.call.set('Authorization', token);

        return this;
    }

    send(data) {
        this.call.send(data);

        return this;
    }

    field(key, value) {
        this.call.field(key, value);

        return this;
    }

    attach(key, value) {
        this.call.attach(key, value);

        return this;
    }

    end(callback) {
        this.call.end(callback);
    }
}
