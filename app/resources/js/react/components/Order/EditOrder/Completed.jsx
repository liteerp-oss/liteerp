import { useI18n } from '@/i18n/useI18n'
import React from 'react'
export default function Completed(){
    const {t} = useI18n();
    return <div>
        <h4 className='text-success'>{t('Approved')}</h4>
        <p>{t("order_approve_desc")}</p>
    </div>
}