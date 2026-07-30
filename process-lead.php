<?php
// Ankabit Farm Lead Processor in PHP
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true) ?: $_POST;

$action = isset($_GET['action']) ? $_GET['action'] : (isset($data['action']) ? $data['action'] : 'step1');

// Function to read saved leads
function getSavedLeads() {
    $filePath = LEADS_FILE_PATH;
    if (!file_exists($filePath)) {
        return array();
    }
    $content = file_get_contents($filePath);
    return json_decode($content, true) ?: array();
}

// Function to save leads array to JSON file
function saveLeadsFile($leads) {
    $filePath = LEADS_FILE_PATH;
    $dir = dirname($filePath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($filePath, json_encode($leads, JSON_PRETTY_PRINT));
}

// ----------------------------------------------------
// STEP 1: CONTACT CAPTCHA CAPTURE
// ----------------------------------------------------
if ($action === 'step1') {
    $fullName = trim(isset($data['fullName']) ? $data['fullName'] : '');
    $email = trim(isset($data['email']) ? $data['email'] : '');
    $phone = trim(isset($data['phone']) ? $data['phone'] : '');
    $captchaId = trim(isset($data['captchaId']) ? $data['captchaId'] : '');
    $captchaAnswer = trim(isset($data['captchaAnswer']) ? $data['captchaAnswer'] : '');

    if (empty($fullName) || empty($email) || empty($phone) || empty($captchaAnswer)) {
        echo json_encode(array('success' => false, 'message' => 'Please fill in all required fields including captcha.'));
        exit;
    }

    // Verify Captcha Answer
    $expectedAnswer = null;
    if ($captchaId) {
        $captchaFile = __DIR__ . '/data/captchas/' . $captchaId . '.json';
        if (file_exists($captchaFile)) {
            $capData = json_decode(file_get_contents($captchaFile), true);
            $expectedAnswer = $capData['answer'];
        }
    }
    if ($expectedAnswer === null && isset($_SESSION['captcha_answer'])) {
        $expectedAnswer = $_SESSION['captcha_answer'];
    }

    if ($expectedAnswer !== null && (int)$captchaAnswer !== (int)$expectedAnswer) {
        echo json_encode(array('success' => false, 'message' => 'Security captcha answer is incorrect. Please try again.'));
        exit;
    }

    // Generate Lead ID
    $leadId = 'ABF-' . strtoupper(substr(md5(uniqid()), 0, 8)) . '-' . rand(100, 999);
    $timestamp = date('c');

    $newLead = array(
        'id' => $leadId,
        'fullName' => $fullName,
        'email' => $email,
        'phone' => $phone,
        'createdAt' => $timestamp,
        'updatedAt' => $timestamp,
        'step1Completed' => true,
        'step2Completed' => false,
        'quantityCrates' => null,
        'deliveryState' => null,
        'deliveryLGA' => null,
        'notes' => null,
        'status' => 'STEP1_CONTACT_CAPTURED'
    );

    $leads = getSavedLeads();
    $leads[] = $newLead;
    saveLeadsFile($leads);

    // Send Alert Email to Admin (olaomansur@gmail.com) via Resend API
    $adminSubject = "⚡ NEW LEAD STEP 1: $fullName ($leadId)";
    $adminBody = "<h2>New Lead Contact Captured</h2>
                  <p><strong>Name:</strong> " . htmlspecialchars($fullName) . "</p>
                  <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                  <p><strong>Phone (WhatsApp):</strong> " . htmlspecialchars($phone) . "</p>
                  <p><strong>Reference ID:</strong> $leadId</p>
                  <p><em>The customer is currently selecting their quantity and delivery location.</em></p>";
    sendResendEmail(ADMIN_EMAIL, $adminSubject, $adminBody);

    // Send Confirmation Email to Buyer
    $buyerSubject = "Ankabit Farm Wholesale Inquiry Received ($leadId)";
    $buyerBody = "<h2>Thank you for contacting Ankabit Farm</h2>
                  <p>Dear " . htmlspecialchars($fullName) . ",</p>
                  <p>We have recorded your initial contact details under reference <strong>$leadId</strong>.</p>
                  <p>Minimum order quantity is <strong>100 crates (3,000 eggs)</strong>. Our trade desk will contact your phone (<strong>" . htmlspecialchars($phone) . "</strong>) shortly.</p>
                  <br><p>Best regards,<br><strong>Ankabit Farm Sales Desk</strong></p>";
    sendResendEmail($email, $buyerSubject, $buyerBody);

    echo json_encode(array(
        'success' => true,
        'leadId' => $leadId,
        'message' => 'Initial contact details successfully captured! Proceed to select quantity.'
    ));
    exit;
}

// ----------------------------------------------------
// STEP 2: WHOLESALE ORDER SPECS (MIN 100 CRATES)
// ----------------------------------------------------
if ($action === 'step2') {
    $leadId = trim(isset($data['leadId']) ? $data['leadId'] : '');
    $quantityCrates = (int)(isset($data['quantityCrates']) ? $data['quantityCrates'] : 0);
    $deliveryState = trim(isset($data['deliveryState']) ? $data['deliveryState'] : '');
    $deliveryLGA = trim(isset($data['deliveryLGA']) ? $data['deliveryLGA'] : '');
    $notes = trim(isset($data['notes']) ? $data['notes'] : '');

    if ($quantityCrates < MIN_WHOLESALE_CRATES) {
        echo json_encode(array(
            'success' => false,
            'message' => 'Minimum wholesale order is ' . MIN_WHOLESALE_CRATES . ' crates. Please enter 100 or more crates.'
        ));
        exit;
    }

    $leads = getSavedLeads();
    $targetIndex = -1;

    if (!empty($leadId)) {
        foreach ($leads as $idx => $ld) {
            if ($ld['id'] === $leadId) {
                $targetIndex = $idx;
                break;
            }
        }
    }

    if ($targetIndex === -1 && count($leads) > 0) {
        $targetIndex = count($leads) - 1; // Fallback to last saved lead
    }

    if ($targetIndex !== -1) {
        $leads[$targetIndex]['quantityCrates'] = $quantityCrates;
        $leads[$targetIndex]['deliveryState'] = $deliveryState;
        $leads[$targetIndex]['deliveryLGA'] = $deliveryLGA;
        $leads[$targetIndex]['notes'] = $notes;
        $leads[$targetIndex]['step2Completed'] = true;
        $leads[$targetIndex]['updatedAt'] = date('c');
        $leads[$targetIndex]['status'] = 'WHOLESALE_ORDER_COMPLETED';

        $updatedLead = $leads[$targetIndex];
        saveLeadsFile($leads);

        $eggCount = number_format($quantityCrates * 30);
        $weightKg = number_format($quantityCrates * 2);

        // Send Complete Order Email to Admin (olaomansur@gmail.com) via Resend API
        $adminSubject = "📦 WHOLESALE ORDER COMPLETE: {$updatedLead['fullName']} ($quantityCrates Crates)";
        $adminBody = "<h2>Wholesale Egg Order Request Received</h2>
                      <p><strong>Lead ID:</strong> {$updatedLead['id']}</p>
                      <p><strong>Customer Name:</strong> " . htmlspecialchars($updatedLead['fullName']) . "</p>
                      <p><strong>Email:</strong> " . htmlspecialchars($updatedLead['email']) . "</p>
                      <p><strong>Phone:</strong> " . htmlspecialchars($updatedLead['phone']) . "</p>
                      <p><strong>Order Quantity:</strong> $quantityCrates Crates ($eggCount Eggs, ~$weightKg kg)</p>
                      <p><strong>Destination:</strong> " . htmlspecialchars($deliveryState) . " (" . htmlspecialchars($deliveryLGA) . " LGA)</p>
                      <p><strong>Logistics Notes:</strong> " . htmlspecialchars($notes) . "</p>";
        sendResendEmail(ADMIN_EMAIL, $adminSubject, $adminBody);

        // Send Order Receipt Email to Buyer
        $buyerSubject = "Ankabit Farm Order Specification Receipt ({$updatedLead['id']})";
        $buyerBody = "<h2>Order Specification Received</h2>
                      <p>Dear " . htmlspecialchars($updatedLead['fullName']) . ",</p>
                      <p>Your wholesale order inquiry for <strong>$quantityCrates Crates ($eggCount eggs)</strong> to <strong>" . htmlspecialchars($deliveryState) . "</strong> has been logged under ID <strong>{$updatedLead['id']}</strong>.</p>
                      <p>Our truck dispatch coordinator will call you directly at <strong>" . htmlspecialchars($updatedLead['phone']) . "</strong> to confirm offloading schedule.</p>
                      <br><p>Best regards,<br><strong>Ankabit Farm Dispatch Team</strong></p>";
        sendResendEmail($updatedLead['email'], $buyerSubject, $buyerBody);

        echo json_encode(array(
            'success' => true,
            'lead' => $updatedLead,
            'message' => 'Thank you! Your bulk egg order request has been received. Our dispatch team will call you shortly.'
        ));
        exit;
    } else {
        echo json_encode(array('success' => false, 'message' => 'Lead reference record not found. Please restart form.'));
        exit;
    }
}
?>
