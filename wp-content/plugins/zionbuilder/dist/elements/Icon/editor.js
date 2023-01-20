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
    name: "icon",
    props: ["options", "element", "api"],
    setup(props) {
      const hasLink = vue.computed(() => {
        return props.options.link && props.options.link.link && props.options.link.link !== "";
      });
      const iconStyle = vue.computed(() => {
        return props.options.style && props.options.style !== "" ? props.options.style : "default";
      });
      const iconConfig = vue.computed(() => {
        return props.options.icon || {
          "family": "Font Awesome 5 Free Regular",
          "name": "star",
          "unicode": "uf005"
        };
      });
      const iconUnicode = vue.computed(() => {
        const json = `"\\${iconConfig.value.unicode}"`;
        return JSON.parse(json).trim();
      });
      return {
        iconUnicode,
        hasLink,
        iconStyle,
        iconConfig
      };
    }
  };
  const _hoisted_1 = ["href", "target", "title", "data-znpbiconfam", "data-znpbicon"];
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_ElementIcon = vue.resolveComponent("ElementIcon");
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.renderSlot(_ctx.$slots, "start"),
      $setup.hasLink ? (vue.openBlock(), vue.createElementBlock("a", vue.mergeProps({
        key: 0,
        href: $props.options.link.link ? $props.options.link.link : null,
        target: $props.options.link.target ? $props.options.link.target : null,
        title: $props.options.link.title ? $props.options.link.title : null,
        class: "zb-el-icon-link zb-el-icon-icon",
        "data-znpbiconfam": $setup.iconConfig.family,
        "data-znpbicon": $setup.iconUnicode
      }, $props.api.getAttributesForTag("shape")), null, 16, _hoisted_1)) : (vue.openBlock(), vue.createBlock(_component_ElementIcon, vue.mergeProps({
        key: 1,
        class: "zb-el-icon-icon",
        iconConfig: $setup.iconConfig
      }, $props.api.getAttributesForTag("shape")), null, 16, ["iconConfig"])),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var Icon = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "icon",
    component: Icon
  });
})(zb.vue);
