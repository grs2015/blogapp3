import{d as c,r as d,o as i,e as p,g as a,i as o,H as u,F as _,E as f,p as B}from"./app.ea05a65f.js";import{h as b,i as g,_ as C}from"./breadcrumbsData.5c06e62c.js";import{_ as E}from"./PostForm.vue_vue_type_script_setup_true_lang.b359223a.js";import"./PaginatedData.7e19470a.js";import"./index.18f6e9ea.js";const v=c({__name:"Form",props:{model:null},setup(r){const t=r,n=d(t.model.post?g:b),m=t.model.post?"Edit":"Create",l=async({id:e,status:s})=>{await f(),B.Inertia.post("/admin/comments/status",{id:e,status:s},{preserveScroll:!0})};return(e,s)=>(i(),p(_,null,[a(o(u),{title:`Blog Post - ${o(m)}`},null,8,["title"]),a(C,{data:n.value},null,8,["data"]),a(E,{data:t.model,onStatus:l},null,8,["data"])],64))}});export{v as default};
