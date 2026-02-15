import React from 'react'
export function InputForm({
    type = 'text',
    className = '',
    placeholder = '',
    errorMessage = null,
    handleChange = (e) => { },
    value = '',
    name = 'input',
    width = null,
    disabled = false,
    ref = null,
    required = false,
    label = null
}) {
    return <div>
        {label ? <label>
            {label} 
            {required ? <span className='text-danger'>*</span> : null}
        </label> : null}
        {type === 'checkbox' ? <input
            disabled={disabled}
            checked={Boolean(Number(value)) ? true : false}
            style={{
                width: width ?? '100%'
            }}
            onChange={handleChange}
            type={type}
            name={name}
            className={(className !== '' ? className : 'checkbox-default') + (errorMessage ? 'is-invalid' : '')}
            placeholder={placeholder} /> : <input
            ref={ref}
            disabled={disabled}
            value={value ?? ''}
            style={{
                width: width ?? '100%'
            }}
            onChange={handleChange}
            type={type}
            name={name}
            className={(className !== '' ? className : 'form-control default-input ') + (errorMessage ? 'is-invalid' : '')}
            placeholder={placeholder} />}
        {errorMessage ? <div className="invalid-feedback">
            {errorMessage.map((mess, index) => {
                return <p key={index}>{mess}</p>
            })}
        </div> : null}
    </div>
}