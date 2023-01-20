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
    name: "image_box",
    props: ["element", "options", "api"],
    computed: {
      imageSrc() {
        return (this.options.image || {}).image;
      },
      titleTag() {
        return this.options.title_tag || "h3";
      }
    }
  };
  const _hoisted_1 = { class: "zb-el-imageBox" };
  const _hoisted_2 = {
    key: 0,
    class: "zb-el-imageBox-imageWrapper"
  };
  const _hoisted_3 = ["src"];
  const _hoisted_4 = /* @__PURE__ */ vue.createElementVNode("span", { class: "zb-el-imageBox-spacer" }, null, -1);
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_RenderValue = vue.resolveComponent("RenderValue");
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1, [
      vue.renderSlot(_ctx.$slots, "\n		start"),
      $options.imageSrc ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_2, [
        vue.createElementVNode("img", vue.mergeProps({
          class: "zb-el-imageBox-image",
          src: $options.imageSrc
        }, $props.api.getAttributesForTag("image_styles"), {
          class: $props.api.getStyleClasses("image_styles")
        }), null, 16, _hoisted_3)
      ])) : vue.createCommentVNode("", true),
      _hoisted_4,
      vue.createElementVNode("div", {
        class: "zb-el-imageBox-text",
        style: vue.normalizeStyle({
          "text-align": $props.options.align
        })
      }, [
        $props.options.title ? (vue.openBlock(), vue.createBlock(vue.resolveDynamicComponent($options.titleTag), vue.mergeProps({
          key: 0,
          class: ["zb-el-imageBox-title", $props.api.getStyleClasses("title_styles")]
        }, $props.api.getAttributesForTag("title_styles"), {
          innerHTML: $props.options.title
        }), null, 16, ["class", "innerHTML"])) : vue.createCommentVNode("", true),
        $props.options.description ? (vue.openBlock(), vue.createElementBlock("div", vue.mergeProps({
          key: 1,
          class: ["zb-el-imageBox-description", $props.api.getStyleClasses("description_styles")]
        }, $props.api.getAttributesForTag("description_styles")), [
          vue.createVNode(_component_RenderValue, { option: "description" })
        ], 16)) : vue.createCommentVNode("", true)
      ], 4),
      vue.renderSlot(_ctx.$slots, "start")
    ]);
  }
  var ImageBox = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "image_box",
    component: ImageBox
  });
})(zb.vue);
