import { registerPlugin } from '@wordpress/plugins';
import render from './components/pppDocumentSettings';

registerPlugin(
	'pay-per-post-plugin-sidebar',
	{
		icon: 'visibility',
		render,
	}
);
