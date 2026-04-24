import React, { useCallback, useMemo, useRef, useState } from 'react'
import ImageManagerService from '../../../services/ImageManagerService';
import LoadImage from '../../LoadImage';

export default function GalleryImage({
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
    const [activeIndex, setActiveIndex] = useState(0)
    const fileRef = useRef();

    const imageUrls = useMemo(() => {
        if (!value) {
            return [];
        }

        return String(value)
            .split(',')
            .map((item) => item.trim())
            .filter(Boolean);
    }, [value]);

    const renderItems = useMemo(() => [...imageUrls, null], [imageUrls]);

    const handleUpload = useCallback((element) => {
        setMessage(null)
        const file = element.target.files[0];
        if (!file) {
            return;
        }

        setLoading(true)
        const formData = new FormData();
        formData.append('file', file);
        ImageManagerService.add(formData)
            .then((resp) => {
                const nextImages = [...imageUrls];
                nextImages[activeIndex] = resp.message.path;
                handleChangeByKey(name, nextImages.filter(Boolean).join(','))
                setLoading(false)
                element.target.value = null;
            })
            .catch((error) => {
                if (error.response?.data.message) {
                    setMessage(error.response?.data.message)
                }
                setLoading(false)
                element.target.value = null;
            })
    }, [activeIndex, handleChangeByKey, imageUrls, name])

    const handleRemoveImage = useCallback((index) => {
        const nextImages = imageUrls.filter((_, imageIndex) => imageIndex !== index);
        handleChangeByKey(name, nextImages.join(','));
    }, [handleChangeByKey, imageUrls, name])

    useMemo(() => {
        setMessage(errorMessage)
    }, [errorMessage])

    return <div>
        {label ? <label>
          {label}
          {required ? <span className='text-danger'>*</span> : null}
        </label> : null}
        <div className='d-flex flex-wrap gap-2'>
            {renderItems.map((imageUrl, index) => (
                <div
                    className='uploadimage-component d-flex justify-content-center align-items-center position-relative'
                    key={`${name}-${index}-${imageUrl ?? 'empty'}`}
                    style={{
                        width: width,
                        height: height
                    }}
                >
                    {imageUrl ? <button
                        className='btn btn-sm btn-danger position-absolute'
                        onClick={() => handleRemoveImage(index)}
                        style={{
                            top: 4,
                            right: 4,
                            zIndex: 2,
                            lineHeight: 1,
                            padding: '2px 6px'
                        }}
                        type='button'
                    >
                        x
                    </button> : null}

                    <LoadImage
                        onClick={() => {
                            setActiveIndex(index)
                            fileRef.current.click();
                        }}
                        url={imageUrl}
                        alt=''
                    />

                    {loading && activeIndex === index ? <div className="uploadimage-component-loading spinner-border text-primary" role="status">
                        <span className="visually-hidden">Loading...</span>
                    </div> : null}
                </div>
            ))}
        </div>
        <input onChange={handleUpload} ref={fileRef} id={name} name={name} type='file' style={{
            display: 'none'
        }} />

        {message ? <div>
            <p className='text-danger'>
                {message}
            </p>
        </div> : null}
    </div>
}
