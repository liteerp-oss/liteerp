import React, { useCallback, useState } from 'react';
import SearchSelect from '../../UI/Input/SearchSelect';
import { Select } from '../../UI/Input/Select';
import { InputForm } from '../../UI/Input/InputForm';
import TextArea from '../../UI/Input/Textarea';
import CustomerService from '../../../services/CustomerService';
import RenderFormFieldByList from '../../RenderFormFieldByList';
import { useI18n } from '../../../../i18n/useI18n';

export default function CustomerInformation({
    form = {
        formData: null,
        formErrors: null,
    },
}) {
    const { t } = useI18n();
    const [customers, setCustomers] = useState([]);

    const getCustomers = useCallback((keywords = '', callback = null) => {
        CustomerService.list({
            keywords,
            page: 1,
            active: 1
        })
            .then((resp) => {
                setCustomers(resp.message.data);
                if (callback) callback();
            })
            .catch(() => {});
    }, []);

    return (
        <div>
            <h4 className="h5">{t('Customer information')}</h4>

            <div>
                <div className="row">
                    <div className="px-2">
                        <div
                            className="alert alert-warning alert-dismissible fade show"
                            role="alert"
                        >
                            <strong>{t('Note!')}</strong>{' '}
                            {t(
                                'You can keep empty to system automatically create order number'
                            )}
                            <button
                                type="button"
                                className="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="Close"
                            ></button>
                        </div>
                    </div>

                    <div className="form-group col-6">
                        <SearchSelect
                            name="customer_id"
                            search={getCustomers}
                            changeValue={form.handleChangeByKey}
                            value={form.formData?.customer_id}
                            errorMessage={form.formErrors?.customer_id}
                            options={customers.map((item) => ({
                                value: item.id,
                                label: item.name,
                            }))}
                            defaultKeywords={form.formData?.customer_name}
                            required={true}
                            label={t('Customer')}
                        />
                    </div>

                    <div className="form-group col-6">
                        <Select
                            name="type"
                            handleChange={form.handleChange}
                            value={form.formData?.type}
                            errorMessage={form.formErrors?.type}
                            options={[
                                { value: 'retail', label: t('Retail') },
                                { value: 'wholesale', label: t('Wholesale') },
                            ]}
                            required={true}
                            label={t('Type')}
                        />
                    </div>
                </div>

                <div className="row mt-3">
                    <div className="form-group col-4">
                        <InputForm
                            type="date"
                            name="expected_delivery_date"
                            handleChange={form.handleChange}
                            value={form.formData?.expected_delivery_date}
                            errorMessage={
                                form.formErrors?.expected_delivery_date
                            }
                            required={true}
                            label={t('Expected delivery date')}
                        />
                    </div>

                    <div className="form-group col-4">
                        <InputForm
                            type="date"
                            name="order_date"
                            handleChange={form.handleChange}
                            value={form.formData?.order_date}
                            errorMessage={form.formErrors?.order_date}
                            required={true}
                            label={t('Order date')}
                        />
                    </div>

                    <div className="form-group col-4">
                        <InputForm
                            type="text"
                            name="order_no"
                            handleChange={form.handleChange}
                            value={form.formData?.order_no}
                            errorMessage={form.formErrors?.order_no}
                            required={false}
                            label={t('Order no')}
                        />
                    </div>
                </div>

                <div className="form-group mt-3">
                    <TextArea
                        name="note"
                        handleChange={form.handleChange}
                        value={form.formData?.note}
                        errorMessage={form.formErrors?.note}
                        required={false}
                        label={t('Note')}
                    />
                </div>

                {form.hookRender.map((item, index) => (
                    <div className="form-group mt-3" key={index}>
                        <RenderFormFieldByList item={item} form={form} />
                    </div>
                ))}
            </div>
        </div>
    );
}
