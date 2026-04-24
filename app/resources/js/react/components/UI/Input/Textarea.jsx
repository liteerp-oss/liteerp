import React from 'react'
export default function TextArea({
    className= '',
    placeholder= '',
    errorMessage = null,
    handleChange= (text) => {},
    value= '',
    name = 'textarea',
    disabled = false,
    required = false,
    label = null,
    rows= 3
}){
    return <div>
        {label ? <label>
          {label}
          {required ? <span className='text-danger'>*</span> : null}
        </label> : null}
        <textarea 
        rows={rows}
        disabled={disabled}
        value={value}
        onChange={handleChange}
        name={name}
        placeholder={placeholder} className={"form-control default-input " + (className ?? '') + (errorMessage ? 'is-invalid' : '')} ></textarea>
        {errorMessage ? <div className="invalid-feedback">
            {errorMessage.map((mess,index) => {
                return <p key={index}>{mess}</p>
            })}
        </div> : null }
    </div>
}