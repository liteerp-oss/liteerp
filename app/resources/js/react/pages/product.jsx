import React, { useEffect, useState } from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import ListProducts from '../components/Product/ListProducts'
import PageHead from '../components/PageHead'
import { useI18n } from '../../i18n/useI18n'
import { useSearchParams } from 'react-router-dom'
import AddProduct from '../components/Product/AddProduct'
import UpdateProduct from '../components/Product/UpdateProduct'

export default function Product() {
    const { t } = useI18n();
    const [searchParams] = useSearchParams();
    const [page,setPage] = useState(null);

    useEffect(() => {
        if(searchParams.get('product')) {
            setPage(searchParams.get('product'))
        } else {
            setPage(null)
        }
    },[searchParams])

    return (
        <DashboardLayout>
            <div>
                <PageHead
                    title={t('Products')}
                    subtitle={t('Manager product by warehouse, category')}
                />

                <div className="container mt-3">
                    {!page ? <div>
                        <ListProducts />
                    </div> : page === 'add' ? <div>
                        <AddProduct/>
                    </div> : page >= 1 ? <div>
                        <UpdateProduct/>
                    </div> : null}
                    
                </div>
            </div>
        </DashboardLayout>
    )
}
