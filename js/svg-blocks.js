/*
 * Gutenberg block Javascript code
 */
   // var __                = wp.i18n.__; // The __() function for internationalization.
    var createElement     = wp.element.createElement; // The wp.element.createElement() function to create elements.
    var registerBlockType = wp.blocks.registerBlockType; // The registerBlockType() function to register blocks.
	
	/**
     * Register block
     *
     * @param  {string}   name     Block name.
     * @param  {Object}   settings Block settings.
     * @return {?WPBlock}          Block itself, if registered successfully,
     *                             otherwise "undefined".
     */
    registerBlockType(
		'formater-wp-pack/svg-block', // Block name. Must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.	
        {
            title: __( 'SVG File' ), // Block title. __() function allows for internationalization.
            icon: 'media-document', // Block icon from Dashicons. https://developer.wordpress.org/resource/dashicons/.
			category: 'common', // Block category. Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
            attributes: {
				svgID: {
                    type: 'number'
                },
                src: {
                    type: 'string'
                },
                interactive: {
                	type: 'boolean',
                	default: true
                },
                class: {
                	type: 'string'
                },
                hide_button: {
                	type: 'boolean'
                }
            },

            // Defines the block within the editor.
            edit: function( props ) {
				
				var {attributes , setAttributes, focus, className} = props;
				var InspectorControls = wp.editor.InspectorControls;
				var MediaUpload = wp.editor.MediaUpload;
				var btn = wp.components.Button;
				var SelectControl = wp.components.SelectControl;
			    var CheckboxControl = wp.components.CheckboxControl;
				var onSelectSVG = function(media) {
					var align = '';
					var html = new DOMParser().parseFromString(media.compat.item, 'text/html');
					// var interactive = html.body.querySelector('[id*="fm_interactive"]');
                    return props.setAttributes({
                        src: media.url,
                        svgID: media.id,
                        interactive: true,
                        class: '',
                        hide_button: false
                    });
                }
			    function onChangeInteractive (v) {
			    	setAttributes({interactive:true});
			    }
				function onChangeClass (v) {
					setAttributes( {class: v});
				}
				function onChangeHideButton (v) {
						setAttributes({hide_button:v});
				}
				return [
					createElement(
                        MediaUpload,
                        {
                            onSelect: onSelectSVG,
                            type: 'image/svg+xml',
                            allowedTypes: ['image/svg+xml'],
                            value: attributes.svgID,
                            render: function(open) {
                                return createElement(btn,{onClick: open.open },
                                    attributes.src ? 'SVG: ' + attributes.src : 'Click here to Open Media Library to select SVG')
                            }
                        }
					),
										
					createElement( InspectorControls, { key: 'inspector' }, // Display the block options in the inspector pancreateElement.
						createElement('div',{ className: 'svg_div_main'}	,
							createElement(
								'hr',
								{},
							),
							createElement(
								CheckboxControl,
								{
									value: attributes.interactive,
								    onChange: onChangeInteractive
								}
							),
							createElement(
								SelectControl,
								{
									// label: __('Alignment'),
									label: 'Alignment',
									value: attributes.class,
									options: [
										{ label: 'No alignment', value: '' },
										{ label: 'Left', value: 'fm-left' },
										{ label: 'Right', value: 'fm-right' }
									],
									onChange: onChangeClass
								}
						    ),
						    createElement(
						    		CheckboxControl,
									{
										// label: __('Interactive'),
										label: 'Hide button enlarge/reduce',
										value: attributes.hide_button,
										checked: attributes.hide_button,
										onChange: onChangeHideButton
									}
						    )
						),
					),
                ];
            },

            // Defines the saved block.
            save: function( props ) {
				return '';
			},
        }
    );
