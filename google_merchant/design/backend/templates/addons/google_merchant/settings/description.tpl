{if fn_allowed_for('ULTIMATE') && !$runtime.company_id || $runtime.simple_ultimate || fn_allowed_for('MULTIVENDOR')}
    <p>เมื่อติดตั้ง google_merchant addon ตัวนี้</p>
    <p>ตรวจสอบว่า มีไฟล์ service-account.json,merchant-info.json,manufacturer-info.json หรือไม่ ตามขั้นตอนต่อไปนี้</p>
    <p>&nbsp&nbsp   1 ชี้ที่แถบเมนู <b>การบริหาร</b></p>
    <p>&nbsp&nbsp   2 ไปที่ <b>ไฟล์</b></p>
    <p>&nbsp&nbsp   3 สังเกตุแถบด้านซ้าย แล้วคลิ๊ก <b>ไฟล์ส่วนตัว</b></p>
    <p>&nbsp&nbsp   4 คลิ๊ก <b>google_merchant</b></p>
    <p>&nbsp&nbsp   5 คลิ๊ก <b>content</b> แล้วตรวจสอบดูว่ามีไฟล์ดังกล่าวข้างต้นหรือไม่</p>
    <br/>
    <p>ถ้ายังไม่มีไฟล์ดังกล่าวข้างต้น ให้ทำตามขั้นตอนต่อไปนี้</p>
    <p>1.เตรียมไฟล์ service-account.json</p>
    <p>โดยการเตรียม service-account.json ทำตาม ลิงก์ นี้ <a href="https://developers.google.com/shopping-content/v2/quickstart" target="_blank">click</a></p>
    <p>2.การเตรียมไฟล์  merchant-info.json,manufacturer-info.json โหลดได้จาก ลิงก์นี้ <a href="https://github.com/googleads/googleads-shopping-samples" target="_blank">click</a>
    <p>โดยการตั้งค่า ข้างในของ manufacturer-info.json ทำตามต่อไปนี้</p>
    <p>&nbsp&nbsp 2.1. "manufacturerId" ค่า ของ key ตัวนี้ คือ เอาข้อมูลจาก service-account.json ซึ่งวิธีการ คือ</p>
    <p>&nbsp&nbsp    -เปิด service-account.json สังเกตุ "project_id": "merchant-center-<b>xxxxxxxxxx</b>" เอาเลขที่เป็นตัวหนาไปใส่เป็นค่า ใน key manufacturerId ใน  manufacturer-info.json</p>
    <p>&nbsp&nbsp 2.2. "websiteUrl"  ของ key ตัวนี้ คือ </p>
    <p>&nbsp&nbsp    - เปิด service-account.json สังเกตุ "auth_uri" : <b>"https://xxxxxxxxx"</b> เอาตัวหนังสือ่เป็นตัวหนาไปใส่เป็นค่า ใน key websiteUrl ใน  manufacturer-info.json</p>
    <p>3.หลังจากเตรียมไฟล์ ดังกล่าวครบหมดแล้ว ให้ทำตามขั้นตอนตามข้อ 4</p>
    <p>4.โดยทำตามต่อไปนี้
    <p>&nbsp&nbsp   4.1 ชี้ที่แถบเมนู <b>การบริหาร</b></p>
    <p>&nbsp&nbsp   4.2 ไปที่ <b>ไฟล์</b></p>
    <p>&nbsp&nbsp   4.3 สังเกตุแถบด้านซ้าย แล้วคลิ๊ก <b>ไฟล์ส่วนตัว</b></p>
    <p>&nbsp&nbsp   4.4 คลิ๊ก <b>google_merchant</b></p>
    <p>&nbsp&nbsp   4.5 คลิ๊ก <b>content</b> แล้วเอาไฟล์ที่เตรียมไว้ในข้อ 1,2,3 ไปไว้</p>
{/if}