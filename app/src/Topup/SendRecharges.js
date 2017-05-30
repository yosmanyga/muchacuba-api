import React from 'react';
import {} from 'matchmedia-polyfill';
import InputIcon from 'material-ui/svg-icons/communication/contact-phone';
import Paper from 'material-ui/Paper';
import PaymentIcon from 'material-ui/svg-icons/editor/attach-money';
import ReviewIcon from 'material-ui/svg-icons/action/find-in-page';
import {Step, Stepper, StepButton} from 'material-ui/Stepper';
import 'flag-icon-css/css/flag-icon.min.css';

import ConnectToServer from '../ConnectToServer';
import AddTopupStep from './SendRecharge/AddTopupStep';
import ReviewStep from './SendRecharge/ReviewStep';
import PaymentStep from './SendRecharge/PaymentStep';

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
            stepStates: {
                addTopup: null
            },
            step: 0,
            currentRecharge: null,
            recharges: [],
        };

        this._connectToServer = new ConnectToServer();
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
                    display: "flex",
                    flexDirection: "column",
                    alignItems: "center",
                }}
            >
                {this._resolveStep(
                    this.state.step,
                    <Paper style={{
                        ...this.state.stepperStyle,
                        display: "flex",
                        flexDirection: "column",
                        alignItems: "center",
                        padding: "16px",
                    }}/>,
                    <Stepper
                        linear={false}
                        activeStep={this.state.step}
                    >
                        <Step>
                            <StepButton
                                icon={<InputIcon />}
                                onTouchTap={() => this.setState({step: 0})}
                            >Datos</StepButton>
                        </Step>
                        <Step>
                            <StepButton
                                icon={<ReviewIcon />}
                                onTouchTap={() => this.setState({step: 1})}
                            >Revisión</StepButton>
                        </Step>
                        <Step completed={false}>
                            <StepButton
                                icon={<PaymentIcon />}
                                onTouchTap={() => this.setState({step: 2})}
                            >Pago</StepButton>
                        </Step>
                    </Stepper>)
                }
            </this.props.layout.type>
        );
    }

    _resolveStep(step, layout, stepper) {
        switch (step) {
            case 0:
                return <AddTopupStep
                    layout={layout}
                    stepper={stepper}
                    state={this.state.stepStates.addTopup}
                    collectCountries={(callback) => {
                        this._connectToServer
                            .get('/topup/collect-countries')
                            .auth(this.props.profile.token)
                            .send()
                            .end((err, res) => {
                                if (err) {
                                    // TODO
                                }

                                callback(res.body);
                            });
                    }}
                    resolveProviders={(country, prefix, account, success, failure) => {
                        this._connectToServer
                            .get('/topup/resolve-providers/'
                                + country
                                + '/'
                                + prefix
                                + '/'
                                + account
                            )
                            .auth(this.props.profile.token)
                            .send()
                            .end((err, res) => {
                                if (err) {
                                    const response = JSON.parse(err.response.text);

                                    if (err.status === 422) {
                                        failure(response.type, response.payload);
                                    }
                                    // Other
                                    else {
                                        this.props.onError(
                                            err.status,
                                            response
                                        );
                                    }

                                    return;
                                }

                                success(res.body);
                            });
                    }}
                    collectProducts={(provider, callback) => {
                        this._connectToServer
                            .get('/topup/collect-products-by-provider/' + provider)
                            .auth(this.props.profile.token)
                            .send()
                            .end((err, res) => {
                                if (err) {
                                    this.props.onError(
                                        err.status,
                                        JSON.parse(err.response.text)
                                    );
                                }

                                callback(res.body);
                            });
                    }}
                    onChange={(state) => {
                        this.setState({
                            stepStates: {
                                ...this.state.stepStates,
                                addTopup: state
                            }
                        });
                    }}
                    onDone={(country, prefix, account, provider, product) => {
                        this.setState({
                            currentRecharge: {
                                country: country,
                                prefix: prefix,
                                account: account,
                                provider: provider,
                                product: product
                            },
                            step: this.state.step + 1
                        })
                    }}
                    onError={this.props.onError}
                />;
            case 1:
                return <ReviewStep
                    layout={layout}
                    stepper={stepper}
                    currentRecharge={this.state.currentRecharge}
                    recharges={this.state.recharges}
                    onDeleteCurrent={() => {
                        this.setState({
                            currentRecharge: null
                        });
                    }}
                    onDelete={(recharge) => {
                        this.setState({
                            recharges: this.state.recharges.filter((internalRecharge) => {
                                return internalRecharge !== recharge;
                            })
                        });
                    }}
                    onAddAnother={() => {
                        let recharges = this.state.recharges;
                        recharges.unshift(this.state.currentRecharge);

                        this.setState({
                            currentRecharge: null,
                            recharges: recharges,
                            step: this.state.step - 1,
                            stepStates: {
                                addTopup: null
                            }
                        })
                    }}
                    onDone={() => {
                        this.setState({
                            step: this.state.step + 1
                        })
                    }}
                />;
            case 2:
                return <PaymentStep
                    layout={layout}
                    stepper={stepper}
                    onBack={() => {
                        this.setState({
                            step: this.state.step - 1
                        })
                    }}
                />;
            default:
                throw new Error();
        }
    }
}
