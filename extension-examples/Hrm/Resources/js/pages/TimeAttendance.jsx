import React, { useState } from 'react';
import IndexTimeAttendance from '../components/IndexTimeAttendance';
import DashboardLayout from '@layouts/DashboardLayout'
import PageHead from '@components/PageHead'
import {useI18n} from '@i18n/useI18n'
const HRMPage = () => {
    const {t} = useI18n()

    return (
        <DashboardLayout>
            <div className="">
                <PageHead title={t('hrm.title')} subtitle={t('hrm.desc')}/>
                <div className='mt-3 container'>
                    <IndexTimeAttendance/>
                </div>
            </div>
        </DashboardLayout>

    );
};

export default HRMPage;