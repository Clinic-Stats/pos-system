{{-- فۆنت و ڕێکخستنی مۆبایل بۆ هەموو لاپەڕەکان. ئەم فایلە دەخرێتە ناو resources/views/partials/ --}}
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@700;800&display=swap" rel="stylesheet">
<style>
/* ===== فۆنتی NRT Bold (فایلەکە دەبێت لە public/fonts/ بێت) ===== */
@font-face{
  font-family:'NRT';
  src:local('NRT Bold'),local('NRT-Bold'),
      url('{{ asset('fonts/NRT-Bold.woff2') }}') format('woff2'),
      url('{{ asset('fonts/NRT-Bold.woff') }}') format('woff'),
      url('{{ asset('fonts/NRT-Bold.ttf') }}') format('truetype');
  font-weight:100 900;   /* هەموو کێشەکان هەمان NRT Bold بەکاردەهێنن */
  font-style:normal;
  font-display:swap;
}
:root{--font-sys:'NRT','Noto Sans Arabic','Segoe UI',Tahoma,sans-serif}
html,body{font-family:var(--font-sys)!important}
button,input,select,textarea,table,th,td,label,h1,h2,h3,h4,h5,h6,p,a{font-family:var(--font-sys)}
.num,.font-num,.font-mono,.font-sans,kbd{font-family:var(--font-sys)!important}
.fa,.fas,.far,.fab,.fa-solid,.fa-regular,.fa-brands{font-family:'Font Awesome 6 Free','Font Awesome 6 Brands'}  /* ئایکۆنەکان نەگۆڕدرێن */
.fa-solid,.fas,.fa{font-weight:900!important}
.fa-regular,.far{font-weight:400!important}

html{-webkit-text-size-adjust:100%;text-size-adjust:100%}
img,video,canvas,svg{max-width:100%}
body{overflow-wrap:anywhere}

/* ===== مۆبایل (تەنها لەسەر شاشە، نەک لە چاپ) ===== */
@media screen and (max-width:768px){
  body{padding:.55rem!important}
  .max-w-7xl,.max-w-6xl,.max-w-5xl,.max-w-4xl,.max-w-3xl{max-width:100%!important}
  /* خشتە: لە لای ڕاست/چەپ دەجوڵێتەوە، شاشە ناشکێنێت */
  table{display:block;max-width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}
  /* ئەگەر ئایفۆن زووم نەکات */
  input:not([type=checkbox]):not([type=radio]):not([type=file]):not([type=range]),select,textarea{font-size:16px!important}
  /* فۆڕمی فلتەر */
  form .w-64,form .w-48,form .w-44,form .w-40,form .w-36{width:100%!important}
  /* پەنجەرە و مۆداڵ */
  .fixed.inset-0>div{max-height:92vh;overflow-y:auto}
  /* دوگمە و لینکی گەورەتر بۆ پەنجە */
  button[type=submit],a.btn,.btn{min-height:42px}
  h1{font-size:1rem!important;line-height:1.5}
}
@media screen and (max-width:480px){
  .grid-cols-3:not(.keep-cols),.grid-cols-4:not(.keep-cols){grid-template-columns:repeat(2,minmax(0,1fr))}
}
</style>