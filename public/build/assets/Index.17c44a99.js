import{d as V,r as w,a as y,b as n,o as q,e as x,g as r,h as a,B as U,f as d,i as o,L as C,j as c}from"./app.ea05a65f.js";import{L as k}from"./RegisterLayout.2baef3a3.js";import{U as h}from"./PaginatedData.7e19470a.js";import"./_plugin-vue_export-helper.cdc0426e.js";const B={class:"row flex-center fit window-height"},L=c(" Register Form "),Y={class:"column q-col-gutter-y-md"},N={class:"col"},R={class:"col"},A={class:"col"},F={class:"col"},S={class:"col"},$={class:"col"},j=c("Cancel"),E=c("Register"),I={layout:k},H=V({...I,__name:"Index",setup(M){const t=w(!0),e=y({first_name:null,last_name:null,email:null,password:null,password_confirmation:null,role:h.Author}),f=()=>e.post("/register");return(P,s)=>{const u=n("q-card-section"),i=n("q-icon"),m=n("q-input"),p=n("q-radio"),_=n("q-btn"),b=n("q-card-actions"),g=n("q-card"),v=n("q-form");return q(),x("div",B,[r(v,{onSubmit:U(f,["prevent"]),style:{width:"30%"}},{default:a(()=>[r(g,{flat:"",bordered:""},{default:a(()=>[r(u,{class:"text-primary text-h6"},{default:a(()=>[L]),_:1}),r(u,null,{default:a(()=>[d("div",Y,[d("div",N,[r(m,{modelValue:o(e).first_name,"onUpdate:modelValue":s[0]||(s[0]=l=>o(e).first_name=l),dense:"",clearable:"","clear-icon":"close",outlined:"",label:"Your first name",type:"text","bottom-slots":"","error-message":o(e).errors.first_name,error:!!o(e).errors.first_name},{prepend:a(()=>[r(i,{name:"person",color:"orange"})]),_:1},8,["modelValue","error-message","error"])]),d("div",R,[r(m,{modelValue:o(e).last_name,"onUpdate:modelValue":s[1]||(s[1]=l=>o(e).last_name=l),dense:"",clearable:"","clear-icon":"close",outlined:"",label:"Your last name",type:"text","bottom-slots":"","error-message":o(e).errors.last_name,error:!!o(e).errors.last_name},{prepend:a(()=>[r(i,{name:"person",color:"orange"})]),_:1},8,["modelValue","error-message","error"])]),d("div",A,[r(m,{modelValue:o(e).email,"onUpdate:modelValue":s[2]||(s[2]=l=>o(e).email=l),dense:"",clearable:"","clear-icon":"close",outlined:"",label:"Your email",type:"email","bottom-slots":"","error-message":o(e).errors.email,error:!!o(e).errors.email},{prepend:a(()=>[r(i,{name:"email",color:"orange"})]),_:1},8,["modelValue","error-message","error"])]),d("div",F,[r(m,{modelValue:o(e).password,"onUpdate:modelValue":s[4]||(s[4]=l=>o(e).password=l),dense:"",clearable:"","clear-icon":"close",outlined:"",label:"Your password",type:t.value?"password":"text","bottom-slots":"","error-message":o(e).errors.password,error:!!o(e).errors.password},{prepend:a(()=>[r(i,{name:"lock",color:"orange"})]),append:a(()=>[r(i,{name:t.value?"visibility_off":"visibility",class:"cursor-pointer",onClick:s[3]||(s[3]=l=>t.value=!t.value)},null,8,["name"])]),_:1},8,["modelValue","type","error-message","error"])]),d("div",S,[r(m,{modelValue:o(e).password_confirmation,"onUpdate:modelValue":s[6]||(s[6]=l=>o(e).password_confirmation=l),dense:"",clearable:"","clear-icon":"close",outlined:"",label:"Confirm your password",type:t.value?"password":"text","bottom-slots":"","error-message":o(e).errors.password_confirmation,error:!!o(e).errors.password_confirmation},{prepend:a(()=>[r(i,{name:"lock",color:"orange"})]),append:a(()=>[r(i,{name:t.value?"visibility_off":"visibility",class:"cursor-pointer",onClick:s[5]||(s[5]=l=>t.value=!t.value)},null,8,["name"])]),_:1},8,["modelValue","type","error-message","error"])]),d("div",$,[r(p,{modelValue:o(e).role,"onUpdate:modelValue":s[7]||(s[7]=l=>o(e).role=l),val:"author",label:"Author"},null,8,["modelValue"]),r(p,{modelValue:o(e).role,"onUpdate:modelValue":s[8]||(s[8]=l=>o(e).role=l),val:"member",label:"Commentor"},null,8,["modelValue"])])])]),_:1}),r(b,{align:"right",class:"q-gutter-sm"},{default:a(()=>[r(o(C),{as:"div",href:"/"},{default:a(()=>[r(_,{flat:"",color:"secondary",disable:o(e).processing},{default:a(()=>[j]),_:1},8,["disable"])]),_:1}),r(_,{flat:"",color:"primary",type:"submit",loading:o(e).processing},{default:a(()=>[E]),_:1},8,["loading"])]),_:1})]),_:1})]),_:1},8,["onSubmit"])])}}});export{H as default};
