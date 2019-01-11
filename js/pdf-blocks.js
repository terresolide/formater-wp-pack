/*
 * Gutenberg block Javascript code
 */
    var __                = wp.i18n.__; // The __() function for internationalization.
    var createElement     = wp.element.createElement; // The wp.element.createElement() function to create elements.
    var registerBlockType = wp.blocks.registerBlockType; // The registerBlockType() function to register blocks.
	
	
    var make_title_from_url = function(url) {
        var re = RegExp('/([^/]+?)(\\.pdf(\\?[^/]*)?)?$', 'i');
        var matches = url.match(re);
        if (matches.length >= 2) {
            return matches[1];
        }
        return url;
    }
    console.log('register block pdf');
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
            title: __( 'PDF Embedder' ), // Block title. __() function allows for internationalization.
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
					
				var onSelectPDF = function(media) {
                    return props.setAttributes({
                        src: media.url,
                        pdfID: media.id,
                        rotate: 0
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
                            value: attributes.pdfID,
                            render: function(open) {
                                return createElement(btn,{onClick: open.open },
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
									TextControl,
									{
										label: __('Rotation'),
										value: attributes.rotate,
										onChange: onChangeRotate
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
