(()=>{var e={};e.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(e){if("object"==typeof window)return window}}(),(()=>{var t;e.g.importScripts&&(t=e.g.location+"");var o=e.g.document;if(!t&&o&&(o.currentScript&&(t=o.currentScript.src),!t)){var r=o.getElementsByTagName("script");r.length&&(t=r[r.length-1].src)}if(!t)throw new Error("Automatic publicPath is not supported in this browser");t=t.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),e.p=t+"../../../"})(),e.p=window.zionBuilderPaths[{}.appName],(()=>{"use strict";const e=zb.vue,t={name:"zu_new_badge",props:["options"],render:function(t,o,r,n,i,a){return"zu"!=r.options.el_valid&&null!=r.options.el_valid?((0,e.openBlock)(),(0,e.createBlock)((0,e.resolveDynamicComponent)(r.options.tag||"div"),{key:0},{default:(0,e.withCtx)((()=>[(0,e.renderSlot)(t.$slots,"start"),(0,e.createTextVNode)(" "+(0,e.toDisplayString)(r.options.badge)+" ",1),(0,e.renderSlot)(t.$slots,"end")])),_:3})):(0,e.createCommentVNode)("",!0)}};window.zb.editor.registerElementComponent({elementType:"zu_new_badge",component:t})})()})();