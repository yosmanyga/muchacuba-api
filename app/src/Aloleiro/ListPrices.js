import React from 'react';
import Dialog from 'material-ui/Dialog';
import FlatButton from 'material-ui/FlatButton';
import {Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn} from 'material-ui/Table';
import TextField from 'material-ui/TextField';

import ConnectToServer from '../ConnectToServer';
import Button from '../Button';
import Wait from '../Wait';

export default class ListPrices extends React.Component {
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
            prices: null
        };

        this._connectToServer = new ConnectToServer();

        this._collectPrices = this._collectPrices.bind(this);
    }

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
                    ? <Table>
                        <TableHeader
                            displaySelectAll={false}
                            adjustForCheckbox={false}
                        >
                            <TableRow>
                                <TableHeaderColumn>País</TableHeaderColumn>
                                <TableHeaderColumn>Prefijo</TableHeaderColumn>
                                <TableHeaderColumn>Type</TableHeaderColumn>
                                <TableHeaderColumn>Código</TableHeaderColumn>
                                <TableHeaderColumn>Precio x minuto</TableHeaderColumn>
                            </TableRow>
                        </TableHeader>
                        <TableBody displayRowCheckbox={false}>
                            {this.state.prices.map((price) => {
                                return (
                                    <TableRow key={price.id}>
                                        <TableRowColumn>{price.country}</TableRowColumn>
                                        <TableRowColumn>{price.prefix}</TableRowColumn>
                                        <TableRowColumn>{price.type}</TableRowColumn>
                                        <TableRowColumn>{price.code}</TableRowColumn>
                                        <TableRowColumn>{price.value}</TableRowColumn>
                                    </TableRow>
                                );
                            })}
                        </TableBody>
                    </Table>
                    : <p>No hay precios</p>
                }
            </this.props.layout.type>
        );
    }
}

