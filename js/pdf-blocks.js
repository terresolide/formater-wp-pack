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
		'formater-wp-pack/pdf-block', // Block name. Must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.	
        {
            title: __( 'PDF File' ), // Block title. __() function allows for internationalization.
            icon: 'media-document', // Block icon from Dashicons. https://developer.wordpress.org/resource/dashicons/.
			category: 'common', // Block category. Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
            attributes: {
				pdfID: {
                    type: 'number'
                },
                src: {
                    type: 'string'
                },
                rotate: {
                	type: 'string'
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
				var TextControl = wp.components.TextControl;
				var SelectControl = wp.components.SelectControl;
					
				var onSelectPDF = function(media) {
                    return props.setAttributes({
                        src: media.url,
                        pdfID: media.id,
                        rotate: '0'
                    });
                }
				function onChangeRotate(v) {
                    setAttributes( {rotate: v} );
                }
				
				return [
					createElement(
                        MediaUpload,
                        {
                            onSelect: onSelectPDF,
                            type: 'application/pdf',
                            allowedTypes: 'application/pdf',
                            value: attributes.pdfID,
                            render: function(open) {
                                return createElement(Button,{onClick: open.open },
                                    attributes.src ? 'PDF: ' + attributes.src : 'Click here to Open Media Library to select PDF')
                            }
                        }
					),
										
					createElement( InspectorControls, { key: 'inspector' }, // Display the block options in the inspector pancreateElement.
						createElement('div',{ className: 'pdf_div_main'}	,
							createElement(
								'hr',
								{},
							),
							createElement(
								'p',
								{},
								__('Change PDF rotation'),
							),
							createElement(
								SelectControl,
								{
									label: __('Rotation'),
									value: typeof attributes.rotate !== 'undefined' ? attributes.rotate: '0',
									onChange: onChangeRotate,
									options: [
										{ label: '-90', value: '-90' },
										{ label: '0', value: '0' },
										{ label: '90', value: '90' },
										{ label: '180', value: '180' }
									]
								}
							)
						),
					)
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
