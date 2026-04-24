import React from 'react';
import { useDispatch, useSelector } from "react-redux";
import LanguageSwitcher from '../components/LanguageSwitcher';
import { setTheme } from '../redux/themeSlice';
import Logo from '../components/Logo';
export default function AuthLayout(data = {
    children
}) {
    const dispatch = useDispatch();
    const theme = useSelector((state) => state.theme.mode);
    const media = window.matchMedia('(prefers-color-scheme: dark)');
      media.addEventListener('change', e => {
        dispatch(setTheme(e.matches ? 'dark-theme' : 'light-theme'));
      });
    return <div className="auth-page-body" data-theme={theme}>
        <div>
            {/** Langauges */}
            <div>
                <div className="text-center mb-3">
                   <Logo/>
                </div>
                {data.children}
                <div className="d-flex justify-content-center mt-3">
                    <LanguageSwitcher />
                </div>
            </div>
        </div>
    </div>
}