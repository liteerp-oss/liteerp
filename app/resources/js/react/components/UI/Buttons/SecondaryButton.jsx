import React from "react";

export default function SecondaryButton({ 
  label = "Success Button", 
  onClick = null, 
  width = 150,
  disabled = false,
loading = false,
  height= 35 }) {
  return (
    <button 
    disabled={disabled || loading}
    className="erp-btn erp-btn-secondary" onClick={onClick} style={{
      width: width,
      height: height
    }}>
      {label}
    </button>
  );
}
