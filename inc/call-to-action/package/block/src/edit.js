import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { PanelBody, SelectControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

export default function CTAEdit( props ) {
	const { attributes, setAttributes } = props;
	const { postId } = attributes;

	useEffect( () => {
		// エディターキャンバス（記事プレビュー領域）の iframe だけを対象にする。
		// 本文中の外部埋め込み（YouTube 等）の iframe を誤って掴まないよう、
		// WordPress がエディターキャンバスに付与する name="editor-canvas" で絞り込む。
		// contentWindow.document は cross-origin の iframe に対して SecurityError を
		// 投げるため、例外を投げず null を返す contentDocument を使う。念のため
		// try/catch でも保護し、失敗時は document にフォールバックする。
		//
		// Only target the editor canvas iframe (the post preview area) by
		// scoping the selector to name="editor-canvas" (the name WordPress
		// assigns to it), so we never grab an embedded external iframe (e.g.
		// YouTube) in the post content. contentWindow.document throws
		// SecurityError for cross-origin iframes, so use contentDocument,
		// which returns null instead. Wrapped in try/catch as a safety net,
		// falling back to document on failure.
		let targetDoc = document;
		try {
			const canvas = document.querySelector(
				'iframe[name="editor-canvas"]'
			);
			targetDoc = canvas?.contentDocument || document;
		} catch ( e ) {
			targetDoc = document;
		}

		// eslint-disable-next-line no-undef
		const observer = new MutationObserver( () => {
			const editorRoot = targetDoc.querySelector(
				'.block-editor-block-list__layout'
			);
			if ( ! editorRoot ) {
				return;
			}

			const shareButtonLinks =
				editorRoot.querySelectorAll( '.veu-cta-block' );
			if ( shareButtonLinks.length === 0 ) {
				return;
			}

			shareButtonLinks.forEach( ( link ) => {
				if ( link.dataset.prevented ) {
					return;
				} // 二重適用防止

				link.dataset.prevented = 'true';
				link.addEventListener( 'click', function ( event ) {
					event.preventDefault();
					link.style.cursor = 'default';
					link.style.boxShadow = 'unset';
					link.style.color = 'inherit';
					link.style.textDecorationColor = 'inherit';
					link.style.pointerEvents = 'none';
				} );
				link.addEventListener( 'mouseover', function ( event ) {
					event.preventDefault();
					link.style.cursor = 'default';
					link.style.boxShadow = 'unset';
					link.style.color = 'inherit';
					link.style.textDecorationColor = 'inherit';
					link.style.pointerEvents = 'none';
				} );
			} );
		} );

		const observeTarget =
			targetDoc.querySelector( '.block-editor-block-list__layout' ) ||
			targetDoc.body;
		if ( observeTarget ) {
			observer.observe( observeTarget, {
				childList: true,
				subtree: true,
			} );
		}

		// クリーンアップ
		return () => {
			observer.disconnect();
		};
	}, [] );

	// eslint-disable-next-line
	const blockOption = veuBlockOption;

	// Make choice list of pages
	const options = blockOption.cta_option;
	const ctaPostsExist = blockOption.cta_posts_exist;
	const adminURL = blockOption.admin_url;

	let setting = '';
	if (
		wp.data.select( 'core/editor' ) &&
		wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' ) &&
		wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )
			.vkexunit_cta_each_option
	) {
		setting = wp.data
			.select( 'core/editor' )
			.getEditedPostAttribute( 'meta' ).vkexunit_cta_each_option;
	}

	let editContent;

	// If no CTA registered.
	if ( ctaPostsExist === 'false' ) {
		editContent = (
			<div className="veu-cta-block-edit-alert alert alert-warning">
				<div className="alert-title">
					{ __(
						'No CTA registered.',
						'vk-all-in-one-expansion-unit'
					) }
				</div>
				[{ ' ' }
				<a
					href={ adminURL + 'edit.php?post_type=cta' }
					target="_blank"
					rel="noopener noreferrer"
				>
					{ __( 'Register CTA', 'vk-all-in-one-expansion-unit' ) }
				</a>{ ' ' }
				]
			</div>
		);
		// If CTA is disabled.
	} else if ( setting === 'disable' ) {
		editContent = (
			<div className="veu-cta-block-edit-alert">
				{ __(
					'Because displaying CTA is disabled. The block render no content.',
					'vk-all-in-one-expansion-unit'
				) }
			</div>
		);
		// Normal.
	} else if ( postId !== '' && postId !== null && postId !== undefined ) {
		editContent = (
			<ServerSideRender block="vk-blocks/cta" attributes={ attributes } />
		);
		// New setqting.
	} else {
		editContent = (
			<div className="veu-cta-block-edit-alert alert alert-warning">
				{ __(
					'Please select CTA from Setting sidebar.',
					'vk-all-in-one-expansion-unit'
				) }
			</div>
		);
	}

	const blockProps = useBlockProps( {
		className: `veu-cta-block-edit`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __(
						'CTA Setting',
						'vk-all-in-one-expansion-unit'
					) }
					initialOpen={ true }
				>
					<SelectControl
						label={ __(
							'Select CTA',
							'vk-all-in-one-expansion-unit'
						) }
						id="veu-cta-block-select"
						value={ postId }
						options={ options }
						onChange={ ( value ) => {
							setAttributes( { postId: value } );
						} }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>{ editContent }</div>
		</>
	);
}
