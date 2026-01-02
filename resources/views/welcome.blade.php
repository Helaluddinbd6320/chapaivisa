<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Go To Login</title>
    <style>
        :root {
            --bg-1: #0f172a; /* dark navy */
            --bg-2: #0b1220;
            --card: rgba(255, 255, 255, 0.03);
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

        * {
            box-sizing: border-box
        }

        html,
        body {
            height: 100%;
            overflow-x: hidden;
        }

        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, 
                         "Noto Sans Bengali", "Kalpurush", "SolaimanLipi", sans-serif;
            /* Animated background */
            background: 
                radial-gradient(1200px 600px at 10% 20%, rgba(124, 58, 237, 0.15), transparent),
                radial-gradient(1000px 500px at 90% 80%, rgba(6, 182, 212, 0.12), transparent),
                radial-gradient(800px 400px at 50% 50%, rgba(236, 72, 153, 0.08), transparent),
                linear-gradient(180deg, var(--bg-1), var(--bg-2));
            color: #e6eef8;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
            overflow-y: auto;
            position: relative;
            cursor: none;
            /* Background animation */
            animation: gradientBackground 30s ease infinite;
            background-size: 400% 400%;
            min-height: 100vh;
        }

        /* Time display container */
        .time-container {
            position: relative;
            width: 100%;
            max-width: 540px;
            margin-bottom: 30px;
            z-index: 100;
            animation: fadeInUp 0.8s ease 0.1s both;
        }

        .time-card {
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.05) 0%,
                    rgba(255, 255, 255, 0.02) 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 20px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow:
                0 15px 35px rgba(2, 6, 23, 0.6),
                inset 0 1px 0 rgba(255, 255, 255, 0.05),
                0 0 0 1px rgba(255, 255, 255, 0.02);
            text-align: center;
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .time-card:hover {
            transform: translateY(-5px);
            box-shadow:
                0 25px 50px rgba(2, 6, 23, 0.8),
                inset 0 1px 0 rgba(255, 255, 255, 0.05),
                0 0 0 1px rgba(255, 255, 255, 0.02);
        }

        .location-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .location-flag {
            font-size: 24px;
            animation: flagWave 3s ease-in-out infinite;
        }

        .location-name {
            font-size: 18px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-4), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradientShift 4s ease infinite;
            background-size: 300% 300%;
        }

        .current-time {
            font-size: 28px;
            font-weight: 800;
            margin: 10px 0;
            background: linear-gradient(135deg, #fff, var(--accent-2), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: timeGlow 2s ease-in-out infinite;
            letter-spacing: 1px;
        }

        .date-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 15px;
        }

        .date-box {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            padding: 15px 10px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .date-box:hover {
            background: rgba(255, 255, 255, 0.06);
            transform: translateY(-3px);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .date-icon {
            font-size: 20px;
            margin-bottom: 8px;
            opacity: 0.8;
        }

        .date-label {
            font-size: 12px;
            color: rgba(226, 232, 240, 0.6);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .date-value {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            line-height: 1.4;
            text-align: center;
        }

        .bangla-date {
            font-family: "Noto Sans Bengali", "Kalpurush", "SolaimanLipi", sans-serif;
            font-size: 15px;
        }

        .hijri-date {
            font-family: "Noto Sans Arabic", "Scheherazade", sans-serif;
            font-size: 15px;
        }

        /* Animated background */
        @keyframes gradientBackground {
            0% {
                background: 
                    radial-gradient(1200px 600px at 10% 20%, rgba(124, 58, 237, 0.15), transparent),
                    radial-gradient(1000px 500px at 90% 80%, rgba(6, 182, 212, 0.12), transparent),
                    radial-gradient(800px 400px at 50% 50%, rgba(236, 72, 153, 0.08), transparent),
                    linear-gradient(180deg, #0f172a, #0b1220);
            }
            25% {
                background: 
                    radial-gradient(1200px 600px at 20% 30%, rgba(124, 58, 237, 0.2), transparent),
                    radial-gradient(1000px 500px at 80% 70%, rgba(6, 182, 212, 0.18), transparent),
                    radial-gradient(800px 400px at 60% 40%, rgba(236, 72, 153, 0.12), transparent),
                    linear-gradient(180deg, #1e293b, #0f172a);
            }
            50% {
                background: 
                    radial-gradient(1200px 600px at 30% 40%, rgba(124, 58, 237, 0.25), transparent),
                    radial-gradient(1000px 500px at 70% 60%, rgba(6, 182, 212, 0.22), transparent),
                    radial-gradient(800px 400px at 40% 60%, rgba(236, 72, 153, 0.15), transparent),
                    linear-gradient(180deg, #334155, #1e293b);
            }
            75% {
                background: 
                    radial-gradient(1200px 600px at 40% 50%, rgba(124, 58, 237, 0.2), transparent),
                    radial-gradient(1000px 500px at 60% 50%, rgba(6, 182, 212, 0.18), transparent),
                    radial-gradient(800px 400px at 50% 50%, rgba(236, 72, 153, 0.12), transparent),
                    linear-gradient(180deg, #1e293b, #0f172a);
            }
            100% {
                background: 
                    radial-gradient(1200px 600px at 10% 20%, rgba(124, 58, 237, 0.15), transparent),
                    radial-gradient(1000px 500px at 90% 80%, rgba(6, 182, 212, 0.12), transparent),
                    radial-gradient(800px 400px at 50% 50%, rgba(236, 72, 153, 0.08), transparent),
                    linear-gradient(180deg, #0f172a, #0b1220);
            }
        }

        /* New animations */
        @keyframes flagWave {
            0%, 100% {
                transform: rotate(0deg) scale(1);
            }
            25% {
                transform: rotate(5deg) scale(1.1);
            }
            75% {
                transform: rotate(-5deg) scale(1.1);
            }
        }

        @keyframes timeGlow {
            0%, 100% {
                filter: drop-shadow(0 0 5px rgba(6, 182, 212, 0.5));
            }
            50% {
                filter: drop-shadow(0 0 15px rgba(6, 182, 212, 0.8));
            }
        }

        /* Custom cursor */
        .mouse-cursor {
            position: fixed;
            width: 60px;
            height: 60px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.3) 0%, transparent 70%);
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
            border: 2px solid rgba(6, 182, 212, 0.3);
            border-radius: 50%;
            animation: cursorRipple 1.5s ease-out infinite;
        }

        /* Flying creatures container */
        .flying-creatures {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            overflow: hidden;
            pointer-events: none;
        }

        /* Butterfly styles - 80 butterflies */
        .butterfly {
            position: absolute;
            width: 35px;
            height: 35px;
            filter: drop-shadow(0 3px 6px rgba(0, 0, 0, 0.3));
            opacity: 0.7;
            z-index: 0;
            pointer-events: none;
        }

        .butterfly .left-wing,
        .butterfly .right-wing {
            position: absolute;
            width: 17px;
            height: 24px;
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
            width: 3px;
            height: 24px;
            background: linear-gradient(to bottom, #fff, #ccc);
            left: 50%;
            top: 5px;
            transform: translateX(-50%);
            border-radius: 1.5px;
            box-shadow: 0 0 2px rgba(255, 255, 255, 0.3);
        }

        .butterfly .antenna {
            position: absolute;
            width: 1px;
            height: 10px;
            background: #fff;
            top: 0;
            transform-origin: bottom;
        }

        .butterfly .antenna.left {
            left: calc(50% - 1px);
            transform: rotate(-20deg);
        }

        .butterfly .antenna.right {
            right: calc(50% - 1px);
            transform: rotate(20deg);
        }

        /* Bird styles - Increased numbers */
        .bird {
            position: absolute;
            width: 60px;
            height: 42px;
            opacity: 0;
            z-index: 1;
            pointer-events: none;
            filter: drop-shadow(0 3px 5px rgba(0, 0, 0, 0.3));
        }

        /* Bird body */
        .bird-body {
            position: absolute;
            width: 36px;
            height: 24px;
            background: var(--bird-body-color, var(--bird-gray));
            border-radius: 50% 50% 40% 40%;
            left: 12px;
            top: 9px;
            box-shadow:
                inset 0 -3px 5px rgba(0, 0, 0, 0.2),
                0 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Bird head */
        .bird-head {
            position: absolute;
            width: 21px;
            height: 21px;
            background: var(--bird-head-color, var(--bird-gray));
            border-radius: 50%;
            left: 0;
            top: 3px;
            z-index: 2;
            box-shadow:
                inset 0 -2px 3px rgba(0, 0, 0, 0.2),
                0 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Bird beak */
        .bird-beak {
            position: absolute;
            width: 15px;
            height: 8px;
            background: var(--bird-beak-color, #ffb347);
            border-radius: 50% 0 0 50%;
            left: -8px;
            top: 9px;
            z-index: 1;
            clip-path: polygon(0 0, 100% 25%, 100% 75%, 0 100%);
            box-shadow: 1px 0 2px rgba(0, 0, 0, 0.2);
        }

        .bird-beak::after {
            content: '';
            position: absolute;
            width: 2px;
            height: 1px;
            background: #000;
            top: 3px;
            right: 3px;
            border-radius: 1px;
        }

        /* Bird eye */
        .bird-eye {
            position: absolute;
            width: 6px;
            height: 6px;
            background: #fff;
            border-radius: 50%;
            left: 12px;
            top: 7px;
            z-index: 3;
            box-shadow:
                0 0 0 2px rgba(0, 0, 0, 0.1),
                inset 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .bird-eye::before {
            content: '';
            position: absolute;
            width: 2px;
            height: 2px;
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
            width: 42px;
            height: 21px;
            background: var(--bird-wing-color, var(--bird-gray));
            border-radius: 50%;
            top: 12px;
            left: 15px;
            transform-origin: 15px 10px;
            animation: birdWingFlap 0.5s ease-in-out infinite;
            box-shadow:
                inset 0 -2px 5px rgba(0, 0, 0, 0.3),
                0 1px 2px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        .bird-wing::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg,
                    transparent 20%,
                    rgba(255, 255, 255, 0.1) 30%,
                    rgba(0, 0, 0, 0.1) 70%,
                    transparent 80%);
            border-radius: 50%;
        }

        /* Bird tail */
        .bird-tail {
            position: absolute;
            width: 27px;
            height: 15px;
            background: var(--bird-tail-color, var(--bird-gray));
            right: -15px;
            top: 18px;
            border-radius: 0 50% 50% 0;
            clip-path: polygon(0 0, 100% 50%, 0 100%);
            box-shadow:
                inset -2px 0 3px rgba(0, 0, 0, 0.2),
                1px 0 2px rgba(0, 0, 0, 0.1);
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
        .card {
            width: 100%;
            max-width: 540px;
            border-radius: 20px;
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.03) 0%,
                    rgba(255, 255, 255, 0.01) 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow:
                0 25px 50px rgba(2, 6, 23, 0.8),
                inset 0 1px 0 rgba(255, 255, 255, 0.05),
                0 0 0 1px rgba(255, 255, 255, 0.02);
            padding: 56px 42px;
            text-align: center;
            position: relative;
            z-index: 100;
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            transform: translateY(0) scale(1);
            transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
            cursor: auto;
            margin: 0 auto;
        }

        .card:hover {
            transform: translateY(-12px) scale(1.01);
            box-shadow:
                0 35px 70px rgba(2, 6, 23, 1),
                inset 0 1px 0 rgba(255, 255, 255, 0.05),
                0 0 0 1px rgba(255, 255, 255, 0.02);
        }

        .title {
            font-size: 32px;
            font-weight: 800;
            margin: 0 0 16px 0;
            background: linear-gradient(135deg, var(--accent), var(--accent-2), var(--accent-3));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradientShift 4s ease infinite;
            background-size: 300% 300%;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .subtitle {
            color: rgba(226, 232, 240, 0.8);
            margin: 0 0 36px 0;
            font-size: 17px;
            line-height: 1.7;
            animation: fadeInUp 0.8s ease 0.2s both;
            font-weight: 400;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            padding: 18px 36px;
            font-size: 17px;
            font-weight: 700;
            color: white;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow:
                0 12px 40px rgba(7, 10, 25, 0.8),
                0 4px 0 rgba(255, 255, 255, 0.05) inset,
                0 -2px 10px rgba(0, 0, 0, 0.3) inset;
            text-decoration: none;
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            will-change: transform, box-shadow;
            position: relative;
            overflow: hidden;
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
                    rgba(255, 255, 255, 0.1),
                    transparent);
            animation: btnShimmer 4s linear infinite;
        }

        .btn:focus {
            outline: none;
            box-shadow:
                0 0 0 4px rgba(6, 182, 212, 0.25),
                0 20px 50px rgba(7, 10, 25, 1);
        }

        .btn:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow:
                0 25px 60px rgba(7, 10, 25, 1),
                0 4px 0 rgba(255, 255, 255, 0.05) inset,
                0 -2px 10px rgba(0, 0, 0, 0.4) inset;
            letter-spacing: 0.5px;
        }

        .btn:active {
            transform: translateY(-4px) scale(0.98);
        }

        .btn .arrow {
            display: inline-grid;
            place-items: center;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow:
                0 6px 20px rgba(2, 6, 23, 0.6),
                0 2px 0 rgba(255, 255, 255, 0.05) inset;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            animation: arrowFloat 2.5s ease-in-out infinite;
            font-size: 18px;
        }

        .btn:hover .arrow {
            transform: translateX(8px) scale(1.1);
            background: rgba(255, 255, 255, 0.15);
            box-shadow:
                0 8px 25px rgba(2, 6, 23, 0.8),
                0 2px 0 rgba(255, 255, 255, 0.1) inset;
        }

        .muted {
            font-size: 14px;
            color: rgba(226, 232, 240, 0.6);
            margin-top: 28px;
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

            0%,
            100% {
                transform: rotate(0deg) translateY(0) scaleY(1);
            }

            50% {
                transform: rotate(-25deg) translateY(-2px) scaleY(0.9);
            }
        }

        @keyframes butterflyFlapRight {

            0%,
            100% {
                transform: rotate(0deg) translateY(0) scaleY(1);
            }

            50% {
                transform: rotate(25deg) translateY(-2px) scaleY(0.9);
            }
        }

        @keyframes birdWingFlap {

            0%,
            100% {
                transform: rotate(0deg);
            }

            50% {
                transform: rotate(35deg);
            }
        }

        @keyframes gradientShift {

            0%,
            100% {
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

            0%,
            100% {
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

        @keyframes cursorFloat {

            0%,
            100% {
                transform: translate(-50%, -50%) scale(1);
            }

            50% {
                transform: translate(-50%, -50%) scale(1.2);
            }
        }

        @keyframes cursorPulse {

            0%,
            100% {
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

        /* Bird animation - LONGER for big screens */
        @keyframes birdFlyOut {
            0% {
                opacity: 0;
                transform: translate(0, 0) scale(0.7) rotate(0deg);
            }

            10% {
                opacity: 1;
                transform: translate(0, 0) scale(1) rotate(0deg);
            }

            85% {
                opacity: 1;
            }

            100% {
                opacity: 0;
                transform: translate(var(--fly-x), var(--fly-y)) scale(0.8) rotate(var(--fly-rotate));
            }
        }

        /* Butterfly animation */
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
            body {
                padding: 24px 16px;
                display: flex;
                flex-direction: column;
                overflow-y: auto;
                min-height: 100vh;
                height: auto;
            }

            .time-container {
                margin-bottom: 20px;
                max-width: 95%;
            }

            .time-card {
                padding: 15px;
            }

            .current-time {
                font-size: 22px;
            }

            .date-container {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .date-box {
                padding: 12px 8px;
                flex-direction: row;
                justify-content: flex-start;
                text-align: left;
                gap: 15px;
            }

            .date-icon {
                margin-bottom: 0;
                font-size: 18px;
                min-width: 30px;
                text-align: center;
            }

            .date-value {
                text-align: left;
                font-size: 13px;
            }

            .card {
                padding: 36px 24px;
                margin: 0 auto;
                backdrop-filter: blur(15px);
                max-width: 95%;
                position: relative;
                top: 0;
                transform: none !important;
            }

            .card:hover {
                transform: none !important;
            }

            .title {
                font-size: 26px;
            }

            .btn {
                padding: 16px 28px;
                font-size: 16px;
                width: 100%;
                justify-content: center;
            }

            .bird {
                width: 45px;
                height: 32px;
            }

            .bird-body {
                width: 27px;
                height: 18px;
                left: 9px;
                top: 6px;
            }

            .bird-head {
                width: 16px;
                height: 16px;
            }

            .bird-wing {
                width: 31px;
                height: 16px;
                left: 11px;
                top: 9px;
            }

            .bird-tail {
                width: 20px;
                height: 12px;
                right: -11px;
                top: 13px;
            }

            .butterfly {
                width: 25px;
                height: 25px;
            }

            .butterfly .left-wing,
            .butterfly .right-wing {
                width: 12px;
                height: 17px;
            }

            body {
                cursor: auto;
            }

            .mouse-cursor {
                display: none;
            }

            /* Reduce numbers on mobile */
            .butterfly:nth-child(n+40) {
                display: none;
            }

            .flying-creatures {
                position: fixed;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 20px 12px;
            }

            .time-card {
                padding: 12px;
            }

            .current-time {
                font-size: 20px;
            }

            .location-name {
                font-size: 16px;
            }

            .date-value {
                font-size: 12px;
            }

            .card {
                padding: 28px 20px;
                border-radius: 16px;
                margin: 0 auto;
            }

            .title {
                font-size: 22px;
            }

            .subtitle {
                font-size: 15px;
                line-height: 1.6;
                margin-bottom: 28px;
            }

            .btn {
                padding: 16px 24px;
                font-size: 15px;
            }

            .muted {
                font-size: 13px;
                margin-top: 24px;
            }

            /* Reduce butterflies on very small screens */
            .butterfly:nth-child(n+30) {
                display: none;
            }
        }

        /* Extra small devices */
        @media (max-width: 360px) {
            .card {
                padding: 24px 16px;
            }

            .title {
                font-size: 20px;
            }

            .subtitle {
                font-size: 14px;
            }

            .btn {
                padding: 14px 20px;
                font-size: 14px;
            }

            .muted {
                font-size: 12px;
            }
        }

        /* Prevent horizontal scroll on all devices */
        @media (max-width: 768px) {
            html, body {
                overflow-x: hidden;
                width: 100%;
            }
            
            .card {
                width: calc(100% - 32px);
                max-width: none;
            }
        }
    </style>
</head>

<body>
    <!-- Custom cursor -->
    <div class="mouse-cursor" id="cursor"></div>

    <!-- Flying creatures container -->
    <div class="flying-creatures" id="creatures"></div>

    <!-- Time display container -->
    <div class="time-container">
        <div class="time-card">
            <div class="location-info">
                <div class="location-flag" id="locationFlag">🌍</div>
                <div class="location-name" id="locationName">Detecting location...</div>
            </div>
            <div class="current-time" id="currentTime">--:--:--</div>
            <div class="date-container">
                <div class="date-box">
                    <div class="date-icon">📅</div>
                    <div>
                        <div class="date-label">English Date</div>
                        <div class="date-value" id="englishDate">--</div>
                    </div>
                </div>
                <div class="date-box">
                    <div class="date-icon">📅</div>
                    <div>
                        <div class="date-label">বাংলা তারিখ</div>
                        <div class="date-value bangla-date" id="banglaDate">--</div>
                    </div>
                </div>
                <div class="date-box">
                    <div class="date-icon">📅</div>
                    <div>
                        <div class="date-label">হিজরি তারিখ</div>
                        <div class="date-value hijri-date" id="hijriDate">--</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="card" role="main">
        <h1 class="title">Welcome back</h1>
        <p class="subtitle">Click the button below to continue to the login page.</p>

        <a class="btn" href="/admin" role="button" aria-label="Go To Login">
            <span>Go To Login</span>
            <span class="arrow" aria-hidden="true">➜</span>
        </a>

        <p class="muted">Need help? <a
                href="https://wa.me/8801841484885?text=Login%20করতে%20প্রবলেম%20হচ্ছে.%20দয়া%20করে%20সহায়তা%20করুন.%20Website%20URL:%20https://chapaivisa.com/"
                target="_blank">Contact support on WhatsApp</a> — and say Hi to Helal Uddin ❤️</p>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Document loaded, creating MORE creatures...');

            const container = document.getElementById('creatures');
            const cursor = document.getElementById('cursor');

            // Show cursor initially
            cursor.style.display = 'block';

            const colors = [
                '#06b6d4', // cyan
                '#7c3aed', // purple
                '#ec4899', // pink
                '#10b981', // emerald
                '#f59e0b', // amber
                '#8b5cf6' // violet
            ];

            // Bird types - 9 varieties
            const birdTypes = ['sparrow', 'pigeon', 'cardinal', 'bluejay', 'canary', 'parrot', 'robin', 'flamingo',
                'peacock'
            ];

            // INCREASED: 80 butterflies initially
            const initialButterflyCount = 80;
            console.log(`Creating ${initialButterflyCount} butterflies...`);

            for (let i = 0; i < initialButterflyCount; i++) {
                setTimeout(() => {
                    createRandomButterfly(container, colors);
                }, i * 30); // Faster creation
            }

            // Variables for bird generation
            let mouseX = window.innerWidth / 2;
            let mouseY = window.innerHeight / 2;
            let birdGenerationInterval;
            let centerGenerationInterval;
            let isGeneratingFromCenter = true;
            const centerX = window.innerWidth / 2;
            const centerY = window.innerHeight / 2;

            // Calculate animation duration based on screen size
            function getBirdAnimationDuration() {
                const screenWidth = window.innerWidth;
                const screenHeight = window.innerHeight;
                const screenArea = screenWidth * screenHeight;

                let baseDuration = 10; // Increased base duration

                if (screenArea > 1920 * 1080) {
                    baseDuration = 18; // Longer for 4K
                } else if (screenArea > 1366 * 768) {
                    baseDuration = 14;
                } else if (screenArea > 1024 * 768) {
                    baseDuration = 12;
                }

                return baseDuration + Math.random() * 4;
            }

            function getButterflyAnimationDuration() {
                const screenWidth = window.innerWidth;
                const screenHeight = window.innerHeight;
                const screenArea = screenWidth * screenHeight;

                let baseDuration = 40; // Increased

                if (screenArea > 1920 * 1080) {
                    baseDuration = 80;
                } else if (screenArea > 1366 * 768) {
                    baseDuration = 60;
                } else if (screenArea > 1024 * 768) {
                    baseDuration = 50;
                }

                return baseDuration + Math.random() * 30;
            }

            function getBirdFlyDistance() {
                const screenWidth = window.innerWidth;
                const screenHeight = window.innerHeight;

                // Fly even longer distance
                const diagonal = Math.sqrt(screenWidth * screenWidth + screenHeight * screenHeight);
                return diagonal * 2 + Math.random() * diagonal; // 2x diagonal
            }

            // INCREASED: More frequent bird generation
            function startCenterGeneration() {
                console.log('Starting center bird generation (INCREASED)...');
                centerGenerationInterval = setInterval(() => {
                    if (isGeneratingFromCenter) {
                        // Create 2-3 birds at once from center
                        const birdCount = 2 + Math.floor(Math.random() * 2);
                        for (let i = 0; i < birdCount; i++) {
                            setTimeout(() => {
                                const birdType = birdTypes[Math.floor(Math.random() * birdTypes
                                    .length)];
                                createBirdFromPoint(container, birdType, centerX, centerY, true);
                            }, i * 100);
                        }
                    }
                }, 800); // More frequent: 800ms
            }

            // INCREASED: More frequent mouse generation
            function startMouseGeneration() {
                console.log('Switching to mouse bird generation (INCREASED)...');
                birdGenerationInterval = setInterval(() => {
                    // Create 1-2 birds at once from mouse
                    const birdCount = 1 + Math.floor(Math.random() * 2);
                    for (let i = 0; i < birdCount; i++) {
                        setTimeout(() => {
                            const birdType = birdTypes[Math.floor(Math.random() * birdTypes
                            .length)];
                            createBirdFromPoint(container, birdType, mouseX, mouseY, false);
                        }, i * 150);
                    }
                }, 400); // More frequent: 400ms
            }

            // Mouse movement tracking
            document.addEventListener('mousemove', (e) => {
                cursor.style.left = `${e.clientX}px`;
                cursor.style.top = `${e.clientY}px`;

                mouseX = e.clientX;
                mouseY = e.clientY;

                if (isGeneratingFromCenter) {
                    isGeneratingFromCenter = false;
                    clearInterval(centerGenerationInterval);
                    startMouseGeneration();
                }

                createExtraBirdsOnMove(e.movementX, e.movementY);
            });

            // Touch support
            document.addEventListener('touchmove', (e) => {
                if (e.touches.length > 0) {
                    const touch = e.touches[0];
                    mouseX = touch.clientX;
                    mouseY = touch.clientY;

                    if (isGeneratingFromCenter) {
                        isGeneratingFromCenter = false;
                        clearInterval(centerGenerationInterval);
                        startMouseGeneration();
                    }

                    cursor.style.left = `${touch.clientX}px`;
                    cursor.style.top = `${touch.clientY}px`;
                    cursor.style.opacity = '1';

                    setTimeout(() => {
                        cursor.style.opacity = '0';
                    }, 1000);

                    // Create multiple birds on touch
                    for (let i = 0; i < 3; i++) {
                        setTimeout(() => {
                            const birdType = birdTypes[Math.floor(Math.random() * birdTypes
                            .length)];
                            createBirdFromPoint(container, birdType, mouseX, mouseY, false);
                        }, i * 100);
                    }
                }
            });

            // INCREASED: More extra birds on fast movement
            function createExtraBirdsOnMove(movementX, movementY) {
                const speed = Math.sqrt(movementX * movementX + movementY * movementY);

                if (speed > 10) {
                    const extraBirds = Math.floor(speed / 15); // More birds
                    for (let i = 0; i < extraBirds && i < 4; i++) { // Up to 4 extra birds
                        setTimeout(() => {
                            const birdType = birdTypes[Math.floor(Math.random() * birdTypes.length)];
                            createBirdFromPoint(container, birdType, mouseX, mouseY, false);
                        }, i * 80);
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
                    angle = Math.random() * Math.PI * 2;
                } else {
                    angle = Math.random() * Math.PI * 2;
                }

                const distance = getBirdFlyDistance();
                const flyX = Math.cos(angle) * distance;
                const flyY = Math.sin(angle) * distance;

                const rotate = (Math.random() * 40) - 20;

                bird.style.setProperty('--fly-x', `${flyX}px`);
                bird.style.setProperty('--fly-y', `${flyY}px`);
                bird.style.setProperty('--fly-rotate', `${rotate}deg`);

                const size = 0.6 + Math.random() * 0.8;
                bird.style.transform = `scale(${size})`;

                const animationDuration = getBirdAnimationDuration();
                bird.style.animation = `birdFlyOut ${animationDuration}s ease-out forwards`;

                container.appendChild(bird);

                setTimeout(() => {
                    if (bird.parentNode) {
                        bird.remove();
                    }
                }, animationDuration * 1000 + 2000);

                return bird;
            }

            function createRandomButterfly(container, colors) {
                const color = colors[Math.floor(Math.random() * colors.length)];
                const butterfly = document.createElement('div');
                butterfly.className = 'butterfly';

                const size = 0.4 + Math.random() * 0.8;

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

                const startX = Math.random() * 130 - 15;
                const startY = Math.random() * 130 - 15;
                const endX = Math.random() * 130 - 15;
                const endY = Math.random() * 130 - 15;

                butterfly.style.setProperty('--start-x', `${startX}vw`);
                butterfly.style.setProperty('--start-y', `${startY}vh`);
                butterfly.style.setProperty('--end-x', `${endX}vw`);
                butterfly.style.setProperty('--end-y', `${endY}vh`);

                const duration = getButterflyAnimationDuration();

                butterfly.style.animation = `butterflyRandomMove ${duration}s linear infinite`;
                butterfly.style.animationDelay = `${Math.random() * 30}s`;
                butterfly.style.transform = `scale(${size})`;
                butterfly.style.opacity = '0';

                container.appendChild(butterfly);

                setTimeout(() => {
                    if (butterfly.parentNode) {
                        butterfly.remove();
                        createRandomButterfly(container, colors);
                    }
                }, (duration + 15) * 1000);

                return butterfly;
            }

            // Start center generation
            setTimeout(() => {
                startCenterGeneration();
                console.log('Center bird generation started (INCREASED)');
            }, 1000);

            // Auto-cleanup with higher limits
            setInterval(() => {
                const birds = container.querySelectorAll('.bird');
                const butterflies = container.querySelectorAll('.butterfly');

                console.log(`Current: ${birds.length} birds, ${butterflies.length} butterflies`);

                // Higher limits for birds
                if (birds.length > 60) {
                    const birdsToRemove = birds.length - 50;
                    for (let i = 0; i < birdsToRemove && i < birds.length; i++) {
                        if (birds[i].parentNode) {
                            birds[i].remove();
                        }
                    }
                }

                // Higher limits for butterflies
                if (butterflies.length > 100) {
                    const butterfliesToRemove = butterflies.length - 80;
                    for (let i = 0; i < butterfliesToRemove && i < butterflies.length; i++) {
                        if (butterflies[i].parentNode) {
                            butterflies[i].remove();
                        }
                    }
                }
            }, 20000); // Less frequent cleanup

            // Fix for mobile scrolling
            function fixMobileScrolling() {
                const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
                if (isMobile) {
                    // Prevent default touch behavior that might block scrolling
                    document.addEventListener('touchmove', function(e) {
                        if (e.target.tagName !== 'A' && e.target.tagName !== 'BUTTON') {
                            // Allow scrolling
                            return;
                        }
                    }, { passive: true });
                    
                    // Ensure body is scrollable
                    document.body.style.overflowY = 'auto';
                    document.body.style.height = 'auto';
                    document.body.style.minHeight = '100vh';
                }
            }
            
            // Call the fix
            fixMobileScrolling();

            // Time and Date Functions
            const locationFlag = document.getElementById('locationFlag');
            const locationName = document.getElementById('locationName');
            const currentTime = document.getElementById('currentTime');
            const englishDate = document.getElementById('englishDate');
            const banglaDate = document.getElementById('banglaDate');
            const hijriDate = document.getElementById('hijriDate');

            // Timezone mapping
            const timezoneMap = {
                'BD': { flag: '🇧🇩', name: 'Bangladesh Time', offset: 6 },
                'SA': { flag: '🇸🇦', name: 'Saudi Arabia Time', offset: 3 },
                'IN': { flag: '🇮🇳', name: 'India Time', offset: 5.5 },
                'PK': { flag: '🇵🇰', name: 'Pakistan Time', offset: 5 },
                'US': { flag: '🇺🇸', name: 'US Time', offset: -5 },
                'GB': { flag: '🇬🇧', name: 'UK Time', offset: 0 },
                'AU': { flag: '🇦🇺', name: 'Australia Time', offset: 10 },
                'JP': { flag: '🇯🇵', name: 'Japan Time', offset: 9 },
                'CN': { flag: '🇨🇳', name: 'China Time', offset: 8 },
                'RU': { flag: '🇷🇺', name: 'Russia Time', offset: 3 }
            };

            // Default to Bangladesh time
            let userTimezone = 'BD';
            let timezoneOffset = 6;

            // Try to detect user location
            function detectLocation() {
                // Try to get location from browser
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            // Get country code from coordinates (simplified)
                            fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${position.coords.latitude}&longitude=${position.coords.longitude}&localityLanguage=en`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.countryCode) {
                                        const countryCode = data.countryCode.toUpperCase();
                                        if (timezoneMap[countryCode]) {
                                            userTimezone = countryCode;
                                            timezoneOffset = timezoneMap[countryCode].offset;
                                        }
                                    }
                                    updateTime();
                                })
                                .catch(() => {
                                    updateTime();
                                });
                        },
                        () => {
                            // Fallback to IP-based detection
                            fetch('https://ipapi.co/json/')
                                .then(response => response.json())
                                .then(data => {
                                    if (data.country_code) {
                                        const countryCode = data.country_code.toUpperCase();
                                        if (timezoneMap[countryCode]) {
                                            userTimezone = countryCode;
                                            timezoneOffset = timezoneMap[countryCode].offset;
                                        }
                                    }
                                    updateTime();
                                })
                                .catch(() => {
                                    updateTime();
                                });
                        }
                    );
                } else {
                    updateTime();
                }
            }

            // Update time display
            function updateTime() {
                const now = new Date();
                
                // Calculate local time based on detected timezone
                const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
                const localTime = new Date(utc + (3600000 * timezoneOffset));
                
                // Format time
                const hours = localTime.getHours().toString().padStart(2, '0');
                const minutes = localTime.getMinutes().toString().padStart(2, '0');
                const seconds = localTime.getSeconds().toString().padStart(2, '0');
                
                currentTime.textContent = `${hours}:${minutes}:${seconds}`;
                
                // Update location info
                const location = timezoneMap[userTimezone] || timezoneMap['BD'];
                locationFlag.textContent = location.flag;
                locationName.textContent = location.name;
                
                // Update dates
                updateDates(localTime);
            }

            // Update all dates
            function updateDates(date) {
                // English date
                const englishDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                const englishMonths = ['January', 'February', 'March', 'April', 'May', 'June', 
                                      'July', 'August', 'September', 'October', 'November', 'December'];
                
                const englishDay = englishDays[date.getDay()];
                const englishDateNum = date.getDate();
                const englishMonth = englishMonths[date.getMonth()];
                const englishYear = date.getFullYear();
                
                englishDate.textContent = `${englishDay}, ${englishDateNum} ${englishMonth} ${englishYear}`;

                // Bengali date
                updateBanglaDate(date);

                // Hijri date
                updateHijriDate(date);
            }

            // Bengali date conversion
            function updateBanglaDate(date) {
                const banglaMonths = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 
                                     'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
                const banglaSeasons = ['পৌষ', 'মাঘ', 'ফাল্গুন', 'চৈত্র', 'বৈশাখ', 'জ্যৈষ্ঠ', 
                                      'আষাঢ়', 'শ্রাবণ', 'ভাদ্র', 'আশ্বিন', 'কার্তিক', 'অগ্রহায়ণ'];
                
                const day = date.getDate();
                const month = date.getMonth();
                const year = date.getFullYear();
                
                // Convert to Bengali year (1421 = 2024-2025)
                const banglaYear = year - 594;
                
                // Get Bengali month (simplified calculation)
                let banglaMonthIndex;
                let banglaDay;
                
                if (month < 3) {
                    // January-March: Poush-Magh
                    banglaMonthIndex = (month + 9) % 12;
                    banglaDay = day;
                } else {
                    // April-December: Chaitra-Agrahayan
                    banglaMonthIndex = month - 3;
                    banglaDay = day;
                }
                
                const banglaMonthName = banglaSeasons[banglaMonthIndex];
                
                // Format: ১৭ পৌষ ১৪৩২ বঙ্গাব্দ
                banglaDate.textContent = `${convertToBanglaNumber(banglaDay)} ${banglaMonthName} ${convertToBanglaNumber(banglaYear)} বঙ্গাব্দ`;
            }

            // Hijri date conversion
            function updateHijriDate(date) {
                // Simple Hijri date calculation (approximate)
                const hijriMonths = ['মুহররম', 'সফর', 'রবিউল আউয়াল', 'রবিউস সানি', 
                                    'জমাদিউল আউয়াল', 'জমাদিউস সানি', 'রজব', 'শাবান', 
                                    'রমজান', 'শাওয়াল', 'জিলকদ', 'জিলহজ্জ'];
                
                // Approximate conversion (2026 Gregorian = 1447-1448 Hijri)
                const startHijri = new Date(2026, 0, 1); // Jan 1, 2026
                const diffTime = date.getTime() - startHijri.getTime();
                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                
                let hijriYear = 1447;
                let hijriDay = diffDays % 30 + 1;
                let hijriMonthIndex = Math.floor((diffDays % 365) / 30);
                
                if (hijriMonthIndex > 11) hijriMonthIndex = 11;
                
                const hijriMonthName = hijriMonths[hijriMonthIndex];
                
                // Format: ১৩ রজব ১৪৪৭ হিজরি
                hijriDate.textContent = `${convertToBanglaNumber(hijriDay)} ${hijriMonthName} ${convertToBanglaNumber(hijriYear)} হিজরি`;
            }

            // Convert numbers to Bengali
            function convertToBanglaNumber(num) {
                const banglaNumbers = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
                return num.toString().split('').map(digit => banglaNumbers[parseInt(digit)] || digit).join('');
            }

            // Initialize time display
            detectLocation();
            setInterval(updateTime, 1000);
        });
    </script>
</body>

</html>