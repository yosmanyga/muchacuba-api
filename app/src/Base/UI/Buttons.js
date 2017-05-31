import React from 'react';
import PropTypes from 'prop-types';

export default class Buttons extends React.Component {
    static propTypes = {
        layout: PropTypes.element,
        buttons: PropTypes.arrayOf(PropTypes.element).isRequired,
    };

    render() {
        const layout = typeof this.props.layout !== 'undefined'
            ? this.props.layout
            : <div style={{
                alignSelf: "center",
                paddingTop: "8px"
            }}/>;

        return <layout.type {...layout.props}>
            {this.props.buttons.map((button, i) => {
                return <button.type
                    key={i}
                    {...button.props}
                />;
            })}
        </layout.type>;
    }
}