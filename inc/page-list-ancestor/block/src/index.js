/**
 * Child Page Index Block
 *
 */
import {
	registerBlockType,
	unstable__bootstrapServerSideBlockDefinitions, // eslint-disable-line camelcase
} from '@wordpress/blocks';
// import React
import { ReactComponent as Icon } from './icon.svg';

// import block files
import metadata from '../block.json';
import edit from './edit';

const { name } = metadata;
const settings = {
	icon: <Icon />,
	edit,
};
unstable__bootstrapServerSideBlockDefinitions( { [ name ]: metadata } );
registerBlockType( metadata, settings );
