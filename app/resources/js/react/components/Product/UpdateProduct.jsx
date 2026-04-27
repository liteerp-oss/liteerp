import React, { useCallback, useEffect, useRef, useState } from 'react'
import { useI18n } from '../../../i18n/useI18n'
import RenderFormFieldByList from '../RenderFormFieldByList'
import UploadImage from '../UI/Input/UploadImage'
import SearchSelect from '../UI/Input/SearchSelect'
import { Select } from '../UI/Input/Select'
import ProductService from '../../services/ProductService'
import { useForm } from '../../libraries/handleInput'
import { InputForm } from '../UI/Input/InputForm'
import { usePopup } from '../popups/PopupContext'
import CreateButton from '../UI/PermissionButtons/CreateButton'
import { useNavigate, useSearchParams } from 'react-router-dom'
import CustomEditor from '../UI/Input/CustomEditor'
import PermissionNode from '@/core/PermissionNode'
export default function UpdateProduct() {
    const permission = new PermissionNode();
    const { t } = useI18n();
    const [searchParams] = useSearchParams();
    const navigate = useNavigate()
    const [category, setCategory] = useState([])
    const { openPopup } = usePopup()
    const form = useForm()
    const update = useCallback(() => {
        form.setLoading(true)
        form.setFormErrors(null)

        ProductService.update({
            id: searchParams.get('id'),
            ...form.formData
        })
            .then(() => {
                if(permission.fromNode('product').getPermission('index')) {
                    openPopup({
                        type: 'success',
                        message: t('update_success'),
                        confirmText: t('go_to_purchase'),
                        onConfirm: () => {
                            navigate('/purchases')
                        }
                    })    
                } else {
                    openPopup({
                        type: 'success',
                        message: t('update_success')
                    })    
                }
                form.setFormErrors(null)
                form.setLoading(false)
            })
            .catch((error) => {
                if (error.response?.data?.errors) {
                    form.setFormErrors(error.response.data.errors)
                }
                if (error.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data.message,
                    })
                }
                form.setLoading(false)
            })
    }, [form.formData])
    const getCategories = useCallback((keywords = '', callback = null) => {
        ProductService.listCategory({
            page: 0,
            keywords,
        }).then((resp) => {
            setCategory(resp.message.data)
            callback && callback()
        })
    }, [])
    const view = useCallback(() => {
        ProductService.view()
            .then((resp) => {
                form.setHookRender(resp.message.form)
            })
            .catch((error) => {
                if (error.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data.message,
                    })
                }
            })
    }, []);
    const getDetail = useCallback(() => {
        ProductService.show(searchParams.get('product')).then((resp) => {
            form.setFormData(resp.message)
        }).catch((error) => {

        })
    }, [])
    useEffect(() => {
        getDetail();
        getCategories();
        view();
    }, [])
    return <div className="container mt-3">
        <div>
            <div className="form-group">
                <InputForm
                    name="name"
                    handleChange={form.handleChange}
                    value={form.formData?.name}
                    errorMessage={form.formErrors?.name}
                    required={true}
                    label={t('Name')}
                />
            </div>

            <div className='row'>
                <div className="form-group mt-3 col-4">
                    <InputForm
                        name="sku"
                        handleChange={form.handleChange}
                        value={form.formData?.sku}
                        errorMessage={form.formErrors?.sku}
                        required={true}
                        label={t('SKU')}
                    />
                </div>

                <div className="form-group mt-3 col-4">
                    <Select
                        name="unit"
                        handleChange={form.handleChange}
                        value={form.formData?.unit}
                        errorMessage={form.formErrors?.unit}
                        options={[
                            { value: 'pcs', label: t('pcs') },
                            { value: 'set', label: t('set') },
                            { value: 'box', label: t('box') },
                            { value: 'carton', label: t('carton') },
                            { value: 'bag', label: t('bag') },
                            { value: 'pack', label: t('pack') },
                            { value: 'roll', label: t('roll') },
                        ]}
                        required={true}
                        label={t('Unit')}
                    />
                </div>
                <div className="form-group mt-3 col-4" style={{
                    position: 'relative',
                    zIndex: 2
                }}>
                    <SearchSelect
                        name="category_id"
                        changeValue={form.handleChangeByKey}
                        value={form.formData?.category_id}
                        search={getCategories}
                        options={category.map((item) => ({
                            value: item.id,
                            label: item.name,
                        }))}
                        errorMessage={form.formErrors?.category_id}
                        required={true}
                        label={t('Category')}
                        defaultKeywords={form.formData?.category[0] ?? ''}
                    />
                </div>
            </div>

            <div className="form-group mt-3" style={{
                position: 'relative',
                zIndex: 1
            }}>
                <CustomEditor
                    name="description"
                    handleChangeByKey={form.handleChangeByKey}
                    value={form.formData?.description}
                    placeholder={t('Description')}
                    errorMessage={form.formErrors?.description}
                    required={false}
                    label={t('Description')}
                />

                
            </div>

            {form.hookRender.map((item, index) => {
                return <div className="form-group mt-3" key={index}>
                    <RenderFormFieldByList item={item} form={form} />
                </div>
            })}
            <div className="form-group mt-3">
                <UploadImage
                    name="image"
                    handleChangeByKey={form.handleChangeByKey}
                    value={form.formData?.image}
                    errorMessage={form.formErrors?.image}
                    required={false}
                    label={t('Thumbnail')}
                />
            </div>
        </div>
        <div className='mt-3'>
            <CreateButton type={'product'} onClick={update} loading={form.loading} />
        </div>
    </div>
}
