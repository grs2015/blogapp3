import{d as $,a as B,v,b as c,o as m,e as p,g as e,h as r,j as b,t as o,f as t,i as s,x as C,L as F,r as S,H as N,F as P}from"./app.2d2cf3e6.js";import{_ as A,o as D}from"./breadcrumbsData.2260b39f.js";const E={class:"q-mt-md"},L={class:"column q-col-gutter-md"},H={class:"row q-col-gutter-x-md"},I={class:"col-8 q-col-gutter-y-md"},M={class:"row q-col-gutter-md"},T={class:"col-6"},G={class:"col q-mb-md"},J={class:"text-subtitle2 text-primary text-weight-regular"},K={class:"col"},O={class:"text-subtitle2 text-primary text-weight-regular"},Q={class:"col-6"},R={class:"col q-mb-md"},W={class:"text-subtitle2 text-primary text-weight-regular"},X={class:"col"},Y={class:"text-subtitle2 text-primary text-weight-regular"},Z={class:"row q-col-gutter-md"},tt={class:"col"},et={class:"col q-mb-md"},st={class:"text-subtitle2 text-primary text-weight-regular"},at={class:"col"},ot={class:"text-subtitle2 text-primary text-weight-regular"},lt={class:"col-4 column q-col-gutter-y-md"},it={class:"col-auto"},rt={class:"text-subtitle2 text-primary text-weight-regular"},dt={class:"row flex-center relative-position"},nt={key:0},ct={key:1},_t={class:"row q-mt-md justify-between items-center"},ut={class:"text-subtitle2 text-primary text-weight-regular"},mt={class:"text-right"},pt={class:"row justify-between items-center"},ht={class:"text-subtitle2 text-primary text-weight-regular"},gt={class:"text-right"},ft={class:"row justify-between items-center"},vt={class:"text-subtitle2 text-primary text-weight-regular"},bt={key:0,class:"row items-center"},xt={class:"text-green"},yt={key:1,class:"row items-center"},qt={class:"text-red"},wt={class:"row justify-between items-center"},Vt={class:"text-subtitle2 text-primary text-weight-regular"},$t={class:"text-right"},Ut={class:"row justify-between items-center"},kt={class:"text-subtitle2 text-primary text-weight-regular"},jt={class:"text-subtitle2 text-primary text-weight-regular"},zt={class:"row flex-center text-h2 text-grey"},Bt={class:"row justify-end items-center"},Ct={class:"row q-col-gutter-md"},Ft=$({__name:"ProfileForm",props:{data:null},setup(f){const i=f,a=B({id:i.data.user.id,email:i.data.user.email,first_name:i.data.user.first_name,last_name:i.data.user.last_name,intro:i.data.user.intro,last_login:i.data.user.last_login,mobile:i.data.user.mobile,posts_count:i.data.user.posts_count,profile:i.data.user.profile,registered_at:i.data.user.registered_at,roles:i.data.user.roles,status:i.data.user.status,email_verified_at:i.data.user.email_verified_at,avatar:i.data.user.avatar}),x=v(()=>i.data.user.avatar),y=v(()=>!!i.data.user.avatar),U=v(()=>a.first_name[0]+a.last_name[0]);return(l,d)=>{const _=c("q-card-section"),g=c("q-input"),h=c("q-card"),q=c("q-editor"),u=c("q-separator"),w=c("q-avatar"),k=c("q-img"),V=c("q-icon"),j=c("q-btn"),z=c("q-form");return m(),p("div",E,[e(z,{onSubmit:l.actionAccount},{default:r(()=>[e(h,{flat:"",bordered:""},{default:r(()=>[e(_,{class:"text-primary text-h6"},{default:r(()=>[b(o(l.$t("User profile information")),1)]),_:1}),e(_,null,{default:r(()=>[t("div",L,[t("div",H,[t("div",I,[t("div",M,[t("div",T,[e(h,{flat:"",bordered:""},{default:r(()=>[e(_,null,{default:r(()=>[t("div",G,[t("div",J,o(l.$t("First name")),1),e(g,{disable:"",modelValue:s(a).first_name,"onUpdate:modelValue":d[0]||(d[0]=n=>s(a).first_name=n),dense:"","data-test":"first_name-input","input-class":"bg-grey-3",filled:""},null,8,["modelValue"])]),t("div",K,[t("div",O,o(l.$t("Last name")),1),e(g,{disable:"",modelValue:s(a).last_name,"onUpdate:modelValue":d[1]||(d[1]=n=>s(a).last_name=n),dense:"","data-test":"last_name-input","input-class":"bg-grey-3",filled:""},null,8,["modelValue"])])]),_:1})]),_:1})]),t("div",Q,[e(h,{flat:"",bordered:""},{default:r(()=>[e(_,null,{default:r(()=>[t("div",R,[t("div",W,o(l.$t("Mobile")),1),e(g,{disable:"",modelValue:s(a).mobile,"onUpdate:modelValue":d[2]||(d[2]=n=>s(a).mobile=n),type:"tel",dense:"","data-test":"mobile-input","input-class":"bg-grey-3",filled:""},null,8,["modelValue"])]),t("div",X,[t("div",Y,o(l.$t("Email")),1),e(g,{disable:"",modelValue:s(a).email,"onUpdate:modelValue":d[3]||(d[3]=n=>s(a).email=n),type:"email",dense:"","data-test":"email-input","input-class":"bg-grey-3",filled:""},null,8,["modelValue"])])]),_:1})]),_:1})])]),t("div",Z,[t("div",tt,[e(h,{flat:"",bordered:""},{default:r(()=>[e(_,null,{default:r(()=>[t("div",et,[t("div",st,o(l.$t("Short introduction info")),1),e(q,{disable:"",placeholder:l.$t("Drop some lines..."),modelValue:s(a).intro,"onUpdate:modelValue":d[4]||(d[4]=n=>s(a).intro=n),"min-height":"5rem","content-class":"bg-grey-4"},null,8,["placeholder","modelValue"])]),t("div",at,[t("div",ot,o(l.$t("Profile description")),1),e(q,{disable:"","min-height":"8rem",placeholder:l.$t("Drop some lines..."),modelValue:s(a).profile,"onUpdate:modelValue":d[5]||(d[5]=n=>s(a).profile=n),"content-class":"bg-grey-4"},null,8,["placeholder","modelValue"])])]),_:1})]),_:1})])])]),t("div",lt,[t("div",it,[e(h,{flat:"",bordered:""},{default:r(()=>[e(_,{class:"column"},{default:r(()=>[t("div",rt,o(l.$t("Current avatar")),1),e(u,{spaced:""}),t("div",dt,[s(y)?(m(),p("div",ct,[e(w,{size:"100px"},{default:r(()=>[e(k,{height:"100px",src:s(x)},null,8,["src"])]),_:1})])):(m(),p("div",nt,[e(w,{size:"100px",color:"orange"},{default:r(()=>[b(o(s(U)),1)]),_:1})]))]),t("div",_t,[t("div",ut,o(l.$t("User registered at:")),1),t("div",mt,o(s(a).registered_at),1)]),e(u,{spaced:""}),t("div",pt,[t("div",ht,o(l.$t("User last logged-in:")),1),t("div",gt,o(s(a).last_login),1)]),e(u,{spaced:""}),t("div",ft,[t("div",vt,o(l.$t("Email status:")),1),s(a).email_verified_at?(m(),p("div",bt,[e(V,{size:"1.5rem",name:"verified",color:"green",class:"q-mr-xs"}),t("div",xt,o(l.$t("Verified")),1)])):(m(),p("div",yt,[e(V,{size:"1.5rem",name:"error_outline",color:"red",class:"q-mr-xs"}),t("div",qt,o(l.$t("Not Verified")),1)]))]),e(u,{spaced:""}),t("div",wt,[t("div",Vt,o(l.$t("User role:")),1),t("div",$t,o(s(a).roles),1)]),e(u,{spaced:""}),t("div",Ut,[t("div",kt,o(l.$t("User status:")),1),t("div",{class:C(["text-right",[s(a).status==="enabled"?"text-green":"text-red"]])},o(s(a).status),3)]),e(u,{spaced:""}),t("div",jt,o(l.$t("User posts")),1),t("div",zt,o(s(a).posts_count),1)]),_:1})]),_:1})])])])])]),_:1}),e(_,null,{default:r(()=>[t("div",Bt,[t("div",Ct,[t("div",null,[e(s(F),{as:"div",href:"/admin/users"},{default:r(()=>[e(j,{outline:"",color:"secondary"},{default:r(()=>[b(o(l.$t("Back")),1)]),_:1})]),_:1})])])])]),_:1})]),_:1})]),_:1},8,["onSubmit"])])}}}),Pt=$({__name:"Show",props:{model:null},setup(f){const i=f,a=S(D);return(x,y)=>(m(),p(P,null,[e(s(N),{title:"User Profile"}),e(A,{data:a.value},null,8,["data"]),e(Ft,{data:i.model},null,8,["data"])],64))}});export{Pt as default};
