document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileName = document.getElementById('fileName');
    const previewTable = document.getElementById('previewTable');
    const previewWrapper = document.getElementById('previewWrapper');

    if (!dropZone || !fileInput) return;

    // === Drag-and-drop behavior ===
    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.classList.add('bg-light');
    });

    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('bg-light'));

    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('bg-light');
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            showFile(fileInput.files[0]);
        }
    });

    fileInput.addEventListener('change', e => {
        if (fileInput.files.length > 0) showFile(fileInput.files[0]);
    });

    // === Display selected file name and preview CSV ===
    function showFile(file) {
        fileName.textContent = `📄 ${file.name}`;
        const reader = new FileReader();

        if (file.name.endsWith('.csv')) {
            reader.onload = e => previewCSV(e.target.result);
            reader.readAsText(file);
        } else {
            previewWrapper.classList.add('d-none');
        }
    }

    // === Parse and preview CSV content ===
    function previewCSV(csv) {
        const rows = csv.trim().split(/\r?\n/).map(r => r.split(/[;,]/)); // flexible split
        let html = `<thead><tr><th>#</th>${rows[0].map(c => `<th>${c}</th>`).join('')}</tr></thead><tbody>`;
        rows.slice(1, 11).forEach((r, i) => { // limit preview to 10 rows
            html += `<tr><td>${i + 1}</td>${r.map(c => `<td>${c}</td>`).join('')}</tr>`;
        });
        html += `</tbody>`;
        previewTable.innerHTML = html;
        previewWrapper.classList.remove('d-none');
    }
});
