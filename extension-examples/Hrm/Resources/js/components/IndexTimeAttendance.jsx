import React, { useEffect, useState } from 'react';
import hrmService from '../services/hrm-service';
import useTable from '@libraries/handleTable'
import CommonDataTableV2 from '@components/CommonDataTableV2'
import { PopupLayout } from '@layouts/PopupLayout'
import { isoToDateTime } from '@libraries/common'
import { useForm } from '@libraries/handleInput'
import { Select } from '@components/UI/Input/Select'
import TextArea from '@components/UI/Input/Textarea'
import { usePopup } from '@components/popups/PopupContext'
import { useI18n } from '@i18n/useI18n'
import PermissionNode from '@/core/PermissionNode'
const IndexTimeAttendance = () => {
    const { t } = useI18n();
    const form = useForm();
    const { openPopup } = usePopup();
    const [showAdd, setShowAdd] = useState(false);
    const [showUpdate, setShowUpdate] = useState(false);
    const [showApprove, setShowApprove] = useState(false);
    const table = useTable();
    const getData = (page = 0) => {
        hrmService.attendance.all({
            page: page
        })
            .then((resp) => {
                table.setData(resp.message.list.data);
            })
            .catch((error) => {
                if (error?.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error?.response?.data?.message
                    })
                }
            })
    };
    useEffect(() => {
        getData();
    }, []);
    const handleAdd = () => {
        form.setFormErrors(null)
        form.setLoading(true)
        hrmService.attendance.add(form.formData)
            .then((resp) => {
                form.setFormData(null)
                form.setLoading(false);
                setShowAdd(false);
                openPopup({
                    type: 'success',
                    message: t('hrm.leave.add.success')
                })
                getData();
            })
            .catch((error) => {
                if (error?.response?.data?.errors) {
                    form.setFormErrors(error?.response?.data?.errors)
                }
                if (error?.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error?.response?.data?.message
                    })
                }

                form.setLoading(false)
            })
    };
    const onEdit = (item) => {
        form.setFormData(item)
        setShowUpdate(true);
    }
    const handleUpdate = () => {
        form.setFormErrors(null)
        form.setLoading(true)
        hrmService.attendance.update(form.formData)
            .then((resp) => {
                form.setFormData(null)
                form.setLoading(false);
                setShowAdd(false);
                openPopup({
                    type: 'success',
                    message: t('hrm.leave.update.success')
                })
                getData();
            })
            .catch((error) => {
                if (error?.response?.data?.errors) {
                    form.setFormErrors(error?.response?.data?.errors)
                }
                if (error?.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error?.response?.data?.message
                    })
                }

                form.setLoading(false)
            })
    };
    const onShow = (row) => {
        setShowApprove(true);
    }
    const permission = new PermissionNode();
    permission.fromNode('hrm');
    return (
        <div className="row justify-content-center">
            <div className="col-lg-12">
                <div>
                    <div className="row g-3">
                        <div className="col-12">
                            <CommonDataTableV2
                                add={() => {
                                    form.setFormData(null)
                                    setShowAdd(true)
                                }}
                                columns={[
                                    {
                                        label: t("hrm.attendance.date"),
                                        key: 'date',
                                        render: (value) => {
                                            return isoToDateTime(value)
                                        }
                                    },
                                    {
                                        label: t("hrm.attendance.checkin"),
                                        key: 'check_in_time'
                                    },
                                    {
                                        label: t("hrm.attendance.checkout"),
                                        key: 'check_out_time'
                                    },
                                    {
                                        label: t("hrm.attendance.note"),
                                        key: 'note'
                                    },
                                    {
                                        label: t("hrm.attendance.name"),
                                        key: 'name'
                                    },
                                    {
                                        label: t("hrm.attendance.approved"),
                                        key: 'approved',
                                        render: (value) => {
                                            return value ? <i className="bi bi-check-all text-success" style={{
                                                fontSize: 34
                                            }}></i> : null
                                        }
                                    }
                                ]}
                                data={table.data}
                                loading={table.loading}
                                movePage={getData}
                                onEdit={onEdit}
                                onShow={onShow}
                                roles={{
                                    add: permission.getPermission("create-attendance"),
                                    edit: permission.getPermission("update-attendance"),
                                    show: permission.getPermission("approve-attendance")
                                }}
                            />
                        </div>
                    </div>
                </div>
            </div>
            {showAdd ? <PopupLayout
                onClose={() => {
                    setShowAdd(false)
                }}
                onConfirm={handleAdd}
                title={t("hrm.attendance.add.title")}>
                <div>
                    <div className="row g-3">
                        <div className="col-md-12">
                            <div>
                                <label className="form-label fw-semibold">
                                    {t('hrm.attendance.add.note')} <span className="text-danger">*</span>
                                </label>
                                <TextArea
                                    name="note"
                                    type="note"
                                    handleChange={form.handleChange}
                                    value={form.formData?.note}
                                    errorMessage={form.formErrors?.note}
                                    placeholder={t("hrm.attendance.add.placeholder.note")}
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </PopupLayout> : null}
            {showUpdate ? <PopupLayout
                onClose={() => setShowUpdate(false)}
                onConfirm={handleUpdate}
                confirmText={t("hrm.attendance.update.button")}
                title={t("hrm.attendance.update.title")}>
                <div>
                    <div className="row g-3">
                        <div className="col-md-12">
                            <div>
                                <label htmlFor="note" className="form-label fw-semibold">
                                    {t('hrm.attendance.update.note')} <span className="text-danger">*</span>
                                </label>
                                <TextArea
                                    name="note"
                                    type="note"
                                    handleChange={form.handleChange}
                                    value={form.formData?.note}
                                    errorMessage={form.formErrors?.note}
                                    placeholder={t("hrm.attendance.update.placeholder.note")}
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </PopupLayout> : null}
            {showApprove ? <PopupLayout
                onClose={() => setShowApprove(false)}
                onConfirm={handleUpdate}
                confirmText={t("hrm.attendance.approve.confirm_button")}
                title={t("hrm.attendance.approve.title")}>
                <div>
                    <div className="row g-3">
                        <div className="col-md-12">
                            <div>
                                <label htmlFor="status" className="form-label fw-semibold">
                                    {t('hrm.attendance.approve.status')} <span className="text-danger">*</span>
                                </label>
                                <div>
                                    <Select
                                        name="approved"
                                        handleChange={form.handleChange}
                                        value={form.formData?.approved ? Number(form.formData?.approved) : 0}
                                        errorMessage={form.formErrors?.approved}
                                        options={[
                                            { value: 1, label: t("hrm.attendance.approve.approved") },
                                            { value: 0, label: t("hrm.attendance.approve.unapprove") }
                                        ]}
                                    />
                                </div>
                            </div>
                            <div className='mt-3'>
                                <label htmlFor="note" className="form-label fw-semibold">
                                    {t('hrm.attendance.approve.note')} <span className="text-danger">*</span>
                                </label>
                                <p>
                                    {form.formData?.note ?? t("hrm.attendance.approve.empty")}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </PopupLayout> : null}
        </div>
    );
};

export default IndexTimeAttendance;