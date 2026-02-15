import React from "react";

export default function PrimaryButton({ 
  label = "Primary Button", 
  onClick = null, 
  disabled = false,
  loading = false,
  width = 70,
  height = 35
}) {
  return (
    <button disabled={disabled || loading} className="erp-btn erp-btn-primary" onClick={onClick}
    style={{
      width: width,
      height: height
    }}
    >
      {label}
    </button>
  );
}
