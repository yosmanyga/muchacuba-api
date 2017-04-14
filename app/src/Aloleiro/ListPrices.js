import React from 'react';
import {Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn} from 'material-ui/Table';
import Checkbox from 'material-ui/Checkbox';
import _ from 'lodash';

import Button from '../Button';
import ConnectToServer from '../ConnectToServer';
import Wait from '../Wait';

export default class ListPrices extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        // (onSuccess(token, roles), onError)
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
            roles: null,
            prices: null,
            favorites: true
        };

        this._connectToServer = new ConnectToServer();

        this._collectPrices = this._collectPrices.bind(this);
        this._renderTableAsAdmin = this._renderTableAsAdmin.bind(this);
        this._renderTableAsSeller = this._renderTableAsSeller.bind(this);
        this._renderTableAsOperator = this._renderTableAsOperator.bind(this);
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

    componentDidUpdate(prevProps, prevState) {
        if (
            prevState.token === null
            && this.state.token !== null
        ) {
            this._collectPrices();
        }
    }

    _collectPrices() {
        this._connectToServer
            .get('/aloleiro/collect-prices')
            .auth(this.state.token)
            .send()
            .end((err, res) => {
                if (err) {
                    // TODO

                    return;
                }

                this.setState({
                    prices: res.body
                });
            });
    }

    render() {
        if (this.state.prices === null) {
            return (
                <Wait layout={this.props.layout}/>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
            >
                {this.state.prices.length !== 0
                    ? [
                        <div key="top" style={{display: "flex"}}>
                            <div style={{width: "200px"}}><Checkbox
                                label="Solo los favoritos"
                                checked={this.state.favorites}
                                onTouchTap={() => {
                                    this.setState({
                                        favorites: !this.state.favorites
                                    })
                                }}
                            /></div>
                            <Button
                                label="Descargar favoritos"
                                icon="file_download"
                                href="/aloleiro/download-prices"
                            />
                        </div>,
                        _.includes(this.state.roles, 'admin')
                            ? this._renderTableAsAdmin()
                            : _.includes(this.state.roles, 'seller')
                            ? this._renderTableAsSeller()
                            : _.includes(this.state.roles, 'operator')
                            ? this._renderTableAsOperator()
                            : null
                    ]
                    : <p>No hay precios</p>
                }
            </this.props.layout.type>
        );
    }

    _renderTableAsAdmin() {
        return (
            <Table key="table">
                <TableHeader
                    displaySelectAll={false}
                    adjustForCheckbox={false}
                >
                    <TableRow>
                        <TableHeaderColumn>País</TableHeaderColumn>
                        <TableHeaderColumn>Tipo</TableHeaderColumn>
                        <TableHeaderColumn>Código</TableHeaderColumn>
                        <TableHeaderColumn>Precio de compra</TableHeaderColumn>
                        <TableHeaderColumn>Precio de venta</TableHeaderColumn>
                    </TableRow>
                </TableHeader>
                <TableBody displayRowCheckbox={false}>
                    {this.state.prices.map((price) => {
                        if (this.state.favorites && price.favorite === false) {
                            return null;
                        }

                        return (
                            <TableRow key={price.id}>
                                <TableRowColumn>{price.country}</TableRowColumn>
                                <TableRowColumn>{price.type}</TableRowColumn>
                                <TableRowColumn>{price.code}</TableRowColumn>
                                <TableRowColumn>{price.purchaseValue} USD</TableRowColumn>
                                <TableRowColumn>{price.saleValue} USD</TableRowColumn>
                            </TableRow>
                        );
                    })}
                </TableBody>
            </Table>
        );
    }

    _renderTableAsSeller() {
        return (
            <Table key="table">
                <TableHeader
                    displaySelectAll={false}
                    adjustForCheckbox={false}
                >
                    <TableRow>
                        <TableHeaderColumn>País</TableHeaderColumn>
                        <TableHeaderColumn>Tipo</TableHeaderColumn>
                        <TableHeaderColumn>Código</TableHeaderColumn>
                        <TableHeaderColumn>Precio de compra</TableHeaderColumn>
                        <TableHeaderColumn>Precio de venta</TableHeaderColumn>
                    </TableRow>
                </TableHeader>
                <TableBody displayRowCheckbox={false}>
                    {this.state.prices.map((price) => {
                        if (this.state.favorites && price.favorite === false) {
                            return null;
                        }

                        return (
                            <TableRow key={price.id}>
                                <TableRowColumn>{price.country}</TableRowColumn>
                                <TableRowColumn>{price.type}</TableRowColumn>
                                <TableRowColumn>{price.code}</TableRowColumn>
                                <TableRowColumn>{price.purchaseValue} Bf</TableRowColumn>
                                <TableRowColumn>{price.saleValue} Bf</TableRowColumn>
                            </TableRow>
                        );
                    })}
                </TableBody>
            </Table>
        );
    }

    _renderTableAsOperator() {
        return (
            <Table key="table">
                <TableHeader
                    displaySelectAll={false}
                    adjustForCheckbox={false}
                >
                    <TableRow>
                        <TableHeaderColumn>País</TableHeaderColumn>
                        <TableHeaderColumn>Tipo</TableHeaderColumn>
                        <TableHeaderColumn>Código</TableHeaderColumn>
                        <TableHeaderColumn>Precio x minuto</TableHeaderColumn>
                    </TableRow>
                </TableHeader>
                <TableBody displayRowCheckbox={false}>
                    {this.state.prices.map((price) => {
                        if (this.state.favorites && price.favorite === false) {
                            return null;
                        }

                        return (
                            <TableRow key={price.id}>
                                <TableRowColumn>{price.country}</TableRowColumn>
                                <TableRowColumn>{price.type}</TableRowColumn>
                                <TableRowColumn>{price.code}</TableRowColumn>
                                <TableRowColumn>{price.saleValue} Bf</TableRowColumn>
                            </TableRow>
                        );
                    })}
                </TableBody>
            </Table>
        );
    }
}

