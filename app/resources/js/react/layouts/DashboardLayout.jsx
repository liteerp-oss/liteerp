import React, { useCallback, useEffect } from 'react'
import Sidebar from "../components/Sidebar";
import Topbar from "../components/Topbar";
import { useDispatch, useSelector } from "react-redux";
import { useNavigate } from 'react-router-dom';
import PermissionService from '../services/PermissionService'
import { setBusinessNav, setBusinessRole } from '../redux/businessRoleSlice';
import { useI18n } from '@/i18n/useI18n';
import Footer from '../components/Footer';
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
            <div className="col-lg-10 dashboard-content" style={{
                paddingLeft: 0
            }}>
                <Topbar />
                <div className='mb-5'>
                    {children}
                </div>
            </div>
        </div>
        <Footer/>


    </div>
}