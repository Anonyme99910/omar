import{b as pt,r as i,g as I,h as vt,c as p,o as v,d as s,j as T,e as b,k as y,v as g,l as o,x as X,F as L,m as D,t as u,s as Z,z as mt,B as bt,w as tt,y as $}from"./index-DZQKp4Su.js";import{f as G,t as a}from"./numbers-BTmV-EI5.js";import{c as A}from"./createLucideIcon-CAVzs_lV.js";import{P as yt}from"./package-ao9RlOSI.js";import{P as ft}from"./plus-CVUSmiiV.js";import{T as gt}from"./trash-2-DNqBEKv2.js";import{U as xt}from"./user-plus-CW9_hvCj.js";/**
 * @license lucide-vue-next v0.294.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const ht=A("MinusIcon",[["path",{d:"M5 12h14",key:"1ays0h"}]]);/**
 * @license lucide-vue-next v0.294.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const _t=A("ScanIcon",[["path",{d:"M3 7V5a2 2 0 0 1 2-2h2",key:"aa7l1z"}],["path",{d:"M17 3h2a2 2 0 0 1 2 2v2",key:"4qcy5o"}],["path",{d:"M21 17v2a2 2 0 0 1-2 2h-2",key:"6vwrx8"}],["path",{d:"M7 21H5a2 2 0 0 1-2-2v-2",key:"ioqczr"}]]);/**
 * @license lucide-vue-next v0.294.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const wt=A("SearchIcon",[["circle",{cx:"11",cy:"11",r:"8",key:"4ej97u"}],["path",{d:"m21 21-4.3-4.3",key:"1qie3q"}]]);/**
 * @license lucide-vue-next v0.294.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const kt=A("ShoppingCartIcon",[["circle",{cx:"8",cy:"21",r:"1",key:"jimo8o"}],["circle",{cx:"19",cy:"21",r:"1",key:"13723u"}],["path",{d:"M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12",key:"9zh506"}]]),$t={class:"grid grid-cols-1 lg:grid-cols-3 gap-6"},Ct={class:"lg:col-span-2 space-y-4"},Pt={class:"card"},qt={class:"flex gap-3"},zt={class:"flex-1 relative"},St={class:"grid grid-cols-2 md:grid-cols-3 gap-4"},Gt=["onClick"],jt={class:"aspect-square bg-gray-100 rounded-lg mb-3 flex items-center justify-center"},Et={class:"font-bold text-gray-900 mb-1 truncate"},Mt={class:"text-sm text-gray-500 mb-2"},Vt={class:"flex items-center justify-between"},Bt={class:"text-lg font-bold text-primary-600"},Ut={class:"space-y-4"},It={class:"card sticky top-24"},Tt={class:"text-xl font-bold mb-4 flex items-center gap-2"},Lt={class:"space-y-3 mb-4 max-h-64 overflow-y-auto"},Dt={key:0,class:"text-center py-8 text-gray-500"},At={class:"flex-1"},Nt={class:"font-medium text-sm"},Ft={class:"text-xs text-gray-500"},Ht={class:"flex items-center gap-2"},Qt=["onClick"],Kt={class:"w-8 text-center font-bold"},Ot=["onClick"],Wt=["onClick"],Rt={class:"mb-4"},Yt={class:"flex gap-2"},Jt=["value"],Xt={class:"border-t pt-4 space-y-2"},Zt={class:"flex justify-between text-sm"},te={class:"font-bold"},ee={class:"flex justify-between text-sm"},se={class:"flex justify-between text-sm"},ne={class:"flex justify-between text-lg font-bold border-t pt-2"},ae={class:"text-primary-600"},oe={class:"flex justify-between text-sm bg-blue-50 p-2 rounded"},le=["placeholder"],ie={key:0,class:"flex justify-between text-sm text-red-600 font-bold"},re={class:"mt-4"},de={class:"grid grid-cols-3 gap-2"},ue=["onClick"],ce={class:"mt-4 space-y-2"},pe=["disabled"],ve={key:0,class:"fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"},me={class:"bg-white rounded-xl p-6 w-full max-w-md"},be={class:"flex gap-3"},ye={class:"bg-white rounded-xl p-6 w-full max-w-md"},fe={class:"flex gap-3"},Pe={__name:"POS",setup(ge){const c=pt(),j=i([]),E=i([]),r=i([]),f=i(""),C=i(""),x=i(0),h=i(0),P=i(null),N=i("cash"),M=i(!1),q=i(!1),_=i(""),H=i(null),w=i({name:"",phone:""}),Q=[{value:"cash",label:"نقدي"},{value:"card",label:"بطاقة"},{value:"transfer",label:"تحويل"}],et=I(()=>f.value?j.value.filter(t=>t.name_ar.includes(f.value)||t.barcode.includes(f.value)):j.value.slice(0,12)),K=I(()=>r.value.reduce((t,e)=>t+e.product.selling_price*e.quantity,0)),z=I(()=>K.value+h.value-x.value),O=I(()=>{const t=P.value||z.value;return Math.max(0,z.value-t)}),st=async()=>{if(f.value.length<2){F();return}try{const t=await $.getProducts({search:f.value});j.value=t.data}catch{c.error("فشل البحث")}},nt=()=>{M.value=!0,_.value="",setTimeout(()=>{var t;(t=H.value)==null||t.focus()},100)},W=async()=>{if(_.value)try{const t=await $.searchByBarcode(_.value);R(t.data),M.value=!1,_.value=""}catch{c.error("المنتج غير موجود")}},R=t=>{if(t.stock_quantity===0){c.error("المنتج غير متوفر في المخزون");return}const e=r.value.find(n=>n.product.id===t.id);if(e){if(e.quantity>=t.stock_quantity){c.error("الكمية المطلوبة غير متوفرة");return}e.quantity++}else r.value.push({product:t,quantity:1});c.success("تم إضافة المنتج للسلة")},at=t=>{if(t.quantity>=t.product.stock_quantity){c.error("الكمية المطلوبة غير متوفرة");return}t.quantity++},ot=t=>{t.quantity>1?t.quantity--:Y(t)},Y=t=>{r.value=r.value.filter(e=>e.product.id!==t.product.id)},lt=()=>{r.value.length!==0&&confirm("هل أنت متأكد من مسح السلة؟")&&(r.value=[],x.value=0,h.value=0)},it=async()=>{try{const t=await $.createCustomer(w.value);c.success("تم إضافة العميل بنجاح"),E.value.push(t.data),C.value=t.data.id,q.value=!1,w.value={name:"",phone:""}}catch{c.error("فشل إضافة العميل")}},rt=async()=>{var e,n,d;if(r.value.length===0)return;const t={customer_id:C.value||null,items:r.value.map(l=>({product_id:l.product.id,quantity:l.quantity,unit_price:l.product.selling_price})),tax:h.value,discount:x.value,paid_amount:P.value||z.value,payment_method:N.value};try{const l=await $.createSale(t),V=a(l.data.invoice_number);c.success(`تم إنشاء الفاتورة ${V}`);const B=[];confirm("هل تريد طباعة الفاتورة؟")&&dt(l.data),(e=l.data.customer)!=null&&e.phone&&confirm("هل تريد مشاركة الفاتورة عبر واتساب؟")&&ut(l.data),r.value=[],x.value=0,h.value=0,P.value=null,C.value="",F()}catch(l){c.error(((d=(n=l.response)==null?void 0:n.data)==null?void 0:d.error)||"فشل إنشاء الفاتورة")}},dt=t=>{var m,S;const e=window.open("","_blank"),n=((m=t.items)==null?void 0:m.map(k=>{var J;return`<tr>
      <td>${((J=k.product)==null?void 0:J.name_ar)||""}</td>
      <td>${a(k.quantity)}</td>
      <td>${a(k.unit_price)} EGP</td>
      <td>${a(k.total_price)} EGP</td>
    </tr>`}).join(""))||"",d=t.customer?`
    <p><strong>العميل:</strong> ${t.customer.name}</p>
    <p><strong>الهاتف:</strong> ${a(t.customer.phone)}</p>
  `:"<p><strong>العميل:</strong> عميل عادي</p>",l=((S=Q.find(k=>k.value===t.payment_method))==null?void 0:S.label)||t.payment_method,V=t.payment_status==="paid"?"مدفوع بالكامل":t.payment_status==="partial"?"مدفوع جزئياً":"غير مدفوع",B=t.payment_status==="paid"?"#10b981":t.payment_status==="partial"?"#f59e0b":"#ef4444",U=`<!DOCTYPE html>
  <html dir="rtl">
    <head>
      <meta charset="UTF-8">
      <title>فاتورة ${a(t.invoice_number)}</title>
      <style>
        @page { size: A4; margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
          font-family: 'Arial', sans-serif; 
          padding: 20px;
          font-size: 14px;
          line-height: 1.6;
        }
        .header { 
          text-align: center; 
          margin-bottom: 30px;
          border-bottom: 3px solid #3b82f6;
          padding-bottom: 20px;
        }
        .header h1 { 
          font-size: 32px; 
          color: #1e40af;
          margin-bottom: 5px;
        }
        .header h2 { 
          font-size: 20px; 
          color: #64748b;
        }
        .invoice-info { 
          display: flex;
          justify-content: space-between;
          margin-bottom: 30px;
          background: #f8fafc;
          padding: 15px;
          border-radius: 8px;
        }
        .invoice-info div { flex: 1; }
        .invoice-info p { margin: 5px 0; }
        .invoice-info strong { color: #1e293b; }
        table { 
          width: 100%; 
          border-collapse: collapse;
          margin: 20px 0;
        }
        th, td { 
          border: 1px solid #cbd5e1; 
          padding: 12px; 
          text-align: right; 
        }
        th { 
          background-color: #3b82f6; 
          color: white;
          font-weight: bold;
        }
        tbody tr:nth-child(even) { background-color: #f8fafc; }
        .totals { 
          margin-top: 30px;
          float: left;
          width: 50%;
        }
        .totals div { 
          display: flex;
          justify-content: space-between;
          padding: 8px 15px;
          border-bottom: 1px solid #e2e8f0;
        }
        .totals .total-row { 
          font-size: 18px;
          font-weight: bold;
          background: #3b82f6;
          color: white;
          margin-top: 10px;
          border-radius: 4px;
        }
        .payment-status {
          display: inline-block;
          padding: 5px 15px;
          border-radius: 20px;
          color: white;
          font-weight: bold;
          background: ${B};
        }
        .footer {
          clear: both;
          margin-top: 50px;
          text-align: center;
          padding-top: 20px;
          border-top: 2px solid #e2e8f0;
          color: #64748b;
        }
        @media print {
          body { padding: 0; }
          .no-print { display: none; }
        }
      </style>
    </head>
    <body>
      <div class="header">
        <h1>متجر العطور</h1>
        <h2>فاتورة بيع</h2>
      </div>
      
      <div class="invoice-info">
        <div>
          <p><strong>رقم الفاتورة:</strong> ${a(t.invoice_number)}</p>
          <p><strong>التاريخ:</strong> ${a(new Date(t.created_at).toLocaleDateString("en-GB"))}</p>
          <p><strong>الوقت:</strong> ${a(new Date(t.created_at).toLocaleTimeString("en-GB"))}</p>
        </div>
        <div>
          ${d}
        </div>
      </div>
      
      <table>
        <thead>
          <tr>
            <th>المنتج</th>
            <th style="width: 80px;">الكمية</th>
            <th style="width: 100px;">السعر</th>
            <th style="width: 100px;">الإجمالي</th>
          </tr>
        </thead>
        <tbody>${n}</tbody>
      </table>
      
      <div class="totals">
        <div><span>المجموع الفرعي:</span><span>${a(t.subtotal)} EGP</span></div>
        <div><span>الضريبة:</span><span>${a(t.tax)} EGP</span></div>
        <div><span>الخصم:</span><span>${a(t.discount)} EGP</span></div>
        <div class="total-row"><span>الإجمالي:</span><span>${a(t.total)} EGP</span></div>
        <div style="background: #10b981; color: white; margin-top: 5px; border-radius: 4px;">
          <span>المبلغ المدفوع:</span><span>${a(t.paid_amount||t.total)} EGP</span>
        </div>
        ${t.remaining_amount>0?`<div style="background: #ef4444; color: white; margin-top: 5px; border-radius: 4px;">
          <span>المتبقي:</span><span>${a(t.remaining_amount)} EGP</span>
        </div>`:""}
        <div style="margin-top: 15px; padding: 10px; background: #f8fafc; border-radius: 4px;">
          <span>طريقة الدفع:</span><span>${l}</span>
        </div>
        <div style="margin-top: 5px; text-align: center;">
          <span class="payment-status">${V}</span>
        </div>
      </div>
      
      <div class="footer">
        <p>شكراً لتعاملكم معنا</p>
        <p>متجر العطور - جميع الحقوق محفوظة</p>
      </div>
    </body>
  </html>`;e.document.write(U),e.document.close(),setTimeout(()=>e.print(),250)},ut=t=>{var U;const e=E.value.find(m=>m.id===t.customer_id);if(!(e!=null&&e.phone))return;const n=((U=t.items)==null?void 0:U.map(m=>{var S;return`• ${(S=m.product)==null?void 0:S.name_ar}: ${a(m.quantity)} × ${a(m.unit_price)} = ${a(m.total_price)} EGP`}).join("\\n"))||"",d=t.payment_status==="paid"?"مدفوع بالكامل ✅":t.payment_status==="partial"?"مدفوع جزئياً ⚠️":"غير مدفوع ❌",l=`🧴 *فاتورة من متجر العطور*\\n\\n📋 رقم الفاتورة: ${a(t.invoice_number)}\\n📅 التاريخ: ${a(new Date(t.created_at).toLocaleDateString("en-GB"))}\\n\\n*المنتجات:*\\n${n}\\n\\n💰 المجموع الفرعي: ${a(t.subtotal)} EGP\\n🏷️ الخصم: ${a(t.discount)} EGP\\n📊 الضريبة: ${a(t.tax)} EGP\\n*💵 الإجمالي: ${a(t.total)} EGP*\\n\\n✅ المدفوع: ${a(t.paid_amount||t.total)} EGP\\n${t.remaining_amount>0?`⚠️ المتبقي: ${a(t.remaining_amount)} EGP\\n`:""}\\n📌 حالة الدفع: ${d}\\n\\nشكراً لتعاملكم معنا 🌹`,B=`https://wa.me/${e.phone.replace(/[^0-9]/g,"")}?text=${encodeURIComponent(l)}`;window.open(B,"_blank")},F=async()=>{try{const t=await $.getProducts();j.value=t.data}catch{c.error("فشل تحميل المنتجات")}},ct=async()=>{try{const t=await $.getCustomers();E.value=t.data}catch{c.error("فشل تحميل العملاء")}};return vt(()=>{F(),ct()}),(t,e)=>(v(),p("div",$t,[s("div",Ct,[s("div",Pt,[s("div",qt,[s("div",zt,[b(s("input",{"onUpdate:modelValue":e[0]||(e[0]=n=>f.value=n),onInput:st,type:"text",placeholder:"ابحث بالاسم أو الباركود...",class:"input pl-10"},null,544),[[g,f.value]]),y(o(wt),{class:"absolute left-3 top-3 text-gray-400",size:20})]),s("button",{onClick:nt,class:"btn btn-primary flex items-center gap-2"},[y(o(_t),{size:20}),e[12]||(e[12]=X(" مسح الباركود ",-1))])])]),s("div",St,[(v(!0),p(L,null,D(et.value,n=>{var d;return v(),p("div",{key:n.id,onClick:l=>R(n),class:"card hover:shadow-lg cursor-pointer transition-all hover:scale-105"},[s("div",jt,[y(o(yt),{size:48,class:"text-gray-400"})]),s("h4",Et,u(n.name_ar),1),s("p",Mt,u((d=n.brand)==null?void 0:d.name_ar),1),s("div",Vt,[s("span",Bt,u(o(G)(n.selling_price)),1),s("span",{class:Z(["text-xs badge",n.stock_quantity>0?"badge-success":"badge-danger"])},u(o(a)(n.stock_quantity))+" قطعة ",3)])],8,Gt)}),128))])]),s("div",Ut,[s("div",It,[s("h3",Tt,[y(o(kt),{size:24}),e[13]||(e[13]=X(" سلة المشتريات ",-1))]),s("div",Lt,[r.value.length===0?(v(),p("div",Dt," السلة فارغة ")):T("",!0),(v(!0),p(L,null,D(r.value,n=>(v(),p("div",{key:n.product.id,class:"flex items-center gap-3 p-3 bg-gray-50 rounded-lg"},[s("div",At,[s("p",Nt,u(n.product.name_ar),1),s("p",Ft,u(o(G)(n.product.selling_price)),1)]),s("div",Ht,[s("button",{onClick:d=>ot(n),class:"w-7 h-7 rounded bg-gray-200 hover:bg-gray-300"},[y(o(ht),{size:16,class:"mx-auto"})],8,Qt),s("span",Kt,u(o(a)(n.quantity)),1),s("button",{onClick:d=>at(n),class:"w-7 h-7 rounded bg-gray-200 hover:bg-gray-300"},[y(o(ft),{size:16,class:"mx-auto"})],8,Ot)]),s("button",{onClick:d=>Y(n),class:"text-red-600 hover:text-red-800"},[y(o(gt),{size:18})],8,Wt)]))),128))]),s("div",Rt,[e[15]||(e[15]=s("label",{class:"block text-sm font-medium mb-2"},"العميل (اختياري)",-1)),s("div",Yt,[b(s("select",{"onUpdate:modelValue":e[1]||(e[1]=n=>C.value=n),class:"input flex-1"},[e[14]||(e[14]=s("option",{value:""},"عميل عادي",-1)),(v(!0),p(L,null,D(E.value,n=>(v(),p("option",{key:n.id,value:n.id},u(n.name)+" - "+u(o(a)(n.phone)),9,Jt))),128))],512),[[mt,C.value]]),s("button",{onClick:e[2]||(e[2]=n=>q.value=!0),class:"btn btn-secondary"},[y(o(xt),{size:18})])])]),s("div",Xt,[s("div",Zt,[e[16]||(e[16]=s("span",null,"المجموع الفرعي:",-1)),s("span",te,u(o(G)(K.value)),1)]),s("div",ee,[e[17]||(e[17]=s("span",null,"الخصم:",-1)),b(s("input",{"onUpdate:modelValue":e[3]||(e[3]=n=>x.value=n),type:"number",step:"0.01",class:"input w-32 text-left py-1"},null,512),[[g,x.value,void 0,{number:!0}]])]),s("div",se,[e[18]||(e[18]=s("span",null,"الضريبة:",-1)),b(s("input",{"onUpdate:modelValue":e[4]||(e[4]=n=>h.value=n),type:"number",step:"0.01",class:"input w-32 text-left py-1"},null,512),[[g,h.value,void 0,{number:!0}]])]),s("div",ne,[e[19]||(e[19]=s("span",null,"الإجمالي:",-1)),s("span",ae,u(o(G)(z.value)),1)]),s("div",oe,[e[20]||(e[20]=s("span",null,"المبلغ المدفوع:",-1)),b(s("input",{"onUpdate:modelValue":e[5]||(e[5]=n=>P.value=n),type:"number",step:"0.01",placeholder:z.value.toString(),class:"input w-32 text-left py-1"},null,8,le),[[g,P.value,void 0,{number:!0}]])]),O.value>0?(v(),p("div",ie,[e[21]||(e[21]=s("span",null,"المتبقي:",-1)),s("span",null,u(o(G)(O.value)),1)])):T("",!0)]),s("div",re,[e[22]||(e[22]=s("label",{class:"block text-sm font-medium mb-2"},"طريقة الدفع",-1)),s("div",de,[(v(),p(L,null,D(Q,n=>s("button",{key:n.value,onClick:d=>N.value=n.value,class:Z([N.value===n.value?"bg-primary-600 text-white":"bg-gray-100","py-2 rounded-lg font-medium transition-all"])},u(n.label),11,ue)),64))])]),s("div",ce,[s("button",{onClick:rt,disabled:r.value.length===0,class:"btn btn-success w-full py-3 text-lg"}," إتمام البيع ",8,pe),s("button",{onClick:lt,class:"btn btn-secondary w-full"}," مسح السلة ")])])]),M.value?(v(),p("div",ve,[s("div",me,[e[23]||(e[23]=s("h3",{class:"text-2xl font-bold mb-4"},"مسح الباركود",-1)),b(s("input",{ref_key:"barcodeInput",ref:H,"onUpdate:modelValue":e[6]||(e[6]=n=>_.value=n),onKeyup:bt(W,["enter"]),type:"text",placeholder:"امسح أو اكتب الباركود...",class:"input mb-4",autofocus:""},null,544),[[g,_.value]]),s("div",be,[s("button",{onClick:e[7]||(e[7]=n=>M.value=!1),class:"btn btn-secondary flex-1"},"إلغاء"),s("button",{onClick:W,class:"btn btn-primary flex-1"},"بحث")])])])):T("",!0),q.value?(v(),p("div",{key:1,class:"fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50",onClick:e[11]||(e[11]=tt(n=>q.value=!1,["self"]))},[s("div",ye,[e[27]||(e[27]=s("h3",{class:"text-2xl font-bold mb-4"},"إضافة عميل",-1)),s("form",{onSubmit:tt(it,["prevent"]),class:"space-y-4"},[s("div",null,[e[24]||(e[24]=s("label",{class:"block text-sm font-medium mb-2"},"الاسم *",-1)),b(s("input",{"onUpdate:modelValue":e[8]||(e[8]=n=>w.value.name=n),type:"text",required:"",class:"input"},null,512),[[g,w.value.name]])]),s("div",null,[e[25]||(e[25]=s("label",{class:"block text-sm font-medium mb-2"},"رقم الهاتف *",-1)),b(s("input",{"onUpdate:modelValue":e[9]||(e[9]=n=>w.value.phone=n),type:"tel",required:"",class:"input"},null,512),[[g,w.value.phone]])]),s("div",fe,[s("button",{type:"button",onClick:e[10]||(e[10]=n=>q.value=!1),class:"btn btn-secondary flex-1"},"إلغاء"),e[26]||(e[26]=s("button",{type:"submit",class:"btn btn-primary flex-1"},"حفظ",-1))])],32)])])):T("",!0)]))}};export{Pe as default};
