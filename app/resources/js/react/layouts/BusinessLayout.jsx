import React from 'react';
import { useSelector } from "react-redux";
import Footer from '../components/Footer';
export default function BusinessLayout(data = {
    children
}){
    const theme = useSelector((state) => state.theme.mode);
    return <div className='business-layout' data-theme={theme}>
        <div className="" style={{
            minHeight: '100vh'
        }}>
            {data.children}
        </div>
        <Footer/>
    </div>
}