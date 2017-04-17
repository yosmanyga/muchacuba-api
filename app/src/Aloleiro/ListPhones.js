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

    componentDidMount() {
        if (this.props.token !== null) {
            this._collectPhones();
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            prevProps.token === null
            && this.props.token !== null
        ) {
            this._collectPhones();
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
                    : <p>No hay cabinas</p>
                }
                {this.state.add === true
                    ? <AddDialog
                        onAdd={(phone) => {
                            this._connectToServer
                                .post('/aloleiro/add-phone')
                                .auth(this.props.token)
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
                                .auth(this.props.token)
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
