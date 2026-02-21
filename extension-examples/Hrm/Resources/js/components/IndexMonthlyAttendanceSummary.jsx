import React, { useState, useEffect } from 'react';
import CommonDatTable from '@components/CommonDataTable'
import hrmService from '../services/hrm-service';
import useTable from '@libraries/handleTable'
import { isoToDateTime } from '@libraries/common'
import {useI18n} from '@i18n/useI18n'
const IndexMonthlyAttendanceSummary = () => {
    const { t, lang } = useI18n();
    const table = useTable();
    const getData = (page = 0) => {
        hrmService.export.all({
            page: page
        })
            .then((resp) => {
               table.setData(resp.message.data)
            })
            .catch((error) => {

            })
    };
    const handleEdit = (item) => {
        hrmService.export.show(item)
            .then((resp) => {
               window.open(resp.message.link,"_blank")
            })
            .catch((error) => {

            })
    }
    useEffect(() => {
       getData();
    },[lang])
    return (
        <div>
            <div>
                <CommonDatTable 
                filter={<div>
                    <p className='theme-title'>{t('hrm.export.desc')}</p>
                </div>}
                columns={[
                    { key: "id", label: "ID" },
                    { key: "name", label: t("hrm.export.name") },
                    { key: "created_at", label: t("hrm.export.created_at"), render:(value) => {
                        return isoToDateTime(value)
                    } }
                ]} 
                data={table.data}
                onEdit={handleEdit}
                iconEdit={<i className="bi bi-cloud-download"></i>}
                movePage={getData}
                />
            </div>
        </div>
    );
};

export default IndexMonthlyAttendanceSummary;