(()=>{var t={};t.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(t){if("object"==typeof window)return window}}(),(()=>{var r;t.g.importScripts&&(r=t.g.location+"");var e=t.g.document;if(!r&&e&&(e.currentScript&&(r=e.currentScript.src),!r)){var n=e.getElementsByTagName("script");n.length&&(r=n[n.length-1].src)}if(!r)throw new Error("Automatic publicPath is not supported in this browser");r=r.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),t.p=r+"../../../"})(),t.p=window.zionBuilderPaths[{}.appName],window.zbFrontend=window.zbFrontend||[],window.zbFrontend.scripts=window.zbFrontend.scripts||{},window.zbFrontend.scripts.zuAnimatedBurger={run:function(t=document){const r=t.querySelectorAll("button.hamburger"),e="click";("ontouchstart"in window||window.navigator.msPointerEnabled||"ontouchstart"in document.documentElement)&&(e="touchstart"),r.length>0&&r.forEach((t=>{t.addEventListener(e,(function(t){t.preventDefault(),t.currentTarget.classList.toggle("is-active"),t.currentTarget.closest(".zb-el-zuBurger").querySelector(".zu-burger-sub-menu")&&zuSlideToggle(t.currentTarget.closest(".zb-el-zuBurger").querySelector(".zu-burger-sub-menu"))}))}))}},window.zbFrontend.scripts.zuAnimatedBurger.run()})();