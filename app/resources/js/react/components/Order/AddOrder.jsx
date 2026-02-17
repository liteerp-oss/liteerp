import React, { useCallback, useEffect } from 'react';
import PageHead from '../PageHead';
import FormStep from '../FormStep';
import { useForm } from '../../libraries/handleInput';
import SecondaryButton from '../UI/Buttons/SecondaryButton';
import PrimaryButton from '../UI/Buttons/PrimaryButton';
import OrderService from '../../services/OrderService';
import { useNavigate } from 'react-router-dom';
import { usePopup } from '../popups/PopupContext';
import CustomerInformation from './EditOrder/CustomerInformation';
import { useI18n } from '../../../i18n/useI18n';
import CreateButton from '../UI/PermissionButtons/CreateButton';

export default function AddOrder() {
    const { t } = useI18n();
    const form = useForm(null);
    const { openPopup } = usePopup();
    const navigate = useNavigate();

    const create = useCallback(() => {
        form.setFormErrors(null);
        form.setLoading(true);

        OrderService.add(form.formData)
            .then((resp) => {
                openPopup({
                    type: 'success',
                    message: t('Order has been created successfully'),
                    onCancel: () => {
                        navigate('/orders?form=edit&id=' + resp.message.id);
                    },
                });
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

    const detailView = useCallback(() => {
        OrderService.view()
            .then((resp) => {
                form.setHookRender(resp.message.index);
            })
            .catch(() => {});
    }, []);

    useEffect(() => {
        detailView();
    }, []);

    return (
        <div>
            <PageHead
                title={t('Order')}
                subtitle={t('Add new order')}
            />

            <div className="container mt-3">
                <FormStep
                    list={[
                        t('Customer & Order'),
                        t('Products'),
                        t('Shipping'),
                        t('Complete'),
                        t('Print'),
                    ]}
                />

                <div className="theme-card p-3 rounded-4">
                    <CustomerInformation form={form} />
                </div>

                <div className="row mt-3">
                    <div className="col-2">
                        <SecondaryButton label={t('Back')} />
                    </div>
                    <div className="col-2 ms-auto text-end">
                        <CreateButton
                            loading={form.loading}
                            onClick={create}
                            label={t('Next')}
                            width={100}
                            type={'order'}
                        />
                    </div>
                </div>
            </div>
        </div>
    );
}
