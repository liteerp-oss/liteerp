import React, { useCallback, useEffect, useMemo, useState } from 'react'
import ProductService from '../../services/ProductService'
import { Select } from '../UI/Input/Select'
import { InputForm } from '../UI/Input/InputForm'
import { useForm } from '../../libraries/handleInput'
import useTable from '../../libraries/handleTable'
import { PopupLayout } from '../../layouts/PopupLayout'
import { usePopup } from '../popups/PopupContext'
import SearchSelect from '../UI/Input/SearchSelect'
import TextArea from '../UI/Input/Textarea'
import { useSelector } from 'react-redux'
import UploadImage from '../UI/Input/UploadImage'
import LoadImage from '../LoadImage'
import { useI18n } from '../../../i18n/useI18n'
import PERMISSIONS from '../../common/permission'
import RenderFieldTableByList from '../RenderFieldTableByList'
import RenderFormFieldByList from '../RenderFormFieldByList'
import CommonDataTableV2 from '../CommonDataTableV2'
export default function ListProducts() {
    const { t } = useI18n()
    const roles = useSelector((state) => state.businessRole.role);
    const { openPopup } = usePopup()

    const [showForm, setShowForm] = useState(false)
    const [category, setCategory] = useState([])

    const form = useForm()
    const search = useForm()
    const table = useTable()

    const getProducts = useCallback(
        (page = 0) => {
            table.setLoading(true)
            ProductService.list({
                page,
                ...search.formData
            })
                .then((resp) => {
                    table.setData(resp.message.data)
                    table.setLinks(resp.message.links)
                    table.setLoading(false)
                })
                .catch((error) => {
                    if (error.response?.data?.message) {
                        openPopup({
                            type: 'error',
                            message: error.response.data.message,
                        })
                    }
                })
        },
        [search]
    )

    const getCategories = useCallback((keywords = '', callback = null) => {
        ProductService.listCategory({
            page: 0,
            keywords,
        }).then((resp) => {
            setCategory(resp.message.data)
            callback && callback()
        })
    }, [])

    const update = useCallback(() => {
        form.setLoading(true)
        form.setFormErrors(null)

        ProductService.update(form.formData)
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('Product has been updated'),
                })
                getProducts()
                setShowForm(false)
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

    const create = useCallback(() => {
        form.setLoading(true)
        form.setFormErrors(null)

        ProductService.add(form.formData)
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('Product has been created'),
                })
                getProducts()
                setShowForm(false)
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

    const handEdit = (row) => {
        setShowForm(true)
        form.setIsEdit(true)
        form.setFormData(row)
        getCategories(row.category?.name)
    }

    const destroy = useCallback((row) => {
        ProductService.delete(row).then(() => {
            openPopup({
                type: 'success',
                message: t('Product has been deleted'),
            })
            getProducts()
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
        table.setLoading(true)
        ProductService.view()
            .then((resp) => {
                form.setHookRender(resp.message.form)
                search.setHookRender(resp.message.form)
                table.addColums(resp.message.index, (item, data) => {
                    return <RenderFieldTableByList item={item} data={data} />
                })
            })
            .catch((error) => {
                if (error.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data.message,
                    })
                }
            })
    }, [])
    useEffect(() => {
        table.setColums([
            { label: t('ID'), key: 'id' },
            {
                label: t('Thumbnail'),
                key: 'image',
                render: (url) => <LoadImage width={35} height={35} url={url} />,
            },
            { label: t('Name'), key: 'name' },
            { label: t('SKU'), key: 'sku' },
            { label: t('Unit'), key: 'unit' },
            { label: t('Category'), key: 'category' },
        ])
        getProducts()
        view();
    }, [])

    return (
        <div className="mt-3">
            <CommonDataTableV2
                add={
                    roles?.includes(PERMISSIONS.PRODUCT.CREATE) ? () => {
                        setShowForm(true)
                        form.setIsEdit(false)
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
                callback={getProducts}
                loading={table.loading}
                columns={table.colums}
                data={table.data}
                links={table.links}
                onEdit={roles?.includes(PERMISSIONS.PRODUCT.UPDATE) ? handEdit : null}
                onDelete={roles?.includes(PERMISSIONS.PRODUCT.DELETE) ? handleDelete : null}
            />

            {showForm && (
                <PopupLayout
                    loading={form.loading}
                    onConfirm={form.isEdit ? update : create}
                    onClose={() => setShowForm(false)}
                    title={
                        form.isEdit
                            ? t('Update product')
                            : t('Add product')
                    }
                >
                    <div className="form-group">
                        <InputForm
                            name="name"
                            handleChange={form.handleChange}
                            value={form.formData?.name}
                            errorMessage={form.formErrors?.name}
                            required={true}
                            label={t('Name')}
                        />
                    </div>

                    <div className="form-group">
                        <InputForm
                            name="sku"
                            handleChange={form.handleChange}
                            value={form.formData?.sku}
                            errorMessage={form.formErrors?.sku}
                            required={true}
                            label={t('SKU')}
                        />
                    </div>

                    <div className="form-group">
                        <Select
                            name="unit"
                            handleChange={form.handleChange}
                            value={form.formData?.unit}
                            errorMessage={form.formErrors?.unit}
                            options={[
                                { value: 'pcs', label: 'pcs' },
                                { value: 'set', label: 'set' },
                                { value: 'box', label: 'box' },
                                { value: 'carton', label: 'carton' },
                                { value: 'bag', label: 'bag' },
                                { value: 'pack', label: 'pack' },
                                { value: 'roll', label: 'roll' },
                            ]}
                            required={true}
                            label={t('Unit')}
                        />
                    </div>

                    <div className="form-group mt-3">
                        <SearchSelect
                            name="category_id"
                            changeValue={form.handleChangeByKey}
                            value={form.formData?.category_id}
                            search={getCategories}
                            options={category.map((item) => ({
                                value: item.id,
                                label: item.name,
                            }))}
                            errorMessage={form.formErrors?.category_id}
                            required={true}
                            label={t('Category')}
                        />
                    </div>

                    <div className="form-group mt-3">
                        <UploadImage
                            name="image"
                            handleChangeByKey={form.handleChangeByKey}
                            value={form.formData?.image}
                            errorMessage={form.formErrors?.image}
                            required={false}
                            label={t('Thumbnail')}
                        />
                    </div>

                    <div className="form-group mt-3">
                        <TextArea
                            name="description"
                            handleChange={form.handleChange}
                            value={form.formData?.description}
                            placeholder={t('Description')}
                            errorMessage={form.formErrors?.description}
                            required={true}
                            label={t('Description')}
                        />
                    </div>
                    {form.hookRender.map((item, index) => {
                        return <div className="form-group mt-3" key={index}>
                            <RenderFormFieldByList item={item} form={form} />
                        </div>
                    })}
                </PopupLayout>
            )}
        </div>
    )
}
