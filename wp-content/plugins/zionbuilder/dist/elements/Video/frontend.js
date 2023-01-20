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
  var main = "";
  class Video {
    constructor(domNode) {
      __publicField(this, "options");
      __publicField(this, "domNode");
      __publicField(this, "youtubePlayer");
      __publicField(this, "vimeoPlayer");
      __publicField(this, "html5Player");
      __publicField(this, "isInit", false);
      __publicField(this, "videoElement");
      this.domNode = domNode;
      this.options = this.getConfig();
      const modalParent = this.domNode.closest(".zb-modal");
      if (modalParent) {
        modalParent.addEventListener("openModal", () => {
          if (this.isInit) {
            this.play();
          } else {
            this.init();
          }
        });
        modalParent.addEventListener("closeModal", () => {
          this.pause();
        });
      } else {
        this.init();
      }
    }
    destroy() {
      var _a;
      const element = (_a = this.domNode) == null ? void 0 : _a.querySelector(".zb-el-video-element");
      if (element && element.parentElement) {
        element.parentElement.removeChild(element);
      }
    }
    play() {
      if (this.youtubePlayer) {
        this.youtubePlayer.playVideo();
      } else if (this.html5Player) {
        this.html5Player.play();
      } else if (this.vimeoPlayer) {
        this.vimeoPlayer.play();
      }
    }
    pause() {
      if (this.youtubePlayer) {
        this.youtubePlayer.pauseVideo();
      } else if (this.html5Player) {
        this.html5Player.pause();
      } else if (this.vimeoPlayer) {
        this.vimeoPlayer.pause();
      }
    }
    init() {
      if (this.options.use_image_overlay) {
        this.initBackdrop();
      } else {
        this.initVideo();
      }
    }
    initBackdrop() {
      var _a;
      const backdrop = (_a = this.domNode) == null ? void 0 : _a.querySelector(".zb-el-zionVideo-overlay");
      backdrop == null ? void 0 : backdrop.addEventListener("click", () => {
        var _a2;
        this.initVideo();
        (_a2 = backdrop.parentElement) == null ? void 0 : _a2.removeChild(backdrop);
      });
    }
    initVideo() {
      var _a, _b, _c, _d, _e, _f;
      if (this.isInit) {
        return;
      }
      if (((_b = (_a = this.options) == null ? void 0 : _a.video_config) == null ? void 0 : _b.videoSource) === "youtube") {
        this.initYoutube();
      } else if (((_d = (_c = this.options) == null ? void 0 : _c.video_config) == null ? void 0 : _d.videoSource) === "local") {
        this.initHTML5();
      } else if (((_f = (_e = this.options) == null ? void 0 : _e.video_config) == null ? void 0 : _f.videoSource) === "vimeo") {
        this.initVimeo();
      }
      this.isInit = true;
    }
    getYoutubeVideoID(url) {
      const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
      const match = url.match(regExp);
      return match && match[7].length === 11 ? match[7] : void 0;
    }
    onYoutubeAPIReady(callback) {
      if (window.YT && window.YT.Player) {
        callback(window.YT.Player);
        return;
      } else if (!window.ZbAttachedYoutubeScript) {
        this.attachYoutubeScript();
      }
      setTimeout(() => {
        this.onYoutubeAPIReady(callback);
      }, 200);
    }
    attachYoutubeScript() {
      var _a;
      if (window.ZbAttachedYoutubeScript) {
        return;
      }
      const tag = document.createElement("script");
      tag.src = "https://www.youtube.com/iframe_api";
      const firstScriptTag = document.getElementsByTagName("script")[0];
      (_a = firstScriptTag == null ? void 0 : firstScriptTag.parentNode) == null ? void 0 : _a.insertBefore(tag, firstScriptTag);
      window.ZbAttachedYoutubeScript = true;
    }
    initYoutube() {
      var _a, _b, _c;
      if (!((_a = this.options.video_config) == null ? void 0 : _a.youtubeURL)) {
        return;
      }
      const videoID = this.getYoutubeVideoID((_b = this.options.video_config) == null ? void 0 : _b.youtubeURL);
      if (!videoID) {
        return;
      }
      const videoElement = document.createElement("div");
      videoElement.classList.add("zb-el-video-element");
      (_c = this.domNode) == null ? void 0 : _c.appendChild(videoElement);
      this.onYoutubeAPIReady(() => {
        this.youtubePlayer = new window.YT.Player(videoElement, {
          videoId: videoID,
          playerVars: {
            autoplay: this.options.autoplay ? 1 : 0,
            controls: this.options.controls ? 1 : 0,
            mute: this.options.muted ? 1 : 0,
            playsinline: 1,
            modestbranding: 1,
            origin: window.location.host
          },
          host: "https://www.youtube-nocookie.com"
        });
      });
    }
    onVimeoApiReady(callback) {
      if (window.Vimeo && window.Vimeo.Player) {
        callback();
        return;
      } else if (!window.ZbAttachedVimeoScript) {
        this.attachVimeoScript();
      }
      setTimeout(() => {
        this.onVimeoApiReady(callback);
      }, 200);
    }
    attachVimeoScript() {
      var _a;
      if (window.ZbAttachedVimeoScript) {
        return;
      }
      const tag = document.createElement("script");
      tag.src = "https://player.vimeo.com/api/player.js";
      const firstScriptTag = document.getElementsByTagName("script")[0];
      (_a = firstScriptTag == null ? void 0 : firstScriptTag.parentNode) == null ? void 0 : _a.insertBefore(tag, firstScriptTag);
      window.ZbAttachedVimeoScript = true;
    }
    initVimeo() {
      var _a, _b;
      if (!((_a = this.options.video_config) == null ? void 0 : _a.vimeoURL)) {
        return;
      }
      const videoElement = document.createElement("div");
      videoElement.classList.add("zb-el-video-element");
      (_b = this.domNode) == null ? void 0 : _b.appendChild(videoElement);
      this.onVimeoApiReady(() => {
        var _a2;
        this.vimeoPlayer = new window.Vimeo.Player(videoElement, {
          id: (_a2 = this.options) == null ? void 0 : _a2.vimeoURL,
          background: false,
          muted: this.options.muted,
          transparent: true,
          autoplay: this.options.autoplay,
          controls: this.options.controls
        });
      });
    }
    initHTML5() {
      var _a;
      const autoplay = this.options.autoplay ? true : false;
      const muted = this.options.muted ? true : false;
      const loop = this.options.loop ? true : false;
      const videoElement = document.createElement("video");
      videoElement.classList.add("zb-el-video-element");
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
      (_a = this.domNode) == null ? void 0 : _a.appendChild(videoElement);
      this.html5Player = videoElement;
    }
    getConfig() {
      var _a;
      const configAttr = (_a = this.domNode) == null ? void 0 : _a.dataset.zionVideo;
      const options = configAttr ? JSON.parse(configAttr) : {};
      return __spreadValues(__spreadValues({
        autoplay: true,
        muted: true,
        loop: true,
        controls: true,
        controlsPosition: "bottom-left",
        videoSource: "local",
        responsive: true
      }, options), options.video_config || {});
    }
  }
  document.querySelectorAll(".zb-el-zionVideo").forEach((domNode) => {
    new Video(domNode);
  });
  window.zbScripts = window.zbScripts || {};
  window.zbScripts.video = Video;
})();
