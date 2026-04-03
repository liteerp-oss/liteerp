<?php

return [
    'not_found'               => '請求書が見つかりません。',
    'approved_cannot_change'  => '請求書はすでに承認されているため、ステータスを変更することはできません。',
    'partial_payment'         => 'この請求書は一部支払いです。支払済み金額を入力してください。支払金額は請求書の合計金額以上である必要があります。',
    'notification'            => [
        'created' => ':username が売上請求書を作成しました',
        'updated' => ':username が売上請求書を更新しました',
        'approved' => ':username が売上請求書を承認しました'
    ],
    'title' => '売上請求書',
];
