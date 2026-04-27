import React, { useCallback, useEffect, useMemo, useState } from 'react'
import ProductService from '../../services/ProductService'
import { useForm } from '../../libraries/handleInput'
import useTable from '../../libraries/handleTable'
import { PopupLayout } from '../../layouts/PopupLayout'
import { InputForm } from '../UI/Input/InputForm'
import { usePopup } from '../popups/PopupContext'
import PriceListService from '../../services/PriceListService'
import SearchSelect from '../UI/Input/SearchSelect'
import CustomerGroupService from '../../services/CustomerGroupService'
import Currency from '../Currencies'
import RenderFormFieldByList from '../RenderFormFieldByList'
import RenderFieldTableByList from '../RenderFieldTableByList'
import { useI18n } from '../../../i18n/useI18n'
import CommonDataTableV2 from '../CommonDataTableV2'

export default function IndexPriceList() {
    const { t, lang } = useI18n();
    const { openPopup } = usePopup()
    const [showAdd, setShowAdd] = useState(false)
    const [products, setProducts] = useState([])
    const [groups, setGroups] = useState([])

    const search = useForm()
    const form = useForm()
    const table = useTable()

    const getPriceList = useCallback(
        (page = 0) => {
            table.setLoading(true)
            PriceListService.list({
                page,
                ...search.formData,
            })
                .then((resp) => {
                    table.setData(resp.message.data)
                    table.setLinks(resp.message.links)
                    table.setLoading(false)
                })
                .catch(() => {
                    table.setLoading(false)
                })
        },
        [search.formData]
    )

    const getProducts = useCallback((keywords = '', callback = null) => {
        ProductService.list({ page: 0, keywords }).then((resp) => {
            setProducts(resp.message.data)
            callback && callback()
        })
    }, [])

    const getGroup = useCallback((keywords = '', callback = null) => {
        CustomerGroupService.list({ page: 0, keywords }).then((resp) => {
            setGroups(resp.message.data)
            callback && callback()
        })
    }, [])

    const submit = useCallback(() => {
        form.setLoading(true)
        form.setFormErrors(null)

        PriceListService.add(form.formData)
            .then(() => {
                openPopup({
                        type: 'success',
                        message: t('create_success')
                    })

                setShowAdd(false)
                getPriceList()
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

    const update = useCallback(() => {
        form.setLoading(true)
        form.setFormErrors(null)

        PriceListService.update(form.formData)
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('update_success')
                })
                setShowAdd(false)
                getPriceList(0)
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

    const destroy = useCallback((row) => {
        PriceListService.delete(row).then(() => {
            openPopup({
                type: 'success',
                message: t('delete_success'),
            })
            getPriceList()
        })
    }, [])

    const handleDelete = (row) => {
        openPopup({
            type: 'warning',
            message: t('Are you sure to delete?'),
            onConfirm: () => destroy(row),
        })
    }

    const view = useCallback(() => {
        PriceListService.view().then((resp) => {
            form.setHookRender(resp.message.form)
            search.setHookRender(resp.message.search)
            table.addColums(resp.message.index, (item, data) => (
                <RenderFieldTableByList item={item} data={data} />
            ))
        })
    }, [])

    useEffect(() => {
        getPriceList()
        view()
        table.setColums([
            { label: t('ID'), key: 'id' },
            { label: t('Name'), key: 'name' },
            {
                label: t('Price'),
                key: 'price',
                render: (value) => (
                    <strong>
                        <Currency amount={value} />
                    </strong>
                ),
            },
            { label: t('Customer group'), key: 'group' },
        ])
    }, [lang])

    return (
        <div className="mt-3">
            <CommonDataTableV2
                loading={table.loading}

                search={search}
                callback={getPriceList}
                add={() => {
                    setShowAdd(true)
                    form.setIsEdit(false)
                }}
                columns={table.colums}
                data={table.data}
                links={table.links}
                onEdit={(row) => {
                    form.setIsEdit(true)
                    form.setFormData(row)
                    setShowAdd(true)
                }}
                onDelete={handleDelete}
                type={'pricelist'}
            />

            {showAdd && (
                <PopupLayout
                    loading={form.loading}
                    confirmText={t('Save changes')}
                    onConfirm={form.isEdit ? update : submit}
                    onClose={() => setShowAdd(false)}
                    title={
                        form.isEdit
                            ? t('Update Price')
                            : t('Add Price')
                    }
                >
                    <div className="form-group">
                        <InputForm
                            type="numeric"
                            name="price"
                            value={form.formData?.price}
                            handleChange={form.handleChange}
                            errorMessage={form.formErrors?.price}
                            required={true}
                            label={t('Price')}
                        />
                    </div>

                    <div className="form-group mt-3">
                        <SearchSelect
                            name="product_id"
                            search={getProducts}
                            value={form.formData?.product_id}
                            changeValue={form.handleChangeByKey}
                            errorMessage={form.formErrors?.product_id}
                            options={products.map((item) => ({
                                value: item.id,
                                label: item.name,
                            }))}
                            defaultKeywords={form.formData?.name}
                            required={true}
                            label={t('Product')}
                        />
                    </div>

                    <div className="form-group mt-3">
                        <SearchSelect
                            name="customer_group_id"
                            search={getGroup}
                            value={form.formData?.customer_group_id}
                            changeValue={form.handleChangeByKey}
                            errorMessage={form.formErrors?.customer_group_id}
                            options={groups.map((item) => ({
                                value: item.id,
                                label: item.name,
                            }))}
                            defaultKeywords={form.formData?.group}
                            required={true}
                            label={t('Customer Group')}
                        />
                    </div>

                    {form.hookRender.map((item, index) => (
                        <div className="form-group mt-3" key={index}>
                            <RenderFormFieldByList item={item} form={form} />
                        </div>
                    ))}
                </PopupLayout>
            )}
        </div>
    )
}
