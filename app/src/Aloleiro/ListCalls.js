/* global google */

import React from 'react';
import Dialog from 'material-ui/Dialog';
import FlatButton from 'material-ui/FlatButton';
import MenuItem from 'material-ui/MenuItem';
import {Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn} from 'material-ui/Table';
import TextField from 'material-ui/TextField';
import SelectField from 'material-ui/SelectField';

import ConnectToServer from '../ConnectToServer';
import Button from '../Button';
import Wait from '../Wait';

export default class ListCalls extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        // (onSuccess(token), onError)
        onBackAuth: React.PropTypes.func.isRequired,
        // ()
        onFrontAuth: React.PropTypes.func.isRequired,
        // (message, finish)
        onNotify: React.PropTypes.func.isRequired,
    };

    constructor(props) {
        super(props);

        this.state = {
            token: null,
            calls: null,
            add: null,
            remove: null
        };

        this._connectToServer = new ConnectToServer();

        this._collectPhones = this._collectPhones.bind(this);
        this._collectCalls = this._collectCalls.bind(this);
    }

    /*
    componentDidMount() {
        this.setState({
            token: "eyJhbGciOiJSUzI1NiIsImtpZCI6IjRhOTk0OTMyZjM4NDAwZDc5NTMwYzQ5N2RkYjE1MzcyNGFkZWUzMjYifQ.eyJpc3MiOiJodHRwczovL3NlY3VyZXRva2VuLmdvb2dsZS5jb20vY3ViYWxpZGVyLW11Y2hhY3ViYSIsIm5hbWUiOiJPcGVyYSBVc2VyIiwicGljdHVyZSI6Imh0dHBzOi8vc2NvbnRlbnQueHguZmJjZG4ubmV0L3YvdDEuMC0xL3MxMDB4MTAwLzEwMzU0Njg2XzEwMTUwMDA0NTUyODAxODU2XzIyMDM2NzUwMTEwNjE1MzQ1NV9uLmpwZz9vaD03MDljZTQ1MGQ5YjBiNGI4ZjM5Yjk3YTNiYjk5ZTE0NyZvZT01OTY1REE3MyIsImF1ZCI6ImN1YmFsaWRlci1tdWNoYWN1YmEiLCJhdXRoX3RpbWUiOjE0OTE4MDUyNDUsInVzZXJfaWQiOiI2c2ZoT3Bva3U5UHoxclBJSEFzYUJRN0N6S28xIiwic3ViIjoiNnNmaE9wb2t1OVB6MXJQSUhBc2FCUTdDektvMSIsImlhdCI6MTQ5MTgwNTI0NSwiZXhwIjoxNDkxODA4ODQ1LCJlbWFpbCI6Im9wZXJhX2xwYmlpY3RfdXNlckB0ZmJudy5uZXQiLCJlbWFpbF92ZXJpZmllZCI6ZmFsc2UsImZpcmViYXNlIjp7ImlkZW50aXRpZXMiOnsiZmFjZWJvb2suY29tIjpbIjEwNzQ3MTE1OTc5Mjg4MCJdLCJlbWFpbCI6WyJvcGVyYV9scGJpaWN0X3VzZXJAdGZibncubmV0Il19LCJzaWduX2luX3Byb3ZpZGVyIjoiZmFjZWJvb2suY29tIn19.dmI_t7pI1rMbkviSE58qvnf3zrn0X_cBSpJkWyHr_axvrcU3yh9dAKhvZ7P7_QOlZPbeQ3KHN6xwYxUHyjk2_t_zAFUnV4LBO_mb8W0vzopA1uERU__s2pEeodo0XpK0bDt_PkLPWteyvXnNtbpGnTC7nGWB1c8fqPXn5bi3kAMfaheGHTi6VCDnutVvSJWPUydfGg1gSMjcqbiuTXBsJ6NQCsK5gF9thaL24113sXKnT7KfF8dfmfjKyHZ7CGZVcEPcNkferZd3eqa8C_DvTAipQDsU7WxAM67MgCUmDp65gRYxhwVkzqsMfQjaDmDBW0v75AXF4Xt8ylV2i6_ImA"
        });
    }
*/

    componentDidMount() {
        this.props.onBackAuth(
            (token) => {
                if (token === 'null') {
                    this.props.onFrontAuth();

                    return;
                }

                this.setState({
                    token: token
                });
            },
            () => {
                this.props.onFrontAuth();
            }
        );
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            prevState.token === null
            && this.state.token !== null
        ) {
            this._collectPhones();
            this._collectCalls();
        }
    }

    _collectPhones() {
        this._connectToServer
            .get('/aloleiro/collect-phones')
            .auth(this.state.token)
            .send()
            .end((err, res) => {
                if (err) {
                    // TODO

                    return;
                }

                this.setState({
                    phones: res.body
                });
            });
    }
    
    _collectCalls() {
        this._connectToServer
            .get('/aloleiro/collect-calls')
            .auth(this.state.token)
            .send()
            .end((err, res) => {
                if (err) {
                    // TODO

                    return;
                }

                this.setState({
                    calls: res.body
                });
            });
    }

    render() {
        if (
            this.state.phones === null
            || this.state.calls === null
        ) {
            return (
                <Wait layout={this.props.layout}/>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
            >
                <Button
                    label="Preparar llamada"
                    icon="add"
                    fullWidth={true}
                    onTouchTap={(finish) => {
                        this.setState({add: true}, finish);
                    }}
                />
                {this.state.calls.length !== 0
                    ? <Table>
                        <TableHeader
                            displaySelectAll={false}
                            adjustForCheckbox={false}
                        >
                            <TableRow>
                                <TableHeaderColumn>Desde</TableHeaderColumn>
                                <TableHeaderColumn>Hacia</TableHeaderColumn>
                            </TableRow>
                        </TableHeader>
                        <TableBody displayRowCheckbox={false}>
                            {this.state.calls.map((call) => {
                                return (
                                    <TableRow key={call.id}>
                                        <TableRowColumn>
                                            {this.state.phones.find((phone) => {
                                                return phone.number === call.from
                                            }).name}
                                        </TableRowColumn>
                                        <TableRowColumn>{call.to}</TableRowColumn>
                                    </TableRow>
                                );
                            })}
                        </TableBody>
                    </Table>
                    : [<p>No hay llamadas</p>]
                }
                {this.state.add === true
                    ? <AddDialog
                        phones={this.state.phones}
                        onAdd={(call) => {
                            this._connectToServer
                                .post('/aloleiro/prepare-call')
                                .auth(this.state.token)
                                .send(call)
                                .end((err, res) => {
                                    if (err) {
                                        // TODO

                                        return;
                                    }

                                    this.setState({
                                        calls: res.body,
                                        add: false
                                    });
                                });
                        }}
                        onCancel={() => {
                            this.setState({add: false})
                        }}
                    />
                    : null
                }
            </this.props.layout.type>
        );
    }
}

class AddDialog extends React.Component {
    static propTypes = {
        phones: React.PropTypes.array,
        // (call)
        onAdd: React.PropTypes.func.isRequired,
        // ()
        onCancel: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            busy: false,
            focus: 'name',
            call: {
                from: null,
                to: ''
            },
        };
    }

    render() {
        return(
            <Dialog
                open={true}
                title="Preparar llamada"
                actions={[
                    <FlatButton
                        label="Cancelar"
                        disabled={this.state.busy === true}
                        onTouchTap={this.props.onCancel}
                    />,
                    <FlatButton
                        label={!this.state.busy ? "Preparar" : "Preparando..."}
                        primary={true}
                        disabled={this.state.text === "" || this.state.busy === true}
                        onTouchTap={() => {
                            this.setState({
                                busy: true
                            }, () => {
                                this.props.onAdd(this.state.call)
                            });
                        }}
                    />
                ]}
                modal={true}
                autoScrollBodyContent={true}
            >
                <SelectField
                    hintText="Desde"
                    value={this.state.call.from}
                    fullWidth={true}
                    onChange={(e, key, value) => {
                        this.setState({
                            call: {
                                ...this.state.call,
                                from: value
                            }
                        });
                    }}
                >
                    {this.props.phones.map((phone) =>
                        <MenuItem
                            key={phone.number}
                            value={phone.number}
                            primaryText={phone.name}
                        />
                    )}
                </SelectField>
                <TextField
                    type="tel"
                    floatingLabelText="Hacia"
                    value={this.state.call.to}
                    fullWidth={true}
                    onChange={(e, value) => this.setState({
                        call: {
                            ...this.state.call,
                            to: value
                        }
                    })}
                />
            </Dialog>
        );
    }
}

