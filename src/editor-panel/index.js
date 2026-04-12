/**
 * VK All in One Expansion Unit - Editor Panel
 *
 * ブロックエディタのサイドバーにExUnit設定パネルを追加する。
 * PluginSidebar を使い、ツールバーに専用アイコンを表示。
 * 各セクションは PanelBody で折りたたみ可能。
 */

import { registerPlugin } from '@wordpress/plugins';
import { PluginSidebar } from '@wordpress/editor';
import {
	CheckboxControl,
	TextControl,
	TextareaControl,
	SelectControl,
	Button,
	PanelBody,
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
 * ExUnit sidebar component.
 *
 * PluginSidebar 内に各機能セクションを PanelBody で配置。
 * 機能の有効/無効に応じてセクションを条件表示。
 * CTA投稿タイプではCTAセクションも表示。
 */
const VeuSidebar = () => {
	const postType = useSelect(
		( s ) => s( 'core/editor' ).getCurrentPostType(),
		[]
	);
	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	// CTA の画像関連（フックは早期リターンの前に呼ぶ必要がある）
	const isCta = postType === 'cta';
	const ctaImg =
		isCta && meta?.vkExUnit_cta_img
			? parseInt( meta.vkExUnit_cta_img, 10 )
			: 0;
	const image = useSelect(
		( s ) => ( ctaImg ? s( 'core' ).getMedia( ctaImg ) : null ),
		[ ctaImg ]
	);

	// activeFeatures が空で CTA でもない場合は何も表示しない
	if ( ! activeFeatures.length && ! isCta ) {
		return null;
	}

	const update = ( key, value ) => setMeta( { ...meta, [ key ]: value } );
	const isChecked = ( key ) => meta?.[ key ] === 'true';

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
		<PluginSidebar
			name="veu-settings"
			title={ data.panelTitle || 'VK ExUnit' }
			icon="admin-plugins"
		>
			{ hasSnsSection && (
				<PanelBody title="SNS" initialOpen={ false }>
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
				</PanelBody>
			) }

			{ hasSeoSection && (
				<PanelBody title="SEO" initialOpen={ false }>
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
				</PanelBody>
			) }

			{ hasDisplaySection && (
				<PanelBody
					title={ i18n.displaySection || 'Display' }
					initialOpen={ false }
				>
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
				</PanelBody>
			) }

			{ hasPageSection && (
				<PanelBody
					title={ i18n.pageSection || 'Page' }
					initialOpen={ false }
				>
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
				</PanelBody>
			) }

			{ hasCssSection && (
				<PanelBody title="CSS" initialOpen={ false }>
					<TextareaControl
						label={ i18n.customCss || 'Custom CSS' }
						value={ meta?._veu_custom_css || '' }
						onChange={ ( v ) => update( '_veu_custom_css', v ) }
						rows={ 4 }
					/>
				</PanelBody>
			) }

			{ isCta && (
				<>
					<PanelBody
						title={ ctaI18n.ctaImage || 'CTA image' }
						initialOpen={ true }
					>
						<CheckboxControl
							label={
								ctaI18n.useClassic ||
								'Use following data (Do not use content data)'
							}
							checked={
								meta?.vkExUnit_cta_use_type === 'veu_cta_normal'
							}
							onChange={ ( c ) =>
								update(
									'vkExUnit_cta_use_type',
									c ? 'veu_cta_normal' : ''
								)
							}
						/>
						{ ctaImg > 0 && image ? (
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
									update(
										'vkExUnit_cta_img',
										String( media.id )
									)
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
											variant={
												ctaImg ? 'secondary' : 'primary'
											}
										>
											{ ctaImg
												? ctaI18n.changeImage ||
												  'Change image'
												: ctaI18n.addImage ||
												  'Add image' }
										</Button>
										{ ctaImg > 0 && (
											<Button
												onClick={ () =>
													update(
														'vkExUnit_cta_img',
														''
													)
												}
												isDestructive
												variant="tertiary"
											>
												{ ctaI18n.removeImage ||
													'Remove image' }
											</Button>
										) }
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
					</PanelBody>

					<PanelBody
						title={ ctaI18n.buttonSection || 'Button' }
						initialOpen={ false }
					>
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
							onChange={ ( v ) =>
								update( 'vkExUnit_cta_url', v )
							}
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
								update(
									'vkExUnit_cta_url_blank',
									c ? 'true' : ''
								)
							}
						/>
					</PanelBody>

					<PanelBody
						title={ ctaI18n.textSection || 'Text' }
						initialOpen={ false }
					>
						<TextareaControl
							label={ ctaI18n.ctaText || 'CTA text' }
							value={ meta?.vkExUnit_cta_text || '' }
							onChange={ ( v ) =>
								update( 'vkExUnit_cta_text', v )
							}
							rows={ 4 }
						/>
					</PanelBody>
				</>
			) }
		</PluginSidebar>
	);
};

registerPlugin( 'veu-settings-panel', { render: VeuSidebar } );
