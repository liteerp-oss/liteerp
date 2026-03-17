import React, { useCallback, useEffect, useMemo, useState } from "react";
import StockInService from "../../services/StockInService";
import { useSearchParams } from "react-router-dom";
import { useForm } from "../../libraries/handleInput";
import { isoToDateTime } from "../../libraries/common";
import SummaryRow from "./StockInDetail/SummaryRow";
import TwoCol from "./StockInDetail/TwoCol";
import InfoBox from './StockInDetail/InfoBox'
import useTable from "../../libraries/handleTable";
import CommonDataTable from "../CommonDataTable";
import SecondaryButton from "../UI/Buttons/SecondaryButton";
import PrimaryButton from "../UI/Buttons/PrimaryButton";
import { PopupLayout } from "../../layouts/PopupLayout";
import { InputForm } from "../UI/Input/InputForm";
import { usePopup } from "../popups/PopupContext";
import PurchaseItemService from "../../services/PurchaseItemService";
import InventoryForm from "./StockInDetail/InventoryForm";
import StockMovementInService from '../../services/StockMovementInService'
import PageHead from "../PageHead";
import LoadingBox from '../LoadingBox'
import RenderFormFieldByList from '../RenderFormFieldByList'
import { ExtraCard } from "../ExtraCard";
import { useI18n } from "../../../i18n/useI18n";
import StatusBadge from "../StatusBadge";
import CommonDataTableV2 from "../CommonDataTableV2";
export default function StockInDetail() {
    const { t } = useI18n();
    const [loading, setLoading] = useState(false);
    const [showForm, setShowForm] = useState(false);
    const [showFormInventory, setShowFormInventory] = useState(false);
    const [showUpdateFormInventory, setUpdateShowFormInventory] = useState(false);
    const form = useForm();
    const searchProduct = useForm();
    const searchInventory = useForm();
    const [detail, setDetail] = useState(null)
    const formInventory = useForm();
    const table = useTable();
    const inventoryTable = useTable();
    const { openPopup } = usePopup();
    const [searchParams] = useSearchParams();
    const navigate = useState();
    const getDetail = useCallback(() => {
        setLoading(true)
        StockInService.show(searchParams.get('stockin'))
            .then((resp) => {
                form.setFormData(resp.message);
                setDetail(resp.message)
                setLoading(false)
            })
            .catch((error) => {
                navigate('/stock')
            });
    }, []);
    const getProducts = useCallback((page = 0) => {
        table.setLoading(true);
        PurchaseItemService.list({
            page: page,
            purchase_id: form.formData?.purchase_id,
            ...searchProduct.formData
        })
            .then((resp) => {
                table.setLoading(false);
                table.setData(resp.message.data);
                table.setLinks(resp.message.links);
                table.setTotal(resp.message.total)
            })
            .catch((error) => {

            })
    }, [form.formData,searchProduct]);
    const update = useCallback(() => {
        form.setLoading(true);
        form.setFormErrors(null)
        StockInService.update(form.formData)
            .then((resp) => {
                openPopup({
                    type: 'success',
                    message: t('You has been confirmed')
                })
                getDetail();
                setShowForm(false)
                form.setLoading(false);
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
                form.handleChangeByKey('status', detail?.status);
                form.setLoading(false);
            })
    }, [form.formData, detail?.status]);
    const confirmRecieve = useCallback(() => {
        openPopup({
            type: 'warning',
            message: t('Are you sure to wanna confirm approved'),
            onConfirm: () => {
                form.handleChangeByKey('status', 'received');
            }
        })
    }, [form]);
    const view = useCallback(() => {
        StockInService.view().then((resp) => {
            table.addColums(resp.message.index, (item, data) => {
                return <RenderFieldTableByList item={item} data={data} />
            })
            form.setHookRender(resp.message.form)
        })
            .catch((error) => {
                if (error.response.message?.errors) {
                    openPopup({
                        type: 'error',
                        message: error.response.message?.errors
                    })
                }
            })
    }, [table]);
    useEffect(() => {
        if (form.formData?.status === 'received' &&
            detail?.status === 'pending') {
            update();
        }
    }, [form.formData?.status, detail?.status])
    const handleAdd = (row) => {
        formInventory.setFormData(row);
        setShowFormInventory(true);
        setUpdateShowFormInventory(false);
    }
    const handleEdit = (row) => {
        formInventory.setFormData(row);
        setShowFormInventory(false);
        setUpdateShowFormInventory(true);
    }
    const createInventory = useCallback(() => {
        formInventory.setLoading(true);
        StockMovementInService.add({
            ...formInventory.formData,
            stock_in_id: searchParams.get('stockin'),
            purchase_item_id: formInventory.formData?.id
        })
            .then((resp) => {
                openPopup({
                    type: 'success',
                    message: t('You has been added')
                })
                setShowFormInventory(false)
                formInventory.setLoading(false);
                getInventories();
            })
            .catch((error) => {
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
                if (error.response.data?.errors) {
                    formInventory.setFormErrors(error.response?.data?.errors)
                }
                formInventory.setLoading(false);
            })
    }, [formInventory, searchParams])
    const updateInventory = useCallback(() => {
        formInventory.setLoading(true);
        StockMovementInService.update({
            ...formInventory.formData,
            stock_in_id: searchParams.get('stockin'),
            purchase_item_id: formInventory.formData?.id
        })
            .then((resp) => {
                openPopup({
                    type: 'success',
                    message: t('You has been added')
                })
                setUpdateShowFormInventory(false)
                formInventory.setLoading(false);
                getInventories();
            })
            .catch((error) => {
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
                if (error.response.data?.errors) {
                    formInventory.setFormErrors(error.response?.data?.errors)
                }
                formInventory.setLoading(false);
            })
    }, [formInventory, searchParams])
    const getInventories = useCallback((page = 0) => {
        inventoryTable.setLoading(true);
        StockMovementInService.list({
            page: page,
            stock_in_id: searchParams.get('stockin'),
            ...searchInventory.formData
        })
            .then((resp) => {
                inventoryTable.setLoading(false);
                inventoryTable.setData(resp.message.data)
                inventoryTable.setLinks(resp.message.links)
            })
            .catch((error) => {
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
            })
    }, [searchParams,searchInventory])
    useEffect(() => {
        if (!searchParams.get('stockin')) {
            return;
        }
        getDetail();
    }, []);
    useEffect(() => {
        if (form.formData?.purchase_id) {
            getProducts();
            getInventories();
        }

        view();
    }, [form.formData?.purchase_id]);
    return (
        <div className="min-vh-100">
            <PageHead
                containerClass=""
                title={t("Detail stock in")}
                subtitle={t("stockin_desc")}
            />
            <div className="mt-3">
                {loading ? <LoadingBox /> :
                    <div>
                        <div className="row g-3 mb-4">
                            <InfoBox label={t("Purchase ID")} value={'PO' + form.formData?.purchase_id} />
                            <InfoBox label={t("Stock In ID")} value={form.formData?.id} />
                            <InfoBox label={t("Import date")} value={isoToDateTime(form.formData?.import_date)} />
                            <InfoBox label={t("Expected date")} value={form.formData?.due_date ?? '-'} />
                        </div>

                        <div className="row g-4">

                            <div className="col-lg-9">
                                <div className="p-4 rounded border">
                                    <div className="d-flex justify-content-between mb-3">
                                        <h5 className="fw-semibold">{t("Stock information")}</h5>
                                    </div>

                                    <TwoCol
                                        label={t("Supplier")}
                                        left={form.formData?.unit_name}
                                        rightLabel={t('Purchase approved')}
                                        right={form.formData?.purchase_approved_name}
                                    />

                                    <TwoCol
                                        label={t("Staff")}
                                        left={form.formData?.purchase_approved_name ?? '-'}
                                        rightLabel={t("Status")}
                                        right={<StatusBadge status={form.formData?.status ?? ''}/>}
                                    />

                                    <div className="mt-3">
                                        <div className="theme-title small">{t("Note")}</div>
                                        <div>{form.formData?.purchase_note ?? '-'}</div>
                                    </div>
                                </div>

                                <ExtraCard form={form} />

                                {/* Product List */}
                                <div className="rounded mt-4 mb-5">
                                    <div className="d-flex justify-content-between mb-3">
                                        <h5 className="fw-semibold">{t("Purchase items")}</h5>
                                        <div className="theme-title small">{table.total} {t("products")}</div>
                                    </div>

                                    <CommonDataTableV2
                                        columns={[
                                            { label: t("ID"), key: "id" },
                                            { label: t("Supplier"), key: "unit_name" },
                                            { label: t("Name"), key: "name" },
                                            { label: t("Category"), key: "category_name" },
                                            { label: t("Quantity"), key: "quantity" },
                                            { label: t("Sku"), key: "sku" },
                                            { label: t("Unit"), key: "unit" }
                                        ]}
                                        data={table.data}
                                        links={table.links}
                                        loading={table.loading}
                                        onEdit={form.formData?.status === 'pending' ? handleAdd : null}
                                        iconEdit={<i className="bi bi-plus-circle-dotted"></i>}
                                        type="stockin"
                                        callback={getProducts}
                                        search={searchProduct}
                                    />
                                </div>

                                {/* Inventory */}
                                <div className="rounded mt-4 mb-5">
                                    <div className="d-flex justify-content-between mb-3">
                                        <h5 className="fw-semibold">{t("Inventory")}</h5>
                                        <div className="theme-title small">{table.total} {t("products")}</div>
                                    </div>

                                    <CommonDataTableV2
                                        callback={getInventories}
                                        columns={[
                                            { label: t("ID"), key: "id" },
                                            { label: t("Supplier"), key: "unit_name" },
                                            { label: t("Name"), key: "name" },
                                            { label: t("Category"), key: "category" },
                                            { label: t("Quantity"), key: "qty_change" },
                                            { label: t("Sku"), key: "sku" },
                                            { label: t("Unit"), key: "unit" },
                                            { label: t("Warehouses"), key: "warehouse" }
                                        ]}
                                        data={inventoryTable.data}
                                        links={inventoryTable.links}
                                        loading={inventoryTable.loading}
                                        type="stockin"
                                        onEdit={form.formData?.status === 'pending' ? handleEdit : null}
                                        search={searchInventory}
                                    />
                                </div>
                            </div>

                            {/* Summary Box */}
                            <div className="col-lg-3">
                                <div className="p-4 rounded mb-4 border">
                                    <h5 className="fw-semibold mb-3">{t("Warehouse")}</h5>
                                    <SummaryRow label={t("Quantity")} value={table.total} />
                                    <SummaryRow label={t("Staff") + ":"} value={form.formData?.approved_name ?? '-'} />
                                    <SummaryRow label={t("Import date") + ":"} value={form.formData?.import_date || "15/01/2024"} />
                                </div>

                                {/* Inspection box */}
                                {form.formData?.status === 'pending' ? (
                                    <div className="row g-2">
                                        <div className="col-6">
                                            <PrimaryButton
                                                width={'100%'}
                                                loading={form.loading}
                                                onClick={confirmRecieve}
                                                label={t("Received")}
                                            />
                                        </div>
                                        <div className="col-6">
                                            <SecondaryButton
                                                loading={form.loading}
                                                onClick={() => setShowForm(true)}
                                                width={'100%'}
                                                label={t("Modifier")}
                                            />
                                        </div>
                                    </div>
                                ) : null}
                            </div>
                        </div>
                    </div>
                }</div>
            {showForm ? (
    <PopupLayout
        loading={form.loading}
        confirmText={t("Save change")}
        onClose={() => setShowForm(false)}
        title={t("Update Stock In")}
        onConfirm={() => update()}
    >
        <div>
            <div>
                <label>{t("Import date")}</label>
                <InputForm
                    errorMessage={form.formErrors?.import_date}
                    value={form.formData?.import_date}
                    name="import_date"
                    handleChange={form.handleChange}
                    type="date"
                />
            </div>
            {form.hookRender.map((item, index) => {
                return (
                    <div className="mt-3" key={index}>
                        <RenderFormFieldByList item={item} form={form} />
                    </div>
                );
            })}
        </div>
    </PopupLayout>
) : null}

{showFormInventory ? (
    <PopupLayout
        loading={formInventory.loading}
        confirmText={t("Save")}
        onClose={() => setShowFormInventory(false)}
        title={t("Add Inventory")} 
        onConfirm={ createInventory}
    >
        <div>
            <InventoryForm form={formInventory} />
        </div>
    </PopupLayout>
) : null}

{showUpdateFormInventory ? (
    <PopupLayout
        loading={formInventory.loading}
        confirmText={t("Save")}
        onClose={() => setShowFormInventory(false)}
        title={t("Update Inventory")} 
        onConfirm={ updateInventory}
    >
        <div>
            <InventoryForm form={formInventory} />
        </div>
    </PopupLayout>
) : null}

        </div>
    );
}