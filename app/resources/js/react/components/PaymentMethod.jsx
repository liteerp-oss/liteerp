import { useI18n } from '@/i18n/useI18n'
import React from 'react'
export default function PaymentMethod({
    value = null 
}) {
    const {t} = useI18n();
    return <span className={'badge text-uppercase badge-' + value}>
        {t(value)}
    </span>
}