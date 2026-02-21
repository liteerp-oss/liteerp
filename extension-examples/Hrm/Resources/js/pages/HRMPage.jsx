import React, { useState } from 'react';
import DashboardLayout from '@layouts/DashboardLayout'
import PageHead from '@components/PageHead'
import TabsCustom from '@components/TabsCustom'
import {useI18n} from '@i18n/useI18n'
import IndexReport from '../components/IndexReport';
import IndexMonthlyAttendanceSummary from '../components/IndexMonthlyAttendanceSummary';
import IndexLeave from '../components/IndexLeave';
import PermissionNode from '@/core/PermissionNode'
import IndexTimeAttendance from '../components/IndexTimeAttendance';
const HRMPage = () => {
    const {t} = useI18n()
    const permission = new PermissionNode();
    permission.fromNode('hrm');
    return (
        <DashboardLayout>
            <div className="">
                <PageHead title={t('hrm.title')} subtitle={t('hrm.desc')}/>
                <div className='mt-3 container'>
                    <TabsCustom
                navs={[
                    {key: permission.getPermission('index-attendance'),label: t('hrm.attendance.title')},
                    {key: permission.getPermission('index-report'),label: t('hrm.report.title')},
                    {key: permission.getPermission('index-monthly-summary'), label: t('hrm.export.title')},
                    {key: permission.getPermission('index-leave'), label: t('hrm.leave.title')}
                ]}
                contents={[
                    <div>
                        <IndexTimeAttendance/>
                    </div>,
                    <div>
                        <IndexReport/>
                    </div>,
                    <div>
                        <IndexMonthlyAttendanceSummary/>
                    </div>,
                    <div>
                        <IndexLeave/>
                    </div>
                ]}
                />
                </div>
            </div>
        </DashboardLayout>

    );
};

export default HRMPage;