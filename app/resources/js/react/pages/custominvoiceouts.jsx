import React from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import IndexCustomInvoiceOuts from '../components/Invoice/IndexCustomInvoiceOuts'
import { useI18n } from '@/i18n/useI18n'
import PageHead from '../components/PageHead';
export default function CustomInvoiceOuts() {
    const { t } = useI18n();
    return <DashboardLayout>
        <div>
            <PageHead
                containerClass='mx-4'
                title={t('Custom invoice outs')}
                subtitle={t('custom_invoice_out_desc')}
            />
            <div className='m-4'>
                <IndexCustomInvoiceOuts />
            </div>
        </div>
    </DashboardLayout>
}