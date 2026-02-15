import { useI18n } from '@/i18n/useI18n'
import React from 'react'
export default function Cancelled(){
    const {t} = useI18n();
    return <div>
        <h4 className='text-danger'>{t("Cancelled")}</h4>
        <p>{t("order_cancel_desc")}</p>
    </div>
}