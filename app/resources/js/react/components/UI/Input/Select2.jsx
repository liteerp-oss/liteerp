import React, { useState } from 'react'
export default function Select2({
    type = 'text',
    className = '',
    placeholder= '',
    errorMessage= null,
    onChangeText= (text) => {},
    value= '',
    options = [],
    onBlur = () => {},
    required = false,
    label = null
}){
    const [showFilter,setShowFilter] = useState(false);
    return <div className='select2'>{label ? <label>
          {label}
          {required ? <span className='text-danger'>*</span> : null}
        </label> : null}
        <input
        value={value}
        onChange={(event) => {
            onChangeText(event.target.value);
            if(event.target.value === '') {
                setShowFilter(false);
            } else {
                setShowFilter(true);
            }
            
        }}
        onBlur={() => {
            setTimeout(() => {
                    setShowFilter(false);
                },200);
            onBlur();
        }}
        type={type} className={"form-control default-input " + (className ?? '') + (errorMessage ? 'is-invalid' : '')} 
        placeholder={placeholder}/>
        {errorMessage ? <div className="invalid-feedback">
            {errorMessage.map((mess,index) => {
                return <p key={index}>{mess}</p>
            })}
        </div> : null }
        {showFilter ? <div>
            <ul className='list-data-select2'>
                {options.map((item,index) => {
                    return <li key={index} onClick={() => {
                        console.log(item.name)
                        onChangeText(item.name)
                        }}>
                            {item.name}
                    </li>
                })}
                
            </ul>
        </div> : null }
        
    </div>
}