import React, { useState, useEffect } from 'react';
import hrmService from '../services/hrm-service';
import useTable from '@libraries/handleTable'
import CommonDataTableV2 from '@components/CommonDataTableV2'
import { PopupLayout } from '@layouts/PopupLayout'
import { isoToDateTime } from '@libraries/common'
import StatusBadge from '@components/StatusBadge'
import { useForm } from '@libraries/handleInput'
import { InputForm } from '@components/UI/Input/InputForm'
import { Select } from '@components/UI/Input/Select'
import TextArea from '@components/UI/Input/Textarea'
import { usePopup } from '@components/popups/PopupContext'
import { useI18n } from '@i18n/useI18n'
const LeaveApprovalList = () => {
    const { t, lang } = useI18n();
    const form = useForm();
    const search = useForm();
    const { openPopup } = usePopup();
    const [showAdd, setShowAdd] = useState(false);
    const table = useTable();

    const getData = (page = 0) => {
        hrmService.leave.all({
            page: page
        })
            .then((resp) => {
                table.setData(resp.message.list.data);
                if (resp.message.permission) {
                    table.addColums([
                        {
                            label: t('hrm.leave.action'),
                            key: 'id'
                        }
                    ], (th, id) => {
                        return <div onClick={() => {
                            console.log(id)
                            resp.message.list.data.map((item, index) => {
                                if (Number(id) === item.id) {
                                    form.setFormData(item);
                                }
                            })
                        }}>
                            <i className="bi bi-file-text text-primary btn border-primary"></i>
                        </div>
                    })
                }
            })
            .catch((error) => {

            })
    };

    useEffect(() => {
        table.setColums([
            {
                label: t("hrm.leave.start_date.label"),
                key: 'start_date',
                render: (value) => {
                    return isoToDateTime(value)
                }
            },
            {
                label: t("hrm.leave.end_date.label"),
                key: 'end_date',
                render: (value) => {
                    return isoToDateTime(value)
                }
            },
            {
                label: t("hrm.leave.leave_type.label"),
                key: 'leave_type',
                render: (value) => {
                    return <span>{t("hrm.leave.form." + value)}</span>
                }
            },
            {
                label: t("hrm.leave.reason"),
                key: 'reason'
            },
            {
                label: t("hrm.leave.status.label"),
                key: 'status',
                render: (value) => {
                    return <StatusBadge status={t("hrm.leave.status." + value)} />
                }
            },
            {
                label: t("hrm.leave.name"),
                key: 'name'
            }
        ])
        if (table.data?.length > 0) {
            return;
        }
        getData();
    }, [lang]);

    const handleAdd = () => {
        form.setFormErrors(null)
        form.setLoading(true)
        hrmService.leave.add(form.formData)
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

    const handleUpdate = () => {
        form.setFormErrors(null)
        form.setLoading(true)
        hrmService.leave.update(form.formData)
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

    return (
        <div className="card shadow-sm">
            <CommonDataTableV2
                add={() => setShowAdd(true)}
                columns={table.colums}
                data={table.data}
                loading={table.loading}
                movePage={getData}
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
            />
            {showAdd ? <PopupLayout
                onClose={() => setShowAdd(false)}
                onConfirm={handleAdd}
                title={t("hrm.leave.add.title")}>
                <div>
                    <div className="row g-3">
                        <div className="col-md-6">
                            <label htmlFor="start_date" className="form-label fw-semibold">
                                {t('hrm.leave.start_date.label')} <span className="text-danger">*</span>
                            </label>
                            <InputForm
                                name="start_date"
                                type="date"
                                handleChange={form.handleChange}
                                value={form.formData?.start_date}
                                errorMessage={form.formErrors?.start_date}
                            />
                        </div>

                        <div className="col-md-6">
                            <label htmlFor="end_date" className="form-label fw-semibold">
                                {t('hrm.leave.end_date.label')} <span className="text-danger">*</span>
                            </label>
                            <InputForm
                                name="end_date"
                                type="date"
                                handleChange={form.handleChange}
                                value={form.formData?.end_date}
                                errorMessage={form.formErrors?.end_date}
                            />
                        </div>

                        <div className="col-12">
                            <label className="form-label fw-semibold">
                                {t('hrm.leave.leave_type.label')}
                            </label>
                            <Select
                                name="leave_type"
                                type="date"
                                handleChange={form.handleChange}
                                value={form.formData?.leave_type}
                                errorMessage={form.formErrors?.leave_type}
                                options={[
                                    { value: 'annual', label: t("hrm.leave.form.annual") },
                                    { value: 'sick', label: t("hrm.leave.form.sick") },
                                    { value: 'unpaid', label: t("hrm.leave.form.unpaid") }
                                ]}
                            />
                        </div>

                        <div className="col-12">
                            <label htmlFor="reason" className="form-label fw-semibold">
                                {t('hrm.leave.reason')}
                            </label>
                            <TextArea
                                name="reason"
                                type="date"
                                handleChange={form.handleChange}
                                value={form.formData?.reason}
                                errorMessage={form.formErrors?.reason}
                            />
                        </div>
                    </div>
                </div>
            </PopupLayout> : null}
            {form.formData ? <PopupLayout
                onClose={() => form.setFormData(null)}
                onConfirm={handleUpdate}
                title={"Active to leave request"}>
                <div>
                    <div className="row g-3">

                        <div className="col-12">
                            <label className="form-label fw-semibold">
                                {t('hrm.leave.status.label')}
                            </label>
                            <Select
                                name="status"
                                handleChange={form.handleChange}
                                value={form.formData?.status}
                                errorMessage={form.formErrors?.status}
                                options={[
                                    { value: 'pending', label: t("hrm.leave.form.pending") },
                                    { value: 'approved', label: t("hrm.leave.form.approved") },
                                    { value: 'rejected', label: t("hrm.leave.form.rejected") }
                                ]}
                            />
                        </div>
                    </div>
                </div>
            </PopupLayout> : null}
        </div>
    );
};

export default LeaveApprovalList;