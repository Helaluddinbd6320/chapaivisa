<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Go To Login </title>
  <style>
    :root{
      --bg-1: #0f172a; /* dark navy */
      --bg-2: #0b1220;
      --card: rgba(255,255,255,0.03);
      --accent: #06b6d4; /* teal/cyan */
      --accent-2: #7c3aed; /* purple */
      --glass: rgba(255,255,255,0.06);
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: radial-gradient(1200px 600px at 10% 20%, rgba(124,58,237,0.12), transparent),
                  radial-gradient(1000px 500px at 90% 80%, rgba(6,182,212,0.08), transparent),
                  linear-gradient(180deg,var(--bg-1),var(--bg-2));
      color: #e6eef8;
      display:grid;
      place-items:center;
      padding:32px;
    }

    .card{
      width:100%;
      max-width:540px;
      border-radius:16px;
      background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
      border:1px solid rgba(255,255,255,0.04);
      box-shadow: 0 10px 30px rgba(2,6,23,0.6), inset 0 1px 0 rgba(255,255,255,0.02);
      padding:48px 36px;
      text-align:center;
    }

    .title{
      font-size:20px;
      margin:0 0 8px 0;
      color:#f1f5f9;
      letter-spacing:0.2px;
    }
    .subtitle{color:rgba(226,232,240,0.7); margin:0 0 28px 0}

    .btn{
      display:inline-flex;
      align-items:center;
      gap:12px;
      padding:14px 28px;
      font-size:16px;
      font-weight:600;
      color: white;
      background: linear-gradient(90deg,var(--accent), var(--accent-2));
      border-radius:12px;
      border: 1px solid rgba(255,255,255,0.12);
      box-shadow: 0 8px 30px rgba(7,10,25,0.6), 0 2px 0 rgba(255,255,255,0.02) inset;
      text-decoration:none;
      transition: transform .18s cubic-bezier(.2,.9,.2,1), box-shadow .18s, opacity .12s;
      will-change: transform, box-shadow;
    }
    .btn:focus{outline:none; box-shadow: 0 0 0 4px rgba(6,182,212,0.12), 0 12px 40px rgba(7,10,25,0.6)}
    .btn:hover{transform: translateY(-3px);}
    .btn:active{transform: translateY(-1px) scale(.995)}

    .btn .arrow{
      display:inline-grid;
      place-items:center;
      width:34px;height:34px;border-radius:8px;
      background: rgba(255,255,255,0.06);
      border:1px solid rgba(255,255,255,0.04);
      box-shadow: 0 4px 12px rgba(2,6,23,0.5);
      transition: background .18s, transform .18s;
    }
    .btn:hover .arrow{transform: translateX(4px)}

    .muted{font-size:13px;color:rgba(226,232,240,0.55); margin-top:18px}

    /* small screens tweak */
    @media (max-width:420px){
      .card{padding:28px 20px}
      .btn{width:100%; justify-content:center}
    }
  </style>
</head>
<body>
  <main class="card" role="main">
    <h1 class="title">Welcome back</h1>
    <p class="subtitle">Click the button below to continue to the login page.</p>

    <a class="btn" href="/admin" role="button" aria-label="Go To Login">
      <span>Go To Login</span>
      <span class="arrow" aria-hidden="true">➜</span>
    </a>

    <p class="muted">Need help? <a href="https://wa.me/8801841484885?text=Login%20করতে%20প্রবলেম%20হচ্ছে.%20দয়া%20করে%20সহায়তা%20করুন.%20Website%20URL:%20https://your-website.com" style="color:inherit;text-decoration:underline;" target="_blank">Contact support on WhatsApp</a> — and say Hi to  Helal Uddin ❤️</p>
  </main>
</body>
</html>
