import{_ as C,d as F}from"./breadcrumbsData.5c06e62c.js";import{d as q,n,b as c,o as u,k as x,h as t,g as e,f as l,t as o,x as T,i as _,e as h,j as g,F as v,v as k,r as L,H as N}from"./app.ea05a65f.js";import{c as P}from"./index.673391cf.js";import{f as A}from"./index.18f6e9ea.js";const V={class:"q-mr-sm"},B={key:1,class:"text-grey"},M=q({__name:"RecentPostsTable",props:{data:null},setup(b){const r=b,m=[{name:"postTitle",required:!0,label:n("Title"),align:"left",sortable:!1,field:a=>a.title,style:"width: 100%"},{name:"postAuthor",required:!0,label:n("Author"),align:"left",sortable:!1,field:a=>a.user.full_name},{name:"postPublishedAt",required:!0,label:n("To be published at"),align:"left",sortable:!1,field:a=>a.published_at}];return(a,y)=>{const p=c("q-badge"),f=c("q-td"),d=c("q-table");return u(),x(d,{dense:"","table-header-class":"text-primary bg-blue-1","row-key":s=>s.id,columns:m,rows:r.data,"rows-per-page-options":[0],"hide-pagination":"","hide-bottom":"",flat:"",color:"primary"},{"body-cell-postPublishedAt":t(s=>[e(f,{props:s,"auto-width":""},{default:t(()=>[l("span",V,o(s.row.published_at),1),s.row.status==="pending"?(u(),x(p,{key:0,align:"top",class:T([_(P)(new Date(s.row.published_at),new Date)===-1?"text-green":"text-red"]),outline:""},{default:t(()=>[_(P)(new Date(s.row.published_at),new Date)===-1?(u(),h(v,{key:0},[g(" + "+o(_(A)(new Date(s.row.published_at))),1)],64)):(u(),h(v,{key:1},[g(" - "+o(_(A)(new Date(s.row.published_at))),1)],64))]),_:2},1032,["class"])):(u(),h("span",B,[e(p,{outline:"",class:"text-grey"},{default:t(()=>[g(o(a.$t("no date set")),1)]),_:1})]))]),_:2},1032,["props"])]),_:1},8,["row-key","rows"])}}}),U={class:"text-primary text-weight-medium"},S=q({__name:"RatedPostsTable",props:{data:null},setup(b){const r=b,m=[{name:"postTitle",required:!0,label:n("Title"),align:"left",sortable:!1,field:a=>a.title,style:"width: 100%"},{name:"postRating",required:!0,label:n("Rating"),align:"left",sortable:!1,field:a=>a.rating},{name:"postAuthor",required:!0,label:n("Author"),align:"left",sortable:!1,field:a=>a.user.full_name},{name:"postPublishedAt",required:!0,label:n("Published at"),align:"left",sortable:!1,field:a=>a.published_at}];return(a,y)=>{const p=c("q-td"),f=c("q-table");return u(),x(f,{dense:"","table-header-class":"text-primary bg-blue-1","row-key":d=>d.id,columns:m,rows:r.data,"rows-per-page-options":[0],"hide-pagination":"","hide-bottom":"",flat:"",color:"primary"},{"body-cell-postRating":t(d=>[e(p,{props:d,"auto-width":"",class:"bg-green-1"},{default:t(()=>[l("span",U,o(d.row.rating),1)]),_:2},1032,["props"])]),_:1},8,["row-key","rows"])}}}),j={class:"text-primary text-weight-medium"},E=q({__name:"ViewedPostsTable",props:{data:null},setup(b){const r=b,m=[{name:"postTitle",required:!0,label:n("Title"),align:"left",sortable:!1,field:a=>a.title,style:"width: 100%"},{name:"postRating",required:!0,label:n("Views"),align:"left",sortable:!1,field:a=>a.views},{name:"postAuthor",required:!0,label:n("Author"),align:"left",sortable:!1,field:a=>a.user.full_name},{name:"postPublishedAt",required:!0,label:n("Published at"),align:"left",sortable:!1,field:a=>a.published_at}];return(a,y)=>{const p=c("q-td"),f=c("q-table");return u(),x(f,{dense:"","table-header-class":"text-primary bg-blue-1","row-key":d=>d.id,columns:m,rows:r.data,"rows-per-page-options":[0],"hide-pagination":"","hide-bottom":"",flat:"",color:"primary"},{"body-cell-postRating":t(d=>[e(p,{props:d,"auto-width":"",class:"bg-green-1"},{default:t(()=>[l("span",j,o(d.row.views),1)]),_:2},1032,["props"])]),_:1},8,["row-key","rows"])}}}),H={class:"text-primary text-weight-medium"},z=q({__name:"CommentedPostsTable",props:{data:null},setup(b){const r=b,m=[{name:"postTitle",required:!0,label:n("Title"),align:"left",sortable:!1,field:a=>a.title,style:"width: 100%"},{name:"postComments",required:!0,label:n("Comments"),align:"left",sortable:!1,field:a=>a.comments_count},{name:"postAuthor",required:!0,label:n("Author"),align:"left",sortable:!1,field:a=>a.user.full_name},{name:"postPublishedAt",required:!0,label:n("Published at"),align:"left",sortable:!1,field:a=>a.published_at}];return(a,y)=>{const p=c("q-td"),f=c("q-table");return u(),x(f,{dense:"","table-header-class":"text-primary bg-blue-1","row-key":d=>d.id,columns:m,rows:r.data,"rows-per-page-options":[0],"hide-pagination":"","hide-bottom":"",flat:"",color:"primary"},{"body-cell-postComments":t(d=>[e(p,{props:d,"auto-width":"",class:"bg-green-1"},{default:t(()=>[l("span",H,o(d.row.comments_count),1)]),_:2},1032,["props"])]),_:1},8,["row-key","rows"])}}}),I={class:"q-mr-sm"},O={key:1,class:"text-grey"},G=q({__name:"RecentUsersTable",props:{data:null},setup(b){const r=b,m=[{name:"userFullName",required:!0,label:n("Full name"),align:"left",sortable:!1,field:a=>a.full_name,style:"width: 100%"},{name:"userRoles",required:!0,label:n("Roles"),align:"left",sortable:!1,field:a=>a.roles},{name:"userCreatedAt",required:!0,label:n("Created at"),align:"left",sortable:!1,field:a=>a.created_at}];return(a,y)=>{const p=c("q-badge"),f=c("q-td"),d=c("q-table");return u(),x(d,{dense:"","table-header-class":"text-primary bg-blue-1","row-key":s=>s.id,columns:m,rows:r.data,"rows-per-page-options":[0],"hide-pagination":"","hide-bottom":"",flat:"",color:"primary"},{"body-cell-userCreatedAt":t(s=>[e(f,{props:s,"auto-width":""},{default:t(()=>[l("span",I,o(s.row.created_at),1),s.row.status==="pending"?(u(),x(p,{key:0,align:"top",class:T([_(P)(new Date(s.row.created_at),new Date)===-1?"text-green":"text-red"]),outline:""},{default:t(()=>[_(P)(new Date(s.row.created_at),new Date)===-1?(u(),h(v,{key:0},[g(" + "+o(_(A)(new Date(s.row.created_at))),1)],64)):(u(),h(v,{key:1},[g(" - "+o(_(A)(new Date(s.row.created_at))),1)],64))]),_:2},1032,["class"])):(u(),h("span",O,[e(p,{outline:"",class:"text-grey"},{default:t(()=>[g(o(a.$t("no date set")),1)]),_:1})]))]),_:2},1032,["props"])]),_:1},8,["row-key","rows"])}}}),J={class:"text-primary text-weight-medium"},K={class:"q-mr-sm"},Q=q({__name:"ActiveUsersTable",props:{data:null},setup(b){const r=b,m=[{name:"userFullName",required:!0,label:n("Full name"),align:"left",sortable:!1,field:a=>a.full_name,style:"width: 100%"},{name:"userPost",required:!0,label:n("Posts"),align:"left",sortable:!1,field:a=>a.posts_count},{name:"userLastLogin",required:!0,label:n("Last Log-in"),align:"left",sortable:!1,field:a=>a.last_login}];return(a,y)=>{const p=c("q-td"),f=c("q-badge"),d=c("q-table");return u(),x(d,{dense:"","table-header-class":"text-primary bg-blue-1","row-key":s=>s.id,columns:m,rows:r.data,"rows-per-page-options":[0],"hide-pagination":"","hide-bottom":"",flat:"",color:"primary"},{"body-cell-userPost":t(s=>[e(p,{props:s,"auto-width":"",class:"bg-green-1"},{default:t(()=>[l("span",J,o(s.row.posts_count),1)]),_:2},1032,["props"])]),"body-cell-userLastLogin":t(s=>[e(p,{props:s,"auto-width":""},{default:t(()=>[l("span",K,o(s.row.last_login),1),e(f,{align:"top",class:T([_(P)(new Date(s.row.last_login),new Date)===-1?"text-green":"text-red"]),outline:""},{default:t(()=>[_(P)(new Date(s.row.last_login),new Date)===-1?(u(),h(v,{key:0},[g(" + "+o(_(A)(new Date(s.row.last_login))),1)],64)):(u(),h(v,{key:1},[g(" - "+o(_(A)(new Date(s.row.last_login))),1)],64))]),_:2},1032,["class"])]),_:2},1032,["props"])]),_:1},8,["row-key","rows"])}}}),W={class:"q-mt-md"},X={class:"row items-top q-col-gutter-md"},Y={class:"col-3"},Z={class:"text-primary"},ee={class:"text-h3 text-center text-primary"},te={class:"col-3"},ae={class:"text-primary"},se={class:"text-h3 text-center text-primary"},le={class:"col-3"},oe={class:"text-primary"},re={class:"text-h3 text-center text-primary"},de={class:"col-3"},ne={class:"text-primary"},ie={class:"text-h3 text-center text-primary"},ce={class:"col-6"},_e={class:"col-6"},ue={class:"col-6"},pe={class:"col-6"},be={class:"row items-top q-col-gutter-md"},me={class:"col-3"},fe={class:"text-primary"},he={class:"text-h3 text-center text-primary"},ge={class:"col-3"},we={class:"text-primary"},ye={class:"text-h3 text-center text-primary"},xe={class:"col-3"},qe={class:"text-primary"},ve={class:"text-h3 text-center text-primary"},De={class:"col-3"},$e={class:"text-primary"},ke={class:"text-h3 text-center text-primary"},Pe={class:"col-6"},Ae={class:"col-6"},Te=q({__name:"DashboardTable",props:{dashboardData:null},setup(b){const r=b,m=k(()=>r.dashboardData.recent_posts),a=k(()=>r.dashboardData.most_rated),y=k(()=>r.dashboardData.most_viewed),p=k(()=>r.dashboardData.most_commented),f=k(()=>r.dashboardData.recent_users),d=k(()=>r.dashboardData.active_authors);return(s,Re)=>{const i=c("q-card-section"),w=c("q-card"),D=c("q-expansion-item"),$=c("q-list"),R=c("q-separator");return u(),h("div",W,[e(w,{flat:"",bordered:""},{default:t(()=>[e(i,{class:"text-primary text-h6"},{default:t(()=>[g(o(s.$t("Post Statistics")),1)]),_:1}),e(i,null,{default:t(()=>[l("div",X,[l("div",Y,[e(w,{bordered:"",flat:""},{default:t(()=>[e(i,{class:"bg-amber-1"},{default:t(()=>[l("div",Z,o(s.$t("Overall posts:")),1)]),_:1}),e(i,null,{default:t(()=>[l("div",ee,o(r.dashboardData.posts_count),1)]),_:1})]),_:1})]),l("div",te,[e(w,{bordered:"",flat:""},{default:t(()=>[e(i,{class:"bg-green-1"},{default:t(()=>[l("div",ae,o(s.$t("Published posts:")),1)]),_:1}),e(i,null,{default:t(()=>[l("div",se,o(r.dashboardData.published_posts_count),1)]),_:1})]),_:1})]),l("div",le,[e(w,{bordered:"",flat:""},{default:t(()=>[e(i,{class:"bg-red-1"},{default:t(()=>[l("div",oe,o(s.$t("Pending posts:")),1)]),_:1}),e(i,null,{default:t(()=>[l("div",re,o(r.dashboardData.pending_posts_count),1)]),_:1})]),_:1})]),l("div",de,[e(w,{bordered:"",flat:"",color:"primary"},{default:t(()=>[e(i,{class:"bg-grey-2"},{default:t(()=>[l("div",ne,o(s.$t("Draft posts:")),1)]),_:1}),e(i,null,{default:t(()=>[l("div",ie,o(r.dashboardData.draft_posts_count),1)]),_:1})]),_:1})]),l("div",ce,[e($,{bordered:"",class:"rounded-borders"},{default:t(()=>[e(D,{"header-class":"text-primary bg-grey-2","expand-separator":"",icon:"schedule",label:s.$t("Recently added posts with pending status")},{default:t(()=>[e(M,{data:_(m)},null,8,["data"])]),_:1},8,["label"])]),_:1})]),l("div",_e,[e($,{bordered:"",class:"rounded-borders"},{default:t(()=>[e(D,{"header-class":"text-primary bg-grey-2","expand-separator":"",icon:"thumb_up_off_alt",label:s.$t("Most rated posts")},{default:t(()=>[e(S,{data:_(a)},null,8,["data"])]),_:1},8,["label"])]),_:1})]),l("div",ue,[e($,{bordered:"",class:"rounded-borders"},{default:t(()=>[e(D,{"header-class":"text-primary bg-grey-2","expand-separator":"",icon:"visibility",label:s.$t("Most viewed posts")},{default:t(()=>[e(E,{data:_(y)},null,8,["data"])]),_:1},8,["label"])]),_:1})]),l("div",pe,[e($,{bordered:"",class:"rounded-borders"},{default:t(()=>[e(D,{"header-class":"text-primary bg-grey-2","expand-separator":"",icon:"chat",label:s.$t("Most commented posts")},{default:t(()=>[e(z,{data:_(p)},null,8,["data"])]),_:1},8,["label"])]),_:1})])])]),_:1}),e(R,{inset:""}),e(i,{class:"text-primary text-h6"},{default:t(()=>[g(o(s.$t("User Statistics")),1)]),_:1}),e(i,null,{default:t(()=>[l("div",be,[l("div",me,[e(w,{bordered:"",flat:""},{default:t(()=>[e(i,{class:"bg-blue-1"},{default:t(()=>[l("div",fe,o(s.$t("Author users:")),1)]),_:1}),e(i,null,{default:t(()=>[l("div",he,o(r.dashboardData.count_author_users),1)]),_:1})]),_:1})]),l("div",ge,[e(w,{bordered:"",flat:""},{default:t(()=>[e(i,{class:"bg-blue-1"},{default:t(()=>[l("div",we,o(s.$t("Member users:")),1)]),_:1}),e(i,null,{default:t(()=>[l("div",ye,o(r.dashboardData.count_member_users),1)]),_:1})]),_:1})]),l("div",xe,[e(w,{bordered:"",flat:""},{default:t(()=>[e(i,{class:"bg-green-1"},{default:t(()=>[l("div",qe,o(s.$t("Enabled users:")),1)]),_:1}),e(i,null,{default:t(()=>[l("div",ve,o(r.dashboardData.enabled_users_count),1)]),_:1})]),_:1})]),l("div",De,[e(w,{bordered:"",flat:""},{default:t(()=>[e(i,{class:"bg-red-1"},{default:t(()=>[l("div",$e,o(s.$t("Pending users:")),1)]),_:1}),e(i,null,{default:t(()=>[l("div",ke,o(r.dashboardData.pending_users_count),1)]),_:1})]),_:1})]),l("div",Pe,[e($,{bordered:"",class:"rounded-borders"},{default:t(()=>[e(D,{"header-class":"text-primary bg-grey-2","expand-separator":"",icon:"person_add",label:s.$t("Recently added users with pending status")},{default:t(()=>[e(G,{data:_(f)},null,8,["data"])]),_:1},8,["label"])]),_:1})]),l("div",Ae,[e($,{bordered:"",class:"rounded-borders"},{default:t(()=>[e(D,{"header-class":"text-primary bg-grey-2","expand-separator":"",icon:"emoji_events",label:s.$t("Most active authors")},{default:t(()=>[e(Q,{data:_(d)},null,8,["data"])]),_:1},8,["label"])]),_:1})])])]),_:1})]),_:1})])}}}),Ve=q({__name:"Index",props:{model:null},setup(b){const r=b,m=L(F);return(a,y)=>(u(),h(v,null,[e(_(N),{title:"Admin dashboard"}),e(C,{data:m.value},null,8,["data"]),e(Te,{dashboardData:r.model},null,8,["dashboardData"])],64))}});export{Ve as default};
