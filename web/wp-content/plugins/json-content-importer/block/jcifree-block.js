( function( editor, components, i18n, element ) {
	const { __ } = wp.i18n;
	var el = element.createElement;
	var registerBlockType = wp.blocks.registerBlockType;
	var InspectorControls = wp.editor.InspectorControls;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var RangeControl = wp.components.RangeControl;
	var ServerSideRender = wp.components.ServerSideRender;
	var RadioControl = wp.components.RadioControl;
	var MenuItemsChoice  = wp.components.MenuItemsChoice;
	var ToggleControl = wp.components.ToggleControl;
  
	registerBlockType( 'jci/jcifree-block-script', { 
		title: __( 'JSON Content Importer FREE', 'json-content-importer'),
		description: __( 'Block with API-data', 'json-content-importer'), 
 		icon: 'welcome-add-page', 
		category: 'widgets', 
		attributes: { 
			apiURL: {
				type: 'string',
				default: '/json-content-importer/json/gutenbergblockexample1.json',
			},
			template: {
				type: 'string',
				default: 'start: {start}<br>{subloop-array:level2:-1}level2: {level2.key}<br>{subloop:level2.data:-1}id: {level2.data.id}, type: {level2.data.type}<br>{/subloop:level2.data}{/subloop-array:level2}',
			},
			basenode: {
				type: 'string',
				default: '',
			},
			urlgettimeout: {
				type: 'string',
				default: 5,
			},
			numberofdisplayeditems: {
				type: 'string',
			},
			oneofthesewordsmustbein: {
				type: 'string',
				//default: 'r',
			},
			oneofthesewordsmustbeindepth: {
				type: 'string',
				//default: 3,
			},
			oneofthesewordsmustnotbein: {
				type: 'string',
				//default: 'three',
			},
			oneofthesewordsmustnotbeindepth: {
				type: 'string',
				//default: 3,
			},
			toggleswitch: {
				type: 'boolean',
				default: false,
			},
			toggleswitchexample: {
				type: 'boolean',
				default: true,
			},
		},

		edit: function( props ) {
			var attributes = props.attributes;
			var apiURL = props.attributes.apiURL;
			var template = props.attributes.template;
			var basenode = props.attributes.basenode;
			//var noitems = props.attributes.noitems;
			var toggleswitch = props.attributes.toggleswitch;
			var toggleswitchexample = props.attributes.toggleswitchexample;
			var urlgettimeout = props.attributes.urlgettimeout;
			var numberofdisplayeditems = props.attributes.numberofdisplayeditems;
			var oneofthesewordsmustbein = props.attributes.oneofthesewordsmustbein;
			var oneofthesewordsmustbeindepth = props.attributes.oneofthesewordsmustbeindepth;
			var oneofthesewordsmustnotbein = props.attributes.oneofthesewordsmustnotbein;
			var oneofthesewordsmustnotbeindepth = props.attributes.oneofthesewordsmustnotbeindepth;
			
			return [
				el( InspectorControls, { key: 'inspector' },
					el( components.PanelBody, {
						title: __( 'Define API-URL and template to insert in block', 'json-content-importer' ),
						className: 'jci_free_block',
						initialOpen: true,
					},
						el( TextControl, {
							type: 'string',
							label: __( 'API-URL:', 'json-content-importer' ),
							help: __( 'if empty try: e1 for "example 1"', 'json-content-importer'),
							placeholder:  __( 'if empty try: e1', 'json-content-importer' ),		
							value: apiURL,
							onChange: function( newapiURL ) {
								props.setAttributes( { apiURL: newapiURL } );
							},
						} ),
    					/*
						el( RangeControl, {
							label: __( 'No of items (-1: all):', 'json-content-importer' ),
							initialPosition: 20,
							value: noitems,
							//columns: 6,
							min: -1,
							max: 5000,
							onChange: function( newnoitems ) {
								props.setAttributes( { noitems: newnoitems } );
							},
						} ),
						*/
						el( ToggleControl, { // https://wordpress.org/gutenberg/handbook/components/toggle-control/
							type: 'string',
							label: __( 'Debugmode on / off:', 'json-content-importer' ),
							checked : !!toggleswitch,
							onChange: function( newtoggleswitch ) {
								props.setAttributes( { toggleswitch: newtoggleswitch } );
							},
						} ), 
						el( ToggleControl, { // https://wordpress.org/gutenberg/handbook/components/toggle-control/
							type: 'string',
							label: __( 'Exampletext on / off:', 'json-content-importer' ),
							//help : i18n.__( 'help' ),
							checked : !!toggleswitchexample,
							onChange: function( newtoggleswitchexample ) {
								props.setAttributes( { toggleswitchexample: newtoggleswitchexample } );
							},
						} ), 
    					el( TextControl, {
							type: 'string',
							label: __( 'Basenode (JSON-node to start):', 'json-content-importer' ),
							help : __( 'if empty and initial example URL: level1', 'json-content-importer' ),
							placeholder:  __( 'if empty and above example URL: level1', 'json-content-importer' ),		
							value: basenode,
							onChange: function( newBasenode ) {
								props.setAttributes( { basenode: newBasenode } );
							},
						} ),
						el( TextareaControl, {
							type: 'string',
							label: __( 'Template to use for JSON:', 'json-content-importer' ),
							placeholder:  __( 'if emtpy: Version: {version}, {downloaded} Downloads, {num_ratings} Ratings: {rating}, {subloop:tags:-1}tag: {tags.json}{/subloop:tags}', 'json-content-importer' ),		
							help : __( 'Use {subloop}, {subloop-array}, {field} etc. just like in the shortcode', 'json-content-importer' ),
							rows: 10,
							value: template,
							onChange: function( newTemplate ) {
								props.setAttributes( { template: newTemplate } );
							},
						} ),
					),
					el( components.PanelBody, {
						title: __( 'JCI Advanced', 'json-content-importer' ),
						className: 'jci_free_block',
						initialOpen: false,
					},
    					el( TextControl, {
							type: 'string',
							label: __( 'Number of seconds waiting for the API:', 'json-content-importer' ),
							placeholder: __( 'default: 5 seconds', 'json-content-importer' ),		
							//help : __( 'Number of seconds waiting for the API', 'json-content-importer' ),
							value: urlgettimeout,
							onChange: function( newurlgettimeout ) {
								props.setAttributes( { urlgettimeout: newurlgettimeout } );
							},
						} ),
    					el( TextControl, {
							type: 'string',
							label: __( 'Number of json-top-level-items to display:', 'json-content-importer' ),
							placeholder:  __( 'default: all', 'json-content-importer' ),		
							value: numberofdisplayeditems,
							onChange: function( newnumberofdisplayeditems ) {
								props.setAttributes( { numberofdisplayeditems: newnumberofdisplayeditems } );
							},
						} ),
						el( TextControl, {
							type: 'string',
							label: __( 'One of these words must be displayed:', 'json-content-importer' ),
							placeholder:  __( 'default: empty', 'json-content-importer' ),		
							value: oneofthesewordsmustbein,
							onChange: function( newoneofthesewordsmustbein ) {
								props.setAttributes( { oneofthesewordsmustbein: newoneofthesewordsmustbein } );
							},
						} ),
    					el( TextControl, {
							type: 'string',
							label: __( 'JSON-depth of the above displayed Words:', 'json-content-importer' ),
							placeholder:  __( 'default: empty', 'json-content-importer' ),		
							value: oneofthesewordsmustbeindepth,
							onChange: function( newoneofthesewordsmustbeindepth ) {
								props.setAttributes( { oneofthesewordsmustbeindepth: newoneofthesewordsmustbeindepth } );
							},
						} ),
    					el( TextControl, {
							type: 'string',
							label: __( 'NONE of these words must be displayed:', 'json-content-importer' ),
							placeholder:  __( 'default: empty', 'json-content-importer' ),		
							value: oneofthesewordsmustnotbein,
							onChange: function( newoneofthesewordsmustnotbein ) {
								props.setAttributes( { oneofthesewordsmustnotbein: newoneofthesewordsmustnotbein } );
							},
						} ),
    					el( TextControl, {
							type: 'string',
							label: __( 'JSON-depth of the above NOT displayed Words:', 'json-content-importer' ),
							placeholder:  __( 'default: empty', 'json-content-importer' ),		
							value: oneofthesewordsmustnotbeindepth,
							onChange: function( newoneofthesewordsmustnotbeindepth ) {
								props.setAttributes( { oneofthesewordsmustnotbeindepth: newoneofthesewordsmustnotbeindepth } );
							},
						} ),
					),
				),
            el(ServerSideRender, {
                block: 'jci/jcifree-block-script',
                attributes:  props.attributes
            })
			];
		},
		
		save: function() {
			return null;
		},
	} );

} )(
	window.wp.editor,
	window.wp.components,
	window.wp.i18n,
	window.wp.element,
);
