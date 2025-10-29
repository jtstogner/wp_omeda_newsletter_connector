( function( blocks, editor, element, components ) {

	const apps = newsletterglue_block_show_hide_content.apps;
	const showconditions = newsletterglue_block_show_hide_content.showconditions;
	const operators = JSON.parse( newsletterglue_block_show_hide_content.operators );
	const operatorsForTag = operators.filter(operator => operator.for.split(',').includes('tag'));
	const operatorsForField = operators.filter(operator => operator.for.split(',').includes('field'));
	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks } = editor;
	const { Fragment, useState } = element;
	const { TextControl, ToggleControl, Panel, PanelBody, PanelRow, BaseControl, SelectControl, ButtonGroup, Button, __experimentalRadioGroup, __experimentalRadio, __experimentalBoxControl, Card, CardHeader, CardBody } = components;

	const icon = el( 'svg', { width: 24, height: 24, viewBox: '0 0 45 36' },
		el( 'path',
			{
				fill: '#0088A0',
				d: "M22.5,28.125a10.087,10.087,0,0,1-10.048-9.359l-7.376-5.7a23.435,23.435,0,0,0-2.582,3.909,2.275,2.275,0,0,0,0,2.052A22.552,22.552,0,0,0,22.5,31.5a21.84,21.84,0,0,0,5.477-.735l-3.649-2.823a10.134,10.134,0,0,1-1.828.184ZM44.565,32.21,36.792,26.2a23.291,23.291,0,0,0,5.713-7.177,2.275,2.275,0,0,0,0-2.052A22.552,22.552,0,0,0,22.5,4.5,21.667,21.667,0,0,0,12.142,7.151L3.2.237a1.125,1.125,0,0,0-1.579.2L.237,2.211a1.125,1.125,0,0,0,.2,1.579L41.8,35.763a1.125,1.125,0,0,0,1.579-.2l1.381-1.777a1.125,1.125,0,0,0-.2-1.579ZM31.648,22.226,28.884,20.09a6.663,6.663,0,0,0-8.164-8.573,3.35,3.35,0,0,1,.655,1.984,3.279,3.279,0,0,1-.108.7l-5.176-4A10.006,10.006,0,0,1,22.5,7.875,10.119,10.119,0,0,1,32.625,18a9.885,9.885,0,0,1-.977,4.226Z"
			}
		)
	);

	let attributes = {
		showblog: {
			'type': 'boolean',
			'default': newsletterglue_block_show_hide_content.showblog ? true : false,
		},
		showemail: {
			'type': 'boolean',
			'default': newsletterglue_block_show_hide_content.showemail ? true : false,
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
			'default' : '10px',
		},
		bottom_padding: {
			'type' : 'string',
			'default' : '10px',
		},
		top_margin: {
			'type' : 'string',
			'default' : '0px',
		},
		bottom_margin: {
			'type' : 'string',
			'default' : '0px',
		},
	}

	apps.forEach( ( esp ) => {
		attributes[ `${ esp }_conditions` ] = {
			type: 'array',
			default: []
		}
	} );

	registerBlockType( 'newsletterglue/group', {
		title: 'NG: Show/hide content',
		description: 'Hide selected content from your blog/newsletter.',
		icon: icon,
		category: 'newsletterglue-legacy',
		keywords: [ 'newsletter', 'glue', 'group', 'container' ],
		attributes,
        edit: function( props ) {

			const [ loading, setLoading ] = useState(false);
			const [ tags, setTags ] = useState([]);
			const [ fields, setFields ] = useState([]);

			if( tags.length === 0 && newsletterglue_meta?.custom_tags?.length ) {
				setTags(newsletterglue_meta.custom_tags);
			}

			if( fields.length === 0 && newsletterglue_meta?.custom_fields?.length ) {
				setFields(newsletterglue_meta.custom_fields);
			}

			let app = newsletterglue_meta?.app;
			let conditionalContent = () => {};
			
			const setCondition = (position, condition, isDelete = false) => {
				const conditions = [ ...props.attributes[ `${ app }_conditions` ] ];

				if( isDelete ) {
					conditions.splice( position, 1 );
				} else {
					conditions[ position ] = condition;
				}
				props.setAttributes({
					[ `${ app }_conditions` ]: conditions
				});
			}

			if( props.attributes.showemail && showconditions ) {

				conditionalContent = () => {
					return el( BaseControl,
						{
							label: 'Conditional content',
							className: `ngl-conditional-content ${ loading ? 'is-loading' : '' }`,
							help: 'Note: Preview in browser will always display this content. Send a test campaign to test conditional content.'
						},
						el( BaseControl,
							{
								label: 'Show email content based on these conditions:'
							},
							props.attributes[ `${ app }_conditions` ].map(( condition, i ) => {
								
								let setConditionTitle = 'Set condition';

								if( condition.key == 'tag' ) {
									if( condition.value ) {
										setConditionTitle = 'Tag: ' + condition.value;
									}
								} else if( condition.key == 'manual' ) {
									if( condition.key_manual ) {
										setConditionTitle = condition.key_manual;
									}
								} else if( condition.key ) {
									let title = condition.key;
									if( app == 'aweber' ) {
										if( title.includes( 'subscriber.' ) ) {
											title = title.replace('subscriber.', '')
										}

										if( title.includes( 'custom_field' ) ) {
											title = title.replace('custom_field["', '').replace('"]', '');
										}
									}
									setConditionTitle = title;
								}

								return el( Card,
									{
										className: `ngl-condition-${i}`
									},
									el( CardHeader,
										{
											onClick: (e) => {
												const element = e.target;
												
												if( !element.classList.contains( 'dashicon' ) ) {
													element.closest('.components-card').classList.toggle('is-active');
												}
											}
										},
										el( 'span', {}, setConditionTitle ),
										el( Button, {
											isSmall: true,
											className: 'btn-remove',
											icon: 'no',
											onClick: ( e ) => {
												setCondition( i, {}, true);
											}
										}),
									),
									el( CardBody, {},

										// show only for all
										el( SelectControl, {
											value: condition.key,
											onChange: ( value ) => {
												setCondition( i, {
													key: value,
													key_manual: '',
													operator: '',
													value: '',
													relationship: condition.relationship
												});
											},
											options: fields,
										} ),
										

										// show only for manual input
										condition.key == 'manual' &&
										el( TextControl, {
											placeholder: "Enter your field key",
											value: condition.key_manual,
											onChange: ( value ) => {
												setCondition( i, {
													key: condition.key,
													key_manual: value,
													operator: '',
													value: '',
													relationship: condition.relationship
												});
											},
										} ),

										// show for all
										condition.key &&
										( ( condition.key == 'manual' && condition.key_manual ) || ( condition.key != 'manual' ) ) &&
										el( SelectControl, {
											value: condition.operator,
											onChange: ( value ) => {
												setCondition( i, {
													key: condition.key,
													key_manual: condition.key_manual,
													operator: value,
													value: '',
													relationship: condition.relationship
												});
											},
											options: condition.key == 'tag' ? operatorsForTag : condition.key == 'manual' ? operators : operatorsForField,
										} ),

										// show only for manual input & custom field (exclude ex, nex, and, or)
										condition.key &&
										condition.key != 'tag' &&
										condition.operator &&
										condition.operator != 'and' &&
										condition.operator != 'or' &&
										( ( condition.key == 'manual' ) || ( condition.operator != 'ex' && condition.operator != 'nex' ) ) &&
										el( TextControl, {
											placeholder: "Enter your value",
											help: condition.key == 'manual' ? "For multiple values use exactly the same pattern. List: value1, value2, value3" : "",
											value: condition.value,
											onChange: ( value ) => {
												setCondition( i, {
													key: condition.key,
													key_manual: condition.key_manual,
													operator: condition.operator,
													value,
													relationship: condition.relationship
												});
											},
										} ),

										// show only for manual input and custom field with operator and, or
										condition.key && 
										condition.key != 'tag' &&
										condition.operator && 
										( condition.operator == 'and' || condition.operator == 'or' ) && 
										el( SelectControl, {
											value: condition.value,
											onChange: ( value ) => {
												setCondition( i, {
													key: condition.key,
													key_manual: condition.key_manual,
													operator: condition.operator,
													value,
													relationship: condition.relationship
												});
											},
											options: fields,
										} ),

										// show only for tag
										condition.key && 
										condition.operator && 
										condition.key == 'tag' && 
										el( SelectControl, {
											multiple: true,
											value: condition.value,
											style: { height: 'auto' },
											options: tags,
											onChange: ( value ) => {
												setCondition( i, {
													key: condition.key,
													key_manual: condition.key_manual,
													operator: condition.operator,
													value,
													relationship: condition.relationship
												});
											}
										} ),
										
										el( BaseControl, {},
											el( __experimentalRadioGroup, {
												checked: condition.relationship,
												onChange: ( value ) => {
													setCondition( i, {
														key: condition.key,
														key_manual: condition.key_manual,
														operator: condition.operator,
														value: condition.value,
														relationship: value
													});
												}
											},
												el( __experimentalRadio, { value: 'AND' } ),
												![ 'campaignmonitor', 'mailchimp', 'moosend', 'sendgrid' ].includes( app ) &&
												el( __experimentalRadio, { value: 'OR' } ),
											)
										)
									)
								)
							})
						),
						el( BaseControl, {},
							el( ButtonGroup, { className: 'ngl-gutenberg--fullwidth' },
								el( Button, {
									text: 'Add new condition',
									isSecondary: true,
									className: 'btn-add-condition',
									onClick: () => {
										const conditions = props.attributes[ `${ app }_conditions` ];
										const tempCond = {
											key: '',
											key_manual: '',
											operator: '',
											value: '',
											relationship: 'AND'
										};
										props.setAttributes({
											[ `${ app }_conditions` ]: [ ...conditions, tempCond ]
										});
									}
								}),
								el( Button, {
									text: 'Refresh',
									isSecondary: true,
									showTooltip: true,
									label: 'Fetch tags/custom fields from API',
									className: 'btn-refresh',
									onClick: async () => {
										setLoading(true);
										await fetch( newsletterglue_params.ajaxurl, {
											method: 'POST',
											body: new URLSearchParams({
												action: 'newsletterglue_block_show_hide_refresh',
												security: newsletterglue_params.ajaxnonce,
											}),										
										})
										.then( response => response.json() )
										.then( response => {
											setLoading(false);
											if( response.success ) {
												if( response.data?.custom_tag_list?.length ) {
													setTags(response.data.custom_tag_list);
												}
												if( response.data?.custom_field_list?.length ) {
													setFields(response.data.custom_field_list);
												}
											} else {
												alert( response.message );
											}
										});
									}
								})
							),
						)
					)
				}
			}

			var top_padding = props.attributes.top_padding ? props.attributes.top_padding : 0;
			var bottom_padding = props.attributes.bottom_padding ? props.attributes.bottom_padding : 0;
			var left_padding = props.attributes.left_padding ? props.attributes.left_padding : 0;
			var right_padding = props.attributes.right_padding ? props.attributes.right_padding : 0;
			var top_margin = props.attributes.top_margin ? props.attributes.top_margin : 0;
			var bottom_margin = props.attributes.bottom_margin ? props.attributes.bottom_margin : 0;

			var metaStyles = {
				paddingTop: props.attributes.top_padding ? props.attributes.top_padding : 0,
				paddingBottom: props.attributes.bottom_padding ? props.attributes.bottom_padding : 0,
				paddingLeft: props.attributes.left_padding ? props.attributes.left_padding : 0,
				paddingRight: props.attributes.right_padding ? props.attributes.right_padding : 0,
				marginTop: props.attributes.top_margin ? props.attributes.top_margin : 0,
				marginBottom: props.attributes.bottom_margin ? props.attributes.bottom_margin : 0,
			};


			var showWarning = false;
			if( showconditions ) {
				showWarning = true;
				if( props.attributes.showblog && props.attributes.showemail ) {
					if( props.attributes[ `${ app }_conditions` ]?.length === 0 ) {
						showWarning = false;
					}
				}
			}

			return (
				el( Fragment, {},
					el( InspectorControls, {},
						el( PanelBody, { title: 'Show/hide - newsletter block', initialOpen: true },

							el( BaseControl, {},
								el( ToggleControl,
									{
										label: 'Show in blog post',
										onChange: ( value ) => {
											props.setAttributes( { showblog: value } );
										},
										checked: props.attributes.showblog,
									}
								)
							),
							el( BaseControl, {},
								el( ToggleControl,
									{
										label: 'Show in email newsletter',
										onChange: ( value ) => {
											props.setAttributes( { showemail: value } );
										},
										checked: props.attributes.showemail,
									}
								)
							),
							conditionalContent()
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
					el( 'section', { className: props.className, style: metaStyles, 'data-padding': top_padding + ',' + bottom_padding + ',' + left_padding + ',' + right_padding, 'data-margin' : top_margin + ',' + bottom_margin, 'data-warning': showWarning },
						el( InnerBlocks )
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

			var metaStyles = {
				paddingTop: props.attributes.top_padding ? props.attributes.top_padding : 0,
				paddingBottom: props.attributes.bottom_padding ? props.attributes.bottom_padding : 0,
				paddingLeft: props.attributes.left_padding ? props.attributes.left_padding : 0,
				paddingRight: props.attributes.right_padding ? props.attributes.right_padding : 0,
				marginTop: props.attributes.top_margin ? props.attributes.top_margin : 0,
				marginBottom: props.attributes.bottom_margin ? props.attributes.bottom_margin : 0,
			};

            return (
                el( 'section',
					{
						className: props.className,
						style: metaStyles,
						'data-padding': top_padding + ',' + bottom_padding + ',' + left_padding + ',' + right_padding,
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