( function( blocks, editor, element, components ) {

	const block = newsletterglue_block_callout;
	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors, BlockControls, AlignmentToolbar } = editor;
	const { Fragment } = element;
	const { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, ServerSideRender, RangeControl, BaseControl, __experimentalBoxControl } = components;

	var borderStyles = [
		{ value: 'dotted', label: 'dotted' },
		{ value: 'dashed', label: 'dashed' },
		{ value: 'solid', label: 'solid' },
		{ value: 'double', label: 'double' },
		{ value: 'groove', label: 'groove' },
		{ value: 'ridge', label: 'ridge' },
		{ value: 'inset', label: 'inset' },
		{ value: 'outset', label: 'outset' },
	];

	const icon = el( 'svg', { width: 24, height: 24, viewBox: '0 0 24 24' },
		el( 'path',
			{ 
				d: 'M21 15V18H24V20H21V23H19V20H16V18H19V15H21M14 18H3V6H19V13H21V6C21 4.89 20.11 4 19 4H3C1.9 4 1 4.89 1 6V18C1 19.11 1.9 20 3 20H14V18Z',
				fill: '#0088A0'
			}
		)
	);

	registerBlockType( 'newsletterglue/callout', {
		title: 'NG: ' + block.name,
		description: block.description,
		icon: icon,
		category: 'newsletterglue-legacy',
		keywords: [ 'newsletter', 'container', 'callout' ],
		attributes: {
			alignment: {
				'type' : 'string',
				'default' : 'left',
			},
			border_color: {
				'type' : 'string',
				'default' : '#eeeeee',
			},
			bg_color: {
				'type' : 'string',
				'default' : '#f9f9f9',
			},
			font_color: {
				'type' : 'string',
			},
			border_radius: {
				'type' : 'number',
			},
			border_size: {
				'type' : 'number',
				'default' : 0,
			},
			border_style: {
				'type' : 'string',
				'default' : 'solid',
			},
			cta_padding: {
				'type' : 'string',
				'default' : '20px',
			},
			cta_paddingb: {
				'type' : 'string',
				'default' : '20px',
			},
			cta_padding2: {
				'type' : 'string',
				'default' : '20px',
			},
			cta_padding2r: {
				'type' : 'string',
				'default' : '20px',
			},
			show_in_blog: {
				'type' : 'boolean',
				'default' : block.show_in_blog ? true : false
			},
			show_in_email: {
				'type' : 'boolean',
				'default' : block.show_in_email ? true : false
			},
			full_width: {
				'type' : 'boolean',
				'default' : 0,
			},
			top_margin: {
				'type' : 'string',
				'default' : '0px',
			},
			bottom_margin: {
				'type' : 'string',
				'default' : '0px',
			},
		},

		edit: withColors( 'formColor' ) ( function( props ) {

			var top_margin = props.attributes.top_margin ? props.attributes.top_margin : 0;
			var bottom_margin = props.attributes.bottom_margin ? props.attributes.bottom_margin : 0;

			var formStyles = {
				color: props.attributes.font_color,
				borderColor : props.attributes.border_color ? props.attributes.border_color : 'transparent',
				borderRadius : props.attributes.border_radius,
				borderStyle : props.attributes.border_style,
				borderWidth : props.attributes.border_size,
				paddingTop : props.attributes.cta_padding,
				paddingBottom : props.attributes.cta_paddingb,
				paddingLeft : props.attributes.cta_padding2,
				paddingRight : props.attributes.cta_padding2r,
				textAlign: props.attributes.alignment ? props.attributes.alignment : 'left',
				marginLeft: 0,
				marginRight: 0,
				marginTop: props.attributes.top_margin ? props.attributes.top_margin : 0,
				marginBottom: props.attributes.bottom_margin ? props.attributes.bottom_margin : 0,
			};

			if ( props.attributes.bg_color ) {
				formStyles[ 'backgroundColor' ] = props.attributes.bg_color;
			}

			function onChangeAlignment( newAlignment ) {
				props.setAttributes( { alignment: newAlignment } );
			}

			const blockTemplate = [
				[ 'core/paragraph', { }, [] ],
			];

			if ( props.attributes.font_color ) {
				var isColorSet = 'is-color-set';
			} else {
				var isColorSet = 'not-color-set';
			}

			var isFullwidth = props.attributes.full_width ? 'is-full-width' : '';

			var showBorderStyle = '';
			if ( props.attributes.border_size > 0 ) {
				showBorderStyle = el( BaseControl, {},
								el( SelectControl, {
									label: 'Border style',
									value: props.attributes.border_style,
									onChange: ( value ) => { props.setAttributes( { border_style: value } ); },
									options: borderStyles,
								} )
							);
			}

			if ( props.attributes.border_size ) {
			var getColorSettings = [
								{
									value: props.attributes.border_color,
									label: 'Border color',
									onChange: function( value ) {
										if ( ! value ) {
											value = 'transparent';
										}
										props.setAttributes( { border_color: value } );
									},
								},
								{
									value: props.attributes.bg_color,
									label: 'Background color',
									onChange: ( value ) => props.setAttributes( { bg_color: value } ),
								},
								{
									value: props.attributes.font_color,
									label: 'Font color',
									onChange: ( value ) => props.setAttributes( { font_color: value } ),
								},
							];
			} else {
			var getColorSettings = [
								{
									value: props.attributes.bg_color,
									label: 'Background color',
									onChange: ( value ) => props.setAttributes( { bg_color: value } ),
								},
								{
									value: props.attributes.font_color,
									label: 'Font color',
									onChange: ( value ) => props.setAttributes( { font_color: value } ),
								},
							];
			}

			return (

				el( Fragment, {},

					el( InspectorControls, {},

						el( PanelBody, { title: 'Spacing and sizing', initialOpen: true },

							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Full width container',
									onChange: ( value ) => { props.setAttributes( { full_width: value } ); },
									checked: props.attributes.full_width,
								} )
							),

							el( BaseControl, {},
								el( __experimentalBoxControl, {
									label: 'Padding',
									values: {
										top: props.attributes.cta_padding,
										bottom: props.attributes.cta_paddingb,
										left: props.attributes.cta_padding2,
										right: props.attributes.cta_padding2r
									},
									resetValues: { top: '20px', bottom: '20px', left: '20px', right: '20px', },
									allowReset: true,
									splitOnAxis: false,
									sides: [ 'top', 'bottom', 'left', 'right' ],
									units: [ { value: 'px', label: 'px', default: 0 } ],
									onChange: ( values ) => {
										props.setAttributes( { cta_padding: values.top, cta_paddingb: values.bottom, cta_padding2: values.left, cta_padding2r: values.right } );
									},
								} )
							),

							el( BaseControl, {},
								el( __experimentalBoxControl, {
									label: 'Margin',
									values: {
										top: props.attributes.top_margin,
										bottom: props.attributes.bottom_margin,
									},
									resetValues: { top: '0px', bottom: '0px' },
									allowReset: true,
									splitOnAxis: false,
									sides: [ 'top', 'bottom' ],
									units: [ { value: 'px', label: 'px', default: 0 } ],
									onChange: ( values ) => {
										props.setAttributes( { top_margin: values.top, bottom_margin: values.bottom } );
									},
								} )
							),

						),

						el( PanelBody, { title: 'Border options', initialOpen: true },

							el( BaseControl, {},
								el( RangeControl, {
									label: 'Border radius (pixels)',
									value: props.attributes.border_radius,
									initialPosition: 0,
									min: 0,
									max: 50,
									allowReset: true,
									resetFallbackValue: 0,
									onChange: ( value ) => { props.setAttributes( { border_radius: value } ); },
								} ),
							),

							el( BaseControl, {},
								el( RangeControl, {
									label: 'Border thickness (pixels)',
									value: props.attributes.border_size,
									initialPosition: 0,
									min: 0,
									max: 20,
									allowReset: true,
									resetFallbackValue: 0,
									onChange: ( value ) => { props.setAttributes( { border_size: value } ); },
								} ),
							),

							showBorderStyle,

						),

						el( PanelColorSettings, {
							initialOpen: true,
							title: 'Color options',
							colorSettings: getColorSettings
						} ),

						el( PanelBody, { title: 'Show/hide block', initialOpen: true },

							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Show in blog post',
									onChange: ( value ) => { props.setAttributes( { show_in_blog: value } ); },
									checked: props.attributes.show_in_blog,
								} )
							),
							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Show in email newsletter',
									onChange: ( value ) => { props.setAttributes( { show_in_email: value } ); },
									checked: props.attributes.show_in_email,
								} )
							)
						),

					),

					el( BlockControls, {},
						el( AlignmentToolbar,
							{
								value: props.attributes.alignment,
								onChange: onChangeAlignment
							}
						)
					),

					/*  
					 * Here will be your block markup 
					 */
					el( 'section', {
							className: props.className + ' ' + isColorSet + ' ' + isFullwidth,
							style: formStyles,
							'data-margin' : top_margin + ',' + bottom_margin
						},
						el( InnerBlocks, { template: blockTemplate }
						
						)
					)
				)
			);
		} ),

		save: function( props, className ) {

			var top_margin = props.attributes.top_margin ? props.attributes.top_margin : 0;
			var bottom_margin = props.attributes.bottom_margin ? props.attributes.bottom_margin : 0;

			var formStyles = {
				color: props.attributes.font_color,
				borderColor : props.attributes.border_color ? props.attributes.border_color : 'transparent',
				borderRadius : props.attributes.border_radius,
				borderStyle : props.attributes.border_style,
				borderWidth : props.attributes.border_size,
				paddingTop : props.attributes.cta_padding,
				paddingBottom : props.attributes.cta_paddingb,
				paddingLeft : props.attributes.cta_padding2,
				paddingRight : props.attributes.cta_padding2r,
				textAlign: props.attributes.alignment ? props.attributes.alignment : 'left',
				marginLeft: 0,
				marginRight: 0,
				marginTop: props.attributes.top_margin ? props.attributes.top_margin : 0,
				marginBottom: props.attributes.bottom_margin ? props.attributes.bottom_margin : 0,
			};

			if ( props.attributes.bg_color ) {
				formStyles[ 'backgroundColor' ] = props.attributes.bg_color;
			}

			if ( props.attributes.font_color ) {
				var isColorSet = 'is-color-set';
			} else {
				var isColorSet = 'not-color-set';
			}

			var isFullwidth = props.attributes.full_width ? 'is-full-width' : '';

            return (
                el( 'section',
					{
						className: props.className + ' ' + isColorSet + ' ' + isFullwidth,
						style: formStyles,
						'data-margin' : top_margin + ',' + bottom_margin
					},
					el( InnerBlocks.Content )
                )
            );
        },

		// Example.
		example: function() {

		},

	} );

} ) (
	window.wp.blocks,
	window.wp.blockEditor,
	window.wp.element,
	window.wp.components
);