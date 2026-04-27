import React, { useCallback, useEffect, useState } from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import { PopupLayout } from '../layouts/PopupLayout'
import { InputForm } from '../components/UI/Input/InputForm'
import WarehouseService from '../services/WarehouseService'
import { usePopup } from '../components/popups/PopupContext'
import { useForm } from '../libraries/handleInput'
import useTable from '../libraries/handleTable'
import PageHead from '../components/PageHead'
import { useI18n } from '../../i18n/useI18n'
import { useSelector } from 'react-redux'
import PERMISSIONS from '../common/permission'
import CommonDataTableV2 from '../components/CommonDataTableV2'

export default function Warehouse() {
    const { t, lang } = useI18n()
    const { openPopup } = usePopup()
    const roles = useSelector((state) => state.businessRole.role);
    const table = useTable()
    const form = useForm()
    const search = useForm(null)

    const [showPopup, setShowPopup] = useState(false)

    const getList = useCallback(
        (page = 0) => {
            table.setLoading(true)
            WarehouseService.list({
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
    const getView = useCallback(() => {
        WarehouseService.view()
            .then((resp) => {
                form.setHookRender(resp.message?.form)
                search.setHookRender(resp.message?.search)
                table.addColums(resp.message.index, (item, data) => (
                    <RenderFormTableByList item={item} data={data} />
                ))
            })
            .catch((error) => {
                if (error.response?.data?.errors) {
                    form.setFormErrors(error.response.data.errors)
                }
                form.setLoading(false)
            })
    }, []);
    useEffect(() => {
        getView();
        table.setColums([
            { label: t('ID'), key: 'id' },
            { label: t('Name'), key: 'name' },
            { label: t('Address'), key: 'address' },
            {
                label: t('Status'),
                key: 'active',
                render: (value) => (
                    <span
                        className={`badge rounded-pill px-3 py-2 ${value === 1
                            ? 'bg-success bg-opacity-75'
                            : 'bg-secondary'
                            }`}
                    >
                        {value === 1 ? t('Active') : t('Inactive')}
                    </span>
                ),
            },
        ])
        getList()
    }, [lang])

    const submit = useCallback(() => {
        form.setLoading(true)
        form.setFormErrors(null)

        WarehouseService.add(form.formData)
            .then(() => {
                getList()
                openPopup({
                    type: 'success',
                    message: t('Warehouse has been created'),
                })
                setShowPopup(false)
                form.setFormData(null)
                form.setLoading(false)
            })
            .catch((error) => {
                if (error.response?.data?.errors) {
                    form.setFormErrors(error.response.data.errors)
                }
                form.setLoading(false)
            })
    }, [form])

    const edit = useCallback(() => {
        form.setFormErrors(null)
        form.setLoading(true)

        WarehouseService.update(form.formData)
            .then(() => {
                getList()
                openPopup({
                    type: 'success',
                    message: t('Warehouse has been updated'),
                })
                setShowPopup(false)
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
    }, [form])

    const destroy = useCallback((row) => {
        WarehouseService.delete(row)
            .then(() => {
                getList()
                openPopup({
                    type: 'success',
                    message: t('Warehouse has been deleted'),
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

    const handleDelete = useCallback((row) => {
        openPopup({
            type: 'warning',
            message: t('Are you sure to delete?'),
            onConfirm: () => destroy(row),
        })
    }, [])

    return (
        <DashboardLayout>
            <PageHead
                containerClass="mx-4"
                title={t('Warehouse')}
                subtitle={t(
                    'warehouse_desc'
                )}
            />

            <div className="m-4">
                <CommonDataTableV2
                    add={roles?.includes(PERMISSIONS.WAREHOUSE.CREATE) ? () => {
                        setShowPopup(true)
                        form.setIsEdit(false)
                    } : null}
                    loading={table.loading}
                    callback={getList}
                    columns={table.colums}
                    data={table.data}
                    links={table.links}
                    onEdit={(row) => {
                        setShowPopup(true)
                        form.setFormData(row)
                        form.setIsEdit(true)
                    }}
                    onDelete={handleDelete}

                    search={search}
                    type={'warehouse'}
                />
            </div>

            {showPopup && (
                <PopupLayout
                    loading={form.loading}
                    confirmText={t('Save changes')}
                    title={
                        !form.isEdit
                            ? t('New warehouse')
                            : t('Update warehouse')
                    }
                    onClose={() => {
                        setShowPopup(false)
                        form.setIsEdit(false)
                    }}
                    onConfirm={!form.isEdit ? submit : edit}
                >
                    <div className="form-group">
                        <InputForm
                            name="name"
                            value={form.formData?.name}
                            handleChange={form.handleChange}
                            errorMessage={form.formErrors?.name}
                            placeholder={t('Warehouse name')}
                            required={true}
                            label={t('Name')}
                        />
                    </div>

                    <div className="form-group mt-3">
                        <InputForm
                            name="address"
                            value={form.formData?.address}
                            handleChange={form.handleChange}
                            errorMessage={form.formErrors?.address}
                            placeholder={t('Warehouse address')}
                            required={true}
                            label={t('Address')}
                        />
                    </div>

                    <div className="form-group mt-3">
                        <div className="d-flex">
                            <InputForm
                                width={20}
                                type="checkbox"
                                name="active"
                                value={form.formData?.active}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.active}
                                required={false}
                                label={t('Active')}
                            />
                        </div>
                        <i className="">
                            {t(
                                'warehouse_active'
                            )}
                        </i>
                    </div>
                </PopupLayout>
            )}
        </DashboardLayout>
    )
}
