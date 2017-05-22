import React from 'react';
import {Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn} from 'material-ui/Table';

import ConnectToServer from '../ConnectToServer';
import Wait from '../Wait';

export default class ListProviders extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        profile: React.PropTypes.object,
        // (status, response)
        onError: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            providers: null
        };

        this._connectToServer = new ConnectToServer();

        this._collectProviders = this._collectProviders.bind(this);
    }

    componentDidMount() {
        if (this.props.profile !== null) {
            this._collectProviders();
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            prevProps.profile === null
            && this.props.profile !== null
        ) {
            this._collectProviders();
        }
    }

    _collectProviders() {
        this._connectToServer
            .get('/topup/collect-providers')
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
                    providers: res.body
                });
            });
    }

    render() {
        if (this.state.providers === null) {
            return (
                <Wait layout={this.props.layout}/>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
                bar="Proveedores"
            >
                {this.state.providers.length !== 0
                    ? [
                        <Table key="table" style={{background: "transparent"}}>
                            <TableHeader
                                displaySelectAll={false}
                                adjustForCheckbox={false}
                            >
                                <TableRow>
                                    <TableHeaderColumn>Id</TableHeaderColumn>
                                    <TableHeaderColumn>País</TableHeaderColumn>
                                    <TableHeaderColumn>Nombre</TableHeaderColumn>
                                    <TableHeaderColumn>Validation</TableHeaderColumn>
                                    <TableHeaderColumn>Proveedor</TableHeaderColumn>
                                </TableRow>
                            </TableHeader>
                            <TableBody displayRowCheckbox={false}>
                                {this.state.providers.map((provider, i) => {
                                    return (
                                        <TableRow key={i}>
                                            <TableRowColumn>{provider.id}</TableRowColumn>
                                            <TableRowColumn>{provider.country}</TableRowColumn>
                                            <TableRowColumn>{provider.name}</TableRowColumn>
                                            <TableRowColumn>{provider.validation}</TableRowColumn>
                                            <TableRowColumn><pre dangerouslySetInnerHTML={{__html: JSON.stringify(provider.payload, null, 4)}} /></TableRowColumn>
                                        </TableRow>
                                    );
                                })}
                            </TableBody>
                        </Table>
                    ]
                    : <p>No hay proveedores</p>
                }
            </this.props.layout.type>
        );
    }
}

