import React, { useCallback, useEffect, useState } from 'react'
import CommonDataTable from '../CommonDataTable'
import useTable from '../../libraries/handleTable'
import { useForm } from '../../libraries/handleInput'
import { usePopup } from '../popups/PopupContext'
import SearchInput from '../UI/Input/SearchInput'
import { PopupLayout } from '../../layouts/PopupLayout'
import { InputForm } from '../UI/Input/InputForm'
import CustomerGroupService from '../../services/CustomerGroupService'
import { useI18n } from '../../../i18n/useI18n'
import { useSelector } from 'react-redux'
import PERMISSIONS from '../../common/permission'
import CommonDataTableV2 from '../CommonDataTableV2'

export default function ListGroup() {
    const { t } = useI18n()
    const roles = useSelector((state) => state.businessRole.role);
    const table = useTable()
    const search = useForm()
    const form = useForm()
    const { openPopup } = usePopup()
    const [showAdd, setShowAdd] = useState(false)

    const columns = [
        { label: t('ID'), key: 'id' },
        { label: t('Name'), key: 'name' },
    ]

    const handleEdit = (row) => {
        form.setIsEdit(true)
        form.setFormData(row)
        setShowAdd(true)
    }

    const submit = useCallback(() => {
        form.setFormErrors(null)
        form.setLoading(true)

        CustomerGroupService.add(form.formData)
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('Group has been created'),
                })
                setShowAdd(false)
                getGroup()
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

        CustomerGroupService.update(form.formData)
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('Group has been updated'),
                })
                setShowAdd(false)
                getGroup()
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

    const getGroup = useCallback(
        (page = 0) => {
            table.setLoading(true)
            CustomerGroupService.list({
                keywords: search.formData?.keywords ?? '',
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

    const destroy = useCallback((row) => {
        CustomerGroupService.delete(row)
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('Group has been deleted'),
                })
                getGroup()
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
        getGroup()
    }, [])

    return (
        <div>
            <CommonDataTableV2
                add={ roles?.includes(PERMISSIONS.CUSTOMER_GROUP.CREATE) ? () => setShowAdd(true) : null}
                loading={table.loading}
                callback={getGroup}
                columns={columns}
                data={table.data}
                links={table.links}
                onEdit={roles?.includes(PERMISSIONS.CUSTOMER_GROUP.UPDATE) ?handleEdit : null}
                onDelete={roles?.includes(PERMISSIONS.CUSTOMER_GROUP.DELETE) ? handleDelete : null}
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
                    confirmText={t('Save changes')}
                    onConfirm={form.isEdit ? update : submit}
                    onClose={() => {
                        setShowAdd(false)
                        form.setIsEdit(false)
                    }}
                    title={
                        form.isEdit
                            ? t('Update group')
                            : t('Add group')
                    }
                >
                    <div className="form-group">
                        <InputForm
                            name="name"
                            handleChange={form.handleChange}
                            value={form.formData?.name}
                            errorMessage={form.formErrors?.name}
                            placeholder={t('Group name')}
                            required={true}
                            label={t('Name')}
                        />
                    </div>
                </PopupLayout>
            )}
        </div>
    )
}
