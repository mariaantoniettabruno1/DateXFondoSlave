(function(vue) {
  "use strict";
  var ProgressBars_vue_vue_type_style_index_0_lang = "";
  var _export_sfc = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
      target[key] = val;
    }
    return target;
  };
  const _sfc_main = {
    name: "ProgressBars",
    props: ["options", "element", "api"],
    data() {
      return {
        resetAnimation: false
      };
    },
    computed: {
      bars() {
        return this.options.bars || [];
      },
      barsWidth() {
        const barsWidth = (this.options.bars || []).map((item) => {
          return item.fill_percentage;
        });
        return barsWidth.join("");
      }
    },
    watch: {
      barsWidth() {
        this.doResetAnimation();
      },
      "options.transition_delay"() {
        this.doResetAnimation();
      }
    },
    mounted() {
      window.requestAnimationFrame(() => {
        this.runScript();
      });
    },
    methods: {
      doResetAnimation() {
        this.resetAnimation = true;
        this.runScript().then(() => {
          this.resetAnimation = false;
        });
      },
      runScript() {
        return new Promise((resolve, reject) => {
          window.requestAnimationFrame(() => {
            new window.zbScripts.progressBars(this.$el);
            resolve();
          });
        });
      }
    }
  };
  const _hoisted_1 = { class: "zb-el-progressBars__barTrack" };
  const _hoisted_2 = ["data-width"];
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createElementBlock("ul", {
      class: vue.normalizeClass({ "znpb-progressBars--resetAnimation": $data.resetAnimation })
    }, [
      vue.renderSlot(_ctx.$slots, "start"),
      (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($options.bars, (item, index) => {
        return vue.openBlock(), vue.createElementBlock("li", vue.mergeProps({
          key: index,
          class: ["zb-el-progressBars__singleBar", [`zb-el-progressBars__bar--${index}`]]
        }, $props.api.getAttributesForTag("single-bar", {}, index)), [
          item.title ? (vue.openBlock(), vue.createElementBlock("h5", vue.mergeProps({
            key: 0,
            class: ["zb-el-progressBars__barTitle", $props.api.getStyleClasses("title_styles")]
          }, $props.api.getAttributesForTag("title_styles")), vue.toDisplayString(item.title), 17)) : vue.createCommentVNode("", true),
          vue.createElementVNode("span", _hoisted_1, [
            vue.createElementVNode("span", {
              class: "zb-el-progressBars__barProgress",
              "data-width": item.fill_percentage !== void 0 ? item.fill_percentage : 50
            }, null, 8, _hoisted_2)
          ])
        ], 16);
      }), 128)),
      vue.renderSlot(_ctx.$slots, "end")
    ], 2);
  }
  var ProgressBars = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "progress_bars",
    component: ProgressBars
  });
})(zb.vue);
