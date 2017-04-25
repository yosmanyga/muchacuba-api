/* global google */

import React from 'react';
import AutoComplete from 'material-ui/AutoComplete';
import Chip from 'material-ui/Chip';
import ChipInput from 'material-ui-chip-input';
import DatePicker from 'material-ui/DatePicker';
import FontIcon from 'material-ui/FontIcon';
import IconButton from 'material-ui/IconButton';
import Paper from 'material-ui/Paper';
import TextField from 'material-ui/TextField';
import {grey400, red500} from 'material-ui/styles/colors';
import Moment from 'moment';
import {} from 'moment/locale/es';
Moment.locale('es');
import areIntlLocalesSupported from 'intl-locales-supported';
let DateTimeFormat;
if (areIntlLocalesSupported(['es'])) {
    DateTimeFormat = global.Intl.DateTimeFormat;
} else {
    const IntlPolyfill = require('intl');
    DateTimeFormat = IntlPolyfill.DateTimeFormat;
    require('intl/locale-data/jsonp/es');
}

import Map from './Map';

import ConnectToServer from '../ConnectToServer';
import Button from '../Button';
import Wait from '../Wait';

export default class ListMyOffers extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        destinations: React.PropTypes.array,
        // (onSuccess, onError)
        onBackAuth: React.PropTypes.func.isRequired,
        // ()
        onFrontAuth: React.PropTypes.func.isRequired,
        // (message, finish)
        onNotify: React.PropTypes.func.isRequired,
        // (status, response)
        onError: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            profile: null,
            offer: null,
            error: {
                field: null,
                message: null
            },
            saved: false // State to show message after saving
        };

        this._connectToServer = new ConnectToServer();

        this._pickOffer = this._pickOffer.bind(this);
        this._handleChanged = this._handleChanged.bind(this);
        this._handleAddressChanged = this._handleAddressChanged.bind(this);
        this._handleSave = this._handleSave.bind(this);
    }

    componentDidMount() {
        this.props.onBackAuth(
            (profile) => {
                if (profile.token === 'null') {
                    this.props.onFrontAuth();

                    return;
                }

                this.setState({
                    profile: profile
                });
            },
            () => {
                this.props.onFrontAuth();
            }
        );

        if (
            this.state.profile !== null
        ) {
            this._pickOffer();
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            prevState.profile === null
            && this.state.profile !== null
        ) {
            this._pickOffer();
        }
    }

    _pickOffer() {
        this._connectToServer
            .get('/mule/me/pick-offer')
            .auth(this.state.profile.token)
            .end((err, res) => {
                if (err) {
                    if (err.status === 401) {
                        this.props.onError(
                            err.status,
                            JSON.parse(err.response.text)
                        );
                    }

                    if (err.status === 404) {
                        this.setState({
                            offer: {
                                name: null,
                                contact: null,
                                address: null,
                                coordinates: null,
                                destinations: [],
                                description: null,
                                trips: []
                            }
                        });

                        return;
                    }
                }

                this.setState({
                    offer: res.body
                });
            });
    }

    render() {
        if (this.state.offer === null) {
            return (
                <Wait layout={this.props.layout}/>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
            >
                <Paper zDepth={2} style={{padding: "10px"}}>
                    <Name
                        value={this.state.offer.name}
                        error={this.state.error.field === 'name' ? this.state.error.message : null}
                        onChange={this._handleChanged.bind(this, 'name')}
                    />
                    <Contact
                        value={this.state.offer.contact}
                        error={this.state.error.field === 'contact' ? this.state.error.message : null}
                        onChange={this._handleChanged.bind(this, 'contact')}
                    />
                    <Address
                        value={{
                            address: this.state.offer.address,
                            coordinates: this.state.offer.coordinates
                        }}
                        error={this.state.error.field === 'address' ? this.state.error.message : null}
                        onChange={this._handleAddressChanged}
                    />
                    <Destinations
                        value={this.state.offer.destinations}
                        error={this.state.error.field === 'destinations' ? this.state.error.message : null}
                        onChange={this._handleChanged.bind(this, 'destinations')}
                    />
                    <Description
                        value={this.state.offer.description}
                        error={this.state.error.field === 'description' ? this.state.error.message : null}
                        onChange={this._handleChanged.bind(this, 'description')}
                    />
                    <Trips
                        value={this.state.offer.trips}
                        error={this.state.error.field === 'trips' ? this.state.error.message : null}
                        onChange={this._handleChanged.bind(this, 'trips')}
                    />
                    {this.state.error.field
                        ?
                            <Chip backgroundColor={red500} style={{marginTop: "20px"}}>
                                <span style={{color: "white"}}>Corrige los errores para poder guardar la información.</span>
                            </Chip>
                        :
                            null
                    }
                    <Button
                        label="Guardar"
                        labelAfterTouchTap="Guardando..."
                        icon="save"
                        onTouchTap={this._handleSave}
                        style={{
                            marginTop: "20px",
                            marginLeft: "10px"
                        }}
                    />
                </Paper>
            </this.props.layout.type>
        );
    }

    _handleSave(finish) {
        this._connectToServer
            .post(typeof this.state.offer.id === "undefined" ? '/mule/me/create-offer' : '/mule/me/update-offer')
            .auth(this.state.profile.token)
            .send(this.state.offer)
            .end((err, res) => {
                if (res.body.error) {
                    let message = null;
                    if (res.body.error.type === 'empty') {
                        if (res.body.error.field === 'trips') {
                            message = 'Debes agregar al menos una fecha.';
                        } else if (res.body.error.field === 'coordinates') {
                            message = 'Debes escribir una dirección correcta';
                        } else {
                            message = 'Esta casilla no puede ser vacía.';
                        }

                    }

                    this.setState({
                        error: {
                            field: res.body.error.field,
                            message: message
                        }
                    }, finish);
                }

                finish(this.props.onNotify("Los datos han sido guardados"));
            });
    }

    _handleChanged(key, value) {
        const offer = this.state.offer;

        offer[key] = value;

        this.setState({
            offer: offer
        });
    }

    _handleAddressChanged(address, coordinates) {
        this.setState({
            offer: {
                ...this.state.offer,
                address: address,
                coordinates: coordinates
            }
        });
    }
}

class Name extends React.Component {
    static propTypes = {
        value: React.PropTypes.string,
        error: React.PropTypes.string,
        onChange: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            value: null
        };

        this._handleChange = this._handleChange.bind(this);
    }

    componentWillMount() {
        this.setState({
            value: this.props.value || "" // The TextField component doesn't accept null value
        });
    }

    render() {
        return (
            <div style={{display: "flex", flexDirection: "row"}}>
                <FontIcon
                    className="material-icons"
                    color={grey400}
                    style={{margin: "10px 10px 0 0"}}
                >account_box</FontIcon>
                <TextField
                    value={this.state.value}
                    hintText="Nombre"
                    errorText={this.props.error}
                    fullWidth={true}
                    onChange={this._handleChange}
                />
            </div>
        );
    }

    _handleChange(e, value) {
        this.setState({
            value: value,
        }, this.props.onChange(value));
    }
}

class Contact extends React.Component {
    static propTypes = {
        value: React.PropTypes.string,
        error: React.PropTypes.string,
        onChange: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            value: null
        };

        this._handleChange = this._handleChange.bind(this);
    }

    componentWillMount() {
        this.setState({
            value: this.props.value || "" // The TextField component doesn't accept null value
        });
    }

    render() {
        return (
            <div style={{display: "flex", flexDirection: "row"}}>
                <FontIcon
                    className="material-icons"
                    color={grey400}
                    style={{margin: "10px 10px 0 0"}}
                >phone</FontIcon>
                <TextField
                    value={this.state.value}
                    hintText="Datos de contacto"
                    errorText={this.props.error}
                    fullWidth={true}
                    multiLine={true}
                    rows={1}
                    onChange={this._handleChange}
                />
            </div>
        );
    }

    _handleChange(e, value) {
        this.setState({
            value: value
        }, this.props.onChange(value));
    }
}

class Address extends React.Component {
    static propTypes = {
        value: React.PropTypes.shape({
            address: React.PropTypes.string,
            coordinates: React.PropTypes.shape({
                lat: React.PropTypes.number.isRequired,
                lng: React.PropTypes.number.isRequired
            })
        }),
        error: React.PropTypes.string,
        onChange: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            value: null,
            suggestions: []
        };

        this._handleChanged = this._handleChanged.bind(this);
        this._handleSelected = this._handleSelected.bind(this);
    }

    componentWillMount() {
        this.setState({
            value: {
                ...this.props.value,
                address: this.props.value.address || "" // The TextField component doesn't accept null value
            }
        });

        this._autocompleteService = new google.maps.places.AutocompleteService();
        this._geocoder = new google.maps.Geocoder();
    }

    render() {
        return (
            <div>
                <div
                    style={{
                        display: "flex",
                        flexDirection: "row"
                    }}
                >
                    <FontIcon
                        className="material-icons"
                        color={grey400}
                        style={{margin: "10px 10px 0 0"}}
                    >store_mall_directory</FontIcon>
                    <AutoComplete
                        searchText={this.state.value.address}
                        fullWidth={true}
                        hintText="Dirección"
                        filter={AutoComplete.caseInsensitiveFilter}
                        dataSource={this.state.suggestions}
                        errorText={this.props.error}
                        onUpdateInput={this._handleChanged}
                        onNewRequest={this._handleSelected}
                    />
                </div>
                <Map
                    container={<div style={{height: "200px"}}/>}
                    origin={{coordinates: this.state.value.coordinates}}
                />
            </div>
        );
    }

    _handleChanged(string) {
        this.setState({
            value: {
                address: string,
                coordinates: null
            }
        }, () => {
            this.props.onChange(string, null);

            if (!string) {
                return;
            }

            const options = {
                input: string
            };

            this._autocompleteService.getPlacePredictions(
                options,
                (serverSuggestions) => {
                    if (!serverSuggestions) {
                        return;
                    }

                    const suggestions = [];

                    serverSuggestions.forEach(function(serverSuggestion) {
                        suggestions.push(serverSuggestion.description)
                    });

                    this.setState({
                        suggestions: suggestions
                    });
                }
            );
        });
    }

    _handleSelected(address) {
        this._geocoder.geocode({address: address}, (results) => {
            const coordinates = {
                lat: results[0].geometry.location.lat(),
                lng: results[0].geometry.location.lng()
            };

            this.setState({
                value: {
                    address: address,
                    coordinates: coordinates
                }
            }, () => {
                this.props.onChange(address, coordinates);
            });
        });
    }
}

class Destinations extends React.Component {
    static propTypes = {
        value: React.PropTypes.arrayOf(React.PropTypes.string),
        error: React.PropTypes.string,
        onChange: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            value: null
        };

        this._destinations = [
            {key: 'hab', text: 'Habana'},
            {key: 'pri', text: 'Pinar del Río'},
            {key: 'ijv', text: 'Isla de la Juventud'},
            {key: 'art', text: 'Artemisa'},
            {key: 'may', text: 'Mayabeque'},
            {key: 'mtz', text: 'Matanzas'},
            {key: 'cfg', text: 'Cienfuegos'},
            {key: 'vcl', text: 'Villa Clara'},
            {key: 'ssp', text: 'Sancti Spíritus'},
            {key: 'cav', text: 'Ciego de Ávila'},
            {key: 'cmg', text: 'Camagüey'},
            {key: 'ltu', text: 'Las Tunas'},
            {key: 'hol', text: 'Holguín'},
            {key: 'gra', text: 'Granma'},
            {key: 'scu', text: 'Santiago de Cuba'},
            {key: 'gtm', text: 'Guantánamo'},
        ];
    }

    componentWillMount() {
        let value = null;

        if (this.props.value) {
            value = this._destinations.filter((destination) => {
                return this.props.value.find((key) => {
                    return key === destination.key
                });
            });
        }

        this.setState({
            value: value || []
        });
    }

    render() {
        return (
            <div
                style={Object.assign(
                    this.props.style ? this.props.style : {},
                    {
                        display: "flex",
                        flexDirection: "row",
                        flex: 1
                    }
                )}
            >
                <FontIcon
                    className="material-icons"
                    color={grey400}
                    style={{margin: "10px 10px 0 0"}}
                >local_shipping</FontIcon>
                <ChipInput
                    value={this.state.value}
                    hintText="Provincias"
                    errorText={this.props.error}
                    fullWidth={true}
                    openOnFocus={true}
                    dataSource={this._destinations}
                    dataSourceConfig={{ value: 'key', text: 'text' }}
                    menuStyle={{maxHeight: '200px'}}
                    inputStyle={{marginBottom: "16px"}}
                    hintStyle={{bottom: "12px"}}
                    onChange={() => {}}
                    onRequestAdd={(destination) => {
                        let isValid = this._destinations.find((value) => {
                                return value === destination;
                            }) || false;

                        if (!isValid) {
                            return;
                        }

                        const value = this.state.value.concat(destination);

                        this.setState({
                            value: value,
                        }, () => {
                            this.props.onChange(this.state.value.map((value) => value.key));
                        });
                    }}
                    onRequestDelete={(destination) => {
                        const value = this.state.value.filter((item) => {
                            return destination !== item.key
                        });

                        this.setState({
                            value: value,
                        }, () => {
                            this.props.onChange(this.state.value.map((value) => value.key));
                        });
                    }}
                />
            </div>
        );
    }
}

class Description extends React.Component {
    static propTypes = {
        value: React.PropTypes.string,
        error: React.PropTypes.string,
        onChange: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            value: null
        };

        this._handleChange = this._handleChange.bind(this);
    }

    componentWillMount() {
        this.setState({
            value: this.props.value || "" // The TextField component doesn't accept null value
        });
    }

    render() {
        return (
            <div
                style={{
                    display: "flex",
                    flexDirection: "row"
                }}
            >
                <FontIcon
                    className="material-icons"
                    color={grey400}
                    style={{margin: "10px 10px 0 0"}}
                >attach_money</FontIcon>
                <TextField
                    value={this.state.value}
                    hintText="Detalles del envío, costos, etc"
                    errorText={this.props.error}
                    fullWidth={true}
                    multiLine={true}
                    rows={1}
                    onChange={this._handleChange}
                />
            </div>
        );
    }

    _handleChange(e, value) {
        this.setState({
            value: value,
        }, () => {
            this.props.onChange(value);
        });
    }
}

class Trips extends React.Component {
    static propTypes = {
        value: React.PropTypes.arrayOf(
            React.PropTypes.number
        ),
        error: React.PropTypes.string,
        onChange: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            value: null
        };

        this._handleChange = this._handleChange.bind(this);
        this._handleDelete = this._handleDelete.bind(this);
    }

    componentWillMount() {
        let value = null;

        if (!this.props.value) {
            value = [];
        } else {
            value = this.props.value.map((value) => {
                return new Date(value);
            });
        }

        this.setState({
            value: value
        });
    }

    render() {
        return (
            <div>
                <div style={{display: "flex"}}>
                    <FontIcon
                        className="material-icons"
                        color={grey400}
                        style={{margin: "10px 10px 0 0"}}
                    >airplanemode_active</FontIcon>
                    <DatePicker
                        hintText="Fechas de viaje"
                        errorText={this.props.error}
                        autoOk={true}
                        container="inline"
                        value={null}
                        DateTimeFormat={DateTimeFormat}
                        locale="es"
                        onChange={this._handleChange}
                    />
                </div>
                {this.state.value
                    .sort((a, b) => {
                        return a.getTime() - b.getTime();
                    })
                    .map((value, index) => (
                        <div key={index} style={{display: "flex", marginLeft: "34px"}}>
                            <p style={{marginBottom: 0}}>{Moment(value).format('LL')}</p>
                            <IconButton
                                iconClassName="material-icons"
                                tooltip="Eliminar"
                                style={{marginBottom: 0}}
                                onTouchTap={this._handleDelete.bind(this, index)}
                            >delete</IconButton>
                        </div>
                    ))
                }
            </div>
        );
    }

    _handleChange(e, value) {
        this.state.value.push(value);

        this.setState({
            value: this.state.value
        }, () => {
            this.props.onChange(this.state.value.map((value) => {
                return value.getTime() / 1000;
            }));
        });
    }

    _handleDelete(index) {
        this.state.value.splice(index, 1);

        this.setState({
            value: this.state.value
        }, () => {
            this.props.onChange(this.state.value.map((value) => {
                return value.getTime();
            }));
        });
    }
}
