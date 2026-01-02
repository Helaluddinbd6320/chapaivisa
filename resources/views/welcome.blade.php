<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Go To Login</title>

<style>
/* ===================== ROOT ===================== */
:root{
--bg-1:#0f172a;
--bg-2:#0b1220;
--accent:#06b6d4;
--accent-2:#7c3aed;
--accent-3:#ec4899;
}

/* ===================== BASE ===================== */
*{box-sizing:border-box}
html,body{height:100%}
body{
margin:0;
background:linear-gradient(180deg,var(--bg-1),var(--bg-2));
overflow:hidden;
font-family:system-ui;
color:#fff;
cursor:none;
}

/* ===================== CURSOR ===================== */
.mouse-cursor{
position:fixed;
width:50px;height:50px;
background:radial-gradient(circle,rgba(6,182,212,.4),transparent 70%);
border-radius:50%;
pointer-events:none;
transform:translate(-50%,-50%);
z-index:9999;
}

/* ===================== CONTAINER ===================== */
.flying-creatures{
position:absolute;
inset:0;
pointer-events:none;
overflow:hidden;
}

/* ===================== BUTTERFLY ===================== */
.butterfly{
position:absolute;
width:35px;height:35px;
opacity:.7;
--base-scale:1;
}

.left-wing,.right-wing{
position:absolute;
width:17px;height:24px;
border-radius:50%;
background:var(--wing-color);
top:5px;
}

.left-wing{left:0;animation:flapL .8s infinite}
.right-wing{right:0;animation:flapR .8s infinite}

.body{
position:absolute;
width:3px;height:24px;
background:#fff;
left:50%;
top:5px;
transform:translateX(-50%);
}

@keyframes flapL{50%{transform:rotate(-25deg)}}
@keyframes flapR{50%{transform:rotate(25deg)}}

/* ---- butterfly normal flight ---- */
@keyframes butterflyMove{
0%{transform:translate(var(--sx),var(--sy)) scale(var(--base-scale));opacity:0}
10%,90%{opacity:1}
100%{transform:translate(var(--ex),var(--ey)) rotate(360deg) scale(var(--base-scale));opacity:0}
}

/* ---- butterfly wave when bird passes ---- */
@keyframes butterflyWave{
0%{transform:translateX(0) scale(var(--base-scale))}
25%{transform:translateX(-12px) rotate(-8deg) scale(var(--base-scale))}
50%{transform:translateX(12px) rotate(8deg) scale(var(--base-scale))}
75%{transform:translateX(-6px) rotate(-4deg) scale(var(--base-scale))}
100%{transform:translateX(0) scale(var(--base-scale))}
}
.butterfly.wave{animation:butterflyWave 1.2s ease-in-out}

/* ===================== BIRD ===================== */
.bird{
position:absolute;
width:60px;height:42px;
opacity:0;
}

.bird-body{
position:absolute;
width:36px;height:24px;
background:#94a3b8;
border-radius:50%;
left:12px;top:9px;
}
.bird-head{
position:absolute;
width:21px;height:21px;
background:#1e293b;
border-radius:50%;
left:0;top:3px;
}
.bird-wing{
position:absolute;
width:42px;height:21px;
background:#1e293b;
border-radius:50%;
top:12px;left:15px;
animation:wing .5s infinite;
}
@keyframes wing{50%{transform:rotate(35deg)}}

@keyframes birdFly{
0%{opacity:0;transform:translate(0,0) scale(.8)}
10%{opacity:1}
100%{opacity:0;transform:translate(var(--fx),var(--fy)) scale(.8)}
}

/* ===================== CARD ===================== */
.card{
position:relative;
z-index:10;
background:rgba(255,255,255,.05);
padding:40px;
border-radius:20px;
text-align:center;
backdrop-filter:blur(20px);
}
.btn{
display:inline-block;
margin-top:20px;
padding:15px 30px;
background:linear-gradient(135deg,var(--accent),var(--accent-2));
border-radius:12px;
color:#fff;
text-decoration:none;
font-weight:bold;
}
</style>
</head>

<body>

<div class="mouse-cursor" id="cursor"></div>
<div class="flying-creatures" id="creatures"></div>

<main class="card">
<h1>Welcome back</h1>
<a class="btn" href="/admin">Go To Login</a>
</main>

<script>
const container=document.getElementById('creatures');
const cursor=document.getElementById('cursor');
let mouseX=innerWidth/2,mouseY=innerHeight/2;

/* ===================== CURSOR ===================== */
document.addEventListener('mousemove',e=>{
cursor.style.left=e.clientX+'px';
cursor.style.top=e.clientY+'px';
mouseX=e.clientX;mouseY=e.clientY;
createBird(mouseX,mouseY);
});

/* ===================== BUTTERFLY ===================== */
const colors=['#06b6d4','#7c3aed','#ec4899','#10b981','#f59e0b'];

function createButterfly(){
const b=document.createElement('div');
b.className='butterfly';
const color=colors[Math.random()*colors.length|0];
b.innerHTML=`
<div class="left-wing" style="--wing-color:${color}"></div>
<div class="right-wing" style="--wing-color:${color}"></div>
<div class="body"></div>`;
const scale=.4+Math.random()*.8;
b.style.setProperty('--base-scale',scale);
b.style.setProperty('--sx',`${Math.random()*120-10}vw`);
b.style.setProperty('--sy',`${Math.random()*120-10}vh`);
b.style.setProperty('--ex',`${Math.random()*120-10}vw`);
b.style.setProperty('--ey',`${Math.random()*120-10}vh`);
b.style.animation=`butterflyMove ${40+Math.random()*40}s linear infinite`;
container.appendChild(b);
}
for(let i=0;i<80;i++)createButterfly();

/* ===================== BIRD ===================== */
function createBird(x,y){
const bird=document.createElement('div');
bird.className='bird';
bird.style.left=x+'px';
bird.style.top=y+'px';
bird.innerHTML=`
<div class="bird-body"></div>
<div class="bird-head"></div>
<div class="bird-wing"></div>`;
const angle=Math.random()*Math.PI*2;
const dist=Math.hypot(innerWidth,innerHeight)*1.5;
const fx=Math.cos(angle)*dist;
const fy=Math.sin(angle)*dist;
bird.style.setProperty('--fx',fx+'px');
bird.style.setProperty('--fy',fy+'px');
bird.style.animation=`birdFly 10s ease-out forwards`;
container.appendChild(bird);

/* ---- trigger butterfly wave along path ---- */
triggerWave(x,y);

setTimeout(()=>bird.remove(),12000);
}

/* ===================== WAVE LOGIC ===================== */
function triggerWave(x,y){
document.querySelectorAll('.butterfly').forEach(b=>{
const r=b.getBoundingClientRect();
const dx=r.left+r.width/2-x;
const dy=r.top+r.height/2-y;
if(Math.hypot(dx,dy)<200){
b.classList.remove('wave');
void b.offsetWidth;
b.classList.add('wave');
}
});
}
</script>

</body>
</html>
