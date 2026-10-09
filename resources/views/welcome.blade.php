<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>SBMart – Groceries & essentials in minutes</title>
<link rel="icon" type="image/png" href="/sbmart.png">
<link rel="shortcut icon" type="image/png" href="/sbmart.png">
<link rel="apple-touch-icon" href="/sbmart.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,700;12..96,800&display=swap" rel="stylesheet">
<style>
:root{--g:#0c831f;--g2:#096517;--gd:#0a2c10;--gt:#2b5e34;--gl:#eaf8ed;--w:#fff;--sh:0 20px 40px -12px rgba(12,131,31,.22);
box-sizing:border-box;padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px)}
*,*::before,*::after{box-sizing:inherit;margin:0}
html{height:100%;scroll-padding-top:env(safe-area-inset-top,0px)}
body{min-height:100%;font-family:'Bricolage Grotesque',system-ui,-apple-system,'Segoe UI',sans-serif;color:var(--gd);background:linear-gradient(135deg,#fff 0%,#f0faf1 55%,#dcf4e1 100%);overflow-x:hidden;position:relative}
.blob{position:fixed;border-radius:50%;filter:blur(70px);opacity:.55;z-index:0;animation:drift 18s ease-in-out infinite alternate;pointer-events:none}
.b1{width:46vmax;height:46vmax;background:#b3ecc0;top:-18vmax;right:-8vmax}
.b2{width:34vmax;height:34vmax;background:#d2f5da;bottom:-14vmax;left:-8vmax;animation-delay:-6s}
.b3{width:18vmax;height:18vmax;background:#9ee4af;top:45%;left:38%;opacity:.3;animation-delay:-11s}
@keyframes drift{to{transform:translate(30px,-24px) scale(1.08)}}
.page{position:relative;z-index:1;min-height:100vh;max-width:1240px;margin:0 auto;padding:22px 32px;display:grid;grid-template-rows:auto 1fr;gap:8px}
.header-row{display:flex;justify-content:space-between;align-items:center;width:100%}
.brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:26px;letter-spacing:-.02em;color:var(--gd)}
.brand i{width:38px;height:38px;border-radius:12px;background:linear-gradient(145deg,var(--g),var(--g2));display:grid;place-items:center;box-shadow:0 8px 18px -6px rgba(12,131,31,.7)}
.admin-btn{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:12px;background:var(--gd);color:#fff;text-decoration:none;font-weight:700;font-size:13px;box-shadow:0 6px 16px -4px rgba(10,44,16,.4);transition:all .2s ease}
.admin-btn:hover{background:var(--g2);transform:translateY(-2px)}
.hero{display:grid;grid-template-columns:1.05fr 1fr;align-items:center;gap:24px}
.copy{max-width:560px}
.badge{display:inline-flex;align-items:center;gap:8px;padding:8px 16px 8px 10px;border-radius:99px;background:rgba(255,255,255,.8);backdrop-filter:blur(12px);border:1px solid rgba(12,131,31,.3);font-size:14px;font-weight:600;color:var(--gt);box-shadow:0 6px 16px -8px rgba(12,131,31,.3)}
.badge b{width:8px;height:8px;border-radius:50%;background:var(--g);box-shadow:0 0 0 0 rgba(12,131,31,.6);animation:pulse 2s infinite}
@keyframes pulse{70%{box-shadow:0 0 0 9px rgba(12,131,31,0)}100%{box-shadow:0 0 0 0 rgba(12,131,31,0)}}
h1{font-size:clamp(38px,5.4vw,72px);line-height:1;letter-spacing:-.035em;font-weight:800;margin:22px 0 18px}
h1 span{display:block;color:var(--g)}
.sub{font-size:clamp(16px,1.35vw,19px);line-height:1.55;color:var(--gt);max-width:470px}
.cta-group{display:flex;gap:14px;flex-wrap:wrap;margin-top:30px}
.cta{display:inline-flex;align-items:center;gap:14px;padding:12px 26px 12px 20px;border-radius:18px;background:var(--gd);color:#fff;text-decoration:none;box-shadow:0 18px 34px -10px rgba(10,44,16,.55);transition:transform .3s cubic-bezier(.2,.8,.2,1),box-shadow .3s;position:relative;overflow:hidden}
.cta::after{content:"";position:absolute;inset:0;background:linear-gradient(105deg,transparent 35%,rgba(255,255,255,.22) 50%,transparent 65%);transform:translateX(-120%);animation:shine 4.5s 1.8s infinite}
@keyframes shine{30%,100%{transform:translateX(120%)}}
.cta:hover,.cta:focus-visible{transform:translateY(-3px) scale(1.03);box-shadow:0 24px 44px -10px rgba(12,131,31,.65);outline:none}
.cta small{display:block;font-size:11px;letter-spacing:.14em;font-weight:600;opacity:.8}
.cta strong{display:block;font-size:22px;letter-spacing:-.02em;line-height:1.1}
.cta svg{width:34px;height:38px;flex:none}
.cta-apk{background:var(--g);box-shadow:0 18px 34px -10px rgba(12,131,31,.55)}
.perks{margin-top:20px;font-size:14px;color:var(--gt);font-weight:600}
.stage{position:relative;height:min(78vh,680px);display:grid;place-items:center}
.glow{position:absolute;width:70%;aspect-ratio:1;border-radius:50%;background:radial-gradient(circle,rgba(12,131,31,.35),rgba(12,131,31,0) 68%);animation:breathe 6s ease-in-out infinite}
@keyframes breathe{50%{transform:scale(1.1);opacity:.8}}
.phone{position:relative;height:100%;max-height:640px;aspect-ratio:9/18.4;border-radius:44px;padding:10px;background:linear-gradient(160deg,#0a2c10,#051708);box-shadow:var(--sh),0 50px 80px -30px rgba(10,44,16,.5),0 0 0 2px #154d20 inset;transition:transform .6s cubic-bezier(.2,.8,.2,1);transform:rotate(-3deg);animation:bob 7s ease-in-out infinite}
.phone:hover{transform:rotate(0) scale(1.03)}
@keyframes bob{50%{translate:0 -10px}}
.screen{height:100%;border-radius:35px;background:linear-gradient(#f6fff2,#fff 40%);overflow:hidden;padding:16px 14px 0;display:flex;flex-direction:column;gap:10px;position:relative;font-size:11px}
.notch{position:absolute;top:8px;left:50%;translate:-50% 0;width:70px;height:18px;border-radius:12px;background:#051708}
.top{margin-top:16px;display:flex;justify-content:space-between;align-items:center}
.eta{font-weight:800;font-size:16px;letter-spacing:-.02em;color:var(--g)}
.loc{color:var(--gt);display:flex;align-items:center;gap:3px;margin-top:2px}
.av{width:30px;height:30px;border-radius:50%;background:var(--gl);border:1px solid #cdeec1;display:grid;place-items:center}
.search{padding:10px 12px;border-radius:12px;background:#fff;border:1px solid #dcf3d2;color:#7a9a86;box-shadow:0 6px 14px -8px rgba(12,131,31,.35)}
.promo{border-radius:16px;padding:14px;background:linear-gradient(120deg,var(--g),#4fc23a);color:#fff;position:relative;overflow:hidden;min-height:82px}
.promo b{display:block;font-size:15px;line-height:1.15;width:62%}
.promo em{display:inline-block;margin-top:8px;padding:4px 9px;border-radius:99px;background:var(--gd);color:#fff;font-style:normal;font-size:10px}
.promo svg{position:absolute;right:-6px;bottom:-4px;width:84px;height:84px}
.chips{display:flex;gap:6px}
.chips span{padding:5px 9px;border-radius:99px;background:var(--gl);color:var(--gt);font-weight:600}
.chips span:first-child{background:var(--gd);color:#fff}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.p{background:#fff;border-radius:14px;padding:8px;border:1px solid #e3f6da;box-shadow:0 8px 16px -12px rgba(12,131,31,.3)}
.p div{height:54px;border-radius:10px;background:var(--gl);display:grid;place-items:center}
.p svg{width:42px;height:42px}
.p b{display:block;margin-top:6px;font-size:11px}
.p span{display:flex;justify-content:space-between;align-items:center;margin-top:3px;color:var(--gt)}
.p u{text-decoration:none;width:20px;height:20px;border-radius:7px;background:var(--g);color:#fff;display:grid;place-items:center;font-weight:800}
.nav{margin-top:auto;margin-inline:-14px;padding:10px 24px 14px;display:flex;justify-content:space-between;background:rgba(255,255,255,.85);backdrop-filter:blur(10px);border-top:1px solid #e3f6da}
.nav i{width:22px;height:22px;border-radius:7px;background:#dff4d5}
.nav i:first-child{background:var(--g)}
.f{position:absolute;width:var(--s,84px);height:var(--s,84px);filter:drop-shadow(0 16px 14px rgba(10,44,16,.22));animation:float var(--d,6s) ease-in-out var(--l,0s) infinite;z-index:3}
@keyframes float{0%,100%{transform:translateY(0) rotate(var(--r,0deg))}50%{transform:translateY(-16px) rotate(calc(var(--r,0deg) + 5deg))}}
.f.glass{background:rgba(255,255,255,.75);backdrop-filter:blur(10px);border-radius:24px;padding:12px;border:1px solid rgba(255,255,255,.9)}
.f1{top:6%;left:2%;--s:96px;--r:-8deg}.f2{top:2%;right:4%;--s:78px;--d:7s;--l:-2s}
.f3{top:38%;left:-4%;--s:80px;--d:5.5s;--l:-1s}.f4{top:34%;right:-3%;--s:82px;--d:6.5s;--l:-3s}
.f5{bottom:8%;left:2%;--s:78px;--d:7.5s;--l:-4s}.f6{bottom:2%;right:0%;--s:110px;--d:8s;--l:-2.5s}
.f7{bottom:26%;left:14%;--s:64px;--d:6s;--l:-5s}.f8{top:15%;right:15%;--s:60px;--d:5s;--l:-3.5s}
.in{opacity:0;transform:translateY(22px);animation:in .9s cubic-bezier(.2,.8,.2,1) forwards}
@keyframes in{to{opacity:1;transform:none}}
@media(max-width:900px){
 .page{padding:16px 20px 32px}
 .hero{grid-template-columns:1fr;text-align:center;gap:10px}
 .copy{margin:0 auto;display:flex;flex-direction:column;align-items:center}
 h1{margin:16px 0 12px}
 .cta-group{justify-content:center;margin-top:20px}
 .stage{height:560px}
 .phone{max-height:520px}
 .f1{left:0}.f2{right:0}.f3{left:-2%}.f4{right:-2%}.f6{right:0}
 .f{--s:64px}.f6{--s:80px}.f1{--s:72px}
}
</style>
</head>
<body>
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
<defs>
<linearGradient id="gg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#4fc23a"/><stop offset="1" stop-color="#0c831f"/></linearGradient>
<linearGradient id="gr" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ff7a6b"/><stop offset="1" stop-color="#e03b3b"/></linearGradient>
<linearGradient id="go" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ffb457"/><stop offset="1" stop-color="#f27a1a"/></linearGradient>
<linearGradient id="gy" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ffe27a"/><stop offset="1" stop-color="#f7b526"/></linearGradient>
<linearGradient id="gw" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff"/><stop offset="1" stop-color="#dcebf5"/></linearGradient>
<linearGradient id="gb" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e2a36b"/><stop offset="1" stop-color="#b86f36"/></linearGradient>
</defs>
<symbol id="bag" viewBox="0 0 64 64"><path d="M22 20c0-8 4-13 10-13s10 5 10 13" fill="none" stroke="#0c831f" stroke-width="3" stroke-linecap="round"/><path d="M12 20h40l-3 36a4 4 0 0 1-4 3H19a4 4 0 0 1-4-3z" fill="url(#gg)"/><path d="M12 20h40l-.6 7H12.6z" fill="#fff" opacity=".28"/><path d="M32 46c-6-3-7-9-2-12 2 3 6 4 8 6-1 3-3 5-6 6z" fill="#fff"/><path d="M29 44c2-3 5-6 8-7" stroke="#0c831f" stroke-width="1.6" fill="none"/></symbol>
<symbol id="milk" viewBox="0 0 64 64"><path d="M25 6h14v9l7 11v30a4 4 0 0 1-4 4H22a4 4 0 0 1-4-4V26l7-11z" fill="url(#gw)" stroke="#c6dbe8" stroke-width="1.5"/><rect x="24" y="3" width="16" height="7" rx="3" fill="url(#gg)"/><rect x="18" y="33" width="28" height="15" fill="url(#gg)"/><path d="M27 40h10M32 36v8" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/><path d="M22 28v22" stroke="#fff" stroke-width="2" opacity=".8"/></symbol>
<symbol id="carrot" viewBox="0 0 64 64"><path d="M40 20c8 4 8 10 2 22L26 58c-2 2-5 0-4-3l6-20c4-14 8-18 12-15z" fill="url(#go)"/><path d="M30 34l5 1M27 43l6 1M34 27l4 1" stroke="#c85f0c" stroke-width="1.8" stroke-linecap="round"/><path d="M40 20c-2-6-1-11 3-14 1 4 3 6 2 10 3-4 7-5 11-4-2 5-6 8-12 9z" fill="url(#gg)"/></symbol>
<symbol id="apple" viewBox="0 0 64 64"><path d="M32 20c-5-4-16-3-19 8-3 12 6 30 15 30 3 0 3-1 4-1s1 1 4 1c9 0 18-18 15-30-3-11-14-12-19-8z" fill="url(#gr)"/><path d="M20 28c1-5 4-7 7-7" stroke="#fff" stroke-width="3" stroke-linecap="round" opacity=".5" fill="none"/><path d="M32 20c0-6 1-10 5-13" stroke="#6b4a2a" stroke-width="3" stroke-linecap="round" fill="none"/><path d="M36 14c3-5 9-6 13-4-2 5-8 7-13 4z" fill="url(#gg)"/></symbol>
<symbol id="snack" viewBox="0 0 64 64"><path d="M14 8h36l-3 6 3 6-3 6 3 6-3 6 3 6-3 6 3 6H14l3-6-3-6 3-6-3-6 3-6-3-6 3-6z" fill="url(#gy)"/><circle cx="32" cy="32" r="12" fill="#fff"/><path d="M26 36c3-8 9-9 13-6-2 7-8 9-13 6z" fill="url(#go)"/><path d="M14 8h36v5H14z" fill="#fff" opacity=".3"/></symbol>
<symbol id="scooter" viewBox="0 0 96 64"><circle cx="20" cy="50" r="10" fill="#0a2c10"/><circle cx="20" cy="50" r="4" fill="#4fc23a"/><circle cx="76" cy="50" r="10" fill="#0a2c10"/><circle cx="76" cy="50" r="4" fill="#4fc23a"/><path d="M14 40c0-8 6-10 14-10h20l8 10 14-2 6 12H70L60 40H30l-4 10H14z" fill="url(#gg)"/><path d="M62 38l-4-18h10" stroke="#0a2c10" stroke-width="4" stroke-linecap="round" fill="none"/><rect x="14" y="8" width="30" height="26" rx="5" fill="url(#gy)"/><path d="M14 16h30" stroke="#fff" stroke-width="2" opacity=".5"/><path d="M24 25l6 4 8-9" stroke="#fff" stroke-width="3.2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></symbol>
<symbol id="basket" viewBox="0 0 64 64"><path d="M16 26c0-14 8-20 16-20s16 6 16 20" stroke="#a5642c" stroke-width="4" fill="none" stroke-linecap="round"/><circle cx="24" cy="24" r="7" fill="url(#gr)"/><circle cx="36" cy="22" r="7" fill="url(#gy)"/><path d="M40 24c4 0 7 2 8 6H30c1-4 5-6 10-6z" fill="url(#gg)"/><path d="M6 28h52l-6 28a4 4 0 0 1-4 3H16a4 4 0 0 1-4-3z" fill="url(#gb)"/><path d="M20 34l2 20M32 34v20M44 34l-2 20" stroke="#8a4c1c" stroke-width="2" opacity=".6"/></symbol>
<symbol id="pin" viewBox="0 0 64 64"><ellipse cx="32" cy="58" rx="12" ry="3.5" fill="#0a2c10" opacity=".25"/><path d="M32 4C19 4 12 13 12 24c0 14 20 32 20 32s20-18 20-32C52 13 45 4 32 4z" fill="url(#gg)"/><circle cx="32" cy="24" r="9" fill="#fff"/><circle cx="32" cy="24" r="4" fill="#0c831f"/><path d="M20 14c3-4 7-6 11-6" stroke="#fff" stroke-width="3" stroke-linecap="round" fill="none" opacity=".5"/></symbol>
</svg>

<div class="blob b1"></div><div class="blob b2"></div><div class="blob b3"></div>

<main class="page">
 <header class="header-row in" style="animation-delay:.05s">
  <div class="brand"><i><svg width="22" height="22"><use href="#bag"/></svg></i>SBMart</div>
 </header>

 <section class="hero">
  <div class="copy">
   <div class="badge in" style="animation-delay:.15s"><b></b>Your Everyday Essentials, Delivered Fast</div>
   <h1 class="in" style="animation-delay:.25s">Everything You Need. <span>Delivered to Your Door.</span></h1>
   <p class="sub in" style="animation-delay:.35s">Groceries, fresh produce, dairy and daily essentials from stores near you in 10 minutes. Download the SBMart app today!</p>
   
   <div class="cta-group in" style="animation-delay:.45s">
    <a class="cta cta-apk" href="/SBMartQuick-Universal.apk" download="SBMartQuick-Universal.apk">
     <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.52 0a1.5 1.5 0 011.06.44l3.98 3.98A1.5 1.5 0 0123 5.48V21a3 3 0 01-3 3H4a3 3 0 01-3-3V3a3 3 0 013-3h13.52zM12 18a1.5 1.5 0 001.5-1.5v-6a1.5 1.5 0 00-3 0v6A1.5 1.5 0 0012 18zm-4-3a1.5 1.5 0 001.5-1.5v-3a1.5 1.5 0 00-3 0v3A1.5 1.5 0 008 15zm8 0a1.5 1.5 0 001.5-1.5v-3a1.5 1.5 0 00-3 0v3A1.5 1.5 0 0016 15z"/></svg>
     <span><small>DIRECT DOWNLOAD</small><strong>Download Android APK</strong></span>
    </a>
    <a class="cta" href="#" onclick="showPlayStoreToast(event)" aria-label="Get it on Google Play">
     <svg viewBox="0 0 38 42" aria-hidden="true"><path d="M2 2.5c-.6.6-1 1.5-1 2.7v31.6c0 1.2.4 2.1 1 2.7L19.6 21z" fill="#00d3ff"/><path d="M25.4 26.8L19.6 21l5.8-5.8 7.3 4.2c2 1.2 2 3 0 4.2z" fill="#ffd400"/><path d="M25.4 26.8L19.6 21 2 39.5c.7.7 1.8.8 3 .1z" fill="#ff3a44"/><path d="M25.4 15.2L5 2.4c-1.2-.7-2.3-.6-3 .1L19.6 21z" fill="#00f076"/></svg>
     <span><small>GET IT ON</small><strong>Google Play</strong></span>
    </a>
   </div>

   <p class="perks in" style="animation-delay:.55s">⚡ 10-Minute Express Delivery • 🥦 100% Fresh Products • 🔒 Safe Payments</p>
  </div>

  <div class="stage in" style="animation-delay:.3s">
   <div class="glow"></div>
   <div class="phone">
    <div class="screen">
     <div class="notch"></div>
     <div class="top"><div><div class="eta">Delivery in 10 mins</div><div class="loc"><svg width="11" height="11"><use href="#pin"/></svg>Krishnanagar Main Hub</div></div><div class="av"><svg width="16" height="16"><use href="#basket"/></svg></div></div>
     <div class="search">Search "milk", "apples", "chips"</div>
     <div class="promo"><b>Fresh picks, up to 30% off</b><em>Shop now</em><svg><use href="#basket"/></svg></div>
     <div class="chips"><span>All</span><span>Fruits</span><span>Dairy</span><span>Snacks</span></div>
     <div class="grid">
      <div class="p"><div><svg><use href="#apple"/></svg></div><b>Red apples</b><span>₹79 <u>+</u></span></div>
      <div class="p"><div><svg><use href="#milk"/></svg></div><b>Fresh milk 1L</b><span>₹68 <u>+</u></span></div>
      <div class="p"><div><svg><use href="#carrot"/></svg></div><b>Carrots 500g</b><span>₹34 <u>+</u></span></div>
      <div class="p"><div><svg><use href="#snack"/></svg></div><b>Crunchy chips</b><span>₹20 <u>+</u></span></div>
     </div>
     <div class="nav"><i></i><i></i><i></i><i></i></div>
    </div>
   </div>
   <svg class="f f1 glass" role="img" aria-label="Grocery bag"><use href="#bag"/></svg>
   <svg class="f f2" role="img" aria-label="Milk bottle"><use href="#milk"/></svg>
   <svg class="f f3" role="img" aria-label="Vegetables"><use href="#carrot"/></svg>
   <svg class="f f4" role="img" aria-label="Fruit"><use href="#apple"/></svg>
   <svg class="f f5" role="img" aria-label="Snacks"><use href="#snack"/></svg>
   <svg class="f f6 glass" viewBox="0 0 96 64" role="img" aria-label="Delivery scooter"><use href="#scooter"/></svg>
   <svg class="f f7" role="img" aria-label="Shopping basket"><use href="#basket"/></svg>
   <svg class="f f8" role="img" aria-label="Location pin"><use href="#pin"/></svg>
  </div>
 </section>
</main>
</body>
</html>
