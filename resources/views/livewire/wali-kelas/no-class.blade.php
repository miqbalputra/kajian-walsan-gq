<div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
    <div class="w-24 h-24 bg-amber-100 rounded-[2rem] flex items-center justify-center mb-6">
        <span class="material-symbols-rounded text-amber-600 text-5xl">warning</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Kelas Terpilih</h1>
    <p class="text-gray-500 max-w-md mx-auto mb-8">
        Akun Anda belum dipasangkan dengan kelas manapun. Silakan hubungi Administrator untuk mengatur kelas tugas Anda.
    </p>
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm inline-block">
        <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-3">Informasi Akun</p>
        <div class="flex items-center gap-3 text-left">
            <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                <span class="material-symbols-rounded text-primary-600">person</span>
            </div>
            <div>
                <p class="font-bold text-gray-900 leading-none mb-1">{{ auth()->user()->name }}</p>
                <p class="text-xs text-primary-600 font-bold uppercase">{{ auth()->user()->role->display_name }}</p>
            </div>
        </div>
    </div>
</div>