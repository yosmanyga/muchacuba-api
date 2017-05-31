import {login} from '../Base/Firebase';
import {request, handle} from '../Base/Request';

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

export {
    collectLogs,
    deleteLogGroup
};