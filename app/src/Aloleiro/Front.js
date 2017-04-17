import React from 'react';
import Drawer from 'material-ui/Drawer';
import FontIcon from 'material-ui/FontIcon';
import MenuItem from 'material-ui/MenuItem';
// import Subheader from 'material-ui/Subheader';
import _ from 'lodash';

import ResolveElement from '../ResolveElement';
import Wait from '../Wait';

// Admin
import ListLogs from './ListLogs';
import ListSystemRates from './ListSystemRates';
// Owner
import ListBusinessRates from './ListBusinessRates';
import ListPhones from './ListPhones';
// Operator
import ListClientCalls from './ListClientCalls';
import ListClientRates from './ListClientRates';

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
        onNotify: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            token: null,
            roles: null
        };

        this._resolveElement = new ResolveElement();
    }

    componentDidMount() {
        this.props.onBackAuth(
            (token, roles) => {
                if (token === 'null') {
                    this.props.onFrontAuth();

                    return;
                }

                this.setState({
                    token: token,
                    roles: roles
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
            roles={this.state.roles}
            onBackAuth={this.props.onBackAuth}
            onFrontAuth={this.props.onFrontAuth}
            onNavigate={this.props.onNavigate}
            onNotify={this.props.onNotify}
        />;

        return this._resolveElement.resolve(
            this.props.url,
            [
                // Admin
                {
                    'url': '/list-logs',
                    'element': <ListLogs
                        layout={layout}
                        token={this.state.token}
                    />
                },
                {
                    'url': '/list-system-rates',
                    'element': <ListSystemRates
                        layout={layout}
                        token={this.state.token}
                    />
                },
                // Owner
                {
                    'url': '/list-business-rates',
                    'element': <ListBusinessRates
                        layout={layout}
                        token={this.state.token}
                    />
                },
                {
                    'url': '/list-phones',
                    'element': <ListPhones
                        layout={layout}
                        token={this.state.token}
                    />,
                    'def': true
                },
                // Operator
                {
                    'url': '/list-client-rates',
                    'element': <ListClientRates
                        layout={layout}
                        token={this.state.token}
                    />
                },
                {
                    'url': '/list-client-calls',
                    'element': <ListClientCalls
                        layout={layout}
                        token={this.state.token}
                    />
                }
            ]
        );
    }
}

class Layout extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        roles: React.PropTypes.array,
        onNavigate: React.PropTypes.func.isRequired,
    };

    render() {
        if (this.props.roles === null) {
            return (
                <Wait layout={this.props.layout}/>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
                title="Aloleiro"
                onTitleTouchTap={() => {this.props.onNavigate('/')}}
                drawer={
                    <Drawer>
                        {_.includes(this.props.roles, 'aloleiro_operator')
                            ?
                                <MenuItem
                                    key="list-client-calls"
                                    onTouchTap={() => {this.props.onNavigate('/list-client-calls')}}
                                    leftIcon={<FontIcon className="material-icons">phone_in_talk</FontIcon>}
                                >
                                    Llamadas
                                </MenuItem>
                            :
                                null
                        }
                        {_.includes(this.props.roles, 'aloleiro_owner')
                            ?
                                <MenuItem
                                    key="list-phones"
                                    onTouchTap={() => {this.props.onNavigate('/list-phones')}}
                                    leftIcon={<FontIcon className="material-icons">picture_in_picture</FontIcon>}
                                >
                                    Cabinas
                                </MenuItem>
                            :
                                null
                        }
                        {_.includes(this.props.roles, 'aloleiro_owner')
                            ?
                                <MenuItem
                                    key="list-business-rates"
                                    onTouchTap={() => {
                                        this.props.onNavigate('/list-business-rates')
                                    }}
                                    leftIcon={<FontIcon className="material-icons">attach_money</FontIcon>}
                                >
                                    Precios y ganancias
                                </MenuItem>
                            :
                                null
                        }
                        {_.includes(this.props.roles, 'aloleiro_operator')
                            ?
                                <MenuItem
                                    key="list-client-rates"
                                    onTouchTap={() => {
                                        this.props.onNavigate('/list-client-rates')
                                    }}
                                    leftIcon={<FontIcon className="material-icons">attach_money</FontIcon>}
                                >
                                    Precios
                                </MenuItem>
                            :
                                null
                        }
                        {_.includes(this.props.roles, 'aloleiro_admin')
                            ?
                                <MenuItem
                                    key="list-system-rates"
                                    onTouchTap={() => {
                                        this.props.onNavigate('/list-system-rates')
                                    }}
                                    leftIcon={<FontIcon className="material-icons">attach_money</FontIcon>}
                                >
                                    Precios
                                </MenuItem>
                            :
                                null
                        }
                        {_.includes(this.props.roles, 'aloleiro_admin')
                            ?
                                <MenuItem
                                    key="list-logs"
                                    onTouchTap={() => {this.props.onNavigate('/list-logs')}}
                                    leftIcon={<FontIcon className="material-icons">compare_arrows</FontIcon>}
                                >
                                    Logs
                                </MenuItem>
                            :
                                null
                        }
                    </Drawer>
                }
                style={{
                    ...this.props.layout.props.style,
                    height: "100%"
                }}
            >
                {this.props.children}
            </this.props.layout.type>
        );
    }
}
