document.addEventListener('DOMContentLoaded', function () {
    const deleteLinks = document.querySelectorAll('[data-confirm]');
    deleteLinks.forEach(function (link) {
        link.addEventListener('click', function (event) {
            const message = link.getAttribute('data-confirm') || 'Yakin ingin menghapus data ini?';
            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });
});
