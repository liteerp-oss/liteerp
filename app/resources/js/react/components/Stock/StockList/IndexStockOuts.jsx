import React, { useCallback, useEffect } from 'react'
import StockOutService from '../../../services/StockOutService'
import useTable from '../../../libraries/handleTable'
import { Link, useNavigate } from 'react-router-dom'
import { useForm } from '../../../libraries/handleInput'
import { usePopup } from '../../popups/PopupContext'
import Currencies from '../../Currencies'
import StatusBadge from '../../StatusBadge'
import RenderFieldTableByList from '../../RenderFieldTableByList'
import { useI18n } from '../../../../i18n/useI18n'
import CommonDataTableV2 from '../../CommonDataTableV2'

export default function IndexStockOuts() {
    const { t, lang } = useI18n()
    const navigate = useNavigate()
    const search = useForm()
    const table = useTable()
    const { openPopup } = usePopup()

    const getListStockOut = useCallback(
        (page = 0) => {
            table.setLoading(true)
            StockOutService.list({
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
        StockOutService.view()
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
        table.setColums([
            {
                label: t('ID'),
                key: 'id',
                render: (id) => (
                    <Link to={`/stock-outs?stockout=${id}`}>{id}</Link>
                ),
            },
            {
                label: t('Customer'),
                key: 'customer_name',
            },
            {
                label: t('Status'),
                key: 'status',
                render: (value) => <StatusBadge status={value} />,
            },
            {
                label: t('Invoice no'),
                key: 'document_no',
            },
            {
                label: t('Quantity'),
                key: 'quantity',
            },
            {
                label: t('Shipping fee'),
                key: 'shipping_fee',
                render: (value) => (
                    <Currencies amount={value} />
                ),
            },
            {
                label: t('Expected delivery date'),
                key: 'expected_delivery_date',
            },
            {
                label: t('Order status'),
                key: 'order_status',
                render: (value) => <StatusBadge status={value} />,
            },
        ])

        getListStockOut()
        view()
    }, [lang])

    return (
        <div className="mt-3">
            <CommonDataTableV2
                loading={table.loading}
                columns={table.colums}
                data={table.data}
                links={table.links}
                onShow={(row) => {
                    navigate(`/stock-outs?stockout=${row.id}`)
                }}
                
                search={search}
                callback={getListStockOut}
                type={'stockout'}
            />
        </div>
    )
}
