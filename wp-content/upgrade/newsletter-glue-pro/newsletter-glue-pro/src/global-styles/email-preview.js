import React from 'react';

import {
  Component,
} from '@wordpress/element';

export default class NGEmailPreview extends Component {

  constructor(props) {

    super(props);

  }

  render() {

    const { isMobile } = this.props.getState;

    let theme = isMobile ? this.props.getState.theme_m : this.props.getState.theme_r;

    var fontFamily = nglue_backend.font_names[theme.font];

    var h1_size = isNaN(theme.h1_size) ? theme.h1_size.replace('px', '') : theme.h1_size;
    var h2_size = isNaN(theme.h2_size) ? theme.h2_size.replace('px', '') : theme.h2_size;
    var h3_size = isNaN(theme.h3_size) ? theme.h3_size.replace('px', '') : theme.h3_size;
    var h4_size = isNaN(theme.h4_size) ? theme.h4_size.replace('px', '') : theme.h4_size;
    var h5_size = isNaN(theme.h5_size) ? theme.h5_size.replace('px', '') : theme.h5_size;
    var h6_size = isNaN(theme.h6_size) ? theme.h6_size.replace('px', '') : theme.h6_size;

    return (
      <div className="editor-visual-editor">
        <div className="editor-visual-editor__content-area" style={{ fontFamily: fontFamily, backgroundColor: theme.email_bg, paddingTop: theme.container_margin1, paddingBottom: theme.container_margin2 }}>
          <div className={`ngl-email-preview-wrap ${isMobile ? "is-mobile-view" : "is-desktop-view"}`} style={{ backgroundColor: theme.email_bg }}>
            <div className="ngl-email-preview" style={{ paddingLeft: '20px', paddingRight: '20px', paddingBottom: theme.container_padding2, paddingTop: theme.container_padding1, backgroundColor: theme.container_bg }}>
              <h1 style={{ fontSize: h1_size + 'px', color: theme.h1_colour, fontFamily: nglue_backend.font_names[theme.h1_font] }}>H1: On the Ning Nang Nong</h1>
              <h2 style={{ fontSize: h2_size + 'px', color: theme.h2_colour, fontFamily: nglue_backend.font_names[theme.h2_font] }}>H2: Where the Cows go Bong!</h2>
              <h3 style={{ fontSize: h3_size + 'px', color: theme.h3_colour, fontFamily: nglue_backend.font_names[theme.h3_font] }}>H3: and the monkeys all say BOO!</h3>
              <h4 style={{ fontSize: h4_size + 'px', color: theme.h4_colour, fontFamily: nglue_backend.font_names[theme.h4_font] }}>H4: There is a Nong Nang Ning</h4>
              <h5 style={{ fontSize: h5_size + 'px', color: theme.h5_colour, fontFamily: nglue_backend.font_names[theme.h5_font] }}>H5: Where the trees go Ping!</h5>
              <h6 style={{ fontSize: h6_size + 'px', color: theme.h6_colour, fontFamily: nglue_backend.font_names[theme.h6_font] }}>H6: And the tea pots jibber jabber joo.</h6>
              <p style={{ fontSize: theme.p_size + 'px', color: theme.p_colour, fontFamily: nglue_backend.font_names[theme.p_font] }}>
                Paragraph: On the Nong Ning Nang<br />
                All the mice go Clang<br />
                And you just can not catch them when they do!<br />
                So its Ning Nang Nong<br />
                Cows go Bong!<br />
                Nong Nang Ning<br />
                Trees go ping<br />
                Nong Ning Nang<br />
                The mice go Clang<br />
                What a noisy place to belong<br />
                is the Ning Nang Ning Nang Nong!!<br />
                <a href="#" style={{ color: theme.a_colour }}>On the Ning Nang Nong, Spike Milligan</a>
              </p>
              <p style={{ paddingTop: '30px', paddingBottom: '15px' }}>
                <a href="#" className="wp-block-button__link" style={{
                  backgroundColor: theme.btn_bg,
                  borderRadius: parseInt(theme.btn_radius) + 'px',
                  borderWidth: '1px',
                  borderStyle: 'solid',
                  borderColor: theme.btn_border,
                  color: theme.btn_colour,
                  minWidth: theme.btn_width + 'px',
                  fontFamily: nglue_backend.font_names[theme.p_font]
                }}>Read more</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    );

  }

}