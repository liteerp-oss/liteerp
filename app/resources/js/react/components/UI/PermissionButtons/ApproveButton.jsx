import React from "react";
import PrimaryButton from "../Buttons/PrimaryButton";
import { useSelector } from "react-redux";
import SuccessButton from "../Buttons/SuccessButton";

export default function ApproveButton({
    label = "Primary Button",
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
        permission = roles?.includes("erp." + type + ".approved");
    }
    return <SuccessButton
        label={label}
        onClick={onClick}
        disabled={disabled || !permission}
        loading={loading}
        width={width}
        height={height}
        type={type}
    />;
}
