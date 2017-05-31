import * as firebase from 'firebase';

const initialize = (app) => {
    firebase.initializeApp(app);
};

const verify = (
    onCredential,  // (token, user)
    onNoCredential, // ()
    refresh = false
) => {
    // if (process.env.NODE_ENV === 'development') {
    //     success(
    //         'dev',
    //         {
    //             id: 1,
    //             displayName: 'Dev',
    //             email: 'dev@localhost',
    //             photoURL: 'https://storage.googleapis.com/material-design/publish/material_v_11/assets/0B5-3BCtasWxEV2R6bkNDOUxFZ00/style_icons_product_human_best_do1.png'
    //         }
    //     );
    //
    //     return;
    // }

    firebase.auth().getRedirectResult()
        .then((result) => {
            if (result.credential) {
                firebase.auth().currentUser.getIdToken(refresh).then((token) => {
                    onCredential(
                        token,
                        {
                            id: result.user.providerData[0].uid,
                            name: typeof result.user.providerData[0].displayName !== 'undefined'
                                ? result.user.providerData[0].displayName
                                : null,
                            email: typeof result.user.providerData[0].email !== 'undefined'
                                ? result.user.providerData[0].email
                                : null,
                            picture: typeof result.user.providerData[0].photoURL !== 'undefined'
                                ? result.user.providerData[0].photoURL
                                : null
                        }
                    );
                }).catch((error) => {
                    console.error(error);
                });
            } else {
                onNoCredential();
            }
        })
        .catch((error) => {
            console.error(error);
        });
};

const login = (success, refresh) => {
    verify(
        (token, user) => {
            success(token, user);
        },
        () => {
            firebase.auth().signInWithRedirect(
                new firebase.auth.FacebookAuthProvider()
            );
        },
        refresh
    );
};

const logout = () => {
    firebase.auth().signOut().then(() => {
        // It does not logout from facebook
    }).catch((error) => {
    });
};

export {initialize, verify, login, logout};