import{b as Z,r as h,g as q,h as tt,c as d,o as p,d as t,j,t as a,l as c,F as T,m as L,s as M,e as A,v as B,k,y as $}from"./index-BkgdYNSs.js";import{f as S,t as z}from"./numbers-BTmV-EI5.js";import{E as et}from"./eye-phNDi1gA.js";import{c as E}from"./createLucideIcon-CjPfVOsg.js";import{X as ot}from"./x-DRpX_9Z2.js";/**
 * @license lucide-vue-next v0.294.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const st=E("PrinterIcon",[["polyline",{points:"6 9 6 2 18 2 18 9",key:"1306q4"}],["path",{d:"M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2",key:"143wyd"}],["rect",{width:"12",height:"8",x:"6",y:"14",key:"5ipwut"}]]);/**
 * @license lucide-vue-next v0.294.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const at=E("XCircleIcon",[["circle",{cx:"12",cy:"12",r:"10",key:"1mglay"}],["path",{d:"m15 9-6 6",key:"1uzhvr"}],["path",{d:"m9 9 6 6",key:"z0biqf"}]]),nt={class:"space-y-6"},lt={class:"grid grid-cols-1 md:grid-cols-4 gap-4"},it={class:"card bg-gradient-to-br from-green-500 to-green-600 text-white"},rt={class:"text-2xl font-bold mt-2"},dt={class:"card bg-gradient-to-br from-blue-500 to-blue-600 text-white"},pt={class:"text-2xl font-bold mt-2"},ct={class:"card bg-gradient-to-br from-orange-500 to-orange-600 text-white"},ut={class:"text-2xl font-bold mt-2"},bt={class:"card bg-gradient-to-br from-purple-500 to-purple-600 text-white"},mt={class:"text-2xl font-bold mt-2"},gt={class:"flex gap-2 border-b"},xt=["onClick"],ft={class:"flex items-center gap-4"},ht={class:"card overflow-hidden"},vt={class:"table"},yt={class:"font-mono font-bold text-primary-600"},_t={class:"font-bold"},wt={class:"font-bold text-green-600"},kt={class:"flex items-center gap-2"},$t=["onClick"],St=["onClick"],zt=["onClick"],Ct={key:0,class:"fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"},Dt={class:"bg-white rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto"},It={class:"flex items-center justify-between mb-6"},jt={class:"space-y-4"},Tt={class:"grid grid-cols-2 gap-4"},Lt={class:"font-bold"},Mt={class:"font-bold"},Pt={class:"font-bold"},Vt={class:"font-bold"},Ft={class:"border-t pt-4"},qt={class:"table"},At={class:"font-bold"},Bt={class:"border-t pt-4 space-y-2"},Et={class:"flex justify-between"},Nt={class:"font-bold"},Ut={class:"flex justify-between"},Ot={class:"font-bold"},Xt={class:"flex justify-between"},Gt={class:"font-bold"},Ht={class:"flex justify-between text-lg border-t pt-2"},Rt={class:"font-bold text-primary-600"},te={__name:"Sales",setup(Wt){const m=Z(),u=h([]),l=h(null),g=h(""),x=h(""),v=h("all"),N=[{value:"all",label:"الكل"},{value:"paid",label:"مدفوعة"},{value:"partial",label:"دفع جزئي"},{value:"unpaid",label:"غير مدفوعة"}],n=o=>S(o),P=o=>{const e=o.paid_amount||o.total;return Math.max(0,o.total-e)},f=o=>{const e=o.paid_amount||o.total;return e>=o.total?"paid":e>0?"partial":"unpaid"},U=o=>({paid:"مدفوعة",partial:"دفع جزئي",unpaid:"غير مدفوعة"})[o]||o,O=q(()=>v.value==="all"?u.value:u.value.filter(o=>f(o)===v.value)),y=q(()=>{const o=u.value.reduce((s,i)=>s+parseFloat(i.total||0),0),e=u.value.reduce((s,i)=>s+parseFloat(i.paid_amount||i.total||0),0),r=o-e;return{total_sales:o,paid:e,unpaid:r,count:u.value.length}}),C=o=>{const r=new Date(o).toLocaleString("en-GB",{year:"numeric",month:"long",day:"numeric",hour:"2-digit",minute:"2-digit"});return z(r)},V=o=>({cash:"نقدي",card:"بطاقة",transfer:"تحويل"})[o]||o,D=async()=>{try{const o={};g.value&&(o.start_date=g.value),x.value&&(o.end_date=x.value);const e=await $.getSales(o);u.value=e.data}catch{m.error("فشل تحميل المبيعات")}},X=async o=>{try{const e=await $.getSale(o.id);l.value=e.data}catch{m.error("فشل تحميل تفاصيل الفاتورة")}},G=async o=>{if(confirm("هل أنت متأكد من إلغاء هذه الفاتورة؟ سيتم إرجاع المنتجات للمخزون."))try{await $.cancelSale(o),m.success("تم إلغاء الفاتورة بنجاح"),D()}catch{m.error("فشل إلغاء الفاتورة")}},H=async o=>{let e=o;if(!o.items||o.items.length===0)try{e=(await $.getSale(o.id)).data}catch{m.error("فشل تحميل تفاصيل الفاتورة");return}const r=window.open("","_blank"),R=(e.items||[]).slice(0,15).map((b,Q)=>{var F;return`<tr style="background-color: ${Q%2===0?"#f9fafb":"white"};">
      <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">${((F=b.product)==null?void 0:F.name_ar)||"منتج"}</td>
      <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;">${z(b.quantity)}</td>
      <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left;">${n(b.unit_price)}</td>
      <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left; font-weight: bold;">${n(b.total_price)}</td>
    </tr>`}).join(""),W=e.customer?`<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
      <h3 style="margin: 0 0 10px 0; font-size: 16px;">معلومات العميل</h3>
      <p style="margin: 5px 0;"><strong>الاسم:</strong> ${e.customer.name}</p>
      <p style="margin: 5px 0;"><strong>الهاتف:</strong> ${z(e.customer.phone)}</p>
    </div>`:"",_=e.paid_amount||e.total,w=Math.max(0,e.total-_),Y=w>0?_>0?"دفع جزئي":"غير مدفوعة":"مدفوعة بالكامل",J=w>0?_>0?"#f59e0b":"#ef4444":"#10b981",K=`<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <title>فاتورة ${e.invoice_number}</title>
  <style>
    @page { size: A4; margin: 0; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: white;
      padding: 20mm;
      font-size: 11pt;
    }
    .header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 30px;
      border-radius: 12px;
      text-align: center;
      margin-bottom: 25px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .header h1 { font-size: 32px; margin-bottom: 5px; }
    .header h2 { font-size: 18px; opacity: 0.9; font-weight: normal; }
    .invoice-info {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
      margin-bottom: 25px;
    }
    .info-box {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
      padding: 15px;
      border-radius: 8px;
    }
    .info-box.alt {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .info-box h3 { font-size: 12px; opacity: 0.9; margin-bottom: 5px; }
    .info-box p { font-size: 18px; font-weight: bold; }
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    thead {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }
    th {
      padding: 15px 12px;
      text-align: right;
      font-weight: 600;
      font-size: 12px;
    }
    th:nth-child(2), th:nth-child(3), th:nth-child(4) { text-align: center; }
    tbody tr:last-child td { border-bottom: none; }
    .totals {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
      padding: 20px;
      border-radius: 8px;
      margin-top: 20px;
    }
    .totals-row {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    .totals-row:last-child { border-bottom: none; padding-top: 15px; }
    .totals-row.grand { font-size: 20px; font-weight: bold; }
    .payment-status {
      display: inline-block;
      padding: 8px 16px;
      border-radius: 20px;
      font-weight: bold;
      margin-top: 10px;
      background: ${J};
      color: white;
    }
    .footer {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 2px solid #e5e7eb;
      color: #6b7280;
      font-size: 10px;
    }
    @media print {
      body { padding: 15mm; }
      .no-print { display: none; }
    }
  </style>
</head>
<body>
  <div class="header">
    <h1>🌸 متجر العطور 🌸</h1>
    <h2>فاتورة مبيعات</h2>
  </div>
  
  <div class="invoice-info">
    <div class="info-box">
      <h3>رقم الفاتورة</h3>
      <p>${e.invoice_number}</p>
    </div>
    <div class="info-box alt">
      <h3>التاريخ</h3>
      <p>${C(e.created_at)}</p>
    </div>
  </div>
  
  ${W}
  
  <table>
    <thead>
      <tr>
        <th>المنتج</th>
        <th>الكمية</th>
        <th>السعر</th>
        <th>الإجمالي</th>
      </tr>
    </thead>
    <tbody>
      ${R}
    </tbody>
  </table>
  
  <div class="totals">
    <div class="totals-row">
      <span>المجموع الفرعي:</span>
      <span>${n(e.subtotal||e.total)}</span>
    </div>
    <div class="totals-row">
      <span>الضريبة:</span>
      <span>${n(e.tax||0)}</span>
    </div>
    <div class="totals-row">
      <span>الخصم:</span>
      <span>${n(e.discount||0)}</span>
    </div>
    <div class="totals-row grand">
      <span>الإجمالي:</span>
      <span>${n(e.total)}</span>
    </div>
    <div class="totals-row">
      <span>المبلغ المدفوع:</span>
      <span>${n(_)}</span>
    </div>
    ${w>0?`<div class="totals-row" style="color: #fef3c7;">
      <span>المتبقي:</span>
      <span style="font-size: 18px;">${n(w)}</span>
    </div>`:""}
    <div style="margin-top: 15px;">
      <span>طريقة الدفع: ${V(e.payment_method)}</span>
      <span class="payment-status">${Y}</span>
    </div>
  </div>
  
  <div class="footer">
    <p>شكراً لتعاملكم معنا 🌹</p>
    <p>للاستفسار: 01234567890 | البريد: info@perfume-store.com</p>
  </div>
  
  <script>
    window.onload = () => {
      setTimeout(() => window.print(), 500)
    }
  <\/script>
</body>
</html>`;r.document.write(K),r.document.close()};return tt(()=>{const o=new Date,e=new Date(o);e.setDate(o.getDate()-30),x.value=o.toISOString().split("T")[0],g.value=e.toISOString().split("T")[0],D()}),(o,e)=>{var r;return p(),d("div",nt,[t("div",lt,[t("div",it,[e[3]||(e[3]=t("h3",{class:"text-sm font-medium opacity-90"},"إجمالي المبيعات",-1)),t("p",rt,a(c(S)(y.value.total_sales)),1)]),t("div",dt,[e[4]||(e[4]=t("h3",{class:"text-sm font-medium opacity-90"},"المدفوع",-1)),t("p",pt,a(c(S)(y.value.paid)),1)]),t("div",ct,[e[5]||(e[5]=t("h3",{class:"text-sm font-medium opacity-90"},"المتبقي",-1)),t("p",ut,a(c(S)(y.value.unpaid)),1)]),t("div",bt,[e[6]||(e[6]=t("h3",{class:"text-sm font-medium opacity-90"},"عدد الفواتير",-1)),t("p",mt,a(c(z)(y.value.count)),1)])]),t("div",gt,[(p(),d(T,null,L(N,s=>t("button",{key:s.value,onClick:i=>v.value=s.value,class:M([v.value===s.value?"border-b-2 border-primary-600 text-primary-600":"text-gray-600","px-4 py-2 font-medium transition-colors"])},a(s.label),11,xt)),64))]),t("div",ft,[A(t("input",{"onUpdate:modelValue":e[0]||(e[0]=s=>g.value=s),type:"date",class:"input w-48"},null,512),[[B,g.value]]),e[7]||(e[7]=t("span",null,"إلى",-1)),A(t("input",{"onUpdate:modelValue":e[1]||(e[1]=s=>x.value=s),type:"date",class:"input w-48"},null,512),[[B,x.value]]),t("button",{onClick:D,class:"btn btn-primary"},"بحث")]),t("div",ht,[t("table",vt,[e[8]||(e[8]=t("thead",null,[t("tr",null,[t("th",null,"رقم الفاتورة"),t("th",null,"التاريخ"),t("th",null,"العميل"),t("th",null,"المبلغ الكلي"),t("th",null,"المدفوع"),t("th",null,"المتبقي"),t("th",null,"حالة الدفع"),t("th",null,"الإجراءات")])],-1)),t("tbody",null,[(p(!0),d(T,null,L(O.value,s=>{var i;return p(),d("tr",{key:s.id},[t("td",yt,a(s.invoice_number),1),t("td",null,a(C(s.created_at)),1),t("td",null,a(((i=s.customer)==null?void 0:i.name)||"عميل عادي"),1),t("td",_t,a(n(s.total)),1),t("td",wt,a(n(s.paid_amount||s.total)),1),t("td",{class:M(["font-bold",P(s)>0?"text-red-600":"text-gray-400"])},a(n(P(s))),3),t("td",null,[t("span",{class:M([{"badge-success":f(s)==="paid","badge-warning":f(s)==="partial","badge-danger":f(s)==="unpaid"},"badge"])},a(U(f(s))),3)]),t("td",null,[t("div",kt,[t("button",{onClick:I=>X(s),class:"text-blue-600 hover:text-blue-800"},[k(c(et),{size:18})],8,$t),s.status==="completed"?(p(),d("button",{key:0,onClick:I=>H(s),class:"text-green-600 hover:text-green-800"},[k(c(st),{size:18})],8,St)):j("",!0),s.status==="completed"?(p(),d("button",{key:1,onClick:I=>G(s.id),class:"text-red-600 hover:text-red-800"},[k(c(at),{size:18})],8,zt)):j("",!0)])])])}),128))])])]),l.value?(p(),d("div",Ct,[t("div",Dt,[t("div",It,[e[9]||(e[9]=t("h3",{class:"text-2xl font-bold"},"تفاصيل الفاتورة",-1)),t("button",{onClick:e[2]||(e[2]=s=>l.value=null),class:"text-gray-500 hover:text-gray-700"},[k(c(ot),{size:24})])]),t("div",jt,[t("div",Tt,[t("div",null,[e[10]||(e[10]=t("p",{class:"text-sm text-gray-500"},"رقم الفاتورة",-1)),t("p",Lt,a(l.value.invoice_number),1)]),t("div",null,[e[11]||(e[11]=t("p",{class:"text-sm text-gray-500"},"التاريخ",-1)),t("p",Mt,a(C(l.value.created_at)),1)]),t("div",null,[e[12]||(e[12]=t("p",{class:"text-sm text-gray-500"},"العميل",-1)),t("p",Pt,a(((r=l.value.customer)==null?void 0:r.name)||"عميل عادي"),1)]),t("div",null,[e[13]||(e[13]=t("p",{class:"text-sm text-gray-500"},"طريقة الدفع",-1)),t("p",Vt,a(V(l.value.payment_method)),1)])]),t("div",Ft,[e[15]||(e[15]=t("h4",{class:"font-bold mb-3"},"المنتجات",-1)),t("table",qt,[e[14]||(e[14]=t("thead",null,[t("tr",null,[t("th",null,"المنتج"),t("th",null,"الكمية"),t("th",null,"السعر"),t("th",null,"الإجمالي")])],-1)),t("tbody",null,[(p(!0),d(T,null,L(l.value.items,s=>{var i;return p(),d("tr",{key:s.id},[t("td",null,a((i=s.product)==null?void 0:i.name_ar),1),t("td",null,a(s.quantity),1),t("td",null,a(n(s.unit_price)),1),t("td",At,a(n(s.total_price)),1)])}),128))])])]),t("div",Bt,[t("div",Et,[e[16]||(e[16]=t("span",null,"المجموع الفرعي:",-1)),t("span",Nt,a(n(l.value.subtotal)),1)]),t("div",Ut,[e[17]||(e[17]=t("span",null,"الضريبة:",-1)),t("span",Ot,a(n(l.value.tax)),1)]),t("div",Xt,[e[18]||(e[18]=t("span",null,"الخصم:",-1)),t("span",Gt,a(n(l.value.discount)),1)]),t("div",Ht,[e[19]||(e[19]=t("span",{class:"font-bold"},"الإجمالي:",-1)),t("span",Rt,a(n(l.value.total)),1)])])])])])):j("",!0)])}}};export{te as default};
