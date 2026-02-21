import React from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import ListGroup from '../components/Customer/ListGroup'
import PageHead from '../components/PageHead'
export default function CustomerGroup() {
    return <DashboardLayout>
        <div>
            <PageHead
                containerClass='mx-4'
                title='Customer group'
                subtitle="page_customer_group_desc"
            />
            <div className='m-4'>
                <ListGroup />
            </div>
        </div>
    </DashboardLayout>
}