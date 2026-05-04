<?php
session_start();

$errors  = [];
$success = '';
$form    = []; // repopulate on error

// ---------- HANDLE REGISTRATION ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $form = [
        'name'  => trim($_POST['name']  ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'dob'   => $_POST['dob']   ?? '',
        'plan'  => $_POST['plan']  ?? 'Bronze',
    ];

    // — Validation —
    if (empty($form['name']))
        $errors[] = 'Full name is required.';

    if (empty($form['email']))
        $errors[] = 'Email is required.';
    elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL))
        $errors[] = 'Please enter a valid email address.';

    if (!empty($form['phone']) && !preg_match('/^\+?[\d\s\-]{7,15}$/', $form['phone']))
        $errors[] = 'Phone number format is invalid.';

    if (!empty($form['dob'])) {
        $dob_ts = strtotime($form['dob']);
        $age    = (int) floor((time() - $dob_ts) / 31557600);
        if ($age < 16) $errors[] = 'You must be at least 16 years old to register.';
    }

    if (!in_array($form['plan'], ['Bronze', 'Silver', 'Gold']))
        $errors[] = 'Please select a valid plan.';

    // — If valid, "save" (replace with real DB insert in production) —
    if (empty($errors)) {
        /*
         * PRODUCTION EXAMPLE (PDO):
         * $stmt = $pdo->prepare("INSERT INTO members (name,email,phone,dob,plan,created_at)
         *                        VALUES (?,?,?,?,?,NOW())");
         * $stmt->execute([$form['name'],$form['email'],$form['phone'],$form['dob'],$form['plan']]);
         */
        $success = "Welcome, " . htmlspecialchars($form['name']) . "! You've been registered on the <strong>" . htmlspecialchars($form['plan']) . "</strong> plan. Check your email for confirmation.";
        $form = []; // clear form after success
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Membership – IronPulse</title>
  <link rel="stylesheet" href="styleIndex2.css" />
  <style>
    /* plan cards with price tags */
    .plan-card { position:relative; cursor:pointer; border:2px solid transparent; transition:.25s; }
    .plan-card:hover, .plan-card.selected { border-color:var(--accent); transform:translateY(-8px); }
    .plan-card .price { font-size:1.6rem; font-weight:700; color:var(--accent); margin:10px 0 6px; }
    .plan-card ul { text-align:left; padding-left:10px; }
    .plan-card ul li { list-style:disc inside; margin:4px 0; color:#ccc; font-size:.9rem; }
    .plan-badge {
      position:absolute; top:-12px; left:50%; transform:translateX(-50%);
      background:var(--accent); color:#fff; font-size:.72rem; font-weight:700;
      padding:3px 12px; border-radius:20px; letter-spacing:1px; white-space:nowrap;
    }

    /* form enhancements */
    .reg-form { max-width:560px; }
    .form-row  { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .field     { display:flex; flex-direction:column; margin-bottom:14px; }
    .field label{ font-size:.85rem; color:#bbb; margin-bottom:4px; }
    .field input, .field select{ margin:0; }
    .field select option{ background:var(--card-bg); }

    /* alerts */
    .alert { padding:14px 18px; border-radius:8px; margin-bottom:20px; font-size:.9rem; line-height:1.6; }
    .alert-error   { background:rgba(225,29,72,.15); border:1px solid var(--accent); color:#f87171; }
    .alert-success { background:rgba(34,197,94,.15); border:1px solid #22c55e;      color:#4ade80; }
    .alert ul{ margin:6px 0 0 16px; }

    .submit-btn{ width:100%; padding:13px; font-size:1.05rem; margin-top:6px; }
  </style>
</head>
<body>

<header>
  <nav>
    <h2>IronPulse</h2>
    <ul>
      <li><a href="index2.html">Home</a></li>
      <li><a href="classes.html">Classes</a></li>
      <li><a href="membership.php" class="active">Membership</a></li>
      <li><a href="trainers.html">Trainers</a></li>
      <li><a href="contact.html">Contact</a></li>
      <li><a href="login.php">Login</a></li>
    </ul>
  </nav>
</header>

<!-- ── Plan Cards ── -->
<section>
  <h2 class="section-title">Choose Your Plan</h2>
  <div class="grid">

    <div class="card plan-card" onclick="selectPlan('Bronze')">
      <h3>Bronze</h3>
      <p class="price">$29<span style="font-size:.9rem;font-weight:400">/mo</span></p>
      <ul>
        <li>Full gym access</li>
        <li>Locker room</li>
        <li>Fitness assessment</li>
      </ul>
    </div>

    <div class="card plan-card" onclick="selectPlan('Silver')" style="position:relative">
      <span class="plan-badge">POPULAR</span>
      <h3>Silver</h3>
      <p class="price">$49<span style="font-size:.9rem;font-weight:400">/mo</span></p>
      <ul>
        <li>Everything in Bronze</li>
        <li>All group classes</li>
        <li>Nutrition guide</li>
      </ul>
    </div>

    <div class="card plan-card" onclick="selectPlan('Gold')">
      <h3>Gold</h3>
      <p class="price">$79<span style="font-size:.9rem;font-weight:400">/mo</span></p>
      <ul>
        <li>Everything in Silver</li>
        <li>Personal trainer (4×/mo)</li>
        <li>Priority booking</li>
        <li>Guest passes</li>
      </ul>
    </div>

  </div>
</section>

<!-- ── Registration Form ── -->
<section>
  <h2 class="section-title">Register</h2>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-error" style="max-width:560px;margin:0 auto 20px;">
      <strong>Please fix the following:</strong>
      <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert alert-success" style="max-width:560px;margin:0 auto 20px;">
      &#10003; <?= $success ?>
    </div>
  <?php endif; ?>

  <form class="reg-form" method="POST" action="membership.php">

    <div class="form-row">
      <div class="field">
        <label for="name">Full Name <span style="color:var(--accent)">*</span></label>
        <input type="text" id="name" name="name"
               value="<?= htmlspecialchars($form['name'] ?? '') ?>"
               placeholder="Ali Amine" />
      </div>
      <div class="field">
        <label for="email">Email <span style="color:var(--accent)">*</span></label>
        <input type="email" id="email" name="email"
               value="<?= htmlspecialchars($form['email'] ?? '') ?>"
               placeholder="you@example.com" />
      </div>
    </div>

    <div class="form-row">
      <div class="field">
        <label for="phone">Phone</label>
        <input type="tel" id="phone" name="phone"
               value="<?= htmlspecialchars($form['phone'] ?? '') ?>"
               placeholder="(555) 123-4567" />
      </div>
      <div class="field">
        <label for="dob">Date of Birth</label>
        <input type="date" id="dob" name="dob"
               value="<?= htmlspecialchars($form['dob'] ?? '') ?>" />
      </div>
    </div>

    <div class="field">
      <label for="plan">Membership Plan <span style="color:var(--accent)">*</span></label>
      <select id="plan" name="plan">
        <?php foreach (['Bronze','Silver','Gold'] as $p): ?>
          <option value="<?= $p ?>" <?= (($form['plan'] ?? 'Bronze') === $p) ? 'selected' : '' ?>><?= $p ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <button type="submit" class="submit-btn">Register Now</button>
  </form>
</section>

<footer>
  <p>&copy; IronPulse Gym | Open 6AM – 11PM</p>
  <div>
    <h3>Contact Info</h3>
    <ul>
      <li>123 Fitness Street</li>
      <li>(555) 123-4567</li>
      <li>info@ironPulse.com</li>
    </ul>
  </div>
</footer>

<script>
function selectPlan(plan) {
  document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
  event.currentTarget.classList.add('selected');
  document.getElementById('plan').value = plan;
}
// Highlight the currently selected plan on page load
(function(){
  const val = document.getElementById('plan').value;
  document.querySelectorAll('.plan-card').forEach(c => {
    if (c.querySelector('h3').textContent === val) c.classList.add('selected');
  });
})();
</script>
</body>
</html>
