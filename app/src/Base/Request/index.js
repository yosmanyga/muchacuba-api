import Superagent from 'superagent';

const request = (
    method,
    uri,
    token,
    data,
    onSuccess // onSuccess(response)
) => {
    let request = (method === 'GET')
        ? Superagent.get(uri)
        : Superagent.post(uri);

    request.send(data);

    if (token) {
        request.set('Authorization', token);
    }

    request.end((err, res) => {
        if (onSuccess !== 'undefined') {
            onSuccess(res)
        }
    });
};

const localRequest = (
    method,
    uri,
    token,
    data,
    onSuccess // onSuccess(response)
) => {
    uri = 'https://localhost:3000' + uri;

    let request = (method === 'GET')
        ? Superagent.get(uri)
        : Superagent.post(uri);

    request.send(data);

    if (token) {
        request.set('Authorization', token);
    }

    request.end((err, res) => {
        if (onSuccess !== 'undefined') {
            onSuccess(res)
        }
    });
};

const handle = (response, handlers) => {
    handlers.forEach((handler) => {
        if (
            response.status === 200
            && response.body.code === handler.code
        ) {
            handler.callback(response.body.payload);

            return;
        }

        if (
            response.status === 200
            && response.body.code === 'expired-token'
            && handler.code === 'expired-token'
        ) {
            handler.callback();

            return;
        }

        if (
            response.status === 401
            && handler.code === 'unauthorized'
        ) {
            handler.callback();
        }
    })
};

export {request, localRequest, handle};