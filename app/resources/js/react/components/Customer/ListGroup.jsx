import React, { useCallback, useEffect, useState } from 'react'
import useTable from '../../libraries/handleTable'
import { useForm } from '../../libraries/handleInput'
import { usePopup } from '../popups/PopupContext'
import { PopupLayout } from '../../layouts/PopupLayout'
import { InputForm } from '../UI/Input/InputForm'
import CustomerGroupService from '../../services/CustomerGroupService'
import { useI18n } from '../../../i18n/useI18n'
import CommonDataTableV2 from '../CommonDataTableV2'

export default function ListGroup() {
    const { t } = useI18n()
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
        form.setFormErrors(null)
        form.setLoading(true)

        CustomerGroupService.add(form.formData)
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('create_success')
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
                        message: t('update_success')
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

    const destroy = useCallback((row) => {
        CustomerGroupService.delete(row)
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('delete_success'),
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
    const getView = useCallback(() => {
        CustomerGroupService.view()
            .then((resp) => {
                form.setHookRender(resp.message?.form)
                table.addColums(resp.message.index, (item, data) => (
                    <RenderFormTableByList item={item} data={data} />
                ))
                search.setHookRender(resp.message?.search ?? [])
            })
            .catch((error) => {
                if (error.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data.message,
                    })
                }
            })
    }, []);
    const handleDelete = (row) => {
        openPopup({
            type: 'warning',
            message: t('Are you sure to delete?'),
            onConfirm: () => destroy(row),
        })
    }

    useEffect(() => {
        getGroup()
        table.setColums([
            { label: t('ID'), key: 'id' },
            { label: t('Name'), key: 'name' },
        ])
        getView();
    }, [])

    return (
        <div>
            <CommonDataTableV2
                add={() => setShowAdd(true)}
                loading={table.loading}
                callback={getGroup}
                columns={table.colums}
                data={table.data}
                links={table.links}
                onEdit={handleEdit}
                onDelete={handleDelete}
                search={search}
                type='customergroup'
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
