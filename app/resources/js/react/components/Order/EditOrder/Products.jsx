import React, { useCallback, useEffect, useState } from 'react'
import ListProduct from './Products/ListProduct'
import ProductAdded from './Products/ProductAdded'
import { useForm } from '../../../libraries/handleInput'
import { PopupLayout } from '../../../layouts/PopupLayout'
import { InputForm } from '../../UI/Input/InputForm'
import OrderItemService from '../../../services/OrderItemService'
import { usePopup } from '../../popups/PopupContext'
import { useSearchParams } from 'react-router-dom'
import useTable from '../../../libraries/handleTable'
import { setSummary } from '../../../redux/order/summarySlice'
import { useDispatch } from 'react-redux'
import { useI18n } from '@/i18n/useI18n'
export default function Products({
    detail = null
}) {
    const { t } = useI18n();
    const dispatch = useDispatch();
    const form = useForm();
    const searchOrderItem = useForm();
    const [showForm, setShowForm] = useState(false);
    const [searchParams] = useSearchParams();
    const { openPopup } = usePopup();
    const add = useCallback((product) => {
        form.setFormData(product);
        setShowForm(true)
    }, [form]);
    const table = useTable();
    const getSummary = useCallback(() => {
        OrderItemService.summary({
            order_id: searchParams.get('id')
        })
            .then((resp) => {
                dispatch(setSummary(resp.message));
                //setSummaryData(resp.message);
            })
            .catch((error) => {

            })
    }, [searchParams]);
    const addInventory = useCallback(() => {
        if (Number(form.formData?.buy_quantity ?? 0) === 0
            && Number(form.formData?.compensation_quantity ?? 0) === 0
            && Number(form.formData?.conversion_quantity ?? 0) === 0
            && Number(form.formData?.gift_quantity ?? 0) === 0) {
            openPopup({
                type: 'error',
                message: t("empty_quantity")
            })
            return;
        }
        const submitData = {
            ...form.formData,
            order_id: searchParams.get('id'),
            inventory_id: form.formData?.id
        }
        form.setLoading(true)
        OrderItemService.add(submitData)
            .then((resp) => {
                getOrderItem();
                openPopup({
                    type: 'success',
                    message: t('You has been added')
                })
                form.setLoading(false)
                setShowForm(false)
                getSummary();
            })
            .catch((error) => {
                if (error.response.data?.errors) {
                    form.setFormErrors(error.response.data?.errors);
                }
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
                form.setLoading(false)
                setShowForm(false)
            })
    }, [form.formData]);
    const updateInventory = useCallback(() => {
        if (Number(form.formData?.buy_quantity ?? 0) === 0
            && Number(form.formData?.compensation_quantity ?? 0) === 0
            && Number(form.formData?.conversion_quantity ?? 0) === 0
            && Number(form.formData?.gift_quantity ?? 0) === 0) {
            openPopup({
                type: 'error',
                message: t("empty_quantity")
            })
            return;
        }
        form.setLoading(true)
        OrderItemService.update(form.formData)
            .then((resp) => {
                getOrderItem();
                openPopup({
                    type: 'success',
                    message: t('You has been updated')
                })
                form.setLoading(false);
                setShowForm(false)
                getSummary();
            })
            .catch((error) => {
                if (error.response.data?.errors) {
                    form.setFormErrors(error.response.data?.errors);
                }
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
                form.setLoading(false)
                setShowForm(false)
            })
    }, [form.formData]);
    const deleteInventory = useCallback((row) => {
        OrderItemService.delete(row)
            .then((resp) => {
                getOrderItem();
                openPopup({
                    type: 'success',
                    message: t('You has been deleted')
                })
                getSummary();
            })
            .catch((error) => {
                if (error.response.data?.errors) {
                    form.setFormErrors(error.response.data?.errors);
                }
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
            })
    }, []);
    const confirmDelete = useCallback((row) => {
        openPopup({
            type: 'warning',
            message: t('Are you sure to delete?'),
            onConfirm: () => {
                deleteInventory(row)
            }
        })
    }, []);
    const getOrderItem = useCallback((page = 0) => {
        table.setLoading(true);
        OrderItemService.list({
            order_id: searchParams.get('id'),
            page: page,
            ...searchOrderItem.formData
        })
            .then((resp) => {
                table.setData(resp.message.data);
                table.setLoading(false);
                table.setLinks(resp.message.links);
            })
            .catch((error) => {
                table.setLoading(false);
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
            })
    }, [searchParams, searchOrderItem.formData]);

    useEffect(() => {
        getOrderItem();
    }, [])
    return <div>
        <ProductAdded onDelete={confirmDelete} search={searchOrderItem} callback={getOrderItem} table={table}
            form={form} setShowForm={setShowForm} />
        <ListProduct detail={detail} add={add} />
        <div>
            {showForm ? <PopupLayout
                loading={form.loading}
                onClose={() => setShowForm(false)}
                title={form.isEdit ? t('Update product order') : t('Add product order')}
                confirmText={form.isEdit ? t('Add') : t('Update')}
                onConfirm={form.isEdit ? updateInventory : addInventory}>
                <div>
                    <div>
                        <div className='row'>
                            <div className='form-group col-6'>
                                <label>{t("Discount")}(%)</label>
                                <InputForm handleChange={form.handleChange}
                                    name='discount' value={form.formData?.discount}
                                    errorMessage={form.formErrors?.discount} />
                            </div>
                            <div className='form-group col-6'>
                                <label>{t("Buy quantity")}</label>
                                <InputForm handleChange={form.handleChange}
                                    name='buy_quantity' value={form.formData?.buy_quantity}
                                    errorMessage={form.formErrors?.buy_quantity} />
                            </div>
                        </div>
                        <div className='row mt-3'>
                            <div className='form-group col-6'>
                                <label>{t("Gift quantity")}</label>
                                <InputForm handleChange={form.handleChange}
                                    name='gift_quantity' value={form.formData?.gift_quantity}
                                    errorMessage={form.formErrors?.gift_quantity} />
                            </div>
                            <div className='form-group col-6'>
                                <label>{t("Compensation quantity")}</label>
                                <InputForm handleChange={form.handleChange}
                                    name='compensation_quantity' value={form.formData?.compensation_quantity}
                                    errorMessage={form.formErrors?.compensation_quantity} />
                            </div>
                        </div>
                        <div className='row mt-3'>
                            <div className='form-group col-6'>
                                <label>{t("Conversion quantity")}</label>
                                <InputForm handleChange={form.handleChange}
                                    name='conversion_quantity' value={form.formData?.conversion_quantity}
                                    errorMessage={form.formErrors?.conversion_quantity} />
                            </div>
                            <div className='form-group col-6'>
                                <label>{t("Price")}</label>
                                <InputForm handleChange={form.handleChange}
                                    name='price'
                                    value={form.formData?.price}
                                    errorMessage={form.formErrors?.price} />
                            </div>
                        </div>
                    </div>
                </div>
            </PopupLayout> : null}

        </div>
    </div>
}