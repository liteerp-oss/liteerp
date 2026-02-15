import React, { useCallback, useEffect } from 'react'
import StockInService from '../../../services/StockInService'
import useTable from '../../../libraries/handleTable'
import CommonDataTable from '../../CommonDataTable'
import { Link, useNavigate } from 'react-router-dom'
import { isoToDateTime } from '../../../libraries/common'
import { useForm } from '../../../libraries/handleInput'
import { Select } from '../../UI/Input/Select'
import SearchInput from '../../UI/Input/SearchInput'
import { usePopup } from '../../popups/PopupContext'
import StatusBadge from '../../StatusBadge'
import RenderFieldTableByList from '../../RenderFieldTableByList'
import { RenderTableSearch } from '../../RenderTableSearch'
import PrimaryButton from '../../UI/Buttons/PrimaryButton'
import { useI18n } from '../../../../i18n/useI18n'
import { useSelector } from 'react-redux'
import PERMISSIONS from '../../../common/permission'
import CommonDataTableV2 from '../../CommonDataTableV2'

export default function StockIns() {
    const { t } = useI18n()
    const roles = useSelector((state) => state.businessRole.role);
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
                    <Link to={`/stock?id=${id}`}>{id}</Link>
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
    }, [])

    return (
        <div className="mt-3">
            <CommonDataTableV2
                loading={table.loading}
                columns={table.colums}
                data={table.data}
                links={table.links}
                iconEdit={<i className="bi bi-eye"></i>}
                onEdit={ roles?.includes(PERMISSIONS.STOCK_IN.SHOW) ? (row) => {
                    navigate(`/stocks?stockin=${row.id}`)
                    } : null
                }
                config={{
                    default: [{
                        key: "order_by",
                        placeholder: t("Order by"),
                        options: [
                            { value: 'ASC', label: t('Oldest') },
                            { value: 'DESC', label: t('Newest') },
                        ],
                        type: "select",
                        label: t("Order by"),
                        col: "col-6"
                    },{
                        key: "keywords",
                        placeholder: t("Keywords"),
                        type: "text",
                        label: t("Search"),
                        col: "col-6"
                    }]
                }}
                search={search}
                callback={getListStockIn}
            />
        </div>
    )
}
