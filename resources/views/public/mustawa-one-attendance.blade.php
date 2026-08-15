<x-layouts.app title="Form Presensi Mustawa 1" :force-light="true">
    @php
        $studentOptions = $students->map(fn ($student) => [
            'id' => (string) $student['id'],
            'label' => $student['label'],
            'parent_types' => $student['parent_types'],
        ])->values();
        $oldStatus = old('status', $availability['allowed_statuses'][0] ?? '');
    @endphp

    <div class="min-h-screen bg-slate-100 px-4 py-7 sm:py-10">
        <main class="mx-auto max-w-xl" x-data="{
            students: @js($studentOptions),
            studentId: @js((string) old('student_id', '')),
            parentType: @js(old('parent_type', '')),
            status: @js($oldStatus),
            allowedStatuses: @js($availability['allowed_statuses']),
            get selectedStudent() { return this.students.find((student) => student.id === this.studentId) || null },
            get parentTypes() { return this.selectedStudent ? this.selectedStudent.parent_types : [] },
            resetParentType() { if (!this.parentTypes.includes(this.parentType)) this.parentType = '' },
        }">
            <header class="mb-5 rounded-3xl bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-600 px-6 py-7 text-white shadow-xl shadow-emerald-900/15">
                <div class="mb-5 flex items-center gap-3">
                    <span class="material-symbols-rounded flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15 text-2xl">menu_book</span>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-emerald-100">Kelompok Tahfidz Griya Qur'an</p>
                        <h1 class="text-xl font-extrabold">Presensi Wali Santri Mustawa 1</h1>
                    </div>
                </div>
                @if($availability['available'])
                    <p class="text-sm text-emerald-50">{{ $availability['event']->title }}</p>
                    <p class="mt-1 text-sm text-emerald-100">{{ $availability['event']->formatted_date }} · {{ $availability['event']->time_range }}</p>
                @else
                    <p class="text-sm text-emerald-50">Form ini hanya tersedia ketika panitia membuka kajian Mustawa 1.</p>
                @endif
            </header>

            @if(session('success'))
                <div class="mb-5 flex gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                    <span class="material-symbols-rounded text-2xl text-emerald-600">check_circle</span>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if(! $availability['available'])
                <section class="rounded-3xl bg-white p-6 text-center shadow-sm ring-1 ring-slate-200">
                    <span class="material-symbols-rounded text-5xl text-amber-500">event_busy</span>
                    <h2 class="mt-4 text-lg font-bold text-slate-900">Form belum tersedia</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $availability['message'] }}</p>
                    <p class="mt-4 text-sm font-medium text-slate-700">Silakan hubungi panitia kelas apabila membutuhkan bantuan.</p>
                </section>
            @else
                <section class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-7">
                    <div class="mb-6 rounded-2xl border border-sky-100 bg-sky-50 p-4 text-sm leading-6 text-sky-900">
                        <p class="font-bold">Khusus wali santri baru Mustawa 1</p>
                        <p class="mt-1">Pilih status, Ananda, lalu unggah bukti yang sesuai. Form ini tidak digunakan untuk presensi hadir fisik.</p>
                    </div>

                    @error('form')
                        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">{{ $message }}</div>
                    @enderror

                    <form method="POST" action="{{ route('public.mustawa-one-form.store', ['token' => $link->token]) }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf

                        <div class="absolute -left-[10000px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
                            <label for="website">Website</label>
                            <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div>
                            <label for="student_id" class="mb-2 block text-sm font-bold text-slate-800">Nama Ananda <span class="text-red-500">*</span></label>
                            <select id="student_id" name="student_id" x-model="studentId" x-on:change="resetParentType()"
                                class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm text-slate-800 focus:border-emerald-500 focus:ring-emerald-500" required>
                                <option value="">Pilih nama Ananda</option>
                                <template x-for="student in students" :key="student.id">
                                    <option :value="student.id" x-text="student.label"></option>
                                </template>
                            </select>
                            @error('student_id') <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <p class="mb-2 text-sm font-bold text-slate-800">Yang mengajukan <span class="text-red-500">*</span></p>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer rounded-xl border p-3 text-center transition-colors"
                                    :class="parentTypes.includes('father') ? (parentType === 'father' ? 'border-emerald-500 bg-emerald-50 text-emerald-800' : 'border-slate-200 text-slate-700') : 'cursor-not-allowed border-slate-100 bg-slate-50 text-slate-300'">
                                    <input type="radio" name="parent_type" value="father" x-model="parentType" :disabled="!parentTypes.includes('father')" class="sr-only" required>
                                    <span class="material-symbols-rounded block text-2xl">man</span>
                                    <span class="mt-1 block text-sm font-bold">Bapak</span>
                                </label>
                                <label class="cursor-pointer rounded-xl border p-3 text-center transition-colors"
                                    :class="parentTypes.includes('mother') ? (parentType === 'mother' ? 'border-emerald-500 bg-emerald-50 text-emerald-800' : 'border-slate-200 text-slate-700') : 'cursor-not-allowed border-slate-100 bg-slate-50 text-slate-300'">
                                    <input type="radio" name="parent_type" value="mother" x-model="parentType" :disabled="!parentTypes.includes('mother')" class="sr-only" required>
                                    <span class="material-symbols-rounded block text-2xl">woman</span>
                                    <span class="mt-1 block text-sm font-bold">Ibu</span>
                                </label>
                            </div>
                            <p x-show="!studentId" class="mt-2 text-xs text-slate-500">Pilih Ananda terlebih dahulu.</p>
                            @error('parent_type') <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <p class="mb-2 text-sm font-bold text-slate-800">Status presensi <span class="text-red-500">*</span></p>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @if(in_array('hadir_online', $availability['allowed_statuses'], true))
                                    <label class="cursor-pointer rounded-xl border p-4 transition-colors"
                                        :class="status === 'hadir_online' ? 'border-blue-500 bg-blue-50 text-blue-900' : 'border-slate-200 text-slate-700'">
                                        <input type="radio" name="status" value="hadir_online" x-model="status" class="sr-only" required>
                                        <span class="material-symbols-rounded text-2xl">play_circle</span>
                                        <span class="mt-2 block text-sm font-bold">Menyimak online</span>
                                        <span class="mt-1 block text-xs leading-5">Upload foto catatan hasil kajian.</span>
                                    </label>
                                @endif
                                @if(in_array('izin', $availability['allowed_statuses'], true))
                                    <label class="cursor-pointer rounded-xl border p-4 transition-colors"
                                        :class="status === 'izin' ? 'border-amber-500 bg-amber-50 text-amber-900' : 'border-slate-200 text-slate-700'">
                                        <input type="radio" name="status" value="izin" x-model="status" class="sr-only" required>
                                        <span class="material-symbols-rounded text-2xl">description</span>
                                        <span class="mt-2 block text-sm font-bold">Izin berhalangan</span>
                                        <span class="mt-1 block text-xs leading-5">Upload surat pernyataan/izin.</span>
                                    </label>
                                @endif
                            </div>
                            @error('status') <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="proof_file" class="mb-2 block text-sm font-bold text-slate-800">
                                <span x-show="status === 'hadir_online'">Foto catatan hasil kajian</span>
                                <span x-show="status === 'izin'">Foto surat pernyataan/izin</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input id="proof_file" name="proof_file" type="file" accept="image/jpeg,image/png" capture="environment"
                                class="block w-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-3 text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-600 file:px-3 file:py-2 file:text-sm file:font-bold file:text-white hover:file:bg-emerald-700" required>
                            <p class="mt-2 text-xs leading-5 text-slate-500">JPG atau PNG, maksimal 2 MB. Pastikan foto jelas dan berisi dokumen, bukan foto kipas, ruangan, meja, atau objek lain.</p>
                            @error('proof_file') <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="notes" class="mb-2 block text-sm font-bold text-slate-800">Catatan/alasan <span x-show="status === 'izin'" class="text-red-500">*</span></label>
                            <textarea id="notes" name="notes" rows="3" maxlength="500" x-bind:required="status === 'izin'"
                                placeholder="Wajib diisi apabila berhalangan hadir."
                                class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-emerald-500">{{ old('notes') }}</textarea>
                            @error('notes') <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-emerald-600/20 transition-colors hover:bg-emerald-700">
                            <span class="material-symbols-rounded">upload_file</span>
                            Kirim Bukti Presensi
                        </button>
                    </form>
                </section>
            @endif

            <p class="px-4 py-6 text-center text-xs leading-5 text-slate-500">Pengajuan akan diperiksa terlebih dahulu. Untuk kendala data atau presensi hadir fisik, silakan menghubungi panitia.</p>
        </main>
    </div>
</x-layouts.app>
