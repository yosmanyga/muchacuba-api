import React from 'react';
import Checkbox from 'material-ui/Checkbox';
import Dialog from 'material-ui/Dialog';
import FlatButton from 'material-ui/FlatButton';
import MenuItem from 'material-ui/MenuItem';
import {Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn} from 'material-ui/Table';
import TextField from 'material-ui/TextField';
import SelectField from 'material-ui/SelectField';

import ConnectToServer from '../ConnectToServer';
import Button from '../Button';
import Wait from '../Wait';

export default class ManageApprovals extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        profile: React.PropTypes.object,
        // (status, response)
        onError: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            approvals: null,
            businesses: null,
            add: null
        };

        this._connectToServer = new ConnectToServer();

        this._collectApprovals = this._collectApprovals.bind(this);
        this._collectBusinesses = this._collectBusinesses.bind(this);
        this._resolveBusinessName = this._resolveBusinessName.bind(this);
    }

    componentDidMount() {
        if (this.props.profile !== null) {
            this._collectApprovals();
            this._collectBusinesses();
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            prevProps.profile === null
            && this.props.profile !== null
        ) {
            this._collectApprovals();
            this._collectBusinesses();
        }
    }

    _collectApprovals() {
        this._connectToServer
            .get('/aloleiro/collect-approvals')
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
                    approvals: res.body
                });
            });
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
        if (
            this.state.approvals === null
            || this.state.businesses === null
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
                    label="Agregar aprovación"
                    icon="add"
                    fullWidth={true}
                    onTouchTap={(finish) => {
                        this.setState({add: true}, finish);
                    }}
                />
                {this.state.approvals.length !== 0
                    ? <Table style={{background: "transparent"}}>
                        <TableHeader
                            displaySelectAll={false}
                            adjustForCheckbox={false}
                        >
                            <TableRow>
                                <TableHeaderColumn>Email</TableHeaderColumn>
                                <TableHeaderColumn>Negocio</TableHeaderColumn>
                                <TableHeaderColumn>Roles</TableHeaderColumn>
                            </TableRow>
                        </TableHeader>
                        <TableBody displayRowCheckbox={false}>
                            {this.state.approvals.map((approval) => {
                                return (
                                    <TableRow key={approval.email}>
                                        <TableRowColumn>{approval.email}</TableRowColumn>
                                        <TableRowColumn>{this._resolveBusinessName(approval.business)}</TableRowColumn>
                                        <TableRowColumn>{approval.roles.map((role, i) => {
                                            return <p key={i}>{role}</p>
                                        })}</TableRowColumn>
                                    </TableRow>
                                );
                            })}
                        </TableBody>
                    </Table>
                    : <p>No hay aprobaciones</p>
                }
                {this.state.add === true
                    ? <AddDialog
                        businesses={this.state.businesses}
                        onAdd={(approval) => {
                            this._connectToServer
                                .post('/aloleiro/add-approval')
                                .auth(this.props.profile.token)
                                .send(approval)
                                .end((err, res) => {
                                    if (err) {
                                        // TODO

                                        return;
                                    }

                                    this.setState({
                                        approvals: res.body,
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

    _resolveBusinessName(id) {
        const business = this.state.businesses.find((business) => {
            return business.id === id
        });

        if (typeof business !== 'undefined') {
            return business.name;
        }

        return id;
    }
}

class AddDialog extends React.Component {
    static propTypes = {
        businesses: React.PropTypes.array.isRequired,
        // (approval)
        onAdd: React.PropTypes.func.isRequired,
        // ()
        onCancel: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            busy: false,
            approval: {
                email: '',
                business: null,
                roles: {
                    aloleiro_owner: false,
                    aloleiro_operator: false
                }
            }
        };
    }

    render() {
        return(
            <Dialog
                open={true}
                title="Agregar aprovación"
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
                                    this.state.approval
                                )
                            });
                        }}
                    />
                ]}
                modal={true}
                autoScrollBodyContent={true}
            >
                <TextField
                    value={this.state.approval.email}
                    hintText="Email"
                    autoFocus={true}
                    fullWidth={true}
                    onChange={(e, value) => {
                        this.setState({
                            approval: {
                                ...this.state.approval,
                                email: value
                            }
                        })
                    }}
                />
                <SelectField
                    value={this.state.approval.business}
                    hintText="Negocio"
                    fullWidth={true}
                    onChange={(event, index, value) => this.setState({
                        approval: {
                            ...this.state.approval,
                            business: value
                        }
                    })}
                    maxHeight={200}
                >
                    {this.props.businesses.map((business) => {
                        return <MenuItem value={business.id} key={business.id} primaryText={business.name} />
                    })}
                </SelectField>
                <br/>
                <div>
                    <Checkbox
                        label="Dueño"
                        onCheck={(event, checked) => {
                            this.setState({
                                approval: {
                                    ...this.state.approval,
                                    roles: {
                                        ...this.state.approval.roles,
                                        aloleiro_owner: checked
                                    }
                                }
                            })
                        }}
                    />
                    <Checkbox
                        label="Operador"
                        onCheck={(event, checked) => {
                            this.setState({
                                approval: {
                                    ...this.state.approval,
                                    roles: {
                                        ...this.state.approval.roles,
                                        aloleiro_operator: checked
                                    }
                                }
                            })
                        }}
                    />
                </div>
            </Dialog>
        );
    }
}
