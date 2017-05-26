import React from 'react';
import {} from 'matchmedia-polyfill';
import DropDownMenu from 'material-ui/DropDownMenu';
import FontIcon from 'material-ui/FontIcon';
import AccountIcon from 'material-ui/svg-icons/hardware/phone-android';
import InputIcon from 'material-ui/svg-icons/communication/contact-phone';
import MenuItem from 'material-ui/MenuItem';
import Paper from 'material-ui/Paper';
import OperatorIcon from 'material-ui/svg-icons/action/settings-input-antenna';
import ProductIcon from 'material-ui/svg-icons/action/class';
import PaymentIcon from 'material-ui/svg-icons/editor/attach-money';
import ReviewIcon from 'material-ui/svg-icons/action/find-in-page';
import TextField from 'material-ui/TextField';
import {Step, Stepper, StepContent, StepLabel} from 'material-ui/Stepper';
import {lime100 as checkColor} from 'material-ui/styles/colors';
import 'flag-icon-css/css/flag-icon.min.css';
import defaultProviderLogo from './provider.png';

import ConnectToServer from '../ConnectToServer';
import Button from '../Button';
import Wait from '../Wait';

export default class SendRecharges extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        profile: React.PropTypes.object,
        // (status, response)
        onError: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            stepperStyle: {
                width: "100%"
            },
            step: 0,
            recharges: [],

        }
    }

    componentDidMount() {
        const tabletAndUpMediaQuery = window.matchMedia('(min-width: 768px)');

        if (tabletAndUpMediaQuery.matches) {
            this.setState({
                stepperStyle: {
                    width: "50%"
                },
            });
        }

        tabletAndUpMediaQuery.addListener((mq) => {
            if (mq.matches) {
                this.setState({
                    stepperStyle: {
                        width: "50%"
                    },
                });
            } else {
                this.setState({
                    stepperStyle: {
                        width: "100%"
                    },
                });
            }
        });
    }

    render() {
        return (
            <this.props.layout.type
                {...this.props.layout.props}
                bar="Enviar recargas"
                style={{
                    ...this.props.layout.props.style,
                    ...this.state.stepperStyle
                }}
            >
                <Stepper
                    activeStep={this.state.step}
                >
                    <Step completed={false}>
                        <StepLabel
                            icon={<InputIcon />}
                        >
                            Entrada de datos
                        </StepLabel>
                    </Step>
                    <Step completed={false}>
                        <StepLabel
                            icon={<ReviewIcon />}
                        >
                            Verificación
                        </StepLabel>
                    </Step>
                    <Step completed={false}>
                        <StepLabel
                            icon={<PaymentIcon />}
                        >
                            Pago
                        </StepLabel>
                    </Step>
                </Stepper>
                {this._resolveStep(this.state.step)}
            </this.props.layout.type>
        );
    }

    _resolveStep(step) {
        switch (step) {
            case 0:
                return <AddTopupStep
                    layout={<Paper/>}
                    profile={this.props.profile}
                    onError={this.props.onError}
                />;
            case 1:
                return <p>Verificaaa...</p>
        }
    }

}

class AddTopupStep extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        profile: React.PropTypes.object,
        // (error)
        onError: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            step: 0,
            subStates: {
                inputAccount: null,
                pickProvider: null,
                pickProduct: null
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

    render() {
        const stepLayout = <div style={{
            display: "flex",
            flexDirection: "column",
            alignItems: "center",
            padding: "16px"
        }}/>;

        return (
            <this.props.layout.type {...this.props.layout.props}>
                <Stepper activeStep={this.state.step} orientation="vertical">
                    <Step>
                        <StepLabel
                            icon={<AccountIcon />}
                        >
                            Teléfono
                        </StepLabel>
                        <StepContent>
                            <InputAccountStep
                                layout={stepLayout}
                                profile={this.props.profile}
                                state={this.state.subStates.inputAccount}
                                onNext={(state, providers, products) => {
                                    this.setState({
                                        subStates: {
                                            ...this.state.subStates,
                                            inputAccount: state
                                        },
                                        data: {
                                            ...this.state.data,
                                            providers: providers,
                                            products: products
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
                            {
                                this.state.data.providers !== null
                                    ? <PickProviderStep
                                        layout={stepLayout}
                                        providers={this.state.data.providers}
                                        products={this.state.data.products}
                                        state={this.state.subStates.pickProvider}
                                        onBack={() => {
                                            this.setState({
                                                step: this.state.step - 1
                                            });
                                        }}
                                        onNext={(provider) => {
                                            this.setState({
                                                input: {
                                                    ...this.state.input,
                                                    provider: provider
                                                },
                                                step: this.state.step + 1
                                            });
                                        }}
                                    /> : null
                            }
                        </StepContent>
                    </Step>
                    <Step>
                        <StepLabel
                            icon={<ProductIcon />}
                        >
                            Producto
                        </StepLabel>
                        <StepContent>
                            {
                                this.state.products !== null
                                    ? <PickProductStep
                                    layout={stepLayout}
                                    provider={this.state.input.provider}
                                    products={this.state.data.products}
                                    state={this.state.subStates.pickProduct}
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
                                            },
                                            step: this.state.step + 1
                                        });
                                    }}
                                /> : null
                            }
                        </StepContent>
                    </Step>
                </Stepper>
            </this.props.layout.type>
        );
    }
}

class InputAccountStep extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        profile: React.PropTypes.object,
        state: React.PropTypes.object,
        // (state, providers, products)
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
        };

        this._connectToServer = new ConnectToServer();
    }

    componentWillMount() {
        if (this.props.state !== null) {
            this.setState(this.props.state);
        }
    }

    componentDidMount() {
        this._connectToServer
            .get('/topup/collect-countries')
            .auth(this.props.profile.token)
            .send()
            .end((err, res) => {
                if (err) {
                    // TODO
                }

                this.setState({
                    data: {
                        ...this.state.data,
                        countries: res.body
                    }
                });
            });
    }

    render() {
        if (this.state.data.countries === null) {
            return <Wait layout={this.props.layout}/>;
        }

        return (
            <this.props.layout.type {...this.props.layout.props}>
                <div
                    key="input"
                    style={{
                        display: "flex"
                    }}
                >
                    {this.state.input.country !== null
                        ? <span
                            className={"flag-icon flag-icon-" + this.state.input.country}
                            style={{fontSize: '2em'}}
                        /> : null
                    }
                    <DropDownMenu
                        value={this.state.input.country}
                        maxHeight={200}
                        onChange={(event, index, value) => {
                            this.setState({
                                input: {
                                    ...this.state.input,
                                    country: value,
                                    prefix: this.state.data.countries.find((country) => {
                                        return country.iso.toLowerCase() === value;
                                    }).dialings[0].prefix
                                }
                            });
                        }}
                    >
                        <MenuItem
                            value={null}
                            primaryText="Selecciona el país"
                        />
                        {this._buildCountryMenu(this.state.data.countries)}
                    </DropDownMenu>
                    {this.state.input.country !== null
                        ? <TextField
                            hintText="Teléfono"
                            type="phone"
                            autoFocus={true}
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
                        /> : null
                    }
                </div>
                <Navigate
                    key="buttons"
                    layout={<div/>}
                    buttons={[
                        {
                            label: "Siguiente",
                            icon: "arrow_forward",
                            onTouchTap: (finish) => {
                                this._connectToServer
                                    .post('/topup/resolve-providers-and-products')
                                    .auth(this.props.profile.token)
                                    .send({
                                        'account': this.state.input.prefix + this.state.input.account
                                    })
                                    .end((err, res) => {
                                        if (err) {
                                            this.props.onError(
                                                err.status,
                                                JSON.parse(err.response.text)
                                            );
                                        }

                                        finish(
                                            this.props.onNext(
                                                this.state,
                                                res.body.providers,
                                                res.body.products
                                            )
                                        );
                                    });
                            }
                        }
                    ]}
                />
            </this.props.layout.type>
        );
    }

    _buildCountryMenu(countries) {
        return (
            countries.map((country) => {
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
                        value={country.iso.toLowerCase()}
                        primaryText={text}
                        leftIcon={<span className={"flag-icon flag-icon-" + country.iso.toLowerCase()}/>}
                    />
                );
            })
        );
    }
}

class PickProviderStep extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        state: React.PropTypes.object,
        providers: React.PropTypes.array,
        // (state, provider)
        onNext: React.PropTypes.func.isRequired,
        onBack: React.PropTypes.func.isRequired,
    };

    constructor(props) {
        super(props);

        this.state = {
            provider: null
        };
    }

    componentWillMount() {
        if (this.props.state !== null) {
            this.setState(this.props.state);
        }
    }

    render() {
        return (
            <this.props.layout.type {...this.props.layout.props}>
                <div style={{
                    display: "flex",
                    justifyContent: "center",
                    flexWrap: "wrap",
                    width: "100%",
                }}>
                    {this.props.providers.map((provider) => {
                        return <Paper
                            key={provider.id}
                            style={{
                                width: "23%",
                                margin: "4px 2% 0 0",
                                padding: "16px",
                                display: "flex",
                                flexDirection: "column",
                                alignItems: "center",
                                cursor: "pointer",
                                backgroundColor: provider === this.state.provider
                                    ? checkColor
                                    : "transparent"
                            }}
                            onTouchTap={() => {
                                this.setState({
                                    provider: provider
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
                                flex: 1 // Stick check button to bottom
                            }}>{provider.name}</p>
                            <FontIcon className="material-icons">
                                {provider === this.state.provider
                                    ? "check"
                                    : "panorama_fish_eye"
                                }
                            </FontIcon>
                        </Paper>
                    })}
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
                            onTouchTap: (finish) => {
                                finish(
                                    this.props.onNext(
                                        this.state,
                                        this.state.provider
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

class PickProductStep extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        state: React.PropTypes.object,
        provider: React.PropTypes.object,
        products: React.PropTypes.array,
        // (state, product)
        onNext: React.PropTypes.func.isRequired,
        onBack: React.PropTypes.func.isRequired,
    };

    constructor(props) {
        super(props);

        this.state = {
            product: null
        };
    }

    componentWillMount() {
        if (this.props.state !== null) {
            this.setState(this.props.state);
        }
    }

    render() {
        return (
            <this.props.layout.type {...this.props.layout.props}>
                <div style={{
                    display: "flex",
                    justifyContent: "center",
                    flexWrap: "wrap",
                    width: "100%",
                }}>
                    {this.props.products.map((product) => {
                        if (product.provider !== this.props.provider.id) {
                            return null;
                        }

                        return <Paper
                            key={product.id}
                            style={{
                                width: "23%",
                                margin: "4px 2% 0 0",
                                padding: "16px",
                                display: "flex",
                                flexDirection: "column",
                                alignItems: "center",
                                cursor: "pointer",
                                backgroundColor: product === this.state.product
                                    ? checkColor
                                    : "transparent"
                            }}
                            onTouchTap={() => {
                                this.setState({
                                    product: product
                                });
                            }}
                        >
                            <p style={{
                                flex: 1 // Stick check button to bottom
                            }}>{product.value}</p>
                            <FontIcon className="material-icons">
                                {product === this.state.product
                                    ? "check"
                                    : "panorama_fish_eye"
                                }
                            </FontIcon>
                        </Paper>
                    })}
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
                            onTouchTap: (finish) => {
                                finish(
                                    this.props.onNext(
                                        this.state,
                                        this.state.product
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

class Navigate extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        buttons: React.PropTypes.array
    };

    render() {
        return (
            <this.props.layout.type
                {...this.props.layout.props}
            >
                {this.props.buttons.map((button) => {
                    return <Button
                        key={button.label}
                        label={button.label}
                        labelAfterTouchTap={button.label}
                        icon={button.icon}
                        onTouchTap={(finish) => {
                            button.onTouchTap(finish);
                        }}
                        style={{
                            margin: "16px 4px 4px 4px"
                        }}
                    />;
                })}
            </this.props.layout.type>
        );
    }
}
