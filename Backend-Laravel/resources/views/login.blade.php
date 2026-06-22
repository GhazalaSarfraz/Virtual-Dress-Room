<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>AURA | Login</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&amp;family=Hanken+Grotesk:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "on-primary": "#ffffff",
                    "on-surface-variant": "#444748",
                    "on-tertiary-container": "#868381",
                    "secondary-fixed-dim": "#95d3ba",
                    "tertiary-fixed-dim": "#cac6c3",
                    "primary-fixed": "#e5e2e1",
                    "inverse-primary": "#c8c6c5",
                    "inverse-on-surface": "#f4f0ef",
                    "secondary-container": "#adedd3",
                    "surface-tint": "#5f5e5e",
                    "surface-container-low": "#f7f3f2",
                    "on-secondary-fixed-variant": "#0b513d",
                    "on-error": "#ffffff",
                    "on-primary-fixed-variant": "#474646",
                    "surface-variant": "#e5e2e1",
                    "inverse-surface": "#313030",
                    "surface": "#fdf8f8",
                    "on-tertiary-fixed": "#1d1b1a",
                    "surface-container-highest": "#e5e2e1",
                    "on-secondary-container": "#306d58",
                    "surface-dim": "#ddd9d8",
                    "tertiary-fixed": "#e6e1df",
                    "on-error-container": "#93000a",
                    "surface-container-lowest": "#ffffff",
                    "tertiary": "#000000",
                    "outline": "#747878",
                    "background": "#fdf8f8",
                    "error-container": "#ffdad6",
                    "on-tertiary-fixed-variant": "#484645",
                    "on-tertiary": "#ffffff",
                    "on-secondary": "#ffffff",
                    "surface-container-high": "#ebe7e6",
                    "secondary-fixed": "#b0f0d6",
                    "on-primary-container": "#858383",
                    "outline-variant": "#c4c7c7",
                    "on-surface": "#1c1b1b",
                    "surface-bright": "#fdf8f8",
                    "on-secondary-fixed": "#002117",
                    "on-background": "#1c1b1b",
                    "tertiary-container": "#1d1b1a",
                    "surface-container": "#f1edec",
                    "secondary": "#2b6954",
                    "primary-container": "#1c1b1b",
                    "primary-fixed-dim": "#c8c6c5",
                    "error": "#ba1a1a",
                    "primary": "#000000",
                    "on-primary-fixed": "#1c1b1b"
            },
            "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
            },
            "spacing": {
                    "stack-lg": "24px",
                    "container-margin-mobile": "20px",
                    "stack-sm": "4px",
                    "base": "8px",
                    "gutter": "16px",
                    "container-margin-desktop": "80px",
                    "stack-md": "12px"
            },
            "fontFamily": {
                    "body-lg": ["Hanken Grotesk"],
                    "price-tag": ["Hanken Grotesk"],
                    "label-uppercase": ["Hanken Grotesk"],
                    "display-lg-mobile": ["Playfair Display"],
                    "headline-md": ["Playfair Display"],
                    "body-md": ["Hanken Grotesk"],
                    "display-lg": ["Playfair Display"]
            },
            "fontSize": {
                    "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                    "price-tag": ["20px", {"lineHeight": "24px", "fontWeight": "500"}],
                    "label-uppercase": ["12px", {"lineHeight": "16px", "letterSpacing": "0.1em", "fontWeight": "600"}],
                    "display-lg-mobile": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "700"}],
                    "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                    "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                    "display-lg": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700"}]
            }
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .ai-shimmer {
            background: linear-gradient(90deg, transparent, rgba(226, 180, 154, 0.2), transparent);
            background-size: 200% 100%;
            animation: shimmer 3s infinite linear;
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .glass-panel {
            backdrop-filter: blur(12px);
            background: rgba(253, 248, 248, 0.8);
        }
    </style>
<style>
    body {
      min-height: max(884px, 100dvh);
    }
  </style>
</head>
<body class="bg-surface text-on-surface font-body-md min-h-screen selection:bg-secondary-container">
<!-- Header Navigation (Simplified for Transactional Page) -->
<header class="sticky top-0 z-50 flex items-center justify-center w-full px-container-margin-mobile py-stack-md bg-surface/80 backdrop-blur-md">
<h1 class="font-display-lg-mobile text-display-lg-mobile text-primary uppercase tracking-widest">AURA</h1>
</header>
<main class="relative flex flex-col md:flex-row min-h-[calc(100vh-80px)] overflow-hidden">
<!-- Left Side: Editorial Image (Desktop Only) -->
<section class="hidden md:flex md:w-1/2 relative overflow-hidden bg-surface-container-highest">
<div class="absolute inset-0 bg-gradient-to-r from-transparent to-surface/20 z-10"></div>
<img alt="Premium Fashion Editorial" class="absolute inset-0 w-full h-full object-cover grayscale-[20%] transition-transform duration-10000 hover:scale-110" data-alt="A high-end editorial fashion photograph of a model in a structured black avant-garde gown, standing against a minimalist architectural background with sharp shadows. The lighting is dramatic and sculptural, emphasizing the texture of the fabric. The overall mood is sophisticated, quiet, and luxurious, following a monochrome palette with soft beige undertones. This imagery reflects a premium boutique aesthetic suitable for a high-fashion AI platform." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBBSSEpiXwE_TWupkDKt4WLQdDffVq7Vivn6pVSV6xY5aohPmYc-2zoYfzGrH-T1w1L0LKOrAF19XJD33vOnAl4jzxPM7V1hGRJRJsVuWHtad_OAFHBJuhJS2eMH7VWLc_AHyRVvG8BV1Rq1Vi94jQZCTwfGStMvzsmR8XrDVlw_ka6NJ6wDoaC4cNS72n_MPk1GsJ17h6vzcgEfVHv99_nCbeRzizc1iCxBgnRPIyAGwGwVa_RC1i0KPrLB1t0AlawXeHFMda840oL"/>
<div class="absolute bottom-20 left-20 z-20 max-w-md">
<p class="font-label-uppercase text-label-uppercase text-white/80 mb-base">AI-DRIVEN COUTURE</p>
<h2 class="font-display-lg text-display-lg text-white mb-stack-lg leading-tight">The future of fitting rooms.</h2>
<div class="h-1 w-24 bg-secondary-fixed"></div>
</div>
</section>
<!-- Right Side: Login Form -->
<section class="flex-1 flex items-center justify-center px-container-margin-mobile py-stack-lg md:px-container-margin-desktop">
<div class="w-full max-w-[440px] space-y-stack-lg">
<header class="space-y-base">
<h2 class="font-headline-md text-headline-md text-on-surface">Welcome back</h2>
<p class="font-body-md text-on-surface-variant">Sign in to your private wardrobe and AI fitting suite.</p>
</header>
<!-- Social Login -->
<button class="group flex items-center justify-center w-full py-4 px-gutter border-[1.5px] border-outline-variant hover:border-primary transition-all duration-300 rounded-lg bg-surface hover:shadow-sm active:scale-[0.98]">
<svg class="w-5 h-5 mr-3" viewbox="0 0 24 24">
<path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
<path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
<path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
<path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
</svg>
<span class="font-label-uppercase text-label-uppercase text-primary">Sign up with Google</span>
</button>
<div class="relative flex items-center py-base">
<div class="flex-grow border-t border-outline-variant"></div>
<span class="flex-shrink mx-gutter font-label-uppercase text-label-uppercase text-on-tertiary-container">OR</span>
<div class="flex-grow border-t border-outline-variant"></div>
</div>
<!-- Form Fields -->
<form class="space-y-gutter" onsubmit="event.preventDefault();">
<!-- Role Selection Segmented Control -->
<div class="space-y-base mb-stack-md">
<label class="font-label-uppercase text-label-uppercase text-on-surface-variant block">Account Type</label>
<div class="flex p-1 bg-surface-container rounded-lg">
<button class="flex-1 py-2 px-4 rounded-md text-label-uppercase font-label-uppercase transition-all duration-200 bg-surface text-primary shadow-sm" id="role-user" type="button">
      User
    </button>
<button class="flex-1 py-2 px-4 rounded-md text-label-uppercase font-label-uppercase transition-all duration-200 text-on-surface-variant hover:text-primary" id="role-admin" type="button">
      Admin
    </button>
</div>
</div><div class="space-y-base">
<label class="font-label-uppercase text-label-uppercase text-on-surface-variant block" for="email">Email Address</label>
<input class="w-full bg-surface border-outline-variant focus:border-secondary focus:ring-0 focus:border-[1.5px] py-4 px-gutter text-on-surface placeholder:text-on-tertiary-container transition-all duration-200 rounded-lg" id="email" placeholder="name@example.com" type="email"/>
</div>
<div class="space-y-base">
<div class="flex justify-between items-center">
<label class="font-label-uppercase text-label-uppercase text-on-surface-variant block" for="password">Password</label>
<a class="font-label-uppercase text-[10px] text-secondary hover:opacity-70 transition-opacity" href="#">Forgot?</a>
</div>
<div class="relative">
<input class="w-full bg-surface border-outline-variant focus:border-secondary focus:ring-0 focus:border-[1.5px] py-4 px-gutter text-on-surface placeholder:text-on-tertiary-container transition-all duration-200 rounded-lg" id="password" placeholder="••••••••" type="password"/>
<button class="absolute right-gutter top-1/2 -translate-y-1/2 text-on-tertiary-container hover:text-primary" type="button">
<span class="material-symbols-outlined" style="font-size: 20px;">visibility</span>
</button>
</div>
</div>
<div class="pt-base">
<!-- Primary Login Button with Subtle Luxury Aesthetic -->
<button class="relative w-full overflow-hidden bg-primary text-on-primary py-4 px-gutter font-label-uppercase text-label-uppercase tracking-widest transition-all duration-300 hover:opacity-90 active:scale-[0.98] shadow-lg shadow-primary/5">
<span class="relative z-10">Login</span>
<div class="ai-shimmer absolute inset-0 opacity-20"></div>
</button>
</div>
</form>
<footer class="text-center pt-stack-lg">
<p class="font-body-md text-on-surface-variant">
                        New to AURA? 
                        <a class="text-primary font-bold hover:underline decoration-secondary-container decoration-2 underline-offset-4 ml-1" href="#">Create Account</a>
</p>
</footer>
</div>
</section>
</main>
<!-- Floating AI Sparkle Effect -->
<div class="fixed inset-0 pointer-events-none z-0" id="sparkle-container"></div>
<script>
        // Micro-interaction: Subtle sparkle generation for "Intelligence" atmosphere
        function createSparkle() {
            const container = document.getElementById('sparkle-container');
            const sparkle = document.createElement('div');
            const size = Math.random() * 4 + 2;
            const x = Math.random() * 100;
            const y = Math.random() * 100;
            
            sparkle.style.position = 'absolute';
            sparkle.style.left = x + '%';
            sparkle.style.top = y + '%';
            sparkle.style.width = size + 'px';
            sparkle.style.height = size + 'px';
            sparkle.style.backgroundColor = '#E2B49A'; // Rose Gold from design guide
            sparkle.style.borderRadius = '50%';
            sparkle.style.opacity = '0';
            sparkle.style.filter = 'blur(1px)';
            sparkle.style.transition = 'all 2s ease-out';
            
            container.appendChild(sparkle);
            
            setTimeout(() => {
                sparkle.style.opacity = '0.4';
                sparkle.style.transform = `translateY(-20px) scale(0.5)`;
            }, 100);
            
            setTimeout(() => {
                sparkle.remove();
            }, 2100);
        }

        setInterval(createSparkle, 800);

        // Simple button press interaction
        document.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.style.transform = 'scale(0.96)';
                setTimeout(() => btn.style.transform = '', 100);
            });
        });
    </script>
</body></html>