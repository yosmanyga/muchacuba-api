import {login} from '../Base/Firebase';
import {request, localRequest, handle} from '../Base/Request';

const collectLogs = (
    onSuccess, // (logs)
) => {
    login((token) => {
        request(
            'GET',
            '/internauta/collect-logs',
            token,
            null,
            (response) => {
                handle(response, [
                    {
                        code: 'success',
                        callback: (payload) => {
                            onSuccess(payload);
                        }
                    }
                ]);
            }
        )
    });
};

const deleteLogGroup = (
    id,
) => {
    login((token) => {
        request(
            'POST',
            '/internauta/delete-log-group',
            token,
            {
                id: id
            },
            (response) => {
                handle(response, [
                    {
                        code: 'success',
                        callback: (payload) => {
                        }
                    }
                ]);
            }
        )
    });
};

const debugLocally = (
    sender,
    recipient,
    subject,
    body,
    onSuccess
) => {
    login((token) => {
        localRequest(
            'POST',
            '/internauta/mailgun/push-request',
            token,
            {
                sender: sender,
                recipient: recipient,
                subject: subject,
                'stripped-text': body
            },
            (response) => {
                handle(response, [
                    {
                        code: 'success',
                        callback: () => {
                            onSuccess();
                        }
                    }
                ]);
            }
        )
    });
};

export {
    collectLogs,
    deleteLogGroup,
    debugLocally
};