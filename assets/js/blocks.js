(function (blocks, element, components, blockEditor, serverSideRender) {
  const el = element.createElement;
  const InspectorControls = blockEditor.InspectorControls;
  const MediaUpload = blockEditor.MediaUpload;
  const MediaUploadCheck = blockEditor.MediaUploadCheck;
  const TextControl = components.TextControl;
  const SelectControl = components.SelectControl;
  const RangeControl = components.RangeControl;
  const PanelBody = components.PanelBody;
  const Button = components.Button;
  const ServerSideRender = serverSideRender.default || serverSideRender;
  const data = window.sutigharBlockData || {};
  const categories = data.categories || [{ label: 'All products', value: '' }];

  function preview(name, attributes) {
    return el(ServerSideRender, { block: name, attributes: attributes });
  }

  blocks.registerBlockType('sutighar/hero', {
    title: 'Sutighar Hero',
    icon: 'cover-image',
    category: 'sutighar',
    attributes: {
      align: { type: 'string', default: 'full' },
      title: { type: 'string', default: 'Home of Quality Lungi' },
      subtitle: { type: 'string', default: 'Sutighar is the Home of Quality Lungi: hand-picked cotton, for everyday comfort.' },
      buttonText: { type: 'string', default: 'Browse All Lungi' },
      buttonUrl: { type: 'string', default: '' },
      imageId: { type: 'number', default: 0 },
      imageUrl: { type: 'string', default: '' },
      mobileImageId: { type: 'number', default: 0 },
      mobileImageUrl: { type: 'string', default: '' }
    },
    supports: { align: ['full'] },
    edit: function (props) {
      const a = props.attributes;
      const set = props.setAttributes;
      return el(
        element.Fragment,
        {},
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: 'Hero options', initialOpen: true },
            el(TextControl, { label: 'Headline', value: a.title, onChange: (value) => set({ title: value }) }),
            el(TextControl, { label: 'Subtitle', value: a.subtitle, onChange: (value) => set({ subtitle: value }) }),
            el(TextControl, { label: 'Button text', value: a.buttonText, onChange: (value) => set({ buttonText: value }) }),
            el(TextControl, { label: 'Button URL', value: a.buttonUrl, placeholder: data.shopUrl || '/shop/', onChange: (value) => set({ buttonUrl: value }) }),
            el(MediaUploadCheck, {}, el(MediaUpload, {
              onSelect: (media) => set({ imageId: media.id, imageUrl: media.url }),
              allowedTypes: ['image'],
              value: a.imageId,
              render: ({ open }) => el(Button, { variant: 'secondary', onClick: open }, a.imageUrl ? 'Replace hero image' : 'Choose hero image')
            })),
            el(MediaUploadCheck, {}, el(MediaUpload, {
              onSelect: (media) => set({ mobileImageId: media.id, mobileImageUrl: media.url }),
              allowedTypes: ['image'],
              value: a.mobileImageId,
              render: ({ open }) => el(Button, { variant: 'secondary', onClick: open }, a.mobileImageUrl ? 'Replace mobile hero image' : 'Choose mobile hero image')
            }))
          )
        ),
        preview('sutighar/hero', a)
      );
    },
    save: function () { return null; }
  });

  blocks.registerBlockType('sutighar/feature-cards', {
    title: 'Sutighar Feature Cards',
    icon: 'screenoptions',
    category: 'sutighar',
    attributes: {
      align: { type: 'string', default: 'full' },
      cardOne: { type: 'string', default: 'Hand-picked Collection' },
      cardTwo: { type: 'string', default: 'Easy Return' },
      cardThree: { type: 'string', default: 'National Delivery' },
      cardFour: { type: 'string', default: 'Safe Payment' }
    },
    supports: { align: ['full'] },
    edit: function (props) {
      const a = props.attributes;
      const set = props.setAttributes;
      return el(
        element.Fragment,
        {},
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: 'Feature labels', initialOpen: true },
            el(TextControl, { label: 'Card 1', value: a.cardOne, onChange: (value) => set({ cardOne: value }) }),
            el(TextControl, { label: 'Card 2', value: a.cardTwo, onChange: (value) => set({ cardTwo: value }) }),
            el(TextControl, { label: 'Card 3', value: a.cardThree, onChange: (value) => set({ cardThree: value }) }),
            el(TextControl, { label: 'Card 4', value: a.cardFour, onChange: (value) => set({ cardFour: value }) })
          )
        ),
        preview('sutighar/feature-cards', a)
      );
    },
    save: function () { return null; }
  });

  blocks.registerBlockType('sutighar/product-section', {
    title: 'Sutighar Product Section',
    icon: 'products',
    category: 'sutighar',
    attributes: {
      align: { type: 'string', default: 'full' },
      title: { type: 'string', default: 'New Arrival' },
      category: { type: 'string', default: '' },
      limit: { type: 'number', default: 8 },
      orderby: { type: 'string', default: 'date' },
      order: { type: 'string', default: 'DESC' },
      browseLabel: { type: 'string', default: 'Browse All' }
    },
    supports: { align: ['full'] },
    edit: function (props) {
      const a = props.attributes;
      const set = props.setAttributes;
      return el(
        element.Fragment,
        {},
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: 'Section options', initialOpen: true },
            el(TextControl, { label: 'Title', value: a.title, onChange: (value) => set({ title: value }) }),
            el(SelectControl, { label: 'Product category', value: a.category, options: categories, onChange: (value) => set({ category: value }) }),
            el(RangeControl, { label: 'Products to show', min: 1, max: 24, value: a.limit, onChange: (value) => set({ limit: value }) }),
            el(SelectControl, {
              label: 'Order by',
              value: a.orderby,
              options: [
                { label: 'Newest', value: 'date' },
                { label: 'Menu order', value: 'menu_order' },
                { label: 'Title', value: 'title' },
                { label: 'Price', value: 'price' },
                { label: 'Popularity', value: 'popularity' }
              ],
              onChange: (value) => set({ orderby: value })
            }),
            el(SelectControl, { label: 'Order', value: a.order, options: [{ label: 'Descending', value: 'DESC' }, { label: 'Ascending', value: 'ASC' }], onChange: (value) => set({ order: value }) }),
            el(TextControl, { label: 'Browse link label', value: a.browseLabel, onChange: (value) => set({ browseLabel: value }) })
          )
        ),
        preview('sutighar/product-section', a)
      );
    },
    save: function () { return null; }
  });
})(
  window.wp.blocks,
  window.wp.element,
  window.wp.components,
  window.wp.blockEditor,
  window.wp.serverSideRender
);
