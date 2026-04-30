import React, { useState, useEffect, useCallback } from "react";
import BusinessLayout from "../layouts/BusinessLayout";
import IconButton from "../components/UI/IconButton/IconButton";
import BusinessListItem from "../components/Business/BusinessListItem";
import { useNavigate } from "react-router-dom";
import businessService from '../services/businessService'
import { usePopup } from "../components/popups/PopupContext";
import EmptyBox from "../components/Emptybox";
import LoadingBox from '../components/LoadingBox'
import { useForm } from '../libraries/handleInput'
import { useDispatch } from "react-redux";
import { setBusinessInfo } from "../redux/businessInfoSlice";
import { clearBusinessNav, clearBusinessRole } from "../redux/businessRoleSlice";
import { cleanNotificationCount } from "../redux/NotificationSlice";
import { useI18n } from "@/i18n/useI18n";
import { PopupLayout } from "../layouts/PopupLayout";
import { InputForm } from "../components/UI/Input/InputForm";
import RenderFormFieldByList from "../components/RenderFormFieldByList";
import Logo from "../components/Logo";
import { BoxArrowInLeft } from "react-bootstrap-icons";
export default function Business() {
    const { t } = useI18n();
    const [loadViewDetail, setLoadingViewDetail] = useState(false)
    const dispatch = useDispatch();
    const [loading, setLoading] = useState(true);
    const navigate = useNavigate();
    const { openPopup } = usePopup();
    const [openAdd, setOpenAdd] = useState(false);
    const form = useForm();
    const [listBusiness, setListBusiness] = useState([]);
    const [renderItem, setRenderItem] = useState(null)
    const getDetail = useCallback((id) => {
        setLoadingViewDetail(true)
        businessService.show(id)
            .then((data) => {
                localStorage.setItem('business-access', data.message.token);
                localStorage.setItem('business', JSON.stringify(data.message.business));
                dispatch(setBusinessInfo(data.message.business))
                navigate('/profile')
                setLoadingViewDetail(false)
            })
            .catch((error) => {
                if (error.response?.data?.message) {
                    openPopup({
                        message: error.response?.data?.message,
                        type: 'error'
                    })
                }
                setLoadingViewDetail(false)
            })
    }, []);
    const getList = useCallback(() => {
        setLoading(true)
        businessService.list().then((data) => {
            setListBusiness(data.message);
            setLoading(false)
        }).catch((error) => {
            setErrors(error.response.data?.errors);
            if (error.response.data?.message) {
                openPopup({
                    message: error.response.data?.message,
                    type: 'error'
                })
            }
            setLoading(false)
        })
    }, []);
    const submit = useCallback(() => {
        businessService.add(form.formData).then((data) => {
            setOpenAdd(false);
            openPopup({
                message: t("Add new company has been successfully"),
                type: 'success',
                onConfirm: () => {
                    getList()
                }
            })
        }).catch((error) => {
            if (error.response.data?.errors) {
                form.setFormErrors(error.response.data?.errors)
            }
            if (error.response.data?.message) {
                openPopup({
                    message: error.response.data?.message,
                    type: 'error'
                })
            }
        })
    }, [form.formData, setOpenAdd, openPopup, getList]);
    const getView = () => {
        businessService.view().then((resp) => {
            form.addHookRender(resp.message.form);
            setRenderItem(resp.message.index);
        }).catch((error) => {
            if (error.response.data?.errors) {
                form.setFormErrors(error.response.data?.errors)
            }
            if (error.response.data?.message) {
                openPopup({
                    message: error.response.data?.message,
                    type: 'error'
                })
            }
        })
    }
    useEffect(() => {
        getList();
        dispatch(clearBusinessNav());
        dispatch(clearBusinessRole());
        dispatch(cleanNotificationCount());
        getView();
    }, []);
    const logout = () => {
        localStorage.removeItem('token');
        window.location.href = "/";
    }
    return (
        <BusinessLayout>
            <div>
                <div className="business-topbar">
                    <div className="container pt-4 pb-2">
                        <div className="d-flex justify-content-between align-items-center">
                            <div className="d-flex">
                                <div className="">
                                    <a href="/">
                                        <Logo />
                                    </a>
                                </div>
                            </div>
                            <div className="d-flex">
                                <div style={{
                                    marginRight: 10
                                }} onClick={logout}>
                                    <IconButton icon={BoxArrowInLeft} color="white" />
                                </div>
                                <IconButton onClick={() => {
                                    form.setFormData(null)
                                    setOpenAdd(true);
                                }} />
                            </div>
                        </div>
                    </div>
                </div>
                <div className="container">
                    <div className="row mt-3">
                        {listBusiness.length >= 1 ? listBusiness.map((item, index) => {
                            return <div key={index} className="col-xs-12 col-sm-12 col-md-12 col-lg-6 mb-2">
                                <div>
                                    <BusinessListItem
                                        renderItem={renderItem}
                                        business={item}
                                        onViewDetail={() => {
                                            if (loadViewDetail) {
                                                return;
                                            }
                                            getDetail(item.id);
                                        }}
                                    />
                                </div>
                            </div>
                        }) : <div className="mt-5">
                            {loading ? <LoadingBox /> : <div>
                                <h4 className="text-center h4">{t('please_add_business')}</h4>
                                <p>

                                </p>
                                <EmptyBox />
                            </div>}
                        </div>}

                    </div>
                </div>
                {openAdd ? <PopupLayout
                    onClose={() => setOpenAdd(false)}
                    onConfirm={submit}
                    title={t("add_business")}
                >
                    <form>
                        <div className="mb-3">
                            <InputForm
                                errorMessage={form.formErrors?.name}
                                value={form.formData?.name}
                                handleChange={form.handleChange}
                                name="name"
                                placeholder={t("Your company name")}
                                label={t("Name")}
                                required={true}
                            />
                        </div>

                        <div className="mb-3">
                            <InputForm
                                errorMessage={form.formErrors?.address}
                                value={form.formData?.address}
                                handleChange={form.handleChange}
                                name="address"
                                placeholder={t("Your company address")}
                                label={t("Address")}
                            />
                        </div>
                        <div className="mb-3">
                            <InputForm
                                errorMessage={form.formErrors?.tax_code}
                                value={form.formData?.tax_code}
                                handleChange={form.handleChange}
                                name="tax_code"
                                placeholder={t("Your company tax code")}
                                label={t("Tax code")}
                            />
                        </div>
                        <div className="row">
                            <div className="mb-3 col-6">
                                <InputForm
                                    errorMessage={form.formErrors?.phone}
                                    value={form.formData?.phone}
                                    handleChange={form.handleChange}
                                    name="phone"
                                    placeholder={t("Your company contact phone")}
                                    label={t("Phone")}
                                />
                            </div>
                            <div className="mb-3 col-6">
                                <InputForm
                                    errorMessage={form.formErrors?.email}
                                    value={form.formData?.email}
                                    handleChange={form.handleChange}
                                    name="email"
                                    placeholder={t("Your company contact email")}
                                    label={t("Email")}
                                />
                            </div>
                        </div>
                        <div className="mb-3">
                            <InputForm
                                errorMessage={form.formErrors?.bank_name}
                                value={form.formData?.bank_name}
                                handleChange={form.handleChange}
                                name="bank_name"
                                placeholder={t("Your company bank name")}
                                label={t("Bank name")}
                                required={true}
                            />
                        </div>
                        <div className="row">
                            <div className="mb-3 col-6">
                                <InputForm
                                    errorMessage={form.formErrors?.bank_account_number}
                                    value={form.formData?.bank_account_number}
                                    handleChange={form.handleChange}
                                    name="bank_account_number"
                                    placeholder={t("Your company bank account number")}
                                    label={t("Bank account number")}
                                    required={true}
                                />
                            </div>
                            <div className="mb-3 col-6">
                                <InputForm
                                    errorMessage={form.formErrors?.bank_account_name}
                                    value={form.formData?.bank_account_name}
                                    handleChange={form.handleChange}
                                    name="bank_account_name"
                                    placeholder={t("Your company bank account name")}
                                    label={t("Bank account name")}
                                />
                            </div>
                        </div>
                        {form.hookRender.map((item, index) => {
                            return <div className="mb-3" key={index}>
                                <RenderFormFieldByList item={item} form={form} />
                            </div>
                        })}
                    </form>
                </PopupLayout> : null}

            </div>
        </BusinessLayout>
    );
}
