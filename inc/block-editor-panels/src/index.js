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
 * ExUnit common settings panel.
 * ExUnit 共通設定パネル。
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

	return (
		<PluginDocumentSettingPanel
			name="veu-settings"
			title={ data.panelTitle || 'VK ExUnit' }
		>
			{ isActive( 'sns' ) && (
				<>
					<CheckboxControl
						label={
							i18n.snsHide ||
							"Don't display sns share button"
						}
						checked={ isChecked( 'sns_share_botton_hide' ) }
						onChange={ ( c ) =>
							update(
								'sns_share_botton_hide',
								c ? 'true' : ''
							)
						}
					/>
					<TextControl
						label={ i18n.snsTitle || 'SNS Title' }
						value={ meta?.vkExUnit_sns_title || '' }
						onChange={ ( v ) =>
							update( 'vkExUnit_sns_title', v )
						}
					/>
				</>
			) }
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
			{ isActive( 'auto_eyecatch' ) && (
				<CheckboxControl
					label={
						i18n.eyecatchHide || "Don't display eyecatch"
					}
					checked={ isChecked(
						'vkExUnit_EyeCatch_disable'
					) }
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
						i18n.promotionAlert ||
						'Display promotion alert'
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
			{ isActive( 'page_exclude_from_list_pages' ) &&
				postType === 'page' && (
					<CheckboxControl
						label={
							i18n.pageExclude ||
							'Exclude from page list'
						}
						checked={ isChecked(
							'_exclude_from_list_pages'
						) }
						onChange={ ( c ) =>
							update(
								'_exclude_from_list_pages',
								c ? 'true' : ''
							)
						}
					/>
				) }
			{ isActive( 'css_customize' ) && (
				<TextareaControl
					label={ i18n.customCss || 'Custom CSS' }
					value={ meta?._veu_custom_css || '' }
					onChange={ ( v ) => update( '_veu_custom_css', v ) }
					rows={ 4 }
				/>
			) }
		</PluginDocumentSettingPanel>
	);
};

/**
 * CTA Contents panel (cta post type only).
 * CTA コンテンツパネル（cta 投稿タイプのみ）。
 */
const VeuCtaPanel = () => {
	const postType = useSelect(
		( s ) => s( 'core/editor' ).getCurrentPostType(),
		[]
	);
	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	if ( postType !== 'cta' ) {
		return null;
	}

	const update = ( key, value ) => setMeta( { ...meta, [ key ]: value } );

	const ctaImg = meta?.vkExUnit_cta_img
		? parseInt( meta.vkExUnit_cta_img, 10 )
		: 0;
	const image = useSelect(
		( s ) => ( ctaImg ? s( 'core' ).getMedia( ctaImg ) : null ),
		[ ctaImg ]
	);

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
					update(
						'vkExUnit_cta_use_type',
						c ? 'veu_cta_normal' : ''
					)
				}
			/>

			<div style={ { marginTop: '16px' } }>
				<p style={ { fontWeight: 'bold', marginBottom: '8px' } }>
					{ ctaI18n.ctaImage || 'CTA image' }
				</p>
				{ ctaImg && image ? (
					<img
						src={ image.source_url }
						alt="CTA"
						style={ {
							maxWidth: '100%',
							height: 'auto',
							marginBottom: '8px',
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
							<>
								<Button
									onClick={ open }
									variant={ ctaImg ? 'secondary' : 'primary' }
									style={ { marginRight: '8px' } }
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
										{ ctaI18n.removeImage || 'Remove image' }
									</Button>
								) : null }
							</>
						) }
					/>
				</MediaUploadCheck>
			</div>

			<SelectControl
				label={ ctaI18n.imgPosition || 'Image position' }
				value={ meta?.vkExUnit_cta_img_position || '' }
				options={ [
					{ label: ctaI18n.posNormal || 'Normal', value: '' },
					{
						label: ctaI18n.posRight || 'Right',
						value: 'right',
					},
				] }
				onChange={ ( v ) =>
					update( 'vkExUnit_cta_img_position', v )
				}
				style={ { marginTop: '16px' } }
			/>

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
				label={
					ctaI18n.urlBlank || 'Open link in new window'
				}
				checked={
					meta?.vkExUnit_cta_url_blank === 'true' ||
					meta?.vkExUnit_cta_url_blank === '1'
				}
				onChange={ ( c ) =>
					update( 'vkExUnit_cta_url_blank', c ? 'true' : '' )
				}
			/>

			<TextareaControl
				label={ ctaI18n.ctaText || 'CTA text' }
				value={ meta?.vkExUnit_cta_text || '' }
				onChange={ ( v ) => update( 'vkExUnit_cta_text', v ) }
				rows={ 4 }
			/>
		</PluginDocumentSettingPanel>
	);
};

registerPlugin( 'veu-settings-panel', { render: VeuSettingsPanel } );
registerPlugin( 'veu-cta-panel', { render: VeuCtaPanel } );
