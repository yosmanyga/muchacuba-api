import React from 'react';
import {Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn} from 'material-ui/Table';
import Checkbox from 'material-ui/Checkbox';

import Button from '../Button';
import ConnectToServer from '../ConnectToServer';
import Wait from '../Wait';

export default class ListClientRates extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        token: React.PropTypes.string,
    };

    constructor(props) {
        super(props);

        this.state = {
            rates: null,
            favorites: true
        };

        this._connectToServer = new ConnectToServer();

        this._collectClientRates = this._collectClientRates.bind(this);
    }

    componentDidMount() {
        if (this.props.token !== null) {
            this._collectClientRates();
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            prevProps.token === null
            && this.props.token !== null
        ) {
            this._collectClientRates();
        }
    }

    _collectClientRates() {
        this._connectToServer
            .get('/aloleiro/collect-client-rates')
            .auth(this.props.token)
            .send()
            .end((err, res) => {
                if (err) {
                    // TODO

                    return;
                }

                this.setState({
                    rates: res.body
                });
            });
    }

    render() {
        if (this.state.rates === null) {
            return (
                <Wait layout={this.props.layout}/>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
            >
                {this.state.rates.length !== 0
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
                                href="/aloleiro/download-rates"
                            />
                        </div>,
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
                                {this.state.rates.map((rate, i) => {
                                    if (this.state.favorites && rate.favorite === false) {
                                        return null;
                                    }

                                    return (
                                        <TableRow key={i}>
                                            <TableRowColumn>{rate.country}</TableRowColumn>
                                            <TableRowColumn>{rate.type}</TableRowColumn>
                                            <TableRowColumn>{rate.code}</TableRowColumn>
                                            <TableRowColumn>{rate.sale} Bf</TableRowColumn>
                                        </TableRow>
                                    );
                                })}
                            </TableBody>
                        </Table>
                    ]
                    : <p>No hay tarifas</p>
                }
            </this.props.layout.type>
        );
    }
}

