import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { useEffect } from '@wordpress/element';

export default function SiteMapEdit( props ) {
	const { attributes } = props;
	const { className } = attributes;

	// iframe がある場合それを取得
	const iframe = document.querySelector( '.block-editor__container iframe' );
	// iframe の中の document を取得
	const iframeDoc = iframe?.contentWindow?.document;

	// エディターのルート要素を取得
	const editorRoot =
		iframeDoc?.querySelector( '.block-editor-block-list__layout' ) ||
		document.querySelector( '.block-editor-block-list__layout' );

	useEffect( () => {
		if ( editorRoot ) {
			// サイトマップのリンクをクリックできないようにする
			const sitemapLinks = editorRoot.querySelectorAll(
				'.veu_sitemap_block .veu_sitemap'
			);
			sitemapLinks.forEach( ( link ) => {
				link.addEventListener( 'click', function ( event ) {
					event.preventDefault();
					link.style.cursor = 'default';
					link.style.boxShadow = 'unset';

					// ホバー効果を無効化
					link.style.color = 'inherit';
					link.style.textDecorationColor = 'inherit';
					link.style.pointerEvents = 'none';
				} );
				link.addEventListener( 'mouseover', function ( event ) {
					event.preventDefault();
					link.style.cursor = 'default';
					link.style.boxShadow = 'unset';

					// ホバー効果を無効化
					link.style.color = 'inherit';
					link.style.textDecorationColor = 'inherit';
					link.style.pointerEvents = 'none';
				} );
			} );
		}
	}, [ editorRoot ] );

	const blockProps = useBlockProps( {
		className: `veu_sitemap_block ${ className }`,
	} );

	return (
		<>
			<div { ...blockProps }>
				<ServerSideRender
					block="vk-blocks/sitemap"
					attributes={ attributes }
				/>
			</div>
		</>
	);
}
