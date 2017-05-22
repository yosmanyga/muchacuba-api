import React from 'react';
import {Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn} from 'material-ui/Table';

import ConnectToServer from '../ConnectToServer';
import Wait from '../Wait';

export default class ListProducts extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        profile: React.PropTypes.object,
        // (status, response)
        onError: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            products: null
        };

        this._connectToServer = new ConnectToServer();

        this._collectProducts = this._collectProducts.bind(this);
    }

    componentDidMount() {
        if (this.props.profile !== null) {
            this._collectProducts();
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            prevProps.profile === null
            && this.props.profile !== null
        ) {
            this._collectProducts();
        }
    }

    _collectProducts() {
        this._connectToServer
            .get('/topup/collect-products')
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
                    products: res.body
                });
            });
    }

    render() {
        if (this.state.products === null) {
            return (
                <Wait layout={this.props.layout}/>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
                bar="Productos"
            >
                {this.state.products.length !== 0
                    ? [
                        <Table key="table" style={{background: "transparent"}}>
                            <TableHeader
                                displaySelectAll={false}
                                adjustForCheckbox={false}
                            >
                                <TableRow>
                                    <TableHeaderColumn>Id</TableHeaderColumn>
                                    <TableHeaderColumn>Logo</TableHeaderColumn>
                                    <TableHeaderColumn>Descripción</TableHeaderColumn>
                                    <TableHeaderColumn>Crudo</TableHeaderColumn>
                                </TableRow>
                            </TableHeader>
                            <TableBody displayRowCheckbox={false}>
                                {this.state.products.map((product, i) => {
                                    return (
                                        <TableRow key={i}>
                                            <TableRowColumn
                                                style={{
                                                    verticalAlign: "top",
                                                    paddingTop: "10px"
                                                }}
                                            >{product.id}</TableRowColumn>
                                            <TableRowColumn
                                                style={{
                                                    verticalAlign: "top",
                                                    paddingTop: "10px"
                                                }}
                                            >
                                                <img src={"data:image/png;base64," + product.logo} alt={product.id}/>
                                            </TableRowColumn>
                                            <TableRowColumn>{product.description}</TableRowColumn>
                                            <TableRowColumn><pre dangerouslySetInnerHTML={{__html: JSON.stringify(product.raw, null, 4)}} /></TableRowColumn>
                                        </TableRow>
                                    );
                                })}
                            </TableBody>
                        </Table>
                    ]
                    : <p>No hay productos</p>
                }
            </this.props.layout.type>
        );
    }
}

