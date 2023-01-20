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
    name: "social_share",
    props: ["options", "api", "element"],
    computed: {
      iconConfig() {
        return this.options.share_icon_group ? this.options.share_icon_group : [];
      }
    },
    methods: {
      getIcon(config) {
        if (config !== void 0) {
          return config.name;
        } else
          return "";
      }
    }
  };
  const _hoisted_1 = {
    key: 0,
    class: "zb-el-socialShare__label"
  };
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_ElementIcon = vue.resolveComponent("ElementIcon");
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.renderSlot(_ctx.$slots, "start"),
      (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($options.iconConfig, (item, index) => {
        return vue.openBlock(), vue.createElementBlock("a", {
          key: index,
          href: "#",
          class: vue.normalizeClass(["zb-el-socialShare__item", [
            $props.api.getStyleClasses("social_block"),
            { [`zb-el-socialShare__item--is-${$options.getIcon(item.icon)}`]: $options.getIcon(item.icon) }
          ]])
        }, [
          vue.createVNode(_component_ElementIcon, vue.mergeProps({ class: "zb-el-socialShare__icon" }, $props.api.getAttributesForTag("icon_styles"), {
            iconConfig: item.icon
          }), null, 16, ["iconConfig"]),
          item.icon_label ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_1, vue.toDisplayString(item.icon_label), 1)) : vue.createCommentVNode("", true)
        ], 2);
      }), 128)),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var SocialShare = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "social_share",
    component: SocialShare
  });
})(zb.vue);
