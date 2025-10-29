( function( blocks, editor, element, components ) {

	const block = newsletterglue_block_metadata;
	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors, MediaUpload, PlainText, BlockControls, AlignmentToolbar } = editor;
	const { Fragment } = element;
	const { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, RangeControl, BaseControl, ButtonGroup, Button, ColorPicker, __experimentalBoxControl } = components;

	const icon = el( 'svg', { width: 24, height: 24, viewBox: '0 0 31.5 31.5' },
		el( 'g', { transform: 'translate(-115 -126)' },
			el( 'path',
				{
					fill: '#0088A0',
					transform: 'translate(115 123.75)',
					d: "M30.984,12.8l.5-2.812A.844.844,0,0,0,30.656,9H25.4l1.028-5.758a.844.844,0,0,0-.831-.992H22.737a.844.844,0,0,0-.831.7L20.825,9H13.89l1.028-5.758a.844.844,0,0,0-.831-.992H11.23a.844.844,0,0,0-.831.7L9.318,9H3.757a.844.844,0,0,0-.831.7l-.5,2.813a.844.844,0,0,0,.831.992h5.26l-1.607,9H1.346a.844.844,0,0,0-.831.7l-.5,2.813A.844.844,0,0,0,.844,27H6.1L5.076,32.758a.844.844,0,0,0,.831.992H8.763a.844.844,0,0,0,.831-.7L10.675,27H17.61l-1.028,5.758a.844.844,0,0,0,.831.992H20.27a.844.844,0,0,0,.831-.7L22.182,27h5.561a.844.844,0,0,0,.831-.7l.5-2.813a.844.844,0,0,0-.831-.992h-5.26l1.607-9h5.561a.844.844,0,0,0,.831-.7Zm-12.57,9.7H11.479l1.607-9h6.935Z"
				}
			)
		)
	);

	function __get_year( d ) {
		return d.toLocaleDateString( newsletterglue_meta.locale, { year: 'numeric' } );
	}

	function __get_month_num( d ) {
		return d.toLocaleDateString( newsletterglue_meta.locale, { month: '2-digit' } );
	}

	function __get_month_short( d ) {
		return d.toLocaleDateString( newsletterglue_meta.locale, { month: 'short' } );
	}

	function __get_month_long( d ) {
		return d.toLocaleDateString( newsletterglue_meta.locale, { month: 'long' } );
	}

	function __get_fullday( d ) {
		return d.toLocaleDateString( newsletterglue_meta.locale, { weekday: 'long' } );
	}

	function __get_day( d, zero = false ) {
		if ( zero ) {
			return d.toLocaleDateString( newsletterglue_meta.locale, { day: '2-digit' } );
		} else {
			return d.toLocaleDateString( newsletterglue_meta.locale, { day: 'numeric' } );
		}
	}

	registerBlockType( 'newsletterglue/metadata', {
		title: 'NG: Newsletter meta data',
		description: 'Add standard meta data to each post.',
		icon: icon,
		category: 'newsletterglue-legacy',
		attributes: {
			issue_title: {
				'type': 'string',
				'default' : block.issue_title,
			},
			post_link: {
				'type': 'string',
				'default' : block.read_online,
			},
			post_location: {
				'type': 'string',
			},
			readtime: {
				'type': 'string',
				'default' : block.readtime
			},
			show_in_blog: {
				'type' : 'boolean',
				'default' : block.show_in_blog ? true : false
			},
			show_in_email: {
				'type' : 'boolean',
				'default' : block.show_in_email ? true : false
			},
			text_color: {
				'type' : 'string',
				'default' : '#666666',
			},
			divider_style: {
				'type' : 'string',
				'default' : 'line',
			},
			alignment: {
				'type' : 'string',
				'default' : 'center',
			},
			date_format: {
				'type' : 'string',
			},
			author_name: {
				'type' : 'string',
			},
    		profile_pic: {
    			type: 'string',
    		},
			show_author: {
				'type' : 'boolean',
				'default' : true,
			},
			show_issue: {
				'type' : 'boolean',
				'default' : true,
			},
			show_date: {
				'type' : 'boolean',
				'default' : true,
			},
			show_location: {
				'type' : 'boolean',
				'default' : true,
			},
			show_readtime: {
				'type' : 'boolean',
				'default' : true,
			},
			show_readonline: {
				'type' : 'boolean',
				'default' : true,
			},
			post_id: {
				'type' : 'number',
			},
			readingtime: {
				'type' : 'string',
			},
			read_online_link: {
				'type' : 'string',
				'default' : 'blog',
			},
			post_date: {
				'type' : 'string',
			},
			font_size: {
				'type' : 'number',
				'default' : 12,
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
		},
		edit: withColors( 'formColor' ) ( function( props ) {

			var postdata = wp.data.select("core/editor").getCurrentPost();
			var d = new Date( postdata.date );
			var dateFormats = [
				{ value: 'l, j M Y', label: __get_fullday( d ) + ', ' + __get_day( d ) + ' ' + __get_month_short( d ) + ' ' + __get_year( d ) },
				{ value: 'F j, Y', label: __get_month_long( d ) + ' ' + __get_day( d ) + ', ' + __get_year( d ) },
				{ value: 'j M Y', label: __get_day( d ) + ' ' + __get_month_short( d ) + ' ' + __get_year( d ) },
				{ value: 'Y-m-d', label: __get_year( d ) + '-' + __get_month_num( d ) + '-' + __get_day( d, 'zero' ) },
				{ value: 'm/d/Y', label: __get_month_num( d ) + '/' + __get_day( d, 'zero' ) + '/' + __get_year( d ) },
				{ value: 'd/m/Y', label: __get_day( d, 'zero' ) + '/' + __get_month_num( d ) + '/' + __get_year( d ) }
			];

			for( var mykey in dateFormats) {
				if ( dateFormats[ mykey ][ 'value' ] == props.attributes.date_format ) {
					props.setAttributes( { post_date: dateFormats[ mykey ][ 'label' ] } );
				}
			}

			var changeDate = function( val ) {
				var postdated = '';
				for( var mykey in dateFormats) {
					if ( dateFormats[ mykey ][ 'value' ] == val ) {
						props.setAttributes( { post_date: dateFormats[ mykey ][ 'label' ] } );
					}
				}
			};

			var onSelectImage = function( media ) {
				return props.setAttributes( {
					profile_pic: media.url
				} );
			};

			var removeImage = function() {
				props.setAttributes( {
					profile_pic: ''
				} );
			};

			var userImage = props.attributes.profile_pic ? props.attributes.profile_pic : newsletterglue_meta.profile_pic;

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
				color: props.attributes.text_color,
				textAlign: props.attributes.alignment,
				fontSize: props.attributes.font_size,
			};

			var dividerStyles = [
				{ value: 'line', label: 'Line' },
				{ value: 'dot', label: 'Dot' }
			];

			var divider = props.attributes.divider_style == 'dot' ? '•' : '|';

			var metaPic = '';
			if ( props.attributes.show_author ) {
				metaPic = [
					el( 'div', { className: 'ngl-metadata-pic' },
						el( 'img', { src: userImage, className: 'avatar avatar-32 photo' },

						)
					),
					el( RichText, {
						tagName: 'div',
						format: 'string',
						className: 'ngl-metadata-author',
						onChange: ( value ) => { props.setAttributes( { author_name: value } ); },
						value: props.attributes.author_name,
						placeholder: newsletterglue_meta.author_name,
					} ),
					el( 'div', { className: 'ngl-metadata-sep' }, divider )
				];
			}

			var metaLocation = '';
			if ( props.attributes.show_location ) {
				metaLocation = [
					el( 'img', {
						className: 'ngl-metadata-map-pin',
						src: block.assets_uri + 'map-pin.png'
					} ),
					el( RichText, {
						tagName: 'div',
						format: 'string',
						className: 'ngl-metadata-map',
						onChange: ( value ) => { props.setAttributes( { post_location: value } ); },
						value: props.attributes.post_location,
						placeholder: 'Location',
					} ),
					el( 'div', { className: 'ngl-metadata-sep' }, divider )
				];
			}

			var metaDate = '';
			if ( props.attributes.show_date ) {
				var postDate = props.attributes.post_date ? props.attributes.post_date : newsletterglue_meta.post_date;
				metaDate = [
					el( 'div', { className: 'ngl-metadata-date', 'data-date-format': props.attributes.date_format },
						postDate
					),
					el( 'div', { className: 'ngl-metadata-sep' }, divider )
				];
			}

			var metaIssue = '';
			if ( props.attributes.show_issue ) {
				metaIssue = [
					el( RichText, {
						tagName: 'div',
						format: 'string',
						className: 'ngl-metadata-issue',
						onChange: ( value ) => { props.setAttributes( { issue_title: value } ); },
						value: props.attributes.issue_title,
						placeholder: 'Issue #',
					} ),
					el( 'div', { className: 'ngl-metadata-sep' }, divider )
				];
			}

			var metaPermalink = '';
			if ( props.attributes.show_readonline ) {
				metaPermalink = [
					el( RichText, {
							tagName: 'div',
							format: 'string',
							className: 'ngl-metadata-permalink',
							onChange: ( value ) => { props.setAttributes( { post_link: value } ); },
							value: props.attributes.post_link,
							placeholder: 'Read online',
					} ),
					el( 'img', {
						className: 'ngl-metadata-permalink-arrow',
						src: block.assets_uri + 'arrow.png'
					} )
				];
			}

			var metaReadtime = '';
			if ( props.attributes.show_readtime ) {
				metaReadtime = [
					el( RichText, {
						tagName: 'div',
						format: 'string',
						className: 'ngl-metadata-readtime',
						onChange: ( value ) => { props.setAttributes( { readtime: value } ); },
						value: props.attributes.readtime,
						placeholder: 'Reading time:',
					} ),
					el( 'div', { className: 'ngl-metadata-readtime-ajax' },
						newsletterglue_meta.readtime
					),
					el( 'div', { className: 'ngl-metadata-sep' }, divider )
				];
			}

			function onChangeAlignment( newAlignment ) {
				props.setAttributes( { alignment: newAlignment } );
			}

			function changeReadTarget( ev ) {
				let read_online_link = ev.currentTarget.value;
				props.setAttributes( { read_online_link } );
			}

			return (

				el( Fragment, { },

					// This is block settings in sidebar.
					el( InspectorControls, {},

						el( PanelBody, { title: 'Show/hide elements', initialOpen: true },
							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Author details',
									onChange: ( value ) => { props.setAttributes( { show_author: value } ); },
									checked: props.attributes.show_author,
								} )
							),
							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Issue number',
									onChange: ( value ) => { props.setAttributes( { show_issue: value } ); },
									checked: props.attributes.show_issue,
								} )
							),
							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Date',
									onChange: ( value ) => { props.setAttributes( { show_date: value } ); },
									checked: props.attributes.show_date,
								} )
							),
							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Location',
									onChange: ( value ) => { props.setAttributes( { show_location: value } ); },
									checked: props.attributes.show_location,
								} )
							),
							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Reading time',
									onChange: ( value ) => { props.setAttributes( { show_readtime: value } ); },
									checked: props.attributes.show_readtime,
								} )
							),
							el( BaseControl, { className: 'ngl-gutenberg-help' },
								el( ToggleControl, {
									label: 'Read online',
									onChange: ( value ) => { props.setAttributes( { show_readonline: value } ); },
									checked: props.attributes.show_readonline,
									help: '"Read online" only appears in your newsletter. It is always hidden in your blog.',
								} )
							),
						),

						el( PanelBody, { title: 'General options', initialOpen: true },

							el( BaseControl, {},
								el( RangeControl, {
									label: 'Font size',
									value: props.attributes.font_size,
									initialPosition: 12,
									min: 10,
									max: 50,
									allowReset: true,
									resetFallbackValue: 12,
									onChange: ( value ) => { props.setAttributes( { font_size: value } ); },
								} ),
							),

							el( BaseControl, {},
								el( MediaUpload, {
									onSelect: onSelectImage,
									type: 'image',
									render: function( obj ) {
										return [

											el( 'a', {
													href: '#',
													className: 'ngl-gutenberg-btn',
													onClick: obj.open
												},
												el( 'svg', { className: '', width: '20', height: '20', viewBox: '0 0 24 24' },
													el( 'path', { d: "M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM5 4.5h14c.3 0 .5.2.5.5v8.4l-3-2.9c-.3-.3-.8-.3-1 0L11.9 14 9 12c-.3-.2-.6-.2-.8 0l-3.6 2.6V5c-.1-.3.1-.5.4-.5zm14 15H5c-.3 0-.5-.2-.5-.5v-2.4l4.1-3 3 1.9c.3.2.7.2.9-.1L16 12l3.5 3.4V19c0 .3-.2.5-.5.5z" } )
												),
												el( 'span', {},
													'Change profile image'
												),
											),

											el( 'a', { href: '#', onClick: removeImage },
												props.attributes.profile_pic ? 'Reset' : ''
											)

										];
									}
								} )
							),

							el( BaseControl, {},
								el( SelectControl, {
									label: 'Date format',
									value: props.attributes.date_format,
									onChange: ( value ) => {
										props.setAttributes( { date_format: value } );
										changeDate( value );
									},
									options: dateFormats,
								} )
							),

							el( BaseControl, {},
								el( SelectControl, {
									label: 'Divider type',
									value: props.attributes.divider_style,
									onChange: ( value ) => { props.setAttributes( { divider_style: value } ); },
									options: dividerStyles,
								} )
							),

							el( BaseControl, {
								label: 'Read online links to',
								className: 'ngl-gutenberg-base--fullwidth',
							},
								el( ButtonGroup, { className: 'ngl-gutenberg--fullwidth' },
									el( Button, {
										value: 'email',
										isPrimary: ( props.attributes.read_online_link === 'email' ),
										isSecondary: ( props.attributes.read_online_link !== 'email' ),
										onClick: changeReadTarget,
										label: 'Email HTML',
									}, 'Email HTML' ),
									el( Button, {
										value: 'blog',
										isPrimary: ( props.attributes.read_online_link === 'blog' ),
										isSecondary: ( props.attributes.read_online_link !== 'blog' ),
										onClick: changeReadTarget,
										label: 'Blog post'
									}, 'Blog post' ),
								)
							),

						),

						el( PanelColorSettings, {
							initialOpen: true,
							title: 'Color options',
							colorSettings: [
								{
									value: props.attributes.text_color,
									label: 'Font color',
									onChange: ( value ) => props.setAttributes( { text_color: value } ),
								},
							]
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

					),

					el( BlockControls, {},
						el( AlignmentToolbar,
							{
								value: props.attributes.alignment,
								onChange: onChangeAlignment
							}
						)
					),

					// This is how the block is rendered in editor.
					el( 'div', { className: 'ngl-metadata', style: metaStyles, 'data-padding': top_padding + ',' + bottom_padding + ',' + left_padding + ',' + right_padding, 'data-margin' : top_margin + ',' + bottom_margin },
						metaPic,
						metaIssue,
						metaDate,
						metaLocation,
						metaReadtime,
						metaPermalink
					)

				)

			);

		} ),

		// This is how the block is rendered in frontend.
		save: function( props ) {

			props.attributes.post_id = jQuery( '#post_ID' ).val();

			var divider = props.attributes.divider_style == 'dot' ? '•' : '|';

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
				color: props.attributes.text_color,
				textAlign: props.attributes.alignment,
				fontSize: props.attributes.font_size,
			};

			var metaIssue = '';
			if ( props.attributes.issue_title && props.attributes.show_issue ) {
				metaIssue = [
					el( RichText.Content, {
						tagName: 'div',
						className: 'ngl-metadata-issue',
						value: props.attributes.issue_title ? props.attributes.issue_title : ''
					} ),
					el( 'div', { className: 'ngl-metadata-sep' }, divider )
				];
			}

			var metaDate = '';
			if ( props.attributes.show_date ) {
					var postDate = props.attributes.post_date ? props.attributes.post_date : newsletterglue_meta.post_date;
				metaDate = [
					el( 'div', { className: 'ngl-metadata-date', 'data-date-format': props.attributes.date_format },
						postDate
					),
					el( 'div', { className: 'ngl-metadata-sep' }, divider )
				];
			}

			var metaLocation = '';
			if ( props.attributes.post_location && props.attributes.show_location ) {
				metaLocation = [
					el( 'img', {
						className: 'ngl-metadata-map-pin',
						src: block.assets_uri + 'map-pin.png'
					} ),
					el( RichText.Content, {
						tagName: 'div',
						className: 'ngl-metadata-map',
						value: props.attributes.post_location,
					} ),
					el( 'div', { className: 'ngl-metadata-sep' }, divider )
				];
			}

			var showName = props.attributes.author_name ? props.attributes.author_name : '';
			var userImage = props.attributes.profile_pic ? props.attributes.profile_pic : newsletterglue_meta.profile_pic;

			var metaPic = '';
			if ( props.attributes.show_author ) {
				metaPic = [
					el( 'div', { className: 'ngl-metadata-pic' },
						el( 'img', { src: userImage, className: 'avatar avatar-32 photo' },

						)
					),
					el( RichText.Content, {
						tagName: 'div',
						className: 'ngl-metadata-author',
						value: showName? showName : newsletterglue_meta.author_name,
					} ),
					el( 'div', { className: 'ngl-metadata-sep' }, divider )
				];
			}

			var read_more_tag = props.attributes.read_online_link === 'blog' ? '{{ blog_post }}' : '{{ webversion }}';

			var metaPermalink = '';
			if ( props.attributes.show_readonline ) {
				metaPermalink = [
					el( RichText.Content, {
						tagName: 'a',
						className: 'ngl-metadata-permalink',
						value: props.attributes.post_link,
						href: read_more_tag
					} ),
					el( 'img', {
						className: 'ngl-metadata-permalink-arrow',
						src: block.assets_uri + 'arrow.png'
					} )
				];
			}

			var metaReadtime = '';
			if ( props.attributes.show_readtime ) {
				metaReadtime = [
					el( RichText.Content, {
						tagName: 'div',
						className: 'ngl-metadata-readtime',
						value: props.attributes.readtime,
					} ),
					el( 'div', { className: 'ngl-metadata-readtime-ajax' },
						newsletterglue_meta.readtime
					),
					el( 'div', { className: 'ngl-metadata-sep' }, divider )
				];
			}

			return (

					el( 'div', { className: 'ngl-metadata', style: metaStyles, 'data-padding': top_padding + ',' + bottom_padding + ',' + left_padding + ',' + right_padding, 'data-margin' : top_margin + ',' + bottom_margin },
						metaPic,
						metaIssue,
						metaDate,
						metaLocation,
						metaReadtime,
						metaPermalink
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