(()=>{var t={};t.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(t){if("object"==typeof window)return window}}(),(()=>{var r;t.g.importScripts&&(r=t.g.location+"");var e=t.g.document;if(!r&&e&&(e.currentScript&&(r=e.currentScript.src),!r)){var n=e.getElementsByTagName("script");n.length&&(r=n[n.length-1].src)}if(!r)throw new Error("Automatic publicPath is not supported in this browser");r=r.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),t.p=r+"../../../"})(),t.p=window.zionBuilderPaths[{}.appName],function(){"use strict";window.zb.hooks.addAction("zionbuilder/server_component/rendered",(function(t,r,e){if("zu_tabs_in_acrd"===r.element_type){const r=window.zbFrontend.scripts.zuTabsInAcrd;r&&(r.tabsinaccordions(t.closest(".zb-el-zuTabsInAcrd")),jQuery("#rating").trigger("init"))}}))}()})();