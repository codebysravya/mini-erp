<?php

return [
    // Items with stock strictly below this value are "low stock".
    'low_stock_threshold' => (int) env('LOW_STOCK_THRESHOLD', 10),
];