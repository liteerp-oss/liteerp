import React, { useCallback, useMemo, useState } from 'react';
import SearchSelect from '../../UI/Input/SearchSelect';
import { InputForm } from '../../UI/Input/InputForm';
import { Select } from '../../UI/Input/Select';
import TextArea from '../../UI/Input/Textarea';
import SupplierService from '../../../services/SupplierService';
import { isoToDateTime } from '../../../libraries/common';
import RenderFormFieldByList from '../../RenderFormFieldByList';
import { useI18n } from '../../../../i18n/useI18n';
import { useNavigate } from 'react-router-dom';

export default function PurchaseInformation({
    form = {
        formData: null,
        handleChange: null,
        formErrors: null,
        handleChangeByKey: null,
        hookRender: [],
    },
}) {
    const { t } = useI18n();
    const navigate = useNavigate();
    const [supplierData, setSupplierData] = useState([]);

    const getSuppliers = useCallback((keywords = '', callback = null) => {
        SupplierService.list({
            page: 0,
            keywords: keywords,
            active: 1
        })
            .then((resp) => {
                setSupplierData(resp.message?.data);
                if (callback) callback();
            })
            .catch(() => {
                // ignore
            });
    }, []);

    const disabled = useMemo(() => {
        return form.formData?.status && form.formData?.status !== 'draft';
    }, [form.formData?.status]);

    return (
        <div>
            <h5>{t('Purchase information')}</h5>

            <div className="row">
                <div className="col-6">
                    <div className='d-flex'>
                        <div className='flex-grow-1'>
                            <SearchSelect
                                errorMessage={form.formErrors?.supplier_id}
                                disabled={disabled}
                                search={getSuppliers}
                                value={form.formData?.supplier_id}
                                changeValue={form.handleChangeByKey}
                                options={supplierData?.map((item) => ({
                                    value: item.id,
                                    label: item.unit_name,
                                }))}
                                name="supplier_id"
                                defaultKeywords={form.formData?.supplier_name}
                                label={t('Supplier')}
                                required={true}
                            />
                        </div>

                        <div
                            className='d-flex justify-content-center align-items-center ms-2'
                            onClick={() => {
                                navigate('/suppliers')
                            }}
                        >
                            <i className="bi bi-plus-square text-success" style={{
                                fontSize: 30,
                                paddingTop: 20
                            }}></i>
                        </div>
                    </div>
                </div>

                <div className="col-6">
                    <InputForm
                        disabled={disabled}
                        type="date"
                        handleChange={form.handleChange}
                        value={isoToDateTime(
                            form.formData?.purchase_date ?? new Date()
                        )}
                        errorMessage={form.formErrors?.purchase_date}
                        name="purchase_date"
                        label={t('Purchase date')}
                        required={true}
                    />
                </div>
            </div>

            <div className="row mt-2">
                <div className="col-4">
                    <InputForm
                        disabled={disabled}
                        type="date"
                        handleChange={form.handleChange}
                        value={isoToDateTime(
                            form.formData?.expected_date ?? new Date()
                        )}
                        errorMessage={form.formErrors?.expected_date}
                        name="expected_date"
                        label={t('Expected date')}
                        required={true}
                    />
                </div>

                <div className="col-4">
                    <Select
                        disabled={disabled}
                        name="payment_method"
                        value={form.formData?.payment_method}
                        handleChange={form.handleChange}
                        errorMessage={form.formErrors?.payment_method}
                        options={[
                            { value: 'cash', label: t('Cash') },
                            { value: 'bank', label: t('Bank') },
                            { value: 'transfer', label: t('Transfer') },
                            { value: 'other', label: t('Other') },
                        ]}
                        label={t('Payment method')}
                        required={true}
                    />
                </div>

                <div className="col-4">
                    <InputForm
                        disabled={disabled}
                        type="number"
                        handleChange={form.handleChange}
                        value={form.formData?.shipping_fee}
                        errorMessage={form.formErrors?.shipping_fee}
                        name="shipping_fee"
                        placeholder={t('Enter full shipping fee')}
                        label={t('Shipping fee')}
                        required={true}
                    />
                </div>
            </div>

            <div className="row mt-2">
                <div>
                    <label>{t('Note')}</label>
                    <TextArea
                        disabled={disabled}
                        handleChange={form.handleChange}
                        value={form.formData?.note}
                        errorMessage={form.formErrors?.note}
                        name="note"
                        placeholder={t('Note purchase')}
                    />
                </div>
            </div>

            {form.hookRender.map((item, index) => (
                <div className="row" key={index}>
                    <RenderFormFieldByList item={item} form={form} />
                </div>
            ))}
        </div>
    );
}
