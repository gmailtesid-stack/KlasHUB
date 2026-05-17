<!-- Universal Preloader / Loading Page -->
<div id="univ-preloader" class="fixed inset-0 w-full h-full flex flex-col items-center justify-center z-[99999] bg-[#050507]">
    <!-- Deep Cyber Ambient Glows -->
    <div class="absolute w-[300px] h-[300px] rounded-full bg-cyan-500/10 blur-[120px] top-1/4 left-1/4 animate-pulse"></div>
    <div class="absolute w-[300px] h-[300px] rounded-full bg-fuchsia-500/10 blur-[120px] bottom-1/4 right-1/4 animate-pulse" style="animation-delay: 1.5s;"></div>

    <div class="relative flex flex-col items-center justify-center">
        <!-- Floating & Glowing Neon Outer Ring -->
        <div class="relative w-32 h-32 flex items-center justify-center rounded-full bg-zinc-950/80 border border-zinc-800/80 shadow-[0_0_50px_rgba(255,255,255,0.03)] p-1">
            <!-- Neon Cyan Rotating Border -->
            <div class="absolute inset-0 rounded-full border border-transparent border-t-cyan-500 border-r-fuchsia-500 animate-spin" style="animation-duration: 2s;"></div>
            <!-- Inner Glowing Ring -->
            <div class="w-full h-full rounded-full bg-zinc-950 flex items-center justify-center overflow-hidden border border-zinc-900 shadow-inner p-1 relative z-10">
                <img src="{{ asset('icon.png') }}" class="w-full h-full object-contain scale-[1.9] origin-center animate-pulse" style="animation-duration: 2s;" alt="KelasHub">
            </div>
        </div>

        <!-- Glowing Identity -->
        <div class="mt-8 text-center relative z-10">
            <h1 class="text-2xl font-black tracking-[0.4em] text-white uppercase ml-[0.4em] relative drop-shadow-[0_0_15px_rgba(255,255,255,0.1)]">
                KelasHub
            </h1>
            <div class="mt-2 flex items-center justify-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                <p id="preloader-status" class="text-[10px] text-zinc-500 font-bold uppercase tracking-[0.2em] min-h-[15px] transition-all duration-300">
                    Sistem Inisialisasi
                </p>
            </div>
        </div>

        <!-- Sleek High-tech Progress Bar -->
        <div class="mt-6 w-48 h-[3px] bg-zinc-900 border border-zinc-800/50 rounded-full overflow-hidden relative z-10">
            <div id="preloader-bar" class="h-full w-0 bg-gradient-to-r from-cyan-500 via-teal-400 to-fuchsia-500 shadow-[0_0_8px_rgba(6,182,212,0.5)] transition-all duration-100 ease-out"></div>
        </div>
        
        <p class="mt-3 text-[8px] text-zinc-600 font-bold uppercase tracking-[0.3em]">Wave Project.ID Stealth App</p>
    </div>
</div>

<style>
    /* Premium Preloader Styles */
    #univ-preloader {
        transition: opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.5s;
    }
    #univ-preloader.fade-out {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }
    @keyframes spin-slow {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<script>
    (function() {
        const statuses = [
            "Inisialisasi Sistem...",
            "Memuat Modul Akademik...",
            "Mengamankan Jaringan Ledger...",
            "Sinkronisasi Data Presensi...",
            "Menyiapkan Kelas Stealth..."
        ];
        
        const statusEl = document.getElementById("preloader-status");
        const barEl = document.getElementById("preloader-bar");
        const preloaderEl = document.getElementById("univ-preloader");
        
        let statusIndex = 0;
        const statusInterval = setInterval(() => {
            if (statusEl) {
                statusEl.style.opacity = 0;
                setTimeout(() => {
                    statusIndex = (statusIndex + 1) % statuses.length;
                    statusEl.textContent = statuses[statusIndex];
                    statusEl.style.opacity = 1;
                }, 150);
            }
        }, 1200);

        // Progress bar simulation
        let progress = 0;
        const speed = 10 + Math.random() * 20; // variable simulation speed
        const progressInterval = setInterval(() => {
            progress += Math.random() * 8;
            if (progress >= 95) {
                progress = 95;
                clearInterval(progressInterval);
            }
            if (barEl) {
                barEl.style.width = progress + "%";
            }
        }, 100);

        // Page fully loaded handler
        function hidePreloader() {
            clearInterval(statusInterval);
            clearInterval(progressInterval);
            if (barEl) {
                barEl.style.width = "100%";
            }
            setTimeout(() => {
                if (preloaderEl) {
                    preloaderEl.classList.add("fade-out");
                }
            }, 300);
        }

        // Standard load event
        if (document.readyState === 'complete') {
            hidePreloader();
        } else {
            window.addEventListener('load', hidePreloader);
        }

        // Safety timeout to prevent infinite loading screen
        setTimeout(hidePreloader, 4000);
    })();
</script>
