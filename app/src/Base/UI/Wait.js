import React from 'react';
import PropTypes from 'prop-types';
import CircularProgress from 'material-ui/CircularProgress';

export default class Wait extends React.Component {
    static propTypes = {
        layout: PropTypes.element
    };

    render() {
        const layout = typeof this.props.layout !== 'undefined'
            ? this.props.layout
            : <div/>;

        return <layout.type
            {...layout.props}
            style={{
                ...this.props.style,
                display: "flex",
                justifyContent: "center",
                marginTop: "8px" // Useful when component is used without layout
            }}
        >
            <CircularProgress size={20} />
        </layout.type>
    }
}