import React, { useCallback, useMemo, useRef, useState } from 'react'
import ImageManagerService from '../../../services/ImageManagerService';
import LoadImage from '../../LoadImage';
export default function UploadImage({
    handleChangeByKey = null,
    errorMessage = null,
    value = null,
    name = 'name',
    width = 150,
    height = 150,
    required = false,
    label = null
}) {
    const [loading, setLoading] = useState(false)
    const [message, setMessage] = useState(null)
    const fileRef = useRef();
    const imageRef = useRef();
    const handleUpload = useCallback((element) => {
        setMessage(null)
        setLoading(true)
        const file = element.target.files[0];
        const formData = new FormData();
        formData.append('file', file);
        ImageManagerService.add(formData)
            .then((resp) => {
                handleChangeByKey(name, resp.message.path)
                imageRef.current.src = resp.message.path
                setLoading(false)
            })
            .catch((error) => {
                if (error.response?.data.message) {
                    setMessage(error.response?.data.message)
                }
                setLoading(false)
            })
    }, [imageRef])
    useMemo(() => {
        setMessage(errorMessage)
    }, [errorMessage])
    return <div>
        {label ? <label>
          {label}
          {required ? <span className='text-danger'>*</span> : null}
        </label> : null}
        <div className='uploadimage-component d-flex justify-content-center align-items-center' style={{
            width: width,
            height: height
        }}>
                
            <LoadImage
                ref={imageRef}
                onClick={() => {
                    fileRef.current.click();
                }} url={value} alt='' />
            <input onChange={handleUpload} ref={fileRef} id={name} name={name} type='file' style={{
                display: 'none'
            }} />
            {loading ? <div className="uploadimage-component-loading spinner-border text-primary" role="status">
                <span className="visually-hidden">Loading...</span>
            </div> : null}

        </div>

        {message ? <div>
            <p className='text-danger'>
                {message}
            </p>
        </div> : null}
    </div>
}