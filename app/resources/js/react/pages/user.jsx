import React, { useCallback, useEffect, useState } from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import PageHead from '../components/PageHead'
import UserService from '../services/UserService'
import useTable from '../libraries/handleTable'
import { useForm } from '../libraries/handleInput'
import { PopupLayout } from '../layouts/PopupLayout'
import { InputForm } from '../components/UI/Input/InputForm'
import { usePopup } from '../components/popups/PopupContext'
import { useI18n } from '../../i18n/useI18n'
import ContentOnTable from '../components/ContentOnTable'
import CommonDataTableV2 from '../components/CommonDataTableV2'
import SearchSelect from '../components/UI/Input/SearchSelect'
import PermissionGroupService from '../services/PermissionGroupService'
import PermissionGroupUserService from '../services/PermissionGroupUserService'

export default function User() {
    const { t, lang } = useI18n()
    const { openPopup } = usePopup()
    const table = useTable()
    const form = useForm()
    const search = useForm();
    const [showForm, setShowForm] = useState(false);
    const [groups, setGroups] = useState([])
    const getUsers = useCallback(() => {
        table.setLoading(true)
        UserService.list({
            ...search.formData,
            page: 0,
        })
            .then((resp) => {
                table.setLoading(false)
                table.setData(resp.message.data)
                table.setLinks(resp.message.links)
            })
            .catch(() => { })
    }, [search.formData])

    const handleEdit = (row) => {
        form.setIsEdit(true)
        form.setFormData(row)
        setShowForm(true)
    }
    const destroy = useCallback((row) => {
        UserService.delete(row)
            .then((resp) => {
                openPopup({
                    type: 'success',
                    message: t('You has been deleted'),
                })
                getUsers();
            })
            .catch((error) => {
                if (error.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response?.data?.message,
                    })
                }
            })
    }, [])
    const handleDelete = (row) => {
        openPopup({
            type: 'warning',
            message: t('Are you sure to delete?'),
            onConfirm: () => {
                destroy(row);
            }
        })
    }

    const submit = useCallback(() => {
        form.setFormErrors(null)
        form.setLoading(true)
        UserService.add({
            ...form.formData
        })  
            .then(() => {
                form.setFormData(null)
                openPopup({
                    type: 'success',
                    message: t('User added successfully'),
                })
                setShowForm(false)
                getUsers()
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
        UserService.update({
            ...form.formData
        })  
            .then(() => {
                form.setFormData(null)
                openPopup({
                    type: 'success',
                    message: t('User updated successfully'),
                })
                setShowForm(false)
                getUsers()
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

    useEffect(() => {
        table.setColums([
            { key: 'id', label: t('ID') },
            {
                key: 'avatar',
                label: t('Avatar'),
                render: (avatar) => (
                    <img
                        width={50}
                        height={50}
                        src={
                            avatar ??
                            '/assets/icons/avatar-default.png'
                        }
                        alt=""
                    />
                ),
            },
            {
                key: 'bio',
                label: t('Bio'),
                render: (bio) => (
                    <ContentOnTable value={bio} />
                ),
            },
            { key: 'phone', label: t('Phone') },
            { key: 'email', label: t('Email') },
            { key: 'name', label: t('Name') },
            {
                key: 'group',
                label: t('Group'),
                render: (group) => (
                    <span>
                        {t(group)}
                    </span>
                ),
            },
            {
                key: 'last_seen',
                label: t('Last seen'),
            },
        ])
        getUsers()
    }, [lang])

    const getGroup = (search, callback) => {
        PermissionGroupService.list({
            keywords: search,
            page: 0
        })
        .then((resp) => {
            setGroups(resp.message.data)
            callback();
        })
        .catch((error) => {})
    }     

    return (
        <DashboardLayout>
            <div>
                <PageHead
                    title={t('Employees')}
                    subtitle={t(
                        'employee_desc'
                    )}
                />

                <div className="container mt-3">
                    <CommonDataTableV2
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
                        callback={getUsers}
                        search={search}
                        loading={table.loading}
                        add={() => setShowForm(true)}
                        columns={table.colums}
                        data={table.data}
                        links={table.links}
                        onDelete={handleDelete}
                        type={'user'}
                    />
                </div>

                {showForm && (
                    <PopupLayout
                        loading={form.loading}
                        onConfirm={form.isEdit ? update : submit}
                        onClose={() => {
                            setShowForm(false)
                            form.setIsEdit(false)
                        }}
                        title={
                            !form.isEdit
                                ? t('Add staff')
                                : t('Update staff')
                        }
                    >
                        <div>
                            <div className="form-group">
                                <InputForm
                                    name="email"
                                    errorMessage={
                                        form.formErrors?.email
                                    }
                                    value={form.formData?.email}
                                    handleChange={
                                        form.handleChange
                                    }
                                    required={true}
                                    label={t('Email')}
                                />
                            </div>

                            <div className="form-group mt-2">
                                <SearchSelect
                                    name="group_id"
                                    errorMessage={
                                        form.formErrors?.group_id
                                    }
                                    value={form.formData?.group_id}
                                    changeValue={form.handleChangeByKey}
                                    options={groups.map((group) => ({
                                        value: group.id,
                                        label: t(group.name)
                                    }))}
                                    required={true}
                                    label={t('Group')}
                                    search={getGroup}
                                />
                            </div>
                        </div>
                    </PopupLayout>
                )}
            </div>
        </DashboardLayout>
    )
}
