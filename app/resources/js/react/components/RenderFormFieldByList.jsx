import React from 'react'
import TextArea from './UI/Input/Textarea'
import { Select } from './UI/Input/Select'
import { InputForm } from './UI/Input/InputForm'
import UploadImage from './UI/Input/UploadImage'
import GalleryImage from './UI/Input/GalleryImage'
export default function RenderFormFieldByList({
    item = {
        label: '',
        key: '',
        value: '',
        type: ''
    },
    form = {
        formData: null
    }
}) {
    return <div>
        {item.type === 'textarea' ? <TextArea
            name={item.key}
            handleChange={form.handleChange}
            value={form.formData?.[item.key]}
            errorMessage={form.formErrors?.[item.key]}
            placeholder={item.placeHolder}
            label={item?.label}
            required={item?.required}
        />
            : item.type === 'select' ? <Select
                name={item.key}
                handleChange={form.handleChange}
                value={form.formData?.[item.key]}
                errorMessage={form.formErrors?.[item.key]}
                placeholder={item.placeHolder}
                options={item.options}
                label={item?.label}
                required={item?.required}
            /> : item.type === 'image' ? <UploadImage
                name={item.key}
                handleChangeByKey={form.handleChangeByKey}
                value={form.formData?.[item.key]}
                errorMessage={form.formErrors?.[item.key]}
                label={item?.label}
                required={item?.required}
            />
                : item.type === 'gallery' ? <GalleryImage
                    name={item.key}
                    handleChangeByKey={form.handleChangeByKey}
                    value={form.formData?.[item.key]}
                    errorMessage={form.formErrors?.[item.key]}
                    required={item?.required}
                    label={item?.label}
                /> : <InputForm
                    name={item.key}
                    handleChange={form.handleChange}
                    value={form.formData?.[item.key]}
                    errorMessage={form.formErrors?.[item.key]}
                    placeholder={item.placeHolder}
                    label={item?.label}
                    required={item?.required}
                />}
    </div>
}