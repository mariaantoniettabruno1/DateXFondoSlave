var __defProp = Object.defineProperty;
var __defProps = Object.defineProperties;
var __getOwnPropDescs = Object.getOwnPropertyDescriptors;
var __getOwnPropSymbols = Object.getOwnPropertySymbols;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __propIsEnum = Object.prototype.propertyIsEnumerable;
var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
var __spreadValues = (a, b) => {
  for (var prop in b || (b = {}))
    if (__hasOwnProp.call(b, prop))
      __defNormalProp(a, prop, b[prop]);
  if (__getOwnPropSymbols)
    for (var prop of __getOwnPropSymbols(b)) {
      if (__propIsEnum.call(b, prop))
        __defNormalProp(a, prop, b[prop]);
    }
  return a;
};
var __spreadProps = (a, b) => __defProps(a, __getOwnPropDescs(b));
(function(vue) {
  "use strict";
  const _hoisted_1 = { class: "swiper" };
  const _hoisted_2 = ["data-zion-slider-config"];
  const _hoisted_3 = { class: "swiper-wrapper" };
  const _hoisted_4 = {
    key: 0,
    ref: "paginationRef",
    class: "swiper-pagination"
  };
  const _hoisted_5 = /* @__PURE__ */ vue.createElementVNode("div", { class: "swiper-button-prev" }, null, -1);
  const _hoisted_6 = /* @__PURE__ */ vue.createElementVNode("div", { class: "swiper-button-next" }, null, -1);
  const __default__ = {
    name: "SliderBuilder"
  };
  const _sfc_main$1 = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__), {
    props: {
      options: null,
      element: null,
      api: null
    },
    setup(__props) {
      const props = __props;
      const contentStore = window.zb.editor.useContentStore();
      const { mobileFirstResponsiveDevices } = window.zb.components;
      let slider = null;
      const sliderWrapper = vue.ref(null);
      const pagination = vue.ref(null);
      if (props.element.content && props.element.content.length === 0) {
        props.element.addChild({
          element_type: "slider_builder_slide"
        });
      }
      const elementContent = vue.computed(() => {
        return props.element.content.map((elementUID) => contentStore.getElement(elementUID));
      });
      vue.watch(
        () => elementContent,
        () => {
          vue.nextTick(() => {
            setTimeout(() => {
              runScript();
            }, 0);
          });
        },
        {
          deep: true
        }
      );
      const elementOptions = vue.computed(() => {
        const config = props.options ? {
          arrows: props.options.arrows,
          pagination: props.options.dots,
          slides_to_show: props.options.slides_to_show,
          slides_to_scroll: props.options.slides_to_scroll,
          rawConfig: {
            observer: true,
            autoplay: false,
            speed: props.options.speed || 300
          }
        } : {};
        if (props.options.autoplay) {
          config.rawConfig.autoplay = {
            delay: props.options.autoplay_delay
          };
        }
        return JSON.stringify(config);
      });
      vue.watch(elementOptions, () => {
        vue.nextTick(() => runScript());
      });
      vue.watch(mobileFirstResponsiveDevices, (newValue) => {
        window.zbFrontendResponsiveDevicesMobileFirst = newValue;
        runScript();
      });
      function runScript() {
        const script = window.zbFrontend.scripts.swiper;
        if (script) {
          const config = script.getConfig(sliderWrapper.value);
          config.on = {
            beforeDestroy: function() {
              if (pagination.value) {
                pagination.value.innerHTML = "";
              }
            }
          };
          if (slider) {
            config.initialSlide = slider.realIndex;
            slider.destroy();
          }
          slider = script.initSlider(sliderWrapper.value, config);
        }
      }
      vue.onMounted(() => {
        runScript();
      });
      vue.onBeforeUnmount(() => {
        if (slider) {
          slider.destroy();
        }
      });
      return (_ctx, _cache) => {
        const _component_Element = vue.resolveComponent("Element");
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1, [
          vue.renderSlot(_ctx.$slots, "start"),
          vue.createElementVNode("div", {
            ref_key: "sliderWrapper",
            ref: sliderWrapper,
            class: "swiper-container",
            "data-zion-slider-config": vue.unref(elementOptions)
          }, [
            vue.createElementVNode("div", _hoisted_3, [
              (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(elementContent), (element) => {
                return vue.openBlock(), vue.createBlock(_component_Element, {
                  key: element.uid,
                  element
                }, null, 8, ["element"]);
              }), 128))
            ]),
            __props.options.dots ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_4, null, 512)) : vue.createCommentVNode("", true),
            __props.options.arrows ? (vue.openBlock(), vue.createElementBlock(vue.Fragment, { key: 1 }, [
              _hoisted_5,
              _hoisted_6
            ], 64)) : vue.createCommentVNode("", true)
          ], 8, _hoisted_2),
          vue.renderSlot(_ctx.$slots, "end")
        ]);
      };
    }
  }));
  var SliderBuilderSlide_vue_vue_type_style_index_0_lang = "";
  var _export_sfc = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
      target[key] = val;
    }
    return target;
  };
  const _sfc_main = {
    name: "SliderBuilderSlide",
    props: ["options", "element", "api"],
    setup(props) {
      return {};
    }
  };
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_SortableContent = vue.resolveComponent("SortableContent");
    return vue.openBlock(), vue.createBlock(_component_SortableContent, {
      element: $props.element,
      class: "swiper-slide"
    }, {
      start: vue.withCtx(() => [
        vue.renderSlot(_ctx.$slots, "start")
      ]),
      end: vue.withCtx(() => [
        vue.renderSlot(_ctx.$slots, "end")
      ]),
      _: 3
    }, 8, ["element"]);
  }
  var SliderBuilderSlide = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  window.zb.editor.registerElementComponent({
    elementType: "slider_builder",
    component: _sfc_main$1
  });
  window.zb.editor.registerElementComponent({
    elementType: "slider_builder_slide",
    component: SliderBuilderSlide
  });
})(zb.vue);
