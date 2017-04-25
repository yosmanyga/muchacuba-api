/* global google */

import React from 'react';
import {grey400} from 'material-ui/styles/colors';
import {} from 'matchmedia-polyfill';
import AutoComplete from 'material-ui/AutoComplete';
import FontIcon from 'material-ui/FontIcon';
import IconButton from 'material-ui/IconButton';
import MenuItem from 'material-ui/MenuItem';
import Paper from 'material-ui/Paper';
import SelectField from 'material-ui/SelectField';
import Moment from 'moment';
import {} from 'moment/locale/es';
Moment.locale('es');

import ConnectToServer from '../ConnectToServer';
import Button from '../Button';
import Wait from '../Wait';

import Map from './Map';

export default class FindOffers extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        destinations: React.PropTypes.array,
        // (message, finish)
        onNotify: React.PropTypes.func.isRequired,
        // (status, response)
        onError: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            origin: {
                address: null,
                coordinates: null
            },
            offers: null
        };

        this._connectToServer = new ConnectToServer();
    }

    render() {
        if (this.props.destinations === null) {
            return (
                <Wait layout={this.props.layout}/>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
                style={{
                    ...this.props.layout.props.style,
                    height: "100%"
                }}
            >
                <Form
                    container={
                        <Paper
                            zDepth={2}
                            style={{
                                display: "flex",
                                flexWrap: "wrap",
                                flexDirection: "row",
                                padding: "10px"
                            }}
                        />
                    }
                    destinations={this.props.destinations}
                    onOriginSet={(address, coordinates) => {
                        this.setState({
                            origin: {
                                address: address,
                                coordinates: coordinates
                            },
                            offers: null
                        });
                    }}
                    onSearch={(coordinates, destination, from, to, finish) => {
                        this._connectToServer
                            .post('/mule/search-offers')
                            .send({
                                coordinates: coordinates,
                                destination: destination,
                                from: from,
                                to: to
                            })
                            .end((err, res) => {
                                if (err) {
                                    this.props.onError(
                                        err.status,
                                        JSON.parse(err.response.text)
                                    );

                                    return;
                                }

                                this.setState({
                                    offers: res.body
                                }, () => {
                                    const message = res.body.length === 0
                                        ? 'No se encontraron mulas cerca de esa dirección.'
                                        : res.body.length === 1
                                            ? 'Se encontró 1 mula cerca de esa dirección.'
                                            : 'Se encontraron ' + res.body.length + ' mulas cerca de esa dirección.';

                                    this.props.onNotify(
                                        message,
                                        finish()
                                    );
                                })
                            });
                    }}
                />
                <Map
                    container={
                        <Paper
                            zDepth={2}
                            style={{
                                height: "100%",
                                flexGrow: 1
                            }}
                        />
                    }
                    destinations={this.props.destinations}
                    origin={this.state.origin}
                    offers={this.state.offers}
                />
            </this.props.layout.type>
        );
    }
}

class Form extends React.Component {
    static propTypes = {
        container: React.PropTypes.element.isRequired,
        destinations: React.PropTypes.array,
        onOriginSet: React.PropTypes.func,
        onSearch: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            coordinates: null,
            destination: null,
            from: null,
            to: null,

            coordinatesErrorOnNull: false,

            coordinatesStyle: {
                width: "100%"
            },
            destinationStyle: {
                width: "100%"
            },
            fromToStyle: {
                width: "100%"
            },
            buttonStyle: {
                width: "100%"
            }
        };

        this._handleOriginChanged = this._handleOriginChanged.bind(this);
        this._handleDestinationChanged = this._handleDestinationChanged.bind(this);
        this._handleFromToChanged = this._handleFromToChanged.bind(this);
        this._handleSearch = this._handleSearch.bind(this);
    }

    componentDidMount() {
        const tabletAndUpMediaQuery = window.matchMedia('(min-width: 768px)');

        if (tabletAndUpMediaQuery.matches) {
            this.setState({
                destinationStyle: {
                    width: "40%"
                },
                fromToStyle: {
                    width: "40%"
                },
                buttonStyle: {
                    width: "30%"
                }
            });
        }

        tabletAndUpMediaQuery.addListener((mq) => {
            if (mq.matches) {
                this.setState({
                    destinationStyle: {
                        width: "40%"
                    },
                    fromToStyle: {
                        width: "40%"
                    },
                    buttonStyle: {
                        width: "30%"
                    }
                });
            } else {
                this.setState({
                    destinationStyle: {
                        width: "100%"
                    },
                    fromToStyle: {
                        width: "100%"
                    },
                    buttonStyle: {
                        width: "100%"
                    }
                });
            }
        });
    }

    render() {
        return (
            <this.props.container.type {...this.props.container.props}>
                <Coordinates
                    onChange={this._handleOriginChanged}
                    errorOnNull={this.state.coordinatesErrorOnNull}
                    style={Object.assign(this.state.coordinatesStyle, {})}
                />
                <Destination
                    destinations={this.props.destinations}
                    onChange={this._handleDestinationChanged}
                    style={Object.assign(this.state.destinationStyle, {paddingLeft: "10px"})}
                />
                <FromTo
                    onChange={this._handleFromToChanged}
                    style={Object.assign(this.state.fromToStyle, {paddingLeft: "10px"})}
                />
                <div
                    style={Object.assign(this.state.buttonStyle, {paddingLeft: "10px"})}
                >
                    <Button
                        label="Buscar"
                        labelAfterTouchTap="Buscando..."
                        icon="search"
                        fullWidth={true}
                        onTouchTap={this._handleSearch}
                    />
                </div>
            </this.props.container.type>
        );
    }

    _handleOriginChanged(address, coordinates) {
        this.setState({
            coordinates: coordinates,
            coordinatesErrorOnNull: false
        }, () => {
            this.props.onOriginSet(address, coordinates)
        });
    }

    _handleDestinationChanged(destination) {
        this.setState({
            destination: destination
        });
    }

    _handleFromToChanged(from, to) {
        this.setState({
            from: from,
            to: to
        });
    }

    _handleSearch(finish) {
        if (!this.state.coordinates) {
            this.setState({
                coordinatesErrorOnNull: true
            });

            finish();

            return;
        }

        this.props.onSearch(
            this.state.coordinates,
            this.state.destination,
            this.state.from,
            this.state.to,
            finish
        );
    }
}

class Coordinates extends React.Component {
    static propTypes = {
        onChange: React.PropTypes.func.isRequired,
        errorOnNull: React.PropTypes.bool.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            mode: 'custom', // 'custom', 'auto'
            suggestions: []
        };

        this._changeToCustomMode = this._changeToCustomMode.bind(this);
        this._changeToAutoMode = this._changeToAutoMode.bind(this);
        this._handleCustomChanged = this._handleCustomChanged.bind(this);
        this._handleSelected = this._handleSelected.bind(this);
    }

    componentWillMount() {
        this._autocompleteService = new google.maps.places.AutocompleteService();
        this._geocoder = new google.maps.Geocoder();
    }

    render() {
        return (
            <div
                style={Object.assign(
                    this.props.style,
                    {
                        display: "flex",
                        flexDirection: "row"
                    }
                )}
            >
                {this._renderCustomControl()}
                {this._renderAutoControl()}
                {this._renderInput()}
            </div>
        );
    }

    _renderInput() {
        if (this.state.mode === 'custom') {
            return (
                <AutoComplete
                    fullWidth={true}
                    autoFocus={true}
                    hintText="Tu dirección"
                    filter={AutoComplete.caseInsensitiveFilter}
                    dataSource={this.state.suggestions}
                    errorText={this.props.errorOnNull ? "Debes escribir una dirección" : ""}
                    onUpdateInput={this._handleCustomChanged}
                    onNewRequest={this._handleSelected}
                />
            );
        } else {
            return (
                <p style={{marginTop: "15px"}}>Mi ubicación</p>
            );
        }
    }

    _renderCustomControl() {
        if (this.state.mode === 'auto') {
            return (
                <IconButton
                    tooltip="Escribir una dirección"
                    tooltipPosition="bottom-right"
                    onTouchTap={this._changeToCustomMode}
                >
                    <FontIcon className="material-icons">settings_ethernet</FontIcon>
                </IconButton>
            );
        } else {
            return (
                <IconButton
                    disabled={true}
                >
                    <FontIcon className="material-icons" disabled={true}>settings_ethernet</FontIcon>
                </IconButton>
            );
        }
    }

    _renderAutoControl() {
        if (this.state.mode === 'custom') {
            return (
                <IconButton
                    tooltip="Usar mi ubicación"
                    tooltipPosition="bottom-right"
                    onTouchTap={this._changeToAutoMode}
                >
                    <FontIcon className="material-icons">my_location</FontIcon>
                </IconButton>
            );
        } else {
            return (
                <IconButton
                    disabled={true}
                >
                    <FontIcon className="material-icons" disabled={true}>location_on</FontIcon>
                </IconButton>
            );
        }
    }

    _changeToCustomMode() {
        this.setState({
            mode: 'custom'
        });
    }

    _changeToAutoMode() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.setState({
                        mode: 'auto'
                    }, () => {
                        this.props.onChange(
                            position.coords.latitude + ', ' + position.coords.longitude,
                            {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            }
                        );
                    });
                }
            );
        }
    }

    _handleCustomChanged(value) {
        if (!value) {
            return;
        }

        const options = {
            input: value
        };

        this._autocompleteService.getPlacePredictions(
            options,
            (serverSuggestions) => {
                if (!serverSuggestions) {
                    return;
                }

                const suggestions = [];

                serverSuggestions.forEach((serverSuggestion) => {
                    suggestions.push(serverSuggestion.description)
                });

                this.setState({
                    suggestions: suggestions
                });
            }
        );
    }

    _handleSelected(address) {
        this._geocoder.geocode({address: address}, (results) => {
            this.props.onChange(
                address,
                {
                    lat: results[0].geometry.location.lat(),
                    lng: results[0].geometry.location.lng()
                }
            );
        });
    }
}

class Destination extends React.Component {
    static propTypes = {
        destinations: React.PropTypes.array,
        onChange: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            value: null
        };

        this._handleChange = this._handleChange.bind(this);
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
                <SelectField
                    hintText="Provincia"
                    value={this.state.value}
                    fullWidth={true}
                    onChange={this._handleChange}
                >
                    <MenuItem value={null} primaryText=""/>
                    {this.props.destinations.map((destination) =>
                        <MenuItem key={destination.key} value={destination.key} primaryText={destination.value}/>
                    )}
                </SelectField>
            </div>
        );
    }

    _handleChange(e, key, value) {
        this.setState({
            value: value
        }, () => {
            if (!value) {
                this.props.onChange(null);

                return;
            }

            this.props.onChange(this.state.value);
        });
    }
}

class FromTo extends React.Component {
    static propTypes = {
        onChange: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            value: null
        };

        this._handleChange = this._handleChange.bind(this);
    }

    render() {
        return (
            <div
                style={Object.assign(
                    this.props.style,
                    {
                        display: "flex",
                        flexDirection: "row"
                    }
                )}
            >
                <FontIcon
                    className="material-icons"
                    color={grey400}
                    style={{margin: "10px 10px 0 0"}}
                >date_range</FontIcon>
                <SelectField
                    hintText="Fecha de vuelo"
                    fullWidth={true}
                    value={this.state.value}
                    onChange={this._handleChange}
                >
                    <MenuItem value={null} primaryText="" />
                    <MenuItem value={7} primaryText="En los próximos 7 días" />
                    <MenuItem value={30} primaryText="En los próximos 30 días" />
                </SelectField>
            </div>
        );
    }

    _handleChange(e, key, value) {
        this.setState({
            value: value
        }, () => {
            if (!value) {
                this.props.onChange(null, null);

                return;
            }

            this.props.onChange(
                Moment().unix(),
                Moment().add(this.state.value, 'days').unix()
            );
        });
    }
}