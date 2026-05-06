import React, { useCallback, useEffect, useMemo, useState } from "react";
import { useSearchParams } from "react-router-dom";
import { useForm } from "../../libraries/handleInput";
import { isoToDateTime } from "../../libraries/common";
import TwoCol from "./StockInDetail/TwoCol";
import InfoBox from './StockInDetail/InfoBox'
import useTable from "../../libraries/handleTable";
import CommonDataTable from "../CommonDataTable";
import SecondaryButton from "../UI/Buttons/SecondaryButton";
import PrimaryButton from "../UI/Buttons/PrimaryButton";
import { PopupLayout } from "../../layouts/PopupLayout";
import { usePopup } from "../popups/PopupContext";
import StockOutService from "../../services/StockOutService";
import FormUpdate from "./StockOutDetail/FormUpdate";
import CustomerInfo from "./StockOutDetail/CustomerInfo";
import OrderShippingService from "../../services/OrderShippingService";
import SuccessButton from "../UI/Buttons/SuccessButton";
import ShippingInformation from "./StockOutDetail/ShippingInformation";
import PaymentInformation from "./StockOutDetail/PaymentInformation";
import PageHead from "../PageHead";
import LoadingBox from "../LoadingBox";
import Currencies from "../Currencies";
import OrderItemService from "../../services/OrderItemService";
import { ExtraCard } from "../ExtraCard";
import RenderFormFieldByList from "../RenderFormFieldByList";
import { useI18n } from "../../../i18n/useI18n";
import StatusBadge from "../StatusBadge";
import ContentOnTable from "../ContentOnTable";
export default function StockOutDetail() {
    const { t } = useI18n();
    const [loading, setLoading] = useState(false)
    const [showForm, setShowForm] = useState(false);
    const [showExtraForm, setExtraShowForm] = useState(false);
    const tableInventory = useTable();
    const form = useForm();
    const shippingForm = useForm();
    const { openPopup } = usePopup();
    const [searchParams] = useSearchParams();
    const navigate = useState();
    const [detail, setDetail] = useState(null);
    const getDetail = useCallback(() => {
        setLoading(true)
        StockOutService.show(searchParams.get('stockout'))
            .then((resp) => {
                form.setFormData(resp.message);
                shippingForm.setFormData(resp.message)
                setDetail(resp.message);
                setLoading(false);
            })
            .catch((error) => {
                navigate('/stock')
            });
    }, []);
    const update = useCallback(() => {
        form.setLoading(true)
        form.setFormErrors(null)
        StockOutService.update(form.formData)
            .then((resp) => {
                openPopup({
                    type: 'success',
                    message: t('update_success')
                })
                setDetail(form.formData);
                form.setLoading(false)
            })
            .catch((error) => {
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
                if (error.response.data?.errors) {
                    form.setFormErrors(error.response?.data?.errors)
                }
                form.setLoading(false)
            })
    }, [form.formData]);
    const updateShipping = useCallback(() => {
        shippingForm.setLoading(true)
        shippingForm.setFormErrors(null)
        OrderShippingService.update({
            ...shippingForm.formData,
            id: shippingForm.formData?.shipping_id
        })
            .then((resp) => {
                openPopup({
                    type: 'success',
                    message: t('update_success')
                })
                setShowForm(false);
                shippingForm.setLoading(false)
                getDetail();
            })
            .catch((error) => {
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
                if (error.response.data?.errors) {
                    shippingForm.setFormErrors(error.response?.data?.errors)
                }
                shippingForm.setLoading(false)
            })
    }, [shippingForm.formData]);
    const confirmSent = useCallback(() => {
        openPopup({
            type: 'warning',
            message: t('confirm_sent'),
            onConfirm: () => {
                //update();
                form.handleChangeByKey('status', 'shipped')
            }
        })
    }, [form]);
    const confirmCompleted = useCallback(() => {
        openPopup({
            type: 'warning',
            message: t('confirm_completed'),
            onConfirm: () => {
                //update();
                form.handleChangeByKey('status', 'completed')
            }
        })
    }, [form]);
    const getOrderItem = useCallback((page = 0) => {
        tableInventory.setLoading(true);
        OrderItemService.list({
            order_id: detail?.order_id,
            page: page
        })
            .then((resp) => {
                tableInventory.setData(resp.message.data);
                tableInventory.setLoading(false);
                tableInventory.setTotal(resp.message.total)
            })
            .catch((error) => {
                tableInventory.setLoading(false);
            })
    }, [searchParams, detail?.order_id]);
    const view = useCallback(() => {
        StockOutService.view()
            .then((resp) => {
                form.setHookRender(resp.message.index)
            })
            .catch((error) => {
                if (error.response?.message?.errors) {
                    openPopup({
                        type: 'error',
                        message: error.response.message?.errors
                    })
                }
            })
    }, []);
    const shippingview = useCallback(() => {
        OrderShippingService.view()
            .then((resp) => {
                shippingForm.setHookRender(resp.message.index)
            })
            .catch((error) => {
                if (error.response?.message?.errors) {
                    openPopup({
                        type: 'error',
                        message: error.response.message?.errors
                    })
                }
            })
    }, []);
    useEffect(() => {
        tableInventory.setColums([
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
            {
                label: t("Buy"), key: "buy_quantity", render: (value) => {
                    return <div style={{ width: 80 }}>{value}</div>
                }
            },
            {
                label: t("Compensation"), key: "compensation_quantity", render: (value) => {
                    return <div style={{ width: 80 }}>{value}</div>
                }
            },
            {
                label: t("Conversion"), key: "conversion_quantity", render: (value) => {
                    return <div style={{ width: 80 }}>{value}</div>
                }
            },
            {
                label: t("Gift"), key: "gift_quantity", render: (value) => {
                    return <div style={{ width: 80 }}>{value}</div>
                }
            },
            { label: t('Sku'), key: 'sku' },
            { label: t('Price'), key: 'price' },
            {
                label: t('Total tax'), key: 'total_tax', render: (value) => {
                    return <div style={{ width: 80 }}>{value}</div>
                }
            },
            {
                label: t('Subtotal'), key: 'subtotal',
                render: (value) => <div style={{ width: 100 }}><Currencies amount={value} /></div>
            },
            {
                label: t('Total'), key: 'total',
                render: (value) => <div style={{ width: 100 }}><Currencies amount={value} /></div>
            },
            {
                label: t("Warehouse"), key: "warehouse", render: (value) => {
                    return <div style={{ width: 80 }}>{value}</div>
                }
            },
        ]);
        if (!searchParams.get('stockout')) {
            return;
        }
        if (!detail?.order_id) {
            getDetail();
        } else {
            getOrderItem();
        }
        shippingview();
        view();
    }, [detail?.order_id]);
    useEffect(() => {
        if (detail?.status !== form.formData?.status) {
            update();
        }
    }, [detail?.status, form.formData?.status]);
    return (
        <div className="min-vh-100">
            <PageHead
                containerClass="mx-4"
                title={t("Detail stock out")}
                subtitle={t("stock_out_desc")}
            />
            <div className="m-4 mt-3">

                {loading ? <LoadingBox /> : <div>
                    <div className="row g-3 mb-4">
                        <InfoBox label={t("Order ID")} value={'OD' + form.formData?.order_id} />
                        <InfoBox label={t("Order code")} value={form.formData?.order_no} />
                        <InfoBox label={t("Order date")} value={isoToDateTime(form.formData?.order_date)} />
                        <InfoBox label={t("Expected delivery date")} value={form.formData?.expected_delivery_date ?? '-'} />
                    </div>

                    <div className="row g-4">

                        <div className="col-lg-8">
                            <div className="p-4 rounded border">
                                <div className="d-flex justify-content-between mb-3">
                                    <h5 className="fw-semibold">{t("Stock information")}</h5>
                                </div>

                                <TwoCol
                                    label={t("Employee")}
                                    left={form.formData?.approved_name ?? '-'}
                                    rightLabel={t('Status')}
                                    right={<StatusBadge status={form.formData?.status ?? ''} />}
                                />

                                <div className="mt-3">
                                    <div className="theme-title small">{t("Note")}</div>
                                    <div>{form.formData?.order_note ?? '-'}</div>
                                </div>
                            </div>

                            {/* Customer Info */}
                            <CustomerInfo form={form} />

                            {/* Shipping Info */}
                            <ShippingInformation form={shippingForm} />

                            <ExtraCard form={form} title={t("Stock extras")} />
                            <ExtraCard form={shippingForm} title={t("Shipping extras")} />

                            {/* Inventory */}
                            <div className="rounded mt-4 mb-5">
                                <div className="d-flex justify-content-between mb-3">
                                    <h5 className="fw-semibold">{t("Inventories")}</h5>
                                    <div className="theme-title small">
                                        {tableInventory.total} {t("Products")}
                                    </div>
                                </div>

                                {/** Table */}
                                <CommonDataTable
                                    columns={tableInventory.colums}
                                    data={tableInventory.data}
                                    links={tableInventory.links}
                                    loading={tableInventory.loading}
                                />
                            </div>
                        </div>

                        {/* Summary Box */}
                        <div className="col-lg-4">
                            <PaymentInformation form={form} />
                            {/* Summary */}
                            <div className="mt-3">
                                <div className="ms-auto">
                                    <div className="p-4 rounded border">
                                        <h5 className="fw-semibold mb-3">{t("Summary")}</h5>
                                        <div className="d-flex justify-content-between theme-title">
                                            <span>{t("Type order")}</span>
                                            <span className="badge bg-primary">{t(form.formData?.type ?? '')}</span>
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
                                            <Currencies amount={detail?.total_adjusted} />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Inspection box */}
                            {form.formData?.status !== 'received' ? (
                                <div className="row g-2 mt-2">
                                    <div className="col-6">
                                        {detail?.status === 'pending' && (
                                            <PrimaryButton
                                                width={'100%'}
                                                onClick={confirmSent}
                                                label={t("Shipped")}
                                            />
                                        )}
                                        {detail?.status === 'shipped' && (
                                            <SuccessButton
                                                width={'100%'}
                                                onClick={confirmCompleted}
                                                label={t("Completed")}
                                            />
                                        )}
                                        {detail?.status === 'completed' && (
                                            <PrimaryButton
                                                width={'100%'}
                                                disabled={true}
                                                label={t("Completed")}
                                            />
                                        )}
                                    </div>
                                    <div className="col-6">
                                        <SecondaryButton
                                            disabled={detail?.status !== 'pending'}
                                            onClick={() => setShowForm(true)}
                                            width={'100%'}
                                            label={t("Shipping information")}
                                        />
                                    </div>
                                    {form.hookRender.length >= 1 ? (
                                        <div className="col-12">
                                            <SecondaryButton
                                                disabled={detail?.status !== 'pending'}
                                                onClick={() => setExtraShowForm(true)}
                                                width={'100%'}
                                                label={t("Extra information")}
                                            />
                                        </div>
                                    ) : null}
                                </div>
                            ) : null}
                        </div>
                    </div></div>}
            </div>
            {showForm ? <PopupLayout
                loading={shippingForm.loading}
                confirmText={t("Save changes")}
                onClose={() => setShowForm(false)}
                title={t("Update shipping")} onConfirm={() => updateShipping()}>
                <div>
                    <FormUpdate form={shippingForm} />
                </div>
            </PopupLayout> : null}
            {showExtraForm ? <PopupLayout
                loading={form.loading}
                confirmText={t("Save changes")}
                onClose={() => setExtraShowForm(false)}
                title={t("Update Extras")} onConfirm={() => update()}>
                <div>
                    {form.hookRender.map((item, index) => {
                        return <div key={index}>
                            <RenderFormFieldByList item={item} form={form} />
                        </div>
                    })}
                </div>
            </PopupLayout> : null}
        </div>
    );
}