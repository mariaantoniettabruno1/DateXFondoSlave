var __defProp = Object.defineProperty;
var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
var __publicField = (obj, key, value) => {
  __defNormalProp(obj, typeof key !== "symbol" ? key + "" : key, value);
  return value;
};
(function() {
  "use strict";
  var main = "";
  class Tabs {
    constructor(domNode) {
      __publicField(this, "tabLinks", []);
      __publicField(this, "tabContents", []);
      __publicField(this, "tabFocus", 0);
      this.tabLinks = Array.from(domNode.querySelectorAll(".zb-el-tabs-nav-title"));
      this.tabContents = Array.from(domNode.querySelectorAll(".zb-el-tabsItem"));
      domNode.addEventListener("click", (event) => this.onTabClick(event));
      domNode.addEventListener("keydown", (event) => this.onKeyDown(event));
    }
    onKeyDown(event) {
      if (event.code === "ArrowRight") {
        this.tabLinks[this.tabFocus].tabIndex = -1;
        this.tabFocus++;
        if (this.tabFocus >= this.tabLinks.length) {
          this.tabFocus = 0;
        }
        this.tabLinks[this.tabFocus].focus();
      } else if (event.code === "ArrowLeft") {
        this.tabFocus--;
        if (this.tabFocus < 0) {
          this.tabFocus = this.tabLinks.length - 1;
        }
        this.tabLinks[this.tabFocus].focus();
      } else if (event.code === "Space" || event.code === "Enter") {
        this.activateTab(this.tabLinks[this.tabFocus]);
      }
    }
    onTabClick(event) {
      const domNode = event.target;
      if (domNode && domNode.classList.contains("zb-el-tabs-nav-title")) {
        this.activateTab(domNode);
      }
    }
    deActivateTabs() {
      [...this.tabLinks, ...this.tabContents].forEach((item) => {
        item.classList.remove("zb-el-tabs-nav--active");
      });
    }
    activateTab(tab) {
      this.deActivateTabs();
      tab.classList.add("zb-el-tabs-nav--active");
      const tabIndex = this.tabLinks.indexOf(tab);
      if (tabIndex !== -1 && this.tabContents[tabIndex]) {
        this.tabContents[tabIndex].classList.add("zb-el-tabs-nav--active");
      }
    }
  }
  document.querySelectorAll(".zb-el-tabs").forEach((domNode) => {
    new Tabs(domNode);
  });
})();
