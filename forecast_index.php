<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESP32 Weather Dashboard</title>
    <link href="index_style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body style="background-color: #121212;">
<div class="container my-4">
    <div class="weather-section text-center">
        <button type="button" class="btn btn-info insert-button" data-bs-toggle="modal" data-bs-target="#insertModal">
            <i class="bi bi-plus-lg"></i> INSERT
        </button>    
        <h1 class="h4" style="color: #0dcaf0;">สถานีสภาพอากาศ</h1>
        <p class="lead text-white-50">Last Updated: <span id="last-updated">--:--:--</span></p>
        <div class="current-temp-display my-2"  ><span  id="temp-value">--</span>°</div>
        <div class="row g-3 justify-content-center mt-4">
            <div class="col-6 col-md-3 d-flex flex-column align-items-center">
                <div class="circle-gauge" id="lvl-gauge" style="--value: 0%;">
                    <div class="circle-gauge-content">
                        <span id="lvl-value">--</span> <span class="unit">%</span>
                    </div>
                </div>
                <div class="gauge-label">Light Level</div>
            </div>
            <div class="col-6 col-md-3 d-flex flex-column align-items-center">
                <div class="circle-gauge" id="lux-gauge" style="--value: 0%;">
                    <div class="circle-gauge-content">
                        <span id="lux-value">--</span> <span class="unit">Lux</span>
                    </div>
                </div>
                <div class="gauge-label">Illuminance</div>
            </div>
            <div class="col-6 col-md-3 d-flex flex-column align-items-center">
                <div class="circle-gauge circle-gauge-pressure" id="pressure-gauge" style="--value: 0%;">
                    <div class="circle-gauge-content">
                        <span id="pressure-value">--</span> <span class="unit">hPa</span>
                    </div>
                </div>
                <div class="gauge-label">Pressure</div>
            </div>
            <div class="col-6 col-md-3 d-flex flex-column align-items-center">
                <div class="circle-gauge circle-gauge-humidity" id="hum-gauge" style="--value: 0%;">
                    <div class="circle-gauge-content">
                        <span id="hum-value">--</span> <span class="unit">%</span>
                    </div>
                </div>
                <div class="gauge-label">Humidity</div>
            </div>
        </div>
    </div> 
</div>
<div class="container my-4">
    <div class="weather-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 style="color: #0dcaf0;" class="h5 m-0">ข้อมูลที่บันทึก 7 ครั้งล่าสุด</h2>
            <a href="all_data.php" class="btn btn-outline-info btn-sm">
                <i class="bi bi-table me-1"></i> ดูข้อมูลทั้งหมด
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th scope="col">สถานที่</th>
                        <th scope="col">เวลาที่บันทึก</th>
                        <th scope="col">อุณหภูมิ (°C)</th>
                        <th scope="col">ความชื้น (%)</th>
                        <th scope="col">ความกดอากาศ (hPa)</th>
                        <th scope="col">ระดับแสง (%)</th>
                        <th scope="col">ความสว่าง (Lux)</th>
                    </tr>
                </thead>
                <tbody id="saved-data-table-body">
                    </tbody>
                
                <tfoot id="summary-footer" style="border-top: 2px solid #0dcaf0;">
                    </tfoot>
                
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #121212;">
            <div class="modal-header">
                <h5 style="color: #0dcaf0;" class="modal-title" id="insertModalLabel">+ INSERT</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="insert-form">
                    <p class="small text-white-50">ข้อมูลล่าสุดจากเซ็นเซอร์:</p>
                    <div class="mb-3">
                        <label for="modal-temp" class="form-label" style="color: #0dcaf0;" >Temperature (°C)</label>
                        <input type="text" class="form-control" id="modal-temp">
                    </div>
                    <div class="mb-3">
                        <label for="modal-hum" class="form-label" style="color: #0dcaf0;" >Humidity (%)</label>
                        <input type="text" class="form-control" id="modal-hum">
                    </div>
                    <div class="mb-3">
                        <label for="modal-lvl" class="form-label" style="color: #0dcaf0;" >Light Level (%)</label>
                        <input type="text" class="form-control" id="modal-lvl">
                    </div>
                    <div class="mb-3">
                        <label for="modal-lux" class="form-label" style="color: #0dcaf0;" >Illuminance (Lux)</label>
                        <input type="text" class="form-control" id="modal-lux">
                    </div>
                    <div class="mb-3">
                        <label for="modal-pres" class="form-label" style="color: #0dcaf0;" >Pressure (hPa)</label>
                        <input type="text" class="form-control" id="modal-pres">
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label for="modal-location" class="form-label" style="color: #0dcaf0;">Location</label>
                        <input type="text" class="form-control" id="modal-location" placeholder="เช่น: ห้องทดลอง, นอกอาคาร" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="submit" class="btn btn-primary" form="insert-form">บันทึกข้อมูล</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: #2c2c2e;">
            
            <div class="modal-header text-white" id="statusModalHeader">
                <h5 class="modal-title" id="statusModalLabel">สถานะการบันทึก</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="statusModalBody" style="color: #f8f9fa;">
                ...
            </div>
            <div class="modal-footer" style="border-top: none;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

    <div id="gemini-fab" title="Ask Gemini">
        <i class="bi bi-stars"></i>
    </div>

    <div id="gemini-popup">
        <div class="gemini-popup-header" style="color: #0dcaf0;">
            <h5><i class="bi bi-stars"></i> Ask Gemini</h5>
            <button type="button" id="gemini-close-btn">&times;</button>
        </div>
        <div class="gemini-popup-body">
            <form id="gemini-form">
                <textarea id="gemini-prompt" placeholder="ถามคำถามเกี่ยวกับข้อมูลสภาพอากาศ หรืออื่นๆ..." rows="4" required></textarea>

                <label class="form-label small text-white-50">เลือกขอบเขตข้อมูลที่จะใช้:</label>
                <div class="btn-group w-100 mb-3" role="group" id="gemini-context-selector">
                    <input type="radio" class="btn-check" name="gemini-context" id="context-7" value="7" checked>
                    <label class="btn btn-outline-info btn-sm" for="context-7">ข้อมูล 7 ครั้ง (ที่แสดง)</label>

                    <input type="radio" class="btn-check" name="gemini-context" id="context-all" value="all">
                    <label class="btn btn-outline-info btn-sm" for="context-all">ข้อมูลทั้งหมด (All)</label>
                </div>
                <button type="submit" class="btn btn-info w-100">
                    <span id="gemini-btn-text">ส่งคำถาม</span>
                    <span id="gemini-spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                </button>
            </form>
            <div id="gemini-response-area" class="text-white">
                <p class="text-white small">คำตอบจะปรากฏที่นี่</p>
            </div>
        </div>
    </div>
    <div class="container my-4">
        <div class="weather-section">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                <h2 style="color: #0dcaf0;" class="h5 mb-2 mb-md-0 me-3">แนวโน้มข้อมูล (Data Trends)</h2>
                <div class="btn-group" role="group" id="chart-range-selector">
                    <input type="radio" class="btn-check" name="btnradio" id="btn-7" autocomplete="off" value="7" checked>
                    <label class="btn btn-outline-info btn-sm" for="btn-7">7 ครั้งล่าสุด</label>

                    <input type="radio" class="btn-check" name="btnradio" id="btn-all" autocomplete="off" value="all">
                    <label class="btn btn-outline-info btn-sm" for="btn-all">ข้อมูลทั้งหมด</label>
                </div>
            </div>
             <div class="row g-4 mb-4"> 
                <div class="col-lg-6">
                    <h3 class="h6 text-white-50">แนวโน้มความกดอากาศ</h3>
                    <canvas id="myPressureChart"></canvas>
                </div>
                <div class="col-lg-6">
                    <h3 class="h6 text-white-50">แนวโน้มแสง</h3>
                    <canvas id="myLightChart"></canvas>
                </div>
            </div>
            <div class="row">
                <div class="col-12"> 
                    <h3 class="h6 text-white-50">อุณหภูมิและความชื้น</h3>
                    <canvas id="myWeatherChart"></canvas>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
<script>
        // ✨ 1. GLOBAL VARS: เพิ่มตัวแปรเก็บ range และ 3 กราฟ
        let tempHumChart, lightChart, pressureChart;
        let currentChartRange = '7'; // ค่าเริ่มต้น '7'
        
        const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit',second: '2-digit'}

        // --- (ฟังก์ชันนี้เหมือนเดิม) ---
        function updateSensorData(data) {
            document.getElementById('temp-value').textContent = parseFloat(data.temperature).toFixed(1);
            document.getElementById('hum-value').textContent = parseInt(data.humidity);
            document.getElementById('lux-value').textContent = parseInt(data.lux);
            document.getElementById('lvl-value').textContent = parseInt(data.light_level);
            document.getElementById('last-updated').textContent = new Date(data.timestamp).toLocaleString('th-TH', options);
            document.getElementById('hum-gauge').style.setProperty('--value', data.humidity + '%');
            document.getElementById('lvl-gauge').style.setProperty('--value', data.light_level + '%');
            let luxPercent = Math.min((data.lux / 2000) * 100, 100);
            document.getElementById('lux-gauge').style.setProperty('--value', luxPercent + '%');
            document.getElementById('pressure-value').textContent = parseFloat(data.pressure).toFixed(1);
            let pressurePercent = Math.max(0, Math.min(100, ((parseFloat(data.pressure) - 980) / 50) * 100));
            document.getElementById('pressure-gauge').style.setProperty('--value', pressurePercent + '%');
        }

        // --- (ฟังก์ชันนี้เหมือนเดิม) ---
        async function fetchData() {
            try {
                const response = await fetch('get_data.php');
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const result = await response.json();
                if (result.status === 'success') {
                    updateSensorData(result.data);
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error("Could not fetch sensor data:", error);
            }
        }

        // ✨ 2. (JS) แยกฟังก์ชัน: ฟังก์ชันนี้สำหรับ "ตาราง" เท่านั้น
        async function fetchAndDisplayTableData() {
            try {
                const response = await fetch('get_saved_data.php'); 
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const result = await response.json();

                const tableBody = document.getElementById('saved-data-table-body');
                const summaryFooter = document.getElementById('summary-footer'); // ดึง tfoot
                
                tableBody.innerHTML = ''; // ล้างข้อมูลเก่าใน tbody
                summaryFooter.innerHTML = ''; // ล้างข้อมูลเก่าใน tfoot

                if (result.status === 'success') {
                    if (result.data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-white-50">ยังไม่มีข้อมูลที่บันทึกไว้</td></tr>';
                        return;
                    }

                    // --- ✨ [ส่วนคำนวณค่าเฉลี่ย เริ่มต้น] ✨ ---
                    let totalTemp = 0, countTemp = 0;
                    let totalHum = 0, countHum = 0;
                    let totalPres = 0, countPres = 0;
                    let totalLvl = 0, countLvl = 0;
                    let totalLux = 0, countLux = 0;
                    // --- จบส่วนประกาศตัวแปร ---

                    result.data.forEach(record => {
                        const row = document.createElement('tr');
                        const formattedTimestamp = new Date(record.datetime).toLocaleString('th-TH', options);
                        
                        row.innerHTML = `
                            <td data-label="สถานที่">${record.location}</td>
                            <td data-label="เวลาที่บันทึก">${formattedTimestamp}</td>
                            <td data-label="อุณหภูมิ (°C)">${parseFloat(record.temp).toFixed(1)}</td>
                            <td data-label="ความชื้น (%)">${parseInt(record.hum)}</td>
                            <td data-label="ความกดอากาศ (hPa)">${parseFloat(record.pres).toFixed(1)}</td> 
                            <td data-label="ระดับแสง (%)">${parseInt(record.lvl)}</td>
                            <td data-label="ความสว่าง (Lux)">${parseInt(record.lux)}</td>
                        `;
                        tableBody.appendChild(row);

                        // --- ✨ [ส่วนคำนวณค่าเฉลี่ย สะสมค่า] ✨ ---
                        // เราจะเช็ค isNaN (Is Not a Number) เพื่อข้ามค่าที่อาจเป็น "NaN" (เช่น Pressure)
                        
                        const temp = parseFloat(record.temp);
                        if (!isNaN(temp)) { totalTemp += temp; countTemp++; }

                        const hum = parseInt(record.hum);
                        if (!isNaN(hum)) { totalHum += hum; countHum++; }

                        const pres = parseFloat(record.pres);
                        if (!isNaN(pres)) { totalPres += pres; countPres++; }

                        const lvl = parseInt(record.lvl);
                        if (!isNaN(lvl)) { totalLvl += lvl; countLvl++; }
                        
                        const lux = parseInt(record.lux);
                        if (!isNaN(lux)) { totalLux += lux; countLux++; }
                        // --- จบส่วนสะสมค่า ---
                    });

                    // --- ✨ [ส่วนคำนวณค่าเฉลี่ย สร้างแถวสรุป] ✨ ---
                    const summaryRow = document.createElement('tr');
                    
                    // คำนวณค่าเฉลี่ย (ใช้ count ถ้าเป็น 0 ให้แสดง '--' เพื่อกันหารด้วย 0)
                    const avgTemp = (countTemp > 0) ? (totalTemp / countTemp).toFixed(1) : '--';
                    const avgHum = (countHum > 0) ? (totalHum / countHum).toFixed(0) : '--'; // ความชื้นเอาเลขกลมๆ
                    const avgPres = (countPres > 0) ? (totalPres / countPres).toFixed(1) : '--';
                    const avgLvl = (countLvl > 0) ? (totalLvl / countLvl).toFixed(0) : '--';
                    const avgLux = (countLux > 0) ? (totalLux / countLux).toFixed(0) : '--';

                    summaryRow.innerHTML = `
                        <td data-label="สรุป" colspan="2" class="text-center" style="color: #0dcaf0; font-weight: bold;">ค่าเฉลี่ย 7 วัน</td>
                        <td data-label="อุณหภูมิเฉลี่ย">${avgTemp}</td>
                        <td data-label="ความชื้นเฉลี่ย">${avgHum}</td>
                        <td data-label="ความกดอากาศเฉลี่ย">${avgPres}</td>
                        <td data-label="ระดับแสงเฉลี่ย">${avgLvl}</td>
                        <td data-label="ความสว่างเฉลี่ย">${avgLux}</td>
                    `;
                    summaryFooter.appendChild(summaryRow); // เพิ่มแถวสรุปไปที่ tfoot
                    // --- จบส่วนสร้างแถวสรุป ---

                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error("Could not fetch table data:", error);
                const tableBody = document.getElementById('saved-data-table-body');
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data.</td></tr>';
            }
        }
        // ✨ 3. (JS) ฟังก์ชันใหม่: สำหรับ "กราฟ" เท่านั้น
        async function fetchAndDisplayChartData() {
            try {
                const response = await fetch(`get_chart_data.php?range=${currentChartRange}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const result = await response.json();

                if (result.status === 'success') {
                    // ทำลาย Chart เก่า (ถ้ามี)
                    if (tempHumChart) tempHumChart.destroy();
                    if (lightChart) lightChart.destroy();
                    if (pressureChart) pressureChart.destroy();

                    if (result.data.length === 0) {
                        return;
                    }
                    
                    // --- 🚀 ส่วนสร้าง/อัปเดต Chart.js เริ่มต้นที่นี่ 🚀 ---
                    
                    // (แก้ไข) ใช้ข้อมูลตรงๆ ไม่ต้อง reverse
                    const chartData = result.data; 

                    const labels = chartData.map(d => 
                        new Date(d.datetime).toLocaleString('th-TH', { 
                            month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' 
                        })
                    );
                    const tempData = chartData.map(d => parseFloat(d.temp));
                    const humidityData = chartData.map(d => parseInt(d.hum));
                    const lvlData = chartData.map(d => parseInt(d.lvl));
                    const luxData = chartData.map(d => parseInt(d.lux));
                    const presData = chartData.map(d => parseFloat(d.pres));

                    Chart.defaults.color = 'rgba(255, 255, 255, 0.7)';
                    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';
                    
                    // --- 📊 กราฟที่ 1: อุณหภูมิ (ซ้าย) และ ความชื้น (ขวา) ---
                    const ctxTempHum = document.getElementById('myWeatherChart').getContext('2d');
                    tempHumChart = new Chart(ctxTempHum, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [
                                { label: 'อุณหภูมิ (°C)', data: tempData, borderColor: 'rgb(255, 99, 132)', backgroundColor: 'rgba(255, 99, 132, 0.2)', tension: 0.2, fill: true, yAxisID: 'yTemp' },
                                { label: 'ความชื้น (%)', data: humidityData, borderColor: 'rgb(54, 162, 235)', backgroundColor: 'rgba(54, 162, 235, 0.2)', tension: 0.2, fill: true, yAxisID: 'yHum' }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: { ticks: { color: 'rgba(255, 255, 255, 0.7)' }, grid: { color: 'rgba(255, 255, 255, 0.1)' } },
                                yTemp: { type: 'linear', position: 'left', beginAtZero: false, ticks: { color: 'rgb(255, 99, 132)' } },
                                yHum: { type: 'linear', position: 'right', max: 100, ticks: { color: 'rgb(54, 162, 235)' }, grid: { display: false } }
                            },
                            plugins: { legend: { labels: { color: '#FFFFFF' } } }
                        }
                    });

                    // --- 📊 กราฟที่ 2: ระดับแสง (ซ้าย) และ ความสว่าง (ขวา) ---
                    const ctxLight = document.getElementById('myLightChart').getContext('2d');
                    lightChart = new Chart(ctxLight, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [
                                { label: 'ระดับแสง (%)', data: lvlData, borderColor: 'rgb(255, 205, 86)', backgroundColor: 'rgba(255, 205, 86, 0.2)', tension: 0.2, fill: true, yAxisID: 'yLvl' },
                                { label: 'ความสว่าง (Lux)', data: luxData, borderColor: 'rgb(201, 203, 207)', tension: 0.2, fill: false, yAxisID: 'yLux' }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: { ticks: { color: 'rgba(255, 255, 255, 0.7)' }, grid: { color: 'rgba(255, 255, 255, 0.1)' } },
                                yLvl: { type: 'linear', position: 'left', max: 100, ticks: { color: 'rgb(255, 205, 86)' } },
                                yLux: { type: 'linear', position: 'right', beginAtZero: true, ticks: { color: 'rgb(201, 203, 207)' }, grid: { display: false } }
                            },
                            plugins: { legend: { labels: { color: '#FFFFFF' } } }
                        }
                    });

                    // --- 📊 กราฟที่ 3: ความกดอากาศ (แกนเดียว) ---
                    const ctxPressure = document.getElementById('myPressureChart').getContext('2d');
                    pressureChart = new Chart(ctxPressure, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [ { label: 'ความกดอากาศ (hPa)', data: presData, borderColor: 'rgb(75, 192, 192)', backgroundColor: 'rgba(75, 192, 192, 0.2)', tension: 0.2, fill: true } ]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: { ticks: { color: 'rgba(255, 255, 255, 0.7)' }, grid: { color: 'rgba(255, 255, 255, 0.1)' } },
                                y: { beginAtZero: false, ticks: { color: 'rgb(75, 192, 192)' } }
                            },
                            plugins: { legend: { labels: { color: '#FFFFFF' } } }
                        }
                    });
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error("Could not fetch chart data:", error);
            }
        }

        // --- (โค้ดส่วน Modal เหมือนเดิม) ---
        const insertModal = document.getElementById('insertModal');
        const insertForm = document.getElementById('insert-form');
        const statusModalElement = document.getElementById('statusModal');
        const statusModal = new bootstrap.Modal(statusModalElement);
        const statusModalHeader = document.getElementById('statusModalHeader');
        const statusModalLabel = document.getElementById('statusModalLabel');
        const statusModalBody = document.getElementById('statusModalBody');


        insertModal.addEventListener('show.bs.modal', function (event) {
            const temp = document.getElementById('temp-value').textContent;
            const hum = document.getElementById('hum-value').textContent;
            const lvl = document.getElementById('lvl-value').textContent;
            const lux = document.getElementById('lux-value').textContent;
            const pres = document.getElementById('pressure-value').textContent;
            document.getElementById('modal-temp').value = temp;
            document.getElementById('modal-hum').value = hum;
            document.getElementById('modal-lvl').value = lvl;
            document.getElementById('modal-lux').value = lux;
            document.getElementById('modal-pres').value = pres;
            document.getElementById('modal-location').value = '';
        });

        insertForm.addEventListener('submit', async function(event) {
            event.preventDefault(); 
            const formData = {
                temperature: document.getElementById('modal-temp').value,
                humidity: document.getElementById('modal-hum').value,
                light_level: document.getElementById('modal-lvl').value,
                lux: document.getElementById('modal-lux').value,
                pressure: document.getElementById('modal-pres').value,
                location: document.getElementById('modal-location').value
            };
            const jsonStringData = JSON.stringify(formData);
            const mainModalInstance = bootstrap.Modal.getInstance(insertModal);

            try {
                const response = await fetch('insert.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', },
                    body: 'savedata=' + encodeURIComponent(jsonStringData)
                });
                const result = await response.json();
                mainModalInstance.hide();
                
                if (result.status === 'success') {
                    // --- 🚀 Success ---
                    statusModalHeader.classList.remove('bg-danger');
                    statusModalHeader.classList.add('bg-success');
                    statusModalLabel.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i> สำเร็จ!';
                    statusModalBody.innerHTML = result.message;
                    statusModal.show();
                    setTimeout(() => { statusModal.hide(); }, 3000);
                    
                    fetchAndDisplayTableData(); 
                    fetchAndDisplayChartData();

                } else {
                    // --- ❌ Error ---
                    statusModalHeader.classList.remove('bg-success');
                    statusModalHeader.classList.add('bg-danger');
                    statusModalLabel.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i> เกิดข้อผิดพลาด';
                    statusModalBody.innerHTML = result.message;
                    statusModal.show();
                    setTimeout(() => { statusModal.hide(); }, 3000);
                }
            } catch (error) {
                mainModalInstance.hide(); 
                statusModalHeader.classList.remove('bg-success');
                statusModalHeader.classList.add('bg-danger');
                statusModalLabel.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i> เกิดข้อผิดพลาดการเชื่อมต่อ';
                statusModalBody.innerHTML = 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้: ' + error.message;
                statusModal.show();
                setTimeout(() => { statusModal.hide(); }, 3000);
            }
        });
        
        // ✨ 4. (JS) อัปเดตการเรียกใช้ตอนโหลดหน้าและ Intervals
        fetchData(); // โหลด real-time
        fetchAndDisplayTableData(); 
        fetchAndDisplayChartData(); 

        setInterval(fetchData, 3000);
        setInterval(fetchAndDisplayTableData, 60000); 
        setInterval(fetchAndDisplayChartData, 60000); 
    document.querySelectorAll('#chart-range-selector label').forEach(label => {
        label.addEventListener('click', (event) => {
            const correspondingInput = document.getElementById(label.getAttribute('for'));
            if (correspondingInput.checked) {
                return; 
            }

            currentChartRange = correspondingInput.value;
            console.log("กำลังโหลดกราฟใหม่... Range:", currentChartRange); 
            
            fetchAndDisplayChartData(); 
        });
    });

        const geminiFab = document.getElementById('gemini-fab');
        const geminiPopup = document.getElementById('gemini-popup');
        const geminiCloseBtn = document.getElementById('gemini-close-btn');
        const geminiForm = document.getElementById('gemini-form');
        const geminiPrompt = document.getElementById('gemini-prompt');
        const geminiResponseArea = document.getElementById('gemini-response-area');
        const geminiBtnText = document.getElementById('gemini-btn-text');
        const geminiSpinner = document.getElementById('gemini-spinner');
        
        geminiFab.addEventListener('click', () => {
            geminiPopup.classList.toggle('show');
        });
        geminiCloseBtn.addEventListener('click', () => {
            geminiPopup.classList.remove('show');
        });
        geminiForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const userPrompt = geminiPrompt.value.trim();
            if (!userPrompt) return;
            
            geminiResponseArea.innerHTML = '<p class="text-light small">กำลังรวบรวมข้อมูลและคิด...</p>';
            geminiSpinner.style.display = 'inline-block';
            geminiBtnText.textContent = 'รอสักครู่';
            geminiForm.querySelector('button').disabled = true;

            let historicalDataContext = "";

            const contextMode = document.querySelector('input[name="gemini-context"]:checked').value;

            if (contextMode === '7') {
                console.log("Gemini Context: Using 7 saved records (SensorForecast_Saved)");
                historicalDataContext = "ข้อมูลย้อนหลัง 7 ครั้งล่าสุดจากตาราง (SensorForecast_Saved):\n";
                const tableRows = document.querySelectorAll('#saved-data-table-body tr');
                tableRows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length > 1) {
                        let location = cells[0].textContent;
                        let time = cells[1].textContent;
                        let temp = cells[2].textContent;
                        let humidity = cells[3].textContent;
                        let pressure = cells[4].textContent;
                        let lightLevel = cells[5].textContent; 
                        let lux = cells[6].textContent;
                        historicalDataContext += `- ที่: ${location}, เวลา: ${time}, อุณหภูมิ: ${temp}°C, ความชื้น: ${humidity}%, ความกดอากาศ: ${pressure} hPa, ระดับแสง: ${lightLevel}%, ความสว่าง: ${lux} Lux\n`;
                    }
                });
                historicalDataContext += "---\n";

            } else {
                console.log("Gemini Context: Fetching all records (SensorForecast)");
                historicalDataContext = "ข้อมูลย้อนหลังทั้งหมดจากฐานข้อมูล (SensorForecast):\n";
                geminiResponseArea.innerHTML = '<p class="text-light small">กำลังดึงข้อมูลทั้งหมด (SensorForecast) มาประกอบการวิเคราะห์...</p>';

                try {
                    const response = await fetch('get_chart_data.php?range=all'); 
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    
                    const result = await response.json();
                    
                    if (result.status === 'success' && result.data.length > 0) {
                        const allData = result.data;
                        let dataToSend;

                        if (allData.length > 500) {
                            dataToSend = allData.slice(-500);
                            historicalDataContext += `(พบข้อมูลทั้งหมด ${allData.length} records, จะใช้เฉพาะ 500 records ล่าสุดในการวิเคราะห์)\n`;
                            console.log(`Gemini Context: Data exceeds 500, slicing to last 500 records.`);
                        } else {
                            // ถ้าไม่เกิน 500, ก็ใช้ทั้งหมด
                            dataToSend = allData;
                            historicalDataContext += `(พบข้อมูลทั้งหมด ${allData.length} records, จะใช้ทั้งหมดในการวิเคราะห์)\n`;
                            console.log(`Gemini Context: Data is ${allData.length} records, using all.`);
                        }

                        historicalDataContext += "(ข้อมูลมีดังนี้:)\n";
                        
                        dataToSend.forEach(d => {
                            historicalDataContext += `- ${new Date(d.datetime).toLocaleString('th-TH')}, Temp: ${d.temp}°C, Hum: ${d.hum}%, Pres: ${d.pres} hPa, Lux: ${d.lux} Lux\n`;
                        });
                        
                    } else {
                        historicalDataContext += "(ไม่พบข้อมูลทั้งหมด)\n";
                    }
                } catch (error) {
                    console.error("Error fetching all data for Gemini:", error);
                    historicalDataContext += "(เกิดข้อผิดพลาดในการดึงข้อมูลทั้งหมด)\n";
                }
                historicalDataContext += "---\n";
                geminiResponseArea.innerHTML = '<p class="text-light small">ข้อมูลพร้อมแล้ว กำลังส่งไปให้ Gemini คิด...</p>';
            }
            
            const latestTemp = document.getElementById('temp-value').textContent;
            const latestHumidity = document.getElementById('hum-value').textContent;
            const latestPressure = document.getElementById('pressure-value').textContent;
            const latestLightLevel = document.getElementById('lvl-value').textContent;
            const latestLux = document.getElementById('lux-value').textContent;
            const latestDataContext = `
            ข้อมูลสภาพอากาศล่าสุด ณ ปัจจุบัน:
            - อุณหภูมิ: ${latestTemp}°C, ความชื้น: ${latestHumidity}%, ความกดอากาศ: ${latestPressure} hPa, ระดับแสง: ${latestLightLevel}%, ความสว่าง: ${latestLux} Lux
            ---
            `;
            
            const finalPrompt = historicalDataContext + latestDataContext + "จากข้อมูลทั้งหมดด้านบน ช่วยตอบคำถามนี้หน่อย: " + userPrompt;
            // console.log(finalPrompt); // (สำหรับ Debug)
            try {
                const response = await fetch('gemini_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ prompt: finalPrompt })
                });
                const result = await response.json();
                if (result.status === 'success') {
                    geminiResponseArea.textContent = result.answer;
                } else {
                    geminiResponseArea.textContent = 'เกิดข้อผิดพลาด: ' + result.message;
                }
            } catch (error) {
                console.error('Error calling Gemini API:', error);
                geminiResponseArea.textContent = 'เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองใหม่อีกครั้ง';
            } 
            finally {
                geminiSpinner.style.display = 'none';
                geminiBtnText.textContent = 'ส่งคำถาม';
                geminiForm.querySelector('button').disabled = false;
            }
        });
    </script>
</body>
</html>
