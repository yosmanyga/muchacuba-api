import React from 'react';
import IconButton from 'material-ui/IconButton';
import DeleteIcon from 'material-ui/svg-icons/content/clear';
import {grey300 as lineColor} from 'material-ui/styles/colors';
import 'flag-icon-css/css/flag-icon.min.css';

import Navigate from './Navigate';

export default class ReviewStep extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        stepper: React.PropTypes.element.isRequired,
        recharges: React.PropTypes.array,
        // ()
        onDeleteCurrent: React.PropTypes.func.isRequired,
        // (recharge)
        onDelete: React.PropTypes.func.isRequired,
        // ()
        onAddAnother: React.PropTypes.func.isRequired,
        // ()
        onDone: React.PropTypes.func.isRequired,
    };

    render() {
        return (
            <this.props.layout.type {...this.props.layout.props}>
                <this.props.stepper.type
                    {...this.props.stepper.props}
                    style={{
                        ...this.props.stepper.props.style,
                        width: "100%"
                    }}
                />
                {this.props.currentRecharge !== null ? this._buildRow(
                    1,
                    this.props.currentRecharge,
                    () => {
                        this.props.onDeleteCurrent()
                    }
                ) : null}
                {this.props.recharges.length !== 0 ? this.props.recharges.map((recharge, i) => {
                    return this._buildRow(
                        (this.props.currentRecharge !== null ? 1 : 0) + i + 1,
                        recharge,
                        () => {
                            this.props.onDelete(recharge)
                        }
                    );
                }) : null}
                {this.props.currentRecharge === null
                && this.props.recharges.length === 0
                    ? "Añade al menos una recarga."
                    : null
                }
                {this.props.currentRecharge !== null
                || this.props.recharges.length !== 0
                    ? <div
                        style={{
                            display: "flex",
                            width: "100%",
                            marginTop: "8px",
                            paddingTop: "8px",
                            borderTop: "1px solid " + lineColor
                        }}
                    >
                        <span
                            style={{
                                width: "70%",
                                paddingRight: "16px"
                            }}
                        >
                            <strong>Total</strong>
                        </span>
                        <span
                            style={{
                                width: "15%",
                                paddingRight: "8px",
                                textAlign: "right"
                            }}
                        >
                            <strong>
                                {
                                    '$'
                                    + this.props.recharges.reduce(
                                        (total, recharge) => {
                                            return total + recharge.product.value;
                                        },
                                        // Initial value
                                        this.props.currentRecharge !== null
                                            ? this.props.currentRecharge.product.value
                                            : 0
                                        )
                                    + ' USD'
                                }
                            </strong>
                        </span>
                        <span style={{ width: "15%"}}/>
                    </div> : null
                }
                {this.props.currentRecharge !== null
                || this.props.recharges.length !== 0 ? <Navigate
                    key="buttons"
                    layout={<div/>}
                    buttons={[
                        {
                            label: "Añadir otra recarga",
                            icon: "add",
                            onTouchTap: (finish) => {
                                this.props.onAddAnother();
                            }
                        },
                        {
                            label: "Siguiente",
                            icon: "arrow_forward",
                            onTouchTap: (finish) => {
                                this.props.onDone();
                            }
                        }
                    ]}
                /> : null}
            </this.props.layout.type>
        );
    }

    _buildRow(i, recharge, onDelete) {
        return (
            <div
                key={recharge.country + recharge.prefix + recharge.account}
                style={{
                    width: "100%",
                    display: "flex",
                    marginTop: "8px"
                }}
            >
                <span
                    style={{
                        width: "5%",
                        paddingRight: "8px"
                    }}
                >
                    {i}.
                </span>
                <span
                    style={{
                        width: "5%",
                        paddingRight: "8px"
                    }}
                >
                    <span
                        className={"flag-icon flag-icon-" + recharge.country.toLowerCase()}
                        style={{
                            fontSize: "1em"
                        }}
                    />
                </span>
                <span
                    style={{
                        width: "25%",
                        paddingRight: "8px"
                    }}
                >
                    {'+' + recharge.prefix + recharge.account}
                </span>
                <span
                    style={{
                        width: "35%",
                        paddingRight: "8px"
                    }}
                >
                    {recharge.provider.name}
                </span>
                <span
                    style={{
                        width: "15%",
                        paddingRight: "8px",
                        textAlign: "right"
                    }}
                >
                    ${recharge.product.value} USD
                </span>
                <span style={{width: "15%", textAlign: "right"}}>
                    <IconButton
                        tooltip="Borrar recarga"
                        onTouchTap={onDelete}
                        style={{
                            height: "auto",
                            marginTop: "-4px",
                            padding: 0
                        }}
                    >
                        <DeleteIcon />
                    </IconButton>
                </span>
            </div>
        );
    }
}