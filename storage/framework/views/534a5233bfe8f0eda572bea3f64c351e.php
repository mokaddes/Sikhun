<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: sans-serif; color: #1a1a2e; max-width: 500px; margin: 0 auto;">
    <h2 style="color: #6c63ff;">Order Confirmed</h2>
    <p>Hi <?php echo e($order->student->name); ?>, your order has been confirmed.</p>
    <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
        <tr><td style="padding: 6px 0; color: #6b6b8a;">Order #</td><td style="padding: 6px 0;"><?php echo e($order->order_number); ?></td></tr>
        <tr><td style="padding: 6px 0; color: #6b6b8a;">Type</td><td style="padding: 6px 0; text-transform: capitalize;"><?php echo e(str_replace('_', ' ', $order->orderable_type)); ?></td></tr>
        <tr><td style="padding: 6px 0; color: #6b6b8a;">Amount</td><td style="padding: 6px 0;">৳<?php echo e($order->amount); ?></td></tr>
        <tr><td style="padding: 6px 0; color: #6b6b8a;">Payment Method</td><td style="padding: 6px 0; text-transform: capitalize;"><?php echo e($order->payment_method); ?></td></tr>
    </table>
    <p>Thanks for learning with Sikhun.com!</p>
</body>
</html>
<?php /**PATH D:\Project files\clude\sikhun\resources\views/emails/order-confirmation.blade.php ENDPATH**/ ?>