import React from 'react';
import Avatar from 'material-ui/Avatar';
import Drawer from 'material-ui/Drawer';
import FontIcon from 'material-ui/FontIcon';
import MenuItem from 'material-ui/MenuItem';
import drawerBackground from './drawerBackground.png';
import Subheader from 'material-ui/Subheader';
import {yellow50} from 'material-ui/styles/colors';
// import containerBackground from './containerBackground.png';
import _ from 'lodash';

import ResolveElement from '../ResolveElement';
import Error from '../Error';
import Wait from '../Wait';

// Admin
import ListSystemRates from './ListSystemRates';
import ManageBusinesses from './ManageBusinesses';
import ManageApprovals from './ManageApprovals';
import ViewSystemStats from './ViewSystemStats';
import ListLogs from './ListLogs';
// Owner
import EditBusiness from './EditBusiness';
import ListBusinessRates from './ListBusinessRates';
import ListPhones from './ListPhones';
import ViewBusinessStats from './ViewBusinessStats';
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
                // Admin
                {
                    'url': '/list-system-rates',
                    'element': <ListSystemRates
                        layout={layout}
                        profile={this.state.profile}
                        onError={this.props.onError}
                    />
                },
                {
                    'url': '/manage-businesses',
                    'element': <ManageBusinesses
                        layout={layout}
                        profile={this.state.profile}
                        onError={this.props.onError}
                    />
                },
                {
                    'url': '/manage-approvals',
                    'element': <ManageApprovals
                        layout={layout}
                        profile={this.state.profile}
                        onError={this.props.onError}
                    />
                },
                {
                    'url': '/view-system-stats',
                    'element': <ViewSystemStats
                        layout={layout}
                        profile={this.state.profile}
                        onError={this.props.onError}
                    />
                },
                {
                    'url': '/list-logs',
                    'element': <ListLogs
                        layout={layout}
                        profile={this.state.profile}
                        onError={this.props.onError}
                    />,
                    'def': _.includes(this.state.profile.roles, 'aloleiro_admin')
                },
                // Owner
                {
                    'url': '/edit-business',
                    'element': <EditBusiness
                        layout={layout}
                        profile={this.state.profile}
                        onNotify={this.props.onNotify}
                        onError={this.props.onError}
                    />
                },
                {
                    'url': '/list-business-rates',
                    'element': <ListBusinessRates
                        layout={layout}
                        profile={this.state.profile}
                        onError={this.props.onError}
                    />
                },
                {
                    'url': '/view-business-stats',
                    'element': <ViewBusinessStats
                        layout={layout}
                        profile={this.state.profile}
                        onError={this.props.onError}
                    />
                },
                {
                    'url': '/list-phones',
                    'element': <ListPhones
                        layout={layout}
                        profile={this.state.profile}
                        onError={this.props.onError}
                    />,
                    'def': _.includes(this.state.profile.roles, 'aloleiro_owner')
                },
                // Operator
                {
                    'url': '/list-client-rates',
                    'element': <ListClientRates
                        layout={layout}
                        profile={this.state.profile}
                        onError={this.props.onError}
                    />
                },
                {
                    'url': '/list-client-calls',
                    'element': <ListClientCalls
                        layout={layout}
                        profile={this.state.profile}
                        onError={this.props.onError}
                    />,
                    'def': _.includes(this.state.profile.roles, 'aloleiro_operator')
                },
                // No role
                {
                    'url': '/forbidden',
                    'element': <Error message="No tienes permiso para acceder a esta página"/>,
                    'def':
                        !_.includes(this.state.profile.roles, 'aloleiro_admin')
                        && !_.includes(this.state.profile.roles, 'aloleiro_owner')
                        && !_.includes(this.state.profile.roles, 'aloleiro_operator')
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
    };

    render() {
        const layout = <this.props.layout.type
            {...this.props.layout.props}
            title="Holapana"
            onTitleTouchTap={() => {this.props.onNavigate('/')}}
            style={{
                ...this.props.layout.props.style,
                backgroundColor: yellow50
                //backgroundImage: `url(${containerBackground})`,
                //backgroundRepeat: 'repeat'
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
                        {_.includes(this.props.profile.roles, 'aloleiro_admin')
                            ?
                                <MenuItem
                                    key="list-system-rates"
                                    onTouchTap={() => {
                                        this.props.onNavigate('/list-system-rates')
                                    }}
                                    leftIcon={<FontIcon className="material-icons">attach_money</FontIcon>}
                                >
                                    Precios del sistema
                                </MenuItem>
                            : null
                        }
                        {_.includes(this.props.profile.roles, 'aloleiro_admin')
                            ?
                            <MenuItem
                                key="view-system-stats"
                                onTouchTap={() => {
                                    this.props.onNavigate('/view-system-stats')
                                }}
                                leftIcon={<FontIcon className="material-icons">assessment</FontIcon>}
                            >
                                Reportes
                            </MenuItem>
                            : null
                        }
                        {_.includes(this.props.profile.roles, 'aloleiro_admin')
                            ?
                                <MenuItem
                                    key="list-logs"
                                    onTouchTap={() => {this.props.onNavigate('/list-logs')}}
                                    leftIcon={<FontIcon className="material-icons">compare_arrows</FontIcon>}
                                >
                                    Logs
                                </MenuItem>
                            : null
                        }
                        {_.includes(this.props.profile.roles, 'aloleiro_admin')
                            ?
                            <MenuItem
                                key="manage-businesses"
                                onTouchTap={() => {
                                    this.props.onNavigate('/manage-businesses')
                                }}
                                leftIcon={<FontIcon className="material-icons">business</FontIcon>}
                            >
                                Negocios
                            </MenuItem>
                            : null
                        }
                        {_.includes(this.props.profile.roles, 'aloleiro_admin')
                            ?
                                <MenuItem
                                    key="manage-approvals"
                                    onTouchTap={() => {
                                        this.props.onNavigate('/manage-approvals')
                                    }}
                                    leftIcon={<FontIcon className="material-icons">card_membership</FontIcon>}
                                >
                                    Aprovaciones
                                </MenuItem>
                            : null
                        }
                        {
                            _.includes(this.props.profile.roles, 'aloleiro_owner')
                            && _.includes(this.props.profile.roles, 'aloleiro_operator')
                                ? <Subheader>Dueño</Subheader>
                                : null
                        }
                        {_.includes(this.props.profile.roles, 'aloleiro_owner')
                            ?
                                <MenuItem
                                    key="edit-business"
                                    onTouchTap={() => {this.props.onNavigate('/edit-business')}}
                                    leftIcon={<FontIcon className="material-icons">account_box</FontIcon>}
                                >
                                    Mi cuenta
                                </MenuItem>
                            : null
                        }
                        {_.includes(this.props.profile.roles, 'aloleiro_owner')
                            ?
                            <MenuItem
                                key="list-phones"
                                onTouchTap={() => {this.props.onNavigate('/list-phones')}}
                                leftIcon={<FontIcon className="material-icons">picture_in_picture</FontIcon>}
                            >
                                Cabinas
                            </MenuItem>
                            : null
                        }
                        {_.includes(this.props.profile.roles, 'aloleiro_owner')
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
                            : null
                        }
                        {_.includes(this.props.profile.roles, 'aloleiro_owner')
                            ?
                                <MenuItem
                                    key="view-business-stats"
                                    onTouchTap={() => {
                                        this.props.onNavigate('/view-business-stats')
                                    }}
                                    leftIcon={<FontIcon className="material-icons">assessment</FontIcon>}
                                >
                                    Reportes
                                </MenuItem>
                            : null
                        }
                        {
                            _.includes(this.props.profile.roles, 'aloleiro_owner')
                            && _.includes(this.props.profile.roles, 'aloleiro_operator')
                                ? <Subheader>Operador</Subheader>
                                : null
                        }
                        {_.includes(this.props.profile.roles, 'aloleiro_operator')
                            ?
                                <MenuItem
                                    key="list-client-calls"
                                    onTouchTap={() => {this.props.onNavigate('/list-client-calls')}}
                                    leftIcon={<FontIcon className="material-icons">phone_in_talk</FontIcon>}
                                >
                                    Llamadas
                                </MenuItem>
                            : null
                        }
                        {_.includes(this.props.profile.roles, 'aloleiro_operator')
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
                            : null
                        }
                    </Drawer>
                }
            >
                <div style={{padding: "10px"}}>{this.props.children}</div>
            </layout.type>
        );
    }
}
