import React from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import IndexInventoryAdjustment from '../components/Inventory/IndexInventoryAdjustment'
import PageHead from '../components/PageHead'
import { useI18n } from '@/i18n/useI18n'
export default function InventoryAdjustments() {
    const { t } = useI18n();
    return <DashboardLayout>
        <div>
            <PageHead
                title={t('Inventory adjustment')}
                subtitle={t('inventory_adjustment_desc')}
            />
            <div className='mt-3 container'>
                <IndexInventoryAdjustment />
            </div>
        </div>
    </DashboardLayout>
}