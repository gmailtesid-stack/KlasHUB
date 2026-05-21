<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KelasHub - Stealth Dashboard</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        window.notify = function (msg) {
            window.dispatchEvent(new CustomEvent('notify-toast', { detail: msg }));
        };
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        zinc: {
                            850: '#1f1f22',
                            900: '#18181b',
                            950: '#09090b',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #000;
            color: #fff;
            -webkit-tap-highlight-color: transparent;
        }

        ::-webkit-scrollbar {
            width: 4px;
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #27272a;
            border-radius: 4px;
        }

        .glass {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="antialiased selection:bg-zinc-800 flex h-screen overflow-hidden text-sm">
    @include('partials.loader')

    <div x-data="{ 
        tab: '{{ Auth::user()->role === 'super_admin' ? 'super' : 'akademi' }}', 
        modalKas: false, 
        modalAbsen: false,
        modalJadwal: false,
        modalTugas: false,
        modalMateri: false,
        modalAddSubject: false,
        modalAddStudent: false,
        modalConfirm: false,
        modalRegisterClass: false,
        confirmData: { title: '', message: '', action: null },
        modalDetailTugas: false,
        selectedTugas: {},
        modalDetailMatkul: false,
        selectedMatkul: '',
        toastMessage: '',
        showToast: false,
        modalPassword: false,
        taskType: 'individual',
        trxType: 'income',
        matkuls: [
            @foreach($master_subjects as $ms)
                {
                    id: {{ $ms->id }},
                    name: '{!! addslashes($ms->name) !!}',
                    sks: {{ $ms->sks }},
                    code: '{{ $ms->code }}',
                    lecturer: '{!! addslashes($ms->default_lecturer) !!}'
                },
            @endforeach
        ],
        get matkuls_sks() {
            let map = {};
            this.matkuls.forEach(m => map[m.name] = m.sks);
            return map;
        },
        jadwalHarian: [
            @foreach($jadwal_harian as $jd)
                {
                    id: {{ $jd->id }},
                    matkul: '{!! addslashes($jd->subject_name) !!}',
                    dosen: '{!! addslashes($jd->lecturer_name) !!}',
                    hari: '{{ $jd->day }}',
                    jamMulai: '{{ substr($jd->time_start, 0, 5) }}',
                    jamSelesai: '{{ substr($jd->time_end, 0, 5) }}',
                    ruangan: '{{ $jd->room }}',
                    kode: '{{ $jd->subject_code ?? "06TPLE013" }}',
                    kelas: '{{ $jd->class_name ?? "06TPLE013" }}',
                    deliveryType: '{{ $jd->delivery_type }}',
                    isValidated: {{ $jd->is_validated ? 'true' : 'false' }},
                    sks: {
                        'Rekayasa Perangkat Lunak': 3,
                        'Kerja Praktek': 2,
                        'Teknologi Internet of Things': 2,
                        'Pemrograman II': 3,
                        'Basis Data II': 3,
                        'Mobile Programming': 3,
                        'Sistem Pendukung Keputusan': 2,
                        'Teknik Kompilasi': 2
                    }['{!! addslashes($jd->subject_name) !!}'] || 2
                },
            @endforeach
        ],
        semuaTugas: [],
        semuaModul: [],
        semuaTransaksi: [],
        semuaMahasiswa: [],
        absensi: [
            @foreach($absensi as $abs)
                {
                    subject: '{{ $abs["subject"] }}',
                    alfa: {{ $abs["total_alfa"] }},
                    nyawa: {{ $abs["nyawa"] }},
                    status: '{{ $abs["status_nilai"] }}',
                    banned: {{ $abs["is_banned"] ? 'true' : 'false' }}
                },
            @endforeach
        ],
        saldoKas: {{ $saldo_kas }},
        pemasukanMingguIni: {{ $pemasukan_mingguan }},
        pengeluaranMingguIni: {{ $pengeluaran_mingguan }},
        notify(msg) {
            this.toastMessage = msg;
            this.showToast = true;
            setTimeout(() => { this.showToast = false }, 3000);
        },
        validateEntry(id, type, arrayName) {
            fetch('/kh/validate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                },
                body: JSON.stringify({ id: id, type: type })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const item = this[arrayName].find(i => i.id === id);
                    if(item) item.isValidated = true;
                    this.notify('Data berhasil divalidasi!');
                }
            });
        },
        calendarDays: Array.from({length: 31}, (_, i) => {
            const d = i + 1;
            return {
                day: d,
                tasks: [] // Will be filled in init
            };
        }),
        initCalendar() {
            this.calendarDays.forEach(day => {
                day.tasks = this.semuaTugas.filter(t => {
                    const dt = new Date(t.deadline);
                    return dt.getDate() === day.day && dt.getMonth() === 4; // May is index 4
                });
            });
        },
        toggleDelivery(matkul) {
            let current = this.jadwalHarian.find(j => j.matkul === matkul);
            let currentType = current ? current.deliveryType : 'offline';
            let nextType = currentType === 'offline' ? 'online' : 'offline';
            
            fetch('/kh/schedule/toggle-delivery', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                },
                body: JSON.stringify({
                    subject_name: matkul,
                    delivery_type: nextType
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    let idx = this.jadwalHarian.findIndex(j => j.matkul === matkul);
                    if(idx !== -1) {
                        this.jadwalHarian[idx].deliveryType = nextType;
                        this.jadwalHarian = [...this.jadwalHarian]; // Force Alpine reactivity
                    } else {
                        this.jadwalHarian.push({
                            id: data.schedule.id,
                            matkul: matkul,
                            deliveryType: nextType,
                            hari: 'Sabtu',
                            jamMulai: '07:30',
                            jamSelesai: '10:00',
                            ruangan: 'V.706',
                            dosen: 'Belum Diatur',
                            sks: this.matkuls_sks[matkul] || 2,
                            isValidated: true
                        });
                        this.jadwalHarian = [...this.jadwalHarian]; // Force Alpine reactivity
                    }
                    this.notify(matkul + ' diubah menjadi ' + nextType.toUpperCase());
                }
            });
        },
        deleteStudent(id) {
            this.confirmData = {
                title: 'Hapus Mahasiswa',
                message: 'Apakah Anda yakin ingin menghapus mahasiswa ini dari kelas?',
                action: () => {
                    fetch('/kh/student/' + id, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            this.semuaMahasiswa = this.semuaMahasiswa.filter(m => m.id !== id);
                            this.notify('Mahasiswa berhasil dihapus');
                            this.modalConfirm = false;
                        } else {
                            this.notify(data.message || 'Gagal menghapus mahasiswa');
                            this.modalConfirm = false;
                        }
                    });
                }
            };
            this.modalConfirm = true;
        },
        deleteSubject(id) {
            this.confirmData = {
                title: 'Hapus Mata Kuliah',
                message: 'Menghapus mata kuliah ini akan menghilangkan akses ke riwayat tugas & modul terkait. Lanjutkan?',
                action: () => {
                    fetch('/kh/subject/' + id, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            this.matkuls = this.matkuls.filter(m => m.id !== id);
                            this.notify('Mata kuliah berhasil dihapus');
                            this.modalConfirm = false;
                        }
                    });
                }
            };
            this.modalConfirm = true;
        },
        init() {
            this.loadingHeavyData = true;
            fetch('/kh/api/dashboard-data')
                .then(res => res.json())
                .then(data => {
                    this.semuaMahasiswa = data.semua_mahasiswa;
                    this.semuaTugas = data.semua_tugas;
                    this.semuaModul = data.semua_modul;
                    this.semuaTransaksi = data.transaksi_kas.map(t => ({
                        ...t,
                        date: new Date(t.transaction_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}),
                        student: t.student ? t.student.name : 'Umum'
                    }));
                    this.loadingHeavyData = false;
                    this.initCalendar();
                })
                .catch(err => {
                    console.error('Failed to load dashboard data:', err);
                    this.loadingHeavyData = false;
                });
        }
    }" x-init="init()" @notify-toast.window="notify($event.detail)" class="flex w-full h-full relative">

        <!-- Sidebar Desktop -->
        <aside class="hidden md:flex flex-col w-72 h-full border-r border-zinc-900 bg-zinc-950 shrink-0">
            <!-- App Brand Header -->
            <div class="p-6 border-b border-zinc-900 flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center p-1 overflow-hidden shadow-lg shadow-white/5">
                    <img src="{{ asset('icon.png') }}" class="w-full h-full object-contain scale-[1.8] origin-center"
                        alt="KelasHub Logo">
                </div>
                <div class="flex flex-col">
                    <span class="font-black text-white tracking-widest text-sm uppercase">KelasHub</span>
                    <span class="text-[8px] text-zinc-500 font-bold tracking-widest uppercase">STEALTH OPERATIONS</span>
                </div>
            </div>

            <div class="p-6 border-b border-zinc-900">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-gradient-to-br from-zinc-700 to-zinc-900 flex items-center justify-center border border-zinc-700 shadow-lg shadow-white/5">
                        <span class="text-white font-bold text-xl">{{ substr($student->name ?? 'M', 0, 1) }}</span>
                    </div>
                    <div class="flex flex-col">
                        <h1 class="font-bold text-zinc-100 tracking-tight text-base">{{ $student->name ?? 'Mahasiswa' }}
                        </h1>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">
                            {{ $student->nim ?? '231011400xxx' }}
                        </p>
                        <button @click="modalPassword = true"
                            class="text-[9px] text-blue-400 font-bold text-left hover:text-blue-300 mt-1 uppercase tracking-tighter">Ganti
                            Password</button>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-5 space-y-3">
                <button @click="tab = 'akademi'"
                    :class="tab === 'akademi' ? 'bg-zinc-900 text-white border-zinc-800 shadow-md' : 'text-zinc-400 hover:bg-zinc-900/50 hover:text-zinc-200 border-transparent'"
                    class="w-full flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all border text-left font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                    Akademi Hub
                    @if(in_array(($student->role ?? ''), ['ketua_kelas', 'super_admin']) && ($pending_count ?? 0) > 0)
                        <span class="ml-auto w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    @endif
                </button>
                <button @click="tab = 'repositori'"
                    :class="tab === 'repositori' ? 'bg-zinc-900 text-white border-zinc-800 shadow-md' : 'text-zinc-400 hover:bg-zinc-900/50 hover:text-zinc-200 border-transparent'"
                    class="w-full flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all border text-left font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    Repositori Kelas
                    @if(in_array(($student->role ?? ''), ['ketua_kelas', 'super_admin']) && ($pending_count ?? 0) > 0)
                        <span class="ml-auto w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    @endif
                </button>
                <button @click="tab = 'finansial'"
                    :class="tab === 'finansial' ? 'bg-zinc-900 text-white border-zinc-800 shadow-md' : 'text-zinc-400 hover:bg-zinc-900/50 hover:text-zinc-200 border-transparent'"
                    class="w-full flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all border text-left font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                    Finansial Kelas
                </button>
                <button @click="tab = 'presensi'"
                    :class="tab === 'presensi' ? 'bg-zinc-900 text-white border-zinc-800 shadow-md' : 'text-zinc-400 hover:bg-zinc-900/50 hover:text-zinc-200 border-transparent'"
                    class="w-full flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all border text-left font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                    Presensi Tracker
                </button>

                @if (($student->role ?? '') === 'super_admin')
                    <button @click="tab = 'super'"
                        :class="tab === 'super' ? 'bg-blue-600/20 text-blue-400 border-blue-500/30' : 'text-zinc-400 hover:bg-zinc-900/50 hover:text-zinc-200 border-transparent'"
                        class="w-full flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all border text-left font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z">
                            </path>
                        </svg>
                        Super Admin Panel
                    </button>
                @endif
            </nav>

            <div class="p-5 border-t border-zinc-900">
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-3 px-4 py-3 rounded-xl text-zinc-500 hover:text-red-400 hover:bg-red-950/20 transition-all font-semibold border border-transparent hover:border-red-900/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        Logout Sistem
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col h-full bg-black relative">

            <!-- Mobile Header -->
            <header
                class="md:hidden px-5 py-4 flex items-center justify-between border-b border-zinc-900 glass sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center p-1 overflow-hidden shadow-lg shadow-white/5">
                        <img src="{{ asset('icon.png') }}"
                            class="w-full h-full object-contain scale-[1.8] origin-center" alt="Logo">
                    </div>
                    <span class="font-black text-white tracking-wider text-xs uppercase">KelasHub</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <div
                            class="w-7 h-7 rounded-full bg-zinc-800 flex items-center justify-center border border-zinc-700">
                            <span
                                class="text-zinc-300 font-bold text-[10px]">{{ substr($student->name ?? 'M', 0, 1) }}</span>
                        </div>
                        <span
                            class="text-xs font-bold text-zinc-300 tracking-tight">{{ explode(' ', $student->name ?? 'Mahasiswa')[0] }}</span>
                    </div>
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="text-zinc-500 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                        </button>
                    </form>
                </div>
            </header>

            @if(in_array(($student->role ?? ''), ['ketua_kelas', 'super_admin']) && ($pending_count ?? 0) > 0)
                <div class="bg-amber-600 px-5 py-2.5 flex items-center justify-between shadow-lg relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-[11px] font-bold text-white uppercase tracking-wider">Ada {{ $pending_count }} Data
                            Menunggu Persetujuan Anda</p>
                    </div>
                    <button @click="notify('Cari label PENDING di setiap menu untuk memvalidasi')"
                        class="bg-white/20 hover:bg-white/30 text-white text-[9px] font-black px-3 py-1 rounded-full uppercase transition">Detail</button>
                </div>
            @endif

            <!-- Scrollable Content -->
            <main class="flex-1 overflow-y-auto p-5 md:p-8 pb-24 md:pb-8">
                <div class="max-w-5xl mx-auto w-full h-full">

                    @if(($student->role ?? '') === 'super_admin')
                        <!-- TAB: SUPER ADMIN -->
                        <div x-show="tab === 'super'" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">

                            <!-- Header -->
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                                <div>
                                    <h2 class="text-2xl md:text-3xl font-bold text-white tracking-tight mb-1">Super Admin
                                        Panel</h2>
                                    <p class="text-sm text-zinc-400">Daftarkan kelas baru beserta Admin Kelasnya sekaligus.
                                    </p>
                                </div>
                                <button @click="modalRegisterClass = true"
                                    class="w-full md:w-auto bg-blue-600 text-white font-bold px-6 py-2.5 rounded-xl hover:bg-blue-500 shadow-lg shadow-blue-500/20 transition-all flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    + Registrasi Kelas
                                </button>
                            </div>

                            <!-- Flash Success Message -->
                            @if(session('success'))
                                <div
                                    class="mb-6 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm px-5 py-3 rounded-xl font-medium">
                                    ✅ {{ session('success') }}
                                </div>
                            @endif

                            <!-- Classes Table -->
                            <div class="bg-zinc-950 border border-zinc-900 rounded-2xl overflow-hidden shadow-xl">
                                <div class="px-6 py-4 border-b border-zinc-900 flex items-center justify-between">
                                    <h3 class="text-sm font-bold text-zinc-300 uppercase tracking-widest">Daftar Kelas
                                        Terdaftar</h3>
                                    <span class="text-xs text-zinc-600 font-semibold">{{ count($academic_classes) }}
                                        Kelas</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-zinc-900">
                                                <th
                                                    class="text-left px-6 py-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                                                    Kode Kelas</th>
                                                <th
                                                    class="text-left px-6 py-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                                                    Prodi / Jurusan</th>
                                                <th
                                                    class="text-left px-6 py-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                                                    Ketua Kelas</th>
                                                <th
                                                    class="text-left px-6 py-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                                                    NIM Ketua</th>
                                                <th
                                                    class="text-left px-6 py-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                                                    Kontak</th>
                                                <th
                                                    class="text-left px-6 py-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                                                    Anggota</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-900/50">
                                            @forelse($academic_classes as $cls)
                                                <tr class="hover:bg-zinc-900/30 transition">
                                                    <td class="px-6 py-4">
                                                        <span
                                                            class="font-mono text-xs font-bold text-blue-400 bg-blue-500/10 px-2 py-1 rounded border border-blue-500/20">{{ $cls->code }}</span>
                                                    </td>
                                                    <td class="px-6 py-4 text-zinc-300 font-medium text-xs">
                                                        {{ $cls->department ?? $cls->name }}
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        @if($cls->ketuaKelas)
                                                            <span
                                                                class="text-white font-semibold text-xs">{{ $cls->ketuaKelas->name }}</span>
                                                        @else
                                                            <span class="text-zinc-600 italic text-xs">Belum ada</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 font-mono text-zinc-400 text-xs">
                                                        {{ $cls->ketuaKelas->nim ?? '-' }}
                                                    </td>
                                                    <td class="px-6 py-4 text-zinc-400 text-xs">{{ $cls->contact ?? '-' }}</td>
                                                    <td class="px-6 py-4">
                                                        <span
                                                            class="text-xs font-bold text-zinc-400 bg-zinc-900 px-2 py-1 rounded border border-zinc-800">{{ $cls->students_count }}
                                                            orang</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="px-6 py-12 text-center">
                                                        <div class="text-zinc-600 text-sm">Belum ada kelas terdaftar.</div>
                                                        <p class="text-zinc-700 text-xs mt-1">Klik "+ Registrasi Kelas" untuk
                                                            menambahkan.</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- TAB: AKADEMI -->
                    <div x-show="tab === 'akademi'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                            <div>
                                <h2 class="text-2xl md:text-3xl font-bold text-white tracking-tight mb-1">Akademi Hub
                                </h2>
                                <p class="text-sm text-zinc-400">Jadwal kuliah harian dan repositori materi.</p>
                            </div>

                            <!-- Warning Dicekal -->
                            <template x-if="absensi.some(a => a.nyawa === 0)">
                                <div
                                    class="bg-red-500/10 border border-red-500/30 p-3 rounded-2xl flex items-center gap-3 animate-pulse">
                                    <div class="bg-red-500 p-2 rounded-xl">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-red-400 font-bold uppercase tracking-widest">Peringatan
                                            Keras</p>
                                        <p class="text-[10px] text-red-500/80 font-medium leading-tight">Anda terdeteksi
                                            DICEKAL di salah satu matkul. Segera lapor sekretaris!</p>
                                    </div>
                                </div>
                            </template>

                            @if(in_array($student->role ?? '', ['ketua_kelas', 'sekretaris', 'bendahara', 'super_admin']))
                                <div class="flex flex-wrap gap-2 w-full md:w-auto">
                                    <button @click="modalJadwal = true"
                                        class="flex-1 md:flex-none bg-zinc-800 border border-zinc-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl hover:bg-zinc-700 transition">+
                                        Jadwal</button>
                                    <button @click="modalTugas = true"
                                        class="flex-1 md:flex-none bg-blue-600 border border-blue-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl hover:bg-blue-500 transition">+
                                        Tugas</button>
                                    <button @click="modalMateri = true"
                                        class="flex-1 md:flex-none bg-zinc-800 border border-zinc-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl hover:bg-zinc-700 transition">+
                                        Materi</button>
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- KIRI: Jadwal & Kalender -->
                            <div class="space-y-6">
                                <!-- Jadwal -->
                                <div
                                    class="bg-zinc-900/50 border border-zinc-800/80 rounded-2xl p-5 hover:border-zinc-700 transition duration-300">
                                    <div
                                        class="flex justify-between items-center mb-5 border-l-2 border-emerald-500 pl-3">
                                        <h3
                                            class="text-xs font-bold text-zinc-400 uppercase tracking-widest flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Jadwal Kelas Karyawan (Sabtu)
                                        </h3>
                                    </div>
                                    <div class="space-y-3 max-h-[350px] overflow-y-auto pr-2 pb-2">

                                        <!-- Template Jadwal Dinamis -->
                                        <template x-if="jadwalHarian.length === 0">
                                            <div class="text-center py-8">
                                                <div
                                                    class="w-16 h-16 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center mx-auto mb-3 text-zinc-700">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                </div>
                                                <p class="text-zinc-500 font-bold">Belum Ada Jadwal</p>
                                                <p class="text-xs text-zinc-600 mt-1">Klik "+ Jadwal" untuk menambahkan.
                                                </p>
                                            </div>
                                        </template>

                                        <template
                                            x-for="(jdwl, index) in jadwalHarian.filter(item => item.dosen && item.dosen !== 'Belum Diatur')"
                                            :key="index">
                                            <div
                                                class="flex gap-4 p-4 rounded-xl bg-black/40 border border-zinc-800/50 group hover:bg-black/60 transition relative">
                                                <div
                                                    class="text-center shrink-0 flex flex-col justify-center items-center min-w-[2.5rem]">
                                                    <p class="text-lg text-emerald-400 font-black leading-none"
                                                        x-text="jdwl.sks"></p>
                                                    <p
                                                        class="text-[8px] text-zinc-500 font-bold uppercase tracking-tighter">
                                                        SKS</p>
                                                    <div class="mt-2">
                                                        <template x-if="jdwl.deliveryType === 'offline'">
                                                            <span
                                                                class="px-1.5 py-0.5 bg-blue-500/10 text-blue-400 text-[7px] font-bold rounded border border-blue-500/20">OFFLINE</span>
                                                        </template>
                                                        <template x-if="jdwl.deliveryType === 'online'">
                                                            <span
                                                                class="px-1.5 py-0.5 bg-amber-500/10 text-amber-400 text-[7px] font-bold rounded border border-amber-500/20">ONLINE</span>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="w-px bg-zinc-800/80"></div>
                                                <div class="flex-1">
                                                    <h4 class="text-sm font-bold text-zinc-100 uppercase"
                                                        x-text="jdwl.matkul"></h4>
                                                    <p class="text-[10px] text-zinc-500 mt-1">
                                                        <span x-text="jdwl.kode"></span> • <span
                                                            x-text="jdwl.kelas"></span> • <span
                                                            x-text="jdwl.dosen"></span>
                                                        <template x-if="!jdwl.isValidated">
                                                            <span
                                                                class="ml-2 text-amber-500 font-bold uppercase tracking-tighter text-[8px]">Menunggu
                                                                Validasi</span>
                                                        </template>
                                                    </p>
                                                    <div class="flex items-center gap-3 mt-2">
                                                        <span
                                                            class="bg-zinc-900 border border-zinc-800 text-zinc-400 text-[9px] px-2 py-0.5 rounded-full"
                                                            x-text="'Ruang: ' + jdwl.ruangan"></span>
                                                        <span
                                                            class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-bold text-[9px] px-2 py-0.5 rounded-full"
                                                            x-show="jdwl.jamMulai"><span x-text="jdwl.jamMulai"></span>
                                                            - <span x-text="jdwl.jamSelesai"></span></span>
                                                        @if(in_array(($student->role ?? ''), ['ketua_kelas', 'super_admin']))
                                                            <template x-if="!jdwl.isValidated">
                                                                <button
                                                                    @click="validateEntry(jdwl.id, 'schedule', 'jadwalHarian')"
                                                                    class="bg-emerald-600 text-white text-[8px] px-2 py-0.5 rounded font-bold hover:bg-emerald-500">Validasi</button>
                                                            </template>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Kalender Bulanan FULL -->
                                <div
                                    class="bg-zinc-900/50 border border-zinc-800/80 rounded-2xl p-5 hover:border-zinc-700 transition duration-300">
                                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Kalender
                                        Deadline (Mei)</h3>
                                    <div
                                        class="grid grid-cols-7 gap-1 text-center text-xs mb-2 text-zinc-500 font-bold border-b border-zinc-800 pb-2">
                                        <div>S</div>
                                        <div>S</div>
                                        <div>R</div>
                                        <div>K</div>
                                        <div>J</div>
                                        <div>S</div>
                                        <div>M</div>
                                    </div>
                                    <div class="grid grid-cols-7 gap-1 text-center text-sm text-zinc-300 pt-1">
                                        <div class="py-2 text-zinc-700">26</div>
                                        <div class="py-2 text-zinc-700">27</div>
                                        <div class="py-2 text-zinc-700">28</div>
                                        <div class="py-2 text-zinc-700">29</div>
                                        <div class="py-2 text-zinc-700">30</div>
                                        <template x-for="d in calendarDays" :key="d.day">
                                            <div
                                                class="py-2 rounded-lg hover:bg-zinc-800 transition relative group cursor-pointer">
                                                <template x-if="d.tasks.length > 0">
                                                    <span class="absolute inset-1 border rounded"
                                                        :class="d.tasks.some(t => t.type === 'KELOMPOK') ? 'border-red-500/50 bg-red-500/10' : 'border-blue-500/50 bg-blue-500/10'"></span>
                                                </template>
                                                <div x-show="d.tasks.length > 0"
                                                    class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 bg-black border border-zinc-700 p-3 rounded-xl text-left hidden group-hover:block z-[60] shadow-2xl">
                                                    <template x-for="t in d.tasks" :key="t.title">
                                                        <div class="mb-2 last:mb-0">
                                                            <p class="text-[9px] font-bold uppercase"
                                                                :class="t.type === 'KELOMPOK' ? 'text-red-400' : 'text-blue-400'"
                                                                x-text="t.type"></p>
                                                            <p class="text-[10px] text-white leading-tight"
                                                                x-text="t.title"></p>
                                                        </div>
                                                    </template>
                                                </div>
                                                <span class="relative z-10"
                                                    :class="d.tasks.length > 0 ? 'text-white font-bold' : ''"
                                                    x-text="d.day"></span>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="mt-4 flex gap-3 text-[10px] text-zinc-500">
                                        <div class="flex items-center gap-1"><span
                                                class="w-2 h-2 rounded bg-blue-500/50 border border-blue-500"></span>
                                            Individu</div>
                                        <div class="flex items-center gap-1"><span
                                                class="w-2 h-2 rounded bg-red-500/50 border border-red-500"></span>
                                            Kelompok</div>
                                    </div>
                                </div>
                            </div>

                            <!-- KANAN: Tugas & Materi -->
                            <div class="space-y-6">
                                <!-- Tugas (Diurutkan) -->
                                <div
                                    class="bg-gradient-to-br from-zinc-900 to-zinc-950 border border-zinc-800/80 rounded-2xl p-5 hover:border-zinc-700 transition duration-300 relative overflow-hidden">
                                    <div
                                        class="absolute -right-10 -top-10 w-32 h-32 bg-zinc-800/30 rounded-full blur-2xl">
                                    </div>
                                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Deadline
                                        Terdekat (Urut)</h3>

                                    <div class="space-y-3">
                                        <template x-for="tugas in semuaTugas" :key="tugas.id">
                                            <div @click="selectedTugas = tugas; modalDetailTugas = true"
                                                class="bg-black/50 border rounded-xl p-5 relative z-10 hover:bg-zinc-900/80 transition cursor-pointer group pr-12"
                                                :class="tugas.type === 'KELOMPOK' ? 'border-red-900/30' : 'border-blue-900/30'">

                                                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-700 transition"
                                                    :class="tugas.type === 'KELOMPOK' ? 'group-hover:text-red-400' : 'group-hover:text-blue-400'">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </div>

                                                <div class="flex justify-between items-start mb-1">
                                                    <h4 class="text-base font-bold text-white transition"
                                                        :class="tugas.type === 'KELOMPOK' ? 'group-hover:text-red-300' : 'group-hover:text-blue-300'"
                                                        x-text="tugas.title"></h4>
                                                    <span class="px-2 py-1 text-[10px] font-bold rounded border"
                                                        :class="tugas.type === 'KELOMPOK' ? 'bg-red-500/10 text-red-400 border-red-500/20' : 'bg-blue-500/10 text-blue-400 border-blue-500/20'"
                                                        x-text="tugas.type"></span>
                                                </div>
                                                <p class="text-xs text-zinc-400 mb-2"
                                                    x-text="'Matkul: ' + tugas.matkul"></p>
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="px-3 py-1 bg-zinc-900 border border-zinc-800 rounded-lg text-[10px] font-mono text-zinc-300 font-bold">
                                                        <span
                                                            x-text="new Date(tugas.deadline).toLocaleDateString('id-ID', {day: 'numeric', month: 'long'})"></span>
                                                    </div>
                                                    <template x-if="!tugas.isValidated">
                                                        <span
                                                            class="text-amber-500 text-[8px] font-black uppercase border border-amber-500/20 px-2 py-1 rounded bg-amber-500/5 transition">Pending
                                                            Approval</span>
                                                    </template>
                                                    @if(in_array(($student->role ?? ''), ['ketua_kelas', 'super_admin']))
                                                        <template x-if="!tugas.isValidated">
                                                            <button
                                                                @click.stop="validateEntry(tugas.id, 'assignment', 'semuaTugas')"
                                                                class="bg-emerald-600 text-white text-[8px] px-2 py-1 rounded font-bold hover:bg-emerald-500">Validasi</button>
                                                        </template>
                                                    @endif
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="semuaTugas.length === 0">
                                            <div class="text-center py-10">
                                                <p class="text-zinc-600 font-bold">Tidak Ada Deadline</p>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Repositori Materi Berdasarkan Matkul -->
                                <div
                                    class="bg-zinc-900/50 border border-zinc-800/80 rounded-2xl p-5 hover:border-zinc-700 transition duration-300">
                                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Modul
                                        Pembelajaran Dosen</h3>
                                    <div class="space-y-5">
                                        <template x-if="!semuaModul.some(m => m.isValidated)">
                                            <div class="text-center py-10">
                                                <p class="text-zinc-600 font-bold">Tidak Ada Modul Pembelajaran</p>
                                            </div>
                                        </template>
                                        <template x-for="(sks, matkul) in matkuls_sks" :key="matkul">
                                            <div x-show="semuaModul.some(m => m.matkul === matkul)">
                                                <div @click="selectedMatkul = matkul; modalDetailMatkul = true"
                                                    class="cursor-pointer group flex items-center justify-between border-b border-zinc-800 pb-1 mb-3">
                                                    <h4 class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest group-hover:text-white transition"
                                                        x-text="matkul"></h4>
                                                    <span
                                                        class="text-[10px] text-zinc-600 group-hover:text-zinc-300 transition flex items-center gap-1">Histori
                                                        Modul & Tugas <svg class="w-3 h-3" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg></span>
                                                </div>
                                                <div class="space-y-2">
                                                    <template
                                                        x-for="modul in semuaModul.filter(m => m.matkul === matkul).slice(0, 2)"
                                                        :key="modul.id">
                                                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-black/40 border border-zinc-800/50 hover:bg-zinc-800 transition group cursor-pointer"
                                                            @click="modul.type === 'link' ? window.open(modul.link_url, '_blank') : window.open('/kh/module/' + modul.id + '/download', '_self')">
                                                            <div class="flex-1 flex items-center gap-3">
                                                                <div
                                                                    class="w-7 h-7 rounded bg-blue-500/10 text-blue-400 flex items-center justify-center">
                                                                    <svg class="w-3.5 h-3.5" fill="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path
                                                                            d="M19.5 3h-15C3.12 3 2 4.12 2 5.5v13C2 19.88 3.12 21 4.5 21h15c1.38 0 2.5-1.12 2.5-2.5v-13C22 4.12 20.88 3 19.5 3zM8 17H6v-2h2v2zm0-4H6v-2h2v2zm0-4H6V7h2v2zm10 8h-8v-2h8v2zm0-4h-8v-2h8v2zm0-4h-8V7h8v2z" />
                                                                    </svg>
                                                                </div>
                                                                <div>
                                                                    <p class="text-[11px] text-zinc-300 font-bold group-hover:text-white truncate"
                                                                        x-text="modul.title"></p>
                                                                    <template x-if="!modul.isValidated">
                                                                        <span
                                                                            class="text-amber-500 text-[7px] font-black uppercase">Menunggu
                                                                            Validasi</span>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            @if(in_array(($student->role ?? ''), ['ketua_kelas', 'super_admin']))
                                                                <template x-if="!modul.isValidated">
                                                                    <button
                                                                        @click.stop="validateEntry(modul.id, 'module', 'semuaModul')"
                                                                        class="bg-emerald-600 text-white text-[8px] px-1.5 py-0.5 rounded font-bold hover:bg-emerald-500">Validasi</button>
                                                                </template>
                                                            @endif
                                                            <svg class="w-4 h-4 text-zinc-700 group-hover:text-zinc-400"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                                </path>
                                                            </svg>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB: FINANSIAL -->
                    <div x-show="tab === 'finansial'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                            <div>
                                <h2 class="text-2xl md:text-3xl font-bold text-white tracking-tight mb-1">Finansial
                                    Kelas</h2>
                                <p class="text-sm text-zinc-400">Transparansi uang kas dan log pengeluaran.</p>
                            </div>
                            @if(in_array($student->role ?? '', ['ketua_kelas', 'sekretaris', 'bendahara', 'super_admin']))
                                <div class="flex gap-2 w-full md:w-auto">
                                    <a href="{{ route('reports.cash.pdf') }}"
                                        class="flex-1 md:flex-none bg-red-600/20 border border-red-500/30 text-red-400 text-xs font-bold px-4 py-2.5 rounded-xl hover:bg-red-600/30 transition flex items-center justify-center gap-2">
                                        PDF Kas
                                    </a>
                                    <a href="{{ route('reports.cash.excel') }}"
                                        class="flex-1 md:flex-none bg-emerald-600/20 border border-emerald-500/30 text-emerald-400 text-xs font-bold px-4 py-2.5 rounded-xl hover:bg-emerald-600/30 transition flex items-center justify-center gap-2">
                                        Excel Kas
                                    </a>
                                    <button @click="modalKas = true"
                                        class="flex-1 md:flex-none bg-white text-black font-bold px-6 py-2.5 rounded-xl hover:bg-zinc-200 shadow-lg shadow-white/10 transition-all flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Input Transaksi
                                    </button>
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <!-- Saldo Card -->
                            <div
                                class="md:col-span-2 bg-gradient-to-br from-zinc-800 to-zinc-950 border border-zinc-700/50 rounded-2xl p-6 md:p-8 relative overflow-hidden shadow-xl">
                                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/5 rounded-full blur-3xl">
                                </div>
                                <div
                                    class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-zinc-500 to-transparent">
                                </div>
                                <p class="text-sm text-zinc-400 mb-2 uppercase tracking-widest font-semibold">Total
                                    Saldo Kas</p>
                                <h3 class="text-4xl md:text-5xl font-bold text-white tracking-tight mb-4"
                                    x-text="'Rp ' + saldoKas.toLocaleString('id-ID')"></h3>
                                <div class="flex gap-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                        <span class="text-xs text-zinc-400">Pemasukan M. Ini: <strong
                                                class="text-zinc-200"
                                                x-text="'Rp ' + pemasukanMingguIni.toLocaleString('id-ID')"></strong></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                        <span class="text-xs text-zinc-400">Pengeluaran M. Ini: <strong
                                                class="text-zinc-200"
                                                x-text="'Rp ' + pengeluaranMingguIni.toLocaleString('id-ID')"></strong></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Tunggakan -->
                            <div
                                class="bg-red-950/20 border border-red-900/40 rounded-2xl p-5 hover:border-red-900/60 transition">
                                <h4
                                    class="text-xs font-bold text-red-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Nunggak Iuran
                                </h4>
                                <div class="space-y-3">
                                    <div
                                        class="bg-black/50 p-6 rounded-xl border border-zinc-800 flex flex-col items-center justify-center text-center">
                                        <div
                                            class="w-10 h-10 bg-emerald-500/10 text-emerald-500 rounded-full flex items-center justify-center mb-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <p class="text-xs font-bold text-white uppercase tracking-widest">Semua Lunas
                                        </p>
                                        <p class="text-[10px] text-zinc-500 mt-1">Tidak ada tunggakan iuran kas minggu
                                            ini.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Log -->
                        <div class="bg-zinc-900/50 border border-zinc-800/80 rounded-2xl p-5 md:p-6">
                            <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-5">Log Transaksi
                                Terakhir</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm whitespace-nowrap">
                                    <thead>
                                        <tr
                                            class="text-zinc-500 border-b border-zinc-800 text-xs uppercase tracking-widest">
                                            <th class="pb-3 font-medium">Tanggal</th>
                                            <th class="pb-3 font-medium">Jenis</th>
                                            <th class="pb-3 font-medium">Keterangan / Oleh</th>
                                            <th class="pb-3 font-medium text-right">Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-800/50 text-zinc-300">
                                        <template x-for="trx in semuaTransaksi" :key="trx.id">
                                            <tr class="hover:bg-zinc-800/20 transition">
                                                <td class="py-4 text-xs" x-text="trx.date"></td>
                                                <td class="py-4"><span
                                                        :class="trx.type === 'income' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20'"
                                                        class="px-2 py-0.5 rounded text-[10px] font-bold border"
                                                        x-text="trx.type === 'income' ? 'UANG MASUK' : 'UANG KELUAR'"></span>
                                                </td>
                                                <td class="py-4 text-xs">
                                                    <span class="font-bold text-zinc-200" x-text="trx.desc"></span><br>
                                                    <span class="text-[10px] text-zinc-500"
                                                        x-text="'Oleh: ' + trx.student"></span>
                                                    <template x-if="!trx.isValidated">
                                                        <span
                                                            class="ml-2 px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-500 text-[8px] font-black tracking-tighter border border-amber-500/20 uppercase">Pending</span>
                                                    </template>
                                                </td>
                                                <td class="py-4 text-right">
                                                    <div :class="trx.type === 'income' ? 'text-emerald-400' : 'text-red-400'"
                                                        class="font-bold mb-1"
                                                        x-text="(trx.type === 'income' ? '+ ' : '- ') + 'Rp ' + trx.amount.toLocaleString('id-ID')">
                                                    </div>
                                                    @if(in_array(($student->role ?? ''), ['ketua_kelas', 'super_admin']))
                                                        <template x-if="!trx.isValidated">
                                                            <button @click="validateEntry(trx.id, 'cash', 'semuaTransaksi')"
                                                                class="text-[9px] bg-emerald-600 text-white px-2 py-0.5 rounded font-bold hover:bg-emerald-500 transition">Validasi</button>
                                                        </template>
                                                    @endif
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB: PRESENSI -->
                    <div x-show="tab === 'presensi'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                            <div>
                                <h2 class="text-2xl md:text-3xl font-bold text-white tracking-tight mb-1">Tracker
                                    Kehadiran</h2>
                                <p class="text-sm text-zinc-400">Pantau sisa nyawa bolos per matakuliah.</p>
                            </div>
                            @if(!in_array($student->role ?? '', ['ketua_kelas', 'sekretaris', 'bendahara', 'super_admin']))
                                <button @click="modalAbsen = true"
                                    class="w-full md:w-auto bg-zinc-800 border border-zinc-700 text-white font-bold px-6 py-2.5 rounded-xl hover:bg-zinc-700 transition-all flex items-center justify-center gap-2">
                                    Rekap Mandiri
                                </button>
                            @endif
                        </div>

                        @if(in_array($student->role ?? '', ['ketua_kelas', 'sekretaris', 'bendahara', 'super_admin']))
                            <!-- Rekap Kelas (Admin View) -->
                            <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <h3 class="text-lg font-bold text-zinc-300 border-l-4 border-emerald-500 pl-3">Daftar Hadir
                                    Kelas (Hari Ini)</h3>
                                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                                    <a href="{{ route('reports.attendance.pdf') }}"
                                        class="flex-1 md:flex-none bg-red-600/20 border border-red-500/30 text-red-500 text-[10px] font-bold px-3 py-2 rounded-xl hover:bg-red-600/30 transition flex items-center justify-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        PDF Absensi
                                    </a>
                                    <a href="{{ route('reports.attendance.excel') }}"
                                        class="flex-1 md:flex-none bg-emerald-600/20 border border-emerald-500/30 text-emerald-500 text-[10px] font-bold px-3 py-2 rounded-xl hover:bg-emerald-600/30 transition flex items-center justify-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        Excel Absensi
                                    </a>
                                    <button @click="modalAddStudent = true"
                                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition shadow-lg shadow-blue-500/20 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Tambah Mahasiswa
                                    </button>
                                    <button @click="notify('Data absensi hari ini berhasil disimpan ke database!')"
                                        class="w-full sm:w-auto bg-red-600 hover:bg-red-500 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition shadow-lg shadow-red-500/20 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Simpan Absensi
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8" x-data="{ 
                                                                            currentAttendance: {},
                                                                            saveAttendance(matkul) {
                                                                                let data = [];
                                                                                semuaMahasiswa.forEach(m => {
                                                                                    data.push({
                                                                                        student_id: m.id,
                                                                                        status: this.currentAttendance[matkul + '_' + m.id] ? 'Hadir' : 'Alfa'
                                                                                    });
                                                                                });
                                                                                fetch('/kh/attendance', {
                                                                                    method: 'POST',
                                                                                    headers: {
                                                                                        'Content-Type': 'application/json',
                                                                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                                                                                    },
                                                                                    body: JSON.stringify({
                                                                                        subject_name: matkul,
                                                                                        date: new Date().toISOString().split('T')[0],
                                                                                        attendances: data
                                                                                    })
                                                                                })
                                                                                .then(res => res.json())
                                                                                .then(res => {
                                                                                    if(res.success) notify('Absensi ' + matkul + ' berhasil disimpan!');
                                                                                });
                                                                            }
                                                                        }">
                                <template x-for="(sks, matkulName) in matkuls_sks" :key="matkulName">
                                    <div
                                        class="bg-zinc-900/50 border border-zinc-800 rounded-2xl overflow-hidden flex flex-col shadow-xl">
                                        <div
                                            class="bg-black/40 p-4 border-b border-zinc-800 flex justify-between items-center">
                                            <div>
                                                <h4 class="text-sm font-bold text-white uppercase tracking-widest"
                                                    x-text="matkulName"></h4>
                                                <p class="text-[10px] text-zinc-500 mt-0.5"
                                                    x-text="'Dosen: ' + (jadwalHarian.find(j => j.matkul === matkulName)?.dosen || 'Belum Diatur')">
                                                </p>
                                            </div>
                                            <button @click="saveAttendance(matkulName)"
                                                class="bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] px-3 py-1 rounded font-bold transition">SIMPAN</button>
                                        </div>
                                        <div class="p-2 space-y-1 flex-1 overflow-y-auto max-h-[300px]">
                                            <template x-for="mhs in semuaMahasiswa" :key="mhs.id">
                                                <label
                                                    class="flex items-center justify-between p-3 rounded-xl hover:bg-zinc-800/50 cursor-pointer transition group">
                                                    <div class="flex items-center gap-4">
                                                        <input type="checkbox"
                                                            x-model="currentAttendance[matkulName + '_' + mhs.id]"
                                                            class="w-5 h-5 rounded border-zinc-600 text-emerald-500 bg-zinc-900">
                                                        <div>
                                                            <p class="text-sm font-bold text-zinc-200" x-text="mhs.name">
                                                            </p>
                                                            <p class="text-[10px] text-zinc-500 font-medium"
                                                                x-text="'NIM: ' + mhs.nim"></p>
                                                        </div>
                                                    </div>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            @if(in_array($student->role ?? '', ['ketua_kelas', 'sekretaris', 'bendahara', 'super_admin']))
                                <!-- Manajemen Anggota Kelas -->
                                <div class="mt-12 mb-8">
                                    <div class="flex justify-between items-center mb-6">
                                        <div>
                                            <h3 class="text-lg font-bold text-white tracking-tight">Manajemen Anggota Kelas</h3>
                                            <p class="text-xs text-zinc-500">Hapus mahasiswa yang keluar atau pindah kelas.</p>
                                        </div>
                                    </div>
                                    <div class="bg-zinc-900/50 border border-zinc-800 rounded-3xl overflow-hidden shadow-2xl">
                                        <table class="w-full text-left table-fixed">
                                            <thead>
                                                <tr class="bg-black/40 border-b border-zinc-800">
                                                    <th
                                                        class="w-[50%] px-4 py-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                                                        Mahasiswa</th>
                                                    <th
                                                        class="w-[33%] px-4 py-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                                                        Jabatan</th>
                                                    <th
                                                        class="w-[17%] px-4 py-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">
                                                        Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-zinc-800/50">
                                                <template x-for="mhs in semuaMahasiswa" :key="mhs.id">
                                                    <tr class="hover:bg-white/5 transition group">
                                                        <td class="px-4 py-3">
                                                            <div class="flex items-center gap-3">
                                                                <div class="w-8 h-8 rounded-full bg-zinc-800 flex items-center justify-center text-[10px] font-bold text-zinc-400 shrink-0"
                                                                    x-text="mhs.name.charAt(0)"></div>
                                                                <div class="flex flex-col min-w-0">
                                                                    <span
                                                                        class="text-sm font-bold text-zinc-200 group-hover:text-white transition truncate"
                                                                        x-text="mhs.name"></span>
                                                                    <span class="text-[9px] text-zinc-500 font-mono mt-0.5"
                                                                        x-text="mhs.nim"></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <span
                                                                class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-tighter inline-block"
                                                                :class="{
                                                                                                                                                              'bg-red-500/10 text-red-400 border border-red-500/20': mhs.role === 'ketua_kelas',
                                                                                                                                                              'bg-blue-500/10 text-blue-400 border border-blue-500/20': mhs.role === 'sekretaris',
                                                                                                                                                              'bg-amber-500/10 text-amber-400 border border-amber-500/20': mhs.role === 'bendahara',
                                                                                                                                                              'bg-zinc-800 text-zinc-500': mhs.role === 'mahasiswa'
                                                                                                                                                          }"
                                                                x-text="mhs.role.replace('_', ' ')"></span>
                                                        </td>
                                                        <td class="px-4 py-3 text-right">
                                                            <button @click="deleteStudent(mhs.id)"
                                                                class="p-2 text-zinc-500 hover:text-red-400 transition inline-flex items-center justify-center">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                    </path>
                                                                </svg>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @else
                            <!-- Tampilan Mahasiswa Biasa -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div
                                    class="bg-zinc-900/50 border border-zinc-800 rounded-2xl p-6 relative overflow-hidden shadow-xl">
                                    <h3
                                        class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-5 border-l-4 border-zinc-500 pl-3">
                                        Sisa Nyawa Absen Anda</h3>
                                    <div class="space-y-6">
                                        <template x-for="item in absensi" :key="item.subject">
                                            <div>
                                                <div class="flex justify-between text-sm mb-2">
                                                    <span class="text-white font-medium" x-text="item.subject"></span>
                                                    <span
                                                        :class="item.nyawa === 0 ? 'text-red-400' : (item.nyawa === 1 ? 'text-yellow-400' : 'text-emerald-400')"
                                                        class="font-bold"
                                                        x-text="item.nyawa + '/3 Nyawa' + (item.nyawa === 0 ? ' (Dicekal)' : '')"></span>
                                                </div>
                                                <div class="w-full bg-zinc-800 rounded-full h-2.5">
                                                    <div :class="item.nyawa === 0 ? 'from-red-600 to-red-400' : (item.nyawa === 1 ? 'from-yellow-500 to-orange-400' : 'from-emerald-500 to-teal-400')"
                                                        class="bg-gradient-to-r h-2.5 rounded-full"
                                                        :style="'width: ' + (item.nyawa * 33.33) + '%'"></div>
                                                </div>
                                                <p x-show="item.nyawa === 0"
                                                    class="text-[10px] text-red-400 mt-2 font-medium flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                        </path>
                                                    </svg>
                                                    Terindikasi Nilai E / Tidak bisa ikut UTS
                                                </p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- TAB: REPOSITORI -->
                    <div x-show="tab === 'repositori'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                            <div>
                                <h2 class="text-2xl md:text-3xl font-bold text-white tracking-tight mb-1">Repositori
                                    Mata Kuliah</h2>
                                <p class="text-sm text-zinc-400">Histori Tugas & Modul Pembelajaran per matakuliah.</p>
                            </div>
                            @if(in_array($student->role ?? '', ['ketua_kelas', 'sekretaris', 'bendahara', 'super_admin']))
                                <button @click="modalAddSubject = true"
                                    class="w-full md:w-auto bg-emerald-600 text-white font-bold px-6 py-2.5 rounded-xl hover:bg-emerald-500 shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah Matkul
                                </button>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="m in matkuls" :key="m.id">
                                <div @click="selectedMatkul = m.name; modalDetailMatkul = true"
                                    class="flex gap-4 p-4 rounded-xl bg-zinc-900/80 border border-zinc-800 shadow-xl hover:border-zinc-700 hover:shadow-2xl transition cursor-pointer relative pr-36 md:pr-48 group">
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-3 z-10">
                                        @if(in_array($student->role ?? '', ['ketua_kelas', 'sekretaris', 'bendahara', 'super_admin']))
                                            <div @click.stop="toggleDelivery(m.name)"
                                                class="flex items-center gap-2 cursor-pointer p-2 rounded-lg hover:bg-white/5 transition">
                                                <span class="text-[9px] font-bold text-zinc-500 uppercase tracking-tighter"
                                                    x-text="(jadwalHarian.find(j => j.matkul === m.name)?.deliveryType || 'offline') === 'offline' ? 'Offline' : 'Online'"></span>
                                                <div class="w-6 h-6 rounded-md border flex items-center justify-center transition"
                                                    :class="(jadwalHarian.find(j => j.matkul === m.name)?.deliveryType || 'offline') === 'offline' ? 'bg-emerald-500 border-emerald-400 text-white' : 'border-zinc-700 text-zinc-700 bg-black'">
                                                    <template
                                                        x-if="(jadwalHarian.find(j => j.matkul === m.name)?.deliveryType || 'offline') === 'offline'">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </template>
                                                </div>
                                            </div>
                                            <button @click.stop="deleteSubject(m.id)"
                                                class="p-2 text-zinc-500 hover:text-red-400 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        @else
                                            <div class="flex items-center gap-2">
                                                <span class="text-[9px] font-bold text-zinc-500 uppercase tracking-tighter"
                                                    x-text="(jadwalHarian.find(j => j.matkul === m.name)?.deliveryType || 'offline') === 'offline' ? 'Offline' : 'Online'"></span>
                                                <div class="w-5 h-5 rounded-md border flex items-center justify-center"
                                                    :class="(jadwalHarian.find(j => j.matkul === m.name)?.deliveryType || 'offline') === 'offline' ? 'bg-emerald-500/20 border-emerald-500 text-emerald-500' : 'border-zinc-800 text-zinc-800'">
                                                    <template
                                                        x-if="(jadwalHarian.find(j => j.matkul === m.name)?.deliveryType || 'offline') === 'offline'">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </template>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="text-zinc-700 group-hover:text-white transition ml-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div
                                        class="text-center shrink-0 flex flex-col justify-center items-center min-w-[3rem]">
                                        <div
                                            class="w-10 h-10 rounded-full bg-zinc-950 flex items-center justify-center border border-zinc-800 mb-2">
                                            <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                                </path>
                                            </svg>
                                        </div>
                                        <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest"><span
                                                x-text="m.sks" class="text-emerald-400"></span> SKS</p>
                                    </div>
                                    <div class="w-px bg-zinc-800"></div>
                                    <div class="flex-1 flex flex-col justify-center">
                                        <h4 class="text-sm font-bold text-zinc-100 group-hover:text-emerald-300 transition uppercase"
                                            x-text="m.name"></h4>
                                        <p class="text-[10px] text-zinc-500 mt-1"
                                            x-text="'Kode: ' + m.code + ' • ' + (m.lecturer || 'Belum Diatur')"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </main>

            <!-- Bottom Navigation Mobile -->
            <nav class="md:hidden fixed bottom-0 left-0 right-0 glass z-30 pb-safe">
                <div class="flex justify-around items-center h-16">
                    <button @click="tab = 'akademi'"
                        class="flex flex-col items-center justify-center w-full h-full space-y-1 transition-colors"
                        :class="tab === 'akademi' ? 'text-white' : 'text-zinc-500 hover:text-zinc-300'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                        <span class="text-[9px] font-semibold tracking-wide">Akademi</span>
                    </button>
                    <button @click="tab = 'repositori'"
                        class="flex flex-col items-center justify-center w-full h-full space-y-1 transition-colors"
                        :class="tab === 'repositori' ? 'text-white' : 'text-zinc-500 hover:text-zinc-300'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        <span class="text-[9px] font-semibold tracking-wide">Repositori</span>
                    </button>
                    <button @click="tab = 'finansial'"
                        class="flex flex-col items-center justify-center w-full h-full space-y-1 transition-colors"
                        :class="tab === 'finansial' ? 'text-white' : 'text-zinc-500 hover:text-zinc-300'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                        <span class="text-[9px] font-semibold tracking-wide">Finansial</span>
                    </button>
                    @if(($student->role ?? '') === 'super_admin')
                        <button @click="tab = 'super'"
                            class="flex flex-col items-center justify-center w-full h-full space-y-1 transition-colors relative"
                            :class="tab === 'super' ? 'text-blue-400' : 'text-zinc-500 hover:text-zinc-300'">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z">
                                </path>
                            </svg>
                            <span class="text-[9px] font-semibold tracking-wide">S. Admin</span>
                        </button>
                    @endif
                    <button @click="tab = 'presensi'"
                        class="flex flex-col items-center justify-center w-full h-full space-y-1 transition-colors relative"
                        :class="tab === 'presensi' ? 'text-white' : 'text-zinc-500 hover:text-zinc-300'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                        <span class="text-[9px] font-semibold tracking-wide">Presensi</span>
                    </button>
                </div>
            </nav>
        </div>

        <!-- MODALS DETAIL -->
        <!-- Modal Detail Tugas -->
        <div x-show="modalDetailTugas" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            style="display: none;">
            <div @click.away="modalDetailTugas = false" x-show="modalDetailTugas"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-md p-6 md:p-8 space-y-6 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1"
                    :class="selectedTugas.type === 'KELOMPOK' ? 'bg-gradient-to-r from-red-500 to-orange-500' : 'bg-gradient-to-r from-blue-500 to-indigo-500'">
                </div>

                <div class="flex justify-between items-start">
                    <div>
                        <span class="px-2 py-1 text-[10px] font-bold rounded"
                            :class="selectedTugas.type === 'KELOMPOK' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-blue-500/10 text-blue-400 border border-blue-500/20'"
                            x-text="selectedTugas.type"></span>
                        <h3 class="text-xl font-bold text-white mt-3" x-text="selectedTugas.title"></h3>
                        <p class="text-xs text-zinc-500 mt-1" x-text="'Mata Kuliah: ' + selectedTugas.matkul"></p>
                    </div>
                    <button @click="modalDetailTugas = false"
                        class="text-zinc-500 hover:text-white bg-black p-2 rounded-full border border-zinc-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="bg-black/50 p-4 rounded-xl border border-zinc-800">
                        <h4 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">Deskripsi Tugas</h4>
                        <p class="text-sm text-zinc-300 leading-relaxed" x-text="selectedTugas.desc"></p>
                    </div>

                    <div x-show="selectedTugas.type === 'KELOMPOK'"
                        class="bg-red-950/10 p-4 rounded-xl border border-red-900/30">
                        <h4 class="text-xs font-bold text-red-400 uppercase tracking-widest mb-2">Anggota Kelompok</h4>
                        <p class="text-sm text-white font-medium" x-text="selectedTugas.tim"></p>
                    </div>

                    <div class="flex items-center justify-between p-4 rounded-xl bg-zinc-950 border border-zinc-800">
                        <span class="text-xs font-bold text-zinc-500 uppercase">Deadline:</span>
                        <span class="text-sm font-bold text-white font-mono" x-text="selectedTugas.sisa"></span>
                    </div>
                </div>

                <button @click="modalDetailTugas = false"
                    class="w-full bg-white text-black py-3 rounded-xl text-sm font-bold hover:bg-zinc-200 transition shadow-lg">Tutup
                    Detail</button>
            </div>
        </div>

        <!-- Modal Detail Matkul (Histori) -->
        <div x-show="modalDetailMatkul" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            style="display: none;">
            <div @click.away="modalDetailMatkul = false" x-show="modalDetailMatkul"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-lg p-6 md:p-8 space-y-6 shadow-2xl relative overflow-hidden max-h-[90vh] flex flex-col">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>

                <div class="flex justify-between items-start shrink-0">
                    <div>
                        <p class="text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Histori Pembelajaran
                        </p>
                        <h3 class="text-xl font-bold text-white mt-1" x-text="selectedMatkul"></h3>
                        <template x-if="jadwalHarian.find(j => j.matkul === selectedMatkul)">
                            <p class="text-[10px] text-emerald-400 mt-1 font-medium">
                                <span x-text="jadwalHarian.find(j => j.matkul === selectedMatkul).kode"></span> •
                                <span x-text="jadwalHarian.find(j => j.matkul === selectedMatkul).dosen"></span>
                            </p>
                        </template>
                    </div>
                    <button @click="modalDetailMatkul = false"
                        class="text-zinc-500 hover:text-white bg-black p-2 rounded-full border border-zinc-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto space-y-6 pr-2">
                    <!-- Histori Modul -->
                    <div>
                        <h4
                            class="text-xs font-bold text-emerald-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                            Modul yang sudah di-share
                        </h4>
                        <div class="space-y-2">
                            <template x-for="modul in semuaModul.filter(m => m.matkul === selectedMatkul)"
                                :key="modul.id || Math.random()">
                                <div class="bg-black/50 p-3 rounded-xl border border-zinc-800 flex justify-between items-center group cursor-pointer hover:bg-zinc-800 transition"
                                    @click="modul.type === 'link' ? window.open(modul.link_url, '_blank') : window.open('/kh/module/' + modul.id + '/download', '_self')">
                                    <div>
                                        <p class="text-sm text-zinc-200 font-medium group-hover:text-white"
                                            x-text="modul.title"></p>
                                        <p class="text-[10px] text-zinc-500"
                                            x-text="modul.type === 'link' ? 'Tautan Eksternal' : 'Dokumen Terlampir'">
                                        </p>
                                    </div>
                                    <svg x-show="modul.type === 'file'"
                                        class="w-4 h-4 text-zinc-600 group-hover:text-emerald-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    <svg x-show="modul.type === 'link'"
                                        class="w-4 h-4 text-zinc-600 group-hover:text-blue-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                        </path>
                                    </svg>
                                </div>
                            </template>
                            <div x-show="semuaModul.filter(m => m.matkul === selectedMatkul).length === 0"
                                class="text-center py-4 bg-black/20 rounded-xl border border-dashed border-zinc-800">
                                <p class="text-xs text-zinc-600">Belum ada modul untuk matkul ini.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Histori Tugas -->
                    <div>
                        <h4
                            class="text-xs font-bold text-blue-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                </path>
                            </svg>
                            Tugas yang sudah dilakukan
                        </h4>
                        <div
                            class="space-y-3 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:ml-[1.125rem] before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-zinc-800 before:to-transparent">
                            <template x-for="tugas in semuaTugas.filter(t => t.matkul === selectedMatkul)"
                                :key="tugas.id || Math.random()">
                                <div
                                    class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 rounded-full border border-zinc-800 bg-zinc-900 text-emerald-500 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-black/50 p-4 rounded-xl border border-zinc-800 hover:border-zinc-700 cursor-pointer transition"
                                        @click="selectedTugas = tugas; modalDetailTugas = true">
                                        <div class="flex items-center justify-between mb-1">
                                            <h5 class="text-sm font-bold text-zinc-200" x-text="tugas.title"></h5>
                                            <span
                                                class="text-[10px] text-emerald-500 font-bold bg-emerald-500/10 px-2 py-0.5 rounded"
                                                x-text="tugas.type"></span>
                                        </div>
                                        <p class="text-[10px] text-zinc-500"
                                            x-text="'Deadline: ' + new Date(tugas.deadline).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})">
                                        </p>
                                    </div>
                                </div>
                            </template>
                            <div x-show="semuaTugas.filter(t => t.matkul === selectedMatkul).length === 0"
                                class="text-center py-4 bg-black/20 rounded-xl border border-dashed border-zinc-800 ml-10">
                                <p class="text-xs text-zinc-600">Belum ada tugas untuk matkul ini.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODALS ADMIN AKADEMI (Unchanged) -->
        <!-- Modal Tambah Jadwal -->
        <div x-show="modalJadwal" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            style="display: none;">
            <div @click.away="modalJadwal = false" x-show="modalJadwal"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-md p-6 space-y-5 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-zinc-500 to-white"></div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">Tambah Jadwal Kuliah</h3>
                </div>
                <form class="space-y-3" x-data="{
                    selectedMatkul: '',
                    dosen: '',
                    hari: 'Sabtu',
                    ruangan: 'V.706',
                    kodeMatkul: '06TPLE013',
                    noKelas: '06TPLE013',
                    jamMulai: '',
                    jamSelesai: '',
                    deliveryType: 'offline',
                    submitting: false,
                    updateAutoFill() {
                        const found = matkuls.find(m => m.name === this.selectedMatkul);
                        this.dosen = found ? found.lecturer : '';
                    }
                }">
                    <div>
                        <label class="block text-xs text-zinc-400 mb-1">Mata Kuliah</label>
                        <select x-model="selectedMatkul" @change="updateAutoFill()"
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-2.5 text-white text-sm focus:border-white focus:outline-none appearance-none">
                            <option value="" disabled selected>Pilih Mata Kuliah...</option>
                            <template x-for="m in matkuls" :key="m.id">
                                <option :value="m.name" x-text="m.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-400 mb-1">Dosen Pengampu</label>
                        <input type="text" x-model="dosen"
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-2.5 text-white text-sm focus:border-white focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-zinc-400 mb-1">Kode Matkul</label>
                            <input type="text" x-model="kodeMatkul"
                                class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-2.5 text-white text-sm focus:border-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-zinc-400 mb-1">No Kelas (Kode Kelas)</label>
                            <input type="text" x-model="noKelas"
                                class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-2.5 text-white text-sm focus:border-white focus:outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-zinc-400 mb-1">Hari</label>
                            <select x-model="hari"
                                class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-2.5 text-white text-sm focus:border-white focus:outline-none">
                                <option>Senin</option>
                                <option>Selasa</option>
                                <option>Rabu</option>
                                <option>Kamis</option>
                                <option>Jumat</option>
                                <option>Sabtu</option>
                                <option>Minggu</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-zinc-400 mb-1">Ruangan</label>
                            <input type="text" x-model="ruangan"
                                class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-2.5 text-white text-sm focus:border-white focus:outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-zinc-400 mb-1">Jam Mulai</label>
                            <input type="time" x-model="jamMulai"
                                class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-2.5 text-white text-sm focus:border-white focus:outline-none"
                                style="color-scheme: dark;">
                        </div>
                        <div>
                            <label class="block text-xs text-zinc-400 mb-1">Jam Selesai</label>
                            <input type="time" x-model="jamSelesai"
                                class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-2.5 text-white text-sm focus:border-white focus:outline-none"
                                style="color-scheme: dark;">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-400 mb-1">Tipe Tatap Muka</label>
                        <select x-model="deliveryType"
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-2.5 text-white text-sm focus:border-white focus:outline-none">
                            <option value="offline">Offline / Tatap Muka</option>
                            <option value="online">Online / Daring</option>
                        </select>
                    </div>
                    <div class="flex gap-3 pt-3">
                        <button type="button" @click="modalJadwal = false"
                            class="flex-1 bg-zinc-800 text-zinc-300 py-2.5 rounded-xl text-sm font-semibold hover:bg-zinc-700">Batal</button>
                        <button type="button" :disabled="submitting" @click="
                            if(!selectedMatkul) return notify('Pilih matkul dulu!');
                            if(submitting) return;
                            submitting = true;
                            let capturedSks = (matkuls.find(m => m.name === selectedMatkul) || {sks: 2}).sks;
                            let bodyData = {
                                subject_name: selectedMatkul,
                                subject_code: kodeMatkul,
                                lecturer_name: dosen,
                                day: hari,
                                room: ruangan,
                                class_name: noKelas,
                                time_start: jamMulai,
                                time_end: jamSelesai,
                                delivery_type: deliveryType
                            };
                            fetch('/kh/schedule', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                                },
                                body: JSON.stringify(bodyData)
                            })
                            .then(res => res.json())
                            .then(data => {
                                if(data.success) {
                                    jadwalHarian.push({
                                        id: data.schedule ? data.schedule.id : null,
                                        matkul: selectedMatkul,
                                        dosen: dosen,
                                        hari: hari,
                                        ruangan: ruangan,
                                        kode: kodeMatkul,
                                        kelas: noKelas,
                                        jamMulai: jamMulai,
                                        jamSelesai: jamSelesai,
                                        deliveryType: deliveryType,
                                        isValidated: false,
                                        sks: capturedSks
                                    });
                                    modalJadwal = false;
                                    notify('Jadwal ' + selectedMatkul + ' berhasil disimpan!');
                                    selectedMatkul = ''; jamMulai = ''; jamSelesai = ''; dosen = '';
                                } else {
                                    notify('Gagal menyimpan jadwal: ' + (data.message || 'Server error'));
                                }
                            })
                            .catch(err => notify('Error: ' + err))
                            .finally(() => { submitting = false; });
                        " :class="submitting ? 'opacity-50 cursor-not-allowed' : 'hover:bg-zinc-200'"
                            class="flex-1 bg-white text-black py-2.5 rounded-xl text-sm font-bold transition"
                            x-text="submitting ? 'Menyimpan...' : 'Simpan Jadwal'"></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Tambah Tugas -->
        <div x-show="modalTugas" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            style="display: none;">
            <div @click.away="modalTugas = false" x-show="modalTugas"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-md p-6 space-y-5 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">Entry Tugas Baru</h3>
                </div>
                <form class="space-y-3"
                    x-data="{ tgsTitle: '', tgsMatkul: '', tgsDeadline: '', tgsMembers: '', submitting: false }">
                    <div>
                        <label class="block text-xs text-zinc-400 mb-1">Judul Tugas</label>
                        <input type="text" x-model="tgsTitle" placeholder="Project Akhir"
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-2.5 text-white text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-zinc-400 mb-1">Mata Kuliah</label>
                            <select x-model="tgsMatkul"
                                class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-2.5 text-white text-sm focus:border-blue-500 focus:outline-none">
                                <option value="">Pilih Matkul...</option>
                                <template x-for="m in matkuls" :key="m.id">
                                    <option :value="m.name" x-text="m.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-zinc-400 mb-1">Tipe Tugas</label>
                            <select x-model="taskType"
                                class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-2.5 text-white text-sm focus:border-blue-500 focus:outline-none">
                                <option value="individual">Individu</option>
                                <option value="kelompok">Kelompok</option>
                            </select>
                        </div>
                    </div>

                    <div x-show="taskType === 'kelompok'" x-transition
                        class="bg-blue-950/20 border border-blue-900/50 p-3 rounded-xl">
                        <label class="block text-xs text-blue-400 font-bold mb-1">Anggota Kelompok (Pisahkan
                            koma)</label>
                        <textarea x-model="tgsMembers" placeholder="Budi, Siti, Andi, Reza"
                            class="w-full bg-black border border-blue-900 rounded-xl px-4 py-2 text-white text-sm focus:border-blue-500 focus:outline-none"
                            rows="2"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs text-zinc-400 mb-1">Tenggat Waktu (Deadline)</label>
                        <input type="datetime-local" x-model="tgsDeadline"
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-2.5 text-white text-sm focus:border-blue-500 focus:outline-none"
                            style="color-scheme: dark;">
                    </div>
                    <div class="flex gap-3 pt-3">
                        <button type="button" @click="modalTugas = false"
                            class="flex-1 bg-zinc-800 text-zinc-300 py-2.5 rounded-xl text-sm font-semibold hover:bg-zinc-700">Batal</button>
                        <button type="button" :disabled="submitting" @click="
                            if(!tgsTitle || !tgsMatkul || !tgsDeadline) return notify('Lengkapi data tugas!');
                            if(submitting) return;
                            submitting = true;
                            fetch('/kh/assignment', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                                },
                                body: JSON.stringify({ subject_name: tgsMatkul, title: tgsTitle, deadline: tgsDeadline, type: taskType, members: tgsMembers })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if(data.success) {
                                    semuaTugas.push({ id: data.assignment?.id, matkul: tgsMatkul, title: tgsTitle, deadline: tgsDeadline, type: taskType.toUpperCase(), tim: tgsMembers, desc: '', isValidated: false });
                                    modalTugas = false; tgsTitle = ''; tgsMatkul = ''; tgsDeadline = ''; tgsMembers = '';
                                    notify('Tugas berhasil disimpan!');
                                } else {
                                    notify('Gagal menyimpan tugas!');
                                }
                            })
                            .catch(err => notify('Error: ' + err))
                            .finally(() => { submitting = false; });
                        " :class="submitting ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-500'"
                            class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl text-sm font-bold transition"
                            x-text="submitting ? 'Menyimpan...' : 'Simpan'"></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Tambah Materi -->
        <div x-show="modalMateri" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            style="display: none;">
            <div @click.away="modalMateri = false" x-show="modalMateri"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-md p-6 space-y-5 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">Upload Modul Pembelajaran</h3>
                </div>
                <form class="space-y-4"
                    x-data="{ mdlMatkul: '', mdlTitle: '', mdlUrl: '', uploadType: 'file', fileName: '', submitting: false }">
                    <div>
                        <label class="block text-xs text-zinc-400 mb-1">Mata Kuliah</label>
                        <select x-model="mdlMatkul"
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-2.5 text-white text-sm focus:border-emerald-500 focus:outline-none appearance-none">
                            <option value="" disabled selected>Pilih Mata Kuliah...</option>
                            <template x-for="m in matkuls" :key="m.id">
                                <option :value="m.name" x-text="m.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-zinc-400 mb-2">Tipe Lampiran</label>
                        <div class="flex gap-2">
                            <button type="button" @click="uploadType = 'file'"
                                :class="uploadType === 'file' ? 'bg-emerald-600/20 text-emerald-400 border-emerald-500/50' : 'bg-black text-zinc-500 border-zinc-800'"
                                class="flex-1 py-2 border rounded-xl text-sm font-bold transition">File
                                (Softcopy)</button>
                            <button type="button" @click="uploadType = 'link'"
                                :class="uploadType === 'link' ? 'bg-emerald-600/20 text-emerald-400 border-emerald-500/50' : 'bg-black text-zinc-500 border-zinc-800'"
                                class="flex-1 py-2 border rounded-xl text-sm font-bold transition">Link
                                (G-Drive)</button>
                        </div>
                    </div>

                    <!-- Softcopy Upload -->
                    <div x-show="uploadType === 'file'" x-transition class="space-y-3">
                        <div>
                            <label class="block text-xs text-zinc-400 mb-1">Pilih Dokumen (PDF, DOCX, TXT)</label>
                            <div class="relative w-full">
                                <input type="file" id="mdlFile" accept=".pdf,.doc,.docx,.txt"
                                    @change="fileName = $event.target.files[0]?.name || ''"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="w-full bg-black border border-zinc-800 border-dashed rounded-xl px-4 py-6 text-center transition-all flex flex-col items-center justify-center gap-2"
                                    :class="fileName ? 'border-emerald-500 bg-emerald-950/10' : 'hover:border-emerald-500/50'">
                                    <svg class="w-6 h-6 transition-colors"
                                        :class="fileName ? 'text-emerald-500' : 'text-zinc-500'" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    <span x-show="!fileName" class="text-xs text-zinc-400 font-medium">Ketuk untuk
                                        memilih file dari device</span>
                                    <span x-show="fileName" class="text-sm text-emerald-400 font-bold break-all"
                                        x-text="fileName"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- URL Input -->
                    <div x-show="uploadType === 'link'" x-transition class="space-y-3" style="display: none;">
                        <div>
                            <label class="block text-xs text-zinc-400 mb-1">Nama / Judul Link</label>
                            <input type="text" x-model="mdlTitle" placeholder="Contoh: Slide Bab 4 - OOP"
                                class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-2.5 text-white text-sm focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-zinc-400 mb-1">Link URL</label>
                            <input type="url" x-model="mdlUrl" placeholder="https://drive.google.com/..."
                                class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-2.5 text-white text-sm focus:border-emerald-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="flex gap-3 pt-3">
                        <button type="button" @click="modalMateri = false"
                            class="flex-1 bg-zinc-800 text-zinc-300 py-3 rounded-xl text-sm font-semibold hover:bg-zinc-700">Batal</button>
                        <button type="button" :disabled="submitting" @click="
                            if(!mdlMatkul) return notify('Pilih matkul dulu!');
                            if(submitting) return;
                            submitting = true;
                            let formData = new FormData();
                            formData.append('subject_name', mdlMatkul);
                            formData.append('type', uploadType);
                            if(uploadType === 'file') {
                                let fileInput = document.getElementById('mdlFile');
                                if(fileInput.files.length === 0) { submitting = false; return notify('Pilih file dulu!'); }
                                const maxSize = 3 * 1024 * 1024; // 3MB limit for Vercel
                                if(fileInput.files[0].size > maxSize) { submitting = false; return notify('File terlalu besar! Maks 3MB. Gunakan Google Drive untuk file lebih besar.'); }
                                formData.append('file', fileInput.files[0]);
                            } else {
                                if(!mdlUrl) { submitting = false; return notify('Isi link dulu!'); }
                                formData.append('title', mdlTitle || mdlUrl);
                                formData.append('link_url', mdlUrl);
                            }
                            fetch('/kh/module', {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') },
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if(data.success) {
                                    semuaModul.unshift({ id: data.module?.id, matkul: mdlMatkul, title: data.module?.title || mdlTitle || mdlUrl, type: uploadType, file_path: data.module?.file_path, link_url: data.module?.link_url, isValidated: false });
                                    modalMateri = false; mdlMatkul = ''; mdlTitle = ''; mdlUrl = ''; fileName = '';
                                    notify('Modul berhasil disimpan!');
                                } else {
                                    notify('Gagal: ' + (data.message || 'Server error'));
                                }
                            })
                            .catch(err => notify('Error: ' + err))
                            .finally(() => { submitting = false; });
                        " :class="submitting ? 'opacity-50 cursor-not-allowed' : 'hover:bg-emerald-500'"
                            class="flex-1 bg-emerald-600 text-white py-3 rounded-xl text-sm font-bold transition shadow-lg shadow-emerald-500/20"
                            x-text="submitting ? 'Mengunggah...' : 'Upload'"></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Catat Kas -->
        <div x-show="modalKas" x-transition.opacity
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            style="display: none;">
            <div @click.away="modalKas = false" x-show="modalKas" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-md p-6 md:p-8 space-y-6 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1"
                    :class="trxType === 'income' ? 'bg-gradient-to-r from-emerald-400 to-teal-500' : 'bg-gradient-to-r from-red-500 to-orange-500'">
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white mb-1">Input Transaksi Keuangan</h3>
                    <p class="text-xs text-zinc-500">Catat pemasukan kas dari anggota atau pengeluaran kelas.</p>
                </div>
                <form class="space-y-4"
                    x-data="{ trxStudent: '', trxAmount: '', trxDesc: '', trxDate: '{{ date('Y-m-d') }}' }">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-2">Jenis
                            Transaksi</label>
                        <select x-model="trxType"
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:border-white focus:ring-1 focus:ring-white transition-all outline-none">
                            <option value="income">🟢 UANG MASUK (Bayar Kas)</option>
                            <option value="expense">🔴 UANG KELUAR (Beli Barang/Jasa)</option>
                        </select>
                    </div>

                    <!-- Jika Uang Masuk -->
                    <div x-show="trxType === 'income'" x-transition class="space-y-4">
                        <div>
                            <label
                                class="block text-xs font-semibold text-emerald-400 uppercase tracking-wide mb-2">Pilih
                                Pembayar (Anggota Kelas)</label>
                            <select x-model="trxStudent"
                                class="w-full bg-black border border-emerald-900/50 rounded-xl px-4 py-3 text-white text-sm focus:border-emerald-500 outline-none">
                                <option value="">Pilih Anggota...</option>
                                <template x-for="std in semuaMahasiswa" :key="std.id">
                                    <option :value="std.id" x-text="std.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-2">Jumlah
                            (Rp)</label>
                        <input type="number" x-model="trxAmount" placeholder="Contoh: 10000"
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:border-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-2">Keterangan
                            / Tujuan</label>
                        <input type="text" x-model="trxDesc" placeholder="Bayar Minggu 3 & 4"
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:border-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-2">Tanggal
                            Transaksi</label>
                        <input type="date" x-model="trxDate"
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:border-emerald-500 outline-none"
                            style="color-scheme: dark;">
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="modalKas = false"
                            class="flex-1 bg-zinc-800 text-zinc-300 py-3.5 rounded-xl text-sm font-bold hover:bg-zinc-700 transition">Batal</button>
                        <button type="button" @click="
                            if(!trxAmount || !trxDesc) return notify('Lengkapi data transaksi!');
                            let bodyData = {
                                type: trxType,
                                amount: trxAmount,
                                description: trxDesc,
                                student_id: trxType === 'income' ? trxStudent : null,
                                transaction_date: trxDate
                            };
                            fetch('/kh/cash', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                                },
                                body: JSON.stringify(bodyData)
                            })
                            .then(res => res.json())
                            .then(data => {
                                if(data.success) {
                                    semuaTransaksi.unshift({
                                        type: trxType,
                                        amount: parseInt(trxAmount),
                                        desc: trxDesc,
                                        student: data.ledger.student ? data.ledger.student.name : 'Umum',
                                        date: new Date(trxDate).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'})
                                    });
                                    if(trxType === 'income') saldoKas += parseInt(trxAmount);
                                    else saldoKas -= parseInt(trxAmount);
                                    modalKas = false;
                                    notify('Transaksi berhasil disimpan!');
                                } else {
                                    notify('Gagal menyimpan transaksi!');
                                }
                            });
                        "
                            class="flex-1 bg-white text-black py-3.5 rounded-xl text-sm font-bold hover:bg-zinc-200 transition shadow-lg">Simpan
                            Transaksi</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Catat Absen -->
        <div x-show="modalAbsen" x-transition.opacity
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            style="display: none;">
            <div @click.away="modalAbsen = false" x-show="modalAbsen"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-md p-6 md:p-8 space-y-6 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 to-orange-500"></div>
                <div>
                    <h3 class="text-xl font-bold text-white mb-1">Rekap Absensi Mandiri</h3>
                    <p class="text-xs text-zinc-500">Catat bolos mu dengan jujur biar gak nangis pas UTS.</p>
                </div>
                <form class="space-y-4" x-data="{ absSubject: '', absStatus: 'Izin', absNotes: '' }">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-2">Mata
                            Kuliah</label>
                        <select x-model="absSubject"
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:border-red-500 transition-all outline-none">
                            <option value="">Pilih Matkul...</option>
                            <template x-for="m in matkuls" :key="m.id">
                                <option :value="m.name" x-text="m.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-2">Status
                            Kehadiran</label>
                        <select x-model="absStatus"
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:border-red-500 transition-all outline-none">
                            <option value="Izin">ℹ️ Izin</option>
                            <option value="Sakit">🤒 Sakit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-2">Deskripsi
                            / Alasan <span class="text-red-500">*Wajib</span></label>
                        <textarea x-model="absNotes" required
                            placeholder="Tuliskan alasan izin atau keterangan sakit..."
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:border-red-500 transition-all outline-none h-24 resize-none"></textarea>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="modalAbsen = false"
                            class="flex-1 bg-zinc-800 text-zinc-300 py-3 rounded-xl text-sm font-semibold hover:bg-zinc-700 transition">Batal</button>
                        <button type="button" @click="
                            if(!absSubject) return notify('Pilih matkul!');
                            if(!absNotes) return notify('Deskripsi wajib diisi!');
                            fetch('/kh/attendance', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    subject_name: absSubject,
                                    date: new Date().toISOString().split('T')[0],
                                    notes: absNotes,
                                    attendances: [{
                                        student_id: {{ $student->id }},
                                        status: absStatus
                                    }]
                                })
                            })
                            .then(res => res.json())
                            .then(res => {
                                if(res.success) {
                                    modalAbsen = false;
                                    notify('Absensi ' + absSubject + ' berhasil direkap!');
                                    location.reload(); // Refresh to update nyawa
                                }
                            });
                        "
                            class="flex-1 bg-red-600 text-white py-3 rounded-xl text-sm font-bold hover:bg-red-500 transition">Catat</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal Tambah Mahasiswa -->
        <div x-show="modalAddStudent" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            style="display: none;">
            <div @click.away="modalAddStudent = false" x-show="modalAddStudent"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-md p-6 md:p-8 space-y-6 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-bold text-white mt-1">Tambah Anggota Kelas</h3>
                        <p class="text-xs text-zinc-500 mt-1">Masukkan data mahasiswa ke database.</p>
                    </div>
                    <button @click="modalAddStudent = false"
                        class="text-zinc-500 hover:text-white bg-black p-2 rounded-full border border-zinc-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form x-data="{ stdName: '', stdNim: '', stdRole: 'mahasiswa' }" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">Nomor Induk
                            Mahasiswa (NIM)</label>
                        <input type="number" x-model="stdNim" required
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
                            placeholder="Contoh: 231011402802">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">Nama
                            Lengkap</label>
                        <input type="text" x-model="stdName" required
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
                            placeholder="Contoh: ARFIANNISA KAYLA">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">Jabatan /
                            Role</label>
                        <select x-model="stdRole"
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                            <option value="mahasiswa">Mahasiswa Biasa</option>
                            <option value="ketua_kelas">Ketua Kelas</option>
                            <option value="sekretaris">Sekretaris</option>
                            <option value="bendahara">Bendahara</option>
                        </select>
                    </div>
                    <button type="button" @click="
                        if(!stdName || !stdNim) return notify('Lengkapi data!');
                        fetch('/kh/student', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                name: stdName,
                                nim: stdNim,
                                role: stdRole
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if(data.success) {
                                semuaMahasiswa.push({
                                    id: data.student.id,
                                    nim: stdNim,
                                    name: stdName,
                                    role: stdRole
                                });
                                modalAddStudent = false;
                                notify('Mahasiswa ' + stdName + ' berhasil ditambahkan!');
                                stdName = ''; stdNim = '';
                            } else {
                                notify('Gagal menambah mahasiswa! NIM mungkin sudah terdaftar.');
                            }
                        })
                        .catch(err => notify('Error: ' + err));
                    "
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-blue-500/20 mt-2">
                        Simpan Mahasiswa
                    </button>
                </form>
            </div>
        </div>


        <!-- Modal Tambah Mata Kuliah Master -->
        <div x-show="modalAddSubject" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            style="display: none;">
            <div @click.away="modalAddSubject = false" x-show="modalAddSubject"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-md p-6 md:p-8 space-y-6 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-bold text-white mt-1">Tambah Mata Kuliah</h3>
                        <p class="text-xs text-zinc-500 mt-1">Masukkan mata kuliah baru untuk semester ini/depan.</p>
                    </div>
                    <button @click="modalAddSubject = false"
                        class="text-zinc-500 hover:text-white bg-black p-2 rounded-full border border-zinc-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form x-data="{ msName: '', msSks: 2, msCode: '06TPLE013', msLecturer: '' }" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">Nama Mata
                            Kuliah</label>
                        <input type="text" x-model="msName" required
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition"
                            placeholder="Contoh: Pemrograman Mobile">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">SKS</label>
                            <input type="number" x-model="msSks" required
                                class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">Kode
                                Matkul</label>
                            <input type="text" x-model="msCode" required
                                class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">Dosen
                            Default</label>
                        <input type="text" x-model="msLecturer"
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition"
                            placeholder="Contoh: Dr. John Doe">
                    </div>
                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="modalAddSubject = false"
                            class="flex-1 bg-zinc-800 text-zinc-300 py-3.5 rounded-xl text-sm font-bold hover:bg-zinc-700 transition">Batal</button>
                        <button type="button" @click="
                            if(!msName || !msSks) return notify('Lengkapi data matkul!');
                            fetch('/kh/master-subject', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                                },
                                body: JSON.stringify({ name: msName, sks: msSks, code: msCode, default_lecturer: msLecturer })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if(data.success) {
                                    matkuls.push({ id: data.subject.id, name: msName, sks: msSks, code: msCode, lecturer: msLecturer });
                                    modalAddSubject = false;
                                    notify('Matkul ' + msName + ' berhasil ditambahkan!');
                                    msName = ''; msLecturer = '';
                                }
                            });
                        "
                            class="flex-1 bg-white text-black py-3.5 rounded-xl text-sm font-bold hover:bg-zinc-200 transition">Simpan
                            Matkul</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="modalPassword" x-transition.opacity
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            style="display: none;">
            <div @click.away="modalPassword = false" x-show="modalPassword"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-md p-6 md:p-8 space-y-6 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
                <div>
                    <h3 class="text-xl font-bold text-white mb-1">Ganti Password</h3>
                    <p class="text-xs text-zinc-500">Gunakan password yang kuat agar akun tetap aman.</p>
                </div>
                <form class="space-y-4" x-data="{ oldP: '', newP: '' }">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-2">Password
                            Lama</label>
                        <input type="password" x-model="oldP"
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 outline-none"
                            required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-2">Password
                            Baru</label>
                        <input type="password" x-model="newP"
                            class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 outline-none"
                            required>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="modalPassword = false"
                            class="flex-1 bg-zinc-800 text-zinc-300 py-3 rounded-xl text-sm font-semibold hover:bg-zinc-700 transition">Batal</button>
                        <button type="button" @click="
                            if(!oldP || !newP) return notify('Lengkapi data!');
                            fetch('/kh/password', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                                },
                                body: JSON.stringify({ old_password: oldP, new_password: newP })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if(data.success) {
                                    modalPassword = false;
                                    notify('Password berhasil diubah!');
                                } else {
                                    notify(data.message || 'Gagal ubah password!');
                                }
                            });
                        "
                            class="flex-1 bg-blue-600 text-white py-3 rounded-xl text-sm font-bold hover:bg-blue-500 transition shadow-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Konfirmasi Custom -->
        <div x-show="modalConfirm" x-transition.opacity
            class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/90 backdrop-blur-md"
            style="display: none;">
            <div @click.away="modalConfirm = false" x-show="modalConfirm"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-sm p-8 text-center space-y-6 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 to-rose-600"></div>
                <div
                    class="w-16 h-16 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto border border-red-500/20">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white mb-2" x-text="confirmData.title"></h3>
                    <p class="text-sm text-zinc-400" x-text="confirmData.message"></p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button @click="modalConfirm = false"
                        class="flex-1 bg-zinc-800 text-zinc-300 py-3.5 rounded-2xl text-sm font-bold hover:bg-zinc-700 transition">Batal</button>
                    <button @click="confirmData.action()"
                        class="flex-1 bg-red-600 text-white py-3.5 rounded-2xl text-sm font-bold hover:bg-red-500 transition shadow-lg shadow-red-600/20">Ya,
                        Hapus</button>
                </div>
            </div>
        </div>

        @if(($student->role ?? '') === 'super_admin')
            <!-- Modal Registrasi Kelas Terpadu -->
            <div x-show="modalRegisterClass" x-transition.opacity
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
                style="display: none;">
                <div @click.away="modalRegisterClass = false" x-show="modalRegisterClass"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-md p-6 space-y-5 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-bold text-white mb-1">Registrasi Administrator Kelas</h3>
                            <p class="text-xs text-zinc-500">Daftarkan kelas baru + Ketua Kelas dalam satu langkah.</p>
                        </div>
                        <button @click="modalRegisterClass = false"
                            class="text-zinc-500 hover:text-white bg-black p-2 rounded-full border border-zinc-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <form method="POST" action="/kh/class" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-1">Nama Lengkap
                                Ketua Kelas <span class="text-blue-400">*</span></label>
                            <input type="text" name="ketua_name" required
                                class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-blue-500 transition"
                                placeholder="Contoh: ARIYAS PRATAMA RAMADHAN">
                            <p class="text-[10px] text-zinc-600 mt-1">⚡ Nama ini akan menjadi <strong
                                    class="text-zinc-400">username login</strong> Ketua Kelas.</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-1">NIM
                                    <span class="text-blue-400">*</span></label>
                                <input type="text" name="ketua_nim" required
                                    class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-blue-500 transition"
                                    placeholder="231011403268">
                                <p class="text-[10px] text-zinc-600 mt-1">🔐 Password: NIM + <strong>KK</strong></p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-1">Kode
                                    Kelas <span class="text-blue-400">*</span></label>
                                <input type="text" name="class_code" required
                                    class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-blue-500 font-mono transition"
                                    placeholder="06TPLE015">
                                <p class="text-[10px] text-zinc-600 mt-1">🔑 Kode unik isolasi data kelas.</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-1">Prodi /
                                Jurusan <span class="text-blue-400">*</span></label>
                            <input type="text" name="department" required
                                class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-blue-500 transition"
                                placeholder="Teknik Informatika">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-1">No. HP /
                                Email Kontak</label>
                            <input type="text" name="contact"
                                class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-blue-500 transition"
                                placeholder="08xxxxxxxxxx atau nama@email.com">
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="modalRegisterClass = false"
                                class="flex-1 bg-zinc-800 text-zinc-300 py-3 rounded-xl text-sm font-bold hover:bg-zinc-700 transition">Batal</button>
                            <button type="submit"
                                class="flex-1 bg-blue-600 text-white py-3 rounded-xl text-sm font-bold hover:bg-blue-500 transition shadow-lg shadow-blue-500/20">Aktifkan
                                Kelas</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Toast Notification UI -->
        <div x-show="showToast" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            class="fixed bottom-20 md:bottom-10 left-1/2 -translate-x-1/2 z-[150]" style="display: none;">
            <div
                class="bg-zinc-800 text-white px-6 py-3 rounded-full shadow-2xl border border-zinc-700 flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium whitespace-nowrap" x-text="toastMessage"></span>
            </div>
        </div>
    </div>
</body>

</html>