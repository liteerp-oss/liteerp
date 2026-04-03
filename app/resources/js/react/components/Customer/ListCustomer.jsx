import React, { useCallback, useEffect, useState } from 'react'
import useTable from '../../libraries/handleTable'
import { useForm } from '../../libraries/handleInput'
import { usePopup } from '../popups/PopupContext'
import { PopupLayout } from '../../layouts/PopupLayout'
import CustomerService from '../../services/CustomerService'
import CustomerForm from './ListCustomer/CustomerForm'
import StatusBadge from '../StatusBadge'
import RenderFormTableByList from '../RenderFieldTableByList'
import { useI18n } from '../../../i18n/useI18n'
import CommonDataTableV2 from '../CommonDataTableV2'

export default function ListCustomer() {
    const { t, lang } = useI18n()
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
            CustomerService.list({
                ...search.formData,
                page,
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

    const renderForm = useCallback(() => {
        CustomerService.view().then((resp) => {
            form.setHookRender(resp.message?.form)
            table.addColums(resp.message.index, (item, data) => (
                <RenderFormTableByList item={item} data={data} />
            ))
            search.setHookRender(resp.message?.search ?? [])
        })
    }, [table])

    useEffect(() => {
        table.setColums([
            { label: t('ID'), key: 'id' },
            { label: t('Name'), key: 'name' },
            { label: t('Email'), key: 'email' },
            { label: t('Phone'), key: 'phone' },
            { label: t('Total orders'), key: 'total_order' },
            { label: t('Group'), key: 'group_name' },
            { label: t('National Id'), key: 'national_id' },
            {
                label: t('Type'),
                key: 'type',
                render: (value) => (
                    <StatusBadge status={value}/>
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
    }, [lang])

    return (
        <div>
            <CommonDataTableV2
                add={ () => setShowAdd(true)}
                loading={table.loading}
                callback={getCustomers}
                columns={table.colums}
                data={table.data}
                links={table.links}
                onEdit={handleEdit}
                onDelete={ handleDelete}
                search={search}
                type={'customer'}
            />

            {showAdd && (
                <PopupLayout
                    loading={form.loading}
                    confirmText={t('Save changes')}
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
