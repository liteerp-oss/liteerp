import React, { useCallback, useState } from 'react'
import { InputForm } from '../../UI/Input/InputForm'
import TextArea from '../../UI/Input/Textarea'
import SearchSelect from '../../UI/Input/SearchSelect'
import ShippingService from '../../../services/ShippingService';
import RenderFormFieldByList from '../../RenderFormFieldByList';
import { formatToDateTime, isoToDateTime } from '../../../libraries/common';
import { useI18n } from '@/i18n/useI18n';
export default function FormUpdate({
    form = {
        formData: null,
        formErrors: null,
        handleChange: null,
        handleChangeByKey: null
    }
}) {
    const {t} = useI18n();
    const [shippings,setShippings] = useState([]);
    const getShippings = useCallback((keywords = '',callback = null) => {
        ShippingService.list({
            page: 0,
            keywords: keywords
        })
            .then((resp) => {
                setShippings(resp.message.data);
                if(callback) {
                    callback();
                }
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
    return <div>
        <div>
            <InputForm
                errorMessage={form.formErrors?.receiver_name}
                value={form.formData?.receiver_name}
                name="receiver_name"
                handleChange={form.handleChange}
                type="text"
                required={true}
                label={t("Receiver name")}
            />
        </div>
        <div className='mt-3'>
            <InputForm
                errorMessage={form.formErrors?.receiver_phone}
                value={form.formData?.receiver_phone}
                name="receiver_phone"
                handleChange={form.handleChange}
                type="text"
                required={true}
                label={t("Receiver phone")}
            />
        </div>
        <div className='mt-3'>
            <TextArea
                errorMessage={form.formErrors?.receiver_address}
                value={form.formData?.receiver_address}
                name="receiver_address"
                handleChange={form.handleChange}
                required={true}
                label={t("Receiver address")}
            />
        </div>
        <div className='mt-3'>
            <InputForm
                errorMessage={form.formErrors?.receiver_note}
                value={form.formData?.receiver_note}
                name="receiver_note"
                handleChange={form.handleChange}
                type="text"
                required={false}
                label={t("Receiver note")}
            />
        </div>
        <div className='mt-3'>
            <InputForm
                errorMessage={form.formErrors?.shipping_code}
                value={form.formData?.shipping_code}
                name="shipping_code"
                handleChange={form.handleChange}
                type="text"
                required={false}
                label={t("Shipping code")}
            />
        </div>
        <div className='mt-3'>
            <InputForm
                errorMessage={form.formErrors?.shipping_fee_actual}
                value={form.formData?.shipping_fee_actual}
                name="shipping_fee_actual"
                handleChange={form.handleChange}
                type="numeric"
                required={false}
                label={t("Shipping fee actual")}
            />
        </div>
        <div className='mt-3'>
            <InputForm
                errorMessage={form.formErrors?.shipped_at}
                value={isoToDateTime(form.formData?.shipped_at ?? new Date().toDateString)}
                name="shipped_at"
                handleChange={form.handleChange}
                type="date"
                required={false}
                label={t("Shipping at")}
            />
        </div>
        <div className='mt-3'>
            <InputForm
                errorMessage={form.formErrors?.delivered_at}
                value={isoToDateTime(form.formData?.delivered_at ?? new Date().toDateString)}
                name="delivered_at"
                handleChange={form.handleChange}
                type="date"
                required={false}
                label={t("Delivered at")}
            />
        </div>
        <div className='mt-3'>
            <SearchSelect
                search={getShippings}
                errorMessage={form.formErrors?.preferred_unit}
                value={form.formData?.preferred_unit}
                name="preferred_unit"
                handleChange={form.handleChangeByKey}
                options={shippings.map((item) => {
                    return {
                        value: item.id,
                        label: item.name
                    }
                })}
                defaultKeywords={form.formData?.preferred_unit_name}
                required={false}
                label={t("Preferred unit")}
            />
        </div>
        {form.hookRender.map((item,index) => {
            return <div key={index}>
                <RenderFormFieldByList item={item} form={form}/>
            </div>
        })}
    </div>
}