import { useI18n } from '@/i18n/useI18n'
import React from 'react'
export function Select({
    className= '',
    errorMessage = null,
    handleChange= (e) => {},
    value= '',
    options= [],
    name= 'select',
    disabled = false,
    label = null,
    required = false 
}) {
    const {t} = useI18n();
    return <div>
        {label ? <label>
            {t(label)} 
            {required ? <span className='text-danger'>*</span> : null}
        </label> : null}
        <select
        disabled={disabled}
        value={value}
        name={name}
        onChange={handleChange} className={"form-control default-input " + (className ?? '') + (errorMessage ? 'is-invalid' : '')}>
            <option value={''}>-- {t('Select')}</option>
            {options.map((item,index) => {
                return <option key={index} value={item.value}>{t(item.label)}</option>
            })}
        </select>
        {errorMessage ? <div className="invalid-feedback">
            {errorMessage.map((mess,index) => {
                return <p key={index}>{mess}</p>
            })}
        </div> : null }
    </div>
}