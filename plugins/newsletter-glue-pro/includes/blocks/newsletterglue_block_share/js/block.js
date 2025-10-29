( function( blocks, editor, element, components ) {

	const block = newsletterglue_block_share;
	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors, MediaUpload, PlainText, BlockControls, AlignmentToolbar } = editor;
	const { Fragment } = element;
	const { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, RangeControl, BaseControl, ButtonGroup, Button, ColorPicker, __experimentalBoxControl } = components;

	const ALLOWED_BLOCKS = [
		'newsletterglue/share-link',
	];

	const icon = el( 'svg', { width: 24, height: 24, viewBox: '0 0 90.545 63.382' },
		el( 'path',
			{
				fill: '#0088A0',
				transform: 'translate(0 -2.25)',
				d: "M27.164,33.941A15.845,15.845,0,1,0,11.318,18.1,15.837,15.837,0,0,0,27.164,33.941Zm10.865,4.527H36.855a21.877,21.877,0,0,1-19.382,0H16.3A16.3,16.3,0,0,0,0,54.766v4.075a6.793,6.793,0,0,0,6.791,6.791H47.536a6.793,6.793,0,0,0,6.791-6.791V54.766A16.3,16.3,0,0,0,38.029,38.468Zm29.88-4.527A13.582,13.582,0,1,0,54.327,20.359,13.585,13.585,0,0,0,67.909,33.941ZM74.7,38.468h-.538a17.841,17.841,0,0,1-12.507,0h-.538a15.714,15.714,0,0,0-7.88,2.179,20.7,20.7,0,0,1,5.617,14.119V60.2c0,.311-.071.608-.085.905H83.754a6.793,6.793,0,0,0,6.791-6.791A15.837,15.837,0,0,0,74.7,38.468Z"
			}
		)
	);

	registerBlockType( 'newsletterglue/share', {
		title: block.name,
		description: block.description,
		icon: icon,
		category: 'newsletterglue-legacy',
		attributes: {
			show_in_blog: {
				'type' : 'boolean',
				'default' : block.show_in_blog ? true : false
			},
			show_in_email: {
				'type' : 'boolean',
				'default' : block.show_in_email ? true : false
			},
			alignment: {
				'type' : 'string',
				'default' : 'left',
			},
			icon_size: {
				'type' : 'number',
				'default' : 24,
			},
			icon_shape: {
				'type' : 'string',
				'default' : 'round',
			},
			icon_color: {
				'type' : 'string',
				'default' : 'black',
			},
			add_description: {
				'type' : 'boolean',
				'default' : true,
			},
			description: {
				'type' : 'string',
				'default' : 'Follow me on',
			},
			description_size: {
				'type' : 'number',
				'default' : 13
			},
			new_window: {
				type: 'boolean',
				'default': true,
			},
			left_padding: {
				'type' : 'string',
				'default' : '0px',
			},
			right_padding: {
				'type' : 'string',
				'default' : '0px',
			},
			top_padding: {
				'type' : 'string',
				'default' : '10px',
			},
			bottom_padding: {
				'type' : 'string',
				'default' : '10px',
			},
			top_margin: {
				'type' : 'string',
				'default' : '20px',
			},
			bottom_margin: {
				'type' : 'string',
				'default' : '20px',
			}
		},
		edit: function( props ) {

			// Show description.
			if ( props.attributes.add_description ) {
				var showDescription = el( RichText, {
							tagName: 'div',
							className: 'ngl-share-description',
							value: props.attributes.description,
							format: 'string',
							onChange: ( value ) => { props.setAttributes( { description: value } ); },
							placeholder: 'Follow me on',
							style: { fontSize: props.attributes.description_size + 'px' }
						} );
			} else {
				var showDescription = '';
			}

			function onChangeAlignment( newAlignment ) {
				props.setAttributes( { alignment: newAlignment } );
			}

			const SocialPlaceholder = el( 'div', { className: 'ngl-block-social-links__social-placeholder' },
				el( 'div', { className: 'ngl-social-links' } ),
				el( 'div', { className: 'ngl-block-social-links__social-placeholder-icons' },
					el( 'div', { className: 'ngl-social-link ngl-social-link-twitter' } ),
					el( 'div', { className: 'ngl-social-link ngl-social-link-facebook' } ),
					el( 'div', { className: 'ngl-social-link ngl-social-link-instagram' } ),
				)
			);

			function onChangeColor( value ) {
				props.setAttributes( { icon_color: value } );
				var children = wp.data.select( 'core/block-editor' ).getBlocksByClientId( props.clientId )[0].innerBlocks;
				children.forEach( function( child ) {
					wp.data.dispatch('core/block-editor').updateBlockAttributes( child.clientId, { icon_color: value });
				} );
			}

			function onChangeShape( value ) {
				props.setAttributes( { icon_shape: value } );
				var children = wp.data.select( 'core/block-editor' ).getBlocksByClientId( props.clientId )[0].innerBlocks;
				children.forEach( function( child ) {
					wp.data.dispatch('core/block-editor').updateBlockAttributes( child.clientId, { icon_shape: value });
				} );
			}

			function onChangeSize( value ) {
				props.setAttributes( { icon_size: value } );
				var children = wp.data.select( 'core/block-editor' ).getBlocksByClientId( props.clientId )[0].innerBlocks;
				children.forEach( function( child ) {
					wp.data.dispatch('core/block-editor').updateBlockAttributes( child.clientId, { icon_size: value });
				} );
			}

			var showDescriptionsize = '';
			if ( props.attributes.add_description ) {
				var showDescriptionsize = el( BaseControl, {},
								el( RangeControl, {
									label: 'Description font size (pixels)',
									value: props.attributes.description_size,
									initialPosition: 13,
									min: 10,
									max: 50,
									allowReset: true,
									resetFallbackValue: 13,
									onChange: ( value ) => { props.setAttributes( { description_size: value } ); },
								} ),
							);
			}

			var top_padding = props.attributes.top_padding ? props.attributes.top_padding : 0;
			var bottom_padding = props.attributes.bottom_padding ? props.attributes.bottom_padding : 0;
			var left_padding = props.attributes.left_padding ? props.attributes.left_padding : 0;
			var right_padding = props.attributes.right_padding ? props.attributes.right_padding : 0;
			var top_margin = props.attributes.top_margin ? props.attributes.top_margin : 0;
			var bottom_margin = props.attributes.bottom_margin ? props.attributes.bottom_margin : 0;

			var boxStyles = {
				paddingTop: props.attributes.top_padding ? props.attributes.top_padding : 0,
				paddingBottom: props.attributes.bottom_padding ? props.attributes.bottom_padding : 0,
				paddingLeft: props.attributes.left_padding ? props.attributes.left_padding : 0,
				paddingRight: props.attributes.right_padding ? props.attributes.right_padding : 0,
				marginTop: props.attributes.top_margin ? props.attributes.top_margin : 0,
				marginBottom: props.attributes.bottom_margin ? props.attributes.bottom_margin : 0,
			};

			return (
				el( Fragment, {},
					el( BlockControls, {},
						el( AlignmentToolbar,
							{
								value: props.attributes.alignment,
								onChange: onChangeAlignment
							}
						)
					),
					el( InspectorControls, {},

						el( PanelBody, { title: 'General options', initialOpen: true },

							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Show description text',
									onChange: ( value ) => { props.setAttributes( { add_description: value } ); },
									checked: props.attributes.add_description,
								} )
							),

							showDescriptionsize,

							el( BaseControl, {},
								el( SelectControl, {
									label: 'Icon shape',
									value: props.attributes.icon_shape,
									onChange: onChangeShape,
									options: [
										{ value: 'round', label: 'Circle' },
										{ value: 'round_stroke', label: 'Outline circle' },
										{ value: 'square', label: 'Square' },
										{ value: 'rounded_corners', label: 'Rounded square' },
										{ value: 'rounded_stroke', label: 'Outlined square' },
										{ value: 'default', label: 'Default' },
									],
								} )
							),

							el( BaseControl, {},
								el( SelectControl, {
									label: 'Icon color',
									value: props.attributes.icon_color,
									onChange: onChangeColor,
									options: [
										{ value: 'black', label: 'Black' },
										{ value: 'color', label: 'Color' },
										{ value: 'grey', label: 'Gray' },
										{ value: 'white', label: 'White' },
									],
								} )
							),

							el( BaseControl, {},
								el( RangeControl, {
									label: 'Icon size (pixels)',
									value: props.attributes.icon_size,
									initialPosition: 12,
									min: 12,
									max: 128,
									allowReset: true,
									resetFallbackValue: 24,
									onChange: onChangeSize,
								} ),
							),

						),

						el( PanelBody, { title: 'Show/hide - newsletter block', initialOpen: true },

							el( BaseControl, {},
								el( ToggleControl,
									{
										label: 'Show in blog post',
										onChange: ( value ) => {
											props.setAttributes( { show_in_blog: value } );
										},
										checked: props.attributes.show_in_blog,
									}
								)
							),
							el( BaseControl, {},
								el( ToggleControl,
									{
										label: 'Show in email newsletter',
										onChange: ( value ) => {
											props.setAttributes( { show_in_email: value } );
										},
										checked: props.attributes.show_in_email,
									}
								)
							)
						),

						el( PanelBody, { title: 'Spacing', initialOpen: true, className: 'ngl-inspector' },
							el( BaseControl, {},
								el( __experimentalBoxControl, {
									label: 'Padding',
									values: {
										left: props.attributes.left_padding,
										right: props.attributes.right_padding,
										top: props.attributes.top_padding,
										bottom: props.attributes.bottom_padding,
									},
									resetValues: { top: '10px', bottom: '10px', left: '20px', right: '20px' },
									allowReset: true,
									splitOnAxis: false,
									sides: [ 'top', 'bottom', 'left', 'right' ],
									units: [ { value: 'px', label: 'px', default: 0 } ],
									onChange: ( values ) => {
										props.setAttributes( { top_padding: values.top, bottom_padding: values.bottom, left_padding: values.left, right_padding: values.right } );
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

					),
		 
					/*  
					 * Here will be your block markup 
					 */
					el( 'div', {
							className: props.className + ' wp-block-newsletter-share-' + props.attributes.alignment + ' ngl-image-size-' + props.attributes.icon_size,
							'data-image-size' : props.attributes.icon_size,
							style: boxStyles,
							'data-padding': top_padding + ',' + bottom_padding + ',' + left_padding + ',' + right_padding, 'data-margin' : top_margin + ',' + bottom_margin
						},
						showDescription,
						el( InnerBlocks, {
							allowedBlocks: ALLOWED_BLOCKS,
							orientation: 'horizontal',
							templateLock: false,
							__experimentalAppenderTagName: 'div',
							placeholder: props.isSelected ? SocialPlaceholder : SocialPlaceholder,
						} )
					)
				)
			);

		},

		save: function( props, className ) {

			var top_padding = props.attributes.top_padding ? props.attributes.top_padding : 0;
			var bottom_padding = props.attributes.bottom_padding ? props.attributes.bottom_padding : 0;
			var left_padding = props.attributes.left_padding ? props.attributes.left_padding : 0;
			var right_padding = props.attributes.right_padding ? props.attributes.right_padding : 0;
			var top_margin = props.attributes.top_margin ? props.attributes.top_margin : 0;
			var bottom_margin = props.attributes.bottom_margin ? props.attributes.bottom_margin : 0;

			var boxStyles = {
				paddingTop: props.attributes.top_padding ? props.attributes.top_padding : 0,
				paddingBottom: props.attributes.bottom_padding ? props.attributes.bottom_padding : 0,
				paddingLeft: props.attributes.left_padding ? props.attributes.left_padding : 0,
				paddingRight: props.attributes.right_padding ? props.attributes.right_padding : 0,
				marginTop: props.attributes.top_margin ? props.attributes.top_margin : 0,
				marginBottom: props.attributes.bottom_margin ? props.attributes.bottom_margin : 0,
			};

			// Show description.
			if ( props.attributes.description && props.attributes.add_description ) {
				var showDescription = el( RichText.Content, {
							tagName: 'div',
							className: 'ngl-share-description',
							value: props.attributes.description,
							style: { fontSize: props.attributes.description_size + 'px' }
						} );
			} else {
				var showDescription = '';
			}

            return (
                el( 'div', {
						className: props.className + ' wp-block-newsletter-share-' + props.attributes.alignment + ' ngl-image-size-' + props.attributes.icon_size,
						'data-image-size' : props.attributes.icon_size,
						style: boxStyles,
						'data-padding': top_padding + ',' + bottom_padding + ',' + left_padding + ',' + right_padding, 'data-margin' : top_margin + ',' + bottom_margin
					},
					showDescription,
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

/**
 * Share link.
 */
( function( blocks, editor, element, components ) {

	const block = newsletterglue_block_share;
	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors, MediaUpload, PlainText, BlockControls, AlignmentToolbar } = editor;
	const { Fragment } = element;
	const { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, RangeControl, BaseControl, ButtonGroup, Button, ColorPicker } = components;

	const tiktokIcon = el( 'svg', { width: 20, height: 20, viewBox: '-32 0 512 512' },
		el( 'path', {
			d: 'm432.734375 112.464844c-53.742187 0-97.464844-43.722656-97.464844-97.464844 0-8.285156-6.714843-15-15-15h-80.335937c-8.28125 0-15 6.714844-15 15v329.367188c0 31.59375-25.707032 57.296874-57.300782 57.296874s-57.296874-25.703124-57.296874-57.296874c0-31.597657 25.703124-57.300782 57.296874-57.300782 8.285157 0 15-6.714844 15-15v-80.335937c0-8.28125-6.714843-15-15-15-92.433593 0-167.632812 75.203125-167.632812 167.636719 0 92.433593 75.199219 167.632812 167.632812 167.632812 92.433594 0 167.636719-75.199219 167.636719-167.632812v-145.792969c29.851563 15.917969 63.074219 24.226562 97.464844 24.226562 8.285156 0 15-6.714843 15-15v-80.335937c0-8.28125-6.714844-15-15-15zm0 0'
		} )
	);

	const webIcon = el( 'svg', { width: 20, height: 20, viewBox: '0 0 20 20' },
		el( 'path', {
			fillRule: 'evenodd',
			d: 'M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z',
			clipRule: 'evenodd'
		} )
	);

	const flipboardIcon = el( 'svg', { width: 20, height: 20, viewBox: '0 0 448 512' },
		el( 'path', {
			d: 'M0 32v448h448V32H0zm358.4 179.2h-89.6v89.6h-89.6v89.6H89.6V121.6h268.8v89.6z',
		} )
	);

	const xIcon = el( 'svg', {
		viewBox: '0 0 30 30',
		width: 20,
		height: 20,
	},
		el( 'path', {
			d: 'M26.37,26l-8.795-12.822l0.015,0.012L25.52,4h-2.65l-6.46,7.48L11.28,4H4.33l8.211,11.971L12.54,15.97L3.88,26h2.65 l7.182-8.322L19.42,26H26.37z M10.23,6l12.34,18h-2.1L8.12,6H10.23z',
		} )
	);

	const icon = el( 'svg', { width: 24, height: 24, viewBox: '0 0 90.545 63.382' },
		el( 'path',
			{
				fill: '#0088A0',
				transform: 'translate(0 -2.25)',
				d: "M27.164,33.941A15.845,15.845,0,1,0,11.318,18.1,15.837,15.837,0,0,0,27.164,33.941Zm10.865,4.527H36.855a21.877,21.877,0,0,1-19.382,0H16.3A16.3,16.3,0,0,0,0,54.766v4.075a6.793,6.793,0,0,0,6.791,6.791H47.536a6.793,6.793,0,0,0,6.791-6.791V54.766A16.3,16.3,0,0,0,38.029,38.468Zm29.88-4.527A13.582,13.582,0,1,0,54.327,20.359,13.585,13.585,0,0,0,67.909,33.941ZM74.7,38.468h-.538a17.841,17.841,0,0,1-12.507,0h-.538a15.714,15.714,0,0,0-7.88,2.179,20.7,20.7,0,0,1,5.617,14.119V60.2c0,.311-.071.608-.085.905H83.754a6.793,6.793,0,0,0,6.791-6.791A15.837,15.837,0,0,0,74.7,38.468Z"
			}
		)
	);

	registerBlockType( 'newsletterglue/share-link', {
		title: block.name,
		description: block.description,
		icon: icon,
		parent: [ 'newsletterglue/share' ],
		supports: {
			reusable: false,
			html: false,
			customClassName: false,
		},
		variations: [
			{
				isDefault: true,
				name: 'instagram',
				title: 'Instagram',
				description: 'Add a social link to Instagram',
				icon: 'instagram',
				attributes: { service: 'instagram' },
				isActive: function ( blockAttributes, variationAttributes ) { return blockAttributes.service === variationAttributes.service }
			},
			{
				name: 'x',
				title: 'X',
				description: 'Add a social link to X',
				icon: xIcon,
				attributes: { service: 'x' },
				isActive: function ( blockAttributes, variationAttributes ) { return blockAttributes.service === variationAttributes.service }
			},
			{
				name: 'twitter',
				title: 'Twitter',
				description: 'Add a social link to Twitter',
				icon: 'twitter',
				attributes: { service: 'twitter' },
				isActive: function ( blockAttributes, variationAttributes ) { return blockAttributes.service === variationAttributes.service }
			},
			{
				name: 'facebook',
				title: 'Facebook',
				description: 'Add a social link to Facebook',
				icon: 'facebook',
				attributes: { service: 'facebook' },
				isActive: function ( blockAttributes, variationAttributes ) { return blockAttributes.service === variationAttributes.service }
			},
			{
				name: 'twitch',
				title: 'Twitch',
				description: 'Add a social link to Twitch',
				icon: 'twitch',
				attributes: { service: 'twitch' },
				isActive: function ( blockAttributes, variationAttributes ) { return blockAttributes.service === variationAttributes.service }
			},
			{
				name: 'tiktok',
				title: 'Tiktok',
				description: 'Add a social link to Tiktok',
				icon: tiktokIcon,
				attributes: { service: 'tiktok' },
				isActive: function ( blockAttributes, variationAttributes ) { return blockAttributes.service === variationAttributes.service }
			},
			{
				name: 'youtube',
				title: 'YouTube',
				description: 'Add a social link to YouTube',
				icon: 'youtube',
				attributes: { service: 'youtube' },
				isActive: function ( blockAttributes, variationAttributes ) { return blockAttributes.service === variationAttributes.service }
			},
			{
				name: 'linkedin',
				title: 'LinkedIn',
				description: 'Add a social link to LinkedIn',
				icon: 'linkedin',
				attributes: { service: 'linkedin' },
				isActive: function ( blockAttributes, variationAttributes ) { return blockAttributes.service === variationAttributes.service }
			},
			{
				name: 'pinterest',
				title: 'Pinterest',
				description: 'Add a social link to Pinterest',
				icon: 'pinterest',
				attributes: { service: 'pinterest' },
				isActive: function ( blockAttributes, variationAttributes ) { return blockAttributes.service === variationAttributes.service }
			},
			{
				name: 'email',
				title: 'Email',
				description: 'Add a social link to Email',
				icon: 'email',
				attributes: { service: 'email' },
				isActive: function ( blockAttributes, variationAttributes ) { return blockAttributes.service === variationAttributes.service }
			},
			{
				name: 'web',
				title: 'Web',
				description: 'Add a social link to Web',
				icon: webIcon,
				attributes: { service: 'web' },
				isActive: function ( blockAttributes, variationAttributes ) { return blockAttributes.service === variationAttributes.service }
			},
			{
				name: 'whatsapp',
				title: 'Whatsapp',
				description: 'Add a social link to Whatsapp',
				icon: 'whatsapp',
				attributes: { service: 'whatsapp' },
				isActive: function ( blockAttributes, variationAttributes ) { return blockAttributes.service === variationAttributes.service }
			},
			{
				name: 'flipboard',
				title: 'Flipboard',
				description: 'Add a social link to Flipboard',
				icon: flipboardIcon,
				attributes: { service: 'flipboard' },
				isActive: function ( blockAttributes, variationAttributes ) { return blockAttributes.service === variationAttributes.service }
			},
		],
		category: 'newsletterglue-blocks',
		attributes: {
			service: {
				type: 'string',
			},
			url: {
				type: 'string',
			},
			attrs: {
				type: 'object',
			},
			icon_size: {
				'type' : 'number',
				'default' : 24,
			},
			icon_shape: {
				'type' : 'string',
				'default' : 'round',
			},
			icon_color: {
				'type' : 'string',
				'default' : 'black',
			},
			new_window: {
				type: 'boolean',
				'default': true,
			}
		},
		edit: function( props ) {

			const parentBlocks = wp.data.select( 'core/block-editor' ).getBlockParents(props.clientId);
			var last = Object.keys(parentBlocks).pop();
			const parentAttributes = wp.data.select('core/block-editor').getBlocksByClientId(parentBlocks)[ last ];

			if ( parentAttributes && parentAttributes.attributes ) {
				if ( props.attributes.icon_color !== parentAttributes.attributes.icon_color ) {
					props.setAttributes( { icon_color: parentAttributes.attributes.icon_color } );
				}

				if ( props.attributes.icon_shape !== parentAttributes.attributes.icon_shape ) {
					props.setAttributes( { icon_shape: parentAttributes.attributes.icon_shape } );
				}

				if ( props.attributes.icon_size !== parentAttributes.attributes.icon_size ) {
					props.setAttributes( { icon_size: parentAttributes.attributes.icon_size } );
				}
			}

			return (
				el( Fragment, {},

					el( InspectorControls, {},

						el( PanelBody, { title: 'Social link options', initialOpen: true },
							el( BaseControl, {},
								el( TextControl, {
									label: 'URL',
									value: props.attributes.url,
									onChange: ( value ) => { props.setAttributes( { url: value } ); },
								} )
							),
							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Open link in new tab',
									onChange: ( value ) => { props.setAttributes( { new_window: value } ); },
									checked: props.attributes.new_window,
								} )
							),
						),

					),
		 
					/*  
					 * Here will be your block markup 
					 */
					el( 'span', {
						className: 'ngl-social-link ngl-social-link-' + props.attributes.service,
						href: props.attributes.url,

					},
						el( 'img', {
							src: block.assets + '/' + props.attributes.icon_shape + '/' + props.attributes.icon_color + '/' + props.attributes.service + '.png',
							width: props.attributes.icon_size,
							height: props.attributes.icon_size,
							style: { width: props.attributes.icon_size, height: props.attributes.icon_size },
							className: 'ngl-inline-image',
						} )
					)
				)
			);

		},

		save: function( props, className ) {

            return (
					el( 'a', {
						className: 'ngl-social-link ngl-social-link-' + props.attributes.service,
						href: props.attributes.url,
						target: props.attributes && props.attributes.new_window ? '_blank' : '_self',
						rel: 'noopener',
					},
						el( 'img', {
							src: block.assets + '/' + props.attributes.icon_shape + '/' + props.attributes.icon_color + '/' + props.attributes.service + '.png',
							width: props.attributes.icon_size,
							height: props.attributes.icon_size,
							style: { width: props.attributes.icon_size, height: props.attributes.icon_size },
							className: 'ngl-inline-image',
						} )
					)
            );
        },

	} );

} ) (
	window.wp.blocks,
	window.wp.blockEditor,
	window.wp.element,
	window.wp.components
);