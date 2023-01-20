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
var __objRest = (source, exclude) => {
  var target = {};
  for (var prop in source)
    if (__hasOwnProp.call(source, prop) && exclude.indexOf(prop) < 0)
      target[prop] = source[prop];
  if (source != null && __getOwnPropSymbols)
    for (var prop of __getOwnPropSymbols(source)) {
      if (exclude.indexOf(prop) < 0 && __propIsEnum.call(source, prop))
        target[prop] = source[prop];
    }
  return target;
};
var __publicField = (obj, key, value) => {
  __defNormalProp(obj, typeof key !== "symbol" ? key + "" : key, value);
  return value;
};
var __async = (__this, __arguments, generator) => {
  return new Promise((resolve, reject) => {
    var fulfilled = (value) => {
      try {
        step(generator.next(value));
      } catch (e) {
        reject(e);
      }
    };
    var rejected = (value) => {
      try {
        step(generator.throw(value));
      } catch (e) {
        reject(e);
      }
    };
    var step = (x) => x.done ? resolve(x.value) : Promise.resolve(x.value).then(fulfilled, rejected);
    step((generator = generator.apply(__this, __arguments)).next());
  });
};
(function(vue) {
  "use strict";
  var isVue2 = false;
  /*!
    * pinia v2.0.21
    * (c) 2022 Eduardo San Martin Morote
    * @license MIT
    */
  let activePinia;
  const setActivePinia = (pinia2) => activePinia = pinia2;
  const piniaSymbol = Symbol();
  function isPlainObject$3(o) {
    return o && typeof o === "object" && Object.prototype.toString.call(o) === "[object Object]" && typeof o.toJSON !== "function";
  }
  var MutationType;
  (function(MutationType2) {
    MutationType2["direct"] = "direct";
    MutationType2["patchObject"] = "patch object";
    MutationType2["patchFunction"] = "patch function";
  })(MutationType || (MutationType = {}));
  function createPinia() {
    const scope = vue.effectScope(true);
    const state = scope.run(() => vue.ref({}));
    let _p = [];
    let toBeInstalled = [];
    const pinia2 = vue.markRaw({
      install(app) {
        setActivePinia(pinia2);
        {
          pinia2._a = app;
          app.provide(piniaSymbol, pinia2);
          app.config.globalProperties.$pinia = pinia2;
          toBeInstalled.forEach((plugin) => _p.push(plugin));
          toBeInstalled = [];
        }
      },
      use(plugin) {
        if (!this._a && !isVue2) {
          toBeInstalled.push(plugin);
        } else {
          _p.push(plugin);
        }
        return this;
      },
      _p,
      _a: null,
      _e: scope,
      _s: /* @__PURE__ */ new Map(),
      state
    });
    return pinia2;
  }
  const noop$1 = () => {
  };
  function addSubscription(subscriptions, callback, detached, onCleanup = noop$1) {
    subscriptions.push(callback);
    const removeSubscription = () => {
      const idx = subscriptions.indexOf(callback);
      if (idx > -1) {
        subscriptions.splice(idx, 1);
        onCleanup();
      }
    };
    if (!detached && vue.getCurrentInstance()) {
      vue.onUnmounted(removeSubscription);
    }
    return removeSubscription;
  }
  function triggerSubscriptions(subscriptions, ...args) {
    subscriptions.slice().forEach((callback) => {
      callback(...args);
    });
  }
  function mergeReactiveObjects(target, patchToApply) {
    for (const key in patchToApply) {
      if (!patchToApply.hasOwnProperty(key))
        continue;
      const subPatch = patchToApply[key];
      const targetValue = target[key];
      if (isPlainObject$3(targetValue) && isPlainObject$3(subPatch) && target.hasOwnProperty(key) && !vue.isRef(subPatch) && !vue.isReactive(subPatch)) {
        target[key] = mergeReactiveObjects(targetValue, subPatch);
      } else {
        target[key] = subPatch;
      }
    }
    return target;
  }
  const skipHydrateSymbol = Symbol();
  function shouldHydrate(obj) {
    return !isPlainObject$3(obj) || !obj.hasOwnProperty(skipHydrateSymbol);
  }
  const { assign } = Object;
  function isComputed(o) {
    return !!(vue.isRef(o) && o.effect);
  }
  function createOptionsStore(id, options2, pinia2, hot) {
    const { state, actions, getters } = options2;
    const initialState = pinia2.state.value[id];
    let store;
    function setup() {
      if (!initialState && true) {
        {
          pinia2.state.value[id] = state ? state() : {};
        }
      }
      const localState = vue.toRefs(pinia2.state.value[id]);
      return assign(localState, actions, Object.keys(getters || {}).reduce((computedGetters, name) => {
        computedGetters[name] = vue.markRaw(vue.computed(() => {
          setActivePinia(pinia2);
          const store2 = pinia2._s.get(id);
          return getters[name].call(store2, store2);
        }));
        return computedGetters;
      }, {}));
    }
    store = createSetupStore(id, setup, options2, pinia2, hot, true);
    store.$reset = function $reset() {
      const newState = state ? state() : {};
      this.$patch(($state) => {
        assign($state, newState);
      });
    };
    return store;
  }
  function createSetupStore($id, setup, options2 = {}, pinia2, hot, isOptionsStore) {
    let scope;
    const optionsForPlugin = assign({ actions: {} }, options2);
    const $subscribeOptions = {
      deep: true
    };
    let isListening;
    let isSyncListening;
    let subscriptions = vue.markRaw([]);
    let actionSubscriptions = vue.markRaw([]);
    let debuggerEvents;
    const initialState = pinia2.state.value[$id];
    if (!isOptionsStore && !initialState && true) {
      {
        pinia2.state.value[$id] = {};
      }
    }
    vue.ref({});
    let activeListener;
    function $patch(partialStateOrMutator) {
      let subscriptionMutation;
      isListening = isSyncListening = false;
      if (typeof partialStateOrMutator === "function") {
        partialStateOrMutator(pinia2.state.value[$id]);
        subscriptionMutation = {
          type: MutationType.patchFunction,
          storeId: $id,
          events: debuggerEvents
        };
      } else {
        mergeReactiveObjects(pinia2.state.value[$id], partialStateOrMutator);
        subscriptionMutation = {
          type: MutationType.patchObject,
          payload: partialStateOrMutator,
          storeId: $id,
          events: debuggerEvents
        };
      }
      const myListenerId = activeListener = Symbol();
      vue.nextTick().then(() => {
        if (activeListener === myListenerId) {
          isListening = true;
        }
      });
      isSyncListening = true;
      triggerSubscriptions(subscriptions, subscriptionMutation, pinia2.state.value[$id]);
    }
    const $reset = noop$1;
    function $dispose() {
      scope.stop();
      subscriptions = [];
      actionSubscriptions = [];
      pinia2._s.delete($id);
    }
    function wrapAction(name, action) {
      return function() {
        setActivePinia(pinia2);
        const args = Array.from(arguments);
        const afterCallbackList = [];
        const onErrorCallbackList = [];
        function after(callback) {
          afterCallbackList.push(callback);
        }
        function onError(callback) {
          onErrorCallbackList.push(callback);
        }
        triggerSubscriptions(actionSubscriptions, {
          args,
          name,
          store,
          after,
          onError
        });
        let ret;
        try {
          ret = action.apply(this && this.$id === $id ? this : store, args);
        } catch (error) {
          triggerSubscriptions(onErrorCallbackList, error);
          throw error;
        }
        if (ret instanceof Promise) {
          return ret.then((value) => {
            triggerSubscriptions(afterCallbackList, value);
            return value;
          }).catch((error) => {
            triggerSubscriptions(onErrorCallbackList, error);
            return Promise.reject(error);
          });
        }
        triggerSubscriptions(afterCallbackList, ret);
        return ret;
      };
    }
    const partialStore = {
      _p: pinia2,
      $id,
      $onAction: addSubscription.bind(null, actionSubscriptions),
      $patch,
      $reset,
      $subscribe(callback, options3 = {}) {
        const removeSubscription = addSubscription(subscriptions, callback, options3.detached, () => stopWatcher());
        const stopWatcher = scope.run(() => vue.watch(() => pinia2.state.value[$id], (state) => {
          if (options3.flush === "sync" ? isSyncListening : isListening) {
            callback({
              storeId: $id,
              type: MutationType.direct,
              events: debuggerEvents
            }, state);
          }
        }, assign({}, $subscribeOptions, options3)));
        return removeSubscription;
      },
      $dispose
    };
    const store = vue.reactive(assign(
      {},
      partialStore
    ));
    pinia2._s.set($id, store);
    const setupStore = pinia2._e.run(() => {
      scope = vue.effectScope();
      return scope.run(() => setup());
    });
    for (const key in setupStore) {
      const prop = setupStore[key];
      if (vue.isRef(prop) && !isComputed(prop) || vue.isReactive(prop)) {
        if (!isOptionsStore) {
          if (initialState && shouldHydrate(prop)) {
            if (vue.isRef(prop)) {
              prop.value = initialState[key];
            } else {
              mergeReactiveObjects(prop, initialState[key]);
            }
          }
          {
            pinia2.state.value[$id][key] = prop;
          }
        }
      } else if (typeof prop === "function") {
        const actionValue = wrapAction(key, prop);
        {
          setupStore[key] = actionValue;
        }
        optionsForPlugin.actions[key] = prop;
      } else
        ;
    }
    {
      assign(store, setupStore);
      assign(vue.toRaw(store), setupStore);
    }
    Object.defineProperty(store, "$state", {
      get: () => pinia2.state.value[$id],
      set: (state) => {
        $patch(($state) => {
          assign($state, state);
        });
      }
    });
    pinia2._p.forEach((extender) => {
      {
        assign(store, scope.run(() => extender({
          store,
          app: pinia2._a,
          pinia: pinia2,
          options: optionsForPlugin
        })));
      }
    });
    if (initialState && isOptionsStore && options2.hydrate) {
      options2.hydrate(store.$state, initialState);
    }
    isListening = true;
    isSyncListening = true;
    return store;
  }
  function defineStore(idOrOptions, setup, setupOptions) {
    let id;
    let options2;
    const isSetupStore = typeof setup === "function";
    if (typeof idOrOptions === "string") {
      id = idOrOptions;
      options2 = isSetupStore ? setupOptions : setup;
    } else {
      options2 = idOrOptions;
      id = idOrOptions.id;
    }
    function useStore(pinia2, hot) {
      const currentInstance = vue.getCurrentInstance();
      pinia2 = pinia2 || currentInstance && vue.inject(piniaSymbol);
      if (pinia2)
        setActivePinia(pinia2);
      pinia2 = activePinia;
      if (!pinia2._s.has(id)) {
        if (isSetupStore) {
          createSetupStore(id, setup, options2, pinia2);
        } else {
          createOptionsStore(id, options2, pinia2);
        }
      }
      const store = pinia2._s.get(id);
      return store;
    }
    useStore.$id = id;
    return useStore;
  }
  function storeToRefs(store) {
    {
      store = vue.toRaw(store);
      const refs = {};
      for (const key in store) {
        const value = store[key];
        if (vue.isRef(value) || vue.isReactive(value)) {
          refs[key] = vue.toRef(store, key);
        }
      }
      return refs;
    }
  }
  var index = "";
  window.addEventListener("load", () => {
    const wp2 = window.wp;
    const Library = wp2.media.controller.Library;
    const _ = window._;
    const Select = window.wp.media.view.MediaFrame.Select;
    const MediaController = Library.extend(
      {
        defaults: _.defaults({
          id: "zion-media",
          filterable: "uploaded",
          priority: 60,
          syncSelection: true
        }, Library.prototype.defaults),
        initialize: function() {
          var library, comparator;
          Library.prototype.initialize.apply(this, arguments);
          library = this.get("library");
          comparator = library.comparator;
          library.comparator = function(a, b) {
            var aInQuery = !!this.mirroring.get(a.cid);
            var bInQuery = !!this.mirroring.get(b.cid);
            if (!aInQuery && bInQuery) {
              return -1;
            } else if (aInQuery && !bInQuery) {
              return 1;
            } else {
              return comparator.apply(this, arguments);
            }
          };
          library.observe(this.get("selection"));
        }
      }
    );
    const ZionBuilderFrame = Select.extend({
      initialize: function() {
        Select.prototype.initialize.apply(this, arguments);
      },
      createStates: function() {
        const options2 = this.options;
        this.states.add(new MediaController({
          library: wp2.media.query(options2.library),
          multiple: options2.multiple,
          title: options2.title
        }));
      }
    });
    window.wp.media.view.MediaFrame.ZionBuilderFrame = ZionBuilderFrame;
  });
  const SvgIcons = [
    {
      paths: [
        "M11 0C4.9 0 0 4.9 0 11s4.9 11 11 11c1 0 1.8-.9 1.8-1.8 0-.5-.1-.9-.5-1.2-.2-.4-.5-.7-.5-1.2 0-1 .9-1.8 1.8-1.8h2.2c3.4 0 6.1-2.7 6.1-6.1C22 4.4 17.1 0 11 0zM4.3 11c-1 0-1.8-.9-1.8-1.8s.9-1.8 1.8-1.8 1.8.9 1.8 1.8S5.3 11 4.3 11zm3.6-4.9c-1 0-1.8-.9-1.8-1.8S7 2.4 7.9 2.4s1.8.9 1.8 1.8-.8 1.9-1.8 1.9zm6.2 0c-1 0-1.8-.9-1.8-1.8s.9-1.8 1.8-1.8 1.8.9 1.8 1.8-.9 1.8-1.8 1.8zm3.6 4.9c-1 0-1.8-.9-1.8-1.8s.9-1.8 1.8-1.8 1.8.9 1.8 1.8-.8 1.8-1.8 1.8z"
      ],
      tags: ["background"],
      viewBox: ["0 0 22 22"]
    },
    {
      paths: [
        "M20 0h2v22h-2z",
        "M0 2v2h2V2h2V0H0zM0 12h2v4H0zM0 6h2v4H0zM6 20h4v2H6zM2 18H0v4h4v-2H2zM18 20h2v2h-2zM6 0h4v2H6zM18 0h2v2h-2zM12 20h4v2h-4zM12 0h4v2h-4z"
      ],
      tags: ["borders"],
      viewBox: ["0 0 22 22"]
    },
    {
      paths: ["M20 2v8H2V2h18m2-2H0v12h22V0zM8 16v4H2v-4h6m2-2H0v8h10v-8zM20 16v4h-6v-4h6m2-2H12v8h10v-8z"],
      tags: ["display"],
      viewBox: ["0 0 22 22"]
    },
    {
      paths: [
        "M0 0l7.5 13v9l2.9-2 2.1-1.5 2-1.4V13L22 0H0zm9.5 18.2v-5.7L8.1 10 3.5 2h15.1L14 10l-1.4 2.5v3.6l-3.1 2.1z"
      ],
      tags: ["filters"],
      viewBox: ["0 0 22 22"]
    },
    {
      paths: [
        "M20 2v18H2V2h18m2-2H0v22h22V0z",
        "M14 7l-1.4 1.3 1.8 1.8H7.7l1.8-1.8L8 7l-4 4 1.3 1.3L8 15l1.4-1.3L7.7 12h6.7l-1.8 1.8L14 15l2.7-2.7L18 11l-4-4z"
      ],
      tags: ["size-spacing"],
      viewBox: ["0 0 22 22"]
    },
    {
      paths: ["M0 0v14h14V0H0zm12 12H2V2h10v10z", "M16 4v2h4v14H6v-4H4v6h18V4z"],
      tags: ["transform"],
      viewBox: ["0 0 22 22"]
    },
    {
      paths: [],
      circle: ['cx="6" cy="16" r="6"', 'cx="11" cy="11" r="6"', 'cx="16" cy="6" r="6"'],
      tags: ["transitions"],
      viewBox: ["0 0 22 22"]
    },
    {
      paths: [
        "M18.3 7.6c-1.4 0-2.6.3-3.6.9v2c.9-.8 2-1.2 3.1-1.2 1.3 0 2 .7 2 2.1l-2.9.4c-2.2.3-3.2 1.4-3.2 3.3 0 .8.3 1.6.8 2.1s1.3.8 2.2.8c1.3 0 2.3-.6 3-1.8v1.5H22v-6.5c0-2.3-1.3-3.6-3.7-3.6zm.9 7.9c-.4.4-1 .7-1.7.7-.5 0-.9-.2-1.2-.4-.3-.3-.5-.6-.5-1 0-.6.2-.9.5-1.2.3-.3.8-.4 1.5-.5l2-.3v.8c0 .9-.2 1.4-.6 1.9zM5.1 4L0 17.7h2.5l1.2-3.5h5.4l1.3 3.5h2.5L7.7 4H5.1zm-.8 8.4l1.8-5.5c0-.2.2-.5.2-.8 0 .4.1.7.2.8l1.9 5.5H4.3z"
      ],
      tags: ["typography"],
      viewBox: ["0 0 22 22"]
    },
    {
      paths: [
        "M28 6H7.7l2.2-2.2L7.1 1 0 8.1l7.1 7 2.8-2.8L7.6 10H28zM0 18.1h20.3l-2.2-2.2 2.8-2.8 7.1 7-7.1 7.1-2.8-2.8 2.3-2.3H0z"
      ],
      tags: ["reverse"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M25 25l5.5-2.8 5.5-2.7L47 14 25 3 3 14l11 5.5L3 25l11 5.5L3 36l22 11v-5.5L13.9 36l5.5-2.8L25 36v-5.5L13.9 25l5.5-2.8L25 25zM13.9 14L25 8.5 36.1 14 25 19.5 13.9 14z",
        "M39.1 25h-5.3v6.2h-6.2v5.2h6.2v6.2h5.3v-6.2h6.1v-5.2h-6.1z"
      ],
      tags: ["dynamic"]
    },
    {
      paths: [
        "M38.8 30.9l-1.1-1.1c-.1-.1-.2-.3-.2-.5v-1.1c0-.2-.2-.4-.4-.4h-.5c-.2 0-.3.1-.3.3l-.4 1.3c0 .2-.2.3-.3.3h-.3c-.1 0-.3-.1-.3-.2l-.5-1.1c-.1-.3-.4-.4-.7-.4h-1.1c-.1 0-.3 0-.4.1l-2.1 1.5c-.2.1-.3.2-.5.3l-3.5 1.4c-.3.1-.4.4-.4.7v.9c0 .2.1.4.2.5l1.1 1.1c.3.3.6.4 1 .4h1.2l1.9-.5c.8-.2 1.7 0 2.3.6l1.2 1.2c.3.3.6.4 1 .4H37c.4 0 .7-.1 1-.4l.8-.8c.3-.3.4-.6.4-1v-2.2c0-.7-.2-1-.4-1.3zM25 3C12.9 3 3 12.8 3 25s9.9 22 22 22 22-9.8 22-22S37.1 3 25 3zm0 39.7c-8.8 0-16.1-6.5-17.5-14.9h5.6c.4 0 .7-.1 1-.4l1.7-1.7c.3-.3.9-.2 1.1.2l2 4c.2.5.7.8 1.3.8h.5c.8 0 1.4-.6 1.4-1.4v-.8c0-.4-.1-.7-.4-1l-.5-.5c-.3-.3-.3-.7 0-1l.5-.5c.3-.3.6-.4 1-.4.5 0 1-.3 1.2-.7l1.5-2.6c.2-.3.6-.3.7 0 .1.2.4.4.6.4h.3c.4 0 .7-.3.7-.7v-6.9c0-.5-.3-1-.8-1.3l-1-.5c-.5-.2-.5-.9-.1-1.2l4.4-3.4c7.1 2.3 12.3 9 12.3 16.9.2 9.7-7.7 17.6-17.5 17.6z"
      ],
      tags: ["globe"]
    },
    {
      paths: [
        "M3 2C1.3 2 0 3.3 0 5s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3zm0 18c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3zm0-9c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"
      ],
      tags: ["ite-move"],
      viewBox: ["0 0 6 28"]
    },
    {
      paths: [
        "M9.1 2.2L.2 25.8h4.3l1.9-5.5h9.2l1.9 5.5h4.3l-9-23.7H9.1zM7.5 17L11 7.2l3.4 9.8H7.5zm29.8 4.4v-7.9c0-1.9-.7-3.3-1.9-4.4-1.2-1-2.9-1.6-4.9-1.6-1.4 0-2.6.2-3.7.7-1.1.5-2 1.1-2.6 2-.6.8-1 1.7-1 2.7h4c0-.7.3-1.3.9-1.7.6-.4 1.3-.7 2.2-.7 1 0 1.8.3 2.3.8.5.5.8 1.3.8 2.2v1.2H31c-2.6 0-4.6.5-6 1.5-1.4 1-2.1 2.4-2.1 4.3 0 1.5.6 2.7 1.7 3.7s2.6 1.5 4.3 1.5c1.8 0 3.4-.7 4.6-2 .1.8.3 1.3.5 1.6h4V25c-.4-.7-.7-2-.7-3.6zm-3.9-.8c-.3.6-.9 1.1-1.6 1.5s-1.4.6-2.2.6c-.8 0-1.5-.2-2-.7-.5-.4-.8-1.1-.8-1.8 0-.9.4-1.7 1.1-2.2.7-.5 1.8-.8 3.3-.8h2.1v3.4z"
      ],
      tags: ["ite-font"],
      viewBox: ["0 0 38 28"]
    },
    {
      paths: [
        "M1.1 26V2h8.4c2.9 0 5.1.6 6.6 1.7s2.3 2.8 2.3 4.9c0 1.2-.3 2.2-.9 3.1-.6.9-1.4 1.6-2.5 2 1.2.3 2.2.9 2.9 1.9.7.9 1.1 2.1 1.1 3.4 0 2.3-.7 4.1-2.2 5.2-1.5 1.2-3.6 1.8-6.3 1.8H1.1zM6 12.1h3.7c2.5 0 3.7-1 3.7-3 0-1.1-.3-1.9-.9-2.3s-1.6-.8-3-.8H6v6.1zm0 3.4V22h4.2c1.2 0 2.1-.3 2.7-.8.7-.6 1-1.3 1-2.3 0-2.2-1.1-3.3-3.4-3.3H6z"
      ],
      tags: ["ite-weight"],
      viewBox: ["0 0 20 28"]
    },
    {
      paths: ["M3.9 23.2L6.8 4.7 4 4l.3-1.9 9.3-.1-.3 2-3 .7-3 18.5 2.9.7-.4 2.1H.5l.3-2.1 3.1-.7z"],
      tags: ["ite-italic"],
      viewBox: ["0 0 14 28"]
    },
    {
      paths: [
        "M5.5 21.8c-1.6-1.4-2.4-3.5-2.4-6V0h4.1v15.8c0 1.6.4 2.8 1.2 3.6.8.8 2 1.3 3.5 1.3 3.2 0 4.7-1.7 4.7-5V0h4.1v15.8c0 2.5-.8 4.5-2.4 6-1.5 1.5-3.6 2.2-6.3 2.2-2.7 0-4.9-.7-6.5-2.2zM0 26v2h24v-2H0z"
      ],
      tags: ["ite-underline"],
      viewBox: ["0 0 24 28"]
    },
    {
      paths: [
        "M9.1 2.1v.1L.2 25.8h4.3l1.9-5.5h9.2l1.9 5.5h4.3l-9-23.7H9.1zM7.5 17L11 7.3l3.4 9.8H7.5zM35.8 2.1h-3.7v.1l-8.9 23.6h4.3l1.9-5.5h9.2l1.9 5.5h4.3l-9-23.7zM30.5 17L34 7.3l3.4 9.8h-6.9z"
      ],
      tags: ["ite-uppercase"],
      viewBox: ["0 0 45 28"]
    },
    {
      paths: [
        "M15 4c-1.7-1.4-3.6-2-5.6-2-2.9 0-5.8 1.3-7.5 3.8C-1.2 10-.4 15.9 3.8 19.2c1 .6 1.9 1.1 3 1.5-1.6 1.8-3.6 3.3-5.9 4.4-.2.1-.3.2-.4.4 0 .1 0 .3.1.4s.2.1.4.1c2.9-.2 6-1 8.8-2.5 2.5-1.3 4.8-3.1 6.5-5.4.3-.3.5-.6.6-.9C20 13.1 19.2 7.2 15 4zm-1.1 10.8c-.2.2-.3.4-.5.6l-.1.1-.1.2c-.6.9-1.5 1.6-2.3 2.4l-3.2-1c-.7-.2-1.3-.6-1.9-1-2.5-1.9-3-5.5-1.1-8 1.1-1.4 2.8-2.2 4.6-2.2 1.2 0 2.4.4 3.4 1.1 2.6 1.8 3.1 5.3 1.2 7.8zM34.2 4c-1.7-1.4-3.6-2-5.6-2-2.9 0-5.8 1.3-7.5 3.8C18 10 18.8 15.9 23 19.2c1 .6 1.9 1.1 3 1.5-1.6 1.8-3.6 3.3-5.9 4.4-.2.1-.3.2-.4.4 0 .1 0 .3.1.4s.2.1.4.1c2.9-.2 6-1 8.8-2.5 2.5-1.3 4.8-3.1 6.5-5.4.3-.3.5-.6.6-.9 3.1-4.1 2.3-10-1.9-13.2zm-1.1 10.8c-.2.2-.3.4-.5.6l-.1.1-.1.2c-.6.9-1.5 1.6-2.3 2.4l-3.2-1c-.7-.2-1.3-.6-1.9-1-2.5-1.9-3-5.5-1.1-8 1.1-1.4 2.8-2.2 4.6-2.2 1.2 0 2.4.4 3.4 1.1 2.6 1.8 3.1 5.3 1.2 7.8z"
      ],
      tags: ["ite-quote"],
      viewBox: ["0 0 38 28"]
    },
    {
      paths: ["M24 2v4H0V2h24zM0 26h24v-4H0v4zm6-14v4h12v-4H6z"],
      tags: ["ite-alignment"],
      viewBox: ["0 0 24 28"]
    },
    {
      paths: [
        "M25.8 2.2c-3.1-3.1-8.1-3.1-11.1 0-4.2 4.2-4.2 4.2-4.5 4.6-2.5 3.2-2.3 7.7.5 10.5.6.6 1.4 1.1 2.1 1.5.2.1.3.1.5.1.3 0 .6-.1.8-.3l.2-.2c.6-.6.8-1.4.9-2 0-.6-.3-1-.7-1.1-.4-.2-.7-.4-1-.8-.8-.8-1.2-1.7-1.2-2.8 0-1.1.4-2.1 1.2-2.8l4-4c1.5-1.5 4.1-1.5 5.6 0s1.5 4.1 0 5.6L20.6 13c-.3.3-.3.7-.3.9v.1c.1.4.2.8.2 1.3 0 .2 0 .5.1.6 0 .5.3.9.7 1.1.4.2.9 0 1.2-.3l3.4-3.4c2.9-2.9 2.8-8-.1-11.1z",
        "M17.4 10.6c-.6-.6-1.4-1.1-1.9-1.5-.4-.3-1-.2-1.4.2l-.2.2c-.6.6-.8 1.4-.9 2 0 .6.3 1 .7 1.1.3.2.7.4 1 .8.8.8 1.2 1.7 1.2 2.8 0 1.1-.4 2.1-1.2 2.8l-4 4c-1.5 1.5-4.1 1.5-5.6 0s-1.5-4.1 0-5.6L7.5 15c.4-.2.5-.8.4-1.1-.2-.6-.3-1.3-.3-1.9 0-.4-.3-.8-.7-1-.4-.2-.9-.1-1.2.2l-3.4 3.4c-3.1 3.1-3.1 8.1 0 11.1C3.9 27.2 5.9 28 7.9 28c2 0 4-.8 5.6-2.3 2.1-2.2 3.2-3.2 3.7-3.8l.8-.8c2.6-3.1 2.3-7.7-.6-10.5z"
      ],
      tags: ["ite-link"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M11.7 9.1L0 40.3h5.7l2.7-8h12.3l2.9 8h5.7L17.6 9.1h-5.9zM9.8 28.2L14 15.7c.1-.5.4-1.2.5-1.9h.1c.1 1 .2 1.5.4 1.9l4.3 12.5H9.8zm31.7-10.8c-3.1 0-5.8.7-8.1 2.1V24c2.1-1.8 4.5-2.7 7.1-2.7 3 0 4.5 1.5 4.5 4.8l-6.7 1c-4.9.7-7.3 3.1-7.3 7.4 0 1.9.6 3.6 1.9 4.8 1.2 1.2 3 1.8 5.1 1.8 3 0 5.2-1.3 6.8-4h.1v3.5H50V25.8c0-5.5-2.9-8.4-8.5-8.4zm3.6 13.9c0 1.7-.5 3-1.5 4S41.3 37 39.8 37c-1.2 0-2-.4-2.7-1-.7-.6-1.1-1.4-1.1-2.3 0-1.3.4-2.1 1.1-2.7.7-.6 1.8-.8 3.3-1.1l4.6-.6v1.9l.1.1z"
      ],
      tags: ["type-font"]
    },
    {
      paths: [
        "M11.9 14.6c1.5 0 3 .5 4.3 1.4 3.2 2.4 3.8 6.9 1.4 10-.2.2-.4.5-.6.7l-.1.1-.1.2c-.8 1.1-1.9 2-2.9 3l-4-1.3c-.9-.2-1.7-.7-2.4-1.2-3.2-2.4-3.8-6.9-1.4-10.1 1.4-1.8 3.5-2.8 5.8-2.8m0-4.7c-3.7 0-7.3 1.7-9.5 4.8-3.9 5.2-2.9 12.7 2.4 16.8 1.2.8 2.4 1.4 3.8 1.9-2 2.3-4.5 4.2-7.5 5.6-.2.1-.4.2-.5.5 0 .1 0 .4.1.5.1.1.4.2.6.2C5 40 8.9 38.9 12.4 37c3.2-1.7 6-3.9 8.2-6.8.4-.4.6-.7.8-1.1 3.9-5.2 2.9-12.7-2.4-16.7-2.1-1.8-4.6-2.5-7.1-2.5zM38.1 14.6c1.5 0 3 .5 4.3 1.4 3.2 2.4 3.8 6.9 1.4 10-.2.2-.4.5-.6.7l-.1.1-.1.2c-.8 1.1-1.9 2-2.9 3l-4-1.3c-.8-.2-1.7-.7-2.4-1.2-3.2-2.4-3.8-6.9-1.4-10.1 1.4-1.8 3.5-2.8 5.8-2.8m0-4.7c-3.7 0-7.3 1.7-9.5 4.8-3.9 5.2-2.9 12.7 2.4 16.8 1.2.8 2.4 1.4 3.8 1.9-2 2.3-4.5 4.2-7.5 5.6-.2.1-.4.2-.5.5 0 .1 0 .4.1.5.1.1.4.2.6.2 3.7-.2 7.6-1.3 11.1-3.2 3.2-1.7 6-3.9 8.2-6.8.4-.4.6-.7.8-1.1 3.9-5.2 2.9-12.7-2.4-16.7-2.1-1.8-4.6-2.5-7.1-2.5z"
      ],
      tags: ["quote"]
    },
    {
      paths: ["M0 24h28v4H0zM0 0h28v4H0zM2 16h10v6H2zM16 16h10v6H16zM16 6h10v6H16zM2 6h10v6H2z"],
      tags: ["content-stretch"],
      viewBox: ["0 0 25 28"]
    },
    {
      paths: [
        "M17.7 22c-.9-2.9-3.5-5-6.7-5s-5.8 2.1-6.7 5H0v4h4.3c.9 2.9 3.5 5 6.7 5s5.8-2.1 6.7-5H32v-4H17.7zM11 27c-.9 0-1.7-.4-2.2-1-.5-.5-.8-1.2-.8-2s.3-1.5.8-2c.5-.6 1.3-1 2.2-1s1.7.4 2.2 1c.5.5.8 1.2.8 2s-.3 1.5-.8 2c-.5.6-1.3 1-2.2 1zM27.7 6c-.9-2.9-3.5-5-6.7-5s-5.8 2.1-6.7 5H0v4h14.3c.9 2.9 3.5 5 6.7 5s5.8-2.1 6.7-5H32V6h-4.3zm-4.5 4c-.5.6-1.3 1-2.2 1s-1.7-.4-2.2-1c-.5-.5-.8-1.2-.8-2s.3-1.5.8-2c.5-.6 1.3-1 2.2-1s1.7.4 2.2 1c.5.5.8 1.2.8 2s-.3 1.5-.8 2z"
      ],
      tags: ["sliders"],
      viewBox: ["0 0 32 32"]
    },
    {
      paths: [
        "M6.6 16.9c0 .8-.6 1.5-1.4 1.5-.8 0-1.5-.6-1.5-1.5 0-.8.6-1.4 1.5-1.4.8 0 1.5.6 1.4 1.4zm-1.4 4.4c-.8 0-1.5.7-1.5 1.5s.6 1.5 1.5 1.5c.8 0 1.4-.7 1.4-1.5.1-.8-.6-1.5-1.4-1.5zm0-17.6c-.8 0-1.5.6-1.5 1.4 0 .8.6 1.5 1.5 1.5.8 0 1.4-.6 1.4-1.5.1-.8-.6-1.4-1.4-1.4zM.8 16.2c-.5 0-.8.3-.8.8 0 .4.3.8.8.8s.8-.4.8-.8c-.1-.5-.4-.8-.8-.8zm4.4-6.6c-.8 0-1.5.6-1.5 1.5 0 .8.6 1.5 1.5 1.5.8 0 1.4-.6 1.4-1.5S6 9.6 5.2 9.6zM17 1.5c.4 0 .8-.4.8-.8-.1-.4-.5-.7-.8-.7-.4 0-.8.3-.8.8 0 .3.3.7.8.7zm-6.5 5c1.2.4 2.3-.7 1.9-1.9-.2-.4-.5-.8-.9-.9-1.2-.4-2.3.7-1.9 1.9.2.4.6.8.9.9zm.5 20c-.4 0-.8.3-.8.8s.5.7.8.7c.4 0 .8-.3.8-.8 0-.4-.3-.7-.8-.7zm16.3-14.7c.4 0 .8-.3.8-.8s-.3-.8-.8-.8-.8.3-.8.8.3.8.8.8zM17 6.6c.8 0 1.5-.6 1.5-1.5 0-.8-.6-1.4-1.5-1.4-.8 0-1.5.6-1.5 1.4 0 .9.6 1.5 1.5 1.5zm-6-5.1c.4 0 .8-.4.8-.8S11.5 0 11 0c-.4 0-.8.3-.8.8.1.3.5.7.8.7zM.8 10.3c-.5 0-.8.3-.8.7s.3.8.8.8.8-.3.8-.8c-.1-.4-.4-.7-.8-.7zm22 5.2c-.8 0-1.4.6-1.4 1.4 0 .8.6 1.5 1.4 1.5.8 0 1.5-.6 1.5-1.5 0-.8-.6-1.4-1.5-1.4zM17 8.8c-1.2 0-2.2 1-2.2 2.2s1 2.2 2.2 2.2 2.2-1 2.2-2.2-1-2.2-2.2-2.2zm5.8.8c-.8 0-1.4.6-1.4 1.5 0 .8.6 1.5 1.4 1.5.8 0 1.5-.6 1.5-1.5s-.6-1.5-1.5-1.5zm0 11.7c-.8 0-1.4.7-1.4 1.5s.6 1.5 1.4 1.5c.8 0 1.5-.7 1.5-1.5s-.6-1.5-1.5-1.5zm-11.8.1c-.8 0-1.5.6-1.5 1.5 0 .8.6 1.5 1.5 1.5.8 0 1.5-.6 1.5-1.5s-.6-1.5-1.5-1.5zm16.3-5.2c-.4 0-.8.3-.8.8 0 .4.3.8.8.8s.7-.5.7-.8c0-.5-.3-.8-.7-.8zM22.8 3.7c-.8 0-1.4.6-1.4 1.4 0 .8.6 1.5 1.4 1.5.8 0 1.5-.6 1.5-1.5 0-.8-.6-1.4-1.5-1.4zM11 14.8c-1.2 0-2.2 1-2.2 2.2s1 2.2 2.2 2.2 2.2-1 2.2-2.2-.9-2.2-2.2-2.2zm0-6c-1.2 0-2.2 1-2.2 2.2s1 2.2 2.2 2.2 2.2-1 2.2-2.2-.9-2.2-2.2-2.2zm6 17.7c-.4 0-.8.3-.8.8s.3.7.8.7c.4 0 .8-.3.8-.8-.1-.4-.5-.7-.8-.7zm0-11.7c-1.2 0-2.2 1-2.2 2.2 0 1.2 1 2.2 2.2 2.2s2.2-1 2.2-2.2c0-1.3-1-2.2-2.2-2.2zm0 6.5c-.8 0-1.5.7-1.5 1.5s.7 1.5 1.5 1.5 1.5-.7 1.5-1.5c-.1-.8-.7-1.5-1.5-1.5z"
      ],
      viewBox: ["0 0 28 28"],
      tags: ["blur"]
    },
    {
      paths: [
        "M6.7 13l2.8-6.5h3.9l-4.8 9.6v5.4H4.8v-5.4L0 6.5h3.9L6.7 13z",
        "M24 7.2h4L22 0l-6 7.2h4v13.6h-4l6 7.2 6-7.2h-4z"
      ],
      viewBox: ["0 0 28 28"],
      tags: ["vertical"]
    },
    {
      paths: [
        "M14 5l2.5-5h4.1l-4.2 7.4 4.3 7.6h-4.2L14 9.9 11.5 15H7.3l4.3-7.6L7.4 0h4.1L14 5z",
        "M20.8 24v4l7.2-6-7.2-6v4H7.2v-4L0 22l7.2 6v-4z"
      ],
      viewBox: ["0 0 28 28"],
      tags: ["horizontal"]
    },
    {
      paths: ["M12 6h16v16H12z", "M0 10h8v8H0z"],
      viewBox: ["0 0 28 28"],
      tags: ["spread"]
    },
    {
      paths: ["M5 0v32h22V0H5zm18 28H9v-6h14v6zm0-10H9v-4h14v4zm0-8H9V4h14v6z"],
      tags: ["structure"],
      viewBox: ["0 0 32 32"]
    },
    {
      paths: ["M3 0v32h26V0H3zm22 28H7V4h18v24zm-11-4c0-1.1.9-2 2-2s2 .9 2 2-.9 2-2 2-2-.9-2-2z"],
      tags: ["tablet"],
      viewBox: ["0 0 32 32"]
    },
    {
      paths: [
        "M4 14c0-2.2 1.8-4 4-4h2.2l4-4H8c-4.4 0-8 3.6-8 8 0 1.7.6 3.3 1.5 4.7l2.9-2.9c-.2-.6-.4-1.2-.4-1.8zM23.8 7l1.5-1.5-2.8-2.8L2.7 22.5l2.8 2.8L8.8 22H20c4.4 0 8-3.6 8-8 0-3-1.7-5.6-4.2-7zM20 18h-7.2l7.9-7.9c1.8.4 3.2 2 3.2 3.9.1 2.2-1.7 4-3.9 4z"
      ],
      tags: ["unlink"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M25 3C12.8 3 3 12.8 3 25s9.8 22 22 22 22-9.8 22-22S37.2 3 25 3zm0 38c-8.8 0-16-7.2-16-16S16.2 9 25 9s16 7.2 16 16-7.2 16-16 16z",
        "M22 14.5h6v12h-6zM22 29.5h6v6h-6z"
      ],
      tags: ["warning"]
    },
    {
      paths: ["M10.2 13.3l-4.3 4.3 14.9 14.8 4.2 4.3 19.1-19.1-4.3-4.3L25 28.2z"],
      tags: ["select"]
    },
    {
      paths: ["M28 14l-2.8 2.8-7.1 7.1-2.8-2.8 5-5.1H0v-4h20.3l-5-5.1 2.8-2.8 7.1 7.1L28 14z"],
      tags: ["long-arrow-right"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M20 0v18H7.7l3.1-3.2L8 12l-5.2 5.2L0 20l2.8 2.8L8 28l2.8-2.8L7.7 22H24V0z"],
      tags: ["line-break"],
      viewBox: ["0 0 24 28"]
    },
    {
      paths: ["M32 7.5l-2.8-2.9-17.1 17.1-9.3-9.2L0 15.3l12.1 12.1z"],
      tags: ["check"],
      viewBox: ["0 0 32 32"]
    },
    {
      paths: ["M25.3 5.5l-2.8-2.8-8.5 8.5-8.5-8.5-2.8 2.8 8.5 8.5-8.5 8.5 2.8 2.8 8.5-8.5 8.5 8.5 2.8-2.8-8.5-8.5z"],
      tags: ["close"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M22 2H8v4h14v14h4V2z", "M2 26h18V8H2v18zm4-14h10v10H6V12z"],
      tags: ["copy"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M2 8h18v18H2V8zm20-6H8v4h14v14h4V2h-4z"],
      tags: ["paste"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M25 6h-8V2h-6v4H3v4h3v16h16V10h3V6zm-7 16h-8V10h8v12z"],
      tags: ["delete"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M17.5 2L6 13.5l-4 4V26h8.5l4-4L26 10.5 17.5 2zM8.8 22H6v-2.8L17.5 7.7l2.8 2.8L8.8 22z"],
      tags: ["edit"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M18.7 10.7c.6-.2 1.6-1 1.6-2.3 0-1.7-1.4-2.8-3.2-2.4-6.7 1.6-8.8 4.3-8.8 8.6 0 .6.2 2.3.2 3.1 0 3.2-1.3 4.4-3.7 4.6C3 22.5 2 23.5 2 25c0 1.6 1.1 2.5 2.8 2.7 2.5.2 3.7 1.4 3.7 4.6 0 .7-.2 2.4-.2 3.1 0 4.3 2 7 8.8 8.6 1.8.4 3.2-.7 3.2-2.4 0-1.3-.9-2-1.6-2.3-3.4-1.2-4-2.5-4-4.9 0-.9.2-2.4.2-3.3 0-3.3-1.7-5.1-4.6-6 3-1.1 4.6-2.8 4.6-6 0-1-.2-2.5-.2-3.3 0-2.6.6-3.8 4-5.1zM45.1 22.3c-2.5-.2-3.7-1.4-3.7-4.6 0-.7.2-2.4.2-3.1 0-4.3-2-7-8.8-8.6-1.8-.4-3.2.7-3.2 2.4 0 1.3.9 2 1.6 2.3 3.4 1.2 4 2.5 4 4.9 0 .9-.2 2.4-.2 3.3 0 3.3 1.7 5.1 4.6 6-3 1.1-4.6 2.8-4.6 6 0 1 .2 2.5.2 3.3 0 2.5-.6 3.7-4 4.9-.6.2-1.6 1-1.6 2.3 0 1.7 1.4 2.8 3.2 2.4 6.7-1.6 8.8-4.3 8.8-8.6 0-.6-.2-2.3-.2-3.1 0-3.2 1.3-4.4 3.7-4.6 1.8-.2 2.8-1.2 2.8-2.7 0-1.4-1.1-2.3-2.8-2.5z"
      ],
      tags: ["braces"]
    },
    {
      paths: [
        "M34.3 12.3L30 16.5l5.5 5.5h-21l5.5-5.5-4.3-4.2L3 25l4.2 4.2 8.5 8.5 4.3-4.2-5.5-5.5h21L30 33.5l4.3 4.2 8.5-8.5L47 25z"
      ],
      tags: ["enlarge"]
    },
    {
      paths: ["M0 4v6h4V4h6V0H0zM24 0h-6v4h6v6h4V0zM24 24h-6v4h10V18h-4zM4 18H0v10h10v-4H4z"],
      tags: ["maximize"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M10 6V0H6v6H0v4h10zM22 10h6V6h-6V0h-4v10zM22 22h6v-4H18v10h4zM6 28h4V18H0v4h6z"],
      tags: ["shrink"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M26 22v4H2v-4h24zm-12-2l2.8-2.8 7.1-7.1-2.8-2.8-5.1 5V2h-4v10.3l-5.1-5-2.8 2.8 7.1 7.1L14 20z"],
      tags: ["export"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M26.9 1.3C26.1.1 24.4-.4 23.2.5c-.4 0-.4.4-.4.4l-13.2 19c-6.2 8.7-4.1 20.3 4.6 26.5s20.3 4.1 26.5-4.6c4.6-6.6 4.6-15.3 0-22L26.9 1.3z"
      ],
      tags: ["style"]
    },
    {
      paths: [
        "M35.4 12.8C32 6.2 25.3 2 18 2S4 6.2.6 12.8c-.4.7-.4 1.6 0 2.3C4 21.8 10.7 26 18 26c7.3 0 14-4.2 17.4-10.8.4-.8.4-1.6 0-2.4zM18 21.9c-5.4 0-10.5-3-13.3-7.9C7.5 9.1 12.6 6.1 18 6.1s10.5 3 13.3 7.9c-2.8 4.9-7.9 7.9-13.3 7.9zm5.8-6.2c-.9 3.2-4.2 5-7.4 4.1-3.2-.9-5-4.2-4.1-7.4.4.3.9.4 1.4.4 1.7 0 3-1.3 3-3 0-.5-.1-1-.4-1.4.5-.3 1.1-.4 1.7-.4.6 0 1.1.1 1.7.2 3.1 1 5 4.3 4.1 7.5z"
      ],
      tags: ["eye"],
      viewBox: ["0 0 36 28"]
    },
    {
      paths: [
        "M4.1 20.1C2.7 18.7 1.5 17 .6 15.2c-.4-.7-.4-1.6 0-2.3C4 6.2 10.7 2 18 2c1.3 0 2.6.1 3.8.4l-3.7 3.7H18c-5.4 0-10.5 3-13.3 7.9.7 1.2 1.5 2.2 2.4 3.1l-3 3zm31.3-4.9C32 21.8 25.3 26 18 26c-2.6 0-5.2-.6-7.6-1.6L6.9 28 4 25.2 29.1.1 32 2.9l-2.7 2.7c2.5 1.8 4.7 4.3 6.2 7.2.3.8.3 1.6-.1 2.4zM31.3 14c-1.3-2.2-3-4-5.1-5.4l-2.8 2.8c.6 1.3.8 2.7.4 4.2-.9 3.2-4.2 5-7.4 4.1l-.9-.3-1.8 1.8c1.4.4 2.8.7 4.3.7 5.4 0 10.5-3 13.3-7.9z"
      ],
      tags: ["hidden"]
    },
    {
      paths: [
        "M25 29.9c-2.7 0-4.9-2.2-4.9-4.9s2.2-4.9 4.9-4.9 4.9 2.2 4.9 4.9-2.2 4.9-4.9 4.9z",
        "M25 40.6c-5.1 0-10.1-1.4-14.5-4C6.3 34.1 2.7 30.5.3 26.2c-.4-.7-.4-1.7 0-2.4 2.4-4.3 5.9-7.9 10.2-10.4 4.4-2.6 9.4-4 14.5-4s10.1 1.4 14.5 4c4.2 2.5 7.8 6.1 10.2 10.4.4.7.4 1.7 0 2.4-2.4 4.3-5.9 7.9-10.2 10.4-4.4 2.7-9.4 4-14.5 4zM5.3 25C9.6 31.7 17 35.7 25 35.7s15.4-4 19.7-10.7C40.4 18.3 33 14.3 25 14.3S9.6 18.3 5.3 25z"
      ],
      tags: ["visibility"]
    },
    {
      paths: [
        'M49.7 23.8c-1.7-3-3.9-5.7-6.6-7.9l-3.5 3.5c2 1.6 3.7 3.5 5.1 5.6C40.4 31.7 33 35.7 25 35.7c-.6 0-1.1 0-1.7-.1L19 40c2 .4 4 .6 6 .6 5.1 0 10.1-1.4 14.5-4 4.2-2.5 7.8-6.1 10.2-10.4.4-.7.4-1.7 0-2.4zM25 29.9c2.7 0 4.9-2.2 4.9-4.9 0-.4-.1-.9-.2-1.3L44.4 9c1-1 1-2.5 0-3.5s-2.5-1-3.5 0l-5.7 5.7C32 10 28.5 9.4 25 9.4c-5.1 0-10.1 1.4-14.5 4C6.3 15.9 2.7 19.5.3 23.8c-.4.7-.4 1.7 0 2.4 2.4 4.2 5.7 7.7 9.8 10.2l-4.5 4.5c-1 1-1 2.5 0 3.5s2.5 1 3.5 0l14.7-14.7c.3.1.8.2 1.2.2zM5.3 25C9.6 18.3 17 14.2 25 14.2c2.2 0 4.3.3 6.4.9l-5.1 5.1c-.4-.1-.8-.2-1.3-.2-2.7 0-4.9 2.2-4.9 4.9 0 .4.1.9.2 1.3l-6.6 6.6C10.3 31 7.4 28.3 5.3 25z"'
      ],
      tags: ["visibility-hidden"]
    },
    {
      paths: [
        "M19.8 18.6l-4.9 4.9-2.8-2.8 3.7-3.7V8.6h4v10zM16 0C11.4 0 7.2 2 4.2 5.2L1.6 2.6 0 12.6 10.1 11l-3-3c2.2-2.5 5.4-4 8.9-4 6.6 0 12 5.4 12 12s-5.4 12-12 12c-3.5 0-6.7-1.5-8.9-4l-3 2.7c3 3.2 7.2 5.3 11.9 5.3 8.8 0 16-7.2 16-16S24.8 0 16 0z"
      ],
      tags: ["history"],
      viewBox: ["0 0 32 32"]
    },
    {
      paths: [
        "M5 25c0 11 9 20 20 20 7.6 0 14.2-4.2 17.6-10.6l-5.5-3.3c-2.2 4.4-6.8 7.4-12.1 7.4-7.5 0-13.6-6.1-13.6-13.6S17.5 11.4 25 11.4c4 0 7.5 1.8 9.9 4.5l-4.2 2.6 12.1 4L45 9.9l-4.3 2.6C37 8 31.4 5 25 5 14 5 5 14 5 25z"
      ],
      tags: ["redo"]
    },
    {
      paths: [
        "M25 5c-6.4 0-12 3-15.6 7.5L5 9.9l2.2 12.6 12.1-4-4.3-2.6c2.4-2.7 5.9-4.5 9.9-4.5 7.5 0 13.6 6.1 13.6 13.6s-6 13.6-13.5 13.6c-5.3 0-9.8-3-12.1-7.4l-5.5 3.3C10.7 40.8 17.4 45 25 45c11 0 20-9 20-20S36 5 25 5z"
      ],
      tags: ["undo"]
    },
    {
      paths: [
        "M16 0C7.2 0 0 7.2 0 16s7.2 16 16 16 16-7.2 16-16S24.8 0 16 0zm0 28C9.4 28 4 22.6 4 16S9.4 4 16 4s12 5.4 12 12-5.4 12-12 12zm2-17c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm-4 4h4v8h-4v-8z"
      ],
      tags: ["info"],
      viewBox: ["0 0 32 32"]
    },
    {
      paths: [
        "M90 20c38.6 0 70 31.4 70 70s-31.4 70-70 70-70-31.4-70-70 31.4-70 70-70m0-20C40.3 0 0 40.3 0 90s40.3 90 90 90 90-40.3 90-90S139.7 0 90 0z",
        "M90 65z",
        "M90 55c-5.5 0-10 4.5-10 10s4.5 10 10 10 10-4.5 10-10-4.5-10-10-10z",
        "M90 115V95v20z",
        "M90 85c-5.5 0-10 4.5-10 10v20c0 5.5 4.5 10 10 10s10-4.5 10-10V95c0-5.5-4.5-10-10-10z"
      ],
      viewBox: ["0 0 180 180"],
      tags: ["infopanel"]
    },
    {
      paths: [
        "M33.9 6.1c0 1.6-.6 3.2-1.7 4.4-1.2 1.1-2.7 1.7-4.3 1.7-1.6 0-3.1-.6-4.2-1.7-1.2-1.2-1.8-2.7-1.7-4.4 0-1.6.6-3.2 1.7-4.3C24.8.6 26.3 0 27.9 0s3.2.7 4.3 1.8c1.1 1.2 1.7 2.7 1.7 4.3zm-1 41.3l-4.2 1.9c-1.2.4-2.4.6-3.7.6-1.8.1-3.7-.6-5-1.8-1.1-1.3-1.8-3-1.7-4.8 0-.8 0-1.6.1-2.3.1-.8.3-1.7.4-2.7l2.2-9.5c.2-.9.4-1.7.5-2.6.1-.7.2-1.5.2-2.2.1-.9-.1-1.7-.6-2.4-.6-.6-1.5-.8-2.3-.7-.6 0-1.2.1-1.7.3-.6.2-1-2.3-1-2.3s2.9-1.2 4.2-1.7c1.2-.5 2.4-.8 3.7-.8 1.8-.1 3.6.5 4.9 1.8 1.2 1.4 1.8 3.1 1.7 4.9 0 .7 0 1.4-.1 2.1-.1 1-.2 1.9-.5 2.9l-2.1 9.4c-.2.7-.3 1.6-.5 2.6-.2.9-.2 1.7-.2 2.2-.1.9.2 1.8.7 2.6.7.5 1.5.7 2.3.6.6 0 1.2-.1 1.8-.3.7-.3.9 2.2.9 2.2z"
      ],
      tags: ["infobig"]
    },
    {
      paths: [
        "M19 4.6L30 11l-11 6.4L8 11l11-6.4M19 0L0 11l19 11 19-11L19 0zm15 18.7l-15 8.7-15-8.7L0 21l15.3 8.8L19 32l3.7-2.1L38 21l-4-2.3z"
      ],
      tags: ["lib"],
      viewBox: ["0 0 38 32"]
    },
    {
      paths: [
        "M20 6H8c-4.4 0-8 3.6-8 8s3.6 8 8 8h12c4.4 0 8-3.6 8-8s-3.6-8-8-8zm0 12H8c-2.2 0-4-1.8-4-4s1.8-4 4-4h12c2.2 0 4 1.8 4 4s-1.8 4-4 4z",
        "M10 12h8v4h-8z"
      ],
      tags: ["link"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M8 21h34v8H8z"],
      tags: ["minus"]
    },
    {
      paths: ["M22 4v24H10V4h12m4-4H6v32h20V0zM16 22c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"],
      tags: ["mobile"],
      viewBox: ["0 0 32 32"]
    },
    {
      paths: ["M25 6v12H7V6h18m4-4H3v20h26V2zM32 26H0v4h32v-4z"],
      tags: ["laptop"],
      viewBox: ["0 0 32 32"]
    },
    {
      paths: ["M28 4v16H4V4h24m4-4H0v24h32V0zM24 28H8v4h16v-4z"],
      tags: ["desktop"],
      viewBox: ["0 0 32 32"]
    },
    {
      paths: ["M24 4v13H4V4h20m4-4H0v21h28V0zM21 24H7v4h14v-4z"],
      tags: ["desktop-sm"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M28 0H18v4h6v6h4zM0 28h10v-4H4v-6H0zM16 12v4h-4v-4h4m4-4H8v12h12V8z"],
      tags: ["drag"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M47.6 2.4C46.1.9 44 0 41.7 0c-2.3 0-4.4.9-5.9 2.4l-5.9 5.9-3-2.9L24 8.3l2.9 2.9L0 37.5V50h12.5l26.3-26.9 2.9 2.9 2.9-2.9-2.9-3 5.9-5.9c1.5-1.5 2.4-3.6 2.4-5.9s-.9-4.4-2.4-5.9zM18.9 37.5H6l23.9-23.3 6 6-17 17.3z"
      ],
      tags: ["picker"]
    },
    {
      paths: ["M42 21H29V8h-8v13H8v8h13v13h8V29h13z"],
      tags: ["plus"]
    },
    {
      paths: [
        "M47.9 0H12.1c-.4 0-.8.2-1 .5L1 15.5c-.3.4-.3 1 .1 1.4l28 34.6c.5.6 1.4.6 1.9 0l28-34.6c.3-.4.4-1 .1-1.4L48.9.5c-.2-.3-.6-.5-1-.5zm-2 4.9l6.4 9.7h-6.9l-5.3-9.7h5.8zm-20.5 0h9.2l5.3 9.7H20.1l5.3-9.7zm-11.3 0h5.8l-5.3 9.7H7.7l6.4-9.7zM9.7 19.5h5.2l6.9 16.2L9.7 19.5zm10.5 0h19.7L30 44.2l-9.8-24.7zm17.9 16.2L45 19.5h5.2L38.1 35.7z"
      ],
      tags: ["quality"],
      viewBox: ["0 0 58 52"]
    },
    {
      paths: [
        "M14 0C6.3 0 0 6.3 0 14s6.3 14 14 14 14-6.3 14-14S21.7 0 14 0zm0 24C8.5 24 4 19.5 4 14S8.5 4 14 4s10 4.5 10 10-4.5 10-10 10zm1.7-4.7c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm3.3-8.2c0 3.3-3.6 3.3-3.6 4.6v.2c0 .4-.3.7-.7.7h-2.1c-.4 0-.7-.3-.7-.7v-.3c0-1.8 1.3-2.5 2.3-3 .9-.5 1.4-.8 1.4-1.5 0-.8-1.1-1.4-2-1.4-1.1 0-1.6.5-2.4 1.4-.2.3-.7.3-.9.1l-1.2-.9c-.1-.3-.2-.7 0-1 1.2-1.7 2.6-2.6 4.9-2.6 2.4 0 5 1.9 5 4.4z"
      ],
      tags: ["question-mark"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M245.9-211.5l-52.2 52.2c-44.9-44.9-106.9-72.6-175.4-72.6-132.8 0-241.2 104.3-247.7 235.5-.3 6.8 5.2 12.5 12 12.5h28c6.4 0 11.6-5 12-11.3 5.8-103.1 91.1-184.7 195.7-184.7 54.2 0 103.2 21.9 138.6 57.4l-54.1 54.1c-7.6 7.6-2.2 20.5 8.5 20.5h143c6.6 0 12-5.4 12-12v-143c.1-10.8-12.9-16.1-20.4-8.6zm8.2 227.6h-28c-6.4 0-11.6 5-12 11.3-5.8 103.1-91.1 184.7-195.7 184.7-54.2 0-103.2-21.9-138.6-57.4l54.1-54.1c7.6-7.6 2.2-20.5-8.5-20.5h-143c-6.6 0-12 5.4-12 12v143c0 10.7 12.9 16 20.5 8.5l52.2-52.2C-112 236.3-50 264 18.5 264c132.8 0 241.2-104.3 247.7-235.5.2-6.8-5.3-12.4-12.1-12.4zM26.8 1.2l-2.9 2.9C21.4 1.6 17.9 0 14 0 6.5 0 .4 5.9 0 13.3c0 .4.3.7.7.7h1.6c.4 0 .7-.3.7-.6.3-5.9 5.1-10.5 11-10.5 3.1 0 5.8 1.2 7.8 3.2l-3.1 3.1c-.4.4-.1 1.2.5 1.2h8.1c.4 0 .7-.3.7-.7V1.6c0-.6-.7-.9-1.2-.4zM27.3 14h-1.6c-.4 0-.7.3-.7.6-.3 5.8-5.1 10.4-11 10.4-3.1 0-5.8-1.2-7.8-3.2l3.1-3.1c.4-.4.1-1.2-.5-1.2H.7c-.4 0-.7.3-.7.7v8.1c0 .6.7.9 1.2.5l2.9-2.9c2.5 2.5 6 4.1 9.9 4.1 7.5 0 13.6-5.9 14-13.3 0-.4-.3-.7-.7-.7z"
      ],
      tags: ["refresh"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M14 14l-2.8-2.8-8.4-8.4L0 5.6 8.4 14 0 22.4l2.8 2.8 8.4-8.4L14 14z"],
      tags: ["right-arrow"],
      viewBox: ["0 0 14 28"]
    },
    {
      paths: ["M0 12h24v4H0zM10 0h14v4H10zM10 24h14v4H10z"],
      tags: ["align--right"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M0 12h24v4H0zM0 0h14v4H0zM0 24h14v4H0z"],
      tags: ["align--left"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M0 0h24v4H0zM0 24h24v4H0zM0 12h24v4H0z"],
      tags: ["align--justify"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M0 0h24v4H0zM0 24h24v4H0zM5 12h14v4H5z"],
      tags: ["align--center"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M0 0v28h28V0H0zm24 24H4V4h20v20zm-3.8-10L9.8 20V8l10.4 6z"],
      tags: ["video"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M0 0v28h28V0H0zm24 4v13.5l-4.2-4.2c-.4-.4-.9-.4-1.3 0l-7.9 7.9-3-3c-.4-.4-.9-.4-1.3 0L4 20.5V4h20zM8 11.5c0-1.5 1.2-2.7 2.7-2.7s2.7 1.2 2.7 2.7c0 1.5-1.2 2.7-2.7 2.7S8 13 8 11.5z"
      ],
      tags: ["picture"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M0 0v28h28V0H0zm4 24V4h20L4 24z"],
      tags: ["gradient"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M16.6 10.1c-1.5-2.2-3.3-4.6-4.4-8.4C11.9.7 11 0 10 0 9 0 8.1.6 7.8 1.7 6.7 5.4 5 7.9 3.4 10.1 1.6 12.6.1 14.8.1 18c0 5.5 4.4 10 9.8 10s9.8-4.5 9.8-10c.1-3.2-1.4-5.4-3.1-7.9zm-6.6 14c-3.3 0-5.9-2.7-5.9-6 0-2 1-3.4 2.6-5.7 1-1.5 2.3-3.3 3.4-5.5 1.1 2.3 2.3 4.1 3.4 5.5 1.6 2.3 2.6 3.7 2.6 5.7-.2 3.3-2.8 6-6.1 6z"
      ],
      tags: ["drop"],
      viewBox: ["0 0 20 28"]
    },
    {
      paths: [
        "M6 14c0 1.7-1.3 3-3 3s-3-1.3-3-3 1.3-3 3-3 3 1.3 3 3zm8-3c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3zm11 0c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"
      ],
      tags: ["more"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M0 0v32h32V0H0zm4 4h10v10H4V4zm24 24H4V18h24v10zm0-14H18V4h10v10z"],
      tags: ["layout"],
      viewBox: ["0 0 32 32"]
    },
    {
      paths: ["M31.3 2.8L28.5 0l-9.8 9.8V0h-4v19.4L3.5 8.2.7 11.1l14 13.9v7h4V15.4z"],
      tags: ["treeview"],
      viewBox: ["0 0 32 32"]
    },
    {
      paths: [
        "M11 4c3.9 0 7 3.1 7 7 0 1.4-.4 2.8-1.2 4l-.8 1-1 .8c-1.2.8-2.5 1.2-4 1.2-3.9 0-7-3.1-7-7s3.1-7 7-7m0-4C4.9 0 0 4.9 0 11s4.9 11 11 11c2.3 0 4.5-.7 6.2-1.9l7.9 7.9 2.8-2.8-7.9-7.9c1.2-1.8 1.9-3.9 1.9-6.2C22 4.9 17.1 0 11 0z"
      ],
      tags: ["search"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M13 40.8V9h11.6c3.5 0 6.3.6 8.2 2 1.8 1.4 2.9 3.1 2.9 5.5 0 1.7-.6 3.3-1.8 4.6-1.2 1.4-2.7 2.2-4.5 2.7v.2c2.3.2 4.1 1.1 5.4 2.5 1.4 1.4 2.1 3.2 2.1 5.1 0 3-1 5.2-3.1 7-2.1 1.8-5 2.5-8.5 2.5L13 40.8zm7.2-26.5v7.5h3.2c1.4 0 2.7-.3 3.5-1.1.8-.8 1.3-1.8 1.3-3 0-2.4-1.8-3.5-5.2-3.5l-2.8.1zm0 12.9v8.4h3.9c1.7 0 3-.4 3.9-1.1.9-.8 1.4-1.8 1.4-3.2.1-1.1-.5-2.3-1.4-3-1-.7-2.2-1.1-3.9-1.1h-3.9z"
      ],
      tags: ["bold"]
    },
    {
      paths: [
        "M39 8h-1c-.5 0-1 .5-1 1v.8C35.6 8.7 33.9 8 32 8c-4.4 0-8 3.6-8 8v2c0 4.4 3.6 7.9 8 7.9 1.9 0 3.6-.7 5-1.8v.9c0 .5.4 1 1 1h1c.5 0 1-.5 1-1V9c0-.5-.4-1-1-1zm-2 10c0 2.8-2.3 5-5 5s-5-2.3-5-5v-2c0-2.8 2.3-5 5-5s5 2.3 5 5v2zM13.2 2.7c-.1-.5-.5-.7-.9-.7H9.6c-.4 0-.8.2-1 .7L0 24.7c-.2.5.1 1.1.6 1.3H2c.4 0 .8-.2 1-.7L5.5 19h10.9l2.5 6.4c.1.4.5.7 1 .7H21c.5 0 1-.5 1-1 0-.2 0-.2-.1-.4l-8.7-22zM6.7 16L11 5l4.3 11H6.7z"
      ],
      tags: ["capitalize"],
      viewBox: ["0 0 40 28"]
    },
    {
      paths: [
        "M20 1v1c0 .6-.4 1-1 1h-4.2L9.3 25H13c.6 0 1 .4 1 1v1c0 .6-.4 1-1 1H1c-.6 0-1-.4-1-1v-1c0-.6.4-1 1-1h4.2l5.5-22H7c-.6 0-1-.4-1-1V1c0-.6.4-1 1-1h12c.6 0 1 .4 1 1z"
      ],
      tags: ["italic"],
      viewBox: ["0 0 20 28"]
    },
    {
      paths: [
        "M33 8h-1c-.5 0-1 .5-1 1v.8C29.6 8.7 27.9 8 26 8c-4.4 0-8 3.6-8 8v2c0 4.3 3.6 8 8 8 1.9 0 3.6-.7 5-1.8v.8c0 .5.4 1 1 1h1c.5 0 1-.5 1-1V9c0-.5-.4-1-1-1zm-2 10c0 2.8-2.3 5-5 5s-5-2.3-5-5v-2c0-2.8 2.3-5 5-5s5 2.3 5 5v2zM15 8h-1c-.5 0-1 .5-1 1v.8C11.6 8.7 9.9 8 8 8c-4.4 0-8 3.6-8 8v2c0 4.3 3.6 8 8 8 1.9 0 3.6-.7 5-1.8v.8c0 .5.4 1 1 1h1c.5 0 1-.5 1-1V9c0-.5-.4-1-1-1zm-2 10c0 2.8-2.3 5-5 5s-5-2.3-5-5v-2c0-2.8 2.3-5 5-5s5 2.3 5 5v2z"
      ],
      tags: ["lowercase"],
      viewBox: ["0 0 34 28"]
    },
    {
      paths: ["M0 12.2h10.2v30.5h5.9V12.2h10.2v-5H0v5zm27.7 3.9v4.5h8.2v22.1h6v-22H50v-4.5H27.7z"],
      tags: ["smallcaps"]
    },
    {
      paths: [
        "M31 13H1c-.6 0-1 .4-1 1v1c0 .6.4 1 1 1h30c.6 0 1-.4 1-1v-1c0-.6-.4-1-1-1zM9.4 11h7.1l-2.9-1.4c-1.4-.7-2-2.4-1.3-3.8.3-.6.7-1 1.3-1.3.6-.3 1.2-.5 1.9-.5h3.9c.9 0 1.7.4 2.3 1.1l.9 1.3c.3.4 1 .5 1.4.2l1.6-1.2c.4-.3.5-1 .2-1.4l-.9-1.3C23.6 1 21.5 0 19.4 0h-3.9c-1.3 0-2.5.3-3.7.9-2.5 1.2-4 3.9-3.8 6.7.1 1.3.7 2.4 1.4 3.4zM25.2 18h-5.7l.9.4c1.4.7 2 2.4 1.3 3.8-.3.6-.7 1-1.3 1.3-.6.3-1.2.4-1.9.4h-3.9c-.9 0-1.7-.4-2.3-1.1l-.9-1.3c-.3-.4-1-.5-1.4-.2l-1.6 1.2c-.4.3-.5 1-.2 1.4l.9 1.3c1.3 1.7 3.3 2.7 5.5 2.7h3.9c1.3 0 2.5-.3 3.7-.9 2.5-1.3 4-3.9 3.8-6.8-.1-.7-.4-1.5-.8-2.2z"
      ],
      tags: ["strikethrough"],
      viewBox: ["0 0 32 28"]
    },
    {
      paths: [
        "M2.5 2.6h1.8V14c0 4.8 3.9 8.8 8.8 8.8s8.8-3.9 8.8-8.8V2.6h1.8c.5 0 .9-.4.9-.9V.9C24.4.4 24 0 23.5 0h-7c-.5 0-.9.4-.9.9v.9c0 .5.4.9.9.9h1.8V14c0 2.9-2.4 5.3-5.3 5.3S7.8 16.9 7.8 14V2.6h1.8c.5 0 .9-.4.9-.9V.9c-.1-.5-.5-.9-1-.9h-7c-.5 0-.9.4-.9.9v.9c0 .4.4.8.9.8zm21.9 22.8H1.6c-.5 0-.9.4-.9.9v.9c0 .5.4.9.9.9h22.8c.5 0 .9-.4.9-.9v-.9c0-.5-.4-.9-.9-.9z"
      ],
      tags: ["underline"],
      viewBox: ["0 0 26 28"]
    },
    {
      paths: [
        "M13.3 2.7c-.1-.5-.5-.7-.9-.7H9.7c-.5 0-.8.2-1 .7l-8.6 22c-.2.5.1 1.1.6 1.3H2c.5 0 .8-.2.9-.7L5.4 19h11.1l2.5 6.4c.1.4.5.7 1 .7h1c.5 0 1-.5 1-1 0-.2 0-.2-.1-.4l-8.6-22zM6.7 16L11 5.2 15.3 16H6.7zM36.3 2.7c-.2-.5-.5-.7-1-.7h-2.7c-.5 0-.8.2-1 .7l-8.6 22c-.2.5.1 1.1.6 1.3h1.5c.4 0 .8-.2 1-.7l2.5-6.4h11l2.5 6.4c.2.4.5.7 1 .7h.9c.5 0 1-.5 1-1 0-.2 0-.2-.1-.4L36.3 2.7zM29.7 16L34 5.1 38.3 16h-8.6z"
      ],
      tags: ["uppercase"],
      viewBox: ["0 0 45 28"]
    },
    {
      paths: ["M24 4v20H4V4h20m4-4H0v28h28V0z"],
      tags: ["all-sides"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M28 0H0v20h4V4h20v16h4V0z", "M28 24H0v4h28v-4z"],
      tags: ["border-bottom"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M28 0H8v4h16v20H8v4h20V0z", "M4 0H0v28h4V0z"],
      tags: ["border-left"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M20 0H0v28h20v-4H4V4h16V0z", "M28 0h-4v28h4V0z"],
      tags: ["border-right"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M28 8h-4v16H4V8H0v20h28V8z", "M28 0H0v4h28V0z"],
      tags: ["border-top"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M38.3 0H12.5C5.8-.2.2 5 0 11.7v25.8C-.2 44.2 5 49.8 11.7 50h25.8c6.7.2 12.3-5 12.5-11.7V12.5C50.2 5.8 45 .2 38.3 0zm3.4 12.5v25c.2 2.1-1.2 3.9-3.3 4.2H12.5c-2.1.2-3.9-1.2-4.2-3.3V12.5c-.2-2.1 1.2-3.9 3.3-4.2h25.9c2.1-.2 3.9 1.2 4.2 3.3v.9z"
      ],
      tags: ["all-corners"]
    },
    {
      paths: ["M0 10h4V4h20v20h-6v4h10V0H0z", "M14 28v-4C8.5 24 4 19.5 4 14H0c0 7.7 6.3 14 14 14z"],
      tags: ["b-l-corner"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M10 28v-4H4V4h20v6h4V0H0v28z", "M28 14h-4c0 5.5-4.5 10-10 10v4c7.7 0 14-6.3 14-14z"],
      tags: ["b-r-corner"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M18 0v4h6v20H4v-6H0v10h28V0z", "M0 14h4C4 8.5 8.5 4 14 4V0C6.3 0 0 6.3 0 14z"],
      tags: ["t-l-corner"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M28 18h-4v6H4V4h6V0H0v28h28z", "M14 0v4c5.5 0 10 4.5 10 10h4c0-7.7-6.3-14-14-14z"],
      tags: ["t-r-corner"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M10.8 21.7V28H7.1V10h7c1.4 0 2.5.2 3.6.7 1 .5 1.8 1.2 2.4 2.1.6.9.8 1.9.8 3.1 0 1.8-.6 3.2-1.8 4.2-1.2 1-2.9 1.5-5 1.5h-3.3zm0-3h3.3c1 0 1.7-.2 2.2-.7.5-.5.8-1.1.8-2 0-.9-.3-1.6-.8-2.1s-1.2-.8-2.2-.8h-3.4v5.6z",
        "M28 0v4H0V0z"
      ],
      tags: ["padding-top"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M3.7 16.7V23H0V5h7c1.4 0 2.5.2 3.6.7s1.8 1.2 2.4 2.1c.6.9.8 1.9.8 3.1 0 1.8-.6 3.2-1.8 4.2-1.2 1-2.9 1.5-5 1.5H3.7zm0-3H7c1 0 1.7-.2 2.2-.7.5-.5.8-1.1.8-2 0-.9-.3-1.6-.8-2.1C8.7 8.3 8 8 7.1 8H3.7v5.7z",
        "M24 0h4v28h-4z"
      ],
      tags: ["padding-right"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M10.8 11.7V18H7.1V0h7c1.4 0 2.5.2 3.6.7s1.8 1.2 2.4 2.1c.6.9.8 1.9.8 3.1 0 1.8-.6 3.2-1.8 4.2-1.2 1-2.9 1.5-5 1.5h-3.3zm0-3h3.3c1 0 1.7-.2 2.2-.7.5-.5.8-1.1.8-2 0-.9-.3-1.6-.8-2.1-.5-.6-1.2-.9-2.1-.9h-3.4v5.7z",
        "M28 24v4H0v-4z"
      ],
      tags: ["padding-bottom"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M17.9 16.7V23h-3.7V5h7c1.4 0 2.5.2 3.6.7s1.8 1.2 2.4 2.1c.5 1 .8 2 .8 3.2 0 1.8-.6 3.2-1.8 4.2-1.2 1-2.9 1.5-5 1.5h-3.3zm0-3h3.3c1 0 1.7-.2 2.2-.7.5-.5.8-1.1.8-2 0-.9-.3-1.6-.8-2.1-.4-.6-1.2-.9-2.1-.9h-3.4v5.7z",
        "M0 0h4v28H0z"
      ],
      tags: ["padding-left"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M9.6 10.9L14 23.3l4.4-12.4H23V28h-3.5v-4.7l.4-8.1L15.2 28h-2.4L8.2 15.3l.4 8.1V28H5V10.9h4.6z",
        "M28 0v4H0V0z"
      ],
      tags: ["margin-top"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M4.6 5.4L9 17.9l4.4-12.4H18v17.1h-3.5v-4.7l.4-8.1-4.6 12.7H7.8L3.2 9.8l.4 8.1v4.7H0V5.4h4.6z",
        "M24 0h4v28h-4z"
      ],
      tags: ["margin-right"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M9.6 0L14 12.4 18.4 0H23v17.1h-3.5v-4.7l.4-8.1L15.3 17h-2.4L8.2 4.4l.4 8.1v4.7H5V0h4.6z",
        "M28 24v4H0v-4z"
      ],
      tags: ["margin-bottom"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M14.6 5.4L19 17.9l4.4-12.4H28v17.1h-3.5v-4.7l.4-8.1-4.6 12.7h-2.4L13.2 9.8l.4 8.1v4.7H10V5.4h4.6z",
        "M0 0h4v28H0z"
      ],
      tags: ["margin-left"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M2 4h10v8H2zM16 4h10v8H16z", "M26 12H0v4h2v8h10v-8h4v8h10v-8h2v-4z"],
      tags: ["align-baseline"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M4 16h8v10H4zM4 2h8v10H4z", "M12 2v26h4v-2h8V16h-8v-4h8V2h-8V0h-4z"],
      tags: ["align-baseline-reversed"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M28 12h-2V6H16v6h-4V4H2v8H0v4h2v8h10v-8h4v6h10v-6h2z"],
      tags: ["align-center"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M2 4h10v18H2zM16 10h10v12H16zM0 24h28v4H0z"],
      tags: ["align-end"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M2 6h10v18H2zM16 6h10v12H16zM0 0h28v4H0z"],
      tags: ["align-start"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M2 6h10v16H2zM16 6h10v16H16zM0 24h28v4H0zM0 0h28v4H0z"],
      tags: ["align-stretch"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M6 26V16h16v10H6zm0-14V2h16v10H6zm18 16V0h4v28h-4zM0 28V0h4v28H0z"],
      tags: ["align-stretch-reversed"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M0 12h28v4H0zM2 0h10v10H2zM2 18h10v10H2zM16 0h10v10H16zM16 18h10v10H16z"],
      tags: ["content-center"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M0 24h28v4H0zM2 0h10v10H2zM2 12h10v10H2zM16 0h10v10H16zM16 12h10v10H16z"],
      tags: ["content-end"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M0 24h28v4H0zM0 0h28v4H0zM2 8h10v4H2zM2 16h10v4H2zM16 8h10v4H16zM16 16h10v4H16z"],
      tags: ["content-space-around"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M0 24h28v4H0zM0 0h28v4H0zM2 6h10v4H2zM2 18h10v4H2zM16 6h10v4H16zM16 18h10v4H16z"],
      tags: ["content-space-btw"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M0 0h28v4H0zM2 6h10v10H2zM2 18h10v10H2zM16 6h10v10H16zM16 18h10v10H16z"],
      tags: ["content-start"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M0 6h10v16H0zM18 6h10v16H18zM12 0h4v28h-4z"],
      tags: ["justify-center"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M0 6h10v16H0zM12 6h10v16H12zM24 0h4v28h-4z"],
      tags: ["justify-end"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M8 6h4v16H8zM16 6h4v16h-4zM24 0h4v28h-4zM0 0h4v28H0z"],
      tags: ["justify-sp-around"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M6 8h16v4H6zM6 16h16v4H6zM0 24h28v4H0zM0 0h28v4H0z"],
      tags: ["justify-sp-around-reverse"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M6 6h4v16H6zM18 6h4v16h-4zM24 0h4v28h-4zM0 0h4v28H0z"],
      tags: ["justify-sp-btw"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M6 6h16v4H6zM6 18h16v4H6zM0 24h28v4H0zM0 0h28v4H0z"],
      tags: ["justify-sp-btw-reverse"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M6 6h10v16H6zM18 6h10v16H18zM0 0h4v28H0z"],
      tags: ["justify-start"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M2 8h10v12H2z", "M16 8V0h-4v28h4v-8h10V8z"],
      tags: ["self-baseline"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M26 8H16V0h-4v8H2v12h10v8h4v-8h10z"],
      tags: ["self-center"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M24 0h4v28h-4zM0 8h22v12H0z"],
      tags: ["self-end"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M0 0h4v28H0zM6 8h22v12H6z"],
      tags: ["self-start"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["M24 0h4v28h-4zM0 0h4v28H0zM6 8h16v12H6z"],
      tags: ["self-stretch"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M43.1 8.5c-2.4-2.2-5.5-3.4-8.6-3.4-3.4 0-6.8 1.4-9.2 4l-.2.1-.2-.2-.5-.5c-2.4-2.2-5.5-3.4-8.6-3.4C12.3 5 8.9 6.5 6.5 9 1.7 14 1.9 22 7 26.9l18 18 18-18 .5-.5c4.9-5.1 4.7-13.1-.4-17.9z",
        "M15.4 11.1h.3c1.6 0 3.2.6 4.5 1.8l.4.4.2.2 3.5 3.5 4.1-2.7.3-.2.6-.4.5-.5c1.2-1.3 2.9-2 4.8-2 1.7 0 3.3.7 4.5 1.8 1.3 1.2 2 2.8 2 4.6s-.6 3.4-1.8 4.7l-.4.4L25 36.4 11.2 22.7l-.1-.1-.1-.1c-2.7-2.5-2.8-6.7-.3-9.4 1.3-1.3 3-2 4.7-2m0-6c-3.4 0-6.6 1.5-8.9 4C1.7 14.1 1.9 22 7 27l18 18 18-18 .5-.5c4.8-5.1 4.6-13.1-.5-17.9-2.4-2.2-5.5-3.4-8.6-3.4-3.4 0-6.8 1.4-9.2 4H25l-.2-.2-.5-.5c-2.4-2.2-5.5-3.4-8.6-3.4h-.3z"
      ],
      tags: ["heart"]
    },
    {
      paths: [
        "M15 4h20v15.347c4.73 2.247 8 7.068 8 12.653H27v12a2 2 0 1 1-4 0V32H7c0-5.585 3.27-10.406 8-12.653V4Z",
        "M43 32c0-1.39-.203-2.733-.58-4A14.037 14.037 0 0 0 35 19.347V4H15v15.347A14.038 14.038 0 0 0 7.58 28 14.003 14.003 0 0 0 7 32h16v12a2 2 0 1 0 4 0V32h16Zm-26.284-9.04L19 21.875V8h12v13.875l2.284 1.085A10.038 10.038 0 0 1 38.168 28H11.832a10.038 10.038 0 0 1 4.884-5.04Z"
      ],
      tags: ["pin"]
    },
    {
      paths: ["M2 6V2h24v4H2zm12 2l-2.8 2.8-7.1 7.1 2.8 2.8 5.1-5V26h4V15.7l5.1 5 2.8-2.8-7.1-7.1L14 8z"],
      tags: ["import"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M24.6 18l3.4 3.4 4.4-4.4v25.2h4.8V17l4.4 4.4L45 18l-6.8-6.8-3.4-3.4L24.6 18zM5 32l3.4-3.4 4.4 4.4V7.8h4.8V33l4.4-4.4 3.4 3.4-6.8 6.8-3.4 3.4L5 32z"
      ],
      tags: ["reverse-y"]
    },
    {
      paths: [
        "M32 24.6L28.6 28l4.4 4.4H7.8v4.8H33l-4.4 4.4L32 45l6.8-6.8 3.4-3.4L32 24.6zM18 5l3.4 3.4-4.4 4.4h25.2v4.8H17l4.4 4.4-3.4 3.4-6.8-6.8-3.4-3.4L18 5z"
      ],
      tags: ["reverse-x"]
    },
    {
      paths: [
        "M37 9H11c-1.1 0-2 .9-2 2v26c0 1.1.9 2 2 2h26c1.1 0 2-.9 2-2V11c0-1.1-.9-2-2-2zM11.6 37c-.3 0-.6-.3-.6-.6V11.6c0-.3.3-.6.6-.6h24.8c.3 0 .6.3.6.6v24.8c0 .3-.3.6-.6.6H11.6z",
        "M38 5H10c-.6 0-1 .4-1 1s.4 1 1 1h28c.6 0 1-.4 1-1s-.4-1-1-1zm0 36H10c-.6 0-1 .4-1 1s.4 1 1 1h28c.6 0 1-.4 1-1s-.4-1-1-1z",
        "M29 16H15c-.6 0-1-.4-1-1s.4-1 1-1h14c.6 0 1 .4 1 1s-.4 1-1 1z"
      ],
      tags: ["element-accordion"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M39 21H21C19.3 21 18 22.3 18 24V34C18 35.7 19.3 37 21 37H29C29.3 37 29.6 37.1 29.8 37.4L33 41.7L36.2 37.4C36.4 37.2 36.7 37 37 37H39C40.7 37 42 35.7 42 34V24C42 22.3 40.7 21 39 21ZM40 34C40 34.6 39.6 35 39 35H37C36.1 35 35.2 35.4 34.6 36.2L33 38.3L31.4 36.2C30.8 35.4 29.9 35 29 35H21C20.4 35 20 34.6 20 34V24C20 23.4 20.4 23 21 23H39C39.6 23 40 23.4 40 24V34Z",
        "M9 11C7.3 11 6 12.3 6 14V24C6 25.7 7.3 27 9 27H16V25H9C8.4 25 8 24.6 8 24V14C8 13.4 8.4 13 9 13H27C27.6 13 28 13.4 28 14V19H30V14C30 12.3 28.7 11 27 11H9Z"
      ],
      tags: ["element-comments"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M33 13H7c-1.1 0-2 .9-2 2v26c0 1.1.9 2 2 2h26c1.1 0 2-.9 2-2V15c0-1.1-.9-2-2-2zm-.6 28H7.6c-.3 0-.6-.3-.6-.6V15.6c0-.3.3-.6.6-.6h24.8c.3 0 .6.3.6.6v24.8c0 .3-.3.6-.6.6z",
        "M31.1 27.9l-3.5-3.5-9.8 9.7-4.5-4.5-1.4 1.4-3 3.1 1.4 1.4 3-3 4.5 4.4 9.8-9.7 2.1 2.1 1.4-1.4zM14.9 21c.6 0 1 .4 1 1s-.4 1-1 1-1-.4-1-1 .5-1 1-1m0-2c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z",
        "M37 9H11c-1.1 0-2 .9-2 2v2h2v-1.4c0-.3.3-.6.6-.6h24.8c.3 0 .6.3.6.6v24.8c0 .3-.3.6-.6.6H35v2h2c1.1 0 2-.9 2-2V11c0-1.1-.9-2-2-2z",
        "M41 5H15c-1.1 0-2 .9-2 2v2h2V7.6c0-.3.3-.6.6-.6h24.8c.3 0 .6.3.6.6v24.8c0 .3-.3.6-.6.6H39v2h2c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2z"
      ],
      tags: ["element-gallery"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M17.4 9.8c-.5.3-1 .6-1.3 1.1-.3.5-.5 1-.5 1.6 0 1 .3 1.8 1 2.4.7.6 1.6.9 2.8.9 1.2 0 2.1-.3 2.8-.9s1-1.4 1-2.4c0-.6-.2-1.2-.5-1.6-.3-.5-.8-.8-1.3-1.1.4-.2.7-.5 1-.8h-6c.2.3.6.6 1 .8zm.9 1.4c.3-.3.7-.4 1.1-.4.5 0 .9.1 1.2.4.3.3.4.7.4 1.2s-.1.9-.4 1.2c-.3.3-.7.4-1.2.4s-.9-.1-1.2-.4-.4-.7-.4-1.2.2-.9.5-1.2zM30.1 9.3c-.1.3-.3.5-.6.7s-.6.3-.9.3c-.5 0-.9-.2-1.2-.6-.2-.2-.3-.4-.4-.7h-2.3c.1.8.4 1.4.8 2 .6.7 1.4 1 2.4 1 .8 0 1.5-.3 2.1-.9-.2 1.8-1.2 2.7-3.2 2.8h-.5v1.9h.6c1.7-.1 3.1-.7 4-1.7.9-1.1 1.4-2.6 1.4-4.5V9H30v.3z",
        "M17.7 26.9h-.5v1.9h.6c1.7-.1 3.1-.7 4-1.7.9-1.1 1.4-2.6 1.4-4.5v-.8c0-.9-.2-1.7-.5-2.4s-.8-1.2-1.4-1.6c-.6-.4-1.3-.6-2-.6s-1.4.2-2 .5c-.6.3-1 .8-1.4 1.5-.3.6-.5 1.3-.5 2.1 0 1.2.3 2.1.9 2.8.6.7 1.4 1 2.4 1 .8 0 1.5-.3 2.1-.9-.1 1.7-1.1 2.6-3.1 2.7zm2.6-3.9c-.3.2-.6.3-.9.3-.5 0-.9-.2-1.2-.6-.3-.4-.4-.9-.4-1.5s.1-1.1.4-1.6c.3-.4.7-.6 1.1-.6.5 0 .9.2 1.2.6.3.4.4 1 .4 1.8v.9c-.1.3-.3.5-.6.7zM28.6 28.8c1.3 0 2.2-.4 2.9-1.3s1-2 1-3.6v-2.1c0-1.5-.4-2.7-1-3.5-.7-.8-1.6-1.2-2.8-1.2s-2.2.4-2.8 1.2c-.7.8-1 2-1 3.6V24c0 1.5.4 2.7 1 3.5s1.5 1.3 2.7 1.3zM27 21.5c0-.9.1-1.5.4-1.9.3-.4.6-.6 1.2-.6.5 0 .9.2 1.2.6s.4 1.1.4 2.1v2.7c0 .9-.1 1.6-.4 2s-.7.6-1.2.6c-.6 0-1-.2-1.2-.7-.3-.5-.4-1.1-.4-2.1v-2.7z",
        "M23.2 35.6v-.8c0-.9-.2-1.7-.5-2.4s-.8-1.2-1.4-1.6c-.6-.4-1.3-.6-2-.6s-1.4.2-2 .5c-.6.3-1 .8-1.4 1.5-.3.6-.5 1.3-.5 2.1 0 1.2.3 2.1.9 2.8.6.7 1.4 1 2.4 1 .8 0 1.5-.3 2.1-.9-.1.8-.3 1.4-.8 1.9h2.4c.5-1 .8-2.2.8-3.5zm-2.3-.3c-.1.3-.3.5-.6.7-.3.2-.6.3-.9.3-.5 0-.9-.2-1.2-.6-.3-.4-.4-.9-.4-1.5s.1-1.1.4-1.6c.3-.4.7-.6 1.1-.6.5 0 .9.2 1.2.6.3.4.4 1 .4 1.8v.9zM30.3 30.3H30L25.3 32v1.8L28 33v6h2.3z"
      ],
      tags: ["element-counter-free"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M36.3 25.9H30l2 2.1c-1.5 2.3-3.9 3.9-6.6 4.3V18.2c1.6-.6 2.9-2.2 2.9-4-.1-2.3-2-4.2-4.3-4.2s-4.2 1.9-4.2 4.2c0 1.9 1.2 3.4 2.9 4v14.1c-2.7-.4-5.1-1.9-6.6-4.3l2.1-2.1h-6.4v6.3L14 30c2 2.9 5.2 4.7 8.6 5.1v1.6c0 .8.6 1.4 1.4 1.4s1.4-.6 1.4-1.4v-1.5c3.5-.4 6.6-2.2 8.6-5.1l2.2 2.2v-6.4z"
      ],
      tags: ["element-anchor-point"],
      viewBox: ["0 0 48 48"]
    },
    {
      circle: ['cx="24" cy="30.5" r="1.5"', 'cx="19" cy="30.5" r="1.5"', 'cx="29" cy="30.5" r="1.5"'],
      paths: ["M11 13h2v2h-2z", "M9 13h2v22H9zM11 33h2v2h-2zM37 13h2v22h-2zM35 13h2v2h-2zM35 33h2v2h-2z"],
      tags: ["element-shortcode"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M34.3 23.1c-.3 0-.7 0-1 .1-.4-4.3-4-7.6-8.4-7.6-.5 0-.9.4-.9.9v15c0 .5.4.9.9.9h9.4c2.6 0 4.7-2.1 4.7-4.7 0-2.5-2.1-4.6-4.7-4.6zM21.2 17.4c-.5 0-.9.4-.9.9v13.1c0 .5.4.9.9.9s.9-.4.9-.9v-13c0-.5-.4-1-.9-1zM17.4 21.2c-.5 0-.9.4-.9.9v9.4c0 .5.4.9.9.9s.9-.4.9-.9v-9.4c.1-.5-.3-.9-.9-.9zM13.7 21.2c-.5 0-.9.4-.9.9v9.4c0 .5.4.9.9.9s.9-.4.9-.9v-9.4c0-.5-.4-.9-.9-.9zM9.9 24c-.5 0-.9.4-.9.9v5.6c0 .5.4.9.9.9s.9-.4.9-.9v-5.6c.1-.5-.3-.9-.9-.9z"
      ],
      tags: ["element-soundcloud"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M41.6 28c.2.9.2 2-.2 3.2-1 3.1-3.4 8.8-4.3 10.9 3.5-1.6 5.9-5 5.9-9.1 0-1.8-.5-3.5-1.4-5zM24.3 29h-.5c-.5 1.1-.8 2.6-.8 4 0 4.1 2.5 7.6 6.1 9.1l-3.8-12.5s-.5-.6-1-.6zM30.6 42.7c.8.2 1.5.3 2.4.3.8 0 1.7-.1 2.4-.3l-2.2-6.2-2.6 6.2z",
        "M37.9 29.7c-1.7-1.1-1.3-3.1 0-3.9.6-.3 1.1-.4 1.6-.3-1.7-1.5-4-2.5-6.5-2.5-3.9 0-7.2 2-8.9 5.1h5.1v.9h-.1c-.1 0-.3.2-.4.3-.1.1-.2.7-.2.7l2.1 8.7 1.9-4.3-.9-2.9c-.3-.9-.6-1.7-.8-2-.2-.2-.4-.5-.6-.5H30v-.9h6v.9h-1c-.2 0-.3.2-.4.3-.1.1-.2.4-.2.6 0 .2.1.7.1.7l2.3 8s2.4-4.6 2.4-5.6c.1-1-.3-2.6-1.3-3.3z",
        "M11.6 37c-.3 0-.6-.3-.6-.6V19h26v2.7c.7.2 1.4.6 2 .9V11c0-1.1-.9-2-2-2H11c-1.1 0-2 .9-2 2v26c0 1.1.9 2 2 2h11.6c-.4-.6-.7-1.3-.9-2H11.6zM11 11.6c0-.3.3-.6.6-.6h24.8c.3 0 .6.3.6.6V17H11v-5.4z"
      ],
      tags: ["element-wp"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M35 19v15.4c0 .3-.3.6-.6.6H13.6c-.3 0-.6-.3-.6-.6V19h-2v16c0 1.1.9 2 2 2h22c1.1 0 2-.9 2-2V19h-2zM37 11H11c-1.1 0-2 .9-2 2v4c0 1.1.9 2 2 2h26c1.1 0 2-.9 2-2v-4c0-1.1-.9-2-2-2zm-.6 6H11.6c-.3 0-.6-.3-.6-.6v-2.8c0-.3.3-.6.6-.6h24.8c.3 0 .6.3.6.6v2.8c0 .3-.3.6-.6.6z",
        "M27.5 26h-7c-.8 0-1.5-.7-1.5-1.5s.7-1.5 1.5-1.5h7c.8 0 1.5.7 1.5 1.5s-.7 1.5-1.5 1.5z",
        "M40 25c-.6 0-1 .4-1 1v5c0 .6.4 1 1 1s1-.4 1-1v-5c0-.6-.4-1-1-1z",
        "M19.5 40c0-.6-.4-1-1-1h-5c-.6 0-1 .4-1 1s.4 1 1 1h5c.6 0 1-.4 1-1z"
      ],
      circle: ['cx="40" cy="22" r="1"', ' cx="22.5" cy="40" r="1" fill="currentColor"'],
      tags: ["element-archive"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M14.8 16c.1-.1.3-.2.4-.4.2-.1.3-.2.5-.4.3-.2.5-.4.7-.5s.4-.4.6-.6c.2-.2.3-.5.4-.8.1-.3.2-.6.2-1 0-.3-.1-.6-.2-.9-.1-.3-.3-.5-.5-.7-.2-.2-.5-.3-.8-.4-.3-.1-.7-.2-1.1-.2-.8 0-1.6.2-2.3.7v1.5c.6-.5 1.2-.8 1.9-.8.4 0 .7.1.9.3.2.2.3.4.3.8 0 .2 0 .3-.1.5-.1.1-.1.3-.3.4l-.4.4c-.2.1-.4.3-.6.5-.2.2-.4.3-.7.5-.2.2-.5.4-.7.7-.2.3-.4.5-.5.8-.1.3-.2.7-.2 1.1v.6h5.2v-1.4h-3.3c0-.1 0-.2.1-.3.3-.1.4-.3.5-.4zM23.6 15.2v-4.9H22c-.2.4-.4.8-.7 1.2-.3.4-.5.9-.8 1.3l-.9 1.2c-.3.4-.6.8-.9 1.1v1.3h3.4V18h1.6v-1.6h.9v-1.3h-1zm-1.5 0h-1.8c.2-.2.3-.4.5-.6.2-.2.3-.4.5-.7.2-.2.3-.5.5-.7.1-.2.3-.5.4-.7v2.7z",
        "M29.5 24.3h-21c-.8 0-1.5-.7-1.5-1.5s.7-1.5 1.5-1.5h21c.8 0 1.5.7 1.5 1.5s-.7 1.5-1.5 1.5z",
        "M39.5 24.3h-31c-.8 0-1.5-.7-1.5-1.5s.7-1.5 1.5-1.5h31c.8 0 1.5.7 1.5 1.5s-.7 1.5-1.5 1.5z",
        "M39.5 31.3h-31c-.8 0-1.5-.7-1.5-1.5s.7-1.5 1.5-1.5h31c.8 0 1.5.7 1.5 1.5s-.7 1.5-1.5 1.5z",
        "M33.5 31.3h-25c-.8 0-1.5-.7-1.5-1.5s.7-1.5 1.5-1.5h25c.8 0 1.5.7 1.5 1.5s-.7 1.5-1.5 1.5z",
        "M39.5 38.3h-31c-.8 0-1.5-.7-1.5-1.5s.7-1.5 1.5-1.5h31c.8 0 1.5.7 1.5 1.5s-.7 1.5-1.5 1.5z",
        "M21.5 38.3h-13c-.8 0-1.5-.7-1.5-1.5s.7-1.5 1.5-1.5h13c.8 0 1.5.7 1.5 1.5s-.7 1.5-1.5 1.5z"
      ],
      tags: ["element-bar-counter"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M10 18h28v12H10z",
        "M17 23h14v2H17z",
        "M31 37.4v-4.2s0-1.2-.7-1.2-.8 1.1-.8 1.1l-.1 1c0 .1-.1.4-.4.4-.2 0-.4-.2-.4-.4l-.1-2.1c0-.4-.3-1-.8-1-.6 0-.8.7-.8 1l-.2 2.2s0 .4-.3.4c-.2 0-.4-.2-.4-.4l-.1-2.3c0-.5-.2-1.2-.8-1.2-.6 0-.8.8-.8 1.2l-.1 2.4c0 .2-.1.4-.4.4-.2 0-.4-.1-.4-.4l-.1-2-.1-4.7c0-.5-.2-1.2-.8-1.2-.7 0-.8.7-.9 1.1l-.6 10.4-1-2.4-.9-1s-.5-.6-1.1-.8c-.3-.1-.7-.1-.9.2-.2.3.2 1 .2 1l.6 1.1c.5.9 1.2 2.3 1.6 3.7 1.1 3 2.8 5 2.8 5H28.4c2.6-3.4 2.6-7.3 2.6-7.3z"
      ],
      tags: ["element-button"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M28 9H2c-1.1 0-2 .9-2 2v26c0 1.1.9 2 2 2h26c1.1 0 2-.9 2-2V11c0-1.1-.9-2-2-2zM2.6 37c-.3 0-.6-.3-.6-.6V11.6c0-.3.3-.6.6-.6h24.8c.3 0 .6.3.6.6v24.8c0 .3-.3.6-.6.6H2.6z",
        "M48 9H36c-1.1 0-2 .9-2 2v26c0 1.1.9 2 2 2h12v-2H36.6c-.3 0-.6-.3-.6-.6V11.6c0-.3.3-.6.6-.6H48V9z"
      ],
      tags: ["element-carousel"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M46 9H36c-1.1 0-2 .9-2 2v26c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V11c0-1.1-.9-2-2-2zm-.6 28h-8.8c-.3 0-.6-.3-.6-.6V11.6c0-.3.3-.6.6-.6h8.8c.3 0 .6.3.6.6v24.8c0 .3-.3.6-.6.6z",
        "M29 9H19c-1.1 0-2 .9-2 2v26c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V11c0-1.1-.9-2-2-2zm-.6 28h-8.8c-.3 0-.6-.3-.6-.6V11.6c0-.3.3-.6.6-.6h8.8c.3 0 .6.3.6.6v24.8c0 .3-.3.6-.6.6z",
        "M12 9H2c-1.1 0-2 .9-2 2v26c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V11c0-1.1-.9-2-2-2zm-.6 28H2.6c-.3 0-.6-.3-.6-.6V11.6c0-.3.3-.6.6-.6h8.8c.3 0 .6.3.6.6v24.8c0 .3-.3.6-.6.6z"
      ],
      tags: ["element-column"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M20.3 26.3h3v1.3h-4.7V27c0-.4.1-.7.2-1s.3-.5.5-.8.4-.4.6-.6.4-.3.6-.5c.2-.1.4-.3.5-.4l.4-.4c.1-.1.2-.3.2-.4s.1-.3.1-.4c0-.3-.1-.5-.3-.7s-.4-.2-.8-.2c-.6 0-1.2.2-1.7.7V21c.6-.4 1.3-.6 2.1-.6.4 0 .7 0 1 .1s.5.2.7.4.3.4.4.6.2.5.2.8c0 .3 0 .6-.1.9s-.2.5-.4.7l-.6.6c-.2.2-.4.3-.7.5-.2.1-.3.2-.5.3s-.3.2-.4.3l-.3.3c-.1.1 0 .3 0 .4zM28.6 20.6V25h.8v1.2h-.8v1.4h-1.4v-1.4h-3.1V25c.3-.3.6-.6.8-1s.6-.7.8-1.1.5-.8.8-1.1.4-.8.6-1.1h1.5zM25.5 25h1.7v-2.4c-.1.2-.2.4-.4.6s-.3.4-.4.6-.3.4-.4.6-.4.4-.5.6z",
        "M11 24c0-7.2 5.8-13 13-13 3.1 0 5.9 1.1 8.1 2.9l1.4-1.4c-.1-.1-.2-.1-.3-.2C30.7 10.2 27.5 9 24 9 15.7 9 9 15.7 9 24s6.7 15 15 15v-2c-7.2 0-13-5.8-13-13z"
      ],
      tags: ["element-circle-counter"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M11.64 16.98c-.85 0-1.5.28-1.96.85-.45.57-.68 1.4-.68 2.49v1.44c.01 1.05.24 1.85.69 2.42.45.56 1.1.84 1.96.84.86 0 1.52-.29 1.97-.86.45-.57.67-1.4.67-2.48v-1.44c-.01-1.05-.24-1.85-.69-2.41s-1.11-.85-1.96-.85zm1.09 4.97c-.01.61-.09 1.07-.26 1.37-.17.3-.44.45-.82.45-.38 0-.66-.16-.83-.46-.17-.31-.26-.78-.26-1.42v-1.9c.01-.6.1-1.04.28-1.32.17-.28.44-.42.81-.42.38 0 .65.15.83.44.18.3.27.77.27 1.42v1.84zm7.17-4.13c-.45-.56-1.1-.84-1.96-.84s-1.5.28-1.96.85c-.45.57-.68 1.39-.68 2.49v1.44c.01 1.05.24 1.85.69 2.42.45.56 1.1.84 1.96.84.86 0 1.52-.29 1.97-.86.45-.57.67-1.4.67-2.48v-1.44c0-1.05-.24-1.86-.69-2.42zm-.86 4.13c-.01.61-.09 1.07-.26 1.37-.17.3-.44.45-.82.45-.38 0-.66-.16-.83-.46-.17-.31-.26-.78-.26-1.42v-1.9c.01-.6.1-1.04.28-1.32.17-.28.44-.42.81-.42.38 0 .65.15.83.44.18.3.27.77.27 1.42v1.84zm5.47.65c.17.15.25.35.25.6 0 .24-.08.44-.25.59-.17.15-.38.23-.63.23s-.46-.08-.63-.23c-.17-.14-.25-.34-.25-.58 0-.25.08-.45.25-.6.17-.15.38-.23.63-.23.25-.01.46.07.63.22zm-1.26-3.2a.75.75 0 01-.25-.59c0-.25.08-.45.25-.6.17-.15.38-.23.63-.23s.46.08.63.23c.17.15.25.35.25.6 0 .24-.08.44-.25.59s-.38.23-.63.23c-.26 0-.47-.08-.63-.23zm8.34-1.58c-.45-.56-1.1-.84-1.96-.84s-1.5.28-1.96.85-.67 1.4-.67 2.49v1.44c.01 1.05.24 1.85.69 2.42s1.1.84 1.96.84c.86 0 1.52-.29 1.97-.86s.67-1.4.67-2.48v-1.44c-.01-1.05-.25-1.86-.7-2.42zm-.86 4.13c-.01.61-.09 1.07-.26 1.37-.17.3-.44.45-.82.45-.38 0-.66-.15-.83-.46s-.26-.78-.26-1.42v-1.9c.01-.6.1-1.04.28-1.32.17-.28.44-.42.81-.42.38 0 .65.15.83.44.18.3.27.77.27 1.42v1.84zM37.2 26h-1.55v-3.22l-1.85.58V22.1l3.24-1.16h.17V26zm-1.55-5.98c.86 0 1.52-.29 1.97-.86s.67-1.4.67-2.48V16h-1.55v.95c-.01.61-.09 1.07-.26 1.37-.17.3-.44.45-.82.45-.38 0-.66-.15-.83-.46s-.26-.78-.26-1.42V16H33v.76c.01 1.05.24 1.85.69 2.42s1.11.84 1.96.84z",
        "M44 30V13.98c0-2.2-1.78-3.98-3.98-3.98H7.99C5.79 10 4 11.79 4 13.99V30H0v8h4c.55 0 1-.45 1-1s-.45-1-1-1H2v-4h29c.55 0 1-.45 1-1s-.45-1-1-1H6v-2h36v2h-1c-.55 0-1 .45-1 1s.45 1 1 1h5v4H14c-.55 0-1 .45-1 1s.45 1 1 1h34v-8h-4zm-2-4H6V16h36v10zm0-12H6v-.01c0-1.1.89-1.99 1.99-1.99h32.03c1.09 0 1.98.89 1.98 1.98V14zM11 37c0 .55-.45 1-1 1H8c-.55 0-1-.45-1-1s.45-1 1-1h2c.55 0 1 .45 1 1zm24-5c-.55 0-1-.45-1-1s.45-1 1-1h2c.55 0 1 .45 1 1s-.45 1-1 1h-2z"
      ],
      tags: ["element-countdown"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M24 5C13.51 5 5 13.51 5 24s8.51 19 19 19 19-8.51 19-19S34.49 5 24 5zm0 36c-9.37 0-17-7.63-17-17S14.63 7 24 7s17 7.63 17 17-7.63 17-17 17z",
        "M28.38 26.45c-1.08 0-2.05.5-2.72 1.26l-4.62-2.66a3.616 3.616 0 00.06-1.84l4.7-2.72c.64.67 1.58 1.11 2.6 1.11 1.99 0 3.6-1.6 3.6-3.59 0-1.99-1.64-3.65-3.62-3.65-1.99 0-3.59 1.61-3.59 3.59 0 .12 0 .26.03.38l-4.97 2.86c-.61-.5-1.4-.79-2.25-.79-1.99.01-3.6 1.61-3.6 3.6s1.61 3.59 3.59 3.59c.79 0 1.49-.26 2.1-.67l5.11 2.92v.18c-.06 2.02 1.58 3.62 3.56 3.62 1.99 0 3.59-1.61 3.59-3.59.02-1.99-1.59-3.6-3.57-3.6zm0-9.76c.7 0 1.26.56 1.26 1.26s-.56 1.26-1.26 1.26-1.26-.56-1.26-1.26.56-1.26 1.26-1.26zm-10.82 8.57c-.7 0-1.26-.56-1.26-1.26s.56-1.26 1.26-1.26 1.26.56 1.26 1.26-.55 1.26-1.26 1.26zm10.82 6.05c-.7 0-1.26-.56-1.26-1.26s.56-1.29 1.26-1.29 1.26.56 1.26 1.26-.56 1.29-1.26 1.29z"
      ],
      tags: ["element-social-share"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M17 31c0 .55-.45 1-1 1h-2c-.55 0-1-.45-1-1s.45-1 1-1h2c.55 0 1 .45 1 1zm23-15H8c-4.42 0-8 3.58-8 8s3.58 8 8 8h2c.55 0 1-.45 1-1s-.45-1-1-1H8c-3.31 0-6-2.69-6-6s2.69-6 6-6h32c3.31 0 6 2.69 6 6s-2.69 6-6 6H20c-.55 0-1 .45-1 1s.45 1 1 1h20c4.42 0 8-3.58 8-8s-3.58-8-8-8z",
        "M21 25H7c-.55 0-1-.45-1-1s.45-1 1-1h14c.55 0 1 .45 1 1s-.45 1-1 1z",
        "M39.88 20.7a4.008 4.008 0 00-5.66 0 4.008 4.008 0 000 5.66 3.991 3.991 0 004.85.61l1.51 1.51L42 27.06l-1.51-1.51c.9-1.54.71-3.54-.61-4.85zm-1.42 4.24c-.78.78-2.05.78-2.83 0-.78-.78-.78-2.05 0-2.83s2.05-.78 2.83 0c.78.78.78 2.05 0 2.83z"
      ],
      tags: ["element-search-form"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M18 22.5c-.8 0-1.5.7-1.5 1.5s.7 1.5 1.5 1.5 1.5-.7 1.5-1.5-.7-1.5-1.5-1.5zm6 0c-.8 0-1.5.7-1.5 1.5s.7 1.5 1.5 1.5 1.5-.7 1.5-1.5-.7-1.5-1.5-1.5zm6 0c-.8 0-1.5.7-1.5 1.5s.7 1.5 1.5 1.5 1.5-.7 1.5-1.5-.7-1.5-1.5-1.5zm-15.5-7l-7.1 7.1L6 24l1.4 1.4 7.1 7.1 1.4-1.4L8.8 24l7.1-7.1-1.4-1.4zm26.1 7.1l-7.1-7.1-1.4 1.4 7.1 7.1-7.1 7.1 1.4 1.4 7.1-7.1L42 24l-1.4-1.4z"
      ],
      tags: ["element-pagination"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M38 43H14c-1.7 0-3-1.3-3-3V8c0-1.7 1.3-3 3-3h15.9C36 5 41 10 41 16.1V40c0 1.7-1.3 3-3 3zM14 7c-.6 0-1 .4-1 1v32c0 .6.4 1 1 1h24c.6 0 1-.4 1-1V16.1c0-5-4.1-9.1-9.1-9.1H14z",
        "M31 37H9c-1.1 0-2-.9-2-2v-9c0-1.1.9-2 2-2h22c1.1 0 2 .9 2 2v9c0 1.1-.9 2-2 2z",
        "M14.7 33.1h-1.1v-2h-2v2h-1.1v-4.9h1.1v2h2v-2h1.1v4.9zm4.6-4.1h-1.4v4h-1.1v-4h-1.4v-.9h3.9v.9zm6.2 4.1h-1.1v-2.9-1c-.1.3-.1.5-.1.6l-1.1 3.3h-.9l-1.2-3.3c0-.1-.1-.3-.2-.7v4h-1v-4.9h1.6l1 2.9c.1.2.1.5.2.7.1-.3.1-.5.2-.7l1-2.9h1.6v4.9zm4.1 0h-2.9v-4.9h1.1v4h1.8v.9z"
      ],
      tags: ["element-code"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M37 9H11c-1.1 0-2 .9-2 2v26c0 1.1.9 2 2 2h26c1.1 0 2-.9 2-2V11c0-1.1-.9-2-2-2zm-.6 28H11.6c-.3 0-.6-.3-.6-.6V11.6c0-.3.3-.6.6-.6h24.8c.3 0 .6.3.6.6v24.8c0 .3-.3.6-.6.6z"
      ],
      tags: ["element-default"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M38 23H10c-.6 0-1 .4-1 1s.4 1 1 1h28c.6 0 1-.4 1-1s-.4-1-1-1z",
        "M24 13.2c-.5 0-1 .2-1.4.6l-3.5 3.5c-.4.4-.4 1 0 1.4.2.2.5.3.7.3.3 0 .5-.1.7-.3l3.1-3.1c.1-.1.3-.2.4-.2s.3.1.4.2l3.1 3.1c.2.2.5.3.7.3.3 0 .5-.1.7-.3.4-.4.4-1 0-1.4l-3.5-3.5c-.4-.4-.9-.6-1.4-.6z",
        "M28.2 29c-.3 0-.5.1-.7.3l-3.1 3.1c-.1.1-.3.2-.4.2s-.3-.1-.4-.2l-3.1-3.1c-.2-.2-.5-.3-.7-.3-.3 0-.5.1-.7.3-.4.4-.4 1 0 1.4l3.5 3.5c.4.4.9.6 1.4.6s1-.2 1.4-.6l3.5-3.5c.4-.4.4-1 0-1.4-.1-.2-.4-.3-.7-.3z"
      ],
      tags: ["element-divider"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M37 8H11c-1.1 0-2 .9-2 2v3c0 1.1.9 2 2 2h26c1.1 0 2-.9 2-2v-3c0-1.1-.9-2-2-2zm-.6 5H11.6c-.3 0-.6-.3-.6-.6v-1.8c0-.3.3-.6.6-.6h24.8c.3 0 .6.3.6.6v1.8c0 .3-.3.6-.6.6zM37 17H11c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h26c1.1 0 2-.9 2-2V19c0-1.1-.9-2-2-2zm-.6 14H11.6c-.3 0-.6-.3-.6-.6V19.6c0-.3.3-.6.6-.6h24.8c.3 0 .6.3.6.6v10.8c0 .3-.3.6-.6.6z",
        "M23 38c0 1.1-.9 2-2 2H11c-1.1 0-2-.9-2-2v-1c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2v1z"
      ],
      tags: ["element-form"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M32 21.2c0-.2-.3-.4-.5-.4l-4.8-.7-2.2-4.4c-.1-.3-.4-.4-.6-.3h-.1l-.3.3-2.2 4.4-4.8.7c-.3 0-.5.3-.5.6 0 .1.1.3.2.4l3.5 3.4-.9 4.8c0 .3.2.6.5.6h.4l4.3-2.3 4.3 2.3h.6c.2-.1.3-.3.3-.5l-.9-4.9 3.5-3.4c.2-.2.2-.4.2-.6z"
      ],
      circle: ['cx="24" cy="24" r="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"'],
      tags: ["element-icon"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M38 36c0 1.1-.9 2-2 2H12c-1.1 0-2-.9-2-2V12c0-1.1.9-2 2-2h24c1.1 0 2 .9 2 2v24z",
        "M35.2 25.5L31.7 22l-9.8 9.7-4.5-4.5-1.4 1.4-3 3.1 1.4 1.4 3-3 4.5 4.4 9.8-9.7 2.1 2.1zM19 18.6c.6 0 1 .4 1 1s-.4 1-1 1-1-.4-1-1 .5-1 1-1m0-2c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"
      ],
      tags: ["element-image"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M30 34c.5 0 1-.5 1-1s-.5-1-1-1H18c-.5 0-1 .5-1 1s.5 1 1 1h12zM33 36H15c-.5 0-1 .5-1 1s.5 1 1 1h18c.5 0 1-.5 1-1s-.5-1-1-1zM33 40H15c-.5 0-1 .5-1 1s.5 1 1 1h18c.5 0 1-.5 1-1s-.5-1-1-1zM34.4 6H13.6c-.9 0-1.6.7-1.6 1.6v20.8c0 .9.7 1.6 1.6 1.6h20.8c.9 0 1.6-.7 1.6-1.6V7.6c0-.9-.7-1.6-1.6-1.6zm0 21.9c0 .2-.2.5-.5.5H14.1c-.2 0-.5-.2-.5-.5V8.1c0-.2.2-.5.5-.5h19.8c.2 0 .5.2.5.5v19.8z",
        "M19.9 15.4c1.4 0 2.4-1 2.4-2.4s-1-2.4-2.4-2.4-2.4 1-2.4 2.4 1.1 2.4 2.4 2.4zm0-3.2c.5 0 .8.3.8.8s-.3.8-.8.8-.8-.3-.8-.8.4-.8.8-.8zM22.2 22.7l-3.6-3.6-1.1 1.1-2.4 2.5 1.1 1.1 2.4-2.4 3.6 3.5 7.9-7.7 1.7 1.7 1.1-1.2-2.8-2.8z"
      ],
      tags: ["element-image-box"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M33 13H7c-1.1 0-2 .9-2 2v26c0 1.1.9 2 2 2h26c1.1 0 2-.9 2-2V15c0-1.1-.9-2-2-2zm-.6 28H7.6c-.3 0-.6-.3-.6-.6V15.6c0-.3.3-.6.6-.6h24.8c.3 0 .6.3.6.6v24.8c0 .3-.3.6-.6.6z",
        "M31.1 27.9l-3.5-3.5-9.8 9.7-4.5-4.5-1.4 1.4-3 3.1 1.4 1.4 3-3 4.5 4.4 9.8-9.7 2.1 2.1 1.4-1.4zM14.9 21c.6 0 1 .4 1 1s-.4 1-1 1-1-.4-1-1 .5-1 1-1m0-2c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z",
        "M37 9H11c-1.1 0-2 .9-2 2v2h2v-1.4c0-.3.3-.6.6-.6h24.8c.3 0 .6.3.6.6v24.8c0 .3-.3.6-.6.6H35v2h2c1.1 0 2-.9 2-2V11c0-1.1-.9-2-2-2z",
        "M41 5H15c-1.1 0-2 .9-2 2v2h2V7.6c0-.3.3-.6.6-.6h24.8c.3 0 .6.3.6.6v24.8c0 .3-.3.6-.6.6H39v2h2c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2z"
      ],
      tags: ["element-image-gallery"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M34.3 25.5l4.6 9.5c.2.4 0 .7-.4.7h-29c-.4 0-.6-.3-.4-.7l4.6-9.5c.1-.2.3-.3.4-.3h4c.1 0 .3.1.4.2.3.3.5.6.8.9.3.3.5.6.8.9h-4.7c-.2 0-.4.1-.4.3l-3.1 6.3h24.3l-3.1-6.3c-.1-.2-.3-.3-.4-.3H28c.3-.3.5-.6.8-.9.3-.3.5-.6.8-.9.1-.1.2-.2.4-.2h4c0 .1.2.2.3.3zm-3.5-6.4c0 5.2-4.3 6.2-6.3 11.1-.2.4-.7.4-.9 0-1.8-4.5-5.5-5.7-6.2-9.7-.7-3.9 2-7.8 6-8.2 4-.4 7.4 2.8 7.4 6.8zm-3.2 0c0-2-1.6-3.6-3.6-3.6s-3.6 1.6-3.6 3.6 1.6 3.6 3.6 3.6 3.6-1.7 3.6-3.6z"
      ],
      tags: ["element-map"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: ["M38 36c0 1.1-.9 2-2 2H12c-1.1 0-2-.9-2-2V12c0-1.1.9-2 2-2h24c1.1 0 2 .9 2 2v24z", "M30.2 24l-10.4-6v12z"],
      tags: ["element-media"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M36.9 24h-6.4V13.3c0-1.2-1-2.1-2.1-2.1H11.1c-1.2 0-2.1 1-2.1 2.1v18.2c0 3 2.4 5.4 5.4 5.4h19.3c3 0 5.4-2.4 5.4-5.4v-5.4C39 25 38 24 36.9 24zM14.4 34.7c-1.8 0-3.2-1.4-3.2-3.2V13.3h17.1V31.5c0 1.2.4 2.4 1.1 3.2h-15zm22.5-3.2c0 1.8-1.4 3.2-3.2 3.2-1.8 0-3.2-1.4-3.2-3.2v-5.4h6.4v5.4z",
        "M25.1 16.5h-5.4c-.6 0-1.1.5-1.1 1.1s.5 1.1 1.1 1.1h5.4c.6 0 1.1-.5 1.1-1.1s-.5-1.1-1.1-1.1zM25.1 20.8h-5.4c-.6 0-1.1.5-1.1 1.1 0 .6.5 1.1 1.1 1.1h5.4c.6 0 1.1-.5 1.1-1.1-.1-.6-.5-1.1-1.1-1.1zM25.1 25.1H14.4c-.6 0-1.1.5-1.1 1.1 0 .6.5 1.1 1.1 1.1h10.7c.6 0 1.1-.5 1.1-1.1-.1-.6-.5-1.1-1.1-1.1zM25.1 29.4H14.4c-.6 0-1.1.5-1.1 1.1 0 .6.5 1.1 1.1 1.1h10.7c.6 0 1.1-.5 1.1-1.1-.1-.7-.5-1.1-1.1-1.1z"
      ],
      tags: ["element-newsletter"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M34 5H14c-1.1 0-2 .9-2 2v34c0 1.1.9 2 2 2h20c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm-.6 36H14.6c-.3 0-.6-.3-.6-.6V7.6c0-.3.3-.6.6-.6h18.8c.3 0 .6.3.6.6v32.8c0 .3-.3.6-.6.6z",
        "M28 29h-8c-.6 0-1-.4-1-1s.4-1 1-1h8c.6 0 1 .4 1 1s-.4 1-1 1zM28 33h-8c-.6 0-1-.4-1-1s.4-1 1-1h8c.6 0 1 .4 1 1s-.4 1-1 1zM28 37h-8c-.6 0-1-.4-1-1s.4-1 1-1h8c.6 0 1 .4 1 1s-.4 1-1 1z",
        "M24.9 21.7c.8-.2 1.5-.6 1.8-1.4.7-1.6 0-2.7-1.6-3.5l-.8-.4-.8-.4c-1-.5-1.2-.9-.9-1.6.4-.8 2.1-.8 2.7-.3l.6.4.9-1.2-.6-.4c-.3-.2-.8-.4-1.3-.5V11h-1.5v1.3c-.9.1-1.7.6-2.1 1.5-.7 1.6 0 2.7 1.6 3.5l.8.4c.6.3.6.3.7.4 1 .5 1.3.9.9 1.6-.4.8-2.1.8-2.7.3l-.6-.5-.9 1.2.6.4c.4.3 1 .5 1.6.6V23h1.5v-1.3z"
      ],
      tags: ["element-pricing-table"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M46 9H2c-1.1 0-2 .9-2 2v26c0 1.1.9 2 2 2h44c1.1 0 2-.9 2-2V11c0-1.1-.9-2-2-2zM2.6 37c-.3 0-.6-.3-.6-.6V11.6c0-.3.3-.6.6-.6h42.8c.3 0 .6.3.6.6v24.8c0 .3-.3.6-.6.6H2.6z",
        "M18 22.5c-.8 0-1.5.7-1.5 1.5s.7 1.5 1.5 1.5 1.5-.7 1.5-1.5-.7-1.5-1.5-1.5zM24 22.5c-.8 0-1.5.7-1.5 1.5s.7 1.5 1.5 1.5 1.5-.7 1.5-1.5-.7-1.5-1.5-1.5zM30 22.5c-.8 0-1.5.7-1.5 1.5s.7 1.5 1.5 1.5 1.5-.7 1.5-1.5-.7-1.5-1.5-1.5z"
      ],
      tags: ["element-section"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M46 9H2C0.9 9 0 9.9 0 11V37C0 38.1 0.9 39 2 39H46C47.1 39 48 38.1 48 37V11C48 9.9 47.1 9 46 9ZM2.6 37C2.3 37 2 36.7 2 36.4V11.6C2 11.3 2.3 11 2.6 11H45.4C45.7 11 46 11.3 46 11.6V36.4C46 36.7 45.7 37 45.4 37H2.6Z",
        "M21 13C19.3431 13 18 14.3431 18 16V32C18 33.6569 19.3431 35 21 35H27C28.6569 35 30 33.6569 30 32V16C30 14.3431 28.6569 13 27 13H21ZM20 16C20 15.4477 20.4477 15 21 15H27C27.5523 15 28 15.4477 28 16V32C28 32.5523 27.5523 33 27 33H21C20.4477 33 20 32.5523 20 32V16ZM7 23C5.34315 23 4 24.3431 4 26V32C4 33.6569 5.34315 35 7 35H13C14.6569 35 16 33.6569 16 32V26C16 24.3431 14.6569 23 13 23H7ZM6 26C6 25.4477 6.44772 25 7 25H13C13.5523 25 14 25.4477 14 26V32C14 32.5523 13.5523 33 13 33H7C6.44772 33 6 32.5523 6 32V26ZM4 16C4 14.3431 5.34315 13 7 13H13C14.6569 13 16 14.3431 16 16V18C16 19.6569 14.6569 21 13 21H7C5.34315 21 4 19.6569 4 18V16ZM7 15C6.44772 15 6 15.4477 6 16V18C6 18.5523 6.44772 19 7 19H13C13.5523 19 14 18.5523 14 18V16C14 15.4477 13.5523 15 13 15H7ZM35 13C33.3431 13 32 14.3431 32 16V32C32 33.6569 33.3431 35 35 35H41C42.6569 35 44 33.6569 44 32V16C44 14.3431 42.6569 13 41 13H35ZM34 16C34 15.4477 34.4477 15 35 15H41C41.5523 15 42 15.4477 42 16V32C42 32.5523 41.5523 33 41 33H35C34.4477 33 34 32.5523 34 32V16Z"
      ],
      tags: ["element-container"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M37 9H27c-1.1 0-2 .9-2 2v26c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V11c0-1.1-.9-2-2-2zm-.6 28h-8.8c-.3 0-.6-.3-.6-.6V11.6c0-.3.3-.6.6-.6h8.8c.3 0 .6.3.6.6v24.8c0 .3-.3.6-.6.6z",
        "M20 9H9v2h10.4c.3 0 .6.3.6.6v24.8c0 .3-.3.6-.6.6H9v2h11c1.1 0 2-.9 2-2V11c0-1.1-.9-2-2-2z"
      ],
      tags: ["element-sidebar"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M46 13H2c-1.1 0-2 .9-2 2v26c0 1.1.9 2 2 2h44c1.1 0 2-.9 2-2V15c0-1.1-.9-2-2-2zM2.6 41c-.3 0-.6-.3-.6-.6V15.6c0-.3.3-.6.6-.6h42.8c.3 0 .6.3.6.6v24.8c0 .3-.3.6-.6.6H2.6z",
        "M44 9H4c-.6 0-1 .4-1 1s.4 1 1 1h40c.6 0 1-.4 1-1s-.4-1-1-1zM7 7h34c.6 0 1-.4 1-1s-.4-1-1-1H7c-.6 0-1 .4-1 1s.4 1 1 1z"
      ],
      tags: ["element-slider"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M33 28c-.1 0-.3 0-.4.1-.2.1-.3.1-.5.1-.8 0-1.6-.6-1.8-1.4l-2-7.4c-.9-3.4-3.9-5.7-7.4-5.7h-.7v-.6c-.2-.8-1-1.2-1.8-1-.8.2-1.2 1-1 1.8.1.2.2.4.3.5-1.4.7-2.6 1.7-3.4 3.1-1 1.8-1.3 3.8-.8 5.8l2 7.4c.1.5.1 1-.2 1.4-.3.4-.7.7-1.1.9-.8.2-1.3 1.1-1.1 1.9.2.7.8 1.2 1.5 1.2.1 0 .3 0 .4-.1l6.3-1.7c.6.9 1.6 1.5 2.7 1.5.3 0 .6 0 .8-.1 1.4-.4 2.3-1.6 2.4-2.9l6.2-1.7c.4-.1.8-.4 1-.7.2-.4.3-.8.2-1.2-.2-.7-.8-1.2-1.6-1.2zm-16.3 5.6c.2-.2.3-.4.4-.6.5-.9.6-1.9.4-2.9l-2-7.4c-.4-1.5-.2-3 .6-4.4.8-1.3 2-2.3 3.5-2.7.5-.1 1-.2 1.5-.2 2.6 0 4.9 1.7 5.5 4.2l2 7.4c.3 1.2 1.2 2.2 2.4 2.6l-14.3 4zM29.1 16.9c.3 0 .5-.1.7-.3l2-2c.4-.4.4-1 0-1.3-.4-.4-1-.4-1.3 0l-2 2c-.4.4-.4 1 0 1.3.1.2.3.3.6.3zM33.8 19.7H31c-.5 0-1 .4-1 1s.4 1 1 1h2.8c.5 0 1-.4 1-1s-.4-1-1-1zM30.5 18.1c.2.4.5.6.9.6.1 0 .3 0 .4-.1l2.6-1.1c.5-.2.7-.8.5-1.2-.2-.5-.8-.7-1.2-.5l-2.7 1c-.5.2-.7.8-.5 1.3z"
      ],
      tags: ["element-alert"],
      viewBox: ["0 0 48 48"]
    },
    {
      circle: ['cx="24" cy="23.3" r="5.7"'],
      paths: [
        "M24.4 32.6l1.7 6.4H34c0-4.4-3.6-8-8-8l-1.6 1.6zM23.7 32.6L22.1 31H22c-4.4 0-8 3.6-8 8h8.1l1.6-6.4z",
        "M25.7 12.9l1.4-1.4c.1-.1.1-.4-.1-.4-.6-.1-1.3-.2-1.9-.3L24.2 9c-.1-.2-.4-.2-.4 0l-.9 1.8c-.7.1-1.3.2-2 .3-.2 0-.2.3-.1.4l1.4 1.4c-.1.6-.2 1.3-.3 1.9 0 .2.2.4.4.3.6-.3 1.1-.6 1.7-.9.5.3 1.1.6 1.6.9 0 .1.1.1.2.1.2 0 .3-.2.3-.3-.2-.7-.3-1.3-.4-2zM20.8 13c-.5-.1-1-.1-1.5-.2-.2-.4-.4-.9-.6-1.3-.1-.2-.4-.2-.4 0-.2.4-.4.9-.7 1.3-.5.1-1 .1-1.5.2-.2 0-.2.3-.1.4l1 1c-.1.5-.2.9-.2 1.4 0 .2.2.4.4.3.4-.2.9-.4 1.3-.7l1.2.6c0 .1.1.1.2.1s.2-.1.3-.2v-.1c-.1-.5-.2-.9-.2-1.4l1-1c.1-.1 0-.4-.2-.4zM15.7 14.5c-.4-.1-.7-.1-1.1-.2-.2-.3-.3-.6-.5-1-.1-.2-.4-.2-.4 0-.2.3-.3.6-.5 1-.4.1-.7.1-1.1.2-.2 0-.2.3-.1.4.3.2.5.5.8.7-.1.3-.1.7-.2 1 0 .2.2.4.4.3.3-.2.6-.3.9-.5.3.1.6.3.8.4.1.1.2.1.3.1.1-.1.1-.1.1-.3-.1-.3-.1-.7-.2-1 .3-.2.5-.5.8-.7.2-.1.2-.4 0-.4zM31.8 13.1c-.5-.1-1-.1-1.5-.2-.2-.4-.4-.9-.7-1.3-.1-.2-.4-.2-.4 0-.2.4-.4.9-.6 1.3-.5.1-1 .1-1.5.2-.2 0-.2.3-.1.4l1 1c0 .5 0 .9-.1 1.4-.1.1 0 .3.2.3.1 0 .2 0 .3-.1l1.2-.6c.4.2.9.4 1.3.7.2.1.4-.1.4-.3-.1-.5-.2-.9-.2-1.4l1-1c0-.1-.1-.4-.3-.4zM35.8 14.6c-.4-.1-.7-.1-1.1-.2-.2-.3-.3-.6-.5-1-.1-.2-.4-.2-.4 0-.2.3-.3.6-.5 1-.4.1-.7.1-1.1.2-.2 0-.2.3-.1.4.3.2.5.5.8.7-.1.3-.1.6-.2.9-.1.1 0 .3.1.3.1.1.3.1.3 0 .3-.1.6-.3.8-.4.3.2.6.3.9.5.2.1.4-.1.4-.3-.1-.3-.1-.7-.2-1 .3-.2.5-.5.8-.7.3-.1.2-.4 0-.4z"
      ],
      tags: ["element-testimonial"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M46 15H24.6c-.3 0-.6-.3-.6-.6V11c0-1.1-.9-2-2-2H12c-1.1 0-2 .9-2 2v3.4c0 .3-.3.6-.6.6H2c-1.1 0-2 .9-2 2v20c0 1.1.9 2 2 2h44c1.1 0 2-.9 2-2V17c0-1.1-.9-2-2-2zM2 36.4V17.6c0-.3.3-.6.6-.6H10c1.1 0 2-.9 2-2v-3.4c0-.3.3-.6.6-.6h8.8c.3 0 .6.3.6.6V15c0 1.1.9 2 2 2h21.4c.3 0 .6.3.6.6v18.8c0 .3-.3.6-.6.6H2.6c-.3 0-.6-.3-.6-.6z",
        "M7 11H1c-.6 0-1 .4-1 1s.4 1 1 1h6c.6 0 1-.4 1-1s-.4-1-1-1zM33 11h-6c-.6 0-1 .4-1 1s.4 1 1 1h6c.6 0 1-.4 1-1s-.4-1-1-1zM43 11h-6c-.6 0-1 .4-1 1s.4 1 1 1h6c.6 0 1-.4 1-1s-.4-1-1-1z"
      ],
      tags: ["element-tabs"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M13.3 12h21.4l.2 7.8h-2.1l-.2-2.2c0-1-.4-2-1.1-2.7-.9-.6-2-.9-3.1-.8-.7-.1-1.5.1-2.1.5-.5.7-.7 1.5-.6 2.3v14.2c-.1.8.1 1.6.6 2.3.3.4 1.1.5 2.2.5H30V36H18v-2.1h1.8c.7 0 1.4-.1 2-.5.4-.7.6-1.5.5-2.3V16.9c.1-.8-.1-1.6-.6-2.3-.6-.4-1.4-.6-2.1-.5-1.1-.1-2.2.2-3.1.8-.7.7-1.1 1.7-1.1 2.7l-.2 2.2h-2.1l.2-7.8z"
      ],
      tags: ["element-heading"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M34.6 12H13.2L13 19.8H15.1L15.3 17.6C15.3 16.6 15.7 15.6 16.4 14.9C17.3 14.3 18.4 14 19.5 14.1C20.2 14 21 14.2 21.6 14.6C22.1 15.3 22.3 16.1 22.2 16.9V24H25.6V16.9C25.5 16.1 25.7 15.3 26.2 14.6C26.8 14.2 27.6 14 28.3 14.1C29.4 14 30.5 14.3 31.4 14.9C32.1 15.6 32.5 16.6 32.5 17.6L32.7 19.8H34.8L34.6 12ZM21 36V33.7303C20.5834 33.8602 20.1417 33.9 19.7 33.9H17.9V36H21Z",
        "M23 27C23 26.4477 23.4477 26 24 26H34C34.5523 26 35 26.4477 35 27C35 27.5523 34.5523 28 34 28H24C23.4477 28 23 27.5523 23 27ZM23 31C23 30.4477 23.4477 30 24 30H38C38.5523 30 39 30.4477 39 31C39 31.5523 38.5523 32 38 32H24C23.4477 32 23 31.5523 23 31ZM24 34C23.4477 34 23 34.4477 23 35C23 35.5523 23.4477 36 24 36H38C38.5523 36 39 35.5523 39 35C39 34.4477 38.5523 34 38 34H24Z"
      ],
      tags: ["element-text"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M22 37h-2c-.6 0-1-.4-1-1s.4-1 1-1h2c.6 0 1 .4 1 1s-.4 1-1 1zm12-26H14c-1.7 0-3 1.3-3 3v20c0 1.7 1.3 3 3 3h2c.6 0 1-.4 1-1s-.4-1-1-1h-2c-.6 0-1-.4-1-1V14c0-.6.4-1 1-1h20c.6 0 1 .4 1 1v20c0 .6-.4 1-1 1h-8c-.6 0-1 .4-1 1s.4 1 1 1h8c1.7 0 3-1.3 3-3V14c0-1.7-1.3-3-3-3z",
        "M23.3 29.5c0 .9-.8 1.7-1.7 1.7-.9 0-1.7-.8-1.7-1.7 0-.9.8-1.7 1.7-1.7.9 0 1.7.8 1.7 1.7zm3.3-1.7c-.9 0-1.7.8-1.7 1.7 0 .9.8 1.7 1.7 1.7s1.7-.8 1.7-1.7c0-.9-.8-1.7-1.7-1.7zm2.9-1.7h-8.9l-.2-1h7.3c.2 0 .4-.1.5-.3l1.7-4.4c.1-.3 0-.6-.3-.7H19.4l-.4-2.4c0-.3-.3-.4-.5-.4h-2c-.3 0-.5.2-.5.5s.2.5.5.5h1.6l1.5 8.9c0 .3.3.4.5.4h9.4c.3 0 .5-.2.5-.5s-.2-.6-.5-.6z"
      ],
      tags: ["element-woo-add-to-cart"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M37.4 24l-2.1 2.1-3.9 3.9h-4.2l6-6-6-6h4.2l3.9 3.9 2.1 2.1zM27 21.9L23.1 18h-4.2l6 6-6 6h4.2l3.9-3.9 2.1-2.1-2.1-2.1zm-8.2 0L14.9 18h-4.2l6 6-6 6h4.2l3.9-3.9 2.1-2.1-2.1-2.1z"
      ],
      tags: ["element-woo-breadcrumbs"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M28 17h20v2H28v-2zm0 6h20v-2H28v2zm0 4h16v-2H28v2zm0 4h14v-2H28v2z",
        "M7 30v-8H5v-4h4.2c.4 1.2 1.5 2 2.8 2s2.4-.8 2.8-2H19v4h-2v8H7zm14-18H3c-1.7 0-3 1.3-3 3v18c0 1.7 1.3 3 3 3h1c.6 0 1-.4 1-1s-.4-1-1-1H3c-.5 0-1-.4-1-1V15c0-.5.4-1 1-1h18c.5 0 1 .4 1 1v18c0 .5-.4 1-1 1h-7c-.6 0-1 .4-1 1s.4 1 1 1h7c1.7 0 3-1.3 3-3V15c0-1.7-1.3-3-3-3zM10 34H8c-.6 0-1 .4-1 1s.4 1 1 1h2c.6 0 1-.4 1-1s-.4-1-1-1z"
      ],
      tags: ["element-woo-description"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M32 12.1v4.6h-2.3v9.1H18.3v-9.1H16v-4.6h4.8c.5 1.3 1.7 2.3 3.2 2.3s2.7-1 3.2-2.3H32zM36 4H12c-1.7 0-3 1.3-3 3v24c0 1.7 1.3 3 3 3h1c.6 0 1-.4 1-1s-.4-1-1-1h-1c-.6 0-1-.4-1-1V7c0-.6.4-1 1-1h24c.6 0 1 .4 1 1v24c0 .6-.4 1-1 1H23c-.6 0-1 .4-1 1s.4 1 1 1h13c1.7 0 3-1.3 3-3V7c0-1.7-1.3-3-3-3zM19 32h-2c-.6 0-1 .4-1 1s.4 1 1 1h2c.6 0 1-.4 1-1s-.4-1-1-1z",
        "M14.6 36h-3.2C10.1 36 9 37.1 9 38.4v3.2c0 1.3 1.1 2.4 2.4 2.4h3.2c1.3 0 2.4-1.1 2.4-2.4v-3.2c0-1.3-1.1-2.4-2.4-2.4zm.8 5.6c0 .4-.4.8-.8.8h-3.2c-.4 0-.8-.4-.8-.8v-3.2c0-.4.4-.8.8-.8h3.2c.4 0 .8.4.8.8v3.2zM25.6 36h-3.2c-1.3 0-2.4 1.1-2.4 2.4v3.2c0 1.3 1.1 2.4 2.4 2.4h3.2c1.3 0 2.4-1.1 2.4-2.4v-3.2c0-1.3-1.1-2.4-2.4-2.4zm.8 5.6c0 .4-.4.8-.8.8h-3.2c-.4 0-.8-.4-.8-.8v-3.2c0-.4.4-.8.8-.8h3.2c.4 0 .8.4.8.8v3.2zM36.6 36h-3.2c-1.3 0-2.4 1.1-2.4 2.4v3.2c0 1.3 1.1 2.4 2.4 2.4h3.2c1.3 0 2.4-1.1 2.4-2.4v-3.2c0-1.3-1.1-2.4-2.4-2.4zm.8 5.6c0 .4-.4.8-.8.8h-3.2c-.4 0-.8-.4-.8-.8v-3.2c0-.4.4-.8.8-.8h3.2c.4 0 .8.4.8.8v3.2z"
      ],
      tags: ["element-woo-product-images"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M19.5 39.8c0-.2-.1-.4-.3-.5-.2-.2-.4-.3-.7-.3-.3 0-.5.1-.7.2-.2.1-.2.3-.2.5s.1.4.3.5c.2.1.4.2.7.2l.5.1.9.3c.3.1.5.3.6.6.2.2.2.5.2.8 0 .5-.2 1-.6 1.3-.4.3-1 .5-1.7.5s-1.3-.2-1.7-.5-.6-.8-.7-1.4h1.2c0 .3.1.5.3.7.2.1.5.2.8.2s.6-.1.7-.2c.2-.1.3-.3.3-.5s-.1-.3-.3-.5c-.2-.1-.4-.2-.7-.3l-.6-.2c-.5-.1-.9-.3-1.2-.6-.3-.3-.4-.6-.4-1s.1-.7.3-.9c.2-.3.5-.5.8-.6s.7-.2 1.1-.2c.4 0 .8.1 1.1.2.3.1.6.4.8.6.2.3.3.6.3.9h-1.1v.1zm2 4.1v-5.8h1.2v2.6h.1l2.1-2.6h1.5l-2.2 2.6 2.2 3.2H25l-1.6-2.4-.6.7v1.7h-1.3zm9.2-5.8h1.2v3.8c0 .4-.1.8-.3 1.1s-.5.6-.8.7c-.4.2-.8.3-1.3.3s-.9-.1-1.3-.3c-.4-.2-.6-.4-.8-.7s-.3-.7-.3-1.1v-3.8h1.2v3.7c0 .3.1.6.3.8.2.2.5.3.9.3s.6-.1.9-.3.3-.5.3-.8v-3.7z",
        "M32 12.1v4.6h-2.3v9.1H18.3v-9.1H16v-4.6h4.8c.5 1.3 1.7 2.3 3.2 2.3s2.7-1 3.2-2.3H32zM36 4H12c-1.7 0-3 1.3-3 3v24c0 1.7 1.3 3 3 3h1c.6 0 1-.4 1-1s-.4-1-1-1h-1c-.6 0-1-.4-1-1V7c0-.6.4-1 1-1h24c.6 0 1 .4 1 1v24c0 .6-.4 1-1 1H23c-.6 0-1 .4-1 1s.4 1 1 1h13c1.7 0 3-1.3 3-3V7c0-1.7-1.3-3-3-3zM19 32h-2c-.6 0-1 .4-1 1s.4 1 1 1h2c.6 0 1-.4 1-1s-.4-1-1-1z"
      ],
      tags: ["element-woo-product-meta"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M20 34c0 .6-.4 1-1 1h-2c-.6 0-1-.4-1-1s.4-1 1-1h2c.6 0 1 .4 1 1zm17-21H11C4.9 13 0 17.9 0 24s4.9 11 11 11h2c.6 0 1-.4 1-1s-.4-1-1-1h-2c-5 0-9-4-9-9s4-9 9-9h26c5 0 9 4 9 9s-4 9-9 9H23c-.6 0-1 .4-1 1s.4 1 1 1h14c6.1 0 11-4.9 11-11s-4.9-11-11-11zm-19.5 8.2c.4 0 .7.1 1 .2.3.1.5.3.7.4l.7-1.5c-.3-.3-.7-.5-1.1-.6-.4-.1-.8-.2-1.3-.2-.9 0-1.7.3-2.4.8s-1.1 1.3-1.3 2.3h-.9l-.3.9h1.1v.8h-.8l-.3 1h1.2c.2 1 .7 1.8 1.3 2.3s1.4.8 2.4.8c.4 0 .9-.1 1.2-.2.4-.1.7-.3 1.1-.6l-.7-1.5c-.2.1-.4.2-.6.4-.3.1-.6.2-1 .2s-.8-.1-1.1-.3c-.3-.2-.5-.6-.7-1.1h1.7l.5-1h-2.3v-.4-.4h2.7l.4-.9h-3c.1-.5.4-.9.7-1.1.3-.2.7-.3 1.1-.3zm6.3 5.7h3.7v1.5h-6.2V27l3.1-2.9c.4-.4.7-.7.9-1s.3-.6.3-.9c0-.4-.1-.7-.4-.9-.3-.2-.6-.3-1-.3s-.7.1-1 .4c-.2.2-.4.6-.4 1H21c0-.6.1-1.1.4-1.5.3-.4.6-.7 1.1-1 .5-.2 1-.3 1.6-.3.6 0 1.2.1 1.6.3.5.2.8.5 1.1.9.3.4.4.8.4 1.3 0 .3-.1.7-.2 1-.1.3-.4.7-.7 1.1s-.7.8-1.3 1.3l-1.2 1.4zm11.3-5.1c-.2-.5-.5-1-.8-1.3-.3-.3-.7-.6-1.1-.7-.4-.1-.8-.2-1.3-.2-.7 0-1.2.1-1.7.4s-.9.6-1.1 1.1c-.3.5-.4 1-.4 1.6 0 .5.1 1 .4 1.4.2.4.5.8 1 1s.9.4 1.4.4c.5 0 .9-.1 1.3-.3.4-.2.7-.5.9-.9h.1c0 .9-.2 1.5-.4 2-.3.5-.7.7-1.3.7-.3 0-.6-.1-.8-.3-.2-.2-.4-.4-.4-.7h-1.8c.1.5.2.9.5 1.3s.6.7 1.1.9c.4.2 1 .3 1.5.3.7 0 1.3-.2 1.9-.6.5-.4.9-.9 1.2-1.6.3-.7.4-1.5.4-2.5-.3-.8-.4-1.5-.6-2zm-1.9 1.5c-.1.2-.3.4-.5.6-.2.1-.5.2-.8.2-.4 0-.8-.1-1-.4s-.4-.7-.4-1.1c0-.3.1-.5.2-.8.1-.2.3-.4.5-.5.2-.1.5-.2.8-.2.3 0 .5.1.8.2s.4.3.5.6c.1.2.2.5.2.8-.1.1-.2.3-.3.6z"
      ],
      tags: ["element-woo-product-price"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M32 12.1v4.6h-2.3v9.1H18.3v-9.1H16v-4.6h4.8c.5 1.3 1.7 2.3 3.2 2.3s2.7-1 3.2-2.3H32zM36 4H12c-1.7 0-3 1.3-3 3v24c0 1.7 1.3 3 3 3h1c.6 0 1-.4 1-1s-.4-1-1-1h-1c-.6 0-1-.4-1-1V7c0-.6.4-1 1-1h24c.6 0 1 .4 1 1v24c0 .6-.4 1-1 1H23c-.6 0-1 .4-1 1s.4 1 1 1h13c1.7 0 3-1.3 3-3V7c0-1.7-1.3-3-3-3zM19 32h-2c-.6 0-1 .4-1 1s.4 1 1 1h2c.6 0 1-.4 1-1s-.4-1-1-1z",
        "M23 38.9l-2.6-.4-1.2-2.4c0-.1-.1-.1-.2-.1s-.2.1-.2.1l-1.2 2.4-2.6.4c-.1 0-.2.1-.2.2s0 .2.1.3l1.9 1.8-.4 2.6c0 .1 0 .2.1.2h.2l2.3-1.2 2.3 1.2h.3c.1-.1.1-.1.1-.2l-.4-2.6 1.9-1.8c.1-.1.1-.2.1-.3-.2-.2-.3-.2-.3-.2zM33.2 39c0-.1-.1-.2-.2-.2l-2.6-.4-1.2-2.4c0-.1-.1-.1-.2-.1s-.2.1-.2.1l-1.2 2.4-2.6.5c-.1 0-.2.1-.2.2s0 .2.1.3l1.9 1.8-.4 2.6c0 .1 0 .2.1.2h.2l2.3-1.2 2.3 1.2h.3c.1-.1.1-.1.1-.2l-.4-2.6 1.9-1.8V39z"
      ],
      tags: ["element-woo-product-rating"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M36 4c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm4.5 5.9L35.4 15c-.3.3-.8.3-1.1 0l-2.7-2.5c-.3-.3-.3-.8-.1-1.1.3-.3.8-.3 1.1 0l2.1 2 4.6-4.6c.3-.3.8-.3 1.1 0 .4.3.4.8.1 1.1zm-3.5 12c.7-.1 1.4-.2 2-.4V36c0 1.7-1.3 3-3 3H23c-.6 0-1-.4-1-1s.4-1 1-1h13c.6 0 1-.4 1-1V21.9zM12 9c-1.7 0-3 1.3-3 3v24c0 1.7 1.3 3 3 3h1c.6 0 1-.4 1-1s-.4-1-1-1h-1c-.6 0-1-.4-1-1V12c0-.6.4-1 1-1h14c.1-.7.2-1.4.4-2H12zm7 28h-2c-.6 0-1 .4-1 1s.4 1 1 1h2c.6 0 1-.4 1-1s-.4-1-1-1zm8.4-19.9h-.2c-.5 1.3-1.7 2.3-3.2 2.3s-2.7-1-3.2-2.3H16v4.6h2.3v9.1h11.4v-9.1H32v-.6c-1.9-.8-3.5-2.2-4.6-4z"
      ],
      tags: ["element-woo-product-stock"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M10.6 39.5c.4 0 .8-.2 1.1-.5l8.7-8.7 3.6 3.6c.6.6 1.6.6 2.2 0l9.8-9.8v3.5c0 .9.7 1.6 1.6 1.6.9 0 1.6-.7 1.6-1.6v-7.2c0-.9-.7-1.6-1.6-1.6h-7.2c-.9 0-1.6.7-1.6 1.6s.7 1.6 1.6 1.6h3.5L25 30.6 21.5 27c-.6-.6-1.6-.6-2.2 0l-9.8 9.8c-.6.6-.6 1.6 0 2.2.2.4.6.5 1.1.5zM18 8.5c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm.5 11.5v1c0 .1-.1.2-.3.2h-.6c-.1 0-.3-.1-.3-.2v-.8c-.6 0-1.1-.1-1.6-.3-.2-.1-.4-.3-.3-.6l.1-.4c0-.1.1-.3.3-.3.1-.1.3-.1.4 0 .4.2.9.3 1.4.3.7 0 1.1-.3 1.1-.7 0-.4-.4-.7-1.2-1-1.2-.4-2.1-1-2.1-2.1 0-1 .7-1.8 2-2.1v-1c0-.1.1-.3.3-.3h.6c.1 0 .3.1.3.3v.8c.5 0 .9.1 1.3.2.2.1.4.3.3.6l-.2.3c0 .1-.1.2-.2.3-.1.1-.3.1-.4 0-.3-.1-.7-.2-1.2-.2-.8 0-1 .3-1 .7 0 .4.4.6 1.4 1 1.4.5 1.9 1.1 1.9 2.2.1 1-.7 1.9-2 2.1z"
      ],
      tags: ["element-woo-product-upsells"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M44 4.8v3.5h-1.7v6.8h-8.6V8.3H32V4.8h3.6c.4 1 1.3 1.7 2.4 1.7s2-.8 2.4-1.7H44zM45 0H31c-1.7 0-3 1.3-3 3v14c0 1.7 1.3 3 3 3h14c1.7 0 3-1.3 3-3V3c0-1.7-1.3-3-3-3zm1 17c0 .6-.5 1-1 1H31c-.6 0-1-.5-1-1V3c0-.6.5-1 1-1h14c.6 0 1 .5 1 1v14zM44 32.8v3.5h-1.7v6.8h-8.6v-6.8H32v-3.5h3.6c.4 1 1.3 1.7 2.4 1.7s2-.8 2.4-1.7H44zm1-4.8H31c-1.7 0-3 1.3-3 3v14c0 1.7 1.3 3 3 3h14c1.7 0 3-1.3 3-3V31c0-1.7-1.3-3-3-3zm1 17c0 .6-.5 1-1 1H31c-.6 0-1-.5-1-1V31c0-.6.5-1 1-1h14c.6 0 1 .5 1 1v14z",
        "M12.4 18.8H16v3.5h-1.7v6.8H5.7v-6.8H4v-3.5h3.6c.4 1 1.3 1.7 2.4 1.7s2-.7 2.4-1.7zM28 11.5l-8.3 4.3c.2.4.3.8.3 1.2v14c0 .5-.1 1-.3 1.4l8.3 4.3V39l-9.9-5.1c-.3 0-.7.1-1.1.1H3c-1.7 0-3-1.3-3-3V17c0-1.7 1.3-3 3-3h14c.5 0 .9.1 1.3.3l9.7-5v2.2zM18 17c0-.6-.5-1-1-1H3c-.6 0-1 .5-1 1v14c0 .6.5 1 1 1h14c.6 0 1-.5 1-1V17z"
      ],
      tags: ["element-woo-product-related"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: ["M11.8 12.7L33.2 25 11.8 37.3V12.7M6.8 4v42l36.4-21L6.8 4z"],
      tags: ["play-video"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: ["M9 8.5h8v33H9zM33 8.5h8v33h-8z"],
      tags: ["pause-video"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M19.3 12.7v24.7l-7.9-4.5-1.2-.7H5V17.9h5.2l1.2-.7 7.9-4.5m5-8.7L8.9 12.9H0v24.2h8.9L24.3 46V4zM41.4 45.8l-3.5-3.5c9.5-9.5 9.5-25 0-34.5l3.5-3.5c11.5 11.4 11.5 30 0 41.5z",
        "M33.2 39l-3.5-3.5c5.8-5.8 5.8-15.1 0-20.9l3.5-3.5c7.7 7.6 7.7 20.2 0 27.9z"
      ],
      tags: ["sound-on"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M19.3 12.7v24.7l-7.9-4.5-1.2-.7H5V17.9h5.2l1.2-.7 7.9-4.5m5-8.7L8.9 12.9H0v24.2h8.9L24.3 46V4zM50 18.6l-3.5-3.5-6.4 6.4-6.4-6.4-3.5 3.5 6.3 6.4-6.3 6.4 3.5 3.5 6.4-6.4 6.4 6.4 3.5-3.5-6.4-6.4z"
      ],
      tags: ["sound-off"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: ["M5 7v8h12V7H5zm10.5 6.5h-9v-5h9v5z"],
      tags: ["default-state"],
      viewBox: ["0 0 22 22"]
    },
    {
      paths: [
        "M17.5 14.4l2.5-1.6-3-1.1V7H5v8h8.8l1.1 3 1.6-2.5 1.7 1.7c.3.3.8.3 1.1 0 .3-.3.3-.8 0-1.1l-1.8-1.7zm-11-.9v-5h9v2.8L12 10l1.3 3.5H6.5z"
      ],
      tags: ["hover-state"],
      viewBox: ["0 0 22 22"]
    },
    {
      paths: [
        "M100.5 82.5L91 77l-9.5 5.5L91 88l9.5-5.5zM91 79.7l4.7 2.7-4.7 2.8-4.7-2.7 4.7-2.8zm9.5 7.8l-7.1 4.1L91 93l-2.4-1.4-7.1-4.1 2.4-1.4 7.1 4.1 7.1-4.1 2.4 1.4z",
        "M24 51h32v2H24v-2zm24 6H24v2h24v-2zm-24 8h27v-2H24v2zm76-33H82v6h18v-6zm46 1h-4v4h4v-4zm-8 1h-10v2h10v-2zm-24 51c0 12.7-10.3 23-23 23S68 97.7 68 85s10.3-23 23-23 23 10.3 23 23zm-2 0c0-11.6-9.4-21-21-21s-21 9.4-21 21 9.4 21 21 21 21-9.4 21-21zm53-35v28c0 2.2-1.8 4-4 4h-9v46.2s.1 0 .1.1c.9.9 1.7 1.7 2.7 2.6 2.6-2.5 2.1-2 2.7-2.6.4-.3.9-.4 1.3 0 .8 1 1.6 1.9 2.5 2.8.8.9-.5 2.1-1.3 1.3-.7-.7-1.3-1.5-1.9-2.2-.9.8-1.7 1.7-2.6 2.5-.4.4-.9.3-1.3 0-.8-.6-1.5-1.3-2.2-2V141c0 2.8-2.2 5-5 5h-27v9c0 1.6-1.3 3-3 3H55c-1.7 0-3-1.4-3-3v-9H35c-2.8 0-5-2.2-5-5v-30.3c-.7.7-1.4 1.4-2.2 2-.4.3-.9.4-1.3 0-.9-.8-1.7-1.7-2.6-2.5-.6.7-1.3 1.5-1.9 2.2-.8.9-2.1-.4-1.3-1.3s1.7-1.9 2.5-2.8c.3-.4.9-.3 1.3 0 .9.8 1.8 1.7 2.7 2.6.9-.8 1.8-1.7 2.7-2.6l.1-.1V73H19c-1.7 0-3-1.4-3-3V20c0-1.7 1.3-3 3-3h42c1.7 0 3 1.3 3 3v4h83c2.8 0 5 2.2 5 5v17h9c2.2 0 4 1.8 4 4zM18 44h44V20c0-.5-.5-1-1-1H19c-.5 0-1 .5-1 1v24zm43 27c.5 0 1-.5 1-1V46H18v24c0 .5.5 1 1 1h42zm57 52c0-.5-.5-1-1-1H55c-.5 0-1 .5-1 1v32c0 .5.5 1 1 1h62c.5 0 1-.5 1-1v-32zm32-41h-17c-2.2 0-4-1.8-4-4V50c0-2.2 1.8-4 4-4h17V29c0-1.7-1.3-3-3-3H64v44c0 1.6-1.3 3-3 3H32v36.1c.6.6 1.2 1.1 1.8 1.7 2.6-2.5 2.1-2 2.7-2.6.4-.3.9-.4 1.3 0 .8 1 1.6 1.9 2.5 2.8.8.9-.5 2.1-1.3 1.3-.7-.7-1.3-1.5-1.9-2.2-.9.8-1.7 1.7-2.6 2.5-.4.4-.9.3-1.3 0s-.8-.7-1.2-1V141c0 1.7 1.3 3 3 3h17v-21c0-1.7 1.3-3 3-3h62c1.7 0 3 1.3 3 3v21h27c1.7 0 3-1.3 3-3v-9.3c-.4.4-.8.7-1.2 1.1-.4.3-.9.4-1.3 0-.9-.8-1.7-1.7-2.6-2.5-.6.7-1.3 1.5-1.9 2.2-.8.9-2.1-.4-1.3-1.3s1.7-1.9 2.5-2.8c.3-.4.9-.3 1.3 0 .9.8 1.8 1.7 2.7 2.6.6-.5 1.2-1.1 1.8-1.7V82zm13-32c0-1.1-.9-2-2-2h-28c-1.1 0-2 .9-2 2v28c0 1.1.9 2 2 2h28c1.1 0 2-.9 2-2V50zm-8.3 11.4l-4.6-.4-1.8-4.3c-.1-.3-.4-.5-.8-.5s-.6.2-.8.5l-1.8 4.3-4.6.4c-.3 0-.6.3-.7.6-.1.3 0 .7.2.9l3.5 3.1-1 4.5c-.1.3.1.7.3.9.1.1.3.2.5.2s.3 0 .4-.1l4-2.4 4 2.4c.3.2.7.2.9 0 .3-.2.4-.5.3-.9l-1-4.5 3.5-3.1c.3-.2.4-.6.2-.9 0-.5-.3-.7-.7-.7zM56 154h30v-30H56v30zm34-23h24v-2H90v2zm16 4H90v2h16v-2zm-16 8h21v-2H90v2zm0 6h18v-2H90v2zm33.1-53.5l-2.1 2.1-2.1-2.1-1.4 1.4 2.1 2.1-2.1 2.1 1.4 1.4 2.1-2.1 2.1 2.1 1.4-1.4-2.1-2.1 2.1-2.1-1.4-1.4zm46.8-64.8c-2.2 0-4 1.8-4 4 0 .5-.4.9-.9.9s-.9-.4-.9-.9c0-2.2-1.8-4-4-4-.5 0-.9-.4-.9-.9s.4-.9.9-.9c2.2 0 4-1.8 4-4 0-.5.4-.9.9-.9s.9.4.9.9c0 2.2 1.8 4 4 4 .5 0 .9.4.9.9s-.3.9-.9.9zm-3.1-.9c-.7-.5-1.3-1.1-1.8-1.8-.5.7-1.1 1.3-1.8 1.8.7.5 1.3 1.1 1.8 1.8.5-.8 1.1-1.4 1.8-1.8zm-154.9 18c0 .5-.4.9-.9.9-2.2 0-4 1.8-4 4 0 .5-.4.9-.9.9s-.9-.4-.9-.9c0-2.2-1.8-4-4-4-.5 0-.9-.4-.9-.9s.4-.9.9-.9c2.2 0 4-1.8 4-4 0-.5.4-.9.9-.9s.9.4.9.9c0 2.2 1.8 4 4 4 .5 0 .9.4.9.9zm-4.1 0c-.7-.5-1.3-1.1-1.8-1.8-.5.7-1.1 1.3-1.8 1.8.8.4 1.4 1 1.8 1.7.5-.7 1.1-1.3 1.8-1.7zm168.4-8c0 1.6-1.3 2.9-2.9 2.9-1.6 0-2.9-1.3-2.9-2.9 0-1.6 1.3-2.9 2.9-2.9s2.9 1.3 2.9 2.9zm-1.9 0c0-.6-.5-1-1-1-.6 0-1 .5-1 1 0 .6.5 1 1 1s1-.4 1-1zm-9.3 0c-.6 0-1.2.5-1.2 1.2 0 .6.5 1.2 1.2 1.2.6 0 1.2-.5 1.2-1.2s-.5-1.2-1.2-1.2zm7.5-15.7c.6 0 1.2-.5 1.2-1.2 0-.6-.5-1.2-1.2-1.2s-1.2.5-1.2 1.2.6 1.2 1.2 1.2zM90.8 9.3c2.2 0 4 1.8 4 4s-1.8 4-4 4-4-1.8-4-4 1.8-4 4-4zm-1.4 4c0 .8.6 1.4 1.4 1.4.8 0 1.4-.6 1.4-1.4 0-.8-.6-1.4-1.4-1.4-.8 0-1.4.6-1.4 1.4zm-2.9-8.8c.9 0 1.6-.7 1.6-1.6 0-.9-.7-1.6-1.6-1.6s-1.6.7-1.6 1.6c0 .9.7 1.6 1.6 1.6z",
        "M68 85c0-10.3 6.7-18.9 16-21.9V49c0-1.7-1.3-3-3-3H64v24c0 1.6-1.3 3-3 3H36v26c0 1.6 1.3 3 3 3h36.5C70.9 97.8 68 91.7 68 85z"
      ],
      tags: ["library-illustration"],
      viewBox: ["0 0 182 158"]
    },
    {
      paths: [
        "M73,69.5A15.1,15.1,0,0,0,58.1,81.8a12.5,12.5,0,0,0,2.4,24.7H87.1a10.9,10.9,0,0,0,1-21.7A15.4,15.4,0,0,0,83.8,74,15.2,15.2,0,0,0,73,69.5Zm0,3.3a11.4,11.4,0,0,1,8.4,3.5,12,12,0,0,1,3.5,9.9A1.6,1.6,0,0,0,86.3,88h.8a7.6,7.6,0,1,1,0,15.2H60.5a9.2,9.2,0,0,1-.8-18.4,1.7,1.7,0,0,0,1.4-1.5,12.5,12.5,0,0,1,3.4-7A11.8,11.8,0,0,1,73,72.8Zm0,9.8a1.4,1.4,0,0,0-1.1.4l-6,5.4a1.6,1.6,0,0,0,2.2,2.4l3.3-2.9V98.3a1.6,1.6,0,1,0,3.2,0V87.9l3.3,2.9a1.6,1.6,0,1,0,2.2-2.4l-6-5.4A1.4,1.4,0,0,0,73,82.6Z",
        "M5,34.9a2.9,2.9,0,0,1,3,3,2.9,2.9,0,0,1-2.8,3H5a2.9,2.9,0,0,1-3-3,3.2,3.2,0,0,1,3-3m0-2a5.2,5.2,0,0,0-5,5,5,5,0,0,0,5,5,5,5,0,0,0,0-10Zm114.8,79a.9.9,0,0,1,1,.8v.2a1,1,0,0,1-2,0,.9.9,0,0,1,.8-1h.2m0-2a3,3,0,1,0,3,3h0a2.9,2.9,0,0,0-2.8-3Z"
      ],
      polygon: [
        "84.3 40.4 82.2 38.3 84.3 36.2 82.8 34.7 80.8 36.8 78.7 34.7 77.2 36.1 79.3 38.3 77.2 40.4 78.6 41.8 80.8 39.7 82.9 41.8 84.3 40.4"
      ],
      circle: [
        'cx="73.5" cy="88" r="60" fill="#f7fafa"',
        'cx="51.5" cy="154.9" r="3" fill="#06bee1"',
        'cx="111.3" cy="5" r="5" fill="#06bee1"'
      ],
      tags: ["import-big-icon"],
      viewBox: ["0 0 133.5 157.9"]
    },
    {
      paths: [
        "M119.7 32.2L64 0 8.8 32.2 35 46.7l29-15.9 25.1 14-64.7 38.9L64 106.3l29.5-16.1 10.3 6.5-39.7 22.9-49.3-28.2.7-45.4-7.2-4.4V96l55.8 32 53.4-31.3-24-14.4-29.4 15.6-25.2-14.4 64-38-38.8-22.9L35 38.3l-11.8-6.5L64.1 8.9 113 37.3v45.2l6.7 4.4z"
      ],
      tags: ["zion-icon-logo"],
      viewBox: ["0 0 128 128"]
    },
    {
      paths: [
        "M7 12h23v2H7zM7 23h23v2H7zM7 34h23v2H7zM36 18.1c2.8 0 5-2.2 5-5s-2.2-5-5-5-5 2.2-5 5c0 2.7 2.3 5 5 5zm0-9.4c2.4 0 4.3 1.9 4.3 4.3s-1.9 4.3-4.3 4.3-4.3-1.9-4.3-4.3c0-2.3 1.9-4.3 4.3-4.3z",
        "M34.6 13.5l-.3 1.6c0 .1.1.2.2.2h.1l1.4-.8 1.4.8h.2c.1 0 .1-.1.1-.2l-.3-1.6 1.2-1.1c.1-.1.1-.1.1-.2s-.1-.1-.2-.1l-1.6-.2-.7-1.5c0-.1-.1-.1-.2-.1l-.1.1-.7 1.5-1.7.1c-.1 0-.2.1-.2.2 0 0 0 .1.1.1l1.2 1.2zM36 19c-2.8 0-5 2.2-5 5s2.2 5 5 5 5-2.2 5-5c0-2.7-2.2-5-5-5zm0 9.4c-2.4 0-4.3-1.9-4.3-4.3s1.9-4.3 4.3-4.3 4.3 1.9 4.3 4.3c.1 2.3-1.9 4.3-4.3 4.3z",
        "M38.5 23l-1.6-.2-.7-1.5c0-.1-.1-.1-.2-.1l-.1.1-.7 1.5-1.7.2c-.1 0-.2.1-.2.2 0 0 0 .1.1.1l1.2 1.1-.3 1.6c0 .1.1.2.2.2h.1l1.4-.8 1.4.8h.2c.1 0 .1-.1.1-.2l-.3-1.6 1.2-1.1c.1-.1.1-.1.1-.2s-.1-.1-.2-.1zM36 30c-2.8 0-5 2.2-5 5s2.2 5 5 5 5-2.2 5-5-2.2-5-5-5zm0 9.3c-2.4 0-4.3-1.9-4.3-4.3s1.9-4.3 4.3-4.3 4.3 1.9 4.3 4.3c.1 2.3-1.9 4.3-4.3 4.3z",
        "M38.5 33.9l-1.6-.2-.7-1.5c0-.1-.1-.1-.2-.1l-.1.1-.7 1.5-1.6.2c-.1 0-.2.1-.2.2 0 0 0 .1.1.1l1.2 1.1-.4 1.7c0 .1.1.2.2.2h.1l1.4-.8 1.4.8h.2c.1 0 .1-.1.1-.2l-.3-1.6 1.2-1.1c.1-.1.1-.1.1-.2s-.1-.2-.2-.2z",
        ""
      ],
      tags: ["icon-list"],
      viewBox: ["0 0 48 48"]
    },
    {
      paths: [
        "M21.5 8.6L13.4.5c-.4-.3-.8-.5-1.3-.5H5.8C4.8 0 4 .8 4 1.8v6.3c0 .5.2.9.5 1.3l8.1 8.1c.3.3.8.5 1.3.5s.9-.2 1.3-.5l6.3-6.3c.3-.3.5-.8.5-1.3s-.2-.9-.5-1.3zM7.2 4.5c-.7 0-1.3-.6-1.3-1.3s.6-1.3 1.3-1.3 1.3.6 1.3 1.3-.6 1.3-1.3 1.3z",
        "M12.6 17.5L4.5 9.4c-.3-.3-.5-.8-.5-1.2-.2.2-.5.3-.8.3-.7 0-1.4-.6-1.4-1.3s.6-1.4 1.4-1.4c.3 0 .6.1.8.3V4H1.8C.8 4 0 4.8 0 5.8v6.3c0 .5.2.9.5 1.3l8.1 8.1c.4.3.8.5 1.3.5s.9-.2 1.3-.5l4-4c-.3.3-.8.5-1.3.5s-.9-.2-1.3-.5z"
      ],
      tags: ["tags-attributes"],
      viewBox: ["0 0 22 22"]
    },
    {
      paths: [
        "M7.4 11.2c1.8.5 3.2 2 3.7 3.6.5-.4 1.1-.9 1.7-1.4l-4-4c-.5.7-1 1.3-1.4 1.8zM19.4 0c-3.1.3-7.7 5.8-9.7 8.3l4.2 4.2c2.3-2 7.9-7 8-10.1.2-1.4-1-2.5-2.5-2.4zM2.2 14.1c-1.8 2.3-.3 4.4-2 6.3-.3.3-.2.8.2 1 2.9 1.2 6.6.4 8.3-1.4 1.6-1.7 2.1-4.6.1-6.5-1.8-1.9-5-1.4-6.6.6z"
      ],
      tags: ["brush"],
      viewBox: ["0 0 22 22"]
    },
    {
      paths: [
        "M8 4c0 2.2-1.8 4-4 4S0 6.2 0 4s1.8-4 4-4 4 1.8 4 4zM4 32c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4zm0-16c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4z"
      ],
      tags: ["three-dots"],
      viewBox: ["0 0 8 40"]
    },
    {
      paths: ["M40 18v8H4v-8h36m4-4H0v16h44V14z", "M44 34H0v10h4v-6h36v6h4V34z", "M44 0h-4v6H4V0H0v10h44V0z"],
      tags: ["templates-body"],
      viewBox: ["0 0 44 44"]
    },
    {
      paths: ["M40 32v8H4v-8h36m4-4H0v16h44V28z", "M44 0h-4v20H4V0H0v24h44V0z"],
      tags: ["templates-footer"],
      viewBox: ["0 0 44 44"]
    },
    {
      paths: ["M40 4v8H4V4h36m4-4H0v16h44V0z", "M42 24v-2 20z", "M44 20H0v24h4V24h36v20h4V20z"],
      tags: ["templates-header"],
      viewBox: ["0 0 44 44"]
    },
    {
      paths: [
        "M23 14.29c-.63-.19-1.3-.29-2-.29s-1.37.1-2 .29A7.018 7.018 0 0014.29 19H11c-1.1 0-2-.9-2-2v-3.29A7.018 7.018 0 0013.71 9c.19-.63.29-1.3.29-2s-.1-1.37-.29-2C12.85 2.11 10.17 0 7 0 3.13 0 0 3.13 0 7c0 3.17 2.11 5.85 5 6.71V17c0 3.31 2.69 6 6 6h3.29c.86 2.89 3.54 5 6.71 5 3.87 0 7-3.13 7-7 0-3.17-2.11-5.85-5-6.71zM4 7c0-1.65 1.35-3 3-3 .99 0 1.86.49 2.4 1.22.37.5.6 1.11.6 1.78 0 1.65-1.35 3-3 3-.67 0-1.28-.23-1.78-.6A2.986 2.986 0 014 7zm17 17c-.99 0-1.86-.49-2.4-1.22-.37-.5-.6-1.11-.6-1.78 0-1.65 1.35-3 3-3 .67 0 1.28.23 1.78.6.73.54 1.22 1.41 1.22 2.4 0 1.65-1.35 3-3 3z"
      ],
      tags: ["child"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M23 14.29c-.63-.19-1.3-.29-2-.29s-1.37.1-2 .29A7.018 7.018 0 0014.29 19H11c-1.1 0-2-.9-2-2v-3.29A7.018 7.018 0 0013.71 9c.19-.63.29-1.3.29-2s-.1-1.37-.29-2C12.85 2.11 10.17 0 7 0 3.13 0 0 3.13 0 7c0 3.17 2.11 5.85 5 6.71V17c0 3.31 2.69 6 6 6h3.29c.86 2.89 3.54 5 6.71 5 3.87 0 7-3.13 7-7 0-3.17-2.11-5.85-5-6.71zM4 7c0-1.65 1.35-3 3-3 .99 0 1.86.49 2.4 1.22.37.5.6 1.11.6 1.78 0 1.65-1.35 3-3 3-.67 0-1.28-.23-1.78-.6A2.986 2.986 0 014 7zm17 17c-.99 0-1.86-.49-2.4-1.22-.37-.5-.6-1.11-.6-1.78 0-1.65 1.35-3 3-3 .67 0 1.28.23 1.78.6.73.54 1.22 1.41 1.22 2.4 0 1.65-1.35 3-3 3zm7-16h-4v4h-4V8h-4V4h4V0h4v4h4v4z"
      ],
      tags: ["child-add"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["m2.8 11.2 8.4-8.4L14 0l2.8 2.8 8.4 8.4-2.8 2.8L16 7.6V28h-4V7.6L5.6 14l-2.8-2.8z"],
      tags: ["long-arrow-up"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: ["m25.2 16.8-8.4 8.4L14 28l-2.8-2.8-8.4-8.4L5.6 14l6.4 6.4V0h4v20.4l6.4-6.4 2.8 2.8z"],
      tags: ["long-arrow-down"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M14 10h3v4h-3v3h-4v-3H7v-4h3V7h4v3zm14 15-2.8 2.8-6.1-6.1c-2 1.4-4.4 2.3-7.1 2.3-6.6 0-12-5.4-12-12S5.4 0 12 0s12 5.4 12 12c0 2.5-.7 4.8-2.1 6.9L28 25zm-8-13c0-4.4-3.6-8-8-8s-8 3.6-8 8 3.6 8 8 8 8-3.6 8-8z"
      ],
      tags: ["zoom"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "m28 5.1-6.2 17.7h-4.1l-2.6-7.7c-.7-2.3-1-3.4-1.1-4.3-.2 1-.5 2-1.2 4.4l-2.6 7.6H5.9L0 5.1h4.7L7 12.7c.4 1.4.8 2.9 1.1 4.3.3-1.4.8-2.9 1.2-4.3l2.4-7.6h4.5l2.4 7.6c.2.7.9 3.4 1.1 4.6.2-1.2 1-3.8 1.2-4.6l2.4-7.6H28z"
      ],
      tags: ["width"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M22 11h-2v-1c0-3.3-2.7-6-6-6s-6 2.7-6 6v1H6c-1.1 0-2 .9-2 2v9c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2v-9c0-1.1-.9-2-2-2zm-6 0h-4v-1c0-1.1.9-2 2-2s2 .9 2 2v1z"
      ],
      tags: ["lock"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "m13 11-9 9v-7c0-1.1.9-2 2-2h2v-1c0-3.3 2.7-6 6-6 1.7 0 3.2.7 4.2 1.8l-2.8 2.8c-.8-.8-2-.8-2.8 0-.4.4-.6.9-.6 1.4v1h1zm9 0h-.5l4.8-4.8-2.8-2.8L3.6 23.2 6.4 26l2-2H22c1.1 0 2-.9 2-2v-9c0-1.1-.9-2-2-2z"
      ],
      tags: ["unlock"],
      viewBox: ["0 0 28 28"]
    },
    {
      paths: [
        "M341.6 29.2L240.1 130.8l-9.4-9.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-9.4-9.4L482.8 170.4c39-39 39-102.2 0-141.1s-102.2-39-141.1 0zM55.4 323.3c-15 15-23.4 35.4-23.4 56.6v42.4L5.4 462.2c-8.5 12.7-6.8 29.6 4 40.4s27.7 12.5 40.4 4L89.7 480h42.4c21.2 0 41.6-8.4 56.6-23.4L309.4 335.9l-45.3-45.3L143.4 411.3c-3 3-7.1 4.7-11.3 4.7H96V379.9c0-4.2 1.7-8.3 4.7-11.3L221.4 247.9l-45.3-45.3L55.4 323.3z"
      ],
      tags: ["eyedropper"],
      viewBox: ["0 0 512 512"]
    }
  ];
  const getSearchIcon = (searchIcon) => SvgIcons.find((icon) => {
    return icon.tags[0] === searchIcon;
  });
  var Icon_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$1j = ["viewBox", "preserveAspectRatio", "innerHTML"];
  const __default__$19 = {
    name: "Icon"
  };
  const _sfc_main$1K = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$19), {
    props: {
      icon: null,
      rotate: { type: [Boolean, String, Number], default: false },
      bgSize: null,
      color: null,
      size: null,
      bgColor: null,
      stroke: null,
      rounded: { type: Boolean, default: false },
      preserveAspectRatio: null
    },
    setup(__props) {
      const props = __props;
      const iconStyles = vue.computed(() => {
        return {
          width: props.bgSize + "px",
          height: props.bgSize + "px",
          color: props.color,
          fontSize: props.size + "px",
          background: props.bgColor,
          stroke: props.stroke,
          transform: elementTransform.value
        };
      });
      const iconClass = vue.computed(() => {
        return {
          "znpb-editor-icon--rounded": props.rounded
        };
      });
      const iconSettings = vue.computed(() => {
        const iconOption = getSearchIcon(props.icon);
        if (!iconOption) {
          return {};
        }
        let pathString = "";
        if (iconOption.circle) {
          for (const circle of iconOption.circle) {
            pathString += `<circle  ${circle} fill="currentColor"></circle>`;
          }
        }
        if (iconOption.rect) {
          for (const rect of iconOption.rect) {
            pathString += `<rect ${rect}></rect>`;
          }
        }
        if (iconOption.polygon) {
          for (const polygon of iconOption.polygon) {
            pathString += `<polygon points='${polygon}' fill="currentColor"></polygon>`;
          }
        }
        for (const path of iconOption.paths) {
          pathString += `<path fill="currentColor" d="${path}"></path>`;
        }
        return {
          viewBox: (iconOption == null ? void 0 : iconOption.viewBox) ? iconOption == null ? void 0 : iconOption.viewBox.join("") : "0 0 50 50 ",
          pathString
        };
      });
      const elementTransform = vue.computed(() => {
        let cssStyles = "";
        if (props.rotate) {
          if (typeof props.rotate === "string" || typeof props.rotate === "number") {
            cssStyles = `rotate(${props.rotate}deg)`;
          } else
            cssStyles = "rotate(90deg)";
        }
        return cssStyles;
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("span", {
          class: vue.normalizeClass(["znpb-editor-icon-wrapper", vue.unref(iconClass)]),
          style: vue.normalizeStyle(vue.unref(iconStyles))
        }, [
          vue.unref(iconSettings) ? (vue.openBlock(), vue.createElementBlock("svg", {
            key: 0,
            class: vue.normalizeClass(["zion-svg-inline znpb-editor-icon zion-icon", {
              [`zion-${__props.icon}`]: __props.icon
            }]),
            xmlns: "http://www.w3.org/2000/svg",
            "aria-hidden": "true",
            viewBox: vue.unref(iconSettings).viewBox,
            preserveAspectRatio: props.preserveAspectRatio || "",
            innerHTML: vue.unref(iconSettings).pathString
          }, null, 10, _hoisted_1$1j)) : vue.createCommentVNode("", true)
        ], 6);
      };
    }
  }));
  var Accordion_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$1i = {
    key: 0,
    class: "znpb-accordion__content"
  };
  const __default__$18 = {
    name: "Accordion"
  };
  const _sfc_main$1J = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$18), {
    props: {
      collapsed: { type: Boolean, default: false },
      header: null
    },
    setup(__props) {
      const props = __props;
      const localCollapsed = vue.ref(props.collapsed);
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          class: vue.normalizeClass(["znpb-accordion", { "znpb-accordion--collapsed": localCollapsed.value }])
        }, [
          vue.createElementVNode("div", {
            class: "znpb-accordion__header",
            onClick: _cache[0] || (_cache[0] = ($event) => localCollapsed.value = !localCollapsed.value)
          }, [
            vue.renderSlot(_ctx.$slots, "header", {}, () => [
              vue.createTextVNode(vue.toDisplayString(__props.header), 1)
            ]),
            vue.createVNode(_sfc_main$1K, {
              icon: "select",
              class: "znpb-accordion-title-icon"
            })
          ]),
          localCollapsed.value ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_1$1i, [
            vue.renderSlot(_ctx.$slots, "default")
          ])) : vue.createCommentVNode("", true)
        ], 2);
      };
    }
  }));
  var ActionsOverlay_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$1h = { class: "znpb-actions-overlay__wrapper" };
  const _hoisted_2$R = {
    key: 0,
    class: "znpb-actions-overlay__actions-wrapper"
  };
  const _hoisted_3$y = { class: "znpb-actions-overlay__actions" };
  const __default__$17 = {
    name: "ActionsOverlay"
  };
  const _sfc_main$1I = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$17), {
    props: {
      showOverlay: { type: Boolean, default: true }
    },
    setup(__props) {
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$1h, [
          vue.renderSlot(_ctx.$slots, "default"),
          __props.showOverlay ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_2$R, [
            vue.createElementVNode("div", _hoisted_3$y, [
              vue.renderSlot(_ctx.$slots, "actions")
            ])
          ])) : vue.createCommentVNode("", true)
        ]);
      };
    }
  }));
  var Button_vue_vue_type_style_index_0_lang = "";
  const __default__$16 = {
    name: "Button"
  };
  const _sfc_main$1H = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$16), {
    props: {
      type: null
    },
    setup(__props) {
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          class: vue.normalizeClass(["znpb-button", { ["znpb-button--" + __props.type]: __props.type }])
        }, [
          vue.renderSlot(_ctx.$slots, "default")
        ], 2);
      };
    }
  }));
  var ChangesBullet_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$1g = { class: "znpb-options-has-changes-wrapper" };
  const _hoisted_2$Q = { key: 0 };
  const __default__$15 = {
    name: "ChangesBullet"
  };
  const _sfc_main$1G = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$15), {
    props: {
      discardChangesTitle: { default: () => window.zb.i18n.translate("discard_changes") }
    },
    emits: ["remove-styles"],
    setup(__props) {
      const showIcon = vue.ref(false);
      return (_ctx, _cache) => {
        const _component_Icon = vue.resolveComponent("Icon");
        const _directive_znpb_tooltip = vue.resolveDirective("znpb-tooltip");
        return vue.withDirectives((vue.openBlock(), vue.createElementBlock("span", _hoisted_1$1g, [
          vue.createElementVNode("span", {
            class: "znpb-options__has-changes",
            onClick: _cache[0] || (_cache[0] = vue.withModifiers(($event) => _ctx.$emit("remove-styles"), ["stop"])),
            onMouseover: _cache[1] || (_cache[1] = ($event) => showIcon.value = true),
            onMouseleave: _cache[2] || (_cache[2] = ($event) => showIcon.value = false)
          }, [
            !showIcon.value ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_2$Q)) : (vue.openBlock(), vue.createBlock(_component_Icon, {
              key: 1,
              class: "znpb-options-has-changes-wrapper__delete",
              icon: "close",
              size: 6
            }))
          ], 32)
        ])), [
          [_directive_znpb_tooltip, __props.discardChangesTitle]
        ]);
      };
    }
  }));
  var commonjsGlobal = typeof globalThis !== "undefined" ? globalThis : typeof window !== "undefined" ? window : typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : {};
  function commonjsRequire(path) {
    throw new Error('Could not dynamically require "' + path + '". Please configure the dynamicRequireTargets or/and ignoreDynamicRequires option of @rollup/plugin-commonjs appropriately for this require call to work.');
  }
  var tinycolor$1 = { exports: {} };
  (function(module2) {
    (function(Math2) {
      var trimLeft = /^\s+/, trimRight = /\s+$/, tinyCounter = 0, mathRound = Math2.round, mathMin = Math2.min, mathMax = Math2.max, mathRandom = Math2.random;
      function tinycolor2(color, opts) {
        color = color ? color : "";
        opts = opts || {};
        if (color instanceof tinycolor2) {
          return color;
        }
        if (!(this instanceof tinycolor2)) {
          return new tinycolor2(color, opts);
        }
        var rgb = inputToRGB(color);
        this._originalInput = color, this._r = rgb.r, this._g = rgb.g, this._b = rgb.b, this._a = rgb.a, this._roundA = mathRound(100 * this._a) / 100, this._format = opts.format || rgb.format;
        this._gradientType = opts.gradientType;
        if (this._r < 1) {
          this._r = mathRound(this._r);
        }
        if (this._g < 1) {
          this._g = mathRound(this._g);
        }
        if (this._b < 1) {
          this._b = mathRound(this._b);
        }
        this._ok = rgb.ok;
        this._tc_id = tinyCounter++;
      }
      tinycolor2.prototype = {
        isDark: function() {
          return this.getBrightness() < 128;
        },
        isLight: function() {
          return !this.isDark();
        },
        isValid: function() {
          return this._ok;
        },
        getOriginalInput: function() {
          return this._originalInput;
        },
        getFormat: function() {
          return this._format;
        },
        getAlpha: function() {
          return this._a;
        },
        getBrightness: function() {
          var rgb = this.toRgb();
          return (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1e3;
        },
        getLuminance: function() {
          var rgb = this.toRgb();
          var RsRGB, GsRGB, BsRGB, R, G, B;
          RsRGB = rgb.r / 255;
          GsRGB = rgb.g / 255;
          BsRGB = rgb.b / 255;
          if (RsRGB <= 0.03928) {
            R = RsRGB / 12.92;
          } else {
            R = Math2.pow((RsRGB + 0.055) / 1.055, 2.4);
          }
          if (GsRGB <= 0.03928) {
            G = GsRGB / 12.92;
          } else {
            G = Math2.pow((GsRGB + 0.055) / 1.055, 2.4);
          }
          if (BsRGB <= 0.03928) {
            B = BsRGB / 12.92;
          } else {
            B = Math2.pow((BsRGB + 0.055) / 1.055, 2.4);
          }
          return 0.2126 * R + 0.7152 * G + 0.0722 * B;
        },
        setAlpha: function(value) {
          this._a = boundAlpha(value);
          this._roundA = mathRound(100 * this._a) / 100;
          return this;
        },
        toHsv: function() {
          var hsv = rgbToHsv(this._r, this._g, this._b);
          return { h: hsv.h * 360, s: hsv.s, v: hsv.v, a: this._a };
        },
        toHsvString: function() {
          var hsv = rgbToHsv(this._r, this._g, this._b);
          var h = mathRound(hsv.h * 360), s = mathRound(hsv.s * 100), v = mathRound(hsv.v * 100);
          return this._a == 1 ? "hsv(" + h + ", " + s + "%, " + v + "%)" : "hsva(" + h + ", " + s + "%, " + v + "%, " + this._roundA + ")";
        },
        toHsl: function() {
          var hsl = rgbToHsl(this._r, this._g, this._b);
          return { h: hsl.h * 360, s: hsl.s, l: hsl.l, a: this._a };
        },
        toHslString: function() {
          var hsl = rgbToHsl(this._r, this._g, this._b);
          var h = mathRound(hsl.h * 360), s = mathRound(hsl.s * 100), l = mathRound(hsl.l * 100);
          return this._a == 1 ? "hsl(" + h + ", " + s + "%, " + l + "%)" : "hsla(" + h + ", " + s + "%, " + l + "%, " + this._roundA + ")";
        },
        toHex: function(allow3Char) {
          return rgbToHex(this._r, this._g, this._b, allow3Char);
        },
        toHexString: function(allow3Char) {
          return "#" + this.toHex(allow3Char);
        },
        toHex8: function(allow4Char) {
          return rgbaToHex(this._r, this._g, this._b, this._a, allow4Char);
        },
        toHex8String: function(allow4Char) {
          return "#" + this.toHex8(allow4Char);
        },
        toRgb: function() {
          return { r: mathRound(this._r), g: mathRound(this._g), b: mathRound(this._b), a: this._a };
        },
        toRgbString: function() {
          return this._a == 1 ? "rgb(" + mathRound(this._r) + ", " + mathRound(this._g) + ", " + mathRound(this._b) + ")" : "rgba(" + mathRound(this._r) + ", " + mathRound(this._g) + ", " + mathRound(this._b) + ", " + this._roundA + ")";
        },
        toPercentageRgb: function() {
          return { r: mathRound(bound01(this._r, 255) * 100) + "%", g: mathRound(bound01(this._g, 255) * 100) + "%", b: mathRound(bound01(this._b, 255) * 100) + "%", a: this._a };
        },
        toPercentageRgbString: function() {
          return this._a == 1 ? "rgb(" + mathRound(bound01(this._r, 255) * 100) + "%, " + mathRound(bound01(this._g, 255) * 100) + "%, " + mathRound(bound01(this._b, 255) * 100) + "%)" : "rgba(" + mathRound(bound01(this._r, 255) * 100) + "%, " + mathRound(bound01(this._g, 255) * 100) + "%, " + mathRound(bound01(this._b, 255) * 100) + "%, " + this._roundA + ")";
        },
        toName: function() {
          if (this._a === 0) {
            return "transparent";
          }
          if (this._a < 1) {
            return false;
          }
          return hexNames[rgbToHex(this._r, this._g, this._b, true)] || false;
        },
        toFilter: function(secondColor) {
          var hex8String = "#" + rgbaToArgbHex(this._r, this._g, this._b, this._a);
          var secondHex8String = hex8String;
          var gradientType = this._gradientType ? "GradientType = 1, " : "";
          if (secondColor) {
            var s = tinycolor2(secondColor);
            secondHex8String = "#" + rgbaToArgbHex(s._r, s._g, s._b, s._a);
          }
          return "progid:DXImageTransform.Microsoft.gradient(" + gradientType + "startColorstr=" + hex8String + ",endColorstr=" + secondHex8String + ")";
        },
        toString: function(format) {
          var formatSet = !!format;
          format = format || this._format;
          var formattedString = false;
          var hasAlpha = this._a < 1 && this._a >= 0;
          var needsAlphaFormat = !formatSet && hasAlpha && (format === "hex" || format === "hex6" || format === "hex3" || format === "hex4" || format === "hex8" || format === "name");
          if (needsAlphaFormat) {
            if (format === "name" && this._a === 0) {
              return this.toName();
            }
            return this.toRgbString();
          }
          if (format === "rgb") {
            formattedString = this.toRgbString();
          }
          if (format === "prgb") {
            formattedString = this.toPercentageRgbString();
          }
          if (format === "hex" || format === "hex6") {
            formattedString = this.toHexString();
          }
          if (format === "hex3") {
            formattedString = this.toHexString(true);
          }
          if (format === "hex4") {
            formattedString = this.toHex8String(true);
          }
          if (format === "hex8") {
            formattedString = this.toHex8String();
          }
          if (format === "name") {
            formattedString = this.toName();
          }
          if (format === "hsl") {
            formattedString = this.toHslString();
          }
          if (format === "hsv") {
            formattedString = this.toHsvString();
          }
          return formattedString || this.toHexString();
        },
        clone: function() {
          return tinycolor2(this.toString());
        },
        _applyModification: function(fn, args) {
          var color = fn.apply(null, [this].concat([].slice.call(args)));
          this._r = color._r;
          this._g = color._g;
          this._b = color._b;
          this.setAlpha(color._a);
          return this;
        },
        lighten: function() {
          return this._applyModification(lighten, arguments);
        },
        brighten: function() {
          return this._applyModification(brighten, arguments);
        },
        darken: function() {
          return this._applyModification(darken, arguments);
        },
        desaturate: function() {
          return this._applyModification(desaturate, arguments);
        },
        saturate: function() {
          return this._applyModification(saturate, arguments);
        },
        greyscale: function() {
          return this._applyModification(greyscale, arguments);
        },
        spin: function() {
          return this._applyModification(spin, arguments);
        },
        _applyCombination: function(fn, args) {
          return fn.apply(null, [this].concat([].slice.call(args)));
        },
        analogous: function() {
          return this._applyCombination(analogous, arguments);
        },
        complement: function() {
          return this._applyCombination(complement, arguments);
        },
        monochromatic: function() {
          return this._applyCombination(monochromatic, arguments);
        },
        splitcomplement: function() {
          return this._applyCombination(splitcomplement, arguments);
        },
        triad: function() {
          return this._applyCombination(triad, arguments);
        },
        tetrad: function() {
          return this._applyCombination(tetrad, arguments);
        }
      };
      tinycolor2.fromRatio = function(color, opts) {
        if (typeof color == "object") {
          var newColor = {};
          for (var i in color) {
            if (color.hasOwnProperty(i)) {
              if (i === "a") {
                newColor[i] = color[i];
              } else {
                newColor[i] = convertToPercentage(color[i]);
              }
            }
          }
          color = newColor;
        }
        return tinycolor2(color, opts);
      };
      function inputToRGB(color) {
        var rgb = { r: 0, g: 0, b: 0 };
        var a = 1;
        var s = null;
        var v = null;
        var l = null;
        var ok = false;
        var format = false;
        if (typeof color == "string") {
          color = stringInputToObject(color);
        }
        if (typeof color == "object") {
          if (isValidCSSUnit(color.r) && isValidCSSUnit(color.g) && isValidCSSUnit(color.b)) {
            rgb = rgbToRgb(color.r, color.g, color.b);
            ok = true;
            format = String(color.r).substr(-1) === "%" ? "prgb" : "rgb";
          } else if (isValidCSSUnit(color.h) && isValidCSSUnit(color.s) && isValidCSSUnit(color.v)) {
            s = convertToPercentage(color.s);
            v = convertToPercentage(color.v);
            rgb = hsvToRgb(color.h, s, v);
            ok = true;
            format = "hsv";
          } else if (isValidCSSUnit(color.h) && isValidCSSUnit(color.s) && isValidCSSUnit(color.l)) {
            s = convertToPercentage(color.s);
            l = convertToPercentage(color.l);
            rgb = hslToRgb(color.h, s, l);
            ok = true;
            format = "hsl";
          }
          if (color.hasOwnProperty("a")) {
            a = color.a;
          }
        }
        a = boundAlpha(a);
        return {
          ok,
          format: color.format || format,
          r: mathMin(255, mathMax(rgb.r, 0)),
          g: mathMin(255, mathMax(rgb.g, 0)),
          b: mathMin(255, mathMax(rgb.b, 0)),
          a
        };
      }
      function rgbToRgb(r, g, b) {
        return {
          r: bound01(r, 255) * 255,
          g: bound01(g, 255) * 255,
          b: bound01(b, 255) * 255
        };
      }
      function rgbToHsl(r, g, b) {
        r = bound01(r, 255);
        g = bound01(g, 255);
        b = bound01(b, 255);
        var max2 = mathMax(r, g, b), min2 = mathMin(r, g, b);
        var h, s, l = (max2 + min2) / 2;
        if (max2 == min2) {
          h = s = 0;
        } else {
          var d = max2 - min2;
          s = l > 0.5 ? d / (2 - max2 - min2) : d / (max2 + min2);
          switch (max2) {
            case r:
              h = (g - b) / d + (g < b ? 6 : 0);
              break;
            case g:
              h = (b - r) / d + 2;
              break;
            case b:
              h = (r - g) / d + 4;
              break;
          }
          h /= 6;
        }
        return { h, s, l };
      }
      function hslToRgb(h, s, l) {
        var r, g, b;
        h = bound01(h, 360);
        s = bound01(s, 100);
        l = bound01(l, 100);
        function hue2rgb(p2, q2, t) {
          if (t < 0)
            t += 1;
          if (t > 1)
            t -= 1;
          if (t < 1 / 6)
            return p2 + (q2 - p2) * 6 * t;
          if (t < 1 / 2)
            return q2;
          if (t < 2 / 3)
            return p2 + (q2 - p2) * (2 / 3 - t) * 6;
          return p2;
        }
        if (s === 0) {
          r = g = b = l;
        } else {
          var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
          var p = 2 * l - q;
          r = hue2rgb(p, q, h + 1 / 3);
          g = hue2rgb(p, q, h);
          b = hue2rgb(p, q, h - 1 / 3);
        }
        return { r: r * 255, g: g * 255, b: b * 255 };
      }
      function rgbToHsv(r, g, b) {
        r = bound01(r, 255);
        g = bound01(g, 255);
        b = bound01(b, 255);
        var max2 = mathMax(r, g, b), min2 = mathMin(r, g, b);
        var h, s, v = max2;
        var d = max2 - min2;
        s = max2 === 0 ? 0 : d / max2;
        if (max2 == min2) {
          h = 0;
        } else {
          switch (max2) {
            case r:
              h = (g - b) / d + (g < b ? 6 : 0);
              break;
            case g:
              h = (b - r) / d + 2;
              break;
            case b:
              h = (r - g) / d + 4;
              break;
          }
          h /= 6;
        }
        return { h, s, v };
      }
      function hsvToRgb(h, s, v) {
        h = bound01(h, 360) * 6;
        s = bound01(s, 100);
        v = bound01(v, 100);
        var i = Math2.floor(h), f = h - i, p = v * (1 - s), q = v * (1 - f * s), t = v * (1 - (1 - f) * s), mod = i % 6, r = [v, q, p, p, t, v][mod], g = [t, v, v, q, p, p][mod], b = [p, p, t, v, v, q][mod];
        return { r: r * 255, g: g * 255, b: b * 255 };
      }
      function rgbToHex(r, g, b, allow3Char) {
        var hex = [
          pad2(mathRound(r).toString(16)),
          pad2(mathRound(g).toString(16)),
          pad2(mathRound(b).toString(16))
        ];
        if (allow3Char && hex[0].charAt(0) == hex[0].charAt(1) && hex[1].charAt(0) == hex[1].charAt(1) && hex[2].charAt(0) == hex[2].charAt(1)) {
          return hex[0].charAt(0) + hex[1].charAt(0) + hex[2].charAt(0);
        }
        return hex.join("");
      }
      function rgbaToHex(r, g, b, a, allow4Char) {
        var hex = [
          pad2(mathRound(r).toString(16)),
          pad2(mathRound(g).toString(16)),
          pad2(mathRound(b).toString(16)),
          pad2(convertDecimalToHex(a))
        ];
        if (allow4Char && hex[0].charAt(0) == hex[0].charAt(1) && hex[1].charAt(0) == hex[1].charAt(1) && hex[2].charAt(0) == hex[2].charAt(1) && hex[3].charAt(0) == hex[3].charAt(1)) {
          return hex[0].charAt(0) + hex[1].charAt(0) + hex[2].charAt(0) + hex[3].charAt(0);
        }
        return hex.join("");
      }
      function rgbaToArgbHex(r, g, b, a) {
        var hex = [
          pad2(convertDecimalToHex(a)),
          pad2(mathRound(r).toString(16)),
          pad2(mathRound(g).toString(16)),
          pad2(mathRound(b).toString(16))
        ];
        return hex.join("");
      }
      tinycolor2.equals = function(color1, color2) {
        if (!color1 || !color2) {
          return false;
        }
        return tinycolor2(color1).toRgbString() == tinycolor2(color2).toRgbString();
      };
      tinycolor2.random = function() {
        return tinycolor2.fromRatio({
          r: mathRandom(),
          g: mathRandom(),
          b: mathRandom()
        });
      };
      function desaturate(color, amount) {
        amount = amount === 0 ? 0 : amount || 10;
        var hsl = tinycolor2(color).toHsl();
        hsl.s -= amount / 100;
        hsl.s = clamp01(hsl.s);
        return tinycolor2(hsl);
      }
      function saturate(color, amount) {
        amount = amount === 0 ? 0 : amount || 10;
        var hsl = tinycolor2(color).toHsl();
        hsl.s += amount / 100;
        hsl.s = clamp01(hsl.s);
        return tinycolor2(hsl);
      }
      function greyscale(color) {
        return tinycolor2(color).desaturate(100);
      }
      function lighten(color, amount) {
        amount = amount === 0 ? 0 : amount || 10;
        var hsl = tinycolor2(color).toHsl();
        hsl.l += amount / 100;
        hsl.l = clamp01(hsl.l);
        return tinycolor2(hsl);
      }
      function brighten(color, amount) {
        amount = amount === 0 ? 0 : amount || 10;
        var rgb = tinycolor2(color).toRgb();
        rgb.r = mathMax(0, mathMin(255, rgb.r - mathRound(255 * -(amount / 100))));
        rgb.g = mathMax(0, mathMin(255, rgb.g - mathRound(255 * -(amount / 100))));
        rgb.b = mathMax(0, mathMin(255, rgb.b - mathRound(255 * -(amount / 100))));
        return tinycolor2(rgb);
      }
      function darken(color, amount) {
        amount = amount === 0 ? 0 : amount || 10;
        var hsl = tinycolor2(color).toHsl();
        hsl.l -= amount / 100;
        hsl.l = clamp01(hsl.l);
        return tinycolor2(hsl);
      }
      function spin(color, amount) {
        var hsl = tinycolor2(color).toHsl();
        var hue = (hsl.h + amount) % 360;
        hsl.h = hue < 0 ? 360 + hue : hue;
        return tinycolor2(hsl);
      }
      function complement(color) {
        var hsl = tinycolor2(color).toHsl();
        hsl.h = (hsl.h + 180) % 360;
        return tinycolor2(hsl);
      }
      function triad(color) {
        var hsl = tinycolor2(color).toHsl();
        var h = hsl.h;
        return [
          tinycolor2(color),
          tinycolor2({ h: (h + 120) % 360, s: hsl.s, l: hsl.l }),
          tinycolor2({ h: (h + 240) % 360, s: hsl.s, l: hsl.l })
        ];
      }
      function tetrad(color) {
        var hsl = tinycolor2(color).toHsl();
        var h = hsl.h;
        return [
          tinycolor2(color),
          tinycolor2({ h: (h + 90) % 360, s: hsl.s, l: hsl.l }),
          tinycolor2({ h: (h + 180) % 360, s: hsl.s, l: hsl.l }),
          tinycolor2({ h: (h + 270) % 360, s: hsl.s, l: hsl.l })
        ];
      }
      function splitcomplement(color) {
        var hsl = tinycolor2(color).toHsl();
        var h = hsl.h;
        return [
          tinycolor2(color),
          tinycolor2({ h: (h + 72) % 360, s: hsl.s, l: hsl.l }),
          tinycolor2({ h: (h + 216) % 360, s: hsl.s, l: hsl.l })
        ];
      }
      function analogous(color, results, slices) {
        results = results || 6;
        slices = slices || 30;
        var hsl = tinycolor2(color).toHsl();
        var part = 360 / slices;
        var ret = [tinycolor2(color)];
        for (hsl.h = (hsl.h - (part * results >> 1) + 720) % 360; --results; ) {
          hsl.h = (hsl.h + part) % 360;
          ret.push(tinycolor2(hsl));
        }
        return ret;
      }
      function monochromatic(color, results) {
        results = results || 6;
        var hsv = tinycolor2(color).toHsv();
        var h = hsv.h, s = hsv.s, v = hsv.v;
        var ret = [];
        var modification = 1 / results;
        while (results--) {
          ret.push(tinycolor2({ h, s, v }));
          v = (v + modification) % 1;
        }
        return ret;
      }
      tinycolor2.mix = function(color1, color2, amount) {
        amount = amount === 0 ? 0 : amount || 50;
        var rgb1 = tinycolor2(color1).toRgb();
        var rgb2 = tinycolor2(color2).toRgb();
        var p = amount / 100;
        var rgba = {
          r: (rgb2.r - rgb1.r) * p + rgb1.r,
          g: (rgb2.g - rgb1.g) * p + rgb1.g,
          b: (rgb2.b - rgb1.b) * p + rgb1.b,
          a: (rgb2.a - rgb1.a) * p + rgb1.a
        };
        return tinycolor2(rgba);
      };
      tinycolor2.readability = function(color1, color2) {
        var c1 = tinycolor2(color1);
        var c2 = tinycolor2(color2);
        return (Math2.max(c1.getLuminance(), c2.getLuminance()) + 0.05) / (Math2.min(c1.getLuminance(), c2.getLuminance()) + 0.05);
      };
      tinycolor2.isReadable = function(color1, color2, wcag2) {
        var readability = tinycolor2.readability(color1, color2);
        var wcag2Parms, out;
        out = false;
        wcag2Parms = validateWCAG2Parms(wcag2);
        switch (wcag2Parms.level + wcag2Parms.size) {
          case "AAsmall":
          case "AAAlarge":
            out = readability >= 4.5;
            break;
          case "AAlarge":
            out = readability >= 3;
            break;
          case "AAAsmall":
            out = readability >= 7;
            break;
        }
        return out;
      };
      tinycolor2.mostReadable = function(baseColor, colorList, args) {
        var bestColor = null;
        var bestScore = 0;
        var readability;
        var includeFallbackColors, level, size;
        args = args || {};
        includeFallbackColors = args.includeFallbackColors;
        level = args.level;
        size = args.size;
        for (var i = 0; i < colorList.length; i++) {
          readability = tinycolor2.readability(baseColor, colorList[i]);
          if (readability > bestScore) {
            bestScore = readability;
            bestColor = tinycolor2(colorList[i]);
          }
        }
        if (tinycolor2.isReadable(baseColor, bestColor, { "level": level, "size": size }) || !includeFallbackColors) {
          return bestColor;
        } else {
          args.includeFallbackColors = false;
          return tinycolor2.mostReadable(baseColor, ["#fff", "#000"], args);
        }
      };
      var names = tinycolor2.names = {
        aliceblue: "f0f8ff",
        antiquewhite: "faebd7",
        aqua: "0ff",
        aquamarine: "7fffd4",
        azure: "f0ffff",
        beige: "f5f5dc",
        bisque: "ffe4c4",
        black: "000",
        blanchedalmond: "ffebcd",
        blue: "00f",
        blueviolet: "8a2be2",
        brown: "a52a2a",
        burlywood: "deb887",
        burntsienna: "ea7e5d",
        cadetblue: "5f9ea0",
        chartreuse: "7fff00",
        chocolate: "d2691e",
        coral: "ff7f50",
        cornflowerblue: "6495ed",
        cornsilk: "fff8dc",
        crimson: "dc143c",
        cyan: "0ff",
        darkblue: "00008b",
        darkcyan: "008b8b",
        darkgoldenrod: "b8860b",
        darkgray: "a9a9a9",
        darkgreen: "006400",
        darkgrey: "a9a9a9",
        darkkhaki: "bdb76b",
        darkmagenta: "8b008b",
        darkolivegreen: "556b2f",
        darkorange: "ff8c00",
        darkorchid: "9932cc",
        darkred: "8b0000",
        darksalmon: "e9967a",
        darkseagreen: "8fbc8f",
        darkslateblue: "483d8b",
        darkslategray: "2f4f4f",
        darkslategrey: "2f4f4f",
        darkturquoise: "00ced1",
        darkviolet: "9400d3",
        deeppink: "ff1493",
        deepskyblue: "00bfff",
        dimgray: "696969",
        dimgrey: "696969",
        dodgerblue: "1e90ff",
        firebrick: "b22222",
        floralwhite: "fffaf0",
        forestgreen: "228b22",
        fuchsia: "f0f",
        gainsboro: "dcdcdc",
        ghostwhite: "f8f8ff",
        gold: "ffd700",
        goldenrod: "daa520",
        gray: "808080",
        green: "008000",
        greenyellow: "adff2f",
        grey: "808080",
        honeydew: "f0fff0",
        hotpink: "ff69b4",
        indianred: "cd5c5c",
        indigo: "4b0082",
        ivory: "fffff0",
        khaki: "f0e68c",
        lavender: "e6e6fa",
        lavenderblush: "fff0f5",
        lawngreen: "7cfc00",
        lemonchiffon: "fffacd",
        lightblue: "add8e6",
        lightcoral: "f08080",
        lightcyan: "e0ffff",
        lightgoldenrodyellow: "fafad2",
        lightgray: "d3d3d3",
        lightgreen: "90ee90",
        lightgrey: "d3d3d3",
        lightpink: "ffb6c1",
        lightsalmon: "ffa07a",
        lightseagreen: "20b2aa",
        lightskyblue: "87cefa",
        lightslategray: "789",
        lightslategrey: "789",
        lightsteelblue: "b0c4de",
        lightyellow: "ffffe0",
        lime: "0f0",
        limegreen: "32cd32",
        linen: "faf0e6",
        magenta: "f0f",
        maroon: "800000",
        mediumaquamarine: "66cdaa",
        mediumblue: "0000cd",
        mediumorchid: "ba55d3",
        mediumpurple: "9370db",
        mediumseagreen: "3cb371",
        mediumslateblue: "7b68ee",
        mediumspringgreen: "00fa9a",
        mediumturquoise: "48d1cc",
        mediumvioletred: "c71585",
        midnightblue: "191970",
        mintcream: "f5fffa",
        mistyrose: "ffe4e1",
        moccasin: "ffe4b5",
        navajowhite: "ffdead",
        navy: "000080",
        oldlace: "fdf5e6",
        olive: "808000",
        olivedrab: "6b8e23",
        orange: "ffa500",
        orangered: "ff4500",
        orchid: "da70d6",
        palegoldenrod: "eee8aa",
        palegreen: "98fb98",
        paleturquoise: "afeeee",
        palevioletred: "db7093",
        papayawhip: "ffefd5",
        peachpuff: "ffdab9",
        peru: "cd853f",
        pink: "ffc0cb",
        plum: "dda0dd",
        powderblue: "b0e0e6",
        purple: "800080",
        rebeccapurple: "663399",
        red: "f00",
        rosybrown: "bc8f8f",
        royalblue: "4169e1",
        saddlebrown: "8b4513",
        salmon: "fa8072",
        sandybrown: "f4a460",
        seagreen: "2e8b57",
        seashell: "fff5ee",
        sienna: "a0522d",
        silver: "c0c0c0",
        skyblue: "87ceeb",
        slateblue: "6a5acd",
        slategray: "708090",
        slategrey: "708090",
        snow: "fffafa",
        springgreen: "00ff7f",
        steelblue: "4682b4",
        tan: "d2b48c",
        teal: "008080",
        thistle: "d8bfd8",
        tomato: "ff6347",
        turquoise: "40e0d0",
        violet: "ee82ee",
        wheat: "f5deb3",
        white: "fff",
        whitesmoke: "f5f5f5",
        yellow: "ff0",
        yellowgreen: "9acd32"
      };
      var hexNames = tinycolor2.hexNames = flip2(names);
      function flip2(o) {
        var flipped = {};
        for (var i in o) {
          if (o.hasOwnProperty(i)) {
            flipped[o[i]] = i;
          }
        }
        return flipped;
      }
      function boundAlpha(a) {
        a = parseFloat(a);
        if (isNaN(a) || a < 0 || a > 1) {
          a = 1;
        }
        return a;
      }
      function bound01(n, max2) {
        if (isOnePointZero(n)) {
          n = "100%";
        }
        var processPercent = isPercentage(n);
        n = mathMin(max2, mathMax(0, parseFloat(n)));
        if (processPercent) {
          n = parseInt(n * max2, 10) / 100;
        }
        if (Math2.abs(n - max2) < 1e-6) {
          return 1;
        }
        return n % max2 / parseFloat(max2);
      }
      function clamp01(val) {
        return mathMin(1, mathMax(0, val));
      }
      function parseIntFromHex(val) {
        return parseInt(val, 16);
      }
      function isOnePointZero(n) {
        return typeof n == "string" && n.indexOf(".") != -1 && parseFloat(n) === 1;
      }
      function isPercentage(n) {
        return typeof n === "string" && n.indexOf("%") != -1;
      }
      function pad2(c) {
        return c.length == 1 ? "0" + c : "" + c;
      }
      function convertToPercentage(n) {
        if (n <= 1) {
          n = n * 100 + "%";
        }
        return n;
      }
      function convertDecimalToHex(d) {
        return Math2.round(parseFloat(d) * 255).toString(16);
      }
      function convertHexToDecimal(h) {
        return parseIntFromHex(h) / 255;
      }
      var matchers = function() {
        var CSS_INTEGER = "[-\\+]?\\d+%?";
        var CSS_NUMBER = "[-\\+]?\\d*\\.\\d+%?";
        var CSS_UNIT = "(?:" + CSS_NUMBER + ")|(?:" + CSS_INTEGER + ")";
        var PERMISSIVE_MATCH3 = "[\\s|\\(]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")\\s*\\)?";
        var PERMISSIVE_MATCH4 = "[\\s|\\(]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")[,|\\s]+(" + CSS_UNIT + ")\\s*\\)?";
        return {
          CSS_UNIT: new RegExp(CSS_UNIT),
          rgb: new RegExp("rgb" + PERMISSIVE_MATCH3),
          rgba: new RegExp("rgba" + PERMISSIVE_MATCH4),
          hsl: new RegExp("hsl" + PERMISSIVE_MATCH3),
          hsla: new RegExp("hsla" + PERMISSIVE_MATCH4),
          hsv: new RegExp("hsv" + PERMISSIVE_MATCH3),
          hsva: new RegExp("hsva" + PERMISSIVE_MATCH4),
          hex3: /^#?([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,
          hex6: /^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/,
          hex4: /^#?([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,
          hex8: /^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/
        };
      }();
      function isValidCSSUnit(color) {
        return !!matchers.CSS_UNIT.exec(color);
      }
      function stringInputToObject(color) {
        color = color.replace(trimLeft, "").replace(trimRight, "").toLowerCase();
        var named = false;
        if (names[color]) {
          color = names[color];
          named = true;
        } else if (color == "transparent") {
          return { r: 0, g: 0, b: 0, a: 0, format: "name" };
        }
        var match;
        if (match = matchers.rgb.exec(color)) {
          return { r: match[1], g: match[2], b: match[3] };
        }
        if (match = matchers.rgba.exec(color)) {
          return { r: match[1], g: match[2], b: match[3], a: match[4] };
        }
        if (match = matchers.hsl.exec(color)) {
          return { h: match[1], s: match[2], l: match[3] };
        }
        if (match = matchers.hsla.exec(color)) {
          return { h: match[1], s: match[2], l: match[3], a: match[4] };
        }
        if (match = matchers.hsv.exec(color)) {
          return { h: match[1], s: match[2], v: match[3] };
        }
        if (match = matchers.hsva.exec(color)) {
          return { h: match[1], s: match[2], v: match[3], a: match[4] };
        }
        if (match = matchers.hex8.exec(color)) {
          return {
            r: parseIntFromHex(match[1]),
            g: parseIntFromHex(match[2]),
            b: parseIntFromHex(match[3]),
            a: convertHexToDecimal(match[4]),
            format: named ? "name" : "hex8"
          };
        }
        if (match = matchers.hex6.exec(color)) {
          return {
            r: parseIntFromHex(match[1]),
            g: parseIntFromHex(match[2]),
            b: parseIntFromHex(match[3]),
            format: named ? "name" : "hex"
          };
        }
        if (match = matchers.hex4.exec(color)) {
          return {
            r: parseIntFromHex(match[1] + "" + match[1]),
            g: parseIntFromHex(match[2] + "" + match[2]),
            b: parseIntFromHex(match[3] + "" + match[3]),
            a: convertHexToDecimal(match[4] + "" + match[4]),
            format: named ? "name" : "hex8"
          };
        }
        if (match = matchers.hex3.exec(color)) {
          return {
            r: parseIntFromHex(match[1] + "" + match[1]),
            g: parseIntFromHex(match[2] + "" + match[2]),
            b: parseIntFromHex(match[3] + "" + match[3]),
            format: named ? "name" : "hex"
          };
        }
        return false;
      }
      function validateWCAG2Parms(parms) {
        var level, size;
        parms = parms || { "level": "AA", "size": "small" };
        level = (parms.level || "AA").toUpperCase();
        size = (parms.size || "small").toLowerCase();
        if (level !== "AA" && level !== "AAA") {
          level = "AA";
        }
        if (size !== "small" && size !== "large") {
          size = "small";
        }
        return { "level": level, "size": size };
      }
      if (module2.exports) {
        module2.exports = tinycolor2;
      } else {
        window.tinycolor = tinycolor2;
      }
    })(Math);
  })(tinycolor$1);
  var tinycolor = tinycolor$1.exports;
  var _a;
  const isClient = typeof window !== "undefined";
  isClient && ((_a = window == null ? void 0 : window.navigator) == null ? void 0 : _a.userAgent) && /iP(ad|hone|od)/.test(window.navigator.userAgent);
  function identity$1(arg) {
    return arg;
  }
  function tryOnMounted(fn, sync = true) {
    if (vue.getCurrentInstance())
      vue.onMounted(fn);
    else if (sync)
      fn();
    else
      vue.nextTick(fn);
  }
  isClient ? window : void 0;
  isClient ? window.document : void 0;
  isClient ? window.navigator : void 0;
  isClient ? window.location : void 0;
  function useSupported(callback, sync = false) {
    const isSupported = vue.ref();
    const update2 = () => isSupported.value = Boolean(callback());
    update2();
    tryOnMounted(update2, sync);
    return isSupported;
  }
  const _global = typeof globalThis !== "undefined" ? globalThis : typeof window !== "undefined" ? window : typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : {};
  const globalKey = "__vueuse_ssr_handlers__";
  _global[globalKey] = _global[globalKey] || {};
  _global[globalKey];
  function useEyeDropper(options2 = {}) {
    const { initialValue = "" } = options2;
    const isSupported = useSupported(() => typeof window !== "undefined" && "EyeDropper" in window);
    const sRGBHex = vue.ref(initialValue);
    function open2(openOptions) {
      return __async(this, null, function* () {
        if (!isSupported.value)
          return;
        const eyeDropper = new window.EyeDropper();
        const result = yield eyeDropper.open(openOptions);
        sRGBHex.value = result.sRGBHex;
        return result;
      });
    }
    return { isSupported, sRGBHex, open: open2 };
  }
  var SwipeDirection;
  (function(SwipeDirection2) {
    SwipeDirection2["UP"] = "UP";
    SwipeDirection2["RIGHT"] = "RIGHT";
    SwipeDirection2["DOWN"] = "DOWN";
    SwipeDirection2["LEFT"] = "LEFT";
    SwipeDirection2["NONE"] = "NONE";
  })(SwipeDirection || (SwipeDirection = {}));
  var __defProp2 = Object.defineProperty;
  var __getOwnPropSymbols2 = Object.getOwnPropertySymbols;
  var __hasOwnProp2 = Object.prototype.hasOwnProperty;
  var __propIsEnum2 = Object.prototype.propertyIsEnumerable;
  var __defNormalProp2 = (obj, key, value) => key in obj ? __defProp2(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
  var __spreadValues2 = (a, b) => {
    for (var prop in b || (b = {}))
      if (__hasOwnProp2.call(b, prop))
        __defNormalProp2(a, prop, b[prop]);
    if (__getOwnPropSymbols2)
      for (var prop of __getOwnPropSymbols2(b)) {
        if (__propIsEnum2.call(b, prop))
          __defNormalProp2(a, prop, b[prop]);
      }
    return a;
  };
  const _TransitionPresets = {
    easeInSine: [0.12, 0, 0.39, 0],
    easeOutSine: [0.61, 1, 0.88, 1],
    easeInOutSine: [0.37, 0, 0.63, 1],
    easeInQuad: [0.11, 0, 0.5, 0],
    easeOutQuad: [0.5, 1, 0.89, 1],
    easeInOutQuad: [0.45, 0, 0.55, 1],
    easeInCubic: [0.32, 0, 0.67, 0],
    easeOutCubic: [0.33, 1, 0.68, 1],
    easeInOutCubic: [0.65, 0, 0.35, 1],
    easeInQuart: [0.5, 0, 0.75, 0],
    easeOutQuart: [0.25, 1, 0.5, 1],
    easeInOutQuart: [0.76, 0, 0.24, 1],
    easeInQuint: [0.64, 0, 0.78, 0],
    easeOutQuint: [0.22, 1, 0.36, 1],
    easeInOutQuint: [0.83, 0, 0.17, 1],
    easeInExpo: [0.7, 0, 0.84, 0],
    easeOutExpo: [0.16, 1, 0.3, 1],
    easeInOutExpo: [0.87, 0, 0.13, 1],
    easeInCirc: [0.55, 0, 1, 0.45],
    easeOutCirc: [0, 0.55, 0.45, 1],
    easeInOutCirc: [0.85, 0, 0.15, 1],
    easeInBack: [0.36, 0, 0.66, -0.56],
    easeOutBack: [0.34, 1.56, 0.64, 1],
    easeInOutBack: [0.68, -0.6, 0.32, 1.6]
  };
  __spreadValues2({
    linear: identity$1
  }, _TransitionPresets);
  var rafSchd = function rafSchd2(fn) {
    var lastArgs = [];
    var frameId = null;
    var wrapperFn = function wrapperFn2() {
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }
      lastArgs = args;
      if (frameId) {
        return;
      }
      frameId = requestAnimationFrame(function() {
        frameId = null;
        fn.apply(void 0, lastArgs);
      });
    };
    wrapperFn.cancel = function() {
      if (!frameId) {
        return;
      }
      cancelAnimationFrame(frameId);
      frameId = null;
    };
    return wrapperFn;
  };
  var rafSchd$1 = rafSchd;
  var HueStrip_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$1f = { class: "znpb-colorpicker-inner-editor__hue" };
  const __default__$14 = {
    name: "HueStrip"
  };
  const _sfc_main$1F = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$14), {
    props: {
      modelValue: null
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      var _a3;
      const props = __props;
      const direction = vue.ref("right");
      const oldHue = vue.ref((_a3 = props.modelValue) == null ? void 0 : _a3.h);
      const lastHue = vue.ref(null);
      const root2 = vue.ref(null);
      let ownerWindow;
      const hueStyles = vue.computed(() => {
        const { h } = props.modelValue;
        let positionValue = props.modelValue.h / 360 * 100;
        if (h === 0 && direction.value === "right") {
          positionValue = 100;
        }
        return {
          left: positionValue + "%"
        };
      });
      vue.watch(
        () => props.modelValue,
        () => {
          const { h } = props.modelValue;
          if (h !== 0 && h > oldHue.value) {
            direction.value = "right";
          }
          if (h !== 0 && h < oldHue.value) {
            direction.value = "left";
          }
          oldHue.value = h;
        }
      );
      const rafDragCircle = rafSchd$1(dragHueCircle);
      function actHueCircleDrag() {
        ownerWindow.addEventListener("mousemove", rafDragCircle);
        ownerWindow.addEventListener("mouseup", deactivatedragHueCircle);
      }
      function deactivatedragHueCircle() {
        ownerWindow.removeEventListener("mousemove", rafDragCircle);
        ownerWindow.removeEventListener("mouseup", deactivatedragHueCircle);
        function preventClicks(e) {
          e.stopPropagation();
        }
        ownerWindow.addEventListener("click", preventClicks, true);
        setTimeout(() => {
          ownerWindow.removeEventListener("click", preventClicks, true);
        }, 100);
      }
      function dragHueCircle(event2) {
        if (!event2.which) {
          deactivatedragHueCircle();
          return false;
        }
        let h;
        const mouseLeftPosition = event2.clientX;
        const stripOffset = root2.value.getBoundingClientRect();
        const startX = stripOffset.left;
        const newLeft = mouseLeftPosition - startX;
        if (newLeft > stripOffset.width) {
          h = 360;
        } else if (newLeft < 0) {
          h = 0;
        } else {
          const percent = newLeft * 100 / stripOffset.width;
          h = 360 * percent / 100;
        }
        let newColor = __spreadProps(__spreadValues({}, props.modelValue), {
          h
        });
        if (lastHue.value !== h) {
          emit("update:modelValue", newColor);
        }
        lastHue.value = h;
      }
      vue.onMounted(() => {
        ownerWindow = root2.value.ownerDocument.defaultView;
      });
      vue.onBeforeUnmount(() => {
        deactivatedragHueCircle();
      });
      vue.onUnmounted(() => {
        ownerWindow.removeEventListener("mousemove", dragHueCircle);
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          ref_key: "root",
          ref: root2,
          class: "znpb-colorpicker-inner-editor__hue-wrapper",
          onClick: dragHueCircle
        }, [
          vue.createElementVNode("div", _hoisted_1$1f, [
            vue.createElementVNode("span", {
              style: vue.normalizeStyle(vue.unref(hueStyles)),
              class: "znpb-colorpicker-inner-editor__hue-indicator",
              onMousedown: actHueCircleDrag
            }, null, 36)
          ])
        ], 512);
      };
    }
  }));
  var OpacityStrip_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$1E = /* @__PURE__ */ vue.defineComponent({
    __name: "OpacityStrip",
    props: {
      modelValue: null
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const root2 = vue.ref(null);
      const opacityStrip = vue.ref(null);
      const rafDragCircle = rafSchd$1(dragCircle);
      let lastA;
      let ownerWindow;
      const opacityStyles = vue.computed(() => {
        return {
          left: props.modelValue.a * 100 + "%"
        };
      });
      const barStyles = vue.computed(() => {
        const color = tinycolor(props.modelValue);
        return {
          "background-image": "linear-gradient(to right, rgba(255, 0, 0, 0)," + color.toHexString() + ")"
        };
      });
      function actCircleDrag() {
        ownerWindow.addEventListener("mousemove", rafDragCircle);
        ownerWindow.addEventListener("mouseup", deactivateDragCircle);
      }
      function deactivateDragCircle() {
        ownerWindow.removeEventListener("mousemove", rafDragCircle);
        ownerWindow.removeEventListener("mouseup", deactivateDragCircle);
        function preventClicks(e) {
          e.stopPropagation();
        }
        ownerWindow.addEventListener("click", preventClicks, true);
        setTimeout(() => {
          ownerWindow.removeEventListener("click", preventClicks, true);
        }, 100);
      }
      function dragCircle(event2) {
        if (!event2.which) {
          deactivateDragCircle();
          return false;
        }
        let a;
        const mouseLeftPosition = event2.clientX;
        const stripOffset = opacityStrip.value.getBoundingClientRect();
        const startX = stripOffset.left;
        const newLeft = mouseLeftPosition - startX;
        if (newLeft > stripOffset.width) {
          a = 1;
        } else if (newLeft < 0) {
          a = 0;
        } else {
          a = newLeft / stripOffset.width;
          a = Number(a.toFixed(2));
        }
        const newColor = __spreadProps(__spreadValues({}, props.modelValue), {
          a
        });
        if (lastA !== a) {
          emit("update:modelValue", newColor);
        }
        lastA = a;
      }
      vue.onMounted(() => {
        var _a3;
        ownerWindow = (_a3 = root2.value) == null ? void 0 : _a3.ownerDocument.defaultView;
      });
      vue.onBeforeUnmount(() => {
        deactivateDragCircle();
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          ref_key: "root",
          ref: root2,
          class: "znpb-colorpicker-inner-editor__opacity-wrapper",
          onMousedown: actCircleDrag
        }, [
          vue.createElementVNode("div", {
            class: "znpb-colorpicker-inner-editor__opacity",
            onClick: dragCircle
          }, [
            vue.createElementVNode("div", {
              ref_key: "opacityStrip",
              ref: opacityStrip,
              style: vue.normalizeStyle(vue.unref(barStyles)),
              class: "znpb-colorpicker-inner-editor__opacity-strip"
            }, null, 4),
            vue.createElementVNode("span", {
              style: vue.normalizeStyle(vue.unref(opacityStyles)),
              class: "znpb-colorpicker-inner-editor__opacity-indicator",
              onMousedown: actCircleDrag
            }, null, 36)
          ])
        ], 544);
      };
    }
  });
  var BaseInput_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$1e = {
    key: 0,
    class: "zion-input__prefix"
  };
  const _hoisted_2$P = {
    key: 0,
    class: "zion-input__prepend"
  };
  const _hoisted_3$x = ["type", "value"];
  const _hoisted_4$m = ["value"];
  const _hoisted_5$f = {
    key: 4,
    class: "zion-input__suffix"
  };
  const _hoisted_6$a = {
    key: 1,
    class: "zion-input__append"
  };
  const __default__$13 = {
    name: "BaseInput",
    inheritAttrs: false
  };
  const _sfc_main$1D = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$13), {
    props: {
      modelValue: { default: "" },
      error: { type: Boolean, default: false },
      type: { default: "text" },
      icon: null,
      clearable: { type: Boolean, default: false },
      size: null,
      fontFamily: null,
      class: { default: "" }
    },
    emits: ["update:modelValue"],
    setup(__props, { expose, emit }) {
      const props = __props;
      const input = vue.ref(null);
      const showClear = vue.computed(() => {
        return props.clearable && props.modelValue ? true : false;
      });
      const hasSuffixContent = vue.computed(() => {
        return props.icon || showClear.value;
      });
      const getStyle = vue.computed(() => {
        return {
          fontFamily: props.fontFamily || ""
        };
      });
      const cssClass = vue.computed(() => {
        return props.class;
      });
      function onKeyDown(e) {
        if (e.shiftKey) {
          e.stopPropagation();
        }
      }
      function focus() {
        var _a3;
        (_a3 = input.value) == null ? void 0 : _a3.focus();
      }
      function blur() {
        var _a3;
        (_a3 = input.value) == null ? void 0 : _a3.blur();
      }
      function onInput(e) {
        if (props.type === "number" && e.target.validity.badInput) {
          return;
        }
        emit("update:modelValue", e.target.value);
      }
      expose({
        input,
        focus,
        blur
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          class: vue.normalizeClass(["zion-input", {
            "zion-input--has-prepend": _ctx.$slots.prepend,
            "zion-input--has-append": _ctx.$slots.append,
            "zion-input--has-suffix": vue.unref(hasSuffixContent),
            "zion-input--error": __props.error,
            [`zion-input--size-${__props.size}`]: __props.size,
            [vue.unref(cssClass)]: vue.unref(cssClass)
          }]),
          onKeydown: onKeyDown
        }, [
          _ctx.$slots.prepend ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_1$1e, [
            _ctx.$slots.prepend ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_2$P, [
              vue.renderSlot(_ctx.$slots, "prepend")
            ])) : vue.createCommentVNode("", true)
          ])) : vue.createCommentVNode("", true),
          __props.type !== "textarea" ? (vue.openBlock(), vue.createElementBlock("input", vue.mergeProps({
            key: 1,
            ref_key: "input",
            ref: input,
            type: __props.type,
            value: __props.modelValue,
            style: vue.unref(getStyle)
          }, _ctx.$attrs, { onInput }), null, 16, _hoisted_3$x)) : (vue.openBlock(), vue.createElementBlock("textarea", vue.mergeProps({
            key: 2,
            ref_key: "input",
            ref: input,
            class: "znpb-fancy-scrollbar",
            value: __props.modelValue
          }, _ctx.$attrs, {
            onInput: _cache[0] || (_cache[0] = ($event) => _ctx.$emit("update:modelValue", $event.target.value))
          }), "\n		", 16, _hoisted_4$m)),
          vue.renderSlot(_ctx.$slots, "after-input"),
          vue.unref(showClear) ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
            key: 3,
            class: "zion-input__suffix-icon zion-input__clear-text",
            icon: "close",
            onMousedown: _cache[1] || (_cache[1] = vue.withModifiers(($event) => _ctx.$emit("update:modelValue", ""), ["stop", "prevent"]))
          })) : vue.createCommentVNode("", true),
          _ctx.$slots.suffix || __props.icon || _ctx.$slots.append ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_5$f, [
            vue.renderSlot(_ctx.$slots, "suffix"),
            __props.icon ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
              key: 0,
              class: "zion-input__suffix-icon",
              icon: __props.icon,
              onClick: _cache[2] || (_cache[2] = vue.withModifiers(($event) => _ctx.$emit("update:modelValue", ""), ["stop", "prevent"]))
            }, null, 8, ["icon"])) : vue.createCommentVNode("", true),
            _ctx.$slots.append ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_6$a, [
              vue.renderSlot(_ctx.$slots, "append")
            ])) : vue.createCommentVNode("", true)
          ])) : vue.createCommentVNode("", true)
        ], 34);
      };
    }
  }));
  var InputNumber_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$1d = { class: "znpb-input-number" };
  const __default__$12 = {
    name: "InputNumber",
    inheritAttrs: false
  };
  const _sfc_main$1C = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$12), {
    props: {
      modelValue: null,
      min: null,
      max: null,
      step: { default: 1 },
      shiftStep: { default: 5 },
      suffix: null,
      placeholder: { default: null }
    },
    emits: ["update:modelValue", "linked-value"],
    setup(__props, { emit }) {
      const props = __props;
      let shiftKey = vue.ref(false);
      let initialPosition = 0;
      let lastPosition = 0;
      let dragTreshold = 3;
      let canChangeValue = false;
      const model = vue.computed({
        get() {
          return props.modelValue;
        },
        set(newValue) {
          if (props.min !== void 0 && newValue < props.min) {
            newValue = props.min;
          }
          if (props.max !== void 0 && newValue > props.max) {
            newValue = props.max;
          }
          if (newValue !== props.modelValue) {
            emit("update:modelValue", +newValue);
          }
        }
      });
      function reset() {
        initialPosition = 0;
        lastPosition = 0;
        canChangeValue = false;
      }
      function actNumberDrag(event2) {
        if (event2 instanceof MouseEvent) {
          initialPosition = event2.clientY;
        }
        document.body.style.userSelect = "none";
        window.addEventListener("mousemove", dragNumber);
        window.addEventListener("mouseup", deactivatedragNumber);
        window.addEventListener("keyup", onKeyUp);
      }
      function onKeyDown(event2) {
        if (event2.altKey) {
          emit("linked-value");
        }
        shiftKey.value = event2.shiftKey;
      }
      function onKeyUp(event2) {
        emit("linked-value");
      }
      function deactivatedragNumber() {
        document.body.style.userSelect = "";
        document.body.style.pointerEvents = "";
        window.removeEventListener("mousemove", dragNumber);
        window.removeEventListener("mouseup", deactivatedragNumber);
        window.removeEventListener("keyup", onKeyUp);
        function preventClicks(e) {
          e.stopPropagation();
        }
        window.addEventListener("click", preventClicks, true);
        setTimeout(() => {
          window.removeEventListener("click", preventClicks, true);
        }, 100);
        reset();
      }
      function dragNumber(event2) {
        var _a3, _b;
        const distance = initialPosition - event2.clientY;
        const directionUp = event2.pageY < lastPosition;
        const initialValue = (_b = (_a3 = model.value) != null ? _a3 : props.min) != null ? _b : 0;
        if (Math.abs(distance) > dragTreshold) {
          canChangeValue = true;
        }
        if (canChangeValue && distance % 2 === 0) {
          document.body.style.pointerEvents = "none";
          const increment = event2.shiftKey ? props.shiftStep : props.step;
          model.value = directionUp ? +(initialValue + increment).toFixed(12) : +(initialValue - increment).toFixed(12);
          event2.preventDefault();
        }
        lastPosition = event2.clientY;
      }
      vue.onBeforeUnmount(() => {
        deactivatedragNumber();
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$1d, [
          vue.createVNode(_sfc_main$1D, {
            modelValue: vue.unref(model),
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(model) ? model.value = $event : null),
            type: "number",
            class: "znpb-input-number__input",
            min: __props.min,
            max: __props.max,
            step: vue.unref(shiftKey) ? __props.shiftStep : __props.step,
            placeholder: __props.placeholder,
            onKeydown: onKeyDown,
            onMousedown: actNumberDrag,
            onTouchstartPassive: vue.withModifiers(actNumberDrag, ["prevent"]),
            onMouseup: deactivatedragNumber
          }, {
            suffix: vue.withCtx(() => [
              vue.renderSlot(_ctx.$slots, "default"),
              vue.createTextVNode(" " + vue.toDisplayString(__props.suffix), 1)
            ]),
            _: 3
          }, 8, ["modelValue", "min", "max", "step", "placeholder", "onTouchstartPassive"])
        ]);
      };
    }
  }));
  var InputNumberUnit_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$1c = { class: "znpb-input-number-unit" };
  const _sfc_main$1B = /* @__PURE__ */ vue.defineComponent({
    __name: "InputNumberUnit",
    props: {
      modelValue: { default: "" },
      min: { default: -Infinity },
      max: { default: Infinity },
      step: { default: 1 },
      shift_step: { default: 5 },
      placeholder: { default: "" },
      default_unit: { default: "" }
    },
    emits: ["update:modelValue", "linked-value"],
    setup(__props, { emit }) {
      const props = __props;
      let mouseDownPositionTop = 0;
      let draggingPositionTop = 0;
      let dragThreshold = 3;
      let shiftDrag = false;
      let toTop = false;
      let directionReset = 0;
      let draggingCached = 0;
      let dragging = false;
      const dragNumberThrottle = rafSchd$1(dragNumber);
      const computedValueUnit = vue.computed(() => {
        return getIntegerAndUnit(props.modelValue);
      });
      const computedIntegerValue = vue.computed({
        get() {
          return computedValueUnit.value.value !== null ? computedValueUnit.value.value : 0;
        },
        set(newValue) {
          computedStringValue.value = `${newValue}${computedUnitValue.value}`;
        }
      });
      const computedUnitValue = vue.computed(() => computedValueUnit.value.unit !== null ? computedValueUnit.value.unit : "");
      const computedStringValue = vue.computed({
        get() {
          return props.modelValue ? props.modelValue : "";
        },
        set(newValue) {
          const integerAndUnit = getIntegerAndUnit(newValue);
          const value = integerAndUnit.value !== null ? getValueInRange(integerAndUnit.value) : "";
          const unit = integerAndUnit.unit !== null ? integerAndUnit.unit : "";
          if (value !== "" || unit !== "") {
            emit("update:modelValue", `${value}${unit}`);
          } else {
            emit("update:modelValue", newValue);
          }
        }
      });
      function getValueInRange(value) {
        return Math.max(props.min, Math.min(props.max, value));
      }
      function getIntegerAndUnit(string) {
        const match = typeof string === "string" && string ? string.match(/^([+-]?[0-9]+([.][0-9]*)?|[.][0-9]+)(\D+)?$/) : null;
        const value = match && match[1] ? parseInt(match[1]) : null;
        const unit = match && match[3] ? match[3] : null;
        return {
          value,
          unit
        };
      }
      function actNumberDrag(event2) {
        dragging = true;
        draggingCached = computedIntegerValue.value;
        mouseDownPositionTop = event2.clientY;
        if (!canUpdateNumber()) {
          return;
        }
        document.body.style.userSelect = "none";
        window.addEventListener("mousemove", dragNumberThrottle);
        window.addEventListener("mouseup", deactivateDragNumber);
      }
      function canUpdateNumber() {
        const integerAndUnit = getIntegerAndUnit(computedStringValue.value);
        return computedStringValue.value === "" || integerAndUnit.value !== null;
      }
      function onKeyDown(event2) {
        if (event2.altKey) {
          event2.preventDefault();
          emit("linked-value");
        }
        shiftDrag = event2.shiftKey;
        if (!canUpdateNumber()) {
          return;
        }
        if (event2.key === "ArrowUp" || event2.key === "ArrowDown") {
          toTop = event2.key === "ArrowUp";
          setDraggingValue(true);
          event2.preventDefault();
        }
      }
      function deactivateDragNumber() {
        dragNumberThrottle.cancel();
        dragging = false;
        document.body.style.userSelect = "";
        document.body.style.pointerEvents = "";
        window.removeEventListener("mousemove", dragNumberThrottle);
      }
      function removeEvents() {
        deactivateDragNumber();
        window.removeEventListener("mouseup", deactivateDragNumber);
      }
      function dragNumber(event2) {
        const pageY = event2.pageY;
        shiftDrag = event2.shiftKey;
        draggingPositionTop = event2.clientY;
        if (Math.abs(mouseDownPositionTop - draggingPositionTop) > dragThreshold) {
          if (pageY < directionReset) {
            toTop = true;
          } else {
            toTop = false;
          }
          document.body.style.pointerEvents = "none";
          if (pageY !== directionReset) {
            setDraggingValue(true);
          }
        }
        directionReset = event2.pageY;
      }
      function setDraggingValue(addUnit = false) {
        let newValue;
        if (dragging) {
          const dragged = mouseDownPositionTop - dragThreshold - draggingPositionTop;
          newValue = draggingCached + dragged;
          if (shiftDrag) {
            newValue = newValue % props.shift_step ? Math.ceil(newValue / props.shift_step) * props.shift_step : newValue;
          }
        } else {
          let increment = 1;
          if (shiftDrag) {
            increment = toTop ? props.shift_step : -props.shift_step;
          } else {
            increment = toTop ? props.step : -props.step;
          }
          newValue = computedIntegerValue.value + increment;
          if (shiftDrag) {
            newValue = newValue % props.shift_step ? Math.ceil(newValue / props.shift_step) * props.shift_step : newValue;
            if (toTop && computedIntegerValue.value % props.shift_step !== 0) {
              newValue -= props.shift_step;
            }
          }
        }
        if (addUnit && props.default_unit.length > 0) {
          computedStringValue.value = `${getValueInRange(newValue)}${props.default_unit}`;
        } else {
          computedIntegerValue.value = getValueInRange(newValue);
        }
      }
      vue.onBeforeMount(() => {
        removeEvents();
      });
      vue.onMounted(() => {
        window.removeEventListener("mousemove", dragNumberThrottle);
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$1c, [
          vue.createVNode(_sfc_main$1D, {
            ref: "numberUnitInput",
            modelValue: vue.unref(computedStringValue),
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(computedStringValue) ? computedStringValue.value = $event : null),
            class: "znpb-input-number--has-units",
            size: "narrow",
            placeholder: __props.placeholder,
            onMousedown: vue.withModifiers(actNumberDrag, ["stop"]),
            onTouchstartPassive: vue.withModifiers(actNumberDrag, ["prevent"]),
            onMouseup: deactivateDragNumber,
            onKeydown: onKeyDown
          }, null, 8, ["modelValue", "placeholder", "onMousedown", "onTouchstartPassive"])
        ]);
      };
    }
  });
  var InputLabel_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$1b = {
    key: 0,
    class: "znpb-form-label-content"
  };
  const _hoisted_2$O = { key: 1 };
  const __default__$11 = {
    name: "InputLabel"
  };
  const _sfc_main$1A = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$11), {
    props: {
      label: null,
      align: { default: "center" },
      position: { default: "bottom" },
      icon: null
    },
    setup(__props) {
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("label", {
          class: vue.normalizeClass(["znpb-form-label", {
            [`znpb-form-label--${__props.align}`]: __props.align,
            [`znpb-form-label--position-${__props.position}`]: __props.position
          }])
        }, [
          vue.renderSlot(_ctx.$slots, "default"),
          _ctx.$slots.label || __props.label || __props.icon ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_1$1b, [
            __props.icon ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
              key: 0,
              icon: __props.icon
            }, null, 8, ["icon"])) : vue.createCommentVNode("", true),
            !_ctx.$slots.label ? (vue.openBlock(), vue.createElementBlock("h4", _hoisted_2$O, vue.toDisplayString(__props.label), 1)) : vue.createCommentVNode("", true),
            vue.renderSlot(_ctx.$slots, "label")
          ])) : vue.createCommentVNode("", true)
        ], 2);
      };
    }
  }));
  var RgbaElement_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$1a = { class: "znpb-colorpicker-inner-editor-rgba" };
  const _hoisted_2$N = /* @__PURE__ */ vue.createTextVNode(" R ");
  const _hoisted_3$w = /* @__PURE__ */ vue.createTextVNode(" G ");
  const _hoisted_4$l = /* @__PURE__ */ vue.createTextVNode(" B ");
  const _hoisted_5$e = /* @__PURE__ */ vue.createTextVNode(" A ");
  const _sfc_main$1z = /* @__PURE__ */ vue.defineComponent({
    __name: "RgbaElement",
    props: {
      modelValue: null
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      function updateValue(property2, newValue) {
        emit("update:modelValue", __spreadProps(__spreadValues({}, props.modelValue), {
          [property2]: newValue
        }));
      }
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$1a, [
          vue.createVNode(vue.unref(_sfc_main$1A), null, {
            default: vue.withCtx(() => {
              var _a3;
              return [
                vue.createVNode(vue.unref(_sfc_main$1C), {
                  modelValue: (_a3 = __props.modelValue) == null ? void 0 : _a3.r,
                  min: 0,
                  max: 255,
                  step: 1,
                  "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => updateValue("r", $event))
                }, null, 8, ["modelValue"]),
                _hoisted_2$N
              ];
            }),
            _: 1
          }),
          vue.createVNode(vue.unref(_sfc_main$1A), null, {
            default: vue.withCtx(() => {
              var _a3;
              return [
                vue.createVNode(vue.unref(_sfc_main$1C), {
                  modelValue: (_a3 = __props.modelValue) == null ? void 0 : _a3.g,
                  min: 0,
                  max: 255,
                  step: 1,
                  "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => updateValue("g", $event))
                }, null, 8, ["modelValue"]),
                _hoisted_3$w
              ];
            }),
            _: 1
          }),
          vue.createVNode(vue.unref(_sfc_main$1A), null, {
            default: vue.withCtx(() => {
              var _a3;
              return [
                vue.createVNode(vue.unref(_sfc_main$1C), {
                  modelValue: (_a3 = __props.modelValue) == null ? void 0 : _a3.b,
                  min: 0,
                  max: 255,
                  step: 1,
                  "onUpdate:modelValue": _cache[2] || (_cache[2] = ($event) => updateValue("b", $event))
                }, null, 8, ["modelValue"]),
                _hoisted_4$l
              ];
            }),
            _: 1
          }),
          vue.createVNode(vue.unref(_sfc_main$1A), null, {
            default: vue.withCtx(() => {
              var _a3;
              return [
                vue.createVNode(vue.unref(_sfc_main$1C), {
                  modelValue: (_a3 = __props.modelValue) == null ? void 0 : _a3.a,
                  min: 0,
                  max: 1,
                  step: 0.01,
                  "onUpdate:modelValue": _cache[3] || (_cache[3] = ($event) => updateValue("a", $event))
                }, null, 8, ["modelValue", "step"]),
                _hoisted_5$e
              ];
            }),
            _: 1
          })
        ]);
      };
    }
  });
  var HslaElement_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$19 = { class: "znpb-colorpicker-inner-editor-hsla" };
  const _hoisted_2$M = /* @__PURE__ */ vue.createTextVNode(" H ");
  const _hoisted_3$v = /* @__PURE__ */ vue.createElementVNode("span", { class: "znpb-colorpicker-inner-editor__number-unit" }, "%", -1);
  const _hoisted_4$k = /* @__PURE__ */ vue.createTextVNode(" S ");
  const _hoisted_5$d = /* @__PURE__ */ vue.createElementVNode("span", { class: "znpb-colorpicker-inner-editor__number-unit" }, "%", -1);
  const _hoisted_6$9 = /* @__PURE__ */ vue.createTextVNode(" L ");
  const _hoisted_7$5 = /* @__PURE__ */ vue.createTextVNode(" A ");
  const _sfc_main$1y = /* @__PURE__ */ vue.defineComponent({
    __name: "HslaElement",
    props: {
      modelValue: null
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const hsla = vue.computed(() => {
        const { h, s, l, a } = props.modelValue;
        return {
          h: Number(h.toFixed()),
          s: Number((s * 100).toFixed()),
          l: Number((l * 100).toFixed()),
          a
        };
      });
      function updateHex(property2, newValue) {
        const value = property2 === "s" || property2 === "l" ? newValue / 100 : newValue;
        emit("update:modelValue", __spreadProps(__spreadValues({}, props.modelValue), {
          [property2]: value
        }));
      }
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$19, [
          vue.createVNode(vue.unref(_sfc_main$1A), null, {
            default: vue.withCtx(() => [
              vue.createVNode(vue.unref(_sfc_main$1C), {
                modelValue: vue.unref(hsla).h,
                min: 0,
                max: 360,
                step: 1,
                "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => updateHex("h", $event))
              }, null, 8, ["modelValue"]),
              _hoisted_2$M
            ]),
            _: 1
          }),
          vue.createVNode(vue.unref(_sfc_main$1A), { class: "znpb-colorpicker-inner-editor__number--has-percentage" }, {
            default: vue.withCtx(() => [
              vue.createVNode(vue.unref(_sfc_main$1C), {
                modelValue: vue.unref(hsla).s,
                "onUpdate:modelValue": [
                  _cache[1] || (_cache[1] = ($event) => vue.unref(hsla).s = $event),
                  _cache[2] || (_cache[2] = ($event) => updateHex("s", $event))
                ],
                min: 0,
                max: 100,
                step: 1
              }, {
                default: vue.withCtx(() => [
                  _hoisted_3$v
                ]),
                _: 1
              }, 8, ["modelValue"]),
              _hoisted_4$k
            ]),
            _: 1
          }),
          vue.createVNode(vue.unref(_sfc_main$1A), { class: "znpb-colorpicker-inner-editor__number--has-percentage" }, {
            default: vue.withCtx(() => [
              vue.createVNode(vue.unref(_sfc_main$1C), {
                modelValue: vue.unref(hsla).l,
                "onUpdate:modelValue": [
                  _cache[3] || (_cache[3] = ($event) => vue.unref(hsla).l = $event),
                  _cache[4] || (_cache[4] = ($event) => updateHex("l", $event))
                ],
                min: 0,
                max: 100,
                step: 1
              }, {
                default: vue.withCtx(() => [
                  _hoisted_5$d
                ]),
                _: 1
              }, 8, ["modelValue"]),
              _hoisted_6$9
            ]),
            _: 1
          }),
          vue.createVNode(vue.unref(_sfc_main$1A), null, {
            default: vue.withCtx(() => [
              vue.createVNode(vue.unref(_sfc_main$1C), {
                modelValue: vue.unref(hsla).a,
                "onUpdate:modelValue": [
                  _cache[5] || (_cache[5] = ($event) => vue.unref(hsla).a = $event),
                  _cache[6] || (_cache[6] = ($event) => updateHex("a", $event))
                ],
                min: 0,
                max: 1,
                step: 0.01
              }, null, 8, ["modelValue", "step"]),
              _hoisted_7$5
            ]),
            _: 1
          })
        ]);
      };
    }
  });
  var HexElement_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$18 = { class: "znpb-colorpicker-inner-editor-hex" };
  const _hoisted_2$L = /* @__PURE__ */ vue.createTextVNode(" HEX ");
  const _sfc_main$1x = /* @__PURE__ */ vue.defineComponent({
    __name: "HexElement",
    props: {
      modelValue: null
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const hexValue = vue.computed({
        get() {
          return props.modelValue;
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$18, [
          vue.createVNode(vue.unref(_sfc_main$1A), null, {
            default: vue.withCtx(() => [
              vue.createVNode(_sfc_main$1D, {
                modelValue: vue.unref(hexValue),
                "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(hexValue) ? hexValue.value = $event : null),
                class: "znpb-form-colorpicker__input-text"
              }, null, 8, ["modelValue"]),
              _hoisted_2$L
            ]),
            _: 1
          })
        ]);
      };
    }
  });
  var PanelHex_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$17 = { class: "znpb-colorpicker-inner-editor" };
  const _hoisted_2$K = { class: "znpb-colorpicker-inner-editor__colors" };
  const _hoisted_3$u = { class: "znpb-colorpicker-inner-editor__current-color" };
  const _hoisted_4$j = { class: "znpb-colorpicker-circle znpb-colorpicker-circle--opacity" };
  const _hoisted_5$c = { class: "znpb-colorpicker-inner-editor__stripes" };
  const _hoisted_6$8 = { class: "znpb-colorpicker-inner-editor__rgba" };
  const _hoisted_7$4 = { class: "znpb-color-picker-change-color znpb-input-number-arrow-wrapper" };
  const _sfc_main$1w = /* @__PURE__ */ vue.defineComponent({
    __name: "PanelHex",
    props: {
      modelValue: null
    },
    emits: ["update:modelValue", "update:format"],
    setup(__props, { emit }) {
      const props = __props;
      const { isSupported, open: open2, sRGBHex } = useEyeDropper();
      function openEyeDropper() {
        return __async(this, null, function* () {
          let result;
          try {
            result = yield open2();
          } catch (error) {
          }
          if (result) {
            emit("update:modelValue", result.sRGBHex);
          }
        });
      }
      const hexValue = vue.computed({
        get() {
          return props.modelValue.format === "hex8" ? props.modelValue.hex8 : props.modelValue.hex;
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      const hslaValue = vue.computed({
        get() {
          return props.modelValue.hsla;
        },
        set(hsla) {
          emit("update:modelValue", hsla);
        }
      });
      const rgbaValue = vue.computed({
        get() {
          return props.modelValue.rgba;
        },
        set(rgba) {
          emit("update:modelValue", rgba);
        }
      });
      function changeHex() {
        if (props.modelValue.format === "hex" || props.modelValue.format === "hex8" || props.modelValue.format === "name") {
          emit("update:modelValue", props.modelValue.hsla);
        } else if (props.modelValue.format === "hsl") {
          emit("update:modelValue", props.modelValue.rgba);
        } else if (props.modelValue.format === "rgb") {
          emit("update:modelValue", props.modelValue.hex);
        }
      }
      function changeHexback() {
        if (props.modelValue.format === "hsl") {
          emit("update:modelValue", props.modelValue.hex);
        } else if (props.modelValue.format === "rgb") {
          emit("update:modelValue", props.modelValue.hsla);
        } else if (props.modelValue.format === "hex" || props.modelValue.format === "hex8" || props.modelValue.format === "name") {
          emit("update:modelValue", props.modelValue.rgba);
        }
      }
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$17, [
          vue.createElementVNode("div", _hoisted_2$K, [
            vue.createElementVNode("div", _hoisted_3$u, [
              vue.createElementVNode("span", _hoisted_4$j, [
                vue.createElementVNode("span", {
                  style: vue.normalizeStyle({ backgroundColor: __props.modelValue.hex8 }),
                  class: "znpb-colorpicker-circle znpb-colorpicker-circle-color"
                }, null, 4)
              ])
            ]),
            vue.createElementVNode("div", _hoisted_5$c, [
              vue.createVNode(_sfc_main$1F, {
                modelValue: vue.unref(hslaValue),
                "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(hslaValue) ? hslaValue.value = $event : null)
              }, null, 8, ["modelValue"]),
              vue.createVNode(_sfc_main$1E, {
                modelValue: vue.unref(hslaValue),
                "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => vue.isRef(hslaValue) ? hslaValue.value = $event : null)
              }, null, 8, ["modelValue"])
            ])
          ]),
          vue.createElementVNode("div", _hoisted_6$8, [
            vue.unref(isSupported) ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
              key: 0,
              icon: "eyedropper",
              class: "znpb-eyedropper",
              onClick: openEyeDropper
            })) : vue.createCommentVNode("", true),
            __props.modelValue.format === "rgb" ? (vue.openBlock(), vue.createBlock(_sfc_main$1z, {
              key: 1,
              modelValue: vue.unref(rgbaValue),
              "onUpdate:modelValue": _cache[2] || (_cache[2] = ($event) => vue.isRef(rgbaValue) ? rgbaValue.value = $event : null)
            }, null, 8, ["modelValue"])) : vue.createCommentVNode("", true),
            __props.modelValue.format === "hsl" ? (vue.openBlock(), vue.createBlock(_sfc_main$1y, {
              key: 2,
              modelValue: vue.unref(hslaValue),
              "onUpdate:modelValue": _cache[3] || (_cache[3] = ($event) => vue.isRef(hslaValue) ? hslaValue.value = $event : null)
            }, null, 8, ["modelValue"])) : vue.createCommentVNode("", true),
            __props.modelValue.format === "hex" || __props.modelValue.format === "hex8" || __props.modelValue.format === "name" ? (vue.openBlock(), vue.createBlock(_sfc_main$1x, {
              key: 3,
              modelValue: vue.unref(hexValue),
              "onUpdate:modelValue": _cache[4] || (_cache[4] = ($event) => vue.isRef(hexValue) ? hexValue.value = $event : null)
            }, null, 8, ["modelValue"])) : vue.createCommentVNode("", true),
            vue.createElementVNode("div", _hoisted_7$4, [
              vue.createVNode(vue.unref(_sfc_main$1K), {
                icon: "select",
                rotate: 180,
                class: "znpb-arrow-increment",
                onClick: changeHex
              }),
              vue.createVNode(vue.unref(_sfc_main$1K), {
                icon: "select",
                class: "znpb-arrow-decrement",
                onClick: changeHexback
              })
            ])
          ])
        ]);
      };
    }
  });
  var ColorBoard_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$16 = { class: "znpb-form-colorPicker-saturation__white" };
  const _hoisted_2$J = { class: "znpb-form-colorPicker-saturation__black" };
  const _sfc_main$1v = /* @__PURE__ */ vue.defineComponent({
    __name: "ColorBoard",
    props: {
      colorObject: null
    },
    emits: ["update:color-object"],
    setup(__props, { emit }) {
      const props = __props;
      const isDragging = vue.ref(false);
      const root2 = vue.ref(null);
      const boardContent = vue.ref(null);
      let ownerWindow;
      const computedColorObject = vue.computed({
        get() {
          return props.colorObject;
        },
        set(newValue) {
          emit("update:color-object", newValue);
        }
      });
      const pointStyles = vue.computed(() => {
        const { v, s } = props.colorObject.hsva;
        const cssStyles = {
          top: 100 - v * 100 + "%",
          left: s * 100 + "%"
        };
        return cssStyles;
      });
      const bgColor = vue.computed(() => {
        const { h } = props.colorObject.hsva;
        return `hsl(${h}, 100%, 50%)`;
      });
      const boardRect = vue.computed(() => {
        return boardContent.value.getBoundingClientRect();
      });
      const rafDragCircle = rafSchd$1(dragCircle);
      function initiateDrag(event2) {
        isDragging.value = true;
        let { clientX, clientY } = event2;
        ownerWindow.addEventListener("mousemove", rafDragCircle);
        ownerWindow.addEventListener("mouseup", deactivateDragCircle, true);
        const newTop = clientY - boardRect.value.top;
        const newLeft = clientX - boardRect.value.left;
        let bright = 100 - newTop / boardRect.value.height * 100;
        let saturation = newLeft * 100 / boardRect.value.width;
        let newColor = __spreadProps(__spreadValues({}, props.colorObject.hsva), {
          v: bright / 100,
          s: saturation / 100
        });
        computedColorObject.value = newColor;
      }
      function deactivateDragCircle() {
        ownerWindow.removeEventListener("mousemove", rafDragCircle);
        ownerWindow.removeEventListener("mouseup", deactivateDragCircle, true);
        function preventClicks(e) {
          e.stopPropagation();
        }
        ownerWindow.addEventListener("click", preventClicks, true);
        setTimeout(() => {
          ownerWindow.removeEventListener("click", preventClicks, true);
        }, 100);
      }
      function dragCircle(event2) {
        if (!event2.which) {
          deactivateDragCircle();
          return false;
        }
        let { clientX, clientY } = event2;
        let newLeft = clientX - boardRect.value.left;
        if (newLeft > boardRect.value.width) {
          newLeft = boardRect.value.width;
        } else if (newLeft < 0) {
          newLeft = 0;
        }
        let newTop = clientY - boardRect.value.top;
        if (newTop >= boardRect.value.height) {
          newTop = boardRect.value.height;
        } else if (newTop < 0) {
          newTop = 0;
        }
        const bright = 100 - newTop / boardRect.value.height * 100;
        const saturation = newLeft * 100 / boardRect.value.width;
        let newColor = __spreadProps(__spreadValues({}, props.colorObject.hsva), {
          v: bright / 100,
          s: saturation / 100
        });
        computedColorObject.value = newColor;
      }
      vue.onMounted(() => {
        ownerWindow = root2.value.ownerDocument.defaultView;
        root2.value.ownerDocument.body.classList.add("znpb-color-picker--backdrop");
      });
      vue.onBeforeUnmount(() => {
        root2.value.ownerDocument.body.classList.remove("znpb-color-picker--backdrop");
        deactivateDragCircle();
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          ref_key: "root",
          ref: root2,
          class: "znpb-form-colorPicker-saturation"
        }, [
          vue.createElementVNode("div", {
            ref_key: "boardContent",
            ref: boardContent,
            style: vue.normalizeStyle({ background: vue.unref(bgColor) }),
            class: "znpb-form-colorPicker-saturation__color",
            onMousedown: initiateDrag,
            onMouseup: deactivateDragCircle
          }, [
            vue.createElementVNode("div", _hoisted_1$16, [
              vue.createElementVNode("div", _hoisted_2$J, [
                vue.createElementVNode("div", {
                  style: vue.normalizeStyle(vue.unref(pointStyles)),
                  class: "znpb-color-picker-pointer"
                }, null, 4)
              ])
            ])
          ], 36)
        ], 512);
      };
    }
  });
  var Colorpicker_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$15 = { class: "znpb-form-colorpicker-inner__panel" };
  const __default__$10 = {
    name: "ColorPicker",
    inheritAttrs: false
  };
  const _sfc_main$1u = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$10), {
    props: {
      model: { default: "" },
      showLibrary: { type: Boolean, default: true },
      zIndex: null
    },
    emits: ["color-changed"],
    setup(__props, { emit }) {
      const props = __props;
      const computedModelValue = vue.computed({
        get() {
          return props.model;
        },
        set(newValue) {
          if (newValue) {
            emit("color-changed", newValue);
          } else {
            emit("color-changed", "");
          }
        }
      });
      const computedColorObject = vue.computed({
        get() {
          return getColorObject(props.model);
        },
        set(newValue) {
          const colorObject = tinycolor(newValue);
          const format = colorObject.getFormat();
          let emittedColor;
          if (colorObject.isValid()) {
            if (format === "hsl") {
              emittedColor = colorObject.toHslString();
            } else if (format === "rgb" || format === "hsv") {
              emittedColor = colorObject.toRgbString();
            } else if (format === "hex" || format === "hex8") {
              emittedColor = colorObject.getAlpha() < 1 ? colorObject.toHex8String() : colorObject.toHexString();
            } else if (format === "name") {
              emittedColor = newValue;
            }
          } else {
            emittedColor = newValue;
          }
          computedModelValue.value = emittedColor;
        }
      });
      const pickerStyle = vue.computed(() => {
        if (props.appendTo) {
          return {
            zIndex: props.zIndex
          };
        }
        return {};
      });
      function getColorObject(model) {
        const colorObject = tinycolor(model);
        let hsva = {
          h: 0,
          s: 0,
          v: 0,
          a: 1
        };
        let hsla = {
          h: 0,
          s: 0,
          l: 0,
          a: 1
        };
        let hex8 = "";
        let rgba = "";
        let hex = model ? model : "";
        let format = "hex";
        if (colorObject.isValid()) {
          format = colorObject.getFormat();
          hsva = colorObject.toHsv();
          hsla = colorObject.toHsl();
          hex = format === "name" ? model : colorObject.toHexString();
          hex8 = colorObject.toHex8String();
          rgba = colorObject.toRgb();
        }
        return {
          hex,
          hex8,
          rgba,
          hsla,
          hsva,
          format
        };
      }
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          ref: "colorPicker",
          class: vue.normalizeClass(["znpb-form-colorpicker__color-picker-holder", { ["color-picker-holder--has-library"]: __props.showLibrary }]),
          style: vue.normalizeStyle(vue.unref(pickerStyle))
        }, [
          vue.createVNode(_sfc_main$1v, {
            "color-object": vue.unref(computedColorObject),
            "onUpdate:color-object": _cache[0] || (_cache[0] = ($event) => vue.isRef(computedColorObject) ? computedColorObject.value = $event : null)
          }, null, 8, ["color-object"]),
          vue.createElementVNode("div", _hoisted_1$15, [
            vue.createVNode(_sfc_main$1w, {
              "model-value": vue.unref(computedColorObject),
              "onUpdate:model-value": _cache[1] || (_cache[1] = ($event) => vue.isRef(computedColorObject) ? computedColorObject.value = $event : null)
            }, null, 8, ["model-value"]),
            vue.renderSlot(_ctx.$slots, "end")
          ])
        ], 6);
      };
    }
  }));
  var EmptyList_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$14 = /* @__PURE__ */ vue.createElementVNode("div", { class: "znpb-empty-list__border-top-bottom" }, null, -1);
  const _hoisted_2$I = /* @__PURE__ */ vue.createElementVNode("div", { class: "znpb-empty-list__border-left-right" }, null, -1);
  const _hoisted_3$t = { class: "znpb-empty-list__content" };
  const __default__$$ = {
    name: "EmptyList"
  };
  const _sfc_main$1t = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$$), {
    props: {
      noMargin: { type: Boolean, default: false }
    },
    setup(__props) {
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          class: vue.normalizeClass(["znpb-empty-list__container", { "znpb-empty-list__container--no-margin": __props.noMargin }])
        }, [
          _hoisted_1$14,
          _hoisted_2$I,
          vue.createElementVNode("div", _hoisted_3$t, [
            vue.renderSlot(_ctx.$slots, "default")
          ])
        ], 2);
      };
    }
  }));
  var GradientPreview_vue_vue_type_style_index_0_lang = "";
  const __default__$_ = {
    name: "GradientPreview"
  };
  const _sfc_main$1s = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$_), {
    props: {
      config: null,
      round: { type: Boolean }
    },
    setup(__props) {
      const props = __props;
      const filteredConfig = vue.computed(() => {
        const { applyFilters: applyFilters2 } = window.zb.hooks;
        return applyFilters2("zionbuilder/options/model", JSON.parse(JSON.stringify(props.config)));
      });
      const getGradientPreviewStyle = vue.computed(() => {
        const style = {};
        const gradient = [];
        filteredConfig.value.forEach((element) => {
          const colors = [];
          let position = "90deg";
          const colorsCopy = [...element.colors].sort((a, b) => {
            return a.position > b.position ? 1 : -1;
          });
          colorsCopy.forEach((color) => {
            colors.push(`${color.color} ${color.position}%`);
          });
          if (element.type === "radial") {
            const { x, y } = element.position || { x: 50, y: 50 };
            position = `circle at ${x}% ${y}%`;
          } else {
            position = `${element.angle}deg`;
          }
          gradient.push(`${element.type}-gradient(${position}, ${colors.join(", ")})`);
        });
        gradient.reverse();
        style["background-image"] = gradient.join(", ");
        return style;
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          class: vue.normalizeClass(["znpb-gradient-preview-transparent", { "gradient-type-rounded": __props.round }])
        }, [
          vue.createElementVNode("div", {
            class: vue.normalizeClass(["znpb-gradient-preview", { "gradient-type-rounded": __props.round }]),
            style: vue.normalizeStyle(vue.unref(getGradientPreviewStyle))
          }, null, 6)
        ], 2);
      };
    }
  }));
  var axios$5 = { exports: {} };
  var bind$5 = function bind2(fn, thisArg) {
    return function wrap() {
      var args = new Array(arguments.length);
      for (var i = 0; i < args.length; i++) {
        args[i] = arguments[i];
      }
      return fn.apply(thisArg, args);
    };
  };
  var bind$4 = bind$5;
  var toString$2 = Object.prototype.toString;
  var kindOf$1 = function(cache2) {
    return function(thing) {
      var str = toString$2.call(thing);
      return cache2[str] || (cache2[str] = str.slice(8, -1).toLowerCase());
    };
  }(/* @__PURE__ */ Object.create(null));
  function kindOfTest$1(type) {
    type = type.toLowerCase();
    return function isKindOf(thing) {
      return kindOf$1(thing) === type;
    };
  }
  function isArray$3(val) {
    return Array.isArray(val);
  }
  function isUndefined$1(val) {
    return typeof val === "undefined";
  }
  function isBuffer$3(val) {
    return val !== null && !isUndefined$1(val) && val.constructor !== null && !isUndefined$1(val.constructor) && typeof val.constructor.isBuffer === "function" && val.constructor.isBuffer(val);
  }
  var isArrayBuffer$1 = kindOfTest$1("ArrayBuffer");
  function isArrayBufferView$1(val) {
    var result;
    if (typeof ArrayBuffer !== "undefined" && ArrayBuffer.isView) {
      result = ArrayBuffer.isView(val);
    } else {
      result = val && val.buffer && isArrayBuffer$1(val.buffer);
    }
    return result;
  }
  function isString$1(val) {
    return typeof val === "string";
  }
  function isNumber$1(val) {
    return typeof val === "number";
  }
  function isObject$2(val) {
    return val !== null && typeof val === "object";
  }
  function isPlainObject$2(val) {
    if (kindOf$1(val) !== "object") {
      return false;
    }
    var prototype2 = Object.getPrototypeOf(val);
    return prototype2 === null || prototype2 === Object.prototype;
  }
  var isDate$1 = kindOfTest$1("Date");
  var isFile$1 = kindOfTest$1("File");
  var isBlob$1 = kindOfTest$1("Blob");
  var isFileList$1 = kindOfTest$1("FileList");
  function isFunction$2(val) {
    return toString$2.call(val) === "[object Function]";
  }
  function isStream$1(val) {
    return isObject$2(val) && isFunction$2(val.pipe);
  }
  function isFormData$1(thing) {
    var pattern = "[object FormData]";
    return thing && (typeof FormData === "function" && thing instanceof FormData || toString$2.call(thing) === pattern || isFunction$2(thing.toString) && thing.toString() === pattern);
  }
  var isURLSearchParams$1 = kindOfTest$1("URLSearchParams");
  function trim$1(str) {
    return str.trim ? str.trim() : str.replace(/^\s+|\s+$/g, "");
  }
  function isStandardBrowserEnv$1() {
    if (typeof navigator !== "undefined" && (navigator.product === "ReactNative" || navigator.product === "NativeScript" || navigator.product === "NS")) {
      return false;
    }
    return typeof window !== "undefined" && typeof document !== "undefined";
  }
  function forEach$2(obj, fn) {
    if (obj === null || typeof obj === "undefined") {
      return;
    }
    if (typeof obj !== "object") {
      obj = [obj];
    }
    if (isArray$3(obj)) {
      for (var i = 0, l = obj.length; i < l; i++) {
        fn.call(null, obj[i], i, obj);
      }
    } else {
      for (var key in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, key)) {
          fn.call(null, obj[key], key, obj);
        }
      }
    }
  }
  function merge$3() {
    var result = {};
    function assignValue2(val, key) {
      if (isPlainObject$2(result[key]) && isPlainObject$2(val)) {
        result[key] = merge$3(result[key], val);
      } else if (isPlainObject$2(val)) {
        result[key] = merge$3({}, val);
      } else if (isArray$3(val)) {
        result[key] = val.slice();
      } else {
        result[key] = val;
      }
    }
    for (var i = 0, l = arguments.length; i < l; i++) {
      forEach$2(arguments[i], assignValue2);
    }
    return result;
  }
  function extend$1(a, b, thisArg) {
    forEach$2(b, function assignValue2(val, key) {
      if (thisArg && typeof val === "function") {
        a[key] = bind$4(val, thisArg);
      } else {
        a[key] = val;
      }
    });
    return a;
  }
  function stripBOM$1(content) {
    if (content.charCodeAt(0) === 65279) {
      content = content.slice(1);
    }
    return content;
  }
  function inherits$1(constructor, superConstructor, props, descriptors2) {
    constructor.prototype = Object.create(superConstructor.prototype, descriptors2);
    constructor.prototype.constructor = constructor;
    props && Object.assign(constructor.prototype, props);
  }
  function toFlatObject$1(sourceObj, destObj, filter) {
    var props;
    var i;
    var prop;
    var merged = {};
    destObj = destObj || {};
    do {
      props = Object.getOwnPropertyNames(sourceObj);
      i = props.length;
      while (i-- > 0) {
        prop = props[i];
        if (!merged[prop]) {
          destObj[prop] = sourceObj[prop];
          merged[prop] = true;
        }
      }
      sourceObj = Object.getPrototypeOf(sourceObj);
    } while (sourceObj && (!filter || filter(sourceObj, destObj)) && sourceObj !== Object.prototype);
    return destObj;
  }
  function endsWith$1(str, searchString, position) {
    str = String(str);
    if (position === void 0 || position > str.length) {
      position = str.length;
    }
    position -= searchString.length;
    var lastIndex = str.indexOf(searchString, position);
    return lastIndex !== -1 && lastIndex === position;
  }
  function toArray$1(thing) {
    if (!thing)
      return null;
    var i = thing.length;
    if (isUndefined$1(i))
      return null;
    var arr = new Array(i);
    while (i-- > 0) {
      arr[i] = thing[i];
    }
    return arr;
  }
  var isTypedArray$3 = function(TypedArray) {
    return function(thing) {
      return TypedArray && thing instanceof TypedArray;
    };
  }(typeof Uint8Array !== "undefined" && Object.getPrototypeOf(Uint8Array));
  var utils$z = {
    isArray: isArray$3,
    isArrayBuffer: isArrayBuffer$1,
    isBuffer: isBuffer$3,
    isFormData: isFormData$1,
    isArrayBufferView: isArrayBufferView$1,
    isString: isString$1,
    isNumber: isNumber$1,
    isObject: isObject$2,
    isPlainObject: isPlainObject$2,
    isUndefined: isUndefined$1,
    isDate: isDate$1,
    isFile: isFile$1,
    isBlob: isBlob$1,
    isFunction: isFunction$2,
    isStream: isStream$1,
    isURLSearchParams: isURLSearchParams$1,
    isStandardBrowserEnv: isStandardBrowserEnv$1,
    forEach: forEach$2,
    merge: merge$3,
    extend: extend$1,
    trim: trim$1,
    stripBOM: stripBOM$1,
    inherits: inherits$1,
    toFlatObject: toFlatObject$1,
    kindOf: kindOf$1,
    kindOfTest: kindOfTest$1,
    endsWith: endsWith$1,
    toArray: toArray$1,
    isTypedArray: isTypedArray$3,
    isFileList: isFileList$1
  };
  var utils$y = utils$z;
  function encode$1(val) {
    return encodeURIComponent(val).replace(/%3A/gi, ":").replace(/%24/g, "$").replace(/%2C/gi, ",").replace(/%20/g, "+").replace(/%5B/gi, "[").replace(/%5D/gi, "]");
  }
  var buildURL$5 = function buildURL2(url, params, paramsSerializer) {
    if (!params) {
      return url;
    }
    var serializedParams;
    if (paramsSerializer) {
      serializedParams = paramsSerializer(params);
    } else if (utils$y.isURLSearchParams(params)) {
      serializedParams = params.toString();
    } else {
      var parts = [];
      utils$y.forEach(params, function serialize(val, key) {
        if (val === null || typeof val === "undefined") {
          return;
        }
        if (utils$y.isArray(val)) {
          key = key + "[]";
        } else {
          val = [val];
        }
        utils$y.forEach(val, function parseValue(v) {
          if (utils$y.isDate(v)) {
            v = v.toISOString();
          } else if (utils$y.isObject(v)) {
            v = JSON.stringify(v);
          }
          parts.push(encode$1(key) + "=" + encode$1(v));
        });
      });
      serializedParams = parts.join("&");
    }
    if (serializedParams) {
      var hashmarkIndex = url.indexOf("#");
      if (hashmarkIndex !== -1) {
        url = url.slice(0, hashmarkIndex);
      }
      url += (url.indexOf("?") === -1 ? "?" : "&") + serializedParams;
    }
    return url;
  };
  var utils$x = utils$z;
  function InterceptorManager$3() {
    this.handlers = [];
  }
  InterceptorManager$3.prototype.use = function use(fulfilled, rejected, options2) {
    this.handlers.push({
      fulfilled,
      rejected,
      synchronous: options2 ? options2.synchronous : false,
      runWhen: options2 ? options2.runWhen : null
    });
    return this.handlers.length - 1;
  };
  InterceptorManager$3.prototype.eject = function eject(id) {
    if (this.handlers[id]) {
      this.handlers[id] = null;
    }
  };
  InterceptorManager$3.prototype.forEach = function forEach2(fn) {
    utils$x.forEach(this.handlers, function forEachHandler(h) {
      if (h !== null) {
        fn(h);
      }
    });
  };
  var InterceptorManager_1$1 = InterceptorManager$3;
  var utils$w = utils$z;
  var normalizeHeaderName$3 = function normalizeHeaderName2(headers, normalizedName) {
    utils$w.forEach(headers, function processHeader(value, name) {
      if (name !== normalizedName && name.toUpperCase() === normalizedName.toUpperCase()) {
        headers[normalizedName] = value;
        delete headers[name];
      }
    });
  };
  var utils$v = utils$z;
  function AxiosError$b(message, code, config, request, response) {
    Error.call(this);
    this.message = message;
    this.name = "AxiosError";
    code && (this.code = code);
    config && (this.config = config);
    request && (this.request = request);
    response && (this.response = response);
  }
  utils$v.inherits(AxiosError$b, Error, {
    toJSON: function toJSON() {
      return {
        message: this.message,
        name: this.name,
        description: this.description,
        number: this.number,
        fileName: this.fileName,
        lineNumber: this.lineNumber,
        columnNumber: this.columnNumber,
        stack: this.stack,
        config: this.config,
        code: this.code,
        status: this.response && this.response.status ? this.response.status : null
      };
    }
  });
  var prototype$1 = AxiosError$b.prototype;
  var descriptors$1 = {};
  [
    "ERR_BAD_OPTION_VALUE",
    "ERR_BAD_OPTION",
    "ECONNABORTED",
    "ETIMEDOUT",
    "ERR_NETWORK",
    "ERR_FR_TOO_MANY_REDIRECTS",
    "ERR_DEPRECATED",
    "ERR_BAD_RESPONSE",
    "ERR_BAD_REQUEST",
    "ERR_CANCELED"
  ].forEach(function(code) {
    descriptors$1[code] = { value: code };
  });
  Object.defineProperties(AxiosError$b, descriptors$1);
  Object.defineProperty(prototype$1, "isAxiosError", { value: true });
  AxiosError$b.from = function(error, code, config, request, response, customProps) {
    var axiosError = Object.create(prototype$1);
    utils$v.toFlatObject(error, axiosError, function filter(obj) {
      return obj !== Error.prototype;
    });
    AxiosError$b.call(axiosError, error.message, code, config, request, response);
    axiosError.name = error.name;
    customProps && Object.assign(axiosError, customProps);
    return axiosError;
  };
  var AxiosError_1$1 = AxiosError$b;
  var transitional$1 = {
    silentJSONParsing: true,
    forcedJSONParsing: true,
    clarifyTimeoutError: false
  };
  var utils$u = utils$z;
  function toFormData$3(obj, formData) {
    formData = formData || new FormData();
    var stack = [];
    function convertValue(value) {
      if (value === null)
        return "";
      if (utils$u.isDate(value)) {
        return value.toISOString();
      }
      if (utils$u.isArrayBuffer(value) || utils$u.isTypedArray(value)) {
        return typeof Blob === "function" ? new Blob([value]) : Buffer.from(value);
      }
      return value;
    }
    function build(data2, parentKey) {
      if (utils$u.isPlainObject(data2) || utils$u.isArray(data2)) {
        if (stack.indexOf(data2) !== -1) {
          throw Error("Circular reference detected in " + parentKey);
        }
        stack.push(data2);
        utils$u.forEach(data2, function each(value, key) {
          if (utils$u.isUndefined(value))
            return;
          var fullKey = parentKey ? parentKey + "." + key : key;
          var arr;
          if (value && !parentKey && typeof value === "object") {
            if (utils$u.endsWith(key, "{}")) {
              value = JSON.stringify(value);
            } else if (utils$u.endsWith(key, "[]") && (arr = utils$u.toArray(value))) {
              arr.forEach(function(el) {
                !utils$u.isUndefined(el) && formData.append(fullKey, convertValue(el));
              });
              return;
            }
          }
          build(value, fullKey);
        });
        stack.pop();
      } else {
        formData.append(parentKey, convertValue(data2));
      }
    }
    build(obj);
    return formData;
  }
  var toFormData_1$1 = toFormData$3;
  var AxiosError$a = AxiosError_1$1;
  var settle$3 = function settle2(resolve, reject, response) {
    var validateStatus = response.config.validateStatus;
    if (!response.status || !validateStatus || validateStatus(response.status)) {
      resolve(response);
    } else {
      reject(new AxiosError$a(
        "Request failed with status code " + response.status,
        [AxiosError$a.ERR_BAD_REQUEST, AxiosError$a.ERR_BAD_RESPONSE][Math.floor(response.status / 100) - 4],
        response.config,
        response.request,
        response
      ));
    }
  };
  var utils$t = utils$z;
  var cookies$3 = utils$t.isStandardBrowserEnv() ? function standardBrowserEnv() {
    return {
      write: function write2(name, value, expires, path, domain, secure) {
        var cookie = [];
        cookie.push(name + "=" + encodeURIComponent(value));
        if (utils$t.isNumber(expires)) {
          cookie.push("expires=" + new Date(expires).toGMTString());
        }
        if (utils$t.isString(path)) {
          cookie.push("path=" + path);
        }
        if (utils$t.isString(domain)) {
          cookie.push("domain=" + domain);
        }
        if (secure === true) {
          cookie.push("secure");
        }
        document.cookie = cookie.join("; ");
      },
      read: function read2(name) {
        var match = document.cookie.match(new RegExp("(^|;\\s*)(" + name + ")=([^;]*)"));
        return match ? decodeURIComponent(match[3]) : null;
      },
      remove: function remove(name) {
        this.write(name, "", Date.now() - 864e5);
      }
    };
  }() : function nonStandardBrowserEnv() {
    return {
      write: function write2() {
      },
      read: function read2() {
        return null;
      },
      remove: function remove() {
      }
    };
  }();
  var isAbsoluteURL$3 = function isAbsoluteURL2(url) {
    return /^([a-z][a-z\d+\-.]*:)?\/\//i.test(url);
  };
  var combineURLs$3 = function combineURLs2(baseURL, relativeURL) {
    return relativeURL ? baseURL.replace(/\/+$/, "") + "/" + relativeURL.replace(/^\/+/, "") : baseURL;
  };
  var isAbsoluteURL$2 = isAbsoluteURL$3;
  var combineURLs$2 = combineURLs$3;
  var buildFullPath$5 = function buildFullPath2(baseURL, requestedURL) {
    if (baseURL && !isAbsoluteURL$2(requestedURL)) {
      return combineURLs$2(baseURL, requestedURL);
    }
    return requestedURL;
  };
  var utils$s = utils$z;
  var ignoreDuplicateOf$1 = [
    "age",
    "authorization",
    "content-length",
    "content-type",
    "etag",
    "expires",
    "from",
    "host",
    "if-modified-since",
    "if-unmodified-since",
    "last-modified",
    "location",
    "max-forwards",
    "proxy-authorization",
    "referer",
    "retry-after",
    "user-agent"
  ];
  var parseHeaders$3 = function parseHeaders2(headers) {
    var parsed = {};
    var key;
    var val;
    var i;
    if (!headers) {
      return parsed;
    }
    utils$s.forEach(headers.split("\n"), function parser(line) {
      i = line.indexOf(":");
      key = utils$s.trim(line.substr(0, i)).toLowerCase();
      val = utils$s.trim(line.substr(i + 1));
      if (key) {
        if (parsed[key] && ignoreDuplicateOf$1.indexOf(key) >= 0) {
          return;
        }
        if (key === "set-cookie") {
          parsed[key] = (parsed[key] ? parsed[key] : []).concat([val]);
        } else {
          parsed[key] = parsed[key] ? parsed[key] + ", " + val : val;
        }
      }
    });
    return parsed;
  };
  var utils$r = utils$z;
  var isURLSameOrigin$3 = utils$r.isStandardBrowserEnv() ? function standardBrowserEnv() {
    var msie = /(msie|trident)/i.test(navigator.userAgent);
    var urlParsingNode = document.createElement("a");
    var originURL;
    function resolveURL(url) {
      var href = url;
      if (msie) {
        urlParsingNode.setAttribute("href", href);
        href = urlParsingNode.href;
      }
      urlParsingNode.setAttribute("href", href);
      return {
        href: urlParsingNode.href,
        protocol: urlParsingNode.protocol ? urlParsingNode.protocol.replace(/:$/, "") : "",
        host: urlParsingNode.host,
        search: urlParsingNode.search ? urlParsingNode.search.replace(/^\?/, "") : "",
        hash: urlParsingNode.hash ? urlParsingNode.hash.replace(/^#/, "") : "",
        hostname: urlParsingNode.hostname,
        port: urlParsingNode.port,
        pathname: urlParsingNode.pathname.charAt(0) === "/" ? urlParsingNode.pathname : "/" + urlParsingNode.pathname
      };
    }
    originURL = resolveURL(window.location.href);
    return function isURLSameOrigin2(requestURL) {
      var parsed = utils$r.isString(requestURL) ? resolveURL(requestURL) : requestURL;
      return parsed.protocol === originURL.protocol && parsed.host === originURL.host;
    };
  }() : function nonStandardBrowserEnv() {
    return function isURLSameOrigin2() {
      return true;
    };
  }();
  var AxiosError$9 = AxiosError_1$1;
  var utils$q = utils$z;
  function CanceledError$7(message) {
    AxiosError$9.call(this, message == null ? "canceled" : message, AxiosError$9.ERR_CANCELED);
    this.name = "CanceledError";
  }
  utils$q.inherits(CanceledError$7, AxiosError$9, {
    __CANCEL__: true
  });
  var CanceledError_1$1 = CanceledError$7;
  var parseProtocol$3 = function parseProtocol2(url) {
    var match = /^([-+\w]{1,25})(:?\/\/|:)/.exec(url);
    return match && match[1] || "";
  };
  var utils$p = utils$z;
  var settle$2 = settle$3;
  var cookies$2 = cookies$3;
  var buildURL$4 = buildURL$5;
  var buildFullPath$4 = buildFullPath$5;
  var parseHeaders$2 = parseHeaders$3;
  var isURLSameOrigin$2 = isURLSameOrigin$3;
  var transitionalDefaults$3 = transitional$1;
  var AxiosError$8 = AxiosError_1$1;
  var CanceledError$6 = CanceledError_1$1;
  var parseProtocol$2 = parseProtocol$3;
  var xhr$1 = function xhrAdapter(config) {
    return new Promise(function dispatchXhrRequest(resolve, reject) {
      var requestData = config.data;
      var requestHeaders = config.headers;
      var responseType = config.responseType;
      var onCanceled;
      function done() {
        if (config.cancelToken) {
          config.cancelToken.unsubscribe(onCanceled);
        }
        if (config.signal) {
          config.signal.removeEventListener("abort", onCanceled);
        }
      }
      if (utils$p.isFormData(requestData) && utils$p.isStandardBrowserEnv()) {
        delete requestHeaders["Content-Type"];
      }
      var request = new XMLHttpRequest();
      if (config.auth) {
        var username = config.auth.username || "";
        var password = config.auth.password ? unescape(encodeURIComponent(config.auth.password)) : "";
        requestHeaders.Authorization = "Basic " + btoa(username + ":" + password);
      }
      var fullPath = buildFullPath$4(config.baseURL, config.url);
      request.open(config.method.toUpperCase(), buildURL$4(fullPath, config.params, config.paramsSerializer), true);
      request.timeout = config.timeout;
      function onloadend() {
        if (!request) {
          return;
        }
        var responseHeaders = "getAllResponseHeaders" in request ? parseHeaders$2(request.getAllResponseHeaders()) : null;
        var responseData = !responseType || responseType === "text" || responseType === "json" ? request.responseText : request.response;
        var response = {
          data: responseData,
          status: request.status,
          statusText: request.statusText,
          headers: responseHeaders,
          config,
          request
        };
        settle$2(function _resolve(value) {
          resolve(value);
          done();
        }, function _reject(err) {
          reject(err);
          done();
        }, response);
        request = null;
      }
      if ("onloadend" in request) {
        request.onloadend = onloadend;
      } else {
        request.onreadystatechange = function handleLoad() {
          if (!request || request.readyState !== 4) {
            return;
          }
          if (request.status === 0 && !(request.responseURL && request.responseURL.indexOf("file:") === 0)) {
            return;
          }
          setTimeout(onloadend);
        };
      }
      request.onabort = function handleAbort() {
        if (!request) {
          return;
        }
        reject(new AxiosError$8("Request aborted", AxiosError$8.ECONNABORTED, config, request));
        request = null;
      };
      request.onerror = function handleError() {
        reject(new AxiosError$8("Network Error", AxiosError$8.ERR_NETWORK, config, request, request));
        request = null;
      };
      request.ontimeout = function handleTimeout() {
        var timeoutErrorMessage = config.timeout ? "timeout of " + config.timeout + "ms exceeded" : "timeout exceeded";
        var transitional2 = config.transitional || transitionalDefaults$3;
        if (config.timeoutErrorMessage) {
          timeoutErrorMessage = config.timeoutErrorMessage;
        }
        reject(new AxiosError$8(
          timeoutErrorMessage,
          transitional2.clarifyTimeoutError ? AxiosError$8.ETIMEDOUT : AxiosError$8.ECONNABORTED,
          config,
          request
        ));
        request = null;
      };
      if (utils$p.isStandardBrowserEnv()) {
        var xsrfValue = (config.withCredentials || isURLSameOrigin$2(fullPath)) && config.xsrfCookieName ? cookies$2.read(config.xsrfCookieName) : void 0;
        if (xsrfValue) {
          requestHeaders[config.xsrfHeaderName] = xsrfValue;
        }
      }
      if ("setRequestHeader" in request) {
        utils$p.forEach(requestHeaders, function setRequestHeader(val, key) {
          if (typeof requestData === "undefined" && key.toLowerCase() === "content-type") {
            delete requestHeaders[key];
          } else {
            request.setRequestHeader(key, val);
          }
        });
      }
      if (!utils$p.isUndefined(config.withCredentials)) {
        request.withCredentials = !!config.withCredentials;
      }
      if (responseType && responseType !== "json") {
        request.responseType = config.responseType;
      }
      if (typeof config.onDownloadProgress === "function") {
        request.addEventListener("progress", config.onDownloadProgress);
      }
      if (typeof config.onUploadProgress === "function" && request.upload) {
        request.upload.addEventListener("progress", config.onUploadProgress);
      }
      if (config.cancelToken || config.signal) {
        onCanceled = function(cancel) {
          if (!request) {
            return;
          }
          reject(!cancel || cancel && cancel.type ? new CanceledError$6() : cancel);
          request.abort();
          request = null;
        };
        config.cancelToken && config.cancelToken.subscribe(onCanceled);
        if (config.signal) {
          config.signal.aborted ? onCanceled() : config.signal.addEventListener("abort", onCanceled);
        }
      }
      if (!requestData) {
        requestData = null;
      }
      var protocol = parseProtocol$2(fullPath);
      if (protocol && ["http", "https", "file"].indexOf(protocol) === -1) {
        reject(new AxiosError$8("Unsupported protocol " + protocol + ":", AxiosError$8.ERR_BAD_REQUEST, config));
        return;
      }
      request.send(requestData);
    });
  };
  var _null$1 = null;
  var utils$o = utils$z;
  var normalizeHeaderName$2 = normalizeHeaderName$3;
  var AxiosError$7 = AxiosError_1$1;
  var transitionalDefaults$2 = transitional$1;
  var toFormData$2 = toFormData_1$1;
  var DEFAULT_CONTENT_TYPE$1 = {
    "Content-Type": "application/x-www-form-urlencoded"
  };
  function setContentTypeIfUnset$1(headers, value) {
    if (!utils$o.isUndefined(headers) && utils$o.isUndefined(headers["Content-Type"])) {
      headers["Content-Type"] = value;
    }
  }
  function getDefaultAdapter$1() {
    var adapter;
    if (typeof XMLHttpRequest !== "undefined") {
      adapter = xhr$1;
    } else if (typeof process !== "undefined" && Object.prototype.toString.call(process) === "[object process]") {
      adapter = xhr$1;
    }
    return adapter;
  }
  function stringifySafely$1(rawValue, parser, encoder) {
    if (utils$o.isString(rawValue)) {
      try {
        (parser || JSON.parse)(rawValue);
        return utils$o.trim(rawValue);
      } catch (e) {
        if (e.name !== "SyntaxError") {
          throw e;
        }
      }
    }
    return (encoder || JSON.stringify)(rawValue);
  }
  var defaults$7 = {
    transitional: transitionalDefaults$2,
    adapter: getDefaultAdapter$1(),
    transformRequest: [function transformRequest(data2, headers) {
      normalizeHeaderName$2(headers, "Accept");
      normalizeHeaderName$2(headers, "Content-Type");
      if (utils$o.isFormData(data2) || utils$o.isArrayBuffer(data2) || utils$o.isBuffer(data2) || utils$o.isStream(data2) || utils$o.isFile(data2) || utils$o.isBlob(data2)) {
        return data2;
      }
      if (utils$o.isArrayBufferView(data2)) {
        return data2.buffer;
      }
      if (utils$o.isURLSearchParams(data2)) {
        setContentTypeIfUnset$1(headers, "application/x-www-form-urlencoded;charset=utf-8");
        return data2.toString();
      }
      var isObjectPayload = utils$o.isObject(data2);
      var contentType = headers && headers["Content-Type"];
      var isFileList2;
      if ((isFileList2 = utils$o.isFileList(data2)) || isObjectPayload && contentType === "multipart/form-data") {
        var _FormData = this.env && this.env.FormData;
        return toFormData$2(isFileList2 ? { "files[]": data2 } : data2, _FormData && new _FormData());
      } else if (isObjectPayload || contentType === "application/json") {
        setContentTypeIfUnset$1(headers, "application/json");
        return stringifySafely$1(data2);
      }
      return data2;
    }],
    transformResponse: [function transformResponse(data2) {
      var transitional2 = this.transitional || defaults$7.transitional;
      var silentJSONParsing = transitional2 && transitional2.silentJSONParsing;
      var forcedJSONParsing = transitional2 && transitional2.forcedJSONParsing;
      var strictJSONParsing = !silentJSONParsing && this.responseType === "json";
      if (strictJSONParsing || forcedJSONParsing && utils$o.isString(data2) && data2.length) {
        try {
          return JSON.parse(data2);
        } catch (e) {
          if (strictJSONParsing) {
            if (e.name === "SyntaxError") {
              throw AxiosError$7.from(e, AxiosError$7.ERR_BAD_RESPONSE, this, null, this.response);
            }
            throw e;
          }
        }
      }
      return data2;
    }],
    timeout: 0,
    xsrfCookieName: "XSRF-TOKEN",
    xsrfHeaderName: "X-XSRF-TOKEN",
    maxContentLength: -1,
    maxBodyLength: -1,
    env: {
      FormData: _null$1
    },
    validateStatus: function validateStatus(status) {
      return status >= 200 && status < 300;
    },
    headers: {
      common: {
        "Accept": "application/json, text/plain, */*"
      }
    }
  };
  utils$o.forEach(["delete", "get", "head"], function forEachMethodNoData(method) {
    defaults$7.headers[method] = {};
  });
  utils$o.forEach(["post", "put", "patch"], function forEachMethodWithData(method) {
    defaults$7.headers[method] = utils$o.merge(DEFAULT_CONTENT_TYPE$1);
  });
  var defaults_1$1 = defaults$7;
  var utils$n = utils$z;
  var defaults$6 = defaults_1$1;
  var transformData$3 = function transformData2(data2, headers, fns) {
    var context = this || defaults$6;
    utils$n.forEach(fns, function transform(fn) {
      data2 = fn.call(context, data2, headers);
    });
    return data2;
  };
  var isCancel$3 = function isCancel2(value) {
    return !!(value && value.__CANCEL__);
  };
  var utils$m = utils$z;
  var transformData$2 = transformData$3;
  var isCancel$2 = isCancel$3;
  var defaults$5 = defaults_1$1;
  var CanceledError$5 = CanceledError_1$1;
  function throwIfCancellationRequested$1(config) {
    if (config.cancelToken) {
      config.cancelToken.throwIfRequested();
    }
    if (config.signal && config.signal.aborted) {
      throw new CanceledError$5();
    }
  }
  var dispatchRequest$3 = function dispatchRequest2(config) {
    throwIfCancellationRequested$1(config);
    config.headers = config.headers || {};
    config.data = transformData$2.call(
      config,
      config.data,
      config.headers,
      config.transformRequest
    );
    config.headers = utils$m.merge(
      config.headers.common || {},
      config.headers[config.method] || {},
      config.headers
    );
    utils$m.forEach(
      ["delete", "get", "head", "post", "put", "patch", "common"],
      function cleanHeaderConfig(method) {
        delete config.headers[method];
      }
    );
    var adapter = config.adapter || defaults$5.adapter;
    return adapter(config).then(function onAdapterResolution(response) {
      throwIfCancellationRequested$1(config);
      response.data = transformData$2.call(
        config,
        response.data,
        response.headers,
        config.transformResponse
      );
      return response;
    }, function onAdapterRejection(reason) {
      if (!isCancel$2(reason)) {
        throwIfCancellationRequested$1(config);
        if (reason && reason.response) {
          reason.response.data = transformData$2.call(
            config,
            reason.response.data,
            reason.response.headers,
            config.transformResponse
          );
        }
      }
      return Promise.reject(reason);
    });
  };
  var utils$l = utils$z;
  var mergeConfig$5 = function mergeConfig2(config1, config2) {
    config2 = config2 || {};
    var config = {};
    function getMergedValue(target, source) {
      if (utils$l.isPlainObject(target) && utils$l.isPlainObject(source)) {
        return utils$l.merge(target, source);
      } else if (utils$l.isPlainObject(source)) {
        return utils$l.merge({}, source);
      } else if (utils$l.isArray(source)) {
        return source.slice();
      }
      return source;
    }
    function mergeDeepProperties(prop) {
      if (!utils$l.isUndefined(config2[prop])) {
        return getMergedValue(config1[prop], config2[prop]);
      } else if (!utils$l.isUndefined(config1[prop])) {
        return getMergedValue(void 0, config1[prop]);
      }
    }
    function valueFromConfig2(prop) {
      if (!utils$l.isUndefined(config2[prop])) {
        return getMergedValue(void 0, config2[prop]);
      }
    }
    function defaultToConfig2(prop) {
      if (!utils$l.isUndefined(config2[prop])) {
        return getMergedValue(void 0, config2[prop]);
      } else if (!utils$l.isUndefined(config1[prop])) {
        return getMergedValue(void 0, config1[prop]);
      }
    }
    function mergeDirectKeys(prop) {
      if (prop in config2) {
        return getMergedValue(config1[prop], config2[prop]);
      } else if (prop in config1) {
        return getMergedValue(void 0, config1[prop]);
      }
    }
    var mergeMap = {
      "url": valueFromConfig2,
      "method": valueFromConfig2,
      "data": valueFromConfig2,
      "baseURL": defaultToConfig2,
      "transformRequest": defaultToConfig2,
      "transformResponse": defaultToConfig2,
      "paramsSerializer": defaultToConfig2,
      "timeout": defaultToConfig2,
      "timeoutMessage": defaultToConfig2,
      "withCredentials": defaultToConfig2,
      "adapter": defaultToConfig2,
      "responseType": defaultToConfig2,
      "xsrfCookieName": defaultToConfig2,
      "xsrfHeaderName": defaultToConfig2,
      "onUploadProgress": defaultToConfig2,
      "onDownloadProgress": defaultToConfig2,
      "decompress": defaultToConfig2,
      "maxContentLength": defaultToConfig2,
      "maxBodyLength": defaultToConfig2,
      "beforeRedirect": defaultToConfig2,
      "transport": defaultToConfig2,
      "httpAgent": defaultToConfig2,
      "httpsAgent": defaultToConfig2,
      "cancelToken": defaultToConfig2,
      "socketPath": defaultToConfig2,
      "responseEncoding": defaultToConfig2,
      "validateStatus": mergeDirectKeys
    };
    utils$l.forEach(Object.keys(config1).concat(Object.keys(config2)), function computeConfigValue(prop) {
      var merge2 = mergeMap[prop] || mergeDeepProperties;
      var configValue = merge2(prop);
      utils$l.isUndefined(configValue) && merge2 !== mergeDirectKeys || (config[prop] = configValue);
    });
    return config;
  };
  var data$1 = {
    "version": "0.27.2"
  };
  var VERSION$1 = data$1.version;
  var AxiosError$6 = AxiosError_1$1;
  var validators$3 = {};
  ["object", "boolean", "number", "function", "string", "symbol"].forEach(function(type, i) {
    validators$3[type] = function validator2(thing) {
      return typeof thing === type || "a" + (i < 1 ? "n " : " ") + type;
    };
  });
  var deprecatedWarnings$1 = {};
  validators$3.transitional = function transitional2(validator2, version, message) {
    function formatMessage(opt, desc) {
      return "[Axios v" + VERSION$1 + "] Transitional option '" + opt + "'" + desc + (message ? ". " + message : "");
    }
    return function(value, opt, opts) {
      if (validator2 === false) {
        throw new AxiosError$6(
          formatMessage(opt, " has been removed" + (version ? " in " + version : "")),
          AxiosError$6.ERR_DEPRECATED
        );
      }
      if (version && !deprecatedWarnings$1[opt]) {
        deprecatedWarnings$1[opt] = true;
        console.warn(
          formatMessage(
            opt,
            " has been deprecated since v" + version + " and will be removed in the near future"
          )
        );
      }
      return validator2 ? validator2(value, opt, opts) : true;
    };
  };
  function assertOptions$1(options2, schema, allowUnknown) {
    if (typeof options2 !== "object") {
      throw new AxiosError$6("options must be an object", AxiosError$6.ERR_BAD_OPTION_VALUE);
    }
    var keys2 = Object.keys(options2);
    var i = keys2.length;
    while (i-- > 0) {
      var opt = keys2[i];
      var validator2 = schema[opt];
      if (validator2) {
        var value = options2[opt];
        var result = value === void 0 || validator2(value, opt, options2);
        if (result !== true) {
          throw new AxiosError$6("option " + opt + " must be " + result, AxiosError$6.ERR_BAD_OPTION_VALUE);
        }
        continue;
      }
      if (allowUnknown !== true) {
        throw new AxiosError$6("Unknown option " + opt, AxiosError$6.ERR_BAD_OPTION);
      }
    }
  }
  var validator$3 = {
    assertOptions: assertOptions$1,
    validators: validators$3
  };
  var utils$k = utils$z;
  var buildURL$3 = buildURL$5;
  var InterceptorManager$2 = InterceptorManager_1$1;
  var dispatchRequest$2 = dispatchRequest$3;
  var mergeConfig$4 = mergeConfig$5;
  var buildFullPath$3 = buildFullPath$5;
  var validator$2 = validator$3;
  var validators$2 = validator$2.validators;
  function Axios$3(instanceConfig) {
    this.defaults = instanceConfig;
    this.interceptors = {
      request: new InterceptorManager$2(),
      response: new InterceptorManager$2()
    };
  }
  Axios$3.prototype.request = function request(configOrUrl, config) {
    if (typeof configOrUrl === "string") {
      config = config || {};
      config.url = configOrUrl;
    } else {
      config = configOrUrl || {};
    }
    config = mergeConfig$4(this.defaults, config);
    if (config.method) {
      config.method = config.method.toLowerCase();
    } else if (this.defaults.method) {
      config.method = this.defaults.method.toLowerCase();
    } else {
      config.method = "get";
    }
    var transitional2 = config.transitional;
    if (transitional2 !== void 0) {
      validator$2.assertOptions(transitional2, {
        silentJSONParsing: validators$2.transitional(validators$2.boolean),
        forcedJSONParsing: validators$2.transitional(validators$2.boolean),
        clarifyTimeoutError: validators$2.transitional(validators$2.boolean)
      }, false);
    }
    var requestInterceptorChain = [];
    var synchronousRequestInterceptors = true;
    this.interceptors.request.forEach(function unshiftRequestInterceptors(interceptor) {
      if (typeof interceptor.runWhen === "function" && interceptor.runWhen(config) === false) {
        return;
      }
      synchronousRequestInterceptors = synchronousRequestInterceptors && interceptor.synchronous;
      requestInterceptorChain.unshift(interceptor.fulfilled, interceptor.rejected);
    });
    var responseInterceptorChain = [];
    this.interceptors.response.forEach(function pushResponseInterceptors(interceptor) {
      responseInterceptorChain.push(interceptor.fulfilled, interceptor.rejected);
    });
    var promise;
    if (!synchronousRequestInterceptors) {
      var chain = [dispatchRequest$2, void 0];
      Array.prototype.unshift.apply(chain, requestInterceptorChain);
      chain = chain.concat(responseInterceptorChain);
      promise = Promise.resolve(config);
      while (chain.length) {
        promise = promise.then(chain.shift(), chain.shift());
      }
      return promise;
    }
    var newConfig = config;
    while (requestInterceptorChain.length) {
      var onFulfilled = requestInterceptorChain.shift();
      var onRejected = requestInterceptorChain.shift();
      try {
        newConfig = onFulfilled(newConfig);
      } catch (error) {
        onRejected(error);
        break;
      }
    }
    try {
      promise = dispatchRequest$2(newConfig);
    } catch (error) {
      return Promise.reject(error);
    }
    while (responseInterceptorChain.length) {
      promise = promise.then(responseInterceptorChain.shift(), responseInterceptorChain.shift());
    }
    return promise;
  };
  Axios$3.prototype.getUri = function getUri(config) {
    config = mergeConfig$4(this.defaults, config);
    var fullPath = buildFullPath$3(config.baseURL, config.url);
    return buildURL$3(fullPath, config.params, config.paramsSerializer);
  };
  utils$k.forEach(["delete", "get", "head", "options"], function forEachMethodNoData(method) {
    Axios$3.prototype[method] = function(url, config) {
      return this.request(mergeConfig$4(config || {}, {
        method,
        url,
        data: (config || {}).data
      }));
    };
  });
  utils$k.forEach(["post", "put", "patch"], function forEachMethodWithData(method) {
    function generateHTTPMethod(isForm) {
      return function httpMethod(url, data2, config) {
        return this.request(mergeConfig$4(config || {}, {
          method,
          headers: isForm ? {
            "Content-Type": "multipart/form-data"
          } : {},
          url,
          data: data2
        }));
      };
    }
    Axios$3.prototype[method] = generateHTTPMethod();
    Axios$3.prototype[method + "Form"] = generateHTTPMethod(true);
  });
  var Axios_1$1 = Axios$3;
  var CanceledError$4 = CanceledError_1$1;
  function CancelToken$1(executor) {
    if (typeof executor !== "function") {
      throw new TypeError("executor must be a function.");
    }
    var resolvePromise;
    this.promise = new Promise(function promiseExecutor(resolve) {
      resolvePromise = resolve;
    });
    var token = this;
    this.promise.then(function(cancel) {
      if (!token._listeners)
        return;
      var i;
      var l = token._listeners.length;
      for (i = 0; i < l; i++) {
        token._listeners[i](cancel);
      }
      token._listeners = null;
    });
    this.promise.then = function(onfulfilled) {
      var _resolve;
      var promise = new Promise(function(resolve) {
        token.subscribe(resolve);
        _resolve = resolve;
      }).then(onfulfilled);
      promise.cancel = function reject() {
        token.unsubscribe(_resolve);
      };
      return promise;
    };
    executor(function cancel(message) {
      if (token.reason) {
        return;
      }
      token.reason = new CanceledError$4(message);
      resolvePromise(token.reason);
    });
  }
  CancelToken$1.prototype.throwIfRequested = function throwIfRequested() {
    if (this.reason) {
      throw this.reason;
    }
  };
  CancelToken$1.prototype.subscribe = function subscribe(listener) {
    if (this.reason) {
      listener(this.reason);
      return;
    }
    if (this._listeners) {
      this._listeners.push(listener);
    } else {
      this._listeners = [listener];
    }
  };
  CancelToken$1.prototype.unsubscribe = function unsubscribe(listener) {
    if (!this._listeners) {
      return;
    }
    var index2 = this._listeners.indexOf(listener);
    if (index2 !== -1) {
      this._listeners.splice(index2, 1);
    }
  };
  CancelToken$1.source = function source() {
    var cancel;
    var token = new CancelToken$1(function executor(c) {
      cancel = c;
    });
    return {
      token,
      cancel
    };
  };
  var CancelToken_1$1 = CancelToken$1;
  var spread$1 = function spread2(callback) {
    return function wrap(arr) {
      return callback.apply(null, arr);
    };
  };
  var utils$j = utils$z;
  var isAxiosError$1 = function isAxiosError2(payload) {
    return utils$j.isObject(payload) && payload.isAxiosError === true;
  };
  var utils$i = utils$z;
  var bind$3 = bind$5;
  var Axios$2 = Axios_1$1;
  var mergeConfig$3 = mergeConfig$5;
  var defaults$4 = defaults_1$1;
  function createInstance$1(defaultConfig) {
    var context = new Axios$2(defaultConfig);
    var instance2 = bind$3(Axios$2.prototype.request, context);
    utils$i.extend(instance2, Axios$2.prototype, context);
    utils$i.extend(instance2, context);
    instance2.create = function create(instanceConfig) {
      return createInstance$1(mergeConfig$3(defaultConfig, instanceConfig));
    };
    return instance2;
  }
  var axios$4 = createInstance$1(defaults$4);
  axios$4.Axios = Axios$2;
  axios$4.CanceledError = CanceledError_1$1;
  axios$4.CancelToken = CancelToken_1$1;
  axios$4.isCancel = isCancel$3;
  axios$4.VERSION = data$1.version;
  axios$4.toFormData = toFormData_1$1;
  axios$4.AxiosError = AxiosError_1$1;
  axios$4.Cancel = axios$4.CanceledError;
  axios$4.all = function all(promises) {
    return Promise.all(promises);
  };
  axios$4.spread = spread$1;
  axios$4.isAxiosError = isAxiosError$1;
  axios$5.exports = axios$4;
  axios$5.exports.default = axios$4;
  var axios$3 = axios$5.exports;
  function createWPService() {
    return axios$3.create({
      baseURL: `${window.ZnRestConfig.rest_root}wp/v2`,
      headers: {
        "X-WP-Nonce": window.ZnRestConfig.nonce,
        Accept: "application/json",
        "Content-Type": "application/json"
      }
    });
  }
  function getService$1() {
    return axios$3.create({
      baseURL: `${window.ZnRestConfig.rest_root}zionbuilder/v1/`,
      headers: {
        "X-WP-Nonce": window.ZnRestConfig.nonce,
        Accept: "application/json",
        "Content-Type": "application/json"
      }
    });
  }
  function getFontsDataSet() {
    return getService$1().get("data-sets");
  }
  function getGoogleFonts() {
    return getService$1().get("google-fonts");
  }
  function saveOptions(options2) {
    return getService$1().post("options", options2);
  }
  function getSavedOptions() {
    return getService$1().get("options");
  }
  function regenerateCache(itemData) {
    return getService$1().post("assets/regenerate", itemData);
  }
  function getCacheList() {
    return getService$1().get("assets");
  }
  function finishRegeneration() {
    return getService$1().get("assets/finish");
  }
  function getUsersById(ids) {
    return createWPService().get(`users`, {
      params: {
        include: ids
      }
    });
  }
  function uploadFile(data2) {
    return getService$1().post("upload", data2, {
      headers: {
        "Content-Type": "multipart/form-data"
      }
    });
  }
  function saveBreakpoints(breakpoints) {
    return getService$1().post("breakpoints", breakpoints);
  }
  var md5 = { exports: {} };
  var core = { exports: {} };
  (function(module2, exports2) {
    (function(root2, factory) {
      {
        module2.exports = factory();
      }
    })(commonjsGlobal, function() {
      var CryptoJS = CryptoJS || function(Math2, undefined$1) {
        var crypto;
        if (typeof window !== "undefined" && window.crypto) {
          crypto = window.crypto;
        }
        if (typeof self !== "undefined" && self.crypto) {
          crypto = self.crypto;
        }
        if (typeof globalThis !== "undefined" && globalThis.crypto) {
          crypto = globalThis.crypto;
        }
        if (!crypto && typeof window !== "undefined" && window.msCrypto) {
          crypto = window.msCrypto;
        }
        if (!crypto && typeof commonjsGlobal !== "undefined" && commonjsGlobal.crypto) {
          crypto = commonjsGlobal.crypto;
        }
        if (!crypto && typeof commonjsRequire === "function") {
          try {
            crypto = require("crypto");
          } catch (err) {
          }
        }
        var cryptoSecureRandomInt = function() {
          if (crypto) {
            if (typeof crypto.getRandomValues === "function") {
              try {
                return crypto.getRandomValues(new Uint32Array(1))[0];
              } catch (err) {
              }
            }
            if (typeof crypto.randomBytes === "function") {
              try {
                return crypto.randomBytes(4).readInt32LE();
              } catch (err) {
              }
            }
          }
          throw new Error("Native crypto module could not be used to get secure random number.");
        };
        var create = Object.create || function() {
          function F() {
          }
          return function(obj) {
            var subtype;
            F.prototype = obj;
            subtype = new F();
            F.prototype = null;
            return subtype;
          };
        }();
        var C = {};
        var C_lib = C.lib = {};
        var Base = C_lib.Base = function() {
          return {
            extend: function(overrides) {
              var subtype = create(this);
              if (overrides) {
                subtype.mixIn(overrides);
              }
              if (!subtype.hasOwnProperty("init") || this.init === subtype.init) {
                subtype.init = function() {
                  subtype.$super.init.apply(this, arguments);
                };
              }
              subtype.init.prototype = subtype;
              subtype.$super = this;
              return subtype;
            },
            create: function() {
              var instance2 = this.extend();
              instance2.init.apply(instance2, arguments);
              return instance2;
            },
            init: function() {
            },
            mixIn: function(properties) {
              for (var propertyName in properties) {
                if (properties.hasOwnProperty(propertyName)) {
                  this[propertyName] = properties[propertyName];
                }
              }
              if (properties.hasOwnProperty("toString")) {
                this.toString = properties.toString;
              }
            },
            clone: function() {
              return this.init.prototype.extend(this);
            }
          };
        }();
        var WordArray = C_lib.WordArray = Base.extend({
          init: function(words2, sigBytes) {
            words2 = this.words = words2 || [];
            if (sigBytes != undefined$1) {
              this.sigBytes = sigBytes;
            } else {
              this.sigBytes = words2.length * 4;
            }
          },
          toString: function(encoder) {
            return (encoder || Hex).stringify(this);
          },
          concat: function(wordArray) {
            var thisWords = this.words;
            var thatWords = wordArray.words;
            var thisSigBytes = this.sigBytes;
            var thatSigBytes = wordArray.sigBytes;
            this.clamp();
            if (thisSigBytes % 4) {
              for (var i = 0; i < thatSigBytes; i++) {
                var thatByte = thatWords[i >>> 2] >>> 24 - i % 4 * 8 & 255;
                thisWords[thisSigBytes + i >>> 2] |= thatByte << 24 - (thisSigBytes + i) % 4 * 8;
              }
            } else {
              for (var j = 0; j < thatSigBytes; j += 4) {
                thisWords[thisSigBytes + j >>> 2] = thatWords[j >>> 2];
              }
            }
            this.sigBytes += thatSigBytes;
            return this;
          },
          clamp: function() {
            var words2 = this.words;
            var sigBytes = this.sigBytes;
            words2[sigBytes >>> 2] &= 4294967295 << 32 - sigBytes % 4 * 8;
            words2.length = Math2.ceil(sigBytes / 4);
          },
          clone: function() {
            var clone = Base.clone.call(this);
            clone.words = this.words.slice(0);
            return clone;
          },
          random: function(nBytes) {
            var words2 = [];
            for (var i = 0; i < nBytes; i += 4) {
              words2.push(cryptoSecureRandomInt());
            }
            return new WordArray.init(words2, nBytes);
          }
        });
        var C_enc = C.enc = {};
        var Hex = C_enc.Hex = {
          stringify: function(wordArray) {
            var words2 = wordArray.words;
            var sigBytes = wordArray.sigBytes;
            var hexChars = [];
            for (var i = 0; i < sigBytes; i++) {
              var bite = words2[i >>> 2] >>> 24 - i % 4 * 8 & 255;
              hexChars.push((bite >>> 4).toString(16));
              hexChars.push((bite & 15).toString(16));
            }
            return hexChars.join("");
          },
          parse: function(hexStr) {
            var hexStrLength = hexStr.length;
            var words2 = [];
            for (var i = 0; i < hexStrLength; i += 2) {
              words2[i >>> 3] |= parseInt(hexStr.substr(i, 2), 16) << 24 - i % 8 * 4;
            }
            return new WordArray.init(words2, hexStrLength / 2);
          }
        };
        var Latin1 = C_enc.Latin1 = {
          stringify: function(wordArray) {
            var words2 = wordArray.words;
            var sigBytes = wordArray.sigBytes;
            var latin1Chars = [];
            for (var i = 0; i < sigBytes; i++) {
              var bite = words2[i >>> 2] >>> 24 - i % 4 * 8 & 255;
              latin1Chars.push(String.fromCharCode(bite));
            }
            return latin1Chars.join("");
          },
          parse: function(latin1Str) {
            var latin1StrLength = latin1Str.length;
            var words2 = [];
            for (var i = 0; i < latin1StrLength; i++) {
              words2[i >>> 2] |= (latin1Str.charCodeAt(i) & 255) << 24 - i % 4 * 8;
            }
            return new WordArray.init(words2, latin1StrLength);
          }
        };
        var Utf8 = C_enc.Utf8 = {
          stringify: function(wordArray) {
            try {
              return decodeURIComponent(escape(Latin1.stringify(wordArray)));
            } catch (e) {
              throw new Error("Malformed UTF-8 data");
            }
          },
          parse: function(utf8Str) {
            return Latin1.parse(unescape(encodeURIComponent(utf8Str)));
          }
        };
        var BufferedBlockAlgorithm = C_lib.BufferedBlockAlgorithm = Base.extend({
          reset: function() {
            this._data = new WordArray.init();
            this._nDataBytes = 0;
          },
          _append: function(data2) {
            if (typeof data2 == "string") {
              data2 = Utf8.parse(data2);
            }
            this._data.concat(data2);
            this._nDataBytes += data2.sigBytes;
          },
          _process: function(doFlush) {
            var processedWords;
            var data2 = this._data;
            var dataWords = data2.words;
            var dataSigBytes = data2.sigBytes;
            var blockSize = this.blockSize;
            var blockSizeBytes = blockSize * 4;
            var nBlocksReady = dataSigBytes / blockSizeBytes;
            if (doFlush) {
              nBlocksReady = Math2.ceil(nBlocksReady);
            } else {
              nBlocksReady = Math2.max((nBlocksReady | 0) - this._minBufferSize, 0);
            }
            var nWordsReady = nBlocksReady * blockSize;
            var nBytesReady = Math2.min(nWordsReady * 4, dataSigBytes);
            if (nWordsReady) {
              for (var offset2 = 0; offset2 < nWordsReady; offset2 += blockSize) {
                this._doProcessBlock(dataWords, offset2);
              }
              processedWords = dataWords.splice(0, nWordsReady);
              data2.sigBytes -= nBytesReady;
            }
            return new WordArray.init(processedWords, nBytesReady);
          },
          clone: function() {
            var clone = Base.clone.call(this);
            clone._data = this._data.clone();
            return clone;
          },
          _minBufferSize: 0
        });
        C_lib.Hasher = BufferedBlockAlgorithm.extend({
          cfg: Base.extend(),
          init: function(cfg) {
            this.cfg = this.cfg.extend(cfg);
            this.reset();
          },
          reset: function() {
            BufferedBlockAlgorithm.reset.call(this);
            this._doReset();
          },
          update: function(messageUpdate) {
            this._append(messageUpdate);
            this._process();
            return this;
          },
          finalize: function(messageUpdate) {
            if (messageUpdate) {
              this._append(messageUpdate);
            }
            var hash2 = this._doFinalize();
            return hash2;
          },
          blockSize: 512 / 32,
          _createHelper: function(hasher) {
            return function(message, cfg) {
              return new hasher.init(cfg).finalize(message);
            };
          },
          _createHmacHelper: function(hasher) {
            return function(message, key) {
              return new C_algo.HMAC.init(hasher, key).finalize(message);
            };
          }
        });
        var C_algo = C.algo = {};
        return C;
      }(Math);
      return CryptoJS;
    });
  })(core);
  (function(module2, exports2) {
    (function(root2, factory) {
      {
        module2.exports = factory(core.exports);
      }
    })(commonjsGlobal, function(CryptoJS) {
      (function(Math2) {
        var C = CryptoJS;
        var C_lib = C.lib;
        var WordArray = C_lib.WordArray;
        var Hasher = C_lib.Hasher;
        var C_algo = C.algo;
        var T = [];
        (function() {
          for (var i = 0; i < 64; i++) {
            T[i] = Math2.abs(Math2.sin(i + 1)) * 4294967296 | 0;
          }
        })();
        var MD52 = C_algo.MD5 = Hasher.extend({
          _doReset: function() {
            this._hash = new WordArray.init([
              1732584193,
              4023233417,
              2562383102,
              271733878
            ]);
          },
          _doProcessBlock: function(M, offset2) {
            for (var i = 0; i < 16; i++) {
              var offset_i = offset2 + i;
              var M_offset_i = M[offset_i];
              M[offset_i] = (M_offset_i << 8 | M_offset_i >>> 24) & 16711935 | (M_offset_i << 24 | M_offset_i >>> 8) & 4278255360;
            }
            var H = this._hash.words;
            var M_offset_0 = M[offset2 + 0];
            var M_offset_1 = M[offset2 + 1];
            var M_offset_2 = M[offset2 + 2];
            var M_offset_3 = M[offset2 + 3];
            var M_offset_4 = M[offset2 + 4];
            var M_offset_5 = M[offset2 + 5];
            var M_offset_6 = M[offset2 + 6];
            var M_offset_7 = M[offset2 + 7];
            var M_offset_8 = M[offset2 + 8];
            var M_offset_9 = M[offset2 + 9];
            var M_offset_10 = M[offset2 + 10];
            var M_offset_11 = M[offset2 + 11];
            var M_offset_12 = M[offset2 + 12];
            var M_offset_13 = M[offset2 + 13];
            var M_offset_14 = M[offset2 + 14];
            var M_offset_15 = M[offset2 + 15];
            var a = H[0];
            var b = H[1];
            var c = H[2];
            var d = H[3];
            a = FF(a, b, c, d, M_offset_0, 7, T[0]);
            d = FF(d, a, b, c, M_offset_1, 12, T[1]);
            c = FF(c, d, a, b, M_offset_2, 17, T[2]);
            b = FF(b, c, d, a, M_offset_3, 22, T[3]);
            a = FF(a, b, c, d, M_offset_4, 7, T[4]);
            d = FF(d, a, b, c, M_offset_5, 12, T[5]);
            c = FF(c, d, a, b, M_offset_6, 17, T[6]);
            b = FF(b, c, d, a, M_offset_7, 22, T[7]);
            a = FF(a, b, c, d, M_offset_8, 7, T[8]);
            d = FF(d, a, b, c, M_offset_9, 12, T[9]);
            c = FF(c, d, a, b, M_offset_10, 17, T[10]);
            b = FF(b, c, d, a, M_offset_11, 22, T[11]);
            a = FF(a, b, c, d, M_offset_12, 7, T[12]);
            d = FF(d, a, b, c, M_offset_13, 12, T[13]);
            c = FF(c, d, a, b, M_offset_14, 17, T[14]);
            b = FF(b, c, d, a, M_offset_15, 22, T[15]);
            a = GG(a, b, c, d, M_offset_1, 5, T[16]);
            d = GG(d, a, b, c, M_offset_6, 9, T[17]);
            c = GG(c, d, a, b, M_offset_11, 14, T[18]);
            b = GG(b, c, d, a, M_offset_0, 20, T[19]);
            a = GG(a, b, c, d, M_offset_5, 5, T[20]);
            d = GG(d, a, b, c, M_offset_10, 9, T[21]);
            c = GG(c, d, a, b, M_offset_15, 14, T[22]);
            b = GG(b, c, d, a, M_offset_4, 20, T[23]);
            a = GG(a, b, c, d, M_offset_9, 5, T[24]);
            d = GG(d, a, b, c, M_offset_14, 9, T[25]);
            c = GG(c, d, a, b, M_offset_3, 14, T[26]);
            b = GG(b, c, d, a, M_offset_8, 20, T[27]);
            a = GG(a, b, c, d, M_offset_13, 5, T[28]);
            d = GG(d, a, b, c, M_offset_2, 9, T[29]);
            c = GG(c, d, a, b, M_offset_7, 14, T[30]);
            b = GG(b, c, d, a, M_offset_12, 20, T[31]);
            a = HH(a, b, c, d, M_offset_5, 4, T[32]);
            d = HH(d, a, b, c, M_offset_8, 11, T[33]);
            c = HH(c, d, a, b, M_offset_11, 16, T[34]);
            b = HH(b, c, d, a, M_offset_14, 23, T[35]);
            a = HH(a, b, c, d, M_offset_1, 4, T[36]);
            d = HH(d, a, b, c, M_offset_4, 11, T[37]);
            c = HH(c, d, a, b, M_offset_7, 16, T[38]);
            b = HH(b, c, d, a, M_offset_10, 23, T[39]);
            a = HH(a, b, c, d, M_offset_13, 4, T[40]);
            d = HH(d, a, b, c, M_offset_0, 11, T[41]);
            c = HH(c, d, a, b, M_offset_3, 16, T[42]);
            b = HH(b, c, d, a, M_offset_6, 23, T[43]);
            a = HH(a, b, c, d, M_offset_9, 4, T[44]);
            d = HH(d, a, b, c, M_offset_12, 11, T[45]);
            c = HH(c, d, a, b, M_offset_15, 16, T[46]);
            b = HH(b, c, d, a, M_offset_2, 23, T[47]);
            a = II(a, b, c, d, M_offset_0, 6, T[48]);
            d = II(d, a, b, c, M_offset_7, 10, T[49]);
            c = II(c, d, a, b, M_offset_14, 15, T[50]);
            b = II(b, c, d, a, M_offset_5, 21, T[51]);
            a = II(a, b, c, d, M_offset_12, 6, T[52]);
            d = II(d, a, b, c, M_offset_3, 10, T[53]);
            c = II(c, d, a, b, M_offset_10, 15, T[54]);
            b = II(b, c, d, a, M_offset_1, 21, T[55]);
            a = II(a, b, c, d, M_offset_8, 6, T[56]);
            d = II(d, a, b, c, M_offset_15, 10, T[57]);
            c = II(c, d, a, b, M_offset_6, 15, T[58]);
            b = II(b, c, d, a, M_offset_13, 21, T[59]);
            a = II(a, b, c, d, M_offset_4, 6, T[60]);
            d = II(d, a, b, c, M_offset_11, 10, T[61]);
            c = II(c, d, a, b, M_offset_2, 15, T[62]);
            b = II(b, c, d, a, M_offset_9, 21, T[63]);
            H[0] = H[0] + a | 0;
            H[1] = H[1] + b | 0;
            H[2] = H[2] + c | 0;
            H[3] = H[3] + d | 0;
          },
          _doFinalize: function() {
            var data2 = this._data;
            var dataWords = data2.words;
            var nBitsTotal = this._nDataBytes * 8;
            var nBitsLeft = data2.sigBytes * 8;
            dataWords[nBitsLeft >>> 5] |= 128 << 24 - nBitsLeft % 32;
            var nBitsTotalH = Math2.floor(nBitsTotal / 4294967296);
            var nBitsTotalL = nBitsTotal;
            dataWords[(nBitsLeft + 64 >>> 9 << 4) + 15] = (nBitsTotalH << 8 | nBitsTotalH >>> 24) & 16711935 | (nBitsTotalH << 24 | nBitsTotalH >>> 8) & 4278255360;
            dataWords[(nBitsLeft + 64 >>> 9 << 4) + 14] = (nBitsTotalL << 8 | nBitsTotalL >>> 24) & 16711935 | (nBitsTotalL << 24 | nBitsTotalL >>> 8) & 4278255360;
            data2.sigBytes = (dataWords.length + 1) * 4;
            this._process();
            var hash2 = this._hash;
            var H = hash2.words;
            for (var i = 0; i < 4; i++) {
              var H_i = H[i];
              H[i] = (H_i << 8 | H_i >>> 24) & 16711935 | (H_i << 24 | H_i >>> 8) & 4278255360;
            }
            return hash2;
          },
          clone: function() {
            var clone = Hasher.clone.call(this);
            clone._hash = this._hash.clone();
            return clone;
          }
        });
        function FF(a, b, c, d, x, s, t) {
          var n = a + (b & c | ~b & d) + x + t;
          return (n << s | n >>> 32 - s) + b;
        }
        function GG(a, b, c, d, x, s, t) {
          var n = a + (b & d | c & ~d) + x + t;
          return (n << s | n >>> 32 - s) + b;
        }
        function HH(a, b, c, d, x, s, t) {
          var n = a + (b ^ c ^ d) + x + t;
          return (n << s | n >>> 32 - s) + b;
        }
        function II(a, b, c, d, x, s, t) {
          var n = a + (c ^ (b | ~d)) + x + t;
          return (n << s | n >>> 32 - s) + b;
        }
        C.MD5 = Hasher._createHelper(MD52);
        C.HmacMD5 = Hasher._createHmacHelper(MD52);
      })(Math);
      return CryptoJS.MD5;
    });
  })(md5);
  var MD5 = md5.exports;
  function hash$3(object) {
    return MD5(JSON.stringify(object));
  }
  const generateUID = function(index2, lastDateInSeconds) {
    const startDate = new Date("2019");
    return function() {
      const d = new Date();
      const n = d - startDate;
      if (lastDateInSeconds === false) {
        lastDateInSeconds = n;
      }
      if (lastDateInSeconds !== n) {
        index2 = 0;
      }
      lastDateInSeconds = n;
      index2 += 1;
      return "uid" + n + index2;
    };
  }(0, false);
  ({
    isMac: window.navigator.userAgent.indexOf("Macintosh") >= 0
  });
  const getDefaultGradient = () => {
    return [
      {
        type: "linear",
        angle: 114,
        colors: [
          {
            color: "#18208d",
            position: 0
          },
          {
            color: "#06bee1",
            position: 100
          }
        ],
        position: {
          x: 75,
          y: 48
        }
      }
    ];
  };
  var GradientRadialDragger_vue_vue_type_style_index_0_scoped_true_lang = "";
  var _export_sfc = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
      target[key] = val;
    }
    return target;
  };
  const __default__$Z = {
    name: "GradientRadialDragger"
  };
  const _sfc_main$1r = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$Z), {
    props: {
      position: null,
      active: { type: Boolean }
    },
    setup(__props) {
      const props = __props;
      const radialPosition = vue.computed(() => {
        const { x, y } = props.position || { x: 50, y: 50 };
        const cssStyles = {
          left: x + "%",
          top: y + "%"
        };
        return cssStyles;
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("span", {
          class: vue.normalizeClass(["znpb-color-picker-pointer", { "znpb-color-picker-pointer--active": __props.active }]),
          style: vue.normalizeStyle(vue.unref(radialPosition))
        }, null, 6);
      };
    }
  }));
  var GradientRadialDragger = /* @__PURE__ */ _export_sfc(_sfc_main$1r, [["__scopeId", "data-v-72da235e"]]);
  var freeGlobal = typeof global == "object" && global && global.Object === Object && global;
  var freeGlobal$1 = freeGlobal;
  var freeSelf = typeof self == "object" && self && self.Object === Object && self;
  var root = freeGlobal$1 || freeSelf || Function("return this")();
  var root$1 = root;
  var Symbol$1 = root$1.Symbol;
  var Symbol$2 = Symbol$1;
  var objectProto$f = Object.prototype;
  var hasOwnProperty$c = objectProto$f.hasOwnProperty;
  var nativeObjectToString$1 = objectProto$f.toString;
  var symToStringTag$1 = Symbol$2 ? Symbol$2.toStringTag : void 0;
  function getRawTag(value) {
    var isOwn = hasOwnProperty$c.call(value, symToStringTag$1), tag = value[symToStringTag$1];
    try {
      value[symToStringTag$1] = void 0;
      var unmasked = true;
    } catch (e) {
    }
    var result = nativeObjectToString$1.call(value);
    if (unmasked) {
      if (isOwn) {
        value[symToStringTag$1] = tag;
      } else {
        delete value[symToStringTag$1];
      }
    }
    return result;
  }
  var objectProto$e = Object.prototype;
  var nativeObjectToString = objectProto$e.toString;
  function objectToString(value) {
    return nativeObjectToString.call(value);
  }
  var nullTag = "[object Null]", undefinedTag = "[object Undefined]";
  var symToStringTag = Symbol$2 ? Symbol$2.toStringTag : void 0;
  function baseGetTag(value) {
    if (value == null) {
      return value === void 0 ? undefinedTag : nullTag;
    }
    return symToStringTag && symToStringTag in Object(value) ? getRawTag(value) : objectToString(value);
  }
  function isObjectLike(value) {
    return value != null && typeof value == "object";
  }
  var symbolTag$3 = "[object Symbol]";
  function isSymbol(value) {
    return typeof value == "symbol" || isObjectLike(value) && baseGetTag(value) == symbolTag$3;
  }
  function arrayMap(array, iteratee) {
    var index2 = -1, length = array == null ? 0 : array.length, result = Array(length);
    while (++index2 < length) {
      result[index2] = iteratee(array[index2], index2, array);
    }
    return result;
  }
  var isArray$1 = Array.isArray;
  var isArray$2 = isArray$1;
  var INFINITY$2 = 1 / 0;
  var symbolProto$2 = Symbol$2 ? Symbol$2.prototype : void 0, symbolToString = symbolProto$2 ? symbolProto$2.toString : void 0;
  function baseToString(value) {
    if (typeof value == "string") {
      return value;
    }
    if (isArray$2(value)) {
      return arrayMap(value, baseToString) + "";
    }
    if (isSymbol(value)) {
      return symbolToString ? symbolToString.call(value) : "";
    }
    var result = value + "";
    return result == "0" && 1 / value == -INFINITY$2 ? "-0" : result;
  }
  var reWhitespace = /\s/;
  function trimmedEndIndex(string) {
    var index2 = string.length;
    while (index2-- && reWhitespace.test(string.charAt(index2))) {
    }
    return index2;
  }
  var reTrimStart = /^\s+/;
  function baseTrim(string) {
    return string ? string.slice(0, trimmedEndIndex(string) + 1).replace(reTrimStart, "") : string;
  }
  function isObject$1(value) {
    var type = typeof value;
    return value != null && (type == "object" || type == "function");
  }
  var NAN = 0 / 0;
  var reIsBadHex = /^[-+]0x[0-9a-f]+$/i;
  var reIsBinary = /^0b[01]+$/i;
  var reIsOctal = /^0o[0-7]+$/i;
  var freeParseInt = parseInt;
  function toNumber(value) {
    if (typeof value == "number") {
      return value;
    }
    if (isSymbol(value)) {
      return NAN;
    }
    if (isObject$1(value)) {
      var other = typeof value.valueOf == "function" ? value.valueOf() : value;
      value = isObject$1(other) ? other + "" : other;
    }
    if (typeof value != "string") {
      return value === 0 ? value : +value;
    }
    value = baseTrim(value);
    var isBinary = reIsBinary.test(value);
    return isBinary || reIsOctal.test(value) ? freeParseInt(value.slice(2), isBinary ? 2 : 8) : reIsBadHex.test(value) ? NAN : +value;
  }
  function identity(value) {
    return value;
  }
  var asyncTag = "[object AsyncFunction]", funcTag$2 = "[object Function]", genTag$1 = "[object GeneratorFunction]", proxyTag = "[object Proxy]";
  function isFunction$1(value) {
    if (!isObject$1(value)) {
      return false;
    }
    var tag = baseGetTag(value);
    return tag == funcTag$2 || tag == genTag$1 || tag == asyncTag || tag == proxyTag;
  }
  var coreJsData = root$1["__core-js_shared__"];
  var coreJsData$1 = coreJsData;
  var maskSrcKey = function() {
    var uid = /[^.]+$/.exec(coreJsData$1 && coreJsData$1.keys && coreJsData$1.keys.IE_PROTO || "");
    return uid ? "Symbol(src)_1." + uid : "";
  }();
  function isMasked(func) {
    return !!maskSrcKey && maskSrcKey in func;
  }
  var funcProto$2 = Function.prototype;
  var funcToString$2 = funcProto$2.toString;
  function toSource(func) {
    if (func != null) {
      try {
        return funcToString$2.call(func);
      } catch (e) {
      }
      try {
        return func + "";
      } catch (e) {
      }
    }
    return "";
  }
  var reRegExpChar = /[\\^$.*+?()[\]{}|]/g;
  var reIsHostCtor = /^\[object .+?Constructor\]$/;
  var funcProto$1 = Function.prototype, objectProto$d = Object.prototype;
  var funcToString$1 = funcProto$1.toString;
  var hasOwnProperty$b = objectProto$d.hasOwnProperty;
  var reIsNative = RegExp(
    "^" + funcToString$1.call(hasOwnProperty$b).replace(reRegExpChar, "\\$&").replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g, "$1.*?") + "$"
  );
  function baseIsNative(value) {
    if (!isObject$1(value) || isMasked(value)) {
      return false;
    }
    var pattern = isFunction$1(value) ? reIsNative : reIsHostCtor;
    return pattern.test(toSource(value));
  }
  function getValue(object, key) {
    return object == null ? void 0 : object[key];
  }
  function getNative(object, key) {
    var value = getValue(object, key);
    return baseIsNative(value) ? value : void 0;
  }
  var WeakMap = getNative(root$1, "WeakMap");
  var WeakMap$1 = WeakMap;
  var objectCreate = Object.create;
  var baseCreate = function() {
    function object() {
    }
    return function(proto) {
      if (!isObject$1(proto)) {
        return {};
      }
      if (objectCreate) {
        return objectCreate(proto);
      }
      object.prototype = proto;
      var result = new object();
      object.prototype = void 0;
      return result;
    };
  }();
  var baseCreate$1 = baseCreate;
  function apply(func, thisArg, args) {
    switch (args.length) {
      case 0:
        return func.call(thisArg);
      case 1:
        return func.call(thisArg, args[0]);
      case 2:
        return func.call(thisArg, args[0], args[1]);
      case 3:
        return func.call(thisArg, args[0], args[1], args[2]);
    }
    return func.apply(thisArg, args);
  }
  function noop() {
  }
  function copyArray(source, array) {
    var index2 = -1, length = source.length;
    array || (array = Array(length));
    while (++index2 < length) {
      array[index2] = source[index2];
    }
    return array;
  }
  var HOT_COUNT = 800, HOT_SPAN = 16;
  var nativeNow = Date.now;
  function shortOut(func) {
    var count = 0, lastCalled = 0;
    return function() {
      var stamp = nativeNow(), remaining = HOT_SPAN - (stamp - lastCalled);
      lastCalled = stamp;
      if (remaining > 0) {
        if (++count >= HOT_COUNT) {
          return arguments[0];
        }
      } else {
        count = 0;
      }
      return func.apply(void 0, arguments);
    };
  }
  function constant(value) {
    return function() {
      return value;
    };
  }
  var defineProperty = function() {
    try {
      var func = getNative(Object, "defineProperty");
      func({}, "", {});
      return func;
    } catch (e) {
    }
  }();
  var defineProperty$1 = defineProperty;
  var baseSetToString = !defineProperty$1 ? identity : function(func, string) {
    return defineProperty$1(func, "toString", {
      "configurable": true,
      "enumerable": false,
      "value": constant(string),
      "writable": true
    });
  };
  var baseSetToString$1 = baseSetToString;
  var setToString = shortOut(baseSetToString$1);
  var setToString$1 = setToString;
  function arrayEach(array, iteratee) {
    var index2 = -1, length = array == null ? 0 : array.length;
    while (++index2 < length) {
      if (iteratee(array[index2], index2, array) === false) {
        break;
      }
    }
    return array;
  }
  function baseFindIndex(array, predicate, fromIndex, fromRight) {
    var length = array.length, index2 = fromIndex + (fromRight ? 1 : -1);
    while (fromRight ? index2-- : ++index2 < length) {
      if (predicate(array[index2], index2, array)) {
        return index2;
      }
    }
    return -1;
  }
  function baseIsNaN(value) {
    return value !== value;
  }
  function strictIndexOf(array, value, fromIndex) {
    var index2 = fromIndex - 1, length = array.length;
    while (++index2 < length) {
      if (array[index2] === value) {
        return index2;
      }
    }
    return -1;
  }
  function baseIndexOf(array, value, fromIndex) {
    return value === value ? strictIndexOf(array, value, fromIndex) : baseFindIndex(array, baseIsNaN, fromIndex);
  }
  function arrayIncludes(array, value) {
    var length = array == null ? 0 : array.length;
    return !!length && baseIndexOf(array, value, 0) > -1;
  }
  var MAX_SAFE_INTEGER$1 = 9007199254740991;
  var reIsUint = /^(?:0|[1-9]\d*)$/;
  function isIndex(value, length) {
    var type = typeof value;
    length = length == null ? MAX_SAFE_INTEGER$1 : length;
    return !!length && (type == "number" || type != "symbol" && reIsUint.test(value)) && (value > -1 && value % 1 == 0 && value < length);
  }
  function baseAssignValue(object, key, value) {
    if (key == "__proto__" && defineProperty$1) {
      defineProperty$1(object, key, {
        "configurable": true,
        "enumerable": true,
        "value": value,
        "writable": true
      });
    } else {
      object[key] = value;
    }
  }
  function eq(value, other) {
    return value === other || value !== value && other !== other;
  }
  var objectProto$c = Object.prototype;
  var hasOwnProperty$a = objectProto$c.hasOwnProperty;
  function assignValue(object, key, value) {
    var objValue = object[key];
    if (!(hasOwnProperty$a.call(object, key) && eq(objValue, value)) || value === void 0 && !(key in object)) {
      baseAssignValue(object, key, value);
    }
  }
  function copyObject(source, props, object, customizer) {
    var isNew = !object;
    object || (object = {});
    var index2 = -1, length = props.length;
    while (++index2 < length) {
      var key = props[index2];
      var newValue = customizer ? customizer(object[key], source[key], key, object, source) : void 0;
      if (newValue === void 0) {
        newValue = source[key];
      }
      if (isNew) {
        baseAssignValue(object, key, newValue);
      } else {
        assignValue(object, key, newValue);
      }
    }
    return object;
  }
  var nativeMax$1 = Math.max;
  function overRest(func, start2, transform) {
    start2 = nativeMax$1(start2 === void 0 ? func.length - 1 : start2, 0);
    return function() {
      var args = arguments, index2 = -1, length = nativeMax$1(args.length - start2, 0), array = Array(length);
      while (++index2 < length) {
        array[index2] = args[start2 + index2];
      }
      index2 = -1;
      var otherArgs = Array(start2 + 1);
      while (++index2 < start2) {
        otherArgs[index2] = args[index2];
      }
      otherArgs[start2] = transform(array);
      return apply(func, this, otherArgs);
    };
  }
  function baseRest(func, start2) {
    return setToString$1(overRest(func, start2, identity), func + "");
  }
  var MAX_SAFE_INTEGER = 9007199254740991;
  function isLength(value) {
    return typeof value == "number" && value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
  }
  function isArrayLike(value) {
    return value != null && isLength(value.length) && !isFunction$1(value);
  }
  function isIterateeCall(value, index2, object) {
    if (!isObject$1(object)) {
      return false;
    }
    var type = typeof index2;
    if (type == "number" ? isArrayLike(object) && isIndex(index2, object.length) : type == "string" && index2 in object) {
      return eq(object[index2], value);
    }
    return false;
  }
  function createAssigner(assigner) {
    return baseRest(function(object, sources) {
      var index2 = -1, length = sources.length, customizer = length > 1 ? sources[length - 1] : void 0, guard = length > 2 ? sources[2] : void 0;
      customizer = assigner.length > 3 && typeof customizer == "function" ? (length--, customizer) : void 0;
      if (guard && isIterateeCall(sources[0], sources[1], guard)) {
        customizer = length < 3 ? void 0 : customizer;
        length = 1;
      }
      object = Object(object);
      while (++index2 < length) {
        var source = sources[index2];
        if (source) {
          assigner(object, source, index2, customizer);
        }
      }
      return object;
    });
  }
  var objectProto$b = Object.prototype;
  function isPrototype(value) {
    var Ctor = value && value.constructor, proto = typeof Ctor == "function" && Ctor.prototype || objectProto$b;
    return value === proto;
  }
  function baseTimes(n, iteratee) {
    var index2 = -1, result = Array(n);
    while (++index2 < n) {
      result[index2] = iteratee(index2);
    }
    return result;
  }
  var argsTag$3 = "[object Arguments]";
  function baseIsArguments(value) {
    return isObjectLike(value) && baseGetTag(value) == argsTag$3;
  }
  var objectProto$a = Object.prototype;
  var hasOwnProperty$9 = objectProto$a.hasOwnProperty;
  var propertyIsEnumerable$1 = objectProto$a.propertyIsEnumerable;
  var isArguments = baseIsArguments(function() {
    return arguments;
  }()) ? baseIsArguments : function(value) {
    return isObjectLike(value) && hasOwnProperty$9.call(value, "callee") && !propertyIsEnumerable$1.call(value, "callee");
  };
  var isArguments$1 = isArguments;
  function stubFalse() {
    return false;
  }
  var freeExports$2 = typeof exports == "object" && exports && !exports.nodeType && exports;
  var freeModule$2 = freeExports$2 && typeof module == "object" && module && !module.nodeType && module;
  var moduleExports$2 = freeModule$2 && freeModule$2.exports === freeExports$2;
  var Buffer$2 = moduleExports$2 ? root$1.Buffer : void 0;
  var nativeIsBuffer = Buffer$2 ? Buffer$2.isBuffer : void 0;
  var isBuffer$1 = nativeIsBuffer || stubFalse;
  var isBuffer$2 = isBuffer$1;
  var argsTag$2 = "[object Arguments]", arrayTag$2 = "[object Array]", boolTag$3 = "[object Boolean]", dateTag$3 = "[object Date]", errorTag$2 = "[object Error]", funcTag$1 = "[object Function]", mapTag$5 = "[object Map]", numberTag$3 = "[object Number]", objectTag$4 = "[object Object]", regexpTag$3 = "[object RegExp]", setTag$5 = "[object Set]", stringTag$3 = "[object String]", weakMapTag$2 = "[object WeakMap]";
  var arrayBufferTag$3 = "[object ArrayBuffer]", dataViewTag$4 = "[object DataView]", float32Tag$2 = "[object Float32Array]", float64Tag$2 = "[object Float64Array]", int8Tag$2 = "[object Int8Array]", int16Tag$2 = "[object Int16Array]", int32Tag$2 = "[object Int32Array]", uint8Tag$2 = "[object Uint8Array]", uint8ClampedTag$2 = "[object Uint8ClampedArray]", uint16Tag$2 = "[object Uint16Array]", uint32Tag$2 = "[object Uint32Array]";
  var typedArrayTags = {};
  typedArrayTags[float32Tag$2] = typedArrayTags[float64Tag$2] = typedArrayTags[int8Tag$2] = typedArrayTags[int16Tag$2] = typedArrayTags[int32Tag$2] = typedArrayTags[uint8Tag$2] = typedArrayTags[uint8ClampedTag$2] = typedArrayTags[uint16Tag$2] = typedArrayTags[uint32Tag$2] = true;
  typedArrayTags[argsTag$2] = typedArrayTags[arrayTag$2] = typedArrayTags[arrayBufferTag$3] = typedArrayTags[boolTag$3] = typedArrayTags[dataViewTag$4] = typedArrayTags[dateTag$3] = typedArrayTags[errorTag$2] = typedArrayTags[funcTag$1] = typedArrayTags[mapTag$5] = typedArrayTags[numberTag$3] = typedArrayTags[objectTag$4] = typedArrayTags[regexpTag$3] = typedArrayTags[setTag$5] = typedArrayTags[stringTag$3] = typedArrayTags[weakMapTag$2] = false;
  function baseIsTypedArray(value) {
    return isObjectLike(value) && isLength(value.length) && !!typedArrayTags[baseGetTag(value)];
  }
  function baseUnary(func) {
    return function(value) {
      return func(value);
    };
  }
  var freeExports$1 = typeof exports == "object" && exports && !exports.nodeType && exports;
  var freeModule$1 = freeExports$1 && typeof module == "object" && module && !module.nodeType && module;
  var moduleExports$1 = freeModule$1 && freeModule$1.exports === freeExports$1;
  var freeProcess = moduleExports$1 && freeGlobal$1.process;
  var nodeUtil = function() {
    try {
      var types = freeModule$1 && freeModule$1.require && freeModule$1.require("util").types;
      if (types) {
        return types;
      }
      return freeProcess && freeProcess.binding && freeProcess.binding("util");
    } catch (e) {
    }
  }();
  var nodeUtil$1 = nodeUtil;
  var nodeIsTypedArray = nodeUtil$1 && nodeUtil$1.isTypedArray;
  var isTypedArray$1 = nodeIsTypedArray ? baseUnary(nodeIsTypedArray) : baseIsTypedArray;
  var isTypedArray$2 = isTypedArray$1;
  var objectProto$9 = Object.prototype;
  var hasOwnProperty$8 = objectProto$9.hasOwnProperty;
  function arrayLikeKeys(value, inherited) {
    var isArr = isArray$2(value), isArg = !isArr && isArguments$1(value), isBuff = !isArr && !isArg && isBuffer$2(value), isType = !isArr && !isArg && !isBuff && isTypedArray$2(value), skipIndexes = isArr || isArg || isBuff || isType, result = skipIndexes ? baseTimes(value.length, String) : [], length = result.length;
    for (var key in value) {
      if ((inherited || hasOwnProperty$8.call(value, key)) && !(skipIndexes && (key == "length" || isBuff && (key == "offset" || key == "parent") || isType && (key == "buffer" || key == "byteLength" || key == "byteOffset") || isIndex(key, length)))) {
        result.push(key);
      }
    }
    return result;
  }
  function overArg(func, transform) {
    return function(arg) {
      return func(transform(arg));
    };
  }
  var nativeKeys = overArg(Object.keys, Object);
  var nativeKeys$1 = nativeKeys;
  var objectProto$8 = Object.prototype;
  var hasOwnProperty$7 = objectProto$8.hasOwnProperty;
  function baseKeys(object) {
    if (!isPrototype(object)) {
      return nativeKeys$1(object);
    }
    var result = [];
    for (var key in Object(object)) {
      if (hasOwnProperty$7.call(object, key) && key != "constructor") {
        result.push(key);
      }
    }
    return result;
  }
  function keys(object) {
    return isArrayLike(object) ? arrayLikeKeys(object) : baseKeys(object);
  }
  function nativeKeysIn(object) {
    var result = [];
    if (object != null) {
      for (var key in Object(object)) {
        result.push(key);
      }
    }
    return result;
  }
  var objectProto$7 = Object.prototype;
  var hasOwnProperty$6 = objectProto$7.hasOwnProperty;
  function baseKeysIn(object) {
    if (!isObject$1(object)) {
      return nativeKeysIn(object);
    }
    var isProto = isPrototype(object), result = [];
    for (var key in object) {
      if (!(key == "constructor" && (isProto || !hasOwnProperty$6.call(object, key)))) {
        result.push(key);
      }
    }
    return result;
  }
  function keysIn(object) {
    return isArrayLike(object) ? arrayLikeKeys(object, true) : baseKeysIn(object);
  }
  var reIsDeepProp = /\.|\[(?:[^[\]]*|(["'])(?:(?!\1)[^\\]|\\.)*?\1)\]/, reIsPlainProp = /^\w*$/;
  function isKey(value, object) {
    if (isArray$2(value)) {
      return false;
    }
    var type = typeof value;
    if (type == "number" || type == "symbol" || type == "boolean" || value == null || isSymbol(value)) {
      return true;
    }
    return reIsPlainProp.test(value) || !reIsDeepProp.test(value) || object != null && value in Object(object);
  }
  var nativeCreate = getNative(Object, "create");
  var nativeCreate$1 = nativeCreate;
  function hashClear() {
    this.__data__ = nativeCreate$1 ? nativeCreate$1(null) : {};
    this.size = 0;
  }
  function hashDelete(key) {
    var result = this.has(key) && delete this.__data__[key];
    this.size -= result ? 1 : 0;
    return result;
  }
  var HASH_UNDEFINED$2 = "__lodash_hash_undefined__";
  var objectProto$6 = Object.prototype;
  var hasOwnProperty$5 = objectProto$6.hasOwnProperty;
  function hashGet(key) {
    var data2 = this.__data__;
    if (nativeCreate$1) {
      var result = data2[key];
      return result === HASH_UNDEFINED$2 ? void 0 : result;
    }
    return hasOwnProperty$5.call(data2, key) ? data2[key] : void 0;
  }
  var objectProto$5 = Object.prototype;
  var hasOwnProperty$4 = objectProto$5.hasOwnProperty;
  function hashHas(key) {
    var data2 = this.__data__;
    return nativeCreate$1 ? data2[key] !== void 0 : hasOwnProperty$4.call(data2, key);
  }
  var HASH_UNDEFINED$1 = "__lodash_hash_undefined__";
  function hashSet(key, value) {
    var data2 = this.__data__;
    this.size += this.has(key) ? 0 : 1;
    data2[key] = nativeCreate$1 && value === void 0 ? HASH_UNDEFINED$1 : value;
    return this;
  }
  function Hash(entries) {
    var index2 = -1, length = entries == null ? 0 : entries.length;
    this.clear();
    while (++index2 < length) {
      var entry = entries[index2];
      this.set(entry[0], entry[1]);
    }
  }
  Hash.prototype.clear = hashClear;
  Hash.prototype["delete"] = hashDelete;
  Hash.prototype.get = hashGet;
  Hash.prototype.has = hashHas;
  Hash.prototype.set = hashSet;
  function listCacheClear() {
    this.__data__ = [];
    this.size = 0;
  }
  function assocIndexOf(array, key) {
    var length = array.length;
    while (length--) {
      if (eq(array[length][0], key)) {
        return length;
      }
    }
    return -1;
  }
  var arrayProto = Array.prototype;
  var splice = arrayProto.splice;
  function listCacheDelete(key) {
    var data2 = this.__data__, index2 = assocIndexOf(data2, key);
    if (index2 < 0) {
      return false;
    }
    var lastIndex = data2.length - 1;
    if (index2 == lastIndex) {
      data2.pop();
    } else {
      splice.call(data2, index2, 1);
    }
    --this.size;
    return true;
  }
  function listCacheGet(key) {
    var data2 = this.__data__, index2 = assocIndexOf(data2, key);
    return index2 < 0 ? void 0 : data2[index2][1];
  }
  function listCacheHas(key) {
    return assocIndexOf(this.__data__, key) > -1;
  }
  function listCacheSet(key, value) {
    var data2 = this.__data__, index2 = assocIndexOf(data2, key);
    if (index2 < 0) {
      ++this.size;
      data2.push([key, value]);
    } else {
      data2[index2][1] = value;
    }
    return this;
  }
  function ListCache(entries) {
    var index2 = -1, length = entries == null ? 0 : entries.length;
    this.clear();
    while (++index2 < length) {
      var entry = entries[index2];
      this.set(entry[0], entry[1]);
    }
  }
  ListCache.prototype.clear = listCacheClear;
  ListCache.prototype["delete"] = listCacheDelete;
  ListCache.prototype.get = listCacheGet;
  ListCache.prototype.has = listCacheHas;
  ListCache.prototype.set = listCacheSet;
  var Map$1 = getNative(root$1, "Map");
  var Map$2 = Map$1;
  function mapCacheClear() {
    this.size = 0;
    this.__data__ = {
      "hash": new Hash(),
      "map": new (Map$2 || ListCache)(),
      "string": new Hash()
    };
  }
  function isKeyable(value) {
    var type = typeof value;
    return type == "string" || type == "number" || type == "symbol" || type == "boolean" ? value !== "__proto__" : value === null;
  }
  function getMapData(map, key) {
    var data2 = map.__data__;
    return isKeyable(key) ? data2[typeof key == "string" ? "string" : "hash"] : data2.map;
  }
  function mapCacheDelete(key) {
    var result = getMapData(this, key)["delete"](key);
    this.size -= result ? 1 : 0;
    return result;
  }
  function mapCacheGet(key) {
    return getMapData(this, key).get(key);
  }
  function mapCacheHas(key) {
    return getMapData(this, key).has(key);
  }
  function mapCacheSet(key, value) {
    var data2 = getMapData(this, key), size = data2.size;
    data2.set(key, value);
    this.size += data2.size == size ? 0 : 1;
    return this;
  }
  function MapCache(entries) {
    var index2 = -1, length = entries == null ? 0 : entries.length;
    this.clear();
    while (++index2 < length) {
      var entry = entries[index2];
      this.set(entry[0], entry[1]);
    }
  }
  MapCache.prototype.clear = mapCacheClear;
  MapCache.prototype["delete"] = mapCacheDelete;
  MapCache.prototype.get = mapCacheGet;
  MapCache.prototype.has = mapCacheHas;
  MapCache.prototype.set = mapCacheSet;
  var FUNC_ERROR_TEXT$1 = "Expected a function";
  function memoize(func, resolver) {
    if (typeof func != "function" || resolver != null && typeof resolver != "function") {
      throw new TypeError(FUNC_ERROR_TEXT$1);
    }
    var memoized = function() {
      var args = arguments, key = resolver ? resolver.apply(this, args) : args[0], cache2 = memoized.cache;
      if (cache2.has(key)) {
        return cache2.get(key);
      }
      var result = func.apply(this, args);
      memoized.cache = cache2.set(key, result) || cache2;
      return result;
    };
    memoized.cache = new (memoize.Cache || MapCache)();
    return memoized;
  }
  memoize.Cache = MapCache;
  var MAX_MEMOIZE_SIZE = 500;
  function memoizeCapped(func) {
    var result = memoize(func, function(key) {
      if (cache2.size === MAX_MEMOIZE_SIZE) {
        cache2.clear();
      }
      return key;
    });
    var cache2 = result.cache;
    return result;
  }
  var rePropName = /[^.[\]]+|\[(?:(-?\d+(?:\.\d+)?)|(["'])((?:(?!\2)[^\\]|\\.)*?)\2)\]|(?=(?:\.|\[\])(?:\.|\[\]|$))/g;
  var reEscapeChar = /\\(\\)?/g;
  var stringToPath = memoizeCapped(function(string) {
    var result = [];
    if (string.charCodeAt(0) === 46) {
      result.push("");
    }
    string.replace(rePropName, function(match, number, quote, subString) {
      result.push(quote ? subString.replace(reEscapeChar, "$1") : number || match);
    });
    return result;
  });
  var stringToPath$1 = stringToPath;
  function toString$1(value) {
    return value == null ? "" : baseToString(value);
  }
  function castPath(value, object) {
    if (isArray$2(value)) {
      return value;
    }
    return isKey(value, object) ? [value] : stringToPath$1(toString$1(value));
  }
  var INFINITY$1 = 1 / 0;
  function toKey(value) {
    if (typeof value == "string" || isSymbol(value)) {
      return value;
    }
    var result = value + "";
    return result == "0" && 1 / value == -INFINITY$1 ? "-0" : result;
  }
  function baseGet(object, path) {
    path = castPath(path, object);
    var index2 = 0, length = path.length;
    while (object != null && index2 < length) {
      object = object[toKey(path[index2++])];
    }
    return index2 && index2 == length ? object : void 0;
  }
  function get(object, path, defaultValue) {
    var result = object == null ? void 0 : baseGet(object, path);
    return result === void 0 ? defaultValue : result;
  }
  function arrayPush(array, values) {
    var index2 = -1, length = values.length, offset2 = array.length;
    while (++index2 < length) {
      array[offset2 + index2] = values[index2];
    }
    return array;
  }
  var spreadableSymbol = Symbol$2 ? Symbol$2.isConcatSpreadable : void 0;
  function isFlattenable(value) {
    return isArray$2(value) || isArguments$1(value) || !!(spreadableSymbol && value && value[spreadableSymbol]);
  }
  function baseFlatten(array, depth, predicate, isStrict, result) {
    var index2 = -1, length = array.length;
    predicate || (predicate = isFlattenable);
    result || (result = []);
    while (++index2 < length) {
      var value = array[index2];
      if (depth > 0 && predicate(value)) {
        if (depth > 1) {
          baseFlatten(value, depth - 1, predicate, isStrict, result);
        } else {
          arrayPush(result, value);
        }
      } else if (!isStrict) {
        result[result.length] = value;
      }
    }
    return result;
  }
  function flatten(array) {
    var length = array == null ? 0 : array.length;
    return length ? baseFlatten(array, 1) : [];
  }
  function flatRest(func) {
    return setToString$1(overRest(func, void 0, flatten), func + "");
  }
  var getPrototype = overArg(Object.getPrototypeOf, Object);
  var getPrototype$1 = getPrototype;
  var objectTag$3 = "[object Object]";
  var funcProto = Function.prototype, objectProto$4 = Object.prototype;
  var funcToString = funcProto.toString;
  var hasOwnProperty$3 = objectProto$4.hasOwnProperty;
  var objectCtorString = funcToString.call(Object);
  function isPlainObject$1(value) {
    if (!isObjectLike(value) || baseGetTag(value) != objectTag$3) {
      return false;
    }
    var proto = getPrototype$1(value);
    if (proto === null) {
      return true;
    }
    var Ctor = hasOwnProperty$3.call(proto, "constructor") && proto.constructor;
    return typeof Ctor == "function" && Ctor instanceof Ctor && funcToString.call(Ctor) == objectCtorString;
  }
  function baseSlice(array, start2, end2) {
    var index2 = -1, length = array.length;
    if (start2 < 0) {
      start2 = -start2 > length ? 0 : length + start2;
    }
    end2 = end2 > length ? length : end2;
    if (end2 < 0) {
      end2 += length;
    }
    length = start2 > end2 ? 0 : end2 - start2 >>> 0;
    start2 >>>= 0;
    var result = Array(length);
    while (++index2 < length) {
      result[index2] = array[index2 + start2];
    }
    return result;
  }
  function castSlice(array, start2, end2) {
    var length = array.length;
    end2 = end2 === void 0 ? length : end2;
    return !start2 && end2 >= length ? array : baseSlice(array, start2, end2);
  }
  var rsAstralRange$2 = "\\ud800-\\udfff", rsComboMarksRange$3 = "\\u0300-\\u036f", reComboHalfMarksRange$3 = "\\ufe20-\\ufe2f", rsComboSymbolsRange$3 = "\\u20d0-\\u20ff", rsComboRange$3 = rsComboMarksRange$3 + reComboHalfMarksRange$3 + rsComboSymbolsRange$3, rsVarRange$2 = "\\ufe0e\\ufe0f";
  var rsZWJ$2 = "\\u200d";
  var reHasUnicode = RegExp("[" + rsZWJ$2 + rsAstralRange$2 + rsComboRange$3 + rsVarRange$2 + "]");
  function hasUnicode(string) {
    return reHasUnicode.test(string);
  }
  function asciiToArray(string) {
    return string.split("");
  }
  var rsAstralRange$1 = "\\ud800-\\udfff", rsComboMarksRange$2 = "\\u0300-\\u036f", reComboHalfMarksRange$2 = "\\ufe20-\\ufe2f", rsComboSymbolsRange$2 = "\\u20d0-\\u20ff", rsComboRange$2 = rsComboMarksRange$2 + reComboHalfMarksRange$2 + rsComboSymbolsRange$2, rsVarRange$1 = "\\ufe0e\\ufe0f";
  var rsAstral = "[" + rsAstralRange$1 + "]", rsCombo$2 = "[" + rsComboRange$2 + "]", rsFitz$1 = "\\ud83c[\\udffb-\\udfff]", rsModifier$1 = "(?:" + rsCombo$2 + "|" + rsFitz$1 + ")", rsNonAstral$1 = "[^" + rsAstralRange$1 + "]", rsRegional$1 = "(?:\\ud83c[\\udde6-\\uddff]){2}", rsSurrPair$1 = "[\\ud800-\\udbff][\\udc00-\\udfff]", rsZWJ$1 = "\\u200d";
  var reOptMod$1 = rsModifier$1 + "?", rsOptVar$1 = "[" + rsVarRange$1 + "]?", rsOptJoin$1 = "(?:" + rsZWJ$1 + "(?:" + [rsNonAstral$1, rsRegional$1, rsSurrPair$1].join("|") + ")" + rsOptVar$1 + reOptMod$1 + ")*", rsSeq$1 = rsOptVar$1 + reOptMod$1 + rsOptJoin$1, rsSymbol = "(?:" + [rsNonAstral$1 + rsCombo$2 + "?", rsCombo$2, rsRegional$1, rsSurrPair$1, rsAstral].join("|") + ")";
  var reUnicode = RegExp(rsFitz$1 + "(?=" + rsFitz$1 + ")|" + rsSymbol + rsSeq$1, "g");
  function unicodeToArray(string) {
    return string.match(reUnicode) || [];
  }
  function stringToArray(string) {
    return hasUnicode(string) ? unicodeToArray(string) : asciiToArray(string);
  }
  function createCaseFirst(methodName) {
    return function(string) {
      string = toString$1(string);
      var strSymbols = hasUnicode(string) ? stringToArray(string) : void 0;
      var chr = strSymbols ? strSymbols[0] : string.charAt(0);
      var trailing = strSymbols ? castSlice(strSymbols, 1).join("") : string.slice(1);
      return chr[methodName]() + trailing;
    };
  }
  var upperFirst = createCaseFirst("toUpperCase");
  var upperFirst$1 = upperFirst;
  function arrayReduce(array, iteratee, accumulator, initAccum) {
    var index2 = -1, length = array == null ? 0 : array.length;
    if (initAccum && length) {
      accumulator = array[++index2];
    }
    while (++index2 < length) {
      accumulator = iteratee(accumulator, array[index2], index2, array);
    }
    return accumulator;
  }
  function basePropertyOf(object) {
    return function(key) {
      return object == null ? void 0 : object[key];
    };
  }
  var deburredLetters = {
    "\xC0": "A",
    "\xC1": "A",
    "\xC2": "A",
    "\xC3": "A",
    "\xC4": "A",
    "\xC5": "A",
    "\xE0": "a",
    "\xE1": "a",
    "\xE2": "a",
    "\xE3": "a",
    "\xE4": "a",
    "\xE5": "a",
    "\xC7": "C",
    "\xE7": "c",
    "\xD0": "D",
    "\xF0": "d",
    "\xC8": "E",
    "\xC9": "E",
    "\xCA": "E",
    "\xCB": "E",
    "\xE8": "e",
    "\xE9": "e",
    "\xEA": "e",
    "\xEB": "e",
    "\xCC": "I",
    "\xCD": "I",
    "\xCE": "I",
    "\xCF": "I",
    "\xEC": "i",
    "\xED": "i",
    "\xEE": "i",
    "\xEF": "i",
    "\xD1": "N",
    "\xF1": "n",
    "\xD2": "O",
    "\xD3": "O",
    "\xD4": "O",
    "\xD5": "O",
    "\xD6": "O",
    "\xD8": "O",
    "\xF2": "o",
    "\xF3": "o",
    "\xF4": "o",
    "\xF5": "o",
    "\xF6": "o",
    "\xF8": "o",
    "\xD9": "U",
    "\xDA": "U",
    "\xDB": "U",
    "\xDC": "U",
    "\xF9": "u",
    "\xFA": "u",
    "\xFB": "u",
    "\xFC": "u",
    "\xDD": "Y",
    "\xFD": "y",
    "\xFF": "y",
    "\xC6": "Ae",
    "\xE6": "ae",
    "\xDE": "Th",
    "\xFE": "th",
    "\xDF": "ss",
    "\u0100": "A",
    "\u0102": "A",
    "\u0104": "A",
    "\u0101": "a",
    "\u0103": "a",
    "\u0105": "a",
    "\u0106": "C",
    "\u0108": "C",
    "\u010A": "C",
    "\u010C": "C",
    "\u0107": "c",
    "\u0109": "c",
    "\u010B": "c",
    "\u010D": "c",
    "\u010E": "D",
    "\u0110": "D",
    "\u010F": "d",
    "\u0111": "d",
    "\u0112": "E",
    "\u0114": "E",
    "\u0116": "E",
    "\u0118": "E",
    "\u011A": "E",
    "\u0113": "e",
    "\u0115": "e",
    "\u0117": "e",
    "\u0119": "e",
    "\u011B": "e",
    "\u011C": "G",
    "\u011E": "G",
    "\u0120": "G",
    "\u0122": "G",
    "\u011D": "g",
    "\u011F": "g",
    "\u0121": "g",
    "\u0123": "g",
    "\u0124": "H",
    "\u0126": "H",
    "\u0125": "h",
    "\u0127": "h",
    "\u0128": "I",
    "\u012A": "I",
    "\u012C": "I",
    "\u012E": "I",
    "\u0130": "I",
    "\u0129": "i",
    "\u012B": "i",
    "\u012D": "i",
    "\u012F": "i",
    "\u0131": "i",
    "\u0134": "J",
    "\u0135": "j",
    "\u0136": "K",
    "\u0137": "k",
    "\u0138": "k",
    "\u0139": "L",
    "\u013B": "L",
    "\u013D": "L",
    "\u013F": "L",
    "\u0141": "L",
    "\u013A": "l",
    "\u013C": "l",
    "\u013E": "l",
    "\u0140": "l",
    "\u0142": "l",
    "\u0143": "N",
    "\u0145": "N",
    "\u0147": "N",
    "\u014A": "N",
    "\u0144": "n",
    "\u0146": "n",
    "\u0148": "n",
    "\u014B": "n",
    "\u014C": "O",
    "\u014E": "O",
    "\u0150": "O",
    "\u014D": "o",
    "\u014F": "o",
    "\u0151": "o",
    "\u0154": "R",
    "\u0156": "R",
    "\u0158": "R",
    "\u0155": "r",
    "\u0157": "r",
    "\u0159": "r",
    "\u015A": "S",
    "\u015C": "S",
    "\u015E": "S",
    "\u0160": "S",
    "\u015B": "s",
    "\u015D": "s",
    "\u015F": "s",
    "\u0161": "s",
    "\u0162": "T",
    "\u0164": "T",
    "\u0166": "T",
    "\u0163": "t",
    "\u0165": "t",
    "\u0167": "t",
    "\u0168": "U",
    "\u016A": "U",
    "\u016C": "U",
    "\u016E": "U",
    "\u0170": "U",
    "\u0172": "U",
    "\u0169": "u",
    "\u016B": "u",
    "\u016D": "u",
    "\u016F": "u",
    "\u0171": "u",
    "\u0173": "u",
    "\u0174": "W",
    "\u0175": "w",
    "\u0176": "Y",
    "\u0177": "y",
    "\u0178": "Y",
    "\u0179": "Z",
    "\u017B": "Z",
    "\u017D": "Z",
    "\u017A": "z",
    "\u017C": "z",
    "\u017E": "z",
    "\u0132": "IJ",
    "\u0133": "ij",
    "\u0152": "Oe",
    "\u0153": "oe",
    "\u0149": "'n",
    "\u017F": "s"
  };
  var deburrLetter = basePropertyOf(deburredLetters);
  var deburrLetter$1 = deburrLetter;
  var reLatin = /[\xc0-\xd6\xd8-\xf6\xf8-\xff\u0100-\u017f]/g;
  var rsComboMarksRange$1 = "\\u0300-\\u036f", reComboHalfMarksRange$1 = "\\ufe20-\\ufe2f", rsComboSymbolsRange$1 = "\\u20d0-\\u20ff", rsComboRange$1 = rsComboMarksRange$1 + reComboHalfMarksRange$1 + rsComboSymbolsRange$1;
  var rsCombo$1 = "[" + rsComboRange$1 + "]";
  var reComboMark = RegExp(rsCombo$1, "g");
  function deburr(string) {
    string = toString$1(string);
    return string && string.replace(reLatin, deburrLetter$1).replace(reComboMark, "");
  }
  var reAsciiWord = /[^\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\x7f]+/g;
  function asciiWords(string) {
    return string.match(reAsciiWord) || [];
  }
  var reHasUnicodeWord = /[a-z][A-Z]|[A-Z]{2}[a-z]|[0-9][a-zA-Z]|[a-zA-Z][0-9]|[^a-zA-Z0-9 ]/;
  function hasUnicodeWord(string) {
    return reHasUnicodeWord.test(string);
  }
  var rsAstralRange = "\\ud800-\\udfff", rsComboMarksRange = "\\u0300-\\u036f", reComboHalfMarksRange = "\\ufe20-\\ufe2f", rsComboSymbolsRange = "\\u20d0-\\u20ff", rsComboRange = rsComboMarksRange + reComboHalfMarksRange + rsComboSymbolsRange, rsDingbatRange = "\\u2700-\\u27bf", rsLowerRange = "a-z\\xdf-\\xf6\\xf8-\\xff", rsMathOpRange = "\\xac\\xb1\\xd7\\xf7", rsNonCharRange = "\\x00-\\x2f\\x3a-\\x40\\x5b-\\x60\\x7b-\\xbf", rsPunctuationRange = "\\u2000-\\u206f", rsSpaceRange = " \\t\\x0b\\f\\xa0\\ufeff\\n\\r\\u2028\\u2029\\u1680\\u180e\\u2000\\u2001\\u2002\\u2003\\u2004\\u2005\\u2006\\u2007\\u2008\\u2009\\u200a\\u202f\\u205f\\u3000", rsUpperRange = "A-Z\\xc0-\\xd6\\xd8-\\xde", rsVarRange = "\\ufe0e\\ufe0f", rsBreakRange = rsMathOpRange + rsNonCharRange + rsPunctuationRange + rsSpaceRange;
  var rsApos$1 = "['\u2019]", rsBreak = "[" + rsBreakRange + "]", rsCombo = "[" + rsComboRange + "]", rsDigits = "\\d+", rsDingbat = "[" + rsDingbatRange + "]", rsLower = "[" + rsLowerRange + "]", rsMisc = "[^" + rsAstralRange + rsBreakRange + rsDigits + rsDingbatRange + rsLowerRange + rsUpperRange + "]", rsFitz = "\\ud83c[\\udffb-\\udfff]", rsModifier = "(?:" + rsCombo + "|" + rsFitz + ")", rsNonAstral = "[^" + rsAstralRange + "]", rsRegional = "(?:\\ud83c[\\udde6-\\uddff]){2}", rsSurrPair = "[\\ud800-\\udbff][\\udc00-\\udfff]", rsUpper = "[" + rsUpperRange + "]", rsZWJ = "\\u200d";
  var rsMiscLower = "(?:" + rsLower + "|" + rsMisc + ")", rsMiscUpper = "(?:" + rsUpper + "|" + rsMisc + ")", rsOptContrLower = "(?:" + rsApos$1 + "(?:d|ll|m|re|s|t|ve))?", rsOptContrUpper = "(?:" + rsApos$1 + "(?:D|LL|M|RE|S|T|VE))?", reOptMod = rsModifier + "?", rsOptVar = "[" + rsVarRange + "]?", rsOptJoin = "(?:" + rsZWJ + "(?:" + [rsNonAstral, rsRegional, rsSurrPair].join("|") + ")" + rsOptVar + reOptMod + ")*", rsOrdLower = "\\d*(?:1st|2nd|3rd|(?![123])\\dth)(?=\\b|[A-Z_])", rsOrdUpper = "\\d*(?:1ST|2ND|3RD|(?![123])\\dTH)(?=\\b|[a-z_])", rsSeq = rsOptVar + reOptMod + rsOptJoin, rsEmoji = "(?:" + [rsDingbat, rsRegional, rsSurrPair].join("|") + ")" + rsSeq;
  var reUnicodeWord = RegExp([
    rsUpper + "?" + rsLower + "+" + rsOptContrLower + "(?=" + [rsBreak, rsUpper, "$"].join("|") + ")",
    rsMiscUpper + "+" + rsOptContrUpper + "(?=" + [rsBreak, rsUpper + rsMiscLower, "$"].join("|") + ")",
    rsUpper + "?" + rsMiscLower + "+" + rsOptContrLower,
    rsUpper + "+" + rsOptContrUpper,
    rsOrdUpper,
    rsOrdLower,
    rsDigits,
    rsEmoji
  ].join("|"), "g");
  function unicodeWords(string) {
    return string.match(reUnicodeWord) || [];
  }
  function words(string, pattern, guard) {
    string = toString$1(string);
    pattern = guard ? void 0 : pattern;
    if (pattern === void 0) {
      return hasUnicodeWord(string) ? unicodeWords(string) : asciiWords(string);
    }
    return string.match(pattern) || [];
  }
  var rsApos = "['\u2019]";
  var reApos = RegExp(rsApos, "g");
  function createCompounder(callback) {
    return function(string) {
      return arrayReduce(words(deburr(string).replace(reApos, "")), callback, "");
    };
  }
  function baseClamp(number, lower, upper) {
    if (number === number) {
      if (upper !== void 0) {
        number = number <= upper ? number : upper;
      }
      if (lower !== void 0) {
        number = number >= lower ? number : lower;
      }
    }
    return number;
  }
  function clamp(number, lower, upper) {
    if (upper === void 0) {
      upper = lower;
      lower = void 0;
    }
    if (upper !== void 0) {
      upper = toNumber(upper);
      upper = upper === upper ? upper : 0;
    }
    if (lower !== void 0) {
      lower = toNumber(lower);
      lower = lower === lower ? lower : 0;
    }
    return baseClamp(toNumber(number), lower, upper);
  }
  function stackClear() {
    this.__data__ = new ListCache();
    this.size = 0;
  }
  function stackDelete(key) {
    var data2 = this.__data__, result = data2["delete"](key);
    this.size = data2.size;
    return result;
  }
  function stackGet(key) {
    return this.__data__.get(key);
  }
  function stackHas(key) {
    return this.__data__.has(key);
  }
  var LARGE_ARRAY_SIZE$1 = 200;
  function stackSet(key, value) {
    var data2 = this.__data__;
    if (data2 instanceof ListCache) {
      var pairs = data2.__data__;
      if (!Map$2 || pairs.length < LARGE_ARRAY_SIZE$1 - 1) {
        pairs.push([key, value]);
        this.size = ++data2.size;
        return this;
      }
      data2 = this.__data__ = new MapCache(pairs);
    }
    data2.set(key, value);
    this.size = data2.size;
    return this;
  }
  function Stack(entries) {
    var data2 = this.__data__ = new ListCache(entries);
    this.size = data2.size;
  }
  Stack.prototype.clear = stackClear;
  Stack.prototype["delete"] = stackDelete;
  Stack.prototype.get = stackGet;
  Stack.prototype.has = stackHas;
  Stack.prototype.set = stackSet;
  function baseAssign(object, source) {
    return object && copyObject(source, keys(source), object);
  }
  function baseAssignIn(object, source) {
    return object && copyObject(source, keysIn(source), object);
  }
  var freeExports = typeof exports == "object" && exports && !exports.nodeType && exports;
  var freeModule = freeExports && typeof module == "object" && module && !module.nodeType && module;
  var moduleExports = freeModule && freeModule.exports === freeExports;
  var Buffer$1 = moduleExports ? root$1.Buffer : void 0, allocUnsafe = Buffer$1 ? Buffer$1.allocUnsafe : void 0;
  function cloneBuffer(buffer, isDeep) {
    if (isDeep) {
      return buffer.slice();
    }
    var length = buffer.length, result = allocUnsafe ? allocUnsafe(length) : new buffer.constructor(length);
    buffer.copy(result);
    return result;
  }
  function arrayFilter(array, predicate) {
    var index2 = -1, length = array == null ? 0 : array.length, resIndex = 0, result = [];
    while (++index2 < length) {
      var value = array[index2];
      if (predicate(value, index2, array)) {
        result[resIndex++] = value;
      }
    }
    return result;
  }
  function stubArray() {
    return [];
  }
  var objectProto$3 = Object.prototype;
  var propertyIsEnumerable = objectProto$3.propertyIsEnumerable;
  var nativeGetSymbols$1 = Object.getOwnPropertySymbols;
  var getSymbols = !nativeGetSymbols$1 ? stubArray : function(object) {
    if (object == null) {
      return [];
    }
    object = Object(object);
    return arrayFilter(nativeGetSymbols$1(object), function(symbol) {
      return propertyIsEnumerable.call(object, symbol);
    });
  };
  var getSymbols$1 = getSymbols;
  function copySymbols(source, object) {
    return copyObject(source, getSymbols$1(source), object);
  }
  var nativeGetSymbols = Object.getOwnPropertySymbols;
  var getSymbolsIn = !nativeGetSymbols ? stubArray : function(object) {
    var result = [];
    while (object) {
      arrayPush(result, getSymbols$1(object));
      object = getPrototype$1(object);
    }
    return result;
  };
  var getSymbolsIn$1 = getSymbolsIn;
  function copySymbolsIn(source, object) {
    return copyObject(source, getSymbolsIn$1(source), object);
  }
  function baseGetAllKeys(object, keysFunc, symbolsFunc) {
    var result = keysFunc(object);
    return isArray$2(object) ? result : arrayPush(result, symbolsFunc(object));
  }
  function getAllKeys(object) {
    return baseGetAllKeys(object, keys, getSymbols$1);
  }
  function getAllKeysIn(object) {
    return baseGetAllKeys(object, keysIn, getSymbolsIn$1);
  }
  var DataView = getNative(root$1, "DataView");
  var DataView$1 = DataView;
  var Promise$1 = getNative(root$1, "Promise");
  var Promise$2 = Promise$1;
  var Set$1 = getNative(root$1, "Set");
  var Set$2 = Set$1;
  var mapTag$4 = "[object Map]", objectTag$2 = "[object Object]", promiseTag = "[object Promise]", setTag$4 = "[object Set]", weakMapTag$1 = "[object WeakMap]";
  var dataViewTag$3 = "[object DataView]";
  var dataViewCtorString = toSource(DataView$1), mapCtorString = toSource(Map$2), promiseCtorString = toSource(Promise$2), setCtorString = toSource(Set$2), weakMapCtorString = toSource(WeakMap$1);
  var getTag = baseGetTag;
  if (DataView$1 && getTag(new DataView$1(new ArrayBuffer(1))) != dataViewTag$3 || Map$2 && getTag(new Map$2()) != mapTag$4 || Promise$2 && getTag(Promise$2.resolve()) != promiseTag || Set$2 && getTag(new Set$2()) != setTag$4 || WeakMap$1 && getTag(new WeakMap$1()) != weakMapTag$1) {
    getTag = function(value) {
      var result = baseGetTag(value), Ctor = result == objectTag$2 ? value.constructor : void 0, ctorString = Ctor ? toSource(Ctor) : "";
      if (ctorString) {
        switch (ctorString) {
          case dataViewCtorString:
            return dataViewTag$3;
          case mapCtorString:
            return mapTag$4;
          case promiseCtorString:
            return promiseTag;
          case setCtorString:
            return setTag$4;
          case weakMapCtorString:
            return weakMapTag$1;
        }
      }
      return result;
    };
  }
  var getTag$1 = getTag;
  var objectProto$2 = Object.prototype;
  var hasOwnProperty$2 = objectProto$2.hasOwnProperty;
  function initCloneArray(array) {
    var length = array.length, result = new array.constructor(length);
    if (length && typeof array[0] == "string" && hasOwnProperty$2.call(array, "index")) {
      result.index = array.index;
      result.input = array.input;
    }
    return result;
  }
  var Uint8Array$1 = root$1.Uint8Array;
  var Uint8Array$2 = Uint8Array$1;
  function cloneArrayBuffer(arrayBuffer) {
    var result = new arrayBuffer.constructor(arrayBuffer.byteLength);
    new Uint8Array$2(result).set(new Uint8Array$2(arrayBuffer));
    return result;
  }
  function cloneDataView(dataView, isDeep) {
    var buffer = isDeep ? cloneArrayBuffer(dataView.buffer) : dataView.buffer;
    return new dataView.constructor(buffer, dataView.byteOffset, dataView.byteLength);
  }
  var reFlags = /\w*$/;
  function cloneRegExp(regexp) {
    var result = new regexp.constructor(regexp.source, reFlags.exec(regexp));
    result.lastIndex = regexp.lastIndex;
    return result;
  }
  var symbolProto$1 = Symbol$2 ? Symbol$2.prototype : void 0, symbolValueOf$1 = symbolProto$1 ? symbolProto$1.valueOf : void 0;
  function cloneSymbol(symbol) {
    return symbolValueOf$1 ? Object(symbolValueOf$1.call(symbol)) : {};
  }
  function cloneTypedArray(typedArray, isDeep) {
    var buffer = isDeep ? cloneArrayBuffer(typedArray.buffer) : typedArray.buffer;
    return new typedArray.constructor(buffer, typedArray.byteOffset, typedArray.length);
  }
  var boolTag$2 = "[object Boolean]", dateTag$2 = "[object Date]", mapTag$3 = "[object Map]", numberTag$2 = "[object Number]", regexpTag$2 = "[object RegExp]", setTag$3 = "[object Set]", stringTag$2 = "[object String]", symbolTag$2 = "[object Symbol]";
  var arrayBufferTag$2 = "[object ArrayBuffer]", dataViewTag$2 = "[object DataView]", float32Tag$1 = "[object Float32Array]", float64Tag$1 = "[object Float64Array]", int8Tag$1 = "[object Int8Array]", int16Tag$1 = "[object Int16Array]", int32Tag$1 = "[object Int32Array]", uint8Tag$1 = "[object Uint8Array]", uint8ClampedTag$1 = "[object Uint8ClampedArray]", uint16Tag$1 = "[object Uint16Array]", uint32Tag$1 = "[object Uint32Array]";
  function initCloneByTag(object, tag, isDeep) {
    var Ctor = object.constructor;
    switch (tag) {
      case arrayBufferTag$2:
        return cloneArrayBuffer(object);
      case boolTag$2:
      case dateTag$2:
        return new Ctor(+object);
      case dataViewTag$2:
        return cloneDataView(object, isDeep);
      case float32Tag$1:
      case float64Tag$1:
      case int8Tag$1:
      case int16Tag$1:
      case int32Tag$1:
      case uint8Tag$1:
      case uint8ClampedTag$1:
      case uint16Tag$1:
      case uint32Tag$1:
        return cloneTypedArray(object, isDeep);
      case mapTag$3:
        return new Ctor();
      case numberTag$2:
      case stringTag$2:
        return new Ctor(object);
      case regexpTag$2:
        return cloneRegExp(object);
      case setTag$3:
        return new Ctor();
      case symbolTag$2:
        return cloneSymbol(object);
    }
  }
  function initCloneObject(object) {
    return typeof object.constructor == "function" && !isPrototype(object) ? baseCreate$1(getPrototype$1(object)) : {};
  }
  var mapTag$2 = "[object Map]";
  function baseIsMap(value) {
    return isObjectLike(value) && getTag$1(value) == mapTag$2;
  }
  var nodeIsMap = nodeUtil$1 && nodeUtil$1.isMap;
  var isMap = nodeIsMap ? baseUnary(nodeIsMap) : baseIsMap;
  var isMap$1 = isMap;
  var setTag$2 = "[object Set]";
  function baseIsSet(value) {
    return isObjectLike(value) && getTag$1(value) == setTag$2;
  }
  var nodeIsSet = nodeUtil$1 && nodeUtil$1.isSet;
  var isSet = nodeIsSet ? baseUnary(nodeIsSet) : baseIsSet;
  var isSet$1 = isSet;
  var CLONE_DEEP_FLAG$2 = 1, CLONE_FLAT_FLAG$1 = 2, CLONE_SYMBOLS_FLAG$2 = 4;
  var argsTag$1 = "[object Arguments]", arrayTag$1 = "[object Array]", boolTag$1 = "[object Boolean]", dateTag$1 = "[object Date]", errorTag$1 = "[object Error]", funcTag = "[object Function]", genTag = "[object GeneratorFunction]", mapTag$1 = "[object Map]", numberTag$1 = "[object Number]", objectTag$1 = "[object Object]", regexpTag$1 = "[object RegExp]", setTag$1 = "[object Set]", stringTag$1 = "[object String]", symbolTag$1 = "[object Symbol]", weakMapTag = "[object WeakMap]";
  var arrayBufferTag$1 = "[object ArrayBuffer]", dataViewTag$1 = "[object DataView]", float32Tag = "[object Float32Array]", float64Tag = "[object Float64Array]", int8Tag = "[object Int8Array]", int16Tag = "[object Int16Array]", int32Tag = "[object Int32Array]", uint8Tag = "[object Uint8Array]", uint8ClampedTag = "[object Uint8ClampedArray]", uint16Tag = "[object Uint16Array]", uint32Tag = "[object Uint32Array]";
  var cloneableTags = {};
  cloneableTags[argsTag$1] = cloneableTags[arrayTag$1] = cloneableTags[arrayBufferTag$1] = cloneableTags[dataViewTag$1] = cloneableTags[boolTag$1] = cloneableTags[dateTag$1] = cloneableTags[float32Tag] = cloneableTags[float64Tag] = cloneableTags[int8Tag] = cloneableTags[int16Tag] = cloneableTags[int32Tag] = cloneableTags[mapTag$1] = cloneableTags[numberTag$1] = cloneableTags[objectTag$1] = cloneableTags[regexpTag$1] = cloneableTags[setTag$1] = cloneableTags[stringTag$1] = cloneableTags[symbolTag$1] = cloneableTags[uint8Tag] = cloneableTags[uint8ClampedTag] = cloneableTags[uint16Tag] = cloneableTags[uint32Tag] = true;
  cloneableTags[errorTag$1] = cloneableTags[funcTag] = cloneableTags[weakMapTag] = false;
  function baseClone(value, bitmask, customizer, key, object, stack) {
    var result, isDeep = bitmask & CLONE_DEEP_FLAG$2, isFlat = bitmask & CLONE_FLAT_FLAG$1, isFull = bitmask & CLONE_SYMBOLS_FLAG$2;
    if (customizer) {
      result = object ? customizer(value, key, object, stack) : customizer(value);
    }
    if (result !== void 0) {
      return result;
    }
    if (!isObject$1(value)) {
      return value;
    }
    var isArr = isArray$2(value);
    if (isArr) {
      result = initCloneArray(value);
      if (!isDeep) {
        return copyArray(value, result);
      }
    } else {
      var tag = getTag$1(value), isFunc = tag == funcTag || tag == genTag;
      if (isBuffer$2(value)) {
        return cloneBuffer(value, isDeep);
      }
      if (tag == objectTag$1 || tag == argsTag$1 || isFunc && !object) {
        result = isFlat || isFunc ? {} : initCloneObject(value);
        if (!isDeep) {
          return isFlat ? copySymbolsIn(value, baseAssignIn(result, value)) : copySymbols(value, baseAssign(result, value));
        }
      } else {
        if (!cloneableTags[tag]) {
          return object ? value : {};
        }
        result = initCloneByTag(value, tag, isDeep);
      }
    }
    stack || (stack = new Stack());
    var stacked = stack.get(value);
    if (stacked) {
      return stacked;
    }
    stack.set(value, result);
    if (isSet$1(value)) {
      value.forEach(function(subValue) {
        result.add(baseClone(subValue, bitmask, customizer, subValue, value, stack));
      });
    } else if (isMap$1(value)) {
      value.forEach(function(subValue, key2) {
        result.set(key2, baseClone(subValue, bitmask, customizer, key2, value, stack));
      });
    }
    var keysFunc = isFull ? isFlat ? getAllKeysIn : getAllKeys : isFlat ? keysIn : keys;
    var props = isArr ? void 0 : keysFunc(value);
    arrayEach(props || value, function(subValue, key2) {
      if (props) {
        key2 = subValue;
        subValue = value[key2];
      }
      assignValue(result, key2, baseClone(subValue, bitmask, customizer, key2, value, stack));
    });
    return result;
  }
  var CLONE_DEEP_FLAG$1 = 1, CLONE_SYMBOLS_FLAG$1 = 4;
  function cloneDeep(value) {
    return baseClone(value, CLONE_DEEP_FLAG$1 | CLONE_SYMBOLS_FLAG$1);
  }
  var HASH_UNDEFINED = "__lodash_hash_undefined__";
  function setCacheAdd(value) {
    this.__data__.set(value, HASH_UNDEFINED);
    return this;
  }
  function setCacheHas(value) {
    return this.__data__.has(value);
  }
  function SetCache(values) {
    var index2 = -1, length = values == null ? 0 : values.length;
    this.__data__ = new MapCache();
    while (++index2 < length) {
      this.add(values[index2]);
    }
  }
  SetCache.prototype.add = SetCache.prototype.push = setCacheAdd;
  SetCache.prototype.has = setCacheHas;
  function arraySome(array, predicate) {
    var index2 = -1, length = array == null ? 0 : array.length;
    while (++index2 < length) {
      if (predicate(array[index2], index2, array)) {
        return true;
      }
    }
    return false;
  }
  function cacheHas(cache2, key) {
    return cache2.has(key);
  }
  var COMPARE_PARTIAL_FLAG$5 = 1, COMPARE_UNORDERED_FLAG$3 = 2;
  function equalArrays(array, other, bitmask, customizer, equalFunc, stack) {
    var isPartial = bitmask & COMPARE_PARTIAL_FLAG$5, arrLength = array.length, othLength = other.length;
    if (arrLength != othLength && !(isPartial && othLength > arrLength)) {
      return false;
    }
    var arrStacked = stack.get(array);
    var othStacked = stack.get(other);
    if (arrStacked && othStacked) {
      return arrStacked == other && othStacked == array;
    }
    var index2 = -1, result = true, seen = bitmask & COMPARE_UNORDERED_FLAG$3 ? new SetCache() : void 0;
    stack.set(array, other);
    stack.set(other, array);
    while (++index2 < arrLength) {
      var arrValue = array[index2], othValue = other[index2];
      if (customizer) {
        var compared = isPartial ? customizer(othValue, arrValue, index2, other, array, stack) : customizer(arrValue, othValue, index2, array, other, stack);
      }
      if (compared !== void 0) {
        if (compared) {
          continue;
        }
        result = false;
        break;
      }
      if (seen) {
        if (!arraySome(other, function(othValue2, othIndex) {
          if (!cacheHas(seen, othIndex) && (arrValue === othValue2 || equalFunc(arrValue, othValue2, bitmask, customizer, stack))) {
            return seen.push(othIndex);
          }
        })) {
          result = false;
          break;
        }
      } else if (!(arrValue === othValue || equalFunc(arrValue, othValue, bitmask, customizer, stack))) {
        result = false;
        break;
      }
    }
    stack["delete"](array);
    stack["delete"](other);
    return result;
  }
  function mapToArray(map) {
    var index2 = -1, result = Array(map.size);
    map.forEach(function(value, key) {
      result[++index2] = [key, value];
    });
    return result;
  }
  function setToArray(set2) {
    var index2 = -1, result = Array(set2.size);
    set2.forEach(function(value) {
      result[++index2] = value;
    });
    return result;
  }
  var COMPARE_PARTIAL_FLAG$4 = 1, COMPARE_UNORDERED_FLAG$2 = 2;
  var boolTag = "[object Boolean]", dateTag = "[object Date]", errorTag = "[object Error]", mapTag = "[object Map]", numberTag = "[object Number]", regexpTag = "[object RegExp]", setTag = "[object Set]", stringTag = "[object String]", symbolTag = "[object Symbol]";
  var arrayBufferTag = "[object ArrayBuffer]", dataViewTag = "[object DataView]";
  var symbolProto = Symbol$2 ? Symbol$2.prototype : void 0, symbolValueOf = symbolProto ? symbolProto.valueOf : void 0;
  function equalByTag(object, other, tag, bitmask, customizer, equalFunc, stack) {
    switch (tag) {
      case dataViewTag:
        if (object.byteLength != other.byteLength || object.byteOffset != other.byteOffset) {
          return false;
        }
        object = object.buffer;
        other = other.buffer;
      case arrayBufferTag:
        if (object.byteLength != other.byteLength || !equalFunc(new Uint8Array$2(object), new Uint8Array$2(other))) {
          return false;
        }
        return true;
      case boolTag:
      case dateTag:
      case numberTag:
        return eq(+object, +other);
      case errorTag:
        return object.name == other.name && object.message == other.message;
      case regexpTag:
      case stringTag:
        return object == other + "";
      case mapTag:
        var convert = mapToArray;
      case setTag:
        var isPartial = bitmask & COMPARE_PARTIAL_FLAG$4;
        convert || (convert = setToArray);
        if (object.size != other.size && !isPartial) {
          return false;
        }
        var stacked = stack.get(object);
        if (stacked) {
          return stacked == other;
        }
        bitmask |= COMPARE_UNORDERED_FLAG$2;
        stack.set(object, other);
        var result = equalArrays(convert(object), convert(other), bitmask, customizer, equalFunc, stack);
        stack["delete"](object);
        return result;
      case symbolTag:
        if (symbolValueOf) {
          return symbolValueOf.call(object) == symbolValueOf.call(other);
        }
    }
    return false;
  }
  var COMPARE_PARTIAL_FLAG$3 = 1;
  var objectProto$1 = Object.prototype;
  var hasOwnProperty$1 = objectProto$1.hasOwnProperty;
  function equalObjects(object, other, bitmask, customizer, equalFunc, stack) {
    var isPartial = bitmask & COMPARE_PARTIAL_FLAG$3, objProps = getAllKeys(object), objLength = objProps.length, othProps = getAllKeys(other), othLength = othProps.length;
    if (objLength != othLength && !isPartial) {
      return false;
    }
    var index2 = objLength;
    while (index2--) {
      var key = objProps[index2];
      if (!(isPartial ? key in other : hasOwnProperty$1.call(other, key))) {
        return false;
      }
    }
    var objStacked = stack.get(object);
    var othStacked = stack.get(other);
    if (objStacked && othStacked) {
      return objStacked == other && othStacked == object;
    }
    var result = true;
    stack.set(object, other);
    stack.set(other, object);
    var skipCtor = isPartial;
    while (++index2 < objLength) {
      key = objProps[index2];
      var objValue = object[key], othValue = other[key];
      if (customizer) {
        var compared = isPartial ? customizer(othValue, objValue, key, other, object, stack) : customizer(objValue, othValue, key, object, other, stack);
      }
      if (!(compared === void 0 ? objValue === othValue || equalFunc(objValue, othValue, bitmask, customizer, stack) : compared)) {
        result = false;
        break;
      }
      skipCtor || (skipCtor = key == "constructor");
    }
    if (result && !skipCtor) {
      var objCtor = object.constructor, othCtor = other.constructor;
      if (objCtor != othCtor && ("constructor" in object && "constructor" in other) && !(typeof objCtor == "function" && objCtor instanceof objCtor && typeof othCtor == "function" && othCtor instanceof othCtor)) {
        result = false;
      }
    }
    stack["delete"](object);
    stack["delete"](other);
    return result;
  }
  var COMPARE_PARTIAL_FLAG$2 = 1;
  var argsTag = "[object Arguments]", arrayTag = "[object Array]", objectTag = "[object Object]";
  var objectProto = Object.prototype;
  var hasOwnProperty = objectProto.hasOwnProperty;
  function baseIsEqualDeep(object, other, bitmask, customizer, equalFunc, stack) {
    var objIsArr = isArray$2(object), othIsArr = isArray$2(other), objTag = objIsArr ? arrayTag : getTag$1(object), othTag = othIsArr ? arrayTag : getTag$1(other);
    objTag = objTag == argsTag ? objectTag : objTag;
    othTag = othTag == argsTag ? objectTag : othTag;
    var objIsObj = objTag == objectTag, othIsObj = othTag == objectTag, isSameTag = objTag == othTag;
    if (isSameTag && isBuffer$2(object)) {
      if (!isBuffer$2(other)) {
        return false;
      }
      objIsArr = true;
      objIsObj = false;
    }
    if (isSameTag && !objIsObj) {
      stack || (stack = new Stack());
      return objIsArr || isTypedArray$2(object) ? equalArrays(object, other, bitmask, customizer, equalFunc, stack) : equalByTag(object, other, objTag, bitmask, customizer, equalFunc, stack);
    }
    if (!(bitmask & COMPARE_PARTIAL_FLAG$2)) {
      var objIsWrapped = objIsObj && hasOwnProperty.call(object, "__wrapped__"), othIsWrapped = othIsObj && hasOwnProperty.call(other, "__wrapped__");
      if (objIsWrapped || othIsWrapped) {
        var objUnwrapped = objIsWrapped ? object.value() : object, othUnwrapped = othIsWrapped ? other.value() : other;
        stack || (stack = new Stack());
        return equalFunc(objUnwrapped, othUnwrapped, bitmask, customizer, stack);
      }
    }
    if (!isSameTag) {
      return false;
    }
    stack || (stack = new Stack());
    return equalObjects(object, other, bitmask, customizer, equalFunc, stack);
  }
  function baseIsEqual(value, other, bitmask, customizer, stack) {
    if (value === other) {
      return true;
    }
    if (value == null || other == null || !isObjectLike(value) && !isObjectLike(other)) {
      return value !== value && other !== other;
    }
    return baseIsEqualDeep(value, other, bitmask, customizer, baseIsEqual, stack);
  }
  var COMPARE_PARTIAL_FLAG$1 = 1, COMPARE_UNORDERED_FLAG$1 = 2;
  function baseIsMatch(object, source, matchData, customizer) {
    var index2 = matchData.length, length = index2, noCustomizer = !customizer;
    if (object == null) {
      return !length;
    }
    object = Object(object);
    while (index2--) {
      var data2 = matchData[index2];
      if (noCustomizer && data2[2] ? data2[1] !== object[data2[0]] : !(data2[0] in object)) {
        return false;
      }
    }
    while (++index2 < length) {
      data2 = matchData[index2];
      var key = data2[0], objValue = object[key], srcValue = data2[1];
      if (noCustomizer && data2[2]) {
        if (objValue === void 0 && !(key in object)) {
          return false;
        }
      } else {
        var stack = new Stack();
        if (customizer) {
          var result = customizer(objValue, srcValue, key, object, source, stack);
        }
        if (!(result === void 0 ? baseIsEqual(srcValue, objValue, COMPARE_PARTIAL_FLAG$1 | COMPARE_UNORDERED_FLAG$1, customizer, stack) : result)) {
          return false;
        }
      }
    }
    return true;
  }
  function isStrictComparable(value) {
    return value === value && !isObject$1(value);
  }
  function getMatchData(object) {
    var result = keys(object), length = result.length;
    while (length--) {
      var key = result[length], value = object[key];
      result[length] = [key, value, isStrictComparable(value)];
    }
    return result;
  }
  function matchesStrictComparable(key, srcValue) {
    return function(object) {
      if (object == null) {
        return false;
      }
      return object[key] === srcValue && (srcValue !== void 0 || key in Object(object));
    };
  }
  function baseMatches(source) {
    var matchData = getMatchData(source);
    if (matchData.length == 1 && matchData[0][2]) {
      return matchesStrictComparable(matchData[0][0], matchData[0][1]);
    }
    return function(object) {
      return object === source || baseIsMatch(object, source, matchData);
    };
  }
  function baseHasIn(object, key) {
    return object != null && key in Object(object);
  }
  function hasPath(object, path, hasFunc) {
    path = castPath(path, object);
    var index2 = -1, length = path.length, result = false;
    while (++index2 < length) {
      var key = toKey(path[index2]);
      if (!(result = object != null && hasFunc(object, key))) {
        break;
      }
      object = object[key];
    }
    if (result || ++index2 != length) {
      return result;
    }
    length = object == null ? 0 : object.length;
    return !!length && isLength(length) && isIndex(key, length) && (isArray$2(object) || isArguments$1(object));
  }
  function hasIn(object, path) {
    return object != null && hasPath(object, path, baseHasIn);
  }
  var COMPARE_PARTIAL_FLAG = 1, COMPARE_UNORDERED_FLAG = 2;
  function baseMatchesProperty(path, srcValue) {
    if (isKey(path) && isStrictComparable(srcValue)) {
      return matchesStrictComparable(toKey(path), srcValue);
    }
    return function(object) {
      var objValue = get(object, path);
      return objValue === void 0 && objValue === srcValue ? hasIn(object, path) : baseIsEqual(srcValue, objValue, COMPARE_PARTIAL_FLAG | COMPARE_UNORDERED_FLAG);
    };
  }
  function baseProperty(key) {
    return function(object) {
      return object == null ? void 0 : object[key];
    };
  }
  function basePropertyDeep(path) {
    return function(object) {
      return baseGet(object, path);
    };
  }
  function property(path) {
    return isKey(path) ? baseProperty(toKey(path)) : basePropertyDeep(path);
  }
  function baseIteratee(value) {
    if (typeof value == "function") {
      return value;
    }
    if (value == null) {
      return identity;
    }
    if (typeof value == "object") {
      return isArray$2(value) ? baseMatchesProperty(value[0], value[1]) : baseMatches(value);
    }
    return property(value);
  }
  function createBaseFor(fromRight) {
    return function(object, iteratee, keysFunc) {
      var index2 = -1, iterable = Object(object), props = keysFunc(object), length = props.length;
      while (length--) {
        var key = props[fromRight ? length : ++index2];
        if (iteratee(iterable[key], key, iterable) === false) {
          break;
        }
      }
      return object;
    };
  }
  var baseFor = createBaseFor();
  var baseFor$1 = baseFor;
  function baseForOwn(object, iteratee) {
    return object && baseFor$1(object, iteratee, keys);
  }
  function createBaseEach(eachFunc, fromRight) {
    return function(collection, iteratee) {
      if (collection == null) {
        return collection;
      }
      if (!isArrayLike(collection)) {
        return eachFunc(collection, iteratee);
      }
      var length = collection.length, index2 = fromRight ? length : -1, iterable = Object(collection);
      while (fromRight ? index2-- : ++index2 < length) {
        if (iteratee(iterable[index2], index2, iterable) === false) {
          break;
        }
      }
      return collection;
    };
  }
  var baseEach = createBaseEach(baseForOwn);
  var baseEach$1 = baseEach;
  var now = function() {
    return root$1.Date.now();
  };
  var now$1 = now;
  var FUNC_ERROR_TEXT = "Expected a function";
  var nativeMax = Math.max, nativeMin = Math.min;
  function debounce$1(func, wait, options2) {
    var lastArgs, lastThis, maxWait, result, timerId, lastCallTime, lastInvokeTime = 0, leading = false, maxing = false, trailing = true;
    if (typeof func != "function") {
      throw new TypeError(FUNC_ERROR_TEXT);
    }
    wait = toNumber(wait) || 0;
    if (isObject$1(options2)) {
      leading = !!options2.leading;
      maxing = "maxWait" in options2;
      maxWait = maxing ? nativeMax(toNumber(options2.maxWait) || 0, wait) : maxWait;
      trailing = "trailing" in options2 ? !!options2.trailing : trailing;
    }
    function invokeFunc(time) {
      var args = lastArgs, thisArg = lastThis;
      lastArgs = lastThis = void 0;
      lastInvokeTime = time;
      result = func.apply(thisArg, args);
      return result;
    }
    function leadingEdge(time) {
      lastInvokeTime = time;
      timerId = setTimeout(timerExpired, wait);
      return leading ? invokeFunc(time) : result;
    }
    function remainingWait(time) {
      var timeSinceLastCall = time - lastCallTime, timeSinceLastInvoke = time - lastInvokeTime, timeWaiting = wait - timeSinceLastCall;
      return maxing ? nativeMin(timeWaiting, maxWait - timeSinceLastInvoke) : timeWaiting;
    }
    function shouldInvoke(time) {
      var timeSinceLastCall = time - lastCallTime, timeSinceLastInvoke = time - lastInvokeTime;
      return lastCallTime === void 0 || timeSinceLastCall >= wait || timeSinceLastCall < 0 || maxing && timeSinceLastInvoke >= maxWait;
    }
    function timerExpired() {
      var time = now$1();
      if (shouldInvoke(time)) {
        return trailingEdge(time);
      }
      timerId = setTimeout(timerExpired, remainingWait(time));
    }
    function trailingEdge(time) {
      timerId = void 0;
      if (trailing && lastArgs) {
        return invokeFunc(time);
      }
      lastArgs = lastThis = void 0;
      return result;
    }
    function cancel() {
      if (timerId !== void 0) {
        clearTimeout(timerId);
      }
      lastInvokeTime = 0;
      lastArgs = lastCallTime = lastThis = timerId = void 0;
    }
    function flush() {
      return timerId === void 0 ? result : trailingEdge(now$1());
    }
    function debounced() {
      var time = now$1(), isInvoking = shouldInvoke(time);
      lastArgs = arguments;
      lastThis = this;
      lastCallTime = time;
      if (isInvoking) {
        if (timerId === void 0) {
          return leadingEdge(lastCallTime);
        }
        if (maxing) {
          clearTimeout(timerId);
          timerId = setTimeout(timerExpired, wait);
          return invokeFunc(lastCallTime);
        }
      }
      if (timerId === void 0) {
        timerId = setTimeout(timerExpired, wait);
      }
      return result;
    }
    debounced.cancel = cancel;
    debounced.flush = flush;
    return debounced;
  }
  function assignMergeValue(object, key, value) {
    if (value !== void 0 && !eq(object[key], value) || value === void 0 && !(key in object)) {
      baseAssignValue(object, key, value);
    }
  }
  function isArrayLikeObject(value) {
    return isObjectLike(value) && isArrayLike(value);
  }
  function safeGet(object, key) {
    if (key === "constructor" && typeof object[key] === "function") {
      return;
    }
    if (key == "__proto__") {
      return;
    }
    return object[key];
  }
  function toPlainObject(value) {
    return copyObject(value, keysIn(value));
  }
  function baseMergeDeep(object, source, key, srcIndex, mergeFunc, customizer, stack) {
    var objValue = safeGet(object, key), srcValue = safeGet(source, key), stacked = stack.get(srcValue);
    if (stacked) {
      assignMergeValue(object, key, stacked);
      return;
    }
    var newValue = customizer ? customizer(objValue, srcValue, key + "", object, source, stack) : void 0;
    var isCommon = newValue === void 0;
    if (isCommon) {
      var isArr = isArray$2(srcValue), isBuff = !isArr && isBuffer$2(srcValue), isTyped = !isArr && !isBuff && isTypedArray$2(srcValue);
      newValue = srcValue;
      if (isArr || isBuff || isTyped) {
        if (isArray$2(objValue)) {
          newValue = objValue;
        } else if (isArrayLikeObject(objValue)) {
          newValue = copyArray(objValue);
        } else if (isBuff) {
          isCommon = false;
          newValue = cloneBuffer(srcValue, true);
        } else if (isTyped) {
          isCommon = false;
          newValue = cloneTypedArray(srcValue, true);
        } else {
          newValue = [];
        }
      } else if (isPlainObject$1(srcValue) || isArguments$1(srcValue)) {
        newValue = objValue;
        if (isArguments$1(objValue)) {
          newValue = toPlainObject(objValue);
        } else if (!isObject$1(objValue) || isFunction$1(objValue)) {
          newValue = initCloneObject(srcValue);
        }
      } else {
        isCommon = false;
      }
    }
    if (isCommon) {
      stack.set(srcValue, newValue);
      mergeFunc(newValue, srcValue, srcIndex, customizer, stack);
      stack["delete"](srcValue);
    }
    assignMergeValue(object, key, newValue);
  }
  function baseMerge(object, source, srcIndex, customizer, stack) {
    if (object === source) {
      return;
    }
    baseFor$1(source, function(srcValue, key) {
      stack || (stack = new Stack());
      if (isObject$1(srcValue)) {
        baseMergeDeep(object, source, key, srcIndex, baseMerge, customizer, stack);
      } else {
        var newValue = customizer ? customizer(safeGet(object, key), srcValue, key + "", object, source, stack) : void 0;
        if (newValue === void 0) {
          newValue = srcValue;
        }
        assignMergeValue(object, key, newValue);
      }
    }, keysIn);
  }
  function arrayIncludesWith(array, value, comparator) {
    var index2 = -1, length = array == null ? 0 : array.length;
    while (++index2 < length) {
      if (comparator(value, array[index2])) {
        return true;
      }
    }
    return false;
  }
  function last(array) {
    var length = array == null ? 0 : array.length;
    return length ? array[length - 1] : void 0;
  }
  function castFunction(value) {
    return typeof value == "function" ? value : identity;
  }
  function forEach$1(collection, iteratee) {
    var func = isArray$2(collection) ? arrayEach : baseEach$1;
    return func(collection, castFunction(iteratee));
  }
  function baseMap(collection, iteratee) {
    var index2 = -1, result = isArrayLike(collection) ? Array(collection.length) : [];
    baseEach$1(collection, function(value, key, collection2) {
      result[++index2] = iteratee(value, key, collection2);
    });
    return result;
  }
  function parent(object, path) {
    return path.length < 2 ? object : baseGet(object, baseSlice(path, 0, -1));
  }
  var kebabCase = createCompounder(function(result, word, index2) {
    return result + (index2 ? "-" : "") + word.toLowerCase();
  });
  var kebabCase$1 = kebabCase;
  var merge$1 = createAssigner(function(object, source, srcIndex) {
    baseMerge(object, source, srcIndex);
  });
  var merge$2 = merge$1;
  function baseUnset(object, path) {
    path = castPath(path, object);
    object = parent(object, path);
    return object == null || delete object[toKey(last(path))];
  }
  function customOmitClone(value) {
    return isPlainObject$1(value) ? void 0 : value;
  }
  var CLONE_DEEP_FLAG = 1, CLONE_FLAT_FLAG = 2, CLONE_SYMBOLS_FLAG = 4;
  var omit = flatRest(function(object, paths) {
    var result = {};
    if (object == null) {
      return result;
    }
    var isDeep = false;
    paths = arrayMap(paths, function(path) {
      path = castPath(path, object);
      isDeep || (isDeep = path.length > 1);
      return path;
    });
    copyObject(object, getAllKeysIn(object), result);
    if (isDeep) {
      result = baseClone(result, CLONE_DEEP_FLAG | CLONE_FLAT_FLAG | CLONE_SYMBOLS_FLAG, customOmitClone);
    }
    var length = paths.length;
    while (length--) {
      baseUnset(result, paths[length]);
    }
    return result;
  });
  var omit$1 = omit;
  function baseSet(object, path, value, customizer) {
    if (!isObject$1(object)) {
      return object;
    }
    path = castPath(path, object);
    var index2 = -1, length = path.length, lastIndex = length - 1, nested = object;
    while (nested != null && ++index2 < length) {
      var key = toKey(path[index2]), newValue = value;
      if (key === "__proto__" || key === "constructor" || key === "prototype") {
        return object;
      }
      if (index2 != lastIndex) {
        var objValue = nested[key];
        newValue = customizer ? customizer(objValue, key, nested) : void 0;
        if (newValue === void 0) {
          newValue = isObject$1(objValue) ? objValue : isIndex(path[index2 + 1]) ? [] : {};
        }
      }
      assignValue(nested, key, newValue);
      nested = nested[key];
    }
    return object;
  }
  function baseSortBy(array, comparer) {
    var length = array.length;
    array.sort(comparer);
    while (length--) {
      array[length] = array[length].value;
    }
    return array;
  }
  function compareAscending(value, other) {
    if (value !== other) {
      var valIsDefined = value !== void 0, valIsNull = value === null, valIsReflexive = value === value, valIsSymbol = isSymbol(value);
      var othIsDefined = other !== void 0, othIsNull = other === null, othIsReflexive = other === other, othIsSymbol = isSymbol(other);
      if (!othIsNull && !othIsSymbol && !valIsSymbol && value > other || valIsSymbol && othIsDefined && othIsReflexive && !othIsNull && !othIsSymbol || valIsNull && othIsDefined && othIsReflexive || !valIsDefined && othIsReflexive || !valIsReflexive) {
        return 1;
      }
      if (!valIsNull && !valIsSymbol && !othIsSymbol && value < other || othIsSymbol && valIsDefined && valIsReflexive && !valIsNull && !valIsSymbol || othIsNull && valIsDefined && valIsReflexive || !othIsDefined && valIsReflexive || !othIsReflexive) {
        return -1;
      }
    }
    return 0;
  }
  function compareMultiple(object, other, orders) {
    var index2 = -1, objCriteria = object.criteria, othCriteria = other.criteria, length = objCriteria.length, ordersLength = orders.length;
    while (++index2 < length) {
      var result = compareAscending(objCriteria[index2], othCriteria[index2]);
      if (result) {
        if (index2 >= ordersLength) {
          return result;
        }
        var order2 = orders[index2];
        return result * (order2 == "desc" ? -1 : 1);
      }
    }
    return object.index - other.index;
  }
  function baseOrderBy(collection, iteratees, orders) {
    if (iteratees.length) {
      iteratees = arrayMap(iteratees, function(iteratee) {
        if (isArray$2(iteratee)) {
          return function(value) {
            return baseGet(value, iteratee.length === 1 ? iteratee[0] : iteratee);
          };
        }
        return iteratee;
      });
    } else {
      iteratees = [identity];
    }
    var index2 = -1;
    iteratees = arrayMap(iteratees, baseUnary(baseIteratee));
    var result = baseMap(collection, function(value, key, collection2) {
      var criteria = arrayMap(iteratees, function(iteratee) {
        return iteratee(value);
      });
      return { "criteria": criteria, "index": ++index2, "value": value };
    });
    return baseSortBy(result, function(object, other) {
      return compareMultiple(object, other, orders);
    });
  }
  function orderBy(collection, iteratees, orders, guard) {
    if (collection == null) {
      return [];
    }
    if (!isArray$2(iteratees)) {
      iteratees = iteratees == null ? [] : [iteratees];
    }
    orders = guard ? void 0 : orders;
    if (!isArray$2(orders)) {
      orders = orders == null ? [] : [orders];
    }
    return baseOrderBy(collection, iteratees, orders);
  }
  function set(object, path, value) {
    return object == null ? object : baseSet(object, path, value);
  }
  var startCase = createCompounder(function(result, word, index2) {
    return result + (index2 ? " " : "") + upperFirst$1(word);
  });
  var startCase$1 = startCase;
  var INFINITY = 1 / 0;
  var createSet = !(Set$2 && 1 / setToArray(new Set$2([, -0]))[1] == INFINITY) ? noop : function(values) {
    return new Set$2(values);
  };
  var createSet$1 = createSet;
  var LARGE_ARRAY_SIZE = 200;
  function baseUniq(array, iteratee, comparator) {
    var index2 = -1, includes = arrayIncludes, length = array.length, isCommon = true, result = [], seen = result;
    if (comparator) {
      isCommon = false;
      includes = arrayIncludesWith;
    } else if (length >= LARGE_ARRAY_SIZE) {
      var set2 = iteratee ? null : createSet$1(array);
      if (set2) {
        return setToArray(set2);
      }
      isCommon = false;
      includes = cacheHas;
      seen = new SetCache();
    } else {
      seen = iteratee ? [] : result;
    }
    outer:
      while (++index2 < length) {
        var value = array[index2], computed = iteratee ? iteratee(value) : value;
        value = comparator || value !== 0 ? value : 0;
        if (isCommon && computed === computed) {
          var seenIndex = seen.length;
          while (seenIndex--) {
            if (seen[seenIndex] === computed) {
              continue outer;
            }
          }
          if (iteratee) {
            seen.push(computed);
          }
          result.push(value);
        } else if (!includes(seen, computed, comparator)) {
          if (seen !== result) {
            seen.push(computed);
          }
          result.push(value);
        }
      }
    return result;
  }
  var unionBy = baseRest(function(arrays) {
    var iteratee = last(arrays);
    if (isArrayLikeObject(iteratee)) {
      iteratee = void 0;
    }
    return baseUniq(baseFlatten(arrays, 1, isArrayLikeObject, true), baseIteratee(iteratee));
  });
  var unionBy$1 = unionBy;
  function unset(object, path) {
    return object == null ? true : baseUnset(object, path);
  }
  function baseUpdate(object, path, updater, customizer) {
    return baseSet(object, path, updater(baseGet(object, path)), customizer);
  }
  function update(object, path, updater) {
    return object == null ? object : baseUpdate(object, path, castFunction(updater));
  }
  var GradientBoard_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$13 = {
    key: 0,
    class: "znpb-gradient-radial-wrapper"
  };
  const __default__$Y = {
    name: "GradientBoard"
  };
  const _sfc_main$1q = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$Y), {
    props: {
      config: null,
      activegrad: null
    },
    emits: ["change-active-gradient", "position-changed"],
    setup(__props, { emit }) {
      const props = __props;
      const gradboard = vue.ref(null);
      const rafMovePosition = rafSchd$1(onCircleDrag);
      const rafEndDragging = rafSchd$1(disableDragging);
      const radialArr = vue.computed({
        get() {
          return props.config.filter((gradient) => gradient.type === "radial");
        },
        set(newArr) {
          radialArr.value = newArr;
        }
      });
      function enableDragging(gradient) {
        document.addEventListener("mousemove", rafMovePosition);
        document.addEventListener("mouseup", rafEndDragging);
        document.body.style.userSelect = "none";
        const activeGradientIndex = props.config.indexOf(gradient);
        emit("change-active-gradient", activeGradientIndex);
      }
      function disableDragging() {
        document.removeEventListener("mousemove", rafMovePosition);
        document.removeEventListener("mouseup", rafEndDragging);
        document.body.style.userSelect = "";
      }
      function onCircleDrag(event2) {
        const gradBoard = gradboard.value.getBoundingClientRect();
        const newLeft = clamp((event2.clientX - gradBoard.left) * 100 / gradBoard.width, 0, 100);
        const newTop = clamp((event2.clientY - gradBoard.top) * 100 / gradBoard.height, 0, 100);
        emit("position-changed", {
          x: Math.round(newLeft),
          y: Math.round(newTop)
        });
      }
      vue.onBeforeUnmount(() => {
        disableDragging();
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          ref_key: "gradboard",
          ref: gradboard,
          class: "znpb-gradient-wrapper__board"
        }, [
          vue.createVNode(_sfc_main$1s, { config: __props.config }, null, 8, ["config"]),
          vue.unref(radialArr) != null ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_1$13, [
            (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(radialArr), (gradient, index2) => {
              return vue.openBlock(), vue.createBlock(GradientRadialDragger, {
                key: gradient.type + index2,
                position: gradient.position,
                active: __props.activegrad === gradient,
                onMousedown: ($event) => enableDragging(gradient)
              }, null, 8, ["position", "active", "onMousedown"]);
            }), 128))
          ])) : vue.createCommentVNode("", true)
        ], 512);
      };
    }
  }));
  var GradientBarPreview_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$12 = ["title"];
  const _hoisted_2$H = { class: "znpb-gradient-preview-transparent" };
  const __default__$X = {
    name: "GradientBarPreview"
  };
  const _sfc_main$1p = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$X), {
    props: {
      config: null
    },
    setup(__props) {
      const props = __props;
      const getGradientPreviewStyle = vue.computed(() => {
        const style = {};
        const gradient = [];
        const colors = [];
        const colorsCopy = [...props.config.colors];
        colorsCopy.sort((a, b) => {
          return a.position > b.position ? 1 : -1;
        });
        colorsCopy.forEach((color) => {
          colors.push(`${color.color} ${color.position}%`);
        });
        gradient.push(`linear-gradient(90deg, ${colors.join(", ")})`);
        style["background"] = gradient.join(", ");
        return style;
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          class: "znpb-gradient-preview-transparent-container",
          title: _ctx.$translate("click_to_add_gradient_point")
        }, [
          vue.createElementVNode("div", _hoisted_2$H, [
            vue.createElementVNode("div", {
              class: "znpb-gradient-preview",
              style: vue.normalizeStyle(vue.unref(getGradientPreviewStyle))
            }, null, 4)
          ])
        ], 8, _hoisted_1$12);
      };
    }
  }));
  let defaultOptions = {
    appendTo: "body",
    placement: "top"
  };
  const getDefaultOptions = () => {
    return defaultOptions;
  };
  var top = "top";
  var bottom = "bottom";
  var right = "right";
  var left = "left";
  var auto = "auto";
  var basePlacements = [top, bottom, right, left];
  var start = "start";
  var end = "end";
  var clippingParents = "clippingParents";
  var viewport = "viewport";
  var popper = "popper";
  var reference = "reference";
  var variationPlacements = /* @__PURE__ */ basePlacements.reduce(function(acc, placement) {
    return acc.concat([placement + "-" + start, placement + "-" + end]);
  }, []);
  var placements = /* @__PURE__ */ [].concat(basePlacements, [auto]).reduce(function(acc, placement) {
    return acc.concat([placement, placement + "-" + start, placement + "-" + end]);
  }, []);
  var beforeRead = "beforeRead";
  var read = "read";
  var afterRead = "afterRead";
  var beforeMain = "beforeMain";
  var main = "main";
  var afterMain = "afterMain";
  var beforeWrite = "beforeWrite";
  var write = "write";
  var afterWrite = "afterWrite";
  var modifierPhases = [beforeRead, read, afterRead, beforeMain, main, afterMain, beforeWrite, write, afterWrite];
  function getNodeName(element) {
    return element ? (element.nodeName || "").toLowerCase() : null;
  }
  function getWindow(node) {
    if (node == null) {
      return window;
    }
    if (node.toString() !== "[object Window]") {
      var ownerDocument = node.ownerDocument;
      return ownerDocument ? ownerDocument.defaultView || window : window;
    }
    return node;
  }
  function isElement(node) {
    var OwnElement = getWindow(node).Element;
    return node instanceof OwnElement || node instanceof Element;
  }
  function isHTMLElement(node) {
    var OwnElement = getWindow(node).HTMLElement;
    return node instanceof OwnElement || node instanceof HTMLElement;
  }
  function isShadowRoot(node) {
    if (typeof ShadowRoot === "undefined") {
      return false;
    }
    var OwnElement = getWindow(node).ShadowRoot;
    return node instanceof OwnElement || node instanceof ShadowRoot;
  }
  function applyStyles(_ref) {
    var state = _ref.state;
    Object.keys(state.elements).forEach(function(name) {
      var style = state.styles[name] || {};
      var attributes = state.attributes[name] || {};
      var element = state.elements[name];
      if (!isHTMLElement(element) || !getNodeName(element)) {
        return;
      }
      Object.assign(element.style, style);
      Object.keys(attributes).forEach(function(name2) {
        var value = attributes[name2];
        if (value === false) {
          element.removeAttribute(name2);
        } else {
          element.setAttribute(name2, value === true ? "" : value);
        }
      });
    });
  }
  function effect$2(_ref2) {
    var state = _ref2.state;
    var initialStyles = {
      popper: {
        position: state.options.strategy,
        left: "0",
        top: "0",
        margin: "0"
      },
      arrow: {
        position: "absolute"
      },
      reference: {}
    };
    Object.assign(state.elements.popper.style, initialStyles.popper);
    state.styles = initialStyles;
    if (state.elements.arrow) {
      Object.assign(state.elements.arrow.style, initialStyles.arrow);
    }
    return function() {
      Object.keys(state.elements).forEach(function(name) {
        var element = state.elements[name];
        var attributes = state.attributes[name] || {};
        var styleProperties = Object.keys(state.styles.hasOwnProperty(name) ? state.styles[name] : initialStyles[name]);
        var style = styleProperties.reduce(function(style2, property2) {
          style2[property2] = "";
          return style2;
        }, {});
        if (!isHTMLElement(element) || !getNodeName(element)) {
          return;
        }
        Object.assign(element.style, style);
        Object.keys(attributes).forEach(function(attribute) {
          element.removeAttribute(attribute);
        });
      });
    };
  }
  var applyStyles$1 = {
    name: "applyStyles",
    enabled: true,
    phase: "write",
    fn: applyStyles,
    effect: effect$2,
    requires: ["computeStyles"]
  };
  function getBasePlacement(placement) {
    return placement.split("-")[0];
  }
  var max = Math.max;
  var min = Math.min;
  var round = Math.round;
  function getUAString() {
    var uaData = navigator.userAgentData;
    if (uaData != null && uaData.brands) {
      return uaData.brands.map(function(item) {
        return item.brand + "/" + item.version;
      }).join(" ");
    }
    return navigator.userAgent;
  }
  function isLayoutViewport() {
    return !/^((?!chrome|android).)*safari/i.test(getUAString());
  }
  function getBoundingClientRect(element, includeScale, isFixedStrategy) {
    if (includeScale === void 0) {
      includeScale = false;
    }
    if (isFixedStrategy === void 0) {
      isFixedStrategy = false;
    }
    var clientRect = element.getBoundingClientRect();
    var scaleX = 1;
    var scaleY = 1;
    if (includeScale && isHTMLElement(element)) {
      scaleX = element.offsetWidth > 0 ? round(clientRect.width) / element.offsetWidth || 1 : 1;
      scaleY = element.offsetHeight > 0 ? round(clientRect.height) / element.offsetHeight || 1 : 1;
    }
    var _ref = isElement(element) ? getWindow(element) : window, visualViewport = _ref.visualViewport;
    var addVisualOffsets = !isLayoutViewport() && isFixedStrategy;
    var x = (clientRect.left + (addVisualOffsets && visualViewport ? visualViewport.offsetLeft : 0)) / scaleX;
    var y = (clientRect.top + (addVisualOffsets && visualViewport ? visualViewport.offsetTop : 0)) / scaleY;
    var width = clientRect.width / scaleX;
    var height = clientRect.height / scaleY;
    return {
      width,
      height,
      top: y,
      right: x + width,
      bottom: y + height,
      left: x,
      x,
      y
    };
  }
  function getLayoutRect(element) {
    var clientRect = getBoundingClientRect(element);
    var width = element.offsetWidth;
    var height = element.offsetHeight;
    if (Math.abs(clientRect.width - width) <= 1) {
      width = clientRect.width;
    }
    if (Math.abs(clientRect.height - height) <= 1) {
      height = clientRect.height;
    }
    return {
      x: element.offsetLeft,
      y: element.offsetTop,
      width,
      height
    };
  }
  function contains(parent2, child) {
    var rootNode = child.getRootNode && child.getRootNode();
    if (parent2.contains(child)) {
      return true;
    } else if (rootNode && isShadowRoot(rootNode)) {
      var next = child;
      do {
        if (next && parent2.isSameNode(next)) {
          return true;
        }
        next = next.parentNode || next.host;
      } while (next);
    }
    return false;
  }
  function getComputedStyle(element) {
    return getWindow(element).getComputedStyle(element);
  }
  function isTableElement(element) {
    return ["table", "td", "th"].indexOf(getNodeName(element)) >= 0;
  }
  function getDocumentElement(element) {
    return ((isElement(element) ? element.ownerDocument : element.document) || window.document).documentElement;
  }
  function getParentNode(element) {
    if (getNodeName(element) === "html") {
      return element;
    }
    return element.assignedSlot || element.parentNode || (isShadowRoot(element) ? element.host : null) || getDocumentElement(element);
  }
  function getTrueOffsetParent(element) {
    if (!isHTMLElement(element) || getComputedStyle(element).position === "fixed") {
      return null;
    }
    return element.offsetParent;
  }
  function getContainingBlock(element) {
    var isFirefox = /firefox/i.test(getUAString());
    var isIE = /Trident/i.test(getUAString());
    if (isIE && isHTMLElement(element)) {
      var elementCss = getComputedStyle(element);
      if (elementCss.position === "fixed") {
        return null;
      }
    }
    var currentNode = getParentNode(element);
    if (isShadowRoot(currentNode)) {
      currentNode = currentNode.host;
    }
    while (isHTMLElement(currentNode) && ["html", "body"].indexOf(getNodeName(currentNode)) < 0) {
      var css = getComputedStyle(currentNode);
      if (css.transform !== "none" || css.perspective !== "none" || css.contain === "paint" || ["transform", "perspective"].indexOf(css.willChange) !== -1 || isFirefox && css.willChange === "filter" || isFirefox && css.filter && css.filter !== "none") {
        return currentNode;
      } else {
        currentNode = currentNode.parentNode;
      }
    }
    return null;
  }
  function getOffsetParent(element) {
    var window2 = getWindow(element);
    var offsetParent = getTrueOffsetParent(element);
    while (offsetParent && isTableElement(offsetParent) && getComputedStyle(offsetParent).position === "static") {
      offsetParent = getTrueOffsetParent(offsetParent);
    }
    if (offsetParent && (getNodeName(offsetParent) === "html" || getNodeName(offsetParent) === "body" && getComputedStyle(offsetParent).position === "static")) {
      return window2;
    }
    return offsetParent || getContainingBlock(element) || window2;
  }
  function getMainAxisFromPlacement(placement) {
    return ["top", "bottom"].indexOf(placement) >= 0 ? "x" : "y";
  }
  function within(min$1, value, max$1) {
    return max(min$1, min(value, max$1));
  }
  function withinMaxClamp(min2, value, max2) {
    var v = within(min2, value, max2);
    return v > max2 ? max2 : v;
  }
  function getFreshSideObject() {
    return {
      top: 0,
      right: 0,
      bottom: 0,
      left: 0
    };
  }
  function mergePaddingObject(paddingObject) {
    return Object.assign({}, getFreshSideObject(), paddingObject);
  }
  function expandToHashMap(value, keys2) {
    return keys2.reduce(function(hashMap, key) {
      hashMap[key] = value;
      return hashMap;
    }, {});
  }
  var toPaddingObject = function toPaddingObject2(padding, state) {
    padding = typeof padding === "function" ? padding(Object.assign({}, state.rects, {
      placement: state.placement
    })) : padding;
    return mergePaddingObject(typeof padding !== "number" ? padding : expandToHashMap(padding, basePlacements));
  };
  function arrow(_ref) {
    var _state$modifiersData$;
    var state = _ref.state, name = _ref.name, options2 = _ref.options;
    var arrowElement = state.elements.arrow;
    var popperOffsets2 = state.modifiersData.popperOffsets;
    var basePlacement = getBasePlacement(state.placement);
    var axis = getMainAxisFromPlacement(basePlacement);
    var isVertical = [left, right].indexOf(basePlacement) >= 0;
    var len = isVertical ? "height" : "width";
    if (!arrowElement || !popperOffsets2) {
      return;
    }
    var paddingObject = toPaddingObject(options2.padding, state);
    var arrowRect = getLayoutRect(arrowElement);
    var minProp = axis === "y" ? top : left;
    var maxProp = axis === "y" ? bottom : right;
    var endDiff = state.rects.reference[len] + state.rects.reference[axis] - popperOffsets2[axis] - state.rects.popper[len];
    var startDiff = popperOffsets2[axis] - state.rects.reference[axis];
    var arrowOffsetParent = getOffsetParent(arrowElement);
    var clientSize = arrowOffsetParent ? axis === "y" ? arrowOffsetParent.clientHeight || 0 : arrowOffsetParent.clientWidth || 0 : 0;
    var centerToReference = endDiff / 2 - startDiff / 2;
    var min2 = paddingObject[minProp];
    var max2 = clientSize - arrowRect[len] - paddingObject[maxProp];
    var center = clientSize / 2 - arrowRect[len] / 2 + centerToReference;
    var offset2 = within(min2, center, max2);
    var axisProp = axis;
    state.modifiersData[name] = (_state$modifiersData$ = {}, _state$modifiersData$[axisProp] = offset2, _state$modifiersData$.centerOffset = offset2 - center, _state$modifiersData$);
  }
  function effect$1(_ref2) {
    var state = _ref2.state, options2 = _ref2.options;
    var _options$element = options2.element, arrowElement = _options$element === void 0 ? "[data-popper-arrow]" : _options$element;
    if (arrowElement == null) {
      return;
    }
    if (typeof arrowElement === "string") {
      arrowElement = state.elements.popper.querySelector(arrowElement);
      if (!arrowElement) {
        return;
      }
    }
    if (!contains(state.elements.popper, arrowElement)) {
      return;
    }
    state.elements.arrow = arrowElement;
  }
  var arrow$1 = {
    name: "arrow",
    enabled: true,
    phase: "main",
    fn: arrow,
    effect: effect$1,
    requires: ["popperOffsets"],
    requiresIfExists: ["preventOverflow"]
  };
  function getVariation(placement) {
    return placement.split("-")[1];
  }
  var unsetSides = {
    top: "auto",
    right: "auto",
    bottom: "auto",
    left: "auto"
  };
  function roundOffsetsByDPR(_ref) {
    var x = _ref.x, y = _ref.y;
    var win = window;
    var dpr = win.devicePixelRatio || 1;
    return {
      x: round(x * dpr) / dpr || 0,
      y: round(y * dpr) / dpr || 0
    };
  }
  function mapToStyles(_ref2) {
    var _Object$assign2;
    var popper2 = _ref2.popper, popperRect = _ref2.popperRect, placement = _ref2.placement, variation = _ref2.variation, offsets = _ref2.offsets, position = _ref2.position, gpuAcceleration = _ref2.gpuAcceleration, adaptive = _ref2.adaptive, roundOffsets = _ref2.roundOffsets, isFixed = _ref2.isFixed;
    var _offsets$x = offsets.x, x = _offsets$x === void 0 ? 0 : _offsets$x, _offsets$y = offsets.y, y = _offsets$y === void 0 ? 0 : _offsets$y;
    var _ref3 = typeof roundOffsets === "function" ? roundOffsets({
      x,
      y
    }) : {
      x,
      y
    };
    x = _ref3.x;
    y = _ref3.y;
    var hasX = offsets.hasOwnProperty("x");
    var hasY = offsets.hasOwnProperty("y");
    var sideX = left;
    var sideY = top;
    var win = window;
    if (adaptive) {
      var offsetParent = getOffsetParent(popper2);
      var heightProp = "clientHeight";
      var widthProp = "clientWidth";
      if (offsetParent === getWindow(popper2)) {
        offsetParent = getDocumentElement(popper2);
        if (getComputedStyle(offsetParent).position !== "static" && position === "absolute") {
          heightProp = "scrollHeight";
          widthProp = "scrollWidth";
        }
      }
      offsetParent = offsetParent;
      if (placement === top || (placement === left || placement === right) && variation === end) {
        sideY = bottom;
        var offsetY = isFixed && offsetParent === win && win.visualViewport ? win.visualViewport.height : offsetParent[heightProp];
        y -= offsetY - popperRect.height;
        y *= gpuAcceleration ? 1 : -1;
      }
      if (placement === left || (placement === top || placement === bottom) && variation === end) {
        sideX = right;
        var offsetX = isFixed && offsetParent === win && win.visualViewport ? win.visualViewport.width : offsetParent[widthProp];
        x -= offsetX - popperRect.width;
        x *= gpuAcceleration ? 1 : -1;
      }
    }
    var commonStyles = Object.assign({
      position
    }, adaptive && unsetSides);
    var _ref4 = roundOffsets === true ? roundOffsetsByDPR({
      x,
      y
    }) : {
      x,
      y
    };
    x = _ref4.x;
    y = _ref4.y;
    if (gpuAcceleration) {
      var _Object$assign;
      return Object.assign({}, commonStyles, (_Object$assign = {}, _Object$assign[sideY] = hasY ? "0" : "", _Object$assign[sideX] = hasX ? "0" : "", _Object$assign.transform = (win.devicePixelRatio || 1) <= 1 ? "translate(" + x + "px, " + y + "px)" : "translate3d(" + x + "px, " + y + "px, 0)", _Object$assign));
    }
    return Object.assign({}, commonStyles, (_Object$assign2 = {}, _Object$assign2[sideY] = hasY ? y + "px" : "", _Object$assign2[sideX] = hasX ? x + "px" : "", _Object$assign2.transform = "", _Object$assign2));
  }
  function computeStyles(_ref5) {
    var state = _ref5.state, options2 = _ref5.options;
    var _options$gpuAccelerat = options2.gpuAcceleration, gpuAcceleration = _options$gpuAccelerat === void 0 ? true : _options$gpuAccelerat, _options$adaptive = options2.adaptive, adaptive = _options$adaptive === void 0 ? true : _options$adaptive, _options$roundOffsets = options2.roundOffsets, roundOffsets = _options$roundOffsets === void 0 ? true : _options$roundOffsets;
    var commonStyles = {
      placement: getBasePlacement(state.placement),
      variation: getVariation(state.placement),
      popper: state.elements.popper,
      popperRect: state.rects.popper,
      gpuAcceleration,
      isFixed: state.options.strategy === "fixed"
    };
    if (state.modifiersData.popperOffsets != null) {
      state.styles.popper = Object.assign({}, state.styles.popper, mapToStyles(Object.assign({}, commonStyles, {
        offsets: state.modifiersData.popperOffsets,
        position: state.options.strategy,
        adaptive,
        roundOffsets
      })));
    }
    if (state.modifiersData.arrow != null) {
      state.styles.arrow = Object.assign({}, state.styles.arrow, mapToStyles(Object.assign({}, commonStyles, {
        offsets: state.modifiersData.arrow,
        position: "absolute",
        adaptive: false,
        roundOffsets
      })));
    }
    state.attributes.popper = Object.assign({}, state.attributes.popper, {
      "data-popper-placement": state.placement
    });
  }
  var computeStyles$1 = {
    name: "computeStyles",
    enabled: true,
    phase: "beforeWrite",
    fn: computeStyles,
    data: {}
  };
  var passive = {
    passive: true
  };
  function effect(_ref) {
    var state = _ref.state, instance2 = _ref.instance, options2 = _ref.options;
    var _options$scroll = options2.scroll, scroll = _options$scroll === void 0 ? true : _options$scroll, _options$resize = options2.resize, resize = _options$resize === void 0 ? true : _options$resize;
    var window2 = getWindow(state.elements.popper);
    var scrollParents = [].concat(state.scrollParents.reference, state.scrollParents.popper);
    if (scroll) {
      scrollParents.forEach(function(scrollParent) {
        scrollParent.addEventListener("scroll", instance2.update, passive);
      });
    }
    if (resize) {
      window2.addEventListener("resize", instance2.update, passive);
    }
    return function() {
      if (scroll) {
        scrollParents.forEach(function(scrollParent) {
          scrollParent.removeEventListener("scroll", instance2.update, passive);
        });
      }
      if (resize) {
        window2.removeEventListener("resize", instance2.update, passive);
      }
    };
  }
  var eventListeners = {
    name: "eventListeners",
    enabled: true,
    phase: "write",
    fn: function fn() {
    },
    effect,
    data: {}
  };
  var hash$2 = {
    left: "right",
    right: "left",
    bottom: "top",
    top: "bottom"
  };
  function getOppositePlacement(placement) {
    return placement.replace(/left|right|bottom|top/g, function(matched) {
      return hash$2[matched];
    });
  }
  var hash$1 = {
    start: "end",
    end: "start"
  };
  function getOppositeVariationPlacement(placement) {
    return placement.replace(/start|end/g, function(matched) {
      return hash$1[matched];
    });
  }
  function getWindowScroll(node) {
    var win = getWindow(node);
    var scrollLeft = win.pageXOffset;
    var scrollTop = win.pageYOffset;
    return {
      scrollLeft,
      scrollTop
    };
  }
  function getWindowScrollBarX(element) {
    return getBoundingClientRect(getDocumentElement(element)).left + getWindowScroll(element).scrollLeft;
  }
  function getViewportRect(element, strategy) {
    var win = getWindow(element);
    var html = getDocumentElement(element);
    var visualViewport = win.visualViewport;
    var width = html.clientWidth;
    var height = html.clientHeight;
    var x = 0;
    var y = 0;
    if (visualViewport) {
      width = visualViewport.width;
      height = visualViewport.height;
      var layoutViewport = isLayoutViewport();
      if (layoutViewport || !layoutViewport && strategy === "fixed") {
        x = visualViewport.offsetLeft;
        y = visualViewport.offsetTop;
      }
    }
    return {
      width,
      height,
      x: x + getWindowScrollBarX(element),
      y
    };
  }
  function getDocumentRect(element) {
    var _element$ownerDocumen;
    var html = getDocumentElement(element);
    var winScroll = getWindowScroll(element);
    var body = (_element$ownerDocumen = element.ownerDocument) == null ? void 0 : _element$ownerDocumen.body;
    var width = max(html.scrollWidth, html.clientWidth, body ? body.scrollWidth : 0, body ? body.clientWidth : 0);
    var height = max(html.scrollHeight, html.clientHeight, body ? body.scrollHeight : 0, body ? body.clientHeight : 0);
    var x = -winScroll.scrollLeft + getWindowScrollBarX(element);
    var y = -winScroll.scrollTop;
    if (getComputedStyle(body || html).direction === "rtl") {
      x += max(html.clientWidth, body ? body.clientWidth : 0) - width;
    }
    return {
      width,
      height,
      x,
      y
    };
  }
  function isScrollParent(element) {
    var _getComputedStyle = getComputedStyle(element), overflow = _getComputedStyle.overflow, overflowX = _getComputedStyle.overflowX, overflowY = _getComputedStyle.overflowY;
    return /auto|scroll|overlay|hidden/.test(overflow + overflowY + overflowX);
  }
  function getScrollParent(node) {
    if (["html", "body", "#document"].indexOf(getNodeName(node)) >= 0) {
      return node.ownerDocument.body;
    }
    if (isHTMLElement(node) && isScrollParent(node)) {
      return node;
    }
    return getScrollParent(getParentNode(node));
  }
  function listScrollParents(element, list) {
    var _element$ownerDocumen;
    if (list === void 0) {
      list = [];
    }
    var scrollParent = getScrollParent(element);
    var isBody = scrollParent === ((_element$ownerDocumen = element.ownerDocument) == null ? void 0 : _element$ownerDocumen.body);
    var win = getWindow(scrollParent);
    var target = isBody ? [win].concat(win.visualViewport || [], isScrollParent(scrollParent) ? scrollParent : []) : scrollParent;
    var updatedList = list.concat(target);
    return isBody ? updatedList : updatedList.concat(listScrollParents(getParentNode(target)));
  }
  function rectToClientRect(rect) {
    return Object.assign({}, rect, {
      left: rect.x,
      top: rect.y,
      right: rect.x + rect.width,
      bottom: rect.y + rect.height
    });
  }
  function getInnerBoundingClientRect(element, strategy) {
    var rect = getBoundingClientRect(element, false, strategy === "fixed");
    rect.top = rect.top + element.clientTop;
    rect.left = rect.left + element.clientLeft;
    rect.bottom = rect.top + element.clientHeight;
    rect.right = rect.left + element.clientWidth;
    rect.width = element.clientWidth;
    rect.height = element.clientHeight;
    rect.x = rect.left;
    rect.y = rect.top;
    return rect;
  }
  function getClientRectFromMixedType(element, clippingParent, strategy) {
    return clippingParent === viewport ? rectToClientRect(getViewportRect(element, strategy)) : isElement(clippingParent) ? getInnerBoundingClientRect(clippingParent, strategy) : rectToClientRect(getDocumentRect(getDocumentElement(element)));
  }
  function getClippingParents(element) {
    var clippingParents2 = listScrollParents(getParentNode(element));
    var canEscapeClipping = ["absolute", "fixed"].indexOf(getComputedStyle(element).position) >= 0;
    var clipperElement = canEscapeClipping && isHTMLElement(element) ? getOffsetParent(element) : element;
    if (!isElement(clipperElement)) {
      return [];
    }
    return clippingParents2.filter(function(clippingParent) {
      return isElement(clippingParent) && contains(clippingParent, clipperElement) && getNodeName(clippingParent) !== "body";
    });
  }
  function getClippingRect(element, boundary, rootBoundary, strategy) {
    var mainClippingParents = boundary === "clippingParents" ? getClippingParents(element) : [].concat(boundary);
    var clippingParents2 = [].concat(mainClippingParents, [rootBoundary]);
    var firstClippingParent = clippingParents2[0];
    var clippingRect = clippingParents2.reduce(function(accRect, clippingParent) {
      var rect = getClientRectFromMixedType(element, clippingParent, strategy);
      accRect.top = max(rect.top, accRect.top);
      accRect.right = min(rect.right, accRect.right);
      accRect.bottom = min(rect.bottom, accRect.bottom);
      accRect.left = max(rect.left, accRect.left);
      return accRect;
    }, getClientRectFromMixedType(element, firstClippingParent, strategy));
    clippingRect.width = clippingRect.right - clippingRect.left;
    clippingRect.height = clippingRect.bottom - clippingRect.top;
    clippingRect.x = clippingRect.left;
    clippingRect.y = clippingRect.top;
    return clippingRect;
  }
  function computeOffsets(_ref) {
    var reference2 = _ref.reference, element = _ref.element, placement = _ref.placement;
    var basePlacement = placement ? getBasePlacement(placement) : null;
    var variation = placement ? getVariation(placement) : null;
    var commonX = reference2.x + reference2.width / 2 - element.width / 2;
    var commonY = reference2.y + reference2.height / 2 - element.height / 2;
    var offsets;
    switch (basePlacement) {
      case top:
        offsets = {
          x: commonX,
          y: reference2.y - element.height
        };
        break;
      case bottom:
        offsets = {
          x: commonX,
          y: reference2.y + reference2.height
        };
        break;
      case right:
        offsets = {
          x: reference2.x + reference2.width,
          y: commonY
        };
        break;
      case left:
        offsets = {
          x: reference2.x - element.width,
          y: commonY
        };
        break;
      default:
        offsets = {
          x: reference2.x,
          y: reference2.y
        };
    }
    var mainAxis = basePlacement ? getMainAxisFromPlacement(basePlacement) : null;
    if (mainAxis != null) {
      var len = mainAxis === "y" ? "height" : "width";
      switch (variation) {
        case start:
          offsets[mainAxis] = offsets[mainAxis] - (reference2[len] / 2 - element[len] / 2);
          break;
        case end:
          offsets[mainAxis] = offsets[mainAxis] + (reference2[len] / 2 - element[len] / 2);
          break;
      }
    }
    return offsets;
  }
  function detectOverflow(state, options2) {
    if (options2 === void 0) {
      options2 = {};
    }
    var _options = options2, _options$placement = _options.placement, placement = _options$placement === void 0 ? state.placement : _options$placement, _options$strategy = _options.strategy, strategy = _options$strategy === void 0 ? state.strategy : _options$strategy, _options$boundary = _options.boundary, boundary = _options$boundary === void 0 ? clippingParents : _options$boundary, _options$rootBoundary = _options.rootBoundary, rootBoundary = _options$rootBoundary === void 0 ? viewport : _options$rootBoundary, _options$elementConte = _options.elementContext, elementContext = _options$elementConte === void 0 ? popper : _options$elementConte, _options$altBoundary = _options.altBoundary, altBoundary = _options$altBoundary === void 0 ? false : _options$altBoundary, _options$padding = _options.padding, padding = _options$padding === void 0 ? 0 : _options$padding;
    var paddingObject = mergePaddingObject(typeof padding !== "number" ? padding : expandToHashMap(padding, basePlacements));
    var altContext = elementContext === popper ? reference : popper;
    var popperRect = state.rects.popper;
    var element = state.elements[altBoundary ? altContext : elementContext];
    var clippingClientRect = getClippingRect(isElement(element) ? element : element.contextElement || getDocumentElement(state.elements.popper), boundary, rootBoundary, strategy);
    var referenceClientRect = getBoundingClientRect(state.elements.reference);
    var popperOffsets2 = computeOffsets({
      reference: referenceClientRect,
      element: popperRect,
      strategy: "absolute",
      placement
    });
    var popperClientRect = rectToClientRect(Object.assign({}, popperRect, popperOffsets2));
    var elementClientRect = elementContext === popper ? popperClientRect : referenceClientRect;
    var overflowOffsets = {
      top: clippingClientRect.top - elementClientRect.top + paddingObject.top,
      bottom: elementClientRect.bottom - clippingClientRect.bottom + paddingObject.bottom,
      left: clippingClientRect.left - elementClientRect.left + paddingObject.left,
      right: elementClientRect.right - clippingClientRect.right + paddingObject.right
    };
    var offsetData = state.modifiersData.offset;
    if (elementContext === popper && offsetData) {
      var offset2 = offsetData[placement];
      Object.keys(overflowOffsets).forEach(function(key) {
        var multiply = [right, bottom].indexOf(key) >= 0 ? 1 : -1;
        var axis = [top, bottom].indexOf(key) >= 0 ? "y" : "x";
        overflowOffsets[key] += offset2[axis] * multiply;
      });
    }
    return overflowOffsets;
  }
  function computeAutoPlacement(state, options2) {
    if (options2 === void 0) {
      options2 = {};
    }
    var _options = options2, placement = _options.placement, boundary = _options.boundary, rootBoundary = _options.rootBoundary, padding = _options.padding, flipVariations = _options.flipVariations, _options$allowedAutoP = _options.allowedAutoPlacements, allowedAutoPlacements = _options$allowedAutoP === void 0 ? placements : _options$allowedAutoP;
    var variation = getVariation(placement);
    var placements$1 = variation ? flipVariations ? variationPlacements : variationPlacements.filter(function(placement2) {
      return getVariation(placement2) === variation;
    }) : basePlacements;
    var allowedPlacements = placements$1.filter(function(placement2) {
      return allowedAutoPlacements.indexOf(placement2) >= 0;
    });
    if (allowedPlacements.length === 0) {
      allowedPlacements = placements$1;
    }
    var overflows = allowedPlacements.reduce(function(acc, placement2) {
      acc[placement2] = detectOverflow(state, {
        placement: placement2,
        boundary,
        rootBoundary,
        padding
      })[getBasePlacement(placement2)];
      return acc;
    }, {});
    return Object.keys(overflows).sort(function(a, b) {
      return overflows[a] - overflows[b];
    });
  }
  function getExpandedFallbackPlacements(placement) {
    if (getBasePlacement(placement) === auto) {
      return [];
    }
    var oppositePlacement = getOppositePlacement(placement);
    return [getOppositeVariationPlacement(placement), oppositePlacement, getOppositeVariationPlacement(oppositePlacement)];
  }
  function flip(_ref) {
    var state = _ref.state, options2 = _ref.options, name = _ref.name;
    if (state.modifiersData[name]._skip) {
      return;
    }
    var _options$mainAxis = options2.mainAxis, checkMainAxis = _options$mainAxis === void 0 ? true : _options$mainAxis, _options$altAxis = options2.altAxis, checkAltAxis = _options$altAxis === void 0 ? true : _options$altAxis, specifiedFallbackPlacements = options2.fallbackPlacements, padding = options2.padding, boundary = options2.boundary, rootBoundary = options2.rootBoundary, altBoundary = options2.altBoundary, _options$flipVariatio = options2.flipVariations, flipVariations = _options$flipVariatio === void 0 ? true : _options$flipVariatio, allowedAutoPlacements = options2.allowedAutoPlacements;
    var preferredPlacement = state.options.placement;
    var basePlacement = getBasePlacement(preferredPlacement);
    var isBasePlacement = basePlacement === preferredPlacement;
    var fallbackPlacements = specifiedFallbackPlacements || (isBasePlacement || !flipVariations ? [getOppositePlacement(preferredPlacement)] : getExpandedFallbackPlacements(preferredPlacement));
    var placements2 = [preferredPlacement].concat(fallbackPlacements).reduce(function(acc, placement2) {
      return acc.concat(getBasePlacement(placement2) === auto ? computeAutoPlacement(state, {
        placement: placement2,
        boundary,
        rootBoundary,
        padding,
        flipVariations,
        allowedAutoPlacements
      }) : placement2);
    }, []);
    var referenceRect = state.rects.reference;
    var popperRect = state.rects.popper;
    var checksMap = /* @__PURE__ */ new Map();
    var makeFallbackChecks = true;
    var firstFittingPlacement = placements2[0];
    for (var i = 0; i < placements2.length; i++) {
      var placement = placements2[i];
      var _basePlacement = getBasePlacement(placement);
      var isStartVariation = getVariation(placement) === start;
      var isVertical = [top, bottom].indexOf(_basePlacement) >= 0;
      var len = isVertical ? "width" : "height";
      var overflow = detectOverflow(state, {
        placement,
        boundary,
        rootBoundary,
        altBoundary,
        padding
      });
      var mainVariationSide = isVertical ? isStartVariation ? right : left : isStartVariation ? bottom : top;
      if (referenceRect[len] > popperRect[len]) {
        mainVariationSide = getOppositePlacement(mainVariationSide);
      }
      var altVariationSide = getOppositePlacement(mainVariationSide);
      var checks = [];
      if (checkMainAxis) {
        checks.push(overflow[_basePlacement] <= 0);
      }
      if (checkAltAxis) {
        checks.push(overflow[mainVariationSide] <= 0, overflow[altVariationSide] <= 0);
      }
      if (checks.every(function(check) {
        return check;
      })) {
        firstFittingPlacement = placement;
        makeFallbackChecks = false;
        break;
      }
      checksMap.set(placement, checks);
    }
    if (makeFallbackChecks) {
      var numberOfChecks = flipVariations ? 3 : 1;
      var _loop = function _loop2(_i2) {
        var fittingPlacement = placements2.find(function(placement2) {
          var checks2 = checksMap.get(placement2);
          if (checks2) {
            return checks2.slice(0, _i2).every(function(check) {
              return check;
            });
          }
        });
        if (fittingPlacement) {
          firstFittingPlacement = fittingPlacement;
          return "break";
        }
      };
      for (var _i = numberOfChecks; _i > 0; _i--) {
        var _ret = _loop(_i);
        if (_ret === "break")
          break;
      }
    }
    if (state.placement !== firstFittingPlacement) {
      state.modifiersData[name]._skip = true;
      state.placement = firstFittingPlacement;
      state.reset = true;
    }
  }
  var flip$1 = {
    name: "flip",
    enabled: true,
    phase: "main",
    fn: flip,
    requiresIfExists: ["offset"],
    data: {
      _skip: false
    }
  };
  function getSideOffsets(overflow, rect, preventedOffsets) {
    if (preventedOffsets === void 0) {
      preventedOffsets = {
        x: 0,
        y: 0
      };
    }
    return {
      top: overflow.top - rect.height - preventedOffsets.y,
      right: overflow.right - rect.width + preventedOffsets.x,
      bottom: overflow.bottom - rect.height + preventedOffsets.y,
      left: overflow.left - rect.width - preventedOffsets.x
    };
  }
  function isAnySideFullyClipped(overflow) {
    return [top, right, bottom, left].some(function(side) {
      return overflow[side] >= 0;
    });
  }
  function hide(_ref) {
    var state = _ref.state, name = _ref.name;
    var referenceRect = state.rects.reference;
    var popperRect = state.rects.popper;
    var preventedOffsets = state.modifiersData.preventOverflow;
    var referenceOverflow = detectOverflow(state, {
      elementContext: "reference"
    });
    var popperAltOverflow = detectOverflow(state, {
      altBoundary: true
    });
    var referenceClippingOffsets = getSideOffsets(referenceOverflow, referenceRect);
    var popperEscapeOffsets = getSideOffsets(popperAltOverflow, popperRect, preventedOffsets);
    var isReferenceHidden = isAnySideFullyClipped(referenceClippingOffsets);
    var hasPopperEscaped = isAnySideFullyClipped(popperEscapeOffsets);
    state.modifiersData[name] = {
      referenceClippingOffsets,
      popperEscapeOffsets,
      isReferenceHidden,
      hasPopperEscaped
    };
    state.attributes.popper = Object.assign({}, state.attributes.popper, {
      "data-popper-reference-hidden": isReferenceHidden,
      "data-popper-escaped": hasPopperEscaped
    });
  }
  var hide$1 = {
    name: "hide",
    enabled: true,
    phase: "main",
    requiresIfExists: ["preventOverflow"],
    fn: hide
  };
  function distanceAndSkiddingToXY(placement, rects, offset2) {
    var basePlacement = getBasePlacement(placement);
    var invertDistance = [left, top].indexOf(basePlacement) >= 0 ? -1 : 1;
    var _ref = typeof offset2 === "function" ? offset2(Object.assign({}, rects, {
      placement
    })) : offset2, skidding = _ref[0], distance = _ref[1];
    skidding = skidding || 0;
    distance = (distance || 0) * invertDistance;
    return [left, right].indexOf(basePlacement) >= 0 ? {
      x: distance,
      y: skidding
    } : {
      x: skidding,
      y: distance
    };
  }
  function offset(_ref2) {
    var state = _ref2.state, options2 = _ref2.options, name = _ref2.name;
    var _options$offset = options2.offset, offset2 = _options$offset === void 0 ? [0, 0] : _options$offset;
    var data2 = placements.reduce(function(acc, placement) {
      acc[placement] = distanceAndSkiddingToXY(placement, state.rects, offset2);
      return acc;
    }, {});
    var _data$state$placement = data2[state.placement], x = _data$state$placement.x, y = _data$state$placement.y;
    if (state.modifiersData.popperOffsets != null) {
      state.modifiersData.popperOffsets.x += x;
      state.modifiersData.popperOffsets.y += y;
    }
    state.modifiersData[name] = data2;
  }
  var offset$1 = {
    name: "offset",
    enabled: true,
    phase: "main",
    requires: ["popperOffsets"],
    fn: offset
  };
  function popperOffsets(_ref) {
    var state = _ref.state, name = _ref.name;
    state.modifiersData[name] = computeOffsets({
      reference: state.rects.reference,
      element: state.rects.popper,
      strategy: "absolute",
      placement: state.placement
    });
  }
  var popperOffsets$1 = {
    name: "popperOffsets",
    enabled: true,
    phase: "read",
    fn: popperOffsets,
    data: {}
  };
  function getAltAxis(axis) {
    return axis === "x" ? "y" : "x";
  }
  function preventOverflow(_ref) {
    var state = _ref.state, options2 = _ref.options, name = _ref.name;
    var _options$mainAxis = options2.mainAxis, checkMainAxis = _options$mainAxis === void 0 ? true : _options$mainAxis, _options$altAxis = options2.altAxis, checkAltAxis = _options$altAxis === void 0 ? false : _options$altAxis, boundary = options2.boundary, rootBoundary = options2.rootBoundary, altBoundary = options2.altBoundary, padding = options2.padding, _options$tether = options2.tether, tether = _options$tether === void 0 ? true : _options$tether, _options$tetherOffset = options2.tetherOffset, tetherOffset = _options$tetherOffset === void 0 ? 0 : _options$tetherOffset;
    var overflow = detectOverflow(state, {
      boundary,
      rootBoundary,
      padding,
      altBoundary
    });
    var basePlacement = getBasePlacement(state.placement);
    var variation = getVariation(state.placement);
    var isBasePlacement = !variation;
    var mainAxis = getMainAxisFromPlacement(basePlacement);
    var altAxis = getAltAxis(mainAxis);
    var popperOffsets2 = state.modifiersData.popperOffsets;
    var referenceRect = state.rects.reference;
    var popperRect = state.rects.popper;
    var tetherOffsetValue = typeof tetherOffset === "function" ? tetherOffset(Object.assign({}, state.rects, {
      placement: state.placement
    })) : tetherOffset;
    var normalizedTetherOffsetValue = typeof tetherOffsetValue === "number" ? {
      mainAxis: tetherOffsetValue,
      altAxis: tetherOffsetValue
    } : Object.assign({
      mainAxis: 0,
      altAxis: 0
    }, tetherOffsetValue);
    var offsetModifierState = state.modifiersData.offset ? state.modifiersData.offset[state.placement] : null;
    var data2 = {
      x: 0,
      y: 0
    };
    if (!popperOffsets2) {
      return;
    }
    if (checkMainAxis) {
      var _offsetModifierState$;
      var mainSide = mainAxis === "y" ? top : left;
      var altSide = mainAxis === "y" ? bottom : right;
      var len = mainAxis === "y" ? "height" : "width";
      var offset2 = popperOffsets2[mainAxis];
      var min$1 = offset2 + overflow[mainSide];
      var max$1 = offset2 - overflow[altSide];
      var additive = tether ? -popperRect[len] / 2 : 0;
      var minLen = variation === start ? referenceRect[len] : popperRect[len];
      var maxLen = variation === start ? -popperRect[len] : -referenceRect[len];
      var arrowElement = state.elements.arrow;
      var arrowRect = tether && arrowElement ? getLayoutRect(arrowElement) : {
        width: 0,
        height: 0
      };
      var arrowPaddingObject = state.modifiersData["arrow#persistent"] ? state.modifiersData["arrow#persistent"].padding : getFreshSideObject();
      var arrowPaddingMin = arrowPaddingObject[mainSide];
      var arrowPaddingMax = arrowPaddingObject[altSide];
      var arrowLen = within(0, referenceRect[len], arrowRect[len]);
      var minOffset = isBasePlacement ? referenceRect[len] / 2 - additive - arrowLen - arrowPaddingMin - normalizedTetherOffsetValue.mainAxis : minLen - arrowLen - arrowPaddingMin - normalizedTetherOffsetValue.mainAxis;
      var maxOffset = isBasePlacement ? -referenceRect[len] / 2 + additive + arrowLen + arrowPaddingMax + normalizedTetherOffsetValue.mainAxis : maxLen + arrowLen + arrowPaddingMax + normalizedTetherOffsetValue.mainAxis;
      var arrowOffsetParent = state.elements.arrow && getOffsetParent(state.elements.arrow);
      var clientOffset = arrowOffsetParent ? mainAxis === "y" ? arrowOffsetParent.clientTop || 0 : arrowOffsetParent.clientLeft || 0 : 0;
      var offsetModifierValue = (_offsetModifierState$ = offsetModifierState == null ? void 0 : offsetModifierState[mainAxis]) != null ? _offsetModifierState$ : 0;
      var tetherMin = offset2 + minOffset - offsetModifierValue - clientOffset;
      var tetherMax = offset2 + maxOffset - offsetModifierValue;
      var preventedOffset = within(tether ? min(min$1, tetherMin) : min$1, offset2, tether ? max(max$1, tetherMax) : max$1);
      popperOffsets2[mainAxis] = preventedOffset;
      data2[mainAxis] = preventedOffset - offset2;
    }
    if (checkAltAxis) {
      var _offsetModifierState$2;
      var _mainSide = mainAxis === "x" ? top : left;
      var _altSide = mainAxis === "x" ? bottom : right;
      var _offset = popperOffsets2[altAxis];
      var _len = altAxis === "y" ? "height" : "width";
      var _min = _offset + overflow[_mainSide];
      var _max = _offset - overflow[_altSide];
      var isOriginSide = [top, left].indexOf(basePlacement) !== -1;
      var _offsetModifierValue = (_offsetModifierState$2 = offsetModifierState == null ? void 0 : offsetModifierState[altAxis]) != null ? _offsetModifierState$2 : 0;
      var _tetherMin = isOriginSide ? _min : _offset - referenceRect[_len] - popperRect[_len] - _offsetModifierValue + normalizedTetherOffsetValue.altAxis;
      var _tetherMax = isOriginSide ? _offset + referenceRect[_len] + popperRect[_len] - _offsetModifierValue - normalizedTetherOffsetValue.altAxis : _max;
      var _preventedOffset = tether && isOriginSide ? withinMaxClamp(_tetherMin, _offset, _tetherMax) : within(tether ? _tetherMin : _min, _offset, tether ? _tetherMax : _max);
      popperOffsets2[altAxis] = _preventedOffset;
      data2[altAxis] = _preventedOffset - _offset;
    }
    state.modifiersData[name] = data2;
  }
  var preventOverflow$1 = {
    name: "preventOverflow",
    enabled: true,
    phase: "main",
    fn: preventOverflow,
    requiresIfExists: ["offset"]
  };
  function getHTMLElementScroll(element) {
    return {
      scrollLeft: element.scrollLeft,
      scrollTop: element.scrollTop
    };
  }
  function getNodeScroll(node) {
    if (node === getWindow(node) || !isHTMLElement(node)) {
      return getWindowScroll(node);
    } else {
      return getHTMLElementScroll(node);
    }
  }
  function isElementScaled(element) {
    var rect = element.getBoundingClientRect();
    var scaleX = round(rect.width) / element.offsetWidth || 1;
    var scaleY = round(rect.height) / element.offsetHeight || 1;
    return scaleX !== 1 || scaleY !== 1;
  }
  function getCompositeRect(elementOrVirtualElement, offsetParent, isFixed) {
    if (isFixed === void 0) {
      isFixed = false;
    }
    var isOffsetParentAnElement = isHTMLElement(offsetParent);
    var offsetParentIsScaled = isHTMLElement(offsetParent) && isElementScaled(offsetParent);
    var documentElement = getDocumentElement(offsetParent);
    var rect = getBoundingClientRect(elementOrVirtualElement, offsetParentIsScaled, isFixed);
    var scroll = {
      scrollLeft: 0,
      scrollTop: 0
    };
    var offsets = {
      x: 0,
      y: 0
    };
    if (isOffsetParentAnElement || !isOffsetParentAnElement && !isFixed) {
      if (getNodeName(offsetParent) !== "body" || isScrollParent(documentElement)) {
        scroll = getNodeScroll(offsetParent);
      }
      if (isHTMLElement(offsetParent)) {
        offsets = getBoundingClientRect(offsetParent, true);
        offsets.x += offsetParent.clientLeft;
        offsets.y += offsetParent.clientTop;
      } else if (documentElement) {
        offsets.x = getWindowScrollBarX(documentElement);
      }
    }
    return {
      x: rect.left + scroll.scrollLeft - offsets.x,
      y: rect.top + scroll.scrollTop - offsets.y,
      width: rect.width,
      height: rect.height
    };
  }
  function order(modifiers) {
    var map = /* @__PURE__ */ new Map();
    var visited = /* @__PURE__ */ new Set();
    var result = [];
    modifiers.forEach(function(modifier) {
      map.set(modifier.name, modifier);
    });
    function sort(modifier) {
      visited.add(modifier.name);
      var requires = [].concat(modifier.requires || [], modifier.requiresIfExists || []);
      requires.forEach(function(dep) {
        if (!visited.has(dep)) {
          var depModifier = map.get(dep);
          if (depModifier) {
            sort(depModifier);
          }
        }
      });
      result.push(modifier);
    }
    modifiers.forEach(function(modifier) {
      if (!visited.has(modifier.name)) {
        sort(modifier);
      }
    });
    return result;
  }
  function orderModifiers(modifiers) {
    var orderedModifiers = order(modifiers);
    return modifierPhases.reduce(function(acc, phase) {
      return acc.concat(orderedModifiers.filter(function(modifier) {
        return modifier.phase === phase;
      }));
    }, []);
  }
  function debounce(fn) {
    var pending;
    return function() {
      if (!pending) {
        pending = new Promise(function(resolve) {
          Promise.resolve().then(function() {
            pending = void 0;
            resolve(fn());
          });
        });
      }
      return pending;
    };
  }
  function mergeByName(modifiers) {
    var merged = modifiers.reduce(function(merged2, current) {
      var existing = merged2[current.name];
      merged2[current.name] = existing ? Object.assign({}, existing, current, {
        options: Object.assign({}, existing.options, current.options),
        data: Object.assign({}, existing.data, current.data)
      }) : current;
      return merged2;
    }, {});
    return Object.keys(merged).map(function(key) {
      return merged[key];
    });
  }
  var DEFAULT_OPTIONS = {
    placement: "bottom",
    modifiers: [],
    strategy: "absolute"
  };
  function areValidElements() {
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    return !args.some(function(element) {
      return !(element && typeof element.getBoundingClientRect === "function");
    });
  }
  function popperGenerator(generatorOptions) {
    if (generatorOptions === void 0) {
      generatorOptions = {};
    }
    var _generatorOptions = generatorOptions, _generatorOptions$def = _generatorOptions.defaultModifiers, defaultModifiers2 = _generatorOptions$def === void 0 ? [] : _generatorOptions$def, _generatorOptions$def2 = _generatorOptions.defaultOptions, defaultOptions2 = _generatorOptions$def2 === void 0 ? DEFAULT_OPTIONS : _generatorOptions$def2;
    return function createPopper2(reference2, popper2, options2) {
      if (options2 === void 0) {
        options2 = defaultOptions2;
      }
      var state = {
        placement: "bottom",
        orderedModifiers: [],
        options: Object.assign({}, DEFAULT_OPTIONS, defaultOptions2),
        modifiersData: {},
        elements: {
          reference: reference2,
          popper: popper2
        },
        attributes: {},
        styles: {}
      };
      var effectCleanupFns = [];
      var isDestroyed = false;
      var instance2 = {
        state,
        setOptions: function setOptions(setOptionsAction) {
          var options3 = typeof setOptionsAction === "function" ? setOptionsAction(state.options) : setOptionsAction;
          cleanupModifierEffects();
          state.options = Object.assign({}, defaultOptions2, state.options, options3);
          state.scrollParents = {
            reference: isElement(reference2) ? listScrollParents(reference2) : reference2.contextElement ? listScrollParents(reference2.contextElement) : [],
            popper: listScrollParents(popper2)
          };
          var orderedModifiers = orderModifiers(mergeByName([].concat(defaultModifiers2, state.options.modifiers)));
          state.orderedModifiers = orderedModifiers.filter(function(m) {
            return m.enabled;
          });
          runModifierEffects();
          return instance2.update();
        },
        forceUpdate: function forceUpdate() {
          if (isDestroyed) {
            return;
          }
          var _state$elements = state.elements, reference3 = _state$elements.reference, popper3 = _state$elements.popper;
          if (!areValidElements(reference3, popper3)) {
            return;
          }
          state.rects = {
            reference: getCompositeRect(reference3, getOffsetParent(popper3), state.options.strategy === "fixed"),
            popper: getLayoutRect(popper3)
          };
          state.reset = false;
          state.placement = state.options.placement;
          state.orderedModifiers.forEach(function(modifier) {
            return state.modifiersData[modifier.name] = Object.assign({}, modifier.data);
          });
          for (var index2 = 0; index2 < state.orderedModifiers.length; index2++) {
            if (state.reset === true) {
              state.reset = false;
              index2 = -1;
              continue;
            }
            var _state$orderedModifie = state.orderedModifiers[index2], fn = _state$orderedModifie.fn, _state$orderedModifie2 = _state$orderedModifie.options, _options = _state$orderedModifie2 === void 0 ? {} : _state$orderedModifie2, name = _state$orderedModifie.name;
            if (typeof fn === "function") {
              state = fn({
                state,
                options: _options,
                name,
                instance: instance2
              }) || state;
            }
          }
        },
        update: debounce(function() {
          return new Promise(function(resolve) {
            instance2.forceUpdate();
            resolve(state);
          });
        }),
        destroy: function destroy() {
          cleanupModifierEffects();
          isDestroyed = true;
        }
      };
      if (!areValidElements(reference2, popper2)) {
        return instance2;
      }
      instance2.setOptions(options2).then(function(state2) {
        if (!isDestroyed && options2.onFirstUpdate) {
          options2.onFirstUpdate(state2);
        }
      });
      function runModifierEffects() {
        state.orderedModifiers.forEach(function(_ref3) {
          var name = _ref3.name, _ref3$options = _ref3.options, options3 = _ref3$options === void 0 ? {} : _ref3$options, effect2 = _ref3.effect;
          if (typeof effect2 === "function") {
            var cleanupFn = effect2({
              state,
              name,
              instance: instance2,
              options: options3
            });
            var noopFn = function noopFn2() {
            };
            effectCleanupFns.push(cleanupFn || noopFn);
          }
        });
      }
      function cleanupModifierEffects() {
        effectCleanupFns.forEach(function(fn) {
          return fn();
        });
        effectCleanupFns = [];
      }
      return instance2;
    };
  }
  var defaultModifiers = [eventListeners, popperOffsets$1, computeStyles$1, applyStyles$1, offset$1, flip$1, preventOverflow$1, arrow$1, hide$1];
  var createPopper = /* @__PURE__ */ popperGenerator({
    defaultModifiers
  });
  const getManager = () => {
    let zIndex = 1e4;
    const getZindex2 = () => {
      zIndex++;
      return zIndex;
    };
    const removeZindex2 = () => {
      zIndex--;
    };
    return {
      getZindex: getZindex2,
      removeZindex: removeZindex2
    };
  };
  const instance = getManager();
  const { getZindex, removeZindex } = instance;
  var Tooltip_vue_vue_type_style_index_0_lang = "";
  let preventOutsideClickPropagation = false;
  const _sfc_main$1o = {
    name: "Tooltip",
    inheritAttrs: false,
    props: {
      modifiers: {
        type: Array,
        required: false
      },
      tag: {
        default: "div"
      },
      content: {
        type: String,
        required: false,
        default: null
      },
      show: {
        type: Boolean,
        required: false,
        default: false
      },
      showOnMouseEnter: {
        type: Boolean,
        required: false,
        default: true
      },
      openDelay: {
        type: Number,
        required: false,
        default: 10
      },
      closeDelay: {
        type: Number,
        required: false,
        default: 10
      },
      enterable: {
        type: Boolean,
        required: false,
        default: true
      },
      hideAfter: {
        type: Number,
        required: false,
        default: null
      },
      showArrows: {
        type: Boolean,
        required: false,
        default: true
      },
      appendTo: {
        required: false
      },
      trigger: {
        type: String,
        required: false,
        default: "hover"
      },
      closeOnOutsideClick: {
        type: Boolean,
        required: false,
        default: false
      },
      closeOnEscape: {
        type: Boolean,
        required: false,
        default: false
      },
      popperRef: {
        type: Object,
        required: false,
        default() {
          return null;
        }
      },
      transition: {
        type: String,
        required: false
      },
      enterActiveClass: {
        type: String,
        required: false,
        default: ""
      },
      leaveActiveClass: {
        type: String,
        required: false,
        default: ""
      },
      tooltipClass: {
        type: String,
        required: false
      },
      tooltipStyle: {
        type: [Object, String],
        required: false,
        default: {}
      }
    },
    setup(props) {
      const root2 = vue.ref(null);
      const popperSelector = vue.ref(null);
      const ownerDocument = vue.ref(null);
      return {
        popperSelector,
        root: root2,
        ownerDocument
      };
    },
    data() {
      return {
        visible: !!this.show,
        showTimeout: null,
        hideTimeout: null,
        hideAfterTimeout: null,
        zIndex: null
      };
    },
    computed: {
      getStyle() {
        return __spreadProps(__spreadValues({}, this.tooltipStyle), {
          "z-index": this.zIndex
        });
      },
      popperProps() {
        const props = {};
        if (this.enterActiveClass) {
          props.enterActiveClass = this.enterActiveClass;
        }
        if (this.leaveActiveClass) {
          props.leaveActiveClass = this.leaveActiveClass;
        }
        return props;
      },
      popperOptions() {
        const options2 = JSON.parse(JSON.stringify(getDefaultOptions()));
        const instanceOptions = JSON.parse(JSON.stringify(this.$attrs));
        instanceOptions.modifiers = this.modifiers || [];
        if (this.showArrows) {
          const hasOffsetModifier = instanceOptions.modifiers.find((modifier) => modifier.name === "offset");
          if (!hasOffsetModifier) {
            instanceOptions.modifiers.push({
              name: "offset",
              options: {
                offset: [0, 10]
              }
            });
          }
        }
        return merge$2(options2, instanceOptions);
      },
      appendToOption() {
        const options2 = JSON.parse(JSON.stringify(getDefaultOptions()));
        return this.appendTo || options2.appendTo;
      }
    },
    watch: {
      closeOnOutsideClick(newValue) {
        if (newValue) {
          this.ownerDocument.addEventListener("click", this.onOutsideClick, true);
        } else {
          this.ownerDocument.removeEventListener("click", this.onOutsideClick, true);
        }
      },
      hideAfter(newValue) {
        if (newValue) {
          this.onHideAfter();
        }
      },
      show(newValue, oldValue) {
        this.visible = !!newValue;
      },
      visible(newValue, oldvalue) {
        if (!!newValue !== !!oldvalue) {
          if (newValue) {
            this.zIndex = getZindex();
          } else if (this.zIndex) {
            removeZindex();
            this.zIndex = null;
          }
        }
      }
    },
    methods: {
      preventOutsideClickPropagation() {
        preventOutsideClickPropagation = true;
      },
      onTransitionEnter() {
        this.instantiatePopper();
        this.$emit("show");
        this.$emit("update:show", true);
      },
      onTransitionLeave() {
        this.destroyPopper();
        this.$emit("hide");
        this.$emit("update:show", false);
      },
      getAppendToElement() {
        if (this.appendToOption === "element") {
          return this.$el;
        } else {
          return this.ownerDocument.querySelector(this.appendToOption);
        }
      },
      showPopper() {
        this.visible = true;
      },
      hidePopper() {
        this.visible = false;
      },
      addPopperToDom() {
        if (this.popperElement && this.appendToOption !== "element") {
          const appendElement = this.getAppendToElement();
          if (!appendElement) {
            console.warn(
              `No HTMLElement was found matching ${appendElement}`
            );
            return;
          }
          appendElement.appendChild(this.popperElement);
        }
      },
      destroyPopper(completeRemove) {
        if (this.visible && !completeRemove) {
          return;
        }
        this.visible = false;
        if (this.popperInstance) {
          this.popperInstance.destroy();
          this.popperInstance = null;
        }
        this.removePopperEvents();
        if (this.popperElement && this.popperElement.parentNode && this.appendToOption !== "element") {
          this.popperElement.parentNode.removeChild(this.popperElement);
        }
        this.popperElement = null;
        preventOutsideClickPropagation = false;
      },
      instantiatePopper() {
        this.popperElement = this.$refs.popper;
        this.popperSelector = this.popperRef || this.root;
        this.ownerDocument = this.popperSelector.ownerDocument || this.root.ownerDocument;
        this.addPopperToDom();
        if (this.popperInstance && this.popperInstance.destroy) {
          this.popperInstance.destroy();
          this.popperInstance = null;
        }
        if (this.popperSelector) {
          this.popperInstance = createPopper(this.popperSelector, this.popperElement, this.popperOptions);
        }
        this.onHideAfter();
        this.addPopperEvents();
      },
      onMouseEnter(event2) {
        if (!this.showOnMouseEnter) {
          return;
        }
        clearTimeout(this.timeout);
        this.timeout = setTimeout(() => {
          this.showPopper();
        }, this.openDelay);
      },
      onMouseLeave(event2) {
        clearTimeout(this.timeout);
        this.timeout = setTimeout(() => {
          this.hidePopper();
        }, this.closeDelay);
      },
      onHideAfter() {
        if (this.hideAfter) {
          clearTimeout(this.timeout);
          this.timeout = setTimeout(() => {
            this.hidePopper();
          }, this.hideAfter);
        }
      },
      onClick: debounce$1(function(event2) {
        if (this.popperElement && this.popperElement.contains(event2.target)) {
          return;
        }
        this.visible = !this.visible;
      }, 10),
      onOutsideClick(event2) {
        if (!this.visible || preventOutsideClickPropagation) {
          return;
        }
        if (this.popperSelector && typeof this.popperSelector.contains === "function" && this.popperSelector.contains(event2.target)) {
          return;
        }
        if (this.popperElement && this.popperElement.contains(event2.target)) {
          return;
        }
        this.hidePopper();
        this.$emit("hide");
        this.$emit("update:show", false);
        preventOutsideClickPropagation = false;
      },
      onKeyUp(event2) {
        if (event2.which === 27) {
          this.hidePopper();
          event2.stopPropagation();
        }
      },
      scheduleUpdate() {
        if (this.popperInstance) {
          this.popperInstance.update();
        }
      },
      addPopperEvents() {
        if (this.closeOnOutsideClick) {
          this.ownerDocument.addEventListener("click", this.onOutsideClick, true);
        }
        if (this.trigger === "hover" && this.enterable && this.popperElement) {
          this.popperElement.addEventListener("mouseenter", this.onMouseEnter);
          this.popperElement.addEventListener("mouseleave", this.onMouseLeave);
        }
        if (this.closeOnEscape) {
          this.ownerDocument.addEventListener("keyup", this.onKeyUp);
        }
      },
      removePopperEvents() {
        if (this.ownerDocument) {
          this.ownerDocument.removeEventListener("click", this.onOutsideClick, true);
        }
        if (this.trigger === "hover" && this.enterable && this.popperElement) {
          this.popperElement.removeEventListener("mouseenter", this.onMouseEnter);
          this.popperElement.removeEventListener("mouseleave", this.onMouseLeave);
        }
        if (this.closeOnEscape && this.ownerDocument) {
          this.ownerDocument.removeEventListener("keyup", this.onKeyUp);
        }
      }
    },
    unmounted() {
      this.$el.removeEventListener("mouseenter", this.onMouseEnter);
      this.$el.removeEventListener("mouseleave", this.onMouseLeave);
      this.$el.removeEventListener("click", this.onClick);
      if (this.ownerDocument) {
        this.ownerDocument.removeEventListener("click", this.onOutsideClick, true);
        this.ownerDocument.removeEventListener("keyup", this.onKeyUp);
      }
      this.destroyPopper(true);
      if (this.zIndex) {
        removeZindex();
        this.zIndex = null;
      }
    },
    mounted() {
      if (this.trigger === "hover") {
        this.$el.addEventListener("mouseenter", this.onMouseEnter);
        this.$el.addEventListener("mouseleave", this.onMouseLeave);
      } else if (this.trigger === "click") {
        this.$el.addEventListener("click", this.onClick);
      }
      if (this.show) {
        this.zIndex = getZindex();
      }
    }
  };
  const _hoisted_1$11 = {
    key: 0,
    "data-popper-arrow": "true",
    class: "hg-popper--with-arrows"
  };
  function _sfc_render$q(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createBlock(vue.resolveDynamicComponent($props.tag), vue.mergeProps(_ctx.$attrs, { ref: "root" }), {
      default: vue.withCtx(() => [
        $data.visible ? (vue.openBlock(), vue.createBlock(vue.Transition, vue.mergeProps({
          key: 0,
          appear: "",
          name: $props.transition
        }, $options.popperProps, {
          onEnter: $options.onTransitionEnter,
          onAfterLeave: $options.onTransitionLeave
        }), {
          default: vue.withCtx(() => [
            vue.createElementVNode("div", {
              ref: "popper",
              class: vue.normalizeClass(["hg-popper", $props.tooltipClass]),
              style: vue.normalizeStyle($options.getStyle)
            }, [
              vue.createTextVNode(vue.toDisplayString($props.content) + " ", 1),
              vue.renderSlot(_ctx.$slots, "content"),
              $props.showArrows ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_1$11)) : vue.createCommentVNode("", true)
            ], 6)
          ]),
          _: 3
        }, 16, ["name", "onEnter", "onAfterLeave"])) : vue.createCommentVNode("", true),
        vue.renderSlot(_ctx.$slots, "default")
      ]),
      _: 3
    }, 16);
  }
  var Tooltip = /* @__PURE__ */ _export_sfc(_sfc_main$1o, [["render", _sfc_render$q]]);
  const PopperDirective = {
    mounted(el, { value, arg }, vnode) {
      el.__ZnPbTooltip__ = initTooltip(el, value, arg);
    },
    beforeUnmount(el) {
      if (el.__ZnPbTooltip__) {
        el.__ZnPbTooltip__.destroy();
      }
    },
    updated(el, { value, arg }) {
      if (el.__ZnPbTooltip__) {
        el.__ZnPbTooltip__.setContent(value);
        const popperPosition = arg || "top";
        el.__ZnPbTooltip__.updatePosition(popperPosition);
      }
    },
    unmounted(el) {
      if (el.__ZnPbTooltip__ && el.__ZnPbTooltip__.popper) {
        el.__ZnPbTooltip__.popper.destroy();
      }
    }
  };
  function initTooltip(element, content, arg) {
    const tooltipObject = {};
    const doc = element.ownerDocument;
    const popperContent = doc.createElement("span");
    popperContent.classList.add("hg-popper", "hg-popper-tooltip");
    popperContent.innerHTML = content;
    popperContent.setAttribute("show-popper", "true");
    const arrow2 = doc.createElement("span");
    arrow2.classList.add("hg-popper--with-arrows");
    arrow2.setAttribute("data-popper-arrow", "true");
    popperContent.appendChild(arrow2);
    tooltipObject.element = element;
    tooltipObject.content = popperContent;
    let popperPosition = arg || "top";
    function showPopper() {
      doc.body.appendChild(popperContent);
      tooltipObject.popper = createPopper(
        element,
        popperContent,
        {
          placement: popperPosition,
          modifiers: [
            {
              name: "offset",
              options: {
                offset: [0, 10]
              }
            }
          ]
        }
      );
    }
    function updatePosition(placement) {
      popperPosition = placement;
    }
    function hidePopper() {
      if (popperContent.parentNode) {
        popperContent.parentNode.removeChild(popperContent);
      }
      if (tooltipObject.popper) {
        tooltipObject.popper.destroy();
      }
    }
    function setContent(content2) {
      if (popperContent.innerHTML !== content2) {
        popperContent.innerHTML = content2;
        popperContent.appendChild(arrow2);
      }
    }
    element.addEventListener("mouseenter", showPopper);
    element.addEventListener("mouseleave", hidePopper);
    function destroy() {
      hidePopper();
      element.removeEventListener("mouseenter", showPopper);
      element.removeEventListener("mouseleave", hidePopper);
    }
    return __spreadProps(__spreadValues({}, tooltipObject), {
      showPopper,
      hidePopper,
      destroy,
      setContent,
      updatePosition
    });
  }
  var GradientDragger_vue_vue_type_style_index_0_scoped_true_lang = "";
  const _hoisted_1$10 = { class: "znpb-gradient-dragger-wrapper" };
  const __default__$W = {
    name: "GradientDragger"
  };
  const _sfc_main$1n = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$W), {
    props: {
      modelValue: null
    },
    emits: ["update:modelValue", "color-picker-open"],
    setup(__props, { emit }) {
      const props = __props;
      const gradientCircle = vue.ref(null);
      const colorpickerHolder = vue.ref(null);
      const showPicker = vue.ref(false);
      const circlePos = vue.ref(null);
      const computedValue = vue.computed({
        get() {
          return props.modelValue;
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      const colorValue = vue.computed({
        get() {
          return computedValue.value.color;
        },
        set(newValue) {
          computedValue.value = __spreadProps(__spreadValues({}, computedValue.value), {
            color: newValue
          });
        }
      });
      const colorPosition = vue.computed(() => {
        const cssStyles = {
          left: computedValue.value.position + "%",
          background: computedValue.value.color
        };
        return cssStyles;
      });
      const parentPosition = vue.computed(() => {
        return {
          left: circlePos.value.left,
          top: circlePos.value.top
        };
      });
      function openColorPicker() {
        showPicker.value = true;
        emit("color-picker-open", true);
        document.addEventListener("mousedown", closePanelOnOutsideClick);
      }
      function closePanelOnOutsideClick(event2) {
        const colorPicker = colorpickerHolder.value.$refs.colorPicker;
        if (!colorPicker.contains(event2.target)) {
          showPicker.value = false;
          document.removeEventListener("mousedown", closePanelOnOutsideClick);
          emit("color-picker-open", false);
        }
      }
      vue.onMounted(() => {
        vue.nextTick(() => {
          circlePos.value = gradientCircle.value.getBoundingClientRect();
        });
      });
      vue.onUnmounted(() => {
        document.removeEventListener("mousedown", closePanelOnOutsideClick);
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$10, [
          vue.createVNode(vue.unref(Tooltip), {
            show: showPicker.value,
            trigger: null,
            placement: "top"
          }, {
            content: vue.withCtx(() => [
              showPicker.value ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1u), {
                key: 0,
                ref_key: "colorpickerHolder",
                ref: colorpickerHolder,
                "parent-position": vue.unref(parentPosition),
                model: vue.unref(computedValue).color,
                "show-library": false,
                onColorChanged: _cache[0] || (_cache[0] = ($event) => colorValue.value = $event)
              }, null, 8, ["parent-position", "model"])) : vue.createCommentVNode("", true)
            ]),
            default: vue.withCtx(() => [
              vue.createElementVNode("span", {
                ref_key: "gradientCircle",
                ref: gradientCircle,
                class: "znpb-gradient-dragger",
                style: vue.normalizeStyle(vue.unref(colorPosition)),
                onDblclick: openColorPicker
              }, null, 36)
            ]),
            _: 1
          }, 8, ["show"])
        ]);
      };
    }
  }));
  var GradientDragger = /* @__PURE__ */ _export_sfc(_sfc_main$1n, [["__scopeId", "data-v-28fb396d"]]);
  var GradientColorConfig_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$$ = { class: "znpb-gradient-actions" };
  const _hoisted_2$G = {
    key: 0,
    class: "znpb-gradient-actions__delete"
  };
  const __default__$V = {
    name: "GradientColorConfig"
  };
  const _sfc_main$1m = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$V), {
    props: {
      config: null,
      showDelete: { type: Boolean, default: true }
    },
    emits: ["delete-color", "update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const schema = {
        color: {
          type: "colorpicker",
          id: "color",
          width: "50"
        },
        position: {
          type: "number",
          id: "position",
          content: "%",
          width: "50",
          min: 0,
          max: 100
        }
      };
      const valueModel = vue.computed({
        get() {
          const value = cloneDeep(props.config);
          if (Array.isArray(value.__dynamic_content__)) {
            value.__dynamic_content__ = {};
          }
          return value;
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      return (_ctx, _cache) => {
        const _component_OptionsForm = vue.resolveComponent("OptionsForm");
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$$, [
          vue.createVNode(_component_OptionsForm, {
            modelValue: vue.unref(valueModel),
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(valueModel) ? valueModel.value = $event : null),
            schema,
            class: "znpb-gradient-color-form"
          }, null, 8, ["modelValue"]),
          __props.showDelete ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_2$G, [
            vue.createVNode(_sfc_main$1K, {
              icon: "close",
              class: "znpb-gradient-actions-delete",
              onClick: _cache[1] || (_cache[1] = vue.withModifiers(($event) => _ctx.$emit("delete-color", __props.config), ["stop"]))
            })
          ])) : vue.createCommentVNode("", true)
        ]);
      };
    }
  }));
  var GradientBar_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$_ = { class: "znpb-gradient-colors-legend" };
  const _hoisted_2$F = { class: "znpb-form__input-title znpb-gradient-colors-legend-item" };
  const _hoisted_3$s = { class: "znpb-form__input-title znpb-gradient-colors-legend-item" };
  const __default__$U = {
    name: "GradientBar"
  };
  const _sfc_main$1l = vue.defineComponent(__spreadProps(__spreadValues({}, __default__$U), {
    props: {
      modelValue: null
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const root2 = vue.ref(null);
      const gradientbar = vue.ref(null);
      const gradref = vue.ref(null);
      const colorPickerOpen = vue.ref(false);
      const deletedColorConfig = vue.ref(null);
      const rafMovePosition = rafSchd$1(onCircleDrag);
      const rafEndDragging = rafSchd$1(disableDragging);
      let draggedCircleIndex;
      let draggedItem;
      const computedValue = vue.computed({
        get() {
          return props.modelValue;
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      const sortedColors = vue.computed(() => {
        let colorsCopy = [...computedValue.value.colors].sort((a, b) => {
          return a.position > b.position ? 1 : -1;
        });
        return colorsCopy;
      });
      const activeDraggedItem = vue.computed(() => {
        return computedValue.value.colors[draggedCircleIndex];
      });
      function onColorConfigUpdate(colorConfig, newValues) {
        const index2 = computedValue.value.colors.indexOf(colorConfig);
        const updatedValues = computedValue.value.colors.slice(0);
        updatedValues.splice(index2, 1, newValues);
        computedValue.value = __spreadProps(__spreadValues({}, computedValue.value), {
          colors: updatedValues
        });
      }
      function onDeleteColor(colorConfig) {
        const index2 = computedValue.value.colors.indexOf(colorConfig);
        const colorsClone = computedValue.value.colors.slice(0);
        deletedColorConfig.value = colorConfig;
        colorsClone.splice(index2, 1);
        computedValue.value = __spreadProps(__spreadValues({}, computedValue.value), {
          colors: colorsClone
        });
      }
      function reAddColor() {
        computedValue.value = __spreadProps(__spreadValues({}, computedValue.value), {
          colors: [...computedValue.value.colors, deletedColorConfig.value]
        });
        deletedColorConfig.value = null;
      }
      function addColor(event2) {
        const defaultColor = {
          color: "white",
          position: 0
        };
        const mouseLeftPosition = event2.clientX;
        const barOffset = root2.value.getBoundingClientRect();
        const startx = barOffset.left;
        const newLeft = mouseLeftPosition - startx;
        defaultColor.position = Math.round(newLeft / barOffset.width * 100);
        const updatedValues = __spreadProps(__spreadValues({}, computedValue.value), {
          colors: [...computedValue.value.colors, defaultColor]
        });
        computedValue.value = updatedValues;
      }
      function enableDragging(colorConfigIndex) {
        if (colorPickerOpen.value === false) {
          document.body.classList.add("znpb-color-gradient--backdrop");
          document.addEventListener("mousemove", rafMovePosition);
          document.addEventListener("mouseup", rafEndDragging);
          document.body.style.userSelect = "none";
          draggedCircleIndex = colorConfigIndex;
          draggedItem = computedValue.value.colors[colorConfigIndex];
          deletedColorConfig.value = null;
        }
      }
      function disableDragging() {
        document.body.classList.remove("znpb-color-gradient--backdrop");
        document.removeEventListener("mousemove", rafMovePosition);
        document.removeEventListener("mouseup", rafEndDragging);
        document.body.style.userSelect = "";
        deletedColorConfig.value = null;
        draggedCircleIndex = null;
      }
      function updateActiveConfigPosition(newPosition) {
        const newConfig = __spreadProps(__spreadValues({}, activeDraggedItem.value), {
          position: newPosition
        });
        onColorConfigUpdate(activeDraggedItem.value, newConfig);
      }
      function onCircleDrag(event2) {
        let newLeft = (event2.clientX - gradref.value.left) * 100 / gradref.value.width;
        const position = Math.min(Math.max(newLeft, 0), 100);
        if (newLeft > 100 || newLeft < 0) {
          if (sortedColors.value.length > 2 && deletedColorConfig.value === null) {
            onDeleteColor(draggedItem);
          }
        } else {
          if (deletedColorConfig.value !== null) {
            reAddColor();
          }
          vue.nextTick(() => {
            updateActiveConfigPosition(Math.round(position));
          });
        }
      }
      vue.onMounted(() => {
        vue.nextTick(() => {
          gradref.value = gradientbar.value.getBoundingClientRect();
        });
      });
      vue.onBeforeUnmount(() => {
        document.body.classList.remove("znpb-color-gradient--backdrop");
        disableDragging();
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          ref_key: "root",
          ref: root2,
          class: "znpb-gradient-bar-colors-wrapper"
        }, [
          vue.createElementVNode("div", {
            ref_key: "gradientbar",
            ref: gradientbar,
            class: "znpb-gradient-bar-wrapper"
          }, [
            vue.createVNode(_sfc_main$1p, {
              config: vue.unref(computedValue),
              onClick: addColor
            }, null, 8, ["config"]),
            (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(computedValue).colors, (colorConfig, i) => {
              return vue.openBlock(), vue.createBlock(GradientDragger, {
                key: i,
                modelValue: colorConfig,
                "onUpdate:modelValue": ($event) => onColorConfigUpdate(colorConfig, $event),
                onColorPickerOpen: _cache[0] || (_cache[0] = ($event) => colorPickerOpen.value = $event),
                onMousedown: ($event) => enableDragging(i)
              }, null, 8, ["modelValue", "onUpdate:modelValue", "onMousedown"]);
            }), 128))
          ], 512),
          vue.createElementVNode("div", _hoisted_1$_, [
            vue.createElementVNode("span", _hoisted_2$F, vue.toDisplayString(_ctx.$translate("color")), 1),
            vue.createElementVNode("span", _hoisted_3$s, vue.toDisplayString(_ctx.$translate("location")), 1)
          ]),
          (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(sortedColors), (colorConfig, i) => {
            return vue.openBlock(), vue.createBlock(_sfc_main$1m, {
              key: i,
              config: colorConfig,
              "show-delete": vue.unref(sortedColors).length > 2,
              "onUpdate:modelValue": ($event) => onColorConfigUpdate(colorConfig, $event),
              onDeleteColor: ($event) => onDeleteColor(colorConfig)
            }, null, 8, ["config", "show-delete", "onUpdate:modelValue", "onDeleteColor"]);
          }), 128))
        ], 512);
      };
    }
  }));
  var InputWrapper_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$Z = { class: "znpb-forms-input-content" };
  const __default__$T = {
    name: "InputWrapper"
  };
  const _sfc_main$1k = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$T), {
    props: {
      title: { default: "" },
      description: { default: "" },
      layout: { default: "full" },
      fakeLabel: { type: Boolean },
      schema: null
    },
    setup(__props) {
      const props = __props;
      const computedWrapperStyle = vue.computed(() => {
        const styles = {};
        if (props.schema !== void 0) {
          if (props.schema.grow) {
            styles.flex = props.schema.grow;
          }
          if (props.schema.width) {
            styles.width = `${props.schema.width}%`;
          }
        }
        return styles;
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          class: vue.normalizeClass(["znpb-input-wrapper", {
            [`znpb-input-wrapper--${__props.layout}`]: true
          }]),
          style: vue.normalizeStyle(vue.unref(computedWrapperStyle))
        }, [
          __props.title ? (vue.openBlock(), vue.createElementBlock("div", {
            key: 0,
            class: vue.normalizeClass(["znpb-form__input-title", { "znpb-form__input-title--fake-label": __props.fakeLabel }])
          }, [
            vue.createElementVNode("span", null, vue.toDisplayString(__props.title), 1),
            __props.description ? (vue.openBlock(), vue.createBlock(vue.unref(Tooltip), {
              key: 0,
              enterable: false
            }, {
              content: vue.withCtx(() => [
                vue.createElementVNode("div", null, vue.toDisplayString(__props.description), 1)
              ]),
              default: vue.withCtx(() => [
                vue.createVNode(vue.unref(_sfc_main$1K), { icon: "question-mark" })
              ]),
              _: 1
            })) : vue.createCommentVNode("", true)
          ], 2)) : vue.createCommentVNode("", true),
          vue.createElementVNode("div", _hoisted_1$Z, [
            vue.renderSlot(_ctx.$slots, "default")
          ])
        ], 6);
      };
    }
  }));
  var InputRange_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$Y = { class: "znpb-input-range" };
  const _hoisted_2$E = { class: "znpb-input-range__label" };
  const __default__$S = {
    name: "InputRange",
    inheritAttrs: false
  };
  const _sfc_main$1j = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$S), {
    props: {
      modelValue: { default: 0 },
      shift_step: { default: 10 },
      min: { default: 0 },
      max: { default: 100 },
      step: { default: 1 }
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const localStep = vue.ref(props.step);
      const optionValue = vue.computed({
        get() {
          var _a3;
          return (_a3 = props.modelValue) != null ? _a3 : props.min;
        },
        set(newValue) {
          emit("update:modelValue", +newValue);
        }
      });
      const trackWidth = vue.computed(() => {
        const thumbSize = 14 * width.value / 100;
        return {
          width: `calc(${width.value}% - ${thumbSize}px)`
        };
      });
      const width = vue.computed(() => {
        const minmax = props.max - props.min;
        return Math.round((props.modelValue - props.min) * 100 / minmax);
      });
      function onKeydown(event2) {
        if (event2.shiftKey) {
          localStep.value = props.shift_step;
        }
      }
      function onKeyUp(event2) {
        if (event2.key === "Shift") {
          localStep.value = props.step;
        }
      }
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$Y, [
          vue.createVNode(_sfc_main$1D, {
            ref: "rangebase",
            modelValue: vue.unref(optionValue),
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(optionValue) ? optionValue.value = $event : null),
            type: "range",
            min: __props.min,
            max: __props.max,
            step: localStep.value,
            onKeydown,
            onKeyup: onKeyUp
          }, {
            suffix: vue.withCtx(() => [
              vue.createElementVNode("div", {
                class: "znpb-input-range__trackwidth",
                style: vue.normalizeStyle(vue.unref(trackWidth))
              }, null, 4)
            ]),
            _: 1
          }, 8, ["modelValue", "min", "max", "step"]),
          vue.createElementVNode("label", _hoisted_2$E, [
            vue.createVNode(vue.unref(_sfc_main$1C), {
              modelValue: vue.unref(optionValue),
              "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => vue.isRef(optionValue) ? optionValue.value = $event : null),
              class: "znpb-input-range-number",
              min: __props.min,
              max: __props.max,
              step: __props.step,
              shift_step: __props.shift_step,
              onKeydown,
              onKeyup: onKeyUp
            }, {
              default: vue.withCtx(() => [
                vue.renderSlot(_ctx.$slots, "default")
              ]),
              _: 3
            }, 8, ["modelValue", "min", "max", "step", "shift_step"])
          ])
        ]);
      };
    }
  }));
  var InputRangeDynamic_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$X = { class: "znpb-input-range__label" };
  const __default__$R = {
    name: "InputRangeDynamic",
    inheritAttrs: false
  };
  const _sfc_main$1i = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$R), {
    props: {
      modelValue: { default: null },
      options: null,
      default_step: { default: 1 },
      default_shift_step: { default: 1 },
      min: null,
      max: null
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const rangebase = vue.ref(null);
      const inputNumberUnit = vue.ref(null);
      const step = vue.ref(1);
      const unit = vue.ref("");
      const customUnit = vue.ref(false);
      const rafUpdateValue = rafSchd$1(updateValue);
      const activeOption = vue.computed(() => {
        let activeOption2 = null;
        props.options.forEach((option) => {
          if (valueUnit.value && option.unit === valueUnit.value.unit) {
            activeOption2 = option;
          }
        });
        return activeOption2 || props.options[0];
      });
      const valueUnit = vue.computed({
        get() {
          const match = typeof props.modelValue === "string" ? props.modelValue.match(/^([+-]?[0-9]+([.][0-9]*)?|[.][0-9]+)(\D+)$/) : null;
          const value = match && match[1] ? +match[1] : null;
          const unit2 = match ? match[3] : null;
          return {
            value,
            unit: unit2
          };
        },
        set(newValue) {
          if (newValue.value && newValue.unit) {
            if (Number(newValue.value) > activeOption.value.max) {
              computedValue.value = `${activeOption.value.max}${newValue.unit}`;
            } else if (Number(newValue.value) < activeOption.value.min) {
              computedValue.value = `${activeOption.value.min}${newValue.unit}`;
            }
          }
        }
      });
      const computedValue = vue.computed({
        get() {
          return props.modelValue;
        },
        set(newValue) {
          rafUpdateValue(newValue);
        }
      });
      const rangeModel = vue.computed({
        get() {
          return disabled.value ? 0 : valueUnit.value.value || props.min || 0;
        },
        set(newValue) {
          if (getUnit.value) {
            computedValue.value = `${newValue}${getUnit.value}`;
          }
        }
      });
      const getUnit = vue.computed(() => {
        var _a3, _b, _c;
        return (_c = (_b = (_a3 = activeOption.value.unit) != null ? _a3 : valueUnit.value.unit) != null ? _b : unit.value) != null ? _c : null;
      });
      const getUnits = vue.computed(() => props.options.map((option) => option.unit));
      const baseStep = vue.computed(() => activeOption.value.step || props.default_step);
      const shiftStep = vue.computed(() => activeOption.value.shiftStep || props.default_shift_step);
      const trackWidth = vue.computed(() => {
        const thumbSize = 14 * width.value / 100;
        return {
          width: `calc(${width.value}% - ${thumbSize}px)`
        };
      });
      const width = vue.computed(() => {
        const minmax = activeOption.value.max - activeOption.value.min;
        return Math.round((activeOption.value.value - activeOption.value.min) * 100 / minmax);
      });
      const disabled = vue.computed(() => {
        const transformOriginUnits = ["left", "right", "top", "bottom", "center"];
        return transformOriginUnits.includes(unit.value) || customUnit.value;
      });
      function updateValue(newValue) {
        emit("update:modelValue", newValue);
      }
      function onUnitUpdate(event2) {
        unit.value = event2;
      }
      function onCustomUnit(event2) {
        customUnit.value = event2;
      }
      function onRangeKeydown(event2) {
        if (event2.shiftKey) {
          step.value = shiftStep.value;
        }
      }
      function onRangeKeyUp(event2) {
        if (event2.key === "Shift") {
          step.value = baseStep.value;
        }
      }
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          class: vue.normalizeClass(["znpb-input-range znpb-input-range--has-multiple-units", { ["znpb-input-range--disabled"]: vue.unref(disabled) }])
        }, [
          vue.createVNode(_sfc_main$1D, {
            ref_key: "rangebase",
            ref: rangebase,
            modelValue: vue.unref(rangeModel),
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(rangeModel) ? rangeModel.value = $event : null),
            type: "range",
            min: vue.unref(activeOption).min,
            max: vue.unref(activeOption).max,
            step: step.value,
            disabled: vue.unref(disabled),
            onKeydown: onRangeKeydown,
            onKeyup: onRangeKeyUp
          }, {
            suffix: vue.withCtx(() => [
              vue.createElementVNode("div", {
                class: "znpb-input-range__trackwidth",
                style: vue.normalizeStyle(vue.unref(trackWidth))
              }, null, 4)
            ]),
            _: 1
          }, 8, ["modelValue", "min", "max", "step", "disabled"]),
          vue.createElementVNode("label", _hoisted_1$X, [
            vue.createVNode(vue.unref(_sfc_main$1B), {
              ref_key: "inputNumberUnit",
              ref: inputNumberUnit,
              modelValue: vue.unref(computedValue),
              "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => vue.isRef(computedValue) ? computedValue.value = $event : null),
              class: "znpb-input-range-number",
              min: vue.unref(activeOption).min,
              max: vue.unref(activeOption).max,
              units: vue.unref(getUnits),
              step: step.value,
              shift_step: vue.unref(shiftStep),
              onIsCustomUnit: onCustomUnit,
              onUnitUpdate
            }, null, 8, ["modelValue", "min", "max", "units", "step", "shift_step"])
          ])
        ], 2);
      };
    }
  }));
  const __default__$Q = {
    name: "Tab"
  };
  const _sfc_main$1h = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$Q), {
    props: {
      name: null,
      icon: null,
      id: null,
      active: { type: Boolean }
    },
    setup(__props) {
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock(vue.Fragment, null, [
          vue.renderSlot(_ctx.$slots, "title"),
          vue.renderSlot(_ctx.$slots, "default")
        ], 64);
      };
    }
  }));
  var Tabs_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$W = ["onClick"];
  const __default__$P = {
    name: "Tabs"
  };
  const _sfc_main$1g = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$P), {
    props: {
      tabStyle: { default: "card" },
      titlePosition: { default: "start" },
      activeTab: { default: null },
      hasScroll: { default: () => [] }
    },
    emits: ["update:activeTab", "changed-tab"],
    setup(__props, { emit }) {
      var _a3;
      const props = __props;
      const tabs = vue.ref();
      const activeTab = vue.ref(props.activeTab);
      vue.watch(
        () => props.activeTab,
        (newValue) => {
          activeTab.value = newValue;
        }
      );
      function RenderComponent(props2) {
        return typeof props2["render-slot"] === "string" ? props2["render-slot"] : props2["render-slot"]();
      }
      function getIdForTab(tab) {
        var _a4;
        if (!tab) {
          return;
        }
        const props2 = tab.props;
        return (_a4 = props2 == null ? void 0 : props2.id) != null ? _a4 : kebabCase$1(props2.name);
      }
      const slots = vue.useSlots();
      if (slots.default) {
        tabs.value = getTabs(slots.default()).filter((child) => child.type.name === "Tab");
        activeTab.value = (_a3 = activeTab.value) != null ? _a3 : getIdForTab(tabs.value[0]);
      }
      function getTabs(vNodes) {
        let tabs2 = [];
        vNodes.forEach((tab) => {
          if (tab.type === vue.Fragment) {
            tabs2 = [...tabs2, ...getTabs(tab.children)];
          } else {
            tabs2.push(tab);
          }
        });
        return tabs2;
      }
      function selectTab(tab) {
        const tabId = getIdForTab(tab);
        activeTab.value = tabId;
        emit("changed-tab", tabId);
        emit("update:activeTab", tabId);
      }
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          class: vue.normalizeClass(["znpb-tabs", { [`znpb-tabs--${__props.tabStyle}`]: __props.tabStyle }])
        }, [
          vue.createElementVNode("div", {
            class: vue.normalizeClass(["znpb-tabs__header", { [`znpb-tabs__header--${__props.titlePosition}`]: __props.titlePosition }])
          }, [
            (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(tabs.value, (tab, index2) => {
              var _a4, _b;
              return vue.openBlock(), vue.createElementBlock("div", {
                key: index2,
                class: vue.normalizeClass(["znpb-tabs__header-item", {
                  "znpb-tabs__header-item--active": getIdForTab(tab) === activeTab.value,
                  [`znpb-tabs__header-item--${getIdForTab(tab)}`]: true
                }]),
                onClick: ($event) => selectTab(tab)
              }, [
                vue.createVNode(RenderComponent, {
                  "render-slot": (_b = (_a4 = tab == null ? void 0 : tab.children) == null ? void 0 : _a4.title) != null ? _b : tab.props.name
                }, null, 8, ["render-slot"])
              ], 10, _hoisted_1$W);
            }), 128))
          ], 2),
          vue.createElementVNode("div", {
            class: vue.normalizeClass(["znpb-tabs__content", { "znpb-fancy-scrollbar": __props.hasScroll }])
          }, [
            (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(tabs.value, (tab, index2) => {
              var _a4;
              return vue.withDirectives((vue.openBlock(), vue.createElementBlock("div", {
                key: index2,
                class: "znpb-tab__wrapper"
              }, [
                getIdForTab(tab) === activeTab.value ? (vue.openBlock(), vue.createBlock(RenderComponent, {
                  key: 0,
                  "render-slot": (_a4 = tab == null ? void 0 : tab.children) == null ? void 0 : _a4.default
                }, null, 8, ["render-slot"])) : vue.createCommentVNode("", true)
              ])), [
                [vue.vShow, getIdForTab(tab) === activeTab.value]
              ]);
            }), 128))
          ], 2)
        ], 2);
      };
    }
  }));
  var GradientOptions_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$V = { class: "znpb-gradient-options-wrapper" };
  const _hoisted_2$D = /* @__PURE__ */ vue.createTextVNode("deg");
  const _hoisted_3$r = { class: "znpb-radial-postion-wrapper" };
  const _hoisted_4$i = /* @__PURE__ */ vue.createTextVNode(" % ");
  const _hoisted_5$b = /* @__PURE__ */ vue.createTextVNode(" % ");
  const __default__$O = {
    name: "GradientOptions"
  };
  const _sfc_main$1f = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$O), {
    props: {
      modelValue: null
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const computedValue = vue.computed({
        get() {
          return props.modelValue;
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      const computedAngle = vue.computed({
        get() {
          return computedValue.value.angle;
        },
        set(newValue) {
          computedValue.value = __spreadProps(__spreadValues({}, computedValue.value), {
            angle: newValue
          });
        }
      });
      const computedPosition = vue.computed({
        get() {
          return computedValue.value.position || {};
        },
        set(newValue) {
          computedValue.value = __spreadProps(__spreadValues({}, computedValue.value), {
            position: newValue
          });
        }
      });
      const computedPositionX = vue.computed({
        get() {
          var _a3;
          return ((_a3 = computedValue.value.position) == null ? void 0 : _a3.x) || 50;
        },
        set(newValue) {
          computedPosition.value = __spreadProps(__spreadValues({}, computedPosition.value), {
            x: newValue
          });
        }
      });
      const computedPositionY = vue.computed({
        get() {
          var _a3;
          return ((_a3 = computedValue.value.position) == null ? void 0 : _a3.y) || 50;
        },
        set(newValue) {
          computedPosition.value = __spreadProps(__spreadValues({}, computedPosition.value), {
            y: newValue
          });
        }
      });
      function onTabChange(tabId) {
        computedValue.value = __spreadProps(__spreadValues({}, computedValue.value), {
          type: tabId
        });
      }
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$V, [
          vue.createVNode(vue.unref(_sfc_main$1k), {
            title: _ctx.$translate("gradient_type"),
            class: "znpb-gradient__type"
          }, {
            default: vue.withCtx(() => [
              vue.createVNode(vue.unref(_sfc_main$1g), {
                "tab-style": "minimal",
                "active-tab": vue.unref(computedValue).type,
                onChangedTab: onTabChange
              }, {
                default: vue.withCtx(() => [
                  vue.createVNode(vue.unref(_sfc_main$1h), { name: "Linear" }, {
                    default: vue.withCtx(() => [
                      vue.createVNode(vue.unref(_sfc_main$1k), {
                        title: _ctx.$translate("gradient_angle"),
                        class: "znpb-gradient__angle"
                      }, {
                        default: vue.withCtx(() => [
                          vue.createVNode(vue.unref(_sfc_main$1j), {
                            modelValue: vue.unref(computedAngle),
                            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(computedAngle) ? computedAngle.value = $event : null),
                            min: 0,
                            max: 360,
                            step: 1
                          }, {
                            default: vue.withCtx(() => [
                              _hoisted_2$D
                            ]),
                            _: 1
                          }, 8, ["modelValue"])
                        ]),
                        _: 1
                      }, 8, ["title"])
                    ]),
                    _: 1
                  }),
                  vue.createVNode(vue.unref(_sfc_main$1h), { name: "Radial" }, {
                    default: vue.withCtx(() => [
                      vue.createElementVNode("div", _hoisted_3$r, [
                        vue.createVNode(vue.unref(_sfc_main$1k), {
                          title: "Position X",
                          layout: "inline"
                        }, {
                          default: vue.withCtx(() => [
                            vue.createVNode(vue.unref(_sfc_main$1C), {
                              modelValue: vue.unref(computedPositionX),
                              "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => vue.isRef(computedPositionX) ? computedPositionX.value = $event : null),
                              min: 0,
                              max: 100,
                              step: 1
                            }, {
                              default: vue.withCtx(() => [
                                _hoisted_4$i
                              ]),
                              _: 1
                            }, 8, ["modelValue"])
                          ]),
                          _: 1
                        }),
                        vue.createVNode(vue.unref(_sfc_main$1k), {
                          title: "Position Y",
                          layout: "inline"
                        }, {
                          default: vue.withCtx(() => [
                            vue.createVNode(vue.unref(_sfc_main$1C), {
                              modelValue: vue.unref(computedPositionY),
                              "onUpdate:modelValue": _cache[2] || (_cache[2] = ($event) => vue.isRef(computedPositionY) ? computedPositionY.value = $event : null),
                              min: 0,
                              max: 100,
                              step: 1
                            }, {
                              default: vue.withCtx(() => [
                                _hoisted_5$b
                              ]),
                              _: 1
                            }, 8, ["modelValue"])
                          ]),
                          _: 1
                        })
                      ])
                    ]),
                    _: 1
                  })
                ]),
                _: 1
              }, 8, ["active-tab"])
            ]),
            _: 1
          }, 8, ["title"]),
          vue.createVNode(vue.unref(_sfc_main$1k), {
            title: _ctx.$translate("gradient_bar")
          }, {
            default: vue.withCtx(() => [
              vue.createVNode(_sfc_main$1l, {
                modelValue: vue.unref(computedValue),
                "onUpdate:modelValue": _cache[3] || (_cache[3] = ($event) => vue.isRef(computedValue) ? computedValue.value = $event : null),
                class: "znpb-gradient__bar"
              }, null, 8, ["modelValue"])
            ]),
            _: 1
          }, 8, ["title"])
        ]);
      };
    }
  }));
  var OneGradient_vue_vue_type_style_index_0_scoped_true_lang = "";
  const _hoisted_1$U = { class: "znpb-gradient-preview-transparent" };
  const __default__$N = {
    name: "OneGradient"
  };
  const _sfc_main$1e = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$N), {
    props: {
      config: null,
      round: { type: Boolean }
    },
    setup(__props) {
      const props = __props;
      const getGradientPreviewStyle = vue.computed(() => {
        const style = {};
        const gradient = [];
        const colors = [];
        let position = "90deg";
        const colorsCopy = [...props.config.colors].sort((a, b) => {
          return a.position > b.position ? 1 : -1;
        });
        colorsCopy.forEach((color) => {
          colors.push(`${color.color} ${color.position}%`);
        });
        if (props.config.type === "radial") {
          const { x, y } = props.config.position || { x: 50, y: 50 };
          position = `circle at ${x}% ${y}%`;
        } else {
          position = `${props.config.angle}deg`;
        }
        gradient.push(`${props.config.type}-gradient(${position}, ${colors.join(", ")})`);
        style["background"] = gradient.join(", ");
        return style;
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$U, [
          vue.createElementVNode("div", {
            class: vue.normalizeClass(["znpb-gradient-preview", { "gradient-type-rounded": __props.round }]),
            style: vue.normalizeStyle(vue.unref(getGradientPreviewStyle))
          }, null, 6)
        ]);
      };
    }
  }));
  var OneGradient = /* @__PURE__ */ _export_sfc(_sfc_main$1e, [["__scopeId", "data-v-08436a44"]]);
  var GradientElement_vue_vue_type_style_index_0_lang = "";
  const __default__$M = {
    name: "GradientElement"
  };
  const _sfc_main$1d = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$M), {
    props: {
      config: null,
      showRemove: { type: Boolean, default: true },
      isActive: { type: Boolean }
    },
    emits: ["change-active-gradient", "delete-gradient"],
    setup(__props) {
      const props = __props;
      const localConfig = vue.computed({
        get() {
          return props.config;
        },
        set(newConfig) {
          localConfig.value = newConfig;
        }
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          class: vue.normalizeClass(["znpb-gradient-element", { "znpb-gradient-element--active": __props.isActive }])
        }, [
          vue.createVNode(OneGradient, {
            round: true,
            config: vue.unref(localConfig),
            onClick: _cache[0] || (_cache[0] = ($event) => _ctx.$emit("change-active-gradient", __props.config))
          }, null, 8, ["config"]),
          __props.showRemove ? (vue.openBlock(), vue.createBlock(_sfc_main$1K, {
            key: 0,
            icon: "close",
            onClick: _cache[1] || (_cache[1] = vue.withModifiers(($event) => _ctx.$emit("delete-gradient"), ["stop"]))
          })) : vue.createCommentVNode("", true)
        ], 2);
      };
    }
  }));
  const cache$1 = vue.ref({});
  function useSelectServerData(config) {
    let requester = vue.inject("serverRequester", null);
    const items2 = vue.ref([]);
    if (!requester) {
      if (window.zb.admin) {
        requester = window.zb.admin.serverRequest;
      }
    }
    function fetch2(config2) {
      if (!requester) {
        return Promise.reject("Server requester not provided");
      }
      const cacheKey = generateCacheKey(vue.toRaw(config2));
      const saveItemsCache = generateItemsCacheKey(vue.toRaw(config2));
      if (cache$1[cacheKey]) {
        saveItems(saveItemsCache, cache$1[cacheKey]);
        return Promise.resolve(cache$1[cacheKey]);
      } else {
        return new Promise((resolve, reject) => {
          config2.useCache = true;
          requester.request(
            {
              type: "get_input_select_options",
              config: config2
            },
            (response) => {
              saveItems(saveItemsCache, response.data);
              resolve(response.data);
            },
            function(message) {
              reject(message);
            }
          );
        });
      }
    }
    function getItems(config2) {
      const saveItemsCache = generateItemsCacheKey(vue.toRaw(config2));
      return get(items2.value, saveItemsCache, []);
    }
    function getItem(config2, id) {
      const saveItemsCache = generateItemsCacheKey(vue.toRaw(config2));
      const cachedItems = get(items2.value, saveItemsCache, []);
      return cachedItems.find((item) => item.id === id);
    }
    function generateItemsCacheKey(config2) {
      const { server_callback_method, server_callback_args } = config2;
      return hash$3({
        server_callback_method,
        server_callback_args
      });
    }
    function generateCacheKey(data2) {
      const _a3 = data2, { server_callback_method, server_callback_args, page, searchKeyword } = _a3, remainingProperties = __objRest(_a3, ["server_callback_method", "server_callback_args", "page", "searchKeyword"]);
      return hash$3({
        server_callback_method,
        server_callback_args,
        page,
        searchKeyword
      });
    }
    function saveItems(key, newItems) {
      const existingItems = get(items2.value, key, []);
      items2.value[key] = unionBy$1(existingItems, newItems, "id");
    }
    return {
      fetch: fetch2,
      getItem,
      getItems
    };
  }
  var InputSelect_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$1c = {
    name: "InputSelect",
    props: {
      modelValue: {
        type: [String, Number, Array, Boolean]
      },
      options: {
        type: Array,
        default: []
      },
      filterable: {
        type: Boolean
      },
      server_callback_method: {
        type: String
      },
      server_callback_args: {},
      server_callback_per_page: {
        type: Number,
        default: 25
      },
      placeholder: {
        type: String
      },
      placement: {
        type: String,
        required: false,
        default: "bottom"
      },
      style_type: {
        type: String,
        required: false
      },
      addable: {
        type: Boolean,
        required: false,
        default: false
      },
      multiple: {
        type: Boolean,
        required: false,
        default: false
      },
      local_callback_method: {
        type: String,
        required: false
      },
      filter_id: {
        type: String,
        required: false
      }
    },
    setup(props, { emit }) {
      const optionWrapper = vue.ref(null);
      const searchInput = vue.ref(null);
      const searchKeyword = vue.ref("");
      const showDropdown = vue.ref(false);
      const loading2 = vue.ref(false);
      const loadingTitle = vue.ref(false);
      const stopSearch = vue.ref(false);
      const tooltipWidth = vue.ref(null);
      const elementInfo = vue.inject("elementInfo", null);
      let page = 1;
      const { fetch: fetch2, getItems } = useSelectServerData();
      const computedModelValue = vue.computed(() => {
        if (props.modelValue && props.multiple && !Array.isArray(props.modelValue)) {
          return [props.modelValue];
        }
        if (!props.modelValue && props.multiple) {
          return [];
        }
        return props.modelValue;
      });
      const items2 = vue.computed(() => {
        let options2 = [...props.options];
        if (props.server_callback_method) {
          const serverOptions = getItems({
            server_callback_method: props.server_callback_method,
            server_callback_args: props.server_callback_args
          });
          if (serverOptions.length > 0) {
            options2.push(...serverOptions);
          }
        }
        if (props.addable && props.modelValue) {
          if (props.multiple) {
            computedModelValue.value.forEach((savedValue) => {
              if (!options2.find((option) => option.id === savedValue)) {
                options2.push({
                  name: savedValue,
                  id: savedValue
                });
              }
            });
          } else if (!options2.find((option) => option.id === computedModelValue.value)) {
            options2.push({
              name: props.modelValue,
              id: props.modelValue
            });
          }
        }
        if (props.local_callback_method) {
          const localOptions = window[props.local_callback_method];
          if (typeof localOptions === "function") {
            options2.push(...localOptions(options2, elementInfo));
          }
        }
        if (props.filter_id) {
          const { applyFilters: applyFilters2 } = window.zb.hooks;
          options2 = applyFilters2(props.filter_id, options2, vue.unref(elementInfo));
        }
        options2 = options2.map((option) => {
          let isSelected = false;
          if (props.multiple) {
            isSelected = computedModelValue.value.includes(option.id);
          } else {
            isSelected = computedModelValue.value === option.id;
          }
          return __spreadProps(__spreadValues({}, option), {
            isSelected
          });
        });
        return options2;
      });
      const visibleItems = vue.computed(() => {
        let options2 = items2.value;
        if (props.filterable || props.addable) {
          if (searchKeyword.value.length > 0) {
            options2 = options2.filter((optionConfig) => {
              return optionConfig.name.toLowerCase().indexOf(searchKeyword.value.toLowerCase()) !== -1;
            });
          }
        }
        if (props.multiple) {
          options2.sort((item) => item.isSelected ? -1 : 1);
        }
        return options2;
      });
      vue.watch(searchKeyword, () => {
        stopSearch.value = false;
        debouncedGetItems();
      });
      vue.watch(showDropdown, (newValue) => {
        if (!newValue) {
          searchKeyword.value = "";
        }
      });
      const debouncedGetItems = debounce$1(() => {
        loadNext();
      }, 300);
      function loadNext() {
        if (!props.server_callback_method) {
          return;
        }
        if (loading2.value) {
          return;
        }
        loading2.value = true;
        const include = props.modelValue;
        fetch2({
          server_callback_method: props.server_callback_method,
          server_callback_args: props.server_callback_args,
          page,
          searchKeyword: searchKeyword.value,
          include
        }).then((response) => {
          if (props.server_callback_per_page === -1) {
            stopSearch.value = true;
          } else if (response.length < props.server_callback_per_page) {
            stopSearch.value = true;
          }
          loading2.value = false;
          loadingTitle.value = false;
        });
      }
      function onScrollEnd() {
        if (!props.server_callback_method) {
          return;
        }
        if (props.server_callback_per_page === -1) {
          return;
        }
        if (!stopSearch.value) {
          page++;
          loadNext();
        }
      }
      if (props.server_callback_method) {
        loadNext();
      }
      const showPlaceholder = vue.computed(() => {
        return typeof props.modelValue === "undefined" || props.multiple && computedModelValue.value.length === 0;
      });
      const dropdownPlaceholder = vue.computed(() => {
        if (showPlaceholder.value) {
          return props.placeholder;
        } else {
          if (props.multiple) {
            const activeTitles = items2.value.filter((option) => computedModelValue.value.includes(option.id));
            if (activeTitles) {
              return activeTitles.map((item) => item.name).join(", ");
            } else if (props.addable) {
              return computedModelValue.value.join(",");
            }
          } else {
            const activeTitle = items2.value.find((option) => option.id === computedModelValue.value);
            if (activeTitle) {
              return activeTitle.name;
            } else if (props.addable) {
              return props.modelValue;
            }
          }
          return null;
        }
      });
      vue.watchEffect(() => {
        if (dropdownPlaceholder.value === null && props.server_callback_method) {
          loadingTitle.value = true;
        }
      });
      function onOptionSelect(option) {
        if (props.multiple) {
          const oldValues = [...computedModelValue.value];
          if (option.isSelected) {
            const selectedOptionIndex = oldValues.indexOf(option.id);
            oldValues.splice(selectedOptionIndex, 1);
            emit("update:modelValue", oldValues);
          } else {
            oldValues.push(option.id);
            emit("update:modelValue", oldValues);
          }
        } else {
          emit("update:modelValue", option.id);
          showDropdown.value = false;
        }
      }
      function onModalShow() {
        if (optionWrapper.value) {
          tooltipWidth.value = optionWrapper.value.getBoundingClientRect().width;
        }
        if ((props.filterable || props.addable) && searchInput.value) {
          searchInput.value.focus();
        }
      }
      function getStyle(font) {
        if (props.style_type === "font-select") {
          return {
            fontFamily: font
          };
        } else
          return null;
      }
      function addItem() {
        onOptionSelect({
          name: searchKeyword.value,
          id: searchKeyword.value
        });
        showDropdown.value = false;
      }
      function onInputKeydown(event2) {
        if (props.addable && event2.keyCode === 13) {
          addItem();
        }
      }
      return {
        optionWrapper,
        tooltipWidth,
        searchInput,
        searchKeyword,
        dropdownPlaceholder,
        onOptionSelect,
        onScrollEnd,
        onModalShow,
        getStyle,
        addItem,
        onInputKeydown,
        loading: loading2,
        showDropdown,
        stopSearch,
        items: items2,
        loadingTitle,
        visibleItems,
        showPlaceholder
      };
    }
  };
  const _hoisted_1$T = {
    key: 1,
    class: "znpb-option-selectOptionPlaceholderText"
  };
  const _hoisted_2$C = { class: "znpb-inputDropdownIcon-wrapper" };
  const _hoisted_3$q = { class: "znpb-option-selectOptionListWrapper" };
  const _hoisted_4$h = ["onClick"];
  const _hoisted_5$a = {
    key: 1,
    class: "znpb-option-selectOptionListNoMoreText"
  };
  function _sfc_render$p(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_Loader = vue.resolveComponent("Loader");
    const _component_Icon = vue.resolveComponent("Icon");
    const _component_BaseInput = vue.resolveComponent("BaseInput");
    const _component_ListScroll = vue.resolveComponent("ListScroll");
    const _component_Tooltip = vue.resolveComponent("Tooltip");
    const _directive_znpb_tooltip = vue.resolveDirective("znpb-tooltip");
    return vue.openBlock(), vue.createBlock(_component_Tooltip, {
      show: $setup.showDropdown,
      "onUpdate:show": _cache[2] || (_cache[2] = ($event) => $setup.showDropdown = $event),
      "append-to": "element",
      placement: $props.placement,
      trigger: "click",
      "close-on-outside-click": true,
      "tooltip-class": "znpb-option-selectTooltip hg-popper--no-padding",
      class: "znpb-option-selectWrapper",
      "tooltip-style": { width: $setup.tooltipWidth + "px" },
      "show-arrows": false,
      strategy: "fixed",
      modifiers: [
        {
          name: "preventOverflow",
          enabled: true
        },
        {
          name: "hide",
          enabled: true
        },
        {
          name: "flip",
          options: {
            fallbackPlacements: ["bottom", "top", "right", "left"]
          }
        }
      ],
      onShow: $setup.onModalShow
    }, {
      content: vue.withCtx(() => [
        vue.createElementVNode("div", _hoisted_3$q, [
          $props.filterable || $props.addable ? (vue.openBlock(), vue.createBlock(_component_BaseInput, {
            key: 0,
            ref: "searchInput",
            modelValue: $setup.searchKeyword,
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $setup.searchKeyword = $event),
            class: "znpb-option-selectOptionListSearchInput",
            placeholder: $props.addable ? _ctx.$translate("search_or_add") : _ctx.$translate("search"),
            clearable: true,
            icon: "search",
            autocomplete: "off",
            onKeydown: $setup.onInputKeydown
          }, vue.createSlots({ _: 2 }, [
            $props.addable && $setup.searchKeyword.length > 0 ? {
              name: "after-input",
              fn: vue.withCtx(() => [
                vue.withDirectives(vue.createVNode(_component_Icon, {
                  icon: "plus",
                  class: "znpb-inputAddableIcon",
                  onClick: vue.withModifiers($setup.addItem, ["stop", "prevent"])
                }, null, 8, ["onClick"]), [
                  [_directive_znpb_tooltip, _ctx.$translate("add_new_item")]
                ])
              ]),
              key: "0"
            } : void 0
          ]), 1032, ["modelValue", "placeholder", "onKeydown"])) : vue.createCommentVNode("", true),
          vue.createVNode(_component_ListScroll, {
            loading: $setup.loading,
            "onUpdate:loading": _cache[1] || (_cache[1] = ($event) => $setup.loading = $event),
            class: "znpb-menuList znpb-mh-200",
            onScrollEnd: $setup.onScrollEnd
          }, {
            default: vue.withCtx(() => [
              (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($setup.visibleItems, (option) => {
                return vue.openBlock(), vue.createElementBlock("div", {
                  key: option.id,
                  class: vue.normalizeClass(["znpb-menuListItem", {
                    "znpb-menuListItem--selected": !option.is_label && option.isSelected,
                    "znpb-menuListItem--is-label": option.is_label,
                    "znpb-menuListItem--is-group_item": option.is_group_item
                  }]),
                  style: vue.normalizeStyle($setup.getStyle(option.name)),
                  onClick: vue.withModifiers(($event) => $setup.onOptionSelect(option), ["stop"])
                }, vue.toDisplayString(option.name), 15, _hoisted_4$h);
              }), 128))
            ]),
            _: 1
          }, 8, ["loading", "onScrollEnd"]),
          $setup.stopSearch ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_5$a, vue.toDisplayString(_ctx.$translate("no_more_items")), 1)) : vue.createCommentVNode("", true)
        ])
      ]),
      default: vue.withCtx(() => [
        vue.createElementVNode("div", {
          ref: "optionWrapper",
          class: vue.normalizeClass(["znpb-option-selectOptionPlaceholder", {
            [`znpb-option-selectOptionPlaceholder--real`]: $setup.showPlaceholder
          }])
        }, [
          $setup.loadingTitle ? (vue.openBlock(), vue.createBlock(_component_Loader, {
            key: 0,
            size: 14
          })) : (vue.openBlock(), vue.createElementBlock("span", _hoisted_1$T, vue.toDisplayString($setup.dropdownPlaceholder), 1)),
          vue.createElementVNode("span", _hoisted_2$C, [
            vue.createVNode(_component_Icon, {
              icon: "select",
              class: "znpb-inputDropdownIcon",
              rotate: $setup.showDropdown ? "180" : false
            }, null, 8, ["rotate"])
          ])
        ], 2)
      ]),
      _: 1
    }, 8, ["show", "placement", "tooltip-style", "onShow"]);
  }
  var InputSelect = /* @__PURE__ */ _export_sfc(_sfc_main$1c, [["render", _sfc_render$p]]);
  var PresetInput_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$S = { class: "znpb-preset-input-wrapper" };
  const __default__$L = {
    name: "PresetInput"
  };
  const _sfc_main$1b = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$L), {
    props: {
      isGradient: { type: Boolean, default: true }
    },
    emits: ["save-preset", "cancel"],
    setup(__props, { emit }) {
      const props = __props;
      const presetName = vue.ref("");
      const gradientType = vue.ref("local");
      const hasError = vue.ref(false);
      const { translate: translate2 } = window.zb.i18n;
      const gradientTypes = vue.ref([
        {
          id: "local",
          name: translate2("local")
        },
        {
          id: "global",
          name: translate2("global")
        }
      ]);
      function savePreset() {
        if (presetName.value.length === 0) {
          hasError.value = true;
          return;
        }
        if (props.isGradient) {
          emit("save-preset", presetName.value, gradientType.value);
        } else {
          emit("save-preset", presetName.value);
        }
      }
      vue.watch(hasError, (newValue) => {
        if (newValue) {
          setTimeout(() => {
            hasError.value = false;
          }, 500);
        }
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$S, [
          vue.createVNode(vue.unref(_sfc_main$1D), {
            modelValue: presetName.value,
            "onUpdate:modelValue": _cache[2] || (_cache[2] = ($event) => presetName.value = $event),
            placeholder: __props.isGradient ? _ctx.$translate("save_gradient_title") : _ctx.$translate("add_preset_title"),
            class: vue.normalizeClass({ "znpb-backgroundGradient__nameInput": __props.isGradient }),
            error: hasError.value
          }, vue.createSlots({ _: 2 }, [
            __props.isGradient ? {
              name: "prepend",
              fn: vue.withCtx(() => [
                vue.createVNode(vue.unref(InputSelect), {
                  modelValue: gradientType.value,
                  "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => gradientType.value = $event),
                  class: "znpb-backgroundGradient__typeDropdown",
                  options: gradientTypes.value,
                  placeholder: "Type"
                }, null, 8, ["modelValue", "options"])
              ]),
              key: "0"
            } : {
              name: "append",
              fn: vue.withCtx(() => [
                vue.createVNode(vue.unref(_sfc_main$1K), {
                  icon: "check",
                  onMousedown: vue.withModifiers(savePreset, ["stop"])
                }, null, 8, ["onMousedown"]),
                vue.createVNode(vue.unref(_sfc_main$1K), {
                  icon: "close",
                  onMousedown: _cache[1] || (_cache[1] = vue.withModifiers(($event) => _ctx.$emit("cancel", true), ["prevent"]))
                })
              ]),
              key: "1"
            }
          ]), 1032, ["modelValue", "placeholder", "class", "error"]),
          __props.isGradient ? (vue.openBlock(), vue.createElementBlock(vue.Fragment, { key: 0 }, [
            vue.createVNode(vue.unref(_sfc_main$1K), {
              icon: "check",
              class: "znpb-backgroundGradient__action",
              onClick: vue.withModifiers(savePreset, ["stop"])
            }, null, 8, ["onClick"]),
            vue.createVNode(vue.unref(_sfc_main$1K), {
              icon: "close",
              class: "znpb-backgroundGradient__action",
              onClick: _cache[3] || (_cache[3] = vue.withModifiers(($event) => _ctx.$emit("cancel", true), ["stop"]))
            })
          ], 64)) : vue.createCommentVNode("", true)
        ]);
      };
    }
  }));
  var HostsManager = () => {
    let hosts2 = [];
    let iframes = [];
    const getHosts = () => {
      return hosts2;
    };
    const getIframes = () => {
      return iframes;
    };
    const resetHosts = () => {
      hosts2 = [document];
      iframes = [];
    };
    const fetchHosts = () => {
      resetHosts();
      const DOMIframes = document.querySelectorAll("iframe");
      DOMIframes.forEach((iframe) => {
        if (iframe.contentDocument) {
          hosts2.push(iframe.contentDocument);
          iframes.push(iframe);
        }
      });
      return globalThis;
    };
    return {
      getHosts,
      getIframes,
      fetchHosts
    };
  };
  var EventsManager = () => {
    let handled = false;
    const handle = () => {
      handled = true;
    };
    const isHandled = () => {
      return handled;
    };
    const reset = () => {
      handled = false;
    };
    return {
      handle,
      isHandled,
      reset
    };
  };
  function matches(element, value, context = null) {
    if (!value) {
      return false;
    } else if (value === "> *") {
      return matches(element.parentElement, context);
    } else if (value instanceof HTMLElement && value.nodeType > 0) {
      return element === value;
    } else if (typeof value === "string") {
      return element.matches(value);
    } else if (value instanceof NodeList || value instanceof Array) {
      return [...value].includes(element);
    } else if (typeof value === "function") {
      return value(element);
    }
    return false;
  }
  function closest(element, target, context = null) {
    let current = element;
    do {
      if (current && matches(current, target, context)) {
        return current;
      }
      if (current === context) {
        return false;
      }
      current = current.parentElement;
    } while (current && current !== document.body);
    return null;
  }
  var safeIsNaN = Number.isNaN || function ponyfill(value) {
    return typeof value === "number" && value !== value;
  };
  function isEqual(first, second) {
    if (first === second) {
      return true;
    }
    if (safeIsNaN(first) && safeIsNaN(second)) {
      return true;
    }
    return false;
  }
  function areInputsEqual(newInputs, lastInputs) {
    if (newInputs.length !== lastInputs.length) {
      return false;
    }
    for (var i = 0; i < newInputs.length; i++) {
      if (!isEqual(newInputs[i], lastInputs[i])) {
        return false;
      }
    }
    return true;
  }
  function memoizeOne(resultFn, isEqual2) {
    if (isEqual2 === void 0) {
      isEqual2 = areInputsEqual;
    }
    var cache2 = null;
    function memoized() {
      var newArgs = [];
      for (var _i = 0; _i < arguments.length; _i++) {
        newArgs[_i] = arguments[_i];
      }
      if (cache2 && cache2.lastThis === this && isEqual2(newArgs, cache2.lastArgs)) {
        return cache2.lastResult;
      }
      var lastResult = resultFn.apply(this, newArgs);
      cache2 = {
        lastResult,
        lastArgs: newArgs,
        lastThis: this
      };
      return lastResult;
    }
    memoized.clear = function clear() {
      cache2 = null;
    };
    return memoized;
  }
  var EventScheduler = (callbacks) => {
    const memoizedMove = memoizeOne((event2) => {
      callbacks.onMove(event2);
    });
    const move = rafSchd$1(memoizedMove);
    const cancel = () => {
      move.cancel();
    };
    return {
      move,
      cancel
    };
  };
  const _AbstractEvent = class {
    constructor(data2) {
      __publicField(this, "cancelled");
      __publicField(this, "data");
      this.cancelled = false;
      this.data = data2;
    }
    isCanceled() {
      return this.cancelled;
    }
    cancel() {
      if (this.isCancelable) {
        this.cancelled = true;
      }
    }
    get type() {
      return _AbstractEvent.type;
    }
    get isCancelable() {
      return _AbstractEvent.cancelable;
    }
  };
  let AbstractEvent = _AbstractEvent;
  __publicField(AbstractEvent, "type", "Event");
  __publicField(AbstractEvent, "cancelable", false);
  class MoveEvent extends AbstractEvent {
  }
  __publicField(MoveEvent, "type", "sortable:move");
  __publicField(MoveEvent, "cancelable", true);
  class Start extends AbstractEvent {
  }
  __publicField(Start, "type", "sortable:start");
  __publicField(Start, "cancelable", true);
  class End extends AbstractEvent {
  }
  __publicField(End, "type", "sortable:end");
  class ChangeEvent extends AbstractEvent {
  }
  __publicField(ChangeEvent, "type", "sortable:change");
  __publicField(ChangeEvent, "cancelable", true);
  class Drop extends AbstractEvent {
  }
  __publicField(Drop, "type", "sortable:drop");
  const hosts = HostsManager();
  const eventsManager = EventsManager();
  const getOffset = (currentDocument) => {
    const frameElement = hosts.getIframes().find((iframe) => {
      return iframe.contentDocument === currentDocument;
    });
    if (void 0 !== frameElement) {
      const { left: left2, top: top2 } = frameElement.getBoundingClientRect();
      return {
        left: left2,
        top: top2
      };
    }
    return {
      left: 0,
      top: 0
    };
  };
  memoizeOne(getOffset);
  const _sfc_main$1a = {
    name: "Sortable",
    props: {
      modelValue: {
        required: false,
        type: Array,
        default() {
          return [];
        }
      },
      allowDuplicate: {
        type: Boolean,
        default: false
      },
      duplicateCallback: {
        type: Function
      },
      tag: {
        type: String,
        required: false,
        default: "div"
      },
      dragTreshold: {
        type: Number,
        required: false,
        default: 5
      },
      dragDelay: {
        type: Number,
        required: false,
        default: 0
      },
      handle: {
        type: String,
        required: false,
        default: null
      },
      draggable: {
        type: String,
        required: false,
        default: "> *"
      },
      disabled: {
        type: Boolean,
        required: false,
        default: false
      },
      group: {
        type: [String, Object, Array],
        required: false,
        default: null
      },
      sort: {
        type: Boolean,
        required: false,
        default: true
      },
      placeholder: {
        type: Boolean,
        required: false,
        default: true
      },
      cssClasses: {
        type: Object,
        required: false,
        default() {
          return {};
        }
      },
      revert: {
        type: Boolean,
        required: false,
        default: true
      },
      axis: {
        type: String,
        required: false,
        default: null
      },
      preserveLastLocation: {
        type: Boolean,
        required: false,
        default: true
      }
    },
    setup(props, { slots, emit }) {
      let duplicateValue = false;
      let draggedItem = null;
      let dragItemInfo = null;
      let dragDelayCompleted = null;
      let dimensions = null;
      let initialX = null;
      let initialY = null;
      let currentDocument = null;
      let helperNode = null;
      let placeholderNode = null;
      let dragTimeout = null;
      let eventScheduler = null;
      let hasHelperSlot = false;
      let childItems = [];
      let hasPlaceholderSlot = false;
      let lastEvent = null;
      const sortableContainer = vue.ref(null);
      const dragging = vue.ref(null);
      const sortableItems = vue.ref([]);
      const helper = vue.ref(null);
      const placeholder = vue.ref(null);
      const computedCssClasses = vue.computed(() => {
        const defaultClasses = {
          body: "vuebdnd-draggable--active",
          source: "vuebdnd__source--dragging",
          "source:container": "vuebdnd__source-container--dragging",
          helper: "vuebdnd__helper",
          placeholder: "vuebdnd__placeholder",
          "placeholder:container": "vuebdnd__placeholder-container"
        };
        return __spreadValues(__spreadValues({}, defaultClasses), props.cssClasses);
      });
      const groupInfo = vue.computed(() => {
        let group = props.group;
        if (!group || typeof group !== "object") {
          group = {
            name: group
          };
        }
        return group;
      });
      const getCssClass = (cssClass) => {
        return computedCssClasses.value[cssClass] || null;
      };
      const canPut = (dragItemInfo2) => {
        const dragGroupInfo = dragItemInfo2.group;
        const sameGroup = dragGroupInfo.value.name === groupInfo.value.name;
        const put = dragGroupInfo.put || null;
        if (put === null && sameGroup) {
          return true;
        } else if (put === null || put === false) {
          return false;
        } else if (typeof put === "function") {
          return put(dragItemInfo2, groupInfo);
        } else {
          if (put === true) {
            return true;
          } else if (typeof put === "string") {
            return put === dragGroupInfo.value.name;
          } else if (Array.isArray(put)) {
            return put.indexOf(dragGroupInfo.value.name) > -1;
          }
        }
        return false;
      };
      const movePlaceholder = (container, element, before) => {
        if (before === null) {
          if (dragItemInfo.lastContainer !== container) {
            removeCssClass("placeholder:container");
            if (props.placeholder) {
              placeholderNode.remove();
            }
            dragItemInfo.lastContainer = null;
          }
        } else {
          if (dragItemInfo.lastContainer !== container) {
            removeCssClass("placeholder:container");
          }
          if (props.placeholder) {
            container.insertBefore(placeholderNode, element);
          }
          if (dragItemInfo.lastContainer !== container) {
            addCssClass("placeholder:container");
          }
          const { container: from, item, index: startIndex, to, newIndex, toItem } = dragItemInfo;
          const changeEvent = new ChangeEvent({
            from,
            item,
            startIndex,
            to,
            newIndex,
            toItem,
            before
          });
          dragItemInfo.lastContainer = container;
          emit("change", changeEvent);
        }
      };
      const onDragStart = (event2) => {
        event2.preventDefault();
      };
      const getEvents = () => {
        return {
          onStart: [onDragStart],
          onMove: onMouseMove
        };
      };
      function onMouseDown(event2) {
        if (eventsManager.isHandled()) {
          return;
        }
        if (event2.button !== 0 || event2.ctrlKey || event2.metaKey) {
          return;
        }
        if (event2.target.isContentEditable) {
          return;
        }
        draggedItem = closest(event2.target, props.draggable, sortableContainer.value);
        const sortableDomElements = getDomElementsFromSortableItems();
        if (!draggedItem || !sortableDomElements.includes(draggedItem)) {
          return;
        }
        if (props.handle && !closest(event2.target, props.handle)) {
          return;
        }
        dragItemInfo = getInfoFromTarget(draggedItem);
        if (!canPull()) {
          return;
        }
        eventsManager.handle();
        dragDelayCompleted = !props.dragDelay;
        if (props.dragDelay) {
          clearTimeout(dragTimeout);
          dragTimeout = setTimeout(() => {
            dragDelayCompleted = true;
          }, props.dragDelay);
        }
        dimensions = draggedItem.getBoundingClientRect();
        const { clientX, clientY } = event2;
        initialX = clientX;
        initialY = clientY;
        currentDocument = event2.view.document;
        hosts.fetchHosts();
        hosts.getHosts().forEach((host) => {
          host.addEventListener("mousemove", onDraggableMouseMove);
          host.addEventListener("mouseup", finishDrag);
        });
      }
      const detachEvents = () => {
        hosts.getHosts().forEach((host) => {
          host.removeEventListener("mousemove", onDraggableMouseMove);
          host.removeEventListener("mouseup", finishDrag);
        });
        eventsManager.reset();
      };
      const startDrag = (event2) => {
        const startEvent = new Start(dragItemInfo);
        emit("start", startEvent);
        if (startEvent.isCanceled()) {
          finishDrag();
          return;
        }
        currentDocument.body.style.userSelect = "none";
        attachPlaceholder();
        attachHelper();
        addCssClass("body");
        addCssClass("source");
        addCssClass("source:container");
        addCssClass("placeholder:container");
        helperNode.style.willChange = "transform";
        helperNode.style.zIndex = 99999;
        helperNode.style.pointerEvents = "none";
        helperNode.style.position = "fixed";
        if (hasHelperSlot) {
          draggedItem.style.display = "none";
          const { width, height } = helperNode.getBoundingClientRect();
          helperNode.style.left = `${initialX - width / 2}px`;
          helperNode.style.top = `${initialY - height / 2}px`;
        } else {
          const { width, height, top: top2, left: left2 } = dimensions;
          if (groupInfo.value.pull !== "clone") {
            helperNode.style.left = `${left2}px`;
          }
          helperNode.style.top = `${top2}px`;
          helperNode.style.width = `${width}px`;
          helperNode.style.height = `${height}px`;
        }
      };
      const applyCssClass = (type, action) => {
        const cssClass = getCssClass(type);
        let node = null;
        if (!cssClass) {
          return;
        }
        if (type === "body") {
          node = currentDocument.body;
        } else if (type === "helper") {
          node = helperNode;
        } else if (type === "placeholder") {
          node = placeholderNode;
        } else if (type === "source") {
          node = draggedItem;
        } else if (type === "source:container") {
          node = draggedItem.parentNode;
        } else if (type === "placeholder:container") {
          node = placeholderNode.parentNode;
        }
        if (node) {
          node.classList[action](cssClass);
        }
      };
      const addCssClass = (type) => {
        applyCssClass(type, "add");
      };
      const removeCssClass = (type) => {
        applyCssClass(type, "remove");
      };
      const attachHelper = () => {
        if (hasHelperSlot) {
          helperNode = helper.value;
          sortableContainer.value.insertBefore(helperNode, draggedItem);
          draggedItem.insertAdjacentElement("afterend", helperNode);
        } else if (groupInfo.value.pull === "clone") {
          const clone = draggedItem.cloneNode(true);
          sortableContainer.value.insertBefore(clone, draggedItem);
          helperNode = clone;
        } else {
          helperNode = draggedItem;
        }
        addCssClass("helper");
      };
      function detachHelper() {
        if (helperNode) {
          removeCssClass("helper");
          if (hasHelperSlot || groupInfo.value.pull === "clone") {
            const helperContainer = helperNode.parentNode;
            if (helperContainer) {
              helperContainer.removeChild(helperNode);
            }
          }
        }
      }
      const attachPlaceholder = () => {
        if (!props.placeholder) {
          return;
        }
        if (hasPlaceholderSlot) {
          placeholderNode = placeholder.value;
        } else {
          placeholderNode = draggedItem.cloneNode(true);
          placeholderNode.style.visibility = "hidden";
        }
        if (placeholderNode && groupInfo.value.pull !== "clone") {
          sortableContainer.value.insertBefore(placeholderNode, draggedItem);
        }
        addCssClass("placeholder");
      };
      function detachPlaceholder() {
        if (placeholderNode) {
          removeCssClass("placeholder");
          const placeholderContainer = placeholderNode.parentNode;
          if (placeholderContainer) {
            placeholderContainer.removeChild(placeholderNode);
          }
        }
      }
      const finishDrag = () => {
        clearTimeout(dragTimeout);
        detachEvents();
        if (dragging.value) {
          dragging.value = false;
          currentDocument.body.style.userSelect = null;
          removeCssClass("body");
          removeCssClass("source");
          removeCssClass("source:container");
          removeCssClass("placeholder:container");
          detachPlaceholder();
          detachHelper();
          if (helperNode) {
            if (props.revert) {
              helperNode.style.position = null;
              helperNode.style.left = null;
              helperNode.style.top = null;
              helperNode.style.width = null;
              helperNode.style.height = null;
              helperNode.style.zIndex = null;
              helperNode.style.transform = null;
            }
            if (props.allowDuplicate && duplicateValue) {
              draggedItem.style.display = null;
              draggedItem.style.opacity = null;
            }
            helperNode.style.willChange = null;
            helperNode.style.pointerEvents = null;
            helperNode.style.zIndex = null;
          }
          if (hasHelperSlot) {
            draggedItem.style.display = null;
          }
          const { from, to, startIndex, newIndex, placeBefore } = lastEvent.data;
          let draggedValueModel = null;
          if (from && to && newIndex !== -1) {
            const toVm = to.__SORTABLE_INFO__;
            if (props.modelValue !== null) {
              let updatedNewIndex = placeBefore ? newIndex : newIndex + 1;
              draggedValueModel = props.duplicateCallback && duplicateValue ? props.duplicateCallback(props.modelValue[startIndex]) : props.modelValue[startIndex];
              if (from === to && startIndex !== newIndex && !duplicateValue) {
                updatePositionInList(startIndex, updatedNewIndex);
              } else if (from === to && startIndex === newIndex && !duplicateValue)
                ;
              else {
                if (!duplicateValue) {
                  removeItemFromList(startIndex);
                }
                toVm.addItemToList(draggedValueModel, updatedNewIndex);
              }
            }
            const dropEvent = new Drop(__spreadProps(__spreadValues({}, lastEvent.data), {
              toVm,
              draggedValueModel,
              fromDraggedValueModel: props.modelValue,
              newIndex,
              duplicateItem: duplicateValue
            }));
            toVm.emit("drop", dropEvent);
          }
          const endEvent = new End();
          emit("end", endEvent);
          eventScheduler.cancel();
          currentDocument = null;
          initialX = null;
          initialY = null;
          dimensions = null;
          draggedItem = null;
          dragDelayCompleted = null;
          dragItemInfo = null;
        }
      };
      const updatePositionInList = (oldIndex, newIndex) => {
        if (props.modelValue) {
          const list = [...props.modelValue];
          if (oldIndex >= newIndex) {
            list.splice(newIndex, 0, list.splice(oldIndex, 1)[0]);
          } else {
            list.splice(newIndex - 1, 0, list.splice(oldIndex, 1)[0]);
          }
          emit("update:modelValue", list);
        }
      };
      const addItemToList = (item, index2) => {
        if (props.modelValue) {
          const list = [...props.modelValue];
          list.splice(index2, 0, item);
          emit("update:modelValue", list);
        }
      };
      const removeItemFromList = (index2) => {
        if (props.modelValue) {
          const list = [...props.modelValue];
          list.splice(index2, 1);
          emit("update:modelValue", list);
        }
      };
      const onDraggableMouseMove = (event2) => {
        if (dragging.value) {
          eventScheduler.move(event2);
        } else {
          const { clientX, clientY } = event2;
          const xDistance = Math.abs(clientX - initialX);
          const yDistance = Math.abs(clientY - initialY);
          if (dragDelayCompleted && (xDistance >= props.dragTreshold || yDistance >= props.dragTreshold)) {
            dragging.value = true;
            vue.nextTick(() => {
              startDrag();
            });
          }
        }
      };
      const getInfoFromTarget = (target) => {
        const validItem = closest(target, props.draggable, sortableContainer.value);
        const sortableDomElements = getDomElementsFromSortableItems();
        const item = sortableDomElements.includes(validItem) ? validItem : false;
        const index2 = sortableDomElements.indexOf(item);
        return {
          container: sortableContainer.value,
          item,
          index: index2,
          newIndex: index2,
          group: groupInfo
        };
      };
      const canPull = () => {
        if (groupInfo.value.pull === false) {
          return false;
        }
        return true;
      };
      const onMouseMove = (event2) => {
        let { clientX, clientY } = event2;
        let offset2 = {
          left: 0,
          top: 0
        };
        if (props.allowDuplicate && event2.ctrlKey) {
          draggedItem.style.display = null;
          draggedItem.style.opacity = 0.2;
          duplicateValue = true;
        } else {
          draggedItem.style.opacity = null;
          duplicateValue = false;
        }
        const movedX = clientX + offset2.left - initialX;
        const movedY = clientY + offset2.top - initialY;
        helperNode.style.transform = `translate3d(${movedX}px, ${movedY}px, 0)`;
        let overItem = {
          container: null,
          item: null,
          index: -1
        };
        const target = currentDocument.elementFromPoint(clientX, clientY);
        if (target) {
          const to2 = closest(target, getSortableContainer);
          const sameContainer = to2 === sortableContainer.value;
          if (sameContainer && !props.sort)
            ;
          else if (to2) {
            const targetVM = to2.__SORTABLE_INFO__;
            const overItemInfo = targetVM.getInfoFromTarget(target);
            overItem = __spreadValues(__spreadValues({}, overItem), overItemInfo);
            dragItemInfo.to = overItem.container;
            dragItemInfo.toItem = overItem.item;
            if (overItem.container) {
              if (overItem.item) {
                const collisionInfoData = collisionInfo(event2, overItem.item, targetVM);
                dragItemInfo.placeBefore = collisionInfoData.before;
                const whereToPutPlaceholder = targetVM.getItemFromList(overItem.index);
                const nextSibling = whereToPutPlaceholder.nextElementSibling;
                const insertBeforeElement = dragItemInfo.placeBefore ? whereToPutPlaceholder : nextSibling;
                movePlaceholderMemoized(overItem.container, insertBeforeElement, dragItemInfo.placeBefore);
                dragItemInfo.newIndex = overItem.index;
              } else {
                if (targetVM.modelValue && targetVM.modelValue.length === 0) {
                  dragItemInfo.newIndex = 0;
                  movePlaceholderMemoized(overItem.container, null, dragItemInfo.placeBefore);
                } else if (sameContainer && props.modelValue.length === 1) {
                  movePlaceholderMemoized(overItem.container, null, dragItemInfo.placeBefore);
                }
              }
            }
          } else if (!props.preserveLastLocation) {
            dragItemInfo.to = null;
            dragItemInfo.newIndex = null;
            dragItemInfo.toItem = null;
            dragItemInfo.placeBefore = null;
            movePlaceholderMemoized(null, null, null);
          }
        }
        const { container: from, item, index: startIndex, to, newIndex, toItem, placeBefore } = dragItemInfo;
        const moveEvent = new MoveEvent({
          from,
          item,
          startIndex,
          to,
          newIndex,
          toItem,
          nativeEvent: event2,
          placeBefore
        });
        emit("move", moveEvent);
        if (moveEvent.isCanceled()) {
          finishDrag();
        }
        lastEvent = moveEvent;
      };
      const collisionInfo = (event2, overItem, targetVm) => {
        const { clientX, clientY } = event2;
        const itemRect = overItem.getBoundingClientRect();
        const orientation = detectOrientation(targetVm);
        const center = orientation === "horizontal" ? itemRect.width / 2 : itemRect.height / 2;
        const before = orientation === "horizontal" ? clientX < itemRect.left + center : clientY < itemRect.top + center;
        return {
          before
        };
      };
      const detectOrientation = (targetVm) => {
        return targetVm.axis || "vertical";
      };
      const getDomElementsFromSortableItems = () => {
        return childItems.filter((el) => el).map((el) => {
          return el.el;
        });
      };
      const getItemFromList = (index2) => {
        const sortableDomElements = getDomElementsFromSortableItems();
        return sortableDomElements[index2];
      };
      const getSortableContainer = (target) => {
        return target && target.__SORTABLE_INFO__ && target.__SORTABLE_INFO__.canPut(dragItemInfo);
      };
      vue.onMounted(() => {
        eventScheduler = EventScheduler(getEvents());
        sortableContainer.value.__SORTABLE_INFO__ = sortableInfo;
        collectChildren();
      });
      vue.onUpdated(() => {
        collectChildren();
      });
      function collectChildren() {
        sortableItems.value = fetchChildren(childItems);
      }
      function fetchChildren(items2) {
        let children = [];
        if (Array.isArray(items2)) {
          items2.forEach((child) => {
            if (child.type === vue.Fragment) {
              children = [...children, ...fetchChildren(child.children)];
            } else {
              children.push(child);
            }
          });
        }
        return children;
      }
      const movePlaceholderMemoized = memoizeOne(movePlaceholder);
      const sortableInfo = {
        group: props.group,
        axis: props.axis,
        getInfoFromTarget,
        canPut,
        getItemFromList,
        addItemToList,
        modelValue: props.modelValue,
        emit
      };
      return () => {
        const childElements = [];
        if (slots.start) {
          childElements.push(slots.start());
        }
        const draggableItems = slots.default();
        childItems = fetchChildren(draggableItems);
        childElements.push(draggableItems);
        if (dragging.value) {
          if (slots.helper) {
            hasHelperSlot = true;
            childElements.push(
              vue.h(
                "div",
                {
                  class: "zion-editor__sortable-helper",
                  ref: helper
                },
                slots.helper()
              )
            );
          }
          if (slots.placeholder) {
            hasPlaceholderSlot = true;
            childElements.push(
              vue.h(
                "div",
                {
                  class: "znpb-sortable__placeholder",
                  ref: placeholder
                },
                slots.placeholder()
              )
            );
          }
        }
        if (slots.end) {
          childElements.push(slots.end());
        }
        return vue.h(
          props.tag,
          {
            onMousedown: props.disabled ? null : onMouseDown,
            onDragstart: onDragStart,
            ref: sortableContainer,
            class: {
              [`vuebdnd__placeholder-empty-container`]: childItems.length === 0 || dragging.value && childItems.length === 1
            }
          },
          [childElements]
        );
      };
    }
  };
  const useBuilderOptionsStore = defineStore("builderOptions", () => {
    const isLoading2 = vue.ref(false);
    let fetched = false;
    const options2 = vue.ref({
      allowed_post_types: ["post", "page"],
      google_fonts: [],
      custom_fonts: [],
      typekit_token: "",
      typekit_fonts: [],
      local_colors: [],
      global_colors: [],
      local_gradients: [],
      global_gradients: [],
      user_roles_permissions: {},
      users_permissions: {},
      custom_code: ""
    });
    if (!fetched) {
      fetchOptions();
    }
    function fetchOptions(force = false) {
      if (fetched && !force) {
        return Promise.resolve(options2.value);
      }
      return getSavedOptions().then((response) => {
        const data2 = response.data;
        if (Array.isArray(data2.user_roles_permissions)) {
          data2.user_roles_permissions = {};
        }
        if (Array.isArray(data2.users_permissions)) {
          data2.users_permissions = {};
        }
        options2.value = __spreadValues(__spreadValues({}, options2.value), data2);
      }).finally(() => {
        fetched = true;
      });
    }
    function getOptionValue(optionId, defaultValue = null) {
      return get(options2.value, optionId, defaultValue);
    }
    function updateOptionValue(path, newValue, saveOptions2 = true) {
      update(options2.value, path, () => newValue);
      if (saveOptions2) {
        saveOptionsToDB();
      }
    }
    function deleteOptionValue(path, saveOptions2 = true) {
      const clonedValues = cloneDeep(options2.value);
      unset(clonedValues, path);
      options2.value = clonedValues;
      if (saveOptions2) {
        saveOptionsToDB();
      }
    }
    function saveOptionsToDB() {
      return __async(this, null, function* () {
        isLoading2.value = true;
        try {
          return yield saveOptions(options2.value);
        } finally {
          isLoading2.value = false;
        }
      });
    }
    const debouncedSaveOptions = debounce$1(saveOptionsToDB, 700);
    function updateGoogleFont(fontFamily, newValue) {
      const savedFont = options2.value.google_fonts.find((fontItem) => fontItem.font_family === fontFamily);
      if (savedFont) {
        const fontIndex = options2.value.google_fonts.indexOf(savedFont);
        options2.value.google_fonts.splice(fontIndex, 1, newValue);
      }
      saveOptionsToDB();
    }
    function removeGoogleFont(fontFamily) {
      const savedFont = options2.value.google_fonts.find((fontItem) => fontItem.font_family === fontFamily);
      if (savedFont) {
        const fontIndex = options2.value.google_fonts.indexOf(savedFont);
        options2.value.google_fonts.splice(fontIndex, 1);
      } else {
        console.warn("Font for deletion was not found");
      }
      saveOptionsToDB();
    }
    function addGoogleFont(fontFamily) {
      options2.value.google_fonts.push({
        font_family: fontFamily,
        font_variants: ["regular"],
        font_subset: ["latin"]
      });
      saveOptionsToDB();
    }
    function addLocalColor(color) {
      options2.value.local_colors.push(color);
      saveOptionsToDB();
    }
    function deleteLocalColor(color) {
      const colorIndex = options2.value.local_colors.indexOf(color);
      if (colorIndex !== -1) {
        options2.value.local_colors.splice(colorIndex, 1);
      }
      saveOptionsToDB();
    }
    function editLocalColor(color, newColor, saveToDB = true) {
      const colorIndex = options2.value.local_colors.indexOf(color);
      if (colorIndex !== -1) {
        options2.value.local_colors.splice(colorIndex, 1, newColor);
      }
      if (saveToDB) {
        saveOptionsToDB();
      }
    }
    function addGlobalColor(color) {
      options2.value.global_colors.push(color);
      saveOptionsToDB();
    }
    function deleteGlobalColor(color) {
      const colorIndex = options2.value.global_colors.indexOf(color);
      if (colorIndex !== -1) {
        options2.value.global_colors.splice(colorIndex, 1);
      }
      saveOptionsToDB();
    }
    function editGlobalColor(index2, newColor, saveToDB = true) {
      const colorToChange = __spreadValues({}, options2.value.global_colors[index2]);
      colorToChange["color"] = newColor;
      options2.value.global_colors.splice(index2, 1, colorToChange);
      if (saveToDB) {
        saveOptionsToDB();
      }
    }
    function addCustomFont(font) {
      options2.value.custom_fonts.push(font);
      saveOptionsToDB();
    }
    function updateCustomFont(fontFamily, newValue) {
      const savedFont = options2.value.custom_fonts.find((fontItem) => fontItem.font_family === fontFamily);
      if (savedFont) {
        const fontIndex = options2.value.custom_fonts.indexOf(savedFont);
        options2.value.custom_fonts.splice(fontIndex, 1, newValue);
      }
      saveOptionsToDB();
    }
    function deleteCustomFont(fontFamily) {
      const savedFont = options2.value.custom_fonts.find((fontItem) => fontItem.font_family === fontFamily);
      if (savedFont) {
        const fontIndex = options2.value.custom_fonts.indexOf(savedFont);
        options2.value.custom_fonts.splice(fontIndex, 1);
      } else {
        console.warn("Font for deletion was not found");
      }
      saveOptionsToDB();
    }
    function addLocalGradient(gradient) {
      options2.value.local_gradients.push(gradient);
      saveOptionsToDB();
    }
    function deleteLocalGradient(gradient) {
      const gradientIndex = options2.value.local_gradients.indexOf(gradient);
      if (gradientIndex !== -1) {
        options2.value.local_gradients.splice(gradientIndex, 1);
      }
      saveOptionsToDB();
    }
    function editLocalGradient(gradientId, newgradient) {
      const editedGradient = options2.value.local_gradients.find((gradient) => gradient.id === gradientId);
      if (editedGradient) {
        editedGradient.config = newgradient;
      }
    }
    function addGlobalGradient(gradient) {
      options2.value.global_gradients.push(gradient);
      saveOptionsToDB();
    }
    function deleteGlobalGradient(gradient) {
      const gradientIndex = options2.value.global_gradients.indexOf(gradient);
      if (gradientIndex !== -1) {
        options2.value.global_gradients.splice(gradientIndex, 1);
      }
      saveOptionsToDB();
    }
    function editGlobalGradient(gradientId, newgradient) {
      const editedGradient = options2.value.global_gradients.find((gradient) => gradient.id === gradientId);
      if (editedGradient) {
        editedGradient.config = newgradient;
      }
    }
    function addTypeKitToken(token) {
      options2.value.typekit_token = token;
    }
    function addFontProject(fontId) {
      const fontIndex = options2.value.typekit_fonts.indexOf(fontId);
      if (fontIndex === -1) {
        options2.value.typekit_fonts.push(fontId);
      }
      saveOptionsToDB();
    }
    function removeFontProject(fontId) {
      const fontIndex = options2.value.typekit_fonts.indexOf(fontId);
      if (fontIndex !== -1) {
        options2.value.typekit_fonts.splice(fontIndex, 1);
      }
      saveOptionsToDB();
    }
    function addUserPermissions(user) {
      options2.value.users_permissions[user.id] = {};
      saveOptionsToDB();
    }
    function editUserPermission(userID, newValues) {
      options2.value.users_permissions[userID] = newValues;
      saveOptionsToDB();
    }
    function deleteUserPermission(userID) {
      delete options2.value.users_permissions[userID];
      saveOptionsToDB();
    }
    function getUserPermissions(userID) {
      return options2.value.users_permissions[userID];
    }
    function getRolePermissions(roleID) {
      return options2.value.user_roles_permissions[roleID] || {
        allowed_access: false,
        permissions: {
          only_content: false,
          features: [],
          post_types: []
        }
      };
    }
    function editRolePermission(roleID, newValues) {
      options2.value.user_roles_permissions[roleID] = newValues;
      saveOptionsToDB();
    }
    return {
      isLoading: isLoading2,
      fetched,
      options: options2,
      fetchOptions,
      getOptionValue,
      updateOptionValue,
      deleteOptionValue,
      saveOptionsToDB,
      editRolePermission,
      getRolePermissions,
      getUserPermissions,
      editUserPermission,
      deleteUserPermission,
      addUserPermissions,
      removeFontProject,
      addFontProject,
      addTypeKitToken,
      editGlobalGradient,
      deleteGlobalGradient,
      addGlobalGradient,
      editLocalGradient,
      deleteLocalGradient,
      addLocalGradient,
      deleteCustomFont,
      updateCustomFont,
      addCustomFont,
      editGlobalColor,
      removeGoogleFont,
      updateGoogleFont,
      addGoogleFont,
      addLocalColor,
      deleteLocalColor,
      editLocalColor,
      addGlobalColor,
      deleteGlobalColor,
      debouncedSaveOptions
    };
  });
  defineStore("googleFonts", {
    state: () => {
      return {
        isLoading: false,
        fetched: false,
        fonts: []
      };
    },
    getters: {
      getFontData: (state) => {
        return (family) => state.fonts.find((font) => font["family"] == family);
      }
    },
    actions: {
      fetchGoogleFonts(force = false) {
        if (this.fetched && !force) {
          return Promise.resolve(this.fonts);
        }
        return getGoogleFonts().then((response) => {
          this.fonts = response.data;
        });
      }
    }
  });
  class Notification {
    constructor(data2) {
      __publicField(this, "title", "");
      __publicField(this, "message", "");
      __publicField(this, "type", "info");
      __publicField(this, "delayClose", 5e3);
      Object.assign(this, data2);
    }
    remove() {
      const notificationsStore = useNotificationsStore();
      notificationsStore.remove(this);
    }
  }
  const useNotificationsStore = defineStore("notifications", {
    state: () => {
      return {
        notifications: []
      };
    },
    actions: {
      add(data2) {
        this.notifications.push(new Notification(data2));
      },
      remove(notification) {
        const index2 = this.notifications.indexOf(notification);
        this.notifications.splice(index2, 1);
      }
    }
  });
  defineStore("usersStore", () => {
    const loadedUsers = vue.ref([]);
    function fetchUsersData(userIDs) {
      return getUsersById(userIDs).then((response) => {
        if (Array.isArray(response.data)) {
          response.data.forEach((user) => loadedUsers.value.push(user));
        }
      });
    }
    function getUserInfo(userID) {
      return loadedUsers.value.find((user) => user.id === userID);
    }
    function addUser(user) {
      loadedUsers.value.push(user);
    }
    return {
      loadedUsers,
      fetchUsersData,
      addUser,
      getUserInfo
    };
  });
  const useDataSetsStore = defineStore("dataSets", () => {
    let loaded = false;
    const dataSets = vue.ref({
      fonts_list: {
        google_fonts: [],
        custom_fonts: [],
        typekit_fonts: []
      },
      user_roles: [],
      post_types: [],
      taxonomies: [],
      icons: [],
      image_sizes: []
    });
    if (!loaded) {
      getFontsDataSet().then((response) => {
        dataSets.value = response.data;
        loaded = true;
      });
    }
    const fontsListForOption = vue.computed(() => {
      let option = [
        {
          id: "Arial",
          name: "Arial"
        },
        {
          id: "Times New Roman",
          name: "Times New Roman"
        },
        {
          id: "Verdana",
          name: "Verdana"
        },
        {
          id: "Trebuchet",
          name: "Trebuchet"
        },
        {
          id: "Georgia",
          name: "Georgia"
        },
        {
          id: "Segoe UI",
          name: "Segoe UI"
        }
      ];
      const fontsProviders = dataSets.value.fonts_list;
      Object.keys(fontsProviders).forEach((fontProviderId) => {
        const fontsList = fontsProviders[fontProviderId];
        option = [...fontsList, ...option];
      });
      return option;
    });
    const addIconsSet = (iconSet) => {
      dataSets.value.icons.push(iconSet);
    };
    const deleteIconSet = (icons) => {
      const iconsPackage = dataSets.value.icons.find((iconSet) => {
        return iconSet.id === icons;
      });
      if (iconsPackage !== void 0) {
        const iconsPackageIndex = dataSets.value.icons.indexOf(iconsPackage);
        dataSets.value.icons.splice(iconsPackageIndex, 1);
      }
    };
    return {
      dataSets,
      fontsListForOption,
      addIconsSet,
      deleteIconSet
    };
  });
  const useAssetsStore = defineStore("assets", {
    state: () => {
      return {
        isLoading: false,
        currentIndex: 0,
        filesCount: 0
      };
    },
    actions: {
      regenerateCache() {
        return __async(this, null, function* () {
          this.isLoading = true;
          try {
            const { data: cacheFiles } = yield getCacheList();
            this.filesCount = cacheFiles.length;
            if (this.filesCount > 0) {
              for (const fileData of cacheFiles) {
                try {
                  this.currentIndex++;
                  yield regenerateCache(fileData);
                } catch (error) {
                  console.error(error);
                }
              }
            }
          } catch (error) {
            console.error(error);
          }
          this.isLoading = false;
          this.filesCount = 0;
          this.currentIndex = 0;
        });
      },
      finish() {
        return finishRegeneration();
      }
    }
  });
  var GradientGenerator_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$R = { class: "znpb-gradient-wrapper" };
  const _hoisted_2$B = { class: "znpb-gradient-elements-wrapper" };
  const __default__$K = {
    name: "GradientGenerator"
  };
  const _sfc_main$19 = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$K), {
    props: {
      modelValue: null,
      saveToLibrary: { type: Boolean, default: true }
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const showPresetInput = vue.ref(false);
      const activeGradientIndex = vue.ref(0);
      const { addLocalGradient, addGlobalGradient } = useBuilderOptionsStore();
      const computedValue = vue.computed({
        get() {
          var _a3;
          const clonedValue = JSON.parse(JSON.stringify((_a3 = props.modelValue) != null ? _a3 : getDefaultGradient()));
          const { applyFilters: applyFilters2 } = window.zb.hooks;
          return applyFilters2("zionbuilder/options/model", clonedValue);
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      const activeGradient = vue.computed({
        get() {
          return computedValue.value[activeGradientIndex.value];
        },
        set(newValue) {
          const valueToSend = [...computedValue.value];
          valueToSend[activeGradientIndex.value] = newValue;
          computedValue.value = valueToSend;
        }
      });
      function addGlobalPattern(name, type) {
        showPresetInput.value = false;
        const defaultGradient = {
          id: generateUID(),
          name,
          config: computedValue.value
        };
        type === "local" ? addLocalGradient(defaultGradient) : addGlobalGradient(defaultGradient);
      }
      function deleteGradient(gradientConfig) {
        const deletedGradientIndex = computedValue.value.indexOf(gradientConfig);
        if (activeGradient.value === gradientConfig) {
          if (deletedGradientIndex > 0) {
            activeGradientIndex.value = deletedGradientIndex - 1;
          } else {
            activeGradientIndex.value = deletedGradientIndex + 1;
          }
        } else {
          if (deletedGradientIndex < activeGradientIndex.value) {
            activeGradientIndex.value = activeGradientIndex.value - 1;
          }
        }
        const updatedValues = computedValue.value.slice(0);
        updatedValues.splice(deletedGradientIndex, 1);
        computedValue.value = updatedValues;
      }
      function addGradientConfig() {
        const defaultConfig = getDefaultGradient();
        computedValue.value = [...computedValue.value, defaultConfig[0]];
        vue.nextTick(() => {
          const newGradientIndex = computedValue.value.length - 1;
          changeActive(newGradientIndex);
        });
      }
      function changeActive(index2) {
        activeGradientIndex.value = index2;
      }
      function changePosition(position) {
        activeGradient.value = __spreadProps(__spreadValues({}, activeGradient.value), {
          position
        });
      }
      function deleteGradientValue() {
        emit("update:modelValue", null);
      }
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$R, [
          !__props.saveToLibrary ? (vue.openBlock(), vue.createBlock(_sfc_main$1q, {
            key: 0,
            config: vue.unref(computedValue),
            activegrad: vue.unref(activeGradient),
            onChangeActiveGradient: _cache[0] || (_cache[0] = ($event) => changeActive($event)),
            onPositionChanged: _cache[1] || (_cache[1] = ($event) => changePosition($event))
          }, null, 8, ["config", "activegrad"])) : (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1I), { key: 1 }, {
            actions: vue.withCtx(() => [
              !showPresetInput.value ? (vue.openBlock(), vue.createElementBlock("span", {
                key: 0,
                class: "znpb-gradient__show-preset",
                onClick: _cache[4] || (_cache[4] = ($event) => showPresetInput.value = true)
              }, vue.toDisplayString(_ctx.$translate("save_to_library")), 1)) : (vue.openBlock(), vue.createBlock(_sfc_main$1b, {
                key: 1,
                onSavePreset: addGlobalPattern,
                onCancel: _cache[5] || (_cache[5] = ($event) => showPresetInput.value = false)
              })),
              !showPresetInput.value ? (vue.openBlock(), vue.createBlock(_sfc_main$1K, {
                key: 2,
                icon: "delete",
                "bg-size": 30,
                class: "znpb-gradient-wrapper__delete-gradient",
                onClick: vue.withModifiers(deleteGradientValue, ["stop"])
              }, null, 8, ["onClick"])) : vue.createCommentVNode("", true)
            ]),
            default: vue.withCtx(() => [
              vue.createVNode(_sfc_main$1q, {
                config: vue.unref(computedValue),
                activegrad: vue.unref(activeGradient),
                onChangeActiveGradient: _cache[2] || (_cache[2] = ($event) => changeActive($event)),
                onPositionChanged: _cache[3] || (_cache[3] = ($event) => changePosition($event))
              }, null, 8, ["config", "activegrad"])
            ]),
            _: 1
          })),
          vue.createElementVNode("div", _hoisted_2$B, [
            vue.createVNode(vue.unref(_sfc_main$1a), {
              modelValue: vue.unref(computedValue),
              "onUpdate:modelValue": _cache[6] || (_cache[6] = ($event) => vue.isRef(computedValue) ? computedValue.value = $event : null),
              class: "znpb-admin-colors__container",
              handle: null,
              "drag-delay": 0,
              "drag-treshold": 10,
              disabled: false,
              revert: true,
              axis: "horizontal"
            }, {
              default: vue.withCtx(() => [
                (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(computedValue), (gradient, i) => {
                  return vue.openBlock(), vue.createBlock(_sfc_main$1d, {
                    key: i,
                    class: "znpb-gradient-elements__delete-button",
                    config: gradient,
                    "show-remove": vue.unref(computedValue).length > 1,
                    "is-active": activeGradientIndex.value === i,
                    onChangeActiveGradient: ($event) => changeActive(i),
                    onDeleteGradient: ($event) => deleteGradient(gradient)
                  }, null, 8, ["config", "show-remove", "is-active", "onChangeActiveGradient", "onDeleteGradient"]);
                }), 128))
              ]),
              _: 1
            }, 8, ["modelValue"]),
            vue.createVNode(_sfc_main$1K, {
              icon: "plus",
              class: "znpb-colorpicker-add-grad",
              onClick: addGradientConfig
            })
          ]),
          vue.createVNode(_sfc_main$1f, {
            modelValue: vue.unref(activeGradient),
            "onUpdate:modelValue": _cache[7] || (_cache[7] = ($event) => vue.isRef(activeGradient) ? activeGradient.value = $event : null)
          }, null, 8, ["modelValue"])
        ]);
      };
    }
  }));
  var LibraryElement_vue_vue_type_style_index_0_lang = "";
  const __default__$J = {
    name: "LibraryElement"
  };
  const _sfc_main$18 = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$J), {
    props: {
      animation: { type: Boolean, default: true },
      icon: null,
      hasInput: { type: Boolean }
    },
    emits: ["close-library"],
    setup(__props) {
      const onstart = vue.ref(true);
      const expand = vue.ref(false);
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          class: vue.normalizeClass(["znpb-form-library-inner-pattern-wrapper", {
            "znpb-form-library-inner-pattern-wrapper--start": onstart.value,
            "znpb-form-library-inner-pattern-wrapper--stretch": !expand.value,
            "znpb-form-library-inner-pattern-wrapper--expand": expand.value,
            "znpb-form-library-inner-pattern-wrapper--hasInput": __props.hasInput
          }])
        }, [
          !__props.hasInput ? (vue.openBlock(), vue.createElementBlock(vue.Fragment, { key: 0 }, [
            __props.animation ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
              key: 0,
              icon: "more",
              class: "znpb-form-library-inner-action-icon",
              onClick: _cache[0] || (_cache[0] = vue.withModifiers(($event) => (expand.value = !expand.value, onstart.value = false), ["stop"]))
            })) : vue.createCommentVNode("", true),
            __props.icon ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
              key: 1,
              icon: __props.icon,
              class: "znpb-form-library-inner-action-icon",
              onClick: _cache[1] || (_cache[1] = vue.withModifiers(($event) => _ctx.$emit("close-library"), ["stop"]))
            }, null, 8, ["icon"])) : vue.createCommentVNode("", true)
          ], 64)) : vue.createCommentVNode("", true),
          vue.renderSlot(_ctx.$slots, "default")
        ], 2);
      };
    }
  }));
  var Label_vue_vue_type_style_index_0_lang = "";
  const __default__$I = {
    name: "Label"
  };
  const _sfc_main$17 = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$I), {
    props: {
      text: null,
      type: null
    },
    setup(__props) {
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("span", {
          class: vue.normalizeClass(["znpb-label", { [`znpb-label--${__props.type}`]: __props.type }])
        }, vue.toDisplayString(__props.text), 3);
      };
    }
  }));
  var GradientLibrary_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$Q = {
    key: 0,
    class: "znpb-form-library-grid__panel-content-message"
  };
  const _hoisted_2$A = {
    key: 1,
    class: "znpb-form-library-grid__panel-content znpb-form-library-grid__panel-content--no-pd znpb-fancy-scrollbar"
  };
  const _hoisted_3$p = {
    key: 0,
    class: "znpb-colorpicker-global-wrapper--pro"
  };
  const _hoisted_4$g = {
    key: 0,
    class: "znpb-form-library-grid__panel-content-message"
  };
  const _hoisted_5$9 = {
    key: 1,
    class: "znpb-form-library-grid__panel-content znpb-form-library-grid__panel-content--no-pd znpb-fancy-scrollbar"
  };
  const __default__$H = {
    name: "GradientLibrary"
  };
  const _sfc_main$16 = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$H), {
    props: {
      model: null
    },
    emits: ["activate-gradient", "close-library"],
    setup(__props, { emit }) {
      const updateValueByPath = vue.inject("updateValueByPath");
      function getPro() {
        if (window.ZnPbComponentsData !== void 0) {
          return window.ZnPbComponentsData.is_pro_active;
        }
        return false;
      }
      const isPro = getPro();
      const schema = vue.inject("schema");
      const { getOptionValue } = useBuilderOptionsStore();
      const getGlobalGradients = vue.computed(() => {
        return getOptionValue("global_gradients");
      });
      const getLocalGradients = vue.computed(() => {
        return getOptionValue("local_gradients");
      });
      function onGlobalGradientSelected(gradient) {
        const { id } = schema;
        updateValueByPath(`__dynamic_content__.${id}`, {
          type: "global-gradient",
          options: {
            gradient_id: gradient.id
          }
        });
        vue.nextTick(() => {
          emit("activate-gradient", null);
        });
      }
      return (_ctx, _cache) => {
        const _component_Tab = vue.resolveComponent("Tab");
        const _component_Tabs = vue.resolveComponent("Tabs");
        return vue.openBlock(), vue.createBlock(_sfc_main$18, {
          animation: false,
          icon: "close",
          onCloseLibrary: _cache[0] || (_cache[0] = ($event) => _ctx.$emit("close-library"))
        }, {
          default: vue.withCtx(() => [
            vue.createVNode(_component_Tabs, { "tab-style": "minimal" }, {
              default: vue.withCtx(() => [
                vue.createVNode(_component_Tab, { name: "Local" }, {
                  default: vue.withCtx(() => [
                    vue.unref(getLocalGradients).length === 0 ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_1$Q, vue.toDisplayString(_ctx.$translate("no_local_gradients")), 1)) : (vue.openBlock(), vue.createElementBlock("div", _hoisted_2$A, [
                      (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(getLocalGradients), (gradient, i) => {
                        return vue.openBlock(), vue.createBlock(_sfc_main$1s, {
                          key: i,
                          config: gradient.config,
                          round: true,
                          onClick: ($event) => _ctx.$emit("activate-gradient", gradient.config)
                        }, null, 8, ["config", "onClick"]);
                      }), 128))
                    ]))
                  ]),
                  _: 1
                }),
                vue.createVNode(_component_Tab, { name: "Global" }, {
                  default: vue.withCtx(() => [
                    !vue.unref(isPro) ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_3$p, [
                      vue.createTextVNode(vue.toDisplayString(_ctx.$translate("global_colors_availability")) + " ", 1),
                      vue.createVNode(vue.unref(_sfc_main$17), {
                        text: _ctx.$translate("pro"),
                        type: "pro"
                      }, null, 8, ["text"])
                    ])) : (vue.openBlock(), vue.createElementBlock(vue.Fragment, { key: 1 }, [
                      vue.unref(getGlobalGradients).length === 0 ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_4$g, vue.toDisplayString(_ctx.$translate("no_global_gradients")), 1)) : (vue.openBlock(), vue.createElementBlock("div", _hoisted_5$9, [
                        (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(getGlobalGradients), (gradient, i) => {
                          return vue.openBlock(), vue.createBlock(_sfc_main$1s, {
                            key: i,
                            config: gradient.config,
                            round: true,
                            onClick: ($event) => onGlobalGradientSelected(gradient)
                          }, null, 8, ["config", "onClick"]);
                        }), 128))
                      ]))
                    ], 64))
                  ]),
                  _: 1
                })
              ]),
              _: 1
            })
          ]),
          _: 1
        });
      };
    }
  }));
  var OptionBreadcrumbs_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$P = { class: "znpb-options-breadcrumbs" };
  const _hoisted_2$z = ["innerHTML"];
  const __default__$G = {
    name: "OptionBreadcrumbs"
  };
  const _sfc_main$15 = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$G), {
    props: {
      breadcrumbs: null,
      showBackButton: { type: Boolean }
    },
    setup(__props) {
      const props = __props;
      const previousItem = vue.computed(() => {
        var _a3;
        return (_a3 = props.breadcrumbs) == null ? void 0 : _a3[props.breadcrumbs.length - 2];
      });
      const computedBreadcrumbs = vue.computed(() => {
        var _a3;
        return (_a3 = props.breadcrumbs) == null ? void 0 : _a3.slice(Math.max(props.breadcrumbs.length - 2, 1));
      });
      function onItemClicked(breadcrumbItem) {
        if (breadcrumbItem.callback !== void 0) {
          breadcrumbItem.callback();
        }
      }
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$P, [
          __props.showBackButton ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
            key: 0,
            class: "znpb-back-icon-breadcrumbs",
            icon: "select",
            onClick: _cache[0] || (_cache[0] = ($event) => onItemClicked(vue.unref(previousItem)))
          })) : vue.createCommentVNode("", true),
          (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(computedBreadcrumbs), (breadcrumb, i) => {
            return vue.openBlock(), vue.createElementBlock("div", {
              key: i,
              class: vue.normalizeClass(["znpb-options-breadcrumbs-path", { ["znpb-options-breadcrumbs-path--current"]: i === vue.unref(computedBreadcrumbs).length - 1 }]),
              onClick: _cache[1] || (_cache[1] = ($event) => onItemClicked(vue.unref(previousItem)))
            }, [
              vue.createElementVNode("span", {
                innerHTML: breadcrumb.title
              }, null, 8, _hoisted_2$z),
              i + 1 < vue.unref(computedBreadcrumbs).length ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
                key: 0,
                icon: "select",
                class: "znpb-options-breadcrumbs-path-icon"
              })) : vue.createCommentVNode("", true)
            ], 2);
          }), 128))
        ]);
      };
    }
  }));
  var HorizontalAccordion_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$O = {
    key: 0,
    class: "znpb-horizontal-accordion__title"
  };
  const _hoisted_2$y = { class: "znpb-horizontal-accordion__header-actions" };
  const _hoisted_3$o = {
    key: 0,
    class: "znpb-horizontal-accordion__content"
  };
  const __default__$F = {
    name: "HorizontalAccordion"
  };
  const _sfc_main$14 = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$F), {
    props: {
      hasBreadcrumbs: { type: Boolean, default: true },
      collapsed: { type: Boolean, default: false },
      title: null,
      icon: null,
      level: null,
      showTriggerArrow: { type: Boolean, default: true },
      showBackButton: { type: Boolean },
      showHomeButton: { type: Boolean },
      homeButtonText: null,
      combineBreadcrumbs: { type: Boolean }
    },
    emits: ["collapse", "expand"],
    setup(__props, { expose, emit }) {
      const props = __props;
      const parentAccordion = vue.inject("parentAccordion", null);
      vue.provide(
        "parentAccordion",
        parentAccordion || {
          addBreadcrumb,
          removeBreadcrumb
        }
      );
      expose({
        closeAccordion
      });
      const root2 = vue.ref(null);
      const localCollapsed = vue.ref(props.collapsed);
      const breadcrumbs = vue.ref([
        {
          title: props.homeButtonText,
          callback: closeAccordion
        },
        {
          title: props.title
        }
      ]);
      const breadCrumbConfig = vue.ref(null);
      const firstChildOpen = vue.ref(false);
      const slots = vue.useSlots();
      const hasHeaderSlot = vue.computed(() => !!slots.header);
      const hasTitleSlot = vue.computed(() => !!slots.title);
      vue.watch(
        () => props.collapsed,
        (newValue) => {
          localCollapsed.value = newValue;
        }
      );
      const wrapperStyles = vue.computed(() => {
        const cssStyles = {};
        if (!props.combineBreadcrumbs && parentAccordion === null && localCollapsed.value && firstChildOpen.value) {
          cssStyles["overflow"] = "hidden";
        }
        return cssStyles;
      });
      function addBreadcrumb(breadcrumb) {
        if (typeof breadcrumb.previousCallback === "function") {
          const lastItem = breadcrumbs.value[breadcrumbs.value.length - 1];
          lastItem.callback = breadcrumb.previousCallback;
        }
        breadcrumbs.value.push(breadcrumb);
        firstChildOpen.value = true;
      }
      function removeBreadcrumb(breadcrumb) {
        const breadCrumbIndex = breadcrumbs.value.indexOf(breadcrumb);
        if (breadCrumbIndex !== -1) {
          breadcrumbs.value.splice(breadCrumbIndex, 1);
          firstChildOpen.value = false;
        }
      }
      function closeAccordion() {
        localCollapsed.value = false;
        if (parentAccordion && breadCrumbConfig.value) {
          removeBreadcrumb(breadCrumbConfig.value);
        }
        emit("collapse", true);
      }
      function openAccordion() {
        var _a3, _b;
        localCollapsed.value = true;
        (_b = (_a3 = root2.value) == null ? void 0 : _a3.closest(".znpb-horizontal-accordion-wrapper")) == null ? void 0 : _b.scrollTo(0, 0);
        if (props.combineBreadcrumbs && parentAccordion) {
          injectBreadcrumbs();
        }
        emit("expand", false);
      }
      function injectBreadcrumbs() {
        breadCrumbConfig.value = {
          title: props.title || "",
          previousCallback: closeAccordion
        };
        addBreadcrumb(breadCrumbConfig.value);
      }
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          ref_key: "root",
          ref: root2,
          class: "znpb-horizontal-accordion"
        }, [
          vue.createVNode(vue.Transition, { name: "slide-title" }, {
            default: vue.withCtx(() => [
              vue.withDirectives(vue.createElementVNode("div", {
                class: vue.normalizeClass(["znpb-horizontal-accordion__header", { "znpb-horizontal-accordion__header--has-slots": vue.unref(hasHeaderSlot) }]),
                onClick: openAccordion
              }, [
                !vue.unref(hasHeaderSlot) ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_1$O, [
                  !vue.unref(hasTitleSlot) ? (vue.openBlock(), vue.createElementBlock(vue.Fragment, { key: 0 }, [
                    __props.icon ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
                      key: 0,
                      icon: __props.icon
                    }, null, 8, ["icon"])) : vue.createCommentVNode("", true),
                    vue.createElementVNode("span", null, vue.toDisplayString(__props.title), 1)
                  ], 64)) : vue.createCommentVNode("", true),
                  vue.renderSlot(_ctx.$slots, "title")
                ])) : vue.createCommentVNode("", true),
                vue.renderSlot(_ctx.$slots, "header"),
                vue.createElementVNode("div", _hoisted_2$y, [
                  vue.renderSlot(_ctx.$slots, "actions"),
                  __props.showTriggerArrow ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
                    key: 0,
                    icon: "right-arrow"
                  })) : vue.createCommentVNode("", true)
                ])
              ], 2), [
                [vue.vShow, !localCollapsed.value]
              ])
            ]),
            _: 3
          }),
          vue.createVNode(vue.Transition, { name: "slide-body" }, {
            default: vue.withCtx(() => [
              localCollapsed.value ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_3$o, [
                __props.hasBreadcrumbs && !(__props.combineBreadcrumbs && vue.unref(parentAccordion)) ? (vue.openBlock(), vue.createBlock(_sfc_main$15, {
                  key: 0,
                  "show-back-button": __props.showBackButton,
                  breadcrumbs: breadcrumbs.value
                }, null, 8, ["show-back-button", "breadcrumbs"])) : vue.createCommentVNode("", true),
                vue.createElementVNode("div", {
                  class: "znpb-horizontal-accordion-wrapper znpb-fancy-scrollbar",
                  style: vue.normalizeStyle(vue.unref(wrapperStyles))
                }, [
                  vue.renderSlot(_ctx.$slots, "default")
                ], 4)
              ])) : vue.createCommentVNode("", true)
            ]),
            _: 3
          })
        ], 512);
      };
    }
  }));
  var FileSaver_min = { exports: {} };
  (function(module2, exports2) {
    (function(a, b) {
      b();
    })(commonjsGlobal, function() {
      function b(a2, b2) {
        return "undefined" == typeof b2 ? b2 = { autoBom: false } : "object" != typeof b2 && (console.warn("Deprecated: Expected third argument to be a object"), b2 = { autoBom: !b2 }), b2.autoBom && /^\s*(?:text\/\S*|application\/xml|\S*\/\S*\+xml)\s*;.*charset\s*=\s*utf-8/i.test(a2.type) ? new Blob(["\uFEFF", a2], { type: a2.type }) : a2;
      }
      function c(a2, b2, c2) {
        var d2 = new XMLHttpRequest();
        d2.open("GET", a2), d2.responseType = "blob", d2.onload = function() {
          g(d2.response, b2, c2);
        }, d2.onerror = function() {
          console.error("could not download file");
        }, d2.send();
      }
      function d(a2) {
        var b2 = new XMLHttpRequest();
        b2.open("HEAD", a2, false);
        try {
          b2.send();
        } catch (a3) {
        }
        return 200 <= b2.status && 299 >= b2.status;
      }
      function e(a2) {
        try {
          a2.dispatchEvent(new MouseEvent("click"));
        } catch (c2) {
          var b2 = document.createEvent("MouseEvents");
          b2.initMouseEvent("click", true, true, window, 0, 0, 0, 80, 20, false, false, false, false, 0, null), a2.dispatchEvent(b2);
        }
      }
      var f = "object" == typeof window && window.window === window ? window : "object" == typeof self && self.self === self ? self : "object" == typeof commonjsGlobal && commonjsGlobal.global === commonjsGlobal ? commonjsGlobal : void 0, a = f.navigator && /Macintosh/.test(navigator.userAgent) && /AppleWebKit/.test(navigator.userAgent) && !/Safari/.test(navigator.userAgent), g = f.saveAs || ("object" != typeof window || window !== f ? function() {
      } : "download" in HTMLAnchorElement.prototype && !a ? function(b2, g2, h) {
        var i = f.URL || f.webkitURL, j = document.createElement("a");
        g2 = g2 || b2.name || "download", j.download = g2, j.rel = "noopener", "string" == typeof b2 ? (j.href = b2, j.origin === location.origin ? e(j) : d(j.href) ? c(b2, g2, h) : e(j, j.target = "_blank")) : (j.href = i.createObjectURL(b2), setTimeout(function() {
          i.revokeObjectURL(j.href);
        }, 4e4), setTimeout(function() {
          e(j);
        }, 0));
      } : "msSaveOrOpenBlob" in navigator ? function(f2, g2, h) {
        if (g2 = g2 || f2.name || "download", "string" != typeof f2)
          navigator.msSaveOrOpenBlob(b(f2, h), g2);
        else if (d(f2))
          c(f2, g2, h);
        else {
          var i = document.createElement("a");
          i.href = f2, i.target = "_blank", setTimeout(function() {
            e(i);
          });
        }
      } : function(b2, d2, e2, g2) {
        if (g2 = g2 || open("", "_blank"), g2 && (g2.document.title = g2.document.body.innerText = "downloading..."), "string" == typeof b2)
          return c(b2, d2, e2);
        var h = "application/octet-stream" === b2.type, i = /constructor/i.test(f.HTMLElement) || f.safari, j = /CriOS\/[\d]+/.test(navigator.userAgent);
        if ((j || h && i || a) && "undefined" != typeof FileReader) {
          var k = new FileReader();
          k.onloadend = function() {
            var a2 = k.result;
            a2 = j ? a2 : a2.replace(/^data:[^;]*;/, "data:attachment/file;"), g2 ? g2.location.href = a2 : location = a2, g2 = null;
          }, k.readAsDataURL(b2);
        } else {
          var l = f.URL || f.webkitURL, m = l.createObjectURL(b2);
          g2 ? g2.location = m : location.href = m, g2 = null, setTimeout(function() {
            l.revokeObjectURL(m);
          }, 4e4);
        }
      });
      f.saveAs = g.saveAs = g, module2.exports = g;
    });
  })(FileSaver_min);
  vue.ref(null);
  vue.ref({});
  const registeredLocations = {};
  const useInjections = () => {
    const registerComponent = (location2, component) => {
      if (!location2 && !component) {
        console.warn("You need to specify a location and a component in order to register an injection component.", {
          location: location2,
          component
        });
        return false;
      }
      if (!Array.isArray(registeredLocations[location2])) {
        registeredLocations[location2] = [];
      }
      registeredLocations[location2].push(component);
    };
    const getComponentsForLocation = (location2) => {
      if (!location2) {
        console.warn("You need to specify a location and a component in order to get injection components.", {
          location: location2
        });
        return false;
      }
      if (!Array.isArray(registeredLocations[location2])) {
        return [];
      }
      return registeredLocations[location2];
    };
    return {
      registerComponent,
      getComponentsForLocation,
      registeredLocations
    };
  };
  var ListScroll_vue_vue_type_style_index_0_lang = "";
  const __default__$E = {
    name: "ListScroll"
  };
  const _sfc_main$13 = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$E), {
    props: {
      loading: { type: Boolean, default: true }
    },
    emits: ["scroll-end"],
    setup(__props, { emit }) {
      const listScrollRef = vue.ref(null);
      function onScroll(event2) {
        if (listScrollRef.value.scrollHeight - Math.round(listScrollRef.value.scrollTop) === listScrollRef.value.clientHeight) {
          emit("scroll-end");
        }
      }
      return (_ctx, _cache) => {
        const _component_Loader = vue.resolveComponent("Loader");
        return vue.openBlock(), vue.createElementBlock("div", {
          class: vue.normalizeClass(["znpb-scroll-list-wrapper", { "znpb-scroll-list-wrapper--loading": __props.loading }])
        }, [
          vue.createElementVNode("div", {
            ref_key: "listScrollRef",
            ref: listScrollRef,
            class: "znpb-fancy-scrollbar znpb-scroll-list-container",
            onWheelPassive: onScroll
          }, [
            vue.renderSlot(_ctx.$slots, "default")
          ], 544),
          vue.createVNode(vue.Transition, { name: "fadeFromBottom" }, {
            default: vue.withCtx(() => [
              __props.loading ? (vue.openBlock(), vue.createBlock(_component_Loader, { key: 0 })) : vue.createCommentVNode("", true)
            ]),
            _: 1
          })
        ], 2);
      };
    }
  }));
  var IconsLibraryModalContent_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$N = { class: "znpb-icon-pack-modal__search" };
  const _hoisted_2$x = { class: "znpb-icon-pack-modal-scroll znpb-fancy-scrollbar" };
  const __default__$D = {
    name: "IconsLibraryModalContent"
  };
  const _sfc_main$12 = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$D), {
    props: {
      modelValue: null,
      specialFilterPack: null
    },
    emits: ["update:modelValue", "selected"],
    setup(__props, { emit }) {
      const props = __props;
      const { dataSets } = storeToRefs(useDataSetsStore());
      const keyword = vue.ref("");
      const activeIcon = vue.ref(null);
      const activeCategory = vue.ref("all");
      const getPacks = vue.computed(() => {
        var _a3;
        return (_a3 = dataSets.value.icons) != null ? _a3 : [];
      });
      const searchModel = vue.computed({
        get() {
          return keyword.value;
        },
        set(newVal) {
          keyword.value = newVal;
        }
      });
      const filteredList = vue.computed(() => {
        if (keyword.value.length > 0) {
          const filtered = [];
          for (const pack of packList.value) {
            const copyPack = __spreadValues({}, pack);
            const b = copyPack.icons.filter((icon) => icon.name.includes(keyword.value.toLowerCase()));
            copyPack.icons = [...b];
            filtered.push(copyPack);
          }
          return filtered;
        } else
          return packList.value;
      });
      const getPlaceholder = vue.computed(() => {
        const { translate: translate2 } = window.zb.i18n;
        return `${translate2("search_for_icons")} ${getIconNumber.value} ${translate2("icons")}`;
      });
      const getIconNumber = vue.computed(() => {
        let iconNumber = 0;
        for (const pack of packList.value) {
          let packNumber = pack.icons.length;
          iconNumber = iconNumber + packNumber;
        }
        return iconNumber;
      });
      const packList = vue.computed(() => {
        if (props.specialFilterPack !== void 0 && props.specialFilterPack.length) {
          return props.specialFilterPack;
        }
        if (activeCategory.value === "all") {
          return getPacks.value;
        } else {
          let newArray = [];
          for (const pack of getPacks.value) {
            if (pack.id === activeCategory.value) {
              newArray.push(pack);
            }
          }
          return newArray;
        }
      });
      const packsOptions = vue.computed(() => {
        const options2 = [
          {
            name: "All",
            id: "all"
          }
        ];
        if (props.specialFilterPack === void 0 || !props.specialFilterPack.length) {
          getPacks.value.forEach((pack) => {
            let a = {
              name: pack.name,
              id: pack.id
            };
            options2.push(a);
          });
        }
        return options2;
      });
      function selectIcon(event2, name) {
        activeIcon.value = event2;
        const icon = {
          family: name,
          name: activeIcon.value.name,
          unicode: activeIcon.value.unicode
        };
        emit("update:modelValue", icon);
      }
      function insertIcon(event2, name) {
        activeIcon.value = event2;
        const icon = {
          family: name,
          name: activeIcon.value.name,
          unicode: activeIcon.value.unicode
        };
        emit("selected", icon);
      }
      return (_ctx, _cache) => {
        const _component_InputSelect = vue.resolveComponent("InputSelect");
        const _component_BaseInput = vue.resolveComponent("BaseInput");
        const _component_IconPackGrid = vue.resolveComponent("IconPackGrid");
        return vue.openBlock(), vue.createElementBlock("div", {
          class: vue.normalizeClass(["znpb-icon-pack-modal", { ["znpb-icon-pack-modal--has-special-filter"]: __props.specialFilterPack }])
        }, [
          vue.createElementVNode("div", _hoisted_1$N, [
            vue.createVNode(_component_InputSelect, {
              modelValue: activeCategory.value,
              options: vue.unref(packsOptions),
              class: "znpb-icons-category-select",
              placement: "bottom-start",
              "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => activeCategory.value = $event)
            }, null, 8, ["modelValue", "options"]),
            vue.createVNode(_component_BaseInput, {
              modelValue: vue.unref(searchModel),
              "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => vue.isRef(searchModel) ? searchModel.value = $event : null),
              placeholder: vue.unref(getPlaceholder),
              clearable: true,
              icon: "search"
            }, null, 8, ["modelValue", "placeholder"])
          ]),
          vue.createElementVNode("div", _hoisted_2$x, [
            (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(filteredList), (pack, i) => {
              var _a3, _b;
              return vue.openBlock(), vue.createBlock(_component_IconPackGrid, {
                key: i,
                "icon-list": pack.icons,
                family: pack.name,
                "active-icon": (_a3 = __props.modelValue) == null ? void 0 : _a3.name,
                "active-family": (_b = __props.modelValue) == null ? void 0 : _b.family,
                onIconSelected: ($event) => selectIcon($event, pack.name),
                "onUpdate:modelValue": ($event) => insertIcon($event, pack.name)
              }, null, 8, ["icon-list", "family", "active-icon", "active-family", "onIconSelected", "onUpdate:modelValue"]);
            }), 128))
          ])
        ], 2);
      };
    }
  }));
  var InputIcon_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$M = { class: "znpb-icon-options" };
  const _hoisted_2$w = ["onClick"];
  const _hoisted_3$n = {
    key: 0,
    class: "znpb-icon-options__delete"
  };
  const _hoisted_4$f = ["data-znpbiconfam", "data-znpbicon"];
  const __default__$C = {
    name: "IconLibrary"
  };
  const _sfc_main$11 = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$C), {
    props: {
      specialFilterPack: null,
      title: null,
      modelValue: null
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const showModal = vue.ref(false);
      const valueModel = vue.computed({
        get() {
          return props.modelValue;
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      function unicode(unicode2) {
        return JSON.parse('"\\' + unicode2 + '"');
      }
      function open2() {
        showModal.value = true;
      }
      return (_ctx, _cache) => {
        const _component_Icon = vue.resolveComponent("Icon");
        const _component_Modal = vue.resolveComponent("Modal");
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$M, [
          vue.createElementVNode("div", {
            class: vue.normalizeClass(["znpb-icon-trigger", { ["znpb-icon-trigger--no-icon"]: !__props.modelValue }]),
            onClick: vue.withModifiers(open2, ["self"])
          }, [
            __props.modelValue ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_3$n, [
              vue.createElementVNode("span", {
                class: "znpb-icon-preview",
                "data-znpbiconfam": __props.modelValue.family,
                "data-znpbicon": unicode(__props.modelValue.unicode),
                onClickPassive: _cache[0] || (_cache[0] = vue.withModifiers(($event) => showModal.value = true, ["stop"]))
              }, null, 40, _hoisted_4$f),
              vue.createVNode(_component_Icon, {
                icon: "delete",
                rounded: true,
                onClick: _cache[1] || (_cache[1] = vue.withModifiers(($event) => _ctx.$emit("update:modelValue", null), ["stop"]))
              })
            ])) : (vue.openBlock(), vue.createElementBlock("span", {
              key: 1,
              onClick: _cache[2] || (_cache[2] = ($event) => showModal.value = true)
            }, vue.toDisplayString(_ctx.$translate("select_icon")), 1))
          ], 10, _hoisted_2$w),
          vue.createVNode(_component_Modal, {
            show: showModal.value,
            "onUpdate:show": _cache[5] || (_cache[5] = ($event) => showModal.value = $event),
            width: 590,
            fullscreen: false,
            "append-to": ".znpb-center-area",
            "show-maximize": false,
            class: "znpb-icon-library-modal",
            title: _ctx.$translate("icon_library_title")
          }, {
            default: vue.withCtx(() => [
              vue.createVNode(_sfc_main$12, {
                modelValue: vue.unref(valueModel),
                "onUpdate:modelValue": _cache[3] || (_cache[3] = ($event) => vue.isRef(valueModel) ? valueModel.value = $event : null),
                "special-filter-pack": __props.specialFilterPack,
                onSelected: _cache[4] || (_cache[4] = ($event) => _ctx.$emit("update:modelValue", vue.unref(valueModel)))
              }, null, 8, ["modelValue", "special-filter-pack"])
            ]),
            _: 1
          }, 8, ["show", "title"])
        ]);
      };
    }
  }));
  var InlineEdit_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$L = ["readonly", "onKeydown"];
  const __default__$B = {
    name: "InlineEdit"
  };
  const _sfc_main$10 = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$B), {
    props: {
      modelValue: { default: "" },
      enabled: { type: Boolean, default: false },
      tag: { default: "div" }
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const root2 = vue.ref(null);
      const isEnabled = vue.ref(props.enabled);
      const computedModelValue = vue.computed({
        get() {
          return props.modelValue;
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      vue.watch(isEnabled, (newValue) => {
        if (newValue) {
          document.addEventListener("click", disableOnOutsideClick, true);
        } else {
          document.removeEventListener("click", disableOnOutsideClick, true);
        }
      });
      vue.onBeforeUnmount(() => {
        document.removeEventListener("click", disableOnOutsideClick, true);
      });
      function disableOnOutsideClick(event2) {
        if (event2.target !== root2.value) {
          disableEdit();
        }
      }
      function disableEdit() {
        var _a3;
        isEnabled.value = false;
        (_a3 = window.getSelection()) == null ? void 0 : _a3.removeAllRanges();
      }
      return (_ctx, _cache) => {
        return vue.withDirectives((vue.openBlock(), vue.createElementBlock("input", {
          ref_key: "root",
          ref: root2,
          "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(computedModelValue) ? computedModelValue.value = $event : null),
          readonly: !isEnabled.value,
          class: vue.normalizeClass(["znpb-inlineEditInput", { "znpb-inlineEditInput--readonly": !isEnabled.value }]),
          onDblclick: _cache[1] || (_cache[1] = ($event) => isEnabled.value = true),
          onKeydown: vue.withKeys(vue.withModifiers(disableEdit, ["stop"]), ["escape"])
        }, null, 42, _hoisted_1$L)), [
          [vue.vModelText, vue.unref(computedModelValue)]
        ]);
      };
    }
  }));
  var createHooksInstance = () => {
    const filters = {};
    const actions = {};
    const addAction2 = (event2, callback) => {
      if (typeof actions[event2] === "undefined") {
        actions[event2] = [];
      }
      actions[event2].push(callback);
    };
    function on(event2, callback) {
      console.warn("zb.hooks.on was deprecated in favour of window.zb.addAction");
      return addAction2(event2, callback);
    }
    const removeAction2 = (event2, callback) => {
      if (typeof actions[event2] !== "undefined") {
        const callbackIndex = actions[event2].indexOf(callback);
        if (callbackIndex !== -1) {
          actions[event2].splice(callbackIndex);
        }
      }
    };
    function off(event2, callback) {
      console.warn("zb.hooks.off was deprecated in favour of window.zb.addAction");
      return addAction2(event2, callback);
    }
    const doAction2 = (event2, ...data2) => {
      if (typeof actions[event2] !== "undefined") {
        actions[event2].forEach((callbackFunction) => {
          callbackFunction(...data2);
        });
      }
    };
    function trigger(event2, ...data2) {
      console.warn("zb.hooks.trigger was deprecated in favour of window.zb.addAction");
      return doAction2(event2, ...data2);
    }
    const addFilter2 = (id, callback) => {
      if (typeof filters[id] === "undefined") {
        filters[id] = [];
      }
      filters[id].push(callback);
    };
    const applyFilters2 = (id, value, ...params) => {
      if (typeof filters[id] !== "undefined") {
        filters[id].forEach((callback) => {
          value = callback(value, ...params);
        });
      }
      return value;
    };
    return {
      addAction: addAction2,
      removeAction: removeAction2,
      doAction: doAction2,
      addFilter: addFilter2,
      applyFilters: applyFilters2,
      on,
      off,
      trigger
    };
  };
  const hooks = createHooksInstance();
  const { addAction, removeAction, doAction, addFilter, applyFilters } = hooks;
  window.zb = window.zb || {};
  window.zb.hooks = hooks;
  var HOOKS = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
    __proto__: null,
    hooks,
    addAction,
    removeAction,
    doAction,
    addFilter,
    applyFilters,
    createHooksInstance
  }, Symbol.toStringTag, { value: "Module" }));
  var CustomSize_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$K = { class: "znpb-forms-image-custom-size__wrapper" };
  const _hoisted_2$v = { class: "znpb-forms-image-custom-size__option-separator" };
  const __default__$A = {
    name: "CustomSize",
    data() {
      return {};
    },
    methods: {}
  };
  const _sfc_main$$ = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$A), {
    props: {
      modelValue: null
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      var _a3, _b;
      const props = __props;
      const width = vue.ref((_a3 = props.modelValue) == null ? void 0 : _a3.width);
      const height = vue.ref((_b = props.modelValue) == null ? void 0 : _b.height);
      function onCustomSizeClick() {
        if (width.value || height.value) {
          emit("update:modelValue", {
            width: width.value || "",
            height: height.value || ""
          });
        }
      }
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$K, [
          vue.createVNode(_sfc_main$1k, {
            title: _ctx.$translate("custom_width"),
            align: "center",
            class: "znpb-forms-image-custom-size__option-wrapper"
          }, {
            default: vue.withCtx(() => [
              vue.createVNode(_sfc_main$1D, {
                modelValue: width.value,
                "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => width.value = $event)
              }, null, 8, ["modelValue"])
            ]),
            _: 1
          }, 8, ["title"]),
          vue.createElementVNode("div", _hoisted_2$v, [
            vue.createVNode(vue.unref(_sfc_main$1K), {
              icon: "close",
              size: 10
            })
          ]),
          vue.createVNode(_sfc_main$1k, {
            title: _ctx.$translate("custom_height"),
            align: "center",
            class: "znpb-forms-image-custom-size__option-wrapper"
          }, {
            default: vue.withCtx(() => [
              vue.createVNode(_sfc_main$1D, {
                modelValue: height.value,
                "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => height.value = $event)
              }, null, 8, ["modelValue"])
            ]),
            _: 1
          }, 8, ["title"]),
          vue.createElementVNode("div", { class: "znpb-forms-image-custom-size__option-wrapper" }, [
            vue.createElementVNode("button", {
              class: "znpb-button znpb-button--line znpb-forms-image-custom-size__apply-button",
              onClick: onCustomSizeClick
            }, " Apply ")
          ])
        ]);
      };
    }
  }));
  var InputImage_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$J = { class: "znpb-input-image__wrapper" };
  const _hoisted_2$u = { class: "znpb-input-image-holder__image-actions" };
  const _hoisted_3$m = ["onMousedown"];
  const _hoisted_4$e = /* @__PURE__ */ vue.createElementVNode("span", { class: "znpb-input-image-holder__drag-button" }, null, -1);
  const _hoisted_5$8 = [
    _hoisted_4$e
  ];
  const _hoisted_6$7 = ["onClick"];
  const _hoisted_7$3 = { class: "znpb-actions-overlay__expander-text" };
  const _hoisted_8$3 = {
    key: 2,
    class: "znpb-input-image__custom-size-wrapper"
  };
  const wp = window.wp;
  const __default__$z = {
    name: "InputImage"
  };
  const _sfc_main$_ = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$z), {
    props: {
      modelValue: null,
      emptyText: { default: "No Image Selected" },
      shouldDragImage: { type: Boolean },
      positionLeft: { default: "50%" },
      positionTop: { default: "50%" },
      show_size: { type: Boolean }
    },
    emits: ["background-position-change", "update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const inputWrapper = vue.inject("inputWrapper");
      const optionsForm = vue.inject("optionsForm");
      const imageComponent = vue.computed(() => {
        return applyFilters("zionbuilder/options/image/image_component", "img", props.modelValue);
      });
      const imageHolder = vue.ref(null);
      const image = vue.ref(null);
      const dragButton = vue.ref(null);
      const attachmentId = vue.ref(null);
      const isDragging = vue.ref(false);
      const imageContainerPosition = vue.ref({
        left: null,
        top: null
      });
      const imageHolderWidth = vue.ref(null);
      const imageHolderHeight = vue.ref(null);
      const previewExpanded = vue.ref(false);
      const minHeight = vue.ref(200);
      const imageHeight = vue.ref(null);
      const initialX = vue.ref(null);
      const initialY = vue.ref(null);
      const attachmentModel = vue.ref(null);
      const loading2 = vue.ref(true);
      const dynamicImageSrc = vue.ref(null);
      let mediaModal;
      vue.watch(
        () => props.modelValue,
        (newValue, oldValue) => {
          if (newValue !== oldValue) {
            vue.nextTick(() => {
              getImageHeight();
              if (previewExpanded.value) {
                toggleExpand();
              }
            });
          }
        }
      );
      const customComponent = vue.computed(() => {
        const { applyFilters: applyFilters2 } = window.zb.hooks;
        return applyFilters2("zionbuilder/options/image/display_component", null, props.modelValue, inputWrapper, optionsForm);
      });
      const isSVG = vue.computed(() => {
        if (imageSrc.value) {
          return imageSrc.value.endsWith(".svg");
        }
        return imageSrc.value;
      });
      const imageSizes = vue.computed(() => {
        var _a3, _b, _c, _d;
        const options2 = [];
        const imageSizes2 = (_b = (_a3 = attachmentModel.value) == null ? void 0 : _a3.attributes) == null ? void 0 : _b.sizes;
        const customSizes = (_d = (_c = attachmentModel.value) == null ? void 0 : _c.attributes) == null ? void 0 : _d.zion_custom_sizes;
        const allSizes = __spreadValues(__spreadValues({}, imageSizes2), customSizes);
        Object.keys(allSizes).forEach((sizeKey) => {
          const name = startCase$1(sizeKey);
          const width = allSizes[sizeKey].width;
          const height = allSizes[sizeKey].height;
          const optionName = `${name} ( ${width} x ${height} )`;
          options2.push({
            name: optionName,
            id: sizeKey
          });
        });
        options2.push({
          name: "Custom",
          id: "custom"
        });
        return options2;
      });
      const sizeValue = vue.computed({
        get() {
          var _a3;
          return typeof props.modelValue === "object" && ((_a3 = props.modelValue) == null ? void 0 : _a3.image_size) || "full";
        },
        set(newValue) {
          const value = typeof props.modelValue === "object" ? props.modelValue : {};
          emit("update:modelValue", __spreadProps(__spreadValues({}, value), {
            image_size: newValue
          }));
        }
      });
      const customSizeValue = vue.computed({
        get() {
          var _a3;
          return typeof props.modelValue === "object" && ((_a3 = props.modelValue) == null ? void 0 : _a3.custom_size) || {};
        },
        set(newValue) {
          emit("update:modelValue", __spreadProps(__spreadValues({}, typeof props.modelValue === "object" && props.modelValue || {}), {
            custom_size: newValue
          }));
        }
      });
      const positionCircleStyle = vue.computed(() => {
        return {
          left: props.positionLeft.includes("%") ? props.positionLeft : "",
          top: props.positionTop.includes("%") ? props.positionTop : ""
        };
      });
      const wrapperStyles = vue.computed(() => {
        if (imageSrc.value && imageHolderHeight.value) {
          return {
            height: imageHolderHeight.value + "px"
          };
        }
        return {};
      });
      const imageValue = vue.computed({
        get() {
          if (props.show_size) {
            return props.modelValue || {};
          } else {
            return props.modelValue || null;
          }
        },
        set(newValue) {
          if (props.show_size) {
            emit("update:modelValue", __spreadValues(__spreadValues({}, typeof props.modelValue === "object" && props.modelValue || {}), newValue));
          } else {
            emit("update:modelValue", newValue);
          }
        }
      });
      const imageSrc = vue.computed(() => {
        var _a3;
        return dynamicImageSrc.value ? dynamicImageSrc.value : typeof props.modelValue === "object" ? ((_a3 = props.modelValue) == null ? void 0 : _a3.image) || null : props.modelValue || null;
      });
      const element = vue.inject("ZionElement");
      vue.watchEffect(() => {
        doAction("zionbuilder/input/image/src_url", dynamicImageSrc, props.modelValue, element);
      });
      const shouldDisplayExpander = vue.computed(() => {
        return imageHolderHeight.value >= minHeight.value;
      });
      function getImageHeight() {
        if (!image.value) {
          return;
        }
        image.value.addEventListener("load", () => {
          const localImageHeight = image.value.getBoundingClientRect().height;
          imageHeight.value = localImageHeight;
          imageHolderHeight.value = localImageHeight < minHeight.value ? localImageHeight : minHeight.value;
        });
      }
      function toggleExpand() {
        previewExpanded.value = !previewExpanded.value;
        if (previewExpanded.value) {
          imageHolderHeight.value = imageHeight.value;
        } else {
          imageHolderHeight.value = minHeight.value;
        }
      }
      function startDrag(event2) {
        if (props.shouldDragImage) {
          window.addEventListener("mousemove", doDrag);
          window.addEventListener("mouseup", stopDrag);
          isDragging.value = true;
          const { height, width, left: left2, top: top2 } = imageHolder.value.getBoundingClientRect();
          imageHolderWidth.value = width;
          imageHolderHeight.value = height;
          imageContainerPosition.value.left = left2;
          imageContainerPosition.value.top = top2;
          initialX.value = event2.pageX;
          initialY.value = event2.pageY;
        }
      }
      function doDrag(event2) {
        window.document.body.style.userSelect = "none";
        const movedX = event2.clientX - imageContainerPosition.value.left;
        const movedY = event2.clientY - imageContainerPosition.value.top;
        let xToSend = clamp(Math.round(movedX / imageHolderWidth.value * 100), 0, 100);
        let yToSend = clamp(Math.round(movedY / imageHolderHeight.value * 100), 0, 100);
        if (event2.shiftKey) {
          xToSend = Math.round(xToSend / 5) * 5;
          yToSend = Math.round(yToSend / 5) * 5;
        }
        emit("background-position-change", { x: xToSend, y: yToSend });
      }
      function stopDrag(event2) {
        initialX.value = event2.pageX;
        initialY.value = event2.pageY;
        window.removeEventListener("mousemove", doDrag);
        window.removeEventListener("mouseup", stopDrag);
        window.document.body.style.userSelect = "";
        setTimeout(() => {
          isDragging.value = false;
        }, 100);
      }
      function openMediaModal() {
        if (isDragging.value) {
          return;
        }
        if (!mediaModal) {
          const args = {
            frame: "select",
            state: "zion-media",
            library: {
              type: "image"
            },
            button: {
              text: "Add Image"
            }
          };
          mediaModal = new window.wp.media.view.MediaFrame.ZionBuilderFrame(args);
          mediaModal.on("select update insert", selectMedia);
          mediaModal.on("open", setMediaModalSelection);
        }
        mediaModal.open();
      }
      function selectMedia() {
        const selection = mediaModal.state().get("selection").first();
        if (props.show_size) {
          emit("update:modelValue", {
            image: selection.get("url")
          });
        } else {
          imageValue.value = selection.get("url");
        }
        attachmentId.value = selection.get("id");
        attachmentModel.value = selection;
        loading2.value = false;
      }
      function setMediaModalSelection() {
        const selection = mediaModal.state().get("selection");
        if (imageSrc.value && !attachmentId.value) {
          const attachment = wp.media.model.Attachment.get(imageSrc.value);
          attachment.fetch({
            data: {
              is_media_image: true,
              image_url: imageSrc.value
            }
          }).done((event2) => {
            if (event2 && event2.id) {
              attachmentId.value = event2.id;
              const attachment2 = wp.media.model.Attachment.get(attachmentId.value);
              selection.reset(attachment2 ? [attachment2] : []);
            }
          });
        } else if (imageSrc.value && attachmentId.value) {
          const attachment = wp.media.model.Attachment.get(attachmentId.value);
          selection.reset(attachment ? [attachment] : []);
        }
      }
      function deleteImage() {
        emit("update:modelValue", null);
        attachmentId.value = null;
        if (mediaModal) {
          let selection = mediaModal.state().get("selection");
          selection.reset([]);
        }
      }
      function getAttachmentModel() {
        if (imageSrc.value && !attachmentModel.value) {
          const attachment = wp.media.model.Attachment.get(imageSrc.value);
          attachment.fetch({
            data: {
              is_media_image: true,
              image_url: imageSrc.value
            }
          }).done((event2) => {
            if (event2 == null ? void 0 : event2.id) {
              attachmentId.value = event2.id;
              attachmentModel.value = wp.media.model.Attachment.get(attachmentId.value);
            }
            loading2.value = false;
          });
        }
      }
      vue.onMounted(() => {
        if (props.show_size) {
          getAttachmentModel();
        } else {
          loading2.value = false;
        }
        getImageHeight();
      });
      vue.watch(dynamicImageSrc, () => {
        getAttachmentModel();
      });
      vue.onBeforeUnmount(() => {
        window.removeEventListener("mousemove", doDrag);
        window.removeEventListener("mouseup", stopDrag);
        window.document.body.style.userSelect = "";
        if (mediaModal) {
          mediaModal.detach();
        }
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$J, [
          vue.unref(customComponent) ? (vue.openBlock(), vue.createBlock(vue.resolveDynamicComponent(vue.unref(customComponent)), { key: 0 })) : (vue.openBlock(), vue.createElementBlock("div", {
            key: 1,
            ref_key: "imageHolder",
            ref: imageHolder,
            class: "znpb-input-image-holder",
            style: vue.normalizeStyle(vue.unref(wrapperStyles)),
            onClick: openMediaModal
          }, [
            vue.createVNode(vue.unref(_sfc_main$1I), {
              "show-overlay": !isDragging.value
            }, {
              actions: vue.withCtx(() => [
                vue.createElementVNode("div", _hoisted_2$u, [
                  vue.createVNode(_sfc_main$1K, {
                    rounded: true,
                    icon: "delete",
                    "bg-size": 30,
                    onClick: vue.withModifiers(deleteImage, ["stop"])
                  }, null, 8, ["onClick"]),
                  vue.createVNode(vue.unref(_sfc_main$j), { location: "options/image/actions" })
                ])
              ]),
              default: vue.withCtx(() => [
                (vue.openBlock(), vue.createBlock(vue.resolveDynamicComponent(vue.unref(imageComponent)), {
                  src: vue.unref(imageSrc),
                  class: "znpb-input-image-holder__image"
                }, null, 8, ["src"]))
              ]),
              _: 1
            }, 8, ["show-overlay"]),
            __props.shouldDragImage && (previewExpanded.value || !vue.unref(shouldDisplayExpander)) ? (vue.openBlock(), vue.createElementBlock("div", {
              key: 0,
              ref_key: "dragButton",
              ref: dragButton,
              class: "znpb-drag-icon-wrapper",
              style: vue.normalizeStyle(vue.unref(positionCircleStyle)),
              onMousedown: vue.withModifiers(startDrag, ["stop"])
            }, _hoisted_5$8, 44, _hoisted_3$m)) : vue.createCommentVNode("", true),
            !isDragging.value && __props.shouldDragImage && vue.unref(shouldDisplayExpander) && vue.unref(imageSrc) ? (vue.openBlock(), vue.createElementBlock("div", {
              key: 1,
              class: vue.normalizeClass(["znpb-actions-overlay__expander", { "znpb-actions-overlay__expander--icon-rotated": previewExpanded.value }]),
              onClick: vue.withModifiers(toggleExpand, ["stop"])
            }, [
              vue.createElementVNode("strong", _hoisted_7$3, vue.toDisplayString(previewExpanded.value ? "CONTRACT" : "EXPAND"), 1),
              vue.createVNode(_sfc_main$1K, {
                icon: "select",
                "bg-size": 12
              })
            ], 10, _hoisted_6$7)) : vue.createCommentVNode("", true),
            !vue.unref(imageSrc) && !vue.unref(customComponent) ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1t), {
              key: 2,
              class: "znpb-input-image-holder__empty",
              "no-margin": true
            }, {
              default: vue.withCtx(() => [
                vue.createTextVNode(vue.toDisplayString(__props.emptyText) + " ", 1),
                vue.createVNode(vue.unref(_sfc_main$j), { location: "options/image/actions" })
              ]),
              _: 1
            })) : vue.createCommentVNode("", true)
          ], 4)),
          __props.show_size && vue.unref(imageSrc) && !vue.unref(isSVG) && !loading2.value ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_8$3, [
            vue.createVNode(vue.unref(_sfc_main$1k), { title: "Image size" }, {
              default: vue.withCtx(() => [
                vue.createVNode(vue.unref(InputSelect), {
                  modelValue: vue.unref(sizeValue),
                  "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(sizeValue) ? sizeValue.value = $event : null),
                  options: vue.unref(imageSizes)
                }, null, 8, ["modelValue", "options"])
              ]),
              _: 1
            }),
            vue.unref(sizeValue) === "custom" ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1k), { key: 0 }, {
              default: vue.withCtx(() => [
                vue.createVNode(_sfc_main$$, {
                  modelValue: vue.unref(customSizeValue),
                  "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => vue.isRef(customSizeValue) ? customSizeValue.value = $event : null)
                }, null, 8, ["modelValue"])
              ]),
              _: 1
            })) : vue.createCommentVNode("", true)
          ])) : vue.createCommentVNode("", true)
        ]);
      };
    }
  }));
  const schemas = vue.ref({
    element_advanced: window.ZnPbComponentsData.schemas.element_advanced,
    element_styles: window.ZnPbComponentsData.schemas.styles,
    typography: window.ZnPbComponentsData.schemas.typography,
    videoOptionSchema: window.ZnPbComponentsData.schemas.video,
    backgroundImageSchema: window.ZnPbComponentsData.schemas.background_image,
    shadowSchema: window.ZnPbComponentsData.schemas.shadow,
    styles: window.ZnPbComponentsData.schemas.styles
  });
  const useOptionsSchemas = () => {
    const getSchema = (schemaId) => {
      return cloneDeep(schemas.value[schemaId]) || {};
    };
    const registerSchema = (schemaId, schema) => {
      schemas.value[schemaId] = schema;
    };
    return {
      schemas,
      getSchema,
      registerSchema
    };
  };
  var BackgroundImage_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$I = { class: "znpb-input-background-image" };
  const __default__$y = {
    name: "InputBackgroundImage"
  };
  const _sfc_main$Z = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$y), {
    props: {
      modelValue: null
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const { getSchema } = useOptionsSchemas();
      const backgroundImageSchema = getSchema("backgroundImageSchema");
      const computedValue = vue.computed({
        get() {
          return props.modelValue || {};
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      const backgroundPositionXModel = vue.computed(() => computedValue.value["background-position-x"]);
      const backgroundPositionYModel = vue.computed(() => computedValue.value["background-position-y"]);
      function changeBackgroundPosition(position) {
        emit("update:modelValue", __spreadProps(__spreadValues({}, computedValue.value), {
          "background-position-x": `${position.x}%`,
          "background-position-y": `${position.y}%`
        }));
      }
      function onOptionUpdated(optionId, newValue) {
        const newValues = __spreadValues({}, props.modelValue);
        newValues[optionId] = newValue;
        computedValue.value = newValues;
      }
      return (_ctx, _cache) => {
        const _component_OptionsForm = vue.resolveComponent("OptionsForm");
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$I, [
          vue.createVNode(_sfc_main$_, {
            modelValue: vue.unref(computedValue)["background-image"],
            "should-drag-image": true,
            "position-top": vue.unref(backgroundPositionYModel),
            "position-left": vue.unref(backgroundPositionXModel),
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => onOptionUpdated("background-image", $event)),
            onBackgroundPositionChange: changeBackgroundPosition
          }, null, 8, ["modelValue", "position-top", "position-left"]),
          vue.createVNode(_component_OptionsForm, {
            modelValue: vue.unref(computedValue),
            "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => vue.isRef(computedValue) ? computedValue.value = $event : null),
            schema: vue.unref(backgroundImageSchema)
          }, null, 8, ["modelValue", "schema"])
        ]);
      };
    }
  }));
  /**
    reframe.js - Reframe.js: responsive iframes for embedded content
    @version v4.0.1
    @link https://github.com/yowainwright/reframe.ts#readme
    @author Jeff Wainwright <yowainwright@gmail.com> (http://jeffry.in)
    @license MIT
  **/
  function reframe(target, cName) {
    var _a3, _b;
    var frames = typeof target === "string" ? document.querySelectorAll(target) : target;
    var c = cName || "js-reframe";
    if (!("length" in frames))
      frames = [frames];
    for (var i = 0; i < frames.length; i += 1) {
      var frame = frames[i];
      var hasClass = frame.className.split(" ").indexOf(c) !== -1;
      if (hasClass || frame.style.width.indexOf("%") > -1) {
        return;
      }
      var height = frame.getAttribute("height") || frame.offsetHeight;
      var width = frame.getAttribute("width") || frame.offsetWidth;
      var heightNumber = typeof height === "string" ? parseInt(height) : height;
      var widthNumber = typeof width === "string" ? parseInt(width) : width;
      var padding = heightNumber / widthNumber * 100;
      var div = document.createElement("div");
      div.className = c;
      var divStyles = div.style;
      divStyles.position = "relative";
      divStyles.width = "100%";
      divStyles.paddingTop = "".concat(padding, "%");
      var frameStyle = frame.style;
      frameStyle.position = "absolute";
      frameStyle.width = "100%";
      frameStyle.height = "100%";
      frameStyle.left = "0";
      frameStyle.top = "0";
      (_a3 = frame.parentNode) === null || _a3 === void 0 ? void 0 : _a3.insertBefore(div, frame);
      (_b = frame.parentNode) === null || _b === void 0 ? void 0 : _b.removeChild(frame);
      div.appendChild(frame);
    }
  }
  let YoutubeApiLoadedState = 0;
  let vimeoApiLoadedState = 0;
  let videoIndex = 0;
  let vimeoVolume = 1;
  const globalEventBus = createHooksInstance();
  class Video {
    constructor(domNode, options2 = {}) {
      this.options = {};
      this.videoContainer = null;
      this.videoSource = "local";
      this.muted = true;
      this.playing = true;
      this.options = __spreadValues({
        autoplay: true,
        muted: true,
        loop: true,
        controls: true,
        controlsPosition: "bottom-left",
        videoSource: "local",
        responsive: true
      }, options2);
      this.eventBus = createHooksInstance();
      this.on = this.eventBus.addAction;
      this.off = this.eventBus.removeAction;
      this.trigger = this.eventBus.doAction;
      this.domNode = domNode;
      this.videoIndex = videoIndex++;
      this.videoContainer = null;
      if (this.options.responsive) {
        this.on("video_ready", () => {
          reframe(this.videoContainer);
        });
      }
      this.domNode.zionVideo = this;
      this.nextTick(() => {
        if (this.options.videoSource === "local" && this.options.mp4) {
          this.videoSource = "local";
          this.setupLocal();
        } else if (this.options.videoSource === "youtube" && this.options.youtubeURL) {
          this.videoSource = "youtube";
          this.YoutubeId = this.youtubeUrlParser(this.options.youtubeURL);
          this.setupYoutube();
        } else if (this.options.videoSource === "vimeo" && this.options.vimeoURL) {
          this.videoSource = "vimeo";
          this.setupVimeo();
        }
      });
    }
    youtubeUrlParser(url) {
      const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
      const match = url.match(regExp);
      return match && match[7].length === 11 ? match[7] : false;
    }
    nextTick(callback) {
      setTimeout(() => {
        callback();
      }, 0);
    }
    setupYoutube() {
      const YtParams = {
        mute: this.options.muted ? 1 : 0,
        autoplay: this.options.autoplay ? 1 : 0,
        iv_load_policy: 3,
        showinfo: 0,
        controls: this.options.controls ? 1 : 0,
        modestbranding: 1,
        rel: 0,
        wmode: "transparent"
      };
      let YtParamsString = "";
      for (const [key, value] of Object.entries(YtParams)) {
        YtParamsString += `&${key}=${value}`;
      }
      const youtubeIframe = document.createElement("iframe");
      youtubeIframe.src = `https://www.youtube-nocookie.com/embed/${this.YoutubeId}?enablejsapi=1${YtParamsString}`;
      youtubeIframe.id = `znpb-video-bg-youtube-${this.videoIndex}`;
      youtubeIframe.allow = "autoplay; fullscreen";
      youtubeIframe.width = 425;
      youtubeIframe.height = 239;
      this.domNode.appendChild(youtubeIframe);
      if (YoutubeApiLoadedState === 0) {
        const youtubeTag = document.createElement("script");
        youtubeTag.src = "https://www.youtube.com/iframe_api";
        const firstScriptTag = document.getElementsByTagName("script")[0];
        firstScriptTag.parentNode.insertBefore(youtubeTag, firstScriptTag);
        const self2 = this;
        window.onYouTubeIframeAPIReady = function() {
          self2.enableYoutube();
          globalEventBus.doAction("youtube_api_ready");
          YoutubeApiLoadedState = 2;
        };
        YoutubeApiLoadedState = 1;
      } else if (YoutubeApiLoadedState === 1) {
        globalEventBus.on("youtube_api_ready", this.enableYoutube.bind(this));
      } else if (YoutubeApiLoadedState === 2) {
        this.enableYoutube();
      }
    }
    enableYoutube() {
      this.player = new window.YT.Player(`znpb-video-bg-youtube-${this.videoIndex}`, {
        height: "100%",
        width: "100%",
        videoId: this.YoutubeId
      });
      this.videoContainer = this.player.getIframe();
      this.trigger("video_ready");
    }
    setupVimeo() {
      const vimeoContainer = document.createElement("div");
      vimeoContainer.id = `znpb-video-bg-vimeo-${this.videoIndex}`;
      this.domNode.appendChild(vimeoContainer);
      if (vimeoApiLoadedState === 0) {
        const vimeoTag = document.createElement("script");
        vimeoTag.src = "https://player.vimeo.com/api/player.js";
        const secondScriptTag = document.getElementsByTagName("script")[1];
        const self2 = this;
        secondScriptTag.parentNode.insertBefore(vimeoTag, secondScriptTag);
        vimeoTag.onload = function() {
          self2.enableVimeo();
          globalEventBus.doAction("vimeo_api_ready");
          vimeoApiLoadedState = 2;
        };
        vimeoApiLoadedState = 1;
      } else if (vimeoApiLoadedState === 1) {
        globalEventBus.on("vimeo_api_ready", this.enableVimeo.bind(this));
      } else if (vimeoApiLoadedState === 2) {
        this.enableVimeo();
      }
    }
    enableVimeo() {
      this.player = new window.Vimeo.Player(`znpb-video-bg-vimeo-${this.videoIndex}`, {
        id: this.options.vimeoURL,
        background: this.options.autoplay,
        muted: this.options.muted,
        transparent: true,
        autoplay: this.options.autoplay,
        controls: this.options.controls
      });
      this.player.ready().then(() => {
        this.videoContainer = this.player.element;
        this.trigger("video_ready");
      });
    }
    setupLocal() {
      const autoplay = this.options.autoplay ? "autoplay" : "";
      const muted = this.options.muted ? "muted" : "";
      const loop = this.options.loop ? "loop" : "";
      const videoElement = document.createElement("video");
      videoElement.muted = muted;
      videoElement.autoplay = autoplay;
      videoElement.loop = loop;
      if (this.options.controls) {
        videoElement.controls = true;
      }
      if (this.options.mp4) {
        const sourceMP4 = document.createElement("source");
        sourceMP4.src = this.options.mp4;
        videoElement.appendChild(sourceMP4);
      }
      this.domNode.appendChild(videoElement);
      this.player = videoElement;
      this.videoContainer = videoElement;
      this.trigger("video_ready");
    }
    getVideoContainer() {
      return this.videoContainer;
    }
    play() {
      if (this.videoSource === "youtube") {
        this.player.playVideo();
      }
      if (this.videoSource === "vimeo") {
        this.player.play();
      }
      if (this.videoSource === "local") {
        this.player.play();
      }
      this.playing = true;
    }
    pause() {
      if (this.videoSource === "youtube") {
        this.player.pauseVideo();
      }
      if (this.videoSource === "vimeo") {
        this.player.pause();
      }
      if (this.videoSource === "local") {
        this.player.pause();
      }
      this.playing = false;
    }
    togglePlay() {
      if (this.playing) {
        this.pause();
      } else {
        this.play();
      }
    }
    mute() {
      if (this.videoSource === "youtube") {
        this.player.mute();
      }
      if (this.videoSource === "vimeo") {
        this.player.getVolume().then((volume) => {
          vimeoVolume = volume;
        });
        this.player.setVolume(0);
      }
      if (this.videoSource === "local") {
        this.player.muted = true;
      }
      this.muted = true;
    }
    unMute() {
      if (this.videoSource === "youtube") {
        this.player.unMute();
      }
      if (this.videoSource === "vimeo") {
        this.player.setVolume(vimeoVolume);
      }
      if (this.videoSource === "local") {
        this.player.muted = false;
      }
      this.muted = false;
    }
    toggleMute() {
      if (this.muted) {
        this.unMute();
      } else {
        this.mute();
      }
    }
    destroy() {
      this.trigger("beforeDestroy");
      this.player = null;
      while (this.domNode.firstChild) {
        this.domNode.removeChild(this.domNode.firstChild);
      }
    }
  }
  window.zbVideo = Video;
  var InputBackgroundVideo_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$H = { class: "znpb-input-background-video" };
  const _hoisted_2$t = { class: "znpb-input-background-video__holder" };
  const __default__$x = {
    name: "InputBackgroundVideo"
  };
  const _sfc_main$Y = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$x), {
    props: {
      modelValue: null,
      options: null,
      exclude_options: null
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const videoInstance = vue.ref(null);
      const mediaModal = vue.ref(null);
      const videoPreview = vue.ref(null);
      const { getSchema } = useOptionsSchemas();
      const schema = vue.computed(() => {
        const schema2 = __spreadValues({}, getSchema("videoOptionSchema"));
        if (props.exclude_options) {
          props.exclude_options.forEach((optionToRemove) => {
            if (schema2[optionToRemove]) {
              delete schema2[optionToRemove];
            }
          });
        }
        return schema2;
      });
      const computedValue = vue.computed({
        get() {
          return props.modelValue || {};
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      const hasVideo = vue.computed(() => {
        if (videoSourceModel.value === "local" && computedValue.value.mp4) {
          return true;
        }
        if (videoSourceModel.value === "youtube" && computedValue.value.youtubeURL) {
          return true;
        }
        if (videoSourceModel.value === "vimeo" && computedValue.value.vimeoURL) {
          return true;
        }
        return false;
      });
      const videoSourceModel = vue.computed({
        get() {
          return computedValue.value["videoSource"] || "local";
        },
        set(newValue) {
          emit("update:modelValue", __spreadProps(__spreadValues({}, props.modelValue), {
            videoSource: newValue
          }));
        }
      });
      vue.watch(computedValue, () => {
        if (videoInstance.value) {
          videoInstance.value.destroy();
        }
        if (hasVideo.value) {
          initVideo();
        }
      });
      function initVideo() {
        vue.nextTick(() => {
          videoInstance.value = new Video(videoPreview.value, computedValue.value);
        });
      }
      function openMediaModal() {
        if (mediaModal.value === null) {
          const args = {
            frame: "select",
            state: "library",
            library: { type: "video" },
            button: { text: "Add video" },
            selection: computedValue.value
          };
          mediaModal.value = window.wp.media(args);
          mediaModal.value.on("select update insert", selectMedia);
        }
        mediaModal.value.open();
      }
      function selectMedia() {
        const selection = mediaModal.value.state().get("selection").toJSON();
        emit("update:modelValue", __spreadProps(__spreadValues({}, computedValue.value), {
          mp4: selection[0].url
        }));
      }
      function deleteVideo() {
        const _a3 = computedValue.value, { mp4 } = _a3, rest = __objRest(_a3, ["mp4"]);
        emit("update:modelValue", __spreadValues({}, rest));
      }
      vue.onMounted(() => {
        if (hasVideo.value) {
          initVideo();
        }
      });
      return (_ctx, _cache) => {
        const _component_OptionsForm = vue.resolveComponent("OptionsForm");
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$H, [
          vue.createElementVNode("div", _hoisted_2$t, [
            vue.unref(hasVideo) ? (vue.openBlock(), vue.createElementBlock("div", {
              key: 0,
              ref_key: "videoPreview",
              ref: videoPreview,
              class: "znpb-input-background-video__source"
            }, null, 512)) : (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1t), {
              key: 1,
              class: "znpb-input-background-video__empty znpb-input-background-video__source",
              "no-margin": true,
              onClick: openMediaModal
            }, {
              default: vue.withCtx(() => [
                vue.createTextVNode(vue.toDisplayString(_ctx.$translate("no_video_selected")), 1)
              ]),
              _: 1
            })),
            vue.unref(videoSourceModel) == "local" && vue.unref(hasVideo) ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
              key: 2,
              class: "znpb-input-background-video__delete",
              icon: "delete",
              "bg-size": 30,
              "bg-color": "#fff",
              onClick: vue.withModifiers(deleteVideo, ["stop"])
            }, null, 8, ["onClick"])) : vue.createCommentVNode("", true)
          ]),
          vue.createVNode(_component_OptionsForm, {
            modelValue: vue.unref(computedValue),
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(computedValue) ? computedValue.value = $event : null),
            schema: vue.unref(schema),
            class: "znpb-input-background-video__holder"
          }, null, 8, ["modelValue", "schema"])
        ]);
      };
    }
  }));
  var InputBorderControl_vue_vue_type_style_index_0_lang = "";
  const __default__$w = {
    name: "InputBorderControl"
  };
  const _sfc_main$X = vue.defineComponent(__spreadProps(__spreadValues({}, __default__$w), {
    props: {
      modelValue: null,
      title: null,
      placeholder: { default: () => {
        return {};
      } }
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const schema = vue.computed(() => {
        return {
          color: {
            id: "color",
            type: "colorpicker",
            css_class: "znpb-border-control-group-item",
            title: "Color",
            width: 100,
            placeholder: props.placeholder ? props.placeholder["color"] : null
          },
          width: {
            id: "width",
            type: "number_unit",
            title: "Width",
            min: 0,
            max: 999,
            default_unit: "px",
            step: 1,
            css_class: "znpb-border-control-group-item",
            width: 50,
            placeholder: props.placeholder ? props.placeholder["width"] : null
          },
          style: {
            id: "style",
            type: "select",
            title: "Style",
            options: ["solid", "dashed", "dotted", "double", "groove", "ridge", "inset", "outset"].map((style) => {
              return { name: style, id: style };
            }),
            css_class: "znpb-border-control-group-item",
            width: 50,
            placeholder: props.placeholder ? props.placeholder["style"] : "solid"
          }
        };
      });
      const computedValue = vue.computed({
        get() {
          return props.modelValue || {};
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      return (_ctx, _cache) => {
        const _component_OptionsForm = vue.resolveComponent("OptionsForm");
        return vue.openBlock(), vue.createBlock(_component_OptionsForm, {
          modelValue: vue.unref(computedValue),
          "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(computedValue) ? computedValue.value = $event : null),
          schema: vue.unref(schema),
          class: "znpb-border-control-group"
        }, null, 8, ["modelValue", "schema"]);
      };
    }
  }));
  var InputBorderTabs_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$G = { class: "znpb-input-border-tabs-wrapper" };
  const __default__$v = {
    name: "InputBorderTabs"
  };
  const _sfc_main$W = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$v), {
    props: {
      modelValue: null,
      placeholder: null
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const positions = [
        {
          name: "all",
          icon: "all-sides",
          id: "all"
        },
        {
          name: "Top",
          icon: "border-top",
          id: "top"
        },
        {
          name: "right",
          icon: "border-right",
          id: "right"
        },
        {
          name: "bottom",
          icon: "border-bottom",
          id: "bottom"
        },
        {
          name: "left",
          icon: "border-left",
          id: "left"
        }
      ];
      const computedValue = vue.computed(() => props.modelValue || {});
      function onValueUpdated(position, newValue) {
        const clonedValue = cloneDeep(props.modelValue || {});
        if (newValue === null) {
          unset(clonedValue, position);
        } else {
          set(clonedValue, position, newValue);
        }
        if (Object.keys(clonedValue).length > 0) {
          emit("update:modelValue", clonedValue);
        } else {
          emit("update:modelValue", null);
        }
      }
      return (_ctx, _cache) => {
        const _component_Tab = vue.resolveComponent("Tab");
        const _component_Tabs = vue.resolveComponent("Tabs");
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$G, [
          vue.createVNode(_component_Tabs, {
            "tab-style": "group",
            class: "znpb-input-border-tabs"
          }, {
            default: vue.withCtx(() => [
              (vue.openBlock(), vue.createElementBlock(vue.Fragment, null, vue.renderList(positions, (tab) => {
                return vue.createVNode(_component_Tab, {
                  key: tab.id,
                  name: tab.name,
                  class: "znpb-input-border-tabs__tab"
                }, {
                  title: vue.withCtx(() => [
                    vue.createElementVNode("div", null, [
                      vue.createVNode(vue.unref(_sfc_main$1K), {
                        icon: tab.icon
                      }, null, 8, ["icon"])
                    ])
                  ]),
                  default: vue.withCtx(() => [
                    vue.createVNode(_sfc_main$X, {
                      modelValue: vue.unref(computedValue)[tab.id] || {},
                      placeholder: __props.placeholder ? __props.placeholder[tab.id] : null,
                      "onUpdate:modelValue": ($event) => onValueUpdated(tab.id, $event)
                    }, null, 8, ["modelValue", "placeholder", "onUpdate:modelValue"])
                  ]),
                  _: 2
                }, 1032, ["name"]);
              }), 64))
            ]),
            _: 1
          })
        ]);
      };
    }
  }));
  var InputBorderRadius_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$F = { class: "znpb-input-border-radius-wrapper" };
  const __default__$u = {
    name: "InputBorderRadius"
  };
  const _sfc_main$V = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$u), {
    props: {
      modelValue: { default: "" },
      title: { default: "" }
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const computedValue = vue.computed({
        get() {
          return props.modelValue;
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      return (_ctx, _cache) => {
        const _component_InputLabel = vue.resolveComponent("InputLabel");
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$F, [
          __props.title.length ? (vue.openBlock(), vue.createBlock(_component_InputLabel, {
            key: 0,
            label: __props.title,
            class: "znpb-typography-group-item znpb-typography-group-item-font-weight"
          }, {
            default: vue.withCtx(() => [
              vue.createVNode(vue.unref(_sfc_main$1B), {
                modelValue: vue.unref(computedValue),
                "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(computedValue) ? computedValue.value = $event : null),
                min: 0,
                max: 999,
                default_unit: "px",
                step: 1,
                "default-unit": "px"
              }, null, 8, ["modelValue"])
            ]),
            _: 1
          }, 8, ["label"])) : vue.createCommentVNode("", true)
        ]);
      };
    }
  }));
  var InputBorderRadiusTabs_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$U = {
    name: "InputBorderRadiusTabs",
    components: {
      InputBorderRadius: _sfc_main$V,
      Icon: _sfc_main$1K
    },
    props: {
      modelValue: {
        default() {
          return {};
        },
        type: Object,
        required: false
      }
    },
    data() {
      return {
        borderRadiusTabs: {
          all: {
            name: "all borders",
            icon: "all-corners",
            id: "all-borders-radius",
            description: "All borders"
          },
          topLeft: {
            name: "top left",
            icon: "t-l-corner",
            id: "border-top-left-radius",
            description: "Top Left Border"
          },
          topRight: {
            name: "top right",
            icon: "t-r-corner",
            id: "border-top-right-radius",
            description: "Top Right Border"
          },
          bottomRight: {
            name: "bottom right",
            icon: "b-r-corner",
            id: "border-bottom-right-radius",
            description: "Bottom Right Border"
          },
          bottomLeft: {
            name: "bottom left",
            icon: "t-l-corner",
            id: "border-bottom-left-radius",
            description: "Bottom Left Border"
          }
        }
      };
    },
    computed: {
      computedValue() {
        return this.modelValue || {};
      }
    },
    methods: {
      onValueUpdated(position, newValue) {
        this.$emit("update:modelValue", __spreadProps(__spreadValues({}, this.modelValue), {
          [position]: newValue
        }));
      }
    }
  };
  const _hoisted_1$E = { class: "znpb-input-border-radius-tabs-wrapper" };
  const _hoisted_2$s = /* @__PURE__ */ vue.createElementVNode("div", null, null, -1);
  function _sfc_render$o(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_InputBorderRadius = vue.resolveComponent("InputBorderRadius");
    const _component_Tab = vue.resolveComponent("Tab");
    const _component_Tabs = vue.resolveComponent("Tabs");
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$E, [
      vue.createVNode(_component_Tabs, {
        "tab-style": "group",
        class: "znpb-input-border-radius-tabs"
      }, {
        default: vue.withCtx(() => [
          (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($data.borderRadiusTabs, (tab, index2) => {
            return vue.openBlock(), vue.createBlock(_component_Tab, {
              key: index2,
              name: tab.name
            }, {
              title: vue.withCtx(() => [
                _hoisted_2$s
              ]),
              default: vue.withCtx(() => [
                vue.createVNode(_component_InputBorderRadius, {
                  title: tab.name,
                  modelValue: $options.computedValue[tab.id] || null,
                  "onUpdate:modelValue": ($event) => $options.onValueUpdated(tab.id, $event)
                }, null, 8, ["title", "modelValue", "onUpdate:modelValue"])
              ]),
              _: 2
            }, 1032, ["name"]);
          }), 128))
        ]),
        _: 1
      })
    ]);
  }
  var InputBorderRadiusTabs = /* @__PURE__ */ _export_sfc(_sfc_main$U, [["render", _sfc_render$o]]);
  var InputCheckbox_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$D = ["aria-disabled"];
  const _hoisted_2$r = ["disabled", "value"];
  const _hoisted_3$l = {
    key: 0,
    class: "znpb-checkmark-option"
  };
  const __default__$t = {
    name: "InputCheckbox"
  };
  const _sfc_main$T = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$t), {
    props: {
      label: null,
      showLabel: { type: Boolean, default: true },
      modelValue: { type: [Boolean, Array], default: true },
      optionValue: { type: [String, Boolean] },
      disabled: { type: Boolean },
      checked: { type: Boolean },
      rounded: { type: Boolean },
      placeholder: { type: [Boolean, Array], default: () => {
        return [];
      } }
    },
    emits: ["update:modelValue", "change"],
    setup(__props, { emit }) {
      const props = __props;
      const isLimitExceeded = vue.ref(false);
      const slots = vue.useSlots();
      const model = vue.computed({
        get() {
          return props.modelValue;
        },
        set(newValue) {
          var _a3, _b, _c, _d, _e;
          isLimitExceeded.value = false;
          const allowUnselect = (_a3 = parentGroup.value) == null ? void 0 : _a3.allowUnselect;
          if (Array.isArray(newValue)) {
            isLimitExceeded.value = false;
            if (((_b = parentGroup.value) == null ? void 0 : _b.min) !== void 0 && newValue.length < ((_c = parentGroup.value) == null ? void 0 : _c.min)) {
              isLimitExceeded.value = true;
            }
            if (((_d = parentGroup.value) == null ? void 0 : _d.max) && newValue.length > ((_e = parentGroup.value) == null ? void 0 : _e.max)) {
              isLimitExceeded.value = true;
            }
            if (isLimitExceeded.value === false) {
              emit("update:modelValue", newValue);
            } else if (allowUnselect && isLimitExceeded.value === true) {
              const clonedValues = [...newValue];
              clonedValues.shift();
              isLimitExceeded.value = false;
              emit("update:modelValue", clonedValues);
            }
          } else {
            emit("update:modelValue", newValue);
          }
        }
      });
      const instance2 = vue.getCurrentInstance();
      const parentGroup = vue.computed(() => {
        var _a3, _b;
        const isInGroup = ((_a3 = instance2 == null ? void 0 : instance2.parent) == null ? void 0 : _a3.type.name) === "InputCheckboxGroup";
        return isInGroup ? (_b = instance2 == null ? void 0 : instance2.parent) == null ? void 0 : _b.ctx : null;
      });
      const hasSlots = vue.computed(() => {
        if (!slots.default) {
          return false;
        }
        const defaultSlot = slots.default();
        const normalNodes = [];
        if (Array.isArray(defaultSlot)) {
          defaultSlot.forEach((vNode) => {
            if (vNode.type !== vue.Comment) {
              normalNodes.push(vNode);
            }
          });
        }
        return normalNodes.length > 0;
      });
      function onChange(event2) {
        const checkbox = event2.target;
        if (isLimitExceeded.value) {
          vue.nextTick(() => {
            checkbox.checked = !checkbox.checked;
          });
          return;
        }
        emit("change", !!checkbox.checked);
      }
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("label", {
          class: "znpb-checkbox-wrapper",
          "aria-disabled": __props.disabled
        }, [
          vue.withDirectives(vue.createElementVNode("input", {
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(model) ? model.value = $event : null),
            type: "checkbox",
            "aria-hidden": "true",
            disabled: __props.disabled,
            value: __props.optionValue,
            class: "znpb-form__input-checkbox",
            onChange
          }, null, 40, _hoisted_2$r), [
            [vue.vModelCheckbox, vue.unref(model)]
          ]),
          vue.createElementVNode("span", {
            class: vue.normalizeClass(["znpb-checkmark", { "znpb-checkmark--rounded": __props.rounded }])
          }, null, 2),
          vue.unref(hasSlots) || __props.label ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_3$l, [
            vue.renderSlot(_ctx.$slots, "default"),
            __props.showLabel && __props.label ? (vue.openBlock(), vue.createElementBlock(vue.Fragment, { key: 0 }, [
              vue.createTextVNode(vue.toDisplayString(__props.label), 1)
            ], 64)) : vue.createCommentVNode("", true)
          ])) : vue.createCommentVNode("", true)
        ], 8, _hoisted_1$D);
      };
    }
  }));
  var InputCheckboxGroup_vue_vue_type_style_index_0_lang = "";
  const __default__$s = {
    name: "InputCheckboxGroup"
  };
  const _sfc_main$S = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$s), {
    props: {
      modelValue: { default: () => {
        return [];
      } },
      min: null,
      max: null,
      allowUnselect: { type: Boolean },
      direction: { default: "vertical" },
      options: null,
      disabled: { type: Boolean },
      displayStyle: null,
      placeholder: { default: () => {
        return [];
      } }
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const slots = vue.useSlots();
      const model = vue.computed({
        get() {
          return props.modelValue ? props.modelValue : [];
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      const wrapperClasses = vue.computed(() => {
        return {
          [`znpb-checkbox-list--${props.direction}`]: props.direction,
          [`znpb-checkbox-list-style--${props.displayStyle}`]: props.displayStyle
        };
      });
      const hasSlots = vue.computed(() => {
        if (!slots.default) {
          return false;
        }
        const defaultSlot = slots.default();
        const normalNodes = [];
        if (Array.isArray(defaultSlot)) {
          defaultSlot.forEach((vNode) => {
            if (vNode.type !== vue.Comment) {
              normalNodes.push(vNode);
            }
          });
        }
        return normalNodes.length > 0;
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          class: vue.normalizeClass(["znpb-checkbox-list", vue.unref(wrapperClasses)])
        }, [
          vue.renderSlot(_ctx.$slots, "default"),
          !vue.unref(hasSlots) ? (vue.openBlock(true), vue.createElementBlock(vue.Fragment, { key: 0 }, vue.renderList(__props.options, (option, i) => {
            return vue.openBlock(), vue.createBlock(_sfc_main$T, {
              key: i,
              modelValue: vue.unref(model),
              "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(model) ? model.value = $event : null),
              "option-value": option.id,
              label: option.name,
              disabled: __props.disabled,
              placeholder: __props.placeholder,
              title: option.icon ? option.name : false,
              class: vue.normalizeClass({
                [`znpb-checkbox-list--isPlaceholder`]: vue.unref(model).length === 0 && __props.placeholder && __props.placeholder.includes(option.id)
              })
            }, {
              default: vue.withCtx(() => [
                option.icon ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
                  key: 0,
                  icon: option.icon
                }, null, 8, ["icon"])) : vue.createCommentVNode("", true)
              ]),
              _: 2
            }, 1032, ["modelValue", "option-value", "label", "disabled", "placeholder", "title", "class"]);
          }), 128)) : vue.createCommentVNode("", true)
        ], 2);
      };
    }
  }));
  var InputCheckboxSwitch_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$R = {
    name: "InputCheckboxSwitch",
    props: {
      label: {
        type: String,
        required: false
      },
      showLabel: {
        type: Boolean,
        required: false,
        default: true
      },
      modelValue: {
        type: [String, Array, Boolean],
        required: false
      },
      optionValue: {
        type: [String, Boolean],
        required: false
      },
      disabled: {
        type: Boolean,
        required: false
      },
      checked: {
        type: Boolean,
        required: false
      },
      rounded: {
        type: Boolean,
        required: false
      }
    },
    data() {
      return {
        isLimitExceeded: false
      };
    },
    computed: {
      model: {
        get() {
          return this.modelValue !== void 0 ? this.modelValue : false;
        },
        set(newValue) {
          this.isLimitExceeded = false;
          const allowUnselect = this.parentGroup.allowUnselect;
          if (this.isInGroup) {
            this.isLimitExceeded = false;
            if (this.parentGroup.min !== void 0 && newValue.length < this.parentGroup.min) {
              this.isLimitExceeded = true;
            }
            if (this.parentGroup.max !== void 0 && newValue.length > this.parentGroup.max) {
              this.isLimitExceeded = true;
            }
            if (this.isLimitExceeded === false) {
              this.$emit("update:modelValue", newValue);
            } else if (allowUnselect && this.isLimitExceeded === true) {
              const clonedValues = [...newValue];
              clonedValues.shift();
              this.isLimitExceeded = false;
              this.$emit("update:modelValue", clonedValues);
            }
          } else {
            this.$emit("update:modelValue", newValue);
          }
        }
      },
      isInGroup() {
        return this.$parent.$options.name === "InputCheckboxGroup";
      },
      parentGroup() {
        return this.isInGroup ? this.$parent : false;
      }
    },
    created() {
      this.checked && this.setInitialValue();
    },
    methods: {
      setInitialValue() {
        this.model = this.modelValue || true;
      },
      onChange(event2) {
        let checked = event2.target.checked;
        if (this.isLimitExceeded) {
          this.$nextTick(() => {
            event2.target.checked = !checked;
          });
          return;
        }
        this.$emit("change", !!event2.target.checked);
      }
    }
  };
  const _hoisted_1$C = { class: "znpb-checkbox-switch-wrapper" };
  const _hoisted_2$q = ["content"];
  const _hoisted_3$k = ["disabled", "modelValue"];
  const _hoisted_4$d = /* @__PURE__ */ vue.createElementVNode("span", { class: "znpb-checkbox-switch-wrapper__button" }, null, -1);
  function _sfc_render$n(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$C, [
      vue.createElementVNode("label", {
        class: vue.normalizeClass(["znpb-checkbox-switch-wrapper__label", { [`znpb-checkbox-switch--${$options.model ? "checked" : "unchecked"}`]: true }]),
        content: $options.model ? _ctx.$translate("yes") : _ctx.$translate("no")
      }, [
        vue.withDirectives(vue.createElementVNode("input", {
          type: "checkbox",
          disabled: $props.disabled,
          class: "znpb-checkbox-switch-wrapper__checkbox",
          modelValue: $props.optionValue,
          "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $options.model = $event)
        }, null, 8, _hoisted_3$k), [
          [vue.vModelCheckbox, $options.model]
        ]),
        _hoisted_4$d
      ], 10, _hoisted_2$q)
    ]);
  }
  var InputCheckboxSwitch = /* @__PURE__ */ _export_sfc(_sfc_main$R, [["render", _sfc_render$n]]);
  var InputCode_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$B = { class: "znpb-custom-code" };
  const _hoisted_2$p = ["placeholder"];
  const __default__$r = {
    name: "InputCode"
  };
  const _sfc_main$Q = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$r), {
    props: {
      placeholder: null,
      mode: null,
      modelValue: { default: "" }
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      let editor;
      const codeMirrorTextarea = vue.ref(null);
      let ignoreChange = false;
      vue.watch(
        () => props.modelValue,
        (newValue) => {
          if ((editor == null ? void 0 : editor.getValue()) !== newValue) {
            ignoreChange = true;
            editor.setValue(newValue);
          }
        }
      );
      function onEditorChange(instance2) {
        if (!ignoreChange) {
          emit("update:modelValue", instance2.getValue());
        }
        ignoreChange = false;
      }
      const lint = [
        "text/css",
        "text/x-scss",
        "text/x-less",
        "text/javascript",
        "application/json",
        "application/ld+json",
        "text/typescript",
        "application/typescript",
        "htmlmixed"
      ].includes(props.mode || "");
      vue.onMounted(() => {
        editor = window.wp.CodeMirror.fromTextArea(codeMirrorTextarea.value, {
          mode: props.mode,
          lineNumbers: true,
          lineWrapping: true,
          lint,
          autoCloseBrackets: true,
          matchBrackets: true,
          autoRefresh: true,
          autoCloseTags: true,
          matchTags: {
            bothTags: true
          },
          csslint: {
            errors: true,
            "box-model": true,
            "display-property-grouping": true,
            "duplicate-properties": true,
            "known-properties": true,
            "outline-none": true
          },
          jshint: {
            boss: true,
            curly: true,
            eqeqeq: true,
            eqnull: true,
            es3: true,
            expr: true,
            immed: true,
            noarg: true,
            nonbsp: true,
            onevar: true,
            quotmark: "single",
            trailing: true,
            undef: true,
            unused: true,
            browser: true,
            globals: {
              _: false,
              Backbone: false,
              jQuery: false,
              JSON: false,
              wp: false
            }
          },
          htmlhint: {
            "tagname-lowercase": true,
            "attr-lowercase": true,
            "attr-value-double-quotes": false,
            "doctype-first": false,
            "tag-pair": true,
            "spec-char-escape": true,
            "id-unique": true,
            "src-not-empty": true,
            "attr-no-duplication": true,
            "alt-require": true,
            "space-tab-mixed-disabled": "tab",
            "attr-unsafe-chars": true
          },
          gutters: ["CodeMirror-lint-markers"]
        });
        if (props.modelValue) {
          editor.setValue(props.modelValue);
        }
        editor.on("change", onEditorChange);
      });
      vue.onBeforeUnmount(() => {
        if (editor) {
          editor.toTextArea();
        }
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$B, [
          vue.createElementVNode("textarea", {
            ref_key: "codeMirrorTextarea",
            ref: codeMirrorTextarea,
            class: "znpb-custom-code__text-area",
            placeholder: __props.placeholder
          }, null, 8, _hoisted_2$p)
        ]);
      };
    }
  }));
  var GridColor_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$A = { class: "znpb-form-library-grid__panel-content znpb-fancy-scrollbar" };
  const _sfc_main$P = /* @__PURE__ */ vue.defineComponent({
    __name: "GridColor",
    emits: ["add-new-color"],
    setup(__props) {
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$A, [
          vue.createVNode(vue.unref(_sfc_main$1K), {
            icon: "plus",
            class: "znpb-colorpicker-circle znpb-colorpicker-add-color",
            onMousedown: _cache[0] || (_cache[0] = vue.withModifiers(($event) => _ctx.$emit("add-new-color"), ["stop"]))
          }),
          vue.renderSlot(_ctx.$slots, "default")
        ]);
      };
    }
  });
  var PatternContainer_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$z = { key: 0 };
  const _hoisted_2$o = ["onClick"];
  const _hoisted_3$j = {
    key: 0,
    class: "znpb-colorpicker-global-wrapper--pro"
  };
  const _hoisted_4$c = /* @__PURE__ */ vue.createTextVNode(" Global colors are available in ");
  const _hoisted_5$7 = ["onClick"];
  const _hoisted_6$6 = {
    key: 0,
    class: "znpb-colorpicker-circle__active-bg"
  };
  const __default__$q = {
    name: "PatternContainer"
  };
  const _sfc_main$O = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$q), {
    props: {
      model: { default: "#000" }
    },
    emits: ["color-updated"],
    setup(__props) {
      const props = __props;
      const formApi = vue.inject("OptionsForm");
      const getValueByPath = vue.inject("getValueByPath");
      const schema = vue.inject("schema", {});
      const { addLocalColor, getOptionValue, addGlobalColor } = useBuilderOptionsStore();
      const localColors = getOptionValue("local_colors", []);
      const globalColors = getOptionValue("global_colors", []);
      const showPresetInput = vue.ref(false);
      const isPro = vue.computed(() => {
        if (window.ZnPbComponentsData !== void 0) {
          return window.ZnPbComponentsData.is_pro_active;
        }
        return false;
      });
      const localColorPatterns = vue.computed(() => {
        return [...localColors].reverse();
      });
      const globalColorPatterns = vue.computed(() => {
        return [...globalColors].reverse();
      });
      const selectedGlobalColor = vue.computed(() => {
        const { id = "" } = schema;
        const { options: options2 = {} } = getValueByPath(`__dynamic_content__.${id}`, {});
        return options2.color_id;
      });
      const activeTab = vue.computed(() => {
        return selectedGlobalColor.value ? "global" : "local";
      });
      function addGlobal(name) {
        let globalColor = {
          id: name.split(" ").join("_"),
          color: props.model,
          name
        };
        showPresetInput.value = false;
        addGlobalColor(globalColor);
      }
      function onGlobalColorSelected(colorConfig) {
        const { id } = schema;
        formApi.updateValueByPath(`__dynamic_content__.${id}`, {
          type: "global-color",
          options: {
            color_id: colorConfig.id
          }
        });
      }
      return (_ctx, _cache) => {
        const _component_Tab = vue.resolveComponent("Tab");
        const _component_Tabs = vue.resolveComponent("Tabs");
        return vue.openBlock(), vue.createBlock(_sfc_main$18, { "has-input": showPresetInput.value }, {
          default: vue.withCtx(() => [
            !showPresetInput.value ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_1$z, [
              vue.createVNode(_component_Tabs, {
                "tab-style": "minimal",
                "active-tab": vue.unref(activeTab)
              }, {
                default: vue.withCtx(() => [
                  vue.createVNode(_component_Tab, { name: "Local" }, {
                    default: vue.withCtx(() => [
                      vue.createVNode(_sfc_main$P, {
                        onAddNewColor: _cache[0] || (_cache[0] = ($event) => vue.unref(addLocalColor)(__props.model))
                      }, {
                        default: vue.withCtx(() => [
                          (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(localColorPatterns), (color, i) => {
                            return vue.openBlock(), vue.createElementBlock("span", {
                              key: i,
                              class: "znpb-colorpicker-circle znpb-colorpicker-circle-color",
                              style: vue.normalizeStyle({ "background-color": color }),
                              onClick: ($event) => _ctx.$emit("color-updated", color)
                            }, null, 12, _hoisted_2$o);
                          }), 128))
                        ]),
                        _: 1
                      })
                    ]),
                    _: 1
                  }),
                  vue.createVNode(_component_Tab, { name: "Global" }, {
                    default: vue.withCtx(() => [
                      !vue.unref(isPro) ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_3$j, [
                        _hoisted_4$c,
                        vue.createVNode(vue.unref(_sfc_main$17), {
                          text: "PRO",
                          type: "pro"
                        })
                      ])) : (vue.openBlock(), vue.createBlock(_sfc_main$P, {
                        key: 1,
                        onAddNewColor: _cache[1] || (_cache[1] = ($event) => showPresetInput.value = !showPresetInput.value)
                      }, {
                        default: vue.withCtx(() => [
                          (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(globalColorPatterns), (colorConfig, i) => {
                            return vue.openBlock(), vue.createElementBlock("span", {
                              key: i,
                              class: vue.normalizeClass(["znpb-colorpicker-circle znpb-colorpicker-circle-color", { "znpb-colorpicker-circle--active": colorConfig.id === vue.unref(selectedGlobalColor) }]),
                              style: vue.normalizeStyle({ backgroundColor: colorConfig.id === vue.unref(selectedGlobalColor) ? "" : colorConfig.color }),
                              onClick: vue.withModifiers(($event) => onGlobalColorSelected(colorConfig), ["stop"])
                            }, [
                              colorConfig.id === vue.unref(selectedGlobalColor) ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_6$6, [
                                vue.createElementVNode("span", {
                                  style: vue.normalizeStyle({ "background-color": colorConfig.color })
                                }, null, 4)
                              ])) : vue.createCommentVNode("", true)
                            ], 14, _hoisted_5$7);
                          }), 128))
                        ]),
                        _: 1
                      }))
                    ]),
                    _: 1
                  })
                ]),
                _: 1
              }, 8, ["active-tab"])
            ])) : vue.createCommentVNode("", true),
            showPresetInput.value ? (vue.openBlock(), vue.createBlock(_sfc_main$1b, {
              key: 1,
              "is-gradient": false,
              onSavePreset: _cache[2] || (_cache[2] = ($event) => addGlobal($event)),
              onCancel: _cache[3] || (_cache[3] = ($event) => showPresetInput.value = false)
            })) : vue.createCommentVNode("", true)
          ]),
          _: 1
        }, 8, ["has-input"]);
      };
    }
  }));
  var Color_vue_vue_type_style_index_0_lang = "";
  const __default__$p = {
    name: "Color"
  };
  const _sfc_main$N = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$p), {
    props: {
      modelValue: { default: "" },
      showLibrary: { type: Boolean, default: true },
      dynamicContentConfig: null,
      placeholder: null
    },
    emits: ["update:modelValue", "option-updated", "open", "close"],
    setup(__props, { emit }) {
      const popper2 = vue.ref(null);
      const colorpickerHolder = vue.ref(null);
      const isDragging = vue.ref(false);
      let backdrop;
      function onLibraryUpdate(newValue) {
        emit("update:modelValue", newValue);
      }
      function onColorPickerClick() {
        isDragging.value = false;
      }
      function onColorPickerMousedown() {
        isDragging.value = true;
      }
      function updateColor(color) {
        emit("option-updated", color);
        emit("update:modelValue", color);
      }
      function openColorPicker() {
        emit("open");
        document.addEventListener("click", closePanelOnOutsideClick, true);
        if (popper2.value) {
          backdrop = document.createElement("div");
          backdrop.classList.add("znpb-tooltip-backdrop");
          const parent2 = popper2.value.$el.parentNode;
          parent2.insertBefore(backdrop, popper2.value.$el);
        }
      }
      function closeColorPicker() {
        var _a3;
        emit("close");
        document.removeEventListener("click", closePanelOnOutsideClick);
        if (backdrop) {
          document.body.appendChild(backdrop);
          (_a3 = backdrop.parentNode) == null ? void 0 : _a3.removeChild(backdrop);
        }
      }
      function closePanelOnOutsideClick(event2) {
        var _a3, _b;
        if (((_a3 = popper2.value) == null ? void 0 : _a3.$el.contains(event2.target)) || ((_b = colorpickerHolder.value) == null ? void 0 : _b.$refs.colorPicker.contains(event2.target))) {
          return;
        }
        if (!isDragging.value && popper2.value) {
          popper2.value.hidePopper();
        }
        isDragging.value = false;
      }
      vue.onBeforeUnmount(() => {
        var _a3;
        document.removeEventListener("click", closePanelOnOutsideClick);
        if (backdrop) {
          (_a3 = backdrop.parentNode) == null ? void 0 : _a3.removeChild(backdrop);
        }
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createBlock(vue.unref(Tooltip), {
          ref_key: "popper",
          ref: popper2,
          "tooltip-class": "hg-popper--no-padding",
          trigger: "click",
          "close-on-outside-click": true,
          "append-to": "body",
          modifiers: [
            {
              name: "preventOverflow",
              options: {
                rootBoundary: "viewport"
              }
            },
            {
              name: "offset",
              options: {
                offset: [0, 15]
              }
            },
            {
              name: "flip",
              options: {
                fallbackPlacements: ["left", "right", "bottom", "top"]
              }
            }
          ],
          strategy: "fixed",
          onShow: openColorPicker,
          onHide: closeColorPicker
        }, {
          content: vue.withCtx(() => [
            vue.createVNode(vue.unref(_sfc_main$1u), {
              ref_key: "colorpickerHolder",
              ref: colorpickerHolder,
              model: __props.modelValue && __props.modelValue.length > 0 ? __props.modelValue : __props.placeholder,
              onColorChanged: updateColor,
              onClick: vue.withModifiers(onColorPickerClick, ["stop"]),
              onMousedown: vue.withModifiers(onColorPickerMousedown, ["stop"])
            }, {
              end: vue.withCtx(() => [
                __props.showLibrary ? (vue.openBlock(), vue.createBlock(_sfc_main$O, {
                  key: 0,
                  model: __props.modelValue,
                  "active-tab": __props.dynamicContentConfig ? "global" : "local",
                  onColorUpdated: onLibraryUpdate
                }, null, 8, ["model", "active-tab"])) : vue.createCommentVNode("", true)
              ]),
              _: 1
            }, 8, ["model", "onClick", "onMousedown"])
          ]),
          default: vue.withCtx(() => [
            vue.renderSlot(_ctx.$slots, "trigger")
          ]),
          _: 3
        }, 512);
      };
    }
  }));
  const createI18n = (initialStrings = {}) => {
    let strings = {};
    const addStrings2 = (newStrings) => {
      strings = __spreadValues(__spreadValues({}, strings), newStrings);
    };
    const translate2 = (stringId) => {
      if (strings[stringId] !== void 0) {
        return strings[stringId];
      }
      console.error(`String with id ${stringId} was not found.`);
      return "";
    };
    if (initialStrings) {
      addStrings2(initialStrings);
    }
    return {
      addStrings: addStrings2,
      translate: translate2
    };
  };
  const i18n = createI18n();
  const install$1 = (app, strings) => {
    i18n.addStrings(strings);
    app.config.globalProperties.$translate = (string) => {
      return i18n.translate(string);
    };
  };
  const { addStrings, translate } = i18n;
  window.zb = window.zb || {};
  window.zb.i18n = {
    install: install$1,
    addStrings,
    translate
  };
  var InputColorPicker_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$y = { class: "znpb-form-colorpicker" };
  const _hoisted_2$n = {
    key: 1,
    class: "znpb-form-colorpicker__color-trigger znpb-colorpicker-circle znpb-colorpicker-circle--no-color"
  };
  const __default__$o = {
    name: "InputColorPicker",
    inheritAttrs: true
  };
  const _sfc_main$M = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$o), {
    props: {
      modelValue: null,
      type: null,
      dynamicContentConfig: null,
      showLibrary: { type: Boolean, default: true },
      placeholder: { default: null }
    },
    emits: ["update:modelValue", "open", "close"],
    setup(__props, { emit }) {
      const props = __props;
      const color = vue.ref(null);
      const computedPlaceholder = vue.computed(() => {
        return props.placeholder || translate("color");
      });
      const colorModel = vue.computed({
        get() {
          return props.modelValue || "";
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      return (_ctx, _cache) => {
        const _directive_znpb_tooltip = vue.resolveDirective("znpb-tooltip");
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$y, [
          __props.type === "simple" ? (vue.openBlock(), vue.createBlock(_sfc_main$N, {
            key: 0,
            ref_key: "color",
            ref: color,
            modelValue: vue.unref(colorModel),
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(colorModel) ? colorModel.value = $event : null),
            "show-library": __props.showLibrary,
            placeholder: __props.placeholder,
            class: "znpb-colorpicker-circle znpb-colorpicker-circle--trigger znpb-colorpicker-circle--opacity",
            onOpen: _cache[1] || (_cache[1] = ($event) => _ctx.$emit("open")),
            onClose: _cache[2] || (_cache[2] = ($event) => _ctx.$emit("close"))
          }, {
            trigger: vue.withCtx(() => [
              vue.createElementVNode("span", {
                style: vue.normalizeStyle({ backgroundColor: __props.modelValue }),
                class: "znpb-form-colorpicker__color-trigger znpb-colorpicker-circle"
              }, null, 4),
              __props.dynamicContentConfig ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
                key: 0,
                icon: "globe",
                rounded: true,
                "bg-color": "#fff",
                "bg-size": 16,
                size: 12,
                class: "znpb-colorpicker-circle__global-icon"
              })) : vue.createCommentVNode("", true),
              !__props.modelValue ? vue.withDirectives((vue.openBlock(), vue.createElementBlock("span", _hoisted_2$n, null, 512)), [
                [_directive_znpb_tooltip, _ctx.$translate("no_color_chosen")]
              ]) : vue.createCommentVNode("", true)
            ]),
            _: 1
          }, 8, ["modelValue", "show-library", "placeholder"])) : (vue.openBlock(), vue.createBlock(_sfc_main$1D, {
            key: 1,
            modelValue: vue.unref(colorModel),
            "onUpdate:modelValue": _cache[6] || (_cache[6] = ($event) => vue.isRef(colorModel) ? colorModel.value = $event : null),
            placeholder: vue.unref(computedPlaceholder)
          }, {
            prepend: vue.withCtx(() => [
              vue.createVNode(_sfc_main$N, {
                modelValue: vue.unref(colorModel),
                "onUpdate:modelValue": _cache[3] || (_cache[3] = ($event) => vue.isRef(colorModel) ? colorModel.value = $event : null),
                "show-library": __props.showLibrary,
                class: "znpb-colorpicker-circle znpb-colorpicker-circle--trigger znpb-colorpicker-circle--opacity",
                placeholder: __props.placeholder,
                onOpen: _cache[4] || (_cache[4] = ($event) => _ctx.$emit("open")),
                onClose: _cache[5] || (_cache[5] = ($event) => _ctx.$emit("close"))
              }, {
                trigger: vue.withCtx(() => [
                  vue.createElementVNode("span", null, [
                    !__props.modelValue || __props.modelValue === void 0 ? (vue.openBlock(), vue.createBlock(vue.unref(Tooltip), {
                      key: 0,
                      content: _ctx.$translate("no_color_chosen"),
                      tag: "span"
                    }, {
                      default: vue.withCtx(() => [
                        vue.createElementVNode("span", {
                          style: vue.normalizeStyle({ backgroundColor: __props.modelValue || __props.placeholder }),
                          class: "znpb-form-colorpicker__color-trigger znpb-colorpicker-circle"
                        }, null, 4)
                      ]),
                      _: 1
                    }, 8, ["content"])) : (vue.openBlock(), vue.createElementBlock("span", {
                      key: 1,
                      style: vue.normalizeStyle({ backgroundColor: __props.modelValue || __props.placeholder }),
                      class: "znpb-form-colorpicker__color-trigger znpb-colorpicker-circle"
                    }, null, 4)),
                    __props.dynamicContentConfig ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
                      key: 2,
                      icon: "globe",
                      rounded: true,
                      "bg-color": "#fff",
                      "bg-size": 16,
                      size: 12,
                      class: "znpb-colorpicker-circle__global-icon"
                    })) : vue.createCommentVNode("", true)
                  ])
                ]),
                _: 1
              }, 8, ["modelValue", "show-library", "placeholder"])
            ]),
            _: 1
          }, 8, ["modelValue", "placeholder"]))
        ]);
      };
    }
  }));
  var InputCustomSelector_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$x = { class: "znpb-custom-selector" };
  const _hoisted_2$m = { class: "znpb-custom-selector__list-wrapper" };
  const _hoisted_3$i = ["title", "onClick"];
  const _hoisted_4$b = {
    key: 0,
    class: "znpb-custom-selector__item-name"
  };
  const _hoisted_5$6 = {
    key: 2,
    class: "znpb-custom-selector__icon-text-content"
  };
  const _hoisted_6$5 = {
    key: 1,
    class: "znpb-custom-selector__item-name"
  };
  const __default__$n = {
    name: "InputCustomSelector"
  };
  const _sfc_main$L = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$n), {
    props: {
      options: null,
      columns: null,
      modelValue: { type: [String, Number, Boolean, null] },
      textIcon: { type: Boolean },
      placeholder: { type: [String, Number, Boolean, null] }
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      function changeValue(newValue) {
        let valueToSend = newValue;
        if (props.modelValue === newValue) {
          valueToSend = null;
        }
        emit("update:modelValue", valueToSend);
      }
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$x, [
          vue.createElementVNode("ul", _hoisted_2$m, [
            (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(__props.options, (option, index2) => {
              return vue.openBlock(), vue.createElementBlock("li", {
                key: index2,
                class: vue.normalizeClass(["znpb-custom-selector__item", {
                  ["znpb-custom-selector__item--activePlaceholder"]: !__props.modelValue && __props.placeholder === option.id,
                  ["znpb-custom-selector__item--active"]: __props.modelValue === option.id,
                  [`znpb-custom-selector__columns-${__props.columns}`]: __props.columns
                }]),
                title: option.icon ? option.name : "",
                onClick: ($event) => changeValue(option.id)
              }, [
                !option.icon ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_4$b, vue.toDisplayString(option.name), 1)) : vue.createCommentVNode("", true),
                !__props.textIcon && option.icon ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
                  key: 1,
                  icon: option.icon
                }, null, 8, ["icon"])) : vue.createCommentVNode("", true),
                __props.textIcon ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_5$6, [
                  option.icon ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
                    key: 0,
                    icon: option.icon
                  }, null, 8, ["icon"])) : vue.createCommentVNode("", true),
                  option.name ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_6$5, vue.toDisplayString(option.name), 1)) : vue.createCommentVNode("", true)
                ])) : vue.createCommentVNode("", true)
              ], 10, _hoisted_3$i);
            }), 128))
          ])
        ]);
      };
    }
  }));
  var vueDatePick_vue_vue_type_style_index_0_lang = "";
  const formatRE = /,|\.|-| |:|\/|\\/;
  const dayRE = /D+/;
  const monthRE = /M+/;
  const yearRE = /Y+/;
  const hoursRE = /h+/i;
  const minutesRE = /m+/;
  const secondsRE = /s+/;
  const AMPMClockRE = /A/;
  const _sfc_main$K = {
    props: {
      modelValue: {
        type: String,
        default: ""
      },
      format: {
        type: String,
        default: "YYYY-MM-DD"
      },
      displayFormat: {
        type: String
      },
      editable: {
        type: Boolean,
        default: true
      },
      hasInputElement: {
        type: Boolean,
        default: true
      },
      inputAttributes: {
        type: Object
      },
      selectableYearRange: {
        type: [Number, Object, Function],
        default: 40
      },
      startPeriod: {
        type: Object
      },
      parseDate: {
        type: Function
      },
      formatDate: {
        type: Function
      },
      pickTime: {
        type: Boolean,
        default: false
      },
      pickMinutes: {
        type: Boolean,
        default: true
      },
      pickSeconds: {
        type: Boolean,
        default: false
      },
      use12HourClock: {
        type: Boolean,
        default: false
      },
      isDateDisabled: {
        type: Function,
        default: () => false
      },
      nextMonthCaption: {
        type: String,
        default: "Next month"
      },
      prevMonthCaption: {
        type: String,
        default: "Previous month"
      },
      setTimeCaption: {
        type: String,
        default: "Set time:"
      },
      mobileBreakpointWidth: {
        type: Number,
        default: 500
      },
      weekdays: {
        type: Array,
        default: () => [
          "Mon",
          "Tue",
          "Wed",
          "Thu",
          "Fri",
          "Sat",
          "Sun"
        ]
      },
      months: {
        type: Array,
        default: () => [
          "January",
          "February",
          "March",
          "April",
          "May",
          "June",
          "July",
          "August",
          "September",
          "October",
          "November",
          "December"
        ]
      },
      startWeekOnSunday: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        inputValue: this.valueToInputFormat(this.modelValue),
        direction: void 0,
        positionClass: void 0,
        opened: !this.hasInputElement,
        currentPeriod: this.startPeriod || this.getPeriodFromValue(
          this.modelValue,
          this.format
        )
      };
    },
    computed: {
      valueDate() {
        const value = this.modelValue;
        const format = this.format;
        return value ? this.parseDateString(value, format) : void 0;
      },
      isReadOnly() {
        return !this.editable || this.inputAttributes && this.inputAttributes.readonly;
      },
      isValidValue() {
        const valueDate = this.valueDate;
        return this.modelValue ? Boolean(valueDate) : true;
      },
      currentPeriodDates() {
        const { year, month } = this.currentPeriod;
        const days = [];
        const date = new Date(year, month, 1);
        const today = new Date();
        const offset2 = this.startWeekOnSunday ? 1 : 0;
        const startDay = date.getDay() || 7;
        if (startDay > 1 - offset2) {
          for (let i = startDay - (2 - offset2); i >= 0; i--) {
            const prevDate = new Date(date);
            prevDate.setDate(-i);
            days.push({ outOfRange: true, date: prevDate });
          }
        }
        while (date.getMonth() === month) {
          days.push({ date: new Date(date) });
          date.setDate(date.getDate() + 1);
        }
        const daysLeft = 7 - days.length % 7;
        for (let i = 1; i <= daysLeft; i++) {
          const nextDate = new Date(date);
          nextDate.setDate(i);
          days.push({ outOfRange: true, date: nextDate });
        }
        days.forEach((day) => {
          day.disabled = this.isDateDisabled(day.date);
          day.today = areSameDates(day.date, today);
          day.dateKey = [
            day.date.getFullYear(),
            day.date.getMonth() + 1,
            day.date.getDate()
          ].join("-");
          day.selected = this.valueDate ? areSameDates(day.date, this.valueDate) : false;
        });
        return chunkArray(days, 7);
      },
      yearRange() {
        const currentYear = this.currentPeriod.year;
        const userRange = this.selectableYearRange;
        const userRangeType = typeof userRange;
        let yearsRange = [];
        if (userRangeType === "number") {
          yearsRange = range(
            currentYear - userRange,
            currentYear + userRange
          );
        } else if (userRangeType === "object") {
          yearsRange = range(
            userRange.from,
            userRange.to
          );
        } else if (userRangeType === "function") {
          yearsRange = userRange(this);
        }
        if (yearsRange.indexOf(currentYear) < 0) {
          yearsRange.push(currentYear);
          yearsRange = yearsRange.sort();
        }
        return yearsRange;
      },
      currentTime() {
        const currentDate = this.valueDate;
        if (!currentDate) {
          return void 0;
        }
        const hours = currentDate.getHours();
        const minutes = currentDate.getMinutes();
        const seconds = currentDate.getSeconds();
        return {
          hours,
          minutes,
          seconds,
          isPM: isPM(hours),
          hoursFormatted: (this.use12HourClock ? to12HourClock(hours) : hours).toString(),
          minutesFormatted: paddNum(minutes, 2),
          secondsFormatted: paddNum(seconds, 2)
        };
      },
      directionClass() {
        return this.direction ? `vdp${this.direction}Direction` : void 0;
      },
      weekdaysSorted() {
        if (this.startWeekOnSunday) {
          const weekdays = this.weekdays.slice();
          weekdays.unshift(weekdays.pop());
          return weekdays;
        } else {
          return this.weekdays;
        }
      }
    },
    watch: {
      modelValue(value) {
        if (this.isValidValue) {
          this.inputValue = this.valueToInputFormat(value);
          this.currentPeriod = this.getPeriodFromValue(value, this.format);
        }
      },
      currentPeriod(currentPeriod, oldPeriod) {
        const currentDate = new Date(currentPeriod.year, currentPeriod.month).getTime();
        const oldDate = new Date(oldPeriod.year, oldPeriod.month).getTime();
        this.direction = currentDate !== oldDate ? currentDate > oldDate ? "Next" : "Prev" : void 0;
        if (currentDate !== oldDate) {
          this.$emit("periodChange", {
            year: currentPeriod.year,
            month: currentPeriod.month
          });
        }
      }
    },
    beforeUnmount() {
      this.removeCloseEvents();
      this.teardownPosition();
    },
    methods: {
      valueToInputFormat(value) {
        return !this.displayFormat ? value : this.formatDateToString(
          this.parseDateString(value, this.format),
          this.displayFormat
        ) || value;
      },
      getPeriodFromValue(dateString, format) {
        const date = this.parseDateString(dateString, format) || new Date();
        return { month: date.getMonth(), year: date.getFullYear() };
      },
      parseDateString(dateString, dateFormat) {
        return !dateString ? void 0 : this.parseDate ? this.parseDate(dateString, dateFormat) : this.parseSimpleDateString(dateString, dateFormat);
      },
      formatDateToString(date, dateFormat) {
        return !date ? "" : this.formatDate ? this.formatDate(date, dateFormat) : this.formatSimpleDateToString(date, dateFormat);
      },
      parseSimpleDateString(dateString, dateFormat) {
        let day, month, year, hours, minutes, seconds;
        const dateParts = dateString.split(formatRE);
        const formatParts = dateFormat.split(formatRE);
        const partsSize = formatParts.length;
        for (let i = 0; i < partsSize; i++) {
          if (formatParts[i].match(dayRE)) {
            day = parseInt(dateParts[i], 10);
          } else if (formatParts[i].match(monthRE)) {
            month = parseInt(dateParts[i], 10);
          } else if (formatParts[i].match(yearRE)) {
            year = parseInt(dateParts[i], 10);
          } else if (formatParts[i].match(hoursRE)) {
            hours = parseInt(dateParts[i], 10);
          } else if (formatParts[i].match(minutesRE)) {
            minutes = parseInt(dateParts[i], 10);
          } else if (formatParts[i].match(secondsRE)) {
            seconds = parseInt(dateParts[i], 10);
          }
        }
        const resolvedDate = new Date(
          [paddNum(year, 4), paddNum(month, 2), paddNum(day, 2)].join("-")
        );
        if (isNaN(resolvedDate)) {
          return void 0;
        } else {
          const date = new Date(year, month - 1, day);
          [
            [year, "setFullYear"],
            [hours, "setHours"],
            [minutes, "setMinutes"],
            [seconds, "setSeconds"]
          ].forEach(([value, method]) => {
            typeof value !== "undefined" && date[method](value);
          });
          return date;
        }
      },
      formatSimpleDateToString(date, dateFormat) {
        return dateFormat.replace(yearRE, (match) => Number(date.getFullYear().toString().slice(-match.length))).replace(monthRE, (match) => paddNum(date.getMonth() + 1, match.length)).replace(dayRE, (match) => paddNum(date.getDate(), match.length)).replace(hoursRE, (match) => paddNum(
          AMPMClockRE.test(dateFormat) ? to12HourClock(date.getHours()) : date.getHours(),
          match.length
        )).replace(minutesRE, (match) => paddNum(date.getMinutes(), match.length)).replace(secondsRE, (match) => paddNum(date.getSeconds(), match.length)).replace(AMPMClockRE, (match) => isPM(date.getHours()) ? "PM" : "AM");
      },
      incrementMonth(increment = 1) {
        const refDate = new Date(this.currentPeriod.year, this.currentPeriod.month);
        const incrementDate = new Date(refDate.getFullYear(), refDate.getMonth() + increment);
        this.currentPeriod = {
          month: incrementDate.getMonth(),
          year: incrementDate.getFullYear()
        };
      },
      processUserInput(userText) {
        const userDate = this.parseDateString(
          userText,
          this.displayFormat || this.format
        );
        this.inputValue = userText;
        this.$emit(
          "update:modelValue",
          userDate ? this.formatDateToString(userDate, this.format) : userText
        );
      },
      toggle() {
        return this.opened ? this.close() : this.open();
      },
      open() {
        if (!this.opened) {
          this.opened = true;
          this.currentPeriod = this.startPeriod || this.getPeriodFromValue(
            this.modelValue,
            this.format
          );
          this.addCloseEvents();
          this.setupPosition();
        }
        this.direction = void 0;
      },
      close() {
        if (this.opened) {
          this.opened = false;
          this.direction = void 0;
          this.removeCloseEvents();
          this.teardownPosition();
        }
      },
      closeViaOverlay(e) {
        if (this.hasInputElement && e.target === this.$refs.outerWrap) {
          this.close();
        }
      },
      addCloseEvents() {
        if (!this.closeEventListener) {
          this.closeEventListener = (e) => this.inspectCloseEvent(e);
          ["click", "keyup", "focusin"].forEach(
            (eventName) => document.addEventListener(eventName, this.closeEventListener)
          );
        }
      },
      inspectCloseEvent(event2) {
        if (event2.keyCode) {
          event2.keyCode === 27 && this.close();
        } else if (!(event2.target === this.$el) && !this.$el.contains(event2.target)) {
          this.close();
        }
      },
      removeCloseEvents() {
        if (this.closeEventListener) {
          ["click", "keyup", "focusin"].forEach(
            (eventName) => document.removeEventListener(eventName, this.closeEventListener)
          );
          delete this.closeEventListener;
        }
      },
      setupPosition() {
        if (!this.positionEventListener) {
          this.positionEventListener = () => this.positionFloater();
          window.addEventListener("resize", this.positionEventListener);
        }
        this.positionFloater();
      },
      positionFloater() {
        const inputRect = this.$el.getBoundingClientRect();
        let verticalClass = "vdpPositionTop";
        let horizontalClass = "vdpPositionLeft";
        const calculate = () => {
          const rect = this.$refs.outerWrap.getBoundingClientRect();
          const floaterHeight = rect.height;
          const floaterWidth = rect.width;
          if (window.innerWidth > this.mobileBreakpointWidth) {
            if (inputRect.top + inputRect.height + floaterHeight > window.innerHeight && inputRect.top - floaterHeight > 0) {
              verticalClass = "vdpPositionBottom";
            }
            if (inputRect.left + floaterWidth > window.innerWidth) {
              horizontalClass = "vdpPositionRight";
            }
            this.positionClass = ["vdpPositionReady", verticalClass, horizontalClass].join(" ");
          } else {
            this.positionClass = "vdpPositionFixed";
          }
        };
        this.$refs.outerWrap ? calculate() : this.$nextTick(calculate);
      },
      teardownPosition() {
        if (this.positionEventListener) {
          this.positionClass = void 0;
          window.removeEventListener("resize", this.positionEventListener);
          delete this.positionEventListener;
        }
      },
      clear() {
        this.$emit("update:modelValue", "");
      },
      selectDateItem(item) {
        if (!item.disabled) {
          const newDate = new Date(item.date);
          if (this.currentTime) {
            newDate.setHours(this.currentTime.hours);
            newDate.setMinutes(this.currentTime.minutes);
            newDate.setSeconds(this.currentTime.seconds);
          }
          this.$emit("update:modelValue", this.formatDateToString(newDate, this.format));
          if (this.hasInputElement && !this.pickTime) {
            this.close();
          }
        }
      },
      set12HourClock(value) {
        const currentDate = new Date(this.valueDate);
        const currentHours = currentDate.getHours();
        currentDate.setHours(
          value === "PM" ? currentHours + 12 : currentHours - 12
        );
        this.$emit("update:modelValue", this.formatDateToString(currentDate, this.format));
      },
      inputHours(event2) {
        const currentDate = new Date(this.valueDate);
        const currentHours = currentDate.getHours();
        const targetValue = parseInt(event2.target.value, 10) || 0;
        const minHours = this.use12HourClock ? 1 : 0;
        const maxHours = this.use12HourClock ? 12 : 23;
        const numValue = boundNumber(targetValue, minHours, maxHours);
        currentDate.setHours(
          this.use12HourClock ? to24HourClock(numValue, isPM(currentHours)) : numValue
        );
        event2.target.value = paddNum(numValue, 1);
        this.$emit("update:modelValue", this.formatDateToString(currentDate, this.format));
      },
      inputTime(method, event2) {
        const currentDate = new Date(this.valueDate);
        const targetValue = parseInt(event2.target.value) || 0;
        const numValue = boundNumber(targetValue, 0, 59);
        event2.target.value = paddNum(numValue, 2);
        currentDate[method](numValue);
        this.$emit("update:modelValue", this.formatDateToString(currentDate, this.format));
      },
      onTimeInputFocus(event2) {
        event2.target.select && event2.target.select();
      }
    }
  };
  function paddNum(num, padsize) {
    return typeof num !== "undefined" ? num.toString().length > padsize ? num : new Array(padsize - num.toString().length + 1).join("0") + num : void 0;
  }
  function chunkArray(inputArray, chunkSize) {
    const results = [];
    while (inputArray.length) {
      results.push(inputArray.splice(0, chunkSize));
    }
    return results;
  }
  function areSameDates(date1, date2) {
    return date1.getDate() === date2.getDate() && date1.getMonth() === date2.getMonth() && date1.getFullYear() === date2.getFullYear();
  }
  function range(start2, end2) {
    const results = [];
    for (let i = start2; i <= end2; i++) {
      results.push(i);
    }
    return results;
  }
  function to12HourClock(hours) {
    const remainder = hours % 12;
    return remainder === 0 ? 12 : remainder;
  }
  function to24HourClock(hours, PM) {
    return PM ? hours === 12 ? hours : hours + 12 : hours === 12 ? 0 : hours;
  }
  function isPM(hours) {
    return hours >= 12;
  }
  function boundNumber(value, min2, max2) {
    return Math.min(Math.max(value, min2), max2);
  }
  const _hoisted_1$w = ["readonly", "value"];
  const _hoisted_2$l = { class: "vdpInnerWrap" };
  const _hoisted_3$h = { class: "vdpHeader" };
  const _hoisted_4$a = ["title"];
  const _hoisted_5$5 = ["title"];
  const _hoisted_6$4 = { class: "vdpPeriodControls" };
  const _hoisted_7$2 = { class: "vdpPeriodControl" };
  const _hoisted_8$2 = ["value"];
  const _hoisted_9$2 = { class: "vdpPeriodControl" };
  const _hoisted_10$2 = ["value"];
  const _hoisted_11$2 = { class: "vdpTable" };
  const _hoisted_12$1 = { class: "vdpHeadCellContent" };
  const _hoisted_13$1 = ["data-id", "onClick"];
  const _hoisted_14$1 = { class: "vdpCellContent" };
  const _hoisted_15 = {
    key: 0,
    class: "vdpTimeControls"
  };
  const _hoisted_16 = { class: "vdpTimeCaption" };
  const _hoisted_17 = { class: "vdpTimeUnit" };
  const _hoisted_18 = /* @__PURE__ */ vue.createElementVNode("br", null, null, -1);
  const _hoisted_19 = ["disabled", "value"];
  const _hoisted_20 = {
    key: 0,
    class: "vdpTimeSeparator"
  };
  const _hoisted_21 = {
    key: 1,
    class: "vdpTimeUnit"
  };
  const _hoisted_22 = /* @__PURE__ */ vue.createElementVNode("br", null, null, -1);
  const _hoisted_23 = ["disabled", "value"];
  const _hoisted_24 = {
    key: 2,
    class: "vdpTimeSeparator"
  };
  const _hoisted_25 = {
    key: 3,
    class: "vdpTimeUnit"
  };
  const _hoisted_26 = /* @__PURE__ */ vue.createElementVNode("br", null, null, -1);
  const _hoisted_27 = ["disabled", "value"];
  const _hoisted_28 = ["disabled"];
  function _sfc_render$m(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createElementBlock("div", {
      class: vue.normalizeClass(["vdpComponent", { vdpWithInput: $props.hasInputElement }])
    }, [
      vue.renderSlot(_ctx.$slots, "default", {
        open: $options.open,
        close: $options.close,
        toggle: $options.toggle,
        inputValue: $data.inputValue,
        processUserInput: $options.processUserInput,
        valueToInputFormat: $options.valueToInputFormat
      }, () => [
        $props.hasInputElement ? (vue.openBlock(), vue.createElementBlock("input", vue.mergeProps({
          key: 0,
          type: "text"
        }, $props.inputAttributes, {
          readonly: $options.isReadOnly,
          value: $data.inputValue,
          onInput: _cache[0] || (_cache[0] = ($event) => $props.editable && $options.processUserInput($event.target.value)),
          onFocus: _cache[1] || (_cache[1] = ($event) => $props.editable && $options.open()),
          onClick: _cache[2] || (_cache[2] = ($event) => $props.editable && $options.open())
        }), null, 16, _hoisted_1$w)) : vue.createCommentVNode("", true),
        $props.editable && $props.hasInputElement && $data.inputValue ? (vue.openBlock(), vue.createElementBlock("button", {
          key: 1,
          class: "vdpClearInput",
          type: "button",
          onClick: _cache[3] || (_cache[3] = (...args) => $options.clear && $options.clear(...args))
        })) : vue.createCommentVNode("", true)
      ]),
      vue.createVNode(vue.Transition, { name: "vdp-toggle-calendar" }, {
        default: vue.withCtx(() => [
          $data.opened ? (vue.openBlock(), vue.createElementBlock("div", {
            key: 0,
            class: vue.normalizeClass(["vdpOuterWrap", [$data.positionClass, { vdpFloating: $props.hasInputElement }]]),
            ref: "outerWrap",
            onClick: _cache[15] || (_cache[15] = (...args) => $options.closeViaOverlay && $options.closeViaOverlay(...args))
          }, [
            vue.createElementVNode("div", _hoisted_2$l, [
              vue.createElementVNode("header", _hoisted_3$h, [
                vue.createElementVNode("button", {
                  class: "vdpArrow vdpArrowPrev",
                  title: $props.prevMonthCaption,
                  type: "button",
                  onClick: _cache[4] || (_cache[4] = ($event) => $options.incrementMonth(-1))
                }, vue.toDisplayString($props.prevMonthCaption), 9, _hoisted_4$a),
                vue.createElementVNode("button", {
                  class: "vdpArrow vdpArrowNext",
                  type: "button",
                  title: $props.nextMonthCaption,
                  onClick: _cache[5] || (_cache[5] = ($event) => $options.incrementMonth(1))
                }, vue.toDisplayString($props.nextMonthCaption), 9, _hoisted_5$5),
                vue.createElementVNode("div", _hoisted_6$4, [
                  vue.createElementVNode("div", _hoisted_7$2, [
                    (vue.openBlock(), vue.createElementBlock("button", {
                      class: vue.normalizeClass($options.directionClass),
                      key: $data.currentPeriod.month,
                      type: "button"
                    }, vue.toDisplayString($props.months[$data.currentPeriod.month]), 3)),
                    vue.withDirectives(vue.createElementVNode("select", {
                      "onUpdate:modelValue": _cache[6] || (_cache[6] = ($event) => $data.currentPeriod.month = $event)
                    }, [
                      (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($props.months, (month, index2) => {
                        return vue.openBlock(), vue.createElementBlock("option", {
                          value: index2,
                          key: month
                        }, vue.toDisplayString(month), 9, _hoisted_8$2);
                      }), 128))
                    ], 512), [
                      [vue.vModelSelect, $data.currentPeriod.month]
                    ])
                  ]),
                  vue.createElementVNode("div", _hoisted_9$2, [
                    (vue.openBlock(), vue.createElementBlock("button", {
                      class: vue.normalizeClass($options.directionClass),
                      key: $data.currentPeriod.year,
                      type: "button"
                    }, vue.toDisplayString($data.currentPeriod.year), 3)),
                    vue.withDirectives(vue.createElementVNode("select", {
                      "onUpdate:modelValue": _cache[7] || (_cache[7] = ($event) => $data.currentPeriod.year = $event)
                    }, [
                      (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($options.yearRange, (year) => {
                        return vue.openBlock(), vue.createElementBlock("option", {
                          value: year,
                          key: year
                        }, vue.toDisplayString(year), 9, _hoisted_10$2);
                      }), 128))
                    ], 512), [
                      [vue.vModelSelect, $data.currentPeriod.year]
                    ])
                  ])
                ])
              ]),
              vue.createElementVNode("table", _hoisted_11$2, [
                vue.createElementVNode("thead", null, [
                  vue.createElementVNode("tr", null, [
                    (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($options.weekdaysSorted, (weekday, weekdayIndex) => {
                      return vue.openBlock(), vue.createElementBlock("th", {
                        class: "vdpHeadCell",
                        key: weekdayIndex
                      }, [
                        vue.createElementVNode("span", _hoisted_12$1, vue.toDisplayString(weekday), 1)
                      ]);
                    }), 128))
                  ])
                ]),
                (vue.openBlock(), vue.createElementBlock("tbody", {
                  key: $data.currentPeriod.year + "-" + $data.currentPeriod.month,
                  class: vue.normalizeClass($options.directionClass)
                }, [
                  (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($options.currentPeriodDates, (week, weekIndex) => {
                    return vue.openBlock(), vue.createElementBlock("tr", {
                      class: "vdpRow",
                      key: weekIndex
                    }, [
                      (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(week, (item) => {
                        return vue.openBlock(), vue.createElementBlock("td", {
                          class: vue.normalizeClass(["vdpCell", {
                            selectable: $props.editable && !item.disabled,
                            selected: item.selected,
                            disabled: item.disabled,
                            today: item.today,
                            outOfRange: item.outOfRange
                          }]),
                          "data-id": item.dateKey,
                          key: item.dateKey,
                          onClick: ($event) => $props.editable && $options.selectDateItem(item)
                        }, [
                          vue.createElementVNode("div", _hoisted_14$1, vue.toDisplayString(item.date.getDate()), 1)
                        ], 10, _hoisted_13$1);
                      }), 128))
                    ]);
                  }), 128))
                ], 2))
              ]),
              $props.pickTime && $options.currentTime ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_15, [
                vue.createElementVNode("span", _hoisted_16, vue.toDisplayString($props.setTimeCaption), 1),
                vue.createElementVNode("div", _hoisted_17, [
                  vue.createElementVNode("pre", null, [
                    vue.createElementVNode("span", null, vue.toDisplayString($options.currentTime.hoursFormatted), 1),
                    _hoisted_18
                  ]),
                  vue.createElementVNode("input", {
                    type: "number",
                    pattern: "\\d*",
                    class: "vdpHoursInput",
                    onInput: _cache[8] || (_cache[8] = vue.withModifiers((...args) => $options.inputHours && $options.inputHours(...args), ["prevent"])),
                    onFocusin: _cache[9] || (_cache[9] = (...args) => $options.onTimeInputFocus && $options.onTimeInputFocus(...args)),
                    disabled: !$props.editable,
                    value: $options.currentTime.hoursFormatted
                  }, null, 40, _hoisted_19)
                ]),
                $props.pickMinutes ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_20, ":")) : vue.createCommentVNode("", true),
                $props.pickMinutes ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_21, [
                  vue.createElementVNode("pre", null, [
                    vue.createElementVNode("span", null, vue.toDisplayString($options.currentTime.minutesFormatted), 1),
                    _hoisted_22
                  ]),
                  $props.pickMinutes ? (vue.openBlock(), vue.createElementBlock("input", {
                    key: 0,
                    type: "number",
                    pattern: "\\d*",
                    class: "vdpMinutesInput",
                    onInput: _cache[10] || (_cache[10] = ($event) => $options.inputTime("setMinutes", $event)),
                    onFocusin: _cache[11] || (_cache[11] = (...args) => $options.onTimeInputFocus && $options.onTimeInputFocus(...args)),
                    disabled: !$props.editable,
                    value: $options.currentTime.minutesFormatted
                  }, null, 40, _hoisted_23)) : vue.createCommentVNode("", true)
                ])) : vue.createCommentVNode("", true),
                $props.pickSeconds ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_24, ":")) : vue.createCommentVNode("", true),
                $props.pickSeconds ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_25, [
                  vue.createElementVNode("pre", null, [
                    vue.createElementVNode("span", null, vue.toDisplayString($options.currentTime.secondsFormatted), 1),
                    _hoisted_26
                  ]),
                  $props.pickSeconds ? (vue.openBlock(), vue.createElementBlock("input", {
                    key: 0,
                    type: "number",
                    pattern: "\\d*",
                    class: "vdpSecondsInput",
                    onInput: _cache[12] || (_cache[12] = ($event) => $options.inputTime("setSeconds", $event)),
                    onFocusin: _cache[13] || (_cache[13] = (...args) => $options.onTimeInputFocus && $options.onTimeInputFocus(...args)),
                    disabled: !$props.editable,
                    value: $options.currentTime.secondsFormatted
                  }, null, 40, _hoisted_27)) : vue.createCommentVNode("", true)
                ])) : vue.createCommentVNode("", true),
                $props.use12HourClock ? (vue.openBlock(), vue.createElementBlock("button", {
                  key: 4,
                  type: "button",
                  class: "vdp12HourToggleBtn",
                  disabled: !$props.editable,
                  onClick: _cache[14] || (_cache[14] = ($event) => $options.set12HourClock($options.currentTime.isPM ? "AM" : "PM"))
                }, vue.toDisplayString($options.currentTime.isPM ? "PM" : "AM"), 9, _hoisted_28)) : vue.createCommentVNode("", true)
              ])) : vue.createCommentVNode("", true)
            ])
          ], 2)) : vue.createCommentVNode("", true)
        ]),
        _: 1
      })
    ], 2);
  }
  var vueDatePick = /* @__PURE__ */ _export_sfc(_sfc_main$K, [["render", _sfc_render$m]]);
  const _sfc_main$J = {
    name: "InputDatePicker",
    components: {
      vueDatePick,
      BaseInput: _sfc_main$1D
    },
    props: {
      modelValue: {
        type: String,
        required: true
      },
      readonly: {
        type: Boolean,
        required: false
      },
      pickTime: {
        type: Boolean,
        required: false,
        default: false
      },
      format: {
        type: String,
        required: false
      },
      use12HourClock: {
        type: Boolean,
        required: false
      },
      pastDisabled: {
        type: Boolean,
        required: false
      },
      futureDisabled: {
        type: Boolean,
        required: false,
        default: false
      }
    },
    data() {
      return {
        weekdaysStrings: [
          this.$translate("monday"),
          this.$translate("tuesday"),
          this.$translate("wednesday"),
          this.$translate("thursday"),
          this.$translate("friday"),
          this.$translate("saturday"),
          this.$translate("sunday")
        ],
        monthsStrings: [
          this.$translate("jan"),
          this.$translate("feb"),
          this.$translate("mar"),
          this.$translate("apr"),
          this.$translate("may"),
          this.$translate("jun"),
          this.$translate("jul"),
          this.$translate("aug"),
          this.$translate("sep"),
          this.$translate("oct"),
          this.$translate("nov"),
          this.$translate("dec")
        ]
      };
    },
    computed: {
      valueModel: {
        get() {
          return this.modelValue;
        },
        set(newValue) {
          this.$emit("update:modelValue", newValue);
        }
      }
    },
    methods: {
      disableDate(date) {
        const currentDate = new Date();
        currentDate.setHours(0, 0, 0, 0);
        if (this.pastDisabled) {
          return date < currentDate;
        } else if (this.futureDisabled) {
          return date > currentDate;
        } else
          return false;
      }
    }
  };
  function _sfc_render$l(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_BaseInput = vue.resolveComponent("BaseInput");
    const _component_vueDatePick = vue.resolveComponent("vueDatePick");
    return vue.openBlock(), vue.createBlock(_component_vueDatePick, {
      modelValue: $options.valueModel,
      "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => $options.valueModel = $event),
      class: "znpb-input-date",
      "next-month-caption": _ctx.$translate("next_month"),
      "previous-month-caption": _ctx.$translate("previous_month"),
      "set-time-caption": _ctx.$translate("set_time"),
      weekdays: $data.weekdaysStrings,
      months: $data.monthsStrings,
      "pick-time": $props.pickTime,
      "use-12-hour-clock": $props.use12HourClock,
      format: $props.format,
      "is-date-disabled": $options.disableDate
    }, {
      default: vue.withCtx(({ toggle }) => [
        vue.createVNode(_component_BaseInput, vue.mergeProps({
          modelValue: $options.valueModel,
          "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $options.valueModel = $event),
          readonly: $props.readonly,
          class: "znpb-input-number__input"
        }, _ctx.$attrs, {
          onKeydown: toggle,
          onMouseup: toggle
        }), null, 16, ["modelValue", "readonly", "onKeydown", "onMouseup"])
      ]),
      _: 1
    }, 8, ["modelValue", "next-month-caption", "previous-month-caption", "set-time-caption", "weekdays", "months", "pick-time", "use-12-hour-clock", "format", "is-date-disabled"]);
  }
  var InputDatePicker = /* @__PURE__ */ _export_sfc(_sfc_main$J, [["render", _sfc_render$l]]);
  var InputEditor_vue_vue_type_style_index_0_lang = "";
  const __default__$m = {
    name: "InputEditor"
  };
  const _sfc_main$I = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$m), {
    props: {
      modelValue: { default: "" }
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      let editorTextarea;
      const root2 = vue.ref(null);
      let editor;
      const randomNumber = Math.floor(Math.random() * 100 + 1);
      const editorID = `znpbwpeditor${randomNumber}`;
      const content = vue.computed({
        get() {
          return props.modelValue || "";
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      vue.onBeforeUnmount(() => {
        editorTextarea.removeEventListener("keyup", onTextChanged);
        if (window.tinyMCE !== void 0 && editor) {
          window.tinyMCE.remove(editor);
        }
        editor = null;
      });
      vue.onMounted(() => {
        root2.value.innerHTML = window.ZnPbInitialData.wp_editor.replace(/znpbwpeditorid/g, editorID).replace("%%ZNPB_EDITOR_CONTENT%%", content.value);
        editorTextarea = document.querySelectorAll(".wp-editor-area")[0];
        editorTextarea.addEventListener("keyup", onTextChanged);
        window.quicktags({
          buttons: "strong,em,del,link,img,close",
          id: editorID
        });
        const config = {
          id: editorID,
          selector: `#${editorID}`,
          setup: onEditorSetup,
          content_style: "body { background-color: #fff; }"
        };
        window.tinyMCEPreInit.mceInit[editorID] = Object.assign({}, window.tinyMCEPreInit.mceInit.znpbwpeditorid, config);
        window.switchEditors.go(editorID, "tmce");
      });
      vue.watch(
        () => props.modelValue,
        (newValue) => {
          const currentValue = editor == null ? void 0 : editor.getContent();
          if (editor && currentValue !== newValue) {
            const value = newValue || "";
            editor.setContent(value);
            debouncedAddToHistory();
            editorTextarea.value = newValue;
          }
        }
      );
      const debouncedAddToHistory = debounce$1(() => {
        if (editor) {
          editor.undoManager.add();
        }
      }, 500);
      function onEditorSetup(editorInstance) {
        editor = editorInstance;
        editor.on("change KeyUp Undo Redo", onEditorContentChange);
      }
      function onEditorContentChange() {
        const currentValue = props.modelValue;
        const newValue = editor == null ? void 0 : editor.getContent();
        if (currentValue !== newValue) {
          emit("update:modelValue", newValue);
        }
      }
      function onTextChanged() {
        emit("update:modelValue", editorTextarea.value);
      }
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          ref_key: "root",
          ref: root2,
          class: "znpb-wp-editor__wrapper znpb-wp-editor-custom"
        }, null, 512);
      };
    }
  }));
  var InputMedia_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$v = { class: "znpb-input-media-wrapper" };
  const __default__$l = {
    name: "InputMedia"
  };
  const _sfc_main$H = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$l), {
    props: {
      modelValue: null,
      media_type: { default: "image" },
      selectButtonText: { default: "select" },
      mediaConfig: { default: () => {
        return {
          inserTitle: "Add File",
          multiple: false
        };
      } }
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const mediaModal = vue.ref(null);
      const inputValue = vue.computed({
        get() {
          return props.modelValue || "";
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      function openMediaModal() {
        if (mediaModal.value === null) {
          let selection = getSelection();
          const args = {
            frame: "select",
            state: "library",
            library: { type: props.media_type },
            button: { text: props.mediaConfig.inserTitle },
            selection
          };
          mediaModal.value = window.wp.media(args);
          mediaModal.value.on("select update insert", selectFont);
        }
        mediaModal.value.open();
      }
      function selectFont(e) {
        console.log("e", e);
        let selection = mediaModal.value.state().get("selection").toJSON();
        if (e !== void 0) {
          selection = e;
        }
        if (props.mediaConfig.multiple) {
          inputValue.value = selection.map((selectedItem) => selectedItem.url).join(",");
        } else {
          inputValue.value = selection[0].url;
        }
      }
      function getSelection() {
        if (typeof props.modelValue === "undefined")
          return;
        let idArray = props.modelValue.split(",");
        let args = { orderby: "post__in", order: "ASC", type: "image", perPage: -1, post__in: idArray };
        let attachments = window.wp.media.query(args);
        let selection = new window.wp.media.model.Selection(attachments.models, {
          props: attachments.props.toJSON(),
          multiple: true
        });
        return selection;
      }
      return (_ctx, _cache) => {
        const _component_Button = vue.resolveComponent("Button");
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$v, [
          vue.createVNode(_sfc_main$1D, {
            modelValue: vue.unref(inputValue),
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(inputValue) ? inputValue.value = $event : null),
            class: "znpb-form__input-text",
            placeholder: "Type your text here",
            onClick: openMediaModal
          }, null, 8, ["modelValue"]),
          vue.createVNode(_component_Button, {
            type: "line",
            onClick: openMediaModal
          }, {
            default: vue.withCtx(() => [
              vue.createTextVNode(vue.toDisplayString(__props.selectButtonText), 1)
            ]),
            _: 1
          })
        ]);
      };
    }
  }));
  var InputFile_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$u = { class: "znpb-input-media-wrapper" };
  const _hoisted_2$k = ["accept"];
  const _hoisted_3$g = { key: 1 };
  const __default__$k = {
    name: "InputFile"
  };
  const _sfc_main$G = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$k), {
    props: {
      modelValue: null,
      type: { default: "image" },
      selectButtonText: { default: "select" }
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const fileInput = vue.ref(null);
      const loading2 = vue.ref(false);
      const inputValue = vue.computed({
        get() {
          return props.modelValue || "";
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      function onButtonClick() {
        if (fileInput.value) {
          fileInput.value.click();
        }
      }
      function uploadFiles(fieldName, fileList) {
        return __async(this, null, function* () {
          const formData = new FormData();
          if (!fileList || !fileList.length)
            return;
          Array.from(fileList).forEach((file) => {
            formData.append(fieldName, file, file.name);
          });
          loading2.value = true;
          try {
            const response = yield uploadFile(formData);
            const responseData = response.data;
            inputValue.value = responseData.file_url;
          } catch (err) {
            console.error(err);
          }
          loading2.value = false;
        });
      }
      return (_ctx, _cache) => {
        const _component_Loader = vue.resolveComponent("Loader");
        const _component_Button = vue.resolveComponent("Button");
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$u, [
          vue.createVNode(_sfc_main$1D, {
            modelValue: vue.unref(inputValue),
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(inputValue) ? inputValue.value = $event : null),
            class: "znpb-form__input-text",
            placeholder: "Type your text here",
            onClick: onButtonClick
          }, null, 8, ["modelValue"]),
          vue.createElementVNode("input", {
            ref_key: "fileInput",
            ref: fileInput,
            type: "file",
            style: { "display": "none" },
            accept: __props.type,
            name: "file",
            onChange: _cache[1] || (_cache[1] = ($event) => uploadFiles($event.target.name, $event.target.files))
          }, null, 40, _hoisted_2$k),
          vue.createVNode(_component_Button, {
            type: "line",
            onClick: onButtonClick
          }, {
            default: vue.withCtx(() => [
              loading2.value ? (vue.openBlock(), vue.createBlock(_component_Loader, {
                key: 0,
                size: 14
              })) : (vue.openBlock(), vue.createElementBlock("span", _hoisted_3$g, vue.toDisplayString(__props.selectButtonText), 1))
            ]),
            _: 1
          })
        ]);
      };
    }
  }));
  var InputRadioGroup_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$F = {
    props: {
      layout: {
        type: String,
        required: false
      }
    },
    data() {
      return {};
    },
    name: "InputRadioGroup"
  };
  function _sfc_render$k(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createElementBlock("div", {
      class: vue.normalizeClass(["zion-radio-group", {
        [`zion-radio-group--${$props.layout}`]: $props.layout
      }])
    }, [
      vue.renderSlot(_ctx.$slots, "default")
    ], 2);
  }
  var InputRadioGroup = /* @__PURE__ */ _export_sfc(_sfc_main$F, [["render", _sfc_render$k]]);
  var InputRadio_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$E = {
    name: "InputRadio",
    props: {
      modelValue: {
        type: String,
        required: false
      },
      label: {
        type: String,
        required: false
      },
      optionValue: {
        type: String,
        required: true
      },
      hideInput: {
        type: Boolean,
        required: false,
        default: false
      }
    },
    data() {
      return {
        checked: ""
      };
    },
    computed: {
      radioButtonValue: {
        get: function() {
          return this.modelValue;
        },
        set: function() {
          this.$emit("update:modelValue", this.optionValue);
        }
      },
      isSelected() {
        return this.modelValue === this.optionValue;
      }
    },
    methods: {}
  };
  const _hoisted_1$t = ["modelValue"];
  const _hoisted_2$j = /* @__PURE__ */ vue.createElementVNode("span", { class: "znpb-radio-item-input" }, null, -1);
  const _hoisted_3$f = {
    key: 0,
    class: "znpb-radio-item-label"
  };
  function _sfc_render$j(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createElementBlock("label", {
      class: vue.normalizeClass(["znpb-radio-item", {
        "znpb-radio-item--active": $options.isSelected,
        "znpb-radio-item--hidden-input": $props.hideInput
      }])
    }, [
      vue.withDirectives(vue.createElementVNode("input", {
        "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $options.radioButtonValue = $event),
        modelValue: $props.optionValue,
        type: "radio",
        class: "znpb-form__input-toggle"
      }, null, 8, _hoisted_1$t), [
        [vue.vModelRadio, $options.radioButtonValue]
      ]),
      _hoisted_2$j,
      vue.renderSlot(_ctx.$slots, "default"),
      $props.label ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_3$f, vue.toDisplayString($props.label), 1)) : vue.createCommentVNode("", true)
    ], 2);
  }
  var InputRadio = /* @__PURE__ */ _export_sfc(_sfc_main$E, [["render", _sfc_render$j]]);
  var InputRadioIcon_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$D = {
    name: "InputRadioIcon",
    components: {
      Icon: _sfc_main$1K
    },
    props: {
      modelValue: {
        type: String,
        required: false
      },
      label: {
        type: String,
        required: false
      },
      optionValue: {
        type: String,
        required: true
      },
      icon: {
        type: String,
        required: false
      },
      bgSize: {
        type: Number,
        required: false,
        default: 32
      }
    },
    data() {
      return {
        checked: ""
      };
    },
    computed: {
      radioButtonValue: {
        get: function() {
          return this.modelValue;
        },
        set: function() {
          this.$emit("update:modelValue", this.optionValue);
        }
      },
      isSelected() {
        return this.modelValue === this.optionValue;
      }
    },
    methods: {}
  };
  const _hoisted_1$s = ["modelValue"];
  function _sfc_render$i(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_Icon = vue.resolveComponent("Icon");
    return vue.openBlock(), vue.createElementBlock("label", {
      class: vue.normalizeClass(["znpb-radio-icon-item", {
        "znpb-radio-icon-item--active": $options.isSelected
      }])
    }, [
      vue.withDirectives(vue.createElementVNode("input", {
        "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $options.radioButtonValue = $event),
        modelValue: $props.optionValue,
        type: "radio",
        class: "znpb-form__input-toggle"
      }, null, 8, _hoisted_1$s), [
        [vue.vModelRadio, $options.radioButtonValue]
      ]),
      $props.icon ? (vue.openBlock(), vue.createBlock(_component_Icon, {
        key: 0,
        icon: $props.icon,
        "bg-size": $props.bgSize,
        class: "znpb-radio-icon-item__icon"
      }, null, 8, ["icon", "bg-size"])) : vue.createCommentVNode("", true),
      vue.createTextVNode(" " + vue.toDisplayString($props.label), 1)
    ], 2);
  }
  var InputRadioIcon = /* @__PURE__ */ _export_sfc(_sfc_main$D, [["render", _sfc_render$i]]);
  const _hoisted_1$r = ["innerHTML"];
  const __default__$j = {
    name: "SvgMask"
  };
  const _sfc_main$C = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$j), {
    props: {
      shapePath: null,
      position: null,
      color: null,
      flip: { type: Boolean }
    },
    setup(__props) {
      const props = __props;
      const masks = vue.inject("masks");
      const svgData = vue.ref("");
      const getSvgIcon = vue.computed(() => svgData.value);
      vue.watch(
        () => props.shapePath,
        (newValue) => {
          getFile(newValue);
        }
      );
      function getFile(shapePath) {
        let url;
        if (shapePath.includes(".svg")) {
          url = shapePath;
        } else {
          const shapeConfig = masks[shapePath];
          url = shapeConfig.url;
        }
        fetch(url).then((response) => response.text()).then((svgFile) => {
          svgData.value = svgFile;
        }).catch((error) => {
          console.error(error);
        });
      }
      vue.onMounted(() => {
        if (props.shapePath !== void 0 && props.shapePath.length) {
          getFile(props.shapePath);
        }
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          class: vue.normalizeClass(["znpb-shape-divider-icon zb-mask", [__props.position === "top" ? "zb-mask-pos--top" : "zb-mask-pos--bottom", __props.flip ? "zb-mask-pos--flip" : ""]]),
          style: vue.normalizeStyle({ color: __props.color }),
          innerHTML: vue.unref(getSvgIcon)
        }, null, 14, _hoisted_1$r);
      };
    }
  }));
  var InputShapeDividers_vue_vue_type_style_index_0_lang = "";
  const __default__$i = {
    name: "InputShapeDividers"
  };
  const _sfc_main$B = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$i), {
    props: {
      modelValue: { default: () => {
        return {};
      } }
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const { translate: translate2 } = window.zb.i18n;
      const maskPosOptions = vue.ref([
        {
          id: "top",
          name: translate2("top_masks")
        },
        {
          id: "bottom",
          name: translate2("bottom_masks")
        }
      ]);
      const activeMaskPosition = vue.ref("top");
      const computedTitle = vue.computed(() => {
        return activeMaskPosition.value === "top" ? translate2("select_top_mask") : translate2("select_bottom_mask");
      });
      const schema = vue.computed(() => {
        return {
          shape: {
            type: "shape_component",
            id: "shape",
            width: "100",
            title: computedTitle.value,
            position: activeMaskPosition.value
          },
          color: {
            type: "colorpicker",
            id: "color",
            width: "100",
            title: translate2("select_mask_color")
          },
          height: {
            type: "dynamic_slider",
            id: "height",
            title: translate2("select_mask_height"),
            width: "100",
            responsive_options: true,
            options: [
              { unit: "px", min: 0, max: 4999, step: 1 },
              { unit: "%", min: 0, max: 100, step: 1 },
              { unit: "vh", min: 0, max: 100, step: 10 },
              { unit: "auto" }
            ]
          },
          flip: {
            type: "checkbox_switch",
            id: "flip",
            title: translate2("flip_mask"),
            width: "100",
            layout: "inline"
          }
        };
      });
      const computedValue = vue.computed({
        get() {
          var _a3, _b;
          return (_b = (_a3 = props.modelValue) == null ? void 0 : _a3[activeMaskPosition.value]) != null ? _b : {};
        },
        set(newValue) {
          if (newValue === null) {
            emit("update:modelValue", null);
            return;
          }
          const shape = get(props.modelValue, `${activeMaskPosition.value}.shape`);
          if (shape !== newValue["shape"] && newValue["height"]) {
            newValue["height"] = "auto";
          }
          emit("update:modelValue", __spreadProps(__spreadValues({}, props.modelValue), {
            [activeMaskPosition.value]: newValue
          }));
        }
      });
      return (_ctx, _cache) => {
        const _component_OptionsForm = vue.resolveComponent("OptionsForm");
        return vue.openBlock(), vue.createElementBlock("div", null, [
          vue.createVNode(vue.unref(_sfc_main$L), {
            modelValue: activeMaskPosition.value,
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => activeMaskPosition.value = $event),
            options: maskPosOptions.value,
            columns: 2
          }, null, 8, ["modelValue", "options"]),
          vue.createVNode(_component_OptionsForm, {
            modelValue: vue.unref(computedValue),
            "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => vue.isRef(computedValue) ? computedValue.value = $event : null),
            schema: vue.unref(schema)
          }, null, 8, ["modelValue", "schema"])
        ]);
      };
    }
  }));
  var Shape_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$q = { class: "znpb-editor-shapeWrapper" };
  const __default__$h = {
    name: "Shape"
  };
  const _sfc_main$A = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$h), {
    props: {
      shapePath: null,
      position: null
    },
    setup(__props) {
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$q, [
          vue.renderSlot(_ctx.$slots, "default"),
          __props.shapePath ? (vue.openBlock(), vue.createBlock(_sfc_main$C, {
            key: 0,
            "shape-path": __props.shapePath,
            position: __props.position
          }, null, 8, ["shape-path", "position"])) : vue.createCommentVNode("", true)
        ]);
      };
    }
  }));
  var UpgradeToPro_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$p = { class: "znpb-option__upgrade-to-pro" };
  const _hoisted_2$i = { class: "znpb-option__upgrade-to-pro-container" };
  const _hoisted_3$e = ["href"];
  const _hoisted_4$9 = {
    href: "https://zionbuilder.io/",
    target: "_blank",
    class: "znpb-button znpb-get-pro__cta znpb-button--secondary znpb-option__upgrade-to-pro-button"
  };
  const __default__$g = {
    name: "UpgradeToPro"
  };
  const _sfc_main$z = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$g), {
    props: {
      message_title: { default: "" },
      message_description: { default: "" },
      info_text: { default: "" },
      info_link: { default: "https://zionbuilder.io/documentation/pro-version/" }
    },
    setup(__props) {
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$p, [
          vue.createElementVNode("div", _hoisted_2$i, [
            vue.createVNode(_sfc_main$17, {
              text: _ctx.$translate("pro"),
              type: "warning",
              class: "znpb-option__upgrade-to-pro-label"
            }, null, 8, ["text"]),
            vue.createElementVNode("h4", null, vue.toDisplayString(__props.message_title), 1),
            vue.createElementVNode("p", null, vue.toDisplayString(__props.message_description), 1),
            __props.info_text ? (vue.openBlock(), vue.createElementBlock("a", {
              key: 0,
              href: __props.info_link,
              target: "_blank"
            }, vue.toDisplayString(__props.info_text), 9, _hoisted_3$e)) : vue.createCommentVNode("", true),
            vue.createElementVNode("div", null, [
              vue.createElementVNode("a", _hoisted_4$9, vue.toDisplayString(_ctx.$translate("upgrade_to_pro")), 1)
            ])
          ])
        ]);
      };
    }
  }));
  var ShapeDividerComponent_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$o = { class: "znpb-shape-list znpb-fancy-scrollbar" };
  const __default__$f = {
    name: "ShapeDividerComponent"
  };
  const _sfc_main$y = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$f), {
    props: {
      position: null,
      modelValue: null
    },
    emits: ["update:modelValue"],
    setup(__props) {
      const showDelete = vue.ref(false);
      const masks = vue.inject("masks");
      const isPro = window.ZnPbComponentsData.is_pro_active;
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", null, [
          vue.createVNode(_sfc_main$A, {
            class: vue.normalizeClass(["znpb-active-shape-preview", [{ "mask-active": __props.modelValue }]]),
            "shape-path": __props.modelValue,
            position: __props.position
          }, {
            default: vue.withCtx(() => [
              !__props.modelValue ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1t), {
                key: 0,
                class: "znpb-style-shape__empty",
                "no-margin": true
              }, {
                default: vue.withCtx(() => [
                  vue.createTextVNode(vue.toDisplayString(_ctx.$translate("select_shape")), 1)
                ]),
                _: 1
              })) : (vue.openBlock(), vue.createElementBlock("span", {
                key: 1,
                class: "znpb-active-shape-preview__action",
                onMouseover: _cache[1] || (_cache[1] = ($event) => showDelete.value = true),
                onMouseleave: _cache[2] || (_cache[2] = ($event) => showDelete.value = false)
              }, [
                vue.createVNode(vue.Transition, {
                  name: "slide-fade",
                  mode: "out-in"
                }, {
                  default: vue.withCtx(() => [
                    !showDelete.value ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
                      key: "1",
                      icon: "check",
                      size: 10
                    })) : (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1K), {
                      key: "2",
                      icon: "close",
                      size: 10,
                      onClick: _cache[0] || (_cache[0] = vue.withModifiers(($event) => (_ctx.$emit("update:modelValue", null), showDelete.value = false), ["stop"]))
                    }))
                  ]),
                  _: 1
                })
              ], 32))
            ]),
            _: 1
          }, 8, ["shape-path", "class", "position"]),
          vue.createElementVNode("div", _hoisted_1$o, [
            (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(masks), (shape, shapeID) => {
              return vue.openBlock(), vue.createBlock(_sfc_main$A, {
                key: shapeID,
                "shape-path": shapeID,
                position: __props.position,
                onClick: ($event) => _ctx.$emit("update:modelValue", shapeID)
              }, null, 8, ["shape-path", "position", "onClick"]);
            }), 128)),
            !vue.unref(isPro) ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$z), {
              key: 0,
              message_title: _ctx.$translate("pro_masks_title"),
              message_description: _ctx.$translate("pro_masks_description"),
              info_text: _ctx.$translate("learn_more_about_pro")
            }, null, 8, ["message_title", "message_description", "info_text"])) : vue.createCommentVNode("", true)
          ])
        ]);
      };
    }
  }));
  const __default__$e = {
    name: "InputTextAlign"
  };
  const _sfc_main$x = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$e), {
    props: {
      modelValue: null,
      placeholder: null
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const { translate: translate2 } = window.zb.i18n;
      const textAlignOptions = [
        {
          icon: "align--left",
          id: "left",
          name: translate2("align_left")
        },
        {
          icon: "align--center",
          id: "center",
          name: translate2("align_center")
        },
        {
          icon: "align--right",
          id: "right",
          name: translate2("align_right")
        },
        {
          icon: "align--justify",
          id: "justify",
          name: translate2("justify")
        }
      ];
      const textAlignModel = vue.computed({
        get() {
          return props.modelValue || "";
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", null, [
          vue.createVNode(vue.unref(_sfc_main$L), {
            modelValue: vue.unref(textAlignModel),
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(textAlignModel) ? textAlignModel.value = $event : null),
            placeholder: __props.placeholder,
            options: textAlignOptions,
            columns: 4
          }, null, 8, ["modelValue", "placeholder"])
        ]);
      };
    }
  }));
  var InputTextShadow_vue_vue_type_style_index_0_lang = "";
  const __default__$d = {
    name: "InputTextShadow"
  };
  const _sfc_main$w = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$d), {
    props: {
      modelValue: { default: () => {
        return {};
      } },
      inset: { type: Boolean },
      shadow_type: { default: "text-shadow" },
      placeholder: { default: () => {
        return {};
      } }
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const { getSchema } = useOptionsSchemas();
      const schema = vue.computed(() => {
        let schema2 = getSchema("shadowSchema");
        if (props.shadow_type === "text-shadow") {
          schema2 = omit$1(schema2, ["inset", "spread"]);
        }
        if (Object.keys(props.placeholder).length > 0) {
          Object.keys(schema2).forEach((singleSchemaID) => {
            const singleSchema = schema2[singleSchemaID];
            if (typeof props.placeholder[singleSchemaID] !== "undefined") {
              singleSchema.placeholder = props.placeholder[singleSchemaID];
            }
          });
        }
        return schema2;
      });
      const valueModel = vue.computed({
        get() {
          return props.modelValue || {};
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      return (_ctx, _cache) => {
        const _component_OptionsForm = vue.resolveComponent("OptionsForm");
        return vue.openBlock(), vue.createElementBlock("div", {
          class: vue.normalizeClass(["znpb-shadow-option-wrapper__outer", `znpb-shadow-option--${__props.shadow_type}`])
        }, [
          vue.createVNode(_component_OptionsForm, {
            modelValue: vue.unref(valueModel),
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(valueModel) ? valueModel.value = $event : null),
            schema: vue.unref(schema),
            class: "znpb-shadow-option"
          }, null, 8, ["modelValue", "schema"])
        ], 2);
      };
    }
  }));
  var InputRadioImage_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$n = { class: "znpb-radio-image-container" };
  const _hoisted_2$h = { class: "znpb-radio-image-wrapper znpb-fancy-scrollbar" };
  const _hoisted_3$d = ["onClick"];
  const _hoisted_4$8 = ["src"];
  const _hoisted_5$4 = {
    key: 0,
    class: "znpb-radio-image-list__item-name"
  };
  const _hoisted_6$3 = {
    key: 0,
    class: "znpb-radio-image-search--noItems"
  };
  const __default__$c = {
    name: "InputRadioImage"
  };
  const _sfc_main$v = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$c), {
    props: {
      modelValue: null,
      options: null,
      columns: { default: 3 },
      useSearch: { type: Boolean, default: true },
      searchText: { default: () => {
        const { translate: translate2 } = window.zb.i18n;
        return translate2("search");
      } }
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const searchKeyword = vue.ref("");
      const visibleItems = vue.computed(() => {
        if (searchKeyword.value.length > 0) {
          return props.options.filter(
            (option) => option.name && option.name.toLowerCase().includes(searchKeyword.value.toLowerCase())
          );
        }
        return props.options;
      });
      function changeValue(newValue) {
        emit("update:modelValue", newValue);
      }
      return (_ctx, _cache) => {
        const _component_BaseInput = vue.resolveComponent("BaseInput");
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$n, [
          __props.useSearch ? (vue.openBlock(), vue.createBlock(_component_BaseInput, {
            key: 0,
            modelValue: searchKeyword.value,
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => searchKeyword.value = $event),
            placeholder: __props.searchText,
            clearable: true,
            class: "znpb-radio-image-search"
          }, null, 8, ["modelValue", "placeholder"])) : vue.createCommentVNode("", true),
          vue.createElementVNode("div", _hoisted_2$h, [
            vue.createElementVNode("ul", {
              class: vue.normalizeClass(["znpb-radio-image-list", [`znpb-radio-image-list--columns-${__props.columns}`]])
            }, [
              (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(visibleItems), (option, index2) => {
                return vue.openBlock(), vue.createElementBlock("li", {
                  key: index2,
                  class: "znpb-radio-image-list__item-wrapper",
                  onClick: ($event) => changeValue(option.value)
                }, [
                  vue.createElementVNode("div", {
                    class: vue.normalizeClass(["znpb-radio-image-list__item", { ["znpb-radio-image-list__item--active"]: __props.modelValue === option.value }])
                  }, [
                    option.image ? (vue.openBlock(), vue.createElementBlock("img", {
                      key: 0,
                      src: option.image,
                      class: "znpb-image-wrapper"
                    }, null, 8, _hoisted_4$8)) : vue.createCommentVNode("", true),
                    option.class ? (vue.openBlock(), vue.createElementBlock("span", {
                      key: 1,
                      class: vue.normalizeClass(["znpb-radio-image-list__preview-element animated", option.value])
                    }, null, 2)) : vue.createCommentVNode("", true),
                    option.icon ? (vue.openBlock(), vue.createBlock(_sfc_main$1K, {
                      key: 2,
                      class: "znpb-radio-image-list__icon",
                      icon: option.icon
                    }, null, 8, ["icon"])) : vue.createCommentVNode("", true)
                  ], 2),
                  option.name ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_5$4, vue.toDisplayString(option.name), 1)) : vue.createCommentVNode("", true)
                ], 8, _hoisted_3$d);
              }), 128)),
              __props.useSearch && vue.unref(visibleItems).length === 0 ? (vue.openBlock(), vue.createElementBlock("li", _hoisted_6$3, vue.toDisplayString(_ctx.$translate("no_items_found")), 1)) : vue.createCommentVNode("", true)
            ], 2)
          ])
        ]);
      };
    }
  }));
  var RepeaterOption_vue_vue_type_style_index_0_lang = "";
  const __default__$b = {
    name: "RepeaterOption"
  };
  const _sfc_main$u = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$b), {
    props: {
      modelValue: { default: () => {
        return {};
      } },
      schema: null,
      propertyIndex: { default: 0 },
      item_title: null,
      default_item_title: null,
      deletable: { type: Boolean, default: true },
      clonable: { type: Boolean, default: true }
    },
    emits: ["update:modelValue", "clone-option", "delete-option"],
    setup(__props, { emit }) {
      const props = __props;
      const selectedOptionModel = vue.computed({
        get() {
          return props.modelValue;
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      const title = vue.computed(() => {
        if (props.item_title && selectedOptionModel.value && selectedOptionModel.value[props.item_title]) {
          return selectedOptionModel.value[props.item_title];
        }
        return props.default_item_title.replace("%s", props.propertyIndex + 1);
      });
      function cloneOption() {
        const clone = JSON.parse(JSON.stringify(props.modelValue));
        emit("clone-option", clone);
      }
      function deleteOption(propertyIndex) {
        emit("delete-option", propertyIndex);
      }
      function onItemChange(newValues, index2) {
        emit("update:modelValue", { newValues, index: index2 });
      }
      return (_ctx, _cache) => {
        const _component_Icon = vue.resolveComponent("Icon");
        const _component_OptionsForm = vue.resolveComponent("OptionsForm");
        const _component_HorizontalAccordion = vue.resolveComponent("HorizontalAccordion");
        return vue.openBlock(), vue.createBlock(_component_HorizontalAccordion, {
          title: vue.unref(title),
          "combine-breadcrumbs": true,
          "show-back-button": true
        }, {
          actions: vue.withCtx(() => [
            __props.clonable ? (vue.openBlock(), vue.createBlock(_component_Icon, {
              key: 0,
              class: "znpb-option-repeater-selector__clone-icon",
              icon: "copy",
              onClick: vue.withModifiers(cloneOption, ["stop"])
            }, null, 8, ["onClick"])) : vue.createCommentVNode("", true),
            __props.deletable ? (vue.openBlock(), vue.createBlock(_component_Icon, {
              key: 1,
              class: "znpb-option-repeater-selector__delete-icon",
              icon: "delete",
              onClick: _cache[0] || (_cache[0] = vue.withModifiers(($event) => deleteOption(__props.propertyIndex), ["stop"]))
            })) : vue.createCommentVNode("", true)
          ]),
          default: vue.withCtx(() => [
            vue.createVNode(_component_OptionsForm, {
              schema: __props.schema,
              modelValue: vue.unref(selectedOptionModel),
              class: "znpb-option-repeater-form",
              "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => onItemChange($event, __props.propertyIndex))
            }, null, 8, ["schema", "modelValue"])
          ]),
          _: 1
        }, 8, ["title"]);
      };
    }
  }));
  var Repeater_vue_vue_type_style_index_0_lang = "";
  const __default__$a = {
    name: "Repeater"
  };
  const _sfc_main$t = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$a), {
    props: {
      modelValue: null,
      addable: { type: Boolean, default: true },
      deletable: { type: Boolean, default: true },
      clonable: { type: Boolean, default: true },
      maxItems: null,
      add_button_text: { default: () => {
        const { translate: translate2 } = window.zb.i18n;
        return translate2("generic_add_new");
      } },
      child_options: null,
      item_title: null,
      default_item_title: null,
      add_template: null
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const sortableItems = vue.computed({
        get() {
          return props.modelValue || [];
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      const showButton = vue.computed(() => {
        return props.maxItems ? props.addable && sortableItems.value.length < props.maxItems : props.addable;
      });
      const checkClonable = vue.computed(() => {
        return !props.addable ? false : !props.maxItems ? props.clonable : sortableItems.value.length < props.maxItems;
      });
      function onItemChange(payload) {
        const { index: index2, newValues } = payload;
        let copiedValues = [...sortableItems.value];
        let clonedNewValue = newValues;
        if (newValues === null) {
          clonedNewValue = [];
        }
        copiedValues[index2] = clonedNewValue;
        emit("update:modelValue", copiedValues);
      }
      function addProperty() {
        var _a3;
        const clone = [...sortableItems.value];
        const newItem = (_a3 = props.add_template) != null ? _a3 : {};
        clone.push(newItem);
        emit("update:modelValue", clone);
      }
      function cloneOption(event2, index2) {
        if (props.maxItems && props.addable && sortableItems.value.length < props.maxItems || props.maxItems === void 0) {
          const repeaterClone = [...sortableItems.value];
          repeaterClone.splice(index2, 0, event2);
          emit("update:modelValue", repeaterClone);
        }
      }
      function deleteOption(optionIndex) {
        let copiedValues = [...sortableItems.value];
        copiedValues.splice(optionIndex, 1);
        emit("update:modelValue", copiedValues);
      }
      return (_ctx, _cache) => {
        const _component_Button = vue.resolveComponent("Button");
        const _component_Sortable = vue.resolveComponent("Sortable");
        return vue.openBlock(), vue.createBlock(_component_Sortable, {
          modelValue: vue.unref(sortableItems),
          "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => vue.isRef(sortableItems) ? sortableItems.value = $event : null),
          class: "znpb-option-repeater",
          handle: ".znpb-horizontal-accordion > .znpb-horizontal-accordion__header"
        }, {
          end: vue.withCtx(() => [
            vue.unref(showButton) ? (vue.openBlock(), vue.createBlock(_component_Button, {
              key: 0,
              class: "znpb-option-repeater__add-button",
              type: "line",
              onClick: addProperty
            }, {
              default: vue.withCtx(() => [
                vue.createTextVNode(vue.toDisplayString(__props.add_button_text), 1)
              ]),
              _: 1
            })) : vue.createCommentVNode("", true)
          ]),
          default: vue.withCtx(() => [
            (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(sortableItems), (item, index2) => {
              return vue.openBlock(), vue.createBlock(_sfc_main$u, {
                key: index2,
                ref_for: true,
                ref: "repeaterItem",
                schema: __props.child_options,
                modelValue: item,
                "property-index": index2,
                item_title: __props.item_title,
                default_item_title: __props.default_item_title,
                deletable: !__props.addable ? false : __props.deletable,
                clonable: vue.unref(checkClonable),
                onCloneOption: ($event) => cloneOption($event, index2),
                onDeleteOption: deleteOption,
                "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => onItemChange($event))
              }, null, 8, ["schema", "modelValue", "property-index", "item_title", "default_item_title", "deletable", "clonable", "onCloneOption"]);
            }), 128))
          ]),
          _: 1
        }, 8, ["modelValue"]);
      };
    }
  }));
  var Loader_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$m = { class: "znpb-loader-wrapper" };
  const __default__$9 = {
    name: "Loader"
  };
  const _sfc_main$s = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$9), {
    props: {
      size: { default: 24 }
    },
    setup(__props) {
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$m, [
          vue.createElementVNode("div", {
            class: "znpb-loader",
            style: vue.normalizeStyle({
              height: `${__props.size}px`,
              width: `${__props.size}px`
            })
          }, null, 4)
        ]);
      };
    }
  }));
  var Notice_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$l = { class: "znpb-notices-wrapper" };
  const _hoisted_2$g = {
    key: 0,
    class: "znpb-notice__title"
  };
  const _hoisted_3$c = { class: "znpb-notice__message" };
  const __default__$8 = {
    name: "Notice"
  };
  const _sfc_main$r = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$8), {
    props: {
      error: null
    },
    emits: ["close-notice"],
    setup(__props, { emit }) {
      const props = __props;
      function hideOnEscape(event2) {
        if (event2.key === "Escape") {
          emit("close-notice");
          event2.preventDefault();
          document.removeEventListener("keydown", hideOnEscape);
        }
      }
      vue.onMounted(() => {
        var _a3;
        const delay = (_a3 = props.error.delayClose) != null ? _a3 : 5e3;
        if (delay !== 0) {
          setTimeout(() => {
            emit("close-notice");
          }, delay);
        }
        document.addEventListener("keydown", hideOnEscape);
      });
      vue.onBeforeUnmount(() => {
        document.removeEventListener("keydown", hideOnEscape);
      });
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createBlock(vue.Transition, {
          appear: "",
          name: "move"
        }, {
          default: vue.withCtx(() => [
            vue.createElementVNode("div", _hoisted_1$l, [
              vue.createElementVNode("div", {
                class: vue.normalizeClass(["znpb-notice", `znpb-notice--${__props.error.type || "success"}`])
              }, [
                vue.createVNode(vue.unref(_sfc_main$1K), {
                  class: "znpb-notice__close",
                  icon: "close",
                  size: 12,
                  onClick: _cache[0] || (_cache[0] = ($event) => _ctx.$emit("close-notice"))
                }),
                __props.error.title ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_2$g, vue.toDisplayString(__props.error.title), 1)) : vue.createCommentVNode("", true),
                vue.createElementVNode("div", _hoisted_3$c, vue.toDisplayString(__props.error.message), 1)
              ], 2)
            ])
          ]),
          _: 1
        });
      };
    }
  }));
  var OptionWrapper_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$k = {
    key: 0,
    class: "znpb-form__input-title"
  };
  const _hoisted_2$f = ["innerHTML"];
  const _hoisted_3$b = ["onClick"];
  const _hoisted_4$7 = ["onClick"];
  const _hoisted_5$3 = { class: "znpb-input-content" };
  const __default__$7 = {
    name: "OptionWrapper"
  };
  const _sfc_main$q = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$7), {
    props: {
      modelValue: null,
      schema: null,
      optionId: null,
      search_tags: { default: () => [] },
      label: { default: void 0 },
      compilePlaceholder: null,
      width: { default: void 0 },
      allModelValue: { default: () => {
        return {};
      } }
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const { getOption } = useOptions();
      const {
        deleteValueByPath,
        getTopModelValueByPath,
        updateTopModelValueByPath,
        deleteTopModelValueByPath,
        deleteValues,
        modelValue
      } = vue.inject("OptionsForm");
      const showChanges = vue.inject("showChanges");
      const { getSchema } = useOptionsSchemas();
      const { activeResponsiveDeviceInfo: activeResponsiveDeviceInfo2, builtInResponsiveDevices: builtInResponsiveDevices2, setActiveResponsiveDeviceId } = useResponsiveDevices();
      const activePseudo = vue.ref(null);
      const showDevices = vue.ref(false);
      const showPseudo = vue.ref(false);
      const panel = vue.inject("panel", null);
      const optionTypeConfig = vue.ref(null);
      const localSchema = vue.toRef(props, "schema");
      vue.provide("schema", vue.readonly(localSchema.value));
      const computedWrapperStyle = vue.computed(() => {
        const styles = {};
        if (props.schema.grow) {
          styles.flex = props.schema.grow;
        }
        if (props.schema.width) {
          styles.width = `${props.schema.width}%`;
        }
        return styles;
      });
      const computedShowTitle = vue.computed(() => {
        if (typeof props.schema.show_title !== "undefined") {
          return props.schema.show_title;
        }
        return true;
      });
      const activeResponsiveMedia = vue.computed(() => {
        return activeResponsiveDeviceInfo2.value.id;
      });
      const compiledSchema = vue.computed(() => {
        const _a3 = props.schema, {
          description,
          type,
          is_layout: isLayout,
          title,
          search_tags: searchTags,
          id,
          css_class: cssClass
        } = _a3, schema = __objRest(_a3, [
          "description",
          "type",
          "is_layout",
          "title",
          "search_tags",
          "id",
          "css_class"
        ]);
        return __spreadProps(__spreadValues(__spreadValues({}, optionTypeConfig.value.componentProps || {}), schema), {
          hasChanges: !!hasChanges.value
        });
      });
      const savedOptionValue = vue.computed(() => {
        return props.schema.sync ? getTopModelValueByPath(props.compilePlaceholder(props.schema.sync)) : props.modelValue;
      });
      const hasChanges = vue.computed(() => {
        if (props.schema.is_layout) {
          const childOptionsIds = getChildOptionsIds(props.schema);
          return childOptionsIds.find((optionId) => {
            let hasDynamicValue = get(props.modelValue, `__dynamic_content__[${optionId}]`);
            return savedOptionValue.value && savedOptionValue.value[optionId] || hasDynamicValue !== void 0;
          });
        } else {
          return typeof savedOptionValue.value !== "undefined" && savedOptionValue.value !== null;
        }
      });
      const optionValue = vue.computed({
        get() {
          let value = typeof savedOptionValue.value !== "undefined" ? savedOptionValue.value : props.schema.default;
          if (props.schema.responsive_options === true) {
            let schemaDefault = props.schema.default;
            if (typeof props.schema.default === "object") {
              schemaDefault = (props.schema.default || {})[activeResponsiveMedia.value];
            }
            if (value && typeof value !== "object") {
              value = {
                default: value
              };
            }
            value = typeof (value || {})[activeResponsiveMedia.value] !== "undefined" ? (value || {})[activeResponsiveMedia.value] : schemaDefault;
          }
          if (Array.isArray(props.schema.pseudo_options)) {
            const activePseudoValue = activePseudo.value || props.schema.pseudo_options[0];
            value = typeof (value || {})[activePseudoValue] !== "undefined" ? (value || {})[activePseudoValue] : void 0;
          }
          return value;
        },
        set(newValue) {
          let valueToUpdate = newValue;
          let newValues = newValue;
          if (Array.isArray(props.schema.pseudo_options)) {
            const activePseudo2 = activePseudo2.value || props.schema.pseudo_options[0];
            let oldValues = props.modelValue;
            if (props.schema.responsive_options === true) {
              oldValues = typeof (props.modelValue || {})[activeResponsiveMedia.value] !== "undefined" ? (props.modelValue || {})[activeResponsiveMedia.value] : void 0;
              newValues = __spreadProps(__spreadValues({}, oldValues), {
                [activePseudo2]: newValue
              });
            } else {
              valueToUpdate = __spreadProps(__spreadValues({}, props.modelValue), {
                [activePseudo2]: newValues
              });
            }
          }
          if (props.schema.responsive_options === true) {
            valueToUpdate = __spreadProps(__spreadValues({}, props.modelValue), {
              [activeResponsiveMedia.value]: newValues
            });
          }
          if (props.schema.sync) {
            const syncValuePath = props.compilePlaceholder(props.schema.sync);
            if (valueToUpdate === null) {
              deleteTopModelValueByPath(syncValuePath);
            } else {
              updateTopModelValueByPath(syncValuePath, valueToUpdate);
            }
            if (panel) {
              panel.addToLocalHistory();
            }
          } else {
            if (valueToUpdate === null) {
              onDeleteOption();
            } else {
              const optionId = props.schema.is_layout ? false : props.optionId;
              emit("update:modelValue", [optionId, valueToUpdate]);
            }
          }
          if (props.schema.on_change) {
            if (props.schema.on_change === "refresh_iframe") {
              const { doAction: doAction2 } = window.zb.hooks;
              doAction2("refreshIframe");
            } else {
              window[props.schema.on_change].apply(null, [newValue]);
            }
          }
        }
      });
      const isValidInput = vue.computed(() => {
        return optionTypeConfig.value;
      });
      vue.watchEffect(() => {
        optionTypeConfig.value = vue.markRaw(getOption(props.schema, optionValue.value, modelValue.value));
      });
      function openResponsive() {
        showDevices.value = true;
      }
      function closeResponsive() {
        showDevices.value = false;
      }
      function closePseudo() {
        showPseudo.value = false;
      }
      function openPseudo() {
        showPseudo.value = true;
      }
      function activateDevice(device) {
        setActiveResponsiveDeviceId(device.id);
        setTimeout(() => {
          showDevices.value = false;
        }, 50);
      }
      function activatePseudo(selector) {
        activePseudo.value = selector;
        setTimeout(() => {
          showPseudo.value = false;
        }, 50);
      }
      function getPseudoIcon(pseudo) {
        return pseudo === "hover" ? "hover-state" : "default-state";
      }
      function onDeleteOption(optionId) {
        if (props.schema.sync) {
          let fullOptionIds = [];
          const childOptionsIds = getChildOptionsIds(props.schema, false);
          const compiledSync = props.compilePlaceholder(props.schema.sync);
          if (childOptionsIds.length > 0) {
            childOptionsIds.forEach((id) => {
              fullOptionIds.push(`${compiledSync}.${id}`);
            });
          } else {
            fullOptionIds.push(compiledSync);
          }
          deleteValues(fullOptionIds);
          deleteTopModelValueByPath(compiledSync);
        } else {
          if (props.schema.is_layout) {
            const childOptionsIds = getChildOptionsIds(props.schema);
            deleteValues(childOptionsIds);
          } else {
            optionId = optionId || props.optionId;
            deleteValueByPath(optionId);
          }
        }
      }
      function getChildOptionsIds(schema, includeSchemaId = true) {
        let ids = [];
        if (schema.type === "background") {
          const backgroundSchema = getSchema("backgroundImageSchema");
          Object.keys(backgroundSchema).forEach((optionId) => {
            const childIds = getChildOptionsIds(backgroundSchema[optionId]);
            if (childIds) {
              ids = [...ids, ...childIds, "background-color", "background-gradient", "background-video", "background-image"];
            }
          });
        } else if (schema.type === "dimensions" && typeof schema.dimensions === "object") {
          schema.dimensions.forEach((item) => {
            ids.push(item.id);
          });
        } else if (schema.type === "spacing") {
          const spacingPositions = [
            "margin-top",
            "margin-right",
            "margin-bottom",
            "margin-left",
            "padding-top",
            "padding-right",
            "padding-bottom",
            "padding-left"
          ];
          ids.push(...spacingPositions);
        } else if (schema.type === "typography") {
          const typographySchema = getSchema("typography");
          Object.keys(typographySchema).forEach((optionId) => {
            const childIds = getChildOptionsIds(typographySchema[optionId]);
            if (childIds) {
              ids = [...ids, ...childIds];
            }
          });
        } else if (schema.type === "responsive_group") {
          ids.push(activeResponsiveMedia.value);
        } else if (schema.type === "pseudo_group") {
          ids.push(activePseudo.value);
        }
        if (schema.is_layout && schema.child_options) {
          Object.keys(schema.child_options).forEach((optionId) => {
            const childIds = getChildOptionsIds(schema.child_options[optionId]);
            if (childIds) {
              ids = [...ids, ...childIds];
            }
          });
        } else if (includeSchemaId) {
          ids.push(schema.id);
        }
        return ids;
      }
      vue.provide("inputWrapper", {
        schema: props.schema,
        hasChanges,
        optionId: props.optionId,
        optionTypeConfig
      });
      return (_ctx, _cache) => {
        const _component_Icon = vue.resolveComponent("Icon");
        const _directive_znpb_tooltip = vue.resolveDirective("znpb-tooltip");
        return vue.unref(isValidInput) && (__props.schema.barebone || optionTypeConfig.value.config && optionTypeConfig.value.config.barebone) ? (vue.openBlock(), vue.createBlock(vue.resolveDynamicComponent(optionTypeConfig.value.component), vue.mergeProps({
          key: 0,
          modelValue: vue.unref(optionValue),
          "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(optionValue) ? optionValue.value = $event : null)
        }, vue.unref(compiledSchema), {
          title: __props.schema.title,
          onDiscardChanges: onDeleteOption
        }), {
          default: vue.withCtx(() => [
            __props.schema.content ? (vue.openBlock(), vue.createElementBlock(vue.Fragment, { key: 0 }, [
              vue.createTextVNode(vue.toDisplayString(__props.schema.content), 1)
            ], 64)) : vue.createCommentVNode("", true)
          ]),
          _: 1
        }, 16, ["modelValue", "title"])) : vue.unref(isValidInput) ? (vue.openBlock(), vue.createElementBlock("div", {
          key: 1,
          class: vue.normalizeClass(["znpb-input-wrapper", {
            [`znpb-input-type--${__props.schema.type}`]: true,
            [`${__props.schema.css_class}`]: __props.schema.css_class,
            [`znpb-forms-input-wrapper--${__props.schema.layout}`]: __props.schema.layout
          }]),
          style: vue.normalizeStyle(vue.unref(computedWrapperStyle))
        }, [
          __props.schema.title && vue.unref(computedShowTitle) ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_1$k, [
            vue.createElementVNode("span", {
              innerHTML: __props.schema.title
            }, null, 8, _hoisted_2$f),
            vue.unref(showChanges) && vue.unref(hasChanges) ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1G), {
              key: 0,
              content: _ctx.$translate("discard_changes"),
              onRemoveStyles: onDeleteOption
            }, null, 8, ["content"])) : vue.createCommentVNode("", true),
            __props.schema.description ? vue.withDirectives((vue.openBlock(), vue.createBlock(_component_Icon, {
              key: 1,
              icon: "question-mark",
              class: "znpb-popper-trigger znpb-popper-trigger--circle"
            }, null, 512)), [
              [_directive_znpb_tooltip, __props.schema.description]
            ]) : vue.createCommentVNode("", true),
            __props.schema.pseudo_options ? (vue.openBlock(), vue.createBlock(vue.unref(Tooltip), {
              key: 2,
              show: showPseudo.value,
              "close-on-outside-click": true,
              "show-arrows": false,
              "append-to": "element",
              trigger: null,
              onShow: openPseudo,
              onHide: closePseudo
            }, {
              content: vue.withCtx(() => [
                (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(__props.schema.pseudo_options, (pseudo_selector, index2) => {
                  return vue.openBlock(), vue.createElementBlock("div", {
                    key: index2,
                    class: "znpb-has-pseudo-options__icon-button znpb-options-devices-buttons",
                    onClick: ($event) => activatePseudo(pseudo_selector)
                  }, [
                    vue.createVNode(_component_Icon, {
                      icon: getPseudoIcon(pseudo_selector)
                    }, null, 8, ["icon"])
                  ], 8, _hoisted_3$b);
                }), 128))
              ]),
              default: vue.withCtx(() => [
                vue.createElementVNode("div", {
                  class: "znpb-has-pseudo-options__icon-button znpb-options-devices-buttons znpb-has-responsive-options__icon-button--trigger",
                  onClick: _cache[1] || (_cache[1] = ($event) => showPseudo.value = !showPseudo.value)
                }, [
                  vue.createVNode(_component_Icon, {
                    icon: getPseudoIcon(activePseudo.value)
                  }, null, 8, ["icon"])
                ])
              ]),
              _: 1
            }, 8, ["show"])) : vue.createCommentVNode("", true),
            __props.schema.responsive_options || __props.schema.show_responsive_buttons ? (vue.openBlock(), vue.createBlock(vue.unref(Tooltip), {
              key: 3,
              show: showDevices.value,
              "show-arrows": false,
              "append-to": "element",
              trigger: null,
              placement: "bottom",
              "tooltip-class": "znpb-has-responsive-options",
              "close-on-outside-click": true,
              onShow: openResponsive,
              onHide: closeResponsive
            }, {
              content: vue.withCtx(() => [
                (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(builtInResponsiveDevices2), (device, index2) => {
                  return vue.openBlock(), vue.createElementBlock("div", {
                    key: index2,
                    ref_for: true,
                    ref: "dropdown",
                    class: "znpb-options-devices-buttons znpb-has-responsive-options__icon-button",
                    onClick: ($event) => activateDevice(device)
                  }, [
                    vue.createVNode(_component_Icon, {
                      icon: device.icon
                    }, null, 8, ["icon"])
                  ], 8, _hoisted_4$7);
                }), 128))
              ]),
              default: vue.withCtx(() => [
                vue.createElementVNode("div", {
                  class: "znpb-has-responsive-options__icon-button--trigger",
                  onClick: _cache[2] || (_cache[2] = ($event) => showDevices.value = !showDevices.value)
                }, [
                  vue.createVNode(_component_Icon, {
                    icon: vue.unref(activeResponsiveDeviceInfo2).icon
                  }, null, 8, ["icon"])
                ])
              ]),
              _: 1
            }, 8, ["show"])) : vue.createCommentVNode("", true),
            vue.createVNode(vue.unref(_sfc_main$j), {
              location: "input_wrapper/end",
              class: "znpb-options-injection--after-title"
            })
          ])) : vue.createCommentVNode("", true),
          vue.createElementVNode("div", _hoisted_5$3, [
            __props.schema.itemIcon ? (vue.openBlock(), vue.createBlock(_component_Icon, {
              key: 0,
              icon: __props.schema.itemIcon
            }, null, 8, ["icon"])) : vue.createCommentVNode("", true),
            __props.schema.label || __props.schema["label-icon"] ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1A), {
              key: 1,
              label: __props.schema.label,
              align: __props.schema["label-align"],
              position: __props.schema["label-position"],
              title: __props.schema["label-title"],
              icon: __props.schema["label-icon"]
            }, {
              default: vue.withCtx(() => [
                (vue.openBlock(), vue.createBlock(vue.resolveDynamicComponent(optionTypeConfig.value.component), vue.mergeProps({
                  modelValue: vue.unref(optionValue),
                  "onUpdate:modelValue": _cache[3] || (_cache[3] = ($event) => vue.isRef(optionValue) ? optionValue.value = $event : null)
                }, vue.unref(compiledSchema)), {
                  default: vue.withCtx(() => [
                    __props.schema.content ? (vue.openBlock(), vue.createElementBlock(vue.Fragment, { key: 0 }, [
                      vue.createTextVNode(vue.toDisplayString(__props.schema.content), 1)
                    ], 64)) : vue.createCommentVNode("", true)
                  ]),
                  _: 1
                }, 16, ["modelValue"]))
              ]),
              _: 1
            }, 8, ["label", "align", "position", "title", "icon"])) : (vue.openBlock(), vue.createBlock(vue.resolveDynamicComponent(optionTypeConfig.value.component), vue.mergeProps({
              key: 2,
              modelValue: vue.unref(optionValue),
              "onUpdate:modelValue": _cache[4] || (_cache[4] = ($event) => vue.isRef(optionValue) ? optionValue.value = $event : null)
            }, vue.unref(compiledSchema)), {
              default: vue.withCtx(() => [
                __props.schema.content ? (vue.openBlock(), vue.createElementBlock(vue.Fragment, { key: 0 }, [
                  vue.createTextVNode(vue.toDisplayString(__props.schema.content), 1)
                ], 64)) : vue.createCommentVNode("", true)
              ]),
              _: 1
            }, 16, ["modelValue"]))
          ])
        ], 6)) : vue.createCommentVNode("", true);
      };
    }
  }));
  var OptionsForm_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$p = {
    name: "OptionsForm",
    components: {
      OptionWrapper: _sfc_main$q
    },
    provide() {
      return {
        showChanges: this.showChanges,
        optionsForm: this
      };
    },
    props: {
      modelValue: {},
      schema: {
        type: Object,
        required: true
      },
      showChanges: {
        required: false,
        default: true
      },
      replacements: {
        type: Array,
        required: false,
        default: () => []
      }
    },
    setup(props, { emit }) {
      let topModelValue = vue.inject("OptionsFormTopModelValue", null);
      if (null === topModelValue) {
        topModelValue = vue.computed(() => props.modelValue);
        vue.provide("OptionsFormTopModelValue", () => topModelValue);
      }
      const { activeResponsiveDeviceInfo: activeResponsiveDeviceInfo2 } = useResponsiveDevices();
      const { activePseudoSelector: activePseudoSelector2 } = usePseudoSelectors();
      function updateTopModelValueByPath(path, newValue) {
        set(topModelValue.value, path, newValue);
      }
      function deleteTopModelValueByPath(path) {
        unset(topModelValue.value, path);
        deleteNested(path, topModelValue.value);
      }
      function getTopModelValueByPath(path, defaultValue = null) {
        return get(topModelValue.value, path, defaultValue);
      }
      const getValueByPath = (path, defaultValue = null) => {
        return get(props.modelValue, path, defaultValue);
      };
      const updateValueByPath = (path, newValue) => {
        const clonedValue = cloneDeep(props.modelValue);
        set(clonedValue, path, newValue);
        emit("update:modelValue", clonedValue);
      };
      function deleteNestedEmptyObjects(paths, object) {
        paths.forEach((path) => {
          const remainingPaths = paths.slice(1, paths.length);
          if (typeof object[path] === "object") {
            object[path] = deleteNestedEmptyObjects(remainingPaths, object[path]);
            if (Object.keys(object[path]).length === 0) {
              delete object[path];
            }
          }
        });
        return object;
      }
      function deleteNested(path, model) {
        const paths = path.split(".");
        paths.pop();
        deleteNestedEmptyObjects(paths, model);
      }
      const deleteValueByPath = (path) => {
        const clonedValue = cloneDeep(props.modelValue);
        unset(clonedValue, path);
        deleteNested(path, clonedValue);
        if (Object.keys(clonedValue).length > 0) {
          emit("update:modelValue", clonedValue);
        } else {
          emit("update:modelValue", null);
        }
      };
      function deleteValues(allPaths) {
        let newValues = __spreadValues({}, props.modelValue);
        allPaths.forEach((path) => {
          const paths = path.split(".");
          paths.reduce((acc, key, index2) => {
            if (index2 === paths.length - 1) {
              let dynamicValue = get(acc, `__dynamic_content__[${key}]`);
              dynamicValue !== void 0 ? delete acc.__dynamic_content__ : delete acc[key];
              return true;
            }
            acc[key] = acc[key] ? __spreadValues({}, acc[key]) : {};
            return acc[key];
          }, newValues);
        });
        if (Object.keys(newValues).length > 0) {
          emit("update:modelValue", newValues);
        } else {
          emit("update:modelValue", null);
        }
      }
      vue.provide("OptionsForm", {
        getValueByPath,
        updateValueByPath,
        deleteValueByPath,
        getTopModelValueByPath,
        updateTopModelValueByPath,
        deleteTopModelValueByPath,
        modelValue: vue.computed(() => props.modelValue),
        deleteValues
      });
      const topOptionsForm = vue.inject("topOptionsForm", null);
      if (!topOptionsForm) {
        vue.provide(topOptionsForm, props.modelValue);
      }
      vue.provide("updateValueByPath", updateValueByPath);
      vue.provide("getValueByPath", getValueByPath);
      vue.provide("deleteValueByPath", deleteValueByPath);
      return {
        activeResponsiveDeviceInfo: activeResponsiveDeviceInfo2,
        updateValueByPath,
        getValueByPath,
        activePseudoSelector: activePseudoSelector2,
        deleteValues,
        getTopModelValueByPath
      };
    },
    computed: {
      optionsSchema() {
        const schema = {};
        Object.keys(this.schema).forEach((optionId) => {
          const optionConfig = this.getProperSchema(this.schema[optionId]);
          const { dependency } = optionConfig;
          if (!dependency) {
            schema[optionId] = optionConfig;
            return;
          }
          let conditionsMet = true;
          dependency.forEach((element) => {
            const { option, value, type, option_path: optionPath } = element;
            let optionSchema;
            let savedValue;
            if (optionPath) {
              optionSchema = this.getOptionSchemaFromPath(optionPath);
            } else {
              optionSchema = this.getOptionConfigFromId(option);
            }
            if (optionPath) {
              const defaultValue = optionSchema ? optionSchema.default : false;
              savedValue = this.getTopModelValueByPath(optionPath, defaultValue);
            } else {
              savedValue = typeof this.modelValue[option] !== "undefined" ? this.modelValue[option] : optionSchema.default;
              if (optionSchema.sync) {
                const syncValue = this.compilePlaceholder(optionSchema.sync);
                savedValue = this.getTopModelValueByPath(syncValue, savedValue);
              }
            }
            const validationType = type || "includes";
            if (conditionsMet && validationType === "includes" && value.includes(savedValue)) {
              conditionsMet = true;
            } else if (conditionsMet && validationType === "not_in" && !value.includes(savedValue)) {
              conditionsMet = true;
            } else if (conditionsMet && validationType === "value_set" && typeof savedValue !== "undefined") {
              conditionsMet = true;
            } else {
              conditionsMet = false;
            }
          });
          if (conditionsMet) {
            schema[optionId] = optionConfig;
          }
        });
        return schema;
      }
    },
    methods: {
      updateModelValueByPath(path, newValue) {
        const clonedValue = cloneDeep(this.modelValue || {});
        const newValues = set(clonedValue, path, newValue);
        this.$emit("update:modelValue", newValues);
      },
      setValue(optionId, newValue) {
        if (optionId) {
          if (newValue === null) {
            const clonedValue = __spreadValues({}, this.modelValue);
            delete clonedValue[optionId];
            if (Object.keys(clonedValue).length === 0) {
              this.$emit("update:modelValue", null);
            } else {
              this.$emit("update:modelValue", clonedValue);
            }
          } else {
            this.$emit("update:modelValue", __spreadProps(__spreadValues({}, this.modelValue), {
              [optionId]: newValue
            }));
          }
        } else {
          if (newValue === null || Object.keys(newValue).length === 0) {
            this.$emit("update:modelValue", null);
          } else {
            let clonedValue = __spreadValues({}, this.modelValue);
            Object.keys(clonedValue).reduce((acc, key, index2) => {
              if (typeof newValue[key] === "undefined") {
                delete acc[key];
              }
              return acc;
            }, clonedValue);
            this.$emit("update:modelValue", __spreadValues(__spreadValues({}, clonedValue), newValue));
          }
        }
      },
      getValue(optionSchema) {
        if (optionSchema.is_layout) {
          return this.modelValue;
        } else {
          return this.modelValue[optionSchema.id];
        }
      },
      getOptionConfigFromId(optionId) {
        if (this.schema[optionId] && !this.schema[optionId].is_layout) {
          return this.schema[optionId];
        } else {
          return this.findOptionConfig(this.schema, optionId);
        }
      },
      findOptionConfig(schema, searchId) {
        let optionConfig;
        for (let [optionId, optionConfig2] of Object.entries(schema)) {
          if (optionConfig2.is_layout && optionConfig2.child_options) {
            optionConfig2 = this.findOptionConfig(optionConfig2.child_options, searchId);
          }
          if (optionConfig2 && optionConfig2.id === searchId) {
            return optionConfig2;
          }
        }
        return optionConfig;
      },
      getOptionSchemaFromPath(optionPath) {
        const pathArray = optionPath.split(".");
        return pathArray.reduce((acc, path, index2) => {
          if (acc[path]) {
            return acc[path];
          } else {
            return false;
          }
        }, this.schema);
      },
      onOptionChange(changed) {
        this.$emit("change", changed);
      },
      getProperSchema(schema) {
        const dataSetsStore = useDataSetsStore();
        if (typeof schema.data_source !== "undefined") {
          if (schema.data_source === "fonts") {
            schema.options = dataSetsStore.fontsListForOption;
            delete schema.data_source;
          } else if (schema.data_source === "taxonomies") {
            schema.options = dataSetsStore.dataSets.taxonomies;
            delete schema.data_source;
          }
        }
        if (schema.type === "textarea") {
          schema.type = "textarea";
        }
        schema = this.compilePlaceholders(schema);
        return schema;
      },
      isIterable(schema) {
        return Array.isArray(schema) || schema === Object(schema) && typeof schema !== "function";
      },
      compilePlaceholders(schema) {
        if (!this.isIterable(schema)) {
          return this.compilePlaceholder(schema);
        } else {
          for (const prop in schema) {
            if (prop !== "sync") {
              if (schema.hasOwnProperty(prop)) {
                schema[prop] = this.compilePlaceholders(schema[prop]);
              }
            }
          }
        }
        return schema;
      },
      compilePlaceholder(value) {
        if (typeof value !== "string") {
          return value;
        }
        const replacements = [
          {
            search: /%%RESPONSIVE_DEVICE%%/g,
            replacement: this.replaceResponsiveDevice
          },
          {
            search: /%%PSEUDO_SELECTOR%%/g,
            replacement: this.replacePseudoSelector
          },
          ...this.replacements
        ];
        replacements.forEach((replacementConfig) => {
          value = value.replace(replacementConfig.search, replacementConfig.replacement);
        });
        return value;
      },
      replaceResponsiveDevice(match) {
        return this.activeResponsiveDeviceInfo.id;
      },
      replacePseudoSelector(match) {
        return this.activePseudoSelector.id;
      }
    }
  };
  const _hoisted_1$j = { class: "znpb-options-form-wrapper" };
  const _hoisted_2$e = {
    key: 0,
    class: "znpb-options-breadcrumbs-path znpb-options-breadcrumbs-path--search"
  };
  const _hoisted_3$a = ["innerHTML"];
  const _hoisted_4$6 = ["innerHTML"];
  function _sfc_render$h(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_Icon = vue.resolveComponent("Icon");
    const _component_OptionWrapper = vue.resolveComponent("OptionWrapper");
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$j, [
      (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($options.optionsSchema, (optionConfig, optionId) => {
        return vue.openBlock(), vue.createElementBlock(vue.Fragment, { key: optionId }, [
          optionConfig.breadcrumbs ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_2$e, [
            (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(optionConfig.breadcrumbs, (breadcrumb, i) => {
              return vue.openBlock(), vue.createElementBlock("div", {
                key: i,
                class: "znpb-options-breadcrumbs-path"
              }, [
                vue.createElementVNode("span", {
                  innerHTML: optionConfig.breadcrumbs[i]
                }, null, 8, _hoisted_3$a),
                i <= optionConfig.breadcrumbs.length ? (vue.openBlock(), vue.createBlock(_component_Icon, {
                  key: 0,
                  icon: "select",
                  class: "znpb-options-breadcrumbs-path-icon"
                })) : vue.createCommentVNode("", true)
              ]);
            }), 128)),
            vue.createElementVNode("span", {
              innerHTML: optionConfig.title
            }, null, 8, _hoisted_4$6)
          ])) : vue.createCommentVNode("", true),
          vue.createVNode(_component_OptionWrapper, {
            schema: optionConfig,
            "option-id": optionId,
            modelValue: optionConfig.is_layout ? $props.modelValue : $props.modelValue[optionId],
            "compile-placeholder": $options.compilePlaceholder,
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $options.setValue(...$event)),
            onChange: $options.onOptionChange
          }, null, 8, ["schema", "option-id", "modelValue", "compile-placeholder", "onChange"])
        ], 64);
      }), 128))
    ]);
  }
  var OptionsForm = /* @__PURE__ */ _export_sfc(_sfc_main$p, [["render", _sfc_render$h]]);
  var Menu_vue_vue_type_style_index_0_lang$1 = "";
  const _hoisted_1$i = { class: "znpb-menu" };
  const _hoisted_2$d = ["onClick"];
  const _hoisted_3$9 = { class: "znpb-menu-itemTitle" };
  const _hoisted_4$5 = {
    key: 1,
    class: "znpb-menu-itemAppend"
  };
  const __default__$6 = {
    name: "Menu"
  };
  const _sfc_main$o = vue.defineComponent(__spreadProps(__spreadValues({}, __default__$6), {
    props: {
      actions: null
    },
    emits: ["action"],
    setup(__props, { emit }) {
      const props = __props;
      function performAction(action) {
        action.action();
        emit("action");
      }
      const availableActions = vue.computed(() => {
        return props.actions.filter((action) => action.disabled !== false);
      });
      return (_ctx, _cache) => {
        const _component_Icon = vue.resolveComponent("Icon");
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$i, [
          (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(availableActions), (action) => {
            return vue.openBlock(), vue.createElementBlock("div", {
              key: action.title,
              class: vue.normalizeClass(["znpb-menu-item", [{ "znpb-menu-item--disabled": action.show === false }, action.cssClasses]]),
              onClick: vue.withModifiers(($event) => performAction(action), ["stop"])
            }, [
              action.icon ? (vue.openBlock(), vue.createBlock(_component_Icon, {
                key: 0,
                class: "znpb-menu-itemIcon",
                icon: action.icon
              }, null, 8, ["icon"])) : vue.createCommentVNode("", true),
              vue.createElementVNode("span", _hoisted_3$9, vue.toDisplayString(action.title), 1),
              action.append ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_4$5, vue.toDisplayString(action.append), 1)) : vue.createCommentVNode("", true)
            ], 10, _hoisted_2$d);
          }), 128))
        ]);
      };
    }
  }));
  const __default__$5 = {
    name: "HiddenMenu"
  };
  const _sfc_main$n = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$5), {
    props: {
      actions: null
    },
    setup(__props) {
      const expanded = vue.ref(false);
      return (_ctx, _cache) => {
        const _component_Icon = vue.resolveComponent("Icon");
        const _component_Tooltip = vue.resolveComponent("Tooltip");
        return vue.openBlock(), vue.createBlock(_component_Tooltip, {
          show: expanded.value,
          "onUpdate:show": _cache[2] || (_cache[2] = ($event) => expanded.value = $event),
          "tooltip-class": "hg-popper--no-padding",
          trigger: "null",
          placement: "right",
          "close-on-outside-click": true,
          "close-on-escape": true,
          class: "znpb-hiddenMenuWrapper"
        }, {
          content: vue.withCtx(() => [
            vue.createVNode(_sfc_main$o, {
              actions: __props.actions,
              onAction: _cache[0] || (_cache[0] = ($event) => expanded.value = !expanded.value)
            }, null, 8, ["actions"])
          ]),
          default: vue.withCtx(() => [
            vue.createVNode(_component_Icon, {
              icon: "more",
              "bg-size": 14,
              onClick: _cache[1] || (_cache[1] = vue.withModifiers(($event) => expanded.value = !expanded.value, ["stop"]))
            })
          ]),
          _: 1
        }, 8, ["show"]);
      };
    }
  }));
  var clickOutside = {
    install(app) {
      app.directive("click-outside", this);
    },
    beforeMount: function(el, binding, vNode) {
      const clickOutsideHandler = (event2) => {
        if (!el.contains(event2.target)) {
          binding.value.call(event2);
        }
      };
      el.__CLICK_OUTSIDE_HANDLER = clickOutsideHandler;
      document.addEventListener("contextmenu", el.__CLICK_OUTSIDE_HANDLER, true);
      document.addEventListener("click", el.__CLICK_OUTSIDE_HANDLER, true);
      document.addEventListener("touchstart", el.__CLICK_OUTSIDE_HANDLER, true);
    },
    beforeUnmount: function(el) {
      document.removeEventListener("click", el.__CLICK_OUTSIDE_HANDLER, true);
      document.removeEventListener("touchstart", el.__CLICK_OUTSIDE_HANDLER, true);
      document.removeEventListener("contextmenu", el.__CLICK_OUTSIDE_HANDLER, true);
      el.__CLICK_OUTSIDE_HANDLER = null;
    }
  };
  var InputSpacing_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$h = { class: "znpb-optSpacing" };
  const _hoisted_2$c = ["onMouseenter", "onMousedown"];
  const _hoisted_3$8 = { class: "znpb-optSpacing-labelWrapper" };
  const _hoisted_4$4 = { class: "znpb-optSpacing-label" };
  const _hoisted_5$2 = { class: "znpb-optSpacing-svg" };
  const _hoisted_6$2 = {
    xmlns: "http://www.w3.org/2000/svg",
    fill: "none",
    viewBox: "0 0 320 186"
  };
  const _hoisted_7$1 = ["cursor", "d", "onMouseenter", "onMousedown"];
  const _hoisted_8$1 = ["onMouseenter", "onMousedown"];
  const _hoisted_9$1 = { class: "znpb-optSpacing-labelWrapper" };
  const _hoisted_10$1 = { class: "znpb-optSpacing-label" };
  const _hoisted_11$1 = { class: "znpb-optSpacing-svg" };
  const _hoisted_12 = {
    xmlns: "http://www.w3.org/2000/svg",
    fill: "none",
    viewBox: "0 0 214 108"
  };
  const _hoisted_13 = ["cursor", "d", "onMouseenter", "onMousedown"];
  const _hoisted_14 = {
    key: 0,
    class: "znpb-optSpacing-info"
  };
  const __default__$4 = {
    name: "InputSpacing",
    directives: {
      clickOutside
    }
  };
  const _sfc_main$m = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$4), {
    props: {
      modelValue: { default: () => {
        return {};
      } },
      placeholder: { default: () => {
        return {};
      } }
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const marginPositionId = [
        {
          position: "margin-top",
          type: "margin",
          title: translate("margin-top"),
          svg: {
            cursor: "n-resize",
            d: "M0 0h320l-50 36H50L0 0Z"
          },
          dragDirection: "vertical"
        },
        {
          position: "margin-right",
          type: "margin",
          title: translate("margin-right"),
          svg: {
            cursor: "e-resize",
            d: "m320 183-50-36V39l50-36v180Z"
          },
          dragDirection: "horizontal"
        },
        {
          position: "margin-bottom",
          type: "margin",
          title: translate("margin-bottom"),
          svg: {
            cursor: "s-resize",
            d: "M50 150h220l50 36H0l50-36Z"
          },
          dragDirection: "vertical"
        },
        {
          position: "margin-left",
          type: "margin",
          title: translate("margin-left"),
          svg: {
            cursor: "w-resize",
            d: "m0 3 50 36v108L0 183V3Z"
          },
          dragDirection: "horizontal"
        }
      ];
      const paddingPositionId = [
        {
          position: "padding-top",
          type: "padding",
          title: translate("padding-top"),
          svg: {
            cursor: "n-resize",
            d: "M0 0h214l-50 36H50L0 0Z"
          },
          dragDirection: "vertical"
        },
        {
          position: "padding-right",
          type: "padding",
          title: translate("padding-right"),
          svg: {
            cursor: "e-resize",
            d: "m214 105-50-36V39l50-36v102Z"
          },
          dragDirection: "horizontal"
        },
        {
          position: "padding-bottom",
          type: "padding",
          title: translate("padding-bottom"),
          svg: {
            cursor: "s-resize",
            d: "M214 108H0l50-36h114l50 36Z"
          },
          dragDirection: "vertical"
        },
        {
          position: "padding-left",
          type: "padding",
          title: translate("padding-left"),
          svg: {
            cursor: "w-resize",
            d: "m0 3 50 36v30L0 105V3Z"
          },
          dragDirection: "horizontal"
        }
      ];
      const allowedValues = [...marginPositionId, ...paddingPositionId].map((position) => position.position);
      const activeHover = vue.ref(null);
      const lastChanged = vue.ref(null);
      vue.ref(null);
      function onDiscardChanges(position) {
        const clonedModelValue = __spreadValues({}, props.modelValue);
        delete clonedModelValue[position];
        emit("update:modelValue", clonedModelValue);
      }
      const computedValues = vue.computed({
        get() {
          const values = {};
          Object.keys(props.modelValue).forEach((optionId) => {
            if (allowedValues.includes(optionId)) {
              values[optionId] = props.modelValue[optionId];
            }
          });
          return values;
        },
        set(newValues) {
          emit("update:modelValue", newValues);
        }
      });
      function onValueUpdated(sizePosition, type, newValue) {
        const isLinked2 = type === "margin" ? linkedMargin : linkedPadding;
        lastChanged.value = {
          position: sizePosition,
          type
        };
        if (isLinked2.value) {
          const valuesToUpdate = type === "margin" ? marginPositionId : paddingPositionId;
          const updatedValues = {};
          valuesToUpdate.forEach((position) => updatedValues[position.position] = newValue);
          computedValues.value = __spreadValues(__spreadValues({}, props.modelValue), updatedValues);
        } else {
          computedValues.value = __spreadProps(__spreadValues({}, props.modelValue), {
            [sizePosition]: newValue
          });
        }
      }
      const linkedMargin = vue.ref(isLinked("margin"));
      const linkedPadding = vue.ref(isLinked("padding"));
      function linkValues(type) {
        const valueToChange = type === "margin" ? linkedMargin : linkedPadding;
        valueToChange.value = !valueToChange.value;
        if (valueToChange.value) {
          if (lastChanged.value && lastChanged.value.type === type) {
            onValueUpdated(lastChanged.value.position, type, computedValues.value[lastChanged.value.position]);
          } else {
            const valuesToCheck = type === "margin" ? marginPositionId : paddingPositionId;
            const savedValueConfig = valuesToCheck.find(
              (positionConfig) => computedValues.value[positionConfig.position] !== "undefined"
            );
            if (savedValueConfig) {
              onValueUpdated(savedValueConfig.position, type, computedValues.value[savedValueConfig.position]);
            }
          }
        }
      }
      function isLinked(type) {
        const valuesToCheck = type === "margin" ? marginPositionId : paddingPositionId;
        return valuesToCheck.every((position) => {
          return computedValues.value[position.position] && computedValues.value[position.position] === computedValues.value[`${type}-top`];
        });
      }
      let startMousePosition;
      let dragDirection;
      let initialValue;
      let draggingConfig;
      const dragThreshold = 0;
      const validUnits = [
        {
          type: "px",
          isModifiable: true
        },
        {
          type: "%",
          isModifiable: true
        },
        {
          type: "vw",
          isModifiable: true
        },
        {
          type: "vh",
          isModifiable: true
        },
        {
          type: "rem",
          isModifiable: true
        },
        {
          type: "em",
          isModifiable: true
        },
        {
          type: "pt",
          isModifiable: true
        },
        {
          type: "auto",
          isModifiable: false
        },
        {
          type: "initial",
          isModifiable: false
        },
        {
          type: "unset",
          isModifiable: false
        }
      ];
      const isDragging = vue.ref(false);
      function startDragging(event2, positionConfig) {
        const { clientY, clientX } = event2;
        startMousePosition = {
          clientY,
          clientX
        };
        dragDirection = positionConfig.dragDirection;
        const { position, type } = positionConfig;
        document.body.style.userSelect = "none";
        initialValue = getSplitValue(position);
        const unit = initialValue && initialValue.unit;
        const validUnit = validUnits.find((singleUnit) => singleUnit.type === unit);
        if (validUnit && validUnit.isModifiable) {
          const linkType = type === "margin" ? linkedMargin : linkedPadding;
          draggingConfig = {
            positionConfig,
            position,
            type,
            initialValue,
            activeLinkStatus: linkType.value,
            activeLinkComputedValue: linkType
          };
          window.addEventListener("mousemove", rafDragValue);
          window.addEventListener("mouseup", rafDeactivateDragging);
          window.addEventListener("keydown", onKeyDown);
          window.addEventListener("keyup", onKeyUp);
        }
      }
      function getSplitValue(position) {
        const savedValue = computedValues.value[position] ? computedValues.value[position] : "0px";
        const splitValue = savedValue.match(/^([+-]?(?:\d+|\d*\.\d+))([a-z]*|%)$/);
        if (!splitValue) {
          return null;
        }
        return {
          value: parseInt(splitValue[1]),
          unit: splitValue[2]
        };
      }
      function onKeyDown(event2) {
        if (isDragging.value) {
          const { activeLinkStatus, activeLinkComputedValue } = draggingConfig;
          if (!activeLinkStatus && event2.ctrlKey) {
            activeLinkComputedValue.value = true;
          } else {
            activeLinkComputedValue.value = activeLinkStatus;
          }
        }
      }
      function onKeyUp(event2) {
        if (isDragging.value) {
          const { activeLinkComputedValue } = draggingConfig;
          if (event2.ctrlKey) {
            activeLinkComputedValue.value = false;
          }
        }
      }
      function deactivateDragging() {
        document.body.style.userSelect = "";
        document.body.style.pointerEvents = "";
        rafDragValue.cancel();
        window.removeEventListener("mousemove", rafDragValue);
        window.removeEventListener("mouseup", rafDeactivateDragging);
        isDragging.value = false;
        startMousePosition = null;
        initialValue = null;
        draggingConfig = null;
      }
      function dragValue(event2) {
        const { clientX, clientY } = event2;
        document.body.style.pointerEvents = "none";
        const movedAmount = dragDirection === "vertical" ? Math.ceil(startMousePosition.clientY - clientY) : Math.ceil(startMousePosition.clientX - clientX) * -1;
        if (Math.abs(movedAmount) > dragThreshold) {
          const positionConfig = draggingConfig ? draggingConfig.positionConfig : null;
          isDragging.value = true;
          activeHover.value = positionConfig;
          setDraggingValue(movedAmount - dragThreshold, event2);
        }
      }
      function setDraggingValue(newValue, event2) {
        const { position, type, initialValue: initialValue2 } = draggingConfig;
        if (!initialValue2)
          return;
        const { value, unit } = initialValue2;
        let updatedValue = newValue + value;
        if (event2.shiftKey) {
          updatedValue = Math.round(updatedValue / 5) * 5;
        }
        const valueToUpdate = `${updatedValue}${unit}`;
        onValueUpdated(position, type, valueToUpdate);
      }
      const rafDragValue = rafSchd$1(dragValue);
      const rafDeactivateDragging = rafSchd$1(deactivateDragging);
      return (_ctx, _cache) => {
        const _component_ChangesBullet = vue.resolveComponent("ChangesBullet");
        const _component_Icon = vue.resolveComponent("Icon");
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$h, [
          vue.createElementVNode("div", {
            class: vue.normalizeClass(["znpb-optSpacing-margin", {
              "znpb-optSpacing--linked": linkedMargin.value,
              "znpb-optSpacing--hover": activeHover.value && activeHover.value.position.includes("margin"),
              [`znpb-optSpacing--hover-${activeHover.value ? activeHover.value.position : ""}`]: activeHover.value
            }])
          }, [
            (vue.openBlock(), vue.createElementBlock(vue.Fragment, null, vue.renderList(marginPositionId, (position) => {
              return vue.createElementVNode("div", {
                key: position.position,
                class: vue.normalizeClass([{
                  [`znpb-optSpacing-${position.position}`]: true
                }, "znpb-optSpacing-value znpb-optSpacing-value--margin"]),
                onMouseenter: ($event) => activeHover.value = position,
                onMouseleave: _cache[0] || (_cache[0] = ($event) => activeHover.value = null),
                onMousedown: ($event) => startDragging($event, position)
              }, [
                vue.createVNode(vue.unref(_sfc_main$1B), {
                  "model-value": vue.unref(computedValues)[position.position],
                  units: ["px", "rem", "pt", "vh", "%"],
                  step: 1,
                  "default-unit": "px",
                  placeholder: __props.placeholder && typeof __props.placeholder[position.position] !== "undefined" ? __props.placeholder[position.position] : "-",
                  "onUpdate:modelValue": ($event) => onValueUpdated(position.position, "margin", $event)
                }, null, 8, ["model-value", "placeholder", "onUpdate:modelValue"]),
                vue.unref(computedValues)[position.position] ? (vue.openBlock(), vue.createBlock(_component_ChangesBullet, {
                  key: 0,
                  content: vue.unref(translate)("discard_changes"),
                  onRemoveStyles: ($event) => onDiscardChanges(position.position)
                }, null, 8, ["content", "onRemoveStyles"])) : vue.createCommentVNode("", true)
              ], 42, _hoisted_2$c);
            }), 64)),
            vue.createElementVNode("div", _hoisted_3$8, [
              vue.createElementVNode("span", _hoisted_4$4, vue.toDisplayString(vue.unref(translate)("margin")), 1),
              vue.createVNode(_component_Icon, {
                icon: linkedMargin.value ? "link" : "unlink",
                title: linkedMargin.value ? vue.unref(translate)("unlink") : vue.unref(translate)("link"),
                size: 12,
                class: vue.normalizeClass(["znpb-optSpacing-link", {
                  "znpb-optSpacing-link--linked": linkedMargin.value
                }]),
                onClick: _cache[1] || (_cache[1] = ($event) => linkValues("margin"))
              }, null, 8, ["icon", "title", "class"])
            ]),
            vue.createElementVNode("div", _hoisted_5$2, [
              (vue.openBlock(), vue.createElementBlock("svg", _hoisted_6$2, [
                (vue.openBlock(), vue.createElementBlock(vue.Fragment, null, vue.renderList(marginPositionId, (position) => {
                  return vue.createElementVNode("path", {
                    key: position.position,
                    cursor: position.svg.cursor,
                    d: position.svg.d,
                    class: vue.normalizeClass({
                      [`znpb-optSpacing--path-${position.position}`]: true
                    }),
                    onMouseenter: ($event) => activeHover.value = position,
                    onMouseleave: _cache[2] || (_cache[2] = ($event) => activeHover.value = null),
                    onMousedown: ($event) => startDragging($event, position)
                  }, null, 42, _hoisted_7$1);
                }), 64))
              ]))
            ])
          ], 2),
          vue.createElementVNode("div", {
            class: vue.normalizeClass(["znpb-optSpacing-padding", {
              "znpb-optSpacing--linked": linkedPadding.value,
              "znpb-optSpacing--hover": activeHover.value && activeHover.value.position.includes("padding"),
              [`znpb-optSpacing--hover-${activeHover.value ? activeHover.value.position : ""}`]: activeHover.value
            }])
          }, [
            (vue.openBlock(), vue.createElementBlock(vue.Fragment, null, vue.renderList(paddingPositionId, (position) => {
              return vue.createElementVNode("div", {
                key: position.position,
                class: vue.normalizeClass([{
                  [`znpb-optSpacing-${position.position}`]: true
                }, "znpb-optSpacing-value znpb-optSpacing-value--padding"]),
                onMouseenter: ($event) => activeHover.value = position,
                onMouseleave: _cache[3] || (_cache[3] = ($event) => activeHover.value = null),
                onMousedown: ($event) => startDragging($event, position)
              }, [
                vue.createVNode(vue.unref(_sfc_main$1B), {
                  "model-value": vue.unref(computedValues)[position.position],
                  units: ["px", "rem", "pt", "vh", "%"],
                  step: 1,
                  "default-unit": "px",
                  min: 0,
                  placeholder: __props.placeholder && typeof __props.placeholder[position.position] !== "undefined" ? __props.placeholder[position.position] : "-",
                  "onUpdate:modelValue": ($event) => onValueUpdated(position.position, "padding", $event)
                }, null, 8, ["model-value", "placeholder", "onUpdate:modelValue"]),
                vue.unref(computedValues)[position.position] ? (vue.openBlock(), vue.createBlock(_component_ChangesBullet, {
                  key: 0,
                  content: vue.unref(translate)("discard_changes"),
                  onRemoveStyles: ($event) => onDiscardChanges(position.position)
                }, null, 8, ["content", "onRemoveStyles"])) : vue.createCommentVNode("", true)
              ], 42, _hoisted_8$1);
            }), 64)),
            vue.createElementVNode("div", _hoisted_9$1, [
              vue.createElementVNode("span", _hoisted_10$1, vue.toDisplayString(vue.unref(translate)("padding")), 1),
              vue.createVNode(_component_Icon, {
                icon: linkedPadding.value ? "link" : "unlink",
                title: linkedPadding.value ? vue.unref(translate)("unlink") : vue.unref(translate)("link"),
                size: 12,
                class: vue.normalizeClass(["znpb-optSpacing-link", {
                  "znpb-optSpacing-link--linked": linkedPadding.value
                }]),
                onClick: _cache[4] || (_cache[4] = ($event) => linkValues("padding"))
              }, null, 8, ["icon", "title", "class"])
            ]),
            vue.createElementVNode("div", _hoisted_11$1, [
              (vue.openBlock(), vue.createElementBlock("svg", _hoisted_12, [
                (vue.openBlock(), vue.createElementBlock(vue.Fragment, null, vue.renderList(paddingPositionId, (position) => {
                  return vue.createElementVNode("path", {
                    key: position.position,
                    cursor: position.svg.cursor,
                    d: position.svg.d,
                    class: vue.normalizeClass({
                      [`znpb-optSpacing--path-${position.position}`]: true
                    }),
                    onMouseenter: ($event) => activeHover.value = position,
                    onMouseleave: _cache[5] || (_cache[5] = ($event) => activeHover.value = null),
                    onMousedown: ($event) => startDragging($event, position)
                  }, null, 42, _hoisted_13);
                }), 64))
              ]))
            ])
          ], 2),
          activeHover.value ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_14, vue.toDisplayString(activeHover.value.title), 1)) : vue.createCommentVNode("", true)
        ]);
      };
    }
  }));
  var InputDimensions_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$g = { class: "znpb-dimensions-wrapper" };
  const _hoisted_2$b = {
    key: 0,
    class: "znpb-dimensions_icon"
  };
  const _hoisted_3$7 = {
    key: 2,
    class: "znpb-dimensions__center"
  };
  const __default__$3 = {
    name: "InputDimensions"
  };
  const _sfc_main$l = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$3), {
    props: {
      modelValue: { default() {
        return {};
      } },
      dimensions: null,
      min: { default: 0 },
      max: { default: Infinity },
      placeholder: { default() {
        return {};
      } }
    },
    emits: ["update:modelValue"],
    setup(__props, { emit }) {
      const props = __props;
      const linked = vue.ref(false);
      const computedDimensions = vue.computed(() => {
        return [
          ...props.dimensions,
          {
            name: "link",
            id: "link"
          }
        ];
      });
      function handleLinkValues() {
        linked.value = !linked.value;
        if (linked.value) {
          const dimensionsIDs = props.dimensions.map((dimension) => dimension.id);
          const savedPositionValue = Object.keys(props.modelValue).find(
            (position) => dimensionsIDs.includes(position) && typeof props.modelValue[position] !== "undefined"
          );
          if (savedPositionValue) {
            onValueUpdated("", props.modelValue[savedPositionValue]);
          }
        }
      }
      function onValueUpdated(position, newValue) {
        if (linked.value) {
          const valuesToUpdate = props.dimensions.filter((dimension) => {
            return dimension.id !== "link";
          });
          let values = {};
          valuesToUpdate.forEach((value) => {
            values[value.id] = newValue;
          });
          emit("update:modelValue", __spreadValues(__spreadValues({}, props.modelValue), values));
        } else {
          emit("update:modelValue", __spreadProps(__spreadValues({}, props.modelValue), {
            [position]: newValue
          }));
        }
      }
      return (_ctx, _cache) => {
        const _component_Icon = vue.resolveComponent("Icon");
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$g, [
          (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(computedDimensions), (dimension, i) => {
            return vue.openBlock(), vue.createElementBlock("div", {
              key: i,
              class: vue.normalizeClass(["znpb-dimension", `znpb-dimension--${i}`])
            }, [
              dimension.name !== "link" ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_2$b, [
                vue.createVNode(_component_Icon, {
                  icon: dimension.icon
                }, null, 8, ["icon"])
              ])) : vue.createCommentVNode("", true),
              dimension.name !== "link" ? (vue.openBlock(), vue.createBlock(vue.unref(_sfc_main$1B), {
                key: 1,
                "model-value": __props.modelValue[dimension.id],
                title: dimension.id,
                min: __props.min,
                max: __props.max,
                default_unit: "px",
                step: 1,
                "default-unit": "px",
                placeholder: __props.placeholder ? __props.placeholder[dimension.id] : "",
                "onUpdate:modelValue": ($event) => onValueUpdated(dimension.id, $event),
                onLinkedValue: handleLinkValues
              }, null, 8, ["model-value", "title", "min", "max", "placeholder", "onUpdate:modelValue"])) : vue.createCommentVNode("", true),
              dimension.name === "link" ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_3$7, [
                vue.createVNode(_component_Icon, {
                  icon: linked.value ? "link" : "unlink",
                  title: linked.value ? "Unlink" : "Link",
                  class: vue.normalizeClass(["znpb-dimensions__link", { ["znpb-dimensions__link--linked"]: linked.value }]),
                  onClick: handleLinkValues
                }, null, 8, ["icon", "title", "class"])
              ])) : vue.createCommentVNode("", true)
            ], 2);
          }), 128))
        ]);
      };
    }
  }));
  const _hoisted_1$f = ["innerHTML"];
  const __default__$2 = {
    name: "HTML"
  };
  const _sfc_main$k = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$2), {
    props: {
      content: { default: "" }
    },
    setup(__props) {
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          class: "znpb-option__html",
          innerHTML: __props.content
        }, null, 8, _hoisted_1$f);
      };
    }
  }));
  const options = [
    {
      id: "text",
      component: _sfc_main$1D,
      dynamic: {
        type: "TEXT"
      }
    },
    {
      id: "icon_library",
      component: _sfc_main$11,
      config: {
        barebone: true
      }
    },
    {
      id: "textarea",
      component: _sfc_main$1D,
      componentProps: {
        type: "textarea"
      },
      dynamic: {
        type: "TEXT"
      }
    },
    {
      id: "password",
      componentProps: {
        type: "password"
      },
      component: _sfc_main$1D
    },
    {
      id: "select",
      component: InputSelect
    },
    {
      id: "slider",
      component: _sfc_main$1j
    },
    {
      id: "dynamic_slider",
      component: _sfc_main$1i
    },
    {
      id: "editor",
      component: _sfc_main$I,
      dynamic: {
        type: "TEXT"
      }
    },
    {
      id: "media",
      component: _sfc_main$H
    },
    {
      id: "file",
      component: _sfc_main$G
    },
    {
      id: "image",
      component: _sfc_main$_
    },
    {
      id: "number",
      component: _sfc_main$1C
    },
    {
      id: "number_unit",
      component: _sfc_main$1B
    },
    {
      id: "code",
      component: _sfc_main$Q
    },
    {
      id: "custom_selector",
      component: _sfc_main$L
    },
    {
      id: "colorpicker",
      component: _sfc_main$M,
      dynamic: {
        type: "TYPE_HIDDEN",
        custom_dynamic: true
      }
    },
    {
      id: "checkbox",
      component: _sfc_main$T
    },
    {
      id: "radio_image",
      component: _sfc_main$v
    },
    {
      id: "checkbox_group",
      component: _sfc_main$S
    },
    {
      id: "checkbox_switch",
      component: InputCheckboxSwitch
    },
    {
      id: "text_align",
      component: _sfc_main$x
    },
    {
      id: "borders",
      component: _sfc_main$W
    },
    {
      id: "shadow",
      component: _sfc_main$w
    },
    {
      id: "video",
      component: _sfc_main$Y
    },
    {
      id: "date_input",
      component: InputDatePicker
    },
    {
      id: "shape_dividers",
      component: _sfc_main$B
    },
    {
      id: "shape_component",
      component: _sfc_main$y
    },
    {
      id: "spacing",
      component: _sfc_main$m
    },
    {
      id: "repeater",
      component: _sfc_main$t
    },
    {
      id: "upgrade_to_pro",
      component: _sfc_main$z
    },
    {
      id: "dimensions",
      component: _sfc_main$l
    },
    {
      id: "html",
      component: _sfc_main$k
    }
  ];
  const useOptions = () => {
    const { applyFilters: applyFilters2 } = window.zb.hooks;
    const getOption = (schema, model = null, formModel = {}) => {
      let optionConfig = options.find((option) => option.id === schema.type);
      optionConfig = applyFilters2("zionbuilder/getOptionConfig", optionConfig, schema, model, formModel);
      if (!optionConfig) {
        console.warn(
          `Option type ${schema.type} not found. Please register the option type using ZionBuilderApi.options.registerOption!`
        );
        return null;
      }
      return optionConfig;
    };
    const getOptionComponent = (schema, model = null, formModel = {}) => {
      const optionConfig = getOption(schema.type);
      return applyFilters2("zionbuilder/getOption", optionConfig == null ? void 0 : optionConfig.component, schema, model, formModel);
    };
    const registerOption = (optionConfig) => {
      if (!Object.prototype.hasOwnProperty.call(optionConfig, "id")) {
        console.warn("You need to specify the option type id.", optionConfig);
      }
      if (!Object.prototype.hasOwnProperty.call(optionConfig, "component")) {
        console.warn("You need to specify the option type id.", optionConfig);
      }
      options.push(optionConfig);
    };
    return {
      registerOption,
      getOptionComponent,
      getOption
    };
  };
  const deviceSizesConfig = [
    {
      width: 992,
      icon: "laptop"
    },
    {
      width: 768,
      icon: "tablet"
    },
    {
      width: 575,
      icon: "mobile"
    }
  ];
  const activeResponsiveDeviceId = vue.ref("default");
  const responsiveDevices = vue.ref(window.ZnPbComponentsData.breakpoints);
  const activeResponsiveOptions = vue.ref(null);
  const iframeWidth = vue.ref(0);
  const autoScaleActive = vue.ref(true);
  const scaleValue = vue.ref(100);
  const ignoreWidthChangeFlag = vue.ref(false);
  const orderedResponsiveDevices = vue.computed(() => {
    return orderBy(responsiveDevices.value, ["width"], ["desc"]);
  });
  const responsiveDevicesAsIdWidth = vue.computed(() => {
    const devices = {};
    orderedResponsiveDevices.value.forEach((deviceConfig) => {
      devices[deviceConfig.id] = deviceConfig.width;
    });
    return devices;
  });
  const activeResponsiveDeviceInfo = vue.computed(
    () => responsiveDevices.value.find((device) => device.id === activeResponsiveDeviceId.value) || responsiveDevices.value[0]
  );
  const builtInResponsiveDevices = vue.computed(
    () => responsiveDevices.value.filter((deviceConfig) => deviceConfig.builtIn === true)
  );
  const mobileFirstResponsiveDevices = vue.computed(() => {
    const newDevices = {};
    let lastDeviceWidth = 0;
    const sortedDevices = Object.entries(responsiveDevicesAsIdWidth.value).sort((a, b) => a[1] > b[1] ? 1 : -1).reduce((acc, pair) => {
      acc[pair[0]] = pair[1];
      return acc;
    }, {});
    for (const [deviceId, deviceWidth] of Object.entries(sortedDevices)) {
      if (deviceId === "mobile") {
        newDevices[deviceId] = 0;
      } else {
        newDevices[deviceId] = lastDeviceWidth + 1;
      }
      if (deviceWidth) {
        lastDeviceWidth = deviceWidth;
      }
    }
    return newDevices;
  });
  const useResponsiveDevices = () => {
    function setActiveResponsiveDeviceId(device) {
      activeResponsiveDeviceId.value = device;
    }
    function setAutoScale(scaleEnabled) {
      autoScaleActive.value = scaleEnabled;
      if (scaleEnabled) {
        scaleValue.value = 100;
      }
    }
    function setCustomScale(newValue) {
      scaleValue.value = newValue;
    }
    function setActiveResponsiveOptions(instanceConfig) {
      activeResponsiveOptions.value = instanceConfig;
    }
    function getActiveResponsiveOptions() {
      return activeResponsiveOptions.value;
    }
    function removeActiveResponsiveOptions() {
      activeResponsiveOptions.value = null;
    }
    function updateBreakpoint(device, newWidth) {
      return __async(this, null, function* () {
        const editedDevice = responsiveDevices.value.find((deviceData) => deviceData === device);
        if (editedDevice && editedDevice.width !== newWidth) {
          editedDevice.width = newWidth;
          yield saveDevices();
          const AssetsStore = useAssetsStore();
          yield AssetsStore.regenerateCache();
        }
      });
    }
    function saveDevices() {
      return saveBreakpoints(responsiveDevices.value);
    }
    function setCustomIframeWidth(newWidth, changeDevice = false) {
      const actualWidth = newWidth < 240 ? 240 : newWidth;
      if (newWidth && changeDevice) {
        let activeDevice = "default";
        responsiveDevices.value.forEach((device) => {
          if (device.width && device.width >= actualWidth) {
            activeDevice = device.id;
          }
        });
        if (activeDevice && activeDevice !== activeResponsiveDeviceId.value) {
          ignoreWidthChangeFlag.value = true;
          setActiveResponsiveDeviceId(activeDevice);
        }
      }
      iframeWidth.value = actualWidth;
    }
    function addCustomBreakpoint(breakPoint) {
      const { width, icon = "desktop" } = breakPoint;
      const newDeviceData = {
        width,
        icon,
        isCustom: true,
        id: generateUID()
      };
      responsiveDevices.value.push(newDeviceData);
      return newDeviceData;
    }
    function deleteBreakpoint(breakpointID) {
      return __async(this, null, function* () {
        const deviceConfig = responsiveDevices.value.find((deviceConfig2) => deviceConfig2.id === breakpointID);
        if (deviceConfig) {
          const index2 = responsiveDevices.value.indexOf(deviceConfig);
          responsiveDevices.value.splice(index2, 1);
          yield saveDevices();
          const AssetsStore = useAssetsStore();
          yield AssetsStore.regenerateCache();
        }
      });
    }
    return {
      ignoreWidthChangeFlag,
      activeResponsiveDeviceId,
      activeResponsiveDeviceInfo,
      responsiveDevices,
      iframeWidth,
      autoScaleActive,
      scaleValue: vue.readonly(scaleValue),
      setActiveResponsiveDeviceId,
      removeActiveResponsiveOptions,
      getActiveResponsiveOptions,
      setActiveResponsiveOptions,
      setCustomIframeWidth,
      setCustomScale,
      setAutoScale,
      addCustomBreakpoint,
      deleteBreakpoint,
      updateBreakpoint,
      saveDevices,
      mobileFirstResponsiveDevices,
      deviceSizesConfig,
      responsiveDevicesAsIdWidth,
      orderedResponsiveDevices,
      builtInResponsiveDevices
    };
  };
  const pseudoSelectors = vue.ref([
    {
      name: "default",
      id: "default"
    },
    {
      name: ":hover",
      id: ":hover"
    },
    {
      name: ":before",
      id: ":before"
    },
    {
      name: ":after",
      id: ":after"
    },
    {
      name: ":active",
      id: ":active"
    },
    {
      name: ":focus",
      id: ":focus"
    },
    {
      name: ":custom",
      id: "custom"
    }
  ]);
  const activePseudoSelector = vue.ref(pseudoSelectors.value[0]);
  const usePseudoSelectors = () => {
    function setActivePseudoSelector(value) {
      activePseudoSelector.value = value || pseudoSelectors.value[0];
    }
    function deleteCustomSelector(selector) {
      const selectorIndex = pseudoSelectors.value.indexOf(selector);
      if (selectorIndex !== -1) {
        pseudoSelectors.value.splice(selectorIndex, 1);
        activePseudoSelector.value = pseudoSelectors.value[0];
      }
    }
    function addCustomSelector(selector) {
      pseudoSelectors.value.push(selector);
    }
    return {
      activePseudoSelector,
      pseudoSelectors,
      addCustomSelector,
      setActivePseudoSelector,
      deleteCustomSelector
    };
  };
  const __default__$1 = {
    name: "Injection",
    inheritAttrs: false
  };
  const _sfc_main$j = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__$1), {
    props: {
      location: null,
      htmlTag: { default: "div" }
    },
    setup(__props) {
      const props = __props;
      const { getComponentsForLocation } = useInjections();
      const computedComponents = vue.computed(() => getComponentsForLocation(props.location));
      return (_ctx, _cache) => {
        return vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(computedComponents), (customComponent, i) => {
          return vue.openBlock(), vue.createBlock(vue.resolveDynamicComponent(customComponent), { key: i });
        }), 128);
      };
    }
  }));
  var IconPackGrid_vue_vue_type_style_index_0_lang = "";
  const _hoisted_1$e = { class: "znpb-icon-pack-modal__icons" };
  const _hoisted_2$a = {
    key: 0,
    class: "znpb-icon-pack-modal__grid"
  };
  const _hoisted_3$6 = ["onClick", "onDblclick"];
  const _hoisted_4$3 = ["data-znpbiconfam", "data-znpbicon"];
  const _hoisted_5$1 = { class: "znpb-modal-icon-wrapper__title" };
  const _hoisted_6$1 = { key: 1 };
  const __default__ = {
    name: "IconPackGrid"
  };
  const _sfc_main$i = /* @__PURE__ */ vue.defineComponent(__spreadProps(__spreadValues({}, __default__), {
    props: {
      iconList: null,
      family: null,
      activeIcon: null,
      activeFamily: null
    },
    emits: ["icon-selected", "update:modelValue"],
    setup(__props) {
      function unicode(unicode2) {
        return JSON.parse('"\\' + unicode2 + '"');
      }
      return (_ctx, _cache) => {
        return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$e, [
          __props.iconList.length > 0 ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_2$a, [
            (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(__props.iconList, (icon, i) => {
              return vue.openBlock(), vue.createElementBlock("div", {
                key: i,
                class: "znpb-icon-pack-modal-icon"
              }, [
                vue.createElementVNode("div", {
                  class: vue.normalizeClass(["znpb-modal-icon-wrapper", { "znpb-modal-icon-wrapper--active": __props.activeIcon === icon.name && __props.activeFamily === __props.family }]),
                  onClick: ($event) => _ctx.$emit("icon-selected", icon),
                  onDblclick: ($event) => _ctx.$emit("update:modelValue", icon)
                }, [
                  vue.createElementVNode("span", {
                    "data-znpbiconfam": __props.family,
                    "data-znpbicon": unicode(icon.unicode)
                  }, null, 8, _hoisted_4$3)
                ], 42, _hoisted_3$6),
                vue.createElementVNode("h4", _hoisted_5$1, vue.toDisplayString(icon.name), 1)
              ]);
            }), 128))
          ])) : (vue.openBlock(), vue.createElementBlock("span", _hoisted_6$1, vue.toDisplayString(_ctx.$translate("no_icons_in_package")) + " " + vue.toDisplayString(__props.family), 1))
        ]);
      };
    }
  }));
  var Modal_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$h = {
    name: "Modal",
    components: {
      Icon: _sfc_main$1K
    },
    props: {
      show: {
        type: Boolean,
        required: false,
        default: false
      },
      title: {
        type: String,
        required: false,
        default: ""
      },
      width: {
        type: Number,
        required: false
      },
      fullscreen: {
        type: Boolean,
        required: false,
        default: false
      },
      appendTo: {
        type: String,
        required: false
      },
      closeOnClick: {
        type: Boolean,
        required: false,
        default: true
      },
      closeOnEscape: {
        type: Boolean,
        required: false,
        default: true
      },
      showClose: {
        type: Boolean,
        required: false,
        default: true
      },
      showMaximize: {
        type: Boolean,
        required: false,
        default: true
      },
      showBackdrop: {
        type: Boolean,
        required: false,
        default: true
      },
      position: {
        type: Object,
        required: false,
        default: null
      },
      enableDrag: {
        type: Boolean,
        required: false,
        default: true
      }
    },
    data: function() {
      return {
        fullSize: this.fullscreen,
        bg: this.showBackdrop,
        hasHeader: false,
        zIndex: null,
        initialPosition: {}
      };
    },
    computed: {
      modalStyle() {
        return {
          zIndex: this.zIndex,
          left: this.position === null || this.position.left + 60 > window.innerWidth || this.topPos === null ? null : "30px",
          top: this.position === null || this.leftPos === null || this.topPos === null ? null : "0",
          transform: this.position === null || this.leftPos === null || this.topPos === null ? null : `translate(${Math.round(this.leftPos)}px,${Math.round(this.topPos)}px)`
        };
      },
      leftPos() {
        return this.position === null || this.position.left + 60 > window.innerWidth ? null : this.position.left;
      },
      topPos() {
        let top2 = 0;
        if (this.position === null) {
          top2 = null;
        } else if (this.position.top - 30 < 0) {
          top2 = 0;
        } else if (this.position.top > window.innerHeight / 2) {
          top2 = this.position.top - 90;
        } else
          top2 = this.position.top;
        return top2;
      },
      hasHeaderSlot() {
        return !!this.$slots["header"];
      },
      maximizeIcon() {
        return this.fullSize ? "minimize" : "maximize";
      },
      modalContentStyle() {
        let modalStyle = {};
        if (this.width) {
          modalStyle["max-width"] = this.width + "px";
        }
        if (this.enableDrag) {
          modalStyle["position"] = "absolute";
        }
        if (this.fullSize) {
          modalStyle["max-height"] = "100%";
        }
        return modalStyle;
      },
      appendToElement() {
        return document.querySelector(this.appendTo);
      }
    },
    watch: {
      show(newValue) {
        if (newValue) {
          this.zIndex = getZindex();
          this.$nextTick(() => {
            if (this.$el.ownerDocument.getElementById("znpb-editor-iframe") !== void 0 && this.$el.ownerDocument.getElementById("znpb-editor-iframe") !== null) {
              document.getElementById("znpb-editor-iframe").contentWindow.document.body.style.overflow = "hidden";
            } else {
              this.$el.ownerDocument.body.style.overflow = "hidden";
            }
          });
        } else {
          this.$nextTick(() => {
            if (this.zIndex) {
              removeZindex();
              this.zIndex = null;
            }
            if (document.getElementById("znpb-editor-iframe") !== void 0 && document.getElementById("znpb-editor-iframe") !== null) {
              document.getElementById("znpb-editor-iframe").contentWindow.document.body.style.overflow = null;
            } else {
              this.$el.ownerDocument.body.style.overflow = null;
            }
          });
        }
      },
      fullscreen(newValue) {
        if (newValue) {
          this.fullSize = newValue;
        } else
          this.fullSize = this.fullscreen;
      },
      showBackdrop(newValue) {
        this.bg = newValue;
      }
    },
    mounted() {
      if (this.appendTo) {
        this.appendModal();
      }
      if (this.closeOnEscape) {
        document.addEventListener("keyup", this.onEscapeKeyPress);
      }
      if (this.show) {
        this.zIndex = getZindex();
      }
    },
    beforeUnmount() {
      window.removeEventListener("mousemove", this.drag);
      window.removeEventListener("mouseup", this.unDrag);
      document.removeEventListener("keyup", this.onEscapeKeyPress);
      if (this.$el.parentNode === this.appendToElement) {
        this.appendToElement.removeChild(this.$el);
      }
      if (this.zIndex) {
        removeZindex();
        this.zIndex = null;
      }
    },
    methods: {
      activateDrag() {
        if (this.enableDrag) {
          this.$refs.modalContent.style.transition = "none";
          const { left: left2, top: top2 } = this.$refs.modalContent.getBoundingClientRect();
          this.initialPosition = {
            clientX: event.clientX,
            clientY: event.clientY,
            left: left2,
            top: top2
          };
          window.addEventListener("mousemove", this.drag);
          window.addEventListener("mouseup", this.unDrag);
        }
      },
      drag(event2) {
        const left2 = event2.clientX - this.initialPosition.clientX + this.initialPosition.left;
        const top2 = event2.clientY - this.initialPosition.clientY + this.initialPosition.top;
        const procentualLeft = left2 * 100 / window.innerWidth + "%";
        const procentualTop = top2 * 100 / window.innerHeight + "%";
        this.$refs.modalContent.style.left = procentualLeft;
        this.$refs.modalContent.style.top = procentualTop;
      },
      unDrag() {
        if (this.$refs.modalContent) {
          this.$refs.modalContent.style.transition = "all .2s";
        }
        window.removeEventListener("mousemove", this.drag);
      },
      closeOnBackdropClick(event2) {
        if (this.closeOnClick) {
          if (this.$refs.modalContent && !this.$refs.modalContent.contains(event2.target)) {
            this.closeModal();
          }
        }
      },
      closeModal() {
        this.$emit("update:show", false);
        this.$emit("close-modal", true);
      },
      appendModal() {
        if (!this.appendToElement) {
          console.warn(`${this.$translate("no_html_matching")} ${this.appendTo}`);
          return;
        }
        this.appendToElement.appendChild(this.$el);
      },
      onEscapeKeyPress(event2) {
        if (event2.which === 27) {
          this.closeModal();
          event2.stopPropagation();
        }
      }
    }
  };
  const _hoisted_1$d = {
    key: 0,
    class: "znpb-modal__header"
  };
  const _hoisted_2$9 = { class: "znpb-modal__content" };
  function _sfc_render$g(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_Icon = vue.resolveComponent("Icon");
    return vue.openBlock(), vue.createBlock(vue.Transition, { name: "modal-fade" }, {
      default: vue.withCtx(() => [
        $props.show ? (vue.openBlock(), vue.createElementBlock("div", {
          key: 0,
          class: vue.normalizeClass(["znpb-modal__backdrop", { "znpb-modal__backdrop--nobg": !_ctx.bg }]),
          style: vue.normalizeStyle($options.modalStyle),
          onClick: _cache[3] || (_cache[3] = (...args) => $options.closeOnBackdropClick && $options.closeOnBackdropClick(...args))
        }, [
          vue.createElementVNode("div", {
            ref: "modalContent",
            style: vue.normalizeStyle($options.modalContentStyle),
            class: vue.normalizeClass(["znpb-modal__wrapper", { "znpb-modal__wrapper--full-size": _ctx.fullSize }])
          }, [
            ($props.title || $props.showClose || $props.showMaximize) && !$options.hasHeaderSlot ? (vue.openBlock(), vue.createElementBlock("header", _hoisted_1$d, [
              vue.createElementVNode("div", {
                class: "znpb-modal__header-title",
                style: vue.normalizeStyle(
                  $props.enableDrag ? {
                    cursor: "pointer",
                    "user-select": "none"
                  } : null
                ),
                onMousedown: _cache[0] || (_cache[0] = (...args) => $options.activateDrag && $options.activateDrag(...args))
              }, [
                vue.createTextVNode(vue.toDisplayString($props.title) + " ", 1),
                vue.renderSlot(_ctx.$slots, "title")
              ], 36),
              $props.showMaximize ? (vue.openBlock(), vue.createBlock(_component_Icon, {
                key: 0,
                icon: _ctx.fullSize ? "shrink" : "maximize",
                class: "znpb-modal__header-button",
                onClick: _cache[1] || (_cache[1] = vue.withModifiers(($event) => (_ctx.fullSize = !_ctx.fullSize, _ctx.$emit("update:fullscreen", _ctx.fullSize)), ["stop"]))
              }, null, 8, ["icon"])) : vue.createCommentVNode("", true),
              $props.showClose ? (vue.openBlock(), vue.createElementBlock("span", {
                key: 1,
                class: "znpb-modal__header-button",
                onClick: _cache[2] || (_cache[2] = vue.withModifiers((...args) => $options.closeModal && $options.closeModal(...args), ["stop"]))
              }, [
                vue.renderSlot(_ctx.$slots, "close"),
                vue.createVNode(_component_Icon, { icon: "close" })
              ])) : vue.createCommentVNode("", true)
            ])) : vue.createCommentVNode("", true),
            vue.renderSlot(_ctx.$slots, "header"),
            vue.createElementVNode("div", _hoisted_2$9, [
              vue.renderSlot(_ctx.$slots, "default")
            ]),
            vue.renderSlot(_ctx.$slots, "footer")
          ], 6)
        ], 6)) : vue.createCommentVNode("", true)
      ]),
      _: 3
    });
  }
  var Modal = /* @__PURE__ */ _export_sfc(_sfc_main$h, [["render", _sfc_render$g]]);
  var ModalConfirm_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$g = {
    name: "ModalConfirm",
    components: {
      Modal,
      Button: _sfc_main$1H
    },
    props: {
      confirmText: {
        type: String,
        required: false,
        default: "confirm"
      },
      cancelText: {
        type: String,
        required: false,
        default: "cancel"
      },
      width: {
        type: Number,
        required: false,
        default: 470
      }
    },
    data() {
      return {};
    }
  };
  const _hoisted_1$c = { class: "znpb-modal__confirm" };
  const _hoisted_2$8 = { class: "znpb-modal__confirm-buttons-wrapper" };
  function _sfc_render$f(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_Button = vue.resolveComponent("Button");
    const _component_Modal = vue.resolveComponent("Modal");
    return vue.openBlock(), vue.createBlock(_component_Modal, {
      "show-close": false,
      "show-maximize": false,
      show: true,
      "append-to": "body",
      width: $props.width
    }, {
      default: vue.withCtx(() => [
        vue.createElementVNode("div", _hoisted_1$c, [
          vue.renderSlot(_ctx.$slots, "default")
        ]),
        vue.createElementVNode("div", _hoisted_2$8, [
          $props.confirmText ? (vue.openBlock(), vue.createBlock(_component_Button, {
            key: 0,
            type: "danger",
            onClick: _cache[0] || (_cache[0] = ($event) => _ctx.$emit("confirm"))
          }, {
            default: vue.withCtx(() => [
              vue.createTextVNode(vue.toDisplayString($props.confirmText), 1)
            ]),
            _: 1
          })) : vue.createCommentVNode("", true),
          $props.cancelText ? (vue.openBlock(), vue.createBlock(_component_Button, {
            key: 1,
            type: "gray",
            onClick: _cache[1] || (_cache[1] = ($event) => _ctx.$emit("cancel"))
          }, {
            default: vue.withCtx(() => [
              vue.createTextVNode(vue.toDisplayString($props.cancelText), 1)
            ]),
            _: 1
          })) : vue.createCommentVNode("", true)
        ])
      ]),
      _: 3
    }, 8, ["width"]);
  }
  var ModalConfirm = /* @__PURE__ */ _export_sfc(_sfc_main$g, [["render", _sfc_render$f]]);
  var ModalTemplateSaveButton_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$f = {
    name: "ModalTemplateSaveButton",
    components: {
      Button: _sfc_main$1H
    },
    setup(props, { emit }) {
      const buttonType = vue.computed(() => {
        return props.disabled ? "gray" : "secondary";
      });
      function onButtonClick() {
        if (!props.disabled) {
          emit("save-modal");
        }
      }
      return {
        buttonType,
        onButtonClick
      };
    }
  };
  const _hoisted_1$b = { class: "znpb-modal-content-save-button" };
  const _hoisted_2$7 = { class: "znpb-modal-content-wrapper znpb-fancy-scrollbar" };
  const _hoisted_3$5 = { class: "znpb-modal-content-save-button__button" };
  function _sfc_render$e(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_Button = vue.resolveComponent("Button");
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$b, [
      vue.createElementVNode("div", _hoisted_2$7, [
        vue.renderSlot(_ctx.$slots, "default")
      ]),
      vue.createElementVNode("div", _hoisted_3$5, [
        vue.createVNode(_component_Button, {
          type: $setup.buttonType,
          onClick: $setup.onButtonClick
        }, {
          default: vue.withCtx(() => [
            vue.createTextVNode(vue.toDisplayString(_ctx.$translate("save")), 1)
          ]),
          _: 1
        }, 8, ["type", "onClick"])
      ])
    ]);
  }
  var ModalTemplateSaveButton = /* @__PURE__ */ _export_sfc(_sfc_main$f, [["render", _sfc_render$e]]);
  const components = [
    _sfc_main$13,
    _sfc_main$1H,
    _sfc_main$z,
    _sfc_main$17,
    _sfc_main$1t,
    _sfc_main$o,
    _sfc_main$n,
    Modal,
    ModalConfirm,
    ModalTemplateSaveButton,
    _sfc_main$1K,
    Tooltip,
    _sfc_main$s,
    _sfc_main$1J,
    _sfc_main$r,
    _sfc_main$1g,
    _sfc_main$1h,
    _sfc_main$1u,
    _sfc_main$N,
    _sfc_main$j,
    _sfc_main$v,
    _sfc_main$10,
    _sfc_main$1I,
    _sfc_main$1s,
    _sfc_main$19,
    _sfc_main$16,
    _sfc_main$14,
    _sfc_main$i,
    _sfc_main$1a,
    _sfc_main$1G,
    _sfc_main$Z,
    _sfc_main$Y,
    _sfc_main$S,
    _sfc_main$T,
    InputCheckboxSwitch,
    _sfc_main$1A,
    _sfc_main$Q,
    InputRadio,
    InputRadioGroup,
    InputRadioIcon,
    InputDatePicker,
    _sfc_main$M,
    _sfc_main$L,
    _sfc_main$B,
    _sfc_main$y,
    _sfc_main$C,
    _sfc_main$X,
    _sfc_main$W,
    _sfc_main$V,
    InputBorderRadiusTabs,
    _sfc_main$H,
    _sfc_main$G,
    _sfc_main$_,
    _sfc_main$I,
    _sfc_main$1D,
    _sfc_main$1k,
    InputSelect,
    _sfc_main$1j,
    _sfc_main$1i,
    _sfc_main$w,
    _sfc_main$1C,
    _sfc_main$1B,
    _sfc_main$x,
    OptionsForm,
    _sfc_main$q,
    _sfc_main$m,
    _sfc_main$t,
    _sfc_main$l,
    _sfc_main$k,
    getDefaultGradient
  ];
  function install(app) {
    components.forEach((component) => {
      app.component(component.name, component);
    });
    app.directive("znpb-tooltip", PopperDirective);
  }
  let restConfig = window.ZionProRestConfig;
  const ZionService = axios$3.create({
    baseURL: `${restConfig.rest_root}zionbuilder-pro/`,
    headers: {
      "X-WP-Nonce": restConfig.nonce,
      "Accept": "application/json",
      "Content-Type": "application/json"
    }
  });
  const saveThemeBuilder = function(values) {
    return ZionService.post("v1/theme-builder", values);
  };
  const getOptions = function(condition_id, searchKeyword = "", page = 1) {
    const callback_arguments = {
      search_keyword: searchKeyword,
      page
    };
    return ZionService.get("v1/conditions/get-rule-options", {
      params: {
        condition_id,
        callback_arguments
      }
    });
  };
  var object_hash = { exports: {} };
  (function(module2, exports2) {
    !function(e) {
      module2.exports = e();
    }(function() {
      return function r(o, i, u) {
        function s(n, e2) {
          if (!i[n]) {
            if (!o[n]) {
              var t = "function" == typeof commonjsRequire && commonjsRequire;
              if (!e2 && t)
                return t(n, true);
              if (a)
                return a(n, true);
              throw new Error("Cannot find module '" + n + "'");
            }
            e2 = i[n] = { exports: {} };
            o[n][0].call(e2.exports, function(e3) {
              var t2 = o[n][1][e3];
              return s(t2 || e3);
            }, e2, e2.exports, r, o, i, u);
          }
          return i[n].exports;
        }
        for (var a = "function" == typeof commonjsRequire && commonjsRequire, e = 0; e < u.length; e++)
          s(u[e]);
        return s;
      }({ 1: [function(w, b, m) {
        !function(e, n, s, c, d, h, p, g, y) {
          var r = w("crypto");
          function t(e2, t2) {
            t2 = u(e2, t2);
            var n2;
            return void 0 === (n2 = "passthrough" !== t2.algorithm ? r.createHash(t2.algorithm) : new l()).write && (n2.write = n2.update, n2.end = n2.update), f(t2, n2).dispatch(e2), n2.update || n2.end(""), n2.digest ? n2.digest("buffer" === t2.encoding ? void 0 : t2.encoding) : (e2 = n2.read(), "buffer" !== t2.encoding ? e2.toString(t2.encoding) : e2);
          }
          (m = b.exports = t).sha1 = function(e2) {
            return t(e2);
          }, m.keys = function(e2) {
            return t(e2, { excludeValues: true, algorithm: "sha1", encoding: "hex" });
          }, m.MD5 = function(e2) {
            return t(e2, { algorithm: "md5", encoding: "hex" });
          }, m.keysMD5 = function(e2) {
            return t(e2, { algorithm: "md5", encoding: "hex", excludeValues: true });
          };
          var o = r.getHashes ? r.getHashes().slice() : ["sha1", "md5"], i = (o.push("passthrough"), ["buffer", "hex", "binary", "base64"]);
          function u(e2, t2) {
            var n2 = {};
            if (n2.algorithm = (t2 = t2 || {}).algorithm || "sha1", n2.encoding = t2.encoding || "hex", n2.excludeValues = !!t2.excludeValues, n2.algorithm = n2.algorithm.toLowerCase(), n2.encoding = n2.encoding.toLowerCase(), n2.ignoreUnknown = true === t2.ignoreUnknown, n2.respectType = false !== t2.respectType, n2.respectFunctionNames = false !== t2.respectFunctionNames, n2.respectFunctionProperties = false !== t2.respectFunctionProperties, n2.unorderedArrays = true === t2.unorderedArrays, n2.unorderedSets = false !== t2.unorderedSets, n2.unorderedObjects = false !== t2.unorderedObjects, n2.replacer = t2.replacer || void 0, n2.excludeKeys = t2.excludeKeys || void 0, void 0 === e2)
              throw new Error("Object argument required.");
            for (var r2 = 0; r2 < o.length; ++r2)
              o[r2].toLowerCase() === n2.algorithm.toLowerCase() && (n2.algorithm = o[r2]);
            if (-1 === o.indexOf(n2.algorithm))
              throw new Error('Algorithm "' + n2.algorithm + '"  not supported. supported values: ' + o.join(", "));
            if (-1 === i.indexOf(n2.encoding) && "passthrough" !== n2.algorithm)
              throw new Error('Encoding "' + n2.encoding + '"  not supported. supported values: ' + i.join(", "));
            return n2;
          }
          function a(e2) {
            if ("function" == typeof e2)
              return null != /^function\s+\w*\s*\(\s*\)\s*{\s+\[native code\]\s+}$/i.exec(Function.prototype.toString.call(e2));
          }
          function f(o2, t2, i2) {
            i2 = i2 || [];
            function u2(e2) {
              return t2.update ? t2.update(e2, "utf8") : t2.write(e2, "utf8");
            }
            return { dispatch: function(e2) {
              return this["_" + (null === (e2 = o2.replacer ? o2.replacer(e2) : e2) ? "null" : typeof e2)](e2);
            }, _object: function(t3) {
              var n2, e2 = Object.prototype.toString.call(t3), r2 = /\[object (.*)\]/i.exec(e2);
              r2 = (r2 = r2 ? r2[1] : "unknown:[" + e2 + "]").toLowerCase();
              if (0 <= (e2 = i2.indexOf(t3)))
                return this.dispatch("[CIRCULAR:" + e2 + "]");
              if (i2.push(t3), void 0 !== s && s.isBuffer && s.isBuffer(t3))
                return u2("buffer:"), u2(t3);
              if ("object" === r2 || "function" === r2 || "asyncfunction" === r2)
                return e2 = Object.keys(t3), o2.unorderedObjects && (e2 = e2.sort()), false === o2.respectType || a(t3) || e2.splice(0, 0, "prototype", "__proto__", "constructor"), o2.excludeKeys && (e2 = e2.filter(function(e3) {
                  return !o2.excludeKeys(e3);
                })), u2("object:" + e2.length + ":"), n2 = this, e2.forEach(function(e3) {
                  n2.dispatch(e3), u2(":"), o2.excludeValues || n2.dispatch(t3[e3]), u2(",");
                });
              if (!this["_" + r2]) {
                if (o2.ignoreUnknown)
                  return u2("[" + r2 + "]");
                throw new Error('Unknown object type "' + r2 + '"');
              }
              this["_" + r2](t3);
            }, _array: function(e2, t3) {
              t3 = void 0 !== t3 ? t3 : false !== o2.unorderedArrays;
              var n2 = this;
              if (u2("array:" + e2.length + ":"), !t3 || e2.length <= 1)
                return e2.forEach(function(e3) {
                  return n2.dispatch(e3);
                });
              var r2 = [], t3 = e2.map(function(e3) {
                var t4 = new l(), n3 = i2.slice();
                return f(o2, t4, n3).dispatch(e3), r2 = r2.concat(n3.slice(i2.length)), t4.read().toString();
              });
              return i2 = i2.concat(r2), t3.sort(), this._array(t3, false);
            }, _date: function(e2) {
              return u2("date:" + e2.toJSON());
            }, _symbol: function(e2) {
              return u2("symbol:" + e2.toString());
            }, _error: function(e2) {
              return u2("error:" + e2.toString());
            }, _boolean: function(e2) {
              return u2("bool:" + e2.toString());
            }, _string: function(e2) {
              u2("string:" + e2.length + ":"), u2(e2.toString());
            }, _function: function(e2) {
              u2("fn:"), a(e2) ? this.dispatch("[native]") : this.dispatch(e2.toString()), false !== o2.respectFunctionNames && this.dispatch("function-name:" + String(e2.name)), o2.respectFunctionProperties && this._object(e2);
            }, _number: function(e2) {
              return u2("number:" + e2.toString());
            }, _xml: function(e2) {
              return u2("xml:" + e2.toString());
            }, _null: function() {
              return u2("Null");
            }, _undefined: function() {
              return u2("Undefined");
            }, _regexp: function(e2) {
              return u2("regex:" + e2.toString());
            }, _uint8array: function(e2) {
              return u2("uint8array:"), this.dispatch(Array.prototype.slice.call(e2));
            }, _uint8clampedarray: function(e2) {
              return u2("uint8clampedarray:"), this.dispatch(Array.prototype.slice.call(e2));
            }, _int8array: function(e2) {
              return u2("int8array:"), this.dispatch(Array.prototype.slice.call(e2));
            }, _uint16array: function(e2) {
              return u2("uint16array:"), this.dispatch(Array.prototype.slice.call(e2));
            }, _int16array: function(e2) {
              return u2("int16array:"), this.dispatch(Array.prototype.slice.call(e2));
            }, _uint32array: function(e2) {
              return u2("uint32array:"), this.dispatch(Array.prototype.slice.call(e2));
            }, _int32array: function(e2) {
              return u2("int32array:"), this.dispatch(Array.prototype.slice.call(e2));
            }, _float32array: function(e2) {
              return u2("float32array:"), this.dispatch(Array.prototype.slice.call(e2));
            }, _float64array: function(e2) {
              return u2("float64array:"), this.dispatch(Array.prototype.slice.call(e2));
            }, _arraybuffer: function(e2) {
              return u2("arraybuffer:"), this.dispatch(new Uint8Array(e2));
            }, _url: function(e2) {
              return u2("url:" + e2.toString());
            }, _map: function(e2) {
              u2("map:");
              e2 = Array.from(e2);
              return this._array(e2, false !== o2.unorderedSets);
            }, _set: function(e2) {
              u2("set:");
              e2 = Array.from(e2);
              return this._array(e2, false !== o2.unorderedSets);
            }, _file: function(e2) {
              return u2("file:"), this.dispatch([e2.name, e2.size, e2.type, e2.lastModfied]);
            }, _blob: function() {
              if (o2.ignoreUnknown)
                return u2("[blob]");
              throw Error('Hashing Blob objects is currently not supported\n(see https://github.com/puleos/object-hash/issues/26)\nUse "options.replacer" or "options.ignoreUnknown"\n');
            }, _domwindow: function() {
              return u2("domwindow");
            }, _bigint: function(e2) {
              return u2("bigint:" + e2.toString());
            }, _process: function() {
              return u2("process");
            }, _timer: function() {
              return u2("timer");
            }, _pipe: function() {
              return u2("pipe");
            }, _tcp: function() {
              return u2("tcp");
            }, _udp: function() {
              return u2("udp");
            }, _tty: function() {
              return u2("tty");
            }, _statwatcher: function() {
              return u2("statwatcher");
            }, _securecontext: function() {
              return u2("securecontext");
            }, _connection: function() {
              return u2("connection");
            }, _zlib: function() {
              return u2("zlib");
            }, _context: function() {
              return u2("context");
            }, _nodescript: function() {
              return u2("nodescript");
            }, _httpparser: function() {
              return u2("httpparser");
            }, _dataview: function() {
              return u2("dataview");
            }, _signal: function() {
              return u2("signal");
            }, _fsevent: function() {
              return u2("fsevent");
            }, _tlswrap: function() {
              return u2("tlswrap");
            } };
          }
          function l() {
            return { buf: "", write: function(e2) {
              this.buf += e2;
            }, end: function(e2) {
              this.buf += e2;
            }, read: function() {
              return this.buf;
            } };
          }
          m.writeToStream = function(e2, t2, n2) {
            return void 0 === n2 && (n2 = t2, t2 = {}), f(t2 = u(e2, t2), n2).dispatch(e2);
          };
        }.call(this, w("lYpoI2"), "undefined" != typeof self ? self : "undefined" != typeof window ? window : {}, w("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/fake_9a5aa49d.js", "/");
      }, { buffer: 3, crypto: 5, lYpoI2: 11 }], 2: [function(e, t, f) {
        !function(e2, t2, n, r, o, i, u, s, a) {
          !function(e3) {
            var a2 = "undefined" != typeof Uint8Array ? Uint8Array : Array, t3 = "+".charCodeAt(0), n2 = "/".charCodeAt(0), r2 = "0".charCodeAt(0), o2 = "a".charCodeAt(0), i2 = "A".charCodeAt(0), u2 = "-".charCodeAt(0), s2 = "_".charCodeAt(0);
            function f2(e4) {
              e4 = e4.charCodeAt(0);
              return e4 === t3 || e4 === u2 ? 62 : e4 === n2 || e4 === s2 ? 63 : e4 < r2 ? -1 : e4 < r2 + 10 ? e4 - r2 + 26 + 26 : e4 < i2 + 26 ? e4 - i2 : e4 < o2 + 26 ? e4 - o2 + 26 : void 0;
            }
            e3.toByteArray = function(e4) {
              var t4, n3;
              if (0 < e4.length % 4)
                throw new Error("Invalid string. Length must be a multiple of 4");
              var r3 = e4.length, r3 = "=" === e4.charAt(r3 - 2) ? 2 : "=" === e4.charAt(r3 - 1) ? 1 : 0, o3 = new a2(3 * e4.length / 4 - r3), i3 = 0 < r3 ? e4.length - 4 : e4.length, u3 = 0;
              function s3(e5) {
                o3[u3++] = e5;
              }
              for (t4 = 0; t4 < i3; t4 += 4, 0)
                s3((16711680 & (n3 = f2(e4.charAt(t4)) << 18 | f2(e4.charAt(t4 + 1)) << 12 | f2(e4.charAt(t4 + 2)) << 6 | f2(e4.charAt(t4 + 3)))) >> 16), s3((65280 & n3) >> 8), s3(255 & n3);
              return 2 == r3 ? s3(255 & (n3 = f2(e4.charAt(t4)) << 2 | f2(e4.charAt(t4 + 1)) >> 4)) : 1 == r3 && (s3((n3 = f2(e4.charAt(t4)) << 10 | f2(e4.charAt(t4 + 1)) << 4 | f2(e4.charAt(t4 + 2)) >> 2) >> 8 & 255), s3(255 & n3)), o3;
            }, e3.fromByteArray = function(e4) {
              var t4, n3, r3, o3, i3 = e4.length % 3, u3 = "";
              function s3(e5) {
                return "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".charAt(e5);
              }
              for (t4 = 0, r3 = e4.length - i3; t4 < r3; t4 += 3)
                n3 = (e4[t4] << 16) + (e4[t4 + 1] << 8) + e4[t4 + 2], u3 += s3((o3 = n3) >> 18 & 63) + s3(o3 >> 12 & 63) + s3(o3 >> 6 & 63) + s3(63 & o3);
              switch (i3) {
                case 1:
                  u3 = (u3 += s3((n3 = e4[e4.length - 1]) >> 2)) + s3(n3 << 4 & 63) + "==";
                  break;
                case 2:
                  u3 = (u3 = (u3 += s3((n3 = (e4[e4.length - 2] << 8) + e4[e4.length - 1]) >> 10)) + s3(n3 >> 4 & 63)) + s3(n3 << 2 & 63) + "=";
              }
              return u3;
            };
          }(void 0 === f ? this.base64js = {} : f);
        }.call(this, e("lYpoI2"), "undefined" != typeof self ? self : "undefined" != typeof window ? window : {}, e("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/node_modules/gulp-browserify/node_modules/base64-js/lib/b64.js", "/node_modules/gulp-browserify/node_modules/base64-js/lib");
      }, { buffer: 3, lYpoI2: 11 }], 3: [function(O, e, H) {
        !function(e2, n, f, r, h, p, g, y, w) {
          var a = O("base64-js"), i = O("ieee754");
          function f(e3, t2, n2) {
            if (!(this instanceof f))
              return new f(e3, t2, n2);
            var r2, o2, i2, u2, s2 = typeof e3;
            if ("base64" === t2 && "string" == s2)
              for (e3 = (u2 = e3).trim ? u2.trim() : u2.replace(/^\s+|\s+$/g, ""); e3.length % 4 != 0; )
                e3 += "=";
            if ("number" == s2)
              r2 = j(e3);
            else if ("string" == s2)
              r2 = f.byteLength(e3, t2);
            else {
              if ("object" != s2)
                throw new Error("First argument needs to be a number, array or string.");
              r2 = j(e3.length);
            }
            if (f._useTypedArrays ? o2 = f._augment(new Uint8Array(r2)) : ((o2 = this).length = r2, o2._isBuffer = true), f._useTypedArrays && "number" == typeof e3.byteLength)
              o2._set(e3);
            else if (C(u2 = e3) || f.isBuffer(u2) || u2 && "object" == typeof u2 && "number" == typeof u2.length)
              for (i2 = 0; i2 < r2; i2++)
                f.isBuffer(e3) ? o2[i2] = e3.readUInt8(i2) : o2[i2] = e3[i2];
            else if ("string" == s2)
              o2.write(e3, 0, t2);
            else if ("number" == s2 && !f._useTypedArrays && !n2)
              for (i2 = 0; i2 < r2; i2++)
                o2[i2] = 0;
            return o2;
          }
          function b(e3, t2, n2, r2) {
            return f._charsWritten = c(function(e4) {
              for (var t3 = [], n3 = 0; n3 < e4.length; n3++)
                t3.push(255 & e4.charCodeAt(n3));
              return t3;
            }(t2), e3, n2, r2);
          }
          function m(e3, t2, n2, r2) {
            return f._charsWritten = c(function(e4) {
              for (var t3, n3, r3 = [], o2 = 0; o2 < e4.length; o2++)
                n3 = e4.charCodeAt(o2), t3 = n3 >> 8, n3 = n3 % 256, r3.push(n3), r3.push(t3);
              return r3;
            }(t2), e3, n2, r2);
          }
          function v(e3, t2, n2) {
            var r2 = "";
            n2 = Math.min(e3.length, n2);
            for (var o2 = t2; o2 < n2; o2++)
              r2 += String.fromCharCode(e3[o2]);
            return r2;
          }
          function o(e3, t2, n2, r2) {
            r2 || (d("boolean" == typeof n2, "missing or invalid endian"), d(null != t2, "missing offset"), d(t2 + 1 < e3.length, "Trying to read beyond buffer length"));
            var o2, r2 = e3.length;
            if (!(r2 <= t2))
              return n2 ? (o2 = e3[t2], t2 + 1 < r2 && (o2 |= e3[t2 + 1] << 8)) : (o2 = e3[t2] << 8, t2 + 1 < r2 && (o2 |= e3[t2 + 1])), o2;
          }
          function u(e3, t2, n2, r2) {
            r2 || (d("boolean" == typeof n2, "missing or invalid endian"), d(null != t2, "missing offset"), d(t2 + 3 < e3.length, "Trying to read beyond buffer length"));
            var o2, r2 = e3.length;
            if (!(r2 <= t2))
              return n2 ? (t2 + 2 < r2 && (o2 = e3[t2 + 2] << 16), t2 + 1 < r2 && (o2 |= e3[t2 + 1] << 8), o2 |= e3[t2], t2 + 3 < r2 && (o2 += e3[t2 + 3] << 24 >>> 0)) : (t2 + 1 < r2 && (o2 = e3[t2 + 1] << 16), t2 + 2 < r2 && (o2 |= e3[t2 + 2] << 8), t2 + 3 < r2 && (o2 |= e3[t2 + 3]), o2 += e3[t2] << 24 >>> 0), o2;
          }
          function _(e3, t2, n2, r2) {
            if (r2 || (d("boolean" == typeof n2, "missing or invalid endian"), d(null != t2, "missing offset"), d(t2 + 1 < e3.length, "Trying to read beyond buffer length")), !(e3.length <= t2))
              return r2 = o(e3, t2, n2, true), 32768 & r2 ? -1 * (65535 - r2 + 1) : r2;
          }
          function E(e3, t2, n2, r2) {
            if (r2 || (d("boolean" == typeof n2, "missing or invalid endian"), d(null != t2, "missing offset"), d(t2 + 3 < e3.length, "Trying to read beyond buffer length")), !(e3.length <= t2))
              return r2 = u(e3, t2, n2, true), 2147483648 & r2 ? -1 * (4294967295 - r2 + 1) : r2;
          }
          function I(e3, t2, n2, r2) {
            return r2 || (d("boolean" == typeof n2, "missing or invalid endian"), d(t2 + 3 < e3.length, "Trying to read beyond buffer length")), i.read(e3, t2, n2, 23, 4);
          }
          function A(e3, t2, n2, r2) {
            return r2 || (d("boolean" == typeof n2, "missing or invalid endian"), d(t2 + 7 < e3.length, "Trying to read beyond buffer length")), i.read(e3, t2, n2, 52, 8);
          }
          function s(e3, t2, n2, r2, o2) {
            o2 || (d(null != t2, "missing value"), d("boolean" == typeof r2, "missing or invalid endian"), d(null != n2, "missing offset"), d(n2 + 1 < e3.length, "trying to write beyond buffer length"), Y(t2, 65535));
            o2 = e3.length;
            if (!(o2 <= n2))
              for (var i2 = 0, u2 = Math.min(o2 - n2, 2); i2 < u2; i2++)
                e3[n2 + i2] = (t2 & 255 << 8 * (r2 ? i2 : 1 - i2)) >>> 8 * (r2 ? i2 : 1 - i2);
          }
          function l(e3, t2, n2, r2, o2) {
            o2 || (d(null != t2, "missing value"), d("boolean" == typeof r2, "missing or invalid endian"), d(null != n2, "missing offset"), d(n2 + 3 < e3.length, "trying to write beyond buffer length"), Y(t2, 4294967295));
            o2 = e3.length;
            if (!(o2 <= n2))
              for (var i2 = 0, u2 = Math.min(o2 - n2, 4); i2 < u2; i2++)
                e3[n2 + i2] = t2 >>> 8 * (r2 ? i2 : 3 - i2) & 255;
          }
          function B(e3, t2, n2, r2, o2) {
            o2 || (d(null != t2, "missing value"), d("boolean" == typeof r2, "missing or invalid endian"), d(null != n2, "missing offset"), d(n2 + 1 < e3.length, "Trying to write beyond buffer length"), F(t2, 32767, -32768)), e3.length <= n2 || s(e3, 0 <= t2 ? t2 : 65535 + t2 + 1, n2, r2, o2);
          }
          function L(e3, t2, n2, r2, o2) {
            o2 || (d(null != t2, "missing value"), d("boolean" == typeof r2, "missing or invalid endian"), d(null != n2, "missing offset"), d(n2 + 3 < e3.length, "Trying to write beyond buffer length"), F(t2, 2147483647, -2147483648)), e3.length <= n2 || l(e3, 0 <= t2 ? t2 : 4294967295 + t2 + 1, n2, r2, o2);
          }
          function U(e3, t2, n2, r2, o2) {
            o2 || (d(null != t2, "missing value"), d("boolean" == typeof r2, "missing or invalid endian"), d(null != n2, "missing offset"), d(n2 + 3 < e3.length, "Trying to write beyond buffer length"), D(t2, 34028234663852886e22, -34028234663852886e22)), e3.length <= n2 || i.write(e3, t2, n2, r2, 23, 4);
          }
          function x(e3, t2, n2, r2, o2) {
            o2 || (d(null != t2, "missing value"), d("boolean" == typeof r2, "missing or invalid endian"), d(null != n2, "missing offset"), d(n2 + 7 < e3.length, "Trying to write beyond buffer length"), D(t2, 17976931348623157e292, -17976931348623157e292)), e3.length <= n2 || i.write(e3, t2, n2, r2, 52, 8);
          }
          H.Buffer = f, H.SlowBuffer = f, H.INSPECT_MAX_BYTES = 50, f.poolSize = 8192, f._useTypedArrays = function() {
            try {
              var e3 = new ArrayBuffer(0), t2 = new Uint8Array(e3);
              return t2.foo = function() {
                return 42;
              }, 42 === t2.foo() && "function" == typeof t2.subarray;
            } catch (e4) {
              return false;
            }
          }(), f.isEncoding = function(e3) {
            switch (String(e3).toLowerCase()) {
              case "hex":
              case "utf8":
              case "utf-8":
              case "ascii":
              case "binary":
              case "base64":
              case "raw":
              case "ucs2":
              case "ucs-2":
              case "utf16le":
              case "utf-16le":
                return true;
              default:
                return false;
            }
          }, f.isBuffer = function(e3) {
            return !(null == e3 || !e3._isBuffer);
          }, f.byteLength = function(e3, t2) {
            var n2;
            switch (e3 += "", t2 || "utf8") {
              case "hex":
                n2 = e3.length / 2;
                break;
              case "utf8":
              case "utf-8":
                n2 = T(e3).length;
                break;
              case "ascii":
              case "binary":
              case "raw":
                n2 = e3.length;
                break;
              case "base64":
                n2 = M(e3).length;
                break;
              case "ucs2":
              case "ucs-2":
              case "utf16le":
              case "utf-16le":
                n2 = 2 * e3.length;
                break;
              default:
                throw new Error("Unknown encoding");
            }
            return n2;
          }, f.concat = function(e3, t2) {
            if (d(C(e3), "Usage: Buffer.concat(list, [totalLength])\nlist should be an Array."), 0 === e3.length)
              return new f(0);
            if (1 === e3.length)
              return e3[0];
            if ("number" != typeof t2)
              for (o2 = t2 = 0; o2 < e3.length; o2++)
                t2 += e3[o2].length;
            for (var n2 = new f(t2), r2 = 0, o2 = 0; o2 < e3.length; o2++) {
              var i2 = e3[o2];
              i2.copy(n2, r2), r2 += i2.length;
            }
            return n2;
          }, f.prototype.write = function(e3, t2, n2, r2) {
            isFinite(t2) ? isFinite(n2) || (r2 = n2, n2 = void 0) : (a2 = r2, r2 = t2, t2 = n2, n2 = a2), t2 = Number(t2) || 0;
            var o2, i2, u2, s2, a2 = this.length - t2;
            switch ((!n2 || a2 < (n2 = Number(n2))) && (n2 = a2), r2 = String(r2 || "utf8").toLowerCase()) {
              case "hex":
                o2 = function(e4, t3, n3, r3) {
                  n3 = Number(n3) || 0;
                  var o3 = e4.length - n3;
                  (!r3 || o3 < (r3 = Number(r3))) && (r3 = o3), d((o3 = t3.length) % 2 == 0, "Invalid hex string"), o3 / 2 < r3 && (r3 = o3 / 2);
                  for (var i3 = 0; i3 < r3; i3++) {
                    var u3 = parseInt(t3.substr(2 * i3, 2), 16);
                    d(!isNaN(u3), "Invalid hex string"), e4[n3 + i3] = u3;
                  }
                  return f._charsWritten = 2 * i3, i3;
                }(this, e3, t2, n2);
                break;
              case "utf8":
              case "utf-8":
                i2 = this, u2 = t2, s2 = n2, o2 = f._charsWritten = c(T(e3), i2, u2, s2);
                break;
              case "ascii":
              case "binary":
                o2 = b(this, e3, t2, n2);
                break;
              case "base64":
                i2 = this, u2 = t2, s2 = n2, o2 = f._charsWritten = c(M(e3), i2, u2, s2);
                break;
              case "ucs2":
              case "ucs-2":
              case "utf16le":
              case "utf-16le":
                o2 = m(this, e3, t2, n2);
                break;
              default:
                throw new Error("Unknown encoding");
            }
            return o2;
          }, f.prototype.toString = function(e3, t2, n2) {
            var r2, o2, i2, u2, s2 = this;
            if (e3 = String(e3 || "utf8").toLowerCase(), t2 = Number(t2) || 0, (n2 = void 0 !== n2 ? Number(n2) : s2.length) === t2)
              return "";
            switch (e3) {
              case "hex":
                r2 = function(e4, t3, n3) {
                  var r3 = e4.length;
                  (!t3 || t3 < 0) && (t3 = 0);
                  (!n3 || n3 < 0 || r3 < n3) && (n3 = r3);
                  for (var o3 = "", i3 = t3; i3 < n3; i3++)
                    o3 += k(e4[i3]);
                  return o3;
                }(s2, t2, n2);
                break;
              case "utf8":
              case "utf-8":
                r2 = function(e4, t3, n3) {
                  var r3 = "", o3 = "";
                  n3 = Math.min(e4.length, n3);
                  for (var i3 = t3; i3 < n3; i3++)
                    e4[i3] <= 127 ? (r3 += N(o3) + String.fromCharCode(e4[i3]), o3 = "") : o3 += "%" + e4[i3].toString(16);
                  return r3 + N(o3);
                }(s2, t2, n2);
                break;
              case "ascii":
              case "binary":
                r2 = v(s2, t2, n2);
                break;
              case "base64":
                o2 = s2, u2 = n2, r2 = 0 === (i2 = t2) && u2 === o2.length ? a.fromByteArray(o2) : a.fromByteArray(o2.slice(i2, u2));
                break;
              case "ucs2":
              case "ucs-2":
              case "utf16le":
              case "utf-16le":
                r2 = function(e4, t3, n3) {
                  for (var r3 = e4.slice(t3, n3), o3 = "", i3 = 0; i3 < r3.length; i3 += 2)
                    o3 += String.fromCharCode(r3[i3] + 256 * r3[i3 + 1]);
                  return o3;
                }(s2, t2, n2);
                break;
              default:
                throw new Error("Unknown encoding");
            }
            return r2;
          }, f.prototype.toJSON = function() {
            return { type: "Buffer", data: Array.prototype.slice.call(this._arr || this, 0) };
          }, f.prototype.copy = function(e3, t2, n2, r2) {
            if (t2 = t2 || 0, (r2 = r2 || 0 === r2 ? r2 : this.length) !== (n2 = n2 || 0) && 0 !== e3.length && 0 !== this.length) {
              d(n2 <= r2, "sourceEnd < sourceStart"), d(0 <= t2 && t2 < e3.length, "targetStart out of bounds"), d(0 <= n2 && n2 < this.length, "sourceStart out of bounds"), d(0 <= r2 && r2 <= this.length, "sourceEnd out of bounds"), r2 > this.length && (r2 = this.length);
              var o2 = (r2 = e3.length - t2 < r2 - n2 ? e3.length - t2 + n2 : r2) - n2;
              if (o2 < 100 || !f._useTypedArrays)
                for (var i2 = 0; i2 < o2; i2++)
                  e3[i2 + t2] = this[i2 + n2];
              else
                e3._set(this.subarray(n2, n2 + o2), t2);
            }
          }, f.prototype.slice = function(e3, t2) {
            var n2 = this.length;
            if (e3 = S(e3, n2, 0), t2 = S(t2, n2, n2), f._useTypedArrays)
              return f._augment(this.subarray(e3, t2));
            for (var r2 = t2 - e3, o2 = new f(r2, void 0, true), i2 = 0; i2 < r2; i2++)
              o2[i2] = this[i2 + e3];
            return o2;
          }, f.prototype.get = function(e3) {
            return console.log(".get() is deprecated. Access using array indexes instead."), this.readUInt8(e3);
          }, f.prototype.set = function(e3, t2) {
            return console.log(".set() is deprecated. Access using array indexes instead."), this.writeUInt8(e3, t2);
          }, f.prototype.readUInt8 = function(e3, t2) {
            if (t2 || (d(null != e3, "missing offset"), d(e3 < this.length, "Trying to read beyond buffer length")), !(e3 >= this.length))
              return this[e3];
          }, f.prototype.readUInt16LE = function(e3, t2) {
            return o(this, e3, true, t2);
          }, f.prototype.readUInt16BE = function(e3, t2) {
            return o(this, e3, false, t2);
          }, f.prototype.readUInt32LE = function(e3, t2) {
            return u(this, e3, true, t2);
          }, f.prototype.readUInt32BE = function(e3, t2) {
            return u(this, e3, false, t2);
          }, f.prototype.readInt8 = function(e3, t2) {
            if (t2 || (d(null != e3, "missing offset"), d(e3 < this.length, "Trying to read beyond buffer length")), !(e3 >= this.length))
              return 128 & this[e3] ? -1 * (255 - this[e3] + 1) : this[e3];
          }, f.prototype.readInt16LE = function(e3, t2) {
            return _(this, e3, true, t2);
          }, f.prototype.readInt16BE = function(e3, t2) {
            return _(this, e3, false, t2);
          }, f.prototype.readInt32LE = function(e3, t2) {
            return E(this, e3, true, t2);
          }, f.prototype.readInt32BE = function(e3, t2) {
            return E(this, e3, false, t2);
          }, f.prototype.readFloatLE = function(e3, t2) {
            return I(this, e3, true, t2);
          }, f.prototype.readFloatBE = function(e3, t2) {
            return I(this, e3, false, t2);
          }, f.prototype.readDoubleLE = function(e3, t2) {
            return A(this, e3, true, t2);
          }, f.prototype.readDoubleBE = function(e3, t2) {
            return A(this, e3, false, t2);
          }, f.prototype.writeUInt8 = function(e3, t2, n2) {
            n2 || (d(null != e3, "missing value"), d(null != t2, "missing offset"), d(t2 < this.length, "trying to write beyond buffer length"), Y(e3, 255)), t2 >= this.length || (this[t2] = e3);
          }, f.prototype.writeUInt16LE = function(e3, t2, n2) {
            s(this, e3, t2, true, n2);
          }, f.prototype.writeUInt16BE = function(e3, t2, n2) {
            s(this, e3, t2, false, n2);
          }, f.prototype.writeUInt32LE = function(e3, t2, n2) {
            l(this, e3, t2, true, n2);
          }, f.prototype.writeUInt32BE = function(e3, t2, n2) {
            l(this, e3, t2, false, n2);
          }, f.prototype.writeInt8 = function(e3, t2, n2) {
            n2 || (d(null != e3, "missing value"), d(null != t2, "missing offset"), d(t2 < this.length, "Trying to write beyond buffer length"), F(e3, 127, -128)), t2 >= this.length || (0 <= e3 ? this.writeUInt8(e3, t2, n2) : this.writeUInt8(255 + e3 + 1, t2, n2));
          }, f.prototype.writeInt16LE = function(e3, t2, n2) {
            B(this, e3, t2, true, n2);
          }, f.prototype.writeInt16BE = function(e3, t2, n2) {
            B(this, e3, t2, false, n2);
          }, f.prototype.writeInt32LE = function(e3, t2, n2) {
            L(this, e3, t2, true, n2);
          }, f.prototype.writeInt32BE = function(e3, t2, n2) {
            L(this, e3, t2, false, n2);
          }, f.prototype.writeFloatLE = function(e3, t2, n2) {
            U(this, e3, t2, true, n2);
          }, f.prototype.writeFloatBE = function(e3, t2, n2) {
            U(this, e3, t2, false, n2);
          }, f.prototype.writeDoubleLE = function(e3, t2, n2) {
            x(this, e3, t2, true, n2);
          }, f.prototype.writeDoubleBE = function(e3, t2, n2) {
            x(this, e3, t2, false, n2);
          }, f.prototype.fill = function(e3, t2, n2) {
            if (t2 = t2 || 0, n2 = n2 || this.length, d("number" == typeof (e3 = "string" == typeof (e3 = e3 || 0) ? e3.charCodeAt(0) : e3) && !isNaN(e3), "value is not a number"), d(t2 <= n2, "end < start"), n2 !== t2 && 0 !== this.length) {
              d(0 <= t2 && t2 < this.length, "start out of bounds"), d(0 <= n2 && n2 <= this.length, "end out of bounds");
              for (var r2 = t2; r2 < n2; r2++)
                this[r2] = e3;
            }
          }, f.prototype.inspect = function() {
            for (var e3 = [], t2 = this.length, n2 = 0; n2 < t2; n2++)
              if (e3[n2] = k(this[n2]), n2 === H.INSPECT_MAX_BYTES) {
                e3[n2 + 1] = "...";
                break;
              }
            return "<Buffer " + e3.join(" ") + ">";
          }, f.prototype.toArrayBuffer = function() {
            if ("undefined" == typeof Uint8Array)
              throw new Error("Buffer.toArrayBuffer not supported in this browser");
            if (f._useTypedArrays)
              return new f(this).buffer;
            for (var e3 = new Uint8Array(this.length), t2 = 0, n2 = e3.length; t2 < n2; t2 += 1)
              e3[t2] = this[t2];
            return e3.buffer;
          };
          var t = f.prototype;
          function S(e3, t2, n2) {
            return "number" != typeof e3 ? n2 : t2 <= (e3 = ~~e3) ? t2 : 0 <= e3 || 0 <= (e3 += t2) ? e3 : 0;
          }
          function j(e3) {
            return (e3 = ~~Math.ceil(+e3)) < 0 ? 0 : e3;
          }
          function C(e3) {
            return (Array.isArray || function(e4) {
              return "[object Array]" === Object.prototype.toString.call(e4);
            })(e3);
          }
          function k(e3) {
            return e3 < 16 ? "0" + e3.toString(16) : e3.toString(16);
          }
          function T(e3) {
            for (var t2 = [], n2 = 0; n2 < e3.length; n2++) {
              var r2 = e3.charCodeAt(n2);
              if (r2 <= 127)
                t2.push(e3.charCodeAt(n2));
              else
                for (var o2 = n2, i2 = (55296 <= r2 && r2 <= 57343 && n2++, encodeURIComponent(e3.slice(o2, n2 + 1)).substr(1).split("%")), u2 = 0; u2 < i2.length; u2++)
                  t2.push(parseInt(i2[u2], 16));
            }
            return t2;
          }
          function M(e3) {
            return a.toByteArray(e3);
          }
          function c(e3, t2, n2, r2) {
            for (var o2 = 0; o2 < r2 && !(o2 + n2 >= t2.length || o2 >= e3.length); o2++)
              t2[o2 + n2] = e3[o2];
            return o2;
          }
          function N(e3) {
            try {
              return decodeURIComponent(e3);
            } catch (e4) {
              return String.fromCharCode(65533);
            }
          }
          function Y(e3, t2) {
            d("number" == typeof e3, "cannot write a non-number as a number"), d(0 <= e3, "specified a negative value for writing an unsigned value"), d(e3 <= t2, "value is larger than maximum value for type"), d(Math.floor(e3) === e3, "value has a fractional component");
          }
          function F(e3, t2, n2) {
            d("number" == typeof e3, "cannot write a non-number as a number"), d(e3 <= t2, "value larger than maximum allowed value"), d(n2 <= e3, "value smaller than minimum allowed value"), d(Math.floor(e3) === e3, "value has a fractional component");
          }
          function D(e3, t2, n2) {
            d("number" == typeof e3, "cannot write a non-number as a number"), d(e3 <= t2, "value larger than maximum allowed value"), d(n2 <= e3, "value smaller than minimum allowed value");
          }
          function d(e3, t2) {
            if (!e3)
              throw new Error(t2 || "Failed assertion");
          }
          f._augment = function(e3) {
            return e3._isBuffer = true, e3._get = e3.get, e3._set = e3.set, e3.get = t.get, e3.set = t.set, e3.write = t.write, e3.toString = t.toString, e3.toLocaleString = t.toString, e3.toJSON = t.toJSON, e3.copy = t.copy, e3.slice = t.slice, e3.readUInt8 = t.readUInt8, e3.readUInt16LE = t.readUInt16LE, e3.readUInt16BE = t.readUInt16BE, e3.readUInt32LE = t.readUInt32LE, e3.readUInt32BE = t.readUInt32BE, e3.readInt8 = t.readInt8, e3.readInt16LE = t.readInt16LE, e3.readInt16BE = t.readInt16BE, e3.readInt32LE = t.readInt32LE, e3.readInt32BE = t.readInt32BE, e3.readFloatLE = t.readFloatLE, e3.readFloatBE = t.readFloatBE, e3.readDoubleLE = t.readDoubleLE, e3.readDoubleBE = t.readDoubleBE, e3.writeUInt8 = t.writeUInt8, e3.writeUInt16LE = t.writeUInt16LE, e3.writeUInt16BE = t.writeUInt16BE, e3.writeUInt32LE = t.writeUInt32LE, e3.writeUInt32BE = t.writeUInt32BE, e3.writeInt8 = t.writeInt8, e3.writeInt16LE = t.writeInt16LE, e3.writeInt16BE = t.writeInt16BE, e3.writeInt32LE = t.writeInt32LE, e3.writeInt32BE = t.writeInt32BE, e3.writeFloatLE = t.writeFloatLE, e3.writeFloatBE = t.writeFloatBE, e3.writeDoubleLE = t.writeDoubleLE, e3.writeDoubleBE = t.writeDoubleBE, e3.fill = t.fill, e3.inspect = t.inspect, e3.toArrayBuffer = t.toArrayBuffer, e3;
          };
        }.call(this, O("lYpoI2"), "undefined" != typeof self ? self : "undefined" != typeof window ? window : {}, O("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/node_modules/gulp-browserify/node_modules/buffer/index.js", "/node_modules/gulp-browserify/node_modules/buffer");
      }, { "base64-js": 2, buffer: 3, ieee754: 10, lYpoI2: 11 }], 4: [function(c, d, e) {
        !function(e2, t, a, n, r, o, i, u, s) {
          var a = c("buffer").Buffer, f = 4, l = new a(f);
          l.fill(0);
          d.exports = { hash: function(e3, t2, n2, r2) {
            for (var o2 = t2(function(e4, t3) {
              e4.length % f != 0 && (n3 = e4.length + (f - e4.length % f), e4 = a.concat([e4, l], n3));
              for (var n3, r3 = [], o3 = t3 ? e4.readInt32BE : e4.readInt32LE, i3 = 0; i3 < e4.length; i3 += f)
                r3.push(o3.call(e4, i3));
              return r3;
            }(e3 = a.isBuffer(e3) ? e3 : new a(e3), r2), 8 * e3.length), t2 = r2, i2 = new a(n2), u2 = t2 ? i2.writeInt32BE : i2.writeInt32LE, s2 = 0; s2 < o2.length; s2++)
              u2.call(i2, o2[s2], 4 * s2, true);
            return i2;
          } };
        }.call(this, c("lYpoI2"), "undefined" != typeof self ? self : "undefined" != typeof window ? window : {}, c("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/node_modules/gulp-browserify/node_modules/crypto-browserify/helpers.js", "/node_modules/gulp-browserify/node_modules/crypto-browserify");
      }, { buffer: 3, lYpoI2: 11 }], 5: [function(v, e, _) {
        !function(l, c, u, d, h, p, g, y, w) {
          var u = v("buffer").Buffer, e2 = v("./sha"), t = v("./sha256"), n = v("./rng"), b = { sha1: e2, sha256: t, md5: v("./md5") }, s = 64, a = new u(s);
          function r(e3, n2) {
            var r2 = b[e3 = e3 || "sha1"], o2 = [];
            return r2 || i("algorithm:", e3, "is not yet supported"), { update: function(e4) {
              return u.isBuffer(e4) || (e4 = new u(e4)), o2.push(e4), e4.length, this;
            }, digest: function(e4) {
              var t2 = u.concat(o2), t2 = n2 ? function(e5, t3, n3) {
                u.isBuffer(t3) || (t3 = new u(t3)), u.isBuffer(n3) || (n3 = new u(n3)), t3.length > s ? t3 = e5(t3) : t3.length < s && (t3 = u.concat([t3, a], s));
                for (var r3 = new u(s), o3 = new u(s), i2 = 0; i2 < s; i2++)
                  r3[i2] = 54 ^ t3[i2], o3[i2] = 92 ^ t3[i2];
                return n3 = e5(u.concat([r3, n3])), e5(u.concat([o3, n3]));
              }(r2, n2, t2) : r2(t2);
              return o2 = null, e4 ? t2.toString(e4) : t2;
            } };
          }
          function i() {
            var e3 = [].slice.call(arguments).join(" ");
            throw new Error([e3, "we accept pull requests", "http://github.com/dominictarr/crypto-browserify"].join("\n"));
          }
          a.fill(0), _.createHash = function(e3) {
            return r(e3);
          }, _.createHmac = r, _.randomBytes = function(e3, t2) {
            if (!t2 || !t2.call)
              return new u(n(e3));
            try {
              t2.call(this, void 0, new u(n(e3)));
            } catch (e4) {
              t2(e4);
            }
          };
          var o, f = ["createCredentials", "createCipher", "createCipheriv", "createDecipher", "createDecipheriv", "createSign", "createVerify", "createDiffieHellman", "pbkdf2"], m = function(e3) {
            _[e3] = function() {
              i("sorry,", e3, "is not implemented yet");
            };
          };
          for (o in f)
            m(f[o]);
        }.call(this, v("lYpoI2"), "undefined" != typeof self ? self : "undefined" != typeof window ? window : {}, v("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/node_modules/gulp-browserify/node_modules/crypto-browserify/index.js", "/node_modules/gulp-browserify/node_modules/crypto-browserify");
      }, { "./md5": 6, "./rng": 7, "./sha": 8, "./sha256": 9, buffer: 3, lYpoI2: 11 }], 6: [function(w, b, e) {
        !function(e2, r, o, i, u, a, f, l, y) {
          var t = w("./helpers");
          function n(e3, t2) {
            e3[t2 >> 5] |= 128 << t2 % 32, e3[14 + (t2 + 64 >>> 9 << 4)] = t2;
            for (var n2 = 1732584193, r2 = -271733879, o2 = -1732584194, i2 = 271733878, u2 = 0; u2 < e3.length; u2 += 16) {
              var s2 = n2, a2 = r2, f2 = o2, l2 = i2, n2 = c(n2, r2, o2, i2, e3[u2 + 0], 7, -680876936), i2 = c(i2, n2, r2, o2, e3[u2 + 1], 12, -389564586), o2 = c(o2, i2, n2, r2, e3[u2 + 2], 17, 606105819), r2 = c(r2, o2, i2, n2, e3[u2 + 3], 22, -1044525330);
              n2 = c(n2, r2, o2, i2, e3[u2 + 4], 7, -176418897), i2 = c(i2, n2, r2, o2, e3[u2 + 5], 12, 1200080426), o2 = c(o2, i2, n2, r2, e3[u2 + 6], 17, -1473231341), r2 = c(r2, o2, i2, n2, e3[u2 + 7], 22, -45705983), n2 = c(n2, r2, o2, i2, e3[u2 + 8], 7, 1770035416), i2 = c(i2, n2, r2, o2, e3[u2 + 9], 12, -1958414417), o2 = c(o2, i2, n2, r2, e3[u2 + 10], 17, -42063), r2 = c(r2, o2, i2, n2, e3[u2 + 11], 22, -1990404162), n2 = c(n2, r2, o2, i2, e3[u2 + 12], 7, 1804603682), i2 = c(i2, n2, r2, o2, e3[u2 + 13], 12, -40341101), o2 = c(o2, i2, n2, r2, e3[u2 + 14], 17, -1502002290), n2 = d(n2, r2 = c(r2, o2, i2, n2, e3[u2 + 15], 22, 1236535329), o2, i2, e3[u2 + 1], 5, -165796510), i2 = d(i2, n2, r2, o2, e3[u2 + 6], 9, -1069501632), o2 = d(o2, i2, n2, r2, e3[u2 + 11], 14, 643717713), r2 = d(r2, o2, i2, n2, e3[u2 + 0], 20, -373897302), n2 = d(n2, r2, o2, i2, e3[u2 + 5], 5, -701558691), i2 = d(i2, n2, r2, o2, e3[u2 + 10], 9, 38016083), o2 = d(o2, i2, n2, r2, e3[u2 + 15], 14, -660478335), r2 = d(r2, o2, i2, n2, e3[u2 + 4], 20, -405537848), n2 = d(n2, r2, o2, i2, e3[u2 + 9], 5, 568446438), i2 = d(i2, n2, r2, o2, e3[u2 + 14], 9, -1019803690), o2 = d(o2, i2, n2, r2, e3[u2 + 3], 14, -187363961), r2 = d(r2, o2, i2, n2, e3[u2 + 8], 20, 1163531501), n2 = d(n2, r2, o2, i2, e3[u2 + 13], 5, -1444681467), i2 = d(i2, n2, r2, o2, e3[u2 + 2], 9, -51403784), o2 = d(o2, i2, n2, r2, e3[u2 + 7], 14, 1735328473), n2 = h(n2, r2 = d(r2, o2, i2, n2, e3[u2 + 12], 20, -1926607734), o2, i2, e3[u2 + 5], 4, -378558), i2 = h(i2, n2, r2, o2, e3[u2 + 8], 11, -2022574463), o2 = h(o2, i2, n2, r2, e3[u2 + 11], 16, 1839030562), r2 = h(r2, o2, i2, n2, e3[u2 + 14], 23, -35309556), n2 = h(n2, r2, o2, i2, e3[u2 + 1], 4, -1530992060), i2 = h(i2, n2, r2, o2, e3[u2 + 4], 11, 1272893353), o2 = h(o2, i2, n2, r2, e3[u2 + 7], 16, -155497632), r2 = h(r2, o2, i2, n2, e3[u2 + 10], 23, -1094730640), n2 = h(n2, r2, o2, i2, e3[u2 + 13], 4, 681279174), i2 = h(i2, n2, r2, o2, e3[u2 + 0], 11, -358537222), o2 = h(o2, i2, n2, r2, e3[u2 + 3], 16, -722521979), r2 = h(r2, o2, i2, n2, e3[u2 + 6], 23, 76029189), n2 = h(n2, r2, o2, i2, e3[u2 + 9], 4, -640364487), i2 = h(i2, n2, r2, o2, e3[u2 + 12], 11, -421815835), o2 = h(o2, i2, n2, r2, e3[u2 + 15], 16, 530742520), n2 = p(n2, r2 = h(r2, o2, i2, n2, e3[u2 + 2], 23, -995338651), o2, i2, e3[u2 + 0], 6, -198630844), i2 = p(i2, n2, r2, o2, e3[u2 + 7], 10, 1126891415), o2 = p(o2, i2, n2, r2, e3[u2 + 14], 15, -1416354905), r2 = p(r2, o2, i2, n2, e3[u2 + 5], 21, -57434055), n2 = p(n2, r2, o2, i2, e3[u2 + 12], 6, 1700485571), i2 = p(i2, n2, r2, o2, e3[u2 + 3], 10, -1894986606), o2 = p(o2, i2, n2, r2, e3[u2 + 10], 15, -1051523), r2 = p(r2, o2, i2, n2, e3[u2 + 1], 21, -2054922799), n2 = p(n2, r2, o2, i2, e3[u2 + 8], 6, 1873313359), i2 = p(i2, n2, r2, o2, e3[u2 + 15], 10, -30611744), o2 = p(o2, i2, n2, r2, e3[u2 + 6], 15, -1560198380), r2 = p(r2, o2, i2, n2, e3[u2 + 13], 21, 1309151649), n2 = p(n2, r2, o2, i2, e3[u2 + 4], 6, -145523070), i2 = p(i2, n2, r2, o2, e3[u2 + 11], 10, -1120210379), o2 = p(o2, i2, n2, r2, e3[u2 + 2], 15, 718787259), r2 = p(r2, o2, i2, n2, e3[u2 + 9], 21, -343485551), n2 = g(n2, s2), r2 = g(r2, a2), o2 = g(o2, f2), i2 = g(i2, l2);
            }
            return Array(n2, r2, o2, i2);
          }
          function s(e3, t2, n2, r2, o2, i2) {
            return g((t2 = g(g(t2, e3), g(r2, i2))) << o2 | t2 >>> 32 - o2, n2);
          }
          function c(e3, t2, n2, r2, o2, i2, u2) {
            return s(t2 & n2 | ~t2 & r2, e3, t2, o2, i2, u2);
          }
          function d(e3, t2, n2, r2, o2, i2, u2) {
            return s(t2 & r2 | n2 & ~r2, e3, t2, o2, i2, u2);
          }
          function h(e3, t2, n2, r2, o2, i2, u2) {
            return s(t2 ^ n2 ^ r2, e3, t2, o2, i2, u2);
          }
          function p(e3, t2, n2, r2, o2, i2, u2) {
            return s(n2 ^ (t2 | ~r2), e3, t2, o2, i2, u2);
          }
          function g(e3, t2) {
            var n2 = (65535 & e3) + (65535 & t2);
            return (e3 >> 16) + (t2 >> 16) + (n2 >> 16) << 16 | 65535 & n2;
          }
          b.exports = function(e3) {
            return t.hash(e3, n, 16);
          };
        }.call(this, w("lYpoI2"), "undefined" != typeof self ? self : "undefined" != typeof window ? window : {}, w("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/node_modules/gulp-browserify/node_modules/crypto-browserify/md5.js", "/node_modules/gulp-browserify/node_modules/crypto-browserify");
      }, { "./helpers": 4, buffer: 3, lYpoI2: 11 }], 7: [function(e, l, t) {
        !function(e2, t2, n, r, o, i, u, s, f) {
          l.exports = function(e3) {
            for (var t3, n2 = new Array(e3), r2 = 0; r2 < e3; r2++)
              0 == (3 & r2) && (t3 = 4294967296 * Math.random()), n2[r2] = t3 >>> ((3 & r2) << 3) & 255;
            return n2;
          };
        }.call(this, e("lYpoI2"), "undefined" != typeof self ? self : "undefined" != typeof window ? window : {}, e("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/node_modules/gulp-browserify/node_modules/crypto-browserify/rng.js", "/node_modules/gulp-browserify/node_modules/crypto-browserify");
      }, { buffer: 3, lYpoI2: 11 }], 8: [function(c, d, e) {
        !function(e2, t, n, r, o, s, a, f, l) {
          var i = c("./helpers");
          function u(l2, c2) {
            l2[c2 >> 5] |= 128 << 24 - c2 % 32, l2[15 + (c2 + 64 >> 9 << 4)] = c2;
            for (var e3, t2, n2, r2 = Array(80), o2 = 1732584193, i2 = -271733879, u2 = -1732584194, s2 = 271733878, d2 = -1009589776, h = 0; h < l2.length; h += 16) {
              for (var p = o2, g = i2, y = u2, w = s2, b = d2, a2 = 0; a2 < 80; a2++) {
                r2[a2] = a2 < 16 ? l2[h + a2] : v(r2[a2 - 3] ^ r2[a2 - 8] ^ r2[a2 - 14] ^ r2[a2 - 16], 1);
                var f2 = m(m(v(o2, 5), (f2 = i2, t2 = u2, n2 = s2, (e3 = a2) < 20 ? f2 & t2 | ~f2 & n2 : !(e3 < 40) && e3 < 60 ? f2 & t2 | f2 & n2 | t2 & n2 : f2 ^ t2 ^ n2)), m(m(d2, r2[a2]), (e3 = a2) < 20 ? 1518500249 : e3 < 40 ? 1859775393 : e3 < 60 ? -1894007588 : -899497514)), d2 = s2, s2 = u2, u2 = v(i2, 30), i2 = o2, o2 = f2;
              }
              o2 = m(o2, p), i2 = m(i2, g), u2 = m(u2, y), s2 = m(s2, w), d2 = m(d2, b);
            }
            return Array(o2, i2, u2, s2, d2);
          }
          function m(e3, t2) {
            var n2 = (65535 & e3) + (65535 & t2);
            return (e3 >> 16) + (t2 >> 16) + (n2 >> 16) << 16 | 65535 & n2;
          }
          function v(e3, t2) {
            return e3 << t2 | e3 >>> 32 - t2;
          }
          d.exports = function(e3) {
            return i.hash(e3, u, 20, true);
          };
        }.call(this, c("lYpoI2"), "undefined" != typeof self ? self : "undefined" != typeof window ? window : {}, c("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/node_modules/gulp-browserify/node_modules/crypto-browserify/sha.js", "/node_modules/gulp-browserify/node_modules/crypto-browserify");
      }, { "./helpers": 4, buffer: 3, lYpoI2: 11 }], 9: [function(c, d, e) {
        !function(e2, t, n, r, u, s, a, f, l) {
          function b(e3, t2) {
            var n2 = (65535 & e3) + (65535 & t2);
            return (e3 >> 16) + (t2 >> 16) + (n2 >> 16) << 16 | 65535 & n2;
          }
          function o(e3, l2) {
            var c2, d2 = new Array(1116352408, 1899447441, 3049323471, 3921009573, 961987163, 1508970993, 2453635748, 2870763221, 3624381080, 310598401, 607225278, 1426881987, 1925078388, 2162078206, 2614888103, 3248222580, 3835390401, 4022224774, 264347078, 604807628, 770255983, 1249150122, 1555081692, 1996064986, 2554220882, 2821834349, 2952996808, 3210313671, 3336571891, 3584528711, 113926993, 338241895, 666307205, 773529912, 1294757372, 1396182291, 1695183700, 1986661051, 2177026350, 2456956037, 2730485921, 2820302411, 3259730800, 3345764771, 3516065817, 3600352804, 4094571909, 275423344, 430227734, 506948616, 659060556, 883997877, 958139571, 1322822218, 1537002063, 1747873779, 1955562222, 2024104815, 2227730452, 2361852424, 2428436474, 2756734187, 3204031479, 3329325298), t2 = new Array(1779033703, 3144134277, 1013904242, 2773480762, 1359893119, 2600822924, 528734635, 1541459225), n2 = new Array(64);
            e3[l2 >> 5] |= 128 << 24 - l2 % 32, e3[15 + (l2 + 64 >> 9 << 4)] = l2;
            for (var r2, o2, h = 0; h < e3.length; h += 16) {
              for (var i2 = t2[0], u2 = t2[1], s2 = t2[2], p = t2[3], a2 = t2[4], g = t2[5], y = t2[6], w = t2[7], f2 = 0; f2 < 64; f2++)
                n2[f2] = f2 < 16 ? e3[f2 + h] : b(b(b((o2 = n2[f2 - 2], m(o2, 17) ^ m(o2, 19) ^ v(o2, 10)), n2[f2 - 7]), (o2 = n2[f2 - 15], m(o2, 7) ^ m(o2, 18) ^ v(o2, 3))), n2[f2 - 16]), c2 = b(b(b(b(w, m(o2 = a2, 6) ^ m(o2, 11) ^ m(o2, 25)), a2 & g ^ ~a2 & y), d2[f2]), n2[f2]), r2 = b(m(r2 = i2, 2) ^ m(r2, 13) ^ m(r2, 22), i2 & u2 ^ i2 & s2 ^ u2 & s2), w = y, y = g, g = a2, a2 = b(p, c2), p = s2, s2 = u2, u2 = i2, i2 = b(c2, r2);
              t2[0] = b(i2, t2[0]), t2[1] = b(u2, t2[1]), t2[2] = b(s2, t2[2]), t2[3] = b(p, t2[3]), t2[4] = b(a2, t2[4]), t2[5] = b(g, t2[5]), t2[6] = b(y, t2[6]), t2[7] = b(w, t2[7]);
            }
            return t2;
          }
          var i = c("./helpers"), m = function(e3, t2) {
            return e3 >>> t2 | e3 << 32 - t2;
          }, v = function(e3, t2) {
            return e3 >>> t2;
          };
          d.exports = function(e3) {
            return i.hash(e3, o, 32, true);
          };
        }.call(this, c("lYpoI2"), "undefined" != typeof self ? self : "undefined" != typeof window ? window : {}, c("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/node_modules/gulp-browserify/node_modules/crypto-browserify/sha256.js", "/node_modules/gulp-browserify/node_modules/crypto-browserify");
      }, { "./helpers": 4, buffer: 3, lYpoI2: 11 }], 10: [function(e, t, f) {
        !function(e2, t2, n, r, o, i, u, s, a) {
          f.read = function(e3, t3, n2, r2, o2) {
            var i2, u2, l = 8 * o2 - r2 - 1, c = (1 << l) - 1, d = c >> 1, s2 = -7, a2 = n2 ? o2 - 1 : 0, f2 = n2 ? -1 : 1, o2 = e3[t3 + a2];
            for (a2 += f2, i2 = o2 & (1 << -s2) - 1, o2 >>= -s2, s2 += l; 0 < s2; i2 = 256 * i2 + e3[t3 + a2], a2 += f2, s2 -= 8)
              ;
            for (u2 = i2 & (1 << -s2) - 1, i2 >>= -s2, s2 += r2; 0 < s2; u2 = 256 * u2 + e3[t3 + a2], a2 += f2, s2 -= 8)
              ;
            if (0 === i2)
              i2 = 1 - d;
            else {
              if (i2 === c)
                return u2 ? NaN : 1 / 0 * (o2 ? -1 : 1);
              u2 += Math.pow(2, r2), i2 -= d;
            }
            return (o2 ? -1 : 1) * u2 * Math.pow(2, i2 - r2);
          }, f.write = function(e3, t3, l, n2, r2, c) {
            var o2, i2, u2 = 8 * c - r2 - 1, s2 = (1 << u2) - 1, a2 = s2 >> 1, d = 23 === r2 ? Math.pow(2, -24) - Math.pow(2, -77) : 0, f2 = n2 ? 0 : c - 1, h = n2 ? 1 : -1, c = t3 < 0 || 0 === t3 && 1 / t3 < 0 ? 1 : 0;
            for (t3 = Math.abs(t3), isNaN(t3) || t3 === 1 / 0 ? (i2 = isNaN(t3) ? 1 : 0, o2 = s2) : (o2 = Math.floor(Math.log(t3) / Math.LN2), t3 * (n2 = Math.pow(2, -o2)) < 1 && (o2--, n2 *= 2), 2 <= (t3 += 1 <= o2 + a2 ? d / n2 : d * Math.pow(2, 1 - a2)) * n2 && (o2++, n2 /= 2), s2 <= o2 + a2 ? (i2 = 0, o2 = s2) : 1 <= o2 + a2 ? (i2 = (t3 * n2 - 1) * Math.pow(2, r2), o2 += a2) : (i2 = t3 * Math.pow(2, a2 - 1) * Math.pow(2, r2), o2 = 0)); 8 <= r2; e3[l + f2] = 255 & i2, f2 += h, i2 /= 256, r2 -= 8)
              ;
            for (o2 = o2 << r2 | i2, u2 += r2; 0 < u2; e3[l + f2] = 255 & o2, f2 += h, o2 /= 256, u2 -= 8)
              ;
            e3[l + f2 - h] |= 128 * c;
          };
        }.call(this, e("lYpoI2"), "undefined" != typeof self ? self : "undefined" != typeof window ? window : {}, e("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/node_modules/gulp-browserify/node_modules/ieee754/index.js", "/node_modules/gulp-browserify/node_modules/ieee754");
      }, { buffer: 3, lYpoI2: 11 }], 11: [function(e, h, t) {
        !function(e2, t2, n, r, o, f, l, c, d) {
          var i, u, s;
          function a() {
          }
          (e2 = h.exports = {}).nextTick = (u = "undefined" != typeof window && window.setImmediate, s = "undefined" != typeof window && window.postMessage && window.addEventListener, u ? function(e3) {
            return window.setImmediate(e3);
          } : s ? (i = [], window.addEventListener("message", function(e3) {
            var t3 = e3.source;
            t3 !== window && null !== t3 || "process-tick" !== e3.data || (e3.stopPropagation(), 0 < i.length && i.shift()());
          }, true), function(e3) {
            i.push(e3), window.postMessage("process-tick", "*");
          }) : function(e3) {
            setTimeout(e3, 0);
          }), e2.title = "browser", e2.browser = true, e2.env = {}, e2.argv = [], e2.on = a, e2.addListener = a, e2.once = a, e2.off = a, e2.removeListener = a, e2.removeAllListeners = a, e2.emit = a, e2.binding = function(e3) {
            throw new Error("process.binding is not supported");
          }, e2.cwd = function() {
            return "/";
          }, e2.chdir = function(e3) {
            throw new Error("process.chdir is not supported");
          };
        }.call(this, e("lYpoI2"), "undefined" != typeof self ? self : "undefined" != typeof window ? window : {}, e("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/node_modules/gulp-browserify/node_modules/process/browser.js", "/node_modules/gulp-browserify/node_modules/process");
      }, { buffer: 3, lYpoI2: 11 }] }, {}, [1])(1);
    });
  })(object_hash);
  var hash = object_hash.exports;
  const items = vue.ref(window.ZnPb_ConditionsData.conditions_saved_data || {});
  const cache = vue.ref({});
  const useRuleData = () => {
    function fetchItems(condition_id, searchKeyword = "", page = 1) {
      const cacheKey = createCacheKey({
        condition_id,
        searchKeyword,
        page
      });
      if (typeof cache.value[cacheKey] !== "undefined") {
        return Promise.resolve(cache.value[cacheKey]);
      } else {
        return getOptions(
          condition_id,
          searchKeyword,
          page
        ).then((response) => {
          cache.value[cacheKey] = response.data;
          const existingItems = items.value[condition_id] || [];
          items.value[condition_id] = unionBy$1(existingItems, response.data, "id");
          return response.data;
        });
      }
    }
    function getData(conditionID, id) {
      if (items.value[conditionID]) {
        return items.value[conditionID].find((item) => {
          return item.id === id;
        });
      }
      return null;
    }
    function createCacheKey(object) {
      return hash(object);
    }
    return {
      items,
      fetchItems,
      getData
    };
  };
  var RuleOptions_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$e = {
    name: "RuleOptions",
    props: ["rule", "modelValue"],
    setup(props, { emit }) {
      const { fetchItems, getData } = useRuleData();
      const computedSearchKeyword = vue.ref("");
      const searchLoading = vue.ref(false);
      const items2 = vue.ref([]);
      const searchInput = vue.ref(null);
      const stopSearch = vue.ref(false);
      const postsPerPage = 25;
      let page = 1;
      const computedSortedItems = vue.computed(() => {
        let itemsWithData = items2.value.filter((item) => !props.modelValue.includes(item.id));
        if (computedSearchKeyword.value.length > 0) {
          itemsWithData = itemsWithData.filter((item) => item.title.toLowerCase().indexOf(computedSearchKeyword.value) != -1);
        }
        return itemsWithData;
      });
      const computedSavedValues = vue.computed(() => {
        const savedItems = props.modelValue || [];
        let itemsWithData = savedItems.map((id2) => {
          return getData(props.rule.id, id2);
        });
        if (computedSearchKeyword.value.length > 0) {
          itemsWithData = itemsWithData.filter((item) => item.title.toLowerCase().indexOf(computedSearchKeyword.value) != -1);
        }
        return itemsWithData;
      });
      const computedModelValue = vue.computed({
        get() {
          return props.modelValue || [];
        },
        set(newValue) {
          emit("update:modelValue", newValue);
        }
      });
      vue.watch(computedSearchKeyword, (newValue) => {
        if (newValue.length > 0) {
          stopSearch.value = false;
          debouncedGetPosts();
        }
      });
      const { id } = props.rule;
      function get_posts() {
        searchLoading.value = true;
        fetchItems(id, computedSearchKeyword.value, page).then((response) => {
          items2.value = unionBy$1(items2.value, response, "id");
          if (response.length < postsPerPage) {
            stopSearch.value = true;
          } else {
            stopSearch.value = false;
          }
        }).finally(() => {
          searchLoading.value = false;
        });
      }
      const debouncedGetPosts = debounce$1(() => {
        get_posts();
      }, 300);
      get_posts();
      function onScrollEnd() {
        if (!stopSearch.value && !searchLoading.value) {
          page++;
          get_posts();
        }
      }
      vue.onMounted(() => {
        setTimeout(() => {
          searchInput.value.focus();
        }, 100);
      });
      return {
        searchInput,
        computedModelValue,
        computedSearchKeyword,
        items: items2,
        searchLoading,
        onScrollEnd,
        computedSortedItems,
        computedSavedValues,
        stopSearch
      };
    }
  };
  const _hoisted_1$a = { class: "znpbpro-templates-ruleOptionsWrapper" };
  const _hoisted_2$6 = {
    key: 0,
    class: "znpbpro-templates-notFoundText"
  };
  const _hoisted_3$4 = {
    key: 1,
    class: "znpbpro-templates-notFoundText"
  };
  function _sfc_render$d(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_BaseInput = vue.resolveComponent("BaseInput");
    const _component_InputCheckbox = vue.resolveComponent("InputCheckbox");
    const _component_ListScroll = vue.resolveComponent("ListScroll");
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$a, [
      vue.createVNode(_component_BaseInput, {
        class: "znpbpro-templates-ruleOptionsSearch",
        modelValue: $setup.computedSearchKeyword,
        "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $setup.computedSearchKeyword = $event),
        placeholder: $props.rule.search_placeholder || _ctx.$translate("search_elements"),
        clearable: true,
        icon: "search",
        autocomplete: "off",
        ref: "searchInput"
      }, null, 8, ["modelValue", "placeholder"]),
      vue.createVNode(_component_ListScroll, {
        onScrollEnd: $setup.onScrollEnd,
        loading: $setup.searchLoading
      }, {
        default: vue.withCtx(() => [
          $setup.computedSavedValues.length > 0 ? (vue.openBlock(true), vue.createElementBlock(vue.Fragment, { key: 0 }, vue.renderList($setup.computedSavedValues, (item) => {
            return vue.openBlock(), vue.createElementBlock("div", {
              key: "saved" + item.id
            }, [
              vue.createVNode(_component_InputCheckbox, {
                modelValue: $setup.computedModelValue,
                "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => $setup.computedModelValue = $event),
                "option-value": item.id
              }, {
                default: vue.withCtx(() => [
                  vue.createTextVNode(vue.toDisplayString(item.title), 1)
                ]),
                _: 2
              }, 1032, ["modelValue", "option-value"])
            ]);
          }), 128)) : vue.createCommentVNode("", true),
          (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($setup.computedSortedItems, (item) => {
            return vue.openBlock(), vue.createElementBlock("div", {
              key: item.id
            }, [
              vue.createVNode(_component_InputCheckbox, {
                modelValue: $setup.computedModelValue,
                "onUpdate:modelValue": _cache[2] || (_cache[2] = ($event) => $setup.computedModelValue = $event),
                "option-value": item.id
              }, {
                default: vue.withCtx(() => [
                  vue.createTextVNode(vue.toDisplayString(item.title), 1)
                ]),
                _: 2
              }, 1032, ["modelValue", "option-value"])
            ]);
          }), 128))
        ]),
        _: 1
      }, 8, ["onScrollEnd", "loading"]),
      !$setup.searchLoading && $setup.computedSavedValues.length === 0 && $setup.computedSortedItems.length === 0 ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_2$6, vue.toDisplayString($props.rule.not_found_text || _ctx.$translate("no_items_found")), 1)) : $setup.stopSearch ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_3$4, vue.toDisplayString($props.rule.no_more_items_text || _ctx.$translate("no_more_items")), 1)) : vue.createCommentVNode("", true)
    ]);
  }
  var RuleOptions = /* @__PURE__ */ _export_sfc(_sfc_main$e, [["render", _sfc_render$d]]);
  var Rule_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$d = {
    name: "Rule",
    components: {
      RuleOptions
    },
    props: {
      rule: {
        type: Object,
        required: true
      },
      modelValue: {}
    },
    setup(props, { emit }) {
      const showTooltip = vue.ref(false);
      const hasOptionsTooltip = vue.computed(() => {
        return props.rule.options || props.rule.options_callback;
      });
      const computedOptionsModel = vue.computed({
        get() {
          return props.modelValue || [];
        },
        set(newValue) {
          updateValue(newValue);
        }
      });
      const isRuleDisabled = vue.computed(() => {
        if (hasOptionsTooltip.value) {
          return !!!props.modelValue;
        }
        return false;
      });
      const checkboxModel = vue.computed({
        get() {
          return !!props.modelValue;
        },
        set(newValue) {
          if (newValue) {
            updateValue(true);
          } else {
            updateValue(null);
          }
        }
      });
      function onRuleClick() {
        if (hasOptionsTooltip.value) {
          showTooltip.value = !showTooltip.value;
        } else {
          props.rule.rule_id;
          updateValue(props.modelValue ? null : true);
        }
      }
      function updateValue(newValue) {
        const optionId = props.rule.id;
        emit("update:modelValue", { optionId, newValue });
      }
      return {
        computedOptionsModel,
        showTooltip,
        onRuleClick,
        isRuleDisabled,
        checkboxModel,
        hasOptionsTooltip
      };
    }
  };
  const _hoisted_1$9 = { class: "znpbpro-templates-ruleName" };
  function _sfc_render$c(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_InputCheckbox = vue.resolveComponent("InputCheckbox");
    const _component_Icon = vue.resolveComponent("Icon");
    const _component_RuleOptions = vue.resolveComponent("RuleOptions");
    const _component_Tooltip = vue.resolveComponent("Tooltip");
    return vue.openBlock(), vue.createBlock(_component_Tooltip, {
      trigger: null,
      class: "znpbpro-templates-ruleWrapper",
      show: $setup.showTooltip,
      placement: "bottom",
      "close-on-outside-click": true,
      onClose: _cache[4] || (_cache[4] = ($event) => $setup.showTooltip = false)
    }, {
      content: vue.withCtx(() => [
        vue.createElementVNode("div", null, [
          vue.createVNode(_component_RuleOptions, {
            rule: $props.rule,
            modelValue: $setup.computedOptionsModel,
            "onUpdate:modelValue": _cache[3] || (_cache[3] = ($event) => $setup.computedOptionsModel = $event)
          }, null, 8, ["rule", "modelValue"])
        ])
      ]),
      default: vue.withCtx(() => [
        vue.createElementVNode("div", {
          class: "znpbpro-templates-ruleNameWrapper",
          onClick: _cache[2] || (_cache[2] = vue.withModifiers((...args) => $setup.onRuleClick && $setup.onRuleClick(...args), ["stop"]))
        }, [
          vue.createVNode(_component_InputCheckbox, {
            class: "znpbpro-templates-ruleCheckbox",
            disabled: $setup.isRuleDisabled,
            modelValue: $setup.checkboxModel,
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $setup.checkboxModel = $event),
            onClick: _cache[1] || (_cache[1] = vue.withModifiers(() => {
            }, ["stop"]))
          }, null, 8, ["disabled", "modelValue"]),
          vue.createElementVNode("span", _hoisted_1$9, vue.toDisplayString($props.rule.name), 1),
          $setup.hasOptionsTooltip ? (vue.openBlock(), vue.createBlock(_component_Icon, {
            key: 0,
            icon: "select",
            class: vue.normalizeClass(["znpbpro-templates-ruleDropdownIcon", { "znpbpro-templates-ruleDropdownIcon--inverted": $setup.showTooltip }])
          }, null, 8, ["class"])) : vue.createCommentVNode("", true)
        ])
      ]),
      _: 1
    }, 8, ["show"]);
  }
  var Rule = /* @__PURE__ */ _export_sfc(_sfc_main$d, [["render", _sfc_render$c]]);
  var Assignments_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$c = {
    name: "Assignments",
    components: {
      Rule
    },
    props: {
      config: {
        type: Array,
        required: true
      },
      modelValue: {}
    },
    setup(props, { emit }) {
      const value = vue.computed(() => props.modelValue || {});
      const computedRuleCategories = vue.computed(() => {
        return props.config.sort((a, b) => {
          return a.priority < b.priority ? -1 : 1;
        });
      });
      function onModelValueUpdate(newValueData) {
        const { optionId, newValue } = newValueData;
        const clonedValues = cloneDeep(props.modelValue);
        if (newValue === null) {
          delete clonedValues[optionId];
        } else {
          clonedValues[optionId] = newValue;
        }
        if (Object.keys(clonedValues).length === 0) {
          emit("update:modelValue", null);
        } else {
          emit("update:modelValue", clonedValues);
        }
      }
      return {
        value,
        onModelValueUpdate,
        computedRuleCategories
      };
    }
  };
  const _hoisted_1$8 = { class: "znpbpro-templates-assignmentContent" };
  const _hoisted_2$5 = { class: "znpbpro-templates-categoryTitle" };
  function _sfc_render$b(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_Rule = vue.resolveComponent("Rule");
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$8, [
      (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($setup.computedRuleCategories, (categoryConfig) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          key: categoryConfig.id
        }, [
          vue.createElementVNode("h3", _hoisted_2$5, vue.toDisplayString(categoryConfig.name), 1),
          (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(categoryConfig.rules, (rule) => {
            return vue.openBlock(), vue.createBlock(_component_Rule, {
              key: rule.id,
              rule,
              modelValue: $setup.value[rule.id],
              "onUpdate:modelValue": $setup.onModelValueUpdate
            }, null, 8, ["rule", "modelValue", "onUpdate:modelValue"]);
          }), 128))
        ]);
      }), 128))
    ]);
  }
  var Assignments = /* @__PURE__ */ _export_sfc(_sfc_main$c, [["render", _sfc_render$b]]);
  var TemplateAssignments_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$b = {
    name: "TemplateAssignments",
    components: {
      Assignments
    },
    props: {
      modelValue: {}
    },
    setup(props, { emit }) {
      let translatedStrings = {
        tabNameUse: window.zb.i18n.translate("use_on"),
        tabNameExclude: window.zb.i18n.translate("exclude_from")
      };
      const showOnValue = vue.computed({
        get() {
          return get(props.modelValue, "show_on", {});
        },
        set(newValue) {
          const newValuesToUpdate = __spreadProps(__spreadValues({}, props.modelValue), {
            show_on: newValue
          });
          if (newValue === null) {
            delete newValuesToUpdate.show_on;
          }
          emit("update:modelValue", newValuesToUpdate);
        }
      });
      const hideOnValue = vue.computed({
        get() {
          return get(props.modelValue, "hide_on", {});
        },
        set(newValue) {
          const newValuesToUpdate = __spreadProps(__spreadValues({}, props.modelValue), {
            hide_on: newValue
          });
          if (newValue === null) {
            delete newValuesToUpdate.hide_on;
          }
          emit("update:modelValue", newValuesToUpdate);
        }
      });
      const categories = window.ZnPb_ConditionsData.categories;
      const conditions = window.ZnPb_ConditionsData.conditions;
      const categoriesAndRules = [];
      function getRulesForCategory(category) {
        const foundRules = [];
        forEach$1(conditions, (conditionConfig, conditionID) => {
          if (conditionConfig.category === category) {
            foundRules.push(conditionConfig);
          }
        });
        return foundRules;
      }
      forEach$1(categories, (category, categoryId) => {
        __spreadValues({
          priority: 100
        }, category);
        categoriesAndRules.push(__spreadProps(__spreadValues({
          priority: 100
        }, category), {
          rules: getRulesForCategory(categoryId)
        }));
      });
      return {
        translatedStrings,
        showOnValue,
        hideOnValue,
        categoriesAndRules
      };
    }
  };
  const _hoisted_1$7 = { class: "znpbpro-templates-modalWrapper" };
  function _sfc_render$a(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_Assignments = vue.resolveComponent("Assignments");
    const _component_Tab = vue.resolveComponent("Tab");
    const _component_Tabs = vue.resolveComponent("Tabs");
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$7, [
      vue.createVNode(_component_Tabs, null, {
        default: vue.withCtx(() => [
          vue.createVNode(_component_Tab, {
            name: $setup.translatedStrings.tabNameUse
          }, {
            default: vue.withCtx(() => [
              vue.createVNode(_component_Assignments, {
                modelValue: $setup.showOnValue,
                "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $setup.showOnValue = $event),
                config: $setup.categoriesAndRules
              }, null, 8, ["modelValue", "config"])
            ]),
            _: 1
          }, 8, ["name"]),
          vue.createVNode(_component_Tab, {
            name: $setup.translatedStrings.tabNameExclude
          }, {
            default: vue.withCtx(() => [
              vue.createVNode(_component_Assignments, {
                modelValue: $setup.hideOnValue,
                "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => $setup.hideOnValue = $event),
                config: $setup.categoriesAndRules
              }, null, 8, ["modelValue", "config"])
            ]),
            _: 1
          }, 8, ["name"])
        ]),
        _: 1
      })
    ]);
  }
  var TemplateAssignments$1 = /* @__PURE__ */ _export_sfc(_sfc_main$b, [["render", _sfc_render$a]]);
  var TemplateAssignments = {
    id: "template_assignments",
    component: TemplateAssignments$1
  };
  var axios$2 = { exports: {} };
  var bind$2 = function bind2(fn, thisArg) {
    return function wrap() {
      var args = new Array(arguments.length);
      for (var i = 0; i < args.length; i++) {
        args[i] = arguments[i];
      }
      return fn.apply(thisArg, args);
    };
  };
  var bind$1 = bind$2;
  var toString = Object.prototype.toString;
  var kindOf = function(cache2) {
    return function(thing) {
      var str = toString.call(thing);
      return cache2[str] || (cache2[str] = str.slice(8, -1).toLowerCase());
    };
  }(/* @__PURE__ */ Object.create(null));
  function kindOfTest(type) {
    type = type.toLowerCase();
    return function isKindOf(thing) {
      return kindOf(thing) === type;
    };
  }
  function isArray(val) {
    return Array.isArray(val);
  }
  function isUndefined(val) {
    return typeof val === "undefined";
  }
  function isBuffer(val) {
    return val !== null && !isUndefined(val) && val.constructor !== null && !isUndefined(val.constructor) && typeof val.constructor.isBuffer === "function" && val.constructor.isBuffer(val);
  }
  var isArrayBuffer = kindOfTest("ArrayBuffer");
  function isArrayBufferView(val) {
    var result;
    if (typeof ArrayBuffer !== "undefined" && ArrayBuffer.isView) {
      result = ArrayBuffer.isView(val);
    } else {
      result = val && val.buffer && isArrayBuffer(val.buffer);
    }
    return result;
  }
  function isString(val) {
    return typeof val === "string";
  }
  function isNumber(val) {
    return typeof val === "number";
  }
  function isObject(val) {
    return val !== null && typeof val === "object";
  }
  function isPlainObject(val) {
    if (kindOf(val) !== "object") {
      return false;
    }
    var prototype2 = Object.getPrototypeOf(val);
    return prototype2 === null || prototype2 === Object.prototype;
  }
  var isDate = kindOfTest("Date");
  var isFile = kindOfTest("File");
  var isBlob = kindOfTest("Blob");
  var isFileList = kindOfTest("FileList");
  function isFunction(val) {
    return toString.call(val) === "[object Function]";
  }
  function isStream(val) {
    return isObject(val) && isFunction(val.pipe);
  }
  function isFormData(thing) {
    var pattern = "[object FormData]";
    return thing && (typeof FormData === "function" && thing instanceof FormData || toString.call(thing) === pattern || isFunction(thing.toString) && thing.toString() === pattern);
  }
  var isURLSearchParams = kindOfTest("URLSearchParams");
  function trim(str) {
    return str.trim ? str.trim() : str.replace(/^\s+|\s+$/g, "");
  }
  function isStandardBrowserEnv() {
    if (typeof navigator !== "undefined" && (navigator.product === "ReactNative" || navigator.product === "NativeScript" || navigator.product === "NS")) {
      return false;
    }
    return typeof window !== "undefined" && typeof document !== "undefined";
  }
  function forEach(obj, fn) {
    if (obj === null || typeof obj === "undefined") {
      return;
    }
    if (typeof obj !== "object") {
      obj = [obj];
    }
    if (isArray(obj)) {
      for (var i = 0, l = obj.length; i < l; i++) {
        fn.call(null, obj[i], i, obj);
      }
    } else {
      for (var key in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, key)) {
          fn.call(null, obj[key], key, obj);
        }
      }
    }
  }
  function merge() {
    var result = {};
    function assignValue2(val, key) {
      if (isPlainObject(result[key]) && isPlainObject(val)) {
        result[key] = merge(result[key], val);
      } else if (isPlainObject(val)) {
        result[key] = merge({}, val);
      } else if (isArray(val)) {
        result[key] = val.slice();
      } else {
        result[key] = val;
      }
    }
    for (var i = 0, l = arguments.length; i < l; i++) {
      forEach(arguments[i], assignValue2);
    }
    return result;
  }
  function extend(a, b, thisArg) {
    forEach(b, function assignValue2(val, key) {
      if (thisArg && typeof val === "function") {
        a[key] = bind$1(val, thisArg);
      } else {
        a[key] = val;
      }
    });
    return a;
  }
  function stripBOM(content) {
    if (content.charCodeAt(0) === 65279) {
      content = content.slice(1);
    }
    return content;
  }
  function inherits(constructor, superConstructor, props, descriptors2) {
    constructor.prototype = Object.create(superConstructor.prototype, descriptors2);
    constructor.prototype.constructor = constructor;
    props && Object.assign(constructor.prototype, props);
  }
  function toFlatObject(sourceObj, destObj, filter) {
    var props;
    var i;
    var prop;
    var merged = {};
    destObj = destObj || {};
    do {
      props = Object.getOwnPropertyNames(sourceObj);
      i = props.length;
      while (i-- > 0) {
        prop = props[i];
        if (!merged[prop]) {
          destObj[prop] = sourceObj[prop];
          merged[prop] = true;
        }
      }
      sourceObj = Object.getPrototypeOf(sourceObj);
    } while (sourceObj && (!filter || filter(sourceObj, destObj)) && sourceObj !== Object.prototype);
    return destObj;
  }
  function endsWith(str, searchString, position) {
    str = String(str);
    if (position === void 0 || position > str.length) {
      position = str.length;
    }
    position -= searchString.length;
    var lastIndex = str.indexOf(searchString, position);
    return lastIndex !== -1 && lastIndex === position;
  }
  function toArray(thing) {
    if (!thing)
      return null;
    var i = thing.length;
    if (isUndefined(i))
      return null;
    var arr = new Array(i);
    while (i-- > 0) {
      arr[i] = thing[i];
    }
    return arr;
  }
  var isTypedArray = function(TypedArray) {
    return function(thing) {
      return TypedArray && thing instanceof TypedArray;
    };
  }(typeof Uint8Array !== "undefined" && Object.getPrototypeOf(Uint8Array));
  var utils$h = {
    isArray,
    isArrayBuffer,
    isBuffer,
    isFormData,
    isArrayBufferView,
    isString,
    isNumber,
    isObject,
    isPlainObject,
    isUndefined,
    isDate,
    isFile,
    isBlob,
    isFunction,
    isStream,
    isURLSearchParams,
    isStandardBrowserEnv,
    forEach,
    merge,
    extend,
    trim,
    stripBOM,
    inherits,
    toFlatObject,
    kindOf,
    kindOfTest,
    endsWith,
    toArray,
    isTypedArray,
    isFileList
  };
  var utils$g = utils$h;
  function encode(val) {
    return encodeURIComponent(val).replace(/%3A/gi, ":").replace(/%24/g, "$").replace(/%2C/gi, ",").replace(/%20/g, "+").replace(/%5B/gi, "[").replace(/%5D/gi, "]");
  }
  var buildURL$2 = function buildURL2(url, params, paramsSerializer) {
    if (!params) {
      return url;
    }
    var serializedParams;
    if (paramsSerializer) {
      serializedParams = paramsSerializer(params);
    } else if (utils$g.isURLSearchParams(params)) {
      serializedParams = params.toString();
    } else {
      var parts = [];
      utils$g.forEach(params, function serialize(val, key) {
        if (val === null || typeof val === "undefined") {
          return;
        }
        if (utils$g.isArray(val)) {
          key = key + "[]";
        } else {
          val = [val];
        }
        utils$g.forEach(val, function parseValue(v) {
          if (utils$g.isDate(v)) {
            v = v.toISOString();
          } else if (utils$g.isObject(v)) {
            v = JSON.stringify(v);
          }
          parts.push(encode(key) + "=" + encode(v));
        });
      });
      serializedParams = parts.join("&");
    }
    if (serializedParams) {
      var hashmarkIndex = url.indexOf("#");
      if (hashmarkIndex !== -1) {
        url = url.slice(0, hashmarkIndex);
      }
      url += (url.indexOf("?") === -1 ? "?" : "&") + serializedParams;
    }
    return url;
  };
  var utils$f = utils$h;
  function InterceptorManager$1() {
    this.handlers = [];
  }
  InterceptorManager$1.prototype.use = function use(fulfilled, rejected, options2) {
    this.handlers.push({
      fulfilled,
      rejected,
      synchronous: options2 ? options2.synchronous : false,
      runWhen: options2 ? options2.runWhen : null
    });
    return this.handlers.length - 1;
  };
  InterceptorManager$1.prototype.eject = function eject(id) {
    if (this.handlers[id]) {
      this.handlers[id] = null;
    }
  };
  InterceptorManager$1.prototype.forEach = function forEach2(fn) {
    utils$f.forEach(this.handlers, function forEachHandler(h) {
      if (h !== null) {
        fn(h);
      }
    });
  };
  var InterceptorManager_1 = InterceptorManager$1;
  var utils$e = utils$h;
  var normalizeHeaderName$1 = function normalizeHeaderName2(headers, normalizedName) {
    utils$e.forEach(headers, function processHeader(value, name) {
      if (name !== normalizedName && name.toUpperCase() === normalizedName.toUpperCase()) {
        headers[normalizedName] = value;
        delete headers[name];
      }
    });
  };
  var utils$d = utils$h;
  function AxiosError$5(message, code, config, request, response) {
    Error.call(this);
    this.message = message;
    this.name = "AxiosError";
    code && (this.code = code);
    config && (this.config = config);
    request && (this.request = request);
    response && (this.response = response);
  }
  utils$d.inherits(AxiosError$5, Error, {
    toJSON: function toJSON() {
      return {
        message: this.message,
        name: this.name,
        description: this.description,
        number: this.number,
        fileName: this.fileName,
        lineNumber: this.lineNumber,
        columnNumber: this.columnNumber,
        stack: this.stack,
        config: this.config,
        code: this.code,
        status: this.response && this.response.status ? this.response.status : null
      };
    }
  });
  var prototype = AxiosError$5.prototype;
  var descriptors = {};
  [
    "ERR_BAD_OPTION_VALUE",
    "ERR_BAD_OPTION",
    "ECONNABORTED",
    "ETIMEDOUT",
    "ERR_NETWORK",
    "ERR_FR_TOO_MANY_REDIRECTS",
    "ERR_DEPRECATED",
    "ERR_BAD_RESPONSE",
    "ERR_BAD_REQUEST",
    "ERR_CANCELED"
  ].forEach(function(code) {
    descriptors[code] = { value: code };
  });
  Object.defineProperties(AxiosError$5, descriptors);
  Object.defineProperty(prototype, "isAxiosError", { value: true });
  AxiosError$5.from = function(error, code, config, request, response, customProps) {
    var axiosError = Object.create(prototype);
    utils$d.toFlatObject(error, axiosError, function filter(obj) {
      return obj !== Error.prototype;
    });
    AxiosError$5.call(axiosError, error.message, code, config, request, response);
    axiosError.name = error.name;
    customProps && Object.assign(axiosError, customProps);
    return axiosError;
  };
  var AxiosError_1 = AxiosError$5;
  var transitional = {
    silentJSONParsing: true,
    forcedJSONParsing: true,
    clarifyTimeoutError: false
  };
  var utils$c = utils$h;
  function toFormData$1(obj, formData) {
    formData = formData || new FormData();
    var stack = [];
    function convertValue(value) {
      if (value === null)
        return "";
      if (utils$c.isDate(value)) {
        return value.toISOString();
      }
      if (utils$c.isArrayBuffer(value) || utils$c.isTypedArray(value)) {
        return typeof Blob === "function" ? new Blob([value]) : Buffer.from(value);
      }
      return value;
    }
    function build(data2, parentKey) {
      if (utils$c.isPlainObject(data2) || utils$c.isArray(data2)) {
        if (stack.indexOf(data2) !== -1) {
          throw Error("Circular reference detected in " + parentKey);
        }
        stack.push(data2);
        utils$c.forEach(data2, function each(value, key) {
          if (utils$c.isUndefined(value))
            return;
          var fullKey = parentKey ? parentKey + "." + key : key;
          var arr;
          if (value && !parentKey && typeof value === "object") {
            if (utils$c.endsWith(key, "{}")) {
              value = JSON.stringify(value);
            } else if (utils$c.endsWith(key, "[]") && (arr = utils$c.toArray(value))) {
              arr.forEach(function(el) {
                !utils$c.isUndefined(el) && formData.append(fullKey, convertValue(el));
              });
              return;
            }
          }
          build(value, fullKey);
        });
        stack.pop();
      } else {
        formData.append(parentKey, convertValue(data2));
      }
    }
    build(obj);
    return formData;
  }
  var toFormData_1 = toFormData$1;
  var AxiosError$4 = AxiosError_1;
  var settle$1 = function settle2(resolve, reject, response) {
    var validateStatus = response.config.validateStatus;
    if (!response.status || !validateStatus || validateStatus(response.status)) {
      resolve(response);
    } else {
      reject(new AxiosError$4(
        "Request failed with status code " + response.status,
        [AxiosError$4.ERR_BAD_REQUEST, AxiosError$4.ERR_BAD_RESPONSE][Math.floor(response.status / 100) - 4],
        response.config,
        response.request,
        response
      ));
    }
  };
  var utils$b = utils$h;
  var cookies$1 = utils$b.isStandardBrowserEnv() ? function standardBrowserEnv() {
    return {
      write: function write2(name, value, expires, path, domain, secure) {
        var cookie = [];
        cookie.push(name + "=" + encodeURIComponent(value));
        if (utils$b.isNumber(expires)) {
          cookie.push("expires=" + new Date(expires).toGMTString());
        }
        if (utils$b.isString(path)) {
          cookie.push("path=" + path);
        }
        if (utils$b.isString(domain)) {
          cookie.push("domain=" + domain);
        }
        if (secure === true) {
          cookie.push("secure");
        }
        document.cookie = cookie.join("; ");
      },
      read: function read2(name) {
        var match = document.cookie.match(new RegExp("(^|;\\s*)(" + name + ")=([^;]*)"));
        return match ? decodeURIComponent(match[3]) : null;
      },
      remove: function remove(name) {
        this.write(name, "", Date.now() - 864e5);
      }
    };
  }() : function nonStandardBrowserEnv() {
    return {
      write: function write2() {
      },
      read: function read2() {
        return null;
      },
      remove: function remove() {
      }
    };
  }();
  var isAbsoluteURL$1 = function isAbsoluteURL2(url) {
    return /^([a-z][a-z\d+\-.]*:)?\/\//i.test(url);
  };
  var combineURLs$1 = function combineURLs2(baseURL, relativeURL) {
    return relativeURL ? baseURL.replace(/\/+$/, "") + "/" + relativeURL.replace(/^\/+/, "") : baseURL;
  };
  var isAbsoluteURL = isAbsoluteURL$1;
  var combineURLs = combineURLs$1;
  var buildFullPath$2 = function buildFullPath2(baseURL, requestedURL) {
    if (baseURL && !isAbsoluteURL(requestedURL)) {
      return combineURLs(baseURL, requestedURL);
    }
    return requestedURL;
  };
  var utils$a = utils$h;
  var ignoreDuplicateOf = [
    "age",
    "authorization",
    "content-length",
    "content-type",
    "etag",
    "expires",
    "from",
    "host",
    "if-modified-since",
    "if-unmodified-since",
    "last-modified",
    "location",
    "max-forwards",
    "proxy-authorization",
    "referer",
    "retry-after",
    "user-agent"
  ];
  var parseHeaders$1 = function parseHeaders2(headers) {
    var parsed = {};
    var key;
    var val;
    var i;
    if (!headers) {
      return parsed;
    }
    utils$a.forEach(headers.split("\n"), function parser(line) {
      i = line.indexOf(":");
      key = utils$a.trim(line.substr(0, i)).toLowerCase();
      val = utils$a.trim(line.substr(i + 1));
      if (key) {
        if (parsed[key] && ignoreDuplicateOf.indexOf(key) >= 0) {
          return;
        }
        if (key === "set-cookie") {
          parsed[key] = (parsed[key] ? parsed[key] : []).concat([val]);
        } else {
          parsed[key] = parsed[key] ? parsed[key] + ", " + val : val;
        }
      }
    });
    return parsed;
  };
  var utils$9 = utils$h;
  var isURLSameOrigin$1 = utils$9.isStandardBrowserEnv() ? function standardBrowserEnv() {
    var msie = /(msie|trident)/i.test(navigator.userAgent);
    var urlParsingNode = document.createElement("a");
    var originURL;
    function resolveURL(url) {
      var href = url;
      if (msie) {
        urlParsingNode.setAttribute("href", href);
        href = urlParsingNode.href;
      }
      urlParsingNode.setAttribute("href", href);
      return {
        href: urlParsingNode.href,
        protocol: urlParsingNode.protocol ? urlParsingNode.protocol.replace(/:$/, "") : "",
        host: urlParsingNode.host,
        search: urlParsingNode.search ? urlParsingNode.search.replace(/^\?/, "") : "",
        hash: urlParsingNode.hash ? urlParsingNode.hash.replace(/^#/, "") : "",
        hostname: urlParsingNode.hostname,
        port: urlParsingNode.port,
        pathname: urlParsingNode.pathname.charAt(0) === "/" ? urlParsingNode.pathname : "/" + urlParsingNode.pathname
      };
    }
    originURL = resolveURL(window.location.href);
    return function isURLSameOrigin2(requestURL) {
      var parsed = utils$9.isString(requestURL) ? resolveURL(requestURL) : requestURL;
      return parsed.protocol === originURL.protocol && parsed.host === originURL.host;
    };
  }() : function nonStandardBrowserEnv() {
    return function isURLSameOrigin2() {
      return true;
    };
  }();
  var AxiosError$3 = AxiosError_1;
  var utils$8 = utils$h;
  function CanceledError$3(message) {
    AxiosError$3.call(this, message == null ? "canceled" : message, AxiosError$3.ERR_CANCELED);
    this.name = "CanceledError";
  }
  utils$8.inherits(CanceledError$3, AxiosError$3, {
    __CANCEL__: true
  });
  var CanceledError_1 = CanceledError$3;
  var parseProtocol$1 = function parseProtocol2(url) {
    var match = /^([-+\w]{1,25})(:?\/\/|:)/.exec(url);
    return match && match[1] || "";
  };
  var utils$7 = utils$h;
  var settle = settle$1;
  var cookies = cookies$1;
  var buildURL$1 = buildURL$2;
  var buildFullPath$1 = buildFullPath$2;
  var parseHeaders = parseHeaders$1;
  var isURLSameOrigin = isURLSameOrigin$1;
  var transitionalDefaults$1 = transitional;
  var AxiosError$2 = AxiosError_1;
  var CanceledError$2 = CanceledError_1;
  var parseProtocol = parseProtocol$1;
  var xhr = function xhrAdapter(config) {
    return new Promise(function dispatchXhrRequest(resolve, reject) {
      var requestData = config.data;
      var requestHeaders = config.headers;
      var responseType = config.responseType;
      var onCanceled;
      function done() {
        if (config.cancelToken) {
          config.cancelToken.unsubscribe(onCanceled);
        }
        if (config.signal) {
          config.signal.removeEventListener("abort", onCanceled);
        }
      }
      if (utils$7.isFormData(requestData) && utils$7.isStandardBrowserEnv()) {
        delete requestHeaders["Content-Type"];
      }
      var request = new XMLHttpRequest();
      if (config.auth) {
        var username = config.auth.username || "";
        var password = config.auth.password ? unescape(encodeURIComponent(config.auth.password)) : "";
        requestHeaders.Authorization = "Basic " + btoa(username + ":" + password);
      }
      var fullPath = buildFullPath$1(config.baseURL, config.url);
      request.open(config.method.toUpperCase(), buildURL$1(fullPath, config.params, config.paramsSerializer), true);
      request.timeout = config.timeout;
      function onloadend() {
        if (!request) {
          return;
        }
        var responseHeaders = "getAllResponseHeaders" in request ? parseHeaders(request.getAllResponseHeaders()) : null;
        var responseData = !responseType || responseType === "text" || responseType === "json" ? request.responseText : request.response;
        var response = {
          data: responseData,
          status: request.status,
          statusText: request.statusText,
          headers: responseHeaders,
          config,
          request
        };
        settle(function _resolve(value) {
          resolve(value);
          done();
        }, function _reject(err) {
          reject(err);
          done();
        }, response);
        request = null;
      }
      if ("onloadend" in request) {
        request.onloadend = onloadend;
      } else {
        request.onreadystatechange = function handleLoad() {
          if (!request || request.readyState !== 4) {
            return;
          }
          if (request.status === 0 && !(request.responseURL && request.responseURL.indexOf("file:") === 0)) {
            return;
          }
          setTimeout(onloadend);
        };
      }
      request.onabort = function handleAbort() {
        if (!request) {
          return;
        }
        reject(new AxiosError$2("Request aborted", AxiosError$2.ECONNABORTED, config, request));
        request = null;
      };
      request.onerror = function handleError() {
        reject(new AxiosError$2("Network Error", AxiosError$2.ERR_NETWORK, config, request, request));
        request = null;
      };
      request.ontimeout = function handleTimeout() {
        var timeoutErrorMessage = config.timeout ? "timeout of " + config.timeout + "ms exceeded" : "timeout exceeded";
        var transitional2 = config.transitional || transitionalDefaults$1;
        if (config.timeoutErrorMessage) {
          timeoutErrorMessage = config.timeoutErrorMessage;
        }
        reject(new AxiosError$2(
          timeoutErrorMessage,
          transitional2.clarifyTimeoutError ? AxiosError$2.ETIMEDOUT : AxiosError$2.ECONNABORTED,
          config,
          request
        ));
        request = null;
      };
      if (utils$7.isStandardBrowserEnv()) {
        var xsrfValue = (config.withCredentials || isURLSameOrigin(fullPath)) && config.xsrfCookieName ? cookies.read(config.xsrfCookieName) : void 0;
        if (xsrfValue) {
          requestHeaders[config.xsrfHeaderName] = xsrfValue;
        }
      }
      if ("setRequestHeader" in request) {
        utils$7.forEach(requestHeaders, function setRequestHeader(val, key) {
          if (typeof requestData === "undefined" && key.toLowerCase() === "content-type") {
            delete requestHeaders[key];
          } else {
            request.setRequestHeader(key, val);
          }
        });
      }
      if (!utils$7.isUndefined(config.withCredentials)) {
        request.withCredentials = !!config.withCredentials;
      }
      if (responseType && responseType !== "json") {
        request.responseType = config.responseType;
      }
      if (typeof config.onDownloadProgress === "function") {
        request.addEventListener("progress", config.onDownloadProgress);
      }
      if (typeof config.onUploadProgress === "function" && request.upload) {
        request.upload.addEventListener("progress", config.onUploadProgress);
      }
      if (config.cancelToken || config.signal) {
        onCanceled = function(cancel) {
          if (!request) {
            return;
          }
          reject(!cancel || cancel && cancel.type ? new CanceledError$2() : cancel);
          request.abort();
          request = null;
        };
        config.cancelToken && config.cancelToken.subscribe(onCanceled);
        if (config.signal) {
          config.signal.aborted ? onCanceled() : config.signal.addEventListener("abort", onCanceled);
        }
      }
      if (!requestData) {
        requestData = null;
      }
      var protocol = parseProtocol(fullPath);
      if (protocol && ["http", "https", "file"].indexOf(protocol) === -1) {
        reject(new AxiosError$2("Unsupported protocol " + protocol + ":", AxiosError$2.ERR_BAD_REQUEST, config));
        return;
      }
      request.send(requestData);
    });
  };
  var _null = null;
  var utils$6 = utils$h;
  var normalizeHeaderName = normalizeHeaderName$1;
  var AxiosError$1 = AxiosError_1;
  var transitionalDefaults = transitional;
  var toFormData = toFormData_1;
  var DEFAULT_CONTENT_TYPE = {
    "Content-Type": "application/x-www-form-urlencoded"
  };
  function setContentTypeIfUnset(headers, value) {
    if (!utils$6.isUndefined(headers) && utils$6.isUndefined(headers["Content-Type"])) {
      headers["Content-Type"] = value;
    }
  }
  function getDefaultAdapter() {
    var adapter;
    if (typeof XMLHttpRequest !== "undefined") {
      adapter = xhr;
    } else if (typeof process !== "undefined" && Object.prototype.toString.call(process) === "[object process]") {
      adapter = xhr;
    }
    return adapter;
  }
  function stringifySafely(rawValue, parser, encoder) {
    if (utils$6.isString(rawValue)) {
      try {
        (parser || JSON.parse)(rawValue);
        return utils$6.trim(rawValue);
      } catch (e) {
        if (e.name !== "SyntaxError") {
          throw e;
        }
      }
    }
    return (encoder || JSON.stringify)(rawValue);
  }
  var defaults$3 = {
    transitional: transitionalDefaults,
    adapter: getDefaultAdapter(),
    transformRequest: [function transformRequest(data2, headers) {
      normalizeHeaderName(headers, "Accept");
      normalizeHeaderName(headers, "Content-Type");
      if (utils$6.isFormData(data2) || utils$6.isArrayBuffer(data2) || utils$6.isBuffer(data2) || utils$6.isStream(data2) || utils$6.isFile(data2) || utils$6.isBlob(data2)) {
        return data2;
      }
      if (utils$6.isArrayBufferView(data2)) {
        return data2.buffer;
      }
      if (utils$6.isURLSearchParams(data2)) {
        setContentTypeIfUnset(headers, "application/x-www-form-urlencoded;charset=utf-8");
        return data2.toString();
      }
      var isObjectPayload = utils$6.isObject(data2);
      var contentType = headers && headers["Content-Type"];
      var isFileList2;
      if ((isFileList2 = utils$6.isFileList(data2)) || isObjectPayload && contentType === "multipart/form-data") {
        var _FormData = this.env && this.env.FormData;
        return toFormData(isFileList2 ? { "files[]": data2 } : data2, _FormData && new _FormData());
      } else if (isObjectPayload || contentType === "application/json") {
        setContentTypeIfUnset(headers, "application/json");
        return stringifySafely(data2);
      }
      return data2;
    }],
    transformResponse: [function transformResponse(data2) {
      var transitional2 = this.transitional || defaults$3.transitional;
      var silentJSONParsing = transitional2 && transitional2.silentJSONParsing;
      var forcedJSONParsing = transitional2 && transitional2.forcedJSONParsing;
      var strictJSONParsing = !silentJSONParsing && this.responseType === "json";
      if (strictJSONParsing || forcedJSONParsing && utils$6.isString(data2) && data2.length) {
        try {
          return JSON.parse(data2);
        } catch (e) {
          if (strictJSONParsing) {
            if (e.name === "SyntaxError") {
              throw AxiosError$1.from(e, AxiosError$1.ERR_BAD_RESPONSE, this, null, this.response);
            }
            throw e;
          }
        }
      }
      return data2;
    }],
    timeout: 0,
    xsrfCookieName: "XSRF-TOKEN",
    xsrfHeaderName: "X-XSRF-TOKEN",
    maxContentLength: -1,
    maxBodyLength: -1,
    env: {
      FormData: _null
    },
    validateStatus: function validateStatus(status) {
      return status >= 200 && status < 300;
    },
    headers: {
      common: {
        "Accept": "application/json, text/plain, */*"
      }
    }
  };
  utils$6.forEach(["delete", "get", "head"], function forEachMethodNoData(method) {
    defaults$3.headers[method] = {};
  });
  utils$6.forEach(["post", "put", "patch"], function forEachMethodWithData(method) {
    defaults$3.headers[method] = utils$6.merge(DEFAULT_CONTENT_TYPE);
  });
  var defaults_1 = defaults$3;
  var utils$5 = utils$h;
  var defaults$2 = defaults_1;
  var transformData$1 = function transformData2(data2, headers, fns) {
    var context = this || defaults$2;
    utils$5.forEach(fns, function transform(fn) {
      data2 = fn.call(context, data2, headers);
    });
    return data2;
  };
  var isCancel$1 = function isCancel2(value) {
    return !!(value && value.__CANCEL__);
  };
  var utils$4 = utils$h;
  var transformData = transformData$1;
  var isCancel = isCancel$1;
  var defaults$1 = defaults_1;
  var CanceledError$1 = CanceledError_1;
  function throwIfCancellationRequested(config) {
    if (config.cancelToken) {
      config.cancelToken.throwIfRequested();
    }
    if (config.signal && config.signal.aborted) {
      throw new CanceledError$1();
    }
  }
  var dispatchRequest$1 = function dispatchRequest2(config) {
    throwIfCancellationRequested(config);
    config.headers = config.headers || {};
    config.data = transformData.call(
      config,
      config.data,
      config.headers,
      config.transformRequest
    );
    config.headers = utils$4.merge(
      config.headers.common || {},
      config.headers[config.method] || {},
      config.headers
    );
    utils$4.forEach(
      ["delete", "get", "head", "post", "put", "patch", "common"],
      function cleanHeaderConfig(method) {
        delete config.headers[method];
      }
    );
    var adapter = config.adapter || defaults$1.adapter;
    return adapter(config).then(function onAdapterResolution(response) {
      throwIfCancellationRequested(config);
      response.data = transformData.call(
        config,
        response.data,
        response.headers,
        config.transformResponse
      );
      return response;
    }, function onAdapterRejection(reason) {
      if (!isCancel(reason)) {
        throwIfCancellationRequested(config);
        if (reason && reason.response) {
          reason.response.data = transformData.call(
            config,
            reason.response.data,
            reason.response.headers,
            config.transformResponse
          );
        }
      }
      return Promise.reject(reason);
    });
  };
  var utils$3 = utils$h;
  var mergeConfig$2 = function mergeConfig2(config1, config2) {
    config2 = config2 || {};
    var config = {};
    function getMergedValue(target, source) {
      if (utils$3.isPlainObject(target) && utils$3.isPlainObject(source)) {
        return utils$3.merge(target, source);
      } else if (utils$3.isPlainObject(source)) {
        return utils$3.merge({}, source);
      } else if (utils$3.isArray(source)) {
        return source.slice();
      }
      return source;
    }
    function mergeDeepProperties(prop) {
      if (!utils$3.isUndefined(config2[prop])) {
        return getMergedValue(config1[prop], config2[prop]);
      } else if (!utils$3.isUndefined(config1[prop])) {
        return getMergedValue(void 0, config1[prop]);
      }
    }
    function valueFromConfig2(prop) {
      if (!utils$3.isUndefined(config2[prop])) {
        return getMergedValue(void 0, config2[prop]);
      }
    }
    function defaultToConfig2(prop) {
      if (!utils$3.isUndefined(config2[prop])) {
        return getMergedValue(void 0, config2[prop]);
      } else if (!utils$3.isUndefined(config1[prop])) {
        return getMergedValue(void 0, config1[prop]);
      }
    }
    function mergeDirectKeys(prop) {
      if (prop in config2) {
        return getMergedValue(config1[prop], config2[prop]);
      } else if (prop in config1) {
        return getMergedValue(void 0, config1[prop]);
      }
    }
    var mergeMap = {
      "url": valueFromConfig2,
      "method": valueFromConfig2,
      "data": valueFromConfig2,
      "baseURL": defaultToConfig2,
      "transformRequest": defaultToConfig2,
      "transformResponse": defaultToConfig2,
      "paramsSerializer": defaultToConfig2,
      "timeout": defaultToConfig2,
      "timeoutMessage": defaultToConfig2,
      "withCredentials": defaultToConfig2,
      "adapter": defaultToConfig2,
      "responseType": defaultToConfig2,
      "xsrfCookieName": defaultToConfig2,
      "xsrfHeaderName": defaultToConfig2,
      "onUploadProgress": defaultToConfig2,
      "onDownloadProgress": defaultToConfig2,
      "decompress": defaultToConfig2,
      "maxContentLength": defaultToConfig2,
      "maxBodyLength": defaultToConfig2,
      "beforeRedirect": defaultToConfig2,
      "transport": defaultToConfig2,
      "httpAgent": defaultToConfig2,
      "httpsAgent": defaultToConfig2,
      "cancelToken": defaultToConfig2,
      "socketPath": defaultToConfig2,
      "responseEncoding": defaultToConfig2,
      "validateStatus": mergeDirectKeys
    };
    utils$3.forEach(Object.keys(config1).concat(Object.keys(config2)), function computeConfigValue(prop) {
      var merge2 = mergeMap[prop] || mergeDeepProperties;
      var configValue = merge2(prop);
      utils$3.isUndefined(configValue) && merge2 !== mergeDirectKeys || (config[prop] = configValue);
    });
    return config;
  };
  var data = {
    "version": "0.27.2"
  };
  var VERSION = data.version;
  var AxiosError = AxiosError_1;
  var validators$1 = {};
  ["object", "boolean", "number", "function", "string", "symbol"].forEach(function(type, i) {
    validators$1[type] = function validator2(thing) {
      return typeof thing === type || "a" + (i < 1 ? "n " : " ") + type;
    };
  });
  var deprecatedWarnings = {};
  validators$1.transitional = function transitional2(validator2, version, message) {
    function formatMessage(opt, desc) {
      return "[Axios v" + VERSION + "] Transitional option '" + opt + "'" + desc + (message ? ". " + message : "");
    }
    return function(value, opt, opts) {
      if (validator2 === false) {
        throw new AxiosError(
          formatMessage(opt, " has been removed" + (version ? " in " + version : "")),
          AxiosError.ERR_DEPRECATED
        );
      }
      if (version && !deprecatedWarnings[opt]) {
        deprecatedWarnings[opt] = true;
        console.warn(
          formatMessage(
            opt,
            " has been deprecated since v" + version + " and will be removed in the near future"
          )
        );
      }
      return validator2 ? validator2(value, opt, opts) : true;
    };
  };
  function assertOptions(options2, schema, allowUnknown) {
    if (typeof options2 !== "object") {
      throw new AxiosError("options must be an object", AxiosError.ERR_BAD_OPTION_VALUE);
    }
    var keys2 = Object.keys(options2);
    var i = keys2.length;
    while (i-- > 0) {
      var opt = keys2[i];
      var validator2 = schema[opt];
      if (validator2) {
        var value = options2[opt];
        var result = value === void 0 || validator2(value, opt, options2);
        if (result !== true) {
          throw new AxiosError("option " + opt + " must be " + result, AxiosError.ERR_BAD_OPTION_VALUE);
        }
        continue;
      }
      if (allowUnknown !== true) {
        throw new AxiosError("Unknown option " + opt, AxiosError.ERR_BAD_OPTION);
      }
    }
  }
  var validator$1 = {
    assertOptions,
    validators: validators$1
  };
  var utils$2 = utils$h;
  var buildURL = buildURL$2;
  var InterceptorManager = InterceptorManager_1;
  var dispatchRequest = dispatchRequest$1;
  var mergeConfig$1 = mergeConfig$2;
  var buildFullPath = buildFullPath$2;
  var validator = validator$1;
  var validators = validator.validators;
  function Axios$1(instanceConfig) {
    this.defaults = instanceConfig;
    this.interceptors = {
      request: new InterceptorManager(),
      response: new InterceptorManager()
    };
  }
  Axios$1.prototype.request = function request(configOrUrl, config) {
    if (typeof configOrUrl === "string") {
      config = config || {};
      config.url = configOrUrl;
    } else {
      config = configOrUrl || {};
    }
    config = mergeConfig$1(this.defaults, config);
    if (config.method) {
      config.method = config.method.toLowerCase();
    } else if (this.defaults.method) {
      config.method = this.defaults.method.toLowerCase();
    } else {
      config.method = "get";
    }
    var transitional2 = config.transitional;
    if (transitional2 !== void 0) {
      validator.assertOptions(transitional2, {
        silentJSONParsing: validators.transitional(validators.boolean),
        forcedJSONParsing: validators.transitional(validators.boolean),
        clarifyTimeoutError: validators.transitional(validators.boolean)
      }, false);
    }
    var requestInterceptorChain = [];
    var synchronousRequestInterceptors = true;
    this.interceptors.request.forEach(function unshiftRequestInterceptors(interceptor) {
      if (typeof interceptor.runWhen === "function" && interceptor.runWhen(config) === false) {
        return;
      }
      synchronousRequestInterceptors = synchronousRequestInterceptors && interceptor.synchronous;
      requestInterceptorChain.unshift(interceptor.fulfilled, interceptor.rejected);
    });
    var responseInterceptorChain = [];
    this.interceptors.response.forEach(function pushResponseInterceptors(interceptor) {
      responseInterceptorChain.push(interceptor.fulfilled, interceptor.rejected);
    });
    var promise;
    if (!synchronousRequestInterceptors) {
      var chain = [dispatchRequest, void 0];
      Array.prototype.unshift.apply(chain, requestInterceptorChain);
      chain = chain.concat(responseInterceptorChain);
      promise = Promise.resolve(config);
      while (chain.length) {
        promise = promise.then(chain.shift(), chain.shift());
      }
      return promise;
    }
    var newConfig = config;
    while (requestInterceptorChain.length) {
      var onFulfilled = requestInterceptorChain.shift();
      var onRejected = requestInterceptorChain.shift();
      try {
        newConfig = onFulfilled(newConfig);
      } catch (error) {
        onRejected(error);
        break;
      }
    }
    try {
      promise = dispatchRequest(newConfig);
    } catch (error) {
      return Promise.reject(error);
    }
    while (responseInterceptorChain.length) {
      promise = promise.then(responseInterceptorChain.shift(), responseInterceptorChain.shift());
    }
    return promise;
  };
  Axios$1.prototype.getUri = function getUri(config) {
    config = mergeConfig$1(this.defaults, config);
    var fullPath = buildFullPath(config.baseURL, config.url);
    return buildURL(fullPath, config.params, config.paramsSerializer);
  };
  utils$2.forEach(["delete", "get", "head", "options"], function forEachMethodNoData(method) {
    Axios$1.prototype[method] = function(url, config) {
      return this.request(mergeConfig$1(config || {}, {
        method,
        url,
        data: (config || {}).data
      }));
    };
  });
  utils$2.forEach(["post", "put", "patch"], function forEachMethodWithData(method) {
    function generateHTTPMethod(isForm) {
      return function httpMethod(url, data2, config) {
        return this.request(mergeConfig$1(config || {}, {
          method,
          headers: isForm ? {
            "Content-Type": "multipart/form-data"
          } : {},
          url,
          data: data2
        }));
      };
    }
    Axios$1.prototype[method] = generateHTTPMethod();
    Axios$1.prototype[method + "Form"] = generateHTTPMethod(true);
  });
  var Axios_1 = Axios$1;
  var CanceledError = CanceledError_1;
  function CancelToken(executor) {
    if (typeof executor !== "function") {
      throw new TypeError("executor must be a function.");
    }
    var resolvePromise;
    this.promise = new Promise(function promiseExecutor(resolve) {
      resolvePromise = resolve;
    });
    var token = this;
    this.promise.then(function(cancel) {
      if (!token._listeners)
        return;
      var i;
      var l = token._listeners.length;
      for (i = 0; i < l; i++) {
        token._listeners[i](cancel);
      }
      token._listeners = null;
    });
    this.promise.then = function(onfulfilled) {
      var _resolve;
      var promise = new Promise(function(resolve) {
        token.subscribe(resolve);
        _resolve = resolve;
      }).then(onfulfilled);
      promise.cancel = function reject() {
        token.unsubscribe(_resolve);
      };
      return promise;
    };
    executor(function cancel(message) {
      if (token.reason) {
        return;
      }
      token.reason = new CanceledError(message);
      resolvePromise(token.reason);
    });
  }
  CancelToken.prototype.throwIfRequested = function throwIfRequested() {
    if (this.reason) {
      throw this.reason;
    }
  };
  CancelToken.prototype.subscribe = function subscribe(listener) {
    if (this.reason) {
      listener(this.reason);
      return;
    }
    if (this._listeners) {
      this._listeners.push(listener);
    } else {
      this._listeners = [listener];
    }
  };
  CancelToken.prototype.unsubscribe = function unsubscribe(listener) {
    if (!this._listeners) {
      return;
    }
    var index2 = this._listeners.indexOf(listener);
    if (index2 !== -1) {
      this._listeners.splice(index2, 1);
    }
  };
  CancelToken.source = function source() {
    var cancel;
    var token = new CancelToken(function executor(c) {
      cancel = c;
    });
    return {
      token,
      cancel
    };
  };
  var CancelToken_1 = CancelToken;
  var spread = function spread2(callback) {
    return function wrap(arr) {
      return callback.apply(null, arr);
    };
  };
  var utils$1 = utils$h;
  var isAxiosError = function isAxiosError2(payload) {
    return utils$1.isObject(payload) && payload.isAxiosError === true;
  };
  var utils = utils$h;
  var bind = bind$2;
  var Axios = Axios_1;
  var mergeConfig = mergeConfig$2;
  var defaults = defaults_1;
  function createInstance(defaultConfig) {
    var context = new Axios(defaultConfig);
    var instance2 = bind(Axios.prototype.request, context);
    utils.extend(instance2, Axios.prototype, context);
    utils.extend(instance2, context);
    instance2.create = function create(instanceConfig) {
      return createInstance(mergeConfig(defaultConfig, instanceConfig));
    };
    return instance2;
  }
  var axios$1 = createInstance(defaults);
  axios$1.Axios = Axios;
  axios$1.CanceledError = CanceledError_1;
  axios$1.CancelToken = CancelToken_1;
  axios$1.isCancel = isCancel$1;
  axios$1.VERSION = data.version;
  axios$1.toFormData = toFormData_1;
  axios$1.AxiosError = AxiosError_1;
  axios$1.Cancel = axios$1.CanceledError;
  axios$1.all = function all(promises) {
    return Promise.all(promises);
  };
  axios$1.spread = spread;
  axios$1.isAxiosError = isAxiosError;
  axios$2.exports = axios$1;
  axios$2.exports.default = axios$1;
  var axios = axios$2.exports;
  function getService() {
    return axios.create({
      baseURL: `${window.ZnRestConfig.rest_root}zionbuilder/v1/`,
      headers: {
        "X-WP-Nonce": window.ZnRestConfig.nonce,
        Accept: "application/json",
        "Content-Type": "application/json"
      }
    });
  }
  function getTemplates(config = {}) {
    return getService().get("templates", {
      params: config
    });
  }
  function addTemplate(template) {
    return getService().post("templates", template);
  }
  function duplicateTemplate(templateID) {
    return getService().post("templates/duplicate", {
      template_id: templateID
    });
  }
  function updateTemplate(templateID, templateData) {
    return getService().post(`templates/${templateID}`, templateData);
  }
  function deleteTemplate(id) {
    return getService().delete(`templates/${id}`);
  }
  const isLoading = vue.ref(true);
  const templates = vue.ref([]);
  const copiedTemplate = vue.ref(null);
  const usedComponents = vue.ref([]);
  const useAreaTemplates = () => {
    function getTemplatesFromDB(data2 = {}) {
      const parsedData = merge$2({
        template_type: "theme_builder"
      }, data2);
      return getTemplates(parsedData).then((response) => {
        templates.value = response.data;
      }).finally(() => {
        isLoading.value = false;
      });
    }
    function getTemplate(templateId) {
      const template = templates.value.find((template2) => {
        return template2.id === templateId;
      });
      if (!template) {
        console.warn(`Template with id ${templateId} not found`);
      }
      return template;
    }
    function copyTemplate(templateID) {
      copiedTemplate.value = templateID;
    }
    function pasteTemplate(templateArea) {
      templateArea.setContent(copiedTemplate.value);
      copiedTemplate.value = null;
    }
    function pasteTemplateAsNew() {
      return duplicateTemplate(copiedTemplate.value).then((response) => {
        templates.value.push(response.data);
        return Promise.resolve(response.data);
      });
    }
    function addNewTemplate(template) {
      return addTemplate(template).then((response) => {
        templates.value.push(response.data);
        return Promise.resolve(response);
      });
    }
    function deleteTemplate$1(templateID) {
      return deleteTemplate(templateID).then((response) => {
        const template = templates.value.find((template2) => template2.id === templateID);
        const templateIndex = templates.value.indexOf(template);
        const { allTemplates: allTemplates2 } = useSiteTemplates();
        allTemplates2.value.forEach((template2) => {
          if (typeof template2.template_config !== "undefined") {
            const templateAreas = Object.keys(template2.template_config);
            if (templateAreas.length > 0) {
              templateAreas.forEach((area) => {
                const areaConfig = template2.template_config[area];
                if (!areaConfig.content) {
                  return;
                }
                if (areaConfig.content === templateID) {
                  areaConfig.delete();
                }
              });
            }
          }
        });
        templates.value.splice(templateIndex, 1);
        return Promise.resolve(response.data);
      });
    }
    function addAreaComponent(id) {
      usedComponents.value.push(id);
    }
    function removeAreaComponent(id) {
      const index2 = usedComponents.value.indexOf(id);
      if (index2 !== -1) {
        usedComponents.value.splice(index2, 1);
      }
    }
    return {
      getTemplatesFromDB,
      getTemplate,
      copyTemplate,
      pasteTemplate,
      pasteTemplateAsNew,
      addNewTemplate,
      deleteTemplate: deleteTemplate$1,
      usedComponents,
      templates,
      isLoading,
      copiedTemplate,
      addAreaComponent,
      removeAreaComponent
    };
  };
  class TemplateArea$1 {
    constructor(areaType, config, template) {
      __publicField(this, "active");
      __publicField(this, "content");
      __publicField(this, "template");
      __publicField(this, "areaType");
      this.areaType = areaType;
      this.active = typeof config.active !== "undefined" ? config.active : true;
      this.content = config.content || null;
      this.template = template;
    }
    get templatePost() {
      const { getTemplate } = useAreaTemplates();
      return getTemplate(this.content);
    }
    setContent(newValue) {
      this.content = newValue;
    }
    get isActive() {
      return this.active;
    }
    set isActive(newValue) {
      this.active = newValue;
    }
    delete() {
      this.template.removeArea(this.areaType);
    }
    toJSON() {
      return {
        active: this.active,
        content: this.content
      };
    }
  }
  class Template {
    constructor(config = {}) {
      __publicField(this, "id");
      __publicField(this, "name");
      __publicField(this, "template_config", {});
      __publicField(this, "conditions", {});
      __publicField(this, "disabled", false);
      const areasConfig = config.template_config || {};
      Object.keys(areasConfig).forEach((area) => {
        areasConfig[area] = new TemplateArea$1(area, areasConfig[area], this);
      });
      this.id = config.id || generateUID();
      this.name = config.name || "";
      this.template_config = areasConfig;
      this.conditions = config.conditions || {};
      this.disabled = config.disabled || false;
    }
    addArea(areaID, areaConfig) {
      this.template_config[areaID] = new TemplateArea$1(areaID, areaConfig, this);
    }
    getArea(area) {
      return this.template_config[area] || null;
    }
    removeArea(areaType) {
      delete this.template_config[areaType];
    }
    update(newValues) {
      const { name, conditions } = newValues;
      this.name = name;
      this.conditions = conditions;
    }
    setDisabled(status) {
      this.disabled = status;
    }
    toJSON() {
      const templateConfig = {};
      Object.keys(this.template_config || {}).forEach((templateArea) => {
        templateConfig[templateArea] = this.template_config[templateArea].toJSON();
      });
      return {
        id: this.id,
        name: this.name,
        template_config: templateConfig,
        conditions: this.conditions,
        disabled: this.disabled
      };
    }
  }
  const loading = vue.ref(false);
  const editedTemplate = vue.ref(null);
  const siteBuilderData = window.ZnPbSiteBuilderData;
  const templatesConfig = vue.ref(siteBuilderData.site_templates);
  const allTemplates = vue.ref(templatesConfig.value.templates.map((template) => new Template(template)));
  if (!templatesConfig.value.default_template || !allTemplates.value.find((template) => template.id === templatesConfig.value.default_template)) {
    const addedTemplate = new Template();
    allTemplates.value.push(addedTemplate);
    console.log("adding default template");
    templatesConfig.value.default_template = addedTemplate.id;
  }
  const useSiteTemplates = () => {
    function createTemplate(config = {}) {
      return new Template(config);
    }
    function updateTemplates(newTemplatesConfig) {
      allTemplates.value = newTemplatesConfig;
    }
    function addTemplate2(templateConfig = {}) {
      allTemplates.value.push(new Template(templateConfig));
    }
    function saveTemplatesConfig() {
      loading.value = true;
      const templatesToSave = allTemplates.value.map((template) => {
        return template.toJSON();
      });
      const data2 = {
        default_template: templatesConfig.value.default_template,
        templates: templatesToSave
      };
      saveThemeBuilder(data2).finally(() => {
        loading.value = false;
      });
    }
    function removeTemplate(template) {
      const index2 = allTemplates.value.indexOf(template);
      if (index2 === -1) {
        console.log("template not found");
      }
      allTemplates.value.splice(index2, 1);
    }
    function duplicateTemplate2(template) {
      const templateConfig = template.toJSON();
      delete templateConfig.id;
      const copyText = window.zb.i18n.translate("copy");
      templateConfig.name = `${templateConfig.name} (${copyText})`;
      addTemplate2(templateConfig);
    }
    function editTemplate(template) {
      editedTemplate.value = template;
    }
    function unEditTemplate() {
      editedTemplate.value = null;
    }
    function setAsDefault(template) {
      templatesConfig.value.default_template = template.id;
    }
    return {
      siteBuilderData,
      templatesConfig,
      allTemplates,
      loading,
      editedTemplate,
      createTemplate,
      updateTemplates,
      saveTemplatesConfig,
      addTemplate: addTemplate2,
      removeTemplate,
      duplicateTemplate: duplicateTemplate2,
      editTemplate,
      unEditTemplate,
      setAsDefault
    };
  };
  const activeTemplate = vue.ref(null);
  const activeTemplateConfig = vue.ref(null);
  const useTemplatePostEdit = () => {
    function editTemplate(template, templateConfig = null) {
      activeTemplate.value = template;
      activeTemplateConfig.value = templateConfig;
    }
    function closeEditTemplate() {
      activeTemplate.value = null;
      activeTemplateConfig.value = null;
    }
    return {
      activeTemplate,
      activeTemplateConfig,
      closeEditTemplate,
      editTemplate
    };
  };
  var Menu_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$a = {
    name: "Menu",
    props: {
      actions: {
        type: Array,
        required: true
      }
    },
    setup(props, { emit }) {
      function performAction(action) {
        action.action();
        emit("action", true);
      }
      return {
        performAction
      };
    }
  };
  const _hoisted_1$6 = { class: "znpbpro-rightClick-menu" };
  const _hoisted_2$4 = ["onClick"];
  function _sfc_render$9(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$6, [
      (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($props.actions, (action) => {
        return vue.openBlock(), vue.createElementBlock("div", {
          key: action.title,
          class: vue.normalizeClass(["znpbpro-rightClick-menu-item", { "znpbpro-rightClick-menu-item--disabled": !action.show }]),
          onClick: vue.withModifiers(($event) => $setup.performAction(action), ["stop"])
        }, [
          vue.createElementVNode("span", null, vue.toDisplayString(action.title), 1)
        ], 10, _hoisted_2$4);
      }), 128))
    ]);
  }
  var Menu = /* @__PURE__ */ _export_sfc(_sfc_main$a, [["render", _sfc_render$9]]);
  const _sfc_main$9 = {
    name: "TemplateAreaItem",
    components: {
      Menu
    },
    props: {
      area: {
        type: String
      },
      data: {
        type: Object
      },
      templateConfig: {
        type: Object
      }
    },
    setup(props, { emit }) {
      const { editTemplate } = useTemplatePostEdit();
      const { copyTemplate, copiedTemplate: copiedTemplate2, pasteTemplate, usedComponents: usedComponents2, addAreaComponent, removeAreaComponent } = useAreaTemplates();
      const loadingArea = vue.ref(false);
      const expanded = vue.ref(false);
      const refTemplateRename = vue.ref(false);
      const isLinked = vue.computed(() => {
        return usedComponents2.value.filter((id) => id === props.data.templatePost.id).length > 1;
      });
      const templatePostName = vue.computed({
        get() {
          return props.data.templatePost.name;
        },
        set(newValue) {
          if (props.data.templatePost.name === newValue) {
            return;
          }
          props.data.templatePost.name = newValue;
          updateTemplateDebounced(props.data.templatePost.id, {
            post_title: newValue
          });
        }
      });
      function doUpdateTemplate(templateID, data2) {
        loadingArea.value = true;
        updateTemplate(templateID, data2).finally(() => {
          loadingArea.value = false;
        });
      }
      const updateTemplateDebounced = debounce$1(doUpdateTemplate, 600);
      const editAreaActions = vue.computed(() => {
        const actions = [
          {
            title: props.data.isActive ? window.zb.i18n.translate("disable") : window.zb.i18n.translate("enable"),
            action: () => props.data.isActive = !props.data.isActive,
            show: props.data.templatePost
          },
          {
            title: window.zb.i18n.translate("edit"),
            action: () => editTemplate(props.data.templatePost, props.templateConfig),
            show: props.data.templatePost
          },
          {
            title: window.zb.i18n.translate("copy_component"),
            action: () => copyTemplate(props.data.content),
            show: props.data.templatePost
          },
          {
            title: window.zb.i18n.translate("paste_component"),
            action: () => pasteTemplate(props.data),
            show: props.data.templatePost && copiedTemplate2.value
          },
          {
            title: window.zb.i18n.translate("paste_as_new_component"),
            action: () => pasteTemplate(props.data),
            show: props.data.templatePost && copiedTemplate2.value
          },
          {
            title: window.zb.i18n.translate("remove"),
            action: () => props.data.delete(),
            show: true
          }
        ];
        return actions;
      });
      vue.onMounted(() => {
        if (props.data.templatePost) {
          addAreaComponent(props.data.templatePost.id);
        }
      });
      vue.onBeforeUnmount(() => {
        if (props.data.templatePost) {
          removeAreaComponent(props.data.templatePost.id);
        }
      });
      return {
        refTemplateRename,
        expanded,
        loadingArea,
        isLinked,
        templatePostName,
        editAreaActions
      };
    }
  };
  const _hoisted_1$5 = {
    key: 0,
    class: "znpbpro-themeBuilder-templates-item-title"
  };
  function _sfc_render$8(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_Loader = vue.resolveComponent("Loader");
    const _component_Icon = vue.resolveComponent("Icon");
    const _component_InlineEdit = vue.resolveComponent("InlineEdit");
    const _component_Menu = vue.resolveComponent("Menu");
    const _component_Tooltip = vue.resolveComponent("Tooltip");
    return vue.openBlock(), vue.createElementBlock("div", {
      class: vue.normalizeClass(["znpbpro-themeBuilder-templates-item", [
        { "znpbpro-themeBuilder-templates-item--active": $props.data && $props.data.isActive },
        { "znpbpro-themeBuilder-templates-item--disabled": $props.data && !$props.data.isActive },
        { "znpbpro-themeBuilder-templates-item--error": $props.data && !$props.data.templatePost },
        { "znpbpro-themeBuilder-templates-item--isHovered": $props.data && $props.data.templatePost && $props.data.templatePost.isHovered }
      ]])
    }, [
      !$props.data.templatePost ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_1$5, vue.toDisplayString(_ctx.$translate("template_not_found")), 1)) : (vue.openBlock(), vue.createElementBlock(vue.Fragment, { key: 1 }, [
        $setup.loadingArea ? (vue.openBlock(), vue.createBlock(_component_Loader, {
          key: 0,
          size: 13
        })) : (vue.openBlock(), vue.createElementBlock(vue.Fragment, { key: 1 }, [
          vue.createVNode(_component_Icon, {
            class: "znpbpro-themeBuilder-templates-item-type",
            icon: `templates-${$props.area}`,
            size: 24
          }, null, 8, ["icon"]),
          $setup.isLinked ? (vue.openBlock(), vue.createBlock(_component_Icon, {
            key: 0,
            icon: "link",
            class: "znpbpro-themeBuilder-templates-item-link",
            size: 14
          })) : vue.createCommentVNode("", true),
          vue.createVNode(_component_InlineEdit, {
            ref: "refTemplateRename",
            modelValue: $setup.templatePostName,
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $setup.templatePostName = $event),
            tag: "span",
            class: "znpbpro-themeBuilder-templates-item-title"
          }, null, 8, ["modelValue"])
        ], 64))
      ], 64)),
      vue.createVNode(_component_Tooltip, {
        show: $setup.expanded,
        "onUpdate:show": _cache[3] || (_cache[3] = ($event) => $setup.expanded = $event),
        "tooltip-class": "hg-popper--no-padding",
        "append-to": "element",
        trigger: "null",
        placement: "right",
        "close-on-outside-click": true,
        "close-on-escape": true,
        "position-fixed": true,
        class: "znpbpro-themeBuilder-more-icon"
      }, {
        content: vue.withCtx(() => [
          vue.createVNode(_component_Menu, {
            actions: $setup.editAreaActions,
            onAction: _cache[1] || (_cache[1] = ($event) => $setup.expanded = !$setup.expanded)
          }, null, 8, ["actions"])
        ]),
        default: vue.withCtx(() => [
          vue.createVNode(_component_Icon, {
            icon: "more",
            "bg-size": 14,
            rotate: true,
            onClick: _cache[2] || (_cache[2] = vue.withModifiers(($event) => $setup.expanded = !$setup.expanded, ["stop"]))
          })
        ]),
        _: 1
      }, 8, ["show"])
    ], 2);
  }
  var TemplateAreaItem = /* @__PURE__ */ _export_sfc(_sfc_main$9, [["render", _sfc_render$8]]);
  var TemplateArea_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$8 = {
    name: "TemplateArea",
    components: {
      Menu,
      TemplateAreaItem
    },
    props: {
      area: {
        type: String
      },
      data: {
        type: Object
      },
      templateConfig: {
        type: Object
      }
    },
    setup(props) {
      const { copiedTemplate: copiedTemplate2, pasteTemplateAsNew, addNewTemplate } = useAreaTemplates();
      const loadingArea = vue.ref(false);
      const areaNicenames = {
        header: window.zb.i18n.translate("header"),
        body: window.zb.i18n.translate("body"),
        footer: window.zb.i18n.translate("footer")
      };
      const areaNiceName = areaNicenames[props.area];
      const emptyAreaActions = vue.computed(() => {
        return [
          {
            title: window.zb.i18n.translate("add_new_component"),
            action: () => {
              loadingArea.value = true;
              const templateName = props.templateConfig.name + " " + areaNiceName;
              addNewTemplate({
                template_type: "theme_builder",
                theme_area: props.area,
                title: templateName
              }).then((response) => {
                props.templateConfig.addArea(props.area, {
                  content: response.data.id
                });
              }).finally(() => {
                loadingArea.value = false;
              });
            },
            show: true
          },
          {
            title: window.zb.i18n.translate("paste_component"),
            action: () => {
              props.templateConfig.addArea(props.area, {
                content: copiedTemplate2.value
              });
            },
            show: copiedTemplate2.value
          },
          {
            title: window.zb.i18n.translate("paste_as_new_component"),
            action: () => {
              loadingArea.value = true;
              pasteTemplateAsNew(props.templateConfig).then((template) => {
                props.templateConfig.addArea(props.area, {
                  content: template.id
                });
              }).finally(() => {
                loadingArea.value = false;
              });
            },
            show: copiedTemplate2.value
          }
        ];
      });
      const sortableGroupInfo = {
        name: props.area,
        put: props.area,
        pull: false
      };
      function onSortableDrop(event2) {
        const itemID = event2.data.item.getAttribute("znpb-component-id");
        props.templateConfig.addArea(props.area, {
          content: parseInt(itemID)
        });
      }
      return {
        emptyAreaActions,
        loadingArea,
        sortableGroupInfo,
        onSortableDrop
      };
    }
  };
  function _sfc_render$7(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_TemplateAreaItem = vue.resolveComponent("TemplateAreaItem");
    const _component_Loader = vue.resolveComponent("Loader");
    const _component_Icon = vue.resolveComponent("Icon");
    const _component_Menu = vue.resolveComponent("Menu");
    const _component_Tooltip = vue.resolveComponent("Tooltip");
    const _component_Sortable = vue.resolveComponent("Sortable");
    return $props.data ? (vue.openBlock(), vue.createBlock(_component_TemplateAreaItem, {
      key: 0,
      data: $props.data,
      area: $props.area,
      "template-config": $props.templateConfig
    }, null, 8, ["data", "area", "template-config"])) : (vue.openBlock(), vue.createBlock(_component_Sortable, {
      key: 1,
      group: $setup.sortableGroupInfo,
      class: "znpbpro-themeBuilder-templates-item-sortable",
      onDrop: $setup.onSortableDrop
    }, {
      default: vue.withCtx(() => [
        vue.createVNode(_component_Tooltip, {
          class: "znpbpro-themeBuilder-templates-item znpbpro-themeBuilder-templates-item--empty",
          "tooltip-class": "hg-popper--no-padding",
          "append-to": "element",
          trigger: "click",
          placement: "bottom",
          "close-on-outside-click": true,
          "close-on-escape": true,
          "position-fixed": true
        }, {
          content: vue.withCtx(() => [
            vue.createVNode(_component_Menu, { actions: $setup.emptyAreaActions }, null, 8, ["actions"])
          ]),
          default: vue.withCtx(() => [
            $setup.loadingArea ? (vue.openBlock(), vue.createBlock(_component_Loader, {
              key: 0,
              size: 13
            })) : (vue.openBlock(), vue.createElementBlock(vue.Fragment, { key: 1 }, [
              vue.createVNode(_component_Icon, {
                class: "znpbpro-themeBuilder-templates-item-type",
                icon: `templates-${$props.area}`,
                size: 24
              }, null, 8, ["icon"]),
              vue.createElementVNode("span", null, "Add " + vue.toDisplayString($props.area), 1)
            ], 64))
          ]),
          _: 1
        })
      ]),
      _: 1
    }, 8, ["group", "onDrop"]));
  }
  var TemplateArea = /* @__PURE__ */ _export_sfc(_sfc_main$8, [["render", _sfc_render$7]]);
  var TemplateListItem_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$7 = {
    name: "TemplateListItem",
    components: {
      TemplateArea,
      Menu
    },
    props: {
      template: {
        type: Object,
        required: true
      },
      isDefault: {
        type: Boolean,
        required: false,
        default: false
      }
    },
    setup(props) {
      const { removeTemplate, duplicateTemplate: duplicateTemplate2, editTemplate, setAsDefault } = useSiteTemplates();
      let templateAreas = ["header", "body", "footer"];
      const templateAreasData = vue.computed(() => {
        let templateAreasData2 = {};
        templateAreas.forEach((area) => {
          templateAreasData2[area] = props.template.getArea(area);
        });
        return templateAreasData2;
      });
      const templateName = vue.computed({
        get() {
          return props.template.name || window.zb.i18n.translate("template");
        },
        set(newValue) {
          props.template.name = newValue;
        }
      });
      const expanded = vue.ref(false);
      const itemMenu = vue.computed(() => [
        {
          title: window.zb.i18n.translate("edit"),
          action: () => editTemplate(props.template),
          show: !props.isDefault
        },
        {
          title: props.template.disabled ? window.zb.i18n.translate("enable") : window.zb.i18n.translate("disable"),
          action: () => props.template.setDisabled(!props.template.disabled),
          show: true
        },
        {
          title: window.zb.i18n.translate("duplicate"),
          action: () => duplicateTemplate2(props.template),
          show: true
        },
        {
          title: window.zb.i18n.translate("remove"),
          action: () => removeTemplate(props.template),
          show: !props.isDefault
        },
        {
          title: window.zb.i18n.translate("set_as_default"),
          action: () => setAsDefault(props.template),
          show: !props.isDefault
        }
      ]);
      return {
        templateAreasData,
        expanded,
        itemMenu,
        templateName
      };
    }
  };
  const _hoisted_1$4 = {
    key: 0,
    class: "znpbpro-themeBuilder-templates-box--disabledBadge"
  };
  const _hoisted_2$3 = { class: "znpbpro-themeBuilder-templates-box__header" };
  const _hoisted_3$3 = {
    key: 0,
    class: "znpb-section-view-item__header-titleType"
  };
  const _hoisted_4$2 = { class: "znpbpro-themeBuilder-template-items" };
  function _sfc_render$6(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_InlineEdit = vue.resolveComponent("InlineEdit");
    const _component_Menu = vue.resolveComponent("Menu");
    const _component_Icon = vue.resolveComponent("Icon");
    const _component_Tooltip = vue.resolveComponent("Tooltip");
    const _component_TemplateArea = vue.resolveComponent("TemplateArea");
    return vue.openBlock(), vue.createElementBlock("div", {
      class: vue.normalizeClass(["znpbpro-themeBuilder-templates-box", {
        "znpbpro-themeBuilder-templates-box--default": $props.isDefault,
        "znpbpro-themeBuilder-templates-box--disabled": $props.template.disabled
      }])
    }, [
      $props.template.disabled ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_1$4, vue.toDisplayString(_ctx.$translate("disabled")), 1)) : vue.createCommentVNode("", true),
      vue.createElementVNode("div", _hoisted_2$3, [
        vue.createElementVNode("h4", null, [
          vue.createVNode(_component_InlineEdit, {
            modelValue: $setup.templateName,
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $setup.templateName = $event),
            class: "znpb-section-view-item__header-title",
            tag: "span"
          }, null, 8, ["modelValue"]),
          $props.isDefault ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_3$3, "(" + vue.toDisplayString(_ctx.$translate("default")) + ")", 1)) : vue.createCommentVNode("", true)
        ]),
        vue.createVNode(_component_Tooltip, {
          show: $setup.expanded,
          "onUpdate:show": _cache[3] || (_cache[3] = ($event) => $setup.expanded = $event),
          "tooltip-class": "hg-popper--no-padding",
          "append-to": "element",
          trigger: "null",
          placement: "right",
          "close-on-outside-click": true,
          "close-on-escape": true,
          "position-fixed": true,
          class: "znpbpro-themeBuilder-more-icon"
        }, {
          content: vue.withCtx(() => [
            vue.createVNode(_component_Menu, {
              actions: $setup.itemMenu,
              onAction: _cache[1] || (_cache[1] = ($event) => $setup.expanded = !$setup.expanded)
            }, null, 8, ["actions"])
          ]),
          default: vue.withCtx(() => [
            vue.createVNode(_component_Icon, {
              icon: "more",
              "bg-size": 14,
              rotate: true,
              onClick: _cache[2] || (_cache[2] = vue.withModifiers(($event) => $setup.expanded = !$setup.expanded, ["stop"]))
            })
          ]),
          _: 1
        }, 8, ["show"])
      ]),
      vue.createElementVNode("div", _hoisted_4$2, [
        (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($setup.templateAreasData, (data2, area) => {
          return vue.openBlock(), vue.createBlock(_component_TemplateArea, {
            key: area,
            area,
            data: data2,
            "template-config": $props.template
          }, null, 8, ["area", "data", "template-config"]);
        }), 128))
      ])
    ], 2);
  }
  var TemplateListItem = /* @__PURE__ */ _export_sfc(_sfc_main$7, [["render", _sfc_render$6]]);
  var TemplateList_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$6 = /* @__PURE__ */ vue.defineComponent({
    __name: "TemplateList",
    setup(__props) {
      const { allTemplates: allTemplates2, templatesConfig: templatesConfig2, updateTemplates } = useSiteTemplates();
      const defaultTemplateID = vue.computed(() => templatesConfig2.value.default_template);
      const defaultTemplate = vue.computed(() => {
        return allTemplates2.value.find((template) => template.id === defaultTemplateID.value);
      });
      const remainingTemplates = vue.computed({
        get() {
          return allTemplates2.value.filter((template) => template.id !== defaultTemplateID.value);
        },
        set(newConfig) {
          console.log(newConfig);
          updateTemplates([defaultTemplate.value, ...newConfig]);
        }
      });
      return (_ctx, _cache) => {
        const _component_Sortable = vue.resolveComponent("Sortable");
        return vue.openBlock(), vue.createBlock(_component_Sortable, {
          modelValue: vue.unref(remainingTemplates),
          "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(remainingTemplates) ? remainingTemplates.value = $event : null),
          class: "znpbpro-themeBuilder-templates-wrapper",
          axis: "horizontal",
          onDrop: _ctx.onSortableDrop
        }, {
          start: vue.withCtx(() => [
            vue.createVNode(TemplateListItem, {
              template: vue.unref(defaultTemplate),
              "is-default": true
            }, null, 8, ["template"])
          ]),
          default: vue.withCtx(() => [
            (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(vue.unref(remainingTemplates), (templateItem) => {
              return vue.openBlock(), vue.createBlock(TemplateListItem, {
                key: templateItem.id,
                template: templateItem
              }, null, 8, ["template"]);
            }), 128))
          ]),
          _: 1
        }, 8, ["modelValue", "onDrop"]);
      };
    }
  });
  var ComponentItem_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$5 = {
    name: "SingleItem",
    components: {
      Menu
    },
    props: {
      component: {
        type: Object
      }
    },
    setup(props) {
      const { editTemplate } = useTemplatePostEdit();
      const { deleteTemplate: deleteTemplate2, usedComponents: usedComponents2 } = useAreaTemplates();
      const expanded = vue.ref(false);
      const previewTemplate = vue.ref(false);
      const loading2 = vue.ref(false);
      const showModalDeleteConfirm = vue.ref(false);
      const isAssigned = vue.computed(() => {
        return usedComponents2.value.includes(props.component.id);
      });
      const templatePostName = vue.computed({
        get() {
          return props.component.name;
        },
        set(newValue) {
          if (props.component.name === newValue) {
            return;
          }
          props.component.name = newValue;
          loading2.value = true;
          updateTemplate(props.component.id, {
            post_title: newValue
          }).finally(() => {
            loading2.value = false;
          });
        }
      });
      const itemMenu = [
        {
          title: window.zb.i18n.translate("edit"),
          action: () => editTemplate(props.component),
          show: true
        },
        {
          title: window.zb.i18n.translate("delete"),
          action: () => showModalDeleteConfirm.value = true,
          show: true
        }
      ];
      function doDeleteTemplate() {
        loading2.value = true;
        deleteTemplate2(props.component.id).finally(() => {
          loading2.value = false;
        });
      }
      return {
        isAssigned,
        previewTemplate,
        expanded,
        loading: loading2,
        templatePostName,
        itemMenu,
        editTemplate,
        doDeleteTemplate,
        showModalDeleteConfirm
      };
    }
  };
  const _hoisted_1$3 = { class: "znpbpro-themeBuilder-components-item znpbpro-themeBuilder-components-item--name" };
  const _hoisted_2$2 = { class: "znpbpro-themeBuilder-components-item znpbpro-themeBuilder-components-item--status" };
  const _hoisted_3$2 = { class: "znpbpro-themeBuilder-components-item znpbpro-themeBuilder-components-item--actions" };
  const _hoisted_4$1 = ["src"];
  function _sfc_render$5(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_Loader = vue.resolveComponent("Loader");
    const _component_Icon = vue.resolveComponent("Icon");
    const _component_InlineEdit = vue.resolveComponent("InlineEdit");
    const _component_Modal = vue.resolveComponent("Modal");
    const _component_ModalConfirm = vue.resolveComponent("ModalConfirm");
    return vue.openBlock(), vue.createElementBlock("div", {
      class: "znpbpro-themeBuilder-components-item-wrapper",
      onMouseover: _cache[6] || (_cache[6] = ($event) => $props.component.isHovered = true),
      onMouseout: _cache[7] || (_cache[7] = ($event) => $props.component.isHovered = false)
    }, [
      $setup.loading ? (vue.openBlock(), vue.createBlock(_component_Loader, {
        key: 0,
        size: 13
      })) : (vue.openBlock(), vue.createElementBlock(vue.Fragment, { key: 1 }, [
        vue.createElementVNode("div", _hoisted_1$3, [
          vue.createVNode(_component_Icon, {
            icon: `templates-${$props.component.theme_area}`,
            size: 24
          }, null, 8, ["icon"]),
          vue.createVNode(_component_InlineEdit, {
            modelValue: $setup.templatePostName,
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $setup.templatePostName = $event),
            tag: "h5",
            class: "znpbpro-themeBuilder-templates-item-title"
          }, null, 8, ["modelValue"])
        ]),
        vue.createElementVNode("div", _hoisted_2$2, vue.toDisplayString($setup.isAssigned ? "assigned" : "unassigned"), 1),
        vue.createElementVNode("div", _hoisted_3$2, [
          vue.createVNode(_component_Icon, {
            icon: "edit",
            onClick: _cache[1] || (_cache[1] = ($event) => $setup.editTemplate($props.component))
          }),
          vue.createVNode(_component_Icon, {
            icon: "eye",
            onClick: _cache[2] || (_cache[2] = ($event) => $setup.previewTemplate = !$setup.previewTemplate)
          }),
          vue.createVNode(_component_Icon, {
            icon: "delete",
            onClick: _cache[3] || (_cache[3] = ($event) => $setup.showModalDeleteConfirm = true)
          })
        ])
      ], 64)),
      $setup.previewTemplate ? (vue.openBlock(), vue.createBlock(_component_Modal, {
        key: 2,
        show: true,
        title: _ctx.$translate("preview_component"),
        "show-backdrop": false,
        "append-to": "body",
        fullscreen: true,
        "enable-drag": false,
        class: "znpb-themeBuilderTemplateEditModal",
        "show-maximize": false,
        onCloseModal: _cache[4] || (_cache[4] = ($event) => $setup.previewTemplate = !$setup.previewTemplate)
      }, {
        default: vue.withCtx(() => [
          vue.createElementVNode("iframe", {
            src: $props.component.urls.preview_url
          }, null, 8, _hoisted_4$1)
        ]),
        _: 1
      }, 8, ["title"])) : vue.createCommentVNode("", true),
      $setup.showModalDeleteConfirm ? (vue.openBlock(), vue.createBlock(_component_ModalConfirm, {
        key: 3,
        width: 530,
        "confirm-text": _ctx.$translate("component_delete_confirm"),
        "cancel-text": _ctx.$translate("component_delete_cancel"),
        onConfirm: $setup.doDeleteTemplate,
        onCancel: _cache[5] || (_cache[5] = ($event) => $setup.showModalDeleteConfirm = false)
      }, {
        default: vue.withCtx(() => [
          vue.createTextVNode(vue.toDisplayString(_ctx.$translate("are_you_sure_delete_component")), 1)
        ]),
        _: 1
      }, 8, ["confirm-text", "cancel-text", "onConfirm"])) : vue.createCommentVNode("", true)
    ], 32);
  }
  var ComponentItem = /* @__PURE__ */ _export_sfc(_sfc_main$5, [["render", _sfc_render$5]]);
  var TemplateComponent_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$4 = {
    name: "TemplateComponent",
    components: {
      ComponentItem
    },
    props: {
      type: {
        type: String
      }
    },
    setup(props) {
      const { templates: templates2, usedComponents: usedComponents2 } = useAreaTemplates();
      const components2 = vue.computed(() => {
        return templates2.value.filter((template) => template.theme_area === props.type).sort((a, b) => {
          return usedComponents2.value.includes(a.id) ? -1 : 1;
        });
      });
      const groupInfo = {
        name: props.type,
        put: false,
        pull: "clone"
      };
      return {
        components: components2,
        groupInfo
      };
    }
  };
  const _hoisted_1$2 = { class: "znpbpro-themeBuilder-components-content" };
  const _hoisted_2$1 = /* @__PURE__ */ vue.createElementVNode("div", { class: "znpbpro-themeBuilder-components-titles" }, [
    /* @__PURE__ */ vue.createElementVNode("div", { class: "znpbpro-themeBuilder-components-titles-heading znpbpro-themeBuilder-components-titles-heading--name" }, " Name "),
    /* @__PURE__ */ vue.createElementVNode("div", { class: "znpbpro-themeBuilder-components-titles-heading znpbpro-themeBuilder-components-titles-heading--status" }, " Status "),
    /* @__PURE__ */ vue.createElementVNode("div", { class: "znpbpro-themeBuilder-components-titles-heading znpbpro-themeBuilder-components-titles-heading--actions" }, " Actions ")
  ], -1);
  const _hoisted_3$1 = {
    key: 1,
    class: "znpbpro-themeBuilder-components--empty"
  };
  function _sfc_render$4(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_Icon = vue.resolveComponent("Icon");
    const _component_ComponentItem = vue.resolveComponent("ComponentItem");
    const _component_sortable = vue.resolveComponent("sortable");
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$2, [
      _hoisted_2$1,
      $setup.components.length > 0 ? (vue.openBlock(), vue.createBlock(_component_sortable, {
        key: 0,
        group: $setup.groupInfo,
        sort: false,
        "preserve-last-location": false
      }, {
        helper: vue.withCtx(() => [
          vue.createVNode(_component_Icon, {
            class: "znpbpro-themeBuilder-componentSortableHelper",
            icon: `templates-${$props.type}`,
            size: 24
          }, null, 8, ["icon"])
        ]),
        placeholder: vue.withCtx(() => [
          vue.createVNode(_component_Icon, {
            class: "znpbpro-themeBuilder-componentSortablePlaceholder",
            icon: `templates-${$props.type}`,
            size: 24
          }, null, 8, ["icon"])
        ]),
        default: vue.withCtx(() => [
          (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($setup.components, (component) => {
            return vue.openBlock(), vue.createBlock(_component_ComponentItem, {
              key: component.id,
              component,
              "znpb-component-id": component.id
            }, null, 8, ["component", "znpb-component-id"]);
          }), 128))
        ]),
        _: 1
      }, 8, ["group"])) : (vue.openBlock(), vue.createElementBlock("div", _hoisted_3$1, vue.toDisplayString(_ctx.$translate("no_components_found")), 1))
    ]);
  }
  var TemplateComponent = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["render", _sfc_render$4]]);
  var AllComponentsTabs_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$3 = {
    name: "AllComponentsTabs",
    components: {
      TemplateComponent
    },
    data() {
      const tabs = [
        {
          name: window.zb.i18n.translate("header"),
          id: "header"
        },
        {
          name: window.zb.i18n.translate("body"),
          id: "body"
        },
        {
          name: window.zb.i18n.translate("footer"),
          id: "footer"
        }
      ];
      return {
        tabs
      };
    }
  };
  const _hoisted_1$1 = { class: "znpbpro-themeBuilder-components-wrapper" };
  function _sfc_render$3(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_TemplateComponent = vue.resolveComponent("TemplateComponent");
    const _component_Tab = vue.resolveComponent("Tab");
    const _component_Tabs = vue.resolveComponent("Tabs");
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1$1, [
      vue.createVNode(_component_Tabs, null, {
        default: vue.withCtx(() => [
          (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList($data.tabs, (tab) => {
            return vue.openBlock(), vue.createBlock(_component_Tab, {
              id: tab.id,
              key: tab.id,
              name: tab.name
            }, {
              default: vue.withCtx(() => [
                vue.createVNode(_component_TemplateComponent, {
                  type: tab.id
                }, null, 8, ["type"])
              ]),
              _: 2
            }, 1032, ["id", "name"]);
          }), 128))
        ]),
        _: 1
      })
    ]);
  }
  var AllComponentsTabs = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["render", _sfc_render$3]]);
  const _sfc_main$2 = {
    name: "ModalAddNewTemplate",
    setup(props, { emit }) {
      const { addTemplate: addTemplate2, editedTemplate: editedTemplate2 } = useSiteTemplates();
      const localData = vue.ref({});
      const optionsSchema = {
        name: {
          title: "Title",
          description: window.zb.i18n.translate("modal_title_description"),
          type: "text",
          placeholder: window.zb.i18n.translate("modal_title_placeholder")
        },
        conditions: {
          type: "template_assignments"
        }
      };
      const data2 = vue.computed({
        get() {
          if (editedTemplate2.value) {
            return {
              name: editedTemplate2.value.name,
              conditions: editedTemplate2.value.conditions
            };
          }
          return localData.value;
        },
        set(newValue) {
          if (editedTemplate2.value) {
            editedTemplate2.value.update(newValue);
          } else {
            localData.value = newValue;
          }
        }
      });
      function onSaveModal() {
        if (!editedTemplate2.value) {
          addTemplate2(localData.value);
        }
        emit("update:show", false);
      }
      return {
        optionsSchema,
        data: data2,
        onSaveModal
      };
    }
  };
  function _sfc_render$2(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_OptionsForm = vue.resolveComponent("OptionsForm");
    const _component_ModalTemplateSaveButton = vue.resolveComponent("ModalTemplateSaveButton");
    return vue.openBlock(), vue.createBlock(_component_ModalTemplateSaveButton, { onSaveModal: $setup.onSaveModal }, {
      default: vue.withCtx(() => [
        vue.createVNode(_component_OptionsForm, {
          modelValue: $setup.data,
          "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => $setup.data = $event),
          schema: $setup.optionsSchema
        }, null, 8, ["modelValue", "schema"])
      ]),
      _: 1
    }, 8, ["onSaveModal"]);
  }
  var ModalAddNewTemplate = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["render", _sfc_render$2]]);
  var TemplatePostIframe_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$1 = {
    name: "TemplatePostIframe",
    setup() {
      const { activeTemplate: activeTemplate2, activeTemplateConfig: activeTemplateConfig2 } = useTemplatePostEdit();
      const editUrl = vue.computed(() => {
        return activeTemplate2.value.urls.edit_url;
      });
      let form;
      vue.onMounted(() => {
        vue.nextTick(() => {
          form = document.createElement("form");
          form.action = editUrl.value;
          form.target = "frame";
          form.method = "post";
          if (activeTemplateConfig2.value) {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "template_conditions";
            input.value = JSON.stringify(activeTemplateConfig2.value.conditions);
            form.appendChild(input);
          }
          document.body.appendChild(form);
          form.submit();
        });
      });
      function onIframeLoad() {
        vue.nextTick(() => {
          if (document.body.contains(form)) {
            document.body.removeChild(form);
          }
        });
      }
      return {
        activeTemplate: activeTemplate2,
        editUrl,
        onIframeLoad
      };
    }
  };
  function _sfc_render$1(_ctx, _cache, $props, $setup, $data, $options) {
    return vue.openBlock(), vue.createElementBlock("iframe", {
      id: "frame",
      class: "znpb-themeBuilderTemplateEditIframe",
      name: "frame",
      onLoad: _cache[0] || (_cache[0] = (...args) => $setup.onIframeLoad && $setup.onIframeLoad(...args))
    }, null, 32);
  }
  var TemplatePostIframe = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["render", _sfc_render$1]]);
  var App_vue_vue_type_style_index_0_lang = "";
  const _sfc_main = {
    name: "App",
    components: {
      TemplateList: _sfc_main$6,
      AllComponentsTabs,
      ModalAddNewTemplate,
      TemplatePostIframe
    },
    setup() {
      const { getTemplatesFromDB, isLoading: isLoading2 } = useAreaTemplates();
      const { saveTemplatesConfig, loading: loading2, editedTemplate: editedTemplate2, unEditTemplate, allTemplates: allTemplates2 } = useSiteTemplates();
      const { activeTemplate: activeTemplate2, closeEditTemplate } = useTemplatePostEdit();
      const logoUrl = window.ZnPbSiteBuilderData.urls.logo_url;
      const showAddNewModal = vue.ref(false);
      const computedShowAddNewModal = vue.computed(() => {
        return showAddNewModal.value || !!editedTemplate2.value;
      });
      let showCloseWindowPrompt = false;
      const isAnyLoading = vue.computed(() => {
        return isLoading2.value || loading2.value;
      });
      getTemplatesFromDB();
      function save() {
        saveTemplatesConfig();
        showCloseWindowPrompt = false;
      }
      vue.onMounted(() => {
        window.document.addEventListener("keydown", (e) => {
          if (e.which === 83 && e.ctrlKey) {
            e.preventDefault();
            if (!loading2.value) {
              saveTemplatesConfig();
            }
          }
        });
      });
      function onAddNewModalClose() {
        if (editedTemplate2.value) {
          unEditTemplate();
        }
        showAddNewModal.value = false;
      }
      vue.watch(
        () => allTemplates2,
        (newValue) => {
          showCloseWindowPrompt = true;
        },
        {
          deep: true
        }
      );
      window.addEventListener("beforeunload", onBeforeUnloadIframe, { capture: true });
      function onBeforeUnloadIframe(event2) {
        if (showCloseWindowPrompt) {
          event2.preventDefault();
          event2.returnValue = "Do you want to leave this site? Changes you made may not be saved.";
        }
      }
      return {
        showAddNewModal,
        computedShowAddNewModal,
        isLoading: isLoading2,
        activeTemplate: activeTemplate2,
        closeEditTemplate,
        onAddNewModalClose,
        save,
        logoUrl,
        isAnyLoading
      };
    }
  };
  const _hoisted_1 = {
    id: "znpbpro-theme-builder-app",
    class: "znpbpro-theme-builder-app"
  };
  const _hoisted_2 = { class: "znpbpro-theme-builder-header" };
  const _hoisted_3 = ["src"];
  const _hoisted_4 = { class: "znpbpro-theme-builder-body" };
  const _hoisted_5 = {
    key: 1,
    class: "znpbpro-themeBuilder-templates"
  };
  const _hoisted_6 = { class: "znpbpro-themeBuilder-templates-header" };
  const _hoisted_7 = { class: "znpbpro-themeBuilder-components" };
  const _hoisted_8 = { class: "znpbpro-themeBuilder-templates-header" };
  const _hoisted_9 = { class: "zb-themebuilderEditModalTitle" };
  const _hoisted_10 = { class: "zb-themebuilderEditModalTitleLabel" };
  const _hoisted_11 = { class: "zb-themebuilderEditModalClose" };
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_Loader = vue.resolveComponent("Loader");
    const _component_Icon = vue.resolveComponent("Icon");
    const _component_TemplateList = vue.resolveComponent("TemplateList");
    const _component_AllComponentsTabs = vue.resolveComponent("AllComponentsTabs");
    const _component_ModalAddNewTemplate = vue.resolveComponent("ModalAddNewTemplate");
    const _component_Modal = vue.resolveComponent("Modal");
    const _component_TemplatePostIframe = vue.resolveComponent("TemplatePostIframe");
    return vue.openBlock(), vue.createElementBlock("div", _hoisted_1, [
      vue.createElementVNode("div", _hoisted_2, [
        vue.createElementVNode("img", {
          class: "znpbpro-theme-builder-headerLogo",
          src: $setup.logoUrl
        }, null, 8, _hoisted_3),
        vue.createElementVNode("h2", null, vue.toDisplayString(_ctx.$translate("theme_builder")), 1),
        vue.createElementVNode("span", {
          class: "znpb-button znpb-button--secondary",
          onClick: _cache[0] || (_cache[0] = (...args) => $setup.save && $setup.save(...args))
        }, [
          $setup.isAnyLoading ? (vue.openBlock(), vue.createBlock(_component_Loader, {
            key: 0,
            size: 13
          })) : vue.createCommentVNode("", true),
          vue.createTextVNode(" " + vue.toDisplayString(_ctx.$translate("save")), 1)
        ])
      ]),
      vue.createElementVNode("div", _hoisted_4, [
        $setup.isLoading ? (vue.openBlock(), vue.createBlock(_component_Loader, { key: 0 })) : (vue.openBlock(), vue.createElementBlock("div", _hoisted_5, [
          vue.createElementVNode("div", _hoisted_6, [
            vue.createElementVNode("h4", null, vue.toDisplayString(_ctx.$translate("templates")), 1),
            vue.createElementVNode("div", {
              class: "znpbpro-themeBuilder-templates-header__right",
              onClick: _cache[1] || (_cache[1] = ($event) => $setup.showAddNewModal = true)
            }, [
              vue.createElementVNode("span", null, vue.toDisplayString(_ctx.$translate("add_new_template")), 1),
              vue.createVNode(_component_Icon, {
                icon: "plus",
                "bg-size": 34,
                class: "znpbpro-themeBuilder-add-icon"
              })
            ])
          ]),
          vue.createVNode(_component_TemplateList)
        ])),
        vue.createElementVNode("div", _hoisted_7, [
          vue.createElementVNode("div", _hoisted_8, [
            vue.createElementVNode("h4", null, vue.toDisplayString(_ctx.$translate("components")), 1)
          ]),
          vue.createVNode(_component_AllComponentsTabs)
        ])
      ]),
      vue.createVNode(_component_Modal, {
        show: $setup.computedShowAddNewModal,
        title: _ctx.$translate("add_new_template"),
        "show-backdrop": false,
        "show-maximize": false,
        width: 520,
        onCloseModal: $setup.onAddNewModalClose
      }, {
        default: vue.withCtx(() => [
          $setup.computedShowAddNewModal ? (vue.openBlock(), vue.createBlock(_component_ModalAddNewTemplate, {
            key: 0,
            show: $setup.computedShowAddNewModal,
            "onUpdate:show": $setup.onAddNewModalClose
          }, null, 8, ["show", "onUpdate:show"])) : vue.createCommentVNode("", true)
        ]),
        _: 1
      }, 8, ["show", "title", "onCloseModal"]),
      $setup.activeTemplate ? (vue.openBlock(), vue.createBlock(_component_Modal, {
        key: 0,
        show: true,
        "show-backdrop": false,
        "append-to": "body",
        fullscreen: true,
        "enable-drag": false,
        class: "znpb-themeBuilderTemplateEditModal",
        "show-maximize": false,
        onCloseModal: $setup.closeEditTemplate
      }, {
        title: vue.withCtx(() => [
          vue.createElementVNode("span", _hoisted_9, [
            vue.createElementVNode("span", _hoisted_10, vue.toDisplayString(_ctx.$translate("editing_component")) + ":", 1),
            vue.createTextVNode(vue.toDisplayString($setup.activeTemplate.name), 1)
          ])
        ]),
        close: vue.withCtx(() => [
          vue.createElementVNode("span", _hoisted_11, vue.toDisplayString(_ctx.$translate("close_component")), 1)
        ]),
        default: vue.withCtx(() => [
          vue.createVNode(_component_TemplatePostIframe)
        ]),
        _: 1
      }, 8, ["onCloseModal"])) : vue.createCommentVNode("", true)
    ]);
  }
  var App = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  const pinia = createPinia();
  const appInstance = vue.createApp(App);
  window.zb = window.zb || {};
  window.zb.hooks = HOOKS;
  appInstance.use(pinia);
  appInstance.use(install$1, window.ZnI18NStrings);
  appInstance.use(install);
  const optionsStore = useOptions();
  optionsStore.registerOption(TemplateAssignments);
  appInstance.mount("#znpbpro-theme-builder-app");
})(zb.vue);
