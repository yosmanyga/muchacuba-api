import React from 'react';

import ViewStats from './ViewStats';

export default class ViewSystemStats extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        profile: React.PropTypes.object,
        // (status, response)
        onError: React.PropTypes.func.isRequired
    };

    render() {
        return (
            <ViewStats
                layout={this.props.layout}
                profile={this.props.profile}
                onError={this.props.onError}
                url="/aloleiro/compute-system-calls/"
                unit="USD"
            />
        );
    }
}

