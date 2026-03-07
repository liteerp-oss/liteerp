import React, { useCallback, useEffect, useState } from "react";
import ExtensionService from "../../services/ExtensionService";
import { useForm } from '../../libraries/handleInput'
import { usePopup } from '../popups/PopupContext'
import { useI18n } from "../../../i18n/useI18n";
import PermissionService from "@/react/services/PermissionService";
import { setBusinessNav, setBusinessRole } from "@/react/redux/businessRoleSlice";
import { useDispatch } from "react-redux";
import DeleteButton from "../UI/PermissionButtons/DeleteButton";
import UpdateButton from "../UI/PermissionButtons/UpdateButton";
export default function ExtensionCard({
  item = {
    icon: "bi bi-box",
    name: "Unknown Extension",
    version: "0.0.0",
    verified: false,
    description: "No description provided.",
    author: "Unknown",
    lastUpdate: "—",
    directory: "",
    status: false,
    setting_link: "#"
  }
}) {
  const { t } = useI18n();
  const { openPopup } = usePopup();
  const form = useForm();
  const dispatch = useDispatch();
  const businessRole = useCallback(() => {
    PermissionService.view()
      .then((resp) => {
        dispatch(setBusinessNav(resp.message.nav))
        dispatch(setBusinessRole(resp.message.roles))
      })
      .catch((error) => {

      })
  }, [dispatch])
  const update = useCallback(() => {
    if (form.formData?.directory) {
      form.setLoading(true)
      ExtensionService.update({
        ...form.formData,
        status: form.formData.status ? false : true
      })
        .then((resp) => {
          form.setFormData({
            ...form.formData,
            status: form.formData.status ? false : true
          })
          form.setLoading(false)
          businessRole();
        })
        .catch((error) => {
          form.setLoading(false)
          if(error.response?.data?.message) {
                openPopup({
                    type: 'error',
                    message: error.response?.data?.message
                })
            }
        })
    }
  }, [form]);
  const destroy = useCallback(() => {
    if (form.formData?.directory) {
      form.setLoading(true)
      ExtensionService.delete({
        ...form.formData
      })
        .then((resp) => {
          form.setLoading(false)
          form.setFormData(null)
          businessRole();
        })
        .catch((error) => {
          form.setLoading(false)
          if(error.response?.data?.message) {
                openPopup({
                    type: 'error',
                    message: error.response?.data?.message
                })
            }
        })
    }
  }, [form]);
  const confirmDelete = useCallback(() => {
    openPopup({
      type: 'warning',
      message: t('Are you sure to delete?'),
      onConfirm: () => {
        destroy();
      }
    })
  }, [form])
  useEffect(() => {
    form.setFormData(item)
  }, [])
  return form.formData ? <div className="card h-100 shadow-sm">
    <div className="card-body d-flex flex-column">

      {/* Header */}
      <div className="d-flex align-items-start mb-3">
        <div className="me-3 fs-3 text-primary">
          <i className={form.formData?.icon ?? 'bi bi-google-play'}></i>
        </div>

        <div className="flex-grow-1">
          <h5 className="card-title mb-1 text-primary text-truncate">
            {form.formData?.name}
          </h5>
          <div className="small theme-title">
            v{form.formData?.version} •{" "}
            {form.formData?.verified ? (
              <span className="text-success">{t('Verified')}</span>
            ) : (
              <span className="text-danger">{t('Unverified')}</span>
            )}
          </div>
        </div>
      </div>

      {/* Description */}
      <p className="card-text small text-muted mb-3">
        {form.formData?.description.length > 120
          ? form.formData?.description.substring(0, 120) + "..."
          : form.formData?.description}
      </p>

      {/* Meta */}
      <ul className="list-unstyled small text-muted mb-4">
        <li>
          <strong>{t('Author')}:</strong> {form.formData?.author}
        </li>
        <li>
          <strong>{t('Directory')}:</strong> {form.formData?.directory}
        </li>
      </ul>

      {/* Actions */}
      <div className="mt-auto d-flex gap-2">
        <UpdateButton
          disabled={form.loading}
          onClick={update}
            label={form.formData?.status ? t("Disable") : t("Enable")}
            type={'extension'}
        />
        {form.formData?.setting_link ? <a
          href={form.formData?.setting_link}
          target="_blank"
          className="btn btn-sm btn-outline-secondary"
          rel="noreferrer"
        >
          <i className="bi bi-gear me-1" />
          {t("Settings")}
        </a> : null}

        <DeleteButton
          type={'extension'}
          onClick={confirmDelete}
          label={t("Delete")}
        />
      </div>
    </div>
  </div> : null

    ;
}
