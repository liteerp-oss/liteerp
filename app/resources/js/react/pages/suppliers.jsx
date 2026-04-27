import React, { useCallback, useEffect, useState } from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import { PopupLayout } from '../layouts/PopupLayout'
import { InputForm } from '../components/UI/Input/InputForm'
import SupplierService from '../services/SupplierService'
import { useForm } from '../libraries/handleInput'
import useTable from '../libraries/handleTable'
import TextArea from '../components/UI/Input/Textarea'
import { usePopup } from '../components/popups/PopupContext'
import PageHead from '../components/PageHead'
import { substring } from '../libraries/common'
import StatusBadge from '../components/StatusBadge'
import RenderFormFieldByList from '../components/RenderFormFieldByList'
import RenderFormTableByList from '../components/RenderFieldTableByList'
import { useI18n } from '../../i18n/useI18n'
import CommonDataTableV2 from '../components/CommonDataTableV2'
import PermissionNode from '@/core/PermissionNode'
import { useNavigate } from 'react-router-dom'

export default function Suppliers() {
    const permission = new PermissionNode();
    const { t, lang } = useI18n()
    const { openPopup } = usePopup()
    const navigate = useNavigate();
    const [addShow, setAddShow] = useState(false)

    const search = useForm()
    const form = useForm()
    const table = useTable()

    const handleEdit = (row) => {
        form.setFormData(row)
        form.setIsEdit(true)
        setAddShow(true)
    }

    const getSupliers = useCallback(
        (page = 0) => {
            table.setLoading(true)
            SupplierService.list({
                ...search.formData,
                page,
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

    const destroy = useCallback((row) => {
        SupplierService.delete(row)
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('Supplier has been deleted'),
                })
                getSupliers()
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

    const submit = useCallback(() => {
        form.setLoading(true)
        form.setFormErrors(null)

        SupplierService.add(form.formData)
            .then(() => {
                setAddShow(false)
                getSupliers()
                form.setLoading(false)
                if (permission.fromNode('supplier').getPermission('index')) {
                    openPopup({
                        type: 'success',
                        message: t('create_success'),
                        confirmText: t('go_to_purchase'),
                        onConfirm: () => {
                            navigate('/purchases')
                        }
                    })
                } else {
                    openPopup({
                        type: 'success',
                        message: t('create_success'),
                    })
                }

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
        form.setFormErrors(null)
        form.setLoading(true)

        SupplierService.update(form.formData)
            .then(() => {
                setAddShow(false)
                getSupliers()
                form.setLoading(false)
                if (permission.fromNode('supplier').getPermission('index')) {
                    openPopup({
                        type: 'success',
                        message: t('update_success'),
                        confirmText: t('go_to_purchase'),
                        onConfirm: () => {
                            navigate('/purchases')
                        }
                    })
                } else {
                    openPopup({
                        type: 'success',
                        message: t('update_success'),
                    })
                }
                
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
        SupplierService.view()
            .then((resp) => {
                form.setHookRender(resp.message?.form)
                search.setHookRender(resp.message?.search)
                table.addColums(resp.message.index, (item, data) => (
                    <RenderFormTableByList item={item} data={data} />
                ))
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
        getSupliers()
        getView()
        table.setColums([
            { label: t('ID'), key: 'id' },
            {
                label: t('Unit Name'),
                key: 'unit_name',
                render: (name) => <span>{substring(name, 0, 30)}</span>,
            },
            { label: t('Email'), key: 'email' },
            { label: t('Phone Number'), key: 'phone' },
            {
                label: t('Address'),
                key: 'address',
                render: (address) => (
                    <span>{substring(address, 0, 30)}</span>
                ),
            },
            { label: t('Tax Code'), key: 'tax_code' },
            { label: t('Bank Name'), key: 'bank_name' },
            { label: t('Bank Account'), key: 'bank_account' },
            {
                label: t('Website'),
                key: 'website',
                render: (website) => (
                    <span>{substring(website, 0, 30)}</span>
                ),
            },
            {
                label: t('Status'),
                key: 'active',
                render: (value) => (
                    <StatusBadge
                        status={value ? 'active' : 'inactive'}
                    />
                ),
            },
        ])
    }, [lang])

    return (
        <DashboardLayout>
            <PageHead
                containerClass="mx-4"
                title={t('Suppliers')}
                subtitle={t('supplier_desc')}
            />

            <div className="m-4">
                <CommonDataTableV2
                    columns={table.colums}
                    loading={table.loading}
                    data={table.data}
                    links={table.links}
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
                    add={() => {
                        setAddShow(true)
                        form.setIsEdit(false)
                    }}
                    onEdit={handleEdit}
                    onDelete={handleDelete}
                    callback={getSupliers}
                    type={'supplier'}
                />

                {addShow && (
                    <PopupLayout
                        loading={form.loading}
                        title={
                            form.isEdit
                                ? t('Update supplier')
                                : t('Add new supplier')
                        }
                        onConfirm={form.isEdit ? update : submit}
                        onClose={() => {
                            setAddShow(false)
                            form.setIsEdit(false)
                        }}
                    >
                        <div className="form-group">
                            <InputForm
                                name="unit_name"
                                value={form.formData?.unit_name}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.unit_name}
                                placeholder={t('Unit name')}
                                label={t("Name")}
                                required={true}
                            />
                        </div>

                        <div className="row mt-1">
                            <div className="form-group col-6">
                                <InputForm
                                    name="email"
                                    value={form.formData?.email}
                                    handleChange={form.handleChange}
                                    errorMessage={form.formErrors?.email}
                                    label={t("Email")}
                                    required={false}
                                />
                            </div>

                            <div className="form-group col-6">
                                <InputForm
                                    name="phone"
                                    value={form.formData?.phone}
                                    handleChange={form.handleChange}
                                    errorMessage={form.formErrors?.phone}
                                    label={t("Phone")}
                                    required={true}
                                />
                            </div>
                        </div>

                        <div className="form-group mt-1">
                            <TextArea
                                name="address"
                                value={form.formData?.address}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.address}
                                label={t("Address")}
                                required={true}
                            />
                        </div>

                        <div className="form-group mt-1">
                            <InputForm
                                name="tax_code"
                                value={form.formData?.tax_code}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.tax_code}
                                label={t("Tax code")}
                                required={true}
                            />
                        </div>

                        <div className="row mt-1">
                            <div className="form-group col-6">
                                <InputForm
                                    name="bank_name"
                                    value={form.formData?.bank_name}
                                    handleChange={form.handleChange}
                                    errorMessage={form.formErrors?.bank_name}
                                    label={t("Bank name")}
                                    required={false}
                                />
                            </div>

                            <div className="form-group col-6">
                                <InputForm
                                    name="bank_account"
                                    value={form.formData?.bank_account}
                                    handleChange={form.handleChange}
                                    errorMessage={form.formErrors?.bank_account}
                                    label={t("Bank account")}
                                    required={false}
                                />
                            </div>
                        </div>

                        <div className="form-group mt-1">
                            <InputForm
                                name="website"
                                value={form.formData?.website}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.website}
                                label={t("Website")}
                                required={false}
                            />
                        </div>

                        <div className="form-group mt-1">
                            <TextArea
                                name="note"
                                value={form.formData?.note ?? ''}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.note}
                                label={t("Note")}
                                required={false}
                            />
                        </div>

                        <div className="form-group mt-3">
                            <InputForm
                                width={20}
                                type="checkbox"
                                name="active"
                                value={form.formData?.active}
                                handleChange={form.handleChange}
                                label={t("Active")}
                                required={false}
                            />
                            <span className="d-block mt-1">
                                {t(
                                    'supplier_checkbox'
                                )}
                            </span>
                        </div>

                        {form.hookRender.map((item, index) => (
                            <div key={index}>
                                <RenderFormFieldByList
                                    form={form}
                                    item={item}
                                />
                            </div>
                        ))}
                    </PopupLayout>
                )}
            </div>
        </DashboardLayout>
    )
}
