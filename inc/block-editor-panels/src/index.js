import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { CheckboxControl, TextControl, TextareaControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';

const data = window.veuPanelData || {};
const i18n = data.i18n || {};
const activeFeatures = data.activeFeatures || [];

const isActive = ( feature ) => activeFeatures.includes( feature );

const VeuSettingsPanel = () => {
	const postType = useSelect( ( s ) => s( 'core/editor' ).getCurrentPostType(), [] );
	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	const update = ( key, value ) => setMeta( { ...meta, [ key ]: value } );
	const isChecked = ( key ) => meta?.[ key ] === 'true';

	// Don't render if no features are active.
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
						label={ i18n.snsHide || "Don't display sns share button" }
						checked={ isChecked( 'sns_share_botton_hide' ) }
						onChange={ ( c ) => update( 'sns_share_botton_hide', c ? 'true' : '' ) }
					/>
					<TextControl
						label={ i18n.snsTitle || 'SNS Title' }
						value={ meta?.vkExUnit_sns_title || '' }
						onChange={ ( v ) => update( 'vkExUnit_sns_title', v ) }
					/>
				</>
			) }
			{ isActive( 'noindex' ) && (
				<CheckboxControl
					label={ i18n.noindex || 'noindex' }
					checked={ isChecked( '_vk_print_noindex' ) }
					onChange={ ( c ) => update( '_vk_print_noindex', c ? 'true' : '' ) }
				/>
			) }
			{ isActive( 'sitemap_page' ) && (
				<CheckboxControl
					label={ i18n.sitemapHide || "Don't display on sitemap" }
					checked={ isChecked( 'sitemap_hide' ) }
					onChange={ ( c ) => update( 'sitemap_hide', c ? 'true' : '' ) }
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
					label={ i18n.eyecatchHide || "Don't display eyecatch" }
					checked={ isChecked( 'vkExUnit_EyeCatch_disable' ) }
					onChange={ ( c ) => update( 'vkExUnit_EyeCatch_disable', c ? 'true' : '' ) }
				/>
			) }
			{ isActive( 'promotion_alert' ) && (
				<CheckboxControl
					label={ i18n.promotionAlert || 'Display promotion alert' }
					checked={ isChecked( 'veu_display_promotion_alert' ) }
					onChange={ ( c ) => update( 'veu_display_promotion_alert', c ? 'true' : '' ) }
				/>
			) }
			{ isActive( 'page_exclude_from_list_pages' ) && postType === 'page' && (
				<CheckboxControl
					label={ i18n.pageExclude || 'Exclude from page list' }
					checked={ isChecked( '_exclude_from_list_pages' ) }
					onChange={ ( c ) => update( '_exclude_from_list_pages', c ? 'true' : '' ) }
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

registerPlugin( 'veu-settings-panel', { render: VeuSettingsPanel } );
