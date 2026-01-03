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
            --time-white: #ffffff;
            --time-light: #f1f5f9;
            --time-gray: #cbd5e1;
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
            padding: 32px 20px;
            overflow-y: auto;
            position: relative;
            cursor: none;
            /* Background animation */
            animation: gradientBackground 30s ease infinite;
            background-size: 400% 400%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Main layout for all devices */
        .main-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        /* Mobile layout - shown by default */
        .mobile-layout {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        /* Desktop layout - hidden on mobile */
        .desktop-layout {
            display: none;
            width: 100%;
            flex-direction: column;
            gap: 40px;
        }

        @media (min-width: 1024px) {
            .mobile-layout {
                display: none;
            }
            
            .desktop-layout {
                display: flex;
            }
            
            .datetime-container {
                display: flex;
                flex-direction: row;
                gap: 40px;
            }
            
            .datetime-container > div {
                flex: 1;
            }
        }

        /* Time display container with integrated date slider */
        .time-container {
            position: relative;
            width: 100%;
            z-index: 100;
            animation: fadeInUp 0.8s ease 0.1s both;
        }

        .time-card {
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.05) 0%,
                    rgba(255, 255, 255, 0.02) 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 25px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow:
                0 15px 35px rgba(2, 6, 23, 0.6),
                inset 0 1px 0 rgba(255, 255, 255, 0.05),
                0 0 0 1px rgba(255, 255, 255, 0.02);
            text-align: center;
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            height: 100%;
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
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .location-flag {
            font-size: 28px;
            animation: flagWave 3s ease-in-out infinite;
        }

        .location-name {
            font-size: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-4), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradientShift 4s ease infinite;
            background-size: 300% 300%;
        }

        /* Time Display Design - WHITE COLOR */
        .time-display {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            margin: 25px 0 35px 0;
        }

        .time-segment {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .time-number-container {
            position: relative;
            perspective: 500px;
            margin-bottom: 5px;
        }

        .time-number {
            font-size: 52px;
            font-weight: 900;
            background: linear-gradient(135deg, var(--time-white), var(--time-light), var(--time-gray));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: timeGlowWhite 3s ease-in-out infinite;
            letter-spacing: 2px;
            text-shadow: 
                0 0 10px rgba(255, 255, 255, 0.5),
                0 0 20px rgba(255, 255, 255, 0.3),
                0 0 30px rgba(255, 255, 255, 0.2);
            min-width: 85px;
            text-align: center;
            padding: 5px 0;
            position: relative;
            z-index: 2;
            transform-style: preserve-3d;
            transition: transform 0.5s ease;
        }

        @media (min-width: 1024px) {
            .time-number {
                font-size: 60px;
                min-width: 100px;
            }
        }

        .time-number::before {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            background: linear-gradient(45deg, 
                transparent, 
                rgba(255, 255, 255, 0.1), 
                rgba(255, 255, 255, 0.15),
                rgba(203, 213, 225, 0.1),
                transparent);
            border-radius: 12px;
            z-index: -1;
            animation: borderGlowWhite 4s linear infinite;
            filter: blur(10px);
        }

        .time-number::after {
            content: attr(data-value);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, var(--time-white), var(--time-light), var(--time-gray));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            opacity: 0.3;
            transform: translateY(2px) scale(1.02);
            filter: blur(3px);
            z-index: -2;
        }

        .time-label {
            font-size: 13px;
            color: var(--time-light);
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
            animation: labelPulseWhite 2s ease-in-out infinite;
        }

        .time-colon {
            font-size: 52px;
            font-weight: 900;
            color: var(--time-white);
            animation: colonPulseWhite 2s infinite;
            margin: 0 5px;
            text-shadow: 
                0 0 15px rgba(255, 255, 255, 0.7),
                0 0 25px rgba(255, 255, 255, 0.4);
            position: relative;
            top: -10px;
        }

        @media (min-width: 1024px) {
            .time-colon {
                font-size: 60px;
            }
        }

        .time-am-pm {
            font-size: 26px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--time-light), var(--time-white));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-left: 15px;
            padding: 10px 20px;
            border-radius: 25px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            animation: amPmGlowWhite 3s ease-in-out infinite;
            box-shadow: 
                0 5px 20px rgba(255, 255, 255, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
            top: -10px;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        /* Desktop Date Container - Three dates in one card */
        .date-container-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 10px;
        }

        /* Mobile Date Slider Container - Now inside time card */
        .date-slider-container {
            position: relative;
            width: 100%;
            height: 140px;
            overflow: hidden;
            border-radius: 12px;
            margin-top: 15px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        @media (max-width: 480px) {
            .date-slider-container {
                height: 130px;
            }
        }

        .date-slider {
            display: flex;
            width: 300%;
            height: 100%;
            transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .date-slide {
            flex: 0 0 33.333%;
            width: 33.333%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 15px;
        }

        /* Slider indicators */
        .slider-indicators {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 12px;
        }

        .slider-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .slider-indicator.active {
            background: var(--accent);
            transform: scale(1.2);
        }

        .date-type {
            font-size: 11px;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .date-value {
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
            text-align: center;
            margin-bottom: 5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        @media (max-width: 480px) {
            .date-value {
                font-size: 15px;
            }
        }

        .date-simple {
            font-size: 13px;
            color: rgba(226, 232, 240, 0.7);
            font-weight: 500;
            margin-top: 3px;
        }

        .bangla-date {
            font-family: "Noto Sans Bengali", "Kalpurush", "SolaimanLipi", sans-serif;
        }

        .hijri-date {
            font-family: "Noto Sans Arabic", "Scheherazade", sans-serif;
            direction: rtl;
        }

        /* Center card for login - Now higher up on mobile */
        .center-card-container {
            width: 100%;
            display: flex;
            justify-content: center;
            animation: fadeInUp 0.8s ease 0.4s both;
            margin-top: 20px;
        }

        @media (min-width: 1024px) {
            .center-card-container {
                margin-top: 40px;
            }
        }

        /* Card styles - Center */
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
        }

        @media (min-width: 1024px) {
            .card {
                animation: cardFloat 3s ease-in-out infinite;
            }
            
            .card:hover {
                transform: translateY(-12px) scale(1.01);
                animation: none;
                box-shadow:
                    0 35px 70px rgba(2, 6, 23, 1),
                    inset 0 1px 0 rgba(255, 255, 255, 0.05),
                    0 0 0 1px rgba(255, 255, 255, 0.02);
            }
        }

        @media (max-width: 768px) {
            .card {
                padding: 36px 24px;
                border-radius: 16px;
                margin-top: 10px;
            }
        }

        @keyframes cardFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
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

        /* New Animations - WHITE VERSION */
        @keyframes timeGlowWhite {
            0%, 100% {
                filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.6))
                       drop-shadow(0 0 30px rgba(241, 245, 249, 0.4));
                transform: scale(1);
            }
            50% {
                filter: drop-shadow(0 0 30px rgba(255, 255, 255, 0.9))
                       drop-shadow(0 0 40px rgba(241, 245, 249, 0.6));
                transform: scale(1.03);
            }
        }

        @keyframes borderGlowWhite {
            0% {
                opacity: 0.5;
                transform: rotate(0deg);
            }
            25% {
                opacity: 0.8;
            }
            50% {
                opacity: 0.5;
                transform: rotate(180deg);
            }
            75% {
                opacity: 0.8;
            }
            100% {
                opacity: 0.5;
                transform: rotate(360deg);
            }
        }

        @keyframes colonPulseWhite {
            0%, 100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
            50% {
                opacity: 0.7;
                transform: scale(1.1) translateY(-2px);
            }
        }

        @keyframes amPmGlowWhite {
            0%, 100% {
                transform: scale(1);
                box-shadow: 
                    0 5px 20px rgba(255, 255, 255, 0.2),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1);
            }
            50% {
                transform: scale(1.08);
                box-shadow: 
                    0 8px 30px rgba(255, 255, 255, 0.3),
                    inset 0 1px 0 rgba(255, 255, 255, 0.2);
            }
        }

        @keyframes labelPulseWhite {
            0%, 100% {
                opacity: 0.8;
                transform: translateY(0);
            }
            50% {
                opacity: 1;
                transform: translateY(-2px);
            }
        }

        @keyframes digitFlip {
            0% {
                transform: rotateX(0deg);
            }
            50% {
                transform: rotateX(90deg);
            }
            100% {
                transform: rotateX(0deg);
            }
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

        /* Flying creatures container - FIXED POSITION */
        .flying-creatures {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
            overflow: hidden;
            pointer-events: none;
        }

        /* Butterfly styles - MULTICOLOR butterflies */
        .butterfly {
            position: absolute;
            width: 35px;
            height: 35px;
            filter: drop-shadow(0 3px 6px rgba(0, 0, 0, 0.3));
            opacity: 0.7;
            z-index: 1;
            pointer-events: none;
        }

        .butterfly .left-wing,
        .butterfly .right-wing {
            position: absolute;
            width: 17px;
            height: 24px;
            border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
            transform-origin: center;
            background: var(--wing-color, #06b6d4);
        }

        .butterfly .left-wing {
            left: 0;
            background: radial-gradient(circle at 70% 30%, var(--wing-color), color-mix(in srgb, var(--wing-color) 80%, transparent));
            animation: butterflyFlapLeft 0.8s ease-in-out infinite;
        }

        .butterfly .right-wing {
            right: 0;
            background: radial-gradient(circle at 30% 30%, var(--wing-color), color-mix(in srgb, var(--wing-color) 80%, transparent));
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

        /* Bird styles - FIXED */
        .bird {
            position: absolute;
            width: 60px;
            height: 42px;
            opacity: 0;
            z-index: 2;
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

        /* Bird types */
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

        /* Bird animation */
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

        /* Performance optimization */
        @media (max-width: 768px) {
            body {
                padding: 24px 16px;
                gap: 20px;
            }

            .time-container {
                max-width: 95%;
            }

            .time-card {
                padding: 20px;
            }

            .time-number {
                font-size: 42px;
                min-width: 70px;
            }

            .time-colon {
                font-size: 42px;
            }

            .time-am-pm {
                font-size: 22px;
                padding: 8px 16px;
            }

            .location-name {
                font-size: 18px;
            }

            .date-slider-container {
                height: 130px;
            }

            .card {
                padding: 36px 24px;
                max-width: 95%;
                margin-top: 10px;
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

            body {
                cursor: auto;
            }

            .mouse-cursor {
                display: none;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 20px 12px;
                gap: 15px;
            }

            .time-card {
                padding: 15px;
            }

            .time-number {
                font-size: 36px;
                min-width: 60px;
            }

            .time-colon {
                font-size: 36px;
            }

            .time-am-pm {
                font-size: 20px;
                padding: 6px 12px;
            }

            .date-slider-container {
                height: 120px;
            }

            .date-value {
                font-size: 15px;
            }

            .date-simple {
                font-size: 12px;
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
        }
    </style>
</head>

<body>
    <!-- Custom cursor -->
    <div class="mouse-cursor" id="cursor"></div>

    <!-- Flying creatures container - FIXED POSITION -->
    <div class="flying-creatures" id="creatures"></div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Mobile Layout (shown by default) -->
        <div class="mobile-layout">
            <!-- Time Container with integrated Date Slider -->
            <div class="time-container">
                <div class="time-card">
                    <div class="location-info">
                        <div class="location-flag" id="locationFlag">🌍</div>
                        <div class="location-name" id="locationName">Detecting location...</div>
                    </div>
                    
                    <!-- Time Display -->
                    <div class="time-display">
                        <div class="time-segment">
                            <div class="time-number-container">
                                <div class="time-number" id="hours" data-value="12">12</div>
                            </div>
                            <div class="time-label">Hours</div>
                        </div>
                        <div class="time-colon">:</div>
                        <div class="time-segment">
                            <div class="time-number-container">
                                <div class="time-number" id="minutes" data-value="00">00</div>
                            </div>
                            <div class="time-label">Minutes</div>
                        </div>
                        <div class="time-colon">:</div>
                        <div class="time-segment">
                            <div class="time-number-container">
                                <div class="time-number" id="seconds" data-value="00">00</div>
                            </div>
                            <div class="time-label">Seconds</div>
                        </div>
                        <div class="time-am-pm" id="ampm">AM</div>
                    </div>
                    
                    <!-- Date Slider inside Time Card -->
                    <div class="date-slider-container">
                        <div class="date-slider" id="dateSlider">
                            <!-- English Date Slide -->
                            <div class="date-slide" data-index="0">
                                <div class="date-type">English Date</div>
                                <div class="date-value" id="englishDate">Friday, 3 January 2026</div>
                                <div class="date-simple" id="englishSimple">3 Jan 2026</div>
                            </div>
                            
                            <!-- Bengali Date Slide -->
                            <div class="date-slide" data-index="1">
                                <div class="date-type">বাংলা তারিখ</div>
                                <div class="date-value bangla-date" id="banglaDate">১৮ পৌষ ১৪৩২</div>
                                <div class="date-simple bangla-date" id="banglaSimple">বঙ্গাব্দ</div>
                            </div>
                            
                            <!-- Hijri Date Slide -->
                            <div class="date-slide" data-index="2">
                                <div class="date-type">التاريخ الهجري</div>
                                <div class="date-value hijri-date" id="hijriDate">١٤ رجب ١٤٤٧</div>
                                <div class="date-simple hijri-date" id="hijriSimple">هـ</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Slider Indicators -->
                    <div class="slider-indicators">
                        <div class="slider-indicator active" data-index="0"></div>
                        <div class="slider-indicator" data-index="1"></div>
                        <div class="slider-indicator" data-index="2"></div>
                    </div>
                </div>
            </div>

            <!-- Center Card for Login - Now higher up -->
            <div class="center-card-container">
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
            </div>
        </div>

        <!-- Desktop Layout (hidden on mobile) -->
        <div class="desktop-layout">
            <div class="datetime-container">
                <!-- Date Card -->
                <div class="date-container-full">
                    <div class="date-card">
                        <div class="date-container-grid">
                            <div class="date-box">
                                <div class="date-type">English Date</div>
                                <div class="date-value" id="desktopEnglishDate">Friday, 3 January 2026</div>
                                <div class="date-simple" id="desktopEnglishSimple">3 Jan 2026</div>
                            </div>
                            <div class="date-box">
                                <div class="date-type">বাংলা তারিখ</div>
                                <div class="date-value bangla-date" id="desktopBanglaDate">১৮ পৌষ ১৪৩২</div>
                                <div class="date-simple bangla-date" id="desktopBanglaSimple">বঙ্গাব্দ</div>
                            </div>
                            <div class="date-box">
                                <div class="date-type">التاريخ الهجري</div>
                                <div class="date-value hijri-date" id="desktopHijriDate">١٤ رجب ١٤٤٧</div>
                                <div class="date-simple hijri-date" id="desktopHijriSimple">هـ</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Time Card -->
                <div class="time-container">
                    <div class="time-card">
                        <div class="location-info">
                            <div class="location-flag" id="desktopLocationFlag">🌍</div>
                            <div class="location-name" id="desktopLocationName">Detecting location...</div>
                        </div>
                        
                        <!-- Time Display -->
                        <div class="time-display">
                            <div class="time-segment">
                                <div class="time-number-container">
                                    <div class="time-number" id="desktopHours" data-value="12">12</div>
                                </div>
                                <div class="time-label">Hours</div>
                            </div>
                            <div class="time-colon">:</div>
                            <div class="time-segment">
                                <div class="time-number-container">
                                    <div class="time-number" id="desktopMinutes" data-value="00">00</div>
                                </div>
                                <div class="time-label">Minutes</div>
                            </div>
                            <div class="time-colon">:</div>
                            <div class="time-segment">
                                <div class="time-number-container">
                                    <div class="time-number" id="desktopSeconds" data-value="00">00</div>
                                </div>
                                <div class="time-label">Seconds</div>
                            </div>
                            <div class="time-am-pm" id="desktopAmpm">AM</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Center Card for Login -->
            <div class="center-card-container">
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
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Document loaded, creating creatures...');

            const container = document.getElementById('creatures');
            const cursor = document.getElementById('cursor');

            // Show cursor initially
            cursor.style.display = 'block';

            // Create creatures immediately
            createCreatures();

            // ============================================
            // CREATE CREATURES FUNCTION (FIXED)
            // ============================================
            function createCreatures() {
                console.log('Creating creatures...');
                
                // MULTICOLOR butterfly colors
                const colors = [
                    '#06b6d4', // cyan
                    '#7c3aed', // purple
                    '#ec4899', // pink
                    '#10b981', // emerald
                    '#f59e0b', // amber
                    '#8b5cf6', // violet
                    '#ef4444', // red
                    '#3b82f6', // blue
                    '#fbbf24', // yellow
                    '#84cc16', // lime
                    '#14b8a6', // teal
                    '#f97316'  // orange
                ];

                // Bird types
                const birdTypes = ['sparrow', 'pigeon', 'cardinal', 'bluejay', 'canary', 'parrot', 'robin', 'flamingo',
                    'peacock'
                ];

                // Create butterflies
                const initialButterflyCount = 80;
                console.log(`Creating ${initialButterflyCount} MULTICOLOR butterflies...`);

                for (let i = 0; i < initialButterflyCount; i++) {
                    setTimeout(() => {
                        createRandomButterfly(container, colors);
                    }, i * 30);
                }

                // Variables for bird generation
                let mouseX = window.innerWidth / 2;
                let mouseY = window.innerHeight / 2;
                let birdGenerationInterval;
                let centerGenerationInterval;
                let isGeneratingFromCenter = true;
                const centerX = window.innerWidth / 2;
                const centerY = window.innerHeight / 2;

                function getBirdAnimationDuration() {
                    const screenWidth = window.innerWidth;
                    const screenHeight = window.innerHeight;
                    const screenArea = screenWidth * screenHeight;

                    let baseDuration = 10;

                    if (screenArea > 1920 * 1080) {
                        baseDuration = 18;
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

                    let baseDuration = 40;

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
                    const diagonal = Math.sqrt(screenWidth * screenWidth + screenHeight * screenHeight);
                    return diagonal * 2 + Math.random() * diagonal;
                }

                // Bird generation functions
                function startCenterGeneration() {
                    console.log('Starting center bird generation...');
                    centerGenerationInterval = setInterval(() => {
                        if (isGeneratingFromCenter) {
                            const birdCount = 2 + Math.floor(Math.random() * 2);
                            for (let i = 0; i < birdCount; i++) {
                                setTimeout(() => {
                                    const birdType = birdTypes[Math.floor(Math.random() * birdTypes.length)];
                                    createBirdFromPoint(container, birdType, centerX, centerY, true);
                                }, i * 100);
                            }
                        }
                    }, 800);
                }

                function startMouseGeneration() {
                    console.log('Switching to mouse bird generation...');
                    birdGenerationInterval = setInterval(() => {
                        const birdCount = 1 + Math.floor(Math.random() * 2);
                        for (let i = 0; i < birdCount; i++) {
                            setTimeout(() => {
                                const birdType = birdTypes[Math.floor(Math.random() * birdTypes.length)];
                                createBirdFromPoint(container, birdType, mouseX, mouseY, false);
                            }, i * 150);
                        }
                    }, 400);
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

                        for (let i = 0; i < 3; i++) {
                            setTimeout(() => {
                                const birdType = birdTypes[Math.floor(Math.random() * birdTypes.length)];
                                createBirdFromPoint(container, birdType, mouseX, mouseY, false);
                            }, i * 100);
                        }
                    }
                });

                function createExtraBirdsOnMove(movementX, movementY) {
                    const speed = Math.sqrt(movementX * movementX + movementY * movementY);

                    if (speed > 10) {
                        const extraBirds = Math.floor(speed / 15);
                        for (let i = 0; i < extraBirds && i < 4; i++) {
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
                    console.log('Center bird generation started');
                }, 1000);

                // Auto-cleanup
                setInterval(() => {
                    const birds = container.querySelectorAll('.bird');
                    const butterflies = container.querySelectorAll('.butterfly');

                    console.log(`Current: ${birds.length} birds, ${butterflies.length} butterflies`);

                    if (birds.length > 60) {
                        const birdsToRemove = birds.length - 50;
                        for (let i = 0; i < birdsToRemove && i < birds.length; i++) {
                            if (birds[i].parentNode) {
                                birds[i].remove();
                            }
                        }
                    }

                    if (butterflies.length > 100) {
                        const butterfliesToRemove = butterflies.length - 80;
                        for (let i = 0; i < butterfliesToRemove && i < butterflies.length; i++) {
                            if (butterflies[i].parentNode) {
                                butterflies[i].remove();
                            }
                        }
                    }
                }, 20000);
            }

            // ============================================
            // DATE SLIDER FUNCTIONALITY FOR MOBILE
            // ============================================
            
            let currentSlide = 0;
            const totalSlides = 3;
            let slideInterval;
            
            function initDateSlider() {
                const slider = document.getElementById('dateSlider');
                const indicators = document.querySelectorAll('.slider-indicator');
                
                // Auto slide every 2 seconds
                slideInterval = setInterval(() => {
                    nextSlide();
                }, 2000);
                
                // Add click event to indicators
                indicators.forEach(indicator => {
                    indicator.addEventListener('click', () => {
                        const index = parseInt(indicator.getAttribute('data-index'));
                        goToSlide(index);
                    });
                });
                
                // Pause on hover
                const sliderContainer = document.querySelector('.date-slider-container');
                sliderContainer.addEventListener('mouseenter', () => {
                    clearInterval(slideInterval);
                });
                
                sliderContainer.addEventListener('mouseleave', () => {
                    slideInterval = setInterval(() => {
                        nextSlide();
                    }, 2000);
                });
                
                // Touch swipe support
                let touchStartX = 0;
                let touchEndX = 0;
                
                sliderContainer.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                    clearInterval(slideInterval);
                });
                
                sliderContainer.addEventListener('touchend', (e) => {
                    touchEndX = e.changedTouches[0].screenX;
                    handleSwipe();
                    slideInterval = setInterval(() => {
                        nextSlide();
                    }, 2000);
                });
                
                function handleSwipe() {
                    const swipeThreshold = 50;
                    const diff = touchStartX - touchEndX;
                    
                    if (Math.abs(diff) > swipeThreshold) {
                        if (diff > 0) {
                            nextSlide(); // Swipe left
                        } else {
                            prevSlide(); // Swipe right
                        }
                    }
                }
            }
            
            function nextSlide() {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateSlider();
            }
            
            function prevSlide() {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                updateSlider();
            }
            
            function goToSlide(index) {
                currentSlide = index;
                updateSlider();
            }
            
            function updateSlider() {
                const slider = document.getElementById('dateSlider');
                const indicators = document.querySelectorAll('.slider-indicator');
                
                // Update slider position
                slider.style.transform = `translateX(-${currentSlide * 33.333}%)`;
                
                // Update indicators
                indicators.forEach((indicator, index) => {
                    if (index === currentSlide) {
                        indicator.classList.add('active');
                    } else {
                        indicator.classList.remove('active');
                    }
                });
            }
            
            // Initialize slider only on mobile
            if (window.innerWidth < 1024) {
                initDateSlider();
            }
            
            // Reinitialize on resize
            window.addEventListener('resize', () => {
                if (window.innerWidth < 1024 && !slideInterval) {
                    initDateSlider();
                } else if (window.innerWidth >= 1024 && slideInterval) {
                    clearInterval(slideInterval);
                    slideInterval = null;
                }
            });

            // Time and Date Functions
            const locationFlag = document.getElementById('locationFlag');
            const locationName = document.getElementById('locationName');
            const hoursElement = document.getElementById('hours');
            const minutesElement = document.getElementById('minutes');
            const secondsElement = document.getElementById('seconds');
            const ampmElement = document.getElementById('ampm');
            const englishDate = document.getElementById('englishDate');
            const banglaDate = document.getElementById('banglaDate');
            const hijriDate = document.getElementById('hijriDate');

            // Desktop elements
            const desktopLocationFlag = document.getElementById('desktopLocationFlag');
            const desktopLocationName = document.getElementById('desktopLocationName');
            const desktopHoursElement = document.getElementById('desktopHours');
            const desktopMinutesElement = document.getElementById('desktopMinutes');
            const desktopSecondsElement = document.getElementById('desktopSeconds');
            const desktopAmpmElement = document.getElementById('desktopAmpm');
            const desktopEnglishDate = document.getElementById('desktopEnglishDate');
            const desktopBanglaDate = document.getElementById('desktopBanglaDate');
            const desktopHijriDate = document.getElementById('desktopHijriDate');

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

            let userTimezone = 'BD';
            let timezoneOffset = 6;

            function detectLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
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

            function updateTime() {
                const now = new Date();
                const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
                const localTime = new Date(utc + (3600000 * timezoneOffset));
                
                let hours = localTime.getHours();
                const minutes = localTime.getMinutes();
                const seconds = localTime.getSeconds();
                
                let ampm = 'AM';
                if (hours >= 12) {
                    ampm = 'PM';
                    if (hours > 12) {
                        hours = hours - 12;
                    }
                }
                if (hours === 0) {
                    hours = 12;
                }
                
                const formattedHours = hours.toString().padStart(2, '0');
                const formattedMinutes = minutes.toString().padStart(2, '0');
                const formattedSeconds = seconds.toString().padStart(2, '0');
                
                // Update mobile elements
                updateTimeElementWithFlip(hoursElement, formattedHours);
                updateTimeElementWithFlip(minutesElement, formattedMinutes);
                updateTimeElementWithFlip(secondsElement, formattedSeconds);
                
                if (ampmElement.textContent !== ampm) {
                    ampmElement.style.animation = 'none';
                    setTimeout(() => {
                        ampmElement.style.animation = 'amPmGlowWhite 3s ease-in-out infinite';
                        ampmElement.textContent = ampm;
                    }, 10);
                }
                
                // Update desktop elements
                updateTimeElementWithFlip(desktopHoursElement, formattedHours);
                updateTimeElementWithFlip(desktopMinutesElement, formattedMinutes);
                updateTimeElementWithFlip(desktopSecondsElement, formattedSeconds);
                
                if (desktopAmpmElement.textContent !== ampm) {
                    desktopAmpmElement.style.animation = 'none';
                    setTimeout(() => {
                        desktopAmpmElement.style.animation = 'amPmGlowWhite 3s ease-in-out infinite';
                        desktopAmpmElement.textContent = ampm;
                    }, 10);
                }
                
                const location = timezoneMap[userTimezone] || timezoneMap['BD'];
                
                // Update mobile location
                locationFlag.textContent = location.flag;
                locationName.textContent = location.name;
                
                // Update desktop location
                desktopLocationFlag.textContent = location.flag;
                desktopLocationName.textContent = location.name;
                
                // Update dates
                updateDates(localTime);
            }

            function updateTimeElementWithFlip(element, newValue) {
                if (element && element.textContent !== newValue) {
                    const oldValue = element.textContent;
                    element.setAttribute('data-value', oldValue);
                    
                    element.style.animation = 'none';
                    element.style.transform = 'rotateX(0deg)';
                    
                    setTimeout(() => {
                        element.style.animation = 'digitFlip 0.6s ease';
                        element.textContent = newValue;
                        
                        setTimeout(() => {
                            element.style.animation = 'timeGlowWhite 3s ease-in-out infinite';
                        }, 600);
                    }, 10);
                }
            }

            function updateDates(date) {
                // English date
                const englishDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                const englishMonths = ['January', 'February', 'March', 'April', 'May', 'June', 
                                      'July', 'August', 'September', 'October', 'November', 'December'];
                
                const englishDay = englishDays[date.getDay()];
                const englishDateNum = date.getDate();
                const englishMonth = englishMonths[date.getMonth()];
                const englishYear = date.getFullYear();
                
                const englishFull = `${englishDay}, ${englishDateNum} ${englishMonth} ${englishYear}`;
                const englishShort = `${englishDateNum} ${englishMonth.slice(0, 3)} ${englishYear}`;
                
                // Update mobile
                englishDate.textContent = englishFull;
                
                // Update desktop
                desktopEnglishDate.textContent = englishFull;

                // Bengali date
                const banglaDateResult = getBanglaDate(date);
                const banglaFull = `${banglaDateResult.day} ${banglaDateResult.month} ${banglaDateResult.year}`;
                
                banglaDate.textContent = banglaFull;
                desktopBanglaDate.textContent = banglaFull;

                // Hijri date
                const hijriDateResult = getHijriDate(date);
                const hijriFull = hijriDateResult.arabicFull;
                
                hijriDate.textContent = hijriFull;
                desktopHijriDate.textContent = hijriFull;
            }

            function getBanglaDate(gregorianDate) {
                const banglaMonths = ['বৈশাখ', 'জ্যৈষ্ঠ', 'আষাঢ়', 'শ্রাবণ', 'ভাদ্র', 'আশ্বিন', 
                                     'কার্তিক', 'অগ্রহায়ণ', 'পৌষ', 'মাঘ', 'ফাল্গুন', 'চৈত্র'];
                
                const day = gregorianDate.getDate();
                const month = gregorianDate.getMonth();
                const year = gregorianDate.getFullYear();
                
                let banglaYear = year - 593;
                if (month < 3) {
                    banglaYear -= 1;
                }
                
                let banglaMonthIndex = (month + 8) % 12;
                let banglaDay = day;
                
                if ([0, 2, 4, 6, 7, 9, 11].includes(banglaMonthIndex)) {
                    if (day > 17) {
                        banglaDay = day - 17;
                        banglaMonthIndex = (banglaMonthIndex + 1) % 12;
                        if (banglaMonthIndex === 0) {
                            banglaYear += 1;
                        }
                    } else {
                        banglaDay = day + 14;
                    }
                } else {
                    if (day > 16) {
                        banglaDay = day - 16;
                        banglaMonthIndex = (banglaMonthIndex + 1) % 12;
                        if (banglaMonthIndex === 0) {
                            banglaYear += 1;
                        }
                    } else {
                        banglaDay = day + 15;
                    }
                }
                
                return {
                    day: convertToBanglaNumber(banglaDay),
                    month: banglaMonths[banglaMonthIndex],
                    year: convertToBanglaNumber(banglaYear)
                };
            }

            function getHijriDate(gregorianDate) {
                const gregorianYear = gregorianDate.getFullYear();
                const gregorianMonth = gregorianDate.getMonth() + 1;
                const gregorianDay = gregorianDate.getDate();
                
                let hijriDay, hijriMonth, hijriYear;
                
                if (gregorianYear === 2026 && gregorianMonth === 1) {
                    hijriYear = 1447;
                    hijriMonth = "رجب";
                    
                    hijriDay = 10 + gregorianDay;
                    
                    if (hijriDay > 30) {
                        hijriDay = hijriDay - 30;
                        hijriMonth = "شعبان";
                    }
                } else {
                    const baseDate = new Date(2026, 0, 1);
                    const diffDays = Math.floor((gregorianDate - baseDate) / (1000 * 60 * 60 * 24));
                    
                    hijriYear = 1447;
                    hijriMonth = "رجب";
                    hijriDay = 11 + diffDays;
                    
                    while (hijriDay > 30) {
                        hijriDay -= 30;
                        if (hijriMonth === "رجب") {
                            hijriMonth = "شعبان";
                        } else if (hijriMonth === "شعبان") {
                            hijriMonth = "رمضان";
                        } else if (hijriMonth === "رمضان") {
                            hijriMonth = "شوال";
                            hijriYear = 1448;
                        }
                    }
                }
                
                const arabicDay = convertToArabicNumber(hijriDay);
                const arabicYear = convertToArabicNumber(hijriYear);
                
                return {
                    arabicFull: `${arabicDay} ${hijriMonth} ${arabicYear}`,
                    day: convertToBanglaNumber(hijriDay),
                    month: hijriMonth,
                    year: hijriYear
                };
            }

            function convertToBanglaNumber(num) {
                const banglaNumbers = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
                return num.toString().split('').map(digit => banglaNumbers[parseInt(digit)] || digit).join('');
            }

            function convertToArabicNumber(num) {
                const arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
                return num.toString().split('').map(digit => arabicNumbers[parseInt(digit)] || digit).join('');
            }

            // Initialize
            detectLocation();
            setInterval(updateTime, 1000);
        });
    </script>
</body>

</html>