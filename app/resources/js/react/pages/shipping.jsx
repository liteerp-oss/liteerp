import React, { useCallback, useEffect, useState } from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import { PopupLayout } from '../layouts/PopupLayout'
import { InputForm } from '../components/UI/Input/InputForm'
import ShippingService from '../services/ShippingService'
import { usePopup } from '../components/popups/PopupContext'
import useTable from '../libraries/handleTable'
import { useForm } from '../libraries/handleInput'
import PageHead from '../components/PageHead'
import FlatIcon32 from '../components/UI/FlatIcons/FlatIcon32'
import UploadImage from '../components/UI/Input/UploadImage'
import RenderFormTableByList from '../components/RenderFieldTableByList'
import RenderFormFieldByList from '../components/RenderFormFieldByList'
import { useI18n } from '../../i18n/useI18n'
import CommonDataTableV2 from '../components/CommonDataTableV2'

export default function Shipping() {
    const { t, lang } = useI18n()
    const { openPopup } = usePopup()
    const table = useTable()
    const form = useForm()
    const search = useForm()
    const [showAdd, setShowAdd] = useState(false)
    const handEdit = (row) => {
        form.setFormData(row)
        form.setIsEdit(true)
        setShowAdd(true)
    }

    const submit = useCallback(() => {
        form.setLoading(true)
        form.setFormErrors(null)

        ShippingService.add(form.formData)
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('Created successfully'),
                })
                getShippings()
                setShowAdd(false)
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

        ShippingService.update(form.formData)
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('Updated successfully'),
                })
                getShippings()
                setShowAdd(false)
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

    const getShippings = useCallback(
        (page = 0) => {
            table.setLoading(true)
            ShippingService.list({
                page,
                ...search.formData,
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
        [search.formData]
    )

    const getView = useCallback(() => {
        ShippingService.view()
            .then((resp) => {
                table.addColums(resp.message.index, (item, data) => (
                    <RenderFormTableByList item={item} data={data} />
                ))
                form.setHookRender(resp.message.form)
                search.setHookRender(resp.message.search)
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

    const destroy = useCallback((row) => {
        ShippingService.delete(row)
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('Deleted successfully'),
                })
                getShippings()
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

    const handleDelete = (row) => {
        openPopup({
            type: 'warning',
            message: t('Are you sure to delete?'),
            onConfirm: () => destroy(row),
        })
    }

    useEffect(() => {
        table.setColums([
            {
                label: t('Logo'),
                key: 'logo',
                render: (value) =>
                    value ? (
                        <img
                            width={45}
                            height={45}
                            src={value}
                            alt=""
                        />
                    ) : (
                        <FlatIcon32 />
                    ),
            },
            { label: t('ID'), key: 'id' },
            { label: t('Name'), key: 'name' },
            { label: t('Code'), key: 'code' },
            {
                label: t('Status'),
                key: 'active',
                render: (active) =>
                    active ? (
                        <span className="badge bg-success">
                            {t('Working')}
                        </span>
                    ) : (
                        <span className="badge bg-secondary">
                            {t('Stopped')}
                        </span>
                    ),
            },
        ])
        getShippings()
        getView()
    }, [lang])

    return (
        <DashboardLayout>
            <div>
                <PageHead
                    title={t('Shippings')}
                    subtitle={t(
                        'Manage shipping service providers'
                    )}
                />

                <div className="container mt-4">
                    <CommonDataTableV2
                        add={() => {
                            setShowAdd(true)
                            form.setIsEdit(false)
                        }}
                        loading={table.loading}
                        callback={getShippings}
                        data={table.data}
                        links={table.links}
                        columns={table.colums}
                        onEdit={handEdit}
                        onDelete={handleDelete}
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
                            }, {
                                key: "keywords",
                                placeholder: t("Keywords"),
                                type: "text",
                                label: t("Search"),
                                col: "col-6"
                            }]
                        }}
                        search={search}
                        type={'shipping'}
                    />
                </div>

                {showAdd && (
                    <PopupLayout
                        loading={form.loading}
                        confirmText={t('Save')}
                        onClose={() => {
                            setShowAdd(false)
                            form.setIsEdit(false)
                        }}
                        onConfirm={
                            form.isEdit ? update : submit
                        }
                        title={
                            form.isEdit
                                ? t('Update shipping unit')
                                : t('Add shipping unit')
                        }
                    >
                        <div>
                            <div className="form-group mt-3">
                                <InputForm
                                    name="name"
                                    handleChange={
                                        form.handleChange
                                    }
                                    errorMessage={
                                        form.formErrors?.name
                                    }
                                    value={form.formData?.name}
                                    placeholder={t('Name')}
                                    required={true}
                                    label={t('Name')}
                                />
                            </div>

                            <div className="form-group mt-3">
                                <InputForm
                                    name="code"
                                    handleChange={
                                        form.handleChange
                                    }
                                    errorMessage={
                                        form.formErrors?.code
                                    }
                                    value={form.formData?.code}
                                    placeholder={t('Code')}
                                    required={true}
                                    label={t('Code')}
                                />
                            </div>

                            <div className="form-group mt-3">
                                <UploadImage
                                    name="logo"
                                    handleChangeByKey={
                                        form.handleChangeByKey
                                    }
                                    errorMessage={
                                        form.formErrors?.logo
                                    }
                                    value={form.formData?.logo}
                                    required={false}
                                    label={t('Logo')}
                                />
                            </div>

                            <div className="form-group mt-3">
                                
                                <InputForm
                                    width={20}
                                    name="active"
                                    handleChange={
                                        form.handleChange
                                    }
                                    value={form.formData?.active}
                                    errorMessage={
                                        form.formErrors?.active
                                    }
                                    type="checkbox"
                                    label={t('Active')}
                                />
                                <label>
                                    {t(
                                        'This shipping provider is active'
                                    )}
                                </label>
                            </div>

                            {form.hookRender.map(
                                (item, index) => (
                                    <div
                                        className="form-group mt-3"
                                        key={index}
                                    >
                                        <RenderFormFieldByList
                                            item={item}
                                            form={form}
                                        />
                                    </div>
                                )
                            )}
                        </div>
                    </PopupLayout>
                )}
            </div>
        </DashboardLayout>
    )
}
