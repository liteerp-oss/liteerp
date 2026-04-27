import React, { useCallback, useEffect, useMemo, useState } from 'react'
import ProductService from '../../services/ProductService'
import { useForm } from '../../libraries/handleInput'
import useTable from '../../libraries/handleTable'
import { PopupLayout } from '../../layouts/PopupLayout'
import { InputForm } from '../UI/Input/InputForm'
import TextArea from '../UI/Input/Textarea'
import { usePopup } from '../popups/PopupContext'
import RenderFieldTableByList from '../RenderFieldTableByList'
import RenderFormFieldByList from '../RenderFormFieldByList'
import { useI18n } from '../../../i18n/useI18n'
import CommonDataTableV2 from '../CommonDataTableV2'
import { useNavigate } from 'react-router-dom'
import PermissionNode from '@/core/PermissionNode'

export default function Category() {
    const permission = new PermissionNode();
    const { t, lang } = useI18n()
    const navigate = useNavigate();
    const [attributes, setAttributes] = useState([])
    const [showAdd, setShowAdd] = useState(false)
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
                if(permission.fromNode('categoryproduct').getPermission('index')) {
                    openPopup({
                        type: 'success',
                        message: t('create_success'),
                        confirmText: t('go_to_product'),
                        onConfirm: () => {
                            navigate('/products')
                        }
                    })    
                } else {
                    openPopup({
                        type: 'success',
                        message: t('create_success')
                    })    
                }
                
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
                if(permission.fromNode('categoryproduct').getPermission('index')) {
                    openPopup({
                        type: 'success',
                        message: t('update_success'),
                        confirmText: t('go_to_product'),
                        onConfirm: () => {
                            navigate('/products')
                        }
                    })
                } else {
                    openPopup({
                        type: 'success',
                        message: t('update_success')
                    })
                }
                
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
    }, [lang])

    return (
        <div className="mt-3">
            <CommonDataTableV2
                loading={tableCategory.loading}
                search={search}
                callback={getCategorires}
                add={() => {
                    setShowAdd(true)
                    form.setIsEdit(false)
                }}
                columns={tableCategory.colums}
                data={tableCategory.data}
                links={tableCategory.links}
                onEdit={handleEdit}
                onDelete={handleDelete}
                type={'categoryproduct'}
            />

            {showAdd && (
                <PopupLayout
                    loading={form.loading}
                    confirmText={t('Save changes')}
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
                        <InputForm
                            name="name"
                            value={form.formData?.name}
                            handleChange={form.handleChange}
                            errorMessage={form.formErrors?.name}
                            required={true}
                            label={t('Name')}
                        />
                    </div>

                    <div className="form-group mt-3">
                        <InputForm
                            name="tax"
                            type="number"
                            value={form.formData?.tax}
                            handleChange={form.handleChange}
                            errorMessage={form.formErrors?.tax}
                            required={true}
                            label={t('Tax') + ' (%)'}
                        />
                    </div>

                    <div className="form-group mt-3">
                        <TextArea
                            name="description"
                            value={form.formData?.description}
                            handleChange={form.handleChange}
                            errorMessage={form.formErrors?.description}
                            required={true}
                            label={t('Description')}
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
