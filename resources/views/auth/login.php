<?php 
ob_start(); 

// Data URI untuk logo agar tidak bergantung pada routing atau path URL
$sourceLogo = __DIR__ . '/../../../assets/logoumb.png';
$logoSrc = file_exists($sourceLogo) ? 'data:image/png;base64,' . base64_encode(file_get_contents($sourceLogo)) : url('assets/logoumb.png');
?>

<div class="w-full max-w-5xl grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">

    <!-- ===== Panel Kiri: Branding ===== -->
    <div class="hidden lg:block text-center lg:text-left">
        <div class="inline-flex items-center gap-3 mb-8">
            <div class="w-16 h-16 rounded-xl bg-white/95 border border-white flex items-center justify-center shadow-lg backdrop-blur p-2">
                <img src="<?= $logoSrc ?>" alt="Logo UMB" class="w-full h-full object-contain">
            </div>
            <div class="text-left">
                <p class="font-heading font-extrabold text-white text-lg leading-tight">SIM Skripsi</p>
                <p class="text-emerald-300 text-xs font-semibold">Universitas Muhammadiyah Bengkulu</p>
            </div>
        </div>

        <h1 class="font-heading font-extrabold text-white text-4xl leading-tight mb-5">
            Selamat Datang<br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 via-teal-200 to-white">
                Di Portal Skripsi
            </span>
        </h1>

        <p class="text-slate-200/90 text-sm leading-relaxed max-w-md mx-auto lg:mx-0 mb-8">
            Kelola pengajuan judul proposal, logbook bimbingan mandiri, pendaftaran seminar, 
            dan sidang akhir mahasiswa dalam satu ekosistem akademik terpadu.
        </p>

        <div class="grid grid-cols-3 gap-4 max-w-md mx-auto lg:mx-0">
            <div class="glass rounded-xl p-4 text-center">
                <i class="fa-solid fa-graduation-cap text-emerald-300 text-lg mb-2"></i>
                <p class="text-white font-bold text-lg leading-none">Skripsi</p>
                <p class="text-[11px] text-slate-300 mt-1">Online</p>
            </div>
            <div class="glass rounded-xl p-4 text-center">
                <i class="fa-solid fa-key text-emerald-300 text-lg mb-2"></i>
                <p class="text-white font-bold text-lg leading-none">SSO</p>
                <p class="text-[11px] text-slate-300 mt-1">JWT Terpadu</p>
            </div>
            <div class="glass rounded-xl p-4 text-center">
                <i class="fa-solid fa-file-pdf text-emerald-300 text-lg mb-2"></i>
                <p class="text-white font-bold text-lg leading-none">Berkas</p>
                <p class="text-[11px] text-slate-300 mt-1">PDF Digital</p>
            </div>
        </div>
    </div>

    <!-- ===== Panel Kanan: Form Login ===== -->
    <div class="w-full">
        <div class="bg-white rounded-2xl shadow-soft p-7 sm:p-9 border border-slate-100">

            <!-- Logo mobile -->
            <div class="lg:hidden flex items-center gap-3 mb-7 justify-center">
                <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 flex items-center justify-center shadow-sm p-1.5">
                    <img src="<?= $logoSrc ?>" alt="Logo UMB" class="w-full h-full object-contain">
                </div>
                <div>
                    <p class="font-heading font-extrabold text-slate-900 text-sm leading-tight">SIM Skripsi UMB</p>
                    <p class="text-blue-600 text-[11px] font-semibold">Portal Layanan Akademik</p>
                </div>
            </div>

            <!-- Judul -->
            <div class="mb-7">
                <h2 class="font-heading font-extrabold text-2xl text-slate-900 mb-1.5">Login Sistem</h2>
                <p class="text-slate-500 text-sm">Gunakan akun SSO SIM Mahasiswa untuk melanjutkan.</p>
            </div>

            <!-- Flash Error Message -->
            <?php if (!empty($error)): ?>
            <div class="flex items-start gap-3 p-4 rounded-xl border bg-red-50 border-red-200 text-red-800 mb-5" role="alert">
                <i class="fa-solid fa-circle-exclamation text-red-600 mt-0.5 text-base flex-shrink-0"></i>
                <div class="text-sm font-medium leading-relaxed flex-1"><?= htmlspecialchars($error) ?></div>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" action="<?= url('/login') ?>" class="space-y-5">
                <div>
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Username / NPM / NIP
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" id="username" name="username" required
                               placeholder="Contoh: 20260001 atau dosen01"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" required
                               placeholder="Masukkan password akun SSO"
                               class="w-full pl-10 pr-11 py-3 rounded-xl border border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                        <button type="button" id="btn-toggle-password"
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600">
                            <i id="icon-eye" class="fa-solid fa-eye"></i>
                            <i id="icon-eye-slash" class="fa-solid fa-eye-slash hidden"></i>
                        </button>
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-3.5 px-4 rounded-xl font-heading font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                    <span>Masuk ke SIM Skripsi</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

            <!-- Mode Cepat / Akun Demo 5 Role SSO -->
            <div class="mt-6 pt-5 border-t border-slate-100">
                <p class="text-center text-xs font-semibold text-slate-500 mb-3">
                    Akses Cepat Pengujian 5 Peran (SSO):
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
                    <a href="<?= url('/quick-login/mahasiswa') ?>"
                       class="py-2 px-2 rounded-lg border border-blue-200 bg-blue-50/70 hover:bg-blue-100 text-blue-800 font-bold text-center transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-user-graduate text-blue-600"></i>
                        <span>Mahasiswa</span>
                    </a>
                    <a href="<?= url('/quick-login/dosen') ?>"
                       class="py-2 px-2 rounded-lg border border-emerald-200 bg-emerald-50/70 hover:bg-emerald-100 text-emerald-800 font-bold text-center transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-chalkboard-user text-emerald-600"></i>
                        <span>Dosen</span>
                    </a>
                    <a href="<?= url('/quick-login/admin_prodi') ?>"
                       class="py-2 px-2 rounded-lg border border-amber-200 bg-amber-50/70 hover:bg-amber-100 text-amber-800 font-bold text-center transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-building-columns text-amber-600"></i>
                        <span>Admin Prodi</span>
                    </a>
                    <a href="<?= url('/quick-login/admin_dekanat') ?>"
                       class="py-2 px-2 rounded-lg border border-teal-200 bg-teal-50/70 hover:bg-teal-100 text-teal-800 font-bold text-center transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-landmark text-teal-600"></i>
                        <span>Admin Dekanat</span>
                    </a>
                    <a href="<?= url('/quick-login/admin') ?>"
                       class="py-2 px-2 rounded-lg border border-purple-200 bg-purple-50/70 hover:bg-purple-100 text-purple-800 font-bold text-center transition flex items-center justify-center gap-1.5 col-span-2 sm:col-span-1">
                        <i class="fa-solid fa-user-shield text-purple-600"></i>
                        <span>Admin Univ</span>
                    </a>
                </div>

                <!-- Info Kredensial SSO Asli (sim-mhs-api) -->
                <div class="mt-4 p-3 bg-slate-50 border border-slate-200 rounded-xl text-[11px] text-slate-600">
                    <p class="font-bold text-slate-700 mb-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info text-blue-600"></i>
                        <span>Kredensial SSO (https://sim-mhs-api.vercel.app):</span>
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 text-[11px] text-slate-500">
                        <div>• <b>Mahasiswa</b>: <code class="text-blue-700 cursor-pointer hover:underline" onclick="fillCred('mhs001','Mhs12345!')">mhs001 / Mhs12345!</code></div>
                        <div>• <b>Dosen</b>: <code class="text-emerald-700 cursor-pointer hover:underline" onclick="fillCred('dosen01','Dosen123!')">dosen01 / Dosen123!</code></div>
                        <div>• <b>Admin Prodi</b>: <code class="text-amber-700 cursor-pointer hover:underline" onclick="fillCred('adm_prodi_ti','AdmProdi123!')">adm_prodi_ti / AdmProdi123!</code></div>
                        <div>• <b>Admin Dekanat</b>: <code class="text-teal-700 cursor-pointer hover:underline" onclick="fillCred('adm_dekanat','AdmDekan123!')">adm_dekanat / AdmDekan123!</code></div>
                        <div class="sm:col-span-2">• <b>Admin Univ</b>: <code class="text-purple-700 cursor-pointer hover:underline" onclick="fillCred('admin','Admin123!')">admin / Admin123!</code></div>
                    </div>
                </div>
            </div>


        </div>

        <p class="text-center text-[11px] text-slate-400 mt-5 leading-relaxed">
            <i class="fa-solid fa-shield-halved text-emerald-600"></i>
            Terintegrasi Single Sign-On (SSO) SIM Mahasiswa UMB.
        </p>
    </div>

</div>

<!-- Toggle password script & auto-fill helper -->
<script>
function fillCred(u, p) {
    var uInput = document.getElementById('username');
    var pInput = document.getElementById('password');
    if (uInput && pInput) {
        uInput.value = u;
        pInput.value = p;
        uInput.focus();
    }
}

(function () {
    var btn = document.getElementById('btn-toggle-password');
    var input = document.getElementById('password');
    var eye = document.getElementById('icon-eye');
    var eyeSlash = document.getElementById('icon-eye-slash');
    if (!btn || !input) return;

    btn.addEventListener('click', function () {
        var isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        eye.classList.toggle('hidden', !isPassword);
        eyeSlash.classList.toggle('hidden', isPassword);
    });
})();
</script>


<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/auth.php'; 
?>
