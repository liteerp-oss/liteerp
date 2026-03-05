import React, { useCallback, useEffect } from 'react';
import DashboardLayout from '../layouts/DashboardLayout';
import PageHead from '../components/PageHead';
import { InputForm } from '../components/UI/Input/InputForm';
import AuthencationService from '../services/AuthencationService';
import { useForm } from '../libraries/handleInput';
import PrimaryButton from '../components/UI/Buttons/PrimaryButton';
import TextArea from '../components/UI/Input/Textarea';
import { usePopup } from '../components/popups/PopupContext';
import { useI18n } from '../../i18n/useI18n';
import UploadImage from '../components/UI/Input/UploadImage';
import { Select } from '../components/UI/Input/Select';

export default function Profile() {
    const { t } = useI18n();
    const { openPopup } = usePopup();
    const form = useForm();

    const profile = useCallback(() => {
        form.setLoading(true);
        AuthencationService.getProfile()
            .then((resp) => {
                form.setFormData(resp.message);
                form.setLoading(false);
            })
            .catch(() => {
                form.setLoading(false);
            });
    }, []);

    const update = useCallback(() => {
        form.setFormErrors(null);
        form.setLoading(true);

        AuthencationService.updateProfile(form.formData)
            .then(() => {
                openPopup({
                    type: 'success',
                    message: t('Profile updated successfully'),
                });
                form.setLoading(false);
                form.handleChangeByKey('password', null);
                form.handleChangeByKey('new_password', null);
            })
            .catch((error) => {
                form.setLoading(false);

                if (error.response?.data.message) {
                    openPopup({
                        type: 'error',
                        message: error.response?.data.message,
                    });
                }

                if (error.response?.data.errors) {
                    form.setFormErrors(error.response?.data.errors);
                }
            });
    }, [form.formData]);

    useEffect(() => {
        profile();
    }, []);

    return (
        <DashboardLayout>
            <div>
                <PageHead
                    title={t('Profile')}
                    subtitle={t('Update your information')}
                />

                <div className="container mt-3">
                    <div className="card rounded-3 p-4 shadow-sm theme-sidebar-bg theme-title">
                        <div className="row">
                            <div className="col-4">
                                <label>{t('Name')}</label>
                                <InputForm
                                    name="name"
                                    handleChange={form.handleChange}
                                    errorMessage={form.formErrors?.name}
                                    value={form.formData?.name}
                                />
                            </div>

                            <div className="col-4">
                                <label>{t('Email')}</label>
                                <InputForm
                                    name="email"
                                    handleChange={form.handleChange}
                                    errorMessage={form.formErrors?.email}
                                    value={form.formData?.email}
                                />
                            </div>
                            <div className="col-4">
                                <label>{t('Email language')}</label>
                                <Select
                                    name="lang"
                                    handleChange={form.handleChange}
                                    errorMessage={form.formErrors?.lang}
                                    value={form.formData?.lang}
                                    options={[{
                                        value: 'en',
                                        label: 'English'
                                    },{
                                        value: 'vi',
                                        label: 'Tiếng việt'
                                    },{
                                        value: 'ja',
                                        label: 'Japanese'
                                    }]}
                                />
                            </div>
                        </div>

                        <div className="row mt-1">
                            <div className="col-4">
                                <label>{t('Phone')}</label>
                                <InputForm
                                    name="phone"
                                    handleChange={form.handleChange}
                                    errorMessage={form.formErrors?.phone}
                                    value={form.formData?.phone}
                                />
                            </div>

                            <div className="col-4">
                                <label>{t('Password')}</label>
                                <InputForm
                                    type="password"
                                    name="password"
                                    handleChange={form.handleChange}
                                    errorMessage={form.formErrors?.password}
                                    value={form.formData?.password}
                                />
                            </div>

                            <div className="col-4">
                                <label>{t('New password')}</label>
                                <InputForm
                                    type="password"
                                    name="new_password"
                                    handleChange={form.handleChange}
                                    errorMessage={form.formErrors?.new_password}
                                    value={form.formData?.new_password}
                                />
                            </div>
                        </div>

                        <div className="row mt-1">
                            <label>{t('Bio')}</label>
                            <TextArea
                                name="bio"
                                handleChange={form.handleChange}
                                errorMessage={form.formErrors?.bio}
                                value={form.formData?.bio ?? ''}
                            />
                        </div>

                        <div className="row mt-1">
                            <label>{t('Avatar')}</label>
                            <div>
                                <UploadImage 
                                name='avatar'
                                handleChangeByKey={form.handleChangeByKey}
                                value={'/assets/icons/avatar-default.png'}
                                errorMessage={form.formErrors?.avatar}
                                />
                            </div>
                        </div>

                        <div className="row mt-1">
                            <div style={{ width: 200 }}>
                                <PrimaryButton
                                    width={200}
                                    onClick={update}
                                    label={t('Submit')}
                                    loading={form.loading}
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
