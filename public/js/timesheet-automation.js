document.addEventListener('DOMContentLoaded', function() {
    
    // Pastikan ID ini sesuai dengan ID di tag <table> Anda
    const TABLE_ID = 'timesheetTable'; 
    const table = document.getElementById(TABLE_ID);

    if (!table) {
        console.warn('Table Timesheet tidak ditemukan. Script berhenti.');
        return; 
    }

    // ===============================================================
    // 1. EVENT LISTENER GLOBAL (Event Delegation)
    // ===============================================================
    // Kita pasang listener di Tabel agar baris baru (Add Row) juga terdeteksi
    table.addEventListener('change', function(e) {
        
        // A. Jika Dropdown DISCIPLINE berubah
        if (e.target && e.target.classList.contains('discipline-select')) {
            filterTasksByDiscipline(e.target, true); // true = Reset nilai task karena disiplin berubah
        }

        // B. Jika Dropdown TASK berubah
        if (e.target && e.target.classList.contains('task-select')) {
            updateCostCode(e.target);
        }
    });

    // ===============================================================
    // 2. INISIALISASI AWAL (PENTING UNTUK HALAMAN EDIT)
    // ===============================================================
    // Saat halaman dimuat, kita harus memfilter task sesuai discipline yang tersimpan
    // TAPI parameter kedua 'false' artinya: JANGAN reset nilai yang sudah ada.
    const allDisciplines = document.querySelectorAll('.discipline-select');
    allDisciplines.forEach(select => {
        if(select.value) {
            filterTasksByDiscipline(select, false); 
        }
    });

    // ===============================================================
    // LOGIC A: FILTER TASK SESUAI DISCIPLINE
    // ===============================================================
    function filterTasksByDiscipline(disciplineDropdown, shouldReset = true) {
        // Ambil nilai discipline yang dipilih (misal: "System3D")
        const selectedDiscipline = disciplineDropdown.value;
        
        // Cari elemen terkait di baris yang sama
        const row = disciplineDropdown.closest('tr');
        const taskDropdown = row.querySelector('.task-select');
        const costCodeDropdown = row.querySelector('.cost-code-select');

        if (!taskDropdown) return;

        // 1. Reset Pilihan (Hanya jika user mengubah discipline secara manual)
        if (shouldReset) {
            taskDropdown.value = "";
            if (costCodeDropdown) costCodeDropdown.value = "";
        }

        // 2. Loop semua opsi di dalam dropdown Task
        const options = taskDropdown.querySelectorAll('option');
        
        options.forEach(option => {
            // Jangan sembunyikan opsi default "Select Task..."
            if (option.value === "") return;

            // Ambil data discipline yang kita titipkan di HTML (data-discipline="...")
            const taskDisc = option.getAttribute('data-discipline');

            // 3. Logika Tampil/Sembunyi
            if (selectedDiscipline && taskDisc === selectedDiscipline) {
                // Jika COCOK: Tampilkan
                option.style.display = ""; 
                option.hidden = false;     
                option.disabled = false;
            } else {
                // Jika BEDA: Sembunyikan
                option.style.display = "none"; 
                option.hidden = true;          
                option.disabled = true; // Disabled biar aman
            }
        });
    }

    // ===============================================================
    // LOGIC B: AUTOFILL COST CODE BERDASARKAN TASK
    // ===============================================================
    function updateCostCode(taskDropdown) {
        const taskValue = taskDropdown.value; // Contoh: "S3D001-Piping Routing"
        const row = taskDropdown.closest('tr');
        const costCodeDropdown = row.querySelector('.cost-code-select');

        if (costCodeDropdown) {
            if (taskValue) {
                // Logic: Ambil kata sebelum tanda strip "-" pertama
                // "S3D001-Piping Routing" -> menjadi "S3D001"
                const parts = taskValue.split('-');
                const extractedCode = parts[0].trim();

                // Isi Cost Code
                costCodeDropdown.value = extractedCode;

                // Efek Visual (Kuning sebentar)
                visualFeedback(costCodeDropdown);
            } else {
                // Jika Task dikosongkan, Cost Code ikut kosong
                costCodeDropdown.value = '';
            }
        }
    }

    // Fungsi Efek Visual
    function visualFeedback(element) {
        element.style.transition = "background-color 0.3s";
        element.style.backgroundColor = "#fef08a"; // Kuning muda
        setTimeout(() => {
            element.style.backgroundColor = ""; // Balik normal
        }, 500);
    }

});