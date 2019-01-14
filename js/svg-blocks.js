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
				pdfID: {
                    type: 'number'
                },
                src: {
                    type: 'string'
                },
                interactive: {
                	type: 'number'
                },
                class: {
                	type: 'string'
                },
                hide_button: {
                	type: 'number'
                }
            },

            // Defines the block within the editor.
            edit: function( props ) {
				
				var {attributes , setAttributes, focus, className} = props;
                	
				var InspectorControls = wp.editor.InspectorControls;
				var Button = wp.components.Button;
				var RichText = wp.editor.RichText;
				var Editable = wp.blocks.Editable; // Editable component of React.
				var MediaUpload = wp.editor.MediaUpload;
				var btn = wp.components.Button;
				var TextControl = wp.components.TextControl;
				var SelectControl = wp.components.SelectControl;
				var RadioControl = wp.components.RadioControl;
			    var CheckboxControl = wp.components.CheckboxControl;
			    
				var onSelectSVG = function(media) {
					var align = '';
					console.log(media);
					console.log(media.compat.item);
					
					var html = new DOMParser().parseFromString(media.compat.item, 'text/html');
					var interactive = html.body.querySelector('[id*="fm_interactive"]');
					console.log(interactive.value);
					
                    return props.setAttributes({
                        src: media.url,
                        svgID: media.id,
                        interactive: interactive.value,
                        class: '',
                        hide_button: true
                    });
                }
				function onChangeInteractive(v) {
                    setAttributes( {interactive: v} );
                }
				function onChangeClass (v) {
					setAttributes( {class: v});
				}
				function onChangeHideButton (v) {
					if (attributes.class in ['fmt-left', 'fmt-right']) {
						setAttributes({class:v});
					} else {
						setAttributes({class: true});
					}
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
								'p',
								{},
								'Change SVG interactive'
								//__('Change SVG interactive')
							),
							createElement(
								CheckboxControl,
								{
									// label: __('Interactive'),
									label: 'Interactive',
									value: attributes.interactive,
									checked: attributes.interactive,
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
										{ label: 'Left', value: 'fmt-left' },
										{ label: 'Right', value: 'fmt-right' }
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
				return createElement(
                    'p',
                    {
                        className: props.className,
						key: 'return-key',
                    },props.attributes.content);
			},
        }
    );
