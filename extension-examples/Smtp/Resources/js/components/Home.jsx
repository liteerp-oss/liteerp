import React, { useEffect, useState } from "react";
import PrimaryButton from '@components/UI/Buttons/PrimaryButton'
import { Select } from '@components/UI/Input/Select'
import { useForm } from '@libraries/handleInput'
import { InputForm } from '@components/UI/Input/InputForm'
import TextArea from '@components/UI/Input/Textarea'
import smtpService from "../services/smtpService";
import { usePopup } from '@components/popups/PopupContext'
import { useI18n } from '@i18n/useI18n'
import PageHead from '@components/PageHead'
import TabsCustom from '@components/TabsCustom'
import PermissionNode from '@/core/PermissionNode'
const Home = () => {
    const { t } = useI18n()
    const permission = new PermissionNode();
    permission.fromNode('smtp');
    const form = useForm();
    const { openPopup } = usePopup();
    const saveSmtp = () => {
        form.setLoading(true)
        form.setFormErrors(null)
        smtpService.save(form.formData)
            .then((resp) => {
                openPopup({
                    message: t('smtp.save.success'),
                    type: 'success'
                })
                form.setLoading(false)
            })
            .catch((error) => {
                if (error.response.data?.errors) {
                    form.setFormErrors(error.response.data?.errors)
                }
                if (error.response.data?.message) {
                    openPopup({
                        message: error.response.data?.message,
                        type: 'error'
                    })
                }
                form.setLoading(false)
            })
    };

    const sendTestMail = () => {
        form.setLoading(true)
        form.setFormErrors(null)
        smtpService.send(form.formData)
            .then((resp) => {
                openPopup({
                    message: t('smtp.send.success'),
                    type: 'success'
                })
                form.setLoading(false)
            })
            .catch((error) => {
                if (error.response.data?.errors) {
                    form.setFormErrors(error.response.data?.errors)
                }
                if (error.response.data?.message) {
                    openPopup({
                        message: error.response.data?.message,
                        type: 'error'
                    })
                }
                form.setLoading(false)
            })
    };

    useEffect(() => {
        form.setLoading(true)
        smtpService.show()
            .then((resp) => {
                form.setFormData(resp.message)
                form.setLoading(false)
            })
            .catch((error) => {
                form.setLoading(false)
            })
    }, []);

    return (
        <div className="py-4">
            <PageHead title={t('smtp.title')} subtitle={t('smtp.subtitle')} />
            <div className="container mt-3">
                <div>
                    <TabsCustom
                        navs={[
                            { key: permission.getPermission('index'), label: t('smtp.setting') },
                            { key: permission.getPermission('test'), label: t('smtp.test') }
                        ]}
                        contents={[
                            <div className="border p-2 rounded-2" id="smtp-setting">
                                <div className="mb-3">
                                    <label className="form-label">{t('smtp.host')}</label>
                                    <InputForm name="host"
                                        value={form.formData?.host}
                                        handleChange={form.handleChange} errorMessage={form.formErrors?.host} />
                                </div>

                                <div className="row">
                                    <div className="col-md-6 mb-3">
                                        <label className="form-label">{t('smtp.port')}</label>
                                        <InputForm
                                            value={form.formData?.port}
                                            name="port" handleChange={form.handleChange} errorMessage={form.formErrors?.port} />
                                    </div>

                                    <div className="col-md-6 mb-3">
                                        <label className="form-label">{t('smtp.encryption')}</label>
                                        <Select
                                            value={form.formData?.encryption}
                                            name="encryption" handleChange={form.handleChange}
                                            errorMessage={form.formErrors?.encryption}
                                            options={[
                                                { label: 'tls', value: 'tls' },
                                                { label: 'ssl', value: 'ssl' }
                                            ]}
                                        />
                                    </div>
                                </div>

                                <div className="mb-3">
                                    <label className="form-label">{t('smtp.username')}</label>
                                    <InputForm name="username"
                                        value={form.formData?.username}
                                        handleChange={form.handleChange}
                                        errorMessage={form.formErrors?.username} />
                                </div>

                                <div className="mb-3">
                                    <label className="form-label">{t('smtp.password')}</label>
                                    <InputForm
                                        type="password"
                                        name="password"
                                        value={form.formData?.password}
                                        handleChange={form.handleChange}
                                        errorMessage={form.formErrors?.password} />
                                </div>

                                <div className="row">
                                    <div className="col-md-6 mb-3">
                                        <label className="form-label">{t('smtp.from_email')}</label>
                                        <InputForm name="from_email"
                                            value={form.formData?.from_email}
                                            handleChange={form.handleChange}
                                            errorMessage={form.formErrors?.from_email} />
                                    </div>

                                    <div className="col-md-6 mb-3">
                                        <label className="form-label">{t('smtp.from_name')}</label>
                                        <InputForm name="from_name"
                                            value={form.formData?.from_name}
                                            handleChange={form.handleChange}
                                            errorMessage={form.formErrors?.from_name} />
                                    </div>
                                </div>

                                <div className="col-2">
                                    <PrimaryButton width={150} loading={form.loading} 
                                        onClick={saveSmtp} label={t("Save changes")} />
                                </div>
                            </div>,
                            <div className="border p-2 rounded-2" id="smtp-test">
                                <div className="mb-3">
                                    <label className="form-label">{t('smtp.to')}</label>
                                    <InputForm name="to"
                                        value={form.formData?.to}
                                        handleChange={form.handleChange}
                                        errorMessage={form.formErrors?.to} />
                                </div>

                                <div className="mb-3">
                                    <label className="form-label">{t('smtp.subject')}</label>
                                    <InputForm name="subject"
                                        value={form.formData?.subject}
                                        handleChange={form.handleChange}
                                        errorMessage={form.formErrors?.subject} />
                                </div>

                                <div className="mb-3">
                                    <label className="form-label">{t('smtp.message')}</label>
                                    <TextArea name="message"
                                        value={form.formData?.message}
                                        handleChange={form.handleChange}
                                        errorMessage={form.formErrors?.message} />
                                </div>

                                <div className="col-2">
                                    <PrimaryButton width={150} loading={form.loading} 
                                        onClick={sendTestMail} label={t("smtp.test")} />
                                </div>
                            </div>
                        ]}
                    />
                </div>
            </div>
        </div>
    );
};

export default Home;
