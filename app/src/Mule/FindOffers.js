/* global google */

import React from 'react';
import {grey400} from 'material-ui/styles/colors';
import {} from 'matchmedia-polyfill';
import {InfoWindow, GoogleMap as GoogleMapComponent, Marker, withGoogleMap} from "react-google-maps";
// import { Map as LeafletMap, Marker, Popup, TileLayer } from 'react-leaflet';
// import LeafletCSS from 'css!leaflet/dist/leaflet.css';
import AutoComplete from 'material-ui/AutoComplete';
import CircularProgress from 'material-ui/CircularProgress';
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

import Offer from './Offer';

export default class FindOffers extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        // (message, finish)
        onNotify: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this._connectToServer = new ConnectToServer();

        this.state = {
            destinations: null,
            address: null,
            coordinates: null,
            offers: null
        };

        this._handleSearch = this._handleSearch.bind(this);
        this._handleOriginSet = this._handleOriginSet.bind(this);
    }

    componentDidMount() {
        this._connectToServer
            .get('/mule/collect-destinations')
            .send()
            .end((err, res) => {
                if (err) {
                    // TODO

                    return;
                }

                let destinations = [];
                for (let property in res.body) {
                    if (res.body.hasOwnProperty(property)) {
                        destinations.push({
                            key: property,
                            value: res.body[property]
                        });
                    }
                }

                this.setState({
                    destinations: destinations
                });
            });
    }

    render() {
        if (this.state.destinations === null) {
            return (
                <this.props.layout.type
                    {...this.props.layout.props}
                >
                    <div style={{
                        display: "flex",
                        justifyContent: "center",
                        paddingTop: "10px"
                    }}>
                        <CircularProgress size={20} style={{marginTop: "10px"}}/>
                    </div>
                </this.props.layout.type>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
                style={{height: "100%"}}
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
                    destinations={this.state.destinations}
                    onOriginSet={this._handleOriginSet}
                    onSearch={this._handleSearch}
                />
                <GoogleMap
                    container={
                        <Paper
                            zDepth={2}
                            style={{
                                height: "100%",
                                flexGrow: 1
                            }}
                        />
                    }
                    coordinates={this.state.coordinates}
                    address={this.state.address}
                    offers={this.state.offers}
                />
            </this.props.layout.type>
        );
    }

    _handleOriginSet(address, coordinates) {
        this.setState({
            address: address,
            coordinates: coordinates,
            offers: null
        });
    }

    _handleSearch(coordinates, destination, from, to, finish) {
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
                    // TODO:

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

// class LeafletMap extends React.Component {
//     render() {
//         return (
//             <this.props.container.type {...this.props.container.props}>
//                 <LeafletMap center={[51.505, -0.09]} zoom={13} style={{height: "100%"}} {...this.props}>
//                     <TileLayer
//                         url='http://{s}.tile.osm.org/{z}/{x}/{y}.png'
//                     />
//                 </LeafletMap>
//             </this.props.container.type>
//         );
//     }
// }

const Map = withGoogleMap(props => (
    <GoogleMapComponent
        ref={props.onMapMounted}
        zoom={props.coordinates !== null ? 12 : 4}
        center={props.coordinates ? props.coordinates : {lat: 25.7823072, lng: -80.301121}}
    >
        {props.coordinates
            ?
                <Marker
                    position={props.coordinates}
                    icon="https://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                />
            :
            null
        }

        {props.offers.map((offer, index) => (
            <Marker
                key={index}
                position={offer.coordinates}
                icon="https://maps.google.com/mapfiles/ms/icons/red-dot.png"
                onTouchTap={() => props.onTouchTapOffer(offer)}
            >
                {props.offer !== null && props.offer.id === offer.id
                    ?
                        <InfoWindow
                            onCloseTouchTap={() => props.onCloseOffer()}
                        >
                            <Offer
                                container={<div/>}
                                name={offer.name}
                                contact={offer.contact}
                                address={offer.address}
                                description={offer.description}
                                destinations={offer.destinations}
                                trips={offer.trips}
                                origin={props.address}
                            />
                        </InfoWindow>
                    :
                        null
                }
            </Marker>
        ))}
    </GoogleMapComponent>
));

class GoogleMap extends React.Component {
    static propTypes = {
        container: React.PropTypes.element.isRequired,
        coordinates: React.PropTypes.object,
        address: React.PropTypes.string,
        offers: React.PropTypes.array
    };

    constructor(props) {
        super(props);

        this.state = {
            offer: null
        };

        this._handleTouchTapOffer = this._handleTouchTapOffer.bind(this);
        this._handleCloseOffer = this._handleCloseOffer.bind(this);
    }

    render() {
        return (
            <this.props.container.type {...this.props.container.props}>
                <Map
                    containerElement={<div style={{height: "100%"}}/>}
                    mapElement={<div style={{height: "100%"}}/>}
                    coordinates={this.props.coordinates}
                    address={this.props.address}
                    offers={this.props.offers || []}
                    offer={this.state.offer}
                    onTouchTapOffer={this._handleTouchTapOffer}
                    onCloseOffer={this._handleCloseOffer}
                />
            </this.props.container.type>
        );
    }

    _handleTouchTapOffer(offer) {
        this.setState({
            offer: offer,
        });
    }

    _handleCloseOffer() {
        this.setState({
            offer: null,
        });
    }
}