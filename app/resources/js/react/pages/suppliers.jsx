import React, { useCallback, useEffect, useState } from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import CommonDataTable from '../components/CommonDataTable'
import { PopupLayout } from '../layouts/PopupLayout'
import { InputForm } from '../components/UI/Input/InputForm'
import SupplierService from '../services/SupplierService'
import SearchInput from '../components/UI/Input/SearchInput'
import { useForm } from '../libraries/handleInput'
import useTable from '../libraries/handleTable'
import { Select } from '../components/UI/Input/Select'
import TextArea from '../components/UI/Input/Textarea'
import { usePopup } from '../components/popups/PopupContext'
import PageHead from '../components/PageHead'
import { substring } from '../libraries/common'
import StatusBadge from '../components/StatusBadge'
import RenderFormFieldByList from '../components/RenderFormFieldByList'
import PrimaryButton from '../components/UI/Buttons/PrimaryButton'
import RenderFormTableByList from '../components/RenderFieldTableByList'
import { RenderTableSearch } from '../components/RenderTableSearch'
import { useI18n } from '../../i18n/useI18n'
import { useSelector } from 'react-redux'
import PERMISSIONS from '../common/permission'
import CommonDataTableV2 from '../components/CommonDataTableV2'

export default function Suppliers() {
    const { t } = useI18n()
    const roles = useSelector((state) => state.businessRole.role);
    const { openPopup } = usePopup()

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
                openPopup({
                    type: 'success',
                    message: t('Supplier has been added'),
                })
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
                openPopup({
                    type: 'success',
                    message: t('Supplier has been updated'),
                })
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
    }, [])

    return (
        <DashboardLayout>
            <PageHead
                containerClass="mx-4"
                title={t('Suppliers')}
                subtitle={t(
                    'List of suppliers for materials, accessories, and goods.'
                )}
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
                    add={roles?.includes(PERMISSIONS.SUPPLIER.CREATE) ? () => {
                        setAddShow(true)
                        form.setIsEdit(false)
                    } : null}
                    onEdit={roles?.includes(PERMISSIONS.SUPPLIER.UPDATE) ? handleEdit : null}
                    onDelete={roles?.includes(PERMISSIONS.SUPPLIER.DELETE) ? handleDelete : null}
                    callback={getSupliers}
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
                            <label>{t('Name')}</label>
                            <InputForm
                                name="unit_name"
                                value={form.formData?.unit_name}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.unit_name}
                                placeholder={t('Unit name')}
                            />
                        </div>

                        <div className="row mt-1">
                            <div className="form-group col-6">
                                <label>{t('Email')}</label>
                                <InputForm
                                    name="email"
                                    value={form.formData?.email}
                                    handleChange={form.handleChange}
                                    errorMessage={form.formErrors?.email}
                                />
                            </div>

                            <div className="form-group col-6">
                                <label>{t('Phone')}</label>
                                <InputForm
                                    name="phone"
                                    value={form.formData?.phone}
                                    handleChange={form.handleChange}
                                    errorMessage={form.formErrors?.phone}
                                />
                            </div>
                        </div>

                        <div className="form-group mt-1">
                            <label>{t('Address')}</label>
                            <TextArea
                                name="address"
                                value={form.formData?.address}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.address}
                            />
                        </div>

                        <div className="form-group mt-1">
                            <label>{t('Tax code')}</label>
                            <InputForm
                                name="tax_code"
                                value={form.formData?.tax_code}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.tax_code}
                            />
                        </div>

                        <div className="row mt-1">
                            <div className="form-group col-6">
                                <label>{t('Bank name')}</label>
                                <InputForm
                                    name="bank_name"
                                    value={form.formData?.bank_name}
                                    handleChange={form.handleChange}
                                    errorMessage={form.formErrors?.bank_name}
                                />
                            </div>

                            <div className="form-group col-6">
                                <label>{t('Bank account')}</label>
                                <InputForm
                                    name="bank_account"
                                    value={form.formData?.bank_account}
                                    handleChange={form.handleChange}
                                    errorMessage={form.formErrors?.bank_account}
                                />
                            </div>
                        </div>

                        <div className="form-group mt-1">
                            <label>{t('Website')}</label>
                            <InputForm
                                name="website"
                                value={form.formData?.website}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.website}
                            />
                        </div>

                        <div className="form-group mt-1">
                            <label>{t('Note')}</label>
                            <TextArea
                                name="note"
                                value={form.formData?.note ?? ''}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.note}
                            />
                        </div>

                        <div className="form-group mt-1">
                            <label>{t('Active')}</label>
                            <InputForm
                                width={20}
                                type="checkbox"
                                name="active"
                                value={form.formData?.active}
                                handleChange={form.handleChange}
                            />
                            <span className="d-block mt-1">
                                {t(
                                    'If inactive, this supplier cannot be selected in purchases'
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
