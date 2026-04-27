import React, { useState, useEffect } from "react";
export default function GradientButton({
    text= 'Submit',
    callback= () => {},
    width= 'auto',
    loading = false,
    disabled = false
}){
    return <button 
    disabled={loading || disabled}
    style={{
        width: width,
        paddingLeft: 15,
        paddingRight: 15
    }}
    onClick={callback}
    className="default-button erp-btn">{text}</button>
}