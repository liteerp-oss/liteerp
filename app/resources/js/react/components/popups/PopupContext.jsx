import React, { createContext, useContext, useState, useCallback } from "react";
import { createPortal } from "react-dom";
import { CheckCircle, X } from "react-bootstrap-icons";
import { useDispatch, useSelector } from "react-redux";
import SecondaryButton from "../UI/Buttons/SecondaryButton";
import WarningButton from "../UI/Buttons/WarningButton";
import SuccessButton from "../UI/Buttons/SuccessButton";
import DangerButton from "../UI/Buttons/DangerButton";
import { useI18n } from "@/i18n/useI18n";
const PopupContext = createContext();

export const PopupProvider = ({ children }) => {

  const {t} = useI18n()

  const [popup, setPopup] = useState(null);

  const openPopup = useCallback((options) => {
    setPopup({
      message: options.message || "",
      type: options.type || "success", // success | warning | error | info
      onConfirm: options.onConfirm || null,
      onCancel: options.onCancel || null,
      confirmText: options.confirmText || t("Confirm"),
      cancelText: options.cancelText || t("Close"),
    });
  }, []);

  const closePopup = useCallback(() => setPopup(null), []);

  const getIconStyle = (type) => {
    switch (type) {
      case "success":
        return { bg: "#10b981", Icon: CheckCircle };
      case "error":
        return { bg: "#ef4444", Icon: X };
      case "warning":
        return { bg: "#f59e0b", Icon: X };
      default:
        return { bg: "#6366f1", Icon: CheckCircle };
    }
  };

  const popupTitle = (type) => {
    switch (type) {
      case "success":
        return t("Completed");
      case "error":
        return t("Failed");
      case "warning":
        return t("Warning");
      default:
        return { bg: "#6366f1", Icon: CheckCircle };
    }
  }

  const getButtonStyle = (type) => {
    switch (type) {
      case "success":
        return <SuccessButton 
                label={popup.confirmText}
                onClick={() => {
                  popup.onConfirm?.();
                      closePopup();
                }}/>
      case "error":
        return <DangerButton label={popup.confirmText}
                onClick={() => {
                  popup.onConfirm?.();
                      closePopup();
                }}/>
      case "warning":
        return <WarningButton label={popup.confirmText}
                onClick={() => {
                  popup.onConfirm?.();
                      closePopup();
                }}/>
      default:
        return <SecondaryButton label={popup.confirmText}
                onClick={() => {
                  popup.onConfirm?.();
                      closePopup();
                }}/>
    }
  };

  const theme = useSelector((state) => state.theme.mode);
  return (
    <PopupContext.Provider value={{ openPopup, closePopup }}>
      {children}

      {popup &&
        createPortal(
          <div className="popup-overlay" data-theme={theme}>
            <div className="popup-card animate-popup">
              {/* Header */}
              <div className="d-flex align-items-start justify-content-between mb-3">
                <div className="d-flex align-items-center">
                  <div
                    className="popup-icon rounded-4 d-flex align-items-center justify-content-center me-3"
                    style={{ backgroundColor: getIconStyle(popup.type).bg }}
                  >
                    {React.createElement(getIconStyle(popup.type).Icon, {
                      size: 22,
                      color: "#fff",
                    })}
                  </div>
                  <div>
                    <h5 className="fw-bold mb-1">{popupTitle(popup.type)}</h5>
                    <p className="text-secondary mb-0">{popup.message}</p>
                  </div>
                </div>
                <button
                  className="btn-close ms-3 theme-title-highlight"
                  onClick={closePopup}
                ></button>
              </div>

              <hr className="my-3 border-secondary border-opacity-25" />

              {/* Footer buttons */}
              <div className="d-flex justify-content-end gap-2">
                <SecondaryButton label={popup.cancelText}
                onClick={() => {
                  popup.onCancel?.();
                      closePopup();
                }}
                />
                { popup.onConfirm ? getButtonStyle(popup.type) : null}
              </div>
            </div>
          </div>,
          document.body
        )}
    </PopupContext.Provider>
  );
};

export const usePopup = () => useContext(PopupContext);
