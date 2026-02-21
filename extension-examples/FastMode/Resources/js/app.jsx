import React from "react";
import DashboardLayout from '@layouts/DashboardLayout'
import PageHead from '@components/PageHead'
import TabsCustom from '@components/TabsCustom'
import { useI18n } from '@i18n/useI18n'
import { InputForm } from '@components/UI/Input/InputForm'
import { Select } from '@components/UI/Input/Select'
import PrimaryButton from '@components/UI/Buttons/PrimaryButton'
import { useForm } from '@libraries/handleInput'
import { usePopup } from '@components/popups/PopupContext'
import { useEffect } from "react";
import fastmodeService from "./services/api";
import { useCallback } from "react";
const FastModeApp = () => {
  const {openPopup} = usePopup()
  const {t} = useI18n();
  const form = useForm();
  useEffect(() => {
    fastmodeService.all()
    .then((resp) => {
      form.setFormData(resp.message)
    })
    .catch((error) => {
      if(error?.response?.data?.message) {
        openPopup({
          type: 'error',
          message: error?.response?.data?.message
        })
      }
    })
  },[])
  const store = useCallback(() => {
    fastmodeService.add(form.formData)
    .then((resp) => {
      openPopup({
        type: 'success',
        message: t('fastmode.success.message')
      })
    })
    .catch((error) => {
      if(error?.response?.data?.errors) {
        form.setFormErrors(error?.response?.data?.errors)
      }
      if(error?.response?.data?.message) {
        openPopup({
          type: 'error',
          message: error?.response?.data?.message
        })
      }
      
    })
  },[form]);
  return <DashboardLayout>
    <div>
      <PageHead title={t("fastmode.title")} subtitle={t("fastmode.subtitle")}/>
      <div className="container mt-3">
        <div className="card p-2">
          <div className="row">
            <div className="col-6">
              <Select 
              name="status"
              handleChange={form.handleChange}
              errorMessage={form.formErrors?.status}
              value={form.formData?.status}
              options={[
                {value: "paid",label: t("fastmode.form.status.paid")},
                {value: "pending",label: t("fastmode.form.status.pending")},
                {value: "partial_payment",label: t("fastmode.form.status.partial_payment")}
              ]}
              label={"fastmode.form.status.label"}
              required={true}
              />
              <div className="mt-3">
                <PrimaryButton width={150} onClick={store} label={t("fastmode.submit.label")}/>
              </div>
            </div>
            <div className="col-6">
              <p className="theme-title">{t("fastmode.desc")}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
};
export default FastModeApp;