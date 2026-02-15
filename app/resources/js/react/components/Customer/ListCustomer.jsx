import React, { useCallback, useEffect, useState } from 'react'
import CommonDataTable from '../CommonDataTable'
import useTable from '../../libraries/handleTable'
import { useForm } from '../../libraries/handleInput'
import { usePopup } from '../popups/PopupContext'
import { Select } from '../UI/Input/Select'
import SearchInput from '../UI/Input/SearchInput'
import { PopupLayout } from '../../layouts/PopupLayout'
import CustomerService from '../../services/CustomerService'
import CustomerForm from './ListCustomer/CustomerForm'
import StatusBadge from '../StatusBadge'
import RenderFormTableByList from '../RenderFieldTableByList'
import ButtonPrimary from '../../components/UI/Buttons/PrimaryButton'
import { RenderTableSearch } from '../RenderTableSearch'
import { useI18n } from '../../../i18n/useI18n'
import { useSelector } from 'react-redux'
import PERMISSIONS from '../../common/permission'
import CommonDataTableV2 from '../CommonDataTableV2'

export default function ListCustomer() {
    const { t } = useI18n()
    const roles = useSelector((state) => state.businessRole.role);
    const table = useTable()
    const search = useForm()
    const form = useForm()
    const { openPopup } = usePopup()

    const [showAdd, setShowAdd] = useState(false)

    const handleEdit = (row) => {
        form.setIsEdit(true)
        form.setFormData(row)
        setShowAdd(true)
    }

    const submit = useCallback(() => {
        form.setLoading(true)
        form.setFormErrors(null)

        CustomerService.add(form.formData)
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('Customer has been created'),
                })
                setShowAdd(false)
                getCustomers()
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
        form.setFormErrors(null)
        form.setLoading(true)

        CustomerService.update(form.formData)
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('Customer has been updated'),
                })
                setShowAdd(false)
                getCustomers()
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
        CustomerService.delete(row)
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('Customer has been deleted'),
                })
                getCustomers()
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

    const getCustomers = useCallback(
        (page = 0) => {
            table.setLoading(true)
            CustomerService.list(search.formData)
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

    const renderForm = useCallback(() => {
        CustomerService.view().then((resp) => {
            form.setHookRender(resp.message?.form)
            table.addColums(resp.message.index, (item, data) => (
                <RenderFormTableByList item={item} data={data} />
            ))
            search.setHookRender(resp.message?.search ?? [])
        })
    }, [])

    useEffect(() => {
        table.setColums([
            { label: t('ID'), key: 'id' },
            { label: t('Name'), key: 'name' },
            { label: t('Email'), key: 'email' },
            { label: t('Phone'), key: 'phone' },
            { label: t('Total orders'), key: 'total_order' },
            { label: t('Group'), key: 'group_name' },
            {
                label: t('Type'),
                key: 'type',
                render: (value) => (
                    <span
                        className={
                            'badge text-uppercase ' +
                            (value === 'company'
                                ? 'bg-primary'
                                : 'bg-secondary')
                        }
                    >
                        {t(value === 'company' ? 'Company' : 'Individual')}
                    </span>
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

        renderForm()
        getCustomers()
    }, [])

    return (
        <div>
            <CommonDataTableV2
                add={ roles?.includes(PERMISSIONS.CUSTOMER.CREATE) 
                    ? () => setShowAdd(true)
                    : null}
                loading={table.loading}
                callback={getCustomers}
                columns={table.colums}
                data={table.data}
                links={table.links}
                onEdit={roles?.includes(PERMISSIONS.CUSTOMER.UPDATE) ? handleEdit : null}
                onDelete={ roles?.includes(PERMISSIONS.CUSTOMER.DELETE) ? handleDelete : null}
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
            />

            {showAdd && (
                <PopupLayout
                    loading={form.loading}
                    confirmText={t('Save')}
                    onConfirm={form.isEdit ? update : submit}
                    onClose={() => {
                        setShowAdd(false)
                        form.setIsEdit(false)
                    }}
                    title={
                        form.isEdit
                            ? t('Update customer')
                            : t('Add customer')
                    }
                >
                    <CustomerForm form={form} />
                </PopupLayout>
            )}
        </div>
    )
}
