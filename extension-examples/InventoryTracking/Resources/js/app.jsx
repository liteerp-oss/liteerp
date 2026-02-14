import React, { useCallback, useEffect, useState } from 'react';
import DashboardLayout from '@layouts/DashboardLayout'
import PageHead from '@components/PageHead'
import { useI18n } from '@i18n/useI18n'
import { InputForm } from '@components/UI/Input/InputForm'
import { useForm } from '@libraries/handleInput'
import { usePopup } from '@components/popups/PopupContext'
import PrimaryButton from '@components/UI/Buttons/PrimaryButton'
import inventorytrackingService from './service';
const InventoryTrackingPage = () => {
    const { openPopup } = usePopup()
    const { t } = useI18n()
    const form = useForm();
    const update = useCallback(() => {
        form.setLoading(true)
        form.setFormErrors(null)
        inventorytrackingService.add(form.formData)
            .then((resp) => {
                form.setLoading(false)
                openPopup({
                    type: 'success',
                    message: t('inventorytracking.form.success.message')
                })
            })
            .catch((error) => {
                form.setLoading(false)

                if (error?.response?.data?.errors) {
                    form.setFormErrors(error?.response?.data?.errors)
                }
                if (error?.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error?.response?.data?.message
                    })
                }
            })
    }, [form]);
    useEffect(() => {
        inventorytrackingService.all()
            .then((resp) => {
                form.setFormData(resp.message)
            })
            .catch((error) => {
                if (error?.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error?.response?.data?.message
                    })
                }
            })
    }, [])
    return (
        <DashboardLayout>
            <div className="">
                <PageHead title={t('inventorytracking.title')} subtitle={t('inventorytracking.desc')} />
                <div className='container'>
                    <div className='inventory-form border p-2 mt-3 rounded-3 shadow-sm'>
                        <div className='row'>
                            <div className='col-6'>
                                <label>{t("inventorytracking.form.min.label")}</label>
                                <InputForm
                                    name="min"
                                    type="number"
                                    handleChange={form.handleChange}
                                    errorMessage={form.formErrors?.min}
                                    value={form.formData?.min}
                                    placeholder={t("inventorytracking.form.min.placeholder")}
                                />
                                <PrimaryButton
                                    label={t("inventorytracking.form.button.label")}
                                    onClick={update}
                                />
                            </div>
                            <div className='col-6'>
                                <p>
                                    {t("inventorytracking.form.min.explain")}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>

    );
};

export default InventoryTrackingPage;