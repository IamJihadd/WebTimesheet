document.addEventListener("DOMContentLoaded", function () {
    const table = document.getElementById("timesheetTable");
    if (!table) return;

    console.log("Timesheet calculator initialized"); // Debug

    // Calculate totals for a specific row
    function calculateRowTotals(row) {
        let totalRegular = 0;
        let totalOvertime = 0;

        const inputs = row.querySelectorAll(".hour-input");

        inputs.forEach((input) => {
            const value = parseFloat(input.value) || 0;
            const type = input.dataset.type;

            if (type === "regular") {
                totalRegular += value;
            } else if (type === "overtime") {
                totalOvertime += value;
            }
        });

        const totalRegCell = row.querySelector(".total-regular");
        const totalOvertimeCell = row.querySelector(".total-overtime");

        if (totalRegCell) {
            totalRegCell.textContent = parseFloat(totalRegular.toFixed(1));
        }
        if (totalOvertimeCell) {
            totalOvertimeCell.textContent = parseFloat(
                totalOvertime.toFixed(1)
            );
        }
    }

    // Calculate total for specific day excluding current input
    function calculateDayTotal(day, type, excludeInput = null) {
        let total = 0;
        const inputs = document.querySelectorAll(
            `.hour-input[data-day="${day}"][data-type="${type}"]`
        );

        inputs.forEach((input) => {
            if (input !== excludeInput) {
                const val = parseFloat(input.value) || 0;
                total += val;
            }
        });
        return total;
    }

    // Calculate total overtime for entire week
    function calculateTotalOvertime(excludeInput = null) {
        let totalOvertime = 0;
        const allOvertimeInputs = document.querySelectorAll(
            '.hour-input[data-type="overtime"]'
        );

        allOvertimeInputs.forEach((input) => {
            if (input !== excludeInput) {
                totalOvertime += parseFloat(input.value) || 0;
            }
        });

        return totalOvertime;
    }

    // Validate and enforce daily regular limit
    function enforceRegularLimit(input) {
        const day = input.dataset.day;
        let currentValue = parseFloat(input.value) || 0;

        // Get total from other tasks for this day
        const otherTasksTotal = calculateDayTotal(day, "regular", input);
        const totalForDay = otherTasksTotal + currentValue;

        console.log(
            `Day: ${day}, Current: ${currentValue}, Others: ${otherTasksTotal}, Total: ${totalForDay}`
        ); // Debug

        if (totalForDay > 8) {
            // Calculate maximum this input can have
            const maxAllowed = 8 - otherTasksTotal;
            const cappedValue = Math.max(0, Math.min(currentValue, maxAllowed));

            input.value = cappedValue;
            input.classList.add("border-red-500", "bg-red-100");

            // Show alert
            alert(
                `Daily limit exceeded!\n\nMonday total cannot exceed 8 hours.\n\nOther tasks already use: ${otherTasksTotal.toFixed(
                    1
                )}h\nMax allowed for this task: ${maxAllowed.toFixed(
                    1
                )}h\n\nValue capped to: ${cappedValue.toFixed(1)}h`
            );

            setTimeout(() => {
                input.classList.remove("border-red-500", "bg-red-100");
            }, 3000);
        } else {
            input.classList.remove("border-red-500", "bg-red-100");
        }
    }

    // Validate and enforce weekly overtime limit
    function enforceOvertimeLimit(input) {
        let currentValue = parseFloat(input.value) || 0;
        const otherOvertimeTotal = calculateTotalOvertime(input);
        const totalWeeklyOT = otherOvertimeTotal + currentValue;

        if (totalWeeklyOT > 4) {
            const maxAllowed = 4 - otherOvertimeTotal;
            const cappedValue = Math.max(0, Math.min(currentValue, maxAllowed));

            input.value = cappedValue;
            input.classList.add("border-red-500", "bg-red-100");

            alert(
                `Weekly overtime limit exceeded!\n\nTotal overtime cannot exceed 4 hours per week.\n\nAlready used: ${otherOvertimeTotal.toFixed(
                    1
                )}h\nMax allowed: ${maxAllowed.toFixed(
                    1
                )}h\n\nValue capped to: ${cappedValue.toFixed(1)}h`
            );

            setTimeout(() => {
                input.classList.remove("border-red-500", "bg-red-100");
            }, 3000);
        } else {
            input.classList.remove("border-red-500", "bg-red-100");
        }
    }

    // Handle input change
    function handleInputChange(e) {
        const input = e.target;
        const type = input.dataset.type;

        // Prevent negative values
        if (parseFloat(input.value) < 0) {
            input.value = 0;
        }

        if (type === "regular") {
            enforceRegularLimit(input);
        } else if (type === "overtime") {
            enforceOvertimeLimit(input);
        }

        const row = input.closest("tr");
        calculateRowTotals(row);
    }

    // Attach event listeners using event delegation
    table.addEventListener("input", function (e) {
        if (e.target.classList.contains("hour-input")) {
            handleInputChange(e);
        }
    });

    table.addEventListener(
        "blur",
        function (e) {
            if (e.target.classList.contains("hour-input")) {
                if (e.target.value === "") {
                    e.target.value = 0;
                }
                handleInputChange(e);
            }
        },
        true
    );

    // Remove row
    table.addEventListener("click", function (e) {
        if (
            e.target.classList.contains("remove-row") ||
            e.target.closest(".remove-row")
        ) {
            e.preventDefault();

            const button = e.target.classList.contains("remove-row")
                ? e.target
                : e.target.closest(".remove-row");
            const row = button.closest("tr");
            const tbody = row.parentElement;

            if (tbody.querySelectorAll("tr").length <= 1) {
                alert("Cannot remove the last row.");
                return;
            }

            if (confirm("Remove this task?")) {
                row.remove();
                reindexRows();
            }
        }
    });

    // Add row
    const tableBody = document.getElementById('entriesContainer');
    const addButton = document.getElementById('addRowBtn');

    if (addButton && tableBody) {
        addButton.addEventListener('click', function () {
            // 1. Ambil baris pertama sebagai template
            const firstRow = tableBody.querySelector('tr');
            if (!firstRow) return;

            // 2. Clone baris
            const newRow = firstRow.cloneNode(true);

            // ============================================================
            // GENERATE ID SEKALI SAJA DI SINI (Agar semua input di baris ini punya ID sama)
            // ============================================================
            const uniqueIndex = Date.now(); 

            // 3. Reset Input
            newRow.querySelectorAll('input').forEach(input => {
                // Simpan nama lama
                const oldName = input.name;
                
                // Update name attribute (Hanya jika punya nama)
                if (oldName) {
                    input.name = oldName.replace(/entries\[\d+\]/, `entries[${uniqueIndex}]`);
                }

                // --- LOGIKA PERBAIKAN ---
                // Cek: Apakah ini Hidden Level Grade (via Name) ATAU Visible Level Grade (via ReadOnly)?
                if (oldName.includes('[level_grade]') || input.hasAttribute('readonly')) {
                    // SOLUSI: Ambil nilai asli dari atribut HTML value (yang dirender PHP)
                    // Ini memastikan teks "Manager" atau level user tetap muncul
                    const originalValue = input.getAttribute('value');
                    input.value = originalValue;
                } 
                else if (input.type === 'number') {
                    input.value = 0; // Reset jam ke 0
                } 
                else {
                    input.value = ''; // Reset text lain
                }
            });

            // 4. Reset Select (Dropdown)
            newRow.querySelectorAll('select').forEach(select => {
                select.name = select.name.replace(/entries\[\d+\]/, `entries[${uniqueIndex}]`);
                select.disabled = false;
                select.value = ""; // Kosongkan pilihan

                // Reset filter task agar opsi muncul semua dulu
                if(select.classList.contains('task-select')) {
                    Array.from(select.options).forEach(opt => {
                        opt.style.display = ""; 
                        opt.hidden = false;
                    });
                }
            });

            // 5. Reset Teks Total
            newRow.querySelectorAll('.total-regular, .total-overtime').forEach(el => el.textContent = '0');

            // 6. Masukkan ke Tabel
            tableBody.appendChild(newRow);
        });
    }

    // Reindex rows
    function reindexRows() {
        const rows = table.querySelectorAll("#entriesContainer tr");

        rows.forEach((row, index) => {
            row.dataset.index = index;

            const inputs = row.querySelectorAll("input, select, textarea");
            inputs.forEach((input) => {
                const name = input.getAttribute("name");
                if (name) {
                    input.setAttribute(
                        "name",
                        name.replace(/\[\d+\]/, `[${index}]`)
                    );
                }
            });
        });
    }

    // Form validation
    const form = document.getElementById("timesheetForm");
    if (form) {
        form.addEventListener("submit", function (e) {
            const days = [
                "monday",
                "tuesday",
                "wednesday",
                "thursday",
                "friday",
                "saturday",
                "sunday",
            ];

            for (let day of days) {
                const total = calculateDayTotal(day, "regular");
                if (total > 8) {
                    e.preventDefault();
                    alert(
                        `ERROR: ${day} has ${total.toFixed(
                            1
                        )} regular hours, which exceeds the 8-hour daily limit!`
                    );
                    return false;
                }
            }

            const totalOT = calculateTotalOvertime();
            if (totalOT > 4) {
                e.preventDefault();
                alert(
                    `ERROR: Total overtime is ${totalOT.toFixed(
                        1
                    )} hours, which exceeds the 4-hour weekly limit!`
                );
                return false;
            }
        });
    }

    // Initialize row totals
    const rows = table.querySelectorAll("#entriesContainer tr");
    rows.forEach((row) => calculateRowTotals(row));

    console.log("Event listeners attached"); // Debug
});
