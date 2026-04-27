import React from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import IndexPriceList from '../components/Product/IndexPriceList'
import PageHead from '../components/PageHead'
import { useI18n } from '@/i18n/useI18n'
export default function Pricelist() {
    const { t } = useI18n();
    return <DashboardLayout>
        <div>
            <PageHead
                title={t('Price List')}
                subtitle={t('price_list_desc')}
            />
            <div className='mt-3 container'>
                <IndexPriceList />
            </div>
        </div>
    </DashboardLayout>
}