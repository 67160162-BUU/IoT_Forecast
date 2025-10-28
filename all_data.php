<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Sensor Data - ESP32 Dashboard</title>
    
    <link href="index_style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* บังคับให้ placeholder เป็นสีขาวสำหรับช่อง search นี้ */
        #searchInput::placeholder {
        color: #fff; 
        opacity: 1; /* ทำให้ไม่จาง */
        }

        #searchInput::-webkit-input-placeholder { /* สำหรับ Chrome/Safari */
        color: #fff;
        opacity: 1;
        }

        #searchInput::-moz-placeholder { /* สำหรับ Firefox */
        color: #fff;
        opacity: 1;
        }
    </style>
</head>
<body style="background-color: #121212;">
    
<div class="container my-4">
    <div class="weather-section">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 style="color: #0dcaf0;" class="h5 m-0">ข้อมูลทั้งหมด (จากตาราง SensorForecast)</h2>
            <a href="forecast_index.php" class="btn btn-info btn-sm"> <i class="bi bi-arrow-left"></i> กลับหน้าหลัก
            </a>
        </div>

        <div class="input-group my-3">
            <span class="input-group-text" id="search-addon" style="background-color: #3a3a3c; border-color: #444;">
                <i class="bi bi-search text-white-50"></i>
            </span>
            <input type="text" class="form-control text-white" id="searchInput" placeholder="ค้นหาในตาราง..." style="background-color: #1e1e1e; color: #fff; border-color: #444;">
        </div>


        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover align-middle">
                
                <thead>
                    <tr style="cursor: pointer;">
                        <th scope="col" onclick="sortTable(0, true)">ID ⇅</th>
                        <th scope="col" onclick="sortTable(1, false)">เวลาที่บันทึก ⇅</th>
                        <th scope="col" onclick="sortTable(2, true)">อุณหภูมิ (°C) ⇅</th>
                        <th scope="col" onclick="sortTable(3, true)">ความชื้น (%) ⇅</th>
                        <th scope="col" onclick="sortTable(4, true)">ความกดอากาศ (hPa) ⇅</th>
                        <th scope="col" onclick="sortTable(5, true)">ระดับแสง (%) ⇅</th>
                        <th scope="col" onclick="sortTable(6, true)">ความสว่าง (Lux) ⇅</th>
                    </tr>
                </thead>
            <tbody id="all-data-table-body">
        </div>
    </div>
</div>

<script>
    
    let sortAscending = true;
     * ✨ [ใหม่] ฟังก์ชันสำหรับเรียงข้อมูลในตาราง
     * @param {number} colIndex - ดัชนีของคอลัมน์ที่จะเรียง (เริ่มจาก 0)
     * @param {boolean} isNumeric - บอกว่าคอลัมน์นั้นเป็นตัวเลขหรือไม่
     */
    function sortTable(colIndex, isNumeric) {
        const tableBody = document.getElementById('all-data-table-body');
        const rows = Array.from(tableBody.querySelectorAll('tr')); // แปลง NodeList เป็น Array

        const sortedRows = rows.sort((a, b) => {
            const valA = a.cells[colIndex].textContent;
            const valB = b.cells[colIndex].textContent;

            let comparisonA, comparisonB;

            if (isNumeric) {
                comparisonA = parseFloat(valA) || 0;
                comparisonB = parseFloat(valB) || 0;
            } else {
                comparisonA = valA.toUpperCase();
                comparisonB = valB.toUpperCase();
            }
            
            let comparison = 0;
            if (comparisonA > comparisonB) {
                comparison = 1;
            } else if (comparisonA < comparisonB) {
                comparison = -1;
            }

            return sortAscending ? comparison : (comparison * -1);
        });

        sortAscending = !sortAscending;
        tableBody.innerHTML = '';
        sortedRows.forEach(row => {
            tableBody.appendChild(row);
        });
    }

    async function fetchAndDisplayAllData() {
        const tableBody = document.getElementById('all-data-table-body');
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-white-50">กำลังโหลดข้อมูลทั้งหมด...</td></tr>';
        
        try {
            const response = await fetch('get_all_data.php'); 
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const result = await response.json();

            if (result.status === 'success' && result.data.length > 0) {
                tableBody.innerHTML = ''; 
                const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
                
                result.data.forEach(record => {
                    const row = document.createElement('tr');
                    const formattedTimestamp = new Date(record.datetime).toLocaleString('th-TH', options);
                    
                    row.innerHTML = `
                        <td data-label="ID">${record.id}</td>
                        <td data-label="เวลาที่บันทึก">${formattedTimestamp}</td>
                        <td data-label="อุณหภูมิ (°C)">${parseFloat(record.temp).toFixed(1)}</td>
                        <td data-label="ความชื้น (%)">${parseInt(record.hum)}</td>
                        <td data-label="ความกดอากาศ (hPa)">${parseFloat(record.pres).toFixed(1)}</td>
                        <td data-label="ระดับแสง (%)">${parseInt(record.lvl)}</td>
                        <td data-label="ความสว่าง (Lux)">${parseInt(record.lux)}</td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">ไม่พบข้อมูลหรือเกิดข้อผิดพลาด</td></tr>';
            }
        } catch (error) {
            console.error("Could not fetch all data:", error);
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data: ' + error.message + '</td></tr>';
        }
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        fetchAndDisplayAllData();
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toUpperCase();
            const tableBody = document.getElementById('all-data-table-body');
            const rows = tableBody.querySelectorAll('tr');

            rows.forEach(row => {
                if (row.textContent.toUpperCase().indexOf(filter) > -1) {
                    row.style.display = ""; // แสดงแถว
                } else {
                    row.style.display = "none"; // ซ่อนแถว
                }
            });
        });
    });
</script>

</body>
</html>