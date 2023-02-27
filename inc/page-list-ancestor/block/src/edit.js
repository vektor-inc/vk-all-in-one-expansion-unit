import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

export default function ContactSectionEdit( props ) {
	const { attributes } = props;
	const { className } = attributes;

	const blockProps = useBlockProps( {
		className: `veu_contact_section_block ${ className }`,
	} );

	return (
		<>
			<div { ...blockProps }>
				<ServerSideRender
					block="vk-blocks/page-list-ancestor"
					attributes={ attributes }
				/>
			</div>
		</>
	);
}
