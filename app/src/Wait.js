import React from 'react';
import CircularProgress from 'material-ui/CircularProgress';

export default class Wait extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element
    };

    render() {
        const layout = typeof this.props.layout !== 'undefined'
            ? this.props.layout
            : <div/>;

        return (
            <layout.type {...layout.props}>
                <div style={{
                    display: "flex",
                    justifyContent: "center",
                    paddingTop: "10px"
                }}>
                    <CircularProgress size={20} />
                </div>
            </layout.type>
        );
    }
}