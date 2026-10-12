<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Request Account & Data Deletion – SB Mart Quick</title>
<link rel="icon" type="image/png" href="/sbmart.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700;12..96,800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --g:#0c831f;
  --g-dark:#085b15;
  --g-light:#eaf8ed;
  --text-dark:#111827;
  --text-muted:#4b5563;
  --border:#e5e7eb;
  --card-bg:#ffffff;
  --danger:#dc2626;
  --danger-light:#fef2f2;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{
  min-height:100vh;
  font-family:'Plus Jakarta Sans',sans-serif;
  color:var(--text-dark);
  background:linear-gradient(135deg,#ffffff 0%,#f0faf1 50%,#e1f5e5 100%);
  padding:32px 16px;
  line-height:1.6;
}
.container{
  max-width:760px;
  margin:0 auto;
  background:var(--card-bg);
  border-radius:24px;
  padding:40px;
  box-shadow:0 20px 45px -10px rgba(12,131,31,0.15);
  border:1px solid #d4eed8;
}
.header{
  display:flex;
  align-items:center;
  justify-content:space-between;
  margin-bottom:30px;
  padding-bottom:18px;
  border-bottom:2px solid var(--g-light);
}
.brand{
  display:flex;
  align-items:center;
  gap:12px;
  font-family:'Bricolage Grotesque',sans-serif;
  font-weight:800;
  font-size:22px;
  color:var(--text-dark);
  text-decoration:none;
}
.brand img{
  width:42px;
  height:42px;
  border-radius:10px;
  object-fit:cover;
}
.badge{
  font-size:12px;
  font-weight:700;
  color:var(--g);
  background:var(--g-light);
  padding:4px 12px;
  border-radius:999px;
  text-transform:uppercase;
  letter-spacing:0.5px;
}
h1{
  font-family:'Bricolage Grotesque',sans-serif;
  font-size:28px;
  font-weight:800;
  color:var(--text-dark);
  margin-bottom:12px;
  letter-spacing:-0.5px;
}
p{
  color:var(--text-muted);
  font-size:15px;
  margin-bottom:16px;
}
.alert-success{
  background:#ecfdf5;
  border:1px solid #a7f3d0;
  border-left:5px solid #059669;
  border-radius:14px;
  padding:20px;
  margin-bottom:28px;
}
.alert-success h3{
  color:#065f46;
  font-size:17px;
  font-weight:700;
  margin-bottom:6px;
}
.alert-success p{
  color:#047857;
  font-size:14px;
  margin:0;
}
.alert-box{
  background:var(--danger-light);
  border:1px solid #fecaca;
  border-left:4px solid var(--danger);
  border-radius:12px;
  padding:16px;
  margin-bottom:26px;
  font-size:14px;
  color:#991b1b;
}
.alert-box strong{
  font-weight:700;
  display:block;
  margin-bottom:4px;
}
.form-card{
  background:#fafafa;
  border:1px solid var(--border);
  border-radius:18px;
  padding:28px;
  margin-bottom:32px;
}
.form-group{
  margin-bottom:20px;
}
label{
  display:block;
  font-size:14px;
  font-weight:600;
  color:var(--text-dark);
  margin-bottom:8px;
}
label .req{
  color:var(--danger);
}
input[type="text"], input[type="tel"], input[type="email"], select, textarea{
  width:100%;
  padding:12px 16px;
  border:1.5px solid var(--border);
  border-radius:12px;
  font-size:15px;
  font-family:inherit;
  color:var(--text-dark);
  background:#fff;
  transition:all 0.2s ease;
}
input:focus, select:focus, textarea:focus{
  outline:none;
  border-color:var(--g);
  box-shadow:0 0 0 4px rgba(12,131,31,0.12);
}
.checkbox-label{
  display:flex;
  align-items:flex-start;
  gap:10px;
  font-size:13.5px;
  color:var(--text-muted);
  cursor:pointer;
  margin-top:14px;
}
.checkbox-label input{
  margin-top:3px;
  width:16px;
  height:16px;
  accent-color:var(--g);
}
.btn-submit{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:8px;
  width:100%;
  padding:14px 24px;
  font-family:'Bricolage Grotesque',sans-serif;
  font-size:16px;
  font-weight:700;
  color:#fff;
  background:linear-gradient(135deg,var(--g) 0%,var(--g-dark) 100%);
  border:none;
  border-radius:12px;
  cursor:pointer;
  transition:transform 0.15s ease, box-shadow 0.2s ease;
  box-shadow:0 8px 20px -4px rgba(12,131,31,0.35);
  margin-top:16px;
}
.btn-submit:hover{
  transform:translateY(-1px);
  box-shadow:0 12px 24px -4px rgba(12,131,31,0.45);
}
.info-section{
  margin-top:30px;
  padding-top:24px;
  border-top:1px solid var(--border);
}
.info-section h2{
  font-family:'Bricolage Grotesque',sans-serif;
  font-size:19px;
  font-weight:700;
  color:var(--text-dark);
  margin-bottom:12px;
}
.info-section ul{
  margin:10px 0 16px 20px;
  font-size:14.5px;
  color:var(--text-muted);
}
.info-section li{
  margin-bottom:8px;
}
.footer{
  margin-top:40px;
  padding-top:20px;
  border-top:1px solid #edf2f7;
  font-size:13px;
  color:#6b7280;
  display:flex;
  justify-content:space-between;
  align-items:center;
  flex-wrap:wrap;
  gap:12px;
}
.footer a{
  color:var(--g);
  text-decoration:none;
  font-weight:600;
}
.footer a:hover{
  text-decoration:underline;
}
@media(max-width:640px){
  .container{padding:24px 18px;border-radius:18px}
  .form-card{padding:20px 16px}
  h1{font-size:24px}
}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <a href="/" class="brand">
      <img src="/sbmart.png" alt="SB Mart Quick Logo">
      <span>SB Mart Quick</span>
    </a>
    <span class="badge">Account Deletion</span>
  </div>

  <h1>Request Account & Data Deletion</h1>
  <p>In accordance with Google Play Developer Policy and user privacy standards, <strong>SB Mart Quick (SonarbanglaMart)</strong> provides this public portal for registered users to submit a formal request to delete their user account and associated personal data.</p>

  @if(session('success'))
    <div class="alert-success">
      <h3>Request Submitted Successfully</h3>
      <p>{{ session('success') }}</p>
      @if(session('ticket_number'))
        <p style="margin-top:8px;font-weight:700;">Reference Ticket ID: <code>{{ session('ticket_number') }}</code></p>
      @endif
    </div>
  @endif

  @if($errors->any())
    <div class="alert-box">
      <strong>Please correct the following:</strong>
      <ul style="margin-left:18px;margin-top:6px;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="alert-box">
    <strong>Notice regarding account deletion:</strong>
    Deleting your account will permanently remove your login credentials, saved delivery addresses, wishlist items, and wallet information. This action is irreversible once processed.
  </div>

  <div class="form-card">
    <form action="/delete-account" method="POST">
      @csrf
      <div class="form-group">
        <label for="user_name">Full Name <span class="req">*</span></label>
        <input type="text" id="user_name" name="user_name" value="{{ old('user_name') }}" placeholder="Enter your full name" required>
      </div>

      <div class="form-group">
        <label for="user_phone">Registered Mobile Phone Number <span class="req">*</span></label>
        <input type="tel" id="user_phone" name="user_phone" value="{{ old('user_phone') }}" placeholder="Enter 10-digit registered mobile number (e.g., 9876543210)" required>
      </div>

      <div class="form-group">
        <label for="user_email">Email Address (Optional)</label>
        <input type="email" id="user_email" name="user_email" value="{{ old('user_email') }}" placeholder="user@example.com">
      </div>

      <div class="form-group">
        <label for="reason">Reason for Account Deletion</label>
        <select id="reason" name="reason">
          <option value="No longer using the service">I no longer need or use the service</option>
          <option value="Moved to another area">Relocated / Not within delivery zone</option>
          <option value="Privacy concerns">Privacy and personal data concerns</option>
          <option value="Created by mistake">Created account by mistake</option>
          <option value="Other">Other reason</option>
        </select>
      </div>

      <div class="form-group">
        <label for="comments">Additional Remarks (Optional)</label>
        <textarea id="comments" name="comments" rows="3" placeholder="Tell us if there is anything we can assist you with before deleting your account...">{{ old('comments') }}</textarea>
      </div>

      <label class="checkbox-label">
        <input type="checkbox" name="confirmation" value="1" required>
        <span>I understand that submitting this request will initiate the permanent deletion of my SB Mart Quick account and personal data.</span>
      </label>

      <button type="submit" class="btn-submit">
        Submit Deletion Request
      </button>
    </form>
  </div>

  <div class="info-section">
    <h2>1. What Data Will Be Deleted?</h2>
    <p>Upon verification of your request by our support team, the following data associated with your registered account will be permanently erased from our active databases:</p>
    <ul>
      <li><strong>User Profile:</strong> Name, phone number, email address, and encrypted authentication password.</li>
      <li><strong>Saved Addresses:</strong> All saved delivery addresses, door numbers, landmarks, and GPS pin locations.</li>
      <li><strong>Preferences:</strong> Wishlist items, cart state, and notification settings.</li>
      <li><strong>Device Identifiers:</strong> Firebase Push Notification (FCM) tokens and session tokens.</li>
    </ul>

    <h2>2. What Data Is Retained and Why?</h2>
    <p>In accordance with statutory Indian commercial laws, GST tax compliance, and auditing regulations:</p>
    <ul>
      <li><strong>Financial & Invoice Records:</strong> Historical tax invoices and order transaction receipts for past fulfilled orders are retained solely for mandatory statutory accounting, tax reporting, and dispute-resolution compliance. These records are archived securely and never utilized for marketing or operational profiling.</li>
    </ul>

    <h2>3. Processing Timeframe</h2>
    <p>Account deletion requests submitted through this web portal or via email are reviewed and processed within <strong>24 to 48 hours</strong> (or at most 7 business days). Once completed, a confirmation notice will be dispatched to your registered phone number or email address.</p>

    <h2>4. Alternative In-App Deletion Method</h2>
    <p>You can also initiate instant account deletion directly inside the <strong>SB Mart Quick</strong> mobile app by navigating to:</p>
    <p><em>Profile &rarr; Account Privacy & Settings &rarr; Delete Account</em>.</p>
  </div>

  <div class="footer">
    <span>&copy; {{ date('Y') }} SB Mart Quick (SonarbanglaMart). All rights reserved.</span>
    <span>Support: <a href="mailto:support@sbmartquick.com">support@sbmartquick.com</a></span>
  </div>
</div>
</body>
</html>
