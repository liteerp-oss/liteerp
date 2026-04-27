import React, { useCallback, useEffect, useState } from 'react';
import FormStep from '../FormStep';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { usePopup } from '../popups/PopupContext';
import { useForm } from '../../libraries/handleInput';
import PrimaryButton from '../UI/Buttons/PrimaryButton';
import SecondaryButton from '../UI/Buttons/SecondaryButton';
import PurchaseService from '../../services/PurchaseService';
import PurchaseInformation from './EditPurchase/PurchaseInformation';
import AddProduct from './AddProduct';
import { isoToDateTime } from '../../libraries/common';
import RequestContent from './EditPurchase/RequestContent';
import ApproveContent from './EditPurchase/ApproveContent';
import ApprovedContent from './EditPurchase/ApprovedContent';
import CancelledContent from './EditPurchase/CancelledContent';
import PageHead from '../PageHead';
import { PopupLayout } from '../../layouts/PopupLayout';
import TextArea from '../UI/Input/Textarea';
import { useDispatch, useSelector } from 'react-redux';
import { setPurchaseDetail } from '../../redux/purchase/detailSlice';
import BootstrapAlert from '../BootstrapAlert';
import { useI18n } from '../../../i18n/useI18n';
import ApproveButton from '../UI/PermissionButtons/ApproveButton';
import CancelButton from '../UI/PermissionButtons/CancelButton';
import PermissionNode from '@/core/PermissionNode';

export default function EditPurchase() {
    const permission = new PermissionNode();
    const { t } = useI18n();
    const dispatch = useDispatch();
    const [showCancel, setShowCancel] = useState(false);
    const [searchParams] = useSearchParams();
    const navigate = useNavigate();
    const { openPopup } = usePopup();
    const form = useForm();
    const detail = useSelector((state) => state.purchasedetail.data);
    const [currentStep, setCurrentStep] = useState(1);

    const getPurchaseDetail = useCallback(() => {
        PurchaseService.show(searchParams.get('id'))
            .then((resp) => {
                form.setFormData(resp.message);
                dispatch(setPurchaseDetail(resp.message));
            })
            .catch((error) => {
                if (error.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data.message,
                        onCancel: () => navigate('/purchases'),
                    });
                }
            });
    }, []);

    const update = useCallback(
        (status = 'draft', callback = null) => {
            form.setLoading(true);
            PurchaseService.update({
                ...form.formData,
                id: searchParams.get('id'),
                expected_date: isoToDateTime(form.formData?.expected_date),
                purchase_date: isoToDateTime(form.formData?.purchase_date),
                status: status,
            }).then(() => {
                    if(status === 'approved' && permission.fromNode('invoicein').getPermission('index')) {
                        openPopup({
                            type: 'success',
                            message: t('Purchase has been updated successfully'),
                            onCancel: callback ?? null,
                            confirmText: t('go_to_invoicein'),
                            onConfirm: () => {
                                navigate('/invoice-ins')
                            }
                        });
                    } else {
                        openPopup({
                            type: 'success',
                            message: t('Purchase has been updated successfully'),
                            onCancel: callback ?? null,
                        });
                    }
                    
                    getPurchaseDetail();
                    form.setLoading(false);
                })
                .catch((error) => {
                    if (error.response?.data?.message) {
                        openPopup({
                            type: 'error',
                            message: error.response.data.message,
                        });
                    }
                    if (error.response?.data?.errors) {
                        form.setFormErrors(error.response.data.errors);
                    }
                    form.setLoading(false);
                });
        },
        [form.formData]
    );

    const confirmUpdateToRequest = () => {
        openPopup({
            type: 'warning',
            message: t('Do you want to send request for approval?'),
            onConfirm: () => update('requested'),
        });
    };

    const confirmUpdateToApprove = () => {
        openPopup({
            type: 'warning',
            message: t('Do you want to approve this purchase?'),
            onConfirm: () => update('approved'),
        });
    };

    const confirmUpdateToCancelled = () => {
        openPopup({
            type: 'warning',
            message: t('Do you want to cancel this purchase?'),
            onConfirm: () => setShowCancel(true),
        });
    };

    const nextStep = () => {
        if (currentStep === 2) return;

        if (
            currentStep === 0 &&
            JSON.stringify(detail) !== JSON.stringify(form.formData)
        ) {
            openPopup({
                cancelText: t('No change'),
                type: 'warning',
                message: t(
                    'You have unsaved changes. Do you want to save before continuing?'
                ),
                onConfirm: () => {
                    update(form.formData?.status ?? 'draft', () =>
                        setCurrentStep((pre) => pre + 1)
                    );
                },
                onCancel: () => setCurrentStep((pre) => pre + 1),
            });
        } else {
            setCurrentStep((pre) => pre + 1);
        }
    };

    const preStep = () => {
        if (currentStep === 0) return;
        setCurrentStep((pre) => pre - 1);
    };

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
        getPurchaseDetail();
        view();
    }, []);

    return (
        <div>
            <PageHead
                containerClass="mx-4"
                title={t('Update purchase')}
                subtitle={t('Update purchase information')}
            />

            {detail?.status === 'cancelled' && (
                <div className="px-4 mt-3">
                    <BootstrapAlert
                        type="danger"
                        title={t('Cancelled')}
                        message={detail?.reason ?? t('No reason')}
                    />
                </div>
            )}

            <div className="mx-4 mt-3">
                <FormStep
                    list={[
                        t('Purchase information'),
                        t('Add Products'),
                        t('Request or Approve'),
                    ]}
                    active={currentStep}
                />

                <div className="theme-card p-4 shadow rounded-4">
                    {form && detail && (
                        <>
                            <div className={currentStep === 0 ? 'show' : 'hidden'}>
                                <PurchaseInformation form={form} />
                            </div>
                            <div className={currentStep === 1 ? 'show' : 'hidden'}>
                                <AddProduct />
                            </div>
                            <div className={currentStep === 2 ? 'show' : 'hidden'}>
                                {detail?.status === 'draft' && <RequestContent />}
                                {detail?.status === 'requested' && (
                                    <ApproveContent />
                                )}
                                {detail?.status === 'approved' && (
                                    <ApprovedContent />
                                )}
                                {detail?.status === 'cancelled' && (
                                    <CancelledContent />
                                )}
                            </div>
                        </>
                    )}
                </div>

                <div className="row mt-3">
                    <div className="col-2">
                        <SecondaryButton
                            onClick={preStep}
                            label={t('Back')}
                        />
                    </div>

                    <div className="col-4 ms-auto text-end">
                        <div className="row">
                            <div className="col-6">
                                {detail?.status !== 'cancelled' && (
                                    <CancelButton
                                        loading={form.loading}
                                        onClick={confirmUpdateToCancelled}
                                        label={t('Cancel purchase')}
                                        type={'purchase'}
                                    />
                                )}
                            </div>

                            <div className="col-6">
                                {currentStep <= 1 && (
                                    <PrimaryButton
                                        loading={form.loading}
                                        onClick={nextStep}
                                        label={t('Next')}
                                    />
                                )}

                                {currentStep === 2 &&
                                    detail?.status === 'draft' && (
                                        <PrimaryButton
                                            loading={form.loading}
                                            onClick={confirmUpdateToRequest}
                                            label={t('Send to request')}
                                        />
                                    )}

                                {currentStep === 2 &&
                                    detail?.status === 'requested' && (
                                        <ApproveButton
                                            loading={form.loading}
                                            onClick={confirmUpdateToApprove}
                                            label={t('Approve')}
                                            type={'purchase'}
                                        />
                                    )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {showCancel && (
                <PopupLayout
                    loading={form.loading}
                    onConfirm={() => {
                        update('cancelled');
                        setShowCancel(false);
                    }}
                    onClose={() => setShowCancel(false)}
                    confirmText={t('Submit cancel')}
                    title={t('Cancel reason')}
                >
                    <label>{t('Reason')}</label>
                    <TextArea
                        name="reason"
                        errorMessage={form.formErrors?.reason}
                        handleChange={form.handleChange}
                        value={form.formData?.reason}
                        placeholder={t(
                            'Reason cancel purchase, maximum 250 characters'
                        )}
                    />
                </PopupLayout>
            )}
        </div>
    );
}
