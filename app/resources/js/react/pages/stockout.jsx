import React from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import IndexStockOuts from '../components/Stock/StockList/IndexStockOuts'
import PageHead from '../components/PageHead'
import { useI18n } from '@/i18n/useI18n'
export default function StockOuts() {
    const {t} = useI18n();
    return <DashboardLayout>
        <div>
            <PageHead
                title={t('Stock outs')}
                subtitle={t('stock_out_desc')}
                containerClass='mx-4'
            />
            <div className="m-4">
                <IndexStockOuts />
            </div>
        </div>
    </DashboardLayout>
}