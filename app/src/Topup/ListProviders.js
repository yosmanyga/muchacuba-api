import React from 'react';
import {Card, CardHeader, CardText} from 'material-ui/Card';
import Paper from 'material-ui/Paper';
import defaultProviderLogo from './provider.png';

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
            countries: null,
            providers: null,
            products: null,
            promotions: null
        };

        this._connectToServer = new ConnectToServer();

        this._collectCountries = this._collectCountries.bind(this);
        this._collectProviders = this._collectProviders.bind(this);
        this._collectProducts = this._collectProducts.bind(this);
    }

    componentDidMount() {
        if (this.props.profile !== null) {
            this._collectCountries();
            this._collectProviders();
            this._collectProducts();
            this._collectPromotions();
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            prevProps.profile === null
            && this.props.profile !== null
        ) {
            this._collectCountries();
            this._collectProviders();
            this._collectProducts();
            this._collectPromotions();
        }
    }

    _collectCountries() {
        this._connectToServer
            .get('/topup/collect-countries')
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
                    countries: res.body
                });
            });
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

    _collectPromotions() {
        this._connectToServer
            .get('/topup/collect-promotions')
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
                    promotions: res.body
                });
            });
    }
    
    render() {
        if (
            this.state.countries === null
            || this.state.providers === null
            || this.state.products === null
            || this.state.promotions === null
        ) {
            return (
                <Wait layout={this.props.layout}/>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
                bar="Proveedores"
            >
                {this.state.countries.map((country) => {
                    return <Card
                        key={country.iso}
                        style={{
                            background: "transparent",
                            marginBottom: "16px"
                        }}
                    >
                        <CardHeader
                            title={country.name + ' (' + country.iso + ')'}
                            actAsExpander={true}
                            showExpandableButton={true}
                        />
                            <CardText
                                expandable={true}
                                style={{padding: "16px"}}
                            >
                                {this.state.providers.map((provider) => {
                                    if (provider.country !== country.iso) {
                                        return null;
                                    }

                                    let i = 0;
                                    let j = 0;

                                    return (
                                        <Paper
                                            key={provider.id}
                                            style={{
                                                marginBottom: "16px",
                                                padding: "16px"
                                            }}
                                        >
                                            <img
                                                src={provider.logo !== null
                                                    ? "data:image/png;base64," + provider.logo
                                                    : defaultProviderLogo
                                                }
                                                alt={provider.name}
                                            />
                                            <p><strong>Id:</strong> {provider.id}</p>
                                            <p><strong>Nombre:</strong> {provider.name}</p>
                                            <p><strong>Validación:</strong> {provider.validation}</p>
                                            <p><strong>Productos: </strong></p>
                                            {this.state.products.map((product) => {
                                                if (product.provider !== provider.id) {
                                                    return null;
                                                }

                                                return <p key={product.code + product.value}>{++i}: ${product.value} {product.description}</p>
                                            })}
                                            <p><strong>Promociones: </strong></p>
                                            {this.state.promotions.map((promotion) => {
                                                if (promotion.provider !== provider.id) {
                                                    return null;
                                                }

                                                return <p key={promotion.id}>{++j}: {promotion.description}</p>
                                            })}
                                        </Paper>
                                    );
                                })}
                            </CardText>
                        </Card>;
                })}
            </this.props.layout.type>
        );
    }
}

