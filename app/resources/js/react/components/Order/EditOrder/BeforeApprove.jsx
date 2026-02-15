import { useI18n } from '@/i18n/useI18n'
import React from 'react'
export default function BeforeApprove(){
    const {t} = useI18n();
    return <div>
        <h4>{t("Take Approved")}</h4>
        <p>{t("order_before_approve_desc")}</p>
    </div>
}