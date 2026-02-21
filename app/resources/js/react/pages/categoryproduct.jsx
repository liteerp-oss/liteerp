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
                title={t('Product categories')}
                subtitle={t('category_product_desc')}
                containerClass='mx-4'
            />
            <div className='m-4'>
                <Category />
            </div>
        </div>
    </DashboardLayout>
}