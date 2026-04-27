import React, { useCallback, useEffect, useMemo, useState } from "react";
import SecondaryButton from '../../UI/Buttons/SecondaryButton'
import PrimaryButton from '../../UI/Buttons/PrimaryButton'
import InvoiceInService from '../../../services/InvoiceInService'
import { Link, useNavigate, useSearchParams } from "react-router-dom";
import { useForm } from "../../../libraries/handleInput";
import { usePopup } from "../../popups/PopupContext";
import PurchaseItemService from '../../../services/PurchaseItemService'
import CommonDataTable from '../../CommonDataTable'
import useTable from '../../../libraries/handleTable'
import { PopupLayout } from '../../../layouts/PopupLayout'
import { InputForm } from "../../UI/Input/InputForm";
import { Select } from "../../UI/Input/Select";
import PageHead from "../../PageHead";
import LoadingBox from "../../LoadingBox";
import Currencies from "../../Currencies";
import UploadImage from "../../UI/Input/UploadImage";
import RenderFormFieldByList from '../../RenderFormFieldByList'
import { useI18n } from "../../../../i18n/useI18n";
import StatusBadge from "../../StatusBadge";
import PermissionNode from "@/core/PermissionNode";
export default function InvoiceInDetail() {
    const permission = new PermissionNode();
    const navigate = useNavigate();
    const { t } = useI18n();
    const [loading, setLoading] = useState(false);
    const { openPopup } = usePopup();
    const [searchParams] = useSearchParams();
    const form = useForm();
    const [detail, setDetail] = useState(null);
    const table = useTable();
    const [showEdit, setShowEdit] = useState(false);
    const getDetail = useCallback(() => {
    setLoading(true)
    InvoiceInService.show(searchParams.get('id'))
        .then((resp) => {
            form.setFormData(resp.message);
            getPurchaseItems(resp.message?.purchase_id);
            setDetail(resp.message);
            setLoading(false)
        })
        .catch((error) => {
            if (error.response?.data?.errors) {
                form.setFormErrors(error.response?.data?.errors);
            }
            if (error.response?.data?.message) {
                openPopup({
                    type: 'error',
                    message: error.response?.data?.message
                })
            }
            setLoading(false)
        })
}, [searchParams]);

const getPurchaseItems = useCallback((purchase_id = 0) => {
    table.setLoading(true)
    PurchaseItemService.list({
        keywords: '',
        page: 1,
        purchase_id: purchase_id
    })
        .then((resp) => {
            table.setData(resp.message.data);
            table.setLinks(resp.message.links);
            table.setLoading(false);
            table.setTotal(resp.message.total)
        })
        .catch((error) => {
            table.setLoading(false);
        })
}, []);

const update = useCallback(() => {
    form.setLoading(true);
    form.setFormErrors(null)
    InvoiceInService.update(form.formData)
        .then((resp) => {
            setShowEdit(false);
            if(permission.fromNode('stockin').getPermission('index')) {
                openPopup({
                    type: 'success',
                    message: t('update_success'),
                    confirmText: t('go_to_stockin'),
                    onConfirm: () => {
                        navigate('/stock-ins')
                    }
                });    
            } else {
                    openPopup({
                    type: 'success',
                    message: t('update_success')
                });
            }
            
            setDetail(form.formData);
            form.setLoading(false);
        })
        .catch((error) => {
            if (error.response?.data?.errors) {
                form.setFormErrors(error.response.data?.errors)
            }
            if (error.response?.data?.message) {
                openPopup({
                    type: 'error',
                    message: error.response.data?.message
                })
            }
            form.setLoading(false);
        })
}, [form.formData, t]);

const confirmApproved = useCallback(() => {
    openPopup({
        type: 'warning',
        message: t('Are you sure to take approve'),
        onConfirm: () => {
            form.handleChangeByKey('approved', true);
        }
    })
}, [form.formData, t]);
    const view = useCallback((page = 0) => {
        InvoiceInService.view()
            .then((resp) => {
                form.setHookRender(resp.message.form)

            })
            .catch((error) => {
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
            });
    }, []);

    useEffect(() => {
        if (form?.formData?.approved === true && detail?.approved === false) {
            update();
        }
    }, [form.formData?.approved, detail?.approved])
    useEffect(() => {
        getDetail();
        view();
        table.setColums([
    { label: t("Name"), key: "name" },
    { label: t("Buy"), key: "buy_quantity" },
    { label: t("Compensation"), key: "compensation_quantity" },
    { label: t("Conversion"), key: "conversion_quantity" },
    { label: t("Gift"), key: "gift_quantity" },
    { label: t('Sku'), key: 'sku' },
    { label: t('Total tax'), key: 'total_tax' },
    {
        label: t('Unit cost'), key: 'unit_cost',
        render: (value) => <span><Currencies amount={value} /></span>
    },
    {
        label: t('Total'), key: 'total',
        render: (value) => <span><Currencies amount={value} /></span>
    }
]);
    }, [])
    return (
        <div className="min-vh-100">
            <PageHead
                title={t("Detail purchase invoice")}
                subtitle={t("invoice_purchase_desc")}
            />
            <div className="container mt-3">
                {loading ? <LoadingBox /> : <div>
                    {/* Invoice Info */}
                    <div className="row g-3 mb-4">
                        <div className="col-md-3">
                            <div className="p-3 rounded border">
                                <div className="theme-title small">
                                    {t("Invoice No")}
                                    {form.formData?.approved
                                        ? <span className="badge bg-success text-uppercase ms-2">{t("Approved")}</span>
                                        : <span className="badge bg-warning text-uppercase ms-2">{t("Waiting")}</span>}
                                </div>
                                <div className="fw-semibold theme-title">
                                    {form.formData?.document_no ?? '-'}
                                </div>
                            </div>
                        </div>
                        <div className="col-md-3">
                            <div className="p-3 rounded border">
                                <div className="theme-title small">{t("Purchase ID")}</div>
                                <div className="fw-semibold theme-title">
                                    PO{form.formData?.purchase_id ?? '-'}
                                </div>
                            </div>
                        </div>
                        <div className="col-md-3">
                            <div className="p-3 rounded border">
                                <div className="theme-title small">{t("Invoice date")}</div>
                                <div className="fw-semibold theme-title">
                                    {form.formData?.invoice_date ?? '-'}
                                </div>
                            </div>
                        </div>
                        <div className="col-md-3">
                            <div className="p-3 rounded border">
                                <div className="theme-title small">{t("Due date")}</div>
                                <div className="fw-semibold theme-title">
                                    {form.formData?.due_date ?? '-'}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="row g-4">
                        {/* Supplier Info */}
                        <div className="col-lg-8">
                            <div className="p-4 rounded border">
                                <div className="d-flex justify-content-between mb-3">
                                    <h5 className="fw-semibold">
                                        {t("Supplier information")}
                                    </h5>
                                </div>
                                <div className="mb-2">
                                    <div className="theme-title small">{t("Name")}</div>
                                    <div className="theme-title">{form.formData?.unit_name}</div>
                                </div>
                                <div className="mb-2">
                                    <div className="theme-title small">{t("Email")}</div>
                                    <div className="theme-title">{form.formData?.email}</div>
                                </div>
                                <div className="mb-2">
                                    <div className="theme-title small">{t("Address")}</div>
                                    <div className="theme-title">{form.formData?.address}</div>
                                </div>
                                <div className="row mt-3">
                                    <div className="col-md-6">
                                        <div className="mb-2">
                                            <div className="theme-title small">{t("Tax code")}</div>
                                            <div className="theme-title">{form.formData?.tax_code}</div>
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <div className="mb-2">
                                            <div className="theme-title small">{t("Phone")}</div>
                                            <div className="theme-title">{form.formData?.phone}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {form.hookRender.length >= 1 ? (
                                <div className="p-4 rounded border mt-3">
                                    <div className="d-flex justify-content-between mb-3">
                                        <h5 className="fw-semibold">
                                            {t("Extras")}
                                        </h5>
                                    </div>
                                    <div className="row mt-3">
                                        {form.hookRender.map((item, index) => {
                                            return (
                                                <div key={index} className="col-md-6">
                                                    <div className="mb-2">
                                                        <div className="theme-title small">
                                                            {t(item.label)}
                                                        </div>
                                                        <div className="theme-title">{form.formData?.[item.key]}</div>
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>
                                </div>
                            ) : null}

                            {/* Product List */}
                            <div className="p-4 rounded mt-4 border">
                                <div className="d-flex justify-content-between mb-3">
                                    <h5 className="fw-semibold">{t("Products")}</h5>
                                    <div className="theme-title small">
                                        {table.total} {t("Products")}
                                    </div>
                                </div>
                                <CommonDataTable
                                    columns={table.colums}
                                    data={table.data}
                                    links={table.links}
                                    loading={table.loading}
                                />
                            </div>
                        </div>

                        {/* Payment Info */}
                        <div className="col-lg-4">
                            <div className="p-4 rounded border">
                                <h5 className="fw-semibold mb-3">{t("Payment information")}</h5>
                                <div className="mb-2">
                                    <div className="theme-title small">{t("Payment method")}</div>
                                    <StatusBadge status={form.formData?.payment_method ?? ''}/>
                                </div>
                                <div className="mb-2">
                                    <div className="theme-title small">{t("Payment status")}</div>
                                    <StatusBadge status={form.formData?.payment_status ?? ''}/>
                                </div>

                                <div className="mt-3 theme-title small">
                                    <div>{t("Bank name")}: {form.formData?.bank_name ?? t("Not found")}</div>
                                    <div>{t("Bank account")}: {form.formData?.bank_account ?? t("Not found")}</div>
                                </div>
                            </div>

                            {/* Summary */}
                            <div className="mt-3">
                                <div className="ms-auto">
                                    <div className="p-4 rounded border">
                                        <h5 className="fw-semibold mb-3">{t("Summary")}</h5>
                                        <div className="d-flex justify-content-between theme-title">
                                            <span>{t("Subtotal")}</span>
                                            <Currencies amount={form.formData?.subtotal} />
                                        </div>

                                        <div className="d-flex justify-content-between theme-title">
                                            <span>{t("Shipping fee")}</span>
                                            <Currencies amount={form.formData?.shipping_fee} />
                                        </div>

                                        <div className="d-flex justify-content-between theme-title">
                                            <span>{t("VAT")}</span>
                                            <Currencies amount={form.formData?.tax} />
                                        </div>
                                        <div className="d-flex justify-content-between theme-title">
                                            <span>{t("Discount")}</span>
                                            <Currencies amount={form.formData?.discount} />
                                        </div>

                                        <div className="d-flex justify-content-between mt-3 fs-5 fw-semibold">
                                            <span>{t("Total")}:</span>
                                            <Currencies amount={form.formData?.total} />
                                        </div>

                                        {/* Buttons */}
                                        <div className="d-grid gap-2 mt-4">
                                            <SecondaryButton
                                                loading={form.loading}
                                                onClick={() => setShowEdit(true)}
                                                label={t("Edit invoice")}
                                                width={'auto'}
                                            />
                                            <PrimaryButton
                                                width={'auto'}
                                                loading={form.loading}
                                                disabled={form.formData?.approved}
                                                onClick={confirmApproved}
                                                label={t("Take approved")}
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>}
            </div>
            {showEdit ? (
                <PopupLayout
                    confirmText={t("Save changes")}
                    loading={form.loading}
                    onClose={() => setShowEdit(false)}
                    title={t("Update invoice")}
                    onConfirm={() => {
                        update();
                    }}
                >
                    <div>
                        <div className="form-group">
                            <InputForm
                                errorMessage={form.formErrors?.document_no}
                                handleChange={form.handleChange}
                                value={form.formData?.document_no}
                                type="text"
                                name="document_no"
                                placeholder={t("Enter invoice number")}
                                required={true}
                                label={t("Invoice number")}
                            />
                        </div>
                        <div className="form-group mt-3">
                            <InputForm
                                errorMessage={form.formErrors?.invoice_date}
                                handleChange={form.handleChange}
                                value={form.formData?.invoice_date}
                                type="date"
                                name="invoice_date"
                                placeholder={t("Enter invoice date")}
                                required={true}
                                label={t("Invoice date")}
                            />
                        </div>
                        <div className="form-group mt-3">
                            <InputForm
                                errorMessage={form.formErrors?.due_date}
                                handleChange={form.handleChange}
                                value={form.formData?.due_date}
                                type="date"
                                name="due_date"
                                placeholder={t("Enter due date")}
                                required={true}
                                label={t("Due date")}
                            />
                        </div>
                        <div className="form-group mt-3">
                            <Select
                                errorMessage={form.formErrors?.payment_status}
                                handleChange={form.handleChange}
                                value={form.formData?.payment_status}
                                name="payment_status"
                                options={[
                                    { value: 'pending', label: t('Pending') },
                                    { value: 'paid', label: t('Paid') },
                                    { value: 'partial_payment', label: t('Partial payment') }
                                ]}
                                required={true}
                                label={t("Payment status")}
                            />
                        </div>
                        <div className="form-group mt-3">
                            <InputForm
                                errorMessage={form.formErrors?.amount_paid}
                                handleChange={form.handleChange}
                                value={form.formData?.amount_paid}
                                name="amount_paid"
                                required={false}
                                label={t("Amount paid")}
                            />
                        </div>
                        <div className="form-group mt-3">
                            <label>
                                {t("Invoice image")}
                                <a target="_blank" rel="noreferrer" href={form.formData?.image} className="ms-2">
                                    {t("View full")}
                                </a>
                            </label>
                            <UploadImage
                                name="image"
                                errorMessage={form.formErrors?.image}
                                handleChangeByKey={form.handleChangeByKey}
                                value={form.formData?.image}
                            />
                        </div>
                        {form.hookRender.map((item, index) => (
                            <div className="form-group mt-3" key={index}>
                                <RenderFormFieldByList item={item} form={form} />
                            </div>
                        ))}
                    </div>
                </PopupLayout>
            ) : null}

        </div>
    );
}