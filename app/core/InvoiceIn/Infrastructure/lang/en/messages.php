<?php

return [
    'created' => 'InvoiceIn created successfully!',
    'deleted' => 'InvoiceIn deleted successfully!',
    'not_found' => 'Invoice not found.',
    'stock_created' => 'The data for stock in has been created, so you cannot change this invoice to unapprove. You can request the Purchase Department to cancel the purchase.',
    'partial_payment' => 'This invoice is a partial payment; please insert amount paid. The amount paid must be greater than or equal to the invoice total.',
    'notification' => [
        'created' => ':username created an incoming invoice',
        'updated' => ':username updated the incoming invoice',
        'approved' => ':username approved the incoming invoice'
    ],
    'title' => 'Incoming Invoice',
];
