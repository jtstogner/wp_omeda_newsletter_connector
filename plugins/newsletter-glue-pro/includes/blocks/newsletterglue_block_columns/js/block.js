// COLUMN - INDIVIDUAL index.js

( function( wp ) {

    var registerBlockType = wp.blocks.registerBlockType;
    var el = wp.element.createElement;

	const { TextControl, __experimentalUnitControl, __experimentalBoxControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, RangeControl, BaseControl, ButtonGroup, Button } = wp.components;
	const { Fragment } = wp.element;
    const { useBlockProps, InnerBlocks, InspectorControls, PanelColorSettings } = wp.blockEditor;
    const allowedBlocks = [
		'core/heading',
		'core/paragraph',
		'core/buttons',
		'core/button',
		'core/list',
		'core/image',
		'core/separator',
		'core/spacer',
		'core/post-date',
		'newsletterglue/author',
		'newsletterglue/metadata',
		'newsletterglue/share-link',
		'newsletterglue/share',
		'newsletterglue/article',
	];

	const MY_TEMPLATE = [
		[ 'core/paragraph', {} ]
	];

	const colIcon = el( 'svg', { width: 35, height: 25, viewBox: '0 0 35 26' },
		el( 'g', { transform: 'translate(-1600 -75)' },
			el( 'g', { transform: 'translate(-229 -427)' },
				el( 'g', { transform: "translate(1829 502)", fill: "none", stroke: "#0088A0", strokeWidth: 2 },
					el( 'rect', { width: 35, height: 26, rx: 3, stroke: 'none' } ),
					el( 'rect', { x: 1, y: 1, width: 33, height: 24, rx: 2, fill: 'none' } ),
				),
				el( 'rect', { width: 18, height: 25, rx: 3, transform: "translate(1829 502)", fill: "#0088A0" } ),
			),
			el( 'g', { transform: 'translate(1596.922 75.319)' },
				el( 'path', { d: 'M18,7.5V17.861', transform: 'translate(-5.319)', fill: 'none', stroke: '#fff', strokeLinecap: 'round', strokeLinejoin: 'round', strokeWidth: 2 } ),
				el( 'path', { d: 'M7.5,18H17.861', transform: 'translate(0 -5.319)', fill: 'none', stroke: '#fff', strokeLinecap: 'round', strokeLinejoin: 'round', strokeWidth: 2 } ),
			),
		)
	);

    registerBlockType( 'newsletterglue/column', {

        apiVersion: 2,
        title: 'NG: Single column',
        description: 'Customize the selected column.',
        category: 'newsletterglue-legacy',
        icon: colIcon,
        supports: {
            html: false,
        },
        parent: [ 'newsletterglue/columns' ],
		attributes: {
			width: {
				'type' : 'string',
			},
			background: {
				'type' : 'string',
			},
			left_padding: {
				'type' : 'string',
				'default' : '20px',
			},
			right_padding: {
				'type' : 'string',
				'default' : '20px',
			},
			top_padding: {
				'type' : 'string',
				'default' : '0px',
			},
			bottom_padding: {
				'type' : 'string',
				'default' : '0px',
			},
		},
        edit: function( props ) {

			var colStyle = {
				flexBasis: props.attributes.width ? props.attributes.width : 'auto',
			};

			if ( parseInt( props.attributes.width ) > 0 ) {
				var styleflex = {
					flexBasis: props.attributes.width,
					flexGrow: 0,
					width: props.attributes.width,
				};
			} else {
				var styleflex = {

				};
			}

			const blockProps = useBlockProps( {
				style: styleflex
			} );

			if ( blockProps.className ) {
				var customClass = blockProps.className.replace( 'block-editor-block-list__block wp-block wp-block-newsletterglue-column', '' );
				customClass = customClass.replace( 'block-editor-block-list__block wp-block has-child-selected wp-block-newsletterglue-column', '' );
				customClass = customClass.replace( 'wp-block-newsletterglue-column', '' ).trim();
			} else {
				var customClass = '';
			}

			const px_units = [
				{ value: 'px', label: 'px', default: 0 },
			];

			var boxStyles = {
				paddingTop: props.attributes.top_padding ? props.attributes.top_padding : '',
				paddingBottom: props.attributes.bottom_padding ? props.attributes.bottom_padding : '',
				paddingLeft: props.attributes.left_padding ? props.attributes.left_padding : '',
				paddingRight: props.attributes.right_padding ? props.attributes.right_padding : '',
			};

			if ( props.attributes.background ) {
				boxStyles[ 'backgroundColor' ] = props.attributes.background;
			}

			var colorSettings = [
				{
					value: props.attributes.background,
					label: 'Background color',
					onChange: ( value ) => props.setAttributes( { background: value } ),
				},
			];

			return (
				el( Fragment, {},

					el( InspectorControls, {},

						el( PanelColorSettings, {
							initialOpen: true,
							title: 'Color options',
							colorSettings: colorSettings
						} ),

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
									resetValues: { top: '0px', bottom: '0px', left: '20px', right: '20px' },
									allowReset: true,
									splitOnAxis: false,
									sides: [ 'top', 'bottom', 'left', 'right' ],
									units: px_units,
									onChange: ( values ) => {
										props.setAttributes( { top_padding: values.top, bottom_padding: values.bottom, left_padding: values.left, right_padding: values.right } );
									},
								} )
							),
						),
					),
					el(
						'div',
						blockProps,
						el(
							'div',
							{
								className: 'ng__column ' + customClass,
								style: boxStyles,
								'data-size' : props.attributes.width ? props.attributes.width : 'auto',
								'data-lpadding' : props.attributes.left_padding,
								'data-rpadding' : props.attributes.right_padding,
								'data-tpadding' : props.attributes.top_padding,
								'data-bpadding' : props.attributes.bottom_padding
							},

							el(
								InnerBlocks, 
								{
									allowedBlocks: allowedBlocks,
									template: MY_TEMPLATE,
									templateLock: false
								},
							),
						),
					)
				)
			);
        },

        save: function( props ) {

			const blockProps = useBlockProps.save();

			if ( blockProps.className ) {
				var customClass = blockProps.className.replace( 'block-editor-block-list__block wp-block wp-block-newsletterglue-column', '' );
				customClass = customClass.replace( 'block-editor-block-list__block wp-block has-child-selected wp-block-newsletterglue-column', '' );
				customClass = customClass.replace( 'wp-block-newsletterglue-column', '' ).trim();
			} else {
				var customClass = '';
			}

			var boxStyles = {
				paddingTop: props.attributes.top_padding ? props.attributes.top_padding : '',
				paddingBottom: props.attributes.bottom_padding ? props.attributes.bottom_padding : '',
				paddingLeft: props.attributes.left_padding ? props.attributes.left_padding : '',
				paddingRight: props.attributes.right_padding ? props.attributes.right_padding : '',
			};

			if ( props.attributes.background ) {
				boxStyles[ 'backgroundColor' ] = props.attributes.background;
			}

            return el(
                'div',
                {
					className: 'ng__column ' + customClass,
					style: boxStyles,
					'data-size' : props.attributes.width ? props.attributes.width : 'auto',
					'data-lpadding' : props.attributes.left_padding,
					'data-rpadding' : props.attributes.right_padding,
					'data-tpadding' : props.attributes.top_padding,
					'data-bpadding' : props.attributes.bottom_padding
				},

                el(
                    InnerBlocks.Content, {},
                ),
            );
        },

    } );
}(
    window.wp
) );

// 0088A0

( function( blocks, editor, element, components ) {

	const block = newsletterglue_block_columns;
	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, useBlockProps, InnerBlocks, PanelColorSettings, withColors, BlockControls, AlignmentToolbar } = editor;
	const { Fragment } = element;
	const { TextControl, __experimentalBoxControl, __experimentalUnitControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, ServerSideRender, RangeControl, BaseControl, Button, ButtonGroup } = components;
	const { useSelect } = wp.data;

	const icon = el( 'svg', { width: 35, height: 25, viewBox: '0 0 35 26' },
		el( 'g', { transform: 'translate(-1829 -502)' },
			el( 'g', { transform: 'translate(1829 502)', fill: 'none', stroke: '#0088A0', strokeWidth: 2 },
				el( 'rect', { width: 35, height: 25, rx: 3, stroke: 'none' } ),
				el( 'rect', { x: 1, y: 1, width: 33, height: 24, rx: 2, fill: 'none' } ),
			),
			el( 'line', {
				y2: "24.855",
				transform: "translate(1846.333 502.981)",
				fill: "none",
				stroke: "#0088A0",
				strokeWidth: 2,
			} )
		)
	);

	const allowedBlocks = [ 'newsletterglue/column' ];

	registerBlockType( 'newsletterglue/columns', {
		title: 'NG: ' + block.name,
		description: block.description,
		icon: icon,
		category: 'newsletterglue-legacy',
		keywords: [ 'newsletter', 'column', 'columns' ],
		attributes: {
			bg_color: {
				'type' : 'string',
			},
			text_color: {
				'type' : 'string',
			},
			col_layout: {
				'type' : 'string',
				'default' : 'column_2',
			},
			top_padding: {
				'type' : 'string',
				'default' : '20px',
			},
			bottom_padding: {
				'type' : 'string',
				'default' : '20px',
			},
			left_padding: {
				'type' : 'string',
				'default' : '0px',
			},
			right_padding: {
				'type' : 'string',
				'default' : '0px',
			},
			top_margin: {
				'type' : 'string',
				'default' : '0px',
			},
			bottom_margin: {
				'type' : 'string',
				'default' : '0px',
			},
			left_margin: {
				'type' : 'string',
				'default' : '0px',
			},
			right_margin: {
				'type' : 'string',
				'default' : '0px',
			},
			layout: {
				'type' : 'string',
			},
			show_in_blog: {
				'type' : 'boolean',
				'default' : block.show_in_blog ? true : false
			},
			show_in_email: {
				'type' : 'boolean',
				'default' : block.show_in_email ? true : false
			},
		},

		edit: withColors( 'formColor' ) ( function( props ) {

            const { attributes, clientId } = props;
            const innerBlockCount = useSelect((select) => select('core/block-editor').getBlock(clientId).innerBlocks);

			const { bg_color, text_color, col_layout, top_padding, bottom_padding, left_padding, right_padding, top_margin, bottom_margin, left_margin, right_margin } = props.attributes;

			const px_units = [
				{ value: 'px', label: 'px', default: 0 },
			];

			const pct_units = [
				{ value: '%', label: '%', default: 0 },
			];

			var PARENT_TEMPLATE = [
				[ 'newsletterglue/column', {} ],
				[ 'newsletterglue/column', {} ],
				[ 'newsletterglue/column', {} ]
			];

			var getColorSettings = [
				{
					value: bg_color,
					label: 'Background color',
					onChange: ( value ) => props.setAttributes( { bg_color: value } ),
				},
				{
					value: text_color,
					label: 'Text color',
					onChange: ( value ) => props.setAttributes( { text_color: value } ),
				}
			];

			var boxStyles = {
				backgroundColor : bg_color ? bg_color : 'inherit',
				color: text_color ? text_color : 'inherit',
				paddingTop: top_padding ? top_padding : '',
				paddingBottom: bottom_padding ? bottom_padding : '',
				paddingLeft: left_padding ? left_padding : '',
				paddingRight: right_padding ? right_padding : '',
				marginTop: top_margin ? top_margin : '',
				marginBottom: bottom_margin ? bottom_margin : '',
				marginLeft: left_margin ? left_margin : '',
				marginRight: right_margin ? right_margin : '',
			};

			function changeLayout( ev ) {
				let new_col_layout = ev.currentTarget.value;
				props.setAttributes( { col_layout: new_col_layout } );
			}

			var colCount = col_layout.replace( 'column_', '' );

			var children = wp.data.select('core/block-editor').getBlocksByClientId(clientId)[0].innerBlocks;

			var isLayout1 = props.attributes.layout == '1' ? 'ng_columns__active' : '';
			var isLayout50_50 = props.attributes.layout == '50_50' ? 'ng_columns__active' : '';
			var isLayout70_30 = props.attributes.layout == '70_30' ? 'ng_columns__active' : '';
			var isLayout30_70 = props.attributes.layout == '30_70' ? 'ng_columns__active' : '';
			var isLayout33_33_33 = props.attributes.layout == '33_33_33' ? 'ng_columns__active' : '';
			var isLayout50_25_25 = props.attributes.layout == '50_25_25' ? 'ng_columns__active' : '';

			var layout = props.attributes.layout;

			var layoutOptions = el( 'div', { className: 'ng_columns__select_options' }, 
					el( 'div', { }, '2-column layout' ),
							el( 'span', { 'data-layout' : '50 : 50', className: 'ng_columns__50_50' + ( layout == '50_50' ? ' ng_columns__active' : '' ), onClick: function() {
								props.setAttributes( { col_layout: 'column_2', layout: '50_50' } );
								var i = 0;
								children.forEach(function(child){
									i++;
									if ( i === 1 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '50%' });
									}
									if ( i === 2 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '50%' });
									}
								} );
							} }, el( 'span', {} ), el( 'span', {} ) ),

							el( 'span', { 'data-layout' : '60 : 40', className: 'ng_columns__60_40' + ( layout == '60_40' ? ' ng_columns__active' : '' ), onClick: function() {
								props.setAttributes( { col_layout: 'column_2', layout: '60_40' } );
								var i = 0;
								children.forEach(function(child){
									i++;
									if ( i === 1 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '60%' });
									}
									if ( i === 2 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '40%' });
									}
								} );
							} }, el( 'span', {} ), el( 'span', {} ) ),

							el( 'span', { 'data-layout' : '70 : 30', className: 'ng_columns__70_30' + ( layout == '70_30' ? ' ng_columns__active' : '' ), onClick: function() {
								props.setAttributes( { col_layout: 'column_2', layout: '70_30' } );
								var i = 0;
								children.forEach(function(child){
									i++;
									if ( i === 1 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '70%' });
									}
									if ( i === 2 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '30%' });
									}
								} );
							} }, el( 'span', {} ), el( 'span', {} ) ),

							el( 'span', { 'data-layout' : '80 : 20', className: 'ng_columns__80_20' + ( layout == '80_20' ? ' ng_columns__active' : '' ), onClick: function() {
								props.setAttributes( { col_layout: 'column_2', layout: '80_20' } );
								var i = 0;
								children.forEach(function(child){
									i++;
									if ( i === 1 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '80%' });
									}
									if ( i === 2 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '20%' });
									}
								} );
							} }, el( 'span', {} ), el( 'span', {} ) ),

							el( 'span', { 'data-layout' : '90 : 10', className: 'ng_columns__90_10' + ( layout == '90_10' ? ' ng_columns__active' : '' ), onClick: function() {
								props.setAttributes( { col_layout: 'column_2', layout: '90_10' } );
								var i = 0;
								children.forEach(function(child){
									i++;
									if ( i === 1 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '90%' });
									}
									if ( i === 2 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '10%' });
									}
								} );
							} }, el( 'span', {} ), el( 'span', {} ) ),

							el( 'span', { 'data-layout': '40 : 60', className: 'ng_columns__40_60' + ( layout == '40_60' ? ' ng_columns__active' : '' ), onClick: function() {
								props.setAttributes( { col_layout: 'column_2', layout: '40_60' } );
								var i = 0;
								children.forEach(function(child){
									i++;
									if ( i === 1 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '40%' });
									}
									if ( i === 2 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '60%' });
									}
								} );
							} }, el( 'span', {} ), el( 'span', {} ) ),

							el( 'span', { 'data-layout': '30 : 70', className: 'ng_columns__30_70' + ( layout == '30_70' ? ' ng_columns__active' : '' ), onClick: function() {
								props.setAttributes( { col_layout: 'column_2', layout: '30_70' } );
								var i = 0;
								children.forEach(function(child){
									i++;
									if ( i === 1 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '30%' });
									}
									if ( i === 2 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '70%' });
									}
								} );
							} }, el( 'span', {} ), el( 'span', {} ) ),

							el( 'span', { 'data-layout' : '20 : 80', className: 'ng_columns__20_80' + ( layout == '20_80' ? ' ng_columns__active' : '' ), onClick: function() {
								props.setAttributes( { col_layout: 'column_2', layout: '20_80' } );
								var i = 0;
								children.forEach(function(child){
									i++;
									if ( i === 1 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '20%' });
									}
									if ( i === 2 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '80%' });
									}
								} );
							} }, el( 'span', {} ), el( 'span', {} ) ),

							el( 'span', { 'data-layout' : '10 : 90', className: 'ng_columns__10_90' + ( layout == '10_90' ? ' ng_columns__active' : '' ), onClick: function() {
								props.setAttributes( { col_layout: 'column_2', layout: '10_90' } );
								var i = 0;
								children.forEach(function(child){
									i++;
									if ( i === 1 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '10%' });
									}
									if ( i === 2 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '90%' });
									}
								} );
							} }, el( 'span', {} ), el( 'span', {} ) ),

							el( 'div', { }, '3-column layout' ),

							el( 'span', { 'data-layout' : '33 : 33 : 33', className: 'ng_columns__33_33_33' + ( layout == '33_33_33' ? ' ng_columns__active' : '' ), onClick: function() {
								props.setAttributes( { col_layout: 'column_3', layout: '33_33_33' } );
								var i = 0;
								children.forEach(function(child){
									i++;
									if ( i === 1 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '33.33%' });
									}
									if ( i === 2 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '33.33%' });
									}
									if ( i === 3 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '33.33%' });
									}
								} );
							} }, el( 'span', {} ), el( 'span', {} ), el( 'span', {} ) ),

							el( 'span', { 'data-layout': '50 : 25 : 25', className: 'ng_columns__50_25_25' + ( layout == '50_25_25' ? ' ng_columns__active' : '' ), onClick: function() {
								props.setAttributes( { col_layout: 'column_3', layout: '50_25_25' } );
								var i = 0;
								children.forEach(function(child){
									i++;
									if ( i === 1 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '50%' });
									}
									if ( i === 2 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '25%' });
									}
									if ( i === 3 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '25%' });
									}
								} );
							} }, el( 'span', {} ), el( 'span', {} ), el( 'span', {} ) ),

							el( 'span', { 'data-layout': '25 : 25 : 50', className: 'ng_columns__25_25_50' + ( layout == '25_25_50' ? ' ng_columns__active' : '' ), onClick: function() {
								props.setAttributes( { col_layout: 'column_3', layout: '25_25_50' } );
								var i = 0;
								children.forEach(function(child){
									i++;
									if ( i === 1 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '25%' });
									}
									if ( i === 2 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '25%' });
									}
									if ( i === 3 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '50%' });
									}
								} );
							} }, el( 'span', {} ), el( 'span', {} ), el( 'span', {} ) ),

							el( 'span', { 'data-layout': '25 : 50 : 25', className: 'ng_columns__25_50_25' + ( layout == '25_50_25' ? ' ng_columns__active' : '' ), onClick: function() {
								props.setAttributes( { col_layout: 'column_3', layout: '25_50_25' } );
								var i = 0;
								children.forEach(function(child){
									i++;
									if ( i === 1 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '25%' });
									}
									if ( i === 2 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '50%' });
									}
									if ( i === 3 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '25%' });
									}
								} );
							} }, el( 'span', {} ), el( 'span', {} ), el( 'span', {} ) ),

							el( 'span', { 'data-layout': '60 : 20 : 20', className: 'ng_columns__60_20_20' + ( layout == '60_20_20' ? ' ng_columns__active' : '' ), onClick: function() {
								props.setAttributes( { col_layout: 'column_3', layout: '60_20_20' } );
								var i = 0;
								children.forEach(function(child){
									i++;
									if ( i === 1 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '60%' });
									}
									if ( i === 2 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '20%' });
									}
									if ( i === 3 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '20%' });
									}
								} );
							} }, el( 'span', {} ), el( 'span', {} ), el( 'span', {} ) ),

							el( 'span', { 'data-layout': '20 : 20 : 60', className: 'ng_columns__20_20_60' + ( layout == '20_20_60' ? ' ng_columns__active' : '' ), onClick: function() {
								props.setAttributes( { col_layout: 'column_3', layout: '20_20_60' } );
								var i = 0;
								children.forEach(function(child){
									i++;
									if ( i === 1 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '20%' });
									}
									if ( i === 2 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '20%' });
									}
									if ( i === 3 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '60%' });
									}
								} );
							} }, el( 'span', {} ), el( 'span', {} ), el( 'span', {} ) ),

							el( 'span', { 'data-layout': '20 : 60 : 20', className: 'ng_columns__20_60_20' + ( layout == '20_60_20' ? ' ng_columns__active' : '' ), onClick: function() {
								props.setAttributes( { col_layout: 'column_3', layout: '20_60_20' } );
								var i = 0;
								children.forEach(function(child){
									i++;
									if ( i === 1 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '20%' });
									}
									if ( i === 2 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '60%' });
									}
									if ( i === 3 ) {
										wp.data.dispatch('core/block-editor').updateBlockAttributes(child.clientId, { width: '20%' });
									}
								} );
							} }, el( 'span', {} ), el( 'span', {} ), el( 'span', {} ) )
			);

			var showControls = el( Fragment, {},

				el( PanelBody, { title: 'Layout', initialOpen: true, className: 'ngl-inspector' },
					el( 'div', { className: 'ng_columns__container ng_columns__select' },
						layoutOptions
					)
				),

				el( PanelBody, { title: 'Spacing', initialOpen: true, className: 'ngl-inspector' },
					el( BaseControl, {},
						el( __experimentalBoxControl, {
							label: 'Padding',
							values: {
								top: props.attributes.top_padding,
								bottom: props.attributes.bottom_padding,
								left: props.attributes.left_padding,
								right: props.attributes.right_padding,
							},
							resetValues: { top: '20px', bottom: '20px', left: '0px', right: '0px' },
							allowReset: true,
							splitOnAxis: false,
							sides: [ 'top', 'bottom', 'left', 'right' ],
							units: px_units,
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
								left: props.attributes.left_margin,
								right: props.attributes.right_margin,
							},
							resetValues: { top: '0px', bottom: '0px', left: '0px', right: '0px' },
							allowReset: true,
							splitOnAxis: false,
							sides: [ 'top', 'bottom', 'left', 'right' ],
							units: px_units,
							onChange: ( values ) => {
								props.setAttributes( { top_margin: values.top, bottom_margin: values.bottom, left_margin: values.left, right_margin: values.right } );
							},
						} )
					),
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
									checked: props.attributes.show_in_blog
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

			);

			if ( ! props.attributes.layout ) {
				showControls = '';
			}

			var showTemplate = el(
						'section',
						{
							 className: 'ng_columns__container ng_columns__container__' + colCount,
							 style: boxStyles,
							 'data-bg-color' : bg_color,
							 'data-text-color' : text_color,
							 'data-columns' : colCount,
							 'data-top-padding' : top_padding,
							 'data-bottom-padding' : bottom_padding,
							 'data-left-padding' : left_padding,
							 'data-right-padding' : right_padding,
							 'data-top-margin' : top_margin,
							 'data-bottom-margin' : bottom_margin,
							 'data-left-margin': left_margin,
							 'data-right-margin' : right_margin,
						},
						el( InnerBlocks, {
								allowedBlocks: allowedBlocks,
								template: PARENT_TEMPLATE,
								templateLock: 'all',
								orientation: 'horizontal'
							}
						)

					);

			if ( ! props.attributes.layout ) {
				showTemplate = el(
						'section',
						{
							 className: 'ng_columns__container ng_columns__select ng_columns__container__' + colCount,
							 style: boxStyles,
							 'data-bg-color' : bg_color,
							 'data-text-color' : text_color,
							 'data-columns' : colCount,
							 'data-top-padding' : top_padding,
							 'data-bottom-padding' : bottom_padding,
							 'data-left-padding' : left_padding,
							 'data-right-padding' : right_padding,
							 'data-top-margin' : top_margin,
							 'data-bottom-margin' : bottom_margin,
							 'data-left-margin': left_margin,
							 'data-right-margin' : right_margin,
						},
						el( 'div', { className: 'ng_columns__select_heading' },
							el( 'div', { },
								el( 'span', { },
									icon
								),
								el( 'span', { },
									'NG: Columns'
								),
							),
							el( 'span', {}, 'Select a layout to begin. You can always change this later.' ),
						),
						layoutOptions,
						el( InnerBlocks, {
								allowedBlocks: allowedBlocks,
								template: PARENT_TEMPLATE,
								templateLock: 'all',
								orientation: 'horizontal',
							}
						)
					);
			}

            return (
				el( Fragment, {},
					el( InspectorControls, { },
						showControls
					),
					showTemplate
				)
			);

        } ),

        save: ( function( props ) {

			const { bg_color, text_color, col_layout, top_padding, bottom_padding, left_padding, right_padding, top_margin, bottom_margin, left_margin, right_margin } = props.attributes;
	
			var boxStyles = {
				backgroundColor : bg_color ? bg_color : 'inherit',
				color: text_color ? text_color : 'inherit',
				paddingTop: top_padding ? top_padding : '',
				paddingBottom: bottom_padding ? bottom_padding : '',
				paddingLeft: left_padding ? left_padding : '',
				paddingRight: right_padding ? right_padding : '',
				marginTop: top_margin ? top_margin : '',
				marginBottom: bottom_margin ? bottom_margin : '',
				marginLeft: left_margin ? left_margin : '',
				marginRight: right_margin ? right_margin : '',
			};

			var colCount = col_layout.replace( 'column_', '' );

            return el(
                'section',
                	{
							 className: 'ng_columns__container ng_columns__container__' + colCount,
							 style: boxStyles,
							 'data-bg-color' : bg_color,
							 'data-text-color' : text_color,
							 'data-columns' : colCount,
							 'data-top-padding' : top_padding,
							 'data-bottom-padding' : bottom_padding,
							 'data-left-padding' : left_padding,
							 'data-right-padding' : right_padding,
							 'data-top-margin' : top_margin,
							 'data-bottom-margin' : bottom_margin,
							 'data-left-margin': left_margin,
							 'data-right-margin' : right_margin,
					},
                 el(
                      InnerBlocks.Content, {},
                  )
            );
        } ),

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