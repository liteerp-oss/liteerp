import React, { useState, useEffect, useCallback } from "react";
import BusinessLayout from "../layouts/BusinessLayout";
import IconButton from "../components/UI/IconButton/IconButton";
import BusinessListItem from "../components/Business/BusinessListItem";
import { useNavigate } from "react-router-dom";
import { AddBusiness } from "../components/Business/AddBusiness";
import businessService from '../services/businessService'
import { usePopup } from "../components/popups/PopupContext";
import EmptyBox from "../components/Emptybox";
import LoadingBox from '../components/LoadingBox'
import { useForm } from '../libraries/handleInput'
import { useDispatch } from "react-redux";
import { setBusinessInfo } from "../redux/businessInfoSlice";
import { clearBusinessNav, clearBusinessRole } from "../redux/businessRoleSlice";
import { cleanNotificationCount } from "../redux/NotificationSlice";
export default function Business() {
    const [loadViewDetail, setLoadingViewDetail] = useState(false)
    const dispatch = useDispatch();
    const [loading, setLoading] = useState(true);
    const navigate = useNavigate();
    const { openPopup } = usePopup();
    const [openAdd, setOpenAdd] = useState(false);
    const form = useForm();
    const [listBusiness, setListBusiness] = useState([]);
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
                message: "Add new company has been successfully",
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
    useEffect(() => {
        getList();
        dispatch(clearBusinessNav());
        dispatch(clearBusinessRole());
        dispatch(cleanNotificationCount())
    }, []);
    return (
        <BusinessLayout>
            <div>
                <div className="business-topbar">
                    <div className="container pt-4 pb-2">
                        <div className="d-flex justify-content-between align-items-center">
                            <div className="d-flex">
                                <div className="">
                                    <img className="thumbnail"
                                        height={50}
                                        src={"/assets/logo-full.png"} alt='' />
                                </div>
                            </div>
                            <div>
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
                            return <div key={index} className="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                                <div>
                                    <BusinessListItem
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
                                <h4 className="text-center h4">Please add your company to continue</h4>
                                <p>

                                </p>
                                <EmptyBox />
                            </div>}
                        </div>}

                    </div>
                </div>
                {openAdd ? <AddBusiness
                    form={form}
                    show={openAdd}
                    onClose={() => setOpenAdd(false)}
                    onConfirm={submit}
                /> : null}

            </div>
        </BusinessLayout>
    );
}
