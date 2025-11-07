import{b as ut,r as l,g as I,h as xt,c,o as u,d as s,j as T,e as m,k as y,v as g,l as a,x as X,F as L,m as D,t as p,s as Z,z as bt,B as mt,w as tt,y as j}from"./index-Blf2-IqN.js";import{f as E,t as o}from"./numbers-BTmV-EI5.js";import{c as F}from"./createLucideIcon-CMb9MUBf.js";import{P as yt}from"./package-e0dhqh2S.js";import{P as vt}from"./plus-k6-JmU9o.js";import{T as gt}from"./trash-2-5RAfPKRo.js";import{U as ft}from"./user-plus-7Gtxhp1D.js";/**
 * @license lucide-vue-next v0.294.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const ht=F("MinusIcon",[["path",{d:"M5 12h14",key:"1ays0h"}]]);/**
 * @license lucide-vue-next v0.294.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const wt=F("ScanIcon",[["path",{d:"M3 7V5a2 2 0 0 1 2-2h2",key:"aa7l1z"}],["path",{d:"M17 3h2a2 2 0 0 1 2 2v2",key:"4qcy5o"}],["path",{d:"M21 17v2a2 2 0 0 1-2 2h-2",key:"6vwrx8"}],["path",{d:"M7 21H5a2 2 0 0 1-2-2v-2",key:"ioqczr"}]]);/**
 * @license lucide-vue-next v0.294.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const _t=F("SearchIcon",[["circle",{cx:"11",cy:"11",r:"8",key:"4ej97u"}],["path",{d:"m21 21-4.3-4.3",key:"1qie3q"}]]);/**
 * @license lucide-vue-next v0.294.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const kt=F("ShoppingCartIcon",[["circle",{cx:"8",cy:"21",r:"1",key:"jimo8o"}],["circle",{cx:"19",cy:"21",r:"1",key:"13723u"}],["path",{d:"M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12",key:"9zh506"}]]),jt={class:"grid grid-cols-1 lg:grid-cols-3 gap-6"},$t={class:"lg:col-span-2 space-y-4"},zt={class:"card"},Ct={class:"flex gap-3"},Pt={class:"flex-1 relative"},qt={class:"grid grid-cols-2 md:grid-cols-3 gap-4"},St=["onClick"],Gt={class:"aspect-square bg-gray-100 rounded-lg mb-3 flex items-center justify-center"},Et={class:"font-bold text-gray-900 mb-1 truncate"},Mt={class:"text-sm text-gray-500 mb-2"},Vt={class:"flex items-center justify-between"},Bt={class:"text-lg font-bold text-primary-600"},Ut={class:"space-y-4"},It={class:"card sticky top-24"},Tt={class:"text-xl font-bold mb-4 flex items-center gap-2"},Lt={class:"space-y-3 mb-4 max-h-64 overflow-y-auto"},Dt={key:0,class:"text-center py-8 text-gray-500"},Ft={class:"flex-1"},At={class:"font-medium text-sm"},Nt={class:"text-xs text-gray-500"},Ht={class:"flex items-center gap-2"},Qt=["onClick"],Kt={class:"w-8 text-center font-bold"},Ot=["onClick"],Wt=["onClick"],Rt={class:"mb-4"},Yt={class:"flex gap-2"},Jt=["value"],Xt={class:"border-t pt-4 space-y-2"},Zt={class:"flex justify-between text-sm"},te={class:"font-bold"},ee={class:"flex justify-between text-sm"},se={class:"flex justify-between text-sm"},ne={class:"flex justify-between text-lg font-bold border-t pt-2"},oe={class:"text-primary-600"},ae={class:"flex justify-between text-sm bg-blue-50 p-2 rounded"},ie=["placeholder"],le={key:0,class:"flex justify-between text-sm text-red-600 font-bold"},re={class:"mt-4"},de={class:"grid grid-cols-3 gap-2"},pe=["onClick"],ce={class:"mt-4 space-y-2"},ue=["disabled"],xe={key:0,class:"fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"},be={class:"bg-white rounded-xl p-6 w-full max-w-md"},me={class:"flex gap-3"},ye={class:"bg-white rounded-xl p-6 w-full max-w-md"},ve={class:"flex gap-3"},ze={__name:"POS",setup(ge){const x=ut(),$=l([]),z=l([]),r=l([]),v=l(""),C=l(""),f=l(0),h=l(0),P=l(null),A=l("cash"),M=l(!1),q=l(!1),w=l(""),H=l(null),_=l({name:"",phone:""}),Q=[{value:"cash",label:"نقدي"},{value:"card",label:"بطاقة"},{value:"transfer",label:"تحويل"}],et=I(()=>v.value?$.value.filter(t=>t.name_ar.includes(v.value)||t.barcode.includes(v.value)):$.value.slice(0,12)),K=I(()=>r.value.reduce((t,e)=>t+e.product.selling_price*e.quantity,0)),S=I(()=>K.value+h.value-f.value),O=I(()=>{const t=P.value||S.value;return Math.max(0,S.value-t)}),st=async()=>{if(v.value.length<2){N();return}try{const t=await j.getProducts({search:v.value});$.value=t.data}catch{x.error("فشل البحث")}},nt=()=>{M.value=!0,w.value="",setTimeout(()=>{var t;(t=H.value)==null||t.focus()},100)},W=async()=>{if(w.value)try{const t=await j.searchByBarcode(w.value);R(t.data),M.value=!1,w.value=""}catch{x.error("المنتج غير موجود")}},R=t=>{if(t.stock_quantity===0){x.error("المنتج غير متوفر في المخزون");return}const e=r.value.find(n=>n.product.id===t.id);if(e){if(e.quantity>=t.stock_quantity){x.error("الكمية المطلوبة غير متوفرة");return}e.quantity++}else r.value.push({product:t,quantity:1});x.success("تم إضافة المنتج للسلة")},ot=t=>{if(t.quantity>=t.product.stock_quantity){x.error("الكمية المطلوبة غير متوفرة");return}t.quantity++},at=t=>{t.quantity>1?t.quantity--:Y(t)},Y=t=>{r.value=r.value.filter(e=>e.product.id!==t.product.id)},it=()=>{r.value.length!==0&&confirm("هل أنت متأكد من مسح السلة؟")&&(r.value=[],f.value=0,h.value=0)},lt=async()=>{try{const t=await j.createCustomer(_.value);x.success("تم إضافة العميل بنجاح"),z.value.push(t.data),C.value=t.data.id,q.value=!1,_.value={name:"",phone:""}}catch{x.error("فشل إضافة العميل")}},rt=async()=>{var e,n,d;if(r.value.length===0)return;const t={customer_id:C.value||null,items:r.value.map(i=>({product_id:i.product.id,quantity:i.quantity,unit_price:i.product.selling_price})),tax:h.value,discount:f.value,paid_amount:P.value||S.value,payment_method:A.value};try{const i=await j.createSale(t),V=o(i.data.invoice_number);x.success(`تم إنشاء الفاتورة ${V}`);const B=[];confirm("هل تريد طباعة الفاتورة؟")&&dt(i.data),(e=i.data.customer)!=null&&e.phone&&confirm("هل تريد مشاركة الفاتورة عبر واتساب؟")&&pt(i.data),r.value=[],f.value=0,h.value=0,P.value=null,C.value="",N()}catch(i){x.error(((d=(n=i.response)==null?void 0:n.data)==null?void 0:d.error)||"فشل إنشاء الفاتورة")}},dt=t=>{var b,G;const e=window.open("","_blank"),n=((b=t.items)==null?void 0:b.map(k=>{var J;return`<tr style="border: 1px solid #cbd5e1;">
      <td style="border: 1px solid #cbd5e1; padding: 8px; text-align: right; font-size: 11px;">${((J=k.product)==null?void 0:J.name_ar)||""}</td>
      <td style="border: 1px solid #cbd5e1; padding: 8px; text-align: center; font-size: 11px; width: 60px;">${o(k.quantity)}</td>
      <td style="border: 1px solid #cbd5e1; padding: 8px; text-align: right; font-size: 11px; width: 90px;">${o(k.unit_price)} EGP</td>
      <td style="border: 1px solid #cbd5e1; padding: 8px; text-align: right; font-size: 11px; width: 90px; font-weight: bold;">${o(k.total_price)} EGP</td>
    </tr>`}).join(""))||"",d=t.customer?`
    <p style="margin: 3px 0; font-size: 12px;"><strong>العميل:</strong> ${t.customer.name}</p>
    <p style="margin: 3px 0; font-size: 12px;"><strong>الهاتف:</strong> ${o(t.customer.phone)}</p>
  `:'<p style="margin: 3px 0; font-size: 12px;"><strong>العميل:</strong> عميل عادي</p>',i=((G=Q.find(k=>k.value===t.payment_method))==null?void 0:G.label)||t.payment_method,V=t.payment_status==="paid"?"مدفوع بالكامل":t.payment_status==="partial"?"مدفوع جزئياً":"غير مدفوع",B=t.payment_status==="paid"?"#10b981":t.payment_status==="partial"?"#f59e0b":"#ef4444",U=`<!DOCTYPE html>
  <html dir="rtl">
    <head>
      <meta charset="UTF-8">
      <title>فاتورة ${o(t.invoice_number)}</title>
      <style>
        @page { 
          size: A4; 
          margin: 15mm 20mm; 
        }
        * { 
          margin: 0; 
          padding: 0; 
          box-sizing: border-box; 
          -webkit-print-color-adjust: exact !important;
          print-color-adjust: exact !important;
          color-adjust: exact !important;
        }
        body { 
          font-family: 'Arial', sans-serif; 
          padding: 20px;
          font-size: 13px;
          line-height: 1.5;
          width: 210mm;
          height: 297mm;
          position: relative;
        }
        @media print {
          body { 
            padding: 0;
            margin: 0;
          }
          .no-print { display: none !important; }
          * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
          }
        }
      </style>
    </head>
    <body>
      <div style="text-align: center; margin-bottom: 20px; border-bottom: 3px solid #3b82f6; padding-bottom: 15px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
        <h1 style="font-size: 32px; color: #1e40af; margin-bottom: 5px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">متجر العطور</h1>
        <h2 style="font-size: 18px; color: #64748b; -webkit-print-color-adjust: exact; print-color-adjust: exact;">فاتورة بيع</h2>
      </div>
      
      <div style="display: flex; justify-content: space-between; margin-bottom: 20px; background: #f8fafc; padding: 15px; border-radius: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
        <div style="flex: 1;">
          <p style="margin: 5px 0; font-size: 13px;"><strong>رقم الفاتورة:</strong> ${o(t.invoice_number)}</p>
          <p style="margin: 5px 0; font-size: 13px;"><strong>التاريخ:</strong> ${o(new Date(t.created_at).toLocaleDateString("en-GB"))}</p>
          <p style="margin: 5px 0; font-size: 13px;"><strong>الوقت:</strong> ${o(new Date(t.created_at).toLocaleTimeString("en-GB",{hour:"2-digit",minute:"2-digit"}))}</p>
        </div>
        <div style="flex: 1; text-align: right;">
          ${d.replace(/font-size: 12px/g,"font-size: 13px")}
        </div>
      </div>
      
      <table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 12px;">
        <thead>
          <tr>
            <th style="border: 1px solid #cbd5e1; padding: 10px; text-align: right; background-color: #3b82f6; color: white; font-weight: bold; font-size: 13px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">المنتج</th>
            <th style="border: 1px solid #cbd5e1; padding: 10px; text-align: center; background-color: #3b82f6; color: white; font-weight: bold; font-size: 13px; width: 70px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">الكمية</th>
            <th style="border: 1px solid #cbd5e1; padding: 10px; text-align: right; background-color: #3b82f6; color: white; font-weight: bold; font-size: 13px; width: 100px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">السعر</th>
            <th style="border: 1px solid #cbd5e1; padding: 10px; text-align: right; background-color: #3b82f6; color: white; font-weight: bold; font-size: 13px; width: 100px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">الإجمالي</th>
          </tr>
        </thead>
        <tbody>${n.replace(/font-size: 11px/g,"font-size: 12px").replace(/padding: 8px/g,"padding: 10px").replace(/width: 60px/g,"width: 70px").replace(/width: 90px/g,"width: 100px")}</tbody>
      </table>
      
      <div style="margin-top: 30px; float: left; width: 50%;">
        <div style="display: flex; justify-content: space-between; padding: 8px 15px; border-bottom: 1px solid #e2e8f0; font-size: 12px;">
          <span>المجموع الفرعي:</span><span>${o(t.subtotal)} EGP</span>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 8px 15px; border-bottom: 1px solid #e2e8f0; font-size: 12px;">
          <span>الضريبة:</span><span>${o(t.tax)} EGP</span>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 8px 15px; border-bottom: 1px solid #e2e8f0; font-size: 12px;">
          <span>الخصم:</span><span>${o(t.discount)} EGP</span>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 10px 15px; font-size: 16px; font-weight: bold; background: #3b82f6; color: white; margin-top: 10px; border-radius: 6px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
          <span>الإجمالي:</span><span>${o(t.total)} EGP</span>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 8px 15px; background: #10b981; color: white; margin-top: 8px; border-radius: 6px; font-size: 12px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
          <span>المبلغ المدفوع:</span><span>${o(t.paid_amount||t.total)} EGP</span>
        </div>
        ${t.remaining_amount>0?`<div style="display: flex; justify-content: space-between; padding: 8px 15px; background: #ef4444; color: white; margin-top: 8px; border-radius: 6px; font-size: 12px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
          <span>المتبقي:</span><span>${o(t.remaining_amount)} EGP</span>
        </div>`:""}
        <div style="margin-top: 15px; padding: 10px; background: #f8fafc; border-radius: 6px; font-size: 12px; display: flex; justify-content: space-between; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
          <span>طريقة الدفع:</span><span style="font-weight: bold;">${i}</span>
        </div>
        <div style="margin-top: 10px; text-align: center;">
          <span style="display: inline-block; padding: 6px 20px; border-radius: 20px; color: white; font-weight: bold; background: ${B}; font-size: 12px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">${V}</span>
        </div>
      </div>
      
      <div style="clear: both; margin-top: 50px; text-align: center; padding-top: 20px; border-top: 2px solid #e2e8f0; color: #64748b; font-size: 12px;">
        <p style="margin: 5px 0;">شكراً لتعاملكم معنا</p>
        <p style="margin: 5px 0;">متجر العطور - جميع الحقوق محفوظة</p>
      </div>
    </body>
  </html>`;e.document.write(U),e.document.close(),setTimeout(()=>e.print(),250)},pt=t=>{var U;const e=z.value.find(b=>b.id===t.customer_id);if(!(e!=null&&e.phone))return;const n=((U=t.items)==null?void 0:U.map(b=>{var G;return`• ${(G=b.product)==null?void 0:G.name_ar}: ${o(b.quantity)} × ${o(b.unit_price)} = ${o(b.total_price)} EGP`}).join("\\n"))||"",d=t.payment_status==="paid"?"مدفوع بالكامل ✅":t.payment_status==="partial"?"مدفوع جزئياً ⚠️":"غير مدفوع ❌",i=`🧴 *فاتورة من متجر العطور*\\n\\n📋 رقم الفاتورة: ${o(t.invoice_number)}\\n📅 التاريخ: ${o(new Date(t.created_at).toLocaleDateString("en-GB"))}\\n\\n*المنتجات:*\\n${n}\\n\\n💰 المجموع الفرعي: ${o(t.subtotal)} EGP\\n🏷️ الخصم: ${o(t.discount)} EGP\\n📊 الضريبة: ${o(t.tax)} EGP\\n*💵 الإجمالي: ${o(t.total)} EGP*\\n\\n✅ المدفوع: ${o(t.paid_amount||t.total)} EGP\\n${t.remaining_amount>0?`⚠️ المتبقي: ${o(t.remaining_amount)} EGP\\n`:""}\\n📌 حالة الدفع: ${d}\\n\\nشكراً لتعاملكم معنا 🌹`,B=`https://wa.me/${e.phone.replace(/[^0-9]/g,"")}?text=${encodeURIComponent(i)}`;window.open(B,"_blank")},N=async()=>{try{const t=await j.getProducts();$.value=t.data.data||t.data||[]}catch(t){console.error("Failed to load products:",t),$.value=[]}},ct=async()=>{try{const t=await j.getCustomers();z.value=t.data.data||t.data||[]}catch(t){console.error("Failed to load customers:",t),z.value=[]}};return xt(()=>{N(),ct()}),(t,e)=>(u(),c("div",jt,[s("div",$t,[s("div",zt,[s("div",Ct,[s("div",Pt,[m(s("input",{"onUpdate:modelValue":e[0]||(e[0]=n=>v.value=n),onInput:st,type:"text",placeholder:"ابحث بالاسم أو الباركود...",class:"input pl-10"},null,544),[[g,v.value]]),y(a(_t),{class:"absolute left-3 top-3 text-gray-400",size:20})]),s("button",{onClick:nt,class:"btn btn-primary flex items-center gap-2"},[y(a(wt),{size:20}),e[12]||(e[12]=X(" مسح الباركود ",-1))])])]),s("div",qt,[(u(!0),c(L,null,D(et.value,n=>{var d;return u(),c("div",{key:n.id,onClick:i=>R(n),class:"card hover:shadow-lg cursor-pointer transition-all hover:scale-105"},[s("div",Gt,[y(a(yt),{size:48,class:"text-gray-400"})]),s("h4",Et,p(n.name_ar),1),s("p",Mt,p((d=n.brand)==null?void 0:d.name_ar),1),s("div",Vt,[s("span",Bt,p(a(E)(n.selling_price)),1),s("span",{class:Z(["text-xs badge",n.stock_quantity>0?"badge-success":"badge-danger"])},p(a(o)(n.stock_quantity))+" قطعة ",3)])],8,St)}),128))])]),s("div",Ut,[s("div",It,[s("h3",Tt,[y(a(kt),{size:24}),e[13]||(e[13]=X(" سلة المشتريات ",-1))]),s("div",Lt,[r.value.length===0?(u(),c("div",Dt," السلة فارغة ")):T("",!0),(u(!0),c(L,null,D(r.value,n=>(u(),c("div",{key:n.product.id,class:"flex items-center gap-3 p-3 bg-gray-50 rounded-lg"},[s("div",Ft,[s("p",At,p(n.product.name_ar),1),s("p",Nt,p(a(E)(n.product.selling_price)),1)]),s("div",Ht,[s("button",{onClick:d=>at(n),class:"w-7 h-7 rounded bg-gray-200 hover:bg-gray-300"},[y(a(ht),{size:16,class:"mx-auto"})],8,Qt),s("span",Kt,p(a(o)(n.quantity)),1),s("button",{onClick:d=>ot(n),class:"w-7 h-7 rounded bg-gray-200 hover:bg-gray-300"},[y(a(vt),{size:16,class:"mx-auto"})],8,Ot)]),s("button",{onClick:d=>Y(n),class:"text-red-600 hover:text-red-800"},[y(a(gt),{size:18})],8,Wt)]))),128))]),s("div",Rt,[e[15]||(e[15]=s("label",{class:"block text-sm font-medium mb-2"},"العميل (اختياري)",-1)),s("div",Yt,[m(s("select",{"onUpdate:modelValue":e[1]||(e[1]=n=>C.value=n),class:"input flex-1"},[e[14]||(e[14]=s("option",{value:""},"عميل عادي",-1)),(u(!0),c(L,null,D(z.value,n=>(u(),c("option",{key:n.id,value:n.id},p(n.name)+" - "+p(a(o)(n.phone)),9,Jt))),128))],512),[[bt,C.value]]),s("button",{onClick:e[2]||(e[2]=n=>q.value=!0),class:"btn btn-secondary"},[y(a(ft),{size:18})])])]),s("div",Xt,[s("div",Zt,[e[16]||(e[16]=s("span",null,"المجموع الفرعي:",-1)),s("span",te,p(a(E)(K.value)),1)]),s("div",ee,[e[17]||(e[17]=s("span",null,"الخصم:",-1)),m(s("input",{"onUpdate:modelValue":e[3]||(e[3]=n=>f.value=n),type:"number",step:"0.01",class:"input w-32 text-left py-1"},null,512),[[g,f.value,void 0,{number:!0}]])]),s("div",se,[e[18]||(e[18]=s("span",null,"الضريبة:",-1)),m(s("input",{"onUpdate:modelValue":e[4]||(e[4]=n=>h.value=n),type:"number",step:"0.01",class:"input w-32 text-left py-1"},null,512),[[g,h.value,void 0,{number:!0}]])]),s("div",ne,[e[19]||(e[19]=s("span",null,"الإجمالي:",-1)),s("span",oe,p(a(E)(S.value)),1)]),s("div",ae,[e[20]||(e[20]=s("span",null,"المبلغ المدفوع:",-1)),m(s("input",{"onUpdate:modelValue":e[5]||(e[5]=n=>P.value=n),type:"number",step:"0.01",placeholder:S.value.toString(),class:"input w-32 text-left py-1"},null,8,ie),[[g,P.value,void 0,{number:!0}]])]),O.value>0?(u(),c("div",le,[e[21]||(e[21]=s("span",null,"المتبقي:",-1)),s("span",null,p(a(E)(O.value)),1)])):T("",!0)]),s("div",re,[e[22]||(e[22]=s("label",{class:"block text-sm font-medium mb-2"},"طريقة الدفع",-1)),s("div",de,[(u(),c(L,null,D(Q,n=>s("button",{key:n.value,onClick:d=>A.value=n.value,class:Z([A.value===n.value?"bg-primary-600 text-white":"bg-gray-100","py-2 rounded-lg font-medium transition-all"])},p(n.label),11,pe)),64))])]),s("div",ce,[s("button",{onClick:rt,disabled:r.value.length===0,class:"btn btn-success w-full py-3 text-lg"}," إتمام البيع ",8,ue),s("button",{onClick:it,class:"btn btn-secondary w-full"}," مسح السلة ")])])]),M.value?(u(),c("div",xe,[s("div",be,[e[23]||(e[23]=s("h3",{class:"text-2xl font-bold mb-4"},"مسح الباركود",-1)),m(s("input",{ref_key:"barcodeInput",ref:H,"onUpdate:modelValue":e[6]||(e[6]=n=>w.value=n),onKeyup:mt(W,["enter"]),type:"text",placeholder:"امسح أو اكتب الباركود...",class:"input mb-4",autofocus:""},null,544),[[g,w.value]]),s("div",me,[s("button",{onClick:e[7]||(e[7]=n=>M.value=!1),class:"btn btn-secondary flex-1"},"إلغاء"),s("button",{onClick:W,class:"btn btn-primary flex-1"},"بحث")])])])):T("",!0),q.value?(u(),c("div",{key:1,class:"fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50",onClick:e[11]||(e[11]=tt(n=>q.value=!1,["self"]))},[s("div",ye,[e[27]||(e[27]=s("h3",{class:"text-2xl font-bold mb-4"},"إضافة عميل",-1)),s("form",{onSubmit:tt(lt,["prevent"]),class:"space-y-4"},[s("div",null,[e[24]||(e[24]=s("label",{class:"block text-sm font-medium mb-2"},"الاسم *",-1)),m(s("input",{"onUpdate:modelValue":e[8]||(e[8]=n=>_.value.name=n),type:"text",required:"",class:"input"},null,512),[[g,_.value.name]])]),s("div",null,[e[25]||(e[25]=s("label",{class:"block text-sm font-medium mb-2"},"رقم الهاتف *",-1)),m(s("input",{"onUpdate:modelValue":e[9]||(e[9]=n=>_.value.phone=n),type:"tel",required:"",class:"input"},null,512),[[g,_.value.phone]])]),s("div",ve,[s("button",{type:"button",onClick:e[10]||(e[10]=n=>q.value=!1),class:"btn btn-secondary flex-1"},"إلغاء"),e[26]||(e[26]=s("button",{type:"submit",class:"btn btn-primary flex-1"},"حفظ",-1))])],32)])])):T("",!0)]))}};export{ze as default};
