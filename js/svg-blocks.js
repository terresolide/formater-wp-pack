/*
 * Gutenberg block Javascript code
 */
    var __                = wp.i18n.__; // The __() function for internationalization.
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
					
				var onSelectSVG = function(media) {
					var align = '';
					switch(media.align) {
					  case 'left':
					  case 'right':
						align = 'fmt-' + media.align;
						break;
					}
                    return props.setAttributes({
                        src: media.url,
                        svgID: media.id,
                        interactive: media.fm_interactive,
                        class: align
                    });
                }
				function onChangeInteractive(v) {
                    setAttributes( {interactive: v} );
                }
				function onChangeClass (v) {
					setAttributes( {class: v});
				}
				return [
					createElement(
                        MediaUpload,
                        {
                            onSelect: onSelectSVG,
                            type: 'application/svg+xml',
                            value: attributes.svgID,
                            render: function(open) {
                                return createElement(btn,{onClick: open.open },
                                    attributes.src ? 'SVG: ' + attributes.svg : 'Click here to Open Media Library to select SVG')
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
								__('Change SVG interactive'),
							),
							createElement(
									TextControl,
									{
										label: __('Interactive'),
										value: attributes.interactive,
										onChange: onChangeInteractive
									}
							),
							createElement(
									SelectControl,
										{
											label: __('Alignement'),
											value: attributes.toolbar,
											options: [
												{ label: 'Left', value: 'fmt-left' },
												{ label: 'Right', value: 'fmt-right' },
												{ label: 'No alignement', value: '' }
											],
											onChange: onChangeClass
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
