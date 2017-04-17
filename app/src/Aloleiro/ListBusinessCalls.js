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

export default class ListBusinessCalls extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        token: React.PropTypes.string,
    };

    constructor(props) {
        super(props);

        this.state = {
            phones: null,
            calls: null,
            add: null,
            remove: null
        };

        this._connectToServer = new ConnectToServer();

        this._collectPhones = this._collectPhones.bind(this);
        this._collectCalls = this._collectCalls.bind(this);
    }

    componentDidMount() {
        if ( this.props.token !== null) {
            this._collectPhones();
        }

        if ( this.props.token !== null) {
            this._collectCalls();
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            this.state.token !== null
            && this.state.phones === null
        ) {
            this._collectPhones();
        }

        if (
            this.state.token !== null
            && this.state.calls === null
        ) {
            this._collectCalls();
        }
    }

    _collectPhones() {
        this._connectToServer
            .get('/aloleiro/collect-phones')
            .auth(this.props.token)
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
            .get('/aloleiro/collect-business-calls')
            .auth(this.props.token)
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
                    onTouchTap={(finish) => {
                        this.setState({add: true}, finish);
                    }}
                />
                <Button
                    label="Refrescar"
                    icon="refresh"
                    onTouchTap={(finish) => {
                        this.setState({calls: null}, finish);
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
                                <TableHeaderColumn>Duración</TableHeaderColumn>
                                <TableHeaderColumn>Precio</TableHeaderColumn>
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
                                        <TableRowColumn>{this._buildDuration(call.duration)}</TableRowColumn>
                                        <TableRowColumn>{call.charge}</TableRowColumn>
                                    </TableRow>
                                );
                            })}
                        </TableBody>
                    </Table>
                    : <p>No hay llamadas</p>
                }
                {this.state.add === true
                    ? <AddDialog
                        phones={this.state.phones}
                        onAdd={(call) => {
                            this._connectToServer
                                .post('/aloleiro/prepare-call')
                                .auth(this.props.token)
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

    _buildDuration(duration) {
        const remainder = duration % 60;

        let string = (duration - remainder) / 60 + ' minutos';

        if (remainder !== 0) {
            string += ' y ' + remainder + ' segundos';
        }

        return string;
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

