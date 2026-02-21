import React from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import IndexCustomInvoiceIns from '../components/Invoice/IndexCustomInvoiceIns'
import PageHead from '../components/PageHead'
import { useI18n } from '@/i18n/useI18n'
export default function CustomInvoiceIns(){
    const {t} = useI18n();
    return <DashboardLayout>
        <div>
            <PageHead
                                  containerClass='mx-4'
                                  title={t('Custom invoice ins')}
                                  subtitle={t('custom_invoice_in_desc')}
                                  />
            <div className='m-4'>
                <IndexCustomInvoiceIns/>
            </div>
        </div>
    </DashboardLayout>
}