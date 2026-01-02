<!doctype html>
<html lang="en">

<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Go To Login</title>

<style>
:root{
    --bg-1:#0f172a;
    --bg-2:#0b1220;
    --accent:#06b6d4;
    --accent-2:#7c3aed;
    --accent-3:#ec4899;
    --bird-gray:#94a3b8;
}

/* ===== GLOBAL ===== */
*{box-sizing:border-box}
html,body{height:100%}

body{
    margin:0;
    font-family:Inter,system-ui;
    background:
        radial-gradient(1200px 600px at 10% 20%, rgba(124,58,237,.15), transparent),
        radial-gradient(1000px 500px at 90% 80%, rgba(6,182,212,.12), transparent),
        linear-gradient(180deg,var(--bg-1),var(--bg-2));
    display:grid;
    place-items:center;
    overflow:hidden;
    cursor:none;
}

/* ===== CURSOR ===== */
.mouse-cursor{
    position:fixed;
    width:60px;height:60px;
    border-radius:50%;
    pointer-events:none;
    background:radial-gradient(circle,rgba(6,182,212,.3),transparent 70%);
    mix-blend-mode:screen;
    z-index:9999;
    transform:translate(-50%,-50%);
}

/* ===== CONTAINER ===== */
.flying-creatures{
    position:absolute;
    inset:0;
    pointer-events:none;
}

/* ===== BUTTERFLY ===== */
.butterfly{
    position:absolute;
    width:35px;height:35px;
    opacity:.7;
    pointer-events:none;
}

.butterfly .left-wing,
.butterfly .right-wing{
    position:absolute;
    width:17px;height:24px;
    border-radius:50%;
    background:var(--wing-color);
}

.butterfly .left-wing{
    left:0;
    animation:flapL .8s infinite;
}
.butterfly .right-wing{
    right:0;
    animation:flapR .8s infinite;
}

@keyframes flapL{50%{transform:rotate(-25deg)}}
@keyframes flapR{50%{transform:rotate(25deg)}}

/* 🌊 WAVE EFFECT */
@keyframes butterflyWave{
    0%{transform:translateX(0) rotate(0deg) scale(var(--base-scale))}
    25%{transform:translateX(-12px) rotate(-6deg) scale(var(--base-scale))}
    50%{transform:translateX(12px) rotate(6deg) scale(var(--base-scale))}
    75%{transform:translateX(-6px) rotate(-3deg) scale(var(--base-scale))}
    100%{transform:translateX(0) rotate(0deg) scale(var(--base-scale))}
}

.butterfly.wave{
    animation:butterflyWave 1.1s ease-in-out;
}

/* ===== BIRD ===== */
.bird{
    position:absolute;
    width:60px;height:42px;
    pointer-events:none;
    opacity:0;
}

.bird-body{
    position:absolute;
    width:36px;height:24px;
    background:var(--bird-gray);
    border-radius:50%;
    left:12px;top:9px;
}

.bird-wing{
    position:absolute;
    width:42px;height:21px;
    background:var(--bird-gray);
    border-radius:50%;
    left:15px;top:12px;
    transform-origin:15px 10px;
    animation:wing .5s infinite;
}

@keyframes wing{50%{transform:rotate(35deg)}}

/* ===== CARD ===== */
.card{
    max-width:540px;
    padding:56px 42px;
    background:rgba(255,255,255,.04);
    border-radius:20px;
    backdrop-filter:blur(25px);
    text-align:center;
    z-index:10;
}

.title{
    font-size:32px;
    font-weight:800;
    background:linear-gradient(135deg,var(--accent),var(--accent-2),var(--accent-3));
    -webkit-background-clip:text;
    color:transparent;
}

.subtitle{opacity:.8;margin-bottom:30px}

.btn{
    display:inline-flex;
    gap:14px;
    padding:18px 36px;
    background:linear-gradient(135deg,var(--accent),var(--accent-2));
    border-radius:14px;
    color:#fff;
    font-weight:700;
    text-decoration:none;
}
</style>
</head>

<body>

<div class="mouse-cursor" id="cursor"></div>
<div class="flying-creatures" id="creatures"></div>

<main class="card">
    <h1 class="title">Welcome back</h1>
    <p class="subtitle">Click the button below to continue to the login page.</p>
    <a class="btn" href="/admin">Go To Login ➜</a>
</main>

<script>
const container=document.getElementById("creatures");
const cursor=document.getElementById("cursor");

document.addEventListener("mousemove",e=>{
    cursor.style.left=e.clientX+"px";
    cursor.style.top=e.clientY+"px";
});

/* ===== BUTTERFLY ===== */
const colors=["#06b6d4","#7c3aed","#ec4899","#10b981","#f59e0b"];

function createButterfly(){
    const b=document.createElement("div");
    b.className="butterfly";

    const size=.5+Math.random()*.8;
    b.style.setProperty("--base-scale",size);
    b.style.transform=`scale(${size})`;

    const lw=document.createElement("div");
    lw.className="left-wing";
    lw.style.background=colors[Math.floor(Math.random()*colors.length)];

    const rw=document.createElement("div");
    rw.className="right-wing";
    rw.style.background=lw.style.background;

    b.append(lw,rw);

    b.style.left=Math.random()*100+"vw";
    b.style.top=Math.random()*100+"vh";

    container.appendChild(b);
}

for(let i=0;i<70;i++) createButterfly();

/* 🌊 WAVE ALONG BIRD PATH */
function waveAlongPath(x,y){
    document.querySelectorAll(".butterfly").forEach(b=>{
        const r=b.getBoundingClientRect();
        const bx=r.left+r.width/2;
        const by=r.top+r.height/2;
        if(Math.hypot(bx-x,by-y)<220){
            b.classList.remove("wave");
            void b.offsetWidth;
            b.classList.add("wave");
        }
    });
}

/* ===== BIRD ===== */
function createBird(x,y){
    const bird=document.createElement("div");
    bird.className="bird";

    bird.style.left=x+"px";
    bird.style.top=y+"px";

    bird.innerHTML=`
        <div class="bird-body"></div>
        <div class="bird-wing"></div>
    `;

    container.appendChild(bird);

    const angle=Math.random()*Math.PI*2;
    const dist=Math.hypot(innerWidth,innerHeight)*2;
    const dx=Math.cos(angle)*dist;
    const dy=Math.sin(angle)*dist;

    let start=null;
    function animate(t){
        if(!start) start=t;
        const p=Math.min((t-start)/12000,1);
        const cx=x+dx*p;
        const cy=y+dy*p;
        bird.style.transform=`translate(${dx*p}px,${dy*p}px)`;
        bird.style.opacity=1-p;

        waveAlongPath(cx,cy);

        if(p<1) requestAnimationFrame(animate);
        else bird.remove();
    }
    requestAnimationFrame(animate);
}

/* AUTO BIRDS */
setInterval(()=>{
    createBird(innerWidth/2,innerHeight/2);
},800);

document.addEventListener("mousemove",e=>{
    if(Math.random()<.1) createBird(e.clientX,e.clientY);
});
</script>

</body>
</html>
