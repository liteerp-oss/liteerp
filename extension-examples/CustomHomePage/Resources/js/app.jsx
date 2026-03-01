import React, { useState } from 'react';
import DashboardLayout from '@layouts/DashboardLayout'
import PageHead from '@components/PageHead'
import {useI18n} from '@i18n/useI18n'
const CustomHomePagePage = () => {
    const {t} = useI18n()

    return (
        <DashboardLayout>
            <div className="">
                <PageHead title={t('customhomepage.title')} subtitle={t('customhomepage.desc')}/>
                <div>

                </div>
            </div>
        </DashboardLayout>

    );
};

export default CustomHomePagePage;