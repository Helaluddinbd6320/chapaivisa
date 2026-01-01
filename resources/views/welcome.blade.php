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

    /* Butterfly styles - Whole screen movement */
    .butterfly {
      position: absolute;
      width: 50px;
      height: 50px;
      filter: drop-shadow(0 4px 12px rgba(0,0,0,0.4));
      opacity: 0.9;
      z-index: 0;
      pointer-events: none;
      animation: butterflyRandomFly 40s linear infinite;
      animation-play-state: running;
    }

    .butterfly .left-wing,
    .butterfly .right-wing {
      position: absolute;
      width: 25px;
      height: 35px;
      border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
      transform-origin: center;
      background: var(--wing-color, var(--accent));
    }

    .butterfly .left-wing {
      left: 0;
      background: radial-gradient(circle at 70% 30%, var(--wing-color, var(--accent)), color-mix(in srgb, var(--wing-color, var(--accent)) 80%, transparent));
      animation: butterflyFlapLeft 0.8s ease-in-out infinite;
    }

    .butterfly .right-wing {
      right: 0;
      background: radial-gradient(circle at 30% 30%, var(--wing-color, var(--accent)), color-mix(in srgb, var(--wing-color, var(--accent)) 80%, transparent));
      animation: butterflyFlapRight 0.8s ease-in-out infinite;
    }

    .butterfly .body {
      position: absolute;
      width: 5px;
      height: 35px;
      background: linear-gradient(to bottom, #fff, #ccc);
      left: 50%;
      top: 7px;
      transform: translateX(-50%);
      border-radius: 2px;
      box-shadow: 0 0 4px rgba(255,255,255,0.3);
    }

    .butterfly .antenna {
      position: absolute;
      width: 2px;
      height: 15px;
      background: #fff;
      top: 0;
      transform-origin: bottom;
    }

    .butterfly .antenna.left {
      left: calc(50% - 2px);
      transform: rotate(-20deg);
    }

    .butterfly .antenna.right {
      right: calc(50% - 2px);
      transform: rotate(20deg);
    }

    /* Bird styles - Directional flying */
    .bird {
      position: absolute;
      width: 100px;
      height: 70px;
      opacity: 0.95;
      z-index: 1;
      pointer-events: none;
      filter: drop-shadow(0 6px 12px rgba(0,0,0,0.3));
      animation: birdCrossFly 25s linear infinite;
      animation-play-state: running;
    }

    /* Bird fleeing from mouse */
    .bird.fleeing {
      animation: birdFleeFly 3s ease-out forwards !important;
      filter: brightness(1.3) drop-shadow(0 8px 15px rgba(239,68,68,0.3));
      z-index: 10;
    }

    /* Bird body */
    .bird-body {
      position: absolute;
      width: 60px;
      height: 40px;
      background: var(--bird-body-color, var(--bird-gray));
      border-radius: 50% 50% 40% 40%;
      left: 20px;
      top: 15px;
      box-shadow: 
        inset 0 -5px 10px rgba(0,0,0,0.2),
        0 3px 5px rgba(0,0,0,0.1);
    }

    /* Bird head */
    .bird-head {
      position: absolute;
      width: 35px;
      height: 35px;
      background: var(--bird-head-color, var(--bird-gray));
      border-radius: 50%;
      left: 0;
      top: 5px;
      z-index: 2;
      box-shadow: 
        inset 0 -3px 5px rgba(0,0,0,0.2),
        0 2px 4px rgba(0,0,0,0.1);
    }

    /* Bird beak */
    .bird-beak {
      position: absolute;
      width: 25px;
      height: 12px;
      background: var(--bird-beak-color, #ffb347);
      border-radius: 50% 0 0 50%;
      left: -15px;
      top: 16px;
      z-index: 1;
      clip-path: polygon(0 0, 100% 25%, 100% 75%, 0 100%);
      box-shadow: 2px 0 5px rgba(0,0,0,0.2);
    }

    .bird-beak::after {
      content: '';
      position: absolute;
      width: 5px;
      height: 2px;
      background: #000;
      top: 5px;
      right: 5px;
      border-radius: 2px;
    }

    /* Bird eye */
    .bird-eye {
      position: absolute;
      width: 10px;
      height: 10px;
      background: #fff;
      border-radius: 50%;
      left: 20px;
      top: 12px;
      z-index: 3;
      box-shadow: 
        0 0 0 3px rgba(0,0,0,0.1),
        inset 0 1px 3px rgba(0,0,0,0.3);
    }

    .bird-eye::before {
      content: '';
      position: absolute;
      width: 4px;
      height: 4px;
      background: #000;
      border-radius: 50%;
      top: 3px;
      left: 3px;
    }

    .bird-eye::after {
      content: '';
      position: absolute;
      width: 2px;
      height: 2px;
      background: #fff;
      border-radius: 50%;
      top: 4px;
      left: 4px;
    }

    /* Bird wings */
    .bird-wing {
      position: absolute;
      width: 70px;
      height: 35px;
      background: var(--bird-wing-color, var(--bird-gray));
      border-radius: 50%;
      top: 20px;
      left: 25px;
      transform-origin: 25px 17px;
      animation: birdWingFlap 1.2s ease-in-out infinite;
      box-shadow: 
        inset 0 -3px 10px rgba(0,0,0,0.3),
        0 3px 5px rgba(0,0,0,0.1);
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
      width: 45px;
      height: 25px;
      background: var(--bird-tail-color, var(--bird-gray));
      right: -25px;
      top: 30px;
      border-radius: 0 50% 50% 0;
      clip-path: polygon(0 0, 100% 50%, 0 100%);
      box-shadow: 
        inset -3px 0 5px rgba(0,0,0,0.2),
        2px 0 5px rgba(0,0,0,0.1);
      z-index: 0;
    }

    /* Bird legs */
    .bird-leg {
      position: absolute;
      width: 3px;
      height: 20px;
      background: #ffb347;
      bottom: -15px;
      border-radius: 2px;
      z-index: 0;
    }

    .bird-leg.left {
      left: 45px;
      transform: rotate(-5deg);
    }

    .bird-leg.right {
      left: 55px;
      transform: rotate(5deg);
    }

    .bird-leg::before {
      content: '';
      position: absolute;
      width: 6px;
      height: 3px;
      background: #ffb347;
      bottom: 0;
      left: -1.5px;
      border-radius: 2px;
    }

    /* Bird flying trail */
    .bird-trail {
      position: absolute;
      width: 15px;
      height: 3px;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1));
      border-radius: 2px;
      right: -20px;
      top: 35px;
      opacity: 0.3;
      animation: trailFade 1s linear infinite;
    }

    /* Bird types */
    .bird.sparrow .bird-body,
    .bird.sparrow .bird-head,
    .bird.sparrow .bird-wing,
    .bird.sparrow .bird-tail {
      background: var(--bird-gray);
    }

    .bird.sparrow .bird-head {
      background: linear-gradient(135deg, var(--bird-gray), var(--bird-black));
    }

    .bird.sparrow .bird-wing {
      background: linear-gradient(135deg, var(--bird-gray), var(--bird-black));
    }

    .bird.pigeon .bird-body,
    .bird.pigeon .bird-head,
    .bird.pigeon .bird-wing,
    .bird.pigeon .bird-tail {
      background: var(--bird-white);
    }

    .bird.pigeon .bird-head {
      background: linear-gradient(135deg, var(--bird-white), #cbd5e1);
    }

    .bird.cardinal .bird-body,
    .bird.cardinal .bird-head,
    .bird.cardinal .bird-tail {
      background: var(--bird-red);
    }

    .bird.cardinal .bird-wing {
      background: linear-gradient(135deg, var(--bird-red), #dc2626);
    }

    .bird.bluejay .bird-body,
    .bird.bluejay .bird-head,
    .bird.bluejay .bird-tail {
      background: var(--bird-blue);
    }

    .bird.bluejay .bird-wing {
      background: linear-gradient(135deg, var(--bird-blue), #1d4ed8);
    }

    .bird.canary .bird-body,
    .bird.canary .bird-head,
    .bird.canary .bird-tail {
      background: var(--bird-yellow);
    }

    .bird.canary .bird-wing {
      background: linear-gradient(135deg, var(--bird-yellow), #d97706);
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
        transform: rotate(-30deg) translateY(-4px) scaleY(0.9);
      }
    }

    @keyframes butterflyFlapRight {
      0%, 100% {
        transform: rotate(0deg) translateY(0) scaleY(1);
      }
      50% {
        transform: rotate(30deg) translateY(-4px) scaleY(0.9);
      }
    }

    @keyframes birdWingFlap {
      0%, 100% {
        transform: rotate(0deg);
      }
      50% {
        transform: rotate(40deg);
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
    }

    @keyframes trailFade {
      0% {
        opacity: 0.3;
        transform: translateX(0) scaleX(1);
      }
      100% {
        opacity: 0;
        transform: translateX(20px) scaleX(0.5);
      }
    }

    /* Butterfly random movement across entire screen */
    @keyframes butterflyRandomFly {
      0% {
        transform: translate(var(--start-x), var(--start-y)) rotate(var(--start-rotate));
        opacity: 0;
      }
      5% {
        opacity: 1;
      }
      95% {
        opacity: 1;
      }
      100% {
        transform: translate(var(--end-x), var(--end-y)) rotate(var(--end-rotate));
        opacity: 0;
      }
    }

    /* Bird directional movement - left to right, right to left, top to bottom, etc */
    @keyframes birdCrossFly {
      0% {
        transform: translate(var(--start-x), var(--start-y)) rotate(var(--start-rotate));
        opacity: 0;
      }
      5% {
        opacity: 1;
      }
      95% {
        opacity: 1;
      }
      100% {
        transform: translate(var(--end-x), var(--end-y)) rotate(var(--end-rotate));
        opacity: 0;
      }
    }

    /* Bird fleeing animation */
    @keyframes birdFleeFly {
      0% {
        transform: translate(var(--flee-start-x), var(--flee-start-y)) rotate(var(--flee-start-rotate));
        opacity: 1;
        filter: brightness(1.3);
      }
      100% {
        transform: translate(var(--flee-end-x), var(--flee-end-y)) rotate(var(--flee-end-rotate));
        opacity: 0;
        filter: brightness(1);
      }
    }

    /* Performance optimization for mobile */
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
        width: 70px;
        height: 50px;
      }
      
      .bird-body {
        width: 40px;
        height: 25px;
        left: 15px;
        top: 10px;
      }
      
      .bird-head {
        width: 25px;
        height: 25px;
      }
      
      .bird-wing {
        width: 50px;
        height: 25px;
        left: 15px;
        top: 15px;
      }
      
      .bird-tail {
        width: 30px;
        height: 15px;
        right: -15px;
        top: 20px;
      }
      
      .butterfly {
        width: 35px;
        height: 35px;
      }
      
      .butterfly .left-wing,
      .butterfly .right-wing {
        width: 17px;
        height: 25px;
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
      
      .bird, .butterfly {
        opacity: 0.7;
      }
      
      /* Reduce number of creatures on very small screens */
      .butterfly:nth-child(n+15),
      .bird:nth-child(n+8) {
        display: none;
      }
    }
  </style>
</head>
<body>
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
      const colors = [
        'var(--accent)',   // cyan
        'var(--accent-2)', // purple
        'var(--accent-3)', // pink
        'var(--accent-4)', // emerald
        'var(--accent-5)', // amber
        'var(--accent-6)'  // violet
      ];

      // Create butterflies - Whole screen random movement (30 butterflies)
      const butterflyCount = 30;
      for (let i = 0; i < butterflyCount; i++) {
        createButterfly(container, colors);
      }

      // Create birds - Directional movement (15 birds)
      const birdTypes = ['sparrow', 'pigeon', 'cardinal', 'bluejay', 'canary'];
      const birdCount = 15;
      
      for (let i = 0; i < birdCount; i++) {
        const birdType = birdTypes[Math.floor(Math.random() * birdTypes.length)];
        createBird(container, birdType);
      }

      // Mouse tracking for bird fleeing behavior
      let mouseX = 0;
      let mouseY = 0;
      let fleeingBirds = new Set();

      document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
        
        // Check for nearby birds
        checkMouseProximity();
      });

      function checkMouseProximity() {
        const birds = container.querySelectorAll('.bird:not(.fleeing)');
        
        birds.forEach(bird => {
          const rect = bird.getBoundingClientRect();
          const birdX = rect.left + rect.width / 2;
          const birdY = rect.top + rect.height / 2;
          
          const distance = Math.sqrt(Math.pow(mouseX - birdX, 2) + Math.pow(mouseY - birdY, 2));
          
          // If mouse is within 150px, bird flees
          if (distance < 150 && !fleeingBirds.has(bird)) {
            fleeBird(bird, birdX, birdY);
          }
        });
      }

      function fleeBird(bird, birdX, birdY) {
        fleeingBirds.add(bird);
        bird.classList.add('fleeing');
        
        // Pause the normal animation
        bird.style.animationPlayState = 'paused';
        
        // Get current position
        const computedStyle = window.getComputedStyle(bird);
        const currentX = parseFloat(computedStyle.left) || 50;
        const currentY = parseFloat(computedStyle.top) || 50;
        
        // Calculate flee direction (away from mouse)
        const dx = birdX - mouseX;
        const dy = birdY - mouseY;
        const distance = Math.sqrt(dx * dx + dy * dy);
        const dirX = dx / distance;
        const dirY = dy / distance;
        
        // Calculate flee distance (outside viewport)
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        
        // Determine which edge to flee to
        let fleeX, fleeY;
        if (Math.abs(dirX) > Math.abs(dirY)) {
          // Flee horizontally
          fleeX = dirX > 0 ? 150 : -150;
          fleeY = (Math.random() - 0.5) * 100;
        } else {
          // Flee vertically
          fleeX = (Math.random() - 0.5) * 100;
          fleeY = dirY > 0 ? 150 : -150;
        }
        
        // Set flee animation variables
        bird.style.setProperty('--flee-start-x', '0px');
        bird.style.setProperty('--flee-start-y', '0px');
        bird.style.setProperty('--flee-end-x', `${fleeX}px`);
        bird.style.setProperty('--flee-end-y', `${fleeY}px`);
        
        // Calculate rotation angle
        const fleeAngle = Math.atan2(fleeY, fleeX) * 180 / Math.PI;
        bird.style.setProperty('--flee-start-rotate', '0deg');
        bird.style.setProperty('--flee-end-rotate', `${fleeAngle}deg`);
        
        // Remove bird after fleeing and create a new one
        setTimeout(() => {
          bird.remove();
          fleeingBirds.delete(bird);
          
          // Create a new bird after some delay
          setTimeout(() => {
            const birdType = birdTypes[Math.floor(Math.random() * birdTypes.length)];
            createBird(container, birdType);
          }, Math.random() * 5000 + 3000); // 3-8 seconds delay
        }, 3000);
      }
    });

    function createButterfly(container, colors) {
      const butterfly = document.createElement('div');
      butterfly.className = 'butterfly';
      
      const color = colors[Math.floor(Math.random() * colors.length)];
      const size = Math.random() * 0.8 + 0.6;
      
      // Random start position on edges
      const startSide = Math.floor(Math.random() * 4); // 0: top, 1: right, 2: bottom, 3: left
      let startX, startY, endX, endY;
      
      switch(startSide) {
        case 0: // Top
          startX = Math.random() * 100;
          startY = -50;
          endX = Math.random() * 100;
          endY = 150;
          break;
        case 1: // Right
          startX = 150;
          startY = Math.random() * 100;
          endX = -50;
          endY = Math.random() * 100;
          break;
        case 2: // Bottom
          startX = Math.random() * 100;
          startY = 150;
          endX = Math.random() * 100;
          endY = -50;
          break;
        case 3: // Left
          startX = -50;
          startY = Math.random() * 100;
          endX = 150;
          endY = Math.random() * 100;
          break;
      }
      
      const duration = Math.random() * 30 + 30; // 30-60 seconds
      const delay = Math.random() * 20;
      const flapSpeed = Math.random() * 0.4 + 0.6;
      
      const leftWing = document.createElement('div');
      leftWing.className = 'left-wing';
      leftWing.style.setProperty('--wing-color', color);
      leftWing.style.animationDuration = `${flapSpeed}s`;
      
      const rightWing = document.createElement('div');
      rightWing.className = 'right-wing';
      rightWing.style.setProperty('--wing-color', color);
      rightWing.style.animationDuration = `${flapSpeed}s`;
      
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
      
      // Set animation properties
      butterfly.style.setProperty('--start-x', `${startX}vw`);
      butterfly.style.setProperty('--start-y', `${startY}vh`);
      butterfly.style.setProperty('--end-x', `${endX}vw`);
      butterfly.style.setProperty('--end-y', `${endY}vh`);
      
      const startRotate = Math.random() * 360;
      const endRotate = startRotate + (Math.random() * 720 - 360);
      butterfly.style.setProperty('--start-rotate', `${startRotate}deg`);
      butterfly.style.setProperty('--end-rotate', `${endRotate}deg`);
      
      butterfly.style.animationDuration = `${duration}s`;
      butterfly.style.animationDelay = `${delay}s`;
      butterfly.style.transform = `scale(${size})`;
      butterfly.style.opacity = '0';
      
      container.appendChild(butterfly);
      
      return butterfly;
    }

    function createBird(container, type) {
      const bird = document.createElement('div');
      bird.className = `bird ${type}`;
      
      // Determine flight direction
      const direction = Math.floor(Math.random() * 4); // 0: left->right, 1: right->left, 2: top->bottom, 3: bottom->top
      let startX, startY, endX, endY, startRotate, endRotate;
      
      switch(direction) {
        case 0: // Left to Right
          startX = -100;
          startY = Math.random() * 100;
          endX = 200;
          endY = startY + (Math.random() * 40 - 20);
          startRotate = 0;
          endRotate = 0;
          break;
        case 1: // Right to Left
          startX = 200;
          startY = Math.random() * 100;
          endX = -100;
          endY = startY + (Math.random() * 40 - 20);
          startRotate = 180;
          endRotate = 180;
          break;
        case 2: // Top to Bottom
          startX = Math.random() * 100;
          startY = -100;
          endX = startX + (Math.random() * 40 - 20);
          endY = 200;
          startRotate = 90;
          endRotate = 90;
          break;
        case 3: // Bottom to Top
          startX = Math.random() * 100;
          startY = 200;
          endX = startX + (Math.random() * 40 - 20);
          endY = -100;
          startRotate = -90;
          endRotate = -90;
          break;
      }
      
      const size = Math.random() * 0.6 + 0.8;
      const duration = Math.random() * 15 + 20; // 20-35 seconds
      const delay = Math.random() * 10;
      const flapSpeed = Math.random() * 0.8 + 0.8;
      
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
      wing.style.animationDuration = `${flapSpeed}s`;
      
      const tail = document.createElement('div');
      tail.className = 'bird-tail';
      
      const legLeft = document.createElement('div');
      legLeft.className = 'bird-leg left';
      
      const legRight = document.createElement('div');
      legRight.className = 'bird-leg right';
      
      const trail = document.createElement('div');
      trail.className = 'bird-trail';
      
      bird.appendChild(body);
      bird.appendChild(head);
      bird.appendChild(beak);
      bird.appendChild(eye);
      bird.appendChild(wing);
      bird.appendChild(tail);
      bird.appendChild(legLeft);
      bird.appendChild(legRight);
      bird.appendChild(trail);
      
      // Set animation properties
      bird.style.setProperty('--start-x', `${startX}vw`);
      bird.style.setProperty('--start-y', `${startY}vh`);
      bird.style.setProperty('--end-x', `${endX}vw`);
      bird.style.setProperty('--end-y', `${endY}vh`);
      bird.style.setProperty('--start-rotate', `${startRotate}deg`);
      bird.style.setProperty('--end-rotate', `${endRotate}deg`);
      
      bird.style.animationDuration = `${duration}s`;
      bird.style.animationDelay = `${delay}s`;
      bird.style.transform = `scale(${size})`;
      bird.style.opacity = '0';
      
      // Add subtle size variation for wing
      const wingSize = Math.random() * 0.2 + 0.9;
      wing.style.transform = `scale(${wingSize})`;
      
      container.appendChild(bird);
      
      // Remove bird after animation and create a new one
      setTimeout(() => {
        if (bird.parentNode && !bird.classList.contains('fleeing')) {
          bird.remove();
          
          // Create a new bird after some delay
          setTimeout(() => {
            const newBirdType = type;
            createBird(container, newBirdType);
          }, Math.random() * 5000 + 2000); // 2-7 seconds delay
        }
      }, (duration + delay) * 1000);
      
      return bird;
    }
  </script>
</body>
</html>