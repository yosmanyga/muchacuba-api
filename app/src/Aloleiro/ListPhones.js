/* global google */

import React from 'react';
import Dialog from 'material-ui/Dialog';
import FlatButton from 'material-ui/FlatButton';
import {Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn} from 'material-ui/Table';
import TextField from 'material-ui/TextField';

import ConnectToServer from '../ConnectToServer';
import Button from '../Button';
import Wait from '../Wait';

export default class ListPhones extends React.Component {
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
            phones: null,
            add: null,
            remove: null
        };

        this._connectToServer = new ConnectToServer();

        this._collectPhones = this._collectPhones.bind(this);
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

    render() {
        if (this.state.phones === null) {
            return (
                <Wait layout={this.props.layout}/>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
            >
                <Button
                    label="Agregar cabina"
                    icon="add"
                    fullWidth={true}
                    onTouchTap={(finish) => {
                        this.setState({add: true}, finish);
                    }}
                />
                {this.state.phones.length !== 0
                    ? <Table>
                        <TableHeader
                            displaySelectAll={false}
                            adjustForCheckbox={false}
                        >
                            <TableRow>
                                <TableHeaderColumn>Número</TableHeaderColumn>
                                <TableHeaderColumn>Nombre</TableHeaderColumn>
                                <TableHeaderColumn>Acciones</TableHeaderColumn>
                            </TableRow>
                        </TableHeader>
                        <TableBody displayRowCheckbox={false}>
                            {this.state.phones.map((phone) => {
                                return (
                                    <TableRow key={phone.number}>
                                        <TableRowColumn>{phone.number}</TableRowColumn>
                                        <TableRowColumn>{phone.name}</TableRowColumn>
                                        <TableRowColumn>
                                            <Button
                                                label="Borrar"
                                                icon="delete"
                                                onTouchTap={(finish) => {
                                                    this.setState({remove: phone}, finish);
                                                }}
                                            />
                                        </TableRowColumn>
                                    </TableRow>
                                );
                            })}
                        </TableBody>
                    </Table>
                    : [<p>No hay cabinas</p>]
                }
                {this.state.add === true
                    ? <AddDialog
                        onAdd={(phone) => {
                            this._connectToServer
                                .post('/aloleiro/add-phone')
                                .auth(this.state.token)
                                .send(phone)
                                .end((err, res) => {
                                    if (err) {
                                        // TODO

                                        return;
                                    }

                                    this.setState({
                                        phones: res.body,
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
                {this.state.remove !== null
                    ? <RemoveDialog
                        phone={this.state.remove}
                        onRemove={() => {
                            this._connectToServer
                                .post('/aloleiro/remove-phone')
                                .auth(this.state.token)
                                .send({
                                    number: this.state.remove.number
                                })
                                .end((err, res) => {
                                    if (err) {
                                        // TODO

                                        return;
                                    }

                                    this.setState({
                                        phones: res.body,
                                        remove: null
                                    });
                                });
                        }}
                        onCancel={() => {
                            this.setState({remove: null})
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
        // (phone)
        onAdd: React.PropTypes.func.isRequired,
        // ()
        onCancel: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            busy: false,
            focus: 'name',
            phone: {
                number: '',
                name: ''
            },
        };
    }

    render() {
        return(
            <Dialog
                open={true}
                title="Agregar cabina"
                actions={[
                    <FlatButton
                        label="Cancelar"
                        disabled={this.state.busy === true}
                        onTouchTap={this.props.onCancel}
                    />,
                    <FlatButton
                        label={!this.state.busy ? "Agregar" : "Agregando..."}
                        primary={true}
                        disabled={this.state.text === "" || this.state.busy === true}
                        onTouchTap={() => {
                            this.setState({
                                busy: true
                            }, () => {
                                this.props.onAdd(this.state.phone)
                            });
                        }}
                    />
                ]}
                modal={true}
                autoScrollBodyContent={true}
            >
                <TextField
                    floatingLabelText="Nombre"
                    value={this.state.phone.name}
                    autoFocus={true}
                    fullWidth={true}
                    onChange={(e, value) => this.setState({
                        phone: {
                            ...this.state.phone,
                            name: value
                        }
                    })}
                />
                <TextField
                    type="tel"
                    floatingLabelText="Número de teléfono"
                    fullWidth={true}
                    value={this.state.phone.number}
                    onChange={(e, value) => this.setState({
                        phone: {
                            ...this.state.phone,
                            number: value
                        }
                    })}
                />
            </Dialog>
        );
    }
}

class RemoveDialog extends React.Component {
    static propTypes = {
        phone: React.PropTypes.object.isRequired,
        // ()
        onRemove: React.PropTypes.func.isRequired,
        // ()
        onCancel: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            busy: false,
            phone: {
                number: '',
                name: ''
            },
        };
    }

    render() {
        return(
            <Dialog
                open={true}
                title="Borrar cabina"
                actions={[
                    <FlatButton
                        label="Cancelar"
                        disabled={this.state.busy === true}
                        onTouchTap={this.props.onCancel}
                    />,
                    <FlatButton
                        label={!this.state.busy ? "Borrar" : "Borrando..."}
                        primary={true}
                        disabled={this.state.text === "" || this.state.busy === true}
                        onTouchTap={() => {
                            this.setState({
                                busy: true
                            }, this.props.onRemove);
                        }}
                    />
                ]}
                modal={true}
                autoScrollBodyContent={true}
            >
                <p>¿Seguro que quieres borrar la cabina <strong>{this.props.phone.name}</strong>?</p>
            </Dialog>
        );
    }
}
