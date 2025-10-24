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
    </div> </div> <div class="container my-4">
    <div class="weather-section">
        <h2 style="color: #0dcaf0;" class="h5 mb-3">ข้อมูลที่บันทึก 7 ครั้งล่าสุด</h2>
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
                        <input type="text" class="form-control" id="modal-temp" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="modal-hum" class="form-label" style="color: #0dcaf0;" >Humidity (%)</label>
                        <input type="text" class="form-control" id="modal-hum" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="modal-lvl" class="form-label" style="color: #0dcaf0;" >Light Level (%)</label>
                        <input type="text" class="form-control" id="modal-lvl" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="modal-lux" class="form-label" style="color: #0dcaf0;" >Illuminance (Lux)</label>
                        <input type="text" class="form-control" id="modal-lux" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="modal-pres" class="form-label" style="color: #0dcaf0;" >Pressure (hPa)</label>
                        <input type="text" class="form-control" id="modal-pres" readonly>
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
<div><h1 style="color: white;"> ตกแต่งหน้าเว็ป graph dashboard <br> respondsive มือถือ</h1></div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ดึงค่าปัจจุบัน
        const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit',second: '2-digit'}
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

        async function fetchAndDisplaySavedData() {
            try {
                const response = await fetch('get_saved_data.php');
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const result = await response.json();

                if (result.status === 'success') {
                    const tableBody = document.getElementById('saved-data-table-body');
                    tableBody.innerHTML = '';

                    if (result.data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-white-50">ยังไม่มีข้อมูลที่บันทึกไว้</td></tr>';
                        return;
                    }

                    result.data.forEach(record => {
                        const row = document.createElement('tr');
                        
                        const formattedTimestamp = new Date(record.datetime).toLocaleString('th-TH', options);

                    row.innerHTML = `
                        <td>${record.location}</td>
                        <td>${formattedTimestamp}</td>
                        <td>${parseFloat(record.temp).toFixed(1)}</td>
                        <td>${parseInt(record.hum)}</td>
                        <td>${parseFloat(record.pres).toFixed(1)}</td> 
                        <td>${parseInt(record.lvl)}</td>
                        <td>${parseInt(record.lux)}</td>
                    `;
                        tableBody.appendChild(row);
                    });
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error("Could not fetch saved data:", error);
                const tableBody = document.getElementById('saved-data-table-body');
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data.</td></tr>';
            }
        }

        const insertModal = document.getElementById('insertModal');
        const insertForm = document.getElementById('insert-form');

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

            try {
                const response = await fetch('insert.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', },
                    body: 'savedata=' + encodeURIComponent(jsonStringData)
                });
                const result = await response.json();
                
                if (result.status === 'success') {
                    alert('Success: ' + result.message);
                    const modal = bootstrap.Modal.getInstance(insertModal);
                    modal.hide();
                    fetchAndDisplaySavedData(); 
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('An error occurred. Please check the console for details.');
            }
        });
        
        fetchData();
        fetchAndDisplaySavedData();

        setInterval(fetchData, 2000);
        setInterval(fetchAndDisplaySavedData, 5000);

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

    // --- 🤖 ส่วนดึงข้อมูลจากตาราง (แก้ไขแล้ว) 🤖 ---
    let historicalDataContext = "ข้อมูลย้อนหลัง 7 ครั้งล่าสุดจากตาราง:\n";
    const tableRows = document.querySelectorAll('#saved-data-table-body tr');

    tableRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length > 1) {
            let location = cells[0].textContent;
            let time = cells[1].textContent;
            let temp = cells[2].textContent;
            let humidity = cells[3].textContent;
            let pressure = cells[4].textContent;
            // (แก้ไข) เข้าถึงเซลล์ตามลำดับที่ถูกต้อง
            let lightLevel = cells[5].textContent; 
            let lux = cells[6].textContent;
            
            historicalDataContext += `- ที่: ${location}, เวลา: ${time}, อุณหภูมิ: ${temp}°C, ความชื้น: ${humidity}%, ความกดอากาศ: ${pressure} hPa, ระดับแสง: ${lightLevel}%, ความสว่าง: ${lux} Lux\n`;
        }
    });
    historicalDataContext += "---\n";
    

    // --- ส่วนดึงข้อมูลล่าสุด ---
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
    console.log(finalPrompt)
    // --- ส่วนส่งข้อมูล ---
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