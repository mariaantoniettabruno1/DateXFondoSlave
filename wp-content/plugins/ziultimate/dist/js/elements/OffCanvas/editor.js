(()=>{var e={};e.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(e){if("object"==typeof window)return window}}(),(()=>{var t;e.g.importScripts&&(t=e.g.location+"");var o=e.g.document;if(!t&&o&&(o.currentScript&&(t=o.currentScript.src),!t)){var n=o.getElementsByTagName("script");n.length&&(t=n[n.length-1].src)}if(!t)throw new Error("Automatic publicPath is not supported in this browser");t=t.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),e.p=t+"../../../"})(),e.p=window.zionBuilderPaths[{}.appName],(()=>{"use strict";const e=zb.vue,t={key:0,class:"zu-oc-backdrop"},o={name:"zu_off_canvas",props:["options","element"],computed:{getElementClasses(){let e="zu-ocp-"+this.options.ocp_position;return this.options.will_customize&&(e+=" zu-customize-sb"),"yes"==this.options.ocpreview&&(e+=" zu-hide-panel"),e}},render:function(o,n,r,s,i,c){const l=(0,e.resolveComponent)("SortableContent");return"zu"!=r.options.el_valid&&null!=r.options.el_valid?((0,e.openBlock)(),(0,e.createBlock)("div",{key:0,class:["zu-off-canvas",c.getElementClasses]},[(0,e.renderSlot)(o.$slots,"start"),r.options.disable_backdrop?(0,e.createCommentVNode)("",!0):((0,e.openBlock)(),(0,e.createBlock)("div",t)),(0,e.createVNode)(l,{key:r.element.uid,element:r.element,class:"zu-oc-inner-wrap zu-off-canvas-panel"},null,8,["element"]),(0,e.renderSlot)(o.$slots,"end")],2)):(0,e.createCommentVNode)("",!0)}};window.zb.editor.registerElementComponent({elementType:"zu_off_canvas",component:o})})()})();