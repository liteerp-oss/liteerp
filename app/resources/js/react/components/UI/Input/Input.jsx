import React from 'react'
export function Input(data = {
    type: 'text',
    class: '',
    placeholder: '',
    errorMessage : null,
    onChangeText: (text) => {},
    value: '',
    required: false,
    label: null
}) {
    return <div>
        {data.label ? <label>
            {data.label} 
            {data.required ? <span className='text-danger'>*</span> : null}
        </label> : null}
        <input
        onChange={(event) => {
            data.onChangeText(event.target.value);
        }}
        type={data.type} className={"form-control default-input " + (data.class ?? '') + (data.errorMessage ? 'is-invalid' : '')} 
        placeholder={data.placeholder}/>
        {data.errorMessage ? <div className="invalid-feedback">
            {data.errorMessage.map((mess,index) => {
                return <p key={index}>{mess}</p>
            })}
        </div> : null }
    </div>
}