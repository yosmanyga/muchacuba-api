import React from 'react';
import Dialog from 'material-ui/Dialog';
import FlatButton from 'material-ui/FlatButton';
import {Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn} from 'material-ui/Table';
import TextField from 'material-ui/TextField';

import ConnectToServer from '../ConnectToServer';
import Button from '../Button';
import Wait from '../Wait';

export default class ManageBusinesses extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        profile: React.PropTypes.object,
        // (status, response)
        onError: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            businesses: null,
            add: null,
            remove: null
        };

        this._connectToServer = new ConnectToServer();

        this._collectBusinesses = this._collectBusinesses.bind(this);
    }

    componentDidMount() {
        if (this.props.profile !== null) {
            this._collectBusinesses();
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            prevProps.profile === null
            && this.props.profile !== null
        ) {
            this._collectBusinesses();
        }
    }

    _collectBusinesses() {
        this._connectToServer
            .get('/aloleiro/collect-businesses')
            .auth(this.props.profile.token)
            .send()
            .end((err, res) => {
                if (err) {
                    this.props.onError(
                        err.status,
                        JSON.parse(err.response.text)
                    );

                    return;
                }

                this.setState({
                    businesses: res.body
                });
            });
    }

    render() {
        if (this.state.businesses === null) {
            return (
                <Wait layout={this.props.layout}/>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
                bar="Administrar negocios"
            >
                <Button
                    label="Agregar negocio"
                    icon="add"
                    fullWidth={true}
                    onTouchTap={(finish) => {
                        this.setState({add: true}, finish);
                    }}
                />
                {this.state.businesses.length !== 0
                    ? <Table style={{background: "transparent"}}>
                        <TableHeader
                            displaySelectAll={false}
                            adjustForCheckbox={false}
                        >
                            <TableRow>
                                <TableHeaderColumn>Nombre</TableHeaderColumn>
                                <TableHeaderColumn>Dirección</TableHeaderColumn>
                                <TableHeaderColumn>Acciones</TableHeaderColumn>
                            </TableRow>
                        </TableHeader>
                        <TableBody displayRowCheckbox={false}>
                            {this.state.businesses.map((business) => {
                                return (
                                    <TableRow key={business.id}>
                                        <TableRowColumn>{business.name}</TableRowColumn>
                                        <TableRowColumn>{business.address}</TableRowColumn>
                                        <TableRowColumn>
                                            {/*<Button*/}
                                                {/*label="Borrar"*/}
                                                {/*icon="delete"*/}
                                                {/*onTouchTap={(finish) => {*/}
                                                    {/*this.setState({remove: business}, finish);*/}
                                                {/*}}*/}
                                            {/*/>*/}
                                        </TableRowColumn>
                                    </TableRow>
                                );
                            })}
                        </TableBody>
                    </Table>
                    : <p>No hay negocios</p>
                }
                {this.state.add === true
                    ? <AddDialog
                        onAdd={(business) => {
                            this._connectToServer
                                .post('/aloleiro/add-business')
                                .auth(this.props.profile.token)
                                .send(business)
                                .end((err, res) => {
                                    if (err) {
                                        // TODO

                                        return;
                                    }

                                    this.setState({
                                        businesses: res.body,
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
                    ?
                        <RemoveDialog
                            business={this.state.remove}
                            onRemove={() => {
                                this._connectToServer
                                    .post('/aloleiro/remove-business')
                                    .auth(this.props.profile.token)
                                    .send({
                                        number: this.state.remove.number
                                    })
                                    .end((err, res) => {
                                        if (err) {
                                            // TODO

                                            return;
                                        }

                                        this.setState({
                                            businesses: res.body,
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
        // (business)
        onAdd: React.PropTypes.func.isRequired,
        // ()
        onCancel: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            busy: false,
            business: {
                balance: '',
                profitPercent: '',
                name: '',
                address: ''
            }
        };
    }

    render() {
        return(
            <Dialog
                open={true}
                title="Agregar negocio"
                actions={[
                    <FlatButton
                        label="Cancelar"
                        disabled={this.state.busy === true}
                        onTouchTap={this.props.onCancel}
                    />,
                    <FlatButton
                        label={!this.state.busy ? "Agregar" : "Agregando..."}
                        primary={true}
                        disabled={this.state.busy === true}
                        onTouchTap={() => {
                            this.setState({
                                busy: true,
                            }, () => {
                                this.props.onAdd(
                                    this.state.business
                                )
                            });
                        }}
                    />
                ]}
                modal={true}
                autoScrollBodyContent={true}
            >
                <TextField
                    hintText="Balance"
                    value={this.state.business.balance}
                    autoFocus={true}
                    fullWidth={true}
                    onChange={(e, value) => this.setState({
                        business: {
                            ...this.state.business,
                            balance: value
                        }
                    })}
                />
                <TextField
                    hintText="ProfitPercent"
                    value={this.state.business.profitPercent}
                    autoFocus={true}
                    fullWidth={true}
                    onChange={(e, value) => this.setState({
                        business: {
                            ...this.state.business,
                            profitPercent: value
                        }
                    })}
                />
                <TextField
                    hintText="Nombre"
                    value={this.state.business.name}
                    autoFocus={true}
                    fullWidth={true}
                    onChange={(e, value) => this.setState({
                        business: {
                            ...this.state.business,
                            name: value
                        }
                    })}
                />
                <TextField
                    hintText="Dirección"
                    value={this.state.business.address}
                    autoFocus={true}
                    fullWidth={true}
                    onChange={(e, value) => this.setState({
                        business: {
                            ...this.state.business,
                            address: value
                        }
                    })}
                />
            </Dialog>
        );
    }
}

class RemoveDialog extends React.Component {
    static propTypes = {
        business: React.PropTypes.object.isRequired,
        // ()
        onRemove: React.PropTypes.func.isRequired,
        // ()
        onCancel: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            busy: false,
        };
    }

    render() {
        return(
            <Dialog
                open={true}
                title="Borrar negocio"
                actions={[
                    <FlatButton
                        label="Cancelar"
                        disabled={this.state.busy === true}
                        onTouchTap={this.props.onCancel}
                    />,
                    <FlatButton
                        label={!this.state.busy ? "Borrar" : "Borrando..."}
                        primary={true}
                        disabled={this.state.busy === true}
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
                <p>¿Seguro que quieres borrar el negocio <strong>{this.props.business.name}</strong>?</p>
            </Dialog>
        );
    }
}
