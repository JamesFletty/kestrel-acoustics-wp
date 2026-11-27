/**
 * Editor UI for kestrel/spec-strip: pick a product from a REST-backed select;
 * the preview is rendered by PHP via ServerSideRender.
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, SelectControl, ToggleControl } = wp.components;
	const { useSelect } = wp.data;
	const { __ } = wp.i18n;

	registerBlockType( 'kestrel/spec-strip', {
		edit( { attributes, setAttributes } ) {
			const products = useSelect( ( select ) => select( 'core' ).getEntityRecords( 'postType', 'ka_product', { per_page: 50, _fields: 'id,title' } ), [] ) || [];
			const options = [ { label: __( 'Flagship (automatic)', 'kestrel' ), value: 0 } ].concat(
				products.map( ( p ) => ( { label: p.title.rendered, value: p.id } ) )
			);
			return el(
				Fragment,
				null,
				el( InspectorControls, null,
					el( PanelBody, { title: __( 'Product', 'kestrel' ) },
						el( SelectControl, { label: __( 'Show specs for', 'kestrel' ), value: attributes.productId, options, onChange: ( v ) => setAttributes( { productId: parseInt( v, 10 ) || 0 } ) } ),
						el( ToggleControl, { label: __( 'Show product name', 'kestrel' ), checked: attributes.showName, onChange: ( showName ) => setAttributes( { showName } ) } )
					)
				),
				el( 'div', useBlockProps(), el( wp.serverSideRender, { block: 'kestrel/spec-strip', attributes } ) )
			);
		},
		save: () => null,
	} );
} )( window.wp );
