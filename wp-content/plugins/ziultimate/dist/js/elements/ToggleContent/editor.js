(()=>{var e={};e.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(e){if("object"==typeof window)return window}}(),(()=>{var t;e.g.importScripts&&(t=e.g.location+"");var o=e.g.document;if(!t&&o&&(o.currentScript&&(t=o.currentScript.src),!t)){var n=o.getElementsByTagName("script");n.length&&(t=n[n.length-1].src)}if(!t)throw new Error("Automatic publicPath is not supported in this browser");t=t.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),e.p=t+"../../../"})(),e.p=window.zionBuilderPaths[{}.appName],(()=>{"use strict";const e=zb.vue,t={name:"zu_toggle_title",props:["options","element"],render:function(t,o,n,l,r,i){const s=(0,e.resolveComponent)("SortableContent");return"zu"!=n.options.el_valid&&null!=n.options.el_valid?((0,e.openBlock)(),(0,e.createBlock)(s,{key:n.element.uid,element:n.element,class:["zu-toggle-title",[{"zu-toggle--active":"yes"==n.options.active_by_default}]],"aria-expanded":"true","aria-selected":"true",role:"button",tabindex:n.options.tabindex},{start:(0,e.withCtx)((()=>[(0,e.renderSlot)(t.$slots,"start")])),end:(0,e.withCtx)((()=>[(0,e.renderSlot)(t.$slots,"end")])),_:1},8,["element","class","tabindex"])):(0,e.createCommentVNode)("",!0)}},o={key:0,class:"zu-toggle-content","aria-selected":"true","aria-hidden":"false"},n={key:1,class:"zu-toggle-content--collapsed"},l={name:"zu_toggle_content",props:["options","element","api"],render:function(t,l,r,i,s,a){const c=(0,e.resolveComponent)("SortableContent");return"zu"!=r.options.el_valid&&null!=r.options.el_valid?((0,e.openBlock)(),(0,e.createBlock)("div",o,[(0,e.renderSlot)(t.$slots,"start"),"none"!=r.options.preview?((0,e.openBlock)(),(0,e.createBlock)(c,(0,e.mergeProps)({key:0,key:r.element.uid,element:r.element},r.api.getAttributesForTag("content_styles"),{class:["zu-toggle-content--inner",r.api.getStyleClasses("content_styles")]}),null,16,["element","class"])):(0,e.createCommentVNode)("",!0),"block"!=r.options.preview?((0,e.openBlock)(),(0,e.createBlock)("div",n,' Select "Expand" for editing. ')):(0,e.createCommentVNode)("",!0),(0,e.renderSlot)(t.$slots,"end")])):(0,e.createCommentVNode)("",!0)}},r={name:"zu_toggle_button",props:["options","element","api"],render:function(t,o,n,l,r,i){const s=(0,e.resolveComponent)("ElementIcon");return"zu"!=n.options.has_button&&null!=n.options.has_button?((0,e.openBlock)(),(0,e.createBlock)("div",{key:0,class:["zu-toggle-button",n.options.icon_anim],role:"button","aria-label":"options.aria_label"},[(0,e.renderSlot)(t.$slots,"start"),(0,e.createVNode)(s,(0,e.mergeProps)({class:["zu-toggle-button--icon",n.api.getStyleClasses("icon_styles")],iconConfig:n.options.icon},n.api.getAttributesForTag("icon_styles")),null,16,["class","iconConfig"]),(0,e.renderSlot)(t.$slots,"end")],2)):(0,e.createCommentVNode)("",!0)}};window.zb.editor.registerElementComponent({elementType:"zu_toggle_title",component:t}),window.zb.editor.registerElementComponent({elementType:"zu_toggle_content",component:l}),window.zb.editor.registerElementComponent({elementType:"zu_toggle_button",component:r})})()})();