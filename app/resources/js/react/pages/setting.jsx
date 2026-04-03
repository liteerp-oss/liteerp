import React, { useCallback, useEffect } from 'react'
import DashboardLayout from '../layouts/DashboardLayout'
import PageHead from '../components/PageHead'
import { InputForm } from '../components/UI/Input/InputForm'
import businessService from '../services/businessService'
import { useForm } from '../libraries/handleInput'
import { useDispatch, useSelector } from 'react-redux'
import { usePopup } from '../components/popups/PopupContext'
import { setBusinessInfo } from '../redux/businessInfoSlice'
import UploadImage from '../components/UI/Input/UploadImage'
import { useI18n } from '../../i18n/useI18n'
import UpdateButton from '../components/UI/PermissionButtons/UpdateButton'

export default function Setting() {
    const { t } = useI18n()
    const dispatch = useDispatch()
    const business = useSelector((state) => state.business.data)
    const form = useForm()
    const { openPopup } = usePopup()
    const update = useCallback(() => {
        form.setLoading(true)
        form.setFormErrors(null)
        businessService
            .update(form.formData)
            .then(() => {
                form.setLoading(false)
                getDetail()
                openPopup({
                    type: 'success',
                    message: t('Updated successfully'),
                })
            })
            .catch((error) => {
                form.setLoading(false)
                if (error.response?.data?.errors) {
                    form.setFormErrors(error.response.data.errors)
                }
                if (error.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data.message,
                    })
                }
            })
    }, [form.formData])

    const getDetail = useCallback(() => {
        form.setLoading(true)
        businessService
            .show(business?.id)
            .then((resp) => {
                form.setLoading(false)
                form.setFormData(resp.message.business)

                localStorage.setItem(
                    'business-access',
                    resp.message.token
                )
                localStorage.setItem(
                    'business',
                    JSON.stringify(resp.message.business)
                )

                dispatch(setBusinessInfo(resp.message.business))
            })
            .catch(() => {
                form.setLoading(false)
            })
    }, [])

    useEffect(() => {
        getDetail()
    }, [])

    return (
        <DashboardLayout>
            <div>
                <PageHead
                    title={t('Settings')}
                    subtitle={t('Business settings')}
                />

                <div className="container mt-3">
                    <div className="card rounded-3 p-4 shadow-sm theme-sidebar-bg theme-title">
                        <h4>{t('Business information')}</h4>

                        <div className="row">
                            <div className="form-group col-6">
                                <InputForm
                                    name="name"
                                    errorMessage={
                                        form.formErrors?.name
                                    }
                                    value={form.formData?.name}
                                    handleChange={
                                        form.handleChange
                                    }
                                    required={true}
                                    label={t('Company name')}
                                />
                            </div>

                            <div className="form-group col-6">
                                <InputForm
                                    name="address"
                                    errorMessage={
                                        form.formErrors?.address
                                    }
                                    value={form.formData?.address}
                                    handleChange={
                                        form.handleChange
                                    }
                                    required={true}
                                    label={t('Company address')}
                                />
                            </div>
                        </div>

                        <div className="row mt-1">
                            <div className="form-group col-4">
                                <InputForm
                                    name="phone"
                                    errorMessage={
                                        form.formErrors?.phone
                                    }
                                    value={form.formData?.phone}
                                    handleChange={
                                        form.handleChange
                                    }
                                    required={true}
                                    label={t('Phone')}
                                />
                            </div>

                            <div className="form-group col-4">
                                <InputForm
                                    name="email"
                                    errorMessage={
                                        form.formErrors?.email
                                    }
                                    value={form.formData?.email}
                                    handleChange={
                                        form.handleChange
                                    }
                                    required={true}
                                    label={t('Email')}
                                />
                            </div>

                            <div className="form-group col-4">
                                <InputForm
                                    name="tax_code"
                                    errorMessage={
                                        form.formErrors?.tax_code
                                    }
                                    value={form.formData?.tax_code}
                                    handleChange={
                                        form.handleChange
                                    }
                                    required={true}
                                    label={t('Tax code')}
                                />
                            </div>
                        </div>

                        <div className="row mt-1">
                            <div className="form-group col-4">
                                <InputForm
                                    name="bank_name"
                                    errorMessage={
                                        form.formErrors?.bank_name
                                    }
                                    value={form.formData?.bank_name}
                                    handleChange={
                                        form.handleChange
                                    }
                                    required={true}
                                    label={t('Bank name')}
                                />
                            </div>

                            <div className="form-group col-4">
                                <InputForm
                                    name="bank_account_number"
                                    errorMessage={
                                        form.formErrors
                                            ?.bank_account_number
                                    }
                                    value={
                                        form.formData
                                            ?.bank_account_number
                                    }
                                    handleChange={
                                        form.handleChange
                                    }
                                    required={true}
                                    label={t('Bank account number')}
                                />
                            </div>

                            <div className="form-group col-4">
                                <InputForm
                                    name="bank_account_name"
                                    errorMessage={
                                        form.formErrors
                                            ?.bank_account_name
                                    }
                                    value={
                                        form.formData
                                            ?.bank_account_name
                                    }
                                    handleChange={
                                        form.handleChange
                                    }
                                    required={true}
                                    label={t('Bank account name')}
                                />
                            </div>
                        </div>

                        <div className="row mt-2">
                            <UploadImage
                                name="logo_url"
                                errorMessage={
                                    form.formErrors?.logo_url
                                }
                                value={form.formData?.logo_url}
                                handleChangeByKey={
                                    form.handleChangeByKey
                                }
                                required={false}
                                label={t('Logo')}
                            />
                        </div>

                        <div className='mt-3'>
                            <UpdateButton
                                width={150}
                                loading={form.loading}
                                onClick={update}
                                label={t('Save changes')}
                                type={'business'}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    )
}
