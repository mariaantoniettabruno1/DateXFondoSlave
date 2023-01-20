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
    name: "pricing_box",
    props: ["options", "element", "api"],
    computed: {
      pricingPrice() {
        return this.options.price ? this.options.price.split(".")[0] : null;
      },
      priceFloat() {
        let floatValue = this.options.price ? this.options.price.split(".")[1] : null;
        return floatValue;
      }
    }
  };
  const _hoisted_1 = { class: "zb-el-pricingBox-content" };
  const _hoisted_2 = { class: "zb-el-pricingBox-heading" };
  const _hoisted_3 = { class: "zb-el-pricingBox-description" };
  const _hoisted_4 = { class: "zb-el-pricingBox-plan-price" };
  const _hoisted_5 = { class: "zb-el-pricingBox-price" };
  const _hoisted_6 = { class: "zb-el-pricingBox-price-dot" };
  const _hoisted_7 = { class: "zb-el-pricingBox-price-float" };
  const _hoisted_8 = { class: "zb-el-pricingBox-period" };
  const _hoisted_9 = ["innerHTML"];
  const _hoisted_10 = ["href", "title", "target"];
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_RenderValue = vue.resolveComponent("RenderValue");
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.renderSlot(_ctx.$slots, "start"),
      $props.options.plan_featured === "featured" ? (vue.openBlock(), vue.createElementBlock("span", vue.mergeProps({
        key: 0,
        class: ["zb-el-pricingBox-featured", $props.api.getStyleClasses("featured_label_styles")]
      }, $props.api.getAttributesForTag("featured_label_styles")), vue.toDisplayString(_ctx.$translate("featured")), 17)) : vue.createCommentVNode("", true),
      vue.createElementVNode("div", _hoisted_1, [
        vue.createElementVNode("div", _hoisted_2, [
          vue.createElementVNode("h3", vue.mergeProps({
            class: ["zb-el-pricingBox-title", $props.api.getStyleClasses("title_styles")]
          }, $props.api.getAttributesForTag("title_styles")), [
            vue.createVNode(_component_RenderValue, { option: "plan_title" })
          ], 16),
          vue.createElementVNode("p", _hoisted_3, [
            vue.createVNode(_component_RenderValue, { option: "plan_description" })
          ])
        ]),
        vue.createElementVNode("div", _hoisted_4, [
          vue.createElementVNode("span", _hoisted_5, [
            vue.createElementVNode("span", vue.mergeProps({
              class: ["zb-el-pricingBox-price-price", $props.api.getStyleClasses("price_styles")]
            }, $props.api.getAttributesForTag("price_styles")), [
              vue.createTextVNode(vue.toDisplayString($options.pricingPrice || "$999"), 1),
              vue.createElementVNode("span", _hoisted_6, vue.toDisplayString($options.priceFloat ? "." : ""), 1)
            ], 16),
            vue.createElementVNode("span", _hoisted_7, vue.toDisplayString($props.options.price && $props.options.price.split(".").length > 1 ? $options.priceFloat : null), 1)
          ]),
          vue.createElementVNode("span", _hoisted_8, [
            vue.createVNode(_component_RenderValue, { option: "period" })
          ])
        ]),
        $props.options.plan_details ? (vue.openBlock(), vue.createElementBlock("div", vue.mergeProps({
          key: 0,
          class: ["zb-el-pricingBox-plan-features", $props.api.getStyleClasses("features_styles")],
          innerHTML: $props.options.plan_details
        }, $props.api.getAttributesForTag("features_styles")), null, 16, _hoisted_9)) : vue.createCommentVNode("", true),
        $props.options.button_link && $props.options.button_link.link ? (vue.openBlock(), vue.createElementBlock("a", vue.mergeProps({
          key: 1,
          href: $props.options.button_link.link,
          title: $props.options.button_link.title,
          target: $props.options.button_link.target
        }, $props.api.getAttributesForTag("button_styles"), {
          class: ["zb-el-pricingBox-action zb-el-button", $props.api.getStyleClasses("button_styles")]
        }), [
          vue.createVNode(_component_RenderValue, { option: "button_text" })
        ], 16, _hoisted_10)) : (vue.openBlock(), vue.createElementBlock("div", vue.mergeProps({
          key: 2,
          class: ["zb-el-pricingBox-action zb-el-button", $props.api.getStyleClasses("button_styles")]
        }, $props.api.getAttributesForTag("button_styles")), [
          vue.createVNode(_component_RenderValue, { option: "button_text" })
        ], 16))
      ]),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var PricingBox = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "pricing_box",
    component: PricingBox
  });
})(zb.vue);
