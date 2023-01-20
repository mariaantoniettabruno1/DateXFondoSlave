(function(vue) {
  "use strict";
  var _export_sfc = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
      target[key] = val;
    }
    return target;
  };
  const _sfc_main = {
    name: "gallery",
    props: ["options", "element", "api"],
    computed: {
      getImages() {
        return this.options.images;
      },
      getWrapperAttributes() {
        if (this.options.use_modal) {
          return {
            "data-zion-lightbox": JSON.stringify({
              selector: ""
            })
          };
        }
        return {};
      }
    },
    methods: {
      getImageWrapperAttrs(image) {
        if (this.options.use_modal) {
          return {
            "data-src": image.image
          };
        }
        return {};
      }
    }
  };
  const _hoisted_1 = ["src"];
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createElementBlock("div", vue.normalizeProps(vue.guardReactiveProps($options.getWrapperAttributes)), [
      vue.renderSlot(_ctx.$slots, "start"),
      (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($options.getImages, (image, index) => {
        return vue.openBlock(), vue.createElementBlock("div", vue.mergeProps({
          class: ["zb-el-gallery-item", $props.api.getStyleClasses("image_wrapper_styles")],
          key: index
        }, $props.api.getAttributesForTag("image_wrapper_styles", $options.getImageWrapperAttrs(image))), [
          vue.createElementVNode("img", {
            src: image.image
          }, null, 8, _hoisted_1)
        ], 16);
      }), 128)),
      vue.renderSlot(_ctx.$slots, "end")
    ], 16);
  }
  var Gallery = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "gallery",
    component: Gallery
  });
})(zb.vue);
