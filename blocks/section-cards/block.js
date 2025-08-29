/* blocks/whats-new/block.js */
(function (wp) {
  const { registerBlockType } = wp.blocks;
  const { createElement: el, Fragment } = wp.element;
  const { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } = wp.blockEditor;
  const { PanelBody, TextControl, TextareaControl, Button, ToolbarGroup, ToolbarButton } = wp.components;

  const emptyItem = () => ({ image: "", heading: "", text: "", url: "" });

  registerBlockType('child/whats-new', {
    apiVersion: 3,
    title: "What's New (Cards)",
    icon: 'screenoptions',
    category: 'widgets',
    supports: { html: false, inserter: false }, // still hidden from Inserter, but editable in template
    edit: ({ attributes, setAttributes }) => {
      const { title, intro, ctaText, ctaUrl } = attributes;
      const items = Array.isArray(attributes.items) ? attributes.items : [];

      const bp = useBlockProps({ className: 'whats-new-editor' });

      const set = (patch) => setAttributes(patch);
      const updateItem = (index, patch) => {
        const next = [...items];
        next[index] = { ...next[index], ...patch };
        set({ items: next });
      };
      const addItem = () => set({ items: [...items, emptyItem()] });
      const removeItem = (i) => set({ items: items.filter((_, idx) => idx !== i) });
      const move = (from, to) => {
        if (to < 0 || to >= items.length) return;
        const next = [...items];
        const [itm] = next.splice(from, 1);
        next.splice(to, 0, itm);
        set({ items: next });
      };

      return el(Fragment, null, [
        // Sidebar controls
        el(InspectorControls, null,
          el(PanelBody, { title: 'Section' }, [
            el(TextControl, {
              label: 'Title',
              value: title || '',
              onChange: (v) => set({ title: v })
            }),
            el(TextareaControl, {
              label: 'Intro',
              value: intro || '',
              onChange: (v) => set({ intro: v })
            })
          ]),
          el(PanelBody, { title: 'CTA', initialOpen: false }, [
            el(TextControl, {
              label: 'Button text',
              value: ctaText || '',
              onChange: (v) => set({ ctaText: v })
            }),
            el(TextControl, {
              label: 'Button URL',
              value: ctaUrl || '',
              onChange: (v) => set({ ctaUrl: v })
            })
          ])
        ),

        // Canvas preview + inline card editors
        el('div', bp, [
          el('div', { className: 'wn-head' }, [
            title ? el('h2', { className: 'wn-title' }, title) : el('h2', { className: 'wn-title is-placeholder' }, 'Section title'),
            intro ? el('p', { className: 'wn-intro' }, intro) : null
          ]),

          el('div', { className: 'wn-grid' },
            (items.length ? items : [emptyItem(), emptyItem(), emptyItem(), emptyItem()]).map((it, i) =>
              el('div', { key: i, className: 'wn-card' }, [
                // Card toolbar (move/remove)
                el('div', { className: 'wn-card-toolbar' },
                  el(ToolbarGroup, null, [
                    el(ToolbarButton, { icon: 'arrow-up', label: 'Move up', onClick: () => move(i, i - 1), disabled: i === 0 }),
                    el(ToolbarButton, { icon: 'arrow-down', label: 'Move down', onClick: () => move(i, i + 1), disabled: i === items.length - 1 }),
                    el(ToolbarButton, { icon: 'trash', label: 'Remove', onClick: () => removeItem(i) })
                  ])
                ),

                // Image picker
                el('div', { className: 'wn-img-wrap' }, [
                  it.image
                    ? el('img', { src: it.image, alt: it.heading || '', className: 'wn-img' })
                    : el('div', { className: 'wn-img-skel' }),
                  el('div', { className: 'wn-img-actions' },
                    el(MediaUploadCheck, null,
                      el(MediaUpload, {
                        onSelect: (media) => updateItem(i, { image: media?.sizes?.large?.url || media?.url || '' }),
                        allowedTypes: ['image'],
                        render: ({ open }) => el(Button, { variant: 'secondary', size: 'small', onClick: open }, it.image ? 'Change' : 'Select image')
                      })
                    ),
                    it.image ? el(Button, { variant: 'tertiary', size: 'small', onClick: () => updateItem(i, { image: '' }) }, 'Clear') : null
                  )
                ]),

                // Text fields
                el('div', { className: 'wn-body' }, [
                  el(TextControl, {
                    label: 'Heading',
                    value: it.heading || '',
                    onChange: (v) => updateItem(i, { heading: v })
                  }),
                  el(TextareaControl, {
                    label: 'Text',
                    value: it.text || '',
                    onChange: (v) => updateItem(i, { text: v })
                  }),
                  el(TextControl, {
                    label: 'Link URL',
                    value: it.url || '',
                    onChange: (v) => updateItem(i, { url: v })
                  })
                ])
              ])
            )
          ),

          el('div', { className: 'wn-actions' },
            el(Button, { variant: 'primary', onClick: addItem }, 'Add card')
          ),

          (ctaText && ctaUrl)
            ? el('div', { className: 'wn-cta' }, el('span', { className: 'button is-secondary is-small' }, ctaText))
            : null
        ])
      ]);
    },
    save: () => null // dynamic (PHP render)
  });
})(window.wp);
