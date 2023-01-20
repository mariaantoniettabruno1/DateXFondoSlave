(function() {
  "use strict";
  var frontend = "";
  function openAccordion(element) {
    const isHidden = window.getComputedStyle(element).display;
    if ("none" === isHidden) {
      element.style.display = "block";
      const height = element.offsetHeight;
      element.style.overflow = "hidden";
      element.style.height = "0";
      element.style.paddingTop = "0";
      element.style.paddingBottom = "0";
      element.style.marginTop = "0";
      element.style.marginBottom = "0";
      element.style.transitionProperty = "height, margin, padding";
      element.style.transitionDuration = "500ms";
      window.requestAnimationFrame(() => {
        element.style.height = height + "px";
        element.style.removeProperty("display");
        element.style.removeProperty("padding-top");
        element.style.removeProperty("margin-top");
        element.style.removeProperty("padding-bottom");
        element.style.removeProperty("margin-bottom");
      });
      setTimeout(() => {
        element.style.removeProperty("height");
        element.style.removeProperty("overflow");
        element.style.removeProperty("transition-duration");
        element.style.removeProperty("transition-property");
      }, 500);
    }
  }
  function closeAccordion(element) {
    const height = element.offsetHeight;
    element.style.transitionProperty = "height, margin, padding";
    element.style.transitionDuration = "500ms";
    element.style.height = height + "px";
    element.style.display = "block";
    element.style.position = "static";
    window.requestAnimationFrame(() => {
      element.style.overflow = "hidden";
      element.style.height = "0";
      element.style.paddingTop = "0";
      element.style.paddingBottom = "0";
      element.style.marginTop = "0";
      element.style.marginBottom = "0";
    });
    setTimeout(() => {
      element.style.removeProperty("position");
      element.style.removeProperty("height");
      element.style.removeProperty("display");
      element.style.removeProperty("overflow");
      element.style.removeProperty("transition");
      element.style.removeProperty("padding-top");
      element.style.removeProperty("padding-bottom");
      element.style.removeProperty("margin-top");
      element.style.removeProperty("margin-bottom");
      element.style.removeProperty("transition-duration");
      element.style.removeProperty("transition-property");
    }, 500);
  }
  function menu(element) {
    if (!element) {
      return;
    }
    if (element.dataset.zbMenuEnabled === "true") {
      return;
    }
    const config = typeof element.dataset.zbMenu !== "undefined" ? JSON.parse(element.dataset.zbMenu) : false;
    const centeredItems = Array.from(element.querySelectorAll(".zb-menuPosition--centered"));
    const fullWidthMenus = Array.from(element.querySelectorAll(".zb-menuWidth--full"));
    const mobileMenuTrigger = element.querySelector(".js-zb-mobile-menu-trigger");
    const menuContainer = element.querySelector(".zb-menu-container");
    let mobileMenuEnabled = false;
    element.dataset.zbMenuEnabled = "true";
    function enableDesktopMenu() {
      centeredItems.forEach((child) => {
        child.addEventListener("mouseover", repositionSubmenu);
      });
      fullWidthMenus.forEach((child) => {
        child.addEventListener("mouseover", setMaxWidth);
      });
    }
    function disableDesktopMenu() {
      centeredItems.forEach((child) => {
        child.removeEventListener("mouseover", repositionSubmenu);
      });
      fullWidthMenus.forEach((child) => {
        child.removeEventListener("mouseover", setMaxWidth);
      });
      centeredItems.forEach((child) => {
        var _a;
        (_a = child.querySelector(".sub-menu")) == null ? void 0 : _a.style.removeProperty("left");
      });
      fullWidthMenus.forEach((child) => {
        var _a;
        (_a = child.querySelector(".sub-menu")) == null ? void 0 : _a.style.removeProperty("max-width");
      });
    }
    function setMaxWidth() {
      this.style.maxWidth = `${window.outerWidth}px`;
    }
    function repositionSubmenu() {
      const subMenu = this.querySelector(":scope > .sub-menu");
      const listItemSize = this.getBoundingClientRect();
      const menuLeftOffset = listItemSize.left;
      const windowSize = window.outerWidth / 2;
      const newLeft = windowSize - menuLeftOffset - subMenu.getBoundingClientRect().width / 2;
      subMenu.style.left = `${newLeft}px`;
    }
    function onBrowserResize() {
      if (window.outerWidth <= config.breakpoint) {
        enableMobileMenu();
        disableDesktopMenu();
        if (mobileMenuEnabled && config.mobile_menu_full_width) {
          setMobileMenuLeft();
        }
      } else {
        enableDesktopMenu();
        disableMobileMenu();
      }
    }
    function enableMobileMenu() {
      if (!mobileMenuEnabled) {
        element.classList.add("zb-menu-mobile--active");
        enableAccordionMenu();
        mobileMenuEnabled = true;
      }
    }
    function disableMobileMenu() {
      element.classList.remove("zb-menu-mobile--active");
      element.classList.remove("zb-menu-trigger--active");
      menuContainer.style.left = "";
      disableAccordionMenu();
      mobileMenuEnabled = false;
    }
    function onAccordionItemClick(event) {
      var _a;
      const hasChild = (_a = this.parentElement) == null ? void 0 : _a.querySelector(".sub-menu");
      if (hasChild) {
        event.preventDefault();
        if (!hasChild.classList.contains("zb-menu--item--expand")) {
          openAccordion(hasChild);
          hasChild.classList.add("zb-menu--item--expand");
        } else {
          closeAccordion(hasChild);
          hasChild.classList.remove("zb-menu--item--expand");
        }
      }
    }
    function enableAccordionMenu() {
      const menuLinks = Array.from(
        element.querySelectorAll(".menu-item-has-children > .menu-link")
      );
      menuLinks.forEach((menuLink) => menuLink.addEventListener("click", onAccordionItemClick));
    }
    function disableAccordionMenu() {
      const menuLinks = Array.from(
        element.querySelectorAll(".menu-item-has-children > .menu-link")
      );
      menuLinks.forEach((menuLink) => menuLink.removeEventListener("click", onAccordionItemClick));
      const expandedMenus = Array.from(element.querySelectorAll(".zb-menu--item--expand"));
      expandedMenus.forEach((expandedMenu) => {
        expandedMenu.classList.remove("zb-menu--item--expand");
      });
    }
    function toggleMobileMenu(event) {
      event.preventDefault();
      event.stopPropagation();
      if (!element.classList.contains("zb-menu-trigger--active")) {
        element.classList.add("zb-menu-trigger--active");
        if (config.mobile_menu_full_width) {
          setMobileMenuLeft();
        }
      } else {
        element.classList.remove("zb-menu-trigger--active");
        menuContainer.style.left = "";
      }
    }
    function setMobileMenuLeft() {
      const elementRect = element.getBoundingClientRect();
      const leftOffset = elementRect.left;
      menuContainer.style.left = `-${leftOffset}px`;
    }
    function destroy() {
      window.removeEventListener("resize", onBrowserResize);
      mobileMenuTrigger == null ? void 0 : mobileMenuTrigger.removeEventListener("click", toggleMobileMenu);
      window.removeEventListener("click", maybePreventDefault);
    }
    function maybePreventDefault(event) {
      const target = event.target;
      if (target.matches('a.main-menu-link[href="#"]')) {
        event.preventDefault();
      }
    }
    window.addEventListener("resize", onBrowserResize);
    mobileMenuTrigger == null ? void 0 : mobileMenuTrigger.addEventListener("click", toggleMobileMenu);
    window.addEventListener("click", maybePreventDefault);
    onBrowserResize();
    if (config.vertical_submenu_style === "accordion") {
      enableAccordionMenu();
    }
    return {
      destroy
    };
  }
  const elements = Array.from(document.querySelectorAll(".zb-menu"));
  elements.forEach((element) => {
    menu(element);
  });
  window.zbScripts = window.zbScripts || {};
  window.zbScripts.menu = menu;
})();
