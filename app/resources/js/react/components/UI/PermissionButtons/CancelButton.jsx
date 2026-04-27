import React from "react";
import { useSelector } from "react-redux";
import DangerButton from "../Buttons/DangerButton";

export default function CancelButton({
    label = "Cancel",
    onClick = null,
    disabled = false,
    loading = false,
    width = 'auto',
    height = 35,
    type = null,
    customPermission = null
}) {
    const roles = useSelector((state) => state.businessRole.role);
    let permission = null;
    if (customPermission) {
        permission = roles?.includes(customPermission);
    } else if (type) {
        permission = roles?.includes("erp." + type + ".cancelled");
    }
    return <DangerButton
        label={label}
        onClick={onClick}
        disabled={disabled || !permission}
        loading={loading}
        width={width}
        height={height}
        type={type}
    />;
}
