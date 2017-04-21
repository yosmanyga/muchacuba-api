import React from 'react';
import Avatar from 'material-ui/Avatar';
import Drawer from 'material-ui/Drawer';
import FontIcon from 'material-ui/FontIcon';
import MenuItem from 'material-ui/MenuItem';
import drawerBackground from './drawerBackground.png';
import {yellow50} from 'material-ui/styles/colors';
// import containerBackground from './containerBackground.png';
import _ from 'lodash';

import ResolveElement from '../ResolveElement';
import Wait from '../Wait';

// Admin
import ListLogs from './ListLogs';
import ListSystemRates from './ListSystemRates';
// Owner
import EditBusiness from './EditBusiness';
import ListBusinessRates from './ListBusinessRates';
import ListPhones from './ListPhones';
import ViewMonthlyStats from './ViewMonthlyStats';
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
        this.setState({
            profile: {
                id: "410922912603217",
                name: "Jefe del negocio",
                email: "jefe_vnzwhyy_del_negocio@tfbnw.net",
                picture: "https://scontent.xx.fbcdn.net/v/t1.0-1/s100x100/1379841_10150004552801901_469209496895221757_n.jpg?oh=1487b71678150fcd5a6143d84997b15c&oe=598B7497",
                roles: ["aloleiro_owner"],
                token: "eyJhbGciOiJSUzI1NiIsImtpZCI6IjFkNmQ5MTFjMGMwMWM3ODcxYmVmYmVkYWI2ZmU0YWE5MzJjYjE0YjEifQ.eyJpc3MiOiJodHRwczovL3NlY3VyZXRva2VuLmdvb2dsZS5jb20vY3ViYWxpZGVyLW11Y2hhY3ViYSIsIm5hbWUiOiJGaXJlZm94IFVzZXIiLCJwaWN0dXJlIjoiaHR0cHM6Ly9zY29udGVudC54eC5mYmNkbi5uZXQvdi90MS4wLTEvczEwMHgxMDAvMTM3OTg0MV8xMDE1MDAwNDU1MjgwMTkwMV80NjkyMDk0OTY4OTUyMjE3NTdfbi5qcGc_b2g9YjhkMWFjOWQwYjk3MmUzMDljNTM3MmQ2MGNkMjhmODMmb2U9NTk2M0U3OTciLCJhdWQiOiJjdWJhbGlkZXItbXVjaGFjdWJhIiwiYXV0aF90aW1lIjoxNDkyNzUwODM5LCJ1c2VyX2lkIjoiMXh1clFlM0hjVlR6Zlp0Z0RYT2NmZTdwaFhKMiIsInN1YiI6IjF4dXJRZTNIY1ZUemZadGdEWE9jZmU3cGhYSjIiLCJpYXQiOjE0OTI3NTA4MzksImV4cCI6MTQ5Mjc1NDQzOSwiZW1haWwiOiJmaXJlZm94X2Npc2NndXRfdXNlckB0ZmJudy5uZXQiLCJlbWFpbF92ZXJpZmllZCI6ZmFsc2UsImZpcmViYXNlIjp7ImlkZW50aXRpZXMiOnsiZmFjZWJvb2suY29tIjpbIjQxMDkyMjkxMjYwMzIxNyJdLCJlbWFpbCI6WyJmaXJlZm94X2Npc2NndXRfdXNlckB0ZmJudy5uZXQiXX0sInNpZ25faW5fcHJvdmlkZXIiOiJmYWNlYm9vay5jb20ifX0.BIkU3P0cQ4XafrVKQRXNxQpXu0om0MUNdk5QHgTJYnZghlnWzB0yqjUYZoqrOkS476QUC9OsDRM-fYtzx-BSAOO1Kx8aoXeeJT-Og4rgKy1aFeej97EAe970xmc0weFyIz2kWDlqBH2PDrnktXBC4xUYAl8w81R2NZfDK1TJdEtL89QCGPUxnhq_O_jAhvL52o-u_uoDmNlgZFQD1sR2hamfStriTioG3JnWhOd6QC4eHLGx3HpFo5YEKQ2LpobhpeCMCnnLHsvaoqnFF1OY86XnvIS2IfxHWXYdB9niSyb6uCBidlUzUTPI2QRAjK8L9raoRQr3UTgIAJS26EAEHg"
            }
        });

        /*
        this.props.onBackAuth(
            (profile) => {
                console.log(profile);

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
        */
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
                    'url': '/list-logs',
                    'element': <ListLogs
                        layout={layout}
                        profile={this.state.profile}
                        onError={this.props.onError}
                    />
                },
                {
                    'url': '/list-system-rates',
                    'element': <ListSystemRates
                        layout={layout}
                        profile={this.state.profile}
                        onError={this.props.onError}
                    />
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
                    'url': '/view-monthly-stats',
                    'element': <ViewMonthlyStats
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
                                    key="list-logs"
                                    onTouchTap={() => {this.props.onNavigate('/list-logs')}}
                                    leftIcon={<FontIcon className="material-icons">compare_arrows</FontIcon>}
                                >
                                    Logs
                                </MenuItem>
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
                                    key="view-monthly-stats"
                                    onTouchTap={() => {
                                        this.props.onNavigate('/view-monthly-stats')
                                    }}
                                    leftIcon={<FontIcon className="material-icons">assessment</FontIcon>}
                                >
                                    Reportes
                                </MenuItem>
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
