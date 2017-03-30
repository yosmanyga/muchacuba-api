import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
injectTapEventPlugin();
import * as firebase from 'firebase';
import History from 'history/createHashHistory';
// import QueryString from 'query-string';
import AppBar from 'material-ui/AppBar';
import CircularProgress from 'material-ui/CircularProgress';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import Snackbar from 'material-ui/Snackbar';
import ConnectToServer from './ConnectToServer';
import ResolveElement from './ResolveElement';
import DocumentTitle from 'react-document-title';

import MuleFront from './Mule/Front';
// import ChuchuchuFront from './Chuchuchu/Front';
import InternautaFront from './Internauta/Front';

firebase.initializeApp({
    apiKey: "AIzaSyClvnStM8ZWjDNjjU-CaQ5NrjC2Ttd8eTI",
    authDomain: "chuchuchu-2bb11.firebaseapp.com",
    databaseURL: "https://chuchuchu-2bb11.firebaseio.com",
    storageBucket: "chuchuchu-2bb11.appspot.com",
    messagingSenderId: "1003585501404"
});

class Layout extends React.Component {
    static propTypes = {
        title: React.PropTypes.string,
        iconElementLeft: React.PropTypes.element,
        iconElementRight: React.PropTypes.element,
        onTitleTouchTap: React.PropTypes.func,
        notification: React.PropTypes.shape({
            message: React.PropTypes.string,
            finish: React.PropTypes.func
        }),
        style: React.PropTypes.object
    };

    render() {
        return (
            <DocumentTitle title={this.props.title}>
                <div style={{
                    ...this.props.style,
                }}>
                    <AppBar
                        title={this.props.title}
                        onTitleTouchTap={this.props.onTitleTouchTap}
                        iconElementLeft={this.props.iconElementLeft}
                        iconElementRight={this.props.iconElementRight}
                    />
                    {this.props.children}
                    {this.props.notification && this.props.notification.message !== null
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
    }

    componentWillMount() {
        /* Resolution */

        this._history.listen((location) => {
            this.setState({
                location: location
            });
        });

        /* Authentication */

        firebase.auth().onAuthStateChanged((user) => {
            if (user) {

            }
        });

        this.setState({
            location: this._history.location
        });
    }

    render() {
        if (this.state.location === null) {
            return (
                <MuiThemeProvider>
                    <CircularProgress size={20} style={{marginTop: "10px"}}/>
                </MuiThemeProvider>
            );
        }

        const layout = (
            <Layout
                title={null}
                iconElementLeft={null}
                iconElementRight={null}
                onTitleTouchTap={null}
                notification={this.state.notification}
            />
        );

        //const query = QueryString.parse(this.state.location.search);

        return (
            <MuiThemeProvider>
                {this._resolveElement.resolve(
                    this.state.location.pathname,
                    [
                        {
                            'url': '/mule',
                            'element': <MuleFront
                                url={this.state.location.pathname.replace('/mule', '')}
                                layout={layout}
                                onBackAuth={this._handleBackAuth}
                                onFrontAuth={this._handleFrontAuth}
                                onNavigate={(url) => this._handleNavigate('/mule' + url)}
                                onNotify={this._handleNotify}
                            />,
                            'def': true
                        },
                        /*
                        {
                            'url': '/chuchuchu',
                            'element': <ChuchuchuFront
                                url={this.state.location.pathname.replace('/chuchuchu', '')}
                                query={query}
                                layout={layout}
                                onBackAuth={this._handleBackAuth}
                                onNavigate={(url) => this._handleNavigate('/chuchuchu' + url)}
                                onNotify={this._handleNotify}
                            />
                        },
                        */
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
                        }
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
                        this._connectToServer
                            .post('/facebook/init-profile')
                            .auth(token)
                            .send({
                                facebook: {
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
                            })
                            .end((err, res) => {
                                if (err) {
                                    // TODO

                                    return;
                                }
                                onSuccess(token);
                            });
                    }).catch((error) => {
                        console.log(error);
                    });
                } else {
                    onSuccess('null')
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
                            ...this.state.notification,
                            message: null
                        }
                    }, typeof finish !== 'undefined' ? finish() : null);
                }
            }
        });
    }
}