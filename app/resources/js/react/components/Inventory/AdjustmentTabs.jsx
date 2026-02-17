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

export default function AdjustmentTabs() {
    const { t, lang } = useI18n()
    const table = useTable()
    const form = useForm()
    const search = useForm()
    const { openPopup } = usePopup()
    const [products, setProducts] = useState([])
    const [warehouses, setWarehouses] = useState([])
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

    const getProducts = useCallback((keywords = '', callback = null) => {
        ProductService.list({
            keywords,
            page: 0,
        }).then((resp) => {
            setProducts(resp.message.data)
            callback && callback()
        })
    }, [])

    const getWarehouses = useCallback((keywords = '', callback = null) => {
        WarehouseService.list({
            keywords,
            page: 0,
        }).then((resp) => {
            setWarehouses(resp.message.data)
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
    }, [lang])

    return (
        <div>
            <CommonDataTableV2
                loading={table.loading}
                callback={getAdjustment}
                data={table.data}
                links={table.links}
                add={ () => setShowForm(true)}
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
                        <div className="form-group">
                            <label>{t('Product')}</label>
                            <SearchSelect
                                name="product_id"
                                search={getProducts}
                                changeValue={form.handleChangeByKey}
                                value={form.formData?.product_id}
                                errorMessage={
                                    form.formErrors?.product_id
                                }
                                options={products.map((item) => ({
                                    value: item.id,
                                    label: item.name,
                                }))}
                            />
                        </div>

                        <div className="form-group">
                            <label>{t('Warehouse')}</label>
                            <SearchSelect
                                name="warehouse_id"
                                search={getWarehouses}
                                changeValue={form.handleChangeByKey}
                                value={form.formData?.warehouse_id}
                                errorMessage={
                                    form.formErrors?.warehouse_id
                                }
                                options={warehouses.map((item) => ({
                                    value: item.id,
                                    label: item.name,
                                }))}
                            />
                        </div>

                        <div className="form-group">
                            <label>{t('Quantity')}</label>
                            <InputForm
                                name="qty_adjusted"
                                value={form.formData?.qty_adjusted}
                                handleChange={form.handleChange}
                                errorMessage={
                                    form.formErrors?.qty_adjusted
                                }
                            />
                        </div>

                        <div className="form-group">
                            <label>{t('Reason')}</label>
                            <TextArea
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
