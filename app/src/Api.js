import {login} from './Base/Firebase';
import {request, handle} from './Base/Request';

const pickUser = (
    token,
    onSuccess // (user)
) => {
    request(
        'POST',
        '/pick-user',
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
    );
};

const initializeUser = (
    onSuccess, // (user)
) => {
    login((token, user) => {
        request(
            'POST',
            '/initialize-facebook-user',
            token,
            {
                id: user.id,
                name: user.name,
                email: user.email,
                picture: user.picture
            },
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
        );
    });
};

export {
    pickUser,
    initializeUser
};