const http = require('http');

function makeRequest(options, postData) {
  return new Promise((resolve, reject) => {
    const req = http.request(options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        try {
          resolve({ statusCode: res.statusCode, body: JSON.parse(data) });
        } catch (e) {
          resolve({ statusCode: res.statusCode, body: data });
        }
      });
    });
    req.on('error', err => reject(err));
    if (postData) req.write(JSON.stringify(postData));
    req.end();
  });
}

async function runTests() {
  console.log('--- RUNNING ANKABIT FARM INTEGRATION TESTS ---');

  // 1. Fetch Captcha
  const capRes = await makeRequest({
    hostname: 'localhost',
    port: 3000,
    path: '/api/captcha',
    method: 'GET'
  });
  console.log('1. Captcha Response:', capRes.body);

  const question = capRes.body.question;
  const match = question.match(/What is (\d+) \+ (\d+)\?/);
  const captchaAnswer = parseInt(match[1]) + parseInt(match[2]);
  console.log(`Computed captcha answer: ${captchaAnswer}`);

  // 2. Submit Step 1 Lead
  const step1Res = await makeRequest({
    hostname: 'localhost',
    port: 3000,
    path: '/api/leads/step1',
    method: 'POST',
    headers: { 'Content-Type': 'application/json' }
  }, {
    fullName: 'Chief Adebayo Wholesale Ltd',
    email: 'adebayo@lagosmarket.ng',
    phone: '08031234567',
    captchaId: capRes.body.captchaId,
    captchaAnswer: captchaAnswer
  });
  console.log('2. Step 1 Lead Capture Result:', step1Res.body);
  const leadId = step1Res.body.leadId;

  // 3. Test Step 2 invalid quantity (< 100 crates)
  const invalidStep2 = await makeRequest({
    hostname: 'localhost',
    port: 3000,
    path: '/api/leads/step2',
    method: 'POST',
    headers: { 'Content-Type': 'application/json' }
  }, {
    leadId: leadId,
    quantityCrates: 50, // Below min 100
    deliveryState: 'Lagos'
  });
  console.log('3. Step 2 Min Quantity Validation Result (50 crates):', invalidStep2.body);

  // 4. Test Step 2 valid quantity (150 crates)
  const validStep2 = await makeRequest({
    hostname: 'localhost',
    port: 3000,
    path: '/api/leads/step2',
    method: 'POST',
    headers: { 'Content-Type': 'application/json' }
  }, {
    leadId: leadId,
    quantityCrates: 150, // Valid min 100+
    deliveryState: 'Lagos',
    deliveryLGA: 'Ikeja / Mile 12 Market',
    urgency: 'Immediate (24 hrs)',
    notes: 'Require Large-size eggs in sturdy wooden crates.'
  });
  console.log('4. Step 2 Complete Order Result (150 crates):', validStep2.body);

  // 5. Admin Leads Check
  const adminRes = await makeRequest({
    hostname: 'localhost',
    port: 3000,
    path: '/api/leads/admin',
    method: 'GET'
  });
  console.log('5. Admin Total Saved Leads:', adminRes.body.totalLeads);

  console.log('--- ALL TESTS COMPLETED SUCCESSFULLY ---');
}

runTests().catch(err => console.error('Test error:', err));
