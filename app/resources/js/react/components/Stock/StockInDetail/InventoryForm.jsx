import React, { useCallback, useState } from 'react'
import SearchSelect from '../../UI/Input/SearchSelect'
import WarehouseService from '../../../services/WarehouseService'
import { InputForm } from '../../UI/Input/InputForm';
import { usePopup } from '../../popups/PopupContext';
import { useI18n } from '@/i18n/useI18n';
export default function InventoryForm({
    form = {
        formData: null,
        handleChange: null,
        handleChangeByKey: null,
        formErrors: null
    }
}){
    const {t} = useI18n();
    const [warehouse,setWarehouse] = useState([]);
    const getWarehouses = useCallback((keywords = '',callback = null)=>{
        WarehouseService.list({
            keywords: keywords,
            page: 0,
        })
        .then((resp) => {
            setWarehouse(resp.message.data)
            if(callback) {
                callback();
            }
        })
        .catch((error) => {

        })
    },[]);

    return <div>
        <div>
            <h5>{form.formData?.name}</h5>
        </div>
        <div className='form-group'>
            <label>{t('Warehouse')}</label>
            <SearchSelect
            search={getWarehouses}
            name='warehouse_id'
            errorMessage={form.formErrors?.warehouse_id}
            value={form.formData?.warehouse_id}
            changeValue={form.handleChangeByKey}
            options={warehouse.map((item) => {
                return {
                    value: item.id,
                    label: item.name
                }
            })}
            defaultKeywords={form.formData?.warehouse}
            />
        </div>
        <div className='form-group'>
            <label>{t('Quantity')}</label>
            <InputForm
            handleChange={form.handleChange}
            name='qty_change'
            errorMessage={form.formErrors?.qty_change}
            value={form.formData?.qty_change}
            />
        </div>
    </div>
}