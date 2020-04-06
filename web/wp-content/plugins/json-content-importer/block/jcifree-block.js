( function( editor, components, i18n, element ) {
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
		title: i18n.__( 'JSON Content Importer FREE' ),
		description: i18n.__( 'Block with API-data' ), 
 		icon: 'welcome-add-page', 
		category: 'widgets', 
		attributes: { 
			apiURL: {
				type: 'string',
				default: '/json-content-importer/json/gutenbergblockexample1.json',
			},
			template: {
				type: 'string',
				default: 'start: {start}<br>\n{subloop-array:level2:-1}\nlevel2: {level2.key}\n<br>{subloop:level2.data:-1}\nid: {level2.data.id}, type: {level2.data.type}<br>\n{/subloop:level2.data}\n{/subloop-array:level2}',
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
						title: i18n.__( 'Define API-URL and template to insert in block' ),
						className: 'jci_free_block',
						initialOpen: true,
					},
						el( TextControl, {
							type: 'string',
							label: i18n.__( 'API-URL:' ),
							help: i18n.__( 'if empty try: e1 for "example 1"' ),
							placeholder:  i18n.__( 'if empty try: e1' ),		
							value: apiURL,
							onChange: function( newapiURL ) {
								props.setAttributes( { apiURL: newapiURL } );
							},
						} ),
    					/*
						el( RangeControl, {
							label: i18n.__( 'No of items (-1: all):' ),
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
							label: i18n.__( 'Debugmode on / off:' ),
							checked : !!toggleswitch,
							onChange: function( newtoggleswitch ) {
								props.setAttributes( { toggleswitch: newtoggleswitch } );
							},
						} ), 
						el( ToggleControl, { // https://wordpress.org/gutenberg/handbook/components/toggle-control/
							type: 'string',
							label: i18n.__( 'Exampletext on / off:' ),
							//help : i18n.__( 'help' ),
							checked : !!toggleswitchexample,
							onChange: function( newtoggleswitchexample ) {
								props.setAttributes( { toggleswitchexample: newtoggleswitchexample } );
							},
						} ), 
    					el( TextControl, {
							type: 'string',
							label: i18n.__( 'Basenode (JSON-node to start):' ),
							help : i18n.__( 'if empty and initial example URL: level1' ),
							placeholder:  i18n.__( 'if empty and above example URL: level1' ),		
							value: basenode,
							onChange: function( newBasenode ) {
								props.setAttributes( { basenode: newBasenode } );
							},
						} ),
						el( TextareaControl, {
							type: 'string',
							label: i18n.__( 'Template to use for JSON:' ),
							placeholder:  i18n.__( 'if emtpy: Version: {version}, {downloaded} Downloads, {num_ratings} Ratings: {rating}, {subloop:tags:-1}tag: {tags.json}{/subloop:tags}' ),		
							help : i18n.__( 'Use {subloop}, {subloop-array}, {field} etc. just like in the shortcode' ),
							rows: 10,
							value: template,
							onChange: function( newTemplate ) {
								props.setAttributes( { template: newTemplate } );
							},
						} ),
					),
					el( components.PanelBody, {
						title: i18n.__( 'JCI Advanced' ),
						className: 'jci_free_block',
						initialOpen: false,
					},
    					el( TextControl, {
							type: 'string',
							label: i18n.__( 'Number of seconds waiting for the API:' ),
							placeholder:  i18n.__( 'default: 5 seconds' ),		
							//help : i18n.__( 'Number of seconds waiting for the API' ),
							value: urlgettimeout,
							onChange: function( newurlgettimeout ) {
								props.setAttributes( { urlgettimeout: newurlgettimeout } );
							},
						} ),
    					el( TextControl, {
							type: 'string',
							label: i18n.__( 'Number of json-top-level-items to display:' ),
							placeholder:  i18n.__( 'default: all' ),		
							value: numberofdisplayeditems,
							onChange: function( newnumberofdisplayeditems ) {
								props.setAttributes( { numberofdisplayeditems: newnumberofdisplayeditems } );
							},
						} ),
						el( TextControl, {
							type: 'string',
							label: i18n.__( 'One of these words must be displayed:' ),
							placeholder:  i18n.__( 'default: empty' ),		
							value: oneofthesewordsmustbein,
							onChange: function( newoneofthesewordsmustbein ) {
								props.setAttributes( { oneofthesewordsmustbein: newoneofthesewordsmustbein } );
							},
						} ),
    					el( TextControl, {
							type: 'string',
							label: i18n.__( 'JSON-depth of the above displayed Words:' ),
							placeholder:  i18n.__( 'default: empty' ),		
							value: oneofthesewordsmustbeindepth,
							onChange: function( newoneofthesewordsmustbeindepth ) {
								props.setAttributes( { oneofthesewordsmustbeindepth: newoneofthesewordsmustbeindepth } );
							},
						} ),
    					el( TextControl, {
							type: 'string',
							label: i18n.__( 'NONE of these words must be displayed:' ),
							placeholder:  i18n.__( 'default: empty' ),		
							value: oneofthesewordsmustnotbein,
							onChange: function( newoneofthesewordsmustnotbein ) {
								props.setAttributes( { oneofthesewordsmustnotbein: newoneofthesewordsmustnotbein } );
							},
						} ),
    					el( TextControl, {
							type: 'string',
							label: i18n.__( 'JSON-depth of the above NOT displayed Words:' ),
							placeholder:  i18n.__( 'default: empty' ),		
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
