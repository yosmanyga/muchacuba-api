import React from 'react';

export default class Center extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
    };

    render() {
        return (
            <this.props.layout.type {...this.props.layout.props}>
                <div style={{
                    display: "flex",
                    justifyContent: "center",
                    paddingTop: "10px"
                }}>
                    {this.props.children}
                </div>
            </this.props.layout.type>
        );
    }
}
