import React, { useEffect } from "react";
import DashboardLayout from "@/react/layouts/DashboardLayout";
import { useForm } from "@/react/libraries/handleInput";
import { InputForm } from "@/react/components/UI/Input/InputForm";
import { usePopup } from "@/react/components/popups/PopupContext";
import PusherService from "./services/pusherService";

export default function App() {
  const form = useForm();
  const { openPopup } = usePopup();
  const load = () => {
    PusherService.config().then((cfg) => {
      form.setFormData({
        app_id: cfg?.app_id != null ? String(cfg.app_id) : "",
        app_key: cfg?.app_key != null ? String(cfg.app_key) : "",
        app_secret: cfg?.app_secret != null ? String(cfg.app_secret) : "",
        cluster: cfg?.cluster != null ? String(cfg.cluster) : "",
        host: cfg?.host != null ? String(cfg.host) : "",
        port: cfg?.port != null ? String(cfg.port) : "443",
        scheme: cfg?.scheme != null ? String(cfg.scheme) : "https",
        enabled: cfg?.enabled ?? true,
      });
    });
  };
  const save = () => {
    PusherService.save(form.formData).then(() => {
      openPopup({
        type: "success",
        message: "Updated successfully",
      });
      load();
    });
  };
  useEffect(() => {
    load();
  }, []);
  return (
    <DashboardLayout>
      <div className="container py-4">
        <h4 className="mb-3">Pusher Settings</h4>
        <div className="row g-3">
          <div className="col-md-6">
            <label>App ID</label>
            <InputForm
              name="app_id"
              value={form.formData?.app_id}
              handleChange={form.handleChange}
            />
          </div>
          <div className="col-md-6">
            <label>App Key</label>
            <InputForm
              name="app_key"
              value={form.formData?.app_key}
              handleChange={form.handleChange}
            />
          </div>
          <div className="col-md-6">
            <label>App Secret</label>
            <InputForm
              name="app_secret"
              value={form.formData?.app_secret}
              handleChange={form.handleChange}
            />
          </div>
          <div className="col-md-6">
            <label>Cluster</label>
            <InputForm
              name="cluster"
              value={form.formData?.cluster}
              handleChange={form.handleChange}
            />
          </div>
          <div className="col-md-6">
            <label>Host</label>
            <InputForm
              name="host"
              value={form.formData?.host}
              handleChange={form.handleChange}
            />
          </div>
          <div className="col-md-3">
            <label>Port</label>
            <InputForm
              name="port"
              value={form.formData?.port}
              handleChange={form.handleChange}
            />
          </div>
          <div className="col-md-3">
            <label>Scheme</label>
            <InputForm
              name="scheme"
              value={form.formData?.scheme}
              handleChange={form.handleChange}
            />
          </div>
        </div>
        <div className="mt-4">
          <button
            disabled={form.loading}
            onClick={save}
            className="btn btn-primary"
          >
            Save
          </button>
        </div>
      </div>
    </DashboardLayout>
  );
}
