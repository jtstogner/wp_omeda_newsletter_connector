import React from 'react'
import { __ } from '@wordpress/i18n';

import {
	Component,
	Fragment
} from '@wordpress/element';

import {
	BaseControl,
	PanelBody,
	FlexItem,
	FontSizePicker,
	SelectControl,
	__experimentalNavigatorScreen as NavigatorScreen,
	__experimentalNavigatorBackButton as NavigatorBackButton,
  __experimentalHStack as HStack,
	__experimentalVStack as VStack,
	__experimentalHeading as Heading,
} from '@wordpress/components';

import {
	__experimentalColorGradientControl as ColorGradientControl,
} from '@wordpress/block-editor';

export default class NGTypographyScreen extends Component {

	constructor( props ) {

		super( props );

	}

	render() {

		const { path, title, description } = this.props;

		const fontSizes = [
			{
				name: __( 'XSmall' ),
				slug: 'xsmall',
				size: 12,
			},
			{
				name: __( 'Small' ),
				slug: 'small',
				size: 14,
			},
			{
				name: __( 'Medium' ),
				slug: 'medium',
				size: 16,
			},
			{
				name: __( 'Large' ),
				slug: 'large',
				size: 18,
			},
			{
				name: __( 'XLarge' ),
				slug: 'xlarge',
				size: 20,
			},
		];

		const { isMobile, theme_m, theme_r, ngColors, quickstyle } = this.props.getState;

		let theme = isMobile ? theme_m : theme_r;

		const id = this.props.id;

		const sizeAttr = this.props.id + '_size';

		const fallbackFontSize = theme[ sizeAttr + '_default' ];

		const fontValue = theme[ sizeAttr ] ? theme[ sizeAttr ] : fallbackFontSize;

    let fallbackFontSize5;
    let sizeAttr5;
    let fontValue5;
    let fontValue6;
    let sizeAttr6;
    let fallbackFontSize6;

		if ( this.props.id == 'h4' ) {
			sizeAttr5 = 'h5_size';
			fallbackFontSize5 = theme[ 'h5_size_default' ];
			fontValue5 = theme[ sizeAttr5 ] ? theme[ sizeAttr5 ] : fallbackFontSize5;
			sizeAttr6 = 'h6_size';
			fallbackFontSize5 = theme[ 'h6_size_default' ];
			fontValue6 = theme[ sizeAttr6 ] ? theme[ sizeAttr6 ] : fallbackFontSize6;
		} else {
			sizeAttr5 = null;
			fallbackFontSize5 = null;
			fontValue5 = '';
			sizeAttr6 = null;
			fallbackFontSize6 = null;
			fontValue6 = '';
		}	

		const themeColors = nglue_backend.themeColors ? nglue_backend.themeColors : null;

		return (
			<NavigatorScreen path={ path } className="edit-site-global-styles-sidebar__navigator-screen">
				<VStack>
					<HStack justify="flex-start">
						<div>
							<NavigatorBackButton
								icon={ <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" className="edit-site-global-styles-icon-with-current-color" aria-hidden="true" focusable="false"><path d="M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z"></path></svg> }
							></NavigatorBackButton>
						</div>
						<FlexItem><Heading level={5}>{ title }</Heading></FlexItem>
					</HStack>
					<p className="edit-site-global-styles-header__description">
					{ description }
					</p>
				</VStack>

				{ this.props.id !== 'a' && 
				<PanelBody title={ this.props.id === 'h4' ? "Heading 4" : false } className="edit-site-typography-panel">

					<BaseControl>
						<SelectControl
							label="Font family"
							value={ theme[ id + '_font' ] ? theme[ id + '_font' ] : theme.font }
							options={ nglue_backend.email_fonts }
							onChange={ ( newValue ) => {
								this.props.handleChange( 'fontFamily', id + '_font', newValue );
							} }
						/>
					</BaseControl>

					<BaseControl>
						<FontSizePicker
							fontSizes={ fontSizes }
							value={ fontValue }
							fallbackFontSize={ fallbackFontSize }
							onChange={ ( newFontSize ) => {
								if ( newFontSize ) {
									this.props.handleChange( 'fontsize', sizeAttr, newFontSize );
								} else {
									this.props.handleChange( 'fontsize', sizeAttr, fallbackFontSize );
								}
							} }
						/>
					</BaseControl>

					<BaseControl>
						<ColorGradientControl
							colorValue={ theme[ id + '_colour' ] }
							colors={ [] }
							gradients={[]}
							onColorChange={ (newValue) => {
								this.props.handleChange( 'fontColor', id + '_colour', newValue );
							} }
							clearable={ false }
							disableCustomColors={ false }
						/>
						{ themeColors && <>
						<Heading level={2} style={ { fontSize: '11px', marginBottom: 0, textTransform: 'uppercase' }}>Site theme</Heading>
						<ColorGradientControl
							colorValue={ theme[ id + '_colour' ] }
							colors={ themeColors }
							gradients={ [] }
							onColorChange={ (newValue) => {
								this.props.handleChange( 'fontColor', id + '_colour', newValue );
							} }
							clearable={ false }
							disableCustomColors={ true }
						/>
						</>
						}
						<Heading level={2} style={ { fontSize: '11px', marginBottom: 0, textTransform: 'uppercase' }}>Newsletter style</Heading>
						<ColorGradientControl
							colorValue={ theme[ id + '_colour' ] }
							colors={ ngColors[ quickstyle ] }
							gradients={[]}
							onColorChange={ (newValue) => {
								this.props.handleChange( 'fontColor', id + '_colour', newValue );
							} }
							clearable={ false }
							disableCustomColors={ true }
						/>
					</BaseControl>

				</PanelBody>
				}

				{ this.props.id === 'a' &&
				<PanelBody title={ this.props.id === 'h4' ? "Heading 4" : false } className="edit-site-typography-panel">
					<BaseControl>
						<ColorGradientControl
							colorValue={ theme[ id + '_colour' ] }
							colors={ [] }
							gradients={[]}
							onColorChange={ (newValue) => {
								this.props.handleChange( 'fontColor', id + '_colour', newValue );
							} }
							clearable={ false }
							disableCustomColors={ false }
						/>
						{ themeColors && <>
						<Heading level={2} style={ { fontSize: '11px', marginBottom: 0, textTransform: 'uppercase' }}>Site theme</Heading>
						<ColorGradientControl
							colorValue={ theme[ id + '_colour' ] }
							colors={ themeColors }
							gradients={ [] }
							onColorChange={ (newValue) => {
								this.props.handleChange( 'fontColor', id + '_colour', newValue );
							} }
							clearable={ false }
							disableCustomColors={ true }
						/>
						</>
						}
						<Heading level={2} style={ { fontSize: '11px', marginBottom: 0, textTransform: 'uppercase' }}>Newsletter style</Heading>
						<ColorGradientControl
							colorValue={ theme[ id + '_colour' ] }
							colors={ ngColors[ quickstyle ] }
							gradients={[]}
							onColorChange={ (newValue) => {
								this.props.handleChange( 'fontColor', id + '_colour', newValue );
							} }
							clearable={ false }
							disableCustomColors={ true }
						/>
					</BaseControl>
				</PanelBody>
				}

				{ this.props.id === 'h4' && this.props.id !== 'a' && <>
				<PanelBody title="Heading 5" className="edit-site-typography-panel" initialOpen={ false }>

					<BaseControl>
						<SelectControl
							label="Font family"
							value={ theme[ 'h5_font' ] ? theme[ 'h5_font' ] : theme.font }
							options={ nglue_backend.email_fonts }
							onChange={ ( newValue ) => {
								this.props.handleChange( 'fontFamily', 'h5_font', newValue );
							} }
						/>
					</BaseControl>

					<BaseControl>
						<FontSizePicker
							fontSizes={ fontSizes }
							value={ fontValue5 }
							fallbackFontSize={ fallbackFontSize5 }
							onChange={ ( newFontSize ) => {
								if ( newFontSize ) {
									this.props.handleChange( 'fontsize', sizeAttr5, newFontSize );
								} else {
									this.props.handleChange( 'fontsize', sizeAttr5, fallbackFontSize5 );
								}
							} }
						/>
					</BaseControl>

					<BaseControl>
						<ColorGradientControl
							colorValue={ theme[ 'h5_colour' ] }
							colors={ [] }
							gradients={[]}
							onColorChange={ (newValue) => {
								this.props.handleChange( 'fontColor', 'h5_colour', newValue );
							} }
							clearable={ false }
							disableCustomColors={ false }
						/>
						{ themeColors && <>
						<Heading level={2} style={ { fontSize: '11px', marginBottom: 0, textTransform: 'uppercase' }}>Site theme</Heading>
						<ColorGradientControl
							colorValue={ theme[ 'h5_colour' ] }
							colors={ themeColors }
							gradients={ [] }
							onColorChange={ (newValue) => {
								this.props.handleChange( 'fontColor', 'h5_colour', newValue );
							} }
							clearable={ false }
							disableCustomColors={ true }
						/>
						</>
						}
						<Heading level={2} style={ { fontSize: '11px', marginBottom: 0, textTransform: 'uppercase' }}>Newsletter style</Heading>
						<ColorGradientControl
							colorValue={ theme[ 'h5_colour' ] }
							colors={ ngColors[ quickstyle ] }
							gradients={[]}
							onColorChange={ (newValue) => {
								this.props.handleChange( 'fontColor', 'h5_colour', newValue );
							} }
							clearable={ false }
							disableCustomColors={ true }
						/>
					</BaseControl>

				</PanelBody>

				<PanelBody title="Heading 6" className="edit-site-typography-panel" initialOpen={ false }>

					<BaseControl>
						<SelectControl
							label="Font family"
							value={ theme[ 'h6_font' ] ? theme[ 'h6_font' ] : theme.font }
							options={ nglue_backend.email_fonts }
							onChange={ ( newValue ) => {
								this.props.handleChange( 'fontFamily', 'h6_font', newValue );
							} }
						/>
					</BaseControl>

					<BaseControl>
						<FontSizePicker
							fontSizes={ fontSizes }
							value={ fontValue6 }
							fallbackFontSize={ fallbackFontSize6 }
							onChange={ ( newFontSize ) => {
								if ( newFontSize ) {
									this.props.handleChange( 'fontsize', sizeAttr6, newFontSize );
								} else {
									this.props.handleChange( 'fontsize', sizeAttr6, fallbackFontSize6 );
								}
							} }
						/>
					</BaseControl>

					<BaseControl>
						<ColorGradientControl
							colorValue={ theme[ 'h6_colour' ] }
							colors={ [] }
							gradients={[]}
							onColorChange={ (newValue) => {
								this.props.handleChange( 'fontColor', 'h6_colour', newValue );
							} }
							clearable={ false }
							disableCustomColors={ false }
						/>
						{ themeColors && <>
						<Heading level={2} style={ { fontSize: '11px', marginBottom: 0, textTransform: 'uppercase' }}>Site theme</Heading>
						<ColorGradientControl
							colorValue={ theme[ 'h6_colour' ] }
							colors={ themeColors }
							gradients={ [] }
							onColorChange={ (newValue) => {
								this.props.handleChange( 'fontColor', 'h6_colour', newValue );
							} }
							clearable={ false }
							disableCustomColors={ true }
						/>
						</>
						}
						<Heading level={2} style={ { fontSize: '11px', marginBottom: 0, textTransform: 'uppercase' }}>Newsletter style</Heading>
						<ColorGradientControl
							colorValue={ theme[ 'h6_colour' ] }
							colors={ ngColors[ quickstyle ] }
							gradients={[]}
							onColorChange={ (newValue) => {
								this.props.handleChange( 'fontColor', 'h6_colour', newValue );
							} }
							clearable={ false }
							disableCustomColors={ true }
						/>
					</BaseControl>

				</PanelBody>

				</> }

			</NavigatorScreen>
		);

	}

}