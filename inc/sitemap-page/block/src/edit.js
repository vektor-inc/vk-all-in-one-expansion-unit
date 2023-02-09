import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

export default function SiteMapEdit( props ) {
	const { attributes } = props;
	const { className } = attributes;

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
