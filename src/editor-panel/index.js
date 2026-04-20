/**
 * VK All in One Expansion Unit - Editor Panel
 *
 * ブロックエディタのサイドバーにExUnit設定パネルを追加する。
 * PluginSidebar を使い、ツールバーに専用アイコンを表示。
 * 各セクションは PanelBody で折りたたみ可能。
 *
 * 旧メタボックス（admin/admin-post-metabox.php経由）と同じ機能・ラベル・データ構造を維持し、
 * 既存ユーザーの保存データとの互換性を確保する。
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
	ExternalLink,
} from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import Icon from './icon.svg';

const data = window.veuPanelData || {};
const i18n = data.i18n || {};
const ctaI18n = data.ctaI18n || {};
const activeFeatures = data.activeFeatures || [];
const ctaOptions = data.ctaOptions || [];
const ctaSettingUrl = data.ctaSettingUrl || '';
const ctaIndexUrl = data.ctaIndexUrl || '';

const isActive = ( feature ) => activeFeatures.includes( feature );

/**
 * 外側コンポーネント: postType を取得し、未定義なら何も描画しない。
 * postType が確定してから内側コンポーネントに渡すことで
 * useEntityProp のフック呼び出し順序を安定させる。
 */
const VeuSidebar = () => {
	const postType = useSelect(
		( s ) => s( 'core/editor' ).getCurrentPostType(),
		[]
	);

	if ( ! postType ) {
		return null;
	}

	return <VeuSidebarInner postType={ postType } />;
};

/**
 * 内側コンポーネント: postType が確定した状態でフックを呼ぶ。
 * 旧メタボックスと同じセクション構成で PanelBody を並べる。
 *
 * @param {Object} props          Component props.
 * @param {string} props.postType 投稿タイプスラッグ
 */
const VeuSidebarInner = ( { postType } ) => {
	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	// veu_head_title は配列メタのため meta 経由では扱えない。
	// REST の独自フィールド veu_head_title_object を読み書きする。
	const postId = useSelect(
		( s ) => s( 'core/editor' ).getCurrentPostId(),
		[]
	);
	const headTitleObject = useSelect(
		( s ) => {
			const record = s( 'core' ).getEntityRecord(
				'postType',
				postType,
				postId
			);
			return record?.veu_head_title_object || null;
		},
		[ postType, postId ]
	);
	const { editEntityRecord } = useDispatch( 'core' );
	const updateHeadTitle = ( key, value ) => {
		const current = headTitleObject || {
			title: '',
			add_site_title: '',
		};
		editEntityRecord( 'postType', postType, postId, {
			veu_head_title_object: {
				...current,
				[ key ]: value,
			},
		} );
	};

	// CTA 投稿タイプ自身の編集UI（旧実装を維持）
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

	const isPage = postType === 'page';

	// 挿入アイテムは page 投稿タイプかつ関連機能が有効な時だけ表示
	const hasInsertItemsSection =
		isPage &&
		( isActive( 'sitemap_page' ) ||
			isActive( 'childPageIndex' ) ||
			isActive( 'pageList_ancestor' ) ||
			isActive( 'contact_section' ) );

	return (
		<PluginSidebar
			name="veu-settings"
			title={ data.panelTitle || 'VK ExUnit' }
			icon={ <Icon /> }
		>
			{ /* 広告開示設定: priority 1, 3択セレクト */ }
			{ isActive( 'promotion_alert' ) && (
				<PanelBody
					title={
						i18n.promotionSection || 'Promotion Disclosure Setting'
					}
					initialOpen={ false }
				>
					<SelectControl
						label={
							i18n.promotionSection ||
							'Promotion Disclosure Setting'
						}
						value={ meta?.veu_display_promotion_alert || '' }
						options={ [
							{
								value: 'common',
								label:
									i18n.promotionCommon ||
									'Apply common settings',
							},
							{
								value: 'display',
								label: i18n.promotionDisplay || 'Display',
							},
							{
								value: 'hide',
								label: i18n.promotionHide || 'Hide',
							},
						] }
						onChange={ ( v ) =>
							update( 'veu_display_promotion_alert', v )
						}
					/>
				</PanelBody>
			) }

			{ /* 挿入アイテムの設定: priority 10, 固定ページのみ */ }
			{ hasInsertItemsSection && (
				<PanelBody
					title={
						i18n.insertItemsSection || 'Setting of insert items'
					}
					initialOpen={ false }
				>
					{ isActive( 'sitemap_page' ) && (
						<CheckboxControl
							__nextHasNoMarginBottom
							label={
								i18n.sitemapInsert || 'Display a HTML sitemap'
							}
							checked={ isChecked( 'vkExUnit_sitemap' ) }
							onChange={ ( c ) =>
								update( 'vkExUnit_sitemap', c ? 'true' : '' )
							}
						/>
					) }
					{ isActive( 'childPageIndex' ) && (
						<CheckboxControl
							__nextHasNoMarginBottom
							label={
								i18n.childPageIndex ||
								'Display a child page index'
							}
							checked={ isChecked( 'vkExUnit_childPageIndex' ) }
							onChange={ ( c ) =>
								update(
									'vkExUnit_childPageIndex',
									c ? 'true' : ''
								)
							}
						/>
					) }
					{ isActive( 'pageList_ancestor' ) && (
						<CheckboxControl
							__nextHasNoMarginBottom
							label={
								i18n.pageListAncestor ||
								'Display a page list from ancestor'
							}
							checked={ isChecked(
								'vkExUnit_pageList_ancestor'
							) }
							onChange={ ( c ) =>
								update(
									'vkExUnit_pageList_ancestor',
									c ? 'true' : ''
								)
							}
						/>
					) }
					{ isActive( 'contact_section' ) && (
						<CheckboxControl
							__nextHasNoMarginBottom
							label={
								i18n.contactEnable || 'Display Contact Section'
							}
							checked={ isChecked( 'vkExUnit_contact_enable' ) }
							onChange={ ( c ) =>
								update(
									'vkExUnit_contact_enable',
									c ? 'true' : ''
								)
							}
						/>
					) }
				</PanelBody>
			) }

			{ /* シェアボタンの非表示設定: priority 50 */ }
			{ isActive( 'sns' ) && (
				<PanelBody
					title={
						i18n.snsHideSection || 'Hide setting of share button'
					}
					initialOpen={ false }
				>
					<CheckboxControl
						__nextHasNoMarginBottom
						label={ i18n.snsHide || "Don't display share bottons." }
						checked={ isChecked( 'sns_share_botton_hide' ) }
						onChange={ ( c ) =>
							update( 'sns_share_botton_hide', c ? 'true' : '' )
						}
					/>
				</PanelBody>
			) }

			{ /* OGPタイトル: priority 50 */ }
			{ isActive( 'sns' ) && (
				<PanelBody
					title={ i18n.snsTitleSection || 'OGP Title' }
					initialOpen={ false }
				>
					<TextControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ i18n.snsTitle || 'SNS Title' }
						value={ meta?.vkExUnit_sns_title || '' }
						onChange={ ( v ) => update( 'vkExUnit_sns_title', v ) }
						help={ i18n.snsTitleHelp }
					/>
				</PanelBody>
			) }

			{ /* noindex設定: priority 50 */ }
			{ isActive( 'noindex' ) && (
				<PanelBody
					title={ i18n.noindexSection || 'Noindex setting' }
					initialOpen={ false }
				>
					<CheckboxControl
						__nextHasNoMarginBottom
						label={
							i18n.noindex ||
							'Print noindex tag that to be do not display on search result.'
						}
						checked={ isChecked( '_vk_print_noindex' ) }
						onChange={ ( c ) =>
							update( '_vk_print_noindex', c ? 'true' : '' )
						}
					/>
				</PanelBody>
			) }

			{ /* サイトマップ非表示: priority 50, 固定ページのみ */ }
			{ isActive( 'sitemap_page' ) && isPage && (
				<PanelBody
					title={
						i18n.sitemapHideSection ||
						'Hide setting of HTML sitemap'
					}
					initialOpen={ false }
				>
					<CheckboxControl
						__nextHasNoMarginBottom
						label={
							i18n.sitemapHide ||
							'Hide this page to HTML Sitemap.'
						}
						checked={ isChecked( 'sitemap_hide' ) }
						onChange={ ( c ) =>
							update( 'sitemap_hide', c ? 'true' : '' )
						}
					/>
				</PanelBody>
			) }

			{ /* head タグ内の title タグ: priority 50 */ }
			{ isActive( 'wpTitle' ) && (
				<PanelBody
					title={ i18n.headTitleSection || 'Head Title (Title tag)' }
					initialOpen={ false }
				>
					<TextControl
						__next40pxDefaultSize
						label={ i18n.headTitle || 'Head Title' }
						value={ headTitleObject?.title || '' }
						onChange={ ( v ) => updateHeadTitle( 'title', v ) }
						help={
							i18n.headTitleHelp
								? i18n.headTitleHelp +
								  ( i18n.headTitleHelp2
										? ' ' + i18n.headTitleHelp2
										: '' )
								: undefined
						}
					/>
					<CheckboxControl
						__nextHasNoMarginBottom
						label={
							i18n.addSiteTitle || 'Add Separator and Site Title'
						}
						checked={ !! headTitleObject?.add_site_title }
						onChange={ ( c ) =>
							updateHeadTitle(
								'add_site_title',
								c ? 'checked' : ''
							)
						}
					/>
				</PanelBody>
			) }

			{ /* 自動アイキャッチ: priority 50 */ }
			{ isActive( 'auto_eyecatch' ) && (
				<PanelBody
					title={ i18n.eyecatchSection || 'Automatic EyeCatch' }
					initialOpen={ false }
				>
					<CheckboxControl
						__nextHasNoMarginBottom
						label={
							i18n.eyecatchHide ||
							'Do not set eyecatch image automatic.'
						}
						checked={ isChecked( 'vkExUnit_EyeCatch_disable' ) }
						onChange={ ( c ) =>
							update(
								'vkExUnit_EyeCatch_disable',
								c ? 'true' : ''
							)
						}
					/>
				</PanelBody>
			) }

			{ /* CTA設定: priority 50, 全投稿タイプ（ただしCTA投稿タイプ自身は除く） */ }
			{ isActive( 'call_to_action' ) && ! isCta && (
				<PanelBody
					title={ i18n.ctaSection || 'Call to Action setting' }
					initialOpen={ false }
				>
					<SelectControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ i18n.ctaSection || 'Call to Action setting' }
						value={ meta?.vkexunit_cta_each_option ?? '0' }
						options={ ctaOptions }
						onChange={ ( v ) =>
							update( 'vkexunit_cta_each_option', v )
						}
					/>
					<p
						style={ {
							display: 'flex',
							gap: '8px',
							flexWrap: 'wrap',
							marginTop: '8px',
						} }
					>
						{ ctaSettingUrl && (
							<ExternalLink href={ ctaSettingUrl }>
								{ i18n.ctaCommonSetting ||
									'CTA common setting' }
							</ExternalLink>
						) }
						<ExternalLink href={ ctaIndexUrl }>
							{ i18n.ctaIndexPage || 'Show CTA index page' }
						</ExternalLink>
					</p>
				</PanelBody>
			) }

			{ /* ページリストからの除外: priority 60, 固定ページのみ */ }
			{ isActive( 'page_exclude_from_list_pages' ) && isPage && (
				<PanelBody
					title={
						i18n.pageExcludeSection ||
						'Exclusion settings from the page list'
					}
					initialOpen={ false }
				>
					<CheckboxControl
						__nextHasNoMarginBottom
						label={
							i18n.pageExclude ||
							'Exclude from displaying Page List (wp_list_pages)'
						}
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

			{ /* カスタムCSS: priority 100 */ }
			{ isActive( 'css_customize' ) && (
				<PanelBody
					title={ i18n.cssSection || 'Custom CSS' }
					initialOpen={ false }
				>
					<TextareaControl
						__nextHasNoMarginBottom
						label={ i18n.customCss || 'Custom CSS' }
						value={ meta?._veu_custom_css || '' }
						onChange={ ( v ) => update( '_veu_custom_css', v ) }
						rows={ 5 }
					/>
				</PanelBody>
			) }

			{ /* CTA投稿タイプ自身の編集UI（既存実装を維持） */ }
			{ isCta && (
				<>
					<PanelBody
						title={ ctaI18n.ctaImage || 'CTA image' }
						initialOpen={ true }
					>
						<CheckboxControl
							__nextHasNoMarginBottom
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
							__nextHasNoMarginBottom
							__next40pxDefaultSize
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
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ ctaI18n.buttonText || 'Button text' }
							value={ meta?.vkExUnit_cta_button_text || '' }
							onChange={ ( v ) =>
								update( 'vkExUnit_cta_button_text', v )
							}
						/>
						<TextControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ ctaI18n.ctaUrl || 'URL' }
							value={ meta?.vkExUnit_cta_url || '' }
							onChange={ ( v ) =>
								update( 'vkExUnit_cta_url', v )
							}
							type="url"
						/>
						<CheckboxControl
							__nextHasNoMarginBottom
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
							__nextHasNoMarginBottom
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
