import React from 'react';

import FontIcon from 'material-ui/FontIcon';
import IconButton from 'material-ui/IconButton';
import {grey400} from 'material-ui/styles/colors';
import Moment from 'moment';
import {} from 'moment/locale/es';
Moment.locale('es');

export default class Offer extends React.Component {
    static propTypes = {
        container: React.PropTypes.element.isRequired,
        name: React.PropTypes.string.isRequired,
        contact: React.PropTypes.string.isRequired,
        address: React.PropTypes.string.isRequired,
        destinations: React.PropTypes.array.isRequired,
        description: React.PropTypes.string.isRequired,
        trips: React.PropTypes.array.isRequired,
        origin: React.PropTypes.string
    };

    constructor(props) {
        super(props);

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
                    <p><strong>Nombre: </strong>{this.props.name}</p>
                </div>
                <div style={{display: "flex", flexDirection: "row"}}>
                    <FontIcon
                        className="material-icons"
                        color={grey400}
                        style={{margin: "10px 10px 0 0"}}
                    >phone</FontIcon>
                    <p><strong>Contacto: </strong>{this.props.contact}</p>
                </div>
                <div style={{display: "flex", flexDirection: "row"}}>
                    <FontIcon
                        className="material-icons"
                        color={grey400}
                        style={{margin: "10px 10px 0 0"}}
                    >store_mall_directory</FontIcon>
                    <p><strong>Dirección: </strong>{this.props.address}</p>
                    <IconButton
                        tooltip="Ver ruta en el mapa"
                        href={
                            "https://www.google.com/maps/dir/"
                            + this.props.origin
                            + "/"
                            + this.props.address
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
                    <p><strong>Destinos: </strong>{this._generateDestinations(this.props.destinations)}</p>
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
                            this.props.trips,
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
                    <p><strong>Detalles: </strong>{this.props.description}</p>
                </div>
            </this.props.container.type>
        );
    }

    _generateDestinations(keys) {
        if (keys.length === this._destinations.length) {
            return "Todas las provincias";
        }

        const destinations = [];

        keys.map((key) => (
            destinations.push(this._destinations.find(destination => destination.key === key).text)
        ));

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