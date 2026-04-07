import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import {
	CheckboxControl,
	TextControl,
	TextareaControl,
	SelectControl,
	Button,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';

const data = window.veuPanelData || {};
const i18n = data.i18n || {};
const ctaI18n = data.ctaI18n || {};
const activeFeatures = data.activeFeatures || [];

const isActive = ( feature ) => activeFeatures.includes( feature );

/**
 * Section heading component.
 *
 * @param {Object} root0          Component props.
 * @param {Object} root0.children Children elements.
 */
const SectionHeading = ( { children } ) => (
	<div
		style={ {
			borderBottom: '1px solid #ddd',
			paddingBottom: '4px',
			marginTop: '16px',
			marginBottom: '8px',
			fontSize: '12px',
			fontWeight: 600,
			textTransform: 'uppercase',
			color: '#757575',
			letterSpacing: '0.5px',
		} }
	>
		{ children }
	</div>
);

/**
 * Section group wrapper.
 *
 * @param {Object} root0          Component props.
 * @param {Object} root0.children Children elements.
 */
const SectionGroup = ( { children } ) => (
	<div style={ { marginBottom: '12px' } }>{ children }</div>
);

/**
 * ExUnit common settings panel.
 */
const VeuSettingsPanel = () => {
	const postType = useSelect(
		( s ) => s( 'core/editor' ).getCurrentPostType(),
		[]
	);
	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	const update = ( key, value ) => setMeta( { ...meta, [ key ]: value } );
	const isChecked = ( key ) => meta?.[ key ] === 'true';

	if ( ! activeFeatures.length ) {
		return null;
	}

	const hasSnsSection = isActive( 'sns' );
	const hasSeoSection =
		isActive( 'noindex' ) ||
		isActive( 'sitemap_page' ) ||
		isActive( 'wpTitle' );
	const hasDisplaySection =
		isActive( 'auto_eyecatch' ) || isActive( 'promotion_alert' );
	const hasPageSection =
		isActive( 'page_exclude_from_list_pages' ) && postType === 'page';
	const hasCssSection = isActive( 'css_customize' );

	return (
		<PluginDocumentSettingPanel
			name="veu-settings"
			title={ data.panelTitle || 'VK ExUnit' }
		>
			{ hasSnsSection && (
				<SectionGroup>
					<SectionHeading>SNS</SectionHeading>
					<CheckboxControl
						label={
							i18n.snsHide || "Don't display sns share button"
						}
						checked={ isChecked( 'sns_share_botton_hide' ) }
						onChange={ ( c ) =>
							update( 'sns_share_botton_hide', c ? 'true' : '' )
						}
					/>
					<TextControl
						label={ i18n.snsTitle || 'SNS Title' }
						value={ meta?.vkExUnit_sns_title || '' }
						onChange={ ( v ) => update( 'vkExUnit_sns_title', v ) }
					/>
				</SectionGroup>
			) }

			{ hasSeoSection && (
				<SectionGroup>
					<SectionHeading>SEO</SectionHeading>
					{ isActive( 'noindex' ) && (
						<CheckboxControl
							label={ i18n.noindex || 'noindex' }
							checked={ isChecked( '_vk_print_noindex' ) }
							onChange={ ( c ) =>
								update( '_vk_print_noindex', c ? 'true' : '' )
							}
						/>
					) }
					{ isActive( 'sitemap_page' ) && (
						<CheckboxControl
							label={
								i18n.sitemapHide || "Don't display on sitemap"
							}
							checked={ isChecked( 'sitemap_hide' ) }
							onChange={ ( c ) =>
								update( 'sitemap_hide', c ? 'true' : '' )
							}
						/>
					) }
					{ isActive( 'wpTitle' ) && (
						<TextControl
							label={ i18n.headTitle || 'Head Title' }
							value={ meta?.veu_head_title || '' }
							onChange={ ( v ) => update( 'veu_head_title', v ) }
						/>
					) }
				</SectionGroup>
			) }

			{ hasDisplaySection && (
				<SectionGroup>
					<SectionHeading>
						{ i18n.displaySection || 'Display' }
					</SectionHeading>
					{ isActive( 'auto_eyecatch' ) && (
						<CheckboxControl
							label={
								i18n.eyecatchHide || "Don't display eyecatch"
							}
							checked={ isChecked( 'vkExUnit_EyeCatch_disable' ) }
							onChange={ ( c ) =>
								update(
									'vkExUnit_EyeCatch_disable',
									c ? 'true' : ''
								)
							}
						/>
					) }
					{ isActive( 'promotion_alert' ) && (
						<CheckboxControl
							label={
								i18n.promotionAlert || 'Display promotion alert'
							}
							checked={ isChecked(
								'veu_display_promotion_alert'
							) }
							onChange={ ( c ) =>
								update(
									'veu_display_promotion_alert',
									c ? 'true' : ''
								)
							}
						/>
					) }
				</SectionGroup>
			) }

			{ hasPageSection && (
				<SectionGroup>
					<SectionHeading>
						{ i18n.pageSection || 'Page' }
					</SectionHeading>
					<CheckboxControl
						label={ i18n.pageExclude || 'Exclude from page list' }
						checked={ isChecked( '_exclude_from_list_pages' ) }
						onChange={ ( c ) =>
							update(
								'_exclude_from_list_pages',
								c ? 'true' : ''
							)
						}
					/>
				</SectionGroup>
			) }

			{ hasCssSection && (
				<SectionGroup>
					<SectionHeading>CSS</SectionHeading>
					<TextareaControl
						label={ i18n.customCss || 'Custom CSS' }
						value={ meta?._veu_custom_css || '' }
						onChange={ ( v ) => update( '_veu_custom_css', v ) }
						rows={ 4 }
					/>
				</SectionGroup>
			) }
		</PluginDocumentSettingPanel>
	);
};

/**
 * CTA Contents panel (cta post type only).
 */
const VeuCtaPanel = () => {
	const postType = useSelect(
		( s ) => s( 'core/editor' ).getCurrentPostType(),
		[]
	);
	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	const ctaImg = meta?.vkExUnit_cta_img
		? parseInt( meta.vkExUnit_cta_img, 10 )
		: 0;
	const image = useSelect(
		( s ) => ( ctaImg ? s( 'core' ).getMedia( ctaImg ) : null ),
		[ ctaImg ]
	);

	if ( postType !== 'cta' ) {
		return null;
	}

	const update = ( key, value ) => setMeta( { ...meta, [ key ]: value } );

	return (
		<PluginDocumentSettingPanel
			name="veu-cta-contents"
			title={ ctaI18n.panelTitle || 'CTA Contents' }
		>
			<CheckboxControl
				label={
					ctaI18n.useClassic ||
					'Use following data (Do not use content data)'
				}
				checked={ meta?.vkExUnit_cta_use_type === 'veu_cta_normal' }
				onChange={ ( c ) =>
					update( 'vkExUnit_cta_use_type', c ? 'veu_cta_normal' : '' )
				}
			/>

			<SectionGroup>
				<SectionHeading>
					{ ctaI18n.ctaImage || 'CTA image' }
				</SectionHeading>
				{ ctaImg && image ? (
					<img
						src={ image.source_url }
						alt="CTA"
						style={ {
							maxWidth: '100%',
							height: 'auto',
							marginBottom: '8px',
							borderRadius: '4px',
						} }
					/>
				) : null }
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ ( media ) =>
							update( 'vkExUnit_cta_img', String( media.id ) )
						}
						allowedTypes={ [ 'image' ] }
						value={ ctaImg }
						render={ ( { open } ) => (
							<div
								style={ {
									display: 'flex',
									gap: '8px',
								} }
							>
								<Button
									onClick={ open }
									variant={ ctaImg ? 'secondary' : 'primary' }
								>
									{ ctaImg
										? ctaI18n.changeImage || 'Change image'
										: ctaI18n.addImage || 'Add image' }
								</Button>
								{ ctaImg ? (
									<Button
										onClick={ () =>
											update( 'vkExUnit_cta_img', '' )
										}
										isDestructive
										variant="tertiary"
									>
										{ ctaI18n.removeImage ||
											'Remove image' }
									</Button>
								) : null }
							</div>
						) }
					/>
				</MediaUploadCheck>

				<SelectControl
					label={ ctaI18n.imgPosition || 'Image position' }
					value={ meta?.vkExUnit_cta_img_position || '' }
					options={ [
						{
							label: ctaI18n.posNormal || 'Normal',
							value: '',
						},
						{
							label: ctaI18n.posRight || 'Right',
							value: 'right',
						},
					] }
					onChange={ ( v ) =>
						update( 'vkExUnit_cta_img_position', v )
					}
				/>
			</SectionGroup>

			<SectionGroup>
				<SectionHeading>
					{ ctaI18n.buttonSection || 'Button' }
				</SectionHeading>
				<TextControl
					label={ ctaI18n.buttonText || 'Button text' }
					value={ meta?.vkExUnit_cta_button_text || '' }
					onChange={ ( v ) =>
						update( 'vkExUnit_cta_button_text', v )
					}
				/>
				<TextControl
					label={ ctaI18n.ctaUrl || 'URL' }
					value={ meta?.vkExUnit_cta_url || '' }
					onChange={ ( v ) => update( 'vkExUnit_cta_url', v ) }
					type="url"
				/>
				<CheckboxControl
					label={ ctaI18n.urlBlank || 'Open link in new window' }
					checked={
						meta?.vkExUnit_cta_url_blank === 'true' ||
						meta?.vkExUnit_cta_url_blank === '1'
					}
					onChange={ ( c ) =>
						update( 'vkExUnit_cta_url_blank', c ? 'true' : '' )
					}
				/>
			</SectionGroup>

			<SectionGroup>
				<SectionHeading>
					{ ctaI18n.textSection || 'Text' }
				</SectionHeading>
				<TextareaControl
					label={ ctaI18n.ctaText || 'CTA text' }
					value={ meta?.vkExUnit_cta_text || '' }
					onChange={ ( v ) => update( 'vkExUnit_cta_text', v ) }
					rows={ 4 }
				/>
			</SectionGroup>
		</PluginDocumentSettingPanel>
	);
};

registerPlugin( 'veu-settings-panel', { render: VeuSettingsPanel } );
registerPlugin( 'veu-cta-panel', { render: VeuCtaPanel } );
