import React from "react";
import PrimaryButton from "../Buttons/PrimaryButton";
import { useSelector } from "react-redux";

export default function CreateButton({
    label = "Create",
    onClick = null,
    disabled = false,
    loading = false,
    width = 70,
    height = 35,
    type = null
}) {
    const roles = useSelector((state) => state.businessRole.role);
    const permission = roles?.includes("erp." + type + ".create");
    return <PrimaryButton
        label={label}
        onClick={onClick}
        disabled={disabled || !permission}
        loading={loading}
        width={width}
        height={height}
        type={type}
    />;
}
