(function(t){function e(e){for(var a,o,u=e[0],s=e[1],c=e[2],l=0,d=[];l<u.length;l++)o=u[l],Object.prototype.hasOwnProperty.call(r,o)&&r[o]&&d.push(r[o][0]),r[o]=0;for(a in s)Object.prototype.hasOwnProperty.call(s,a)&&(t[a]=s[a]);f&&f(e);while(d.length)d.shift()();return i.push.apply(i,c||[]),n()}function n(){for(var t,e=0;e<i.length;e++){for(var n=i[e],a=!0,u=1;u<n.length;u++){var s=n[u];0!==r[s]&&(a=!1)}a&&(i.splice(e--,1),t=o(o.s=n[0]))}return t}var a={},r={software:0},i=[];function o(e){if(a[e])return a[e].exports;var n=a[e]={i:e,l:!1,exports:{}};return t[e].call(n.exports,n,n.exports,o),n.l=!0,n.exports}o.m=t,o.c=a,o.d=function(t,e,n){o.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},o.r=function(t){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},o.t=function(t,e){if(1&e&&(t=o(t)),8&e)return t;if(4&e&&"object"===typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(o.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var a in t)o.d(n,a,function(e){return t[e]}.bind(null,a));return n},o.n=function(t){var e=t&&t.__esModule?function(){return t["default"]}:function(){return t};return o.d(e,"a",e),e},o.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},o.p="/wp-content/themes/culu/vue/dist/";var u=window["webpackJsonp"]=window["webpackJsonp"]||[],s=u.push.bind(u);u.push=e,u=u.slice();for(var c=0;c<u.length;c++)e(u[c]);var f=s;i.push([0,"chunk-vendors"]),n()})({0:function(t,e,n){t.exports=n("e1ab")},"0bbf":function(t,e,n){"use strict";n("b6bc")},"68bc":function(t,e,n){var a=n("24fb");e=a(!1),e.push([t.i,"[aria-hidden=true][data-v-9e91d274]{display:none}.software-avail[data-v-9e91d274]{margin-bottom:3em}",""]),t.exports=e},b6bc:function(t,e,n){var a=n("68bc");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var r=n("499e").default;r("3dca1732",a,!0,{sourceMap:!1,shadowMode:!1})},e1ab:function(t,e,n){"use strict";n.r(e);n("e260"),n("e6cf"),n("cca6"),n("a79d");var a,r=n("2b0e"),i=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-app",[n("v-content",t._l(t.sassafrasData,(function(t,e){return n("SoftwareList",{key:e,attrs:{"unit-data":t}})})),1)],1)},o=[],u=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[n("h2",[t._v(t._s(t.unit)+" Library Software Availability")]),n("v-simple-table",{staticClass:"software-avail",attrs:{"fixed-header":"",height:"100vh"}},[n("thead",[n("tr",[n("th",[t._v("Software Name")]),t._l(t.locations,(function(e,a){return n("th",{key:a},[t._v(" "+t._s(t.trimLocation(e))+" ")])}))],2)]),n("tbody",{staticClass:"software-list__tbody"},t._l(t.uniqueSoftware,(function(e,a){return n("tr",{key:a},[n("td",[t._v(t._s(e.title))]),t._l(t.locations,(function(a,r){return n("td",{key:r},[-1!==e.locations.findIndex((function(t){return t.includes(a)}))?n("unicon",{attrs:{name:"check",fill:"limegreen",width:"35",height:"35"}}):t._e()],1)}))],2)})),0),n("caption",{attrs:{"aria-hidden":"true"}},[t._v(" Software availability on Mann Library computers ")])])],1)},s=[],c=(n("c740"),n("baa5"),n("d81d"),n("4ec9"),n("d3b7"),n("ac1f"),n("6062"),n("3ca3"),n("1276"),n("ddb0"),n("b85c")),f=n("2909"),l={props:{unitData:{type:Array,default:function(){return[]}}},computed:{locations:function(){return Object(f["a"])(new Set(this.unitData.map((function(t){return t.division})))).sort()},uniqueSoftware:function(){return this.mergeSoftware(this.unitData)},unit:function(){var t=this.locations[0].split(".");return t[t.length-2]}},methods:{trimLocation:function(t){var e=t.lastIndexOf(".")+1;return t.substring(e)},mergeSoftware:function(t){var e,n=[],a=new Map,r=Object(c["a"])(t);try{var i=function(){var t=e.value;if(a.has(t.id)){var r=n.findIndex((function(e){return e.id===t.id}));n[r].locations.push(t.division)}else a.set(t.id,!0),n.push({id:t.id,locations:[t.division],familyname:t.familyname,title:t.title})};for(r.s();!(e=r.n()).done;)i()}catch(o){r.e(o)}finally{r.f()}return n.sort((function(t,e){return t.title.localeCompare(e.title)}))}}},d=l,p=(n("0bbf"),n("2877")),v=n("6544"),b=n.n(v),h=n("1f4f"),y=Object(p["a"])(d,u,s,!1,null,"9e91d274",null),m=y.exports;b()(y,{VSimpleTable:h["a"]});var w={name:"Software",components:{SoftwareList:m},data:function(){return{sassafrasData:"undefined"!==typeof sassafrasDataWP?sassafrasDataWP:a}}},_=w,g=n("7496"),S=n("a75b"),O=Object(p["a"])(_,i,o,!1,null,null,null),x=O.exports;b()(O,{VApp:g["a"],VContent:S["a"]});var j=n("a2e0"),P=n.n(j),k=n("cf18"),M=n("f309");r["a"].use(M["a"]);var D=new M["a"]({});P.a.add([k["d"]]),r["a"].use(P.a),r["a"].config.productionTip=!1,new r["a"]({vuetify:D,render:function(t){return t(x)}}).$mount("#cul-software")}});