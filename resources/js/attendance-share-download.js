import { toPng } from 'html-to-image';

/**
 * The captured cards are rendered off-screen at a fixed width so downloads
 * look identical on desktop and mobile browsers. Kept as a window helper so
 * a Livewire-rendered Alpine block never depends on module execution timing.
 */
window.downloadAttendanceShareImage = async (targetId, filename) => {
    const node = document.getElementById(targetId);

    if (!node) {
        throw new Error('Kartu statistik tidak ditemukan.');
    }

    try {
        if (document.fonts?.ready) {
            await document.fonts.ready;
        }

        const image = await toPng(node, {
            backgroundColor: '#f5faf7',
            cacheBust: true,
            pixelRatio: 2,
        });
        const link = document.createElement('a');

        link.download = `${filename}.png`;
        link.href = image;
        link.click();
    } catch (error) {
        console.error('Gagal membuat gambar statistik kehadiran.', error);
        window.Swal?.fire({
            icon: 'error',
            title: 'Gambar belum dapat dibuat',
            text: 'Silakan coba kembali. Jika masalah berlanjut, muat ulang halaman laporan.',
        });
    }
};
