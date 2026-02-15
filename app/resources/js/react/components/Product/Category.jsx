import React, { useCallback, useEffect, useMemo, useState } from 'react'
import CommonDataTable from '../CommonDataTable'
import ProductService from '../../services/ProductService'
import SearchInput from '../UI/Input/SearchInput'
import { useForm } from '../../libraries/handleInput'
import useTable from '../../libraries/handleTable'
import { PopupLayout } from '../../layouts/PopupLayout'
import { InputForm } from '../UI/Input/InputForm'
import TextArea from '../UI/Input/Textarea'
import { usePopup } from '../popups/PopupContext'
import { useSelector } from 'react-redux'
import PrimaryButton from '../UI/Buttons/PrimaryButton'
import { Select } from '../UI/Input/Select'
import RenderFieldTableByList from '../RenderFieldTableByList'
import RenderFormFieldByList from '../RenderFormFieldByList'
import { RenderTableSearch } from '../RenderTableSearch'
import { useI18n } from '../../../i18n/useI18n'
import PERMISSIONS from '../../common/permission'
import CommonDataTableV2 from '../CommonDataTableV2'

export default function Category() {
    const { t } = useI18n()
    const roles = useSelector((state) => state.businessRole.role);
    const business = useSelector((state) => state.business.data)

    const [attributes, setAttributes] = useState([])
    const [showAdd, setShowAdd] = useState(false)

    const attrAddForm = useForm()
    const attrForm = useForm()
    const form = useForm()
    const search = useForm()
    const tableCategory = useTable()

    const { openPopup } = usePopup()

    const getCategorires = useCallback((page = 0) => {
        tableCategory.setLoading(true)
        ProductService.listCategory({
            page,
            ...search.formData,
        }).then((resp) => {
            tableCategory.setData(resp.message.data)
            tableCategory.setLinks(resp.message.links)
            tableCategory.setLoading(false)
        })
    }, [search.formData])

    const resetAttribute = () => {
        setAttributes([])
        attrForm.setFormData(null)
    }

    const submit = useCallback(() => {
        form.setLoading(true)
        form.setFormErrors(null)

        ProductService.addCategory({
            ...form.formData,
            attributes: attributes.map((item) => ({
                ...item,
                value: attrForm.formData?.[item.key] ?? '',
            })),
        })
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('Category has been created'),
                })
                setShowAdd(false)
                getCategorires(0)
                resetAttribute()
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
    }, [form.formData, attributes, attrForm.formData])

    const update = useCallback(() => {
        form.setFormErrors(null)
        form.setLoading(true)

        ProductService.updateCategory({
            ...form.formData,
            attributes: attributes.map((item) => ({
                ...item,
                value: attrForm.formData?.[item.key] ?? '',
            })),
        })
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('Category has been updated'),
                })
                setShowAdd(false)
                getCategorires(0)
                resetAttribute()
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
    }, [form.formData, attributes, attrForm.formData])

    const destroy = useCallback((row) => {
        ProductService.deleteCategory(row).then(() => {
            openPopup({
                type: 'success',
                message: t('Category has been deleted'),
            })
            getCategorires()
        })
    }, [])

    const view = useCallback(() => {
        ProductService.viewCategory().then((resp) => {
            form.setHookRender(resp.message?.form)
            search.setHookRender(resp.message?.search)
            tableCategory.addColums(resp.message?.index, (item, data) => (
                <RenderFieldTableByList item={item} data={data} />
            ))
        })
    }, [])

    const handleDelete = useCallback((row) => {
        openPopup({
            type: 'warning',
            message: t('Are you sure to delete?'),
            onConfirm: () => destroy(row),
        })
    }, [])

    const handleEdit = useCallback(
        (row) => {
            form.setIsEdit(true)
            form.setFormData(row)
            setShowAdd(true)

            row.attributes?.forEach((item) => {
                setAttributes((prev) => [...prev, item])
                attrForm.handleChangeByKey(item.key, item.value)
            })
        },
        [attrForm]
    )

    const addAttribute = () => {
        if (!attrAddForm.formData?.type) {
            return openPopup({
                type: 'error',
                message: t('You are not select type'),
            })
        }

        if (!attrAddForm.formData?.key) {
            return openPopup({
                type: 'error',
                message: t('You are not insert attribute name'),
            })
        }

        if (attrAddForm.formData.key.length >= 50) {
            return openPopup({
                type: 'error',
                message: t('Attribute name should not greater than 50 characters'),
            })
        }

        setAttributes((prev) => {
            if (prev.find((item) => item.key === attrAddForm.formData.key)) {
                openPopup({
                    type: 'error',
                    message: t('This attribute has been used'),
                })
                return prev
            }
            if (prev.length >= 10) {
                openPopup({
                    type: 'error',
                    message: t('You have reached your limit'),
                })
                return prev
            }
            return [...prev, attrAddForm.formData]
        })

        attrAddForm.setFormData(null)
    }

    const removeAttribute = (attr) => {
        setAttributes((prev) => prev.filter((item) => item.key !== attr.key))
    }

    useEffect(() => {
        getCategorires()
        view()

        tableCategory.setColums([
            { label: t('ID'), key: 'id' },
            { label: t('Name'), key: 'name' },
            { label: t('Tax (%)'), key: 'tax' },
            { label: t('Description'), key: 'description' },
            {
                label: t('Created by'),
                key: 'created_by_name',
                render: (name) => <span className="badge bg-primary">{name}</span>,
            },
        ])
    }, [])

    const hasPermission = useMemo(
        () => business.role === 'manager' || business.role === 'admin',
        [business]
    )

    return (
        <div className="mt-3">
            <CommonDataTableV2
                loading={tableCategory.loading}
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
                callback={getCategorires}
                add={
                    roles?.includes(PERMISSIONS.CATEGORY_PRODUCT.CREATE) ? () => {
                              setShowAdd(true)
                              form.setIsEdit(false)
                          } : null
                }
                columns={tableCategory.colums}
                data={tableCategory.data}
                links={tableCategory.links}
                onEdit={roles?.includes(PERMISSIONS.CATEGORY_PRODUCT.UPDATE) ? handleEdit : null}
                onDelete={roles?.includes(PERMISSIONS.CATEGORY_PRODUCT.DELETE) ? handleDelete : null}
            />

            {showAdd && (
                <PopupLayout
                    loading={form.loading}
                    confirmText={t('Save')}
                    onConfirm={form.isEdit ? update : submit}
                    onClose={() => {
                        setShowAdd(false)
                        resetAttribute()
                    }}
                    title={
                        form.isEdit
                            ? t('Update Category')
                            : t('Add category')
                    }
                >
                    <div className="form-group">
                        <label>{t('Name')}</label>
                        <InputForm
                            name="name"
                            value={form.formData?.name}
                            handleChange={form.handleChange}
                            errorMessage={form.formErrors?.name}
                        />
                    </div>

                    <div className="form-group mt-3">
                        <label>{t('Tax (%)')}</label>
                        <InputForm
                            name="tax"
                            type="number"
                            value={form.formData?.tax}
                            handleChange={form.handleChange}
                            errorMessage={form.formErrors?.tax}
                        />
                    </div>

                    <div className="form-group mt-3">
                        <label>{t('Description')}</label>
                        <TextArea
                            name="description"
                            value={form.formData?.description}
                            handleChange={form.handleChange}
                            errorMessage={form.formErrors?.description}
                        />
                    </div>

                    {attributes.map((item, index) => (
                        <div className="form-group mt-3" key={index}>
                            <label className="text-capitalize">{item.key}</label>
                            <div className="row align-items-center">
                                <div className="col-11">
                                    {item.type !== 'textarea' ? (
                                        <InputForm
                                            name={item.key}
                                            type={item.type}
                                            value={attrForm.formData?.[item.key]}
                                            handleChange={attrForm.handleChange}
                                            errorMessage={
                                                form.formErrors?.[
                                                    `attributes.${index}.value`
                                                ]
                                            }
                                        />
                                    ) : (
                                        <TextArea
                                            name={item.key}
                                            value={attrForm.formData?.[item.key]}
                                            handleChange={attrForm.handleChange}
                                            errorMessage={
                                                form.formErrors?.[
                                                    `attributes.${index}.value`
                                                ]
                                            }
                                        />
                                    )}
                                </div>
                                <div
                                    className="col-1"
                                    onClick={() => removeAttribute(item)}
                                >
                                    <i className="bi bi-x"></i>
                                </div>
                            </div>
                        </div>
                    ))}

                    {form.hookRender.map((item, index) => (
                        <div className="form-group mt-3" key={index}>
                            <RenderFormFieldByList item={item} form={form} />
                        </div>
                    ))}

                    <div className="row mt-3">
                        <h4 className="h6">{t('Attributes')}</h4>
                        <p>
                            {t(
                                'You can add maximum 10 custom fields for products in this category'
                            )}
                        </p>

                        <div className="col-6">
                            <label>{t('Attribute name')}</label>
                            <InputForm
                                name="key"
                                value={attrAddForm.formData?.key}
                                handleChange={attrAddForm.handleChange}
                            />
                        </div>

                        <div className="col-3">
                            <label>{t('Type')}</label>
                            <Select
                                name="type"
                                value={attrAddForm.formData?.type}
                                handleChange={attrAddForm.handleChange}
                                options={[
                                    { value: 'number', label: t('Number') },
                                    { value: 'text', label: t('Character') },
                                    { value: 'textarea', label: t('Long text') },
                                    { value: 'date', label: t('Date') },
                                ]}
                            />
                        </div>

                        <div className="col-3">
                            <PrimaryButton
                                loading={form.loading}
                                onClick={addAttribute}
                                label={t('Add')}
                            />
                        </div>
                    </div>
                </PopupLayout>
            )}
        </div>
    )
}
