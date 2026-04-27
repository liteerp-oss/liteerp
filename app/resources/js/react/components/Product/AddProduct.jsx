import React, { useCallback, useEffect, useState } from 'react'
import { useI18n } from '../../../i18n/useI18n'
import RenderFormFieldByList from '../RenderFormFieldByList'
import TextArea from '../UI/Input/Textarea'
import UploadImage from '../UI/Input/UploadImage'
import SearchSelect from '../UI/Input/SearchSelect'
import { Select } from '../UI/Input/Select'
import ProductService from '../../services/ProductService'
import { useForm } from '../../libraries/handleInput'
import { InputForm } from '../UI/Input/InputForm'
import { usePopup } from '../popups/PopupContext'
import CreateButton from '../UI/PermissionButtons/CreateButton'
import { useNavigate } from 'react-router-dom'
import CustomEditor from '../UI/Input/CustomEditor'
import PermissionNode from '@/core/PermissionNode'

export default function AddProduct() {
    const permission = new PermissionNode();
    const { t } = useI18n()
    const navigate = useNavigate()
    const [category, setCategory] = useState([])
    const { openPopup } = usePopup()
    const form = useForm()
    const create = useCallback(() => {
        form.setLoading(true)
        form.setFormErrors(null)

        ProductService.add(form.formData)
            .then(() => {
                if (permission.fromNode('product').getPermission('index')) {
                    openPopup({
                        type: 'success',
                        message: t('create_success'),
                        confirmText: t('go_to_purchase'),
                        onConfirm: () => {
                            navigate('/purchases')
                        }
                    })
                } else {
                    openPopup({
                        type: 'success',
                        message: t('create_success')
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
    useEffect(() => {
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
                <div className="form-group mt-3 col-4">
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
                        defaultKeywords={form.formData?.category}
                    />
                </div>
            </div>

            <div className="form-group mt-3">
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
            <CreateButton type={'product'} onClick={create} loading={form.loading} />
        </div>
    </div>
}
