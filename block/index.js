/**
 * Internal dependencies
 */
import './editor.scss';
import './style.scss';

const { __ } = wp.i18n;
const { Toolbar, PanelColor, Dashicon } = wp.components;
const InspectorControls = wp.blocks.InspectorControls;
const { RangeControl, ToggleControl, SelectControl } = InspectorControls;

const {
	registerBlockType,
	Editable,
	BlockControls,
	AlignmentToolbar,
	ColorPalette,
	source
} = wp.blocks;

registerBlockType( 'gutenkit/social-sharing', {

	title: __( 'Social' ),

	description: __( 'Easily add a social sharing element to your post.' ),

	icon: 'share',

	category: 'common',

	keywords: [ __( 'share' ), __( 'twitter' ), __( 'gutenkit' ) ],

	edit( { attributes, setAttributes, focus, setFocus, className } ) {

		const { twitter, facebook, pinterest, tumblr, linkedin, align, backgroundColor } = attributes;

		const iconStyle = {
			backgroundColor: backgroundColor,
		};

		const inspectorControls = focus && (
			<InspectorControls key="inspector">
				<ToggleControl
					label={ __( 'Twitter' ) }
					checked={ !! twitter }
					onChange={ () => setAttributes( {  twitter: ! twitter } ) }
				/>
				<ToggleControl
					label={ __( 'Facebook' ) }
					checked={ !! facebook }
					onChange={ () => setAttributes( {  facebook: ! facebook } ) }
				/>
				<ToggleControl
					label={ __( 'LinkedIn' ) }
					checked={ !! linkedin }
					onChange={ () => setAttributes( {  linkedin: ! linkedin } ) }
				/>
				<ToggleControl
					label={ __( 'Pinterest' ) }
					checked={ !! pinterest }
					onChange={ () => setAttributes( {  pinterest: ! pinterest } ) }
				/>
				<ToggleControl
					label={ __( 'Tumblr' ) }
					checked={ !! tumblr }
					onChange={ () => setAttributes( {  tumblr: ! tumblr } ) }
				/>
				<PanelColor title={ __( 'Icon Color' ) } colorValue={ backgroundColor }>
					<ColorPalette
						value={ backgroundColor }
						onChange={ ( colorValue ) => setAttributes( { backgroundColor: colorValue } ) }
					/>
				</PanelColor>
			</InspectorControls>
		);

		const controls = focus && (
			<BlockControls key="controls">
				<AlignmentToolbar
					value={ align }
					onChange={ ( newAlignment ) => setAttributes( { align: newAlignment } ) }
					controls={ [ 'left', 'center', 'right' ] }
				/>
			</BlockControls>
		);

		return [
			controls,
			inspectorControls,
			<div className={ className } style={ { textAlign: align } }>

				<p>
					{ twitter &&
						<a className={ 'wp-block-gutenkit-social-sharing__button button--twitter icon--gutenkit' } style={ iconStyle }>
							<span className={ 'screen-reader-text' }>
								{ __( 'Share on Twitter' ) }
							</span>
						</a>
					}

					{ facebook &&
						<a className={ 'wp-block-gutenkit-social-sharing__button button--facebook icon--gutenkit' } style={ iconStyle }>
							<span className={ 'screen-reader-text' }>
								{ __( 'Share on Facebook' ) }
							</span>
						</a>
					}

					{ pinterest &&
						<a className={ 'wp-block-gutenkit-social-sharing__button button--pinterest icon--gutenkit' } style={ iconStyle }>
							<span className={ 'screen-reader-text' }>
								{ __( 'Share on Pinterest' ) }
							</span>
						</a>
					}

					{ linkedin &&
						<a className={ 'wp-block-gutenkit-social-sharing__button button--linkedin icon--gutenkit' } style={ iconStyle }>
							<span className={ 'screen-reader-text' }>
								{ __( 'Share on LinkedIn' ) }
							</span>
						</a>
					}

					{ tumblr &&
						<a className={ 'wp-block-gutenkit-social-sharing__button button--tumblr icon--gutenkit' } style={ iconStyle }>
							<span className={ 'screen-reader-text' }>
								{ __( 'Share on Tumblr' ) }
							</span>
						</a>
					}
				</p>
			</div>
		];
	},
	save() {
		return null;
	},
} );
