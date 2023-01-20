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
    name: "testimonial",
    props: ["element", "options", "api"],
    computed: {
      image() {
        return this.options && this.options.image ? this.options.image : null;
      },
      content() {
        return this.options && this.options.content ? this.options.content : null;
      },
      name() {
        return this.options && this.options.name ? this.options.name : null;
      },
      description() {
        return this.options && this.options.description ? this.options.description : null;
      },
      getStar() {
        return {
          family: "Font Awesome 5 Free Solid",
          name: "star",
          unicode: "uf005"
        };
      },
      getEmptyStar() {
        return {
          family: "Font Awesome 5 Free Regular",
          name: "star",
          unicode: "uf005"
        };
      },
      stars() {
        return this.options.stars || 5;
      }
    }
  };
  const _hoisted_1 = ["src"];
  const _hoisted_2 = { class: "zb-el-testimonial__user" };
  const _hoisted_3 = ["src"];
  const _hoisted_4 = { class: "zb-el-testimonial__userInfo" };
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_RenderValue = vue.resolveComponent("RenderValue");
    const _component_ElementIcon = vue.resolveComponent("ElementIcon");
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.renderSlot(_ctx.$slots, "start"),
      $options.image && $props.options.position !== void 0 && $props.options.position === "top" ? (vue.openBlock(), vue.createElementBlock("img", vue.mergeProps({
        key: 0,
        class: ["zb-el-testimonial__userImage", $props.api.getStyleClasses("inner_content_styles_image")]
      }, $props.api.getAttributesForTag("inner_content_styles_image"), { src: $options.image }), null, 16, _hoisted_1)) : vue.createCommentVNode("", true),
      vue.createVNode(_component_RenderValue, vue.mergeProps({
        option: "content",
        class: ["zb-el-testimonial-content", $props.api.getStyleClasses("inner_content_styles_misc")]
      }, $props.api.getAttributesForTag("inner_content_styles_misc")), null, 16, ["class"]),
      vue.createElementVNode("div", _hoisted_2, [
        $options.image && $props.options.position !== void 0 && $props.options.position !== "top" ? (vue.openBlock(), vue.createElementBlock("img", vue.mergeProps({
          key: 0,
          class: ["zb-el-testimonial__userImage", $props.api.getStyleClasses("inner_content_styles_image")]
        }, $props.api.getAttributesForTag("inner_content_styles_image"), { src: $options.image }), null, 16, _hoisted_3)) : vue.createCommentVNode("", true),
        vue.createElementVNode("div", _hoisted_4, [
          vue.createVNode(_component_RenderValue, vue.mergeProps({
            option: "name",
            class: [$props.api.getStyleClasses("inner_content_styles_user"), "zb-el-testimonial__userInfo-name"]
          }, $props.api.getAttributesForTag("inner_content_styles_user")), null, 16, ["class"]),
          vue.createVNode(_component_RenderValue, vue.mergeProps({
            option: "description",
            class: [$props.api.getStyleClasses("inner_content_styles_description"), "zb-el-testimonial__userInfo-description"]
          }, $props.api.getAttributesForTag("inner_content_styles_description")), null, 16, ["class"]),
          $options.stars && $options.stars !== "no_stars" ? (vue.openBlock(), vue.createElementBlock("div", vue.mergeProps({
            key: 0,
            class: ["zb-el-testimonial__stars", $props.api.getStyleClasses("inner_content_styles_stars")]
          }, $props.api.getAttributesForTag("inner_content_styles_stars")), [
            (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($options.stars, (star, index) => {
              return vue.openBlock(), vue.createBlock(_component_ElementIcon, {
                class: "zb-el-testimonial__stars--full",
                key: index + 10,
                iconConfig: $options.getStar
              }, null, 8, ["iconConfig"]);
            }), 128)),
            (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(5 - $options.stars, (star) => {
              return vue.openBlock(), vue.createBlock(_component_ElementIcon, {
                key: star,
                iconConfig: $options.getEmptyStar
              }, null, 8, ["iconConfig"]);
            }), 128))
          ], 16)) : vue.createCommentVNode("", true)
        ])
      ]),
      vue.renderSlot(_ctx.$slots, "end")
    ]);
  }
  var Testimonial = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "testimonial",
    component: Testimonial
  });
})(zb.vue);
