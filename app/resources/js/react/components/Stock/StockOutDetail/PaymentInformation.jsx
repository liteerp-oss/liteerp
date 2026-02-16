import { useI18n } from '@/i18n/useI18n'
import React from 'react'
import StatusBadge from '../../StatusBadge';
export default function PaymentInformation({
    form = {
        formData: null
    }
}) {
    const {t} = useI18n();
    return <div className="p-4 rounded border">
        <h5 className="fw-semibold mb-3">{t("Payment information")}</h5>
        <div className="mb-2">
            <div className="theme-title small">{t("Payment method")}</div>
            <div className="theme-title badge 
                                bg-primary text-white text-uppercase">{t(form.formData?.payment_method ?? '')}</div>
        </div>
        <div className="mb-2">
            <div className="theme-title small">{t("Payment status")}</div>
            <StatusBadge status={form.formData?.payment_status ?? ''}/>
        </div>
    </div>
}