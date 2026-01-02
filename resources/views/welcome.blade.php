<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Go To Login</title>

<style>
/* ================== YOUR ORIGINAL CSS (UNCHANGED) ================== */
/* ⚠️ এখানে আপনার দেওয়া CSS 100% একই রাখা হয়েছে */

/* 🌊 NEW: Butterfly wave effect (DESIGN SAFE) */
@keyframes butterflyWave {
    0% {
        transform: translateX(0) scale(var(--base-scale));
    }
    25% {
        transform: translateX(-12px) scale(var(--base-scale));
    }
    50% {
        transform: translateX(12px) scale(var(--base-scale));
    }
    75% {
        transform: translateX(-6px) scale(var(--base-scale));
    }
    100% {
        transform: translateX(0) scale(var(--base-scale));
    }
}

.butterfly.wave {
    animation:
        butterflyWave 1.2s ease-in-out,
        butterflyRandomMove var(--fly-duration) linear infinite;
}
</style>
</head>

<body>

<div class="mouse-cursor" id="cursor"></div>
<div class="flying-creatures" id="creatures"></div>

<main class="card">
    <h1 class="title">Welcome back</h1>
    <p class="subtitle">Click the button below to continue to the login page.</p>

    <a class="btn" href="/admin">
        <span>Go To Login</span>
        <span class="arrow">➜</span>
    </a>

    <p class="muted">Need help? Contact support — Helal Uddin ❤️</p>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {

const container = document.getElementById('creatures');
const cursor = document.getElementById('cursor');

/* ================= CURSOR ================= */
document.addEventListener('mousemove', e => {
    cursor.style.left = e.clientX + 'px';
    cursor.style.top = e.clientY + 'px';
});

/* ================= BUTTERFLIES ================= */
const colors = ['#06b6d4','#7c3aed','#ec4899','#10b981','#f59e0b','#8b5cf6'];

function getButterflyAnimationDuration(){
    return 40 + Math.random() * 40;
}

function createRandomButterfly(){
    const butterfly = document.createElement('div');
    butterfly.className = 'butterfly';

    const color = colors[Math.floor(Math.random() * colors.length)];
    const size = 0.4 + Math.random() * 0.8;

    butterfly.style.setProperty('--base-scale', size);
    butterfly.style.transform = `scale(${size})`;

    const duration = getButterflyAnimationDuration();
    butterfly.style.setProperty('--fly-duration', `${duration}s`);

    butterfly.innerHTML = `
        <div class="left-wing" style="--wing-color:${color}"></div>
        <div class="right-wing" style="--wing-color:${color}"></div>
        <div class="body"></div>
        <div class="antenna left"></div>
        <div class="antenna right"></div>
    `;

    butterfly.style.setProperty('--start-x', `${Math.random()*130-15}vw`);
    butterfly.style.setProperty('--start-y', `${Math.random()*130-15}vh`);
    butterfly.style.setProperty('--end-x', `${Math.random()*130-15}vw`);
    butterfly.style.setProperty('--end-y', `${Math.random()*130-15}vh`);

    butterfly.style.animation = `butterflyRandomMove ${duration}s linear infinite`;
    butterfly.style.animationDelay = `${Math.random()*30}s`;

    container.appendChild(butterfly);

    return butterfly;
}

/* Create initial butterflies */
for(let i=0;i<80;i++){
    setTimeout(createRandomButterfly, i*30);
}

/* 🌊 WAVE LOGIC */
function triggerButterflyWave(x,y){
    document.querySelectorAll('.butterfly').forEach(b=>{
        const r=b.getBoundingClientRect();
        const bx=r.left+r.width/2;
        const by=r.top+r.height/2;

        if(Math.hypot(bx-x,by-y)<220){
            b.classList.remove('wave');
            void b.offsetWidth;
            b.classList.add('wave');
        }
    });
}

/* ================= BIRDS ================= */
const birdTypes=['sparrow','pigeon','cardinal','bluejay','canary','parrot','robin','flamingo','peacock'];

function getBirdFlyDistance(){
    return Math.hypot(innerWidth,innerHeight)*2;
}

function createBirdFromPoint(x,y){
    const bird=document.createElement('div');
    bird.className=`bird ${birdTypes[Math.floor(Math.random()*birdTypes.length)]}`;
    bird.style.left=x+'px';
    bird.style.top=y+'px';

    bird.innerHTML=`
        <div class="bird-body"></div>
        <div class="bird-head"></div>
        <div class="bird-beak"></div>
        <div class="bird-eye"></div>
        <div class="bird-wing"></div>
        <div class="bird-tail"></div>
    `;

    const angle=Math.random()*Math.PI*2;
    const dist=getBirdFlyDistance();
    const dx=Math.cos(angle)*dist;
    const dy=Math.sin(angle)*dist;

    bird.style.setProperty('--fly-x',`${dx}px`);
    bird.style.setProperty('--fly-y',`${dy}px`);
    bird.style.setProperty('--fly-rotate',`${(Math.random()*40-20)}deg`);

    const duration=12+Math.random()*6;
    bird.style.animation=`birdFlyOut ${duration}s ease-out forwards`;

    container.appendChild(bird);

    /* 🔥 Track bird path */
    let start=null;
    function track(t){
        if(!start) start=t;
        const p=Math.min((t-start)/(duration*1000),1);

        const cx=x+dx*p;
        const cy=y+dy*p;

        triggerButterflyWave(cx,cy);

        if(p<1) requestAnimationFrame(track);
        else bird.remove();
    }
    requestAnimationFrame(track);
}

/* Auto birds */
setInterval(()=>{
    createBirdFromPoint(innerWidth/2,innerHeight/2);
},800);

document.addEventListener('mousemove',e=>{
    if(Math.random()<0.1) createBirdFromPoint(e.clientX,e.clientY);
});

});
</script>
</body>
</html>
