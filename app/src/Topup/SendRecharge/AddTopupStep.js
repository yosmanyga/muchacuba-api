import React from 'react';
import {} from 'matchmedia-polyfill';
import DropDownMenu from 'material-ui/DropDownMenu';
import FontIcon from 'material-ui/FontIcon';
import AccountIcon from 'material-ui/svg-icons/hardware/phone-android';
import MenuItem from 'material-ui/MenuItem';
import Paper from 'material-ui/Paper';
import OperatorIcon from 'material-ui/svg-icons/action/settings-input-antenna';
import ProductIcon from 'material-ui/svg-icons/image/iso';
import TextField from 'material-ui/TextField';
import {Step, Stepper, StepContent, StepLabel} from 'material-ui/Stepper';
import {lime100 as checkColor} from 'material-ui/styles/colors';
import 'flag-icon-css/css/flag-icon.min.css';
import defaultProviderLogo from '../provider.png';

import Wait from '../../Wait';
import Navigate from './Navigate';
import Error from '../../Error';

export default class AddTopupStep extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        stepper: React.PropTypes.element.isRequired,
        // (callback(countries))
        collectCountries: React.PropTypes.func.isRequired,
        // (country, account, success(providers), failure(type, payload))
        resolveProviders: React.PropTypes.func.isRequired,
        // (provider, callback(products))
        collectProducts: React.PropTypes.func.isRequired,
        state: React.PropTypes.object,
        // (country, prefix, account, provider, product)
        onDone: React.PropTypes.func.isRequired,
        // (status)
        onChange: React.PropTypes.func.isRequired,
        // (error)
        onError: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            step: 0,
            stepStates: {
                account: null,
                providers: null,
                products: null
            },
            input: {
                country: null,
                prefix: null,
                account: null,
                provider: null,
                product: null
            },
            data: {
                providers: null,
                products: null
            }
        };
    }

    componentWillMount() {
        if (this.props.state !== null) {
            this.setState(this.props.state);
        }
    }

    componentWillUpdate(nextProps, nextState) {
        if (nextState !== this.state) {
            this.props.onChange(nextState);
        }
    }

    render() {
        const stepLayout = <div style={{
            display: "flex",
            flexDirection: "column",
            alignItems: "center",
            padding: "16px"
        }}/>;

        return (
            <this.props.layout.type {...this.props.layout.props}>
                <this.props.stepper.type
                    {...this.props.stepper.props}
                    style={{
                        ...this.props.stepper.props.style,
                        width: "100%"
                    }}
                />
                <Stepper
                    activeStep={this.state.step}
                    orientation="vertical"
                    style={{width: "100%"}}
                >
                    <Step>
                        <StepLabel
                            icon={<AccountIcon />}
                        >
                            Teléfono
                        </StepLabel>
                        <StepContent>
                            <AccountStep
                                layout={stepLayout}
                                collectCountries={this.props.collectCountries}
                                resolveProviders={this.props.resolveProviders}
                                state={this.state.stepStates.account}
                                onChange={(state) => {
                                    this.setState({
                                        stepStates: {
                                            ...this.state.stepStates,
                                            account: state
                                        }
                                    });
                                }}
                                onNext={(country, prefix, account, providers) => {
                                    this.setState({
                                        input: {
                                            ...this.state.input,
                                            country: country,
                                            prefix: prefix,
                                            account: account
                                        },
                                        data: {
                                            ...this.state.data,
                                            providers: providers,
                                        },
                                        step: this.state.step + 1
                                    });
                                }}
                                onError={this.props.onError}
                            />
                        </StepContent>
                    </Step>
                    <Step>
                        <StepLabel
                            icon={<OperatorIcon />}
                        >
                            Operador
                        </StepLabel>
                        <StepContent>
                            {this.state.data.providers !== null ? <ProvidersStep
                                layout={stepLayout}
                                providers={this.state.data.providers}
                                collectProducts={this.props.collectProducts}
                                state={this.state.stepStates.providers}
                                onChange={(state) => {
                                    this.setState({
                                        stepStates: {
                                            ...this.state.stepStates,
                                            providers: state
                                        }
                                    });
                                }}
                                onNext={(provider, products) => {
                                    this.setState({
                                        input: {
                                            ...this.state.input,
                                            provider: provider
                                        },
                                        data: {
                                            ...this.state.data,
                                            products: products,
                                        },
                                        step: this.state.step + 1
                                    });
                                }}
                                onBack={() => {
                                    this.setState({
                                        step: this.state.step - 1
                                    });
                                }}
                            /> : null}
                        </StepContent>
                    </Step>
                    <Step>
                        <StepLabel
                            icon={<ProductIcon />}
                        >
                            Cantidad
                        </StepLabel>
                        <StepContent>
                            {this.state.input.products !== null ? <ProductsStep
                                layout={stepLayout}
                                provider={this.state.input.provider}
                                products={this.state.data.products}
                                state={this.state.stepStates.products}
                                onChange={(state) => {
                                    this.setState({
                                        stepStates: {
                                            ...this.state.stepStates,
                                            products: state
                                        }
                                    });
                                }}
                                onBack={() => {
                                    this.setState({
                                        step: this.state.step - 1
                                    });
                                }}
                                onNext={(product) => {
                                    this.setState({
                                        input: {
                                            ...this.state.input,
                                            product: product
                                        }
                                    }, () => {
                                        this.props.onDone(
                                            this.state.input.country,
                                            this.state.input.prefix,
                                            this.state.input.account,
                                            this.state.input.provider,
                                            this.state.input.product
                                        );
                                    });
                                }}
                            /> : null}
                        </StepContent>
                    </Step>
                </Stepper>
            </this.props.layout.type>
        );
    }
}

class AccountStep extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        state: React.PropTypes.object,
        // (callback(countries))
        collectCountries: React.PropTypes.func.isRequired,
        // (country, account, callback(success(providers), failure(type, payload)))
        resolveProviders: React.PropTypes.func.isRequired,
        // (state)
        onChange: React.PropTypes.func.isRequired,
        // (country, prefix, account, providers)
        onNext: React.PropTypes.func.isRequired,
        // (error)
        onError: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            data: {
                countries: null
            },
            input: {
                country: null,
                prefix: null,
                account: ''
            },
            error: null,
            style: {
                flexDirection: "column"
            }
        };
    }

    componentWillMount() {
        if (this.props.state !== null) {
            this.setState(this.props.state);
        }
    }

    componentDidMount() {
        this.props.collectCountries((countries) => {
            this.setState({
                data: {
                    ...this.state.data,
                    countries: countries
                }
            });
        });

        const tabletAndUpMediaQuery = window.matchMedia('(min-width: 768px)');

        if (tabletAndUpMediaQuery.matches) {
            this.setState({
                style: {
                    flexDirection: "row"
                },
            });
        }

        tabletAndUpMediaQuery.addListener((mq) => {
            if (mq.matches) {
                this.setState({
                    style: {
                        flexDirection: "row"
                    },
                });
            } else {
                this.setState({
                    style: {
                        flexDirection: "column"
                    },
                });
            }
        });
    }

    componentWillUpdate(nextProps, nextState) {
        if (nextState !== this.state) {
            this.props.onChange(nextState);
        }
    }

    render() {
        if (this.state.data.countries === null) {
            return <Wait layout={this.props.layout}/>;
        }

        return (
            <this.props.layout.type {...this.props.layout.props}>
                <div
                    style={{
                        display: "flex",
                        alignItems: "center",
                        ...this.state.style
                    }}
                >
                    <div style={{
                        display: "flex",
                        alignItems: "center"
                    }}
                    >
                        {this.state.input.country !== null ? <span
                            className={"flag-icon flag-icon-" + this.state.input.country.toLowerCase()}
                            style={{fontSize: '2em'}}
                        /> : null}
                        <DropDownMenu
                            value={this.state.input.country !== null
                                ? this.state.input.country + ':' + this.state.input.prefix
                                : null
                            }
                            maxHeight={200}
                            onChange={(event, index, value) => {
                                if (value === null) {
                                    return;
                                }

                                const values = value.split(':');

                                this.setState({
                                    input: {
                                        ...this.state.input,
                                        country: values[0],
                                        prefix: values[1]
                                    }
                                });
                            }}
                        >
                            <MenuItem
                                value={null}
                                primaryText="Selecciona el país"
                            />
                            {this.state.data.countries.map((country) => {
                                // Ignore countries with more than one dialing
                                // TODO: Improve UI to handle this
                                if (country.dialings.length > 1) {
                                    return null;
                                }

                                // Ignore countries with no dialing
                                // TODO: Ask provider
                                if (country.dialings.length === 0) {
                                    return null;
                                }

                                // Ignore countries with no flag on current library
                                // TODO: Check flags status
                                // XK: https://github.com/lipis/flag-icon-css/pull/256
                                // AN: https://github.com/lipis/flag-icon-css/issues/334
                                if (
                                    country.iso === 'XK'
                                    || country.iso === 'AN'
                                ) {
                                    return null;
                                }

                                // const text =
                                //     country.name
                                //     + ' ('
                                //     + country.dialings.map((dialing) => {
                                //         return '+' + dialing.prefix;
                                //     }).join(',')
                                //     + ')';

                                // const text =
                                //     country.name
                                //     + ' (+'
                                //     + country.dialings[0].prefix
                                //     + ')';

                                const text = country.name;

                                return (
                                    <MenuItem
                                        key={country.iso}
                                        value={country.iso + ':' + country.dialings[0].prefix}
                                        primaryText={text}
                                        leftIcon={<span className={"flag-icon flag-icon-" + country.iso.toLowerCase()}/>}
                                    />
                                );
                            })}
                        </DropDownMenu>
                    </div>
                    {this.state.input.country !== null ? <TextField
                        hintText="Teléfono"
                        type="phone"
                        autoFocus={true}
                        fullWidth={true}
                        value={'+' + this.state.input.prefix + this.state.input.account}
                        onChange={(event, value) => {
                            const prefix = '+' + this.state.input.prefix;

                            // Ignore change if user removed part of prefix
                            if (!value.startsWith(prefix)) {
                                return;
                            }

                            // Remove prefix from value, so it doesn't repeat
                            value = value.replace(prefix, '');

                            this.setState({
                                input: {
                                    ...this.state.input,
                                    account: value
                                }
                            });
                        }}
                        style={{paddingTop: "8px"}}
                        underlineStyle={{bottom: "4px"}}
                    /> : null}
                </div>
                {this.state.error !== null ? <Error
                    message={this.state.error}
                /> : null}
                <Navigate
                    key="buttons"
                    layout={<div/>}
                    buttons={[
                        {
                            label: "Siguiente",
                            icon: "arrow_forward",
                            disabled:
                                this.state.input.country === null
                                || this.state.input.prefix === null
                                || this.state.input.account === '',
                            onTouchTap: (finish) => {
                                this.setState({
                                    error: null
                                }, () => {
                                    this.props.resolveProviders(
                                        this.state.input.country,
                                        this.state.input.prefix,
                                        this.state.input.account,
                                        (providers) => {
                                            finish(() => {
                                                this.props.onNext(
                                                    this.state.input.country,
                                                    this.state.input.prefix,
                                                    this.state.input.account,
                                                    providers
                                                );
                                            })
                                        },
                                        (type, payload) => {
                                            if (
                                                type === 'invalid-field'
                                                && payload['field'] === 'account'
                                            ) {
                                                finish(() => {
                                                    this.setState({
                                                        error: "El número entrado no es válido."
                                                    });
                                                })
                                            } else {
                                                throw new Error();
                                            }
                                        }
                                    );
                                });
                            }
                        }
                    ]}
                />
            </this.props.layout.type>
        );
    }
}

class ProvidersStep extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        state: React.PropTypes.object,
        providers: React.PropTypes.array,
        // (provider, callback(products))
        collectProducts : React.PropTypes.func.isRequired,
        // (state)
        onChange: React.PropTypes.func.isRequired,
        // (provider, products)
        onNext: React.PropTypes.func.isRequired,
        // ()
        onBack: React.PropTypes.func.isRequired,
    };

    constructor(props) {
        super(props);

        this.state = {
            input: {
                provider: null
            }
        };
    }

    componentWillMount() {
        if (this.props.state !== null) {
            this.setState(this.props.state);
        }
    }

    componentWillUpdate(nextProps, nextState) {
        if (nextState !== this.state) {
            this.props.onChange(nextState);
        }
    }

    render() {
        return (
            <this.props.layout.type {...this.props.layout.props}>
                <div style={{
                    display: "flex",
                    justifyContent: "center",
                    flexWrap: "wrap"
                }}>
                    {this.props.providers.length > 0 ? this.props.providers.map((provider) => {
                        return <Paper
                            key={provider.id}
                            style={{
                                flex: 3,
                                margin: "4px 2% 0 0",
                                padding: "16px",
                                display: "flex",
                                flexDirection: "column",
                                alignItems: "center",
                                cursor: "pointer",
                                backgroundColor: provider === this.state.input.provider
                                    ? checkColor
                                    : "transparent"
                            }}
                            onTouchTap={() => {
                                this.setState({
                                    input: {
                                        ...this.state.input,
                                        provider: provider
                                    }
                                });
                            }}
                        >
                            <img
                                src={provider.logo !== null
                                    ? "data:image/png;base64," + provider.logo
                                    : defaultProviderLogo
                                }
                                alt={provider.name}
                                style={{maxWidth: '100%'}}
                            />
                            <p style={{
                                flex: 1, // Stick check button to bottom
                                textAlign: "center"
                            }}>{provider.name}</p>
                            <FontIcon className="material-icons">
                                {provider === this.state.input.provider
                                    ? "check"
                                    : "panorama_fish_eye"
                                }
                            </FontIcon>
                        </Paper>
                    }) : "Actualmente no existen operadores para este país."}
                </div>
                <Navigate
                    key="buttons"
                    layout={<div/>}
                    buttons={[
                        {
                            label: "Anterior",
                            icon: "arrow_back",
                            onTouchTap: this.props.onBack
                        },
                        {
                            label: "Siguiente",
                            icon: "arrow_forward",
                            disabled: this.state.input.provider === null,
                            onTouchTap: (finish) => {
                                this.props.collectProducts(
                                    this.state.input.provider.id,
                                    (products) => {
                                        finish(
                                            this.props.onNext(
                                                this.state.input.provider,
                                                products
                                            )
                                        )
                                    }
                                );
                            }
                        }
                    ]}
                />
            </this.props.layout.type>
        );
    }
}

class ProductsStep extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        state: React.PropTypes.object,
        products: React.PropTypes.array,
        // (state)
        onChange: React.PropTypes.func.isRequired,
        // (product)
        onNext: React.PropTypes.func.isRequired,
        // ()
        onBack: React.PropTypes.func.isRequired,
    };

    constructor(props) {
        super(props);

        this.state = {
            input: {
                product: null
            }
        };
    }

    componentWillMount() {
        if (this.props.state !== null) {
            this.setState(this.props.state);
        }
    }

    componentWillUpdate(nextProps, nextState) {
        if (nextState !== this.state) {
            this.props.onChange(nextState);
        }
    }

    render() {
        return (
            <this.props.layout.type {...this.props.layout.props}>
                <div style={{
                    display: "flex",
                    justifyContent: "center",
                    flexWrap: "wrap"
                }}>
                    {this.props.products.length !== 0 ? this.props.products.map((product) => {
                        return <Paper
                            key={product.code + product.value}
                            style={{
                                flex: 3,
                                margin: "4px 2% 0 0",
                                padding: "16px",
                                display: "flex",
                                flexDirection: "column",
                                alignItems: "center",
                                cursor: "pointer",
                                backgroundColor: product === this.state.input.product
                                    ? checkColor
                                    : "transparent"
                            }}
                            onTouchTap={() => {
                                this.setState({
                                    input: {
                                        ...this.state.input,
                                        product: product
                                    }
                                });
                            }}
                        >
                            <p style={{
                                flex: 1, // Stick check button to bottom
                                textAlign: "center"
                            }}>${product.value} USD</p>
                            <FontIcon className="material-icons">
                                {product === this.state.input.product
                                    ? "check"
                                    : "panorama_fish_eye"
                                }
                            </FontIcon>
                        </Paper>
                    }) : "Actualmente no existen recargas para este operador."}
                </div>
                <Navigate
                    key="buttons"
                    layout={<div/>}
                    buttons={[
                        {
                            label: "Anterior",
                            icon: "arrow_back",
                            onTouchTap: this.props.onBack
                        },
                        {
                            label: "Siguiente",
                            icon: "arrow_forward",
                            disabled: this.state.input.product === null,
                            onTouchTap: (finish) => {
                                finish(
                                    this.props.onNext(
                                        this.state.input.product
                                    )
                                );
                            }
                        }
                    ]}
                />
            </this.props.layout.type>
        );
    }
}

