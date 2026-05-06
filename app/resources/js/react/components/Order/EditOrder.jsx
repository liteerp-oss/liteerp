import React, { useCallback, useEffect, useMemo, useState } from 'react'
import PageHead from '../PageHead'
import FormStep from '../FormStep'
import { useForm } from '../../libraries/handleInput';
import SecondaryButton from '../UI/Buttons/SecondaryButton';
import PrimaryButton from '../UI/Buttons/PrimaryButton';
import OrderService from '../../services/OrderService';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { usePopup } from '../popups/PopupContext';
import CustomerInformation from './EditOrder/CustomerInformation';
import Products from './EditOrder/Products';
import OrderShippingService from '../../services/OrderShippingService';
import ShippingForm from './EditOrder/ShippingForm';
import Summary from './EditOrder/Summary';
import OrderItemService from '../../services/OrderItemService';
import Completed from './EditOrder/Completed';
import BeforeApprove from './EditOrder/BeforeApprove';
import { PopupLayout } from '../../layouts/PopupLayout';
import TextArea from '../UI/Input/Textarea';
import Cancelled from './EditOrder/Cancelled';
import BootstrapAlert from '../BootstrapAlert'
import { useDispatch, useSelector } from 'react-redux';
import { setSummary } from '../../redux/order/summarySlice';
import { useI18n } from '../../../i18n/useI18n';
import ApproveButton from '../UI/PermissionButtons/ApproveButton';
import CancelButton from '../UI/PermissionButtons/CancelButton';
export default function EditOrder() {
    const {t} = useI18n();
    const dispatch = useDispatch();
    const [showCancelReason, setShowCancelReason] = useState(false);
    const [currentStep, setCurrentStep] = useState(0);
    const [searchParams] = useSearchParams();
    const form = useForm(null);
    const shippingForm = useForm();
    const [detail, setDetail] = useState(null);
    const [shippingDetail, setShippingDetail] = useState(null);
    const { openPopup } = usePopup();
    const summaryData = useSelector((state) => state.summarydetai.data);
    const updateInformation = useCallback((callback = null) => {
        form.setFormErrors(null);
        form.setLoading(true)
        OrderService.update(form.formData)
            .then((resp) => {
                openPopup({
                    type: 'success',
                    message: t('update_success')
                });
                setDetail(form.formData);
                if (callback) {
                    callback();
                }
                form.setLoading(false)
            })
            .catch((error) => {
                if (error.response?.data?.errors) {
                    form.setFormErrors(error.response.data?.errors)
                }
                if (error.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
                form.setLoading(false)
                getDetail();
            })
    }, [form]);
    const confirmUpdateInformation = useCallback(() => {
        if (JSON.stringify(detail) !== JSON.stringify(form.formData)) {
            return openPopup({
                type: 'warning',
                message: t('save_data_change'),
                onConfirm: () => {
                    updateInformation(() => {
                        setCurrentStep((pre) => pre + 1)
                    });
                },
                onCancel: () => {
                    setCurrentStep((pre) => pre + 1)
                }
            })
        } else {
            setCurrentStep((pre) => pre + 1)
        }
    }, [form.formData, detail]);
    const getDetail = useCallback(() => {
        OrderService.show(searchParams.get('id'))
            .then((resp) => {
                setDetail(resp.message);
                form.setFormData(resp.message);
            })
            .catch((error) => {

            })
    }, [searchParams]);
    const saveShipping = useCallback(() => {
        shippingForm.setLoading(true)
        shippingForm.setFormErrors(null)
        OrderShippingService.update(shippingForm.formData)
            .then((resp) => {
                openPopup({
                    type: 'success',
                    message: t('update_success')
                });
                setShippingDetail(shippingForm.formData);
                shippingForm.setLoading(false)
                getSummary();
            })
            .catch((error) => {
                if (error.response.data?.errors) {
                    shippingForm.setFormErrors(error.response.data?.errors)
                }
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
                shippingForm.setLoading(false)
            })
    }, [shippingForm.formData, shippingDetail]);
    const confirmUpdateShipping = useCallback(() => {
        if (JSON.stringify(shippingDetail) !== JSON.stringify(shippingForm.formData)) {
            return openPopup({
                type: 'warning',
                message: t('save_data_change'),
                onConfirm: () => {
                    saveShipping(() => {
                        setCurrentStep((pre) => pre + 1)
                    });
                },
                onCancel: () => {
                    setCurrentStep((pre) => pre + 1)
                }
            })
        } else {
            setCurrentStep((pre) => pre + 1)
        }
    }, [shippingForm.formData, shippingDetail]);
    const getSummary = useCallback(() => {
        OrderItemService.summary({
            order_id: searchParams.get('id')
        })
            .then((resp) => {
                dispatch(setSummary(resp.message));
            })
            .catch((error) => {

            })
    }, [searchParams]);
    const confirmApprove = () => {
        openPopup({
            type: 'warning',
            message: t('confirm_approve'),
            onConfirm: () => {
                form.handleChangeByKey('status', 'approved');
            }
        })
    };
    const confirmCancelled = () => {
        openPopup({
            type: 'warning',
            message: t('confirm_cancel'),
            onConfirm: () => {
                setShowCancelReason(true)
            }
        })
    };
    useEffect(() => {
        if (detail?.status === 'pending' && form.formData?.status === 'approved') {
            updateInformation(() => {

            });
        }
        if (detail?.status !== 'cancelled' && form.formData?.status === 'cancelled') {
            updateInformation(() => {
                setShowCancelReason(false)
            });
        }
    }, [form?.formData?.status, detail?.status])
    const nextStep = () => {
        if (currentStep >= 3) {
            return;
        }
        if (currentStep === 0) {
            return confirmUpdateInformation();
        }
        if (currentStep === 2) {
            return confirmUpdateShipping();
        }
        setCurrentStep((pre) => pre + 1)
    }
    const prevStep = () => {
        if (currentStep === 0) {
            return;
        }
        setCurrentStep((pre) => pre - 1)
    }
    const getDetailOrderShipping = useCallback(() => {
        OrderShippingService.show({
            order_id: detail?.id,
            id: detail?.shipping_id
        })
            .then((resp) => {
                shippingForm.setFormData(resp.message);
                setShippingDetail(resp.message);
            }).catch((error) => {

            })
    }, [detail])

    const shippingView = useCallback(() => {
        OrderShippingService.view()
            .then((resp) => {
                shippingForm.setHookRender(resp.message.form)
            }).catch((error) => {

            })
    }, []);

    const detailView = useCallback(() => {
        OrderService.view()
            .then((resp) => {
               form.setHookRender(resp.message.index)
            })
            .catch((error) => {

            })
    }, [searchParams]);

    useEffect(() => {
        getDetail();
        shippingView();
        detailView();
    }, []);
    useEffect(() => {
        if (detail) {
            getDetailOrderShipping();
            getSummary();
        }
    }, [detail])

    return <div>
        <PageHead
            containerClass='lg-m-4'
            title={t('Order')} subtitle={t("order_desc")} />
        {detail?.status === 'cancelled' ? <div className='row mx-4 mt-3'>
            <div className='col-12'>
                <BootstrapAlert 
                title={t('Cancelled')} 
                type='danger' 
                message={detail?.reason ?? 'No reason'} />
            </div>
        </div> : null}
        {detail ? <div>
            <div className='row lg-m-4'>
                <div className='mt-3'>
                    <FormStep
                        list={[t("Customer & Order"), t("Products"), t("Shipping"), t("Completed")]}
                        active={currentStep} />
                </div>
                <div className='col-lg-9'>
                    <div className='mt-3'>
                        <div className='theme-card p-3 rounded-4 border'>
                            <div className={currentStep == 0 ? 'show' : 'hidden'}>
                                <CustomerInformation form={form} />
                            </div>
                            <div className={currentStep == 1 ? 'show' : 'hidden'}>
                                <Products detail={detail} />
                            </div>
                            <div className={currentStep == 2 ? 'show' : 'hidden'}>
                                <ShippingForm form={shippingForm} />
                            </div>
                            <div className={currentStep == 3 ? 'show' : 'hidden'}>
                                {detail?.status === 'approved'
                                    ? <Completed />
                                    : detail?.status === 'cancelled'
                                        ? <Cancelled /> : <BeforeApprove />}
                            </div>
                        </div>
                        <div className="row mt-3">
                            <div className="col-lg-2 col-md-6">
                                <SecondaryButton loading={form.loading || shippingForm.loading} onClick={prevStep} 
                                label={t('Back')} />
                            </div>
                            <div className="col-lg-4 col-md-6 ms-auto text-end">
                                <div className='d-flex'>
                                    <div className=''>
                                        {detail?.status !== 'cancelled' ?
                                            <CancelButton 
                                            
                                            loading={form.loading || shippingForm.loading} onClick={confirmCancelled} 
                                            label={t('button_cancel_label')} type={'order'} />
                                            : null}
                                    </div>
                                    <div className='mx-2'>
                                        {currentStep <= 2
                                            ? <PrimaryButton loading={form.loading || shippingForm.loading} onClick={nextStep} 
                                            label={t('Next')} />
                                            : null}

                                        {currentStep === 3 && detail?.status === 'pending'
                                            ? <ApproveButton type={'order'} loading={form.loading} onClick={confirmApprove} 
                                            label={t('Approved')} />
                                            : null}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className='col-lg-3'>
                    <Summary summaryData={summaryData} />
                </div>
            </div>
        </div> : null}
        {showCancelReason ? <PopupLayout
            loading={form.loading}
            title={t('Cancel reason')}
            confirmText={t('Submit cancel')}
            onClose={() => setShowCancelReason(false)}
            onConfirm={() => {
                form.handleChangeByKey('status', 'cancelled');
            }}
        >
            <label>{t("Reason")}</label>
            <TextArea
                name='reason'
                value={form.formData?.reason}
                errorMessage={form.formErrors?.reason}
                handleChange={form.handleChange}
                placeholder={t("reason_placeholder")}
            />
        </PopupLayout> : null}

    </div>
}