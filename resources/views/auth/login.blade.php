<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - KelasHub Secure</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            900: '#134e4a',
                            950: '#042f2e',
                        },
                        dark: {
                            900: '#09090b',
                            800: '#18181b',
                            700: '#27272a',
                        }
                    },
                    animation: {
                        'blob': 'blob 7s infinite',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #050505;
            color: #ffffff;
            -webkit-tap-highlight-color: transparent;
        }

        .glass-panel {
            background: rgba(24, 24, 27, 0.4);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .input-glow:focus-within {
            box-shadow: 0 0 20px rgba(45, 212, 191, 0.15);
            border-color: rgba(45, 212, 191, 0.5);
        }

        /* Prevent autofill white background in Chrome */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #18181b inset !important;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
</head>

<body class="antialiased min-h-screen relative overflow-hidden flex flex-col justify-center py-10 px-5 sm:px-6 lg:px-8">

    @include('partials.loader')

    <!-- Animated Background Glowing Orbs -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div
            class="absolute -top-[20%] -left-[10%] w-[500px] h-[500px] rounded-full bg-brand-900/30 mix-blend-screen filter blur-[100px] opacity-70 animate-blob">
        </div>
        <div class="absolute top-[20%] -right-[10%] w-[400px] h-[400px] rounded-full bg-indigo-900/30 mix-blend-screen filter blur-[100px] opacity-70 animate-blob"
            style="animation-delay: 2s;"></div>
        <div class="absolute -bottom-[20%] left-[20%] w-[600px] h-[600px] rounded-full bg-emerald-900/20 mix-blend-screen filter blur-[120px] opacity-70 animate-blob"
            style="animation-delay: 4s;"></div>
    </div>

    <div class="relative w-full max-w-sm mx-auto z-10 animate-float" style="animation-duration: 8s;">
        <!-- Logo Section -->
        <div class="flex flex-col items-center justify-center mb-10">
            <div class="relative w-28 h-28 flex items-center justify-center mb-6">
                <!-- Glowing Ring Behind Logo -->
                <div
                    class="absolute inset-0 rounded-full bg-gradient-to-tr from-brand-500 to-indigo-500 blur-xl opacity-30 animate-pulse">
                </div>
                <!-- Premium Glass Logo Holder -->
                <div
                    class="relative w-full h-full rounded-[2rem] glass-panel flex items-center justify-center p-4 border border-white/10 overflow-hidden">
                    <img src="{{ asset('icon.png') }}"
                        class="w-full h-full object-contain scale-[1.3] filter drop-shadow-2xl" alt="KelasHub Logo">
                    <div class="absolute inset-0 bg-gradient-to-tr from-white/10 to-transparent mix-blend-overlay">
                    </div>
                </div>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight text-white mb-2">STEALTH <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-indigo-400">LOGIN</span>
            </h1>
            <p class="text-sm text-zinc-400 font-medium">Sistem Informasi Akademik Terpadu</p>
        </div>

        <!-- Login Form -->
        <form method="POST" action="/login" class="space-y-6 glass-panel p-8 rounded-[2rem] relative">
            @csrf

            <!-- Deco line -->
            <div
                class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-brand-500/50 to-transparent">
            </div>

            <div class="space-y-1 group input-glow rounded-2xl transition-all duration-300">
                <label for="name"
                    class="block text-[11px] font-bold text-zinc-400 uppercase tracking-widest pl-1 mb-1 group-focus-within:text-brand-400 transition-colors">Nomor
                    Induk / Nama</label>
                <div class="relative flex items-center">
                    <div class="absolute left-4 text-zinc-500 group-focus-within:text-brand-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <input type="text" name="name" id="name" required placeholder="NIM / Nama Mahasiswa"
                        class="w-full bg-dark-800/80 border border-white/5 text-white rounded-2xl pl-12 pr-4 py-4 focus:outline-none focus:bg-dark-800 font-medium text-sm transition-all placeholder-zinc-600 shadow-inner">
                </div>
            </div>

            <div class="space-y-1 group input-glow rounded-2xl transition-all duration-300">
                <label for="password"
                    class="block text-[11px] font-bold text-zinc-400 uppercase tracking-widest pl-1 mb-1 group-focus-within:text-brand-400 transition-colors">Kata
                    Sandi</label>
                <div class="relative flex items-center">
                    <div class="absolute left-4 text-zinc-500 group-focus-within:text-brand-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                    </div>
                    <input type="password" name="password" id="password" required placeholder="••••••••"
                        class="w-full bg-dark-800/80 border border-white/5 text-white rounded-2xl pl-12 pr-12 py-4 focus:outline-none focus:bg-dark-800 font-medium text-sm transition-all placeholder-zinc-600 shadow-inner">
                    <button type="button" id="togglePassword"
                        class="absolute right-4 text-zinc-500 hover:text-white transition-colors focus:outline-none p-1 rounded-lg">
                        <svg id="eyeIcon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="eyeOffIcon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>

            @if ($errors->any())
                <div
                    class="!mt-4 flex items-center gap-3 bg-red-500/10 border border-red-500/20 text-red-400 text-xs rounded-2xl p-4 animate-pulse">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <p class="font-medium leading-relaxed">Kredensial tidak valid. Silakan periksa kembali NIM & sandi Anda.
                    </p>
                </div>
            @endif

            <button type="submit"
                class="relative group w-full flex items-center justify-center gap-2 overflow-hidden rounded-2xl p-[1px] mt-6 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-dark-900 transition-all active:scale-[0.98]">
                <span
                    class="absolute inset-0 bg-gradient-to-r from-brand-400 via-indigo-500 to-brand-400 opacity-70 group-hover:opacity-100 group-hover:duration-200 transition-opacity bg-[length:200%_auto] animate-[shimmer_2s_linear_infinite]"></span>
                <span
                    class="relative w-full flex items-center justify-center gap-2 px-4 py-4 transition-all duration-200 bg-dark-900 rounded-[15px] group-hover:bg-opacity-0">
                    <span class="block font-bold text-sm text-white tracking-wide">AUTENTIKASI SISTEM</span>
                    <svg class="w-4 h-4 text-white transform group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </span>
            </button>

            <!-- Bottom Note -->
            <div
                class="mt-8 pt-6 border-t border-white/5 flex items-center justify-center gap-2 opacity-60 hover:opacity-100 transition-opacity cursor-default">
                <svg class="w-3.5 h-3.5 text-brand-400 animate-pulse" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                    </path>
                </svg>
                <div class="flex flex-col">
                    <p class="text-[9px] text-zinc-300 font-bold uppercase tracking-[0.2em] leading-tight">Wave
                        Project.ID Secured</p>
                    <p class="text-[8px] text-zinc-500 font-medium uppercase tracking-widest mt-0.5">End-to-End
                        Encryption</p>
                </div>
            </div>
        </form>
    </div>

    <!-- Additional Custom Animations -->
    <style>
        @keyframes shimmer {
            from {
                background-position: 200% center;
            }

            to {
                background-position: -200% center;
            }
        }
    </style>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');
        const eyeOffIcon = document.querySelector('#eyeOffIcon');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            eyeIcon.classList.toggle('hidden');
            eyeOffIcon.classList.toggle('hidden');
        });
    </script>
</body>

</html>