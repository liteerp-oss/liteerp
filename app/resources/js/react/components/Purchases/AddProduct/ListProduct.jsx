import React, { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import CommonDataTable from '../../CommonDataTable';
import ProductService from '../../../services/ProductService';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import { usePopup } from '../../popups/PopupContext';
import PrimaryButton from '../../UI/Buttons/PrimaryButton'
import { useForm } from '../../../libraries/handleInput';
import useTable from '../../../libraries/handleTable';
import PurchaseItemService from '../../../services/PurchaseItemService';
import { PopupLayout } from '../../../layouts/PopupLayout';
import { InputForm } from '../../UI/Input/InputForm';
import SearchSelect from '../../UI/Input/SearchSelect';
import PurchaseTaxService from '../../../services/PurchaseTaxService';
import Currencies from '../../Currencies';
import PurchaseService from '../../../services/PurchaseService';
import { useDispatch } from 'react-redux';
import { setPurchaseDetail } from '../../../redux/purchase/detailSlice';
import { useI18n } from '../../../../i18n/useI18n';
import RenderFormTableByList from '../../RenderFieldTableByList'
export default function ListProducts({
    purchase = null
}) {
    const { t } = useI18n();
    const dispatch = useDispatch();
    const [checkList, setCheckList] = useState([]);
    const [isCheckAll, setIsCheckAll] = useState(false);
    const [showForm, setShowForm] = useState(false);
    const [showTaxForm, setShowTaxForm] = useState(false);
    const table = useTable();
    const [searchParams] = useSearchParams();
    const form = useForm();
    const taxForm = useForm();
    const { openPopup } = usePopup();
    const [products, setProducts] = useState([])
    const navigate = useNavigate();
    const getPurchaseDetail = useCallback(() => {
        PurchaseService.show(searchParams.get('id'))
            .then((resp) => {
                dispatch(setPurchaseDetail(resp.message))
            })
            .catch((error) => {
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message,
                        onCancel: () => {
                            navigate('/purchases')
                        }
                    })
                }
            })
    }, []);

    const handleEdit = (row) => {
        form.setFormData(row);
        form.setIsEdit(true);
        setShowForm(true);
    };


    const create = useCallback(() => {
        form.setLoading(true);
        form.setFormErrors(null);
        PurchaseItemService.add({
            ...form.formData,
            purchase_id: searchParams.get('id')
        })
            .then(() => {
                getPurchaseItems();
                openPopup({
                    type: 'success',
                    message: 'You has been created',
                    onConfirm: () => {
                        setShowForm(false);
                    }
                });
                form.setFormData(null)
                form.setLoading(false);
                getPurchaseDetail();
            })
            .catch((error) => {
                if (error.response?.data?.errors) {
                    form.setFormErrors(error.response.data.errors);
                }
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
                form.setLoading(false);
            });
    }, [form.formData, searchParams]);
    const update = useCallback(() => {
        form.setLoading(true);
        form.setFormErrors(null);
        PurchaseItemService.update(form.formData)
            .then(() => {
                getPurchaseItems();
                openPopup({
                    type: 'success',
                    message: 'You has been update',
                    onConfirm: () => {
                        setShowForm(false);
                    }
                });
                form.setFormData(null);
                setShowForm(false);
                form.setLoading(false);
                getPurchaseDetail();
            })
            .catch((error) => {
                if (error.response?.data?.errors) {
                    form.setFormErrors(error.response.data.errors);
                }
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
                form.setLoading(false);
            });
    }, [form.formData, searchParams]);

    const destroy = useCallback((row) => {
        PurchaseItemService.delete(row)
            .then(() => {
                getPurchaseItems();
                openPopup({
                    type: 'success',
                    message: 'You has been update',
                    onConfirm: () => {
                        setShowForm(false);
                    }
                });
                getPurchaseDetail();
            })
            .catch((error) => {
                if (error.response?.data?.errors) {
                    form.setFormErrors(error.response.data.errors);
                }
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
            });
    }, []);

    const handleDelete = (row) => {
        openPopup({
            type: 'warning',
            message: 'Are you sure to delete?',
            onConfirm: () => {
                destroy(row)
            }
        })
    };

    const getPurchaseItems = useCallback((page = 0) => {
        table.setLoading(true);
        PurchaseItemService.list({
            purchase_id: searchParams.get('id'),
            page: page
        })
            .then((resp) => {
                table.setData(resp.message.data);
                table.setLinks(resp.message.links);
                table.setLoading(false);
            })
            .catch((error) => {

            });
    }, [searchParams]);
    const getProducts = useCallback((keywords = '', callback = null) => {
        ProductService.list({
            keywords: keywords,
            page: 0,
            active: 1
        })
            .then((resp) => {
                setProducts(resp.message.data)
                if (callback) {
                    callback();
                }
            })
            .catch((error) => {

            });
    }, [searchParams]);
    const checkAll = useCallback(() => {
        setIsCheckAll(true)
        openPopup({
            type: 'warning',
            message: 'Are you sure select all',
            onConfirm: () => {
                let l = [];
                table.data?.map((item) => {
                    l.push(item.id)
                })
                setCheckList(l);
            }
        })
    }, [table.data]);
    const uncheckAll = useCallback(() => {
        setIsCheckAll(false)
        setCheckList([]);
    }, []);
    const setTax = () => {
        if (checkList.length === 0) {
            return;
        }
        PurchaseTaxService.add({
            id: searchParams.get('id'),
            purchase_item_id: checkList,
            tax: taxForm.formData?.tax
        })
            .then((resp) => {
                setIsCheckAll(false);
                setCheckList([]);
                openPopup({
                    type: 'success',
                    message: 'You has been updated'
                });
                setShowTaxForm(false);
                getPurchaseItems();
                taxForm.setFormData(null);
            })
            .catch((error) => {

            })
    };
    useEffect(() => {
        table.setColums([
            {
                label: t('Select'),
                key: 'id',
                render: (id) => (
                    <input
                        disabled={purchase.status !== 'draft'}
                        type="checkbox"
                        checked={checkList.includes(id)}
                        onChange={(e) =>
                            e.target.checked
                                ? setCheckList((p) => [...p, id])
                                : setCheckList((p) => p.filter((x) => x !== id))
                        }
                    />
                ),
            },
            {
                label: t('Image'),
                key: 'image',
                render: (src) =>
                    src ? (
                        <img
                            src={src}
                            alt="product"
                            style={{
                                width: 50,
                                height: 50,
                                borderRadius: 8,
                                objectFit: 'cover',
                            }}
                        />
                    ) : (
                        <img
                            src="/assets/icons/default_image.png"
                            width={50}
                            height={50}
                            alt=""
                        />
                    ),
            },
            { label: t('Name'), key: 'name' },
            { label: t('SKU'), key: 'sku' },
            { label: t('Category'), key: 'category_name' },
            { label: t('Buy'), key: 'buy_quantity' },
            { label: t('Gift'), key: 'gift_quantity' },
            { label: t('Compensation'), key: 'compensation_quantity' },
            { label: t('Conversion'), key: 'conversion_quantity' },
            {
                label: t('Unit cost'),
                key: 'unit_cost',
                render: (value) => <Currencies amount={value} />,
            },
            {
                label: t('Subtotal'),
                key: 'subtotal',
                render: (value) => <Currencies amount={value} />,
            },
            {
                label: t('Tax'),
                key: 'tax',
                render: (value) => <span>{value}%</span>,
            },
            {
                label: t('Total tax'),
                key: 'total_tax',
                render: (value) => <Currencies amount={value} />,
            },
            {
                label: t('Total price'),
                key: 'total',
                render: (value) => <Currencies amount={value} />,
            },
        ])
        getPurchaseItems();
        getViewPurchaseItem();
    }, []);
    const getViewPurchaseItem = () => {
        PurchaseItemService.view().then((resp) => {
            //form.setHookRender(resp.message?.form)
            table.addColums(resp.message.index, (item, data) => (
                <RenderFormTableByList item={item} data={data} />
            ))
        }).catch((error) => {

        })
    }
    return (
        <div className="">
            <div className="d-flex justify-content-between align-items-center">
                <div>
                    <h5 className="fw-bold mb-2 theme-title">{t('List products')}</h5>
                </div>
            </div>

            <CommonDataTable
                filter={<div className='row'>
                    <div className='col-3'>
                        <PrimaryButton
                            width={120}
                            disabled={purchase?.status !== 'draft' || table.data.length === 0}
                            onClick={!isCheckAll ? checkAll : uncheckAll} label={t('Check all')} />
                    </div>
                    <div className='col-3'>
                        <PrimaryButton
                            width={120}
                            disabled={checkList.length >= 1 ? false : true}
                            onClick={() => {
                                if (checkList.length >= 1) {
                                    return setShowTaxForm(true)
                                }
                            }} label={t('Tax') + ' (%)'} />
                    </div>
                </div>}
                movePage={getPurchaseItems}
                loading={table.loading}
                add={purchase?.status !== 'draft' ? null : () => setShowForm(true)}
                links={table.links}
                columns={table.colums}
                data={table.data}
                onEdit={purchase?.status !== 'draft' ? null : handleEdit}
                onDelete={purchase?.status !== 'draft' ? null : handleDelete}
            />
            {showForm ? (
                <PopupLayout
                    loading={form.loading}
                    confirmText={t('Save change')}
                    onClose={() => {
                        setShowForm(false);
                        form.setIsEdit(false);
                    }}
                    onConfirm={form.isEdit ? update : create}
                    title={t('Add product')}
                >
                    <h4>{t('Information')}</h4>

                    <div className="mb-3 form-group">
                        <SearchSelect
                            placeholder={t('Search by name or sku')}
                            name="product_id"
                            value={form.formData?.product_id}
                            changeValue={form.handleChangeByKey}
                            errorMessage={form.formErrors?.product_id}
                            search={getProducts}
                            options={products.map((item) => ({
                                value: item.id,
                                label: `${item.name} | ${item.category} | ${item.sku}`,
                            }))}
                            defaultKeywords={form.formData?.name ?? ''}
                            label={t("Product")}
                            required={true}
                        />
                    </div>

                    <div className="row">
                        <div className="mb-3 form-group col-6">
                            <InputForm
                                name="discount"
                                type="number"
                                value={form.formData?.discount}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.discount}
                                label={t("Discount")}
                                required={false}
                            />
                        </div>

                        <div className="mb-3 form-group col-6">
                            <InputForm
                                name="tax"
                                type="number"
                                value={form.formData?.tax}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.tax}
                                label={t("Tax") + ' (%)'}
                                required={true}
                            />
                        </div>
                    </div>

                    <div className="mb-3 form-group">
                        <InputForm
                            name="product_link"
                            type="text"
                            value={form.formData?.product_link}
                            handleChange={form.handleChange}
                            errorMessage={form.formErrors?.product_link}
                            label={t("Product link")}
                            required={false}
                        />
                    </div>

                    <div className="row">
                        <div className="mb-3 form-group col-6">
                            <InputForm
                                name="buy_quantity"
                                type="text"
                                value={form.formData?.buy_quantity}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.buy_quantity}
                                label={t("Buy quantity")}
                                required={false}
                            />
                        </div>

                        <div className="mb-3 form-group col-6">
                            <InputForm
                                name="gift_quantity"
                                type="number"
                                value={form.formData?.gift_quantity}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.gift_quantity}
                                label={t("Gift quantity")}
                                required={false}
                            />
                        </div>

                        <div className="mb-3 form-group col-6">
                            <InputForm
                                name="compensation_quantity"
                                type="number"
                                value={form.formData?.compensation_quantity}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.compensation_quantity}
                                label={t("Compensation quantity")}
                                required={false}
                            />
                        </div>

                        <div className="mb-3 form-group col-6">
                            <InputForm
                                name="conversion_quantity"
                                type="number"
                                value={form.formData?.conversion_quantity}
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.conversion_quantity}
                                label={t("Conversion quantity")}
                                required={false}
                            />
                        </div>
                    </div>

                    <div className="mb-3 form-group">
                        <InputForm
                            name="unit_cost"
                            type="number"
                            value={form.formData?.unit_cost}
                            handleChange={form.handleChange}
                            errorMessage={form.formErrors?.unit_cost}
                            label={t("Unit cost")}
                            required={false}
                        />
                    </div>
                </PopupLayout>
            ) : null}

            {showTaxForm ? <PopupLayout
                onConfirm={setTax}
                title={t('Set tax products')}
                confirmText={t('Change')}
                onClose={() => {
                    setShowTaxForm(false)
                }}>
                <div>
                    <InputForm
                        name='tax'
                        type='number'
                        errorMessage={taxForm.formErrors?.tax}
                        handleChange={taxForm.handleChange}
                        value={taxForm.formData?.tax}
                        placeholder='Enter tax for products selected'
                        label={t("Tax") + ' (%)'}
                        required={true}
                    />
                </div>
            </PopupLayout> : null}
        </div>
    );
}