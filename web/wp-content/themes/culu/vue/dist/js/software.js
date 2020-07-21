(function(t){function e(e){for(var r,i,u=e[0],s=e[1],c=e[2],l=0,p=[];l<u.length;l++)i=u[l],Object.prototype.hasOwnProperty.call(a,i)&&a[i]&&p.push(a[i][0]),a[i]=0;for(r in s)Object.prototype.hasOwnProperty.call(s,r)&&(t[r]=s[r]);f&&f(e);while(p.length)p.shift()();return o.push.apply(o,c||[]),n()}function n(){for(var t,e=0;e<o.length;e++){for(var n=o[e],r=!0,u=1;u<n.length;u++){var s=n[u];0!==a[s]&&(r=!1)}r&&(o.splice(e--,1),t=i(i.s=n[0]))}return t}var r={},a={software:0},o=[];function i(e){if(r[e])return r[e].exports;var n=r[e]={i:e,l:!1,exports:{}};return t[e].call(n.exports,n,n.exports,i),n.l=!0,n.exports}i.m=t,i.c=r,i.d=function(t,e,n){i.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},i.r=function(t){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},i.t=function(t,e){if(1&e&&(t=i(t)),8&e)return t;if(4&e&&"object"===typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(i.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var r in t)i.d(n,r,function(e){return t[e]}.bind(null,r));return n},i.n=function(t){var e=t&&t.__esModule?function(){return t["default"]}:function(){return t};return i.d(e,"a",e),e},i.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},i.p="/";var u=window["webpackJsonp"]=window["webpackJsonp"]||[],s=u.push.bind(u);u.push=e,u=u.slice();for(var c=0;c<u.length;c++)e(u[c]);var f=s;o.push([0,"chunk-vendors"]),n()})({0:function(t,e,n){t.exports=n("e1ab")},"2f0b":function(t,e,n){"use strict";var r=n("e083"),a=n.n(r);a.a},e083:function(t,e,n){var r=n("f073");"string"===typeof r&&(r=[[t.i,r,""]]),r.locals&&(t.exports=r.locals);var a=n("499e").default;a("b19fa79c",r,!0,{sourceMap:!1,shadowMode:!1})},e1ab:function(t,e,n){"use strict";n.r(e);n("e260"),n("e6cf"),n("cca6"),n("a79d");var r=n("2b0e"),a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-app",[n("v-content",[n("SoftwareList")],1)],1)},o=[],i=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-simple-table",{attrs:{"fixed-header":"",height:"100vh"}},[n("thead",[n("tr",t._l(t.softwareList[0],(function(e,r){return n("th",{key:r},[t.smartmapQuery(r)?n("a",{attrs:{href:t.smartmapQuery(r),title:"Map it",target:"_blank"}},[t._v(" "+t._s(r)+" ")]):n("span",[t._v(t._s(r))])])})),0)]),n("tbody",{staticClass:"software-list__tbody"},t._l(t.softwareList,(function(e,r){return n("tr",{key:r},t._l(e,(function(e,r){return n("td",{key:r},["X"===e?n("unicon",{attrs:{name:"check",fill:"limegreen",width:"35",height:"35"}}):n("span",[t._v(t._s(e))])],1)})),0)})),0),n("caption",{attrs:{"aria-hidden":"true"}},[t._v("Software availability on Mann Library computers")])])},u=[],s=(n("c975"),n("ac1f"),n("5319"),n("498a"),n("bc3a")),c=n.n(s),f=n("4d7c"),l=n.n(f),p={data:function(){return{softwareList:[]}},methods:{smartmapQuery:function(t){var e="http://smartmap.mannlib.cornell.edu/location/";return-1!==t.indexOf("iMacs")||-1!==t.indexOf("Research")?e+"stone computing center":-1!==t.indexOf("PCs")?e+t.replace("PCs","").trim().toLowerCase():-1!==t.indexOf("Circ")&&e+"circulation services"}},mounted:function(){var t=this;c.a.get("https://raw.githubusercontent.com/cul-it/mann-softwarelist-csv/master/softwarelist.csv").then((function(t){return l()().fromString(t.data)})).then((function(e){return t.softwareList=e}))}},d=p,h=(n("2f0b"),n("2877")),v=n("6544"),b=n.n(v),m=n("1f4f"),y=Object(h["a"])(d,i,u,!1,null,null,null),w=y.exports;b()(y,{VSimpleTable:m["a"]});var _={name:"Software",components:{SoftwareList:w},data:function(){return{}}},g=_,O=n("7496"),x=n("a75b"),j=Object(h["a"])(g,a,o,!1,null,null,null),S=j.exports;b()(j,{VApp:O["a"],VContent:x["a"]});var M=n("a2e0"),P=n.n(M),k=n("cf18"),L=n("f309");r["a"].use(L["a"]);var C=new L["a"]({});P.a.add([k["a"]]),r["a"].use(P.a),r["a"].config.productionTip=!1,new r["a"]({vuetify:C,render:function(t){return t(S)}}).$mount("#cul-software")},f073:function(t,e,n){var r=n("24fb");e=r(!1),e.push([t.i,"[aria-hidden=true]{display:none}",""]),t.exports=e}});