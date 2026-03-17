import React, { useCallback, useEffect, useState } from 'react'
import useTable from '../../libraries/handleTable'
import InventoryAdjustmentService from '../../services/InventoryAdjustmentService'
import ProductService from '../../services/ProductService'
import WarehouseService from '../../services/WarehouseService'
import { PopupLayout } from '../../layouts/PopupLayout'
import SearchSelect from '../UI/Input/SearchSelect'
import { InputForm } from '../UI/Input/InputForm'
import { useForm } from '../../libraries/handleInput'
import { usePopup } from '../popups/PopupContext'
import TextArea from '../UI/Input/Textarea'
import { isoToDateTime } from '../../libraries/common'
import { useI18n } from '../../../i18n/useI18n'
import CommonDataTableV2 from '../CommonDataTableV2'
import PurchaseService from '@/react/services/PurchaseService'
import StockMovementInService from '@/react/services/StockMovementInService'
import { Select } from '../UI/Input/Select'

export default function IndexInventoryAdjustment() {
    const { t, lang } = useI18n()
    const table = useTable()
    const form = useForm()
    const search = useForm()
    const { openPopup } = usePopup()
    const [stockMovementIn, setStockMovementIn] = useState([])
    const [purchases,setPurchases] = useState([])
    const [showForm, setShowForm] = useState(false)

    const getAdjustment = useCallback(
        (page = 0) => {
            table.setLoading(true)
            InventoryAdjustmentService.list({
                ...search.formData,
                page,
            }).then((resp) => {
                table.setLoading(false)
                table.setData(resp.message.data)
                table.setLinks(resp.message.links)
            })
        },
        [search.formData]
    )

    const getStockMovementIn = useCallback((keywords = '') => {
        StockMovementInService.list({
            keywords,
            page: 0,
            purchase_id: form.formData?.purchase_id
        }).then((resp) => {
            setStockMovementIn(resp.message.data)
        })
    }, [form.formData?.purchase_id])

    const getPurchase = useCallback((keywords = '', callback = null) => {
        PurchaseService.list({
            keywords,
            page: 0,
            isCompleted: 1
        }).then((resp) => {
            setPurchases(resp.message.data)
            callback && callback()
        })
    }, [])

    const add = useCallback(() => {
        form.setLoading(true)
        form.setFormErrors(null)

        InventoryAdjustmentService.add(form.formData)
            .then(() => {
                getAdjustment()
                openPopup({
                    type: 'success',
                    message: t('Added successfully'),
                })
                setShowForm(false)
                form.setFormData(null)
                form.setLoading(false)
            })
            .catch((error) => {
                if (error.response?.data?.errors) {
                    form.setFormErrors(error.response.data.errors)
                }
                if (error.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data.message,
                    })
                }
                form.setLoading(false)
            })
    }, [form.formData])

    const getView = useCallback(() => {
        InventoryAdjustmentService.view().then((resp) => {
            table.addColums(resp.message.index, (item, data) => (
                <RenderFormTableByList item={item} data={data} />
            ))
            search.setHookRender(resp.message?.search ?? [])
        })
    }, [table])
    useEffect(() => {
        if(form.formData?.purchase_id) {
            getStockMovementIn();
        } else {
            setStockMovementIn([])
        }
        
    },[form.formData?.purchase_id])
    useEffect(() => {
        table.setColums([
            { key: 'id', label: t('ID') },
            { key: 'product_name', label: t('Product') },
            {
                key: 'qty_adjusted',
                label: t('Quantity'),
                render: (quantity) =>
                    quantity >= 1 ? (
                        <span>
                            <i className="bi bi-arrow-up-short text-success"></i>{' '}
                            +{quantity}
                        </span>
                    ) : (
                        <span>
                            <i className="bi bi-arrow-down-short text-danger"></i>{' '}
                            {quantity}
                        </span>
                    ),
            },
            { key: 'reason', label: t('Reason') },
            { key: 'warehouse', label: t('Warehouse') },
            {
                key: 'created_by',
                label: t('Created by'),
                render: (name) => (
                    <span className="badge bg-primary">
                        {name}
                    </span>
                ),
            },
            {
                key: 'created_at',
                label: t('Created at'),
                render: (date) => (
                    <span className="badge bg-warning text-dark">
                        {isoToDateTime(date)}
                    </span>
                ),
            },
        ])
        getAdjustment()
        getView();
    }, [lang])

    return (
        <div>
            <CommonDataTableV2
                loading={table.loading}
                callback={getAdjustment}
                data={table.data}
                links={table.links}
                add={() => setShowForm(true)}

                search={search}
                columns={table.colums}
                type={'inventoryadjustment'}
            />

            {showForm && (
                <PopupLayout
                    loading={form.loading}
                    onClose={() => {
                        setShowForm(false)
                        form.setFormData(null)
                    }}
                    onConfirm={add}
                    title={t('Add adjustment')}
                >
                    <div>

                        <div className="form-group mt-3">
                            <Select
                                label={t('Inventory')}
                                required={true}
                                name="stock_movements_in_id"
                                handleChange={form.handleChange}
                                value={form.formData?.stock_movements_in_id}
                                errorMessage={
                                    form.formErrors?.stock_movements_in_id
                                }
                                options={stockMovementIn.map((item) => ({
                                    value: item.id,
                                    label: item.name,
                                }))}
                            />
                        </div>
                        <div className="form-group mt-3">
                            <SearchSelect
                                label={t('Purchase')}
                                required={true}
                                name="purchase_id"
                                search={getPurchase}
                                changeValue={form.handleChangeByKey}
                                value={form.formData?.purchase_id}
                                errorMessage={
                                    form.formErrors?.purchase_id
                                }
                                options={purchases.map((item) => ({
                                    value: item.id,
                                    label: `PU${item.id}`,
                                }))}
                            />
                        </div>

                        <div className="form-group mt-3">
                            <InputForm
                                label={t('Quantity')}
                                required={true}
                                name="qty_adjusted"
                                value={form.formData?.qty_adjusted}
                                handleChange={form.handleChange}
                                errorMessage={
                                    form.formErrors?.qty_adjusted
                                }
                            />
                        </div>

                        <div className="form-group mt-3">
                            <TextArea
                                label={t('Reason')}
                                required={true}
                                name="reason"
                                value={form.formData?.reason}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.reason}
                            />
                        </div>
                    </div>
                </PopupLayout>
            )}
        </div>
    )
}
