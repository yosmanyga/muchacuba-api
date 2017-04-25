import React from 'react';
import {InfoWindow, GoogleMap, Marker, withGoogleMap} from "react-google-maps";

import FontIcon from 'material-ui/FontIcon';
import IconButton from 'material-ui/IconButton';
import {grey400} from 'material-ui/styles/colors';
import Moment from 'moment';
import {} from 'moment/locale/es';
Moment.locale('es');

export default class Map extends React.Component {
    static propTypes = {
        container: React.PropTypes.element.isRequired,
        destinations: React.PropTypes.array,
        // User location
        origin: React.PropTypes.shape({
            address: React.PropTypes.string,
            coordinates: React.PropTypes.shape({
                lat: React.PropTypes.number,
                lng: React.PropTypes.number
            })
        }),
        offers: React.PropTypes.array,
        // Selected offer id
        offer: React.PropTypes.string
    };

    constructor(props) {
        super(props);

        this.state = {
            // Selected offer id
            offer: null
        };
    }

    componentDidMount() {
        if (typeof this.props.offer !== 'undefined') {
            this.setState({
                offer: this.props.offer
            });
        }
    }

    render() {
        return (
            <this.props.container.type {...this.props.container.props}>
                <GoogleMapInstance
                    containerElement={<div style={{height: "100%"}}/>}
                    mapElement={<div style={{height: "100%"}}/>}
                    destinations={this.props.destinations}
                    origin={this.props.origin}
                    offers={this.props.offers}
                    offer={this.state.offer}
                    onClickOffer={(offer) => {
                        this.setState({
                            offer: offer,
                        });
                    }}
                    onCloseOffer={() => {
                        this.setState({
                            offer: null,
                        });
                    }}
                />
            </this.props.container.type>
        );
    }
}

const GoogleMapInstance = withGoogleMap(props => (
    <GoogleMap
        ref={props.onMapMounted}
        zoom={props.origin && props.origin.coordinates
            ? 12
            : 4
        }
        center={props.origin && props.origin.coordinates
            ? props.origin.coordinates
            : {lat: 25.7823072, lng: -80.301121}
        }
    >
        {props.origin
            ?
                <Marker
                    position={props.origin.coordinates}
                    icon="https://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                />
            :
                null
        }

        {props.offers && props.offers.map((offer) => (
            <Marker
                key={offer.id}
                position={offer.coordinates}
                icon="https://maps.google.com/mapfiles/ms/icons/red-dot.png"
                onClick={() => props.onClickOffer(offer.id)}
            >
                {props.offer === offer.id
                    ?
                        <InfoWindow
                            onCloseClick={() => props.onCloseOffer()}
                        >
                            <Offer
                                container={<div/>}
                                destinations={props.destinations}
                                offer={offer}
                                origin={props.origin ? props.origin.address : null}
                            />
                        </InfoWindow>
                    :
                        null
                }
            </Marker>
        ))}
    </GoogleMap>
));

class Offer extends React.Component {
    static propTypes = {
        container: React.PropTypes.element.isRequired,
        destinations: React.PropTypes.array.isRequired,
        origin: React.PropTypes.string,
        offer: React.PropTypes.shape({
            name: React.PropTypes.string.isRequired,
            contact: React.PropTypes.string.isRequired,
            address: React.PropTypes.string.isRequired,
            destinations: React.PropTypes.array.isRequired,
            description: React.PropTypes.string.isRequired,
            trips: React.PropTypes.array.isRequired,
        })
    };

    constructor(props) {
        super(props);

        this._generateDestinations = this._generateDestinations.bind(this);
    }

    render() {
        return (
            <this.props.container.type {...this.props.container.props}>
                <div style={{display: "flex", flexDirection: "row"}}>
                    <FontIcon
                        className="material-icons"
                        color={grey400}
                        style={{margin: "10px 10px 0 0"}}
                    >account_box</FontIcon>
                    <p><strong>Nombre: </strong>{this.props.offer.name}</p>
                </div>
                <div style={{display: "flex", flexDirection: "row"}}>
                    <FontIcon
                        className="material-icons"
                        color={grey400}
                        style={{margin: "10px 10px 0 0"}}
                    >phone</FontIcon>
                    <p><strong>Contacto: </strong>{this.props.offer.contact}</p>
                </div>
                <div style={{display: "flex", flexDirection: "row"}}>
                    <FontIcon
                        className="material-icons"
                        color={grey400}
                        style={{margin: "10px 10px 0 0"}}
                    >store_mall_directory</FontIcon>
                    <p><strong>Dirección: </strong>{this.props.offer.address}</p>
                    <IconButton
                        tooltip="Ver ruta en el mapa"
                        href={
                            "https://www.google.com/maps/dir/"
                            + (this.props.origin ? this.props.origin : '')
                            + "/"
                            + this.props.offer.address
                        }
                        target="_blank"
                    ><FontIcon className="material-icons">directions</FontIcon></IconButton>
                </div>
                <div style={{display: "flex", flexDirection: "row"}}>
                    <FontIcon
                        className="material-icons"
                        color={grey400}
                        style={{margin: "10px 10px 0 0"}}
                    >local_shipping</FontIcon>
                    <p><strong>Destinos: </strong>{this._generateDestinations(this.props.offer.destinations)}</p>
                </div>
                <div style={{display: "flex", flexDirection: "row"}}>
                    <FontIcon
                        className="material-icons"
                        color={grey400}
                        style={{margin: "10px 10px 0 0"}}
                    >airplanemode_active</FontIcon>
                    <div><p><strong>Próximos viajes:</strong></p>
                    <ul style={{paddingLeft: 0, marginBottom: 0}}>
                        {this._generateTrips(
                            this.props.offer.trips,
                            <li style={{listStyle: "none", marginBottom: "5px"}}/>
                        )}
                    </ul></div>
                </div>
                <div style={{display: "flex", flexDirection: "row"}}>
                    <FontIcon
                        className="material-icons"
                        color={grey400}
                        style={{margin: "10px 10px 0 0"}}
                    >attach_money</FontIcon>
                    <p><strong>Detalles: </strong>{this.props.offer.description}</p>
                </div>
            </this.props.container.type>
        );
    }

    _generateDestinations(keys) {
        if (keys.length === this.props.destinations.length) {
            return "Todas las provincias";
        }

        const destinations = keys.map((key) => {
            return this.props.destinations.find((destination) => {
                return destination.key === key;
            }).value;
        });

        return destinations.join(', ');
    }

    _generateTrips(trips, container) {
        return trips
            .sort()
            .map((trip, i) =>
            <container.type {...container.props} key={i}>{Moment.unix(trip).format('LL')}</container.type>
        );
    }
}