(function() {
  "use strict";
  var createHooksInstance = () => {
    const filters = {};
    const actions = {};
    const addAction = (event, callback) => {
      if (typeof actions[event] === "undefined") {
        actions[event] = [];
      }
      actions[event].push(callback);
    };
    function on(event, callback) {
      console.warn("zb.hooks.on was deprecated in favour of window.zb.addAction");
      return addAction(event, callback);
    }
    const removeAction = (event, callback) => {
      if (typeof actions[event] !== "undefined") {
        const callbackIndex = actions[event].indexOf(callback);
        if (callbackIndex !== -1) {
          actions[event].splice(callbackIndex);
        }
      }
    };
    function off(event, callback) {
      console.warn("zb.hooks.off was deprecated in favour of window.zb.addAction");
      return addAction(event, callback);
    }
    const doAction = (event, ...data) => {
      if (typeof actions[event] !== "undefined") {
        actions[event].forEach((callbackFunction) => {
          callbackFunction(...data);
        });
      }
    };
    function trigger(event, ...data) {
      console.warn("zb.hooks.trigger was deprecated in favour of window.zb.addAction");
      return doAction(event, ...data);
    }
    const addFilter = (id, callback) => {
      if (typeof filters[id] === "undefined") {
        filters[id] = [];
      }
      filters[id].push(callback);
    };
    const applyFilters = (id, value, ...params) => {
      if (typeof filters[id] !== "undefined") {
        filters[id].forEach((callback) => {
          value = callback(value, ...params);
        });
      }
      return value;
    };
    return {
      addAction,
      removeAction,
      doAction,
      addFilter,
      applyFilters,
      on,
      off,
      trigger
    };
  };
  const hooks = createHooksInstance();
  window.zb = window.zb || {};
  window.zb.hooks = hooks;
})();
