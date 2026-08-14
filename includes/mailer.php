<?php
/**
 * mailer.php
 * -----------
 * Order confirmation + order status update emails ahi thi jay che.
 * Ahi niche apela SMTP settings potana email na hisab e badlo.
 */

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ============================================================
// 🔧 SMTP SETTINGS — AA VALUES POTANA MUJAB BADLO
// ============================================================
// Gmail vaparta ho to:
// 1) Potana Google Account ma jaine 2-Step Verification ON karo
// 2) https://myaccount.google.com/apppasswords par jaine "App Password" banavo
// 3) Te 16 digit password niche MAIL_PASSWORD ma nakho (normal gmail password nahi chalse)
define('MAIL_HOST', 'smtp.gmail.com');
// <-- Gmail App Password
define('MAIL_PORT', 587);
define('MAIL_FROM_NAME', 'KD Dairy Farm');

/**
 * Order confirmation email moklo (jyare user order place kare tyare).
 *
 * @param string $toEmail   User nu email
 * @param string $toName    User nu naam
 * @param int    $orderId   Order ID
 * @param float  $total     Order no total amount
 * @param string $paymentMethod
 * @param array  $items     [ ['name'=>.., 'qty'=>.., 'price'=>..], ... ]
 * @param array  $shipping  ['name'=>.., 'phone'=>.., 'address'=>..]
 * @return bool  true = email mokli gayo, false = error aavi
 */
function sendOrderConfirmationEmail($toEmail, $toName, $orderId, $total, $paymentMethod, $items, $shipping)
{
    $mail = new PHPMailer(true);

    try {
        // --- Server settings ---
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;
        $mail->CharSet    = 'UTF-8';

        // --- Spam-score ghatadva mate niche na settings ---
        $mail->setFrom(MAIL_USERNAME, MAIL_FROM_NAME);
        $mail->addReplyTo(MAIL_USERNAME, MAIL_FROM_NAME);
        $mail->MessageID = '<' . uniqid('order' . $orderId . '-', true) . '@' . preg_replace('/^.*@/', '', MAIL_USERNAME) . '>';
        $mail->XMailer = ' '; // default "PHPMailer" X-Mailer header kadhi nakho (spammy signal)

        // --- Recipient ---
        $mail->addAddress($toEmail, $toName);

        // --- Build order items table (HTML) ---
        $rows = '';
        foreach ($items as $item) {
            $lineTotal = $item['price'] * $item['qty'];
            $rows .= '<tr>
                <td style="padding:8px;border:1px solid #ddd;">' . htmlspecialchars($item['name']) . '</td>
                <td style="padding:8px;border:1px solid #ddd;text-align:center;">' . (int)$item['qty'] . '</td>
                <td style="padding:8px;border:1px solid #ddd;text-align:right;">₹' . number_format($item['price'], 2) . '</td>
                <td style="padding:8px;border:1px solid #ddd;text-align:right;">₹' . number_format($lineTotal, 2) . '</td>
            </tr>';
        }

        // --- Email content ---
        $mail->isHTML(true);
        $mail->Subject = "Order #$orderId confirmed - KD Dairy Farm";
        $mail->Body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;'>
            <h2 style='color:#2e7d32;'>Thank you, " . htmlspecialchars($toName) . "</h2>
            <p>Your order has been placed successfully. Here are your order details:</p>

            <p>
                <strong>Order ID:</strong> #$orderId<br>
                <strong>Payment Method:</strong> " . htmlspecialchars($paymentMethod) . "<br>
                <strong>Order Status:</strong> Pending
            </p>

            <table style='border-collapse:collapse;width:100%;margin-top:10px;'>
                <thead>
                    <tr style='background:#f2f2f2;'>
                        <th style='padding:8px;border:1px solid #ddd;text-align:left;'>Product</th>
                        <th style='padding:8px;border:1px solid #ddd;'>Qty</th>
                        <th style='padding:8px;border:1px solid #ddd;'>Price</th>
                        <th style='padding:8px;border:1px solid #ddd;'>Subtotal</th>
                    </tr>
                </thead>
                <tbody>$rows</tbody>
            </table>

            <p style='text-align:right;font-size:16px;margin-top:10px;'>
                <strong>Total: ₹" . number_format($total, 2) . "</strong>
            </p>

            <h3>Shipping Details</h3>
            <p>
                " . htmlspecialchars($shipping['name']) . "<br>
                " . htmlspecialchars($shipping['phone']) . "<br>
                " . nl2br(htmlspecialchars($shipping['address'])) . "
            </p>

            <p style='margin-top:20px;color:#555;'>We will notify you again once your order is confirmed and shipped.</p>
            <p style='color:#555;'>Regards,<br>KD Dairy Farm Team</p>
        </div>
        ";
        $mail->AltBody = "Thank you " . $toName . ". Your order #$orderId has been placed successfully. "
            . "Payment Method: $paymentMethod. Total: Rs. " . number_format($total, 2)
            . ". Shipping to: " . $shipping['name'] . ", " . $shipping['phone'] . ", " . $shipping['address']
            . ". Regards, KD Dairy Farm Team";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Email fail thay to bhi order place thaeloj rahevo joie, etle error ne log karo
        error_log('Order confirmation email failed for order #' . $orderId . ': ' . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Order status update email moklo (jyare admin order confirm/cancel/shipped/delivered kare).
 *
 * @param string $toEmail   User nu email
 * @param string $toName    User nu naam
 * @param int    $orderId   Order ID
 * @param string $status    'confirmed' | 'cancelled' | 'shipped' | 'delivered'
 * @param float  $total     Order no total amount
 * @return bool  true = email mokli gayo, false = error aavi
 */
function sendOrderStatusEmail($toEmail, $toName, $orderId, $status, $total)
{
    // Dareka status mate subject, heading, message ane color set karo
    $statusInfo = [
        'confirmed' => [
            'subject' => "Order #$orderId confirmed - KD Dairy Farm",
            'heading' => 'Your order is confirmed',
            'color'   => '#2e7d32',
            'message' => 'Good news! Your order has been confirmed and is now being prepared for shipping.',
        ],
        'cancelled' => [
            'subject' => "Order #$orderId cancelled - KD Dairy Farm",
            'heading' => 'Your order has been cancelled',
            'color'   => '#c62828',
            'message' => 'Your order has been cancelled. If you did not request this or have any questions, please contact us.',
        ],
        'shipped' => [
            'subject' => "Order #$orderId shipped - KD Dairy Farm",
            'heading' => 'Your order is on the way',
            'color'   => '#1565c0',
            'message' => 'Your order has been shipped and will reach you soon.',
        ],
        'delivered' => [
            'subject' => "Order #$orderId delivered - KD Dairy Farm",
            'heading' => 'Your order has been delivered',
            'color'   => '#2e7d32',
            'message' => 'Your order has been delivered. Thank you for shopping with us!',
        ],
    ];

    // Agar aa status list ma nathi (jem ke 'pending'), to email moklo j nahi
    if (!isset($statusInfo[$status])) {
        return false;
    }
    $info = $statusInfo[$status];

    $mail = new PHPMailer(true);

    try {
        // --- Server settings ---
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;
        $mail->CharSet    = 'UTF-8';

        // --- Spam-score ghatadva mate niche na settings ---
        $mail->setFrom(MAIL_USERNAME, MAIL_FROM_NAME);
        $mail->addReplyTo(MAIL_USERNAME, MAIL_FROM_NAME);
        $mail->MessageID = '<' . uniqid('order' . $orderId . '-', true) . '@' . preg_replace('/^.*@/', '', MAIL_USERNAME) . '>';
        $mail->XMailer = ' ';

        // --- Recipient ---
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);

        $mail->Subject = $info['subject'];
        $mail->Body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;'>
            <h2 style='color:" . $info['color'] . ";'>" . htmlspecialchars($info['heading']) . "</h2>
            <p>Hi " . htmlspecialchars($toName) . ",</p>
            <p>" . htmlspecialchars($info['message']) . "</p>

            <p>
                <strong>Order ID:</strong> #$orderId<br>
                <strong>Order Total:</strong> ₹" . number_format($total, 2) . "<br>
                <strong>Current Status:</strong> " . htmlspecialchars(ucfirst($status)) . "
            </p>

            <p style='margin-top:20px;color:#555;'>You can check your full order details anytime from your account.</p>
            <p style='color:#555;'>Regards,<br>KD Dairy Farm Team</p>
        </div>
        ";
        $mail->AltBody = $info['heading'] . ". Order #$orderId, Total: Rs. " . number_format($total, 2) . ", Status: " . ucfirst($status);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Order status email failed for order #' . $orderId . ': ' . $mail->ErrorInfo);
        return false;
    }
}