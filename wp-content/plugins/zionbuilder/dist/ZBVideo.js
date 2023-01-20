var __defProp = Object.defineProperty;
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
var __publicField = (obj, key, value) => {
  __defNormalProp(obj, typeof key !== "symbol" ? key + "" : key, value);
  return value;
};
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
  /**
    reframe.js - Reframe.js: responsive iframes for embedded content
    @version v4.0.1
    @link https://github.com/yowainwright/reframe.ts#readme
    @author Jeff Wainwright <yowainwright@gmail.com> (http://jeffry.in)
    @license MIT
  **/
  function reframe(target, cName) {
    var _a, _b;
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
      (_a = frame.parentNode) === null || _a === void 0 ? void 0 : _a.insertBefore(div, frame);
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
    constructor(domNode, options = {}) {
      __publicField(this, "options", {});
      __publicField(this, "eventBus");
      __publicField(this, "on");
      __publicField(this, "off");
      __publicField(this, "trigger");
      __publicField(this, "domNode");
      __publicField(this, "videoIndex");
      __publicField(this, "videoContainer", null);
      __publicField(this, "videoSource", "local");
      __publicField(this, "YoutubeId");
      __publicField(this, "player");
      __publicField(this, "muted", true);
      __publicField(this, "playing", true);
      this.options = __spreadValues({
        autoplay: true,
        muted: true,
        loop: true,
        controls: true,
        controlsPosition: "bottom-left",
        videoSource: "local",
        responsive: true
      }, options);
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
        const self = this;
        window.onYouTubeIframeAPIReady = function() {
          self.enableYoutube();
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
        const self = this;
        secondScriptTag.parentNode.insertBefore(vimeoTag, secondScriptTag);
        vimeoTag.onload = function() {
          self.enableVimeo();
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
})();
