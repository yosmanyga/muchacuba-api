import React from 'react';
import Avatar from 'material-ui/Avatar';
import Drawer from 'material-ui/Drawer';
import FontIcon from 'material-ui/FontIcon';
import MenuItem from 'material-ui/MenuItem';
import drawerBackground from './drawerBackground.png';
import {yellow50} from 'material-ui/styles/colors';

import ResolveElement from '../ResolveElement';
import Wait from '../Wait';

import ListProviders from './ListProviders';
import SendRecharges from './SendRecharges';

export default class Front extends React.Component {
    static propTypes = {
        url: React.PropTypes.string.isRequired,
        layout: React.PropTypes.element.isRequired,
        // (onSuccess, onError)
        onBackAuth: React.PropTypes.func.isRequired,
        // ()
        onFrontAuth: React.PropTypes.func.isRequired,
        // (url)
        onNavigate: React.PropTypes.func.isRequired,
        // (message, finish)
        onNotify: React.PropTypes.func.isRequired,
        // (status, response)
        onError: React.PropTypes.func.isRequired,
    };

    constructor(props) {
        super(props);

        this.state = {
            profile: null
        };

        this._resolveElement = new ResolveElement();
    }

    componentDidMount() {
        this.props.onBackAuth(
            (profile) => {
                if (profile.token === 'null') {
                    this.props.onFrontAuth();

                    return;
                }

                this.setState({
                    profile: profile
                });
            },
            () => {
                this.props.onFrontAuth();
            }
        );
    }

    render() {
        const layout = <Layout
            url={this.props.url}
            layout={this.props.layout}
            profile={this.state.profile}
            onNavigate={this.props.onNavigate}
        />;

        if (this.state.profile === null) {
            return <Wait layout={layout}/>;
        }

        return this._resolveElement.resolve(
            this.props.url,
            [
                {
                    url: '/send-recharges',
                    element: <SendRecharges
                        layout={layout}
                        profile={this.state.profile}
                        onError={this.props.onError}
                    />,
                    def: true

                },
                {
                    url: '/list-providers',
                    element: <ListProviders
                        layout={layout}
                        profile={this.state.profile}
                        onError={this.props.onError}
                    />
                }
            ]
        );
    }
}

class Layout extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        profile: React.PropTypes.object,
        onNavigate: React.PropTypes.func.isRequired,
        bar: React.PropTypes.node
    };

    render() {
        const layout = <this.props.layout.type
            {...this.props.layout.props}
            title="Mundorecarga"
            bar={this.props.bar}
            style={{
                ...this.props.layout.props.style,
                backgroundColor: yellow50
            }}
        />;

        if (this.props.profile === null) {
            return (
                <Wait layout={layout}/>
            );
        }

        return (
            <layout.type
                {...layout.props}
                drawer={
                    <Drawer containerStyle={{
                        backgroundImage: `url(${drawerBackground})`,
                    }}>
                        <div style={{
                            textAlign: "center",
                            marginTop: "10px"
                        }}>
                            <Avatar src={this.props.profile.picture}/>
                            <p>{this.props.profile.name}</p>
                        </div>
                        <MenuItem
                            key="send-recharges"
                            onTouchTap={() => {this.props.onNavigate('/send-recharges')}}
                            leftIcon={<FontIcon className="material-icons">redeem</FontIcon>}
                        >
                            Enviar recarga
                        </MenuItem>
                        <MenuItem
                            key="list-providers"
                            onTouchTap={() => {this.props.onNavigate('/list-providers')}}
                            leftIcon={<FontIcon className="material-icons">settings_input_antenna</FontIcon>}
                        >
                            Proveedores
                        </MenuItem>
                    </Drawer>
                }
            >
                <div style={{
                    padding: "10px",
                    ...this.props.style
                }}>
                    {this.props.children}
                </div>
            </layout.type>
        );
    }
}
