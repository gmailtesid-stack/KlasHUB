<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - KelasHub</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: 'Inter', sans-serif;
        }

        .bg-zinc-950 {
            background-color: #09090b;
        }

        .bg-zinc-900 {
            background-color: #18181b;
        }

        .border-zinc-900 {
            border-color: #18181b;
        }

        .border-zinc-800 {
            border-color: #27272a;
        }

        .text-zinc-400 {
            color: #a1a1aa;
        }

        .text-zinc-300 {
            color: #d4d4d8;
        }

        .text-zinc-600 {
            color: #52525b;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="antialiased flex flex-col items-center justify-center min-h-screen bg-black p-5">

    @include('partials.loader')

    <div class="w-full max-w-sm">
        <div class="text-center mb-10">
            <div class="w-56 h-56 mx-auto flex items-center justify-center mb-2 overflow-hidden">
                <img src="{{ asset('icon.png') }}"
                    class="w-full h-full object-contain scale-[1.8] origin-center animate-pulse"
                    style="animation-duration: 3.5s;" alt="KelasHub Logo">
            </div>
        </div>

        <form method="POST" action="/login" class="space-y-4 bg-zinc-950 p-6 rounded-3xl border border-zinc-900">
            @csrf
            <div>
                <label for="name" class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Nama
                    Lengkap</label>
                <input type="text" name="name" id="name" required placeholder="Contoh: ARIYAS PRATAMA RAMADHAN"
                    class="w-full bg-zinc-900 border border-zinc-800 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-500 text-sm transition-all placeholder-zinc-600">
            </div>

            <div>
                <label for="password"
                    class="block text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required placeholder="••••••••"
                        class="w-full bg-zinc-900 border border-zinc-800 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-500 text-sm transition-all placeholder-zinc-600 pr-10">
                    <button type="button" id="togglePassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-500 hover:text-zinc-300">
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
                <div class="bg-red-950/50 border border-red-900 text-red-500 text-xs rounded-lg p-3">
                    Kesalahan kredensial, periksa kembali NIM & Password Anda.
                </div>
            @endif

            <button type="submit"
                class="w-full bg-white text-black font-bold text-sm rounded-xl px-4 py-3 hover:bg-zinc-200 transition-colors mt-2">
                Masuk ke Dashboard
            </button>

            <div class="mt-6 pt-4 border-t border-zinc-900 flex items-center justify-center gap-2">
                <svg class="w-3.5 h-3.5 text-emerald-400 animate-pulse" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                    </path>
                </svg>
                <p class="text-[10px] text-zinc-300 font-bold uppercase tracking-[0.2em]">Wave Project.ID Secured</p>
            </div>
        </form>


    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');
        const eyeOffIcon = document.querySelector('#eyeOffIcon');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            eyeIcon.classList.toggle('hidden');
            eyeOffIcon.classList.toggle('hidden');
        });
    </script>
</body>

</html>