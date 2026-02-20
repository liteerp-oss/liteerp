import React from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import ListProducts from '../components/Product/ListProducts'
import PageHead from '../components/PageHead'
import { useI18n } from '../../i18n/useI18n'

export default function Product() {
    const { t } = useI18n()

    return (
        <DashboardLayout>
            <div>
                <PageHead
                    title={t('Products')}
                    subtitle={t('Manager product by warehouse, category')}
                />

                <div className="container mt-3">
                    <ListProducts />
                </div>
            </div>
        </DashboardLayout>
    )
}
