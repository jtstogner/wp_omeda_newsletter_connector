import React from 'react'

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save( { attributes } ) {

	const classes = classnames( {
		'ng-block': true,
		'ng-should-remove' : !attributes.width
	} );

	var attrs = {
		width: attributes.width ? attributes.width : 'auto',
		className: classes,
		valign: attributes.verticalAlign,
		style: {
			width: attributes.width ? attributes.width + 'px' : 'auto',
			paddingTop: attributes.padding.top,
			paddingBottom: attributes.padding.bottom,
			paddingLeft: attributes.padding.left,
			paddingRight: attributes.padding.right,
			display: !attributes.width ? 'none' : 'table-cell',
			verticalAlign: attributes.verticalAlign,
			backgroundColor: attributes.background,
		}
	};

	let blockProps = useBlockProps.save( attrs );

	const innerBlocksProps = useInnerBlocksProps.save(blockProps);

	return (
		<td {...innerBlocksProps} />
	);

}