import React, { useCallback, useEffect, useState } from "react";
import { Bell, Sun, Moon } from "react-bootstrap-icons";
import { useDispatch, useSelector } from "react-redux";
import { setTheme, toggleTheme } from "../redux/themeSlice";
import { Link, useNavigate } from "react-router-dom";
import NotificationService from "../services/NotificationService";
import { setNotificationCount } from "../redux/NotificationSlice";
import { useEcho } from "@laravel/echo-react";
import LanguageSwitcher from "./LanguageSwitcher";
import { useI18n } from "../../i18n/useI18n";
import MenuMobile from "./Topbar/MenuMobile";
export default function Topbar() {
  const { t } = useI18n();
  const navigate = useNavigate();
  const dispatch = useDispatch();
  const theme = useSelector((state) => state.theme.mode);
  const business = useSelector((state) => state.business.data);
  const notify = useSelector((state) => state.notify.data);
  const getNotification = useCallback(() => {
    NotificationService.listIsNotRead()
      .then((resp) => {
        dispatch(setNotificationCount(resp.message.total ?? 0))
      })
      .catch((error) => { })
  }, []);
  /**
  * Listen event broadcast 
  */
  useEcho(`user.${business?.user_id}.${business.id}`, ".NewNotificationBroadcast", (e) => {
    getNotification();
  }).listen();
  /**
   * Pull notifications
  */
  useEffect(() => {
    if (!notify) {
      getNotification();
    }
  }, [notify]);
  const media = window.matchMedia('(prefers-color-scheme: dark)');
  media.addEventListener('change', e => {
    dispatch(setTheme(e.matches ? 'dark-theme' : 'light-theme'));
  });
  return (
    <div className="erp-topbar d-flex align-items-center justify-content-between px-4">
      {/* Left Section */}
      <div className="d-flex align-items-center">
        <div className="menu-mobile">
          <MenuMobile />
        </div>
        <div>
          <h5 className="fw-bold mb-0 erp-topbar-title">{business?.name}</h5>
          <small className="theme-title">
            {business?.address}
          </small>
        </div>
      </div>

      {/* Right Section */}
      <div className="d-flex align-items-center">
        <LanguageSwitcher />
        <div onClick={() => {
          navigate('/notification')
        }} className="position-relative me-4 topbar-notification ml-2">
          <Bell size={20} color="#fff" />
          <span className="erp-badge bg-danger">{notify}</span>
        </div>

        {/* Theme Toggle */}
        <button
          onClick={() => {
            dispatch(toggleTheme())
          }}
          className="btn btn-sm btn-outline-light me-3 topbar-theme-toggle"
          title="Chuyển theme"
        >
          {theme === "dark-theme" ? <Sun size={22} /> : <Moon size={22} />}
        </button>

        <div className="erp-avatar fw-bold">A</div>
        <button className="btn btn-link p-0 ms-1 dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i className="bi bi-caret-down-fill"></i>
        </button>
        <div>
          <ul className="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><Link className="dropdown-item" to={"/profile"}>{t('Profile')}</Link></li>
            <li><hr className="dropdown-divider" /></li>
            <li><Link className="dropdown-item" to="/logout">{t('Logout account')}</Link></li>
            <li><Link className="dropdown-item" to="/business">{t('Logout business')}</Link></li>
          </ul>
        </div>
      </div>
    </div>
  );
}
