import React, { useRef, useState } from 'react'
import { Editor } from '@tinymce/tinymce-react';
export default function CustomEditor({
    className = '',
    placeholder = '',
    errorMessage = null,
    handleChangeByKey = (name,text) => { },
    value = '',
    name = 'textarea',
    disabled = false,
    required = false,
    label = null
}) {
    const [defaultValue,setDefaultValue] = useState(false)
    return <div>
        {label ? <label>
            {label}
            {required ? <span className='text-danger'>*</span> : null}
        </label> : null}
        <div style={{
            position: 'relative',
            zIndex: 0
        }}>
            <Editor
                className={className}
                placeholder={placeholder}
                disabled={disabled}
                name={name}
                apiKey={import.meta.env.VITE_TINYMCE_API_KEY}
                value={value}
                init={{
                    height: 500,
                    menubar: true,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                        'preview', 'anchor', 'searchreplace', 'visualblocks',
                        'code', 'fullscreen', 'insertdatetime', 'media',
                        'table', 'help', 'wordcount'
                    ],
                    toolbar:
                        'undo redo | blocks | bold italic forecolor | ' +
                        'alignleft aligncenter alignright alignjustify | ' +
                        'bullist numlist outdent indent | removeformat | help'
                }}
                onEditorChange={(content) => {
                    handleChangeByKey(name,content);
                }}
            />
        </div>
        {errorMessage ? <div className="invalid-feedback">
            {errorMessage.map((mess, index) => {
                return <p key={index}>{mess}</p>
            })}
        </div> : null}
    </div>
}