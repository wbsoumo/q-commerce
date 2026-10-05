<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>{{ $title ?? 'SonarbanglaMart Information' }}</title>
<link rel="icon" type="image/png" href="/sbmart.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,700;12..96,800&display=swap" rel="stylesheet">
<style>
:root{--g:#0c831f;--g2:#096517;--gd:#0a2c10;--gt:#2b5e34;--gl:#eaf8ed;--w:#fff}
*,*::before,*::after{box-sizing:border-box;margin:0}
body{min-height:100vh;font-family:'Bricolage Grotesque',system-ui,-apple-system,sans-serif;color:var(--gd);background:linear-gradient(135deg,#fff 0%,#f0faf1 55%,#dcf4e1 100%);padding:24px 16px;line-height:1.6}
.container{max-width:840px;margin:0 auto;background:#fff;border-radius:24px;padding:36px;box-shadow:0 20px 40px -12px rgba(12,131,31,.18);border:1px solid #e3f6da}
.header{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;padding-bottom:16px;border-bottom:2px solid var(--gl)}
.brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:24px;color:var(--gd);text-decoration:none}
.brand i{width:36px;height:36px;border-radius:10px;background:linear-gradient(145deg,var(--g),var(--g2));display:grid;place-items:center;color:#fff;font-weight:bold;font-style:normal}
h1{font-size:28px;font-weight:800;color:var(--gd);margin-bottom:20px}
h2{font-size:18px;font-weight:700;color:var(--g);margin:24px 0 10px}
p{color:#333;font-size:15px;margin-bottom:14px}
ul{margin:10px 0 18px 24px;color:#333;font-size:15px}
li{margin-bottom:8px}
.footer{margin-top:36px;padding-top:20px;border-top:1px solid #eee;font-size:13px;color:#777;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px}
.footer a{color:var(--g);text-decoration:none;font-weight:600}
</style>
</head>
<body>
<div class="container">
 <div class="header">
  <a href="/" class="brand"><i>SB</i>SonarbanglaMart</a>
 </div>

 @if($page == 'about-us')
  <h1>About Us</h1>
  <p>Welcome to <strong>SonarbanglaMart</strong> – your premier quick-commerce grocery delivery service delivering fresh groceries, everyday essentials, dairy, produce, and household items straight to your doorstep in 10-16 minutes.</p>
  <h2>Our Mission</h2>
  <p>Our goal is to redefine convenience for everyday households across Bengal and India by providing lightning-fast deliveries powered by hyper-local micro-fulfillment centers (dark stores).</p>
  <h2>Why Choose SonarbanglaMart?</h2>
  <ul>
   <li><strong>Express 10-16 Mins Delivery:</strong> Automated store dispatch ensuring record-speed delivery.</li>
   <li><strong>100% Quality & Freshness:</strong> Direct sourcing from local farms, trusted vendors, and brand partners.</li>
   <li><strong>Unmatched Variety:</strong> Over 10,000+ products across vegetables, fruits, packaged foods, personal care, and home care.</li>
  </ul>
 @elseif($page == 'terms')
  <h1>Terms & Conditions</h1>
  <p>Last updated: October 2026</p>
  <p>Please read these Terms & Conditions carefully before using the SonarbanglaMart mobile application or website operated by SonarbanglaMart Quick Commerce Private Limited.</p>
  <h2>1. Account & Use</h2>
  <p>By creating an account or placing an order on SonarbanglaMart, you represent that you are at least 18 years of age and agree to comply with all local laws and regulations.</p>
  <h2>2. Orders & Delivery</h2>
  <p>All orders are subject to stock availability and address delivery radius verification. Orders are fulfilled via Cash on Delivery (COD) or electronic wallet credits.</p>
  <h2>3. Pricing & Discounts</h2>
  <p>Prices listed in the app reflect current local store availability and applicable Taxes (GST). Promotional offers and coupons are non-transferable and subject to terms.</p>
 @elseif($page == 'privacy')
  <h1>Privacy Policy</h1>
  <p>Last updated: October 2026</p>
  <p>At SonarbanglaMart, accessible from https://sbmartquick.com, protecting user privacy and safeguarding personal data is our highest priority.</p>
  <h2>Information We Collect</h2>
  <ul>
   <li><strong>Account & Contact Info:</strong> Mobile phone number, user name, and delivery address.</li>
   <li><strong>Location Data:</strong> Device GPS coordinates (latitude & longitude) collected while using the app to calculate nearest store eligibility and serviceability.</li>
   <li><strong>Order History:</strong> Product items, wishlist, cart details, and order transactions.</li>
  </ul>
  <h2>How We Use Your Data</h2>
  <p>We use collected information solely to process orders, assign delivery riders, calculate store availability, send order notifications via Firebase FCM, and improve customer experience.</p>
  <h2>Data Deletion Request</h2>
  <p>Users can request complete account and data deletion at any time by contacting support at support@sbmartquick.com or using the in-app deletion option.</p>
 @elseif($page == 'shopping')
  <h1>Shopping Policy</h1>
  <p>Learn about our order placement, fulfillment, and delivery standards at SonarbanglaMart.</p>
  <h2>Order Placement</h2>
  <p>Orders can be placed 24/7 through our Flutter Android application. Real-time stock reservation ensures items added to your cart are allocated for your delivery slot.</p>
  <h2>Minimum Order Value & Delivery Fees</h2>
  <p>Standard delivery charges are calculated dynamically based on store distance. Minimum order requirements, if applicable, are shown clearly at checkout prior to order confirmation.</p>
 @elseif($page == 'refund')
  <h1>Refund & Cancellation Policy</h1>
  <p>We take immense pride in quality, but if you are dissatisfied with an item, our hassle-free return and refund policy has you covered.</p>
  <h2>Order Cancellation</h2>
  <p>You can cancel your order directly from the app order status screen before it is picked up by a delivery partner.</p>
  <h2>Return & Instant Refund</h2>
  <p>Damaged, missing, or unsatisfactory items reported at the time of delivery will be refunded instantly to your SB Mart Wallet balance or cash adjustment.</p>
 @endif

 <div class="footer">
  <span>&copy; {{ date('Y') }} SonarbanglaMart. All rights reserved.</span>
  <span>Contact: <a href="mailto:support@sbmartquick.com">support@sbmartquick.com</a></span>
 </div>
</div>
</body>
</html>
