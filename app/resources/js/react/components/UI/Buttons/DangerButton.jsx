import React from "react";

export default function DangerButton({ 
  label = "Danger Button", 
  onClick = null, 
  disabled = false,
  loading = false,
  width = 70,
  height = 35
}) {
  return (
    <button 
    disabled={disabled || loading}
    className="erp-btn erp-btn-danger" onClick={onClick} style={{
      width: width,
      height: height
    }}>
      {label}
    </button>
  );
}
