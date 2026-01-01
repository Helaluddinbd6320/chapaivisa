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

    /* Butterfly styles */
    .butterfly {
      position: absolute;
      width: 50px;
      height: 50px;
      filter: drop-shadow(0 4px 12px rgba(0,0,0,0.4));
      opacity: 0.85;
      z-index: -1;
      pointer-events: none;
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

    /* Bird styles */
    .bird {
      position: absolute;
      width: 80px;
      height: 40px;
      opacity: 0.9;
      z-index: -1;
      pointer-events: none;
    }

    .bird .body {
      position: absolute;
      width: 50px;
      height: 25px;
      background: var(--bird-color, var(--accent-2));
      border-radius: 50% 50% 40% 40%;
      left: 15px;
      top: 7px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }

    .bird .wing {
      position: absolute;
      width: 40px;
      height: 20px;
      background: var(--bird-color, var(--accent-2));
      border-radius: 50%;
      top: 5px;
      transform-origin: 30px 10px;
      animation: birdFlap 1s ease-in-out infinite;
    }

    .bird .wing.left {
      left: 0;
      background: linear-gradient(to right, color-mix(in srgb, var(--bird-color, var(--accent-2)) 70%, black), var(--bird-color, var(--accent-2)));
    }

    .bird .wing.right {
      right: 0;
      background: linear-gradient(to left, color-mix(in srgb, var(--bird-color, var(--accent-2)) 70%, black), var(--bird-color, var(--accent-2)));
      animation-delay: 0.5s;
    }

    .bird .tail {
      position: absolute;
      width: 0;
      height: 0;
      border-left: 15px solid var(--bird-color, var(--accent-2));
      border-top: 8px solid transparent;
      border-bottom: 8px solid transparent;
      right: -10px;
      top: 15px;
    }

    .bird .head {
      position: absolute;
      width: 20px;
      height: 20px;
      background: var(--bird-color, var(--accent-2));
      border-radius: 50%;
      left: 0;
      top: 10px;
    }

    .bird .beak {
      position: absolute;
      width: 0;
      height: 0;
      border-left: 10px solid #ffb347;
      border-top: 5px solid transparent;
      border-bottom: 5px solid transparent;
      left: -8px;
      top: 15px;
    }

    .bird .eye {
      position: absolute;
      width: 6px;
      height: 6px;
      background: #fff;
      border-radius: 50%;
      left: 12px;
      top: 13px;
      box-shadow: 0 0 2px rgba(0,0,0,0.5);
    }

    .bird .eye::after {
      content: '';
      position: absolute;
      width: 2px;
      height: 2px;
      background: #000;
      border-radius: 50%;
      top: 2px;
      left: 2px;
    }

    /* Sparkle particles */
    .sparkle {
      position: absolute;
      width: 4px;
      height: 4px;
      background: #fff;
      border-radius: 50%;
      filter: blur(1px);
      opacity: 0;
      animation: sparkleFloat 3s linear infinite;
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
      z-index: 2;
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

    .card::before {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: 20px;
      padding: 1px;
      background: linear-gradient(135deg, 
        rgba(6,182,212,0.2), 
        rgba(124,58,237,0.2), 
        transparent 50%);
      -webkit-mask: 
        linear-gradient(#fff 0 0) content-box, 
        linear-gradient(#fff 0 0);
      -webkit-mask-composite: xor;
      mask-composite: exclude;
      pointer-events: none;
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

    .btn::after {
      content: '';
      position: absolute;
      inset: -2px;
      border-radius: 16px;
      background: linear-gradient(135deg, 
        rgba(6,182,212,0.4), 
        rgba(124,58,237,0.4), 
        rgba(236,72,153,0.4));
      z-index: -1;
      opacity: 0;
      transition: opacity 0.4s;
      filter: blur(10px);
    }

    .btn:hover::after {
      opacity: 1;
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

    @keyframes birdFlap {
      0%, 100% {
        transform: rotate(0deg);
      }
      50% {
        transform: rotate(30deg);
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

    @keyframes sparkleFloat {
      0% {
        opacity: 0;
        transform: translateY(0) scale(0.5);
      }
      10% {
        opacity: 1;
        transform: translateY(-10px) scale(1);
      }
      90% {
        opacity: 1;
      }
      100% {
        opacity: 0;
        transform: translateY(-50px) scale(0.5);
      }
    }

    @keyframes creatureFloat {
      0% {
        transform: translate(0, 0) rotate(0deg);
      }
      25% {
        transform: translate(40px, -30px) rotate(10deg);
      }
      50% {
        transform: translate(-20px, 20px) rotate(-5deg);
      }
      75% {
        transform: translate(30px, -15px) rotate(5deg);
      }
      100% {
        transform: translate(0, 0) rotate(0deg);
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
      
      /* Reduce number of creatures on mobile */
      .butterfly, .bird {
        opacity: 0.6;
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
      
      .bird {
        width: 60px;
        height: 30px;
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

      // Create many butterflies (20-25)
      const butterflyCount = 25;
      for (let i = 0; i < butterflyCount; i++) {
        createButterfly(container, colors);
      }

      // Create birds (10-15)
      const birdCount = 15;
      for (let i = 0; i < birdCount; i++) {
        createBird(container, colors);
      }

      // Create sparkles around creatures
      setInterval(() => {
        createSparkle(container);
      }, 100);

      // Interactive movement
      let mouseX = 0;
      let mouseY = 0;
      
      document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
        attractCreatures();
      });

      function attractCreatures() {
        const creatures = container.querySelectorAll('.butterfly, .bird');
        creatures.forEach(creature => {
          const rect = creature.getBoundingClientRect();
          const creatureX = rect.left + rect.width / 2;
          const creatureY = rect.top + rect.height / 2;
          
          const dx = mouseX - creatureX;
          const dy = mouseY - creatureY;
          const distance = Math.sqrt(dx * dx + dy * dy);
          
          if (distance < 400) { // Increased attraction radius
            const force = (400 - distance) / 400 * 0.8;
            const angle = Math.atan2(dy, dx);
            const speed = 2 + force * 3;
            
            // Get current animation values
            const currentLeft = parseFloat(creature.style.left) || Math.random() * 90 + 5;
            const currentTop = parseFloat(creature.style.top) || Math.random() * 90 + 5;
            
            // Calculate new position
            const newX = currentLeft + Math.cos(angle) * speed;
            const newY = currentTop + Math.sin(angle) * speed;
            
            // Apply boundaries
            creature.style.left = `${Math.max(2, Math.min(98, newX))}%`;
            creature.style.top = `${Math.max(2, Math.min(98, newY))}%`;
            
            // Add slight rotation towards mouse
            const rotation = (angle * 180 / Math.PI) + 90;
            creature.style.transform = `rotate(${rotation}deg)`;
          }
        });
      }

      // Auto movement when no mouse interaction
      let autoMoveInterval = setInterval(() => {
        if (mouseX === 0 && mouseY === 0) {
          moveCreaturesRandomly();
        }
      }, 2000);

      function moveCreaturesRandomly() {
        const creatures = container.querySelectorAll('.butterfly, .bird');
        creatures.forEach(creature => {
          const currentLeft = parseFloat(creature.style.left) || Math.random() * 90 + 5;
          const currentTop = parseFloat(creature.style.top) || Math.random() * 90 + 5;
          
          const moveX = (Math.random() - 0.5) * 4;
          const moveY = (Math.random() - 0.5) * 4;
          
          const newX = Math.max(2, Math.min(98, currentLeft + moveX));
          const newY = Math.max(2, Math.min(98, currentTop + moveY));
          
          creature.style.left = `${newX}%`;
          creature.style.top = `${newY}%`;
          
          // Gentle rotation
          const rotation = (Math.random() - 0.5) * 20;
          creature.style.transform = `rotate(${rotation}deg)`;
        });
      }
    });

    function createButterfly(container, colors) {
      const butterfly = document.createElement('div');
      butterfly.className = 'butterfly';
      
      const color = colors[Math.floor(Math.random() * colors.length)];
      const size = Math.random() * 0.8 + 0.6;
      const left = Math.random() * 90 + 5;
      const top = Math.random() * 90 + 5;
      const duration = Math.random() * 40 + 30;
      const delay = Math.random() * 20;
      const flapSpeed = Math.random() * 0.4 + 0.6;
      
      // Create butterfly parts
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
      
      // Style butterfly
      butterfly.style.left = `${left}%`;
      butterfly.style.top = `${top}%`;
      butterfly.style.transform = `scale(${size})`;
      butterfly.style.animation = `creatureFloat ${duration}s ease-in-out ${delay}s infinite`;
      butterfly.style.zIndex = Math.floor(Math.random() * 5);
      
      container.appendChild(butterfly);
    }

    function createBird(container, colors) {
      const bird = document.createElement('div');
      bird.className = 'bird';
      
      const color = colors[Math.floor(Math.random() * colors.length)];
      const size = Math.random() * 0.7 + 0.8;
      const left = Math.random() * 90 + 5;
      const top = Math.random() * 90 + 5;
      const duration = Math.random() * 50 + 40;
      const delay = Math.random() * 30;
      const flapSpeed = Math.random() * 0.5 + 0.8;
      
      // Create bird parts
      const body = document.createElement('div');
      body.className = 'body';
      body.style.setProperty('--bird-color', color);
      
      const leftWing = document.createElement('div');
      leftWing.className = 'wing left';
      leftWing.style.setProperty('--bird-color', color);
      leftWing.style.animationDuration = `${flapSpeed}s`;
      
      const rightWing = document.createElement('div');
      rightWing.className = 'wing right';
      rightWing.style.setProperty('--bird-color', color);
      rightWing.style.animationDuration = `${flapSpeed}s`;
      rightWing.style.animationDelay = `${flapSpeed/2}s`;
      
      const tail = document.createElement('div');
      tail.className = 'tail';
      tail.style.setProperty('--bird-color', color);
      
      const head = document.createElement('div');
      head.className = 'head';
      head.style.setProperty('--bird-color', color);
      
      const beak = document.createElement('div');
      beak.className = 'beak';
      
      const eye = document.createElement('div');
      eye.className = 'eye';
      
      bird.appendChild(body);
      bird.appendChild(leftWing);
      bird.appendChild(rightWing);
      bird.appendChild(tail);
      bird.appendChild(head);
      bird.appendChild(beak);
      bird.appendChild(eye);
      
      // Style bird
      bird.style.left = `${left}%`;
      bird.style.top = `${top}%`;
      bird.style.transform = `scale(${size})`;
      bird.style.animation = `creatureFloat ${duration}s ease-in-out ${delay}s infinite`;
      bird.style.zIndex = Math.floor(Math.random() * 3) + 2;
      
      container.appendChild(bird);
    }

    function createSparkle(container) {
      if (Math.random() > 0.3) return; // 70% chance to skip for performance
      
      const sparkle = document.createElement('div');
      sparkle.className = 'sparkle';
      
      const left = Math.random() * 100;
      const top = Math.random() * 100;
      const size = Math.random() * 3 + 2;
      const delay = Math.random() * 5;
      
      sparkle.style.left = `${left}%`;
      sparkle.style.top = `${top}%`;
      sparkle.style.width = `${size}px`;
      sparkle.style.height = `${size}px`;
      sparkle.style.animationDelay = `${delay}s`;
      sparkle.style.background = `hsl(${Math.random() * 60 + 180}, 100%, 70%)`;
      
      container.appendChild(sparkle);
      
      // Remove after animation
      setTimeout(() => {
        if (sparkle.parentNode) {
          sparkle.remove();
        }
      }, 3000);
    }
  </script>
</body>
</html>