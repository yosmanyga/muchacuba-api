import React from 'react';
import Checkbox from 'material-ui/Checkbox';
import {Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn} from 'material-ui/Table';
import TextField from 'material-ui/TextField';

import ConnectToServer from '../ConnectToServer';
import Wait from '../Wait';

export default class ListClientRates extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        profile: React.PropTypes.object,
        // (status, response)
        onError: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            rates: null,
            filter: '',
            favorites: true
        };

        this._connectToServer = new ConnectToServer();

        this._collectClientRates = this._collectClientRates.bind(this);
    }

    componentDidMount() {
        if (this.props.profile !== null) {
            this._collectClientRates();
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            prevProps.profile === null
            && this.props.profile !== null
        ) {
            this._collectClientRates();
        }
    }

    _collectClientRates() {
        this._connectToServer
            .get('/aloleiro/collect-client-rates')
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
                            <TextField
                                value={this.state.filter}
                                hintText="Filtrar países"
                                autoFocus={true}
                                onChange={(event) => {
                                    this.setState({
                                        filter: event.target.value
                                    });
                                }}
                                style={{
                                    height: "auto"
                                }}
                            />
                            <div style={{
                                width: "200px",
                                marginTop: "10px",
                                marginLeft: "20px"
                            }}>
                                <Checkbox
                                    label="Solo los favoritos"
                                    checked={this.state.favorites}
                                    onTouchTap={() => {
                                        this.setState({
                                            favorites: !this.state.favorites
                                        })
                                    }}
                                />
                            </div>
                            {/*<Button*/}
                                {/*label="Descargar favoritos"*/}
                                {/*icon="file_download"*/}
                                {/*onTouchTap={() => {*/}
                                    {/*this._connectToServer*/}
                                        {/*.post('/aloleiro/download-client-rates')*/}
                                        {/*.auth(this.props.profile.token)*/}
                                        {/*.send()*/}
                                        {/*.end();*/}
                                {/*}}*/}
                            {/*/>*/}
                        </div>,
                        <Table key="table" style={{background: "transparent"}}>
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

                                    if (
                                        this.state.filter !== ''
                                        && !rate.country
                                            .toLowerCase()
                                            .includes(this.state.filter.toLowerCase())
                                    ) {
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

