<?php
// ========== HEADER SETTINGS (ADDED) ==========
    // อนุญาตให้มีการเรียกใช้งานจากทุกโดเมน (สำหรับการทดสอบ)
    header("Access-Control-Allow-Origin: *");
    // กำหนดว่า Content Type คือ JSON
    header("Content-Type: application/json; charset=utf-8");


    // 1. ============== CONFIGURATION ==============
    $apiKey = 'AIzaSyBtkOgvvmhgRRIcR368FGYPy2D1fc5QeMo'; // กรุณาใช้ Key ของคุณ
    // (CRITICAL FIX) แนะนำให้ใช้ 1.5 Pro เพื่อการวิเคราะห์ที่ดีที่สุด
    //$model = 'gemini-1.5-pro-latest';
    $model = 'gemini-2.5-pro';


    // 2. ============== DATA PREPARATION (CHANGED) ==============
    // รับข้อมูล JSON ที่ถูกส่งมาจากฝั่ง Client (JavaScript)
    $energyDataJson = file_get_contents('php://input');

    // (ADDED) ตรวจสอบว่าได้รับข้อมูลหรือไม่
    if (empty($energyDataJson)) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'No JSON data received.']);
        exit;
    }

    // 3. ============== PROMPT CONSTRUCTION ==============
    // Prompt นี้รองรับข้อมูลครบถ้วน รวมถึง Hz และ Power Factor
    $promptTemplate = <<<PROMPT
    คุณคือผู้เชี่ยวชาญด้านการวิเคราะห์ข้อมูลพลังงานไฟฟ้าสำหรับโรงงานอุตสาหกรรม
    จากข้อมูล Time-Series ในรูปแบบ JSON ที่แนบมานี้ โปรดทำการวิเคราะห์และสรุปประเด็นต่อไปนี้อย่างละเอียด:

    1.  ภาพรวมการใช้พลังงาน
    2.  วิเคราะห์ความผิดปกติหลัก (กระแสไฟฟ้าไม่สมดุล)
    3.  วิเคราะห์แรงดันไฟฟ้า (แรงดันตก)
    4.  วิเคราะห์ความถี่ไฟฟ้า (Hz)
    5.  วิเคราะห์ตัวประกอบกำลังไฟฟ้า (Power Factor)
    6.  สรุปและให้ข้อเสนอแนะที่นำไปปฏิบัติได้จริง

    โปรดนำเสนอผลการวิเคราะห์ในรูปแบบรายงานที่ชัดเจนและเข้าใจง่าย
    --- BEGIN DATA ---
    %s
    --- END DATA ---
    PROMPT;

    // รวม Prompt กับข้อมูล JSON ที่ได้รับมา
    $fullPrompt = sprintf($promptTemplate, $energyDataJson);

    // 4. ============== API CALL (cURL) ==============
    // (CRITICAL FIX) แก้ไข URL ให้ถูกต้องและยืดหยุ่นตามตัวแปร $model
    $url = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$apiKey}";

    $requestBody = json_encode(['contents' => [['parts' => [['text' => $fullPrompt]]]]]);


    // ตั้งค่า cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);
    $responseJson = curl_exec($ch);

    if (curl_errno($ch)) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'cURL Error: ' . curl_error($ch)]);
        exit;
    }

    curl_close($ch);

    // 5. ============== PROCESS RESPONSE ==============
    $responseObject = json_decode($responseJson);

    if (isset($responseObject->candidates[0]->content->parts[0]->text)) {
        $analysisText = $responseObject->candidates[0]->content->parts[0]->text;
        // ส่งผลลัพธ์กลับไปให้ Client เป็น JSON
        echo json_encode(['analysis' => $analysisText]);
    } else {
        http_response_code(502); // Bad Gateway
        // ส่ง Raw Response ที่ผิดพลาดกลับไปให้ Client เพื่อดีบัก
        echo json_encode(['error' => 'Could not extract analysis from the API response.', 'raw_response' => json_decode($responseJson)]);
    }

?>