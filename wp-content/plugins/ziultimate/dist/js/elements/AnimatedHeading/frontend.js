(()=>{var e={};e.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(e){if("object"==typeof window)return window}}(),(()=>{var i;e.g.importScripts&&(i=e.g.location+"");var n=e.g.document;if(!i&&n&&(n.currentScript&&(i=n.currentScript.src),!i)){var s=n.getElementsByTagName("script");s.length&&(i=s[s.length-1].src)}if(!i)throw new Error("Automatic publicPath is not supported in this browser");i=i.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),e.p=i+"../../../"})(),e.p=window.zionBuilderPaths[{}.appName],window.zbFrontend=window.zbFrontend||[],window.zbFrontend.scripts=window.zbFrontend.scripts||{},window.zbFrontend.scripts.zuAnimatedHeading=function(e=window.jQuery){var n=0,s=0,t=0,a=0,r=0,d=0,o=0,l=0,c=0;function p(p){var h,f,w=JSON.parse(p.find(".zu-animh-wrap").attr("data-animh-config"));n=parseInt(w.animationDelay),s=parseInt(w.barAnimationDelay),t=s-3e3,a=parseInt(w.lettersDelay),r=parseInt(w.typeLettersDelay),d=parseInt(w.selectionDuration),o=d+800,l=parseInt(w.revealDuration),c=parseInt(w.revealAnimationDelay),p.find(".cd-headline.letters").find("b").each((function(){var n=e(this),s=n.text().split(""),t=n.hasClass("is-visible");for(i in s)n.parents(".rotate-2").length>0&&(s[i]="<em>"+s[i]+"</em>"),s[i]=t?'<i class="in">'+s[i]+"</i>":"<i>"+s[i]+"</i>";var a=s.join("");n.html(a).css("opacity",1)})),h=p.find(".cd-headline"),f=n,h.each((function(){var i=e(this);if(i.hasClass("loading-bar"))f=s,setTimeout((function(){i.find(".cd-words-wrapper").addClass("is-loading")}),t);else if(i.hasClass("clip")){var n=i.find(".cd-words-wrapper"),a=n.width()+10;n.css("width",a)}else if(!i.hasClass("type")){var r=i.find(".cd-words-wrapper b"),d=0;r.each((function(){var i=e(this).innerWidth();i>d&&(d=i)})),i.find(".cd-words-wrapper").css("width",d)}setTimeout((function(){u(i.find(".is-visible").eq(0))}),f)}))}function u(e){var i=m(e);if(e.parents(".cd-headline").hasClass("type")){var c=e.parent(".cd-words-wrapper");c.addClass("selected").removeClass("waiting"),setTimeout((function(){c.removeClass("selected"),e.removeClass("is-visible").addClass("is-hidden").children("i").removeClass("in").addClass("out")}),d),setTimeout((function(){h(i,r)}),o)}else if(e.parents(".cd-headline").hasClass("letters")){var p=e.children("i").length>=i.children("i").length;f(e.find("i").eq(0),e,p,a),w(i.find("i").eq(0),i,p,a)}else e.parents(".cd-headline").hasClass("clip")?e.parents(".cd-words-wrapper").animate({width:"2px"},l,(function(){v(e,i),h(i)})):e.parents(".cd-headline").hasClass("loading-bar")?(e.parents(".cd-words-wrapper").removeClass("is-loading"),v(e,i),setTimeout((function(){u(i)}),s),setTimeout((function(){e.parents(".cd-words-wrapper").addClass("is-loading")}),t)):(v(e,i),setTimeout((function(){u(i)}),n))}function h(e,i){e.parents(".cd-headline").hasClass("type")?(w(e.find("i").eq(0),e,!1,i),e.addClass("is-visible").removeClass("is-hidden")):e.parents(".cd-headline").hasClass("clip")&&e.parents(".cd-words-wrapper").animate({width:e.width()+10},l,(function(){setTimeout((function(){u(e)}),c)}))}function f(i,s,t,a){if(i.removeClass("in").addClass("out"),i.is(":last-child")?t&&setTimeout((function(){u(m(s))}),n):setTimeout((function(){f(i.next(),s,t,a)}),a),i.is(":last-child")&&e("html").hasClass("no-csstransitions")){var r=m(s);v(s,r)}}function w(e,i,s,t){e.addClass("in").removeClass("out"),e.is(":last-child")?(i.parents(".cd-headline").hasClass("type")&&setTimeout((function(){i.parents(".cd-words-wrapper").addClass("waiting")}),200),s||setTimeout((function(){u(i)}),n)):setTimeout((function(){w(e.next(),i,s,t)}),t)}function m(e){return e.is(":last-child")?e.parent().children().eq(0):e.next()}function v(e,i){e.removeClass("is-visible").addClass("is-hidden"),i.removeClass("is-hidden").addClass("is-visible")}return{run:function(i=document){const n=i.querySelectorAll(".zb-el-zuAnimatedHeading");n.length>0&&n.forEach((i=>{p(e(i))}))},initAnimatedHeading:p}}(),window.zbFrontend.scripts.zuAnimatedHeading.run()})();