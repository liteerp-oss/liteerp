import React from "react";

export default function SuccessButton({ 
  label = "Success Button", 
  onClick = null, 
  width = 'auto',
  disabled = false,
  loading = false,
  height = 35
}) {
  return (
    <button 
    disabled={disabled || loading}
    className="erp-btn erp-btn-success" onClick={onClick} style={{
      width: width,
      height : height,
      paddingLeft: 15,
      paddingRight: 15
    }}>
      {label}
    </button>
  );
}
