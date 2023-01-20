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
    name: "ZionImage",
    props: ["element", "options", "api"],
    computed: {
      imageSrc() {
        return (this.options.image || {}).image;
      },
      hasLink() {
        return this.options.link && this.options.link.link;
      },
      extraAttributes() {
        const attributes = window.zb.utils.getLinkAttributes(this.options.link);
        if (this.options.use_modal) {
          attributes.href = this.imageSrc;
          attributes["data-zion-lightbox"] = true;
        }
        return attributes;
      }
    }
  };
  const _hoisted_1 = ["src"];
  const _hoisted_2 = ["src"];
  const _hoisted_3 = {
    key: 2,
    class: "zb-el-zionImage-caption"
  };
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.renderSlot(_ctx.$slots, "start"),
      $options.hasLink ? (vue.openBlock(), vue.createElementBlock("a", vue.mergeProps({ key: 0 }, $props.api.getAttributesForTag("link_styles", $options.extraAttributes), {
        class: $props.api.getStyleClasses("link_styles")
      }), [
        vue.createElementVNode("img", vue.mergeProps($props.api.getAttributesForTag("image_styles"), {
          src: $options.imageSrc,
          class: $props.api.getStyleClasses("image_styles")
        }), null, 16, _hoisted_1)
      ], 16)) : $options.imageSrc ? (vue.openBlock(), vue.createElementBlock("img", vue.mergeProps({ key: 1 }, $props.api.getAttributesForTag("image_styles", $options.extraAttributes), {
        src: $options.imageSrc,
        class: $props.api.getStyleClasses("image_styles")
      }), null, 16, _hoisted_2)) : vue.createCommentVNode("", true),
      $props.options.show_caption ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_3, vue.toDisplayString($props.options.caption_text), 1)) : vue.createCommentVNode("", true),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var Image = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "zion_image",
    component: Image
  });
})(zb.vue);
