import React from "react";
import PrimaryButton from "../Buttons/PrimaryButton";
import { useSelector } from "react-redux";
import { useI18n } from "@/i18n/useI18n";

export default function CreateButton({
    label = "Save changes",
    onClick = null,
    disabled = false,
    loading = false,
    width = 'auto',
    height = 35,
    type = null,
    customPermission = null
}) {
    const {t} = useI18n()
    const roles = useSelector((state) => state.businessRole.role);
    let permission = null;
    if(customPermission) {
        permission = roles?.includes(customPermission);
    } else if(type) {
        permission = roles?.includes("erp." + type + ".create");
    }
    
    return <PrimaryButton
        label={t(label)}
        onClick={onClick}
        disabled={disabled || !permission}
        loading={loading}
        width={width}
        height={height}
        type={type}
    />;
}
