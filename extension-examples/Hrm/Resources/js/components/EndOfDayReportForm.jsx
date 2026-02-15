import React, { useEffect, useState } from 'react';
import hrmService from '../services/hrm-service';
import useTable from '@libraries/handleTable'
import CommonDataTableV2 from '@components/CommonDataTableV2'
import { PopupLayout } from '@layouts/PopupLayout'
import { isoToDateTime } from '@libraries/common'
import { useForm } from '@libraries/handleInput'
import TextArea from '@components/UI/Input/Textarea'
import { usePopup } from '@components/popups/PopupContext'
import ContentOnTable from '@components/ContentOnTable'
import { useI18n } from '@i18n/useI18n'
import {InputForm} from '@components/UI/Input/InputForm'
import PrimaryButton from '@components/UI/Buttons/PrimaryButton'
const EndOfDayReportForm = () => {
    const { t } = useI18n();
    const form = useForm();
    const search = useForm();
    const { openPopup } = usePopup();
    const [showAdd, setShowAdd] = useState(false);
    const [showDetail, setShowDetail] = useState(false);
    const table = useTable();
    const getData = (page = 0) => {
        hrmService.report.all({
            page: page,
            ...search.formData
        })
            .then((resp) => {
                table.setData(resp.message.data);
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
        hrmService.report.add(form.formData)
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

    return (
        <div className="row">
            <div>
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
                    columns={[
                        {
                            label: 'ID',
                            key: 'id'
                        },
                        {
                            label: t("hrm.report.date"),
                            key: 'date',
                            render: (value) => {
                                return isoToDateTime(value)
                            }
                        },
                        {
                            label: t("hrm.report.summary"),
                            key: 'summary',
                            render: (value) => {
                                return <ContentOnTable value={value} />
                            }
                        },
                        {
                            label: t("hrm.report.tasks"),
                            key: 'tasks_done',
                            render: (value) => {
                                return <ContentOnTable value={value} />
                            }
                        },
                        {
                            label: t("hrm.report.issues"),
                            key: 'issues',
                            render: (value) => {
                                return <ContentOnTable value={value} />
                            }
                        },
                        {
                            label: t("hrm.report.name"),
                            key: 'name'
                        },
                    ]}
                    add={() => setShowAdd(true)}
                    data={table.data}
                    loading={table.loading}
                    callback={getData}
                    onEdit={(item) => {
                        setShowDetail(true)
                        form.setFormData(item)
                    }}
                    iconEdit={<i className="bi bi-eyeglasses"></i>}
                    search={search}
                />
            </div>
            {showAdd ? <PopupLayout
                onClose={() => setShowAdd(false)}
                onConfirm={handleAdd}
                title={t("hrm.report.add.title")}>
                <div>
                    <div className="row g-3">

                        <div className="col-12">
                            <label className="form-label fw-semibold">
                                {t("hrm.report.form.daily_summary.label")} <span className="text-danger">*</span>
                            </label>

                            <TextArea
                                name="summary"
                                handleChange={form.handleChange}
                                value={form.formData?.summary}
                                errorMessage={form.formErrors?.summary}
                                placeholder={t("hrm.report.form.daily_summary.placeholder")}
                            />
                        </div>

                        <div className="col-12">
                            <label className="form-label fw-semibold">
                                {t("hrm.report.form.tasks_completed.label")}
                            </label>
                            <TextArea
                                name="tasks_done"
                                handleChange={form.handleChange}
                                value={form.formData?.tasks_done}
                                errorMessage={form.formErrors?.tasks_done}
                                placeholder={t("hrm.report.form.tasks_completed.placeholder")}
                            />
                        </div>

                        <div className="col-12">
                            <label className="form-label fw-semibold">
                                {t("hrm.report.form.issue.label")}
                            </label>
                            <TextArea
                                name="issues"
                                handleChange={form.handleChange}
                                value={form.formData?.issues}
                                errorMessage={form.formErrors?.issues}
                                placeholder={t("hrm.report.form.issue.placeholder")}
                            />
                        </div>
                    </div>
                </div>
            </PopupLayout>
                : null}
            {showDetail ? <PopupLayout
                onClose={() => setShowDetail(false)}
                title={t("hrm.report.detail.title")}>
                <div>
                    <div className="row g-3">

                        <div className="col-12">
                            <label className="form-label fw-semibold">
                                {t("hrm.report.form.daily_summary.label")} <span className="text-danger">*</span>
                            </label>

                            <p>
                                {form.formData?.summary}
                            </p>
                        </div>

                        <div className="col-12">
                            <label className="form-label fw-semibold">
                                {t("hrm.report.form.tasks_completed.label")}
                            </label>
                            <p>
                                {form.formData?.tasks_done}
                            </p>
                        </div>

                        <div className="col-12">
                            <label className="form-label fw-semibold">
                                {t("hrm.report.form.issue.label")}
                            </label>
                            <p>
                                {form.formData?.issues}
                            </p>
                        </div>
                    </div>
                </div>
            </PopupLayout>
                : null}
        </div>
    );
};

export default EndOfDayReportForm;