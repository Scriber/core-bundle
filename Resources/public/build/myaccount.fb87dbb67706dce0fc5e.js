!function(t){function r(e){if(a[e])return a[e].exports;var s=a[e]={i:e,l:!1,exports:{}};return t[e].call(s.exports,s,s.exports,r),s.l=!0,s.exports}var a={};r.m=t,r.c=a,r.d=function(t,a,e){r.o(t,a)||Object.defineProperty(t,a,{configurable:!1,enumerable:!0,get:e})},r.n=function(t){var a=t&&t.__esModule?function(){return t.default}:function(){return t};return r.d(a,"a",a),a},r.o=function(t,r){return Object.prototype.hasOwnProperty.call(t,r)},r.p="/bundles/scribercore/build/",r(r.s="hid1")}({F9h8:function(t,r,a){"use strict";var e=function(){var t=this,r=t.$createElement,a=t._self._c||r;return a("div",[a("div",{staticClass:"row"},[a("div",{staticClass:"col-12 col-sm-6 col-md-8 col-lg-6 col-xl-8"},[a("b-form",{on:{submit:function(r){r.preventDefault(),t.updateProfile(r)}}},[a("b-card",{attrs:{"border-variant":"none","bg-variant":"transparent",header:t.$t.trans("my_account.title.account_settings")}},[a("b-form-group",[a("label",{attrs:{for:"form-email"}},[t._v(t._s(t.$t.trans("form.label.email")))]),t._v(" "),a("b-form-input",{attrs:{id:"form-email",required:"",type:"email"},model:{value:t.user.data.email,callback:function(r){t.$set(t.user.data,"email",r)},expression:"user.data.email"}})],1),t._v(" "),a("b-form-group",[a("label",{attrs:{for:"form-name"}},[t._v(t._s(t.$t.trans("my_account.form.label.name")))]),t._v(" "),a("b-form-input",{attrs:{id:"form-name",required:"",type:"text"},model:{value:t.user.data.name,callback:function(r){t.$set(t.user.data,"name",r)},expression:"user.data.name"}})],1),t._v(" "),a("div",{attrs:{slot:"footer"},slot:"footer"},[a("b-button",{attrs:{type:"submit",variant:"primary",disabled:!t.dataLoaded||t.user.loading}},[t._v("\n                            "+t._s(t.$t.trans("action.save"))+"\n                        ")])],1)],1)],1)],1),t._v(" "),a("div",{staticClass:"col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4"},[a("b-form",{on:{submit:function(r){r.preventDefault(),t.updatePassword(r)}}},[a("b-card",{attrs:{header:t.$t.trans("my_account.title.change_password"),"footer-tag":"footer"}},[a("b-form-group",[a("label",{attrs:{for:"form-current-password"}},[t._v(t._s(t.$t.trans("my_account.form.label.current_password")))]),t._v(" "),a("b-form-input",{attrs:{id:"form-current-password",required:"",type:"password",state:t.password.errorState("oldPassword")},model:{value:t.password.data.oldPassword,callback:function(r){t.$set(t.password.data,"oldPassword",r)},expression:"password.data.oldPassword"}}),t._v(" "),t._l(t.password.getErrors("oldPassword"),function(r,e){return a("b-form-invalid-feedback",{key:e},[t._v(t._s(r)+"\n                        ")])})],2),t._v(" "),a("b-form-group",[a("label",{attrs:{for:"form-new-password"}},[t._v(t._s(t.$t.trans("my_account.form.label.new_password")))]),t._v(" "),a("b-form-input",{attrs:{id:"form-new-password",required:"",type:"password",state:t.password.errorState("password")},model:{value:t.password.data.password,callback:function(r){t.$set(t.password.data,"password",r)},expression:"password.data.password"}})],1),t._v(" "),a("b-form-group",[a("b-form-input",{attrs:{required:"",type:"password",state:t.password.errorState("password")},model:{value:t.password.data.repeat,callback:function(r){t.$set(t.password.data,"repeat",r)},expression:"password.data.repeat"}}),t._v(" "),t._l(t.password.getErrors("password"),function(r,e){return a("b-form-invalid-feedback",{key:e},[t._v(t._s(r)+"\n                        ")])})],2),t._v(" "),a("div",{attrs:{slot:"footer"},slot:"footer"},[a("b-button",{attrs:{type:"submit",variant:"primary",disabled:t.password.data.password!==t.password.data.repeat||t.password.loading}},[t._v("\n                            "+t._s(t.$t.trans("action.save"))+"\n                        ")])],1)],1)],1)],1)])])},s=[];e._withStripped=!0;var o={render:e,staticRenderFns:s};r.a=o},TH1Y:function(t,r,a){"use strict";r.a={data:function(){return{user:new $Scriber.form(["email","name"],!0),password:new $Scriber.form(["password","repeat","oldPassword"],!0),dataLoaded:!1}},mounted:function(){var t=this;this.$http.get($Scriber.getUrl("/api/my-account")).then(function(r){t.user.data.email=r.data.email,t.user.data.name=r.data.name,t.dataLoaded=!0})},methods:{updateProfile:function(){this.user.submit($Scriber.getUrl("/api/my-account"),"PUT")},updatePassword:function(){this.password.data.password===this.password.data.repeat&&this.password.submit($Scriber.getUrl("/api/my-account/password"),"PUT",{password:"password",oldPassword:"oldPassword"})}}}},"VU/8":function(t,r){t.exports=function(t,r,a,e,s,o){var n,d=t=t||{},i=typeof t.default;"object"!==i&&"function"!==i||(n=t,d=t.default);var u="function"==typeof d?d.options:d;r&&(u.render=r.render,u.staticRenderFns=r.staticRenderFns,u._compiled=!0),a&&(u.functional=!0),s&&(u._scopeId=s);var l;if(o?(l=function(t){t=t||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext,t||"undefined"==typeof __VUE_SSR_CONTEXT__||(t=__VUE_SSR_CONTEXT__),e&&e.call(this,t),t&&t._registeredComponents&&t._registeredComponents.add(o)},u._ssrRegister=l):e&&(l=e),l){var c=u.functional,p=c?u.render:u.beforeCreate;c?(u._injectStyles=l,u.render=function(t,r){return l.call(r),p(t,r)}):u.beforeCreate=p?[].concat(p,l):[l]}return{esModule:n,exports:d,options:u}}},hid1:function(t,r,a){"use strict";Object.defineProperty(r,"__esModule",{value:!0});var e=a("lRwf"),s=a.n(e),o=a("vN0u");new s.a({el:"#app",render:function(t){return t(o.a)}})},lRwf:function(t,r){t.exports=Vue},vN0u:function(t,r,a){"use strict";var e=a("TH1Y"),s=a("F9h8"),o=a("VU/8"),n=o(e.a,s.a,!1,null,null,null);n.options.__file="js/my-account/App.vue",r.a=n.exports}});