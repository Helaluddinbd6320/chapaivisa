<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Go To Login</title>
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
      overflow:hidden;
      position:relative;
    }

    /* Animated background particles */
    .particles-container {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: -1;
      overflow: hidden;
    }

    .particle {
      position: absolute;
      border-radius: 50%;
      background: linear-gradient(90deg, var(--accent), var(--accent-2));
      opacity: 0;
      animation: floatParticle 20s infinite linear;
    }

    /* Pulse animation around the card */
    .card-pulse {
      position: absolute;
      width: 100%;
      height: 100%;
      border-radius: 20px;
      background: transparent;
      border: 1px solid transparent;
      animation: pulseRing 4s cubic-bezier(0.4, 0, 0.2, 1) infinite;
      z-index: -1;
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
      position:relative;
      z-index: 1;
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      transform: translateY(0);
      transition: transform 0.8s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.8s;
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 25px 50px rgba(2,6,23,0.8), inset 0 1px 0 rgba(255,255,255,0.02);
    }

    .title{
      font-size:28px;
      font-weight:700;
      margin:0 0 12px 0;
      color:#f1f5f9;
      letter-spacing:0.2px;
      background: linear-gradient(90deg, var(--accent), var(--accent-2));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: gradientShift 3s ease infinite;
      background-size: 200% auto;
    }
    
    .subtitle{
      color:rgba(226,232,240,0.7); 
      margin:0 0 28px 0;
      font-size:16px;
      line-height:1.6;
      animation: fadeInUp 0.8s ease 0.2s both;
    }

    .btn{
      display:inline-flex;
      align-items:center;
      gap:12px;
      padding:16px 32px;
      font-size:16px;
      font-weight:600;
      color: white;
      background: linear-gradient(90deg,var(--accent), var(--accent-2));
      border-radius:12px;
      border: 1px solid rgba(255,255,255,0.12);
      box-shadow: 0 8px 30px rgba(7,10,25,0.6), 0 2px 0 rgba(255,255,255,0.02) inset;
      text-decoration:none;
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
      will-change: transform, box-shadow;
      position:relative;
      overflow:hidden;
      animation: fadeInUp 0.8s ease 0.4s both;
    }

    .btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.7s ease;
    }

    .btn:hover::before {
      left: 100%;
    }

    .btn:focus{outline:none; box-shadow: 0 0 0 4px rgba(6,182,212,0.12), 0 12px 40px rgba(7,10,25,0.6)}
    .btn:hover{
      transform: translateY(-6px) scale(1.02);
      box-shadow: 0 20px 40px rgba(7,10,25,0.8), 0 2px 0 rgba(255,255,255,0.02) inset;
    }
    .btn:active{transform: translateY(-2px) scale(0.98)}

    .btn .arrow{
      display:inline-grid;
      place-items:center;
      width:34px;height:34px;border-radius:8px;
      background: rgba(255,255,255,0.06);
      border:1px solid rgba(255,255,255,0.04);
      box-shadow: 0 4px 12px rgba(2,6,23,0.5);
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
      animation: arrowFloat 2s ease-in-out infinite;
    }
    .btn:hover .arrow{
      transform: translateX(6px) scale(1.1);
      background: rgba(255,255,255,0.1);
    }

    .muted{
      font-size:13px;
      color:rgba(226,232,240,0.55); 
      margin-top:24px;
      animation: fadeInUp 0.8s ease 0.6s both;
    }

    /* Glowing orbs */
    .orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(40px);
      opacity: 0.3;
      z-index: -1;
    }
    
    .orb-1 {
      width: 300px;
      height: 300px;
      background: var(--accent);
      top: -150px;
      left: -150px;
      animation: orbFloat 20s ease-in-out infinite;
    }
    
    .orb-2 {
      width: 200px;
      height: 200px;
      background: var(--accent-2);
      bottom: -100px;
      right: -100px;
      animation: orbFloat 25s ease-in-out infinite reverse;
    }

    /* Animations */
    @keyframes floatParticle {
      0% {
        transform: translateY(100vh) rotate(0deg);
        opacity: 0;
      }
      10% {
        opacity: 0.3;
      }
      90% {
        opacity: 0.3;
      }
      100% {
        transform: translateY(-100px) rotate(720deg);
        opacity: 0;
      }
    }

    @keyframes pulseRing {
      0% {
        transform: scale(1);
        opacity: 0.5;
        border-color: rgba(6, 182, 212, 0.1);
      }
      50% {
        transform: scale(1.05);
        opacity: 0.8;
        border-color: rgba(124, 58, 237, 0.2);
      }
      100% {
        transform: scale(1);
        opacity: 0.5;
        border-color: rgba(6, 182, 212, 0.1);
      }
    }

    @keyframes gradientShift {
      0%, 100% {
        background-position: 0% 50%;
      }
      50% {
        background-position: 100% 50%;
      }
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes arrowFloat {
      0%, 100% {
        transform: translateX(0);
      }
      50% {
        transform: translateX(2px);
      }
    }

    @keyframes orbFloat {
      0%, 100% {
        transform: translate(0, 0) scale(1);
      }
      33% {
        transform: translate(30px, -30px) scale(1.1);
      }
      66% {
        transform: translate(-20px, 20px) scale(0.9);
      }
    }

    /* Loading shimmer for button */
    .shimmer {
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.1),
        transparent
      );
      animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
      0% {
        left: -100%;
      }
      100% {
        left: 100%;
      }
    }

    /* small screens tweak */
    @media (max-width:420px){
      .card{
        padding:28px 20px;
        transform: none !important;
      }
      .card:hover{
        transform: translateY(-4px) !important;
      }
      .btn{
        width:100%; 
        justify-content:center;
      }
      .title{
        font-size:24px;
      }
      .orb-1, .orb-2 {
        display: none;
      }
    }
  </style>
</head>
<body>
  <!-- Animated background particles -->
  <div class="particles-container" id="particles"></div>
  
  <!-- Glowing orbs -->
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  
  <!-- Pulse effect -->
  <div class="card-pulse"></div>

  <main class="card" role="main">
    <h1 class="title">Welcome back</h1>
    <p class="subtitle">Click the button below to continue to the login page.</p>

    <a class="btn" href="/admin" role="button" aria-label="Go To Login">
      <span>Go To Login</span>
      <span class="arrow" aria-hidden="true">➜</span>
    </a>

    <p class="muted">Need help? <a href="https://wa.me/8801841484885?text=Login%20করতে%20প্রবলেম%20হচ্ছে.%20দয়া%20করে%20সহায়তা%20করুন.%20Website%20URL:%20https://chapaivisa.com/" style="color:inherit;text-decoration:underline;" target="_blank">Contact support on WhatsApp</a> — and say Hi to Helal Uddin ❤️</p>
  </main>

  <script>
    // Create floating particles
    document.addEventListener('DOMContentLoaded', function() {
      const particlesContainer = document.getElementById('particles');
      const particleCount = 15;
      
      for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        // Random properties
        const size = Math.random() * 6 + 2;
        const left = Math.random() * 100;
        const delay = Math.random() * 20;
        const duration = Math.random() * 10 + 20;
        
        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        particle.style.left = `${left}vw`;
        particle.style.animationDelay = `${delay}s`;
        particle.style.animationDuration = `${duration}s`;
        
        particlesContainer.appendChild(particle);
      }

      // Add shimmer effect to button
      const btn = document.querySelector('.btn');
      const shimmer = document.createElement('div');
      shimmer.className = 'shimmer';
      btn.appendChild(shimmer);

      // Add subtle card entrance animation
      const card = document.querySelector('.card');
      setTimeout(() => {
        card.style.opacity = '1';
      }, 100);
    });
  </script>
</body>
</html>