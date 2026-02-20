import React, { useCallback, useEffect, useState } from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import ListCustomer from '../components/Customer/ListCustomer'
import PageHead from '../components/PageHead'
import { useI18n } from '../../i18n/useI18n'
export default function Customer() {
   const {t} = useI18n();
    return <DashboardLayout>
        <div>
            <PageHead
            containerClass='mx-4'
            title='Customers'
            subtitle="page_customer_desc"
            />
            <div className="m-4">
                <ListCustomer/>
            </div>
        </div>
    </DashboardLayout>
}