import React from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import Category from '../components/Product/Category'
import { useI18n } from '@/i18n/useI18n'
import PageHead from '../components/PageHead';
export default function CategoryProduct() {
    const { t } = useI18n();
    return <DashboardLayout>
        <div>
            <PageHead
                title={t('Category Product')}
                subtitle={t('category_product')}
                containerClass='mx-4'
            />
            <div className='m-4'>
                <Category />
            </div>
        </div>
    </DashboardLayout>
}