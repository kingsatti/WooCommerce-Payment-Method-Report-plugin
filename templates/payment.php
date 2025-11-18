<?php
// Get all available payment gateways
$gateways = WC()->payment_gateways->get_available_payment_gateways();

if ( $gateways ) {

    $totalm = [];
    $qtym = [];
    $payt = [];

    // Loop through gateways
    foreach ( $gateways as $gateway ) {

        if ( $gateway->enabled == 'yes' ) {

            $payt[$gateway->id] = $gateway->title;
            $totalm[$gateway->id] = [];
            $qtym[$gateway->id] = [];

            // Define current month range
            $monthsStart = date("Y-m-01");
            $monthsEnd   = date("Y-m-t");

            // Get all completed/processing orders for this payment gateway
            $args = [
                'date_created'  => $monthsStart . '...' . $monthsEnd,
                'status'        => ['wc-completed', 'wc-processing'],
                'limit'         => -1,
                'payment_method'=> [$gateway->id],
                'order'         => 'ASC',
            ];
            $orders = wc_get_orders($args);

            $total = 0;
            $qty = 0;

            foreach ($orders as $order) {
                $total += $order->get_total();
                foreach ($order->get_items() as $orderItem) {
                    $qty += $orderItem->get_quantity();
                }
            }

            $totalm[$gateway->id][] = $total;
            $qtym[$gateway->id][] = $qty;
        }
    }

    // Build data array
    $dataArray = [];
    $totalP = 0;

    foreach ($totalm as $key => $totals) {
        $method = $payt[$key];

        $gquantity = array_sum($qtym[$key]);
        $gtotal = array_sum($totals);

        if ($gtotal > 0) {
            $dataArray[] = [
                'method'   => $method,
                'quantity' => $gquantity,
                'total'    => $gtotal,
            ];
            $totalP += $gtotal;
        }
    }

    // Sort data by total descending
    $keys = array_column($dataArray, 'total');
    array_multisort($keys, SORT_DESC, SORT_NUMERIC, $dataArray);
}
?>

<style>
table.payment-report {
  font-family: Arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
  margin-top: 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
table.payment-report th, table.payment-report td {
  border: 1px solid #e2e2e2;
  padding: 8px 10px;
  text-align: left;
}
table.payment-report th {
  background-color: #0073aa;
  color: #fff;
}
table.payment-report tr:nth-child(even) {
  background-color: #f9f9f9;
}
table.payment-report tr:hover {
  background-color: #eef6fb;
}
tfoot td {
  font-weight: bold;
  background: #f1f1f1;
}
</style>

<?php if (!empty($dataArray)) : ?>
  <table class="payment-report">
    <thead>
      <tr>
        <th>#</th>
        <th>Payment Method</th>
        <th>Quantity</th>
        <th>Total</th>
        <th>%</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $i = 1;
      foreach ($dataArray as $data) :
          $percentage = ($data['total'] / $totalP) * 100;
      ?>
      <tr>
        <td><?php echo $i++; ?></td>
        <td><?php echo esc_html($data['method']); ?></td>
        <td><?php echo number_format($data['quantity']); ?></td>
        <td><?php echo wc_price($data['total']); ?></td>
        <td><?php echo number_format($percentage, 2) . '%'; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3" style="text-align:right;">Total</td>
        <td><?php echo wc_price($totalP); ?></td>
        <td>100%</td>
      </tr>
    </tfoot>
  </table>
<?php else : ?>
  <p>No payment data found for this month.</p>
<?php endif; ?>
