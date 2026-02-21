import React, { useCallback, useEffect, useState } from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import IndexStockIns from '../components/Stock/StockList/IndexStockIns';
import { useI18n } from '@/i18n/useI18n';
import PageHead from '../components/PageHead';
export default function StockIns() {
    const {t} = useI18n();
    return <DashboardLayout>
        <div>
            <PageHead
                title={t('Stock ins')}
                subtitle={t('stock_ins_desc')}
                containerClass='mx-4'
            />
            <div className='m-4'>
                <IndexStockIns />
            </div>
        </div>
    </DashboardLayout>
}