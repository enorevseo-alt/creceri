(function () {
  const { registerBlockType } = wp.blocks;
  const { __ } = wp.i18n;
  const be = wp.blockEditor || wp.editor;
  const { RichText, MediaUpload, InspectorControls, ColorPalette } = be;
  const { PanelBody, Button, TextControl } = wp.components;
  const el = wp.element.createElement;
  const { Fragment } = wp.element;

  registerBlockType('creceri/sample-card', {
    title: __('Sample Card', 'vite-ttf-child-creceri'),
    icon: 'id',
    category: 'design',
    description: __('A simple card with image, title, text, and button1.', 'vite-ttf-child-creceri'),
    supports: { align: ['wide', 'full'], html: false },

    attributes: {
      title: { type: 'string', source: 'html', selector: '.sample-card__title' },
      text: { type: 'string', source: 'html', selector: '.sample-card__text' },
      imageUrl: { type: 'string', default: '' },
      imageAlt: { type: 'string', default: '' },
      buttonText: { type: 'string', source: 'html', selector: '.sample-card__button' },
      buttonUrl: { type: 'string', default: '' },
      backgroundColor: { type: 'string', default: '#ffffff' }
    },

    edit: (props) => {
      const { attributes, setAttributes, className } = props;
      const { title, text, imageUrl, imageAlt, buttonText, buttonUrl, backgroundColor } = attributes;

      const onSelectImage = (media) => {
        if (!media || !media.url) return;
        setAttributes({ imageUrl: media.url, imageAlt: media.alt || '' });
      };

      return el(
        Fragment,
        null,

        // Inspector
        el(
          InspectorControls,
          null,
          el(
            PanelBody,
            { title: __('Card Settings', 'vite-ttf-child-creceri'), initialOpen: true },
            el(TextControl, {
              label: __('Button URL', 'vite-ttf-child-creceri'),
              placeholder: 'https://example.com',
              value: buttonUrl || '',
              onChange: (val) => setAttributes({ buttonUrl: val })
            }),
            el(
              'div',
              { style: { marginTop: '12px' } },
              el('label', { style: { display: 'block', marginBottom: '6px' } }, __('Background color', 'vite-ttf-child-creceri')),
              el(ColorPalette, {
                value: backgroundColor,
                onChange: (color) => setAttributes({ backgroundColor: color || '#ffffff' })
              })
            )
          )
        ),

        // Block UI
        el(
          'div',
          { className: (className || '') + ' sample-card', style: { backgroundColor: backgroundColor || '#ffffff' } },

          // Media
          el(
            'div',
            { className: 'sample-card__media' },
            imageUrl
              ? el('img', { src: imageUrl, alt: imageAlt || '' })
              : el(MediaUpload, {
                  onSelect: onSelectImage,
                  allowedTypes: ['image'],
                  render: ({ open }) => el(Button, { onClick: open, variant: 'secondary' }, __('Select image', 'vite-ttf-child-creceri'))
                })
          ),

          // Content
          el(
            'div',
            { className: 'sample-card__content' },

            el(RichText, {
              tagName: 'h3',
              className: 'sample-card__title',
              placeholder: __('Add title…', 'vite-ttf-child-creceri'),
              value: title,
              onChange: (val) => setAttributes({ title: val })
            }),

            el(RichText, {
              tagName: 'p',
              className: 'sample-card__text',
              placeholder: __('Write something…', 'vite-ttf-child-creceri'),
              value: text,
              onChange: (val) => setAttributes({ text: val })
            }),

            el(
              'div',
              { className: 'sample-card__actions' },
              el(RichText, {
                tagName: 'a',
                className: 'sample-card__button',
                placeholder: __('Button text…', 'vite-ttf-child-creceri'),
                value: buttonText,
                onChange: (val) => setAttributes({ buttonText: val }),
                href: buttonUrl || '#',
                onClick: (e) => e.preventDefault()
              })
            )
          )
        )
      );
    },

    save: (props) => {
      const { title, text, imageUrl, imageAlt, buttonText, buttonUrl, backgroundColor } = props.attributes;

      return el(
        'div',
        { className: 'sample-card', style: { backgroundColor: backgroundColor || '#ffffff' } },

        imageUrl &&
          el(
            'div',
            { className: 'sample-card__media' },
            el('img', { src: imageUrl, alt: imageAlt || '' })
          ),

        el(
          'div',
          { className: 'sample-card__content' },

          el(RichText.Content, {
            tagName: 'h3',
            className: 'sample-card__title',
            value: title
          }),

          el(RichText.Content, {
            tagName: 'p',
            className: 'sample-card__text',
            value: text
          }),

          (buttonText || buttonUrl) &&
            el(
              'div',
              { className: 'sample-card__actions' },
              el(
                'a',
                { className: 'sample-card__button', href: buttonUrl || '#', target: '_self', rel: 'noopener' },
                buttonText || ''
              )
            )
        )
      );
    }
  });
})();
