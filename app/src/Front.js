/* global FB */

import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
injectTapEventPlugin();
import History from 'history/createHashHistory';
import QueryString from 'query-string';
import LocalStorage from 'local-storage';
import AppBar from 'material-ui/AppBar';
import CircularProgress from 'material-ui/CircularProgress';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import Paper from 'material-ui/Paper';
import Snackbar from 'material-ui/Snackbar';
import Button from './Button';
import ConnectToServer from './ConnectToServer';
import ResolveElement from './ResolveElement';
import DocumentTitle from 'react-document-title';

// import MuleFront from './Mule/Front';
import ChuchuchuFront from './Chuchuchu/Front';
import InternautaFront from './Internauta/Front';

class ManageAuthentication {
    constructor() {
        this._connectToServer = new ConnectToServer();
    }

    under(onSuccess, onFailure) {
        const token = LocalStorage.get('token');

        if (token) {
            onSuccess(token);

            return;
        }

        this._init(() => {
            FB.getLoginStatus((response) => {
                this._process(response, onSuccess, onFailure);
            });
        });
    }

    front(onSuccess, onFailure) {
        this._init(() => {
            FB.login((response) => {
                this._process(response, onSuccess, onFailure);
            });
        });
    }

    _init(finish) {
        if (document.getElementById('facebook-jssdk')) {
            finish();

            return;
        }

        ((d, s, id) => {
            const element = d.getElementsByTagName(s)[0];
            const fjs = element;
            let js = element;
            if (d.getElementById(id)) { return; }
            js = d.createElement(s); js.id = id;
            js.src = '//connect.facebook.net/es_ES/all.js';
            fjs.parentNode.insertBefore(js, fjs);
        })(document, 'script', 'facebook-jssdk');

        let fbRoot = document.getElementById('fb-root');
        if (!fbRoot) {
            fbRoot = document.createElement('div');
            fbRoot.id = 'fb-root';
            document.body.appendChild(fbRoot);
        }

        window.fbAsyncInit = () => {
            window.FB.init({
                appId: '837622639692718',
                cookie: false,
                xfbml: false,
                version: 'v2.8'
            });

            finish();
        };
    }

    _process(response, onSuccess, onFailure) {
        switch(response.status) {
            case 'connected':
                this._authenticate(
                    response,
                    (token) => {
                        LocalStorage.set('token', token);

                        onSuccess(token);
                    },
                    onFailure
                );

                break;
            case 'not_authorized':
                onFailure();

                break;
            case 'unknown':
                onFailure();

                break;
            default:
                onFailure();
        }
    }

    _authenticate(response, onSuccess, onFailure) {
        this._connectToServer
            .get('/authenticate/' + response.authResponse.accessToken)
            .send()
            .end((err, res) => {
                if (err) {
                    onFailure();

                    return;
                }

                onSuccess(res.body.token);

                // // Get some personal data
                // FB.api('/me', (response) => {
                // });
            });
    }
}

class Login extends React.Component {
    static propTypes = {
        back: React.PropTypes.string,
        onFrontAuth: React.PropTypes.func.isRequired,
        onNavigate: React.PropTypes.func.isRequired,
    };

    render() {
        return (
            <Paper
                style={{
                    display: "flex",
                    flexDirection: "column",
                    alignItems: "center",
                    padding: '10px',
                }}
                zDepth={0}
            >
                <Button
                    label="Entrar"
                    labelAfterTouchTap="Entrando..."
                    icon="account_box"
                    onTouchTap={(finish) => {
                        this.props.onFrontAuth(
                            () => {
                                finish();

                                this.props.onNavigate(this.props.back);
                            },
                            finish
                        )
                    }}
                />
            </Paper>
        );
    }
}

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
            authentication: {
                authenticating: false,
                authenticated: false,
                token: null
            },
            notification: {
                message: null,
                finish: null
            }
        };

        this._connectToServer = new ConnectToServer();
        this._manageAuthentication = new ManageAuthentication();
        this._resolveElement = new ResolveElement();
        this._history = new History();

        this._handleUnderAuth = this._handleUnderAuth.bind(this);
        this._handleFrontAuth = this._handleFrontAuth.bind(this);
        this._handleUnauthorized = this._handleUnauthorized.bind(this);
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

        const query = QueryString.parse(this.state.location.search);

        return (
            <MuiThemeProvider>
                {this._resolveElement.resolve(
                    this.state.location.pathname,
                    [
                        /*
                        {
                            'url': '/mule',
                            'element': <MuleFront
                                url={this.state.location.pathname.replace('/mule', '')}
                                layout={layout}
                                authentication={this.state.authentication}
                                onUnderAuth={(onSuccess, backUrl) => {
                                    this._handleUnderAuth(onSuccess, '/mule' + backUrl);
                                }}
                                onUnauthorized={(url) => this._handleNavigate('/login?back=/mule' + url)}
                                onNavigate={(url) => this._handleNavigate('/mule' + url)}
                                onNotify={this._handleNotify}
                            />,
                            'def': true
                        },
                        */
                        {
                            'url': '/chuchuchu',
                            'element': <ChuchuchuFront
                                url={this.state.location.pathname.replace('/chuchuchu', '')}
                                query={query}
                                layout={layout}
                                onUnderAuth={this._handleUnderAuth}
                                onUnauthorized={(url) => this._handleUnauthorized('/chuchuchu' + url)}
                                onNavigate={(url) => this._handleNavigate('/chuchuchu' + url)}
                                onNotify={this._handleNotify}
                            />
                        },
                        {
                            'url': '/internauta',
                            'element': <InternautaFront
                                url={this.state.location.pathname.replace('/internauta', '')}
                                query={query}
                                layout={layout}
                                onUnderAuth={this._handleUnderAuth}
                                onUnauthorized={(url) => this._handleUnauthorized('/internauta' + url)}
                                onNavigate={(url) => this._handleNavigate('/internauta' + url)}
                                onNotify={this._handleNotify}
                            />,
                        },
                        {
                            'url': '/login',
                            'element': <Login
                                back={typeof query.back !== 'undefined' ? query.back : ''}
                                onFrontAuth={this._handleFrontAuth}
                                onNavigate={this._handleNavigate}
                            />
                        }
                    ]
                )}
            </MuiThemeProvider>
        );
    }

    _handleUnderAuth(onSuccess, onFailure) {
        this._manageAuthentication.under(
            onSuccess,
            onFailure
        );
    }

    _handleFrontAuth(onSuccess, onFailure) {
        this._manageAuthentication.front(
            onSuccess,
            onFailure
        );
    }

    _handleUnauthorized(backUrl) {
        this._handleNavigate('/login?back=' + backUrl);
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