import React, { useCallback, useEffect, useMemo, useState } from "react";
import SecondaryButton from '../../UI/Buttons/SecondaryButton'
import PrimaryButton from '../../UI/Buttons/PrimaryButton'
import { useSearchParams } from "react-router-dom";
import { useForm } from "../../../libraries/handleInput";
import { usePopup } from "../../popups/PopupContext";
import CommonDataTable from '../../CommonDataTable'
import useTable from '../../../libraries/handleTable'
import { PopupLayout } from '../../../layouts/PopupLayout'
import { InputForm } from "../../UI/Input/InputForm";
import { Select } from "../../UI/Input/Select";
import InvoiceOutService from "../../../services/InvoiceOutService";
import BootstrapAlert from "../../BootstrapAlert";
import OrderItemService from "../../../services/OrderItemService";
import PageHead from "../../PageHead";
import LoadingBox from "../../LoadingBox";
import Currencies from "../../Currencies";
import UploadImage from '../../UI/Input/UploadImage'
import RenderFormFieldByList from "../../RenderFormFieldByList";
import { ExtraCard } from '../../ExtraCard'
import { useI18n } from "../../../../i18n/useI18n";
import StatusBadge from "../../StatusBadge";
import ContentOnTable from "../../ContentOnTable";
export default function InvoiceOutDetail() {
    const { t } = useI18n();
    const [loading, setLoading] = useState(false)
    const { openPopup } = usePopup();
    const [searchParams] = useSearchParams();
    const form = useForm();
    const table = useTable();
    const [detail, setDetail] = useState(null);
    const [showEdit, setShowEdit] = useState(false);
    const getDetail = useCallback(() => {
        setLoading(true)
        InvoiceOutService.show(searchParams.get('id'))
            .then((resp) => {
                form.setFormData(resp.message);
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
    }, []);

    const getOrderItems = useCallback((order_id = 0) => {
        table.setLoading(true)
        OrderItemService.list({
            keywords: '',
            page: 1,
            order_id: order_id
        })
            .then((resp) => {
                table.setData(resp.message.data);
                table.setLinks(resp.message.links);
                table.setLoading(false);
                table.setTotal(resp.message.total)
            })
            .catch((error) => {

            })
    }, []);
    const view = useCallback(() => {
        InvoiceOutService.view()
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
            })
    }, []);
    useEffect(() => {
        if (detail?.order_id) {
            getOrderItems(detail?.order_id);
        }
    }, [detail?.order_id])
    const update = useCallback(() => {
        form.setFormErrors(null)
        form.setLoading(true)
        InvoiceOutService.update(form.formData)
            .then((resp) => {
                setShowEdit(false);
                openPopup({
                    type: 'success',
                    message: t('update_success')
                });
                setDetail(form.formData);
                form.setLoading(false)
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
                form.setLoading(false)
            })
    }, [form.formData]);
    const confirmApproved = useCallback(() => {
        openPopup({
            type: 'warning',
            message: t('confirm_approve'),
            onConfirm: () => {
                form.handleChangeByKey('approved', true);
            }
        })

    }, [form.handleChangeByKey]);
    useEffect(() => {
        if (!detail) {
            getDetail();
        }
        if (detail?.approved === false && form.formData?.approved === true) {
            update();
        }
    }, [form.formData?.approved, detail?.approved])
    useEffect(() => {
        table.setColums([
            {
                label: t("Name"), key: "name", render: (value) => {
                    return <div style={{
                        width: 100
                    }}>
                        <ContentOnTable value={value} />
                    </div>
                }
            },
            {
                label: t("PU"), key: "purchase_id", render: (value) => {
                    return `PU${value}`
                }
            },
            { label: t("Buy"), key: "buy_quantity",render:(value) => {
                return <div style={{width: 80}}>{value}</div>
            } },
            { label: t("Compensation"), key: "compensation_quantity",render:(value) => {
                return <div style={{width: 80}}>{value}</div>
            } },
            { label: t("Conversion"), key: "conversion_quantity",render:(value) => {
                return <div style={{width: 80}}>{value}</div>
            } },
            { label: t("Gift"), key: "gift_quantity",render:(value) => {
                return <div style={{width: 80}}>{value}</div>
            } },
            { label: t('Sku'), key: 'sku' },
            { label: t('Price'), key: 'price' },
            { label: t('Total tax'), key: 'total_tax',render:(value) => {
                return <div style={{width: 80}}>{value}</div>
            } },
            {
                label: t('Subtotal'), key: 'subtotal',
                render: (value) => <div style={{width: 100}}><Currencies amount={value} /></div>
            },
            {
                label: t('Total'), key: 'total',
                render: (value) => <div style={{width: 100}}><Currencies amount={value} /></div>
            },
            { label: t("Warehouse"), key: "warehouse",render:(value) => {
                return <div style={{width: 80}}>{value}</div>
            } },
        ]);
        view();
    }, [])
    return (
        <div className="text-light min-vh-100">
            <PageHead
                containerClass="mx-4"
                title={t("Detail order invoice")}
                subtitle={t("detail_order_invoice_desc")}
            />
            <div className="mx-4 mt-3">
                {loading ? <LoadingBox /> : <div>
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
                                <div className="theme-title small">{t("Order ID")}</div>
                                <div className="fw-semibold theme-title">
                                    OD{form.formData?.order_id ?? '-'}
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
                        <div className="col-lg-8">
                            {/* Customer Info */}
                            <div className="p-4 rounded border">
                                <div className="d-flex justify-content-between mb-3">
                                    <h5 className="fw-semibold">
                                        {t("Customer information")}
                                    </h5>
                                </div>
                                <div className="mb-2">
                                    <div className="theme-title small">{t("Name")}</div>
                                    <div className="theme-title">{form.formData?.customer_name}</div>
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
                                            <div className="theme-title">{form.formData?.tax_code ?? '-'}</div>
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <div className="mb-2">
                                            <div className="theme-title small">{t("Phone")}</div>
                                            <div className="theme-title">{form.formData?.phone ?? '-'}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <ExtraCard form={form} />

                            {/* Shipping Info */}
                            <div className="p-4 rounded border mt-3">
                                <div className="d-flex justify-content-between mb-3">
                                    <h5 className="fw-semibold">
                                        {t("Shipping information")}
                                    </h5>
                                </div>
                                <BootstrapAlert
                                    message={t("explain_free_shipping")}
                                />
                                <div className="mb-2">
                                    <div className="theme-title small">{t("Name")}</div>
                                    <div className="theme-title">{form.formData?.receiver_name}</div>
                                </div>
                                <div className="mb-2">
                                    <div className="theme-title small">{t("Email")}</div>
                                    <div className="theme-title">{form.formData?.receiver_email ?? '-'}</div>
                                </div>
                                <div className="mb-2">
                                    <div className="theme-title small">{t("Address")}</div>
                                    <div className="theme-title">{form.formData?.receiver_address ?? '-'}</div>
                                </div>
                                <div className="mb-2">
                                    <div className="theme-title small">{t("Tax code")}</div>
                                    <div className="theme-title">{form.formData?.tax_code ?? '-'}</div>
                                </div>
                                <div className="row mt-3">
                                    <div className="col-md-6">
                                        <div className="mb-2">
                                            <div className="theme-title small">{t("Shipping unit")}</div>
                                            <div className="theme-title">{form.formData?.preferred_unit_name}</div>
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <div className="mb-2">
                                            <div className="theme-title small">{t("Phone")}</div>
                                            <div className="theme-title">{form.formData?.phone ?? '-'}</div>
                                        </div>
                                    </div>
                                </div>
                                <div className="row mt-3">
                                    <div className="col-md-6">
                                        <div className="mb-2">
                                            <div className="theme-title small">{t("Shipping fee actual")}</div>
                                            <div className="theme-title">
                                                <Currencies amount={form.formData?.shipping_fee_actual} />
                                            </div>
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <div className="mb-2">
                                            <div className="theme-title small">{t("Shipping fee estimated")}</div>
                                            <div className="theme-title">
                                                <Currencies amount={form.formData?.shipping_fee_estimated} />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="row mt-3">
                                    <div className="col-md-6">
                                        <div className="mb-2">
                                            <div className="theme-title small">{t("Shipping code")}</div>
                                            <div className="theme-title">{form.formData?.shipping_code ?? '-'}</div>
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <div className="mb-2">
                                            <div className="theme-title small">{t("Receiver note")}</div>
                                            <div className="theme-title">{form.formData?.receiver_note ?? '-'}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Product List */}
                            <div className="p-4 rounded mt-4 border">
                                <div className="d-flex justify-content-between mb-3">
                                    <h5 className="fw-semibold">{t("Products Order")}</h5>
                                    <div className="theme-title small">{table.total} {t("Products")}</div>
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
                                    <StatusBadge status={form.formData?.payment_method ?? ''} />
                                </div>
                                <div className="mb-2">
                                    <div className="theme-title small">{t("Payment status")}</div>
                                    <StatusBadge status={form.formData?.payment_status ?? ''} />
                                </div>
                            </div>

                            {/* Summary */}
                            <div className="mt-3">
                                <div className="ms-auto">
                                    <div className="p-4 rounded border">
                                        <h5 className="fw-semibold mb-3">{t("Summary")}</h5>
                                        <div className="d-flex justify-content-between theme-title">
                                            <span>{t("Type order")}</span>
                                            <span className="badge bg-primary">{form.formData?.type}</span>
                                        </div>
                                        <div className="d-flex justify-content-between theme-title">
                                            <span>{t("Subtotal")}</span>
                                            <Currencies amount={detail?.subtotal} />
                                        </div>
                                        <div className="d-flex justify-content-between theme-title">
                                            <span>{t("Shipping fee")}</span>
                                            <Currencies amount={detail?.shipping_fee} />
                                        </div>
                                        <div className="d-flex justify-content-between theme-title">
                                            <span>{t("VAT")}</span>
                                            <Currencies amount={detail?.tax} />
                                        </div>
                                        <div className="d-flex justify-content-between theme-title">
                                            <span>{t("Discount")}</span>
                                            <Currencies amount={detail?.discount} />
                                        </div>
                                        <div className="d-flex justify-content-between mt-3 fs-5 fw-semibold">
                                            <span className="theme-title">{t("Total")}:</span>
                                            <span className="text-primary">
                                                <Currencies amount={detail?.total} />
                                            </span>
                                        </div>

                                        {/* Buttons */}
                                        <div className="d-grid gap-2 mt-4">
                                            <SecondaryButton
                                                onClick={() => setShowEdit(true)}
                                                label={t("Edit invoice")}
                                                width={'auto'}
                                            />
                                            <PrimaryButton
                                                width={'auto'}
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
                    loading={form.loading}
                    onClose={() => setShowEdit(false)}
                    title={t("Update invoice")}
                    confirmText={t('Save changes')}
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
                                required={false}
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
                            <UploadImage
                                name="image"
                                errorMessage={form.formErrors?.image}
                                handleChangeByKey={form.handleChangeByKey}
                                value={form.formData?.image}
                                required={false}
                                label={t("Image")}
                            />
                        </div>
                        {form.hookRender.map((item, index) => {
                            return (
                                <div className="form-group mt-3" key={index}>
                                    <RenderFormFieldByList item={item} form={form} />
                                </div>
                            );
                        })}
                    </div>
                </PopupLayout>
            ) : null}

        </div>
    );
}