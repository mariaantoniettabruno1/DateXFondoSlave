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
    name: "search",
    props: ["options", "api", "element"],
    computed: {
      getPlaceholder() {
        return this.options.placeholder_text || "Search for articles";
      },
      getButtonText() {
        return this.options.search_text || "Search";
      },
      showButton() {
        return this.options.show_button ? this.options.show_button : false;
      }
    },
    methods: {}
  };
  const _hoisted_1 = { class: "zb-el-search__form" };
  const _hoisted_2 = ["placeholder"];
  const _hoisted_3 = {
    key: 1,
    type: "hidden",
    name: "post_type",
    value: "product"
  };
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.renderSlot(_ctx.$slots, "start"),
      vue.createElementVNode("form", _hoisted_1, [
        vue.createElementVNode("input", {
          type: "text",
          maxlength: "30",
          name: "s",
          class: vue.normalizeClass(["zb-el-search__input", $props.api.getStyleClasses("input_styles")]),
          placeholder: $options.getPlaceholder
        }, null, 10, _hoisted_2),
        $options.showButton ? (vue.openBlock(), vue.createElementBlock("button", {
          key: 0,
          type: "submit",
          alt: "Search",
          class: vue.normalizeClass(["zb-el-search__submit", $props.api.getStyleClasses("button_styles")]),
          value: "Search"
        }, vue.toDisplayString($options.getButtonText), 3)) : vue.createCommentVNode("", true),
        $props.options.woocommerce ? (vue.openBlock(), vue.createElementBlock("input", _hoisted_3)) : vue.createCommentVNode("", true)
      ]),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var Search = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "search",
    component: Search
  });
})(zb.vue);
