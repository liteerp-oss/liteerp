import React, { useCallback, useEffect } from 'react'
import Sidebar from "../components/Sidebar";
import Topbar from "../components/Topbar";
import { useDispatch, useSelector } from "react-redux";
import { useNavigate } from 'react-router-dom';
import PermissionService from '../services/PermissionService'
import { setBusinessNav, setBusinessRole } from '../redux/businessRoleSlice';
import { useI18n } from '@/i18n/useI18n';
export default function DashboardLayout({
    children
}) {
    const navigate = useNavigate();
    const { lang } = useI18n();
    const nav = useSelector((state) => state.businessRole.nav);
    const business = useSelector((state) => state.business.data);
    const theme = useSelector((state) => state.theme.mode);
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
    useEffect(() => {
        if (!business) {
            navigate("/business");
        }
        if (!nav) {
            businessRole();
        }
    }, [business, nav]);
    /**
     * If has event change language then need call API again
     */
    useEffect(() => {
        businessRole();
    }, [lang]);
    return <div className={"container-fuild dashboard-megabox dark-theme "} data-theme={theme}>
        <div className="row desktop">
            <div className="col-lg-2 sidebar-desktop mb-5 px-0">
                <Sidebar />
            </div>
            <div className="col-lg-10 dashboard-content">
                <Topbar />
                <div>
                    {children}
                </div>
            </div>
        </div>
        <footer className="border-top bg-white">
            <div className="container-fluid py-2">
                <div className="row align-items-center text-muted small">
                    <div className="col-md-4 d-flex align-items-center gap-2 justify-content-center justify-content-md-start">
                        <i className="bi bi-box"></i>
                        <span>LiteERP © 2026</span>
                    </div>
                    <div className="col-md-8 d-flex align-items-center gap-3 justify-content-center justify-content-md-end">
                        <a href="https://www.hetzner.com/cloud" target="_blank"
                            className="text-muted text-decoration-none d-flex align-items-center gap-1 hover-opacity">
                            <img src="/Hetzner-Logo.png" height={25}/>
                            Sponsorship Cloud
                        </a>
                        <a href="https://github.com/liteerp-oss/liteerp" target="_blank"
                            className="text-muted text-decoration-none d-flex align-items-center gap-1 hover-opacity">
                            <i className="bi bi-github"></i>
                            Github
                        </a>

                        <a href="https://github.com/liteerp-oss/docs"
                            className="text-muted text-decoration-none d-flex align-items-center gap-1">
                            <i className="bi bi-book"></i>
                            Docs
                        </a>

                        <a href="https://github.com/liteerp-oss/liteerp/issues"
                            className="text-muted text-decoration-none d-flex align-items-center gap-1">
                            <i className="bi bi-life-preserver"></i>
                            Support
                        </a>
                    </div>

                </div>
            </div>
        </footer>


    </div>
}