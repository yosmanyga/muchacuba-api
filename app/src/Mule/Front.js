import React from 'react';
import FontIcon from 'material-ui/FontIcon';
import Dialog from 'material-ui/Dialog';
import FlatButton from 'material-ui/FlatButton';
import MenuItem from 'material-ui/MenuItem';
import TextField from 'material-ui/TextField';
import IconButton from 'material-ui/IconButton';
import IconMenu from 'material-ui/IconMenu';
import MoreVertIcon from 'material-ui/svg-icons/navigation/more-vert';
import SvgIcon from 'material-ui/SvgIcon';

import ConnectToServer from '../ConnectToServer';
import ResolveElement from '../ResolveElement';

import ListMyOffers from './ListMyOffers';
import FindOffers from './FindOffers';
// import CircularProgress from 'material-ui/CircularProgress';

export default class Front extends React.Component {
    static propTypes = {
        url: React.PropTypes.string.isRequired,
        layout: React.PropTypes.element.isRequired,
        authentication: React.PropTypes.object.isRequired,
        onLogin: React.PropTypes.func.isRequired,
        onUnauthorized: React.PropTypes.func.isRequired,
        onNavigate: React.PropTypes.func.isRequired,
        // (message, finish)
        onNotify: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this._resolveElement = new ResolveElement();
    }

    render() {
        const layout = <Layout
            url={this.props.url}
            layout={this.props.layout}
            authentication={this.props.authentication}
            onLogin={this.props.onLogin}
            onNavigate={this.props.onNavigate}
            onNotify={this.props.onNotify}
        />;

        return this._resolveElement.resolve(
            this.props.url,
            [
                {
                    'url': '/find-offers',
                    'element': <FindOffers
                        layout={layout}
                        onNotify={this.props.onNotify}
                    />,
                    'def': true
                },
                {
                    'url': '/list-my-offers',
                    'element': <ListMyOffers
                        layout={layout}
                        authentication={this.props.authentication}
                        onNotify={this.props.onNotify}
                        onUnauthorized={(url) => {this.props.onUnauthorized('/list-my-offers' + url)}}
                    />
                }
            ]
        );
    }
}

class Layout extends React.Component {
    static propTypes = {
        url: React.PropTypes.string.isRequired,
        layout: React.PropTypes.element.isRequired,
        authentication: React.PropTypes.object,
        onLogin: React.PropTypes.func.isRequired,
        onNavigate: React.PropTypes.func.isRequired,
        // (notification) => {}
        onNotify: React.PropTypes.func.isRequired,
        style: React.PropTypes.object
    };

    constructor(props) {
        super(props);

        this._connectToServer = new ConnectToServer();

        this.state = {
            helpDialog: null,
            feedbackDialog: null
        }
    }

    render() {
        return (
            <this.props.layout.type
                {...this.props.layout.props}
                title="Mulas"
                onTitleTouchTap={() => {this.props.onNavigate('/')}}
                iconElementLeft={
                    <IconButton
                        onTouchTap={() => {this.props.onNavigate('/')}}
                        style={{padding: 0}}
                    >
                        <SvgIcon>
                            <path
                                style={{transform: "scale(0.5)"}}
                                d="M49.009,21.486l-6.618-6.921l2.582-6.285l-3.251,2.781l1.076-4.612L29.859,18.539c-0.447-1.896-1.956-3.375-3.858-3.766   v-1.161c0-2.441-1.801-4.426-4.014-4.426h-4.19c-1.623,0-3.016,1.073-3.647,2.604h-3.948c-2.348,0-4.258,2.186-4.258,4.872v3.992   c0,0.167,0.026,0.324,0.04,0.485H5.136c-1.431,0-2.643,0.51-3.641,1.527C0.497,23.686,0,24.911,0,26.336v8.561h2.854l0-7.94h9.346   c-0.316-0.493-0.505-1.078-0.505-1.708v-5.551c0-1.734,1.396-3.147,3.113-3.147h1.132v11.848h-0.718v0.025H3.592l1.974,5.254   l-1.899,2.996v11.344c0,0.611,0.334,2.322,2.051,2.322c1.678,0,2.044-1.711,2.044-2.322v-9.568l2.325-3.608v4.172l4.312,10.094   c0.514,1.143,1.81,1.678,3.031,0.99c1.2-0.674,0.878-2.251,0.535-3.013l-3.566-8.596l2.538-3.275c0,0,2.379,1.847,5.465,2.378   v-9.081h1.411v9.235c3.638,0.196,5.917-0.621,5.917-0.621v10.894c0,0.783,0.519,2.322,2.161,2.322c1.642,0,2.057-1.711,2.057-2.322   V34.716l2.866-5.114c0.667,0.179,1.353,0.308,2.028,0.308c0.688,0,1.366-0.118,1.994-0.4c2.147-0.972,3.031-2.519,3.404-3.633   l2.222,0.889c1.317,0.458,2.243,0.265,2.887-0.302c0.634-0.556,0.92-1.186,1.034-1.958C50.511,23.643,50.238,22.73,49.009,21.486z    M13.784,14.771c-2.268,0.478-3.976,2.505-3.976,4.927v1.44H7.834c-0.021-0.161-0.059-0.317-0.059-0.485v-3.992   c0-1.676,1.088-3.04,2.427-3.04h3.582V14.771z M22.196,28.397h-4.425V16.55h4.425V28.397z M15.636,14.662v-1.05   c0-1.418,0.969-2.574,2.161-2.574h4.19c1.192,0,2.163,1.155,2.163,2.574v1.05H15.636z M28.12,25.249   c0,1.737-1.396,3.149-3.114,3.149h-0.979V16.55h0.979c1.717,0,3.114,1.413,3.114,3.147V25.249z M40.27,28.257   c-0.821,0.37-1.797,0.341-2.771,0.121l2.381-4.246l3.09,1.236C42.701,26.211,42.021,27.466,40.27,28.257z"
                            />
                        </SvgIcon>
                    </IconButton>
                }
                iconElementRight={
                    <IconMenu
                        iconButtonElement={
                            <IconButton><MoreVertIcon /></IconButton>
                        }
                        targetOrigin={{horizontal: 'right', vertical: 'top'}}
                        anchorOrigin={{horizontal: 'right', vertical: 'top'}}
                    >
                        <LoginMenuItem
                            url={this.props.url}
                            authentication={this.props.authentication}
                            onLogin={this.props.onLogin}
                            onNavigate={this.props.onNavigate}
                        />
                        <MenuItem
                            primaryText="Ayuda"
                            leftIcon={<FontIcon className="material-icons">help</FontIcon>}
                            onTouchTap={() => {this.setState({helpDialog: true})}}
                        />
                        <MenuItem
                            primaryText="Escríbenos"
                            leftIcon={<FontIcon className="material-icons">email</FontIcon>}
                            onTouchTap={() => {this.setState({feedbackDialog: true})}}
                        />
                    </IconMenu>
                }
                style={{
                    ...this.props.style,
                    height: "100%"
                }}
            >
                {this.props.children}
                {this.state.helpDialog === true
                    ? <HelpDialog
                        url={this.props.url}
                        onClose={() => {
                            this.setState({helpDialog: false})
                        }}
                    />
                    : null
                }
                {this.state.feedbackDialog === true
                    ? <FeedbackDialog
                        onSend={(feedback) => {
                            this._connectToServer
                                .post('/insert-feedback')
                                .send(feedback)
                                .end((err) => {
                                    if (err) {
                                        // TODO

                                        return;
                                    }

                                    this.setState({
                                        feedbackDialog: false
                                    }, this.props.onNotify(
                                        "Gracias por enviarnos tus comentarios!"
                                    ))
                                });
                        }}
                        onCancel={() => {
                            this.setState({
                                feedbackDialog: false
                            })
                        }}
                    />
                    :  null
                }
            </this.props.layout.type>
        );
    }
}

class HelpDialog extends React.Component {
    static propTypes = {
        url: React.PropTypes.string,
        onClose: React.PropTypes.func.isRequired
    };

    render() {
        return (
            <Dialog
                actions={<FlatButton
                    label="Cerrar"
                    primary={true}
                    onTouchTap={this.props.onClose}
                />}
                modal={true}
                open={this.props.url !== null}
                autoScrollBodyContent={true}
            >
                {this.props.url !== '/list-my-offers'
                    ?
                        <div>
                            <p><strong>Este sistema te ayuda a encontrar personas que viajan a Cuba como mulas.</strong></p>
                            <p>En el campo <strong>Dirección</strong> escribe y selecciona tu dirección para encontrar mulas cerca de ti. O puedes usar tu ubicación actual sin tener que escribir tu dirección.</p>
                            <p>Opcionalmente, en el campo <strong>Provincia</strong> puedes filtrar para ver cuáles mulas hacen entrega a tu provincia.</p>
                            <p>Y en el campo <strong>Fecha de vuelo</strong> puedes encontrar las mulas que viajen próximamente.</p>
                        </div>
                    :
                        <div>
                            <p><strong>Esta sección te permite entrar los datos de tu viaje a Cuba.</strong></p>
                            <p>En el campo <strong>Nombre</strong> escribe tu nombre o el de tu empresa si eres una agencia de viaje.</p>
                            <p>En el campo <strong>Datos de contacto</strong> escribe tu teléfono, tu email y otros datos útiles para contactarte.</p>
                            <p>En el campo <strong>Dirección</strong> escribe y selecciona tu dirección física para que te puedan encontrar en el mapa.</p>
                            <p>En el campo <strong>Provincias</strong> selecciona las provincias hasta donde haces entrega.</p>
                            <p>En el campo <strong>Detalles</strong> escribe detalles como el costo por libras.</p>
                            <p>En el campo <strong>Fechas</strong> selecciona las fechas de tus próximos viajes.</p>
                        </div>
                }
            </Dialog>
        );
    }
}

class FeedbackDialog extends React.Component {
    static propTypes = {
        // (feedback, success(finish()))
        onSend: React.PropTypes.func.isRequired,
        // ()
        onCancel: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            text: "",
            busy: false
        };
    }

    render() {
        return(
            <Dialog
                open={true}
                actions={[
                    <FlatButton
                        label="Cancelar"
                        disabled={this.state.busy === true}
                        onTouchTap={this.props.onCancel}
                    />,
                    <FlatButton
                        label={!this.state.busy ? "Enviar" : "Enviando..."}
                        primary={true}
                        disabled={this.state.text === "" || this.state.busy === true}
                        onTouchTap={() => {
                            this.setState({
                                busy: true
                            }, this.props.onSend({text: this.state.text}))
                        }}
                    />
                ]}
                modal={true}
                autoScrollBodyContent={true}
            >
                <TextField
                    autoFocus={true}
                    value={this.state.text}
                    hintText="Escribe tu crítica o sugerencia."
                    fullWidth={true}
                    multiLine={true}
                    rows={2}
                    onChange={(e, text) => this.setState({text: text})}
                />
            </Dialog>
        );
    }
}

class LoginMenuItem extends React.Component {
    static propTypes = {
        url: React.PropTypes.string.isRequired,
        authentication: React.PropTypes.object,
        onLogin: React.PropTypes.func.isRequired,
        onNavigate: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            busy: false
        };
    }

    render() {
        if (this.props.authentication.authenticated === false) {
            return (
                <MenuItem
                    primaryText={!this.state.busy ? "Entrar" : "Entrando..."}
                    leftIcon={<FontIcon className="material-icons">account_box</FontIcon>}
                    onTouchTap={() => {
                        this.setState({
                            busy: true
                        }, () => {
                            this.props.onLogin(
                                () => {
                                    this.setState({
                                        busy: false
                                    }, this.props.onNavigate('/list-my-offers'));
                                },
                                () => {
                                    this.setState({
                                        busy: false
                                    });
                                }
                            );
                        });
                    }}
                />
            );
        }

        return (
            <MenuItem
                primaryText="Mis viajes"
                leftIcon={<FontIcon className="material-icons">airplanemode_active</FontIcon>}
                onTouchTap={() => {
                    if (this.props.url !== '/list-my-offers') {
                        this.props.onNavigate('/list-my-offers')
                    }
                }}
            />
        );
    }
}
