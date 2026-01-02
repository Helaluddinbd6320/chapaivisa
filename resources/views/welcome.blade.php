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
      --accent-3: #ec4899; /* pink */
      --accent-4: #10b981; /* emerald */
      --accent-5: #f59e0b; /* amber */
      --accent-6: #8b5cf6; /* violet */
      --bird-white: #e0f2fe;
      --bird-gray: #94a3b8;
      --bird-black: #1e293b;
      --bird-red: #ef4444;
      --bird-blue: #3b82f6;
      --bird-yellow: #fbbf24;
      --bird-green: #10b981;
      --bird-orange: #f97316;
      --bird-pink: #ec4899;
      --bird-purple: #8b5cf6;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: radial-gradient(1200px 600px at 10% 20%, rgba(124,58,237,0.15), transparent),
                  radial-gradient(1000px 500px at 90% 80%, rgba(6,182,212,0.12), transparent),
                  radial-gradient(800px 400px at 50% 50%, rgba(236,72,153,0.08), transparent),
                  linear-gradient(180deg,var(--bg-1),var(--bg-2));
      color: #e6eef8;
      display:grid;
      place-items:center;
      padding:32px;
      overflow:hidden;
      position:relative;
      cursor: none;
    }

    /* Custom cursor */
    .mouse-cursor {
      position: fixed;
      width: 60px;
      height: 60px;
      background: radial-gradient(circle, rgba(6,182,212,0.3) 0%, transparent 70%);
      border-radius: 50%;
      pointer-events: none;
      z-index: 9999;
      transform: translate(-50%, -50%);
      transition: transform 0.1s;
      mix-blend-mode: screen;
      animation: cursorPulse 2s ease-in-out infinite;
    }

    .mouse-cursor::before {
      content: '✨';
      position: absolute;
      font-size: 24px;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      animation: cursorFloat 1.5s ease-in-out infinite;
    }

    .mouse-cursor::after {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      border: 2px solid rgba(6,182,212,0.3);
      border-radius: 50%;
      animation: cursorRipple 1.5s ease-out infinite;
    }

    /* Flying creatures container */
    .flying-creatures {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: -1;
      overflow: hidden;
      pointer-events: none;
    }

    /* Butterfly styles - 50 butterflies */
    .butterfly {
      position: absolute;
      width: 40px;
      height: 40px;
      filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
      opacity: 0.8;
      z-index: 0;
      pointer-events: none;
      animation: butterflyRandomMove 30s linear infinite;
    }

    .butterfly .left-wing,
    .butterfly .right-wing {
      position: absolute;
      width: 20px;
      height: 28px;
      border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
      transform-origin: center;
      background: var(--wing-color, var(--accent));
    }

    .butterfly .left-wing {
      left: 0;
      background: radial-gradient(circle at 70% 30%, var(--wing-color, var(--accent)), color-mix(in srgb, var(--wing-color, var(--accent)) 80%, transparent));
      animation: butterflyFlapLeft 0.6s ease-in-out infinite;
    }

    .butterfly .right-wing {
      right: 0;
      background: radial-gradient(circle at 30% 30%, var(--wing-color, var(--accent)), color-mix(in srgb, var(--wing-color, var(--accent)) 80%, transparent));
      animation: butterflyFlapRight 0.6s ease-in-out infinite;
    }

    .butterfly .body {
      position: absolute;
      width: 4px;
      height: 28px;
      background: linear-gradient(to bottom, #fff, #ccc);
      left: 50%;
      top: 6px;
      transform: translateX(-50%);
      border-radius: 2px;
      box-shadow: 0 0 3px rgba(255,255,255,0.3);
    }

    .butterfly .antenna {
      position: absolute;
      width: 1.5px;
      height: 12px;
      background: #fff;
      top: 0;
      transform-origin: bottom;
    }

    .butterfly .antenna.left {
      left: calc(50% - 1.5px);
      transform: rotate(-20deg);
    }

    .butterfly .antenna.right {
      right: calc(50% - 1.5px);
      transform: rotate(20deg);
    }

    /* Bird styles - Constant generation */
    .bird {
      position: absolute;
      width: 70px;
      height: 49px;
      opacity: 0;
      z-index: 1;
      pointer-events: none;
      filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));
      animation: birdFlyOut 5s ease-out forwards;
    }

    /* Bird body */
    .bird-body {
      position: absolute;
      width: 42px;
      height: 28px;
      background: var(--bird-body-color, var(--bird-gray));
      border-radius: 50% 50% 40% 40%;
      left: 14px;
      top: 10px;
      box-shadow: 
        inset 0 -4px 6px rgba(0,0,0,0.2),
        0 2px 3px rgba(0,0,0,0.1);
    }

    /* Bird head */
    .bird-head {
      position: absolute;
      width: 24px;
      height: 24px;
      background: var(--bird-head-color, var(--bird-gray));
      border-radius: 50%;
      left: 0;
      top: 3px;
      z-index: 2;
      box-shadow: 
        inset 0 -2px 3px rgba(0,0,0,0.2),
        0 1px 2px rgba(0,0,0,0.1);
    }

    /* Bird beak */
    .bird-beak {
      position: absolute;
      width: 18px;
      height: 9px;
      background: var(--bird-beak-color, #ffb347);
      border-radius: 50% 0 0 50%;
      left: -10px;
      top: 11px;
      z-index: 1;
      clip-path: polygon(0 0, 100% 25%, 100% 75%, 0 100%);
      box-shadow: 1px 0 3px rgba(0,0,0,0.2);
    }

    .bird-beak::after {
      content: '';
      position: absolute;
      width: 3px;
      height: 1px;
      background: #000;
      top: 3px;
      right: 3px;
      border-radius: 1px;
    }

    /* Bird eye */
    .bird-eye {
      position: absolute;
      width: 7px;
      height: 7px;
      background: #fff;
      border-radius: 50%;
      left: 14px;
      top: 8px;
      z-index: 3;
      box-shadow: 
        0 0 0 2px rgba(0,0,0,0.1),
        inset 0 1px 2px rgba(0,0,0,0.3);
    }

    .bird-eye::before {
      content: '';
      position: absolute;
      width: 2.5px;
      height: 2.5px;
      background: #000;
      border-radius: 50%;
      top: 2px;
      left: 2px;
    }

    .bird-eye::after {
      content: '';
      position: absolute;
      width: 1px;
      height: 1px;
      background: #fff;
      border-radius: 50%;
      top: 3px;
      left: 3px;
    }

    /* Bird wings */
    .bird-wing {
      position: absolute;
      width: 49px;
      height: 24px;
      background: var(--bird-wing-color, var(--bird-gray));
      border-radius: 50%;
      top: 14px;
      left: 17px;
      transform-origin: 17px 12px;
      animation: birdWingFlap 0.5s ease-in-out infinite;
      box-shadow: 
        inset 0 -2px 6px rgba(0,0,0,0.3),
        0 2px 3px rgba(0,0,0,0.1);
      z-index: 1;
    }

    .bird-wing::before {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, 
        transparent 20%,
        rgba(255,255,255,0.1) 30%,
        rgba(0,0,0,0.1) 70%,
        transparent 80%);
      border-radius: 50%;
    }

    /* Bird tail */
    .bird-tail {
      position: absolute;
      width: 32px;
      height: 18px;
      background: var(--bird-tail-color, var(--bird-gray));
      right: -18px;
      top: 21px;
      border-radius: 0 50% 50% 0;
      clip-path: polygon(0 0, 100% 50%, 0 100%);
      box-shadow: 
        inset -2px 0 3px rgba(0,0,0,0.2),
        1px 0 3px rgba(0,0,0,0.1);
      z-index: 0;
    }

    /* Bird types - More varieties */
    .bird.sparrow {
      --bird-body-color: var(--bird-gray);
      --bird-head-color: var(--bird-black);
      --bird-wing-color: var(--bird-black);
      --bird-tail-color: var(--bird-gray);
    }

    .bird.pigeon {
      --bird-body-color: var(--bird-white);
      --bird-head-color: #cbd5e1;
      --bird-wing-color: var(--bird-white);
      --bird-tail-color: var(--bird-white);
    }

    .bird.cardinal {
      --bird-body-color: var(--bird-red);
      --bird-head-color: var(--bird-red);
      --bird-wing-color: #dc2626;
      --bird-tail-color: var(--bird-red);
    }

    .bird.bluejay {
      --bird-body-color: var(--bird-blue);
      --bird-head-color: var(--bird-blue);
      --bird-wing-color: #1d4ed8;
      --bird-tail-color: var(--bird-blue);
    }

    .bird.canary {
      --bird-body-color: var(--bird-yellow);
      --bird-head-color: var(--bird-yellow);
      --bird-wing-color: #d97706;
      --bird-tail-color: var(--bird-yellow);
    }

    .bird.parrot {
      --bird-body-color: var(--bird-green);
      --bird-head-color: var(--bird-green);
      --bird-wing-color: #059669;
      --bird-tail-color: var(--bird-green);
    }

    .bird.robin {
      --bird-body-color: var(--bird-orange);
      --bird-head-color: var(--bird-orange);
      --bird-wing-color: #ea580c;
      --bird-tail-color: var(--bird-orange);
    }

    .bird.flamingo {
      --bird-body-color: var(--bird-pink);
      --bird-head-color: var(--bird-pink);
      --bird-wing-color: #db2777;
      --bird-tail-color: var(--bird-pink);
    }

    .bird.peacock {
      --bird-body-color: var(--bird-blue);
      --bird-head-color: var(--bird-green);
      --bird-wing-color: var(--bird-purple);
      --bird-tail-color: var(--bird-green);
    }

    /* Card styles */
    .card{
      width:100%;
      max-width:540px;
      border-radius:20px;
      background: linear-gradient(135deg, 
        rgba(255,255,255,0.03) 0%,
        rgba(255,255,255,0.01) 100%);
      border: 1px solid rgba(255,255,255,0.08);
      box-shadow: 
        0 25px 50px rgba(2,6,23,0.8),
        inset 0 1px 0 rgba(255,255,255,0.05),
        0 0 0 1px rgba(255,255,255,0.02);
      padding: 56px 42px;
      text-align:center;
      position:relative;
      z-index: 100;
      backdrop-filter: blur(25px);
      -webkit-backdrop-filter: blur(25px);
      transform: translateY(0) scale(1);
      transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
      cursor: auto;
    }

    .card:hover {
      transform: translateY(-12px) scale(1.01);
      box-shadow: 
        0 35px 70px rgba(2,6,23,1),
        inset 0 1px 0 rgba(255,255,255,0.05),
        0 0 0 1px rgba(255,255,255,0.02);
    }

    .title{
      font-size: 32px;
      font-weight: 800;
      margin:0 0 16px 0;
      background: linear-gradient(135deg, var(--accent), var(--accent-2), var(--accent-3));
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      animation: gradientShift 4s ease infinite;
      background-size: 300% 300%;
      letter-spacing: -0.5px;
      text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    .subtitle{
      color:rgba(226,232,240,0.8); 
      margin:0 0 36px 0;
      font-size:17px;
      line-height:1.7;
      animation: fadeInUp 0.8s ease 0.2s both;
      font-weight: 400;
    }

    .btn{
      display:inline-flex;
      align-items:center;
      gap:14px;
      padding:18px 36px;
      font-size:17px;
      font-weight:700;
      color: white;
      background: linear-gradient(135deg, var(--accent), var(--accent-2));
      border-radius:14px;
      border: 1px solid rgba(255,255,255,0.15);
      box-shadow: 
        0 12px 40px rgba(7,10,25,0.8),
        0 4px 0 rgba(255,255,255,0.05) inset,
        0 -2px 10px rgba(0,0,0,0.3) inset;
      text-decoration:none;
      transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
      will-change: transform, box-shadow;
      position:relative;
      overflow:hidden;
      animation: fadeInUp 0.8s ease 0.4s both;
      letter-spacing: 0.3px;
      cursor: pointer;
    }

    .btn::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, 
        transparent, 
        rgba(255,255,255,0.1), 
        transparent);
      animation: btnShimmer 4s linear infinite;
    }

    .btn:focus{outline:none; box-shadow: 
      0 0 0 4px rgba(6,182,212,0.25),
      0 20px 50px rgba(7,10,25,1);
    }
    
    .btn:hover{
      transform: translateY(-8px) scale(1.03);
      box-shadow: 
        0 25px 60px rgba(7,10,25,1),
        0 4px 0 rgba(255,255,255,0.05) inset,
        0 -2px 10px rgba(0,0,0,0.4) inset;
      letter-spacing: 0.5px;
    }
    
    .btn:active{
      transform: translateY(-4px) scale(0.98);
    }

    .btn .arrow{
      display:inline-grid;
      place-items:center;
      width:36px;height:36px;border-radius:10px;
      background: rgba(255,255,255,0.08);
      border:1px solid rgba(255,255,255,0.1);
      box-shadow: 
        0 6px 20px rgba(2,6,23,0.6),
        0 2px 0 rgba(255,255,255,0.05) inset;
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
      animation: arrowFloat 2.5s ease-in-out infinite;
      font-size: 18px;
    }
    
    .btn:hover .arrow{
      transform: translateX(8px) scale(1.1);
      background: rgba(255,255,255,0.15);
      box-shadow: 
        0 8px 25px rgba(2,6,23,0.8),
        0 2px 0 rgba(255,255,255,0.1) inset;
    }

    .muted{
      font-size:14px;
      color:rgba(226,232,240,0.6); 
      margin-top:28px;
      animation: fadeInUp 0.8s ease 0.6s both;
      line-height: 1.6;
    }

    .muted a {
      color: var(--accent);
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s;
      position: relative;
    }

    .muted a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 100%;
      height: 1px;
      background: linear-gradient(90deg, var(--accent), transparent);
      transform: scaleX(0);
      transition: transform 0.3s;
    }

    .muted a:hover {
      color: var(--accent-2);
    }

    .muted a:hover::after {
      transform: scaleX(1);
    }

    /* Animations */
    @keyframes butterflyFlapLeft {
      0%, 100% {
        transform: rotate(0deg) translateY(0) scaleY(1);
      }
      50% {
        transform: rotate(-25deg) translateY(-2px) scaleY(0.9);
      }
    }

    @keyframes butterflyFlapRight {
      0%, 100% {
        transform: rotate(0deg) translateY(0) scaleY(1);
      }
      50% {
        transform: rotate(25deg) translateY(-2px) scaleY(0.9);
      }
    }

    @keyframes birdWingFlap {
      0%, 100% {
        transform: rotate(0deg);
      }
      50% {
        transform: rotate(35deg);
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
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes arrowFloat {
      0%, 100% {
        transform: translateX(0) rotate(0deg);
      }
      33% {
        transform: translateX(2px) rotate(5deg);
      }
      66% {
        transform: translateX(-1px) rotate(-5deg);
      }
    }

    @keyframes btnShimmer {
      0% {
        transform: translateX(-100%);
      }
      100% {
        transform: translateX(100%);
    }

    @keyframes cursorFloat {
      0%, 100% {
        transform: translate(-50%, -50%) scale(1);
      }
      50% {
        transform: translate(-50%, -50%) scale(1.2);
      }
    }

    @keyframes cursorPulse {
      0%, 100% {
        opacity: 0.7;
        transform: translate(-50%, -50%) scale(1);
      }
      50% {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1.1);
      }
    }

    @keyframes cursorRipple {
      0% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
      }
      100% {
        transform: translate(-50%, -50%) scale(2);
        opacity: 0;
      }
    }

    @keyframes birdFlyOut {
      0% {
        opacity: 0;
        transform: translate(0, 0) scale(0.5) rotate(0deg);
      }
      10% {
        opacity: 1;
        transform: translate(0, 0) scale(1) rotate(0deg);
      }
      100% {
        opacity: 0;
        transform: translate(var(--fly-x), var(--fly-y)) scale(0.7) rotate(var(--fly-rotate));
      }
    }

    @keyframes butterflyRandomMove {
      0% {
        transform: translate(var(--start-x), var(--start-y)) rotate(0deg);
        opacity: 0;
      }
      10% {
        opacity: 1;
      }
      90% {
        opacity: 1;
      }
      100% {
        transform: translate(var(--end-x), var(--end-y)) rotate(360deg);
        opacity: 0;
      }
    }

    /* Performance optimization */
    @media (max-width: 768px) {
      .card {
        padding: 36px 24px;
        margin: 20px;
        backdrop-filter: blur(15px);
      }
      
      .title {
        font-size: 26px;
      }
      
      .btn {
        padding: 16px 28px;
        font-size: 16px;
      }
      
      .bird {
        width: 50px;
        height: 35px;
      }
      
      .bird-body {
        width: 30px;
        height: 20px;
        left: 10px;
        top: 7px;
      }
      
      .bird-head {
        width: 18px;
        height: 18px;
      }
      
      .bird-wing {
        width: 35px;
        height: 17px;
        left: 12px;
        top: 10px;
      }
      
      .bird-tail {
        width: 23px;
        height: 13px;
        right: -12px;
        top: 15px;
      }
      
      .butterfly {
        width: 30px;
        height: 30px;
      }
      
      .butterfly .left-wing,
      .butterfly .right-wing {
        width: 15px;
        height: 21px;
      }
      
      body {
        cursor: auto;
      }
      
      .mouse-cursor {
        display: none;
      }
    }

    @media (max-width: 480px) {
      body {
        padding: 20px;
      }
      
      .card {
        padding: 28px 20px;
        border-radius: 16px;
      }
      
      .title {
        font-size: 22px;
      }
      
      .subtitle {
        font-size: 15px;
      }
      
      .btn {
        width: 100%;
        justify-content: center;
      }
      
      /* Reduce butterflies on very small screens */
      .butterfly:nth-child(n+30) {
        display: none;
      }
    }
  </style>
</head>
<body>
  <!-- Custom cursor -->
  <div class="mouse-cursor" id="cursor"></div>
  
  <!-- Flying creatures container -->
  <div class="flying-creatures" id="creatures"></div>

  <main class="card" role="main">
    <h1 class="title">Welcome back</h1>
    <p class="subtitle">Click the button below to continue to the login page.</p>

    <a class="btn" href="/admin" role="button" aria-label="Go To Login">
      <span>Go To Login</span>
      <span class="arrow" aria-hidden="true">➜</span>
    </a>

    <p class="muted">Need help? <a href="https://wa.me/8801841484885?text=Login%20করতে%20প্রবলেম%20হচ্ছে.%20দয়া%20করে%20সহায়তা%20করুন.%20Website%20URL:%20https://chapaivisa.com/" target="_blank">Contact support on WhatsApp</a> — and say Hi to Helal Uddin ❤️</p>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const container = document.getElementById('creatures');
      const cursor = document.getElementById('cursor');
      
      const colors = [
        'var(--accent)',   // cyan
        'var(--accent-2)', // purple
        'var(--accent-3)', // pink
        'var(--accent-4)', // emerald
        'var(--accent-5)', // amber
        'var(--accent-6)'  // violet
      ];

      // Bird types - 9 varieties
      const birdTypes = ['sparrow', 'pigeon', 'cardinal', 'bluejay', 'canary', 'parrot', 'robin', 'flamingo', 'peacock'];
      
      // Initial 50 butterflies - staggered for performance
      const initialButterflyCount = 50;
      for (let i = 0; i < initialButterflyCount; i++) {
        setTimeout(() => {
          createRandomButterfly(container, colors);
        }, i * 100);
      }

      // Variables for bird generation
      let mouseX = window.innerWidth / 2;
      let mouseY = window.innerHeight / 2;
      let birdGenerationInterval;
      let centerGenerationInterval;
      let isGeneratingFromCenter = true;
      const centerX = window.innerWidth / 2;
      const centerY = window.innerHeight / 2;

      // Create birds from center continuously
      function startCenterGeneration() {
        centerGenerationInterval = setInterval(() => {
          if (isGeneratingFromCenter) {
            const birdType = birdTypes[Math.floor(Math.random() * birdTypes.length)];
            createBirdFromPoint(container, birdType, centerX, centerY, true);
          }
        }, 800); // Create bird every 800ms from center
      }

      // Create birds from mouse position continuously
      function startMouseGeneration() {
        birdGenerationInterval = setInterval(() => {
          const birdType = birdTypes[Math.floor(Math.random() * birdTypes.length)];
          createBirdFromPoint(container, birdType, mouseX, mouseY, false);
        }, 300); // Create bird every 300ms from mouse
      }

      // Mouse movement tracking
      document.addEventListener('mousemove', (e) => {
        // Update cursor position
        cursor.style.left = `${e.clientX}px`;
        cursor.style.top = `${e.clientY}px`;
        
        // Update mouse position
        mouseX = e.clientX;
        mouseY = e.clientY;
        
        // Switch from center generation to mouse generation
        if (isGeneratingFromCenter) {
          isGeneratingFromCenter = false;
          clearInterval(centerGenerationInterval);
          startMouseGeneration();
        }
        
        // Create extra birds based on mouse speed
        createExtraBirdsOnMove(e.movementX, e.movementY);
      });

      // Touch support for mobile/tablet
      document.addEventListener('touchmove', (e) => {
        if (e.touches.length > 0) {
          const touch = e.touches[0];
          mouseX = touch.clientX;
          mouseY = touch.clientY;
          
          // Switch from center generation to touch generation
          if (isGeneratingFromCenter) {
            isGeneratingFromCenter = false;
            clearInterval(centerGenerationInterval);
            startMouseGeneration();
          }
          
          // Show cursor at touch point
          cursor.style.left = `${touch.clientX}px`;
          cursor.style.top = `${touch.clientY}px`;
          cursor.style.opacity = '1';
          
          // Hide cursor after touch ends
          setTimeout(() => {
            cursor.style.opacity = '0';
          }, 1000);
          
          // Create birds from touch
          const birdType = birdTypes[Math.floor(Math.random() * birdTypes.length)];
          createBirdFromPoint(container, birdType, mouseX, mouseY, false);
        }
      });

      function createExtraBirdsOnMove(movementX, movementY) {
        // Calculate movement speed
        const speed = Math.sqrt(movementX * movementX + movementY * movementY);
        
        // Create extra birds based on speed
        if (speed > 10) {
          const extraBirds = Math.floor(speed / 20);
          for (let i = 0; i < extraBirds && i < 3; i++) {
            setTimeout(() => {
              const birdType = birdTypes[Math.floor(Math.random() * birdTypes.length)];
              createBirdFromPoint(container, birdType, mouseX, mouseY, false);
            }, i * 50);
          }
        }
      }

      function createBirdFromPoint(container, type, x, y, fromCenter = false) {
        const bird = document.createElement('div');
        bird.className = `bird ${type}`;
        
        bird.style.left = `${x}px`;
        bird.style.top = `${y}px`;
        
        // Create bird parts
        const body = document.createElement('div');
        body.className = 'bird-body';
        
        const head = document.createElement('div');
        head.className = 'bird-head';
        
        const beak = document.createElement('div');
        beak.className = 'bird-beak';
        
        const eye = document.createElement('div');
        eye.className = 'bird-eye';
        
        const wing = document.createElement('div');
        wing.className = 'bird-wing';
        
        const tail = document.createElement('div');
        tail.className = 'bird-tail';
        
        bird.appendChild(body);
        bird.appendChild(head);
        bird.appendChild(beak);
        bird.appendChild(eye);
        bird.appendChild(wing);
        bird.appendChild(tail);
        
        // Random flying direction
        let angle;
        if (fromCenter) {
          // From center, fly outwards in all directions
          angle = Math.random() * Math.PI * 2;
        } else {
          // From mouse, random direction
          angle = Math.random() * Math.PI * 2;
        }
        
        const distance = 300 + Math.random() * 400; // 300-700px
        const flyX = Math.cos(angle) * distance;
        const flyY = Math.sin(angle) * distance;
        
        // Random rotation for visual effect
        const rotate = (Math.random() * 60) - 30; // -30 to +30 degrees
        
        // Set CSS custom properties for animation
        bird.style.setProperty('--fly-x', `${flyX}px`);
        bird.style.setProperty('--fly-y', `${flyY}px`);
        bird.style.setProperty('--fly-rotate', `${rotate}deg`);
        
        // Random size for variety
        const size = 0.6 + Math.random() * 0.8; // 0.6 to 1.4
        bird.style.transform = `scale(${size})`;
        
        // Random wing flap speed
        wing.style.animationDuration = `${0.3 + Math.random() * 0.4}s`; // 0.3 to 0.7s
        
        container.appendChild(bird);
        
        // Remove bird after animation completes
        setTimeout(() => {
          if (bird.parentNode) {
            bird.remove();
          }
        }, 5000);
        
        return bird;
      }

      function createRandomButterfly(container, colors) {
        const color = colors[Math.floor(Math.random() * colors.length)];
        const butterfly = document.createElement('div');
        butterfly.className = 'butterfly';
        
        const size = 0.5 + Math.random() * 0.7; // 0.5 to 1.2
        
        const leftWing = document.createElement('div');
        leftWing.className = 'left-wing';
        leftWing.style.setProperty('--wing-color', color);
        
        const rightWing = document.createElement('div');
        rightWing.className = 'right-wing';
        rightWing.style.setProperty('--wing-color', color);
        
        const body = document.createElement('div');
        body.className = 'body';
        
        const antennaLeft = document.createElement('div');
        antennaLeft.className = 'antenna left';
        
        const antennaRight = document.createElement('div');
        antennaRight.className = 'antenna right';
        
        butterfly.appendChild(leftWing);
        butterfly.appendChild(rightWing);
        butterfly.appendChild(body);
        butterfly.appendChild(antennaLeft);
        butterfly.appendChild(antennaRight);
        
        // Random start and end positions for butterfly
        const startX = Math.random() * 120 - 10; // -10% to 110%
        const startY = Math.random() * 120 - 10;
        const endX = Math.random() * 120 - 10;
        const endY = Math.random() * 120 - 10;
        
        butterfly.style.setProperty('--start-x', `${startX}vw`);
        butterfly.style.setProperty('--start-y', `${startY}vh`);
        butterfly.style.setProperty('--end-x', `${endX}vw`);
        butterfly.style.setProperty('--end-y', `${endY}vh`);
        
        const duration = 20 + Math.random() * 40; // 20-60 seconds
        butterfly.style.animationDuration = `${duration}s`;
        butterfly.style.animationDelay = `${Math.random() * 20}s`;
        butterfly.style.transform = `scale(${size})`;
        butterfly.style.opacity = '0';
        
        container.appendChild(butterfly);
        
        // Remove and recreate butterfly after animation
        setTimeout(() => {
          if (butterfly.parentNode) {
            butterfly.remove();
            createRandomButterfly(container, colors);
          }
        }, (duration + 5) * 1000);
        
        return butterfly;
      }

      // Start center generation on load
      startCenterGeneration();

      // Auto-cleanup to prevent too many elements
      setInterval(() => {
        const birds = container.querySelectorAll('.bird');
        
        // Keep reasonable limits for birds
        if (birds.length > 50) {
          const birdsToRemove = birds.length - 40;
          for (let i = 0; i < birdsToRemove && i < birds.length; i++) {
            if (birds[i].parentNode) {
              birds[i].remove();
            }
          }
        }
      }, 10000);
    });
  </script>
</body>
</html>