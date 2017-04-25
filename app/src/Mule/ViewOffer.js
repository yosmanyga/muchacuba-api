import React from 'react';
import Paper from 'material-ui/Paper';

import ConnectToServer from '../ConnectToServer';
import Wait from '../Wait';

import Map from './Map';

export default class ViewOffer extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        query: React.PropTypes.object.isRequired,
        destinations: React.PropTypes.array
    };

    constructor(props) {
        super(props);

        this._connectToServer = new ConnectToServer();

        this.state = {
            offer: null
        };
    }

    componentDidMount() {
        this._connectToServer
            .get('/mule/pick-offer/' + this.props.query.id)
            .send()
            .end((err, res) => {
                if (err) {
                    // TODO

                    return;
                }

                this.setState({
                    offer: res.body
                });
            });
    }

    render() {
        if (
            this.props.destinations === null
            || this.state.offer === null
        ) {
            return <Wait layout={this.props.layout}/>;
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
                style={{height: "100%"}}
            >
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
                    offers={[this.state.offer]}
                    offer={this.state.offer.id}
                />
            </this.props.layout.type>
        );
    }
}