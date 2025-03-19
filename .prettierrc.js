// Import the default config file and expose it in the project root.
// Useful for editor integrations.
module.exports = {
	...require( '@wordpress/prettier-config' ),
	proseWrap: 'preserve',
	markdownListMarkerSpace: 1,
};
