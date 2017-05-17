import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
injectTapEventPlugin();
import * as firebase from 'firebase';
import History from 'history/createHashHistory';
import QueryString from 'query-string';
import getMuiTheme from 'material-ui/styles/getMuiTheme';
import {cyan800} from 'material-ui/styles/colors';
import AppBar from 'material-ui/AppBar';
import CircularProgress from 'material-ui/CircularProgress';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import Snackbar from 'material-ui/Snackbar';
import ConnectToServer from './ConnectToServer';
import ResolveElement from './ResolveElement';
import DocumentTitle from 'react-document-title';

import AloleiroFront from './Aloleiro/Front';
import MuleFront from './Mule/Front';
import ChuchuchuFront from './Chuchuchu/Front';
import InternautaFront from './Internauta/Front';
import TopupFront from './Topup/Front';

firebase.initializeApp({
    apiKey: "AIzaSyApFrRpHVKRK1pvBchd0rcC_ycUa0H-5AU",
    authDomain: "cubalider-muchacuba.firebaseapp.com",
    databaseURL: "https://cubalider-muchacuba.firebaseio.com",
    projectId: "cubalider-muchacuba",
    storageBucket: "cubalider-muchacuba.appspot.com",
    messagingSenderId: "43324202525"
});

const muiTheme = getMuiTheme({
    palette: {
        textColor: cyan800,
    }
});

class Layout extends React.Component {
    static propTypes = {
        title: React.PropTypes.string,
        bar: React.PropTypes.node,
        drawer: React.PropTypes.element,
        iconElementLeft: React.PropTypes.element,
        iconElementRight: React.PropTypes.element,
        onTitleTouchTap: React.PropTypes.func,
        notification: React.PropTypes.shape({
            message: React.PropTypes.string,
            finish: React.PropTypes.func
        }),
        style: React.PropTypes.object
    };

    constructor(props) {
        super(props);

        this.state = {
            drawer: false
        };
    }

    render() {
        return (
            <DocumentTitle title={this.props.title ? this.props.title : ''}>
                <div style={{height: "100%"}}>
                    <AppBar
                        title={this.props.bar}
                        onTitleTouchTap={this.props.onTitleTouchTap}
                        iconElementLeft={this.props.iconElementLeft}
                        iconElementRight={this.props.iconElementRight}
                        onLeftIconButtonTouchTap={() => {
                            this.setState({drawer: true});
                        }}
                    />
                    <div style={{
                        height: "calc(100% - 64px)",
                        ...this.props.style
                    }}>
                        {this.props.children}
                    </div>
                    {typeof this.props.drawer !== 'undefined'
                        ? <this.props.drawer.type
                            {...this.props.drawer.props}
                            docked={false}
                            open={this.state.drawer}
                            onRequestChange={(open) => this.setState({drawer: open})}
                        >
                            {this.props.drawer.props.children}
                        </this.props.drawer.type>
                        : null
                    }
                    {
                        this.props.notification
                        && this.props.notification.message !== null
                            ? <Snackbar
                                open={true}
                                message={this.props.notification.message}
                                autoHideDuration={4000}
                                onRequestClose={typeof this.props.notification.finish !== 'undefined'
                                    ? this.props.notification.finish
                                    : null
                                }
                            />
                            : null
                    }
                </div>
            </DocumentTitle>
        );
    }
}

export default class Front extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            location: null,
            notification: {
                message: null,
                finish: null
            }
        };

        this._connectToServer = new ConnectToServer();
        this._resolveElement = new ResolveElement();
        this._history = new History();

        this._handleBackAuth = this._handleBackAuth.bind(this);
        this._handleFrontAuth = this._handleFrontAuth.bind(this);
        this._handleNavigate = this._handleNavigate.bind(this);
        this._handleNotify = this._handleNotify.bind(this);
        this._handleError = this._handleError.bind(this);
    }

    componentWillMount() {
        /* Authentication */

        firebase.auth().onAuthStateChanged((user) => {
            if (user) {

            }
        });

        // var connectedRef = firebase.database().ref(".info/connected");
        // connectedRef.on("value", function(snap) {
        //     if (snap.val() === true) {
        //         alert("connected");
        //     } else {
        //         alert("not connected");
        //     }
        // });

        /* Resolution */

        this._history.listen((location) => {
            this.setState({
                location: location
            });
        });

        this.setState({
            location: this._history.location
        });
    }

    render() {
        if (this.state.location === null) {
            return (
                <MuiThemeProvider muiTheme={muiTheme}>
                    <CircularProgress size={20} style={{marginTop: "10px"}}/>
                </MuiThemeProvider>
            );
        }

        const layout = (
            <Layout
                title={null}
                bar={null}
                iconElementLeft={null}
                iconElementRight={null}
                onTitleTouchTap={null}
                notification={this.state.notification}
            />
        );

        const query = QueryString.parse(this.state.location.search);

        return (
            <MuiThemeProvider muiTheme={muiTheme}>
                {this._resolveElement.resolve(
                    this.state.location.pathname,
                    [
                        {
                            'url': '/internauta',
                            'element': <InternautaFront
                                url={this.state.location.pathname.replace('/internauta', '')}
                                layout={layout}
                                onBackAuth={this._handleBackAuth}
                                onFrontAuth={this._handleFrontAuth}
                                onNavigate={(url) => this._handleNavigate('/internauta' + url)}
                                onNotify={this._handleNotify}
                            />,
                            'def': true
                        },
                        {
                            'url': '/mule',
                            'element': <MuleFront
                                url={this.state.location.pathname.replace('/mule', '')}
                                layout={layout}
                                query={query}
                                onBackAuth={this._handleBackAuth}
                                onFrontAuth={this._handleFrontAuth}
                                onNavigate={(url) => this._handleNavigate('/mule' + url)}
                                onNotify={this._handleNotify}
                                onError={this._handleError}
                            />
                        },
                        {
                            'url': '/chuchuchu',
                            'element': <ChuchuchuFront
                                url={this.state.location.pathname.replace('/chuchuchu', '')}
                                query={query}
                                layout={layout}
                                onBackAuth={this._handleBackAuth}
                                onFrontAuth={this._handleFrontAuth}
                                onNavigate={(url) => this._handleNavigate('/chuchuchu' + url)}
                                onNotify={this._handleNotify}
                                onError={this._handleError}
                            />
                        },
                        {
                            'hostname': 'mundorecarga.com',
                            'url': '',
                            'element': <TopupFront
                                url={this.state.location.pathname.replace('', '')}
                                layout={layout}
                                onBackAuth={this._handleBackAuth}
                                onFrontAuth={this._handleFrontAuth}
                                onLogout={this._handleLogout}
                                onNavigate={(url) => this._handleNavigate('' + url)}
                                onNotify={this._handleNotify}
                                onError={this._handleError}
                            />
                        },
                        {
                            'hostname': 'holapana.com',
                            'url': '',
                            'element': <AloleiroFront
                                url={this.state.location.pathname.replace('', '')}
                                layout={layout}
                                onBackAuth={this._handleBackAuth}
                                onFrontAuth={this._handleFrontAuth}
                                onLogout={this._handleLogout}
                                onNavigate={(url) => this._handleNavigate('' + url)}
                                onNotify={this._handleNotify}
                                onError={this._handleError}
                            />
                        },
                    ]
                )}
            </MuiThemeProvider>
        );
    }

    _handleBackAuth(onSuccess, onError) {
        firebase.auth().getRedirectResult()
            .then((result) => {
                if (result.credential) {
                    firebase.auth().currentUser.getToken(true).then((token) => {
                        const profile = {
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
                        };

                        this._connectToServer
                            .post('/init-facebook-user')
                            .auth(token)
                            .send(profile)
                            .end((err, res) => {
                                if (err) {
                                    // TODO

                                    return;
                                }

                                onSuccess(
                                    {
                                        ...profile,
                                        token: token,
                                        roles: res.body.roles
                                    }
                                );
                            });
                    }).catch((error) => {
                        console.log(error);
                    });
                } else {
                    onSuccess({
                        token: 'null'
                    })
                }
            })
            .catch((error) => {
                onError(error)
            });
    }

    _handleFrontAuth() {
        firebase.auth().signInWithRedirect(
            new firebase.auth.FacebookAuthProvider()
        );
    }

    _handleLogout() {
        firebase.auth().signOut().then(() => {
            // It does not logout from facebook
        }).catch((error) => {
        });
    }

    _handleNavigate(url) {
        if (url === this.state.location.pathname) {
            return;
        }

        this._history.push(url);
    }

    _handleNotify(message, finish) {
        this.setState({
            notification: {
                message: message,
                finish: () => {
                    this.setState({
                        notification: {
                            message: null,
                            finish: null
                        }
                    }, typeof finish !== 'undefined' ? finish() : null);
                }
            }
        });
    }

    _handleError(status, response) {
        if (status === 401) {
            if (response.type === 'expired-credential') {
                this._handleFrontAuth();

                return;
            }
        }

        this._handleNotify('Se ha producido un error en el sistema');
    }
}