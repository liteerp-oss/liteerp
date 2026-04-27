import React from "react";

export default function PrimaryButton({ 
  label = "Primary Button", 
  onClick = null, 
  disabled = false,
  loading = false,
  width = 'auto',
  height = 35
}) {
  return (
    <button disabled={disabled || loading} className="erp-btn erp-btn-primary" onClick={onClick}
    style={{
      width: width,
      height: height,
      paddingLeft: 15,
      paddingRight: 15
    }}
    >
      {label}
    </button>
  );
}
