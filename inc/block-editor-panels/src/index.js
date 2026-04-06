import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { CheckboxControl, TextControl, TextareaControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';

const i18n = window.veuPanelI18n || {};

const VeuSettingsPanel = () => {
	const postType = useSelect( ( s ) => s( 'core/editor' ).getCurrentPostType(), [] );
	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	const update = ( key, value ) => setMeta( { ...meta, [ key ]: value } );
	const isChecked = ( key ) => meta?.[ key ] === 'true';

	return (
		<PluginDocumentSettingPanel
			name="veu-settings"
			title={ i18n.panelTitle || 'VK ExUnit' }
		>
			<CheckboxControl
				label={ i18n.snsHide || "Don't display sns share button" }
				checked={ isChecked( 'sns_share_botton_hide' ) }
				onChange={ ( c ) => update( 'sns_share_botton_hide', c ? 'true' : '' ) }
			/>
			<TextControl
				label={ i18n.snsTitle || 'SNS Title' }
				value={ meta?.vkExUnit_sns_title || '' }
				onChange={ ( v ) => update( 'vkExUnit_sns_title', v ) }
				help={ i18n.snsTitleHelp || '' }
			/>
			<CheckboxControl
				label={ i18n.noindexLabel || 'noindex' }
				checked={ isChecked( '_vk_print_noindex' ) }
				onChange={ ( c ) => update( '_vk_print_noindex', c ? 'true' : '' ) }
			/>
			<CheckboxControl
				label={ i18n.sitemapHide || "Don't display on sitemap" }
				checked={ isChecked( 'sitemap_hide' ) }
				onChange={ ( c ) => update( 'sitemap_hide', c ? 'true' : '' ) }
			/>
			<TextControl
				label={ i18n.headTitle || 'Head Title' }
				value={ meta?.veu_head_title || '' }
				onChange={ ( v ) => update( 'veu_head_title', v ) }
				help={ i18n.headTitleHelp || '' }
			/>
			<CheckboxControl
				label={ i18n.eyecatchHide || "Don't display eyecatch" }
				checked={ isChecked( 'vkExUnit_EyeCatch_disable' ) }
				onChange={ ( c ) => update( 'vkExUnit_EyeCatch_disable', c ? 'true' : '' ) }
			/>
			<CheckboxControl
				label={ i18n.promotionAlert || 'Display promotion alert' }
				checked={ isChecked( 'veu_display_promotion_alert' ) }
				onChange={ ( c ) => update( 'veu_display_promotion_alert', c ? 'true' : '' ) }
			/>
			{ postType === 'page' && (
				<CheckboxControl
					label={ i18n.pageExclude || 'Exclude from page list' }
					checked={ isChecked( '_exclude_from_list_pages' ) }
					onChange={ ( c ) => update( '_exclude_from_list_pages', c ? 'true' : '' ) }
				/>
			) }
			<TextareaControl
				label={ i18n.customCss || 'Custom CSS' }
				value={ meta?._veu_custom_css || '' }
				onChange={ ( v ) => update( '_veu_custom_css', v ) }
				rows={ 4 }
			/>
		</PluginDocumentSettingPanel>
	);
};

registerPlugin( 'veu-settings-panel', { render: VeuSettingsPanel } );
