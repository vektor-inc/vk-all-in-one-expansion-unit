import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

export default function ShareButtonEdit( props ) {
	const { attributes } = props;
	const { className } = attributes;

	const blockProps = useBlockProps( {
		className: `veu_share_button_block ${ className }`,
	} );

	return (
		<>
			<div { ...blockProps }>
				<ServerSideRender
					block="vk-blocks/share-button"
					attributes={ attributes }
				/>
			</div>
		</>
	);
}
