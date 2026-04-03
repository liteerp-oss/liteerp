<?php

return [
    'not_found' => 'Invoice not found.',
    'approved_cannot_change' => 'Invoice has been approved; you cannot change its status.',
    'partial_payment' => 'This invoice is a partial payment; please insert amount paid. The amount paid must be greater than or equal to the invoice total.',
    'notification' => [
        'created' => ':username created an outgoing invoice',
        'updated' => ':username updated the outgoing invoice',
        'approved' => ':username approved the outgoing invoice'
    ],
    'title' => 'Invoice Out',
];
