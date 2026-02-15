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
    return <div>
        {label ? <label>
            {label} 
            {required ? <span className='text-danger'>*</span> : null}
        </label> : null}
        <select
        disabled={disabled}
        value={value}
        name={name}
        onChange={handleChange} className={"form-control default-input " + (className ?? '') + (errorMessage ? 'is-invalid' : '')}>
            <option value={''}>-- select</option>
            {options.map((item,index) => {
                return <option key={index} value={item.value}>{item.label}</option>
            })}
        </select>
        {errorMessage ? <div className="invalid-feedback">
            {errorMessage.map((mess,index) => {
                return <p key={index}>{mess}</p>
            })}
        </div> : null }
    </div>
}