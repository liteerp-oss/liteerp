import React from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import PageHead from '../components/PageHead'
import { useI18n } from '../../i18n/useI18n'
import IndexInventory from '../components/Inventory/IndexInventory'

export default function Inventory() {
    const { t } = useI18n()

    return (
        <DashboardLayout>
            <div>
                <PageHead
                    title={t('Inventory')}
                    subtitle={t('Manage inventory')}
                />

                <div className="container mt-3">
                    <IndexInventory/>
                </div>
            </div>
        </DashboardLayout>
    )
}
