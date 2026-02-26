import React, { useCallback, useEffect } from 'react'
import StockInService from '../../../services/StockInService'
import useTable from '../../../libraries/handleTable'
import { Link, useNavigate } from 'react-router-dom'
import { isoToDateTime } from '../../../libraries/common'
import { useForm } from '../../../libraries/handleInput'
import { usePopup } from '../../popups/PopupContext'
import StatusBadge from '../../StatusBadge'
import RenderFieldTableByList from '../../RenderFieldTableByList'
import { useI18n } from '../../../../i18n/useI18n'
import CommonDataTableV2 from '../../CommonDataTableV2'

export default function IndexStockIns() {
    const { t, lang } = useI18n()
    const navigate = useNavigate()
    const search = useForm()
    const table = useTable()
    const { openPopup } = usePopup()

    const getListStockIn = useCallback(
        (page = 0) => {
            table.setLoading(true)
            StockInService.list({
                page,
                ...search.formData,
            })
                .then((resp) => {
                    table.setData(resp.message.data)
                    table.setLinks(resp.message.links)
                    table.setLoading(false)
                })
                .catch((error) => {
                    if (error.response?.message?.errors) {
                        openPopup({
                            type: 'error',
                            message: error.response.message.errors,
                        })
                    }
                    table.setLoading(false)
                })
        },
        [search.formData]
    )

    const view = useCallback(() => {
        StockInService.view()
            .then((resp) => {
                table.addColums(resp.message.index, (item, data) => {
                    return (
                        <RenderFieldTableByList
                            item={item}
                            data={data}
                        />
                    )
                })
                search.setHookRender(resp.message.search)
            })
            .catch((error) => {
                if (error.response?.message?.errors) {
                    openPopup({
                        type: 'error',
                        message: error.response.message.errors,
                    })
                }
            })
    }, [])

    useEffect(() => {
        getListStockIn()
        table.setColums([
            {
                label: t('ID'),
                key: 'id',
                render: (id) => (
                    <Link to={`/stock-ins?stockin=${id}`}>{id}</Link>
                ),
            },
            {
                label: t('Supplier'),
                key: 'supplier_name',
            },
            {
                label: t('Purchase ID'),
                key: 'purchase_id',
                render: (id) => <span>PU{id}</span>,
            },
            {
                label: t('Invoice no'),
                key: 'document_no',
            },
            {
                label: t('Status'),
                key: 'status',
                render: (value) => <StatusBadge status={value} />,
            },
            {
                label: t('Products'),
                key: 'total_product',
            },
            {
                label: t('Approver'),
                key: 'approved_name',
                render: (name) =>
                    name ? (
                        <span className="badge bg-success">{name}</span>
                    ) : (
                        '-'
                    ),
            },
            {
                label: t('Import date'),
                key: 'import_date',
                render: (date) =>
                    date ? isoToDateTime(date) : '-',
            },
            {
                label: t('Purchase status'),
                key: 'purchase_status',
                render: (value) => <StatusBadge status={value} />,
            },
        ])
        view()
    }, [lang])

    return (
        <div className="mt-3">
            <CommonDataTableV2
                loading={table.loading}
                columns={table.colums}
                data={table.data}
                links={table.links}
                onShow={ (row) => {
                    navigate(`/stock-ins?stockin=${row.id}`)
                    }}
                
                search={search}
                callback={getListStockIn}
                type={'stockin'}
            />
        </div>
    )
}
