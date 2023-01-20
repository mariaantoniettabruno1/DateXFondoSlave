(function(vue) {
  "use strict";
  const _hoisted_1 = ["data-zion-countdown-config"];
  const _sfc_main = /* @__PURE__ */ vue.defineComponent({
    __name: "Countdown",
    props: {
      options: null,
      api: null,
      element: null
    },
    setup(__props) {
      const props = __props;
      const root = vue.ref(null);
      let countdownInstance;
      vue.onMounted(() => {
        runScript();
      });
      vue.watch(
        () => props.options,
        () => {
          vue.nextTick(() => {
            runScript();
          });
        }
      );
      const l10n = window.zbProCountdownData;
      const countdownConfig = vue.computed(() => {
        var _a;
        let config = {
          daysString: props.options.days_text || l10n.days,
          hoursString: props.options.hours_text || l10n.hours,
          minutesString: props.options.minutes_text || l10n.minutes,
          secondsString: props.options.seconds_text || l10n.seconds
        };
        if (props.options.type === "date") {
          config.finalDate = `${props.options.date} ${props.options.hour}:${props.options.minutes}:00`;
        } else if (props.options.type === "evergreen") {
          config.evergreen_config = {
            days: props.options.evergreen_days,
            hours: props.options.evergreen_hours,
            minutes: props.options.evergreen_minutes,
            uid: (_a = props.options.evergreen_uid) != null ? _a : props.element.uid
          };
        }
        config.expirationAction = props.options.expiration_action;
        config.expirationMessage = props.options.expiration_message;
        config.redirectURL = props.options.expiration_redirect_url;
        return JSON.stringify(config);
      });
      function runScript() {
        const script = window.zbScripts.countdown;
        if (countdownInstance) {
          countdownInstance.destroy();
        }
        countdownInstance = script(root.value);
      }
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          ref_key: "root",
          ref: root,
          "data-zion-countdown-config": vue.unref(countdownConfig)
        }, [
          vue.renderSlot(_ctx.$slots, "start"),
          vue.renderSlot(_ctx.$slots, "end")
        ], 8, _hoisted_1);
      };
    }
  });
  window.zb.editor.registerElementComponent({
    elementType: "countdown",
    component: _sfc_main
  });
})(zb.vue);
