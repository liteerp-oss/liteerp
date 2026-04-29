import React, { useCallback, useEffect } from 'react';
import FormStep from '../FormStep';
import { useNavigate } from 'react-router-dom';
import { usePopup } from '../popups/PopupContext';
import { useForm } from '../../libraries/handleInput';
import PrimaryButton from '../UI/Buttons/PrimaryButton';
import SecondaryButton from '../UI/Buttons/SecondaryButton';
import PurchaseService from '../../services/PurchaseService';
import PurchaseInformation from './EditPurchase/PurchaseInformation';
import PageHead from '../PageHead';
import { useI18n } from '../../../i18n/useI18n';
import { isoToDateTime } from '../../libraries/common';

export default function AddPurchase() {
    const { t } = useI18n();
    const navigate = useNavigate();
    const { openPopup } = usePopup();
    const form = useForm();

    const create = useCallback(() => {
        form.setLoading(true);
        form.setFormErrors(null);

        PurchaseService.add({
            ...form.formData,
        })
            .then((resp) => {
                openPopup({
                    type: 'success',
                    message: t('Purchase has been created successfully'),
                });

                navigate('/purchases?form=edit&id=' + resp.message.id);
                form.setLoading(false);
            })
            .catch((error) => {
                if (error.response?.data?.errors) {
                    form.setFormErrors(error.response.data.errors);
                }

                if (error.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data.message,
                    });
                }

                form.setLoading(false);
            });
    }, [form.formData]);

    const view = useCallback(() => {
        PurchaseService.view()
            .then((resp) => {
                form.setHookRender(resp.message.form);
            })
            .catch((error) => {
                if (error.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data.message,
                    });
                }
            });
    }, []);

    useEffect(() => {
        view();
        form.setFormData({
            purchase_date: isoToDateTime(new Date()),
            expected_date: isoToDateTime(new Date()),
        });
    }, []);

    return (
        <div>
            <PageHead
                containerClass="mx-4"
                title={t('Add purchase')}
                subtitle={t('Add new purchase')}
            />

            <div className="mx-4 mt-3">
                <FormStep
                    list={[
                        t('Purchase information'),
                        t('Add Products'),
                        t('Send review'),
                        t('Approve'),
                    ]}
                    active={0}
                />

                <div className="theme-card p-4 shadow rounded-4">
                    <PurchaseInformation form={form} />
                </div>

                <div className="row mt-3">
                    <div className="col-2">
                        <SecondaryButton label={t('Back')} />
                    </div>
                    <div className="col-2 ms-auto text-end">
                        <PrimaryButton
                            loading={form.loading}
                            onClick={create}
                            label={t('Next')}
                        />
                    </div>
                </div>
            </div>
        </div>
    );
}
