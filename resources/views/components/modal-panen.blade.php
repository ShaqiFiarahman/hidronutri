<!-- Modal Panen Kustom Global -->
<div id="modal-panen" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0">
    <div id="modal-panen-content" class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 transform scale-95 transition-all duration-300 border border-gray-100">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-50 mb-5 border border-red-100">
            <i class="fa-solid fa-basket-shopping text-2xl text-red-600"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Konfirmasi Panen</h3>
        <p class="text-sm text-gray-500 text-center mb-6 leading-relaxed">
            Apakah Anda yakin ingin menyelesaikan sesi tanam ini dan menandainya sebagai telah panen? Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="flex gap-3">
            <button type="button" onclick="closeModalPanen()" 
                    class="flex-1 px-4 py-3 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="button" onclick="submitModalPanen()" 
                    class="flex-1 px-4 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold text-sm transition-colors shadow-sm shadow-red-200/50">
                Ya, Selesaikan
            </button>
        </div>
    </div>
</div>

<script>
    let currentPanenFormId = null;

    function openModalPanen(formId) {
        currentPanenFormId = formId;
        const modal = document.getElementById('modal-panen');
        const modalContent = document.getElementById('modal-panen-content');
        
        // Pindahkan modal ke body agar tidak terpengaruh z-index atau overflow parent
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
        
        // Hapus class hidden dan tambahkan flex
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Sedikit delay agar transisi CSS berjalan
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);
    }

    function closeModalPanen() {
        const modal = document.getElementById('modal-panen');
        const modalContent = document.getElementById('modal-panen-content');
        
        // Jalankan animasi hilang
        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        
        // Tunggu animasi selesai lalu sembunyikan sepenuhnya
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            currentPanenFormId = null;
        }, 300);
    }

    function submitModalPanen() {
        if (currentPanenFormId) {
            document.getElementById(currentPanenFormId).submit();
        }
    }
</script>
